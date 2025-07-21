<?php

namespace App\Http\Controllers;
use App\Helpers\Helper;
use App\Models\MilkTransfer;
use App\Models\Price;
use App\Models\Workflow;
use App\Models\WorkFlowApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maklad\Permission\Models\Role;
use URL;


class WorkFlowApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Milk Base Price'], ['only' => ['index']]);
        $this->middleware(['permission:Milk Transfer (mcc to mcc)'], ['only' => ['transferRequestDetail']]);
        $this->middleware(['permission:Milk Transfer (ao to ao)'], ['only' => ['transferRequestDetail']]);
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role_ids[0];
        $approvals = WorkFlowApproval::where('data.role_id',$role)->orderBy('_id','desc');
        if ($request->ajax()) {
             return datatables($approvals)
                 ->addColumn('document_type', function ($workflow) {
                    return $workflow->workflow_id && $workflow->WorkFlow?Workflow::$types[$workflow->WorkFlow->document_type]['sub_name']:$workflow->workflow_id;
                })
                 ->editColumn('created_at', function ($workflow) {
                     return Carbon::createFromFormat('Y-m-d H:i:s', $workflow->created_at)->format('l jS, F Y');
                 })
                 ->editColumn('created_by', function ($workflow) {
                    return $workflow->created_by ?$workflow->user->name:'';
                })
                 ->editColumn('type', function ($workflow) {
                     $text= '';
                     if($workflow->WorkFlow->document_type==1) {
                         if ($workflow->type == 'milk_base_pricing' && $workflow->request_type == 'create') {
                             $text = "Pricing (new)";
                         } else {
                             $text = "Pricing (update)";
                         }
                     }else if($workflow->WorkFlow->document_type==2){
                             $text = "Mcc to MCC";
                     }else if($workflow->WorkFlow->document_type==3){
                             $text = "Area Office to Area Office";
                     }

                    return $text;
                })
                ->editColumn('code', function ($workflow) {
                    if($workflow->WorkFlow->document_type==1) {
                        return '<a href="'.URL::to("/workflow/batch-prices/$workflow->code").'">' . $workflow->code . '</a>';
                    }else if($workflow->WorkFlow->document_type==2 || $workflow->WorkFlow->document_type==3){
                       return '<a href="'.URL::to("/workflow/transfer/$workflow->code").'">' . $workflow->code . '</a>';
                    }
                })
                 ->editColumn('status', function ($row) {
                    return ($row->status==1)?'Accepted':($row->status==3?'Rejected':($row->status==4?'Reverted':'Pending'));
                })
                 ->addColumn('steps', function ($row) {
                     $data = '';
                   foreach  ($row->data as $key=>$step) {
                         $role = Role::where('_id', $step['role_id'])->pluck('name')->first();
                         $status= $step['status']==1?'text-success':($step['status']==3?'text-danger':($step['status']==4?'text-warning':''));
                         $data .= '<i class=" fa fa-circle ' . $status . '"></i> '.ucfirst($role).'<br>';
                     }
                     return $data;
                 })

                ->rawColumns(['document_type','type','status','code','steps'])
                ->toJson();
        }
        return view('/content/workflow_approval/index');
    }


    public function updateStatus(Request $request){
        $code = (int)$request->code;
        $status = (int)$request->status;
        $user = auth()->user();
        $workflow_approvel = WorkFlowApproval::where('code',$code)->first();

        $workflow_approvel->updated_by= $user->id;

        $arr = [];
        $remrk = ['remark'=>$request->remark, 'created_by'=>$user->id,'date'=>\Carbon\Carbon::now()->toDateTimeString()];
        if($workflow_approvel->remarks){
            $arr = $workflow_approvel->remarks;
            array_push($arr,$remrk);
        }else{
            $arr[]= $remrk;
        }
        $workflow_approvel->remarks = $arr;

        $data = $workflow_approvel->data;
        $role = $user->role_ids[0];
            if($status == 3){
                $workflow_approvel->status = $status;
                if($workflow_approvel->request_type=='update'){
                    $prices = Price::where('code',$code)->get();
                    foreach ($prices as $row ) {
                        Helper::createPriceLog($row,3);
                        $row->update(['code' => null,'update_request' => null, 'update_price' => null, 'update_volume' => null]);
                    }
                }else if($workflow_approvel->request_type=='create'){
                    $prices = Price::where('code',$code)->get();
                    foreach ($prices as $row ) {
                        Helper::createPriceLog($row,3);
                        $row->update(['status'=>3]);
                    }
                }
                foreach ($data as $key => $row) {
                    if ($row['role_id'] == $role) {
                        $data[$key] = ['role_id' => $row['role_id'], 'status' => $status, 'updated_by' => $user->id];
                    }
                }

                $message = 'Price Rejected by '.$user->name;
                Helper::createNotification($workflow_approvel->created_by, $message, 2);
            }
        else if($status == 1) {
            $workflow_approvel->status =0;

            $is_approved_by_all = 0;
            $required_approvals_count = count($data);
            $workflow_approvel->current_step = ($required_approvals_count==$workflow_approvel->current_step)?$workflow_approvel->current_step:$workflow_approvel->current_step+1;
            foreach ($data as $key => $row) {
                if($row['status'] == 1){
                    $is_approved_by_all += 1;
                }
                if ($row['role_id'] == $role) {
                    $is_approved_by_all += 1;
                    $data[$key] = ['role_id' => $row['role_id'], 'status' => $status, 'updated_by' => $user->id];
                }
            }

            if($is_approved_by_all == $required_approvals_count){
                $prices = Price::where('code',$code)->get();
                if($workflow_approvel->request_type == 'create') {
                    foreach ($prices as $pric) {
                        if ($pric->update_price) {
                            $pric->price = $pric->update_price;
                            $pric->volume = $pric->update_volume;
                            $pric->update_volume = null;
                            $pric->update_price = null;
                            $pric->update_request = 0;
                            $pric->is_reverted = 0;
                        }
                        $pric->status = 1;
                        $pric->approved_at = date('d-m-Y');
                        $pric->save();
                    }
                }
                else if($workflow_approvel->request_type == 'update'){
                    $prices = Price::where('code',$code)->get();
                    foreach ($prices as $row ) {
                        Helper::createPriceLog($row,1);
                        $row->update(['code' => null,'approved_at'=>date('d-m-Y'),'status'=>1,'price'=>$row->update_price,'volume'=>$row->update_volume,'update_request' => null, 'update_price' => null, 'update_volume' => null]);
                    }
                }
                $workflow_approvel->status = 1;

                $message = 'Price Approved by '.$user->name;
                Helper::createNotification($workflow_approvel->created_by, $message, 1);
            }
        } else if($status == 4) {
            $workflow_approvel->status = $status;
            $workflow_approvel->current_step = 1;

            $prices = Price::where('code',$code)->get();
            foreach ($prices as $row ) {
                Helper::createPriceLog($row,4);
                $row->update(['status'=>4]);
            }

            foreach ($data as $key => $row) {
                if ($row['role_id'] == $role) {
                    $data[$key] = ['role_id' => $row['role_id'], 'status' => 0, 'updated_by' => $user->id];
                } else {
                    $data[$key] = ['role_id' => $row['role_id'], 'status' => 0, 'updated_by' => $user->id];
                }
            }
            
            $message = 'Price Reverted by '.$user->name;
            Helper::createNotification($workflow_approvel->created_by, $message, 4);
        }

        $workflow_approvel->data = $data;
        $workflow_approvel->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }


    public function updateStatusMT(Request $request){
        $code = (int)$request->code;
        $status = (int)$request->status;
        $user = auth()->user();
        $workflow_approvel = WorkFlowApproval::where('code',$code)->first();
        $workflow_approvel->updated_by = $user->id;

        $mt = MilkTransfer::where('wf_code',(int)$code)->first();

        $arr = [];
        $remrk = ['remark'=>$request->remark, 'created_by'=>$user->id,'date'=>\Carbon\Carbon::now()->toDateTimeString()];
        if($workflow_approvel->remarks){
            $arr = $workflow_approvel->remarks;
            array_push($arr,$remrk);
        }else{
            $arr[]= $remrk;
        }
        $workflow_approvel->remarks = $arr;

        $data = $workflow_approvel->data;
        $role = $user->role_ids[0];
         if($status == 3){
                $workflow_approvel->status = $status;
                foreach ($data as $key => $row) {
                    if ($row['role_id'] == $role) {
                        $data[$key] = ['role_id' => $row['role_id'], 'status' => $status, 'updated_by' => $user->id];
                    }
                }
                $mt->status = 3;
                $message = 'Transfer Rejected by '.$user->name;
                Helper::createNotification($mt->created_by, $message, 6);
            }
        else if($status == 1) {
            $workflow_approvel->status = 0;
            $is_approved_by_all = 0;
            $required_approvals_count = count($data);
            $workflow_approvel->current_step = ($required_approvals_count==$workflow_approvel->current_step)?$workflow_approvel->current_step:$workflow_approvel->current_step+1;
            foreach ($data as $key => $row) {
                if($row['status'] == 1){
                    $is_approved_by_all += 1;
                }
                if ($row['role_id'] == $role) {
                    $is_approved_by_all += 1;
                    $data[$key] = ['role_id' => $row['role_id'], 'status' => $status, 'updated_by' => $user->id];
                }
            }

            if($is_approved_by_all == $required_approvals_count){
                $mt->status = 1;
                $message = 'Transfer Approved by '.$user->name;
                Helper::createNotification($mt->created_by, $message, 5);
                $workflow_approvel->status = 1;
                }
        }
        else if($status == 4){
            $workflow_approvel->status = 0;
            $workflow_approvel->current_step = 1;
            foreach ($data as $key => $row) {
                if ($row['role_id'] == $role) {
                    $data[$key] = ['role_id' => $row['role_id'], 'status' => 0, 'updated_by' => $user->id];
                } else {
                    $data[$key] = ['role_id' => $row['role_id'], 'status' => 0, 'updated_by' => $user->id];
                }
            }
        }

        $workflow_approvel->data = $data;
        $mt->save();
        $workflow_approvel->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }


    public function transferRequestDetail(Request $request, $code)
    {
        $user = auth()->user();
        $role = $user->role_ids[0]??'';
        $mt = MilkTransfer::where('wf_code',(int)$code)->with('fromCp','toCp','fromAo','toAo')->first();
        $WorkFlowApproval = WorkFlowApproval::where('code', (int)$code)->first();
        $is_curr_user_on_curr_step = 0;
        if ($WorkFlowApproval){
            foreach ($WorkFlowApproval->data as $key => $data) {
                if ($WorkFlowApproval->status == 0 && $WorkFlowApproval->current_step == $key + 1 && $role == $data['role_id']) {
                    $is_curr_user_on_curr_step = 1;
                }
            }
        }

        return view('content/workflow_approval/transfer_request_detail')->with(get_defined_vars());
    }

}
