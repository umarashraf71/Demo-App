<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use App\Models\MilkDispatch;
use App\Models\GatePassToken;
use App\Models\MilkReception;
use App\Models\Plant;
use App\Models\PurchasedMilkRejection;
use App\Models\MilkPurchase;
use App\Models\MilkRejection;

class PlantController extends Controller
{
    use HttpResponseTrait;

    public function GeneratetokenNumber(Request $request)
    {
        $user = auth()->user('api');
        $vehicle_id = $request->vehicle_id;
        $delivery_challan = $request->delivery_challan;
        $gatePasstoken = new GatePassToken();
        if ($vehicle_id <> null) {
            $milkDispatches = MilkDispatch::where('vehicle_id', $vehicle_id)->where('gate_pass_token_id', 'exists', false)->get();
            if ($milkDispatches->pluck('gate_pass_token_id')->toArray() == null)
                return $this->fail('No milk dispatches found against vehicle ID');
            elseif (GatePassToken::where('gate_out_date_time', 'exists', false)->where('vehicle_id', $vehicle_id)->exists())
                return $this->fail('Token number is already generated');


            $gatePasstoken->gross_volume = $milkDispatches->sum('gross_volume');
            $gatePasstoken->volume_ts = $milkDispatches->sum('volume_ts');
            $gatePasstoken->vehicle_id = $vehicle_id;
            $gatePasstoken->milk_dispatch_ids = $milkDispatches->pluck('_id')->toArray();
            $gatePasstoken->type = 'dispatched';
        } else if ($delivery_challan <> null) {
            $gatePasstoken->delivery_challan = $delivery_challan;
        }
        $gatePasstoken->created_by = $user->id;
        $gatePasstoken->date_time_in = date('Y-m-d H:i:s');
        $gatePasstoken->remarks = $request->remarks;
        $gatePasstoken->type = 'purchased';
        $gatePasstoken->plant_id = $request->plant_id;
        $gatePasstoken->save();

        //update token id to dispatches 
        if ($vehicle_id <> null)
            foreach ($milkDispatches as $key => $milkDispatch) {
                $milkDispatch->update(['gate_pass_token_id' => $gatePasstoken->id]);
            }

        $data = ['id' => $gatePasstoken->id];

        return $this->response($data, true, 'Token number Generated');
    }
    public function getTokens()
    {
        $data['tokens'] = GatePassToken::where('gate_out_date_time', 'exists', false)
            ->with(array('vehicle' => function ($q) {
                $q->select('_id', 'vehicle_number', 'compartments');
            }))
            ->get();
        return $this->response($data, true, '');
    }
    public function plantReceptionqa(Request $request)
    {
        $user = auth()->user('api');
        $token = $request->id;
        $compartment = $request->compartment;
        if ($token == null)
            return $this->fail('Token id is required');

        //load token model
        $gatePasstoken = GatePassToken::find($token);
        if ($gatePasstoken->vehicle_id == null && $gatePasstoken->delivery_challan <> null) {
            $res = $this->plantReceptionPurchase($request, $gatePasstoken, $user, $compartment);
            if ($res)
                return $this->response(null, true, 'Purchase is created with general tests');
            else
                return $this->fail('Something went wrong purchase is not created');
        }
        //if general compartment create reception entry and save data and also add weight 
        if ($compartment == 0) {
            $area_office_ids = $gatePasstoken->milkDispatches->pluck('area_office_id')->toArray();
            $mmt_ids = $gatePasstoken->milkDispatches->pluck('mmt_id')->toArray();
            $mr = new MilkReception;
            $mr->compartments = (int) $request->compartments;
            $mr->created_by = $user->id;
            $mr->type = 'plant_reception';
            $mr->area_office_ids = (!empty(array_filter($area_office_ids, function ($a) {
                return $a !== null;
            }))) ? $area_office_ids : null;
            $mr->mmt_ids = (!empty(array_filter($mmt_ids, function ($a) {
                return $a !== null;
            }))) ? $area_office_ids : null;
            $mr->gross_volume = 0;
            $mr->left_over_milk = 0;
            $mr->opening_balance = 0;
            $mr->opening_balance1 = 0;
            $mr->volume_ts = 0;
            $mr->gain_loss = 0;
            $mr->gate_pass_token_id = $token;
            $mr->tests = $request->tests;
            $mr->compartment_status_0 = (int) $request->compartment_status;
            $mr->date = date('Y-m-d H:i:s');
            $mr->isGeneralOnly = (int) $request->isGeneralOnly;
            $mr->plant_id = $gatePasstoken->plant_id;
            $mr->save();
            return $this->response(null, true, 'Reception is created with general tests');
        } elseif ($compartment <> 0) {
            $mr = MilkReception::where('gate_pass_token_id', $token)->get()->first();
            if ($mr == null)
                return $this->fail('Milk reception not found against token number');

            $mr->push('tests', $request->tests);
            if ($compartment == 1)
                $mr->compartment_status_1 = (int) $request->compartment_status;
            elseif ($compartment == 2)
                $mr->compartment_status_2 = (int) $request->compartment_status;
            elseif ($compartment == 3)
                $mr->compartment_status_3 = (int) $request->compartment_status;

            $mr->save();
            return $this->response(null, true, 'QA is updated against milk reception');
        }
    }
    public static function plantReceptionPurchase($request, $gatePasstoken, $user, $compartment)
    {
        try {
            //if general compartment create reception entry and save data and also add weight 
            if ($compartment == 0) {
                $mp = new MilkPurchase();
                $mp->created_by = $user->id;
                $mp->plant_id = $gatePasstoken->plant_id;
                $mp->type = 'purchase_at_plant';
                $mp->supplier_id = $request->supplier_id;
                $mp->gross_volume = 0;
                $mp->ts_volume = 0;
                // Actual purchase Time
                $mp->time = date('Y-m-d H:i:s');
                $mp->compartments = (int) $request->compartments;
                $mp->compartment = (int) $request->compartment;
                $mp->gate_pass_token_id = $gatePasstoken->id;
                $mp->tests = $request->tests;
                $mp->compartment_status_0 = (int) $request->compartment_status;
                $mp->date = date('Y-m-d H:i:s');
                $mp->isGeneralOnly = (int) $request->isGeneralOnly;
                $plant = Plant::find($gatePasstoken->plant_id);
                $mp->opening_balance = ($plant->balance == null) ? 0 : $plant->balance;
                $mp->save();
                return true;
            } elseif ($compartment <> 0) {
                $mp = MilkPurchase::where('gate_pass_token_id', $gatePasstoken->id)->get()->first();
                if ($mp == null)
                    return false;

                $mp->push('tests', $request->tests);
                if ($compartment == 1)
                    $mp->compartment_status_1 = (int) $request->compartment_status;
                elseif ($compartment == 2)
                    $mp->compartment_status_2 = (int) $request->compartment_status;
                elseif ($compartment == 3)
                    $mp->compartment_status_3 = (int) $request->compartment_status;

                $mp->save();
                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function plantReceptionweight(Request $request)
    {
        $user = auth()->user('api');
        $token = $request->id;
        $compartment = $request->compartment;
        if ($token == null)
            return $this->fail('Token id is required');

        $mr = MilkReception::where('gate_pass_token_id', $token)->get()->first();
        $mr->updated_by = $user->id;
        //if general compartment then whole vehicle filled weight and empty weight 
        if ($compartment == 0) {
            $mr->total_vehicle_weight = (float) $request->total_vehicle_weight;
            if ($request->total_empty_vehicle_weight <> null) {
                $mr->total_empty_vehicle_weight = $request->total_empty_vehicle_weight;
                $mr->total_milk_weight_kgs = round($request->total_vehicle_weight - $request->total_empty_vehicle_weight, 2);
                //if milk is accepted convert to liter 
                //calculate specific gravity ----formula 1+(LR/1000)
                $tests = collect($mr->tests);
                $lr = $tests->where('qa_test_name', 'LR')->where('compartment', 0)->pluck('value')->first();
                $specific_gravity = 1 + ($lr / 1000);
                $mr->gross_volume = round($mr->total_milk_weight_kgs / $specific_gravity, 2);
                //updated plant balance and opening balance
                $plant = Plant::find($mr->plant_id);
                $mr->left_over_milk = 0;
                $mr->opening_balance = ($plant->balance == null) ? 0 : $plant->balance;
                $mr->opening_balance_compartment_0 = ($plant->balance == null) ? 0 : $plant->balance;
                $plant->balance = round($plant->balance + $mr->gross_volume, 2);
                $mr->opening_balance1 = 0;
                //calculate volume ts 
                $fat = $tests->where('qa_test_name', 'Fat')->where('compartment', 0)->pluck('value')->first();
                $snf = $tests->where('qa_test_name', 'SNF')->where('compartment', 0)->pluck('value')->first();
                $ts = $fat + $snf;
                $mr->volume_ts = round(($mr->gross_volume * $ts) / 13, 2);
                //calculate gain loss
                if ($mr->isGeneralOnly == 1) {
                    $gatePasstoken = GatePassToken::find($token);
                    $mr->gain_loss = round($gatePasstoken->gross_volume - $mr->gross_volume, 2);
                }
                if ($mr->compartment_status_0 == 1)
                    $plant->save();
                $mr->save();
                //milk rejection
                if ($mr->compartment_status_0 == 0) {
                    $PurchasedMilkRejection = new PurchasedMilkRejection();
                    $PurchasedMilkRejection->plant_id = $plant->id;
                    $PurchasedMilkRejection->compartment = $compartment;
                    $PurchasedMilkRejection->created_by = $user->id;
                    $PurchasedMilkRejection->type = 'plant_reception';
                    $PurchasedMilkRejection->token_id = $token;
                    $PurchasedMilkRejection->total_vehicle_weight = (float) $request->total_vehicle_weight;
                    $PurchasedMilkRejection->total_milk_weight_kgs = $mr->total_milk_weight_kgs;
                    $PurchasedMilkRejection->tests = $tests;
                    $PurchasedMilkRejection->opening_balance = $mr->opening_balance;
                    $PurchasedMilkRejection->opening_balance_compartment_0 = $mr->opening_balance_compartment_0;
                    $PurchasedMilkRejection->gain_loss = $mr->gain_loss;
                    //calculate specific gravity ----formula 1+(LR/1000)
                    $PurchasedMilkRejection->gross_volume = round($mr->gross_volume, 2);
                    //calculate volume ts 
                    $PurchasedMilkRejection->volume_ts = round($mr->volume_ts, 2);
                    $PurchasedMilkRejection->date = date('Y-m-d H:i:s');
                    $PurchasedMilkRejection->save();
                }
            } else {
                $mr->save();
            }
            if ($mr->compartment_status_0 == 1)
                return $this->response(null, true, 'Calculations are performed and balance is added');
            elseif ($mr->compartment_status_0 == 0)
                return $this->response(null, true, 'Calculations are performed and milk is rejected and rejection is entered in to the system');
        } elseif ($compartment == 1) {
            if ($request->total_vehicle_weight_after_c1 == null)
                return $this->response(null, true, 'Compartment 1 weight is required');
            $mr->total_vehicle_weight_after_c1 = (float) $request->total_vehicle_weight_after_c1;
            $mr->total_milk_weight_kgs = round($mr->total_vehicle_weight - $request->total_vehicle_weight_after_c1, 2);
            //calculate specific gravity ----formula 1+(LR/1000)
            $tests = collect($mr->tests);
            $lr = $tests->where('qa_test_name', 'LR')->where('compartment', 1)->pluck('value')->first();
            $specific_gravity = 1 + ($lr / 1000);
            $mr->gross_volume = round($mr->total_milk_weight_kgs / $specific_gravity, 2);
            //updated plant balance and opening balance
            $plant = Plant::find($mr->plant_id);
            $mr->left_over_milk = 0;
            $mr->opening_balance = ($plant->balance == null) ? 0 : $plant->balance;
            $mr->opening_balance_compartment_1 = ($plant->balance == null) ? 0 : $plant->balance;
            $plant->balance = round($plant->balance + $mr->gross_volume, 2);
            $mr->opening_balance1 = 0;
            //calculate volume ts 
            $fat = $tests->where('qa_test_name', 'Fat')->where('compartment', 1)->pluck('value')->first();
            $snf = $tests->where('qa_test_name', 'SNF')->where('compartment', 1)->pluck('value')->first();
            $ts = $fat + $snf;
            $mr->volume_ts = round(($mr->gross_volume * $ts) / 13, 2);
            //calculate gain loss
            if ($mr->compartments == 1) {
                $gatePasstoken = GatePassToken::find($token);
                $mr->gain_loss = round($gatePasstoken->gross_volume - $mr->gross_volume, 2);
            }
            if ($mr->compartment_status_1 == 1)
                $plant->save();
            $mr->save();
            //milk rejection
            if ($mr->compartment_status_1 == 0) {
                $PurchasedMilkRejection = new PurchasedMilkRejection();
                $PurchasedMilkRejection->plant_id = $plant->id;
                $PurchasedMilkRejection->compartment = $compartment;
                $PurchasedMilkRejection->created_by = $user->id;
                $PurchasedMilkRejection->type = 'plant_reception';
                $PurchasedMilkRejection->token_id = $token;
                $PurchasedMilkRejection->total_vehicle_weight = (float) $mr->total_vehicle_weight;
                $PurchasedMilkRejection->total_milk_weight_kgs = $mr->total_milk_weight_kgs;
                $PurchasedMilkRejection->total_vehicle_weight_after_c1 = (float) $mr->total_vehicle_weight_after_c1;
                $PurchasedMilkRejection->tests = $tests;
                $PurchasedMilkRejection->opening_balance = $mr->opening_balance;
                $PurchasedMilkRejection->opening_balance_compartment_1 = $mr->opening_balance_compartment_1;
                $PurchasedMilkRejection->gain_loss = $mr->gain_loss;
                //calculate specific gravity ----formula 1+(LR/1000)
                $PurchasedMilkRejection->gross_volume = round($mr->gross_volume, 2);
                //calculate volume ts 
                $PurchasedMilkRejection->volume_ts = round($mr->volume_ts, 2);
                $PurchasedMilkRejection->date = date('Y-m-d H:i:s');
                $PurchasedMilkRejection->save();
            }
            if ($mr->compartment_status_1 == 1)
                return $this->response(null, true, 'Calculations are performed and compartment 1 balance is added');
            elseif ($mr->compartment_status_1 == 0)
                return $this->response(null, true, 'Calculations are performed and compartment 1 milk is rejected and rejection is entered in to the system');
        } elseif ($compartment == 2) {
            if ($request->total_vehicle_weight_after_c2 == null)
                return $this->response(null, true, 'Compartment 2 weight is required');
            $mr->total_vehicle_weight_after_c2 = (float) $request->total_vehicle_weight_after_c2;
            $c2_milk_weight = round($mr->total_vehicle_weight_after_c1 - $request->total_vehicle_weight_after_c2, 2);
            $mr->total_milk_weight_kgs = $mr->total_milk_weight_kgs + $c2_milk_weight;
            //calculate specific gravity ----formula 1+(LR/1000)
            $tests = collect($mr->tests);
            $lr = $tests->where('qa_test_name', 'LR')->where('compartment', 2)->pluck('value')->first();
            $specific_gravity = 1 + ($lr / 1000);
            $c2_gross_volume = round($c2_milk_weight / $specific_gravity, 2);
            $mr->gross_volume = round($mr->gross_volume + $c2_gross_volume, 2);
            //updated plant balance and opening balance
            $plant = Plant::find($mr->plant_id);
            $mr->left_over_milk = 0;
            $mr->opening_balance = ($plant->balance == null) ? 0 : $plant->balance;
            $mr->opening_balance_compartment_2 = ($plant->balance == null) ? 0 : $plant->balance;
            $plant->balance = round($plant->balance + $c2_gross_volume, 2);
            $mr->opening_balance1 = 0;
            //calculate volume ts 
            $fat = $tests->where('qa_test_name', 'Fat')->where('compartment', 2)->pluck('value')->first();
            $snf = $tests->where('qa_test_name', 'SNF')->where('compartment', 2)->pluck('value')->first();
            $ts = $fat + $snf;
            $c2_volume_ts = round(($c2_gross_volume * $ts) / 13, 2);
            $mr->volume_ts = round($mr->volume_ts + $c2_volume_ts, 2);
            //calculate gain loss
            if ($mr->compartments == 2) {
                $gatePasstoken = GatePassToken::find($token);
                $mr->gain_loss = round($gatePasstoken->gross_volume - $mr->gross_volume, 2);
            }
            if ($mr->compartment_status_2 == 1)
                $plant->save();
            $mr->save();
            //milk rejection
            if ($mr->compartment_status_2 == 0) {
                $PurchasedMilkRejection = new PurchasedMilkRejection();
                $PurchasedMilkRejection->plant_id = $plant->id;
                $PurchasedMilkRejection->compartment = $compartment;
                $PurchasedMilkRejection->created_by = $user->id;
                $PurchasedMilkRejection->type = 'plant_reception';
                $PurchasedMilkRejection->token_id = $token;
                $PurchasedMilkRejection->total_vehicle_weight = (float) $mr->total_vehicle_weight;
                $PurchasedMilkRejection->total_milk_weight_kgs = $c2_milk_weight;
                $PurchasedMilkRejection->total_vehicle_weight_after_c2 = (float) $mr->total_vehicle_weight_after_c2;
                $PurchasedMilkRejection->tests = $tests;
                $PurchasedMilkRejection->opening_balance = $mr->opening_balance;
                $PurchasedMilkRejection->opening_balance_compartment_2 = $mr->opening_balance_compartment_2;
                $PurchasedMilkRejection->gain_loss = $mr->gain_loss;
                //calculate specific gravity ----formula 1+(LR/1000)
                $PurchasedMilkRejection->gross_volume = $c2_gross_volume;
                //calculate volume ts 
                $PurchasedMilkRejection->volume_ts = $c2_volume_ts;
                $PurchasedMilkRejection->date = date('Y-m-d H:i:s');
                $PurchasedMilkRejection->save();
            }
            if ($mr->compartment_status_2 == 1)
                return $this->response(null, true, 'Calculations are performed and compartment 2 balance is added');
            elseif ($mr->compartment_status_2 == 0)
                return $this->response(null, true, 'Calculations are performed and compartment 2 milk is rejected and rejection is entered in to the system');
        } elseif ($compartment == 3) {
            if ($request->total_vehicle_weight_after_c3 == null)
                return $this->response(null, true, 'Compartment 3 weight is required');
            $mr->total_vehicle_weight_after_c3 = (float) $request->total_vehicle_weight_after_c3;
            $c3_milk_weight = round($mr->total_vehicle_weight_after_c2 - $request->total_vehicle_weight_after_c3, 2);
            $mr->total_milk_weight_kgs = $mr->total_milk_weight_kgs + $c3_milk_weight;
            //calculate specific gravity ----formula 1+(LR/1000)
            $tests = collect($mr->tests);
            $lr = $tests->where('qa_test_name', 'LR')->where('compartment', 3)->pluck('value')->first();
            $specific_gravity = 1 + ($lr / 1000);
            $c3_gross_volume = round($c3_milk_weight / $specific_gravity, 2);
            $mr->gross_volume = round($mr->gross_volume + $c3_gross_volume, 2);
            //updated plant balance and opening balance
            $plant = Plant::find($mr->plant_id);
            $mr->left_over_milk = 0;
            $mr->opening_balance = ($plant->balance == null) ? 0 : $plant->balance;
            $mr->opening_balance_compartment_3 = ($plant->balance == null) ? 0 : $plant->balance;
            $plant->balance = round($plant->balance + $c3_gross_volume, 2);
            $mr->opening_balance1 = 0;
            //calculate volume ts 
            $fat = $tests->where('qa_test_name', 'Fat')->where('compartment', 3)->pluck('value')->first();
            $snf = $tests->where('qa_test_name', 'SNF')->where('compartment', 3)->pluck('value')->first();
            $ts = $fat + $snf;
            $c3_volume_ts = round(($c3_gross_volume * $ts) / 13, 2);
            $mr->volume_ts = $mr->volume_ts + $c3_volume_ts;
            //calculate gain loss
            if ($mr->compartments == 3) {
                $gatePasstoken = GatePassToken::find($token);
                $mr->gain_loss = round($gatePasstoken->gross_volume - $mr->gross_volume, 2);
            }
            if ($mr->compartment_status_3 == 1)
                $plant->save();
            $mr->save();
            //milk rejection
            if ($mr->compartment_status_3 == 0) {
                $PurchasedMilkRejection = new PurchasedMilkRejection();
                $PurchasedMilkRejection->plant_id = $plant->id;
                $PurchasedMilkRejection->compartment = $compartment;
                $PurchasedMilkRejection->created_by = $user->id;
                $PurchasedMilkRejection->type = 'plant_reception';
                $PurchasedMilkRejection->token_id = $token;
                $PurchasedMilkRejection->total_vehicle_weight = (float) $mr->total_vehicle_weight;
                $PurchasedMilkRejection->total_milk_weight_kgs = $c3_milk_weight;
                $PurchasedMilkRejection->total_vehicle_weight_after_c3 = (float) $mr->total_vehicle_weight_after_c3;
                $PurchasedMilkRejection->tests = $tests;
                $PurchasedMilkRejection->opening_balance = $mr->opening_balance;
                $PurchasedMilkRejection->opening_balance_compartment_3 = $mr->opening_balance_compartment_3;
                $PurchasedMilkRejection->gain_loss = $mr->gain_loss;
                //calculate specific gravity ----formula 1+(LR/1000)
                $PurchasedMilkRejection->gross_volume = $c3_gross_volume;
                //calculate volume ts 
                $PurchasedMilkRejection->volume_ts = $c3_volume_ts;
                $PurchasedMilkRejection->date = date('Y-m-d H:i:s');
                $PurchasedMilkRejection->save();
            }
            if ($mr->compartment_status_3 == 1)
                return $this->response(null, true, 'Calculations are performed and compartment 3 balance is added');
            elseif ($mr->compartment_status_3 == 0)
                return $this->response(null, true, 'Calculations are performed and compartment 3 milk is rejected and rejection is entered in to the system');
        }
    }

    public function plantpurchaseweight(Request $request)
    {
        $user = auth()->user('api');
        $token = $request->id;
        $compartment = $request->compartment;
        if ($token == null)
            return $this->fail('Token id is required');

        $mp = MilkPurchase::where('gate_pass_token_id', $token)->get()->first();
        $mp->updated_by = $user->id;
        //if general compartment then whole vehicle filled weight and empty weight 
        if ($compartment == 0) {
            $mp->total_vehicle_weight = (float) $request->total_vehicle_weight;
            $mp->total_empty_vehicle_weight = (float) $request->total_empty_vehicle_weight;
            $mp->total_milk_weight_kgs = round($request->total_vehicle_weight - $request->total_empty_vehicle_weight, 2);
            //if milk is accepted convert to liter 
            //calculate specific gravity ----formula 1+(LR/1000)
            $tests = collect($mp->tests);
            $lr = $tests->where('qa_test_name', 'LR')->where('compartment', 0)->pluck('value')->first();
            $specific_gravity = 1 + ($lr / 1000);
            if ($mp->compartment_status_0 == 1)
                $mp->gross_volume = round($mp->total_milk_weight_kgs / $specific_gravity, 2);
            elseif ($mp->compartment_status_0 == 0)
                $mp->gross_volume = 0;
            //updated plant balance and opening balance
            $plant = Plant::find($mp->plant_id);
            $mp->opening_balance = ($plant->balance == null) ? 0 : $plant->balance;
            $mp->opening_balance_compartment_0 = ($plant->balance == null) ? 0 : $plant->balance;
            $plant->balance = round($plant->balance + $mp->gross_volume, 2);
            if ($mp->compartment_status_0 == 1)
                $plant->save();
            //calculate volume ts 
            $fat = $tests->where('qa_test_name', 'Fat')->where('compartment', 0)->pluck('value')->first();
            $snf = $tests->where('qa_test_name', 'SNF')->where('compartment', 0)->pluck('value')->first();
            $ts = $fat + $snf;
            if ($mp->compartment_status_0 == 1)
                $mp->ts_volume = round(($mp->gross_volume * $ts) / 13, 2);
            elseif ($mp->compartment_status_0 == 0)
                $mp->ts_volume = 0;
            $mp->save();
            //milk rejection
            if ($mp->compartment_status_0 == 0) {
                $PurchasedMilkRejection = new MilkRejection();
                $PurchasedMilkRejection->plant_id = $plant->id;
                $PurchasedMilkRejection->compartment = (int) $compartment;
                $PurchasedMilkRejection->created_by = $user->id;
                $PurchasedMilkRejection->type = 'purchase_at_plant';
                $PurchasedMilkRejection->token_id = $token;
                $PurchasedMilkRejection->total_vehicle_weight = (float) $request->total_vehicle_weight;
                $PurchasedMilkRejection->total_milk_weight_kgs = (float) $mp->total_milk_weight_kgs;
                $PurchasedMilkRejection->tests = $tests;
                $PurchasedMilkRejection->opening_balance = $mp->opening_balance;
                $PurchasedMilkRejection->opening_balance_compartment_0 = $mp->opening_balance_compartment_0;
                //calculate specific gravity ----formula 1+(LR/1000)
                $PurchasedMilkRejection->gross_volume = round($mp->gross_volume, 2);
                //calculate volume ts 
                $PurchasedMilkRejection->ts_volume = round($mp->volume_ts, 2);
                $PurchasedMilkRejection->date = date('Y-m-d H:i:s');
                $PurchasedMilkRejection->save();
            }
            if ($mp->compartment_status_0 == 1)
                return $this->response(null, true, 'Calculations are performed and milk is purchased from supplier');
            elseif ($mp->compartment_status_0 == 0)
                return $this->response(null, true, 'Calculations are performed and milk is rejected and rejection is entered in to the system');
        }
    }

    public function getPlantreception(Request $request)
    {
        $token = $request->id;
        if ($token == null)
            return $this->fail('Token id is required');
        $data['milk_reception'] = MilkReception::where('gate_pass_token_id', $token)->get()->first();
        $data['milk_purchase'] = MilkPurchase::where('gate_pass_token_id', $token)->get()->first();
        return $this->response($data, true, '');
    }
    public function cip(Request $request)
    {
        $token = $request->id;
        $status = $request->status;
        if ($token == null)
            return $this->fail('Token id is required');

        $gatePasstoken = GatePassToken::find($token);
        if ($gatePasstoken == null)
            return $this->fail('No record found against token');

        if ($status == null) {
            $data = ['cip_start' => $gatePasstoken->cip_start, 'cip_end' => $gatePasstoken->cip_end];
            return $this->response($data, true, '');
        }

        if ($status == 1) {
            $gatePasstoken->cip_start = date('Y-m-d H:i:s');
            $gatePasstoken->save();
            $data = ['cip_start' => $gatePasstoken->cip_start, 'cip_end' => null];
        }
        if ($status == 0) {
            $gatePasstoken->cip_end = date('Y-m-d H:i:s');
            $gatePasstoken->save();
            $data = ['cip_start' => $gatePasstoken->cip_start, 'cip_end' => $gatePasstoken->cip_end];
        }
        return $this->response($data, true, '');
    }
    public function gateOut(Request $request)
    {
        $token = $request->id;
        if ($token == null)
            return $this->fail('Token id is required');
        $gatePasstoken = GatePassToken::find($token);
        if ($gatePasstoken == null)
            return $this->fail('No record found against token');

        $gatePasstoken->gate_out_date_time = date('Y-m-d H:i:s');
        $gatePasstoken->save();
        $data = ['gate_out_date_time' => $gatePasstoken->gate_out_date_time];
        return $this->response($data, true, '');
    }
}
