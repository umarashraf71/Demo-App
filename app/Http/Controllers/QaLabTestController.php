<?php

namespace App\Http\Controllers;

use App\Http\Controllers\IncentiveTypeController as ControllersIncentiveTypeController;
use App\Models\IncentiveType;
use App\Models\QaLabTest;
use App\Models\MeasurementUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class QaLabTestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View QaLabTest'], ['only' => ['index']]);
        $this->middleware(['permission:Create QaLabTest'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit QaLabTest'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete QaLabTest'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(QaLabTest::all());
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $btn = '';
                if (Auth::user()->can('Edit QaLabTest')) {
                    $btn .= '<a title="Edit" href="' . route('qa-labtest.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete QaLabTest')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('qa-labtest/' . $row->id . '') . '\',\'qa_labtest_datatable\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })
                ->editColumn('measurementunit_id', function ($row) {
                    if ($row->uom);
                    return ($row->uom) ? $row->uom->name : '';
                })
                ->rawColumns(['action']);

            return $table->toJson();
        }
        return view('content/ffl_qa_labtest/qa_labtest_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $test_uom = MeasurementUnit::all();
        return view('content.ffl_qa_labtest.qa_labtest_create', compact('test_uom'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qa_test_name' => 'required|string',
            'description' => 'required|string',
            'test_type' => 'required|numeric',
            'test_data_type' => 'required|string',
            'measurementunit_id' => 'required|string',
            'apply_test' => 'required|array',
            'rejection' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $request->merge([
            'created_by' => auth()->user()->id,
            'updated_by' => null
        ]);

        $input_values = $request->all();

        if ($input_values['test_data_type'] == 1) {
            $validator = Validator::make($request->all(), [
                'min' => 'required',
                'max' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $input_values['positive_negative'] = null;
            $input_values['yes_or_no'] = null;
        } elseif ($input_values['test_data_type'] == 2) {
            $validator = Validator::make($request->all(), [
                'positive_negative' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $input_values['range_value'] = null;
            $input_values['yes_or_no'] = null;
        } elseif ($input_values['test_data_type'] == 3) {
            $validator = Validator::make($request->all(), [
                'yes_or_no' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $input_values['range_value'] = null;
            $input_values['positive_negative'] = null;
        }

        $result = QaLabTest::create($input_values);
        if ($result) {
            return redirect()
                ->back()
                ->with('success', 'New record added successfully.');
        } else {
            return redirect()
                ->back()
                ->with('errorMessage', 'Record Not Save. Please check your information.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\QaLabTest  $qaLabTest
     * @return \Illuminate\Http\Response
     */
    public function show(QaLabTest $qaLabTest)
    {
        return redirect()
            ->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\QaLabTest  $qaLabTest
     * @return \Illuminate\Http\Response
     */
    public function edit(QaLabTest $qaLabTest, $id)
    {
        $qaLabTest = QaLabTest::find($id);
        $test_uom = MeasurementUnit::all();
        return view('content/ffl_qa_labtest/qa_labtest_edit', compact('qaLabTest', 'test_uom'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QaLabTest  $qaLabTest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'test_type' => 'required|numeric',
            'test_data_type' => 'required|string',
            'measurementunit_id' => 'required|string',
            'apply_test' => 'required|array',
            'rejection' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $request->merge([
            'updated_by' => auth()->user()->id
        ]);
        $input_values = $request->all();

        if ($input_values['test_data_type'] == 1) {
            $validator = Validator::make($request->all(), [
                'min' => 'required',
                'max' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $input_values['positive_negative'] = null;
            $input_values['yes_or_no'] = null;
        } elseif ($input_values['test_data_type'] == 2) {
            $validator = Validator::make($request->all(), [
                'positive_negative' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $input_values['range_value'] = null;
            $input_values['yes_or_no'] = null;
        } elseif ($input_values['test_data_type'] == 3) {
            $validator = Validator::make($request->all(), [
                'yes_or_no' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $input_values['range_value'] = null;
            $input_values['positive_negative'] = null;
        }
        unset($input_values['qa_test_name']);
        unset($input_values['_token']);
        unset($input_values['_method']);
        if (!isset($input_values['exceptional_release']))
            $input_values['exceptional_release'] = null;

        $result = QaLabTest::find($id)->update($input_values);
        if ($result) {
            return redirect()
                ->route('qa-labtest.index')
                ->with('success', 'Record updated successfully');
        } else {
            return redirect()
                ->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\QaLabTest  $qaLabTest
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (IncentiveType::where('qa_test_id', $id)->exists())
                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted due to exist in other module'

                ]);
            $qaLabTest = QaLabTest::where('_id', $id)->first();
            $res = $qaLabTest->delete();
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
}
