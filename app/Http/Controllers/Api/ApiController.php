<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\MIlkReceptionATAreaOfficeRequest;
use App\Http\Requests\MIlkReceptionRequest;
use App\Http\Requests\PurchaseReciptRequest;
use App\Http\Resources\AreaOfficeResource;
use App\Http\Resources\CollectionPointResource;
use App\Http\Resources\SupplierResource;
use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\LactometerChart;
use App\Models\MilkCollectionVehicle;
use App\Models\MilkDispatch;
use App\Models\MilkPurchase;
use App\Models\MilkReception;
use App\Models\MilkRejection;
use App\Models\RouteVehicle;
use App\Models\Supplier;
use App\Models\User;
use App\Models\PurchasedMilkRejection;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maklad\Permission\Models\Role;
use Illuminate\Support\Facades\Date;
use App\Traits\JazzSMSTrait;
use App\Models\Handshake;
use Validator;

class ApiController extends Controller
{
    use HttpResponseTrait, JazzSMSTrait;

    public function preparSMS($mp, $request, $purchaseType)
    {
        $tests = collect($mp->tests);
        $lr = $tests->where('qa_test_name', 'LR')->pluck('value')->first();
        $fat = $tests->where('qa_test_name', 'Fat')->pluck('value')->first();
        $snf = $tests->where('qa_test_name', 'SNF')->pluck('value')->first();
        $prepareMsgBody = [];
        $prepareMsgBody['number'] = $mp->supplier->contact ?? "N/A";
        $prepareMsgBody['business_name'] = $mp->supplier->business_name ?? $mp->supplier->name;
        $prepareMsgBody['collection_point'] = $mp->cp->name ?? 'N/A';
        $prepareMsgBody['lr'] = $lr;
        $prepareMsgBody['fat'] = $fat;
        $prepareMsgBody['snf'] = $snf;
        $prepareMsgBody['gross_volume'] = $request->gross_volume;
        $prepareMsgBody['ts_volume'] = $mp->ts_volume;
        $this->sendSMSToSuplier($prepareMsgBody);
        return;
    }
    public function getSuppliers(Request $request)
    {
        $user = auth()->user('api');
        $id =  $user['access_level_ids'][0] ?? '';

        $supplier_query = Supplier::where('status', 1)->with('supplier_type');
        if ($request->search_by == 'mcc') {
            $supplier_query->where('mcc', $id);
        } else if ($request->search_by == 'ao') {
            $supplier_query->where('area_office', $id);
        } else if ($request->search_by == 'plant') {
            $supplier_query->where('plant', $id);
        }
        if ($request->code) {
            $supplier_query->where('code', 'like', '%' .  $request->code . '%');
        }
        $suppliers = SupplierResource::collection($supplier_query->get());
        return $this->response(['suppliers' => $suppliers]);
    }

    public function getCpSuppliers(Request $request)
    {
        $supplier_query = Supplier::active();
        $supplier_query->whereIn('cp_ids', [$request->cp_id]);
        $suppliers = SupplierResource::collection($supplier_query->get());
        return $this->response(['suppliers' => $suppliers]);
    }

    public function getCps(Request $request)
    {
        $user = auth()->user('api');
        $access_level = $user->roles->first()->access_level;
        $cps = CollectionPoint::where(['is_mcc' => '0', 'status', 1]);
        if ($access_level == 2) {
            $cps->where(['area_office_id' => $user['access_level_ids'][0]]);
        }
        $cps = CollectionPointResource::collection($cps->get());
        return $this->response(['cps' => $cps]);
    }

    public function getAOs(Request $request)
    {
        $user = auth()->user('api');
        $access_level = $user->roles->first()->access_level;
        $aos = AreaOffice::active();
        //        for MMT user type
        if ($access_level == 2) {
            $aos->whereNotIn('_id', $user['access_level_ids']);
        }
        $aos = AreaOfficeResource::collection($aos->get());
        return $this->response(['area_offices' => $aos]);
    }

    public function getLactoMeterReadings()
    {
        $tests = LactometerChart::get();
        return $this->response(['lactoMeterRaadings' => $tests]);
    }

    public function getRoute()
    {
        $user = auth()->user('api');
        $routePlan = RouteVehicle::where('user_id', $user->id)->with('route', 'vehicle')->where('reception', 0)->first();
        if ($routePlan == null)
            $this->fail('Route not found');

        $cps_array = $cps = [];
        $route = $vehicle = null;
        if ($routePlan) {
            if ($routePlan->route && $routePlan->route->collection_points) {
                $cps = $routePlan->route->collection_points;
                $co_points = CollectionPoint::whereIn('_id', $cps)->select('id', 'name', 'code', 'is_mcc')->get();
                foreach ($co_points as $cp) {
                    $cps_array[] =  ['name' => $cp->name, 'code' => $cp->code, 'id' => $cp->id, 'is_mcc_ffl' => $cp->is_mcc, 'suppliers' => SupplierResource::collection($cp->suppliers)];
                }
            }
            $route = ['name' => $routePlan->route->name, 'id' => $routePlan->route->id, 'status' => $routePlan->status];
            $vehicle = ['number' => $routePlan->vehicle->vehicle_number, 'id' => $routePlan->vehicle->id];
            $success = true;
            $message = 'Route found';
        } else {
            $success = false;
            $message = 'No route found';
        }
        $data = ['route' => $route, 'vehicle' => $vehicle, 'cps' => $cps_array];
        return $this->response($data, $success, $message);
    }

    //MCA PURCHASE at collection Center
    public function purchaseSave(PurchaseReciptRequest $request)
    {
        $user = auth()->user('api');
        $status = $request->status;
        $mp = ($status == 1) ? new MilkPurchase : new MilkRejection;
        $mp->referenceNumber = $request->referenceNumber;

        $duplicate = MilkPurchase::where('referenceNumber', $request->referenceNumber)->where('created_by', $user->id)->exists();
        if ($duplicate)
            return $this->fail('Duplicate entry');




        $mp->created_by = $user->id;
        $mp->mcc_id = $request->mcc_id;
        $mp->type = 'purchase_at_mcc';
        $mp->supplier_id = $request->supplier_id;
        $mp->supplier_type_id = $request->supplier_type_id;
        $mp->shift = (int)$request->shift;
        $mp->gross_volume = (float)$request->gross_volume;
        $mp->ts_volume = (float)$request->ts_volume;
        // Actual purchase Time
        $date = Carbon::createFromFormat('d/m/Y H:i a', $request->date . ' ' . $request->time);
        $mp->time = $date->format('Y-m-d H:i:s');
        $mp->tests = $request->tests;

        $mp->booked_at = Carbon::createFromFormat('d/m/Y', $request->booked_at)->format('Y-m-d');
        if ($status == 1) {

            // $cp = CollectionPoint::where('_id', $request->mcc_id)->select('balance')->withTrashed()->first();
            // $mp->opening_balance = $cp->balance ?? 0;
            // $cp->balance = (float)$cp->balance + $request->gross_volume;
            // $cp->save();

            $cp = CollectionPoint::where('_id', $request->mcc_id)->withTrashed()->first();

            if ($cp) {
                $mp->opening_balance = $cp->balance ?? 0;
                $mp->opening_balance_ts = $cp->ts_balance ?? 0;
                $cp->balance = $cp->balance ? (float)$cp->balance + (float)$request->gross_volume : (float)$request->gross_volume;
                $cp->ts_balance = $cp->ts_balance ? (float)$cp->ts_balance + (float)$request->ts_volume : (float)$request->ts_volume;
                $cp->save();
            }
        }
        $mp->save();

        if ($status == 1) {
            $mcc = CollectionPoint::find($request->mcc_id);
            if ($mcc->area_office_id == '63c79839b57d0000ef002091')
                $this->preparSMS($mp, $request, 'purchase_at_mcc');
        }

        $data = ['serial_number' => $mp->serial_number, 'mpr_id' => $mp->id,];

        return $this->response($data, 'true', 'Saved successfully');
    }

    public function bulkPurchaseSave(Request $request)
    {
        $user = auth()->user('api');

        $responseArray = array();
        $dataArray = $request->data;

        foreach ($dataArray as $data) {
            $duplicate = MilkPurchase::where('referenceNumber', $data['referenceNumber'])->where('created_by', $user->id)->exists();
            if ($duplicate)
                continue;
            $status = $data['status'];
            $mp = ($status == 1) ? new MilkPurchase : new MilkRejection;
            $mp->referenceNumber = $data['referenceNumber'];
            $mp->created_by = $user->id;
            $mp->mcc_id = $data['mcc_id'];
            $mp->type = 'purchase_at_mcc';
            $mp->supplier_id = $data['supplier_id'];
            $mp->supplier_type_id = $data['supplier_type_id'] ?? null;
            $mp->shift = (int)$data['shift'];
            $mp->gross_volume = (float)$data['gross_volume'];
            $mp->ts_volume = (float)$data['ts_volume'];

            // Actual purchase Time
            $date = Carbon::createFromFormat('d/m/Y H:i a', $data['date'] . ' ' . $data['time']);
            $mp->time = $date->format('Y-m-d H:i:s');
            $mp->tests =  $data['tests'];
            $mp->booked_at = Carbon::createFromFormat('d/m/Y', $data['booked_at'])->format('Y-m-d');

            if ($status == 1) {

                // $cp = CollectionPoint::where('_id', $data['mcc_id'])->select('balance')->withTrashed()->first();
                // $mp->opening_balance = $cp->balance ?? 0;
                // $cp->balance = (float)$cp->balance + (float)$data['gross_volume'];
                // $cp->save();

                $cp = CollectionPoint::where('_id', $data['mcc_id'])->withTrashed()->first();

                if ($cp) {
                    $mp->opening_balance = $cp->balance ?? 0;
                    $mp->opening_balance_ts = $cp->ts_balance ?? 0;
                    $cp->balance = $cp->balance ? (float)$cp->balance + (float)$data['gross_volume'] : (float)$data['gross_volume'];
                    $cp->ts_balance = $cp->ts_balance ? (float)$cp->ts_balance + (float)$data['ts_volume'] : (float)$data['ts_volume'];
                    $cp->save();
                }
            }
            $mp->save();
            // if ($status == 1) {
            //     $this->preparSMS($mp, $request, 'purchase_at_mcc');
            // }

            $responseObject = [
                "mpr_id" => $mp->id,
                "referenceNumber" => $mp->referenceNumber,
                "serial_number" => 'MPR-' . $mp->serial_number
            ];

            array_push($responseArray, $responseObject);
        }


        $data = ['mpr_ids' => $responseArray];
        return $this->response($data, 'true', 'Saved successfully');
    }

    //MMT PURCHASE at collection point
    public function purchaseMMTSave(Request $request)
    {
        $user = auth()->user('api');
        $mp = ($request->status == 1) ? new MilkPurchase : new MilkRejection;
        $mp->referenceNumber = $request->referenceNumber;

        $duplicate = MilkPurchase::where('referenceNumber', $request->referenceNumber)->where('created_by', $user->id)->exists();
        if ($duplicate)
            return $this->fail('Duplicate entry');

        $mp->created_by = $user->id;
        $mp->cp_id = $request->cp_id;
        $mp->supplier_id = $request->supplier_id;
        $mp->supplier_type_id = $request->supplier_type_id;
        $mp->type = 'mmt_purchase';
        $mp->shift = $request->shift;
        $mp->gross_volume = (float) $request->gross_volume;
        $mp->ts_volume = (float) $request->ts_volume;
        // Actual purchase Time
        $mp->time = Carbon::createFromFormat('d/m/Y H:i a', $request->date . ' ' . $request->time)->format('Y-m-d H:i:s');
        $mp->time_in = $request->time_in;
        $mp->time_out = $request->time_out;
        $mp->tests = $request->tests;
        $mp->booked_at = Carbon::createFromFormat('d/m/Y', $request->booked_at)->format('Y-m-d');

        //  MMT BALANCE will be added after taking milk from Cp
        if ($request->status == 1) {
            $mp->opening_balance = $user->balance ?? 0;
            $mp->opening_balance_ts = $user->ts_balance ?? 0;
            $user->balance += (float) $request->gross_volume;
            $user->ts_balance = $user->ts_balance ? (float) $user->ts_balance + (float) $request->ts_volume : (float) $request->ts_volume;
            $user->save();
        }
        $mp->save();
        if ($request->status == 1) {
            $cp = CollectionPoint::find($request->cp_id);
            if ($cp->area_office_id == '63c79839b57d0000ef002091')
                $this->preparSMS($mp, $request, 'mmt_purchase');
        }

        $data = ['serial_number' => $mp->serial_number, 'mpr_id' => $mp->id,];
        return $this->response($data, 'true', 'Saved successfully');
    }

    public function bulkPurchaseMMTSave(Request $request)
    {
        $user = auth()->user('api');

        $responseArray = array();
        $dataArray = $request->data;

        foreach ($dataArray as $data) {

            $duplicate = MilkPurchase::where('referenceNumber', $data['referenceNumber'])->where('created_by', $user->id)->exists();
            if ($duplicate)
                continue;

            $status = $data['status'];
            $mp = ($status == 1) ? new MilkPurchase : new MilkRejection;
            $mp->referenceNumber = $data['referenceNumber'];
            $mp->created_by = $user->id;
            $mp->cp_id = $data['cp_id'];
            $mp->type = 'mmt_purchase';
            $mp->supplier_id = $data['supplier_id'];
            $mp->supplier_type_id = $data['supplier_type_id'] ?? null;
            $mp->shift = (int)$data['shift'];
            $mp->gross_volume = (float)$data['gross_volume'];
            $mp->ts_volume = (float)$data['ts_volume'];

            $date = Carbon::createFromFormat('d/m/Y H:i a', $data['date'] . ' ' . $data['time']);
            $mp->time = $date->format('Y-m-d H:i:s');
            $mp->time_in = $data['time_in'] ?? null;
            $mp->time_out = $data['time_out'] ?? null;
            $mp->tests =  $data['tests'];
            $mp->booked_at = Carbon::createFromFormat('d/m/Y', $data['booked_at'])->format('Y-m-d');

            if ($status == 1) {

                $mp->opening_balance = $user->balance;
                $mp->opening_balance_ts = $user->ts_balance ?? 0;
                $user->balance += (float)$data['gross_volume'];
                $user->ts_balance = $user->ts_balance ? (float)$user->ts_balance + (float)$data['ts_volume'] : (float)$data['ts_volume'];
                $user->save();
            }

            $mp->save();

            $responseObject = [
                "mpr_id" => $mp->id,
                "referenceNumber" => $mp->referenceNumber,
                "serial_number" => 'MPR-' . $mp->serial_number
            ];

            array_push($responseArray, $responseObject);
        }

        $data = ['mpr_ids' => $responseArray];
        return $this->response($data, 'true', 'Saved successfully');
    }

    //Area office lab supervisor purchase(when purchases by supplier) at area office
    public function purchaseAtAreaOffice(Request $request)
    {
        $user = auth()->user('api');
        $ao_id = $user->access_level_ids[0]; //Current area lab supervisor area_id who is submitting the form
        $mp = ($request->status == 1) ? new MilkPurchase : new MilkRejection;
        $mp->referenceNumber = $request->referenceNumber;

        $duplicate = MilkPurchase::where('referenceNumber', $request->referenceNumber)->where('created_by', $user->id)->exists();
        if ($duplicate)
            return $this->fail('Duplicate entry');

        $mp->created_by = $user->id;
        $mp->area_office_id = $ao_id;
        $mp->supplier_id = $request->supplier_id;
        $mp->source_id = $request->source_id;
        $mp->type = 'purchase_at_ao';
        $mp->shift = $request->shift;
        $mp->gross_volume = (float)$request->gross_volume;
        $mp->ts_volume = (float)$request->ts_volume;
        $date = Carbon::createFromFormat('d/m/Y H:i a', $request->date . ' ' . $request->time);
        $mp->time = $date->format('Y-m-d H:i:s');
        $mp->tests = $request->tests;
        $mp->booked_at = Carbon::createFromFormat('d/m/Y', $request->booked_at)->format('Y-m-d');

        //Area office BALANCE will be added after taking milk from supplier
        if ($request->status == 1) {
            // $area_office =  AreaOffice::where('_id', $ao_id)->select('balance')->first();
            // $mp->opening_balance = $area_office->balance;
            // $area_office->balance += $request->gross_volume;
            // $area_office->save();

            $area_office =  AreaOffice::where('_id', $ao_id)->first();

            if ($area_office) {
                $mp->opening_balance = $area_office->balance ?? 0;
                $mp->opening_balance_ts = $area_office->ts_balance ?? 0;
                $area_office->balance = $area_office->balance ? (float)$area_office->balance + (float)$request->gross_volume : (float)$request->gross_volume;
                $area_office->ts_balance = $area_office->ts_balance ? (float)$area_office->ts_balance + (float)$request->ts_volume : (float)$request->ts_volume;
                $area_office->save();
            }
        }
        $mp->vehicle_number = $request->vehicle_number;
        $mp->route_name = $request->route_name;
        $mp->save();

        if ($request->status == 1) {
            if ($ao_id == '63c79839b57d0000ef002091')
                $this->preparSMS($mp, $request, 'purchase_at_ao');
        }
        $data = ['serial_number' => $mp->serial_number, 'mpr_id' => $mp->id];
        return $this->response($data, true, 'Saved successfully');
    }

    public function bulkPurchaseAtAreaOffice(Request $request)
    {
        $user = auth()->user('api');

        $responseArray = array();
        $dataArray = $request->data;
        $ao_id = $user->access_level_ids[0]; //Current area lab supervisor area_id who is submitting the form

        foreach ($dataArray as $data) {

            $duplicate = MilkPurchase::where('referenceNumber', $data['referenceNumber'])->where('created_by', $user->id)->exists();
            if ($duplicate)
                continue;

            $status = $data['status'];
            $mp = ($status == 1) ? new MilkPurchase : new MilkRejection;
            $mp->referenceNumber = $data['referenceNumber'];
            $mp->created_by = $user->id;
            $mp->area_office_id = $ao_id;
            $mp->type = 'purchase_at_ao';
            $mp->supplier_id = $data['supplier_id'];
            $mp->source_id = $data['source_id'];
            $mp->shift = (int)$data['shift'];
            $mp->gross_volume = (float)$data['gross_volume'];
            $mp->ts_volume = (float)$data['ts_volume'];

            $date = Carbon::createFromFormat('d/m/Y H:i a', $data['date'] . ' ' . $data['time']);
            $mp->time = $date->format('Y-m-d H:i:s');
            $mp->tests =  $data['tests'];

            //Area office BALANCE will be added after taking milk from supplier
            if ($status == 1) {

                // $area_office =  AreaOffice::where('_id', $ao_id)->select('balance')->first();
                // $mp->opening_balance = $area_office->balance;
                // $area_office->balance += (float)$data['gross_volume'];;
                // $area_office->save();

                $area_office =  AreaOffice::where('_id', $ao_id)->first();

                if ($area_office) {
                    $mp->opening_balance = $area_office->balance ?? 0;
                    $mp->opening_balance_ts = $area_office->ts_balance ?? 0;
                    $area_office->balance = $area_office->balance ? (float)$area_office->balance + (float)$data['gross_volume'] : (float)$data['gross_volume'];
                    $area_office->ts_balance = $area_office->ts_balance ? (float)$area_office->ts_balance + (float)$data['ts_volume'] : (float)$data['ts_volume'];
                    $area_office->save();
                }
            }

            $mp->vehicle_number = $data['vehicle_number'];
            $mp->route_name = $data['route_name'];
            $mp->save();

            $responseObject = [
                "mpr_id" => $mp->id,
                "referenceNumber" => $mp->referenceNumber,
                "serial_number" => 'MPR-' . $mp->serial_number
            ];

            array_push($responseArray, $responseObject);
        }

        $data = ['mpr_ids' => $responseArray];
        return $this->response($data, 'true', 'Saved successfully');
    }

    public function milkReceptionAtMCC(MIlkReceptionRequest $request)
    {
        $user = auth()->user('api');
        $status = $request->status;
        $tests = $request->tests;
        $snfValue = 0.0;
        $fatValue = 0.0;

        $duplicate = MilkReception::where('referenceNumber', $request->referenceNumber)->where('created_by', $user->id)->exists();
        if ($duplicate)
            return $this->fail('Duplicate entry');

        foreach ($tests as $test) {
            if (array_key_exists('qa_test_name', $test) && $test['qa_test_name'] !== null && (strtolower($test['qa_test_name']) == 'snf')) {
                $snfValue = $test['value'];
            } else if (array_key_exists('qa_test_name', $test) && $test['qa_test_name'] !== null && (strtolower($test['qa_test_name']) == 'fat')) {
                $fatValue =  $test['value'];
            }
        }


        $cp = CollectionPoint::where('_id', $request->mcc_id)->withTrashed()->first();
        if ($cp == null)
            return $this->fail('Mcc not found');
        $current_balance_cp = (float)$cp->balance;
        $current_balance_cp_ts = (float)$cp->ts_balance ? $cp->ts_balance : 0;
        //MMT BALANCE

        $mmt_balance = $user->balance ? $user->balance : 0;
        $mmt_ts_balance = $user->ts_balance ? $user->ts_balance : 0;

        $user->balance += $request->gross_volume;
        $user->ts_balance += $request->volume_ts;
        $user->save();

        //         CP BALANCE GROSS
        $gain_loss =  (float)($request->gross_volume + $request->left_over_milk) - $current_balance_cp; //(70+10)-100 =-20
        $cp->balance = (float)$request->left_over_milk;  //100-70 =30
        $cp->save();

        // CP BALANCE TS
        $left_over_milk_ts = (float)(($snfValue + $fatValue) * $request->left_over_milk) / 13;
        $gain_loss_ts =  (float)(round(($request->volume_ts + $left_over_milk_ts), 2) - round($current_balance_cp_ts, 2));
        $cp->ts_balance = (float)$left_over_milk_ts;  //100-70 =30
        // dd(round(($request->volume_ts + $left_over_milk_ts),6));
        $cp->save();

        $previous_mr =  MilkReception::where(['type' => 'mmt_reception', 'mcc_id' => $request->mcc_id])->latest()->first();

        $from_time = ($previous_mr && $previous_mr->to_time) ? $previous_mr->to_time : 1672513200;

        $mr = $status == 1 ? new MilkReception : new PurchasedMilkRejection;
        $mr->referenceNumber = $request->referenceNumber;
        $mr->created_by = $user->id;
        $mr->type = 'mmt_reception';
        $mr->mcc_id = $request->mcc_id;
        $mr->mmt_id = $user->id;
        $mr->gross_volume = $request->gross_volume;
        $mr->left_over_milk = $request->left_over_milk;
        $mr->left_over_milk_ts = round($left_over_milk_ts, 2);
        //COllection point balance
        $mr->opening_balance = $current_balance_cp;
        $mr->opening_balance_ts = round($current_balance_cp_ts, 2);
        //MMT Balance
        $mr->opening_balance1 = $mmt_balance;
        $mr->opening_balance2 = $mmt_ts_balance;
        $mr->volume_ts = $request->volume_ts;
        $mr->gain_loss = $gain_loss;
        $mr->gain_loss_ts = $gain_loss_ts;
        $mr->tests = $request->tests;
        // Actual purchase Time
        $mr->to_time = Carbon::createFromFormat('d/m/Y H:i a', $request->date . ' ' . $request->time)->format('Y-m-d H:i:s');
        $mr->from_time = $from_time;
        $mr->time_in = $request->time_in;
        $mr->time_out = $request->time_out;
        $mr->save();
        //handshake functionality
        if ($request->mmt_handshake_date <> '') {
            $handShake = new Handshake();
            $handShake->created_by = $user->id;
            $handShake->milk_reception_id = $mr->id;
            $handShake->mmt_handshake_balance = $request->mmt_handshake_balance;
            if ($request->mmt_handshake_date <> '')
                $handShake->mmt_date_time = Carbon::createFromFormat('d/m/Y H:i a', $request->mmt_handshake_date . ' ' . $request->mmt_handshake_time)->format('Y-m-d H:i:s');
            else
                $handShake->mmt_date_time = null;
            $handShake->mmt_status = ($request->mmt_handshake_balance == "") ? (int) 0 : (int) 1; //0=no handshake 1=handshake
            $handShake->save();
        }

        $data = ['serial_number' => $mr->serial_number, 'mr_id' => $mr->id,];
        $msg = $status == 1 ? 'Milk Reception saved successfully' : 'Purchased Milk Rejection saved successfully';
        return $this->response($data, 'true', $msg);
    }

    public function milkReceptionAtMCCbulk(Request $request)
    {
        $user = auth()->user('api');

        $responseArray = array();
        $dataArray = $request->data;

        foreach ($dataArray as $data) {

            $duplicate = MilkReception::where('referenceNumber', $data['referenceNumber'])->where('created_by', $user->id)->exists();
            if ($duplicate)
                continue;

            $status = $data['status'];
            $tests = $data['tests'];
            $snfValue = 0.0;
            $fatValue = 0.0;

            foreach ($tests as $test) {
                if (array_key_exists('qa_test_name', $test) && $test['qa_test_name'] !== null && (strtolower($test['qa_test_name']) == 'snf')) {
                    $snfValue = $test['value'];
                } else if (array_key_exists('qa_test_name', $test) && $test['qa_test_name'] !== null && (strtolower($test['qa_test_name']) == 'fat')) {
                    $fatValue =  $test['value'];
                }
            }

            $cp = CollectionPoint::where('_id', $data['mcc_id'])->withTrashed()->first();
            if ($cp == null)
                return $this->fail('Mcc not found');
            $current_balance_cp = (float) $cp->balance;
            $current_balance_cp_ts = (float) $cp->ts_balance ? $cp->ts_balance : 0;
            //MMT BALANCE

            $mmt_balance = $user->balance ? $user->balance : 0;
            $mmt_ts_balance = $user->ts_balance ? $user->ts_balance : 0;

            $user->balance += $data['gross_volume'];
            $user->ts_balance += $data['volume_ts'];
            $user->save();

            //CP BALANCE GROSS
            $gain_loss =  (float) ($data['gross_volume'] + $data['left_over_milk']) - $current_balance_cp; //(70+10)-100 =-20
            $cp->balance = (float) $data['left_over_milk'];  //100-70 =30
            $cp->save();

            // CP BALANCE TS
            $left_over_milk_ts = (float) (($snfValue + $fatValue) * $data['left_over_milk']) / 13;
            $gain_loss_ts =  (float)(round(($data['volume_ts'] + $left_over_milk_ts), 2) - round($current_balance_cp_ts, 2));
            $cp->ts_balance = (float)$left_over_milk_ts;  //100-70 =30
            $cp->save();

            $previous_mr =  MilkReception::where(['type' => 'mmt_reception', 'mcc_id' => $data['mcc_id']])->latest()->first();

            $from_time = ($previous_mr && $previous_mr->to_time) ? $previous_mr->to_time : 1672513200;

            $mr = $status == 1 ? new MilkReception : new PurchasedMilkRejection;
            $mr->referenceNumber = $data['referenceNumber'];
            $mr->created_by = $user->id;
            $mr->type = 'mmt_reception';
            $mr->mcc_id = $data['mcc_id'];
            $mr->mmt_id = $user->id;
            $mr->gross_volume = $data['gross_volume'];
            $mr->left_over_milk = $data['left_over_milk'];
            $mr->left_over_milk_ts = round($left_over_milk_ts, 2);
            //COllection point balance
            $mr->opening_balance = $current_balance_cp;
            $mr->opening_balance_ts = round($current_balance_cp_ts, 2);
            //MMT Balance
            $mr->opening_balance1 = $mmt_balance;
            $mr->opening_balance2 = $mmt_ts_balance;
            $mr->volume_ts = $data['volume_ts'];
            $mr->gain_loss = $gain_loss;
            $mr->gain_loss_ts = $gain_loss_ts;
            $mr->tests = $data['tests'];
            // Actual purchase Time
            $mr->to_time = Carbon::createFromFormat('d/m/Y H:i a', $data['date'] . ' ' . $data['time'])->format('Y-m-d H:i:s');
            $mr->from_time = $from_time;
            $mr->time_in = $data['time_in'];
            $mr->time_out = $data['time_out'];
            $mr->save();
            //handshake functionality
            if ($data['mmt_handshake_date'] <> ''){
            $handShake = new Handshake();
            $handShake->created_by = $user->id;
            $handShake->milk_reception_id = $mr->id;
            $handShake->mmt_handshake_balance = $data['mmt_handshake_balance'];
            if ($data['mmt_handshake_date'] <> '')
                $handShake->mmt_date_time = Carbon::createFromFormat('d/m/Y H:i a', $data['mmt_handshake_date'] . ' ' . $data['mmt_handshake_time'])->format('Y-m-d H:i:s');
            else
                $handShake->mmt_date_time = null;
            $handShake->mmt_status = ($data['mmt_handshake_balance'] == "") ? (int) 0 : (int) 1; //0=no handshake 1=handshake
            $handShake->save();
            }

            $responseObject = [
                "mr_id" => $mr->id,
                "referenceNumber" => $mr->referenceNumber,
                "serial_number" => 'MR-' . $mr->serial_number,
            ];

            array_push($responseArray, $responseObject);
        }
        $data = ['mr_ids' => $responseArray];
        return $this->response($data, 'true', 'Milk receptions synced successfully');
    }


    public function MMTDispatchPlant(Request $request)
    {
        $user  = auth()->user('api');
        $mmt_balance = (float) $user->balance;
        $user->balance = (float) $mmt_balance - $request->gross_volume;
        $user->save();
        $mr = new MilkDispatch();
        $mr->referenceNumber = $request->referenceNumber;
        $mr->created_by = $user->id;
        $mr->type = 'mmt_dispatch_plant';
        $mr->mmt_id = $user->id;
        $mr->plant_id = $request->plant_id;
        $mr->gross_volume = $request->gross_volume;
        $mr->volume_ts = $request->volume_ts;
        $mr->tests = $request->tests;
        $mr->seals = $request->seals;
        $mr->route_id = $request->route_id;
        $mr->milk_type = $request->milk_type;
        // Actual purchase Time
        $date = Carbon::createFromFormat('d/m/Y H:i a', $request->date . ' ' . $request->time);
        $mr->time = $date->format('Y-m-d H:i:s');
        $mr->vehicle_id = $request->vehicle_id;

        $mr->save();
        $data = ['serial_number' => $mr->serial_number];
        return $this->response($data, 'true', 'Saved successfully');
    }

    public function AODispatchPlant(Request $request)
    {
        $user  = auth()->user('api');
        $ao_id = $user->access_level_ids[0]; //Current user area office id who is sending milk
        $ao = AreaOffice::where('_id', $ao_id)->select('balance')->withTrashed()->first();
        $ao_balance = (float) $ao->balance;
        $ao->balance = (float) $ao_balance - $request->gross_volume;
        $ao->save();
        $mr = new MilkDispatch();
        $mr->referenceNumber = $request->referenceNumber;
        $mr->created_by = $user->id;
        $mr->type = 'ao_dispatch_plant';
        $mr->area_office_id = $ao_id;
        $mr->plant_id = $request->plant_id;
        $mr->gross_volume = $request->gross_volume;
        $mr->volume_ts = $request->volume_ts;
        $mr->tests = $request->tests;
        $mr->seals = $request->seals;
        $mr->seals_status = $request->seals_status;
        $mr->route_id = $request->route_id;
        // Actual purchase Time
        $date = Carbon::createFromFormat('d/m/Y H:i a', $request->date . ' ' . $request->time);
        $mr->time = $date->format('Y-m-d H:i:s');
        $mr->vehicle_id = $request->vehicle_id;
        $mr->save();
        $data = ['serial_number' => $mr->serial_number];
        return $this->response($data, 'true', 'Saved successfully');
    }


    public function milkReceptionAtAO(MIlkReceptionATAreaOfficeRequest $request)
    {
        $user = auth()->user('api');
        $duplicate = MilkReception::where('referenceNumber', $request->referenceNumber)->where('created_by', $user->id)->exists();
        if ($duplicate)
            return $this->fail('Duplicate entry');

        $status = $request->status;
        //Route Plan implementation by @irfan majeed @07-25-2023
        $rv = RouteVehicle::where(['route_id' => $request->route_id, 'user_id' => $request->mmt_id, 'vehicle_id' => $request->vehicle_id])->where('check_out', 'exists', true)->where('reception', 0)->first();
        if ($rv == null)
            return $this->response(null, true, 'Route not found');
        elseif ($rv->check_in == null)
            return $this->response(null, true, 'Route is even not checked in! Reception is not possible');
        elseif ($rv->delivered_to == null)
            return $this->response(null, true, 'Route is not closed! Reception is not possible');
        elseif ($rv->check_out == null)
            return $this->response(null, true, 'Route is not checked out! Please check out route first');

        $tests = $request->tests;
        $snfValue = 0.0;
        $fatValue = 0.0;

        foreach ($tests as $test) {
            if (array_key_exists('qa_test_name', $test) && $test['qa_test_name'] !== null && (strtolower($test['qa_test_name']) == 'snf')) {
                $snfValue = $test['value'];
            } else if (array_key_exists('qa_test_name', $test) && $test['qa_test_name'] !== null && (strtolower($test['qa_test_name']) == 'fat')) {
                $fatValue =  $test['value'];
            }
        }

        $ao_id = $user->access_level_ids[0]; //Current ao_id of Area office lab supervisor who is submitting the form
        $ao = AreaOffice::where('_id', $ao_id)->withTrashed()->first();
        $mmt_user = User::where('_id', $request->mmt_id)->withTrashed()->first();

        if ($ao == null) {
            return $this->fail('Area Office not found');
        }

        if ($mmt_user == null) {
            return $this->fail('Mmt user not found');
        }

        $current_balance_mmt = (float)$mmt_user->balance;
        $current_balance_mmt_ts = (float)$mmt_user->ts_balance ?? 0;

        //       Area office balance credited
        $ao_balance = $ao->balance ?? 0;
        $ao_balance_ts = $ao->ts_balance ?? 0;
        $ao->balance += $request->gross_volume;
        $ao->ts_balance += $request->volume_ts;
        $ao->save();

        //      MMT BALANCE debited
        $gain_loss =  (float)($request->gross_volume + $request->left_over_milk) - $current_balance_mmt; //(70+10)-100 =-20
        $mmt_user->balance = (float)$request->left_over_milk;  //10
        $mmt_user->save();

        //      MMT TS BALANCE debited
        $left_over_milk_ts = (float)(($snfValue + $fatValue) * $request->left_over_milk) / 13;
        $gain_loss_ts =  (float)(round(($request->volume_ts + $left_over_milk_ts), 2) - round($current_balance_mmt_ts, 2));
        $mmt_user->ts_balance = (float)$left_over_milk_ts;
        $mmt_user->save();

        $previous_mr =  MilkReception::where(['type' => 'ao_lab_reception', 'mmt_id' => $mmt_user->id, 'ao_id' => $request->ao_id])->latest()->first();
        $from_time = ($previous_mr && $previous_mr->to_time) ? $previous_mr->to_time : 1672513200;

        $mr = $status == 1 ? new MilkReception : new PurchasedMilkRejection;
        $mr->referenceNumber = $request->referenceNumber;
        $mr->created_by = $user->id;
        $mr->type = 'ao_lab_reception';
        $mr->mmt_id = $mmt_user->id;
        $mr->area_office_id = $ao->id;
        $mr->gross_volume = $request->gross_volume;
        $mr->left_over_milk = $request->left_over_milk;
        $mr->left_over_milk_ts = round($left_over_milk_ts, 2);
        $mr->opening_balance = $current_balance_mmt;
        $mr->opening_balance_ts = round($current_balance_mmt_ts, 2);
        $mr->opening_balance1 = $ao_balance;
        $mr->opening_balance2 = $ao_balance_ts;
        $mr->volume_ts = $request->volume_ts;
        $mr->gain_loss = $gain_loss;
        $mr->gain_loss_ts = $gain_loss_ts;
        $mr->tests = $request->tests;
        // Actual reception Time
        $date = Carbon::createFromFormat('d/m/Y H:i a', $request->date . ' ' . $request->time);
        $mr->to_time = $date->format('Y-m-d H:i:s');
        $mr->from_time = $from_time;
        $mr->vehicle_id = $request->vehicle_id;
        $mr->route_id = $request->route_id;
        $mr->save();
        //update route reception flag on route 
        $rv->reception = 1;
        $rv->milk_reception_id = $mr->_id;
        $rv->status = 2;
        $rv->save();
        $data = ['serial_number' => $mr->serial_number, 'mr_id' => $mr->id];
        $msg = $status == 1 ? 'Milk Reception saved successfully' : 'Purchased Milk Rejection saved successfully';
        return $this->response($data, 'true', $msg);
    }

    public function milkReceptionCp(Request $request)
    {
        $cp = CollectionPoint::where('_id', $request->mcc_id)->select('balance')->withTrashed()->first();
        $data = ['balance' => (float)$cp->balance];
        return $this->response($data);
    }
    public function aoMilkReceptionCp(Request $request)
    {
        $cp = AreaOffice::where('_id', $request->ao_id)->select('balance')->withTrashed()->first();
        if ($cp) {
            $data = ['balance' => (float)$cp->balance];
            return $this->response($data);
        }
        return $this->fail("No Record Found");
    }


    public function mmts()
    {
        $user = auth()->user('api');
        $mmt_user_ids = Role::where('name', 'MMT')->pluck('user_ids')->first();
        $mmts = User::with(array('RouteVehicle' => function ($q) {
            $q->select('route_id', 'vehicle_id', 'user_id', 'date')->where('check_out', 'exists', true)->where('reception', 0)
                ->with(array('route' => function ($q) {
                    $q->select('_id', 'name');
                }))
                ->with(array('vehicle' => function ($q) {
                    $q->select('_id', 'vehicle_number');
                }));
        }))->whereIn('_id', $mmt_user_ids)->where('access_level_ids', 'all', $user->access_level_ids)->get(['name', 'balance', 'id'])->toArray();
        foreach ($mmts as $key => $mmt) {
            if (!empty($mmt['route_vehicle'])) {
                $mmts[$key]['route_id'] = $mmt['route_vehicle']['route']['_id'];
                $mmts[$key]['route_name'] = $mmt['route_vehicle']['route']['name'];

                $mmts[$key]['vehicle_id'] = $mmt['route_vehicle']['vehicle']['_id'];
                $mmts[$key]['vehicle_number'] = $mmt['route_vehicle']['vehicle']['vehicle_number'];
            } else {
                $mmts[$key]['route_id'] = null;
                $mmts[$key]['route_name'] = null;

                $mmts[$key]['vehicle_id'] = null;;
                $mmts[$key]['vehicle_number'] = null;;
            }
            unset($mmts[$key]['route_vehicle']);

            $data['mmts'] = $mmts;
        }
        return $this->response($data);
    }

    public function closeTodayRoute(Request $request)
    {
        $user = auth()->user('api');
        $rv = RouteVehicle::where('user_id', $user->id)->where('check_in', 'exists', true)->where('route_id', $request->route_id)->where('reception', 0)->first();

        if ($rv == null)
            return $this->response(null, true, 'Route can not be closed because route can not checked in');

        $rv->delivered_to = $request->delivered_to;
        $rv->lat = $request->lat;
        $rv->lng = $request->lng;
        // $rv->status = $request->status;
        $rv->time = $request->time;
        $rv->visiting_points = $request->visiting_points;
        $rv->save();

        return $this->response(null, true, 'Route closed successfully');
    }

    //current login user mprs
    public function mprs()
    {
        $user = auth()->user('api');
        $mr = MilkReception::where('mcc_id', $user->access_level_ids[0])->orderBy('_id', 'DESC')->get()->first();

        $query = MilkPurchase::with('mcc', 'supplier', 'cp')->where('created_by', $user->id)->orderBy('_id', 'desc');
        if ($mr <> null)
            $query->WhereDate('time', '>=', $mr->getAttributes()['to_time']);
        $mprs = $query->get();
        $data = [];
        foreach ($mprs as $row) {
            $mpr['id'] = $row->id;
            $mpr['serial_number'] = ($row->serial_number) ? 'MPR-' . $row->serial_number : '';
            $mpr['mcc'] = ($row->mcc) ? $row->mcc->name : ($row->cp ? $row->cp->name : '');
            $mpr['supplier_name'] = ($row->supplier) ? $row->supplier->name : '';
            $mpr['date'] = $row->time;
            $mpr['gross_volume'] = $row->gross_volume;
            $mpr['shift'] = $row->shift;
            $mpr['tests'] = $row->tests;
            $mpr['opening_balance'] = $row->opening_balance;
            $mpr['supplier_code'] = $row->supplier->code;
            $mpr['total_gross_volume'] = $mprs->sum('gross_volume');
            $mpr['total_ts_volume'] = $mprs->sum('ts_volume');
            $mpr['ts_volume'] = $row->ts_volume;
            $mpr['referenceNumber'] = $row->referenceNumber;
            $data[] = $mpr;
        }
        return $this->response(['mps' => $data]);
    }
    public function mpr($id)
    {
        $row = MilkPurchase::with('mcc', 'supplier', 'cp')->where('_id', $id)->first();

        $mpr['id'] = $row->id;
        $mpr['serial_number'] = ($row->serial_number) ? 'MPR-' . $row->serial_number : '';
        $mpr['mcc'] = ($row->mcc) ? $row->mcc->name : ($row->cp ? $row->cp->name : '');
        $mpr['supplier_name'] = ($row->supplier) ? $row->supplier->name : '';
        $mpr['date'] = date("d/m/y H:i a", $row->time);
        $mpr['gross_volume'] = $row->gross_volume;
        $mpr['shift'] = $row->shift == 1 ? 'Morning' : 'Evening';
        $mpr['tests'] = $row->tests;
        $data = $mpr;

        return $this->response(['mpr' => $data]);
    }

    public function areaOffices()
    {
        $area_offices = AreaOfficeResource::collection(AreaOffice::where(['status' => 1])->get(['_id', 'code', 'name']));
        return $this->response(['area_offices' => $area_offices]);
    }
    public function getCpmmt(Request $request)
    {
        $user = auth()->user('api');
        $areaOfficeid = $user->areaOffice->first()->id;
        if ($user->areaOffice->first() == null)
            return $this->fail('Area Office is not assigned');
        $routePlan = RouteVehicle::where('user_id', $user->id)->where('check_in', 'exists', true)->where('reception', 0)->first();

        if ($routePlan == null)
            return $this->fail('Route not found');

        $cps = $routePlan->route->collection_points;
        $co_points = CollectionPoint::where('area_office_id', $areaOfficeid)->select('id', 'name', 'code', 'is_mcc')->get();
        $cps_array = array();
        foreach ($co_points as $cp) {
            $partofRoutePlan = 0;
            if (in_array($cp->id, $cps))
                $partofRoutePlan = 1;
            $cps_array[] =  ['name' => $cp->name, 'code' => $cp->code, 'id' => $cp->id, 'is_mcc_ffl' => $cp->is_mcc, 'route_plan' => $partofRoutePlan, 'suppliers' => SupplierResource::collection($cp->suppliers)];
        }
        $data = ['cps' => $cps_array];
        return $this->response($data, true);
    }

    public function getAllVehicles()
    {
        $vehicles = MilkCollectionVehicle::where('status', 1)->with('routeId')->get();

        $newRouteId = array();
        $finalArray = [];

        foreach ($vehicles as $key => $value) {

            foreach ($value->routeId as $inner) {
                $temArray = [];
                array_push($newRouteId, $inner->route_id);
            }
            $temArray['route_ids'] =  $newRouteId;
            $temArray['id'] = $value->_id;
            $temArray['number'] = $value->vehicle_number;
            array_push($finalArray, $temArray);
        }
        return $this->response(['vehicles' => $finalArray]);
    }

    public function getAreaOfficeBalance(Request $request)
    {
        $checkBalance = AreaOffice::where('_id', $request->area_office_id)->first();
        if ($checkBalance) {
            $result['balance']  = ($checkBalance->balance) ? $checkBalance->balance : 0;
            return $this->response($result, true);
        } else {
            return $this->fail("No Record Found");
        }
    }
    public function getMmtBalance(Request $request)
    {
        $checkBalance = User::where('_id', $request->user_id)->first();
        if ($checkBalance) {
            $result['balance']  = ($checkBalance->balance) ? $checkBalance->balance : 0;
            return $this->response($result, true);
        } else {
            return $this->fail("No Record Found");
        }
    }

    public function getPlantDispatchReport()
    {
        $currentDate = Carbon::now()->startOfDay();
        $startOfDay = Date::createFromTimestamp($currentDate->timestamp);
        $endOfDay = Date::createFromTimestamp($currentDate->endOfDay()->timestamp);

        $user = auth()->user('api');
        $areaOfficeId = $user->access_level_ids[0];

        $plantDispatchReport['plant_dispatch_report'] = MilkDispatch::with(array('routeVehicle' => function ($q) {
            $q->select('_id', 'vehicle_number', 'vehicle_id');
        }))
            ->whereBetween('created_at', [$startOfDay, $endOfDay])->where('area_office_id', $areaOfficeId)->get()->toArray();

        foreach ($plantDispatchReport['plant_dispatch_report'] as $key => $plant_dispatch_report) {
            $plantDispatchReport['plant_dispatch_report'][$key]['route_vehicle']['id'] = $plant_dispatch_report['route_vehicle']['_id'];
            $plantDispatchReport['plant_dispatch_report'][$key]['route_vehicle']['number'] = $plant_dispatch_report['route_vehicle']['vehicle_number'];
            unset($plantDispatchReport['plant_dispatch_report'][$key]['route_vehicle']['_id'], $plantDispatchReport['plant_dispatch_report'][$key]['route_vehicle']['vehicle_number']);
        }

        if ($plantDispatchReport) {
            return $this->response($plantDispatchReport, true);
        } else {
            return $this->fail("No Record Found");
        }
    }
    public function handshakeMCC(Request $request)
    {
        $user = auth()->user('api');
        $validator = Validator::make($request->all(), [
            'referenceNumber' => 'required',
            'mcc_status' => 'required',
            'mcc_handshake_balance' => 'required',
            'mcc_handshake_date' => 'required',
            'mcc_handshake_time' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }
        $milkReception = MilkReception::where('referenceNumber', $request->referenceNumber)->first();
        if ($milkReception == null)
            return $this->fail('Milk reception is not found against reference Number');

        $handShake = Handshake::where('milk_reception_id', $milkReception->id)->first();
        if ($handShake == null)
            return $this->fail('Handshake record not found');

        $handShake->created_by_mca = $user->id;
        $handShake->mcc_handshake_balance = $request->mcc_handshake_balance;
        $handShake->mcc_status = (int) $request->mcc_status;
        $handShake->mcc_handshake_date_time = Carbon::createFromFormat('d/m/Y H:i a', $request->mcc_handshake_date . ' ' . $request->mcc_handshake_time)->format('Y-m-d H:i:s');
        $handShake->save();

        return $this->response(null, true, 'Mcc handshake data is saved');
    }
}
