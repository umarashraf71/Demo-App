<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use App\Models\QaLabTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class MeasurementUnitController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Test UOM'], ['only' => ['index']]);
        $this->middleware(['permission:Create Test UOM'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Test UOM'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Test UOM'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(MeasurementUnit::all());
            $table->addIndexColumn()->addColumn('action', function (MeasurementUnit $row) {
                $btn = '';
                if (Auth::user()->can('Edit Test UOM')) {
                    $btn .= '<a title="Edit" href="' . route('test-uom.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Test UOM')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('test-uom/' . $row->id . '') . '\',\'test_uom_datatable\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })
                ->rawColumns(['action']);
            return $table->toJson();
        }
        return view('content.ffl_test_uom.test_uom_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('content.ffl_test_uom.test_uom_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->toArray());
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:supplier_types|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $request->merge([
            'created_by' => auth()->user()->id,
            'updated_by' => null,
        ]);

        $result = MeasurementUnit::create($request->all());
        if ($result) {
            return redirect()->back()
                ->with('success', 'New record added successfully');
        } else {
            return redirect()
                ->back()
                ->with('errorMessage', 'Record not save. Please check your information.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()
            ->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $measurementUnit  = MeasurementUnit::find($id);
        return view('content.ffl_test_uom.test_uom_edit', compact('measurementUnit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $request->merge([
            'updated_by' => auth()->user()->id,
        ]);

        $measurementUnit = MeasurementUnit::where('_id', $id)->first();
        $result = $measurementUnit->update($request->all());
        if ($result) {
            return redirect()->route('test-uom.index')
                ->with('success', 'Record updated successfully');
        } else {
            return redirect()
                ->back()
                ->with('errorMessage', 'Record not save. Please check your information.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (QaLabTest::where('measurementunit_id', $id)->exists())
                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted due to exist in other module'

                ]);

            $measurementUnit = MeasurementUnit::where('_id', $id)->first();
            $res = $measurementUnit->delete();
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
