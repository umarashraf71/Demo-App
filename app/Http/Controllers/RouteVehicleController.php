<?php

namespace App\Http\Controllers;

use App\Models\MilkCollectionVehicle;
use App\Models\Route;
use App\Models\RouteVehicle;
use App\Models\User;
use App\Models\MilkReception;
use Illuminate\Http\Request;
use Validator;
use Auth;

class RouteVehicleController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:Create Route Vehicles'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:View Route Vehicles'], ['only' => ['index']]);
        $this->middleware(['permission:Edit Route Vehicles'], ['only' => ['update']]);
        $this->middleware(['permission:Delete Route Vehicles'], ['only' => ['destroy']]);
        $this->middleware(['permission:Open Closed Route'], ['only' => ['openRoute']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $vehicles = MilkCollectionVehicle::where('status', 1)->orderBy('created_at', 'desc')->get();
        $routes = Route::where('status', 1)->orderBy('created_at', 'desc')->get();
        if ($request->ajax()) {
            $table = datatables(RouteVehicle::with('user')->orderBy('created_at', 'desc')->get());
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $btn = '';
                $btn .= '<a href="' . route('view-vehicle.routes', $row->id) . '" title="View Vehicle Routes"  class="btn btn-icon btn-primary mr_5px">
                <i class="fas fa-truck"></i>
                </a>';
                if (Auth::user()->can('Edit Route Vehicles')) {
                $btn .= '<a href="#" title="Edit" onclick="editRecord(\'' . route('route-vehicle.edit', $row->id) . '\')" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
            }
            if (Auth::user()->can('Delete Route Vehicles')) {
                $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . route('route-vehicle.destroy', $row->id) . '\',\'route_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';            
            }
            if (Auth::user()->can('Open Closed Route') && $row->reception == 0) {
                $btn .= '<a href="#" title="Edit" onclick="openRoute(\'' . route('route-vehicle.open.route', $row->id) . '\')" class="btn btn-icon btn-primary mr_5px" style="margin-left:5px;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather book-open"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg></a>';  
            }
                return $btn;
            }); 
            $table->addColumn('route', function ($row) {
                $data = '';
                if ($row->route_id) {
                    $dt = Route::where('_id', $row->route_id)->pluck('name')->first();
                    $data = ucfirst($dt);
                }
                return $data;
            });
            $table->addColumn('user', function ($row) {
                $data = '';
                if ($row->user_id && $row->user) {
                    $data = ucfirst($row->user->name);
                }
                return $data;
            });
            $table->addColumn('vehicle', function ($row) {
                $data = '';
                if ($row->vehicle_id) {
                    $dt = MilkCollectionVehicle::where('_id', $row->vehicle_id)->pluck('vehicle_number')->first();
                    $data = ucfirst($dt);
                }
                return $data;
            });
            $table->editColumn('status', function ($row) {
                if ($row->status == 1) {
                    $status = 'Active';
                } else if ($row->status == 2) {
                    $status = 'Closed';
                } else {
                    $status = '';
                }
                return $status;
            });
            $table->editColumn('check_in', function ($row) {
                if ($row->check_in == null) 
                    return 'Not Checked in';
                    else 
                return $row->check_in;
            });
            $table->editColumn('check_out', function ($row) {
                if ($row->check_out == null) 
                    return 'Not Checked out';
                    else 
                return $row->check_out;
            });
            $table->editColumn('reception', function ($row) {
            
                
                if ($row->reception == 1 && $row->milkReception <> null) 
                {
                    return $row->milkReception->to_time;
                }
                else if($row->reception == 1)
                { 
                    return 'Received';
                }
                else if($row->reception == 0)
                { 
                    return 'Not Received';
                }
            })
                ->rawColumns(['action', 'route', 'vehicle', 'user']);
            return $table->toJson();
        }

        return view('content/route_vehicles/index')->with(get_defined_vars());
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
            'route' => 'required|string|max:66|min:2',
            'vehicle' => 'required|string',
            'user' => 'required|string',
            'date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }
        if (RouteVehicle::where(['vehicle_id' => $request->vehicle])->where('check_in','exists',false)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle already assigned to a route and route is not checked in',
                'key' => 'vehicle'
            ]);
        }
        if (RouteVehicle::where(['user_id' => $request->user])->where('check_in','exists',false)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User already assigned to a route and route is not checked in',
                'key' => 'user'
            ]);
        }
        if (RouteVehicle::where(['vehicle_id' => $request->vehicle])->where('check_out','exists',false)->where('reception',0)->where('date','=', $request->date)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle already assigned to a route for the date and route is not completed yet',
                'key' => 'date'
            ]);
        }
        if (RouteVehicle::where(['user_id' => $request->user])->where('check_out','exists',false)->where('reception',0)->where('date','=', $request->date)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User is already assigned to a route for the date and route is not completed yet',
                'key' => 'date'
            ]);
        }

        $route = new RouteVehicle();
        $route->route_id = $request->route;
        $route->vehicle_id = $request->vehicle;
        $route->user_id = $request->user;
        $route->date = $request->date;
        $route->status = 1;
        $route->save();

        return response()->json([
            'success' => true,
            'message' => 'Route assigned successfully.'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RouteVehicle  $routeVehicle
     * @return \Illuminate\Http\Response
     */
    public function edit(RouteVehicle $routeVehicle)
    {
        $users = [];
        if ($routeVehicle->route) {
            $users = User::where(['status' => 1, 'mobile_user_only' => '1'])->whereIn('access_level_ids', [$routeVehicle->route->area_office_id])->select('name', 'id')->get();
        }
        if($routeVehicle->check_in <> null)
        return response()->json([
            'success' => false,
            'message' => 'Route can not updated because route is checked in',
        ]);

        return response()->json([
            'success' => true,
            'data' => $routeVehicle,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RouteVehicle  $routeVehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route' => 'required|string|max:66|min:2',
            'vehicle' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }
        
        if (RouteVehicle::where('_id', '!=', $request->id)->where(['vehicle_id' => $request->vehicle])->where('check_in','exists',false)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle already assigned to a route and route is not checked in',
                'key' => 'vehicle'
            ]);
        }
        if (RouteVehicle::where('_id', '!=', $request->id)->where(['user_id' => $request->user])->where('check_in','exists',false)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User already assigned to a route and route is not checked in',
                'key' => 'user'
            ]);
        }
        if (RouteVehicle::where('_id', '!=', $request->id)->where(['vehicle_id' => $request->vehicle])->where('check_in','exists',true)->where('date','=', $request->date)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle already assigned to a route for the date',
                'key' => 'date'
            ]);
        }
        if (RouteVehicle::where('_id', '!=', $request->id)->where(['user_id' => $request->user])->where('check_in','exists',true)->where('date','=', $request->date)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User is already assigned to a route for the date',
                'key' => 'date'
            ]);
        }

        $route = RouteVehicle::find($request->id);
        $route->route_id = $request->route;
        $route->vehicle_id = $request->vehicle;
        $route->user_id = $request->user;
        $route->date = $request->date;
        $route->save();

        return response()->json([
            'success' => true,
            'message' => 'Data updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RouteVehicle  $routeVehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(RouteVehicle $routeVehicle)
    {
        if($routeVehicle->check_in <> null)
        return response()->json([
            'success' => false,
            'message' => 'Route can not deleted because route is checked in',
        ]);
        $routeVehicle->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'

        ]);
    }

    public function viewVistedRoutesOnMap($id)
    {
        $result = RouteVehicle::find($id);
        if ($result->locations) {
            $decodeLoc = json_decode($result->locations);
            $interval = (int)ceil(count($decodeLoc) / 27);
            for ($i = 0; $i < count($decodeLoc); $i += $interval) {
                $makeNewArray[] = $decodeLoc[$i];
            }
            $jsonArray = json_encode($makeNewArray);
            return view('content/route_vehicles/vehicle-routes', compact('jsonArray'));
        } else {
            return redirect()->back()->with('errorMessage', 'Server not received any locations data from application side when closing the Route.');
        }
    }
    public function openRoute($id)
    {
        $routeVehicle = RouteVehicle::find($id);
        $routeVehicle->unset(['delivered_to','lat','lng','time','visiting_points']);
        $routeVehicle->status = (int) 1;
        $res = $routeVehicle->save();
        if($res)
        return response()->json([
            'success' => true,
            'message' => 'Route is open now'

        ]);
        else 
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong'

        ]);
    }
}
