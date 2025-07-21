<?php

namespace App\Http\Controllers;

use App\Models\MilkCollectionVehicle;
use App\Models\RouteVehicle;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class MilkCollectionVehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View MCVehicle'], ['only' => ['index']]);
        $this->middleware(['permission:Create MCVehicle'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit MCVehicle'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete MCVehicle'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(MilkCollectionVehicle::with('vendor')->get());
            $table->addIndexColumn()->addColumn('action', function (MilkCollectionVehicle $row) {
                $btn = '';
                if (Auth::user()->can('Edit MCVehicle')) {
                    $btn .= '<a title="Edit" href="' . route('mc-vehicle.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete MCVehicle')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('mc-vehicle/' . $row->id . '') . '\',\'mc_vehicle_datatable\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })
                ->rawColumns(['action']);

            $table->editColumn('status', function ($row) {
                return $row->status ? 'Active' : 'Not Active';
            });
            $table->editColumn('company', function ($row) {
                return ($row->vendor) ? $row->vendor->name : $row->company;
            });
            return $table->toJson();
        }
        return view('content/ffl_mc_vehicle/mc_vehicle_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $venders = VendorProfile::all();
        return view('content/ffl_mc_vehicle/mc_vehicle_create')->with(get_defined_vars());
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
            'vehicle_number' => 'required|string|unique:milk_collection_vehicles',
            'company' => 'required',
            'status' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $status = (int) $request->status;
        $refinevm = str_replace('-', '', $request->vehicle_number);
        $request->merge([
            'status' => $status,
            'vehicle_number' => $refinevm,
            'created_by' => auth()->user()->id,
            'updated_by' => null,
        ]);
        $result = MilkCollectionVehicle::create($request->all());
        if ($result) {
            return redirect()
                ->route('mc-vehicle.index')
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
     * @param  \App\Models\MilkCollectionVehicle  $milkCollectionVehicle
     * @return \Illuminate\Http\Response
     */
    public function show(MilkCollectionVehicle $milkCollectionVehicle)
    {
        return redirect()
            ->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MilkCollectionVehicle  $milkCollectionVehicle
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $milkCollectionVehicle = MilkCollectionVehicle::find($id);
        $venders = VendorProfile::all();
        return view('content/ffl_mc_vehicle/mc_vehicle_edit')->with(get_defined_vars());;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MilkCollectionVehicle  $milkCollectionVehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_number' => 'required|string|unique:milk_collection_vehicles,vehicle_number,' . $request->id . ',_id',
            'status' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $status = (int)$request->status;
        $refinevm = str_replace('-', '', $request->vehicle_number);
        $request->merge([
            'status' => $status,
            'vehicle_number' => $refinevm,
            'updated_by' => auth()->user()->id,
        ]);
        $result = MilkCollectionVehicle::where('_id', $id)->update($request->all());
        if ($result) {
            return redirect()
                ->route('mc-vehicle.index')
                ->with('success', 'Record updated successfully');
        } else {
            return redirect()
                ->back()
                ->with('errorMessage', 'Record not updated. Please check your information.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MilkCollectionVehicle  $milkCollectionVehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (RouteVehicle::where('vehicle_id', $id)->exists())
                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted due to exist in other module'

                ]);
            $res = MilkCollectionVehicle::where('_id', $id)->delete();
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
