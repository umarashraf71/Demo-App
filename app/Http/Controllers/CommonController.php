<?php

namespace App\Http\Controllers;

use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\MilkDispatch;
use App\Models\MilkTransfer;
use App\Models\Notification;
use App\Models\Plant;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;
use Maklad\Permission\Models\Permission;

class CommonController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Milk Transfers'], ['only' => ['transfers']]);
    }
    public function updateStatus(Request $request)
    {
        try {
            DB::collection($request->input('table'))->where('_id', $request->input('id'))->update(['status' => (int) $request->input('status')]);
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Status is not updated successfully'

            ]);
        }
    }

    public function changeThememode(Request $request)
    {
        $mode = $request->input('mode');
        cookie()->queue(cookie('theme', $mode, 60 * 60 * 24 * 365 * 100));
        return response()->json([
            'success' => true,
            'message' => 'Theme mode Changed!'

        ]);
    }

    public function superAdminpermissions()
    {
        $role = Auth::user()->roles()->first();
        if ($role->name <> 'Super Admin')
            dd('Not Allowed');
        $permissions = Permission::pluck('name')->toArray();
        $role->syncPermissions($permissions);
        dd('All Permissions are given to super admin');
    }

    public function notifications(Request $request)
    {
        $user = auth()->user();
        $query = Notification::where('to', $user->id);
        $query->update(['is_read' => 1]);
        if ($request->ajax()) {
            $table = datatables($query);
            $table->editColumn('type', function ($row) {
                return Notification::$type[$row->type];
            })
                ->editColumn('created_at', function ($workflow) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $workflow->created_at)->format('l jS, F Y');
                });
            return $table->toJson();
        }
        return view('content/pages/notifications');
    }

    public function transfers()
    {
        $user = auth()->user();
        $role = $user->role_ids[0] ?? '';
        $mts = MilkTransfer::with('fromCp', 'toCp', 'fromAo', 'toAo')->get();

        return view('content/transfer/index')->with(get_defined_vars());
    }

    public function listMilkDispatches(Request $request)
    {
        $mrs_query  = MilkDispatch::with('plant', 'route', 'areaOffice');

        if ($request->ajax()) {
            $table = datatables($mrs_query);
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $btn = '';
                // if (Auth::user()->can('View Dispatches')) {
                //     $btn .= '<a title="View" href="' . route('reception.view', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                // }
                $btn .= '<a title="View" href="' . route('dispatch.details', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                return $btn;
            });
            $table->addIndexColumn()
                ->addColumn('type', function ($row) {


                    if ($row->type == 'ao_dispatch_plant') {
                        return '<span class="badge badge-glow bg-info">Area Office</span>';
                    } elseif ($row->type == 'mmt_dispatch_plant') {
                        return '<span class="badge badge-glow bg-primary">MMT</span>';
                    } else {
                        return 'N/A';
                    }
                });
            $table->addColumn('serial_number', function ($row) {
                return ($row->serial_number) ? ("MD-" . $row->serial_number) : 'N/A';
            });
            $table->addColumn('route_name', function ($row) {
                return ($row->route) ? ($row->route->name) : 'N/A';
            });
            $table->addColumn('area_office', function ($row) {
                return ($row->areaOffice) ? ($row->areaOffice->name) : 'N/A';
            });
            $table->addColumn('date', function ($row) {
                return  date('d-M-Y', strtotime($row->time));
            }); 
            $table->addColumn('gross_vol', function ($row) {
                return  $row->gross_volume ?? 0;
            });
            $table->addColumn('plant_name', function ($row) {
                return ($row->plant) ? ($row->plant->name) : 'N/A';
            });
            $table->addColumn('ts_vol', function ($row) {
                return  $row->volume_ts ?? 0;
            })
                ->filter(function ($instance) use ($request) {

                    if ($request->filled('type')) {
                        $instance->where('type', $request->type);
                    }

                    if ($request->filled('area_office')) {
                        $instance->where('area_office_id', $request->area_office);
                    }
                    if ($request->filled('plant_name')) {
                        $instance->where('plant_id', $request->plant);
                    }
                })
                ->rawColumns(['type', 'serial_number', 'date', 'route_name', 'area_office', 'gross_vol', 'ts_vol', 'plant_name','action']);

            return $table->toJson();
        }
        $collectionPoints = CollectionPoint::get();
        $areaOffices = AreaOffice::get();
        $plants = Plant::get();
        $suppliers = Supplier::get();
        return view('content/milk_dispatches/index', compact('collectionPoints', 'areaOffices', 'plants', 'suppliers'));
    }

    public function milkDispatchDetails(Request $request)
    {
        $id = $request->id;
        $dispatch = MilkDispatch::with('areaOffice', 'plant','mmt')->findorFail($id);
        return view('content.milk_dispatches.dispatch-details', compact('dispatch'));
    }
}
