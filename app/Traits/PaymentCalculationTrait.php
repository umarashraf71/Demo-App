<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use App\Models\Supplier;
use App\Models\Price;
use App\Models\Incentive;
use App\Models\IncentiveType;

trait PaymentCalculationTrait
{
    /**
     * Calculate payemnts for given date time 
     *
     * @param  date  $fromDate
     * @param  date  $todate
     * @return array of calculated payments 
     * @return message in any failed case 
     */
    public function Paymentcalculation($formDate, $toDate)
    {
        //convert date to carbon date objects 
        $fromDate = Carbon::createFromFormat('Y-m-d', $formDate)->startOfDay();
        $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
        //get suppliers with purchases
        $suppliers = Supplier::with(['purchases' => function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('created_at', [$fromDate, $toDate]);
        }])
            ->whereHas('purchases', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->get();

        // foreach ($suppliers as $key => $supplier) {
        //     echo $supplier->purchases->count() . "<br>";
        //     // foreach ($supplier->purchases as $key => $purchase) {
        //     // echo $purchase->created_at . "<br>";
        //     // }
        // }
        //get base pricing
        $basePricing = Price::where('approved_at', 'exists', true)->get();
        //calculate pricing and make an array and return
        $payments = array();
        foreach ($suppliers as $supplier) {
            //Volume Incentive Calculation
            $incentiveType = IncentiveType::where('name', 'Volume')->pluck('_id')->first();
            $gross_purchases_volume = (int) $supplier->purchases->sum('ts_volume');
            $incentives = Incentive::where('source_type', $supplier->supplier_type_id)
                ->where('incentive_type', $incentiveType)
                ->where('from', '<=', $gross_purchases_volume)
                ->where('to', '>=', $gross_purchases_volume)
                ->get()->first();

            $volumeIncentive = array();
            if ($incentives <> null) {
                $volumeIncentive['incentive_id'] = $incentives->id;
                $volumeIncentive['volume_incentive_rate'] = (int) $incentives->amount;
                $volumeIncentive['total_volume_incentive'] = (int) round($incentives->amount * $gross_purchases_volume, 2);
            } else {
                $volumeIncentive['incentive_id'] = null;
                $volumeIncentive['volume_incentive_rate'] = (int) 0;
                $volumeIncentive['total_volume_incentive'] = (int) 0;
            }
            if ($supplier->cp_ids <> null && $supplier->collectionPoints->pluck('chillers')->count() > 0) {
                //Chilling Incentive Calculation
                $incentiveType = IncentiveType::where('name', 'Chilling FFL Chiller')->pluck('_id')->first();
                $incentives = Incentive::where('source_type', $supplier->supplier_type_id)
                    ->where('incentive_type', $incentiveType)
                    ->where('from', '<=', $gross_purchases_volume)
                    ->where('to', '>=', $gross_purchases_volume)
                    ->get()->first();
                $chillingIncentive = array();
                if ($incentives <> null) {
                    $chillingIncentive['incentive_id'] = $incentives->id;
                    $chillingIncentive['chilling_incentive_rate'] = (int) $incentives->amount;
                    $chillingIncentive['total_chilling_incentive'] = (int) round($incentives->amount * $gross_purchases_volume, 2);
                } else {
                    $chillingIncentive['incentive_id'] = null;
                    $chillingIncentive['chilling_incentive_rate'] = (int) 0;
                    $chillingIncentive['total_chilling_incentive'] = (int) 0;
                }
            } else {
                $chillingIncentive['incentive_id'] = null;
                $chillingIncentive['chilling_incentive_rate'] = (int) 0;
                $chillingIncentive['total_chilling_incentive'] = (int) 0;
            }
            $payment = array();
            //Get 1st Level price get
            //Top most priority pricing level
            //supplier level pricing 
            //pricing set only using supplier id
            $basePrice = $basePricing->where('supplier', $supplier->id)->where('collection_point', '=', null)->sortByDesc('wef')->first();
            if ($basePrice <> null) {
                // $wef = Carbon::createFromFormat('Y-m-d', $basePrice->wef);
                //iterate on purchases and calculate payment
                // foreach ($supplier->purchases as $key => $purchase) {
                // $purchaseDate = Carbon::createFromFormat('Y-m-d H:i:s',$purchase->getAttributes()['time']);
                // if(!$purchaseDate->gte($wef))
                // return 'Some payments date is less than with effect date of base price';
                // }
                $volume = round($supplier->purchases->sum('ts_volume'), 2);
                $payment['supplier_id'] = $supplier->id;
                $payment['supplier_type_id'] = $supplier->supplier_type_id;
                $payment['is_payment_active'] = ($supplier->payment_process == null) ? (int) 0 : $supplier->payment_process;
                $payment['total_ts_volume'] = $volume;
                $payment['payable_without_incentives'] = round($volume * $basePrice->price, 2);
                $payment['payable'] = round($volume * $basePrice->price + $volumeIncentive['total_volume_incentive'] + $chillingIncentive['total_chilling_incentive'], 2);
                $payment['base_price'] = $basePrice->price;
                $payment['incentives']['volume'] = $volumeIncentive;
                $payment['incentives']['chilling'] = $chillingIncentive;
                $purchase_ids = $supplier->purchases->pluck('id')->toArray();
                $purchases = array();
                //make purchase ids object
                foreach ($purchase_ids as $id) {
                    $temp = array();
                    $temp['id'] = $id;
                    $temp['level'] = 'Supplier Level';
                    $temp['base_price'] = $basePrice->price;
                    array_push($purchases, $temp);
                }
                $payment['purchases'] = $purchases;
                $payment['created_at'] = gmdate('c');
                $payment['updated_at'] = gmdate('c');
                array_push($payments, $payment);
            }
            //if first level supplier level price not found
            //find price using 2nd level i.e supplier+collection point level
            //for this level we need to iterate on every purchase because purchase can be of different collection points
            //create sum of volume and calculate payment and make an array of collection point ids and then create payment array for this supplier after purchase loop
            if ($basePrice == null) {
                $volume = 0;
                $payable = 0;
                $purchases = array();
                foreach ($supplier->purchases as $purchase) {
                    $cp = ($purchase->mcc <> null) ? $purchase->mcc : $purchase->cp_id;
                    //if this condition fails it means purchase is of area office level and move to the next priority level of pricing
                    if ($cp <> null) {
                        //find price using 2nd level i.e supplier+collection point level
                        $basePrice = $basePricing->where('supplier', $supplier->id)
                            ->where('collection_point', $cp)
                            ->where('source_type', null)
                            ->sortByDesc('wef')
                            ->first();
                        $level = 'Supplier + Collection Point Level';
                        if ($basePrice == null) {
                            // 3rd level collection point + source type level 
                            $basePrice = $basePricing->where('collection_point', $cp)
                                ->where('source_type', $supplier->supplier_type_id)
                                ->where('supplier', null)
                                ->sortByDesc('wef')
                                ->first();
                            $level = 'Collection Point + Source Type Level';
                            //4th level only collection point 
                            if ($basePrice == null) {
                                $basePrice = $basePricing->where('collection_point', $cp)
                                    ->where('source_type', null)
                                    ->where('supplier', null)
                                    ->sortByDesc('wef')
                                    ->first();
                                $level = 'Collection Point Level';
                            }
                        }
                        if ($basePrice <> null) {
                            $volume = round($volume + $purchase->ts_volume, 2);
                            $payable = round($payable + ($purchase->ts_volume * $basePrice->price), 2);
                            $temp = array();
                            $temp['id'] = $purchase->id;
                            $temp['level'] = 'Supplier Level';
                            $temp['base_price'] = $basePrice->price;
                            array_push($purchases, $temp);
                        }
                    }
                }
                if ($volume <> 0) {
                    $payment['supplier_id'] = $supplier->id;
                    $payment['supplier_type_id'] = $supplier->supplier_type_id;
                    $payment['is_payment_active'] = ($supplier->payment_process == null) ? (int) 0 : $supplier->payment_process;
                    $payment['total_ts_volume'] = $volume;
                    $payment['payable_without_incentives'] = $payable;
                    $payment['payable'] = round($payable + $volumeIncentive['total_volume_incentive'] + $chillingIncentive['total_chilling_incentive'], 2); // also add volume incentive
                    $payment['base_price'] = $basePrice->price;
                    $payment['incentives']['volume'] = $volumeIncentive;
                    $payment['incentives']['chilling'] = $chillingIncentive;
                    $payment['purchases'] = $purchases;
                    $payment['created_at'] = gmdate('c');
                    $payment['updated_at'] = gmdate('c');
                    array_push($payments, $payment);
                }
            }
            //if still keys are not set in payment array then 5th level pricing source type and fall back option for payment calculation
            if (empty($payment)) {
                $basePrice = $basePricing->where('source_type', $supplier->supplier_type_id)
                    ->where('collection_point', null)
                    ->where('supplier', '=', null)
                    ->sortByDesc('wef')
                    ->first();
                if ($basePrice <> null) {
                    $volume = round($supplier->purchases->sum('ts_volume'), 2);
                    $payment['supplier_id'] = $supplier->id;
                    $payment['supplier_type_id'] = $supplier->supplier_type_id;
                    $payment['is_payment_active'] = ($supplier->payment_process == null) ? (int) 0 : $supplier->payment_process;
                    $payment['total_ts_volume'] = $volume;
                    $payment['payable_without_incentives'] = round($volume * $basePrice->price, 2);
                    $payment['payable'] = round($volume * $basePrice->price + $volumeIncentive['total_volume_incentive'] + $chillingIncentive['total_chilling_incentive'], 2);
                    $payment['base_price'] = $basePrice->price;
                    $payment['incentives']['volume'] = $volumeIncentive;
                    $payment['incentives']['chilling'] = $chillingIncentive;
                    $purchase_ids = $supplier->purchases->pluck('id')->toArray();
                    $purchases = array();
                    //make purchase ids object
                    foreach ($purchase_ids as $id) {
                        $temp = array();
                        $temp['id'] = $id;
                        $temp['level'] = 'Source Type Level';
                        $temp['base_price'] = $basePrice->price;
                        array_push($purchases, $temp);
                    }
                    $payment['purchases'] = $purchases;
                    $payment['created_at'] = gmdate('c');
                    $payment['updated_at'] = gmdate('c');
                    array_push($payments, $payment);
                } else
                    return 'No Pricing found against Supplier:' . $supplier->name . '';
            }
        }
        return $payments;
    }
}
