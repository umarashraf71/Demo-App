<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workflow;
use Illuminate\Http\Request;
use Maklad\Permission\Models\Role;
use Maklad\Permission\Models\Permission;
use Validator;
use URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Response;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Roles'], ['only' => ['index']]);
        $this->middleware(['permission:Create Roles'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Roles'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Roles'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            return datatables(Role::select(['name', 'access_level', 'created_at'])->where('deleted_at', '=', null)->where('name', '!=', 'Super Admin'))
                ->editColumn('created_at', function (Role $role) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $role->created_at)->format('l jS, F Y');
                })
                ->addColumn('action', function (Role $role) {
                    $btn = '';
                    if (Auth::user()->can('Edit Roles')) {
                        $btn .= '<a title="Edit" href="' . route('roles.edit', $role->id) . '" class="btn btn-icon btn-primary" style="margin-right:5px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                    }
                    if (Auth::user()->can('Delete Roles')) {
                        $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('roles/' . $role->id . '') . '\',\'roles_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('/content/roles/roles');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array();
        $data['permissions'] = Permission::get()->groupBy('module')->toArray();
        ksort($data['permissions']);
        return view('/content/roles/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        if (!isset($input['is_single']))
            $input['is_single'] = (int) 0;
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|min:2|max:33|unique:roles,name',
            'access_level' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        app()['cache']->forget('maklad.permission.cache');
        $role = Role::create(['name' => $input['role'], 'access_level' => (int) $input['access_level'], 'is_single' => (int) $input['is_single'], 'deleted_at' => null]);
        if (isset($input['permissions']))
            $role->syncPermissions($input['permissions']);
        return redirect()
            ->route('roles.index')
            ->with('success', 'New role added successfully.');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = array();
        try {
            $data['permissions'] = Permission::get()->groupBy('module')->toArray();
            ksort($data['permissions']);
            $data['role'] = Role::with('permissions')->findOrFail($id);
        } catch (\Throwable $th) {
            return redirect()->route('mcc.index')
                ->with('error', 'Something went wrong');
        }
        return view('/content/roles/edit', $data);
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
        $input = $request->all();
        if (!isset($input['is_single']))
            $input['is_single'] = (int) 0;
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|min:1|max:33|unique:roles,name,' . $id . ',_id',
            'access_level' => 'required|string|min:1|max:33',

        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        app()['cache']->forget('maklad.permission.cache');
        try {
            $role = Role::findOrFail($id);

            $role->update(['name' => $input['role'], 'access_level' => (int) $input['access_level'], 'is_single' => (int) $input['is_single']]);
            if ($role->name == 'Super Admin') {
                array_push($input['permissions'], 'View Permissions');
                array_push($input['permissions'], 'Create Permissions');
                array_push($input['permissions'], 'Edit Permissions');
                array_push($input['permissions'], 'Delete Permissions');
            }
            if (isset($input['permissions']))
                $role->syncPermissions($input['permissions']);
            else
                $role->syncPermissions([]);

               

            return redirect()
                ->route('roles.index')
                ->with('success', 'Role Updated successfully.');
        } catch (\Throwable $th) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role not Updated.');
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
            if (User::where('role_ids', $id)->exists() || Workflow::where('role_ids', $id)->exists()) {

                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted due to exist in other module'

                ]);
            }
            app()['cache']->forget('maklad.permission.cache');
            $res = Role::where('_id', $id)->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
            if ($res)
                return response()->json([
                    'success' => true,
                    'message' => 'Role deleted'

                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'Role not deleted'

                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Role not deleted'

            ]);
        }
    }
}
