<?php

namespace App\Http\Controllers;

use App\Models\Incentive;
use App\Models\IncentiveType;
use App\Models\QaLabTest;
use App\Models\Supplier;
use App\Models\SupplierTestBaseIncentive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SupplierTestBaseIncentiveController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Test Based Supplier Incentives'], ['only' => ['index']]);
        $this->middleware(['permission:Create Test Based Supplier Incentives'], ['only' => ['save']]);
        $this->middleware(['permission:Edit Test Based Supplier Incentives'], ['only' => ['update']]);
    }
    public function index(Request $request)
    {
        $suppliers = [];
    if ($request->ajax()) {
            $table = datatables(SupplierTestBaseIncentive::with('qa_test','supplier','incentive','type_incentive')->orderBy('created_at','desc')->get());
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $ranges = $row->qa_test && isset($row->qa_test->range_value)?explode("-",$row->qa_test->range_value):[];
                $test_id = $row->qa_test ?$row->qa_test->id:'';
                $min = isset($ranges[0])?$ranges[0]:0;
                $max = isset($ranges[1])?$ranges[1]:0;
                $range_value = $row->qa_test?$row->qa_test->range_value:0;
                $btn = '';
                $btn .= '<a title="Edit" onclick="editForm(\'' . $row->id . '\',\'' . $row->supplier_id . '\',\'' . $row->incentive_type . '\',\'' . $row->from . '\',\'' . $row->to . '\',\'' . $row->passing_value . '\',\'' . $range_value . '\',\'' . $min . '\',\'' . $max . '\',\'' . $test_id  . '\')" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                return $btn;
            });
            $table->addIndexColumn()->addColumn('incentive', function ($row) {
                return ($row->incentive) ? ucfirst($row->incentive->name) : $row->incentive_type;
            });
            $table->addIndexColumn()->addColumn('status', function ($row) {
                $status = $row->status ? 'checked' : '';
                $data = '<div class="form-switch">
                                      <input type="checkbox" class="form-check-input" id="status_' . $row->id . '" ' . $status . ' onclick="statusUpdate(this,\'' . $row->id . '\')">
                                      <label class="form-check-label" for="status_' . $row->id . '"  >
                                          <span class="switch-icon-left"><i data-feather="check"></i></span>
                                          <span class="switch-icon-right"><i data-feather="x"></i></span>
                                      </label>
                                </div>';
                return $data;
            });
            $table->addIndexColumn()->addColumn('supplier', function ($row) {
                return ($row->supplier) ? ucfirst($row->supplier->name) : $row->supplier_id;
            });
//            $table->addIndexColumn()->addColumn('rate', function ($row) {
//                return ($row->incentive) ? $row->incentive->amount: '';
//            });
            $table->addIndexColumn()->addColumn('type', function ($row) {
                return ($row->type_incentive) ? ucfirst($row->type_incentive->name) : $row->incentive_type;
            });
            $table->addIndexColumn()->addColumn('test', function ($row) {
                return ($row->qa_test) ? ucfirst($row->qa_test->qa_test_name) : $row->qa_test_id;
            })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('supplier'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['supplier_id'], $request->get('supplier')) ? true : false;
                        });
                    }
                    if (!empty($request->get('incentive_type'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['incentive_type'], $request->get('incentive_type')) ? true : false;
                        });
                    }
                })->rawColumns(['status','action', 'test', 'supplier', 'type','rate']);
            return $table->toJson();
        }

        $incentiveTypes = IncentiveType::where(['is_test_base' => 1])->get();
        $test_base_incentive_types = IncentiveType::where(['is_test_base' => 1, 'status' => 1])->orderBy('created_at', 'desc')->get();
        if (count($test_base_incentive_types)>0) {
            $incentive_types = IncentiveType::where(['is_test_base' => 1, 'status' => 1])->get()->pluck('id');
            if (count($incentive_types) > 0) {
                $incentive_source_types = Incentive::whereIn('incentive_type', $incentive_types)->get()->pluck('source_type');
                if (count($incentive_source_types) > 0) {
                    $suppliers = Supplier::select('id', 'name','supplier_type_id')->whereIn('supplier_type_id', $incentive_source_types)->orderBy('created_at', 'desc')->get();
                }
            }
        }
        return view('content/incentive/test_base_suppliers')->with(get_defined_vars());
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier' => 'required|string',
            'incentive_type' => 'required|string',
            'to' => 'required|string',
            'from' => 'required|before_or_equal:to',
            'passing_value' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $from = $request->from;
        $to = $request->to;
        if(SupplierTestBaseIncentive::where(['supplier_id'=>$request->supplier,'incentive_type'=>$request->incentive_type])
            ->where('from', '<=', $request->to)
            ->where('to', '>=',$request->from)
            ->exists()){
            return response()->json([
                'success' => false,
                'message' => 'Incentive already given in this date range'
            ]);
        }
        $source_type_id = Supplier::where('_id',$request->supplier)->pluck('supplier_type_id')->first();
        $incentive = Incentive::where(['source_type'=>$source_type_id,'incentive_type'=>$request->incentive_type])->first();

        $data = new SupplierTestBaseIncentive;
        $data->supplier_id = $request->supplier;
        $data->incentive_type = $request->incentive_type;
        $data->from = $from;
        $data->to = $to;
        $data->qa_test_id = $request->test_id;
        $data->incentive_id = ($incentive)?$incentive->id:'';
        $data->incentive_amount = $incentive?$incentive->amount:'';
        $data->passing_value = $request->passing_value;
        $data->status = 1;
        $data->save();

        return response()->json([
            'success' => true,
            'message' => 'Data added successfully'
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier' => 'required|string',
            'incentive_type' => 'required|string',
            'to' => 'required|string',
            'from' => 'required|before_or_equal:to',
            'passing_value' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $from = $request->from;
        $to = $request->to;
        if(SupplierTestBaseIncentive::where(['supplier_id'=>$request->supplier,'incentive_type'=>$request->incentive_type])->whereNotIn('_id',[$request->id])
            ->where('from', '<=', $request->to)
            ->where('to', '>=',$request->from)
            ->exists()){
            return response()->json([
                'success' => false,
                'message' => 'Incentive already given in this date range'
            ]);
        }
        $source_type_id = Supplier::where('_id',$request->supplier)->pluck('supplier_type_id')->first();
        $incentive = Incentive::where(['source_type'=>$source_type_id,'incentive_type'=>$request->incentive_type])->first();
        $inc_type= IncentiveType::where('_id',$request->incentive_type)->first();

        if($inc_type){
            $request->test_id =  $inc_type->qa_test_id;
        }

        $data = SupplierTestBaseIncentive::where('_id',$request->id)->first();
        $data->supplier_id = $request->supplier;
        $data->incentive_type = $request->incentive_type;
        $data->from = $from;
        $data->to = $to;
        $data->qa_test_id = $request->test_id;
        $data->incentive_id = ($incentive)?$incentive->id:'';
        $data->incentive_amount = $incentive?$incentive->amount:'';
        $data->passing_value = $request->passing_value;
        $data->status = 1;
        $data->save();

        return response()->json([
            'success' => true,
            'message' => 'Data added successfully'
        ]);
    }

    public function statusUpdate(Request $request){
        SupplierTestBaseIncentive::updateOrCreate(array('_id' => $request->id),[
            'status'=>(int)$request->status,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }


}
