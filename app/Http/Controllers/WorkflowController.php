<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workflow;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use URL;

class WorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Workflow'], ['only' => ['index']]);
        $this->middleware(['permission:Create Workflow'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Workflow'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Workflow'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables(Workflow::select(['name', 'role_ids', 'document_type', 'created_at']))
                ->editColumn('created_at', function (Workflow $workflow) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $workflow->created_at)->format('l jS, F Y');
                })
                ->editColumn('document_type', function ($workflow) {
                    return Workflow::$types[$workflow->document_type]['name'];
                })
                ->editColumn('role_ids', function (Workflow $workflow) {
                    $roleString = '';
                    foreach ($workflow->role_ids as $key => $role_id) {
                        $role = Role::where('_id', $role_id)->pluck('name')->first();
                        if ($key == 0)
                            $roleString .= $role;
                        else {
                            $roleString .= ' => ';
                            $roleString .= ucfirst($role);
                        }
                    }
                    return $roleString;
                })
                ->addColumn('action', function (Workflow $workflow) {
                    $btn = '';
                    if (Auth::user()->can('Edit Workflow')) {
                        $btn .= '<a title="Edit" href="' . route('workflow.edit', $workflow->id) . '" class="btn btn-icon btn-primary" style="margin-right:5px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                    }
                    if (Auth::user()->can('Delete Workflow')) {
                        $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('workflow/' . $workflow->id . '') . '\',\'workflow_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('/content/workflow/workflow');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array();
        $permission  = Permission::whereIn('name', ['Milk Base Price', 'Milk Transfer (mcc to mcc)', 'Milk Transfer (ao to ao)'])->first();
        $data['roles'] = Role::whereIn('_id', $permission->role_ids)->where('name','!=','Super Admin')->get()->toArray();
        return view('/content/workflow/create', $data);
    }


    public function getWorkflowDocumentTypeRoles(Request $request)
    {
        
        $roles = [];
        $document_types = \App\Models\Workflow::$types;
        $documentType = $document_types[$request->id]; 

        $permission  = Permission::whereIn('name', [$documentType['name']])->first();
        if($permission)
        {
            $roles = Role::whereIn('_id', $permission->role_ids)->where('name','!=','Super Admin')->get()->toArray();

            if(empty($roles))
            {
                $roles = [];
            }
        }

        return response()->json([
            'success' => true,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->request->set('document_type', (int)$request->request->get('document_type'));

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:workflows,name,NULL,id,deleted_at,NULL',
            'document_type' => 'required|numeric|unique:workflows,document_type,deleted_at,NULL',
            'role_ids' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        if(Workflow::where('document_type', (int) $request->input('document_type'))->exists())
        {
            return redirect()
            ->route('workflow.create')
            ->with('errorMessage', 'Can not create more than one workflows for a single document type');
        }
        else
        {

            $workflow = Workflow::create($request->all());
            if ($workflow) {
                return redirect()
                    ->route('workflow.index')
                    ->with('success', 'New Workflow added successfully.');
            } else {
                return redirect()
                    ->route('workflow.index')
                    ->with('errorMessage', 'Something went wrong');
            }
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $workflow =  Workflow::findorFail($id);

        if($workflow)
        {
            $documentTypeNo = $workflow['document_type'];

            $document_types = \App\Models\Workflow::$types;
            $documentType = $document_types[$documentTypeNo]; 
    
            $data = array();
            $permission  = Permission::whereIn('name', [$documentType['name']])->first();
            $data['roles'] = Role::whereIn('_id', $permission->role_ids)->where('name','!=','Super Admin')->get()->toArray();

            try {
                $data['workflow'] = Workflow::findorFail($id);
            } catch (\Throwable $th) {
                return redirect()
                    ->route('workflow.index')
                    ->with('error', 'Something went wrong');
            }
            $data['workflow'] = Workflow::findorFail($id);
            return view('/content/workflow/edit', $data);

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
        $request->request->set('document_type', (int)$request->request->get('document_type'));

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:77|unique:workflows,name,' . $id . ',_id,deleted_at,NULL',
            'document_type' => 'required|numeric|unique:workflows,document_type,' . $id . ',_id,deleted_at,NULL',
            'role_ids' => 'required|array',
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $workflow = Workflow::findOrFail($id);
            $workflow->update($input);
            return redirect()
                ->route('workflow.index')
                ->with('success', 'Workflow Updated successfully.');
        } catch (\Throwable $th) {
            return redirect()
                ->route('workflow.index')
                ->with('error', 'Workflow not Updated successfully.');
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
            $workflow = Workflow::where('_id', $id)->first();
            $workflow->delete();

            return response()->json([
                'success' => true,
                'message' => 'Workflow deleted successfully'

            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Workflow not deleted successfully'

            ]);
        }
    }
}
