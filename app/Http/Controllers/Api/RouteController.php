<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MilkDispatch;
use App\Models\RouteVehicle;
use Carbon\Carbon;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use App\Models\Log;

class RouteController extends Controller
{
    use HttpResponseTrait;

    public function checkIn($id)
    {
        $user = auth()->user('api');
        $routeVehicle = RouteVehicle::where('user_id', $user->id)->where('check_in', 'exists', false)->where('route_id', $id)->where('reception', 0)->first();

        if ($routeVehicle) {
            if (!empty($routeVehicle->check_in)) {
                return $this->response(null, true, 'You have Already Checked in');
            } else {
                $currentDateTime = Carbon::now();
                $currentDateTimeFormatted = $currentDateTime->format('Y-m-d h:i');

                $routeVehicle->update([
                    'check_in' => $currentDateTimeFormatted
                ]);
                return $this->response(null, true, 'Sucessfully Checked in');
            }
        } else {
            return $this->response(null, true, 'No Route Found');
        }
    }

    public function checkOut($id)
    {
        $user = auth()->user('api');

        $routeVehicle = RouteVehicle::where('user_id', $user->id)->where('route_id', $id)->where('reception', 0)->where('check_in', 'exists', true)->first();

        if ($routeVehicle) {

            if (empty($routeVehicle->check_in)) {
                return $this->response(null, false, 'Please Check In First');
            } elseif (!empty($routeVehicle->check_out)) {
                return $this->response(null, false, 'You have Already Checked out');
            } elseif (empty($routeVehicle->delivered_to)) {
                return $this->response(null, false, 'Please close route first');
            }
            $routeVehicle->update([
                'check_out' => date('Y-m-d H:i')
            ]);
            $data['route_vehicle_id'] = $routeVehicle->id;
            return $this->response($data, true, 'Sucessfully Checked out');
        } else {
            return $this->response(null, false, 'No Route Found');
        }
    }

    public function updateLocations(Request $request)
    {
        $user = auth()->user('api');
        $routeVehicle = RouteVehicle::where('user_id', $user->id)->where('_id', $request->route_vehicle_id)->first();
        if (empty($routeVehicle->check_in)) {
            return $this->response(null, false, 'Please Check In First');
        } elseif (empty($routeVehicle->check_out)) {
            return $this->response(null, false, 'Please Check Out First');
        }
        if ($routeVehicle) {
            if (isset($request['location'])) {
                $newLocations = [];
                foreach ($request['location'] as $location) {
                    $result = [];
                    $result['lat'] = (float) $location['Latitude'];
                    $result['lng'] = (float) $location['Longitude'];
                    array_push($newLocations, $result);
                }
                $interval = (int)ceil(count($newLocations) / 27);
                for ($i = 0; $i < count($newLocations); $i += $interval) {
                    $makeNewArray[] = $newLocations[$i];
                }
                $encodedLocations = json_encode($makeNewArray);
                $routeVehicle->update([
                    'locations' => $encodedLocations,
                ]);
                return $this->response(null, true, 'Location Updated Successfully');
            } else {
                Log::error('No Locations End Points Find');
                return $this->response(null, true, 'No Locations End Points Find');
            }
        } else {
            Log::error('No Route Vehicle Found');
            return $this->response(null, true, 'No Route Vehicle Found');
        }
    }


    public function RouteStatus($id)
    {
        $user = auth()->user('api');
        $routeVehicle = RouteVehicle::where('user_id', $user->id)->where('route_id', $id)->where('reception', 0)->first();
        if ($routeVehicle == null)
            return $this->response(null, true, 'Route not found');
        $data = array();
        $data['check_in'] = ($routeVehicle->check_in <> null) ? $routeVehicle->check_in : null;
        $data['check_out'] = ($routeVehicle->check_out <> null) ? $routeVehicle->check_out : null;
        return $this->response($data, true, '');
    }
}
