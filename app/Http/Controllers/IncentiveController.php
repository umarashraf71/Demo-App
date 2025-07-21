<?php

namespace App\Http\Controllers;

use App\Models\Incentive;
use App\Models\IncentiveType;
use App\Models\Supplier;
use App\Models\SupplierTestBaseIncentive;
use App\Models\SupplierType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class IncentiveController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Incentive Rates'], ['only' => ['index']]);
        $this->middleware(['permission:Create Incentive Rates'], ['only' => ['store']]);
        $this->middleware(['permission:Edit Incentive Rates'], ['only' => ['update']]);
    }

    public function save(Request $request)
    {
        $request->validate(['name' => 'required|unique:incentives|string']);
        $incentive = new Incentive;
        $delivery_config = array();
        $delivery_config['cdf'] = (int) 0;
        $delivery_config['mvmc'] = (int) 0;
        $delivery_config['lf_ffl_chiller'] = (int) 0;
        $delivery_config['lf_own_chiller'] = (int) 0;
        $delivery_config['vmca_ffl_chiller'] = (int) 0;
        $delivery_config['vmca_own_chiller'] = (int) 0;
        $delivery_config['cf'] = (int) 0;

        $incentive->name = $request->name;
        $incentive->config = (array) $delivery_config;
        $incentive->save();
        return redirect()->back()->with('success', 'Configuration updated successfully');
    }


    public function store(Request $request)
    {
        $request->validate(['incentive_type' => 'required|string', 'source_type' => 'required|string', 'amount' => 'required|numeric', 'range' => 'required|string']);
        $newrange = explode(" - ", $request->range);

        $previous_ranges = Incentive::where(['incentive_type' => $request->incentive_type, 'source_type' => $request->source_type])->get(['from', 'to']);
        if (count($previous_ranges) > 0) {
            foreach ($previous_ranges as $range) {
                $from = $range->from;
                $to = $range->to;
                for ($i = $from; $i <= $to; $i++) {
                    if ($newrange[0] == $i || $newrange[1] == $i) {
                        return back()->withErrors(['range' => ['Range already selected.']])->withInput();
                    }
                }
                for ($i = $newrange[0]; $i <= $newrange[1]; $i++) {
                    if ($i == $from || $i == $to) {
                        return back()->withErrors(['range' => ['Range already selected.']])->withInput();
                    }
                }
            }
        }
        $incentive = new Incentive;
        $incentive->incentive_type = $request->incentive_type;
        $incentive->source_type = $request->source_type;
        $incentive->amount = $request->amount;
        $incentive->range = $request->range;
        $incentive->from = (int)$newrange[0];
        $incentive->to = (int)$newrange[1];
        $incentive->status = 1;
        $incentive->save();
        return redirect()->back()->with('success', 'Saved successfully');
    }

    public function update(Request $request)
    {
        $request->validate(['incentive_type' => 'required|string', 'source_type' => 'required|string', 'amount' => 'required|numeric', 'range' => 'required|string']);
        $newrange = explode(" - ", $request->range);
        $incentive = Incentive::where('_id', $request->id)->first();
        $previous_ranges = Incentive::where(['incentive_type' => $request->incentive_type, 'source_type' => $request->source_type])->whereNotIn('_id', [$request->id])->get(['from', 'to']);

        if (count($previous_ranges) > 0) {
            foreach ($previous_ranges as $range) {
                $from = $range->from;
                $to = $range->to;
                for ($i = $from; $i <= $to; $i++) {
                    if ($newrange[0] == $i || $newrange[1] == $i) {
                        return back()->withErrors(['range' => ['Range already selected.']])->withInput();
                    }
                }
                for ($i = $newrange[0]; $i <= $newrange[1]; $i++) {
                    if ($i == $from || $i == $to) {
                        return back()->withErrors(['range' => ['Range already selected.']])->withInput();
                    }
                }
            }
        }

        $incentive->incentive_type = $request->incentive_type;
        $incentive->source_type = $request->source_type;
        $incentive->amount =  $request->amount;
        $incentive->range =  $request->range;
        $incentive->from =  (int)$newrange[0];
        $incentive->to =  (int)$newrange[1];
        $incentive->save();
        return redirect()->back()->with('success', 'Saved successfully');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(Incentive::with('incentive', 'source')->orderBy('created_at', 'desc')->get());
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $btn = '';
                if (Auth::user()->can('Edit Incentive Rates')) {
                    $btn .= '<a title="Edit" onclick="editForm(\'' . $row->id . '\',\'' . $row->amount . '\',\'' . $row->incentive_type . '\',\'' . $row->source_type . '\',\'' . $row->range . '\',\'' . $row->from . '\',\'' . $row->to . '\')" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
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
            $table->addIndexColumn()->addColumn('source', function ($row) {
                return ($row->source) ? ucfirst($row->source->name) : $row->source_type;
            })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('source_type'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['source_type'], $request->get('source_type')) ? true : false;
                        });
                    }
                    if (!empty($request->get('incentive_type'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['incentive_type'], $request->get('incentive_type')) ? true : false;
                        });
                    }
                })->rawColumns(['action', 'incentiveType', 'sourceType', 'status']);
            return $table->toJson();
        }

        $incentiveTypes = IncentiveType::orderBy('created_at', 'desc')->get();
        $sourceTypes = SupplierType::orderBy('created_at', 'desc')->get();
        return view('content/incentive/index')->with(get_defined_vars());
    }

    public function statusUpdate(Request $request)
    {
        Incentive::updateOrCreate(array('_id' => $request->id), [
            'status' => (int)$request->status,
        ]);
        return Response::json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    public function getIncentiveTypes(Request $request)
    {
        $incentive_types = Incentive::where('source_type', $request->source_type)->get()->pluck('incentive_type');
        if (count($incentive_types) > 0) {
            $incentive_types = IncentiveType::whereIn('_id', $incentive_types)->where('is_test_base', 1)->with('qa_test')->get();
        }
        $html = '<option value="" selected disabled>Select Incentive Type</option>';
        foreach ($incentive_types as $data) {
            if ($data->qa_test) {
                $ranges = explode("-", $data->qa_test->range_value);
                $min = isset($ranges[0]) ? $ranges[0] : '';
                $max = isset($ranges[1]) ? $ranges[1] : '';
                $html .= '<option  range="' . $data->qa_test->range_value . '"  test-id="' . $data->qa_test->id . '"  min-range="' . $min . '" max-range="' . $max . '" value="' . $data->id . '" >' . ucfirst($data->name) . '(' . $data->qa_test->qa_test_name . ')' . '</option>';
            }
        }
        return Response::json([
            'success' => true,
            'data' => $html,
        ]);
    }
}
