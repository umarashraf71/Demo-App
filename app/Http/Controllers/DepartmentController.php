<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Department;
use App\Models\Plant;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;


class DepartmentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(Department::with('plant')->get());
            $table->addIndexColumn()->addColumn('action', function (Department $row) {
                $btn = '';
               
                    $btn .= '<a title="Edit" href="' . route('dept.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
              
                
                    $btn .= '<button title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('dept/' . $row->id . '') . '\',\'department_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                
                return $btn;
            })
                ->editColumn('status', function ($row) {
                    return Helper::addStatusColumn($row, 'departments');
                })
                ->rawColumns(['action', 'status']);

            $table->editColumn('plant_id', function ($row) {
                return ($row->plant) ? ucfirst($row->plant->name) : $row->plant_id;
            });
            return $table->toJson();
        }
        return view('content.ffl_dept.dept_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $code = Helper::generateCode('departments', 'D');
        $all_plant = Plant::active()->get();
        return view('content.ffl_dept.dept_create', compact('code', 'all_plant'));
    }
    //    public function generateUniqueCode()
    //    {
    //        do {
    //            $random_code = random_int(1000, 9999);
    //        } while (Department::where("code", "=", $random_code)->first());
    //
    //        return $random_code;
    //    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:departments',
            'address' => 'required|string',
            'plant_id' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
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
        $result = Department::create($request->all());
        if ($result) {
            return redirect()->back()
                ->with('success', 'New record added successfully.');
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
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        return redirect()
            ->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $department  = Department::find($id);

        $all_plant = Plant::active()->orWhere('_id', $department->plant_id)->get();
        return view('content.ffl_dept.dept_edit', compact('department', 'all_plant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'address' => 'required|string',
            'plant_id' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
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
        $result = Department::where('_id', $id)->update($request->all());
        if ($result) {
            return redirect()->route('dept.index')
                ->with('success', 'Record updated successfully.');
        } else {
            return redirect()
                ->back()
                ->with('errorMessage', 'Record not save. Please check your information.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Section::where('dept_id', $id)->exists()) {
            return Response::json([
                'success' => false,
                'message' => 'Record not deleted due to exist in other module'
            ]);
        }
        try {
            $res = Department::where('_id', $id)->delete();
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
