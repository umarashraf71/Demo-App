<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maklad\Permission\Models\Permission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Maklad\Permission\Models\Role;
use Response;
use Validator;


class PermissionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $permissions = Permission::all();
        // foreach ($permissions as $key => $permission) {
        //     $permission->order = (int) 1;
        //     $permission->save();
        // }
        if ($request->ajax()) {
            return datatables(Permission::select(['name', 'module', 'order', 'created_at'])->orderBy('created_at', 'desc'))
                ->editColumn('created_at', function (Permission $permission) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $permission->created_at)->format('l jS, F Y');
                })
                ->addColumn('action', function (Permission $permission) {
                    $btn = '';
                    if (Auth::user()->can('Edit Permissions')) {
                        $btn .= '<button title="Edit" class="btn btn-icon btn-primary" style="margin-right:5px;" onclick="editPermission(\'' . $permission->id . '\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>';
                    }
                    if (Auth::user()->can('Delete Permissions')) {
                        $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('permissions/' . $permission->id . '') . '\',\'permissions_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])->toJson();
        }
        return view('/content/permissions/permissions');
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
            'name' => 'required|string',
            'module' => 'required|string',
            'order' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        try {
            $permission = Permission::create(['name' => $request->input('name'), 'module' => $request->input('module'), 'order' => (int) $request->input('order')]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
        if ($permission)
            return response()->json([
                'success' => true,
                'message' => 'Permission Added!'

            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Permission not added succcessfully!'
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $permission = Permission::findorFail($id);
            return response()->json([
                'success' => true,
                'record' => $permission

            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'

            ]);
        }
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
            'module' => 'required|string',
            'order' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        try {
            $res = Permission::where('_id', $id)->update(['name' => $request->input('name'), 'module' => $request->input('module'), 'order' => (int) $request->input('order')]);
            if ($res)
                return response()->json([
                    'success' => true,
                    'message' => 'Permission updated'

                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'Permission not updated'

                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Permission not updated'

            ]);
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
            if (Role::where('permission_ids', $id)->exists()) {
                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted due to exist in other module'

                ]);
            }
            $res = Permission::where('_id', $id)->delete();
            if ($res)
                return response()->json([
                    'success' => true,
                    'message' => 'Permission deleted'

                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'Permission not deleted'

                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Permission not deleted'

            ]);
        }
    }
}
