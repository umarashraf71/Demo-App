<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public function index(Request $request)
    {
        // $logs = Log::with('logable', 'user')->latest()->paginate(10);
        if ($request->ajax()) {
            $table = datatables(Log::with('logable', 'user')->latest());
            $table->addIndexColumn()->addColumn('detail', function (Log $row) {
                $btn = '';
                if (Auth::user()->can('Edit Area Office')) {
                    $btn .= '<a href="' . route('area-office.edit', $row->id) . '" title="History Detail" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                return $btn;
            });

            $table->addColumn('action', function ($row) {
                $words = explode(" ", $row->Description);
                $lastWord = end($words);
                $actionString = ' ';
                if ($lastWord == 'Updated') {
                    $actionString .= '<span class="badge badge-glow bg-info">Updated</span>';
                } elseif ($lastWord == 'Created') {
                    $actionString .= '<span class="badge badge-glow bg-warning">Updated</span>';
                } elseif ($lastWord == 'Deleted') {
                    $actionString .= '<span class="badge badge-glow bg-danger">Deleted</span>';
                }
                return $actionString;
            })->addColumn('module', function ($row) {
                $firstpart = explode(" ", $row->Description);
                $exceptLastWord = array_slice($firstpart, 0, -1);
                $moduleName = implode(" ", $exceptLastWord);
                return $moduleName;
            })->addColumn('model_name', function ($row) {
                $modelName = explode("\\", $row->logable_type);
                $lastModelName = end($modelName);
                return $lastModelName;
            })->addColumn('created_by', function ($row) {
                return $row->user->name;
            })
                ->addColumn('record', function ($row) {
                    if ($row->logable_type == 'App\Models\Category') {
                        return $row->logableWithTrashed ? ($row->logableWithTrashed->category_name ? $row->logableWithTrashed->category_name : 'N/A') : 'N/A';
                    }
                    if ($row->logable_type == 'App\Models\MilkCollectionVehicle') {
                        return $row->logableWithTrashed ? ($row->logableWithTrashed->vehicle_number ? $row->logableWithTrashed->vehicle_number : 'N/A') : 'N/A';
                    }
                    return $row->logableWithTrashed ? ($row->logableWithTrashed->name ? $row->logableWithTrashed->name : 'N/A') : 'N/A';
                })
                ->rawColumns(['action', 'detail', 'model_name', 'module', 'created_by', 'record']);
            return $table->toJson();
        }

        return view('content.log.index');
    }
}
