<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaOfficeResource;
use App\Http\Resources\CollectionPointResource;
use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\MilkTransfer;
use App\Models\Workflow;
use App\Models\WorkFlowApproval;
use App\Traits\HttpResponseTrait;
use Auth;
use App\Models\Plant;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PurchasedMilkRejections;


class TransferController extends Controller
{
    use HttpResponseTrait;

    //  Get all Collection Centers related to current MCC area Office
    public function mccToMccCCs()
    {
        $collection_centers = [];
        $user = auth()->user('api');
        $cp_id = $user->access_level_ids[0];
        $cp = CollectionPoint::where('_id', $cp_id)->select('area_office_id', '_id')->first();
        if ($cp) {
            $collection_centers = CollectionPointResource::collection(CollectionPoint::whereNotIn('_id', [$cp->id])->where('area_office_id', $cp->area_office_id)->where('is_mcc', '1')->get(['_id', 'code', 'name']));
        }
        return $this->response(['collection_centers' => $collection_centers]);
    }
    public function aoToAoAreaOffices()
    {
        $area_offices = [];
        $user = auth()->user('api');
        $ao_id = $user->access_level_ids[0];
        $ao = AreaOffice::where('_id', $ao_id)->select('_id', 'zone_id')->first();
        if ($ao) {
            $area_offices = AreaOfficeResource::collection(AreaOffice::whereNotIn('_id', [$ao->id])->where(['zone_id' => $ao->zone_id, 'status' => 1])->get(['_id', 'code', 'name']));
        }

        return $this->response(['area_offices' => $area_offices]);
    }

    public function mccToMccSaveRequest(Request $request)
    {
        $user = auth()->user('api');
        $workFlow = Workflow::where('document_type', 2)->first();
        if ($workFlow) {
            $unique_code = $this->generateUniqueCode('App\Models\WorkFlowApproval');
            foreach ($workFlow->role_ids as $key => $role_id) {
                $data[] = ['role_id' => $role_id, 'status' => 0, 'updated_by' => null];
            }

            $remark = [];
            $wf = WorkFlowApproval::create(['code' => $unique_code, 'type' => 'milk_transfer', 'workflow_id' => $workFlow->id, 'data' => $data, 'created_by' => $user->id, 'status' => 0, 'current_step' => 1, 'remarks' => $remark]);
            $transfer_to = $request->transfer_to;
            $transfer_from =  $user['access_level_ids'][0] ?? '';
            if ($request->note && $transfer_to) {
                MilkTransfer::create(['wf_code' => $wf->code, 'volume' => $request->volume, 'note' => $request->note, 'from' => $transfer_from, 'to' => $transfer_to, 'created_by' => $user->id, 'type' => 'mcc', 'status' => 0]);
            }
            $data = [
                'mcc-to-mcc' => null
            ];

            return $this->response($data, true, 'Request created. Waiting for approval');
        } else {
            return $this->fail('Please create workflow first');
        }
    }

    public function aoToAoSaveRequest(Request $request)
    {
        $user = auth()->user('api');
        $workFlow = Workflow::where('document_type', 3)->first();
        if ($workFlow) {
            $unique_code = $this->generateUniqueCode('App\Models\WorkFlowApproval');
            foreach ($workFlow->role_ids as $key => $role_id) {
                $data[] = ['role_id' => $role_id, 'status' => 0, 'updated_by' => null];
            }

            $remark = [];
            $wf = WorkFlowApproval::create(['code' => $unique_code, 'type' => 'milk_transfer', 'workflow_id' => $workFlow->id, 'data' => $data, 'created_by' => $user->id, 'status' => 0, 'current_step' => 1, 'remarks' => $remark]);
            $transfer_to = $request->transfer_to;
            $transfer_from =  $user['access_level_ids'][0] ?? '';
            if ($request->note && $transfer_to) {
                MilkTransfer::create(['wf_code' => $wf->code, 'volume' => $request->volume, 'note' => $request->note, 'from' => $transfer_from, 'to' => $transfer_to, 'created_by' => $user->id, 'type' => 'ao', 'status' => 0]);
            }
            $data = [
                'ao-to-ao' => null
            ];
            return $this->response($data, true, 'Request created. Waiting for approval');
        } else {
            return $this->fail('Please create workflow first');
        }
    }

    public function MmtToOtherAoSaveRequest(Request $request)
    {
        $user = auth()->user('api');

        $transfer_to = $request->transfer_to;
        $transfer_from =  $user->id;
        if ($request->note && $transfer_to) {
            MilkTransfer::create(['volume' => $request->volume, 'note' => $request->note, 'from' => $transfer_from, 'to' => $transfer_to, 'created_by' => $user->id, 'type' => 'mmt_other_ao', 'status' => 1]);
        }
        return $this->response(null, true, 'Request created.');
    }

    public function transferMCCToMCC(Request $request)
    {
        $transfer_id = $request->transfer_id;
        $mt = MilkTransfer::where('_id', $transfer_id)->where('status', 1)->first();
        if ($mt) {
            //if transferred milk is rejected 
            if ($request->status == 0) {
                $milkTransfer = $mt->toArray();
                $milkTransfer['transfer_id'] = $milkTransfer['_id'];
                $milkTransfer['type'] = 'mcc_transfer';
                $milkTransfer['gross_volume'] = $milkTransfer['volume'];
                unset($milkTransfer['_id'], $milkTransfer['volume'], $milkTransfer['status']);
                PurchasedMilkRejections::create($milkTransfer);
                //deduct balance from transferring collection points and update milktransfer document
                $from_balance = (float)CollectionPoint::where('_id', $mt->from)->pluck('balance')->first();
                $to_balance = (float)CollectionPoint::where('_id', $mt->to)->pluck('balance')->first();
                $gain_loss = $request->received_volume - $mt->volume;

                if ($request->received_volume != $mt->volume) {
                    $mt->gain_loss = $gain_loss;
                }
                $mt->opening_balance_from = $from_balance;
                $mt->opening_balance_to = $to_balance;
                CollectionPoint::where('_id', $mt->from)->update(['balance' => $from_balance - $mt->volume]);
                $mt->volume_received = $request->received_volume;
                $mt->status = 3;
                $mt->tests = $request->test;
                $mt->save();
                return $this->response(null, true, 'Transaction Successfull! Milk is rejected');
            }
            //milk is not rejected
            $from_balance = (float)CollectionPoint::where('_id', $mt->from)->pluck('balance')->first();
            $to_balance = (float)CollectionPoint::where('_id', $mt->to)->pluck('balance')->first();
            $gain_loss = $request->received_volume - $mt->volume;

            if ($request->received_volume != $mt->volume) {
                $mt->gain_loss = $gain_loss;
            }
            $mt->opening_balance_from = $from_balance;
            $mt->opening_balance_to = $to_balance;

            $from_cutting = $mt->volume;
            if ($from_balance >= $from_cutting) {
                CollectionPoint::where('_id', $mt->from)->update(['balance' => $from_balance - $from_cutting]);
                CollectionPoint::where('_id', $mt->to)->update(['balance' => $request->received_volume + $to_balance]);
                $mt->volume_received = $request->received_volume;
                $mt->status = 2;
                $mt->tests = $request->test;
                $mt->save();
                return $this->response(null, true, 'Transaction Successfull');
            } else {
                return $this->response(null, false, 'Not enough balance to transfer');
            }
        } else {
            return $this->response(null, false, 'No transfer request found');
        }
    }

    public function transferMCCStatus()
    {
        $cp = CollectionPoint::where('_id', Auth::user()['access_level_ids'][0])->first();
        $milkTransfers = MilkTransfer::where([
            ['to', $cp->id],
            ['type', 'mcc']
        ])->with('user')->get();
        $data = [
            'milkTransferStatuses' => $milkTransfers
        ];
        return $this->response($data, true, 'Status Recieved Successfully');
    }

    public function transferAoStatus()
    {
        $ao = AreaOffice::where('_id', Auth::user()['access_level_ids'][0])->first();
        $milkTransferStatuses = MilkTransfer::where('to', $ao->id)->with('user')->get();

        $data = [
            'milkTransferStatuses' => $milkTransferStatuses
        ];
        return $this->response($data, true, 'Status Recieved Successfully');
    }

    // 
    public function getCurrMCCTransferReq()
    {
        $cp = CollectionPoint::where('_id', Auth::user()['access_level_ids'][0])->first();
        if ($cp) {
            $getmilkTransfersReq = MilkTransfer::where([
                ['from', $cp->id],
                ['type', 'mcc']
            ])->with('user')->get();
            $data = [
                'milkTransferRequests' => $getmilkTransfersReq
            ];
            return $this->response($data, true, 'MCC Milk Transfer Statuses Get Succesfully');
        } else {
            return $this->fail('No Record Found');
        }
    }

    public function getCurrAreaOfficeTransferReq()
    {
        $ao = AreaOffice::where('_id', Auth::user()['access_level_ids'][0])->first();
        if ($ao) {
            $milkTransferStatuses = MilkTransfer::where('from', $ao->id)->with('user')->get();

            $data = [
                'milkTransferRequests' => $milkTransferStatuses
            ];
            return $this->response($data, true, 'AO Milk Transfer Statuses Get Succesfully');
        } else {
            return $this->fail('No Record Found');
        }
    }

    public function getPlants()
    {
        $plants = Plant::all();
        if ($plants) {
            $data = [
                'plants' => $plants
            ];
            return $this->response($data, true, 'List of Plants');
        } else {
            return $this->fail('No Record Found');
        }
    }
    public function transferAoToAo(Request $request)
    {
        $transfer_id = $request->transfer_id;
        $mt = MilkTransfer::where('_id', $transfer_id)->where('status', 1)->withTrashed()->first();

        if ($mt) {
            //if transferred milk is rejected 
            if ($request->status == 0) {
                $milkTransfer = $mt->toArray();
                $milkTransfer['transfer_id'] = $milkTransfer['_id'];
                $milkTransfer['gross_volume'] = $milkTransfer['volume'];
                unset($milkTransfer['_id'], $milkTransfer['volume'], $milkTransfer['status']);
                //if area office to area office transfer
                if ($mt->type == 'ao') {
                    $from_balance = (float)AreaOffice::where('_id', $mt->from)->pluck('balance')->first();
                    $milkTransfer['type'] = 'ao_transfer';
                } elseif ($mt->type == 'mmt_other_ao') {
                    $from_balance = (float)User::where('_id', $mt->from)->pluck('balance')->first();
                    $milkTransfer['type'] = 'mmt_other_ao_transfer';
                }
                PurchasedMilkRejections::create($milkTransfer);
                //deduct balance from transferring area office/mmt and update milktransfer document
                $to_balance = (float)AreaOffice::where('_id', $mt->to)->pluck('balance')->first();
                $gain_loss = $request->received_volume - $mt->volume;

                if ($request->received_volume != $mt->volume) {
                    $mt->gain_loss = $gain_loss;
                }
                $mt->opening_balance_from = $from_balance;
                $mt->opening_balance_to = $to_balance;
                if ($mt->type == 'ao')
                    AreaOffice::where('_id', $mt->from)->update(['balance' => $from_balance - $mt->volume]);
                elseif ($mt->type == 'mmt_other_ao')
                    User::where('_id', $mt->from)->update(['balance' => $from_balance - $mt->volume]);
                $mt->volume_received = $request->received_volume;
                $mt->status = 3;
                $mt->tests = $request->test;
                $mt->save();
                return $this->response(null, true, 'Transaction Successfull! Milk is rejected');
            }
            //if area office to area office transfer
            if ($mt->type == 'ao')
                $from_balance = (float)AreaOffice::where('_id', $mt->from)->pluck('balance')->first();
            elseif ($mt->type == 'mmt_other_ao')
                $from_balance = (float)User::where('_id', $mt->from)->pluck('balance')->first();
            $to_balance = (float)AreaOffice::where('_id', $mt->to)->pluck('balance')->first();
            $gain_loss = $request->received_volume - $mt->volume;

            if ($request->received_volume != $mt->volume) {
                $mt->gain_loss = $gain_loss;
            }
            $mt->opening_balance_from = $from_balance;
            $mt->opening_balance_to = $to_balance;

            $from_cutting = $mt->volume;
            if ($from_balance >= $from_cutting) {
                if ($mt->type == 'ao')
                    AreaOffice::where('_id', $mt->from)->update(['balance' => $from_balance - $from_cutting]);
                elseif ($mt->type == 'mmt_other_ao')
                    User::where('_id', $mt->from)->update(['balance' => $from_balance - $from_cutting]);
                AreaOffice::where('_id', $mt->to)->update(['balance' => $request->received_volume + $to_balance]);
                $mt->volume_received = $request->received_volume;
                $mt->status = 2;
                $mt->tests = $request->test;
                $mt->save();

                return $this->response(null, true, 'Transaction Successfull');
            } else {
                return $this->fail('Not enough balance to transfer');
            }
        } else {
            return $this->response(null, false, 'No transfer request found');
        }
    }

    public function generateUniqueCode($Class = null)
    {
        do {
            $random_code = random_int(1000, 9999);
        } while ($Class::where("code", "=", $random_code)->first());

        return $random_code;
    }
}
