<?php

namespace App\Http\Controllers;

use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\Route;
use App\Models\RouteVehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maklad\Permission\Models\Role;
use Response;
use Validator;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $areaOffices = AreaOffice::orderBy('created_at', 'desc')->get();
        if ($request->ajax()) {
            $table = datatables(Route::orderBy('created_at', 'desc')->get());
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $btn = '';
                if (Auth::user()->can('Edit Route Plan')) {
                    $btn .= '<a title="Edit" href="#" onclick="editRecord(\'' . $row->id . '\')" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Route Plan')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . route('routes.delete', $row->id) . '\',\'route_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            });
            $table->editColumn('collection_points', function ($row) {
                $ao = AreaOffice::where('_id', $row->area_office_id)->pluck('name')->first();
                $data = '';
                if ($row->collection_points) {
                    foreach ($row->collection_points as $key => $cp_id) {
                        $cp = CollectionPoint::where('_id', $cp_id)->pluck('name')->first();
                        if ($key == 0) {
                            $data .= $ao ? '<span class="text-danger">' . ucfirst($ao) . ' </span>' : '';
                            $data .= ' <i class="fas fa-map-marker-alt text-danger"></i> ' . ucfirst($cp);
                        } else {
                            $data .= ' <i class="fas fa-map-marker-alt text-danger"></i> ';
                            $data .= ucfirst($cp);
                        }
                    }
                }
                return $data;
            });
            $table->addIndexColumn()->editColumn('status', function ($row) {
                //                    if (Auth::user()->can('Edit Supplier Type')) {
                if ($row->status) {
                    $status = 'Active';
                } else {
                    $status = '';
                }
                //                   }
                return $status;
            })
                ->rawColumns(['action', 'collection_points']);
            return $table->toJson();
        }
        return view('content/route/index')->with(get_defined_vars());
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:66|min:2|unique:routes',
            'collection_point' => 'required|array',
            'area_office' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }
        $registered_cps = [];
        $all_registeed_collection_points = Route::get()->pluck('collection_points');
        foreach ($all_registeed_collection_points as $reg_cps) {
            foreach ($reg_cps as $cp) {
                array_push($registered_cps, $cp);
            }
        }
        $exist = 0;
        if (count($registered_cps) > 0) {
            foreach ($request->collection_point as $cp)
                if (in_array($cp, $registered_cps)) {
                    $exist = 1;
                }
        }
        if ($exist) {
            return response()->json([
                'success' => false,
                'message' => 'Route already assigned to this collection point',
                'key' => 'collection_point'
            ]);
        }
        $route = new Route;
        $route->name = $request->name;
        $route->collection_points = $request->collection_point;
        $route->area_office_id = $request->area_office;
        $route->status = 1;
        $route->save();

        return response()->json([
            'success' => true,
            'message' => 'Route added successfully.'
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:66|min:2|unique:routes,name,' . $request->id . ',_id',
            'collection_point' => 'required|array',
            'area_office' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }

        $registered_cps = [];
        $all_registeed_collection_points = Route::where('_id', '!=', $request->id)->get()->pluck('collection_points');
        foreach ($all_registeed_collection_points as $reg_cps) {
            foreach ($reg_cps as $cp) {
                array_push($registered_cps, $cp);
            }
        }
        $exist = 0;
        if (count($registered_cps) > 0 && $request->collection_point) {
            foreach ($request->collection_point as $cp)
                if (in_array($cp, $registered_cps)) {
                    $exist = 1;
                }
        }
        if ($exist) {
            return response()->json([
                'success' => false,
                'message' => 'Route already assigned to inserted collection point',
                'key' => 'collection_point'
            ]);
        }



        $route = Route::where('_id', $request->id)->first();
        $route->name = $request->name;
        $route->collection_points = $request->collection_point;
        $route->area_office_id = $request->area_office;
        $route->save();

        return response()->json([
            'success' => true,
            'message' => 'Route updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        try {
            if (RouteVehicle::where('route_id', $id)->exists())
                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted due to route is attached'

                ]);
            Route::where('_id', $id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Route deleted successfully'

            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Route not deleted successfully'

            ]);
        }
    }
    public function show(Request $request)
    {
        $route = Route::where('_id', $request->id)->first();
        if ($route && $route->collection_points && count($route->collection_points) > 0) {
            $data = [];
            foreach ($route->collection_points as  $cp_id) {
                $cp = CollectionPoint::where('_id', $cp_id)->pluck('name')->first();
                $data[] = ucfirst($cp);
            }
            $route->cps = $data;
        }
        $role = Role::where('name', 'MMT')->first();
        $users = User::where(['status' => 1, 'mobile_user_only' => '1'])->whereIn('access_level_ids', [$route->area_office_id])->whereIn('role_ids', [$role->id])->select('name', 'id')->get();
        return response()->json([
            'success' => true,
            'data' => $route,
            'users' => $users
        ]);
    }


    public function getCollectionPoints(Request $request)
    {

        if ($request->form_type == 'create') {
            $registered_cps = [];
            $registered_cpss = [];
            $all_registeed_collection_points = Route::get()->pluck('collection_points');
            foreach ($all_registeed_collection_points as $reg_cps) {
                foreach ($reg_cps as $cp) {
                    array_push($registered_cps, $cp);
                }
            }
            if (count($registered_cps) > 0) {
                foreach ($registered_cps as $cp)
                    $registered_cpss[] = $cp;
            }
            $data = CollectionPoint::where('area_office_id', $request->id)->whereNotIn('_id', $registered_cpss)->select('id', 'name')->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } else {
            $data = CollectionPoint::where('area_office_id', $request->id)->select('id', 'name')->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }
    }
}
