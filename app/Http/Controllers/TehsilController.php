<?php

namespace App\Http\Controllers;

use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\District;
use App\Models\Supplier;
use App\Models\Tehsil;
use Auth;
use Illuminate\Http\Request;
use URL;
use Validator;

class TehsilController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Create Tehsil'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:View Tehsil'], ['only' => ['index']]);
        $this->middleware(['permission:Edit Tehsil'], ['only' => ['update']]);
        $this->middleware(['permission:Delete Tehsil'], ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $districts = District::get();
        if ($request->ajax()) {
            $table = datatables(Tehsil::with('district')->get());
            $table->addIndexColumn()->addColumn('action', function (Tehsil $row) {
                $btn = '';
                if (Auth::user()->can('Edit Tehsil')) {
                    $btn .= '<a title="Edit" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editRecord(\'' . $row->id . '' . '\',\'' . $row->name . '' . '\',\'' . $row->short_name . '' . '\',\'' . $row->district->id . '' . '\')" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Tehsil')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('tehsil/delete/' . $row->id . '') . '\',\'tehsil_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })
                ->rawColumns(['action']);
            return $table->toJson();
        }
        return view('content.tehsil.index', compact('districts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:banks|string|max:55|min:2',
            'short_name' => 'required|string|max:55|min:1',
            'district_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }

        $result = Tehsil::create([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'district_id' => $request->district_id,
            'created_by' => auth()->user()->id,
            'status' => 1,
            'updated_by' => null,
        ]);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Tehsil created successfully',
            ]);
        } else {

            return redirect()
                ->back()
                ->with('errorMessage', 'Tehsil not save. Please check your information.')
                ->withInput();
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:55|min:2|unique:tehsils,name,' . $request->id . ',_id',
            'short_name' => 'required|string|max:55|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }
        $tehsil = Tehsil::where('_id', $request->id)->first();
        $result = $tehsil->update(['name' => $request->name, 'short_name' => $request->short_name, 'updated_by' => auth()->user()->id, 'district_id' => $request->district_id]);
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully',
            ]);
        }
    }

    public function destroy(Tehsil $tehsil)
    {
        $areaOffice = AreaOffice::where('tehsil_id', $tehsil->id)->exists();
        $collectionPoint = CollectionPoint::where('tehsil_id', $tehsil->id)->exists();
        $supplier = Supplier::where('tehsil_id', $tehsil->id)->exists();
        if ($areaOffice || $collectionPoint || $supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Oops! We can`t delete the selected record just yet. It looks like there are some child elements that need to be removed first. Please remove all associated child elements before trying again.'
            ]);
        }

        $res = $tehsil->delete();
        if ($res)
            return response()->json([
                'success' => true,
                'message' => 'Tehsil is deleted successfully'

            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Tehsil is not deleted successfully'
            ]);
    }

    public function getTehsils($id)
    {
        $tehsils = Tehsil::where('district_id', $id)->pluck('name', '_id');
        return response()->json($tehsils);
    }
}
