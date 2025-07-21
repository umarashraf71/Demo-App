<?php

namespace App\Http\Controllers;

use App\Models\AreaOffice;
use Illuminate\Support\Carbon;
use App\Models\CollectionPoint;
use App\Models\MilkPurchase;
use App\Models\User;
use MongoDB\BSON\UTCDateTime;
use App\Models\Categories;
use App\Models\Bank;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\InventoryItem;
use App\Models\SupplierType;
use App\Models\Supplier;
use App\Models\Incentive;

class DashboardController extends Controller
{
    public function index()
    {
        return view('content.dashboard.home');
    }

    public function dashboardAnalytics()
    {
        $pageConfigs = ['pageHeader' => false];
        $areaOffices = AreaOffice::select('id', 'name')->get();
        $milkPurchases = $this->getBalances($areaOffices);
        $milkPurchasesAtDate = $this->getBalancesAtDate($areaOffices);
        $data = $this->getAreaOfficesAndtotalMilkPurchases($areaOffices);
        $activeUsers = User::where('status', 1)->count();
        $inActiveUsers = User::where('status', '!=', 1)->count();


        return view('content.dashboard.dashboard-analytics', compact('pageConfigs', 'milkPurchases', 'milkPurchasesAtDate', 'data', 'areaOffices', 'activeUsers', 'inActiveUsers'));
    }

    private function getBalances($areaOffices)
    {
        $milkPurchases = collect();
        foreach ($areaOffices as $areaOffice) {
            $areaOfficeGrossVolume = MilkPurchase::where([
                ['area_office_id', $areaOffice->id],
                ['type', 'purchase_at_ao'],
            ])
                ->sum('gross_volume');
            $users = User::whereIn('access_level_ids', [$areaOffice->id])->get();
            $mmt = 0;
            foreach ($users as $user) {
                $mmt = $mmt + MilkPurchase::where([
                    ['created_by', $user->id],
                    ['type', 'mmt_purchase']
                ])->sum('gross_volume');
            }
            $collection_points = CollectionPoint::where('area_office_id', $areaOffice->id)->get();
            $mcc = 0;
            foreach ($collection_points as $collection_point) {
                $mcc = $mcc + MilkPurchase::where('mcc_id', $collection_point->id)->sum('gross_volume');
            }
            $milkPurchases->push([
                'area_office_id' => $areaOffice->name,
                'areaOfficeGrossVolume' => $areaOfficeGrossVolume,
                'mmt' => $mmt,
                'mcc' => $mcc
            ]);
        }
        return $milkPurchases;
    }

    private function getBalancesAtDate($areaOffices)
    {
        $currentDate = now()->startOfDay();
        $milkPurchasesAtDate = collect();
        foreach ($areaOffices as $areaOffice) {
            $areaOfficeGrossVolume = MilkPurchase::where([
                ['area_office_id', $areaOffice->id],
                ['type', 'purchase_at_ao'],
                ['created_at', '>=', $currentDate],
                ['created_at', '<', $currentDate->copy()->addDay()]
            ])->sum('gross_volume');

            $users = User::whereIn('access_level_ids', [$areaOffice->id])->get();
            $mmt = 0;
            foreach ($users as $user) {
                $mmt = $mmt + MilkPurchase::where([
                    ['created_by', $user->id],
                    ['type', 'mmt_purchase'],
                    ['created_at', '>=', $currentDate],
                    ['created_at', '<', $currentDate->copy()->addDay()]
                ])->sum('gross_volume');
            }

            $collection_points = CollectionPoint::where('area_office_id', $areaOffice->id)->get();
            $mcc = 0;
            foreach ($collection_points as $collection_point) {
                $mcc = $mcc + MilkPurchase::where([
                    ['mcc_id', $collection_point->id],
                    ['created_at', '>=', $currentDate],
                    ['created_at', '<', $currentDate->copy()->addDay()]
                ])->sum('gross_volume');
            }

            $milkPurchasesAtDate->push([
                'area_office_id' => $areaOffice->name,
                'areaOfficeGrossVolume' => $areaOfficeGrossVolume,
                'mmt' => $mmt,
                'mcc' => $mcc
            ]);
        }
        return $milkPurchasesAtDate;
    }

    private function getAreaOfficesAndtotalMilkPurchases($areaOffices)
    {
        $areaOfficePurchases = collect();
        $totalMilkPurchasesGrossVolume = collect();
        for ($i = 0; $i < 7; $i++) {
            $startDate = Carbon::now()->subDays($i)->startOfDay();
            $endDate = Carbon::now()->subDays($i)->endOfDay();

            $startDateMongo = new UTCDateTime($startDate->getTimestamp() * 1000);
            $endDateMongo = new UTCDateTime($endDate->getTimestamp() * 1000);

            $areaOfficeGrossVolume = array();
            $areaOfficeNames = array();
            foreach ($areaOffices as $areaOffice) {
                $totalGrossVolumeAreaOffice = MilkPurchase::where('area_office_id', $areaOffice->id)
                    ->whereBetween('created_at', [$startDateMongo, $endDateMongo])
                    ->sum('gross_volume');
                array_push($areaOfficeGrossVolume, $totalGrossVolumeAreaOffice);
                array_push($areaOfficeNames, $areaOffice->name);
            }
            $areaOfficePurchases->push([
                'date' => $startDate->format('d-M'),
                'areaOfficeGrossVolume' => $areaOfficeGrossVolume,
                'areaOfficeNames' => $areaOfficeNames
            ]);
            $totalMilkPurchasesGrossVolume->push([
                'totalVolume' => MilkPurchase::whereBetween('created_at', [$startDateMongo, $endDateMongo])
                    ->sum('gross_volume'),
                'date' => $startDate->format('d-M')
            ]);
        }
        $data['areaOfficePurchases'] = $areaOfficePurchases;
        $data['totalMilkPurchasesGrossVolume'] = $totalMilkPurchasesGrossVolume;
        return $data;
    }

    public function areaOfficeDailyRecord($officeId)
    {
        $areaOffice = AreaOffice::where('_id', $officeId)->first();
        $milkPurchases = collect();

        for ($i = 0; $i < 7; $i++) {
            $startDate = Carbon::now()->subDays($i)->startOfDay();
            $endDate = Carbon::now()->subDays($i)->endOfDay();

            $startDateMongo = new UTCDateTime($startDate->getTimestamp() * 1000);
            $endDateMongo = new UTCDateTime($endDate->getTimestamp() * 1000);

            $totalGrossVolume = MilkPurchase::where('area_office_id', $areaOffice->id)
                ->whereBetween('created_at', [$startDateMongo, $endDateMongo])
                ->sum('gross_volume');

            $milkPurchases->push([
                'date' => $startDate->toDateString(),
                'total_gross_volume' => $totalGrossVolume,
            ]);
        }

        return response()->json($milkPurchases);
    }

    // Dashboard - Ecommerce
    public function dashboardEcommerce()
    {
        $pageConfigs = ['pageHeader' => false];

        return view('/content/dashboard/dashboard-ecommerce', ['pageConfigs' => $pageConfigs]);
    }
    public function lacto()
    {
        $values = $this->readCSV('lact.csv', array('delimiter' => ','));
        $mainarray = array();
        $temparray = array();

        foreach ($values as $key => $value) {
            if ($key == 0)
                continue;
            for ($i = 1; $i <= 22; $i++) {
                $temparray['reading'] = (float) $values[0][$i];
                $temparray['temp'] = (float) $value[0];
                $temparray['value'] = (float) $value[$i];
                array_push($mainarray, $temparray);
            }
            if ($key == 37)
                break;
        }
        $res = \App\Models\LactometerChart::insert($mainarray);
        dd($res);
    }
    public function importCollectionpoints()
    {
        //collection points script
        $rows = $this->readCSV('public/bhowana_collection_points.csv', array('delimiter' => ','));
        $header = array_shift($rows);
        $csv    = array();

        foreach ($rows as $key => $row) {
            if ($key == 69)
                break;
            $csv[] = array_combine($header, $row);
        }

        foreach ($csv as $key => $singleRow) {

            try {
                $singleRow['area_office_id'] = '63c798a3b57d0000ef002092';
                $singleRow['name'] = $singleRow['name'];
                $singleRow['address'] = $singleRow['address'];
                $singleRow['latitude'] = $singleRow['latitude'];
                $singleRow['longitude'] = $singleRow['longitude'];
                $singleRow['meter_no'] = $singleRow['meter_no'];
                $singleRow['phase'] = $singleRow['meter_phases'];
                $singleRow['meter_owner_name'] = $singleRow['meter_owner_name'];
                $cat = $singleRow['category_id'];
                $singleRow['category_id'] = Categories::where('category_name', $singleRow['category_id'])->pluck('_id')->first();
                $singleRow['district_id'] = trim($singleRow['district_id']);
                $singleRow['district_id'] = District::where('name', $singleRow['district_id'])->pluck('_id')->first();
                $singleRow['tehsil_id'] = Tehsil::where('name', $singleRow['tehsil_id'])->pluck('_id')->first();
               
                if ($cat == 'Self') {
                    //prepare bank data
                    $singleRow['bank_id'] = Bank::where('short_name', $singleRow['bank_name'])->pluck('_id')->first();
                    $singleRow['is_mcc'] = '1';
                    //bank
                    $singleRow['bank_address'] = $singleRow['bank_branch_name'];
                    //prepare owner data
                    $singleRow['owner_contact'] = str_replace("-", "", $singleRow['owners.contact']);
                    $singleRow['owner_contact'] = preg_replace('/' . '0' . '/', '+92', $singleRow['owner_contact'], 1);

                    $singleRow['owner_wp'] = str_replace("-", "", $singleRow['owners.whatsapp']);
                    $singleRow['owner_wp'] = preg_replace('/' . '0' . '/', '+92', $singleRow['owner_wp'], 1);
                    $ownersMainarray = array();
                    $owners['name'] = $singleRow['owners.name'];
                    $owners['father_name'] = $singleRow['owners.father_name'];
                    $owners['cnic'] = str_replace("-", "", $singleRow['owners.cnic']);
                    $owners['ntn'] = str_replace("-", "", $singleRow['owners.ntn']);
                    $owners['contact'] = $singleRow['owner_contact'];
                    $owners['owner_whatsapp'] = $singleRow['owner_wp'];
                    if ($singleRow['owners.with_effective_date'] <> "")
                        $owners['with_effective_date'] = Carbon::createFromFormat('d-M-y', $singleRow['owners.with_effective_date'])->format('Y-m-d');
                    $owners['status'] = 1;
                    array_push($ownersMainarray, $owners);
                    $singleRow['owners'] = $ownersMainarray;

                    //prepare agreement data
                    $agreementMainarray = array();
                    if ($singleRow['agreements.from'] <> "")
                        $agreement['from'] = Carbon::createFromFormat('d-M-y', $singleRow['agreements.from'])->format('Y-m-d');
                    if ($singleRow['agreements.to'] <> "")
                        $agreement['to'] =   Carbon::createFromFormat('d-M-y', $singleRow['agreements.to'])->format('Y-m-d');
                    if ($singleRow['agreements.wef'] <> "")
                        $agreement['wef'] = Carbon::createFromFormat('d-M-y', $singleRow['agreements.wef'])->format('Y-m-d');
                    $agreement['rent'] = $singleRow['agreements.rent'];
                    $agreement['status'] = 1;
                    array_push($agreementMainarray, $agreement);
                    $singleRow['agreements'] = $agreementMainarray;

                    unset(
                        $singleRow['bank_name'],
                        $singleRow['shop_owner_name'],
                        $singleRow['owner_contact'],
                        $singleRow['owner_wp'],
                        $singleRow['owners.name'],
                        $singleRow['owners.father_name'],
                        $singleRow['owners.cnic'],
                        $singleRow['owners.ntn'],
                        $singleRow['owners.contact'],
                        $singleRow['owners.whatsapp'],
                        $singleRow['agreements.from'],
                        $singleRow['agreements.to'],
                        $singleRow['agreements.wef'],
                        $singleRow['agreements.refrence_no'],
                        $singleRow['agreements.rent'],
                        $singleRow['shop_rent'],
                        $singleRow['meter_phases'],
                        $singleRow['is_Self']
                    );
                } else {
                    $singleRow['is_mcc'] = '0';
                    unset(
                        $singleRow['bank_name'],
                        $singleRow['owners.name'],
                        $singleRow['owners.father_name'],
                        $singleRow['owners.cnic'],
                        $singleRow['owners.ntn'],
                        $singleRow['owners.contact'],
                        $singleRow['owners.whatsapp'],
                        $singleRow['agreements.from'],
                        $singleRow['agreements.to'],
                        $singleRow['agreements.wef'],
                        $singleRow['agreements.refrence_no'],
                        $singleRow['agreements.rent'],
                        $singleRow['shop_rent'],
                        $singleRow['meter_no'],
                        $singleRow['meter_phases'],
                        $singleRow['bank_branch_name'],
                        $singleRow['meter_owner_name'],
                        $singleRow['shop_owner_name'],
                        $singleRow['is_Self'],
                        $singleRow['bank_account_no'],
                        $singleRow['bank_account_title'],
                        $singleRow['bank_branch_code'],
                        $singleRow['bank_address'],
                        $singleRow['phase'],
                    );
                }
                $singleRow['status'] = 1;

                // CollectionPoint::create($singleRow);
            } catch (\Throwable $th) {
                dd($th->getMessage(), $singleRow);
            }
        }
        dd('success', $singleRow);
    }
    public function importInventoryitems()
    {
        //collection points script
        $rows = $this->readCSV('public/bhowana_inventory_items.csv', array('delimiter' => ','));
        $header = array_shift($rows);
        $csv    = array();

        foreach ($rows as $key => $row) {
            if ($key == 144)
                break;
            $csv[] = array_combine($header, $row);
        }

        foreach ($csv as $key => $singleRow) {
            try {
                $singleRow['name'] = $singleRow['tag_number'];
                $singleRow['area_office_id'] = '63c798a3b57d0000ef002092';
                if ($singleRow['item_type'] == 'Generator')
                    $singleRow['item_type'] = '64196e50e943000002003c0b';
                elseif ($singleRow['item_type'] == 'Chiller')
                    $singleRow['item_type'] = '64196e60e943000002003c0c';

                if ($singleRow['nature_of_asset'] == 'Fixed')
                    $singleRow['nature_of_asset'] = '1';
                elseif ($singleRow['nature_of_asset'] == 'Revenue')
                    $singleRow['nature_of_asset'] = '2';

                $singleRow['status'] = (int) 1;

                // InventoryItem::create($singleRow);
            } catch (\Throwable $th) {
                dd($th->getMessage(), $singleRow);
            }
        }
        dd('success');
    }
    public function importSuppliers()
    {
        // $rows = $this->readCSV('Incentives.csv', array('delimiter' => ','));
        // $header = array_shift($rows);
        // $csv    = array();
       
        // foreach ($rows as $key => $row) {
        //     if ($key == 14)
        //         break;
        //     $csv[] = array_combine($header, $row);
        // }
        // foreach ($csv as $row) {
        //     $array = array();
        //     $array['incentive_type'] = '642baf9a674400009e003ac3';
        //     $array['source_type'] = '63b55d6c781e0000b4000f07';
        //     $array['amount'] = $row['TPC'];
        //     $array['range'] = "".$row['From']." - ".$row['To']."";
        //     $array['from'] = (int) $row['From'];
        //     $array['to'] = (int) $row['To'];
        //     $array['status'] = (int) 1;
        //     Incentive::create($array);
        // }
        // dd('done');
        // $milkPurchases = MilkPurchase::where('type', 'mmt_purchase')->get();
        // foreach ($milkPurchases as $key => $milkPurchase) {
        //     $time = Carbon::createFromFormat('Y-m-d H:i:s', $milkPurchase->getAttributes()['time']);
        //     $startOfday = Carbon::createFromFormat('Y-m-d H:i:s', $milkPurchase->getAttributes()['time'])->startOfDay();
        //     $dayChangetime = Carbon::createFromFormat('Y-m-d H:i:s', $milkPurchase->getAttributes()['time'])->startOfDay()->addHours(16);
        //     if($time->between($startOfday, $dayChangetime))
        //     {
        //         $milkPurchase->booked_at = $time->toDateString();
        //         $milkPurchase->save();
        //     } 
        //     else {
        //         $milkPurchase->booked_at = $time->addDay(+1)->toDateString();
        //         $milkPurchase->save();
        //     }
        // }
        // dd('done');
        // $suppliers = Supplier::get();
        // foreach ($suppliers as $key => $supplier) {
        //     $supplier->code = (int) $key + 1;
        //     $supplier->save();
        // }
        // dd('here');
        // $mrs = \App\Models\MilkReception::whereIn('type',['mmt_reception','ao_lab_reception'])->get();
        // foreach ($mrs as $key => $mr) {
        //     $res = $mr->forceDelete();
        // }
        // dd($res);


        // $mps = MilkPurchase::whereIn('type',['purchase_at_mcc','mmt_purchase','purchase_at_ao'])->get();
        // foreach ($mps as $key => $mp) {
        //     $res = $mp->forceDelete();
        // }
        // dd($res);

        // $sps = Supplier::get();
        // $ids = array();
        // foreach ($sps as $key => $sp) {
        //     if(empty($sp->purchases->toArray()))
        //     $sp->forceDelete();
        // }
        // dd($ids);
        //suppliers script
        // $rows = $this->readCSV('public/bhowana_suppliers.csv', array('delimiter' => ','));
        // $header = array_shift($rows);
        // $csv    = array();
     
        // foreach ($rows as $key => $row) {
        //     if ($key == 67)
        //         break;
        //     $csv[] = array_combine($header, $row);
        // }

        // foreach ($csv as $key => $singleRow) {
        //     try {
        //     $supplier_type_id = SupplierType::where('name', $singleRow['supplier_type_id'])->first();
        //     $singleRow['supplier_type_id'] = $supplier_type_id->id;
        //     $singleRow['contact'] = str_replace("-", "", $singleRow['contact']);
        //     $singleRow['contact'] = preg_replace('/' . '0' . '/', '+92', $singleRow['contact'], 1);

        //     // $singleRow['whatsapp'] = str_replace("-", "", $singleRow['whatsapp']);
        //     // $singleRow['whatsapp'] = preg_replace('/' . '0' . '/', '+92', $singleRow['whatsapp'], 1);

        //     $singleRow['cnic'] = str_replace("-", "", $singleRow['cnic']);
        //     $singleRow['bank_id'] = Bank::where('short_name', $singleRow['bank_name'])->pluck('_id')->first();
        //     $singleRow['district_id'] = District::where('name', $singleRow['district_id'])->pluck('_id')->first();
        //     $singleRow['tehsil_id'] = Tehsil::where('name', $singleRow['tehsil_id'])->pluck('_id')->first();
        //     $singleRow['next_of_kin_contact'] = str_replace("-", "", $singleRow['next_of_kin_contact']);
        //     $singleRow['next_of_kin_contact'] = preg_replace('/' . '0' . '/', '+92', $singleRow['next_of_kin_contact'], 1);
        //     $singleRow['ntn'] = str_replace("-", "", $singleRow['ntn']);
        //     //prepare agreement data
        //     $agreementMainarray = array();
        //     if ($singleRow['agreements.from'] <> '')
        //         $agreement['from'] = Carbon::createFromFormat('d-M-y', $singleRow['agreements.from'])->format('Y-m-d');
        //     if ($singleRow['agreements.to'] <> '')
        //         $agreement['to'] =   Carbon::createFromFormat('d-M-y', $singleRow['agreements.to'])->format('Y-m-d');
        //     if ($singleRow['agreements.effective_from'] <> '')
        //         $agreement['effective_from'] = Carbon::createFromFormat('d-M-y', $singleRow['agreements.effective_from'])->format('Y-m-d');
        //     $agreement['status'] = 1;
        //     array_push($agreementMainarray, $agreement);
        //     $singleRow['agreements'] = $agreementMainarray;

        //     //collection point association
        //     $cp_ids = array();
        //     $cps = $singleRow['cp_ids'];
        //     array_push($cp_ids, CollectionPoint::where('name', $cps)->pluck('_id')->first());

        //     unset($singleRow['agreements.from'], $singleRow['agreements.to'], $singleRow['agreements.effective_from']);
        //     $singleRow['status'] = 1;

        //     unset($singleRow['bank_name'], $singleRow['cp_ids']);
        //     // $supplier_id = Supplier::create($singleRow);

        //     //associations according to delivery configuration
        //     if ($supplier_type_id->delivery_config['at_mcc'] == 1) {
        //         if (!empty(array_filter($cp_ids, function ($a) {
        //             return $a !== null;
        //         }))) {
        //             $supplier_id->mcc  = $cp_ids;
        //             $supplier_id->save();
        //             foreach ($cp_ids as $cp) {
        //                 $record = CollectionPoint::where('_id', $cp)->first();
        //                 $record->push('supplier_ids', $supplier_id->id, true);
        //             }
        //         }
        //     } elseif ($supplier_type_id->delivery_config['at_area_office'] == 1) {
        //         $supplier_id->area_office = '63c798a3b57d0000ef002092';
        //         if (!empty(array_filter($cp_ids, function ($a) {
        //             return $a !== null;
        //         }))) {
        //             $supplier_id->cp_ids = $cp_ids;
        //             foreach ($cp_ids as $cp) {
        //                 $record = CollectionPoint::where('_id', $cp)->first();
        //                 $record->push('supplier_ids', $supplier_id->id, true);
        //             }
        //         }
        //         $supplier_id->save();
        //     } elseif ($supplier_type_id->delivery_config['at_plant'] == 1 || $supplier_type_id->delivery_config['by_plant'] == 1) {
        //         $supplier_id->plant = '63907be6b32f0000000031d8';
        //         $supplier_id->save();
        //     }
        //     } catch (\Throwable $th) {

        //         dd($th->getMessage(),$singleRow);
        //     }
        // }
        // dd('success', $singleRow);
    }
    public function readCSV($csvFile, $array)
    {
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        fclose($file_handle);
        return $line_of_text;
    }
}
