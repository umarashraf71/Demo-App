<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maklad\Permission\Models\Role;
use \App\Models\User;
use Validator;
use URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Models\CollectionPoint;
use App\Models\AreaOffice;
use App\Models\Zone;
use App\Models\Section;
use App\Models\Department;
use App\Models\Plant;



class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Users'], ['only' => ['index']]);
        $this->middleware(['permission:Create Users'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Users'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Users'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables(User::select(['name', 'email', 'user_name', 'role_id', 'status'])->where('_id', '!=', '635f6fbd224d0000a70057c2'))
                ->editColumn('role_id', function (User $user) {
                    return $user->roles->pluck('name')->first();
                })
                ->editColumn('status', function (User $user) {
                    $checked = '';
                    if ($user->status == 1)
                        $checked = 'checked';

                    $statusBtn = '<div class="form-check form-switch form-check-primary">
                    <input type="checkbox" class="form-check-input" id="status' . $user->id . '" name="status" value="1" ' . $checked . ' onchange="updateStatus(this,\'' . $user->id . '\',\'users\', \'' . url('update-status') . '\')"/>
                    <label class="form-check-label" for="status' . $user->id . '">
                      <span class="switch-icon-left"><i data-feather="check"></i></span>
                      <span class="switch-icon-right"><i data-feather="x"></i></span>
                    </label>
                  </div>';
                    return $statusBtn;
                })
                ->addColumn('action', function (User $user) {
                    $btn = '';
                    if (Auth::user()->can('Edit Users')) {
                        $btn .= '<a title="Edit" href="' . route('users.edit', $user->id) . '" class="btn btn-icon btn-primary" style="margin-right:5px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                    }
                    if (Auth::user()->can('Delete Users')) {
                        $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('users/' . $user->id . '') . '\',\'users_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])->toJson();
        }
        $data = array();
        $data['total_users'] = User::where('_id', '!=', '635f6fbd224d0000a70057c2')->count();
        $data['active_users'] = User::where('_id', '!=', '635f6fbd224d0000a70057c2')->where('status', 1)->count();
        $data['inactive_users'] = User::where('_id', '!=', '635f6fbd224d0000a70057c2')->where('status', 0)->count();
        return view('content/user/users', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array();
        $data['roles'] = Role::select(['name', 'access_level', 'is_single'])->where('name', '!=', 'Super Admin')->where('deleted_at', null)->get();

        return view('content/user/user_create', $data);
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
        $role = $input['role_id'] ?? '';
        unset($input['role_id']);
        if (isset($input['mobile_user_only']) && !isset($input['email'])) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:55',
                'user_name' => 'required|string|max:55|unique:users,deleted_at,NULL',
                'password' => 'required|string|min:8|max:55',
                'role_id' => 'required|string',
                'phone' => 'required|string|unique:users',
                'access_level_ids' => 'required|array'
            ]);
        } elseif (isset($input['mobile_user_only']) && isset($input['email'])) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'user_name' => 'required|string|max:55|unique:users,deleted_at,NULL',
                'password' => 'required|string|min:8|max:55',
                'role_id' => 'required|string',
                'phone' => 'required|string|unique:users',
                'access_level_ids' => 'required|array'
            ]);
        } else {
            $input['mobile_user_only'] = 0;
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users|max:55',
                'user_name' => 'required|string|max:55|unique:users,deleted_at,NULL',
                'password' => 'required|string|min:8|max:55',
                'role_id' => 'required|string',
                'phone' => 'required|string|unique:users',
                'access_level_ids' => 'required|array'
            ]);
        }
        if ($input['mobile_user_only'] == 1 && count($input['access_level_ids']) > 1) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Mobile user can be associated to only one Instance');
        }
        $phone = str_replace('-', '', $request->phone);
        $phone = str_replace('_', '', $phone);
        $whatsapp = str_replace('-', '', $request->whatsapp);
        $whatsapp = str_replace('_', '', $whatsapp);
        $request->merge([
            'phone' => $phone,
            'whatsapp' => $whatsapp,
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $input['password'] = Hash::make($input['password']);
        if (!isset($input['status']))
            $input['status'] = (int) 0;
        else
            $input['status'] = (int) 1;
        $user = User::create($input);
        if ($user) {
            $user->assignRole($role);
            //assign user id to coressponding access level instances
            $this->assignUserid($role, $input, $user);
            return redirect()
                ->route('users.index')
                ->with('success', 'New user added successfully.');
        } else {
            return redirect()
                ->route('users.index')
                ->with('error', 'Something went wrong');
        }
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
            $data['roles'] = Role::select(['name', 'access_level', 'is_single'])->where('name', '!=', 'Super Admin')->where('deleted_at', null)->get();
            $data['user'] = User::find($id);
            $accessLevel = $data['user']->roles->pluck('access_level')->first();
            $data['accessLevel'] = $accessLevel;
            if ($accessLevel == 1)
                $data['records'] = CollectionPoint::select('name')->where('is_mcc', '1')->get()->toArray();
            if ($accessLevel == 2)
                $data['records'] = AreaOffice::select('name')->get()->toArray();
            if ($accessLevel == 3)
                $data['records'] = Zone::select('name')->get()->toArray();
            if ($accessLevel == 4)
                $data['records'] = Section::select('name')->get()->toArray();
            if ($accessLevel == 5)
                $data['records'] = Department::select('name')->get()->toArray();
            if ($accessLevel == 6)
                $data['records'] = Plant::select('name')->get()->toArray();
            return view('content.user.user_edit', $data);
        } catch (\Throwable $th) {
            return redirect()->route('users.index')
                ->with('error', 'Something went wrong');
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
        $input = $request->all();
        if (isset($input['mobile_user_only']) && !isset($input['email'])) {
            $validator = Validator::make($request->all(), [
                'user_name' => 'required|max:55|string|unique:users,user_name,' . $id . ',_id,deleted_at,NULL',
                'role_id' => 'required|string',
                'phone' => 'required|string|unique:users,phone,' . $id . ',_id,deleted_at,NULL',
                'access_level_ids' => 'required|array'
            ]);
            $input['email'] = null;
        } elseif (isset($input['mobile_user_only']) && isset($input['email'])) {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,' . $id . ',_id',
                'user_name' => 'required|max:55|string|unique:users,user_name,' . $id . ',_id,deleted_at,NULL',
                'role_id' => 'required|string',
                'phone' => 'required|string|unique:users,phone,' . $id . ',_id,deleted_at,NULL',
                'access_level_ids' => 'required|array'
            ]);
        } else {
            $input['mobile_user_only'] = 0;
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,' . $id . ',_id',
                'user_name' => 'required|max:55|string|unique:users,user_name,' . $id . ',_id,deleted_at,NULL',
                'role_id' => 'required|string',
                'phone' => 'required|string|unique:users,phone,' . $id . ',_id,deleted_at,NULL',
                'access_level_ids' => 'required|array'
            ]);
        }
        if ($input['mobile_user_only'] == 1 && count($input['access_level_ids']) > 1) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Mobile user can be associated to only one Instance');
        }
        $phone = str_replace('-', '', $request->phone);
        $phone = str_replace('_', '', $phone);
        $whatsapp = str_replace('-', '', $request->whatsapp);
        $whatsapp = str_replace('_', '', $whatsapp);
        $request->merge([
            'phone' => $phone,
            'whatsapp' => $whatsapp,
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $role = $input['role_id'];
            unset($input['role_id']);
            if (!isset($input['status']))
                $input['status'] = (int) 0;
            else
                $input['status'] = (int) 1;

            if (isset($input['password']) && $input['password'] <> NULL)
                $input['password'] = Hash::make($input['password']);
            else
                unset($input['password']);

            $user = User::findOrFail($id);
            //remove user ids if access level is changed or instances are changed
            $this->removeUserid($role, $user);
            //assign user id to coressponding access level instances
            $this->assignUserid($role, $input, $user);
            $user->update($input);
            $user->syncRoles($role);
            return redirect()
                ->route('users.index')
                ->with('success', 'User Updated successfully.');
        } catch (\Throwable $th) {
            return redirect()
                ->route('users.index')
                ->with('error', 'User not Updated successfully.');
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
            $user = User::find($id);
            $role = $user->roles->first();
            if ($role->access_level == 1)
                $records = CollectionPoint::find($user->access_level_ids);
            else if ($role->access_level == 2)
                $records = AreaOffice::find($user->access_level_ids);
            else if ($role->access_level == 3)
                $records = Zone::find($user->access_level_ids);
            else if ($role->access_level == 4)
                $records = Section::find($user->access_level_ids);
            else if ($role->access_level == 5)
                $records = Department::find($user->access_level_ids);
            else if ($role->access_level == 6)
                $records = Plant::find($user->access_level_ids);
            foreach ($records as $record) {
                $record->pull('user_ids', $user->id);
            }
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted'

            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'User not deleted'

            ]);
        }
    }
    public function changePassword(Request $request)
    {
        $input = $request->all();
        if (!Hash::check($input['old_password'], Auth::user()->password)) {
            return redirect()
                ->back()
                ->withErrors(['old_password' => 'Old Password does not match'])
                ->withInput();
        }
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $user = User::find(Auth::user()->id);
        $res = $user->update(['password' => Hash::make($input['password'])]);
        if ($res) {
            return redirect()
                ->back()
                ->with('success', 'Password Updated successfully.');
        } else {
            return redirect()
                ->back()
                ->with('error', 'Password does not Updated successfully.');
        }
    }
    public function getAccesslevelParent(Request $request)
    {
        //parent is area office if access level=1 (collection points)
        if ($request->input('access_level') == 1)
            $records = AreaOffice::select('name')->get()->toArray();
        //parent is zone if access level=2 (area office)
        else if ($request->input('access_level') == 2)
            $records = Zone::select('name')->get()->toArray();
        //parent is section if access level=3 (zone)
        else if ($request->input('access_level') == 3)
            $records = Section::select('name')->get()->toArray();
        //parent is department if access level=4 (section)
        else if ($request->input('access_level') == 4)
            $records = Department::select('name')->get()->toArray();
        //parent is plant if access level=5 (department)
        else if ($request->input('access_level') == 5)
            $records = Plant::select('name')->get()->toArray();
        //if access level=6 (plant) then get plants because it has no parent
        else if ($request->input('access_level') == 6)
            $records = Plant::select('name')->get()->toArray();
        return response()->json([
            'success' => true,
            'records' => $records

        ]);
    }
    public function getAccesslevelData(Request $request)
    {
        $records = [];
        if ($request->input('access_level') == 1)
            $records = CollectionPoint::select('name')->where('is_mcc', '1')->where('area_office_id', $request->input('parent_id'))->get()->toArray();
        else if ($request->input('access_level') == 2)
            $records = AreaOffice::select('name')->where('zone_id', $request->input('parent_id'))->get()->toArray();
        else if ($request->input('access_level') == 3)
            $records = Zone::select('name')->where('section_id', $request->input('parent_id'))->get()->toArray();
        else if ($request->input('access_level') == 4)
            $records = Section::select('name')->where('dept_id', $request->input('parent_id'))->get()->toArray();
        else if ($request->input('access_level') == 5)
            $records = Department::select('name')->where('plant_id', $request->input('parent_id'))->get()->toArray();
        else if ($request->input('access_level') == 6)
            $records = Plant::select('name')->get()->toArray();

        return response()->json([
            'success' => true,
            'records' => $records

        ]);
    }
    protected function assignUserid($role, $input, $user)
    {
        $role = Role::select(['access_level'])->where('name', $role)->get()->first();
        if ($role->access_level == 1)
            $records = CollectionPoint::find($input['access_level_ids']);
        else if ($role->access_level == 2)
            $records = AreaOffice::find($input['access_level_ids']);
        else if ($role->access_level == 3)
            $records = Zone::find($input['access_level_ids']);
        else if ($role->access_level == 4)
            $records = Section::find($input['access_level_ids']);
        else if ($role->access_level == 5)
            $records = Department::find($input['access_level_ids']);
        else if ($role->access_level == 6)
            $records = Plant::find($input['access_level_ids']);
        if ($records <> null) {
            foreach ($records as $record) {
                $record->push('user_ids', $user->id, true);
            }
        }
    }
    protected function removeUserid($role, $user)
    {
        $role = Role::select(['access_level'])->where('name', $role)->get()->first();
        if ($role->access_level == 1)
            $records = CollectionPoint::find($user->access_level_ids);
        else if ($role->access_level == 2)
            $records = AreaOffice::find($user->access_level_ids);
        else if ($role->access_level == 3)
            $records = Zone::find($user->access_level_ids);
        else if ($role->access_level == 4)
            $records = Section::find($user->access_level_ids);
        else if ($role->access_level == 5)
            $records = Department::find($user->access_level_ids);
        else if ($role->access_level == 6)
            $records = Plant::find($user->access_level_ids);
        if ($records <> null) {
            foreach ($records as $record) {
                $record->pull('user_ids', $user->id);
            }
        }
    }
}
