<?php

namespace App\Http\Controllers;

use App\Models\Incentive;
use App\Models\IncentiveType;
use App\Models\QaLabTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class IncentiveTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Incentive Type'], ['only' => ['index']]);
        $this->middleware(['permission:Create Incentive Type'], ['only' => ['store']]);
        $this->middleware(['permission:Edit Incentive Type'], ['only' => ['update']]);
        $this->middleware(['permission:Delete Incentive Type'], ['only' => ['destroy']]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:33|unique:incentive_types,name,' . $request->id . ',_id',
            'qa_test' => 'required_with:is_test_base',
            'from' => 'nullable|before_or_equal:to',
        ]);

        $incentive = IncentiveType::where('_id', $request->id)->first();
        $incentive->name = $request->name;
        $incentive->description = $request->description;
        $incentive->from =  $request->from;
        $incentive->to =  $request->to;
        $incentive->is_test_base = ($request->has('is_test_base')) ? 1 : 0;
        $incentive->qa_test_id = $request->qa_test;
        $incentive->status = 1;
        $incentive->save();
        return redirect()->back()->with('success', 'Saved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:incentive_types|string',
            'qa_test' => 'required_with:is_test_base',
            'from' => 'nullable|before_or_equal:to',
        ]);
        $incentive = new IncentiveType;
        //        $delivery_config = array();
        //        $delivery_config['cdf'] = (int) 0;
        //        $delivery_config['mvmc'] = (int) 0;
        //        $delivery_config['lf_ffl_chiller'] = (int) 0;
        //        $delivery_config['lf_own_chiller'] = (int) 0;
        //        $delivery_config['vmca_ffl_chiller'] = (int) 0;
        //        $delivery_config['vmca_own_chiller'] = (int) 0;
        //        $delivery_config['cf'] = (int) 0;

        $incentive->name = $request->name;
        $incentive->description = $request->description;
        $incentive->from =  $request->from;
        $incentive->to =  $request->to;
        $incentive->is_test_base = ($request->has('is_test_base')) ? 1 : 0;
        $incentive->qa_test_id = $request->qa_test;
        $incentive->status = 1;
        //      $incentive->config = (array) $delivery_config;
        $incentive->save();
        return redirect()->back()->with('success', 'Saved successfully');
    }
    public function index(Request $request)
    {
        $tests = QaLabTest::where('is_test_based', 1)->get();
        if ($request->ajax()) {
            $table = datatables(IncentiveType::with('qa_test')->orderBy('created_at', 'desc')->get());
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $btn = '';
                $btn .= '<a title="Edit" onclick="editForm(\'' . $row->id . '\',\'' . $row->name . '\',\'' . $row->description . '\',\'' . $row->from . '\',\'' . $row->to . '\',\'' . $row->is_test_base . '\',\'' . $row->qa_test_id . '\')" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                //                if (Auth::user()->can('Delete Area Office')) {
                $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="delRecord(\'' . route('incentives.type.destroy', $row->id) . '\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                //                }

                return $btn;
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
            $table->addIndexColumn()->addColumn('qa_test', function ($row) {
                return $row->qa_test ? $row->qa_test->qa_test_name : '';
            })
                ->rawColumns(['action', 'status']);
            return $table->toJson();
        }
        return view('content/incentive/types')->with(get_defined_vars());
    }

    public function destroy($id)
    {
        if (Incentive::where('incentive_type', $id)->exists()) {
            return Response::json([
                'success' => false,
                'message' => 'Record not deleted due to exist in other module'
            ]);
        }
        try {
            $type = IncentiveType::where('_id', $id)->first();
            $res = $type->delete();
            if ($res)
                return Response::json([
                    'success' => true,
                    'message' => 'Record deleted'

                ]);
            else
                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted'

                ]);
        } catch (\Throwable $th) {
            return Response::json([
                'success' => false,
                'message' => 'Record not deleted'

            ]);
        }
    }
    public function statusUpdate(Request $request)
    {
        IncentiveType::updateOrCreate(array('_id' => $request->id), [
            'status' => (int)$request->status,
        ]);
        return Response::json([
            'success' => true,
            'message' => 'Status updated successfully'

        ]);
    }
}
