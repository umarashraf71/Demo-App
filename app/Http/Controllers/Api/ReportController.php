<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MilkDispatch;
use App\Models\MilkPurchase;
use App\Models\MilkReception;
use Auth;
use Carbon\Carbon;
use App\Traits\HttpResponseTrait;
use Illuminate\Support\Facades\Date;

class ReportController extends Controller
{
    use HttpResponseTrait;

    //mmt lab report
    public function index()
    {
        $user = auth()->user('api');
        $mr = MilkReception::where('mmt_id', $user->id)->where('type','ao_lab_reception')->orderBy('_id', 'DESC')->get()->first();
       
        $query = MilkPurchase::with(array('supplier' => function ($q) {
            $q->select('id', 'name', 'code');
        }))
            ->with(array('cp' => function ($q) {
                $q->select('_id', 'name');
            }))
            ->whereType('mmt_purchase')
            ->where('created_by', Auth::user()->id);
            if($mr <> null)
            $query->WhereDate('time', '>=', $mr->getAttributes()['to_time']);
            $milkPurchases = $query->get()->toArray();
        
        foreach ($milkPurchases as $key => $milkPurchase) {
            $milkPurchases[$key]['cp_name'] = $milkPurchase['cp']['name'];
            unset($milkPurchases[$key]['cp']);
        }

        $query = MilkReception::with(array('mcc' => function ($q) {
            $q->select('_id', 'name');
        }))
            ->whereType('mmt_reception')
            ->where('created_by', Auth::user()->id);
            if($mr <> null)
            $query->WhereDate('to_time', '>=', $mr->getAttributes()['to_time']);
            $milkReceptions = $query->get();
  
        foreach ($milkReceptions as $key => $milkReception) {
            $milkReceptions[$key]['mcc_name'] = $milkReception['mcc']['name'];
            unset($milkReceptions[$key]['mcc']);
        }

        $data['milkPurchases'] = $milkPurchases;
        $data['milkReceptions'] = $milkReceptions;

        return $this->response($data, true, 'Report Fetched Successfully');
    }

    public function aoLabReport()
    {
        $user = auth()->user('api');
        $md = MilkDispatch::where('area_office_id', $user->access_level_ids[0])->where('type','ao_dispatch_plant')->orderBy('_id', 'DESC')->get()->first();

         $query = MilkPurchase::with(array('supplier' => function ($q) {
            $q->select('id', 'name', 'code');
        }))
            ->with(array('ao' => function ($q) {
                $q->select('name')->first();
            }))
            ->whereType('purchase_at_ao')->where('created_by', Auth::user()->id);
            if($md <> null)
            $query->WhereDate('time', '>=', $md->getAttributes()['time']);
            $milkPurchases = $query->get()->toArray();
        foreach ($milkPurchases as $key => $milkPurchase) {
            $milkPurchases[$key]['ao_name'] = $milkPurchase['ao']['name'];
            unset($milkPurchases[$key]['ao']);
        }

         $query= MilkReception::with(array('mmt' => function ($q) {
            $q->select('_id', 'name');
        }))
            ->with(array('vehicle' => function ($q) {
                $q->select('_id', 'vehicle_number');
            }))
            ->with(array('route' => function ($q) {
                $q->select('_id', 'name');
            }))
            ->whereType('ao_lab_reception')->where('created_by', Auth::user()->id);
            if($md <> null)
            $query->WhereDate('to_time', '>=', $md->getAttributes()['time']);
            $milkReceptions = $query->get();
        
        foreach ($milkReceptions as $key => $milkReception) {
            $milkReceptions[$key]['mmt_name'] = $milkReception['mmt']['name'];
            unset($milkReceptions[$key]['mmt']);

            $milkReceptions[$key]['vehicle_number'] = $milkReception['vehicle']['vehicle_number'];
            unset($milkReceptions[$key]['vehicle']);

            $milkReceptions[$key]['route_name'] = $milkReception['route']['name'];
            unset($milkReceptions[$key]['route']);
        }

        $data['milkPurchases'] = $milkPurchases;
        $data['milkReceptions'] = $milkReceptions;

        return $this->response($data, true, 'Report Fetched Successfully');
    }

    public function plantReport()
    {
        // plant_reception
        // purchase_at_plant
        $currentDate = Carbon::now()->startOfDay();
        $startOfDay = Date::createFromTimestamp($currentDate->timestamp);
        $endOfDay = Date::createFromTimestamp($currentDate->endOfDay()->timestamp);

        $milkPurchases = MilkPurchase::with(array('supplier' => function ($q) {
            $q->select('id', 'name', 'code');
        }))
            ->with(array('plant' => function ($q) {
                $q->select('name')->first();
            }))

            ->whereType('purchase_at_plant')
            ->where('created_by', Auth::user()->id)->whereBetween('created_at', [$startOfDay, $endOfDay])->get()->toArray();

        foreach ($milkPurchases as $key => $milkPurchase) {
            $milkPurchases[$key]['plant_name'] = $milkPurchase['plant']['name'];
            unset($milkPurchases[$key]['plant']);
        }

        $milkReceptions = MilkReception::with(array('vehicle' => function ($q) {
            $q->select('_id', 'vehicle_number');
        }))

            ->whereType('plant_reception')
            ->where('created_by', Auth::user()->id)->whereBetween('created_at', [$startOfDay, $endOfDay])->get();

        foreach ($milkReceptions as $key => $milkReception) {
            $milkReceptions[$key]['mmt_name'] = $milkReception['mmt']['name'];
            unset($milkReceptions[$key]['mmt']);

            $milkReceptions[$key]['vehicle_number'] = $milkReception['vehicle']['vehicle_number'];
            unset($milkReceptions[$key]['vehicle']);

            $milkReceptions[$key]['route_name'] = $milkReception['route']['name'];
            unset($milkReceptions[$key]['route']);
        }

        $data['milkPurchases'] = $milkPurchases;
        $data['milkReceptions'] = $milkReceptions;

        return $this->response($data, true, 'Report Fetched Successfully');
    }
}
