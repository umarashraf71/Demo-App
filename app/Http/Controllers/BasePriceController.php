<?php

namespace App\Http\Controllers;

use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\Department;
use App\Models\Price;
use App\Models\PriceLog;
use App\Models\Section;
use App\Models\Supplier;
use App\Models\SupplierType;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkFlowApproval;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class BasePriceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Base Pricing'], ['only' => ['index']]);
        $this->middleware(['permission:Create Base Pricing'], ['only' => ['create']]);
        //        $this->middleware(['permission:View Source Type Pricing'], ['only' => ['supplierType']]);
        //        $this->middleware(['permission:View Suppliers Pricing'], ['only' => ['suppliers']]);
        //        $this->middleware(['permission:View Collection Point Pricing'], ['only' => ['collectionPoint']]);
        //        $this->middleware(['permission:View Supplier + Collection Point Pricing'], ['only' => ['supplierCollectionPoint']]);
        //        $this->middleware(['permission:View Source Type & Collection Point Pricing'], ['only' => ['sourceTypeCollectionPoint']]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $prices = Price::with('source', 'suplier', 'collPoint', 'areaOffice')->where('status', 1)->orderBy('_id', 'desc');
        //      $prices = Price::where('created_by',$user->id)->with('source','suplier','collPoint','areaOffice')->where('status',1)->orderBy('_id','desc');

        if ($request->ajax()) {
            $table = datatables($prices);
            $table->addIndexColumn()->addColumn('areaOffice', function ($row) {
                return ($row->areaOffice) ? $row->areaOffice->name : '';
            });
            $table->addIndexColumn()->addColumn('edit', function ($row) use ($user) {
                if (!$row->update_request && $user->can('Edit Base Pricing') && $user->id == $row->created_by) {
                    return '<input id ="' . $row->id . '" type="checkbox" class="cursor-pointer form-check-input row_checkbox text-center" />';
                } else {
                    return '';
                }
            });
            $table->addIndexColumn()->addColumn('suplier', function ($row) {
                return ($row->suplier) ? $row->suplier->name : '';
            });
            $table->addIndexColumn()->addColumn('collPoint', function ($row) {
                return ($row->collPoint) ? $row->collPoint->name : '';
            })

                ->editColumn('price', function ($row) {
                    return $row->update_request ? $row->price . '(' . $row->update_price . ')' : $row->price;
                })
                ->editColumn('volume', function ($row) {
                    return $row->update_request ? $row->volume . '(' . $row->update_volume . ')' : $row->volume;
                })
                ->addIndexColumn()->addColumn('source', function ($row) {
                    return ($row->source) ? $row->source->name : '';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('area_office')) {
                        $instance->where('area_office', $request->get('area_office'));
                    }
                })
                ->rawColumns(['areaOffice', 'suplier', 'collPoint', 'source', 'edit']);
            return $table->toJson();
        }

        return view('content/base_pricing/index')->with(get_defined_vars());
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        $access_level = $user->roles->first()->access_level;
        if ($access_level && $access_level == 1 && isset($user['access_level_ids'][0])) {
            $collection_points = CollectionPoint::where('_id', $user['access_level_ids'][0])->where('is_mcc', '1')->select('id', 'name')->get();
        } else if ($access_level && $access_level == 2 && isset($user['access_level_ids'][0])) {
            $collection_points = CollectionPoint::where('area_office_id', $user['access_level_ids'][0])->select('id', 'name')->where('is_mcc', '1')->orderBy('created_at', 'desc')->get();
        } else {
            $collection_points = CollectionPoint::where('is_mcc', '1')->orderBy('created_at', 'desc')->select('id', 'name')->get();
        }

        $types = SupplierType::select('id', 'name')->get();
        $prices = Price::with('source', 'suplier', 'collPoint', 'areaOffice')->where(['created_by' => $user->id, 'status' => 0])->orderBy('created_at', 'desc')->get();
        
        return view('content/base_pricing/create')->with(get_defined_vars());
    }

    public function edit(Request $request)
    {
        $user = auth()->user();
        $wf = '';

        if ($request->code) {
            $prices = Price::with('source', 'suplier', 'collPoint', 'areaOffice')->where('code', (int)$request->code)->get();
            $wf = WorkFlowApproval::where('code', (int)$request->code)->first(['status']);
        } else {
            $ids = explode(",", $request->id);
            $prices = Price::with('source', 'suplier', 'collPoint', 'areaOffice')->whereIn('_id', $ids)->get();
        }
        $is_reverted_request = $wf && $wf->status == 4 ? 1 : 0;

        return view('content/base_pricing/edit')->with(get_defined_vars());
    }

    public function pending(Request $request)
    {
        $user = auth()->user();
        $approvals = WorkFlowApproval::where(['created_by' => $user->id, 'status' => 0])->with('prices')->orderBy('_id', 'desc');
        if ($request->ajax()) {
            return datatables($approvals)
                ->editColumn('created_at', function ($workflow) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $workflow->created_at)->format('l jS, F Y');
                })
                ->editColumn('code', function ($row) {
                    return '<a href="./batch/' . $row->code . '" >' . $row->code . '</a>';
                })->editColumn('request_type', function ($row) {
                    return $row->request_type == 'create' ? 'New' : 'Update';
                })
                ->addColumn('count', function ($row) {
                    return count($row->prices);
                })

                ->rawColumns(['code', 'count'])->toJson();
        }
        return view('content/base_pricing/pending')->with(get_defined_vars());
    }
    public function reverted(Request $request)
    {

        $user = auth()->user();
        $approvals = WorkFlowApproval::where(['created_by' => $user->id, 'status' => 4])->with('prices')->orderBy('updated_at', 'desc');

        if ($request->ajax()) {
            return datatables($approvals)
                ->editColumn('updated_at', function ($workflow) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $workflow->updated_at)->format('M d-Y h:ia');
                })
                ->editColumn('code', function ($row) {
                    return '<a href="./batch/' . $row->code . '" >' . $row->code . '</a>';
                })->editColumn('request_type', function ($row) {
                    return $row->request_type == 'create' ? 'New' : 'Update';
                })
                ->addColumn('count', function ($row) {
                    return count($row->prices);
                })
                ->addColumn('edit', function ($row) {
                    return '<a class="btn btn-primary" title="Edit" href="' . route('price.edit', 'code=' . $row->code) . '" >
                            <i class="fa fa-edit"></i></a>';
                })
                ->addColumn('remarks', function ($row) {
                    return '<a onclick="getRemarks(\'' . $row->code . '\')" href="#" >view</a>';
                })
                ->rawColumns(['code', 'count', 'remarks', 'edit'])->toJson();
        }
        return view('content/base_pricing/reverted')->with(get_defined_vars());
    }

    public function rejected(Request $request)
    {
        $user = auth()->user();
        $prices =  PriceLog::with('workFlow', 'getPrice.source', 'getPrice.suplier', 'getPrice.collPoint', 'getPrice.areaOffice')->whereHas('workFlow', function ($query) use ($user) {
            $query->where('created_by', $user->id);
        })->where('type', 3)->orderBy('_id', 'desc');

        if ($request->ajax()) {
            $table = datatables($prices);
            $table->addIndexColumn()->addColumn('areaOffice', function ($row) {
                $data = '';
                if ($row->getPrice && $row->getPrice->area_office) {
                    $data = $row->getPrice->areaOffice->name;
                }
                return $data;
            });
            $table->addIndexColumn()->addColumn('suplier',  function ($row) {
                $data = '';
                if ($row->getPrice && $row->getPrice->supplier) {
                    $data = $row->getPrice->suplier->name;
                }
                return $data;
            });

            $table->addIndexColumn()->addColumn('collPoint', function ($row) {
                $data = '';
                if ($row->getPrice && $row->getPrice->collection_point) {
                    $data = $row->getPrice->collPoint->name;
                }
                return $data;
            });
            $table->addIndexColumn()->addColumn('source', function ($row) {
                $data = '';
                if ($row->getPrice && $row->getPrice->source) {
                    $data = $row->getPrice->source->name;
                }
                return $data;
            });
            $table->editColumn('updated_price', function ($row) {
                if ($row->workFlow && $row->workFlow->request_type == 'update') {
                    return  $row->price_to_change;
                } else {
                    return $row->current_price;
                }
            });
            $table->addIndexColumn()->addColumn('updated_price', function ($row) {
                return ($row->workFlow && $row->workFlow->request_type == 'create') ? $row->price : $row->update_price;
            });
            $table->editColumn('price', function ($row) {
                if ($row->workFlow && $row->workFlow->request_type == 'update') {
                    return $row->current_price;
                } else {
                    return '';
                }
            });

            $table->addIndexColumn()->addColumn('type', function ($row) {
                return ($row->workFlow && $row->workFlow->request_type == 'create') ? 'Create' : 'Update';
            });
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $data = "<button onclick=\"deleteRecord('$row->id')\"  class=\"btn btn-danger btn-sm waves-effect waves-float waves-light\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"14\" height=\"14\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path></svg></button>";

                return $data;
            })

                ->rawColumns(['areaOffice', 'suplier', 'collPoint', 'source', 'updated_price', 'updated_volume', 'type', 'action']);
            return $table->toJson();
        }

        return view('content/base_pricing/rejected')->with(get_defined_vars());
    }

    public function batchPricesListing(Request $request, $code)
    {
        $user = auth()->user();
        $role = $user->role_ids[0];
        $from = '';
        $prices = Price::with('source', 'suplier', 'collPoint', 'areaOffice')->where('code', (int)$code)->orderBy('_id', 'desc')->get();

        if (count($prices) == 0) {
            $from = 'price_log';
            $prices = PriceLog::where('code', (int)$code)->with('getPrice.source', 'getPrice.suplier', 'getPrice.collPoint', 'getPrice.areaOffice')->get();
        }

        $WorkFlowApproval = WorkFlowApproval::where('code', (int)$code)->first();

        $is_curr_user_on_curr_step = 0;
        if ($WorkFlowApproval) {
            foreach ($WorkFlowApproval->data as $key => $data) {
                if ($WorkFlowApproval->status == 0 && $WorkFlowApproval->current_step == $key + 1 && $role == $data['role_id']) {
                    $is_curr_user_on_curr_step = 1;
                }
            }
        }

        if ($request->ajax()) {
            $table = datatables($prices);
            $table->addIndexColumn()->addColumn('areaOffice', function ($row) use ($from) {
                $data = '';
                if ($from == 'price_log' && $row->getPrice && $row->getPrice->area_office) {
                    $data = $row->getPrice->areaOffice->name;
                } else if ($row->area_office) {
                    $data = $row->areaOffice->name;
                }
                return $data;
            });
            $table->addIndexColumn()->addColumn('suplier',  function ($row) use ($from) {
                $data = '';
                if ($from == 'price_log' && $row->getPrice && $row->getPrice->supplier) {
                    $data = $row->getPrice->suplier->name;
                } else if ($row->supplier && $row->suplier) {
                    $data = $row->suplier->name;
                }
                return $data;
            });

            $table->addIndexColumn()->addColumn('collPoint', function ($row) use ($from) {
                $data = '';
                if ($from == 'price_log' && $row->getPrice && $row->getPrice->collection_point) {
                    $data = $row->getPrice->collPoint->name;
                } else if ($row->collPoint) {
                    $data = $row->collPoint->name;
                }
                return $data;
            });
            $table->addIndexColumn()->addColumn('source', function ($row) use ($from) {
                $data = '';
                if ($from == 'price_log' && $row->getPrice && $row->getPrice->source) {
                    $data = $row->getPrice->source->name;
                } else if ($row->source_type) {
                    $data = $row->source->name;
                }
                return $data;
            });

            $table->addIndexColumn()->addColumn('updated_price', function ($row) use ($WorkFlowApproval, $from) {
                if ($from == 'price_log') {
                    $price = $row->price_to_change;
                } else if ($WorkFlowApproval->request_type == 'create') {
                    if ($row->update_price) {
                        $price = $row->price . "(" . $row->update_price . ")";
                    } else {
                        $price = $row->price;
                    }
                } else {
                    $price =   $row->update_price;
                }
                return $price;
            });
            $table->editColumn('price', function ($row) use ($WorkFlowApproval, $from) {
                if ($from == 'price_log') {
                    return $row->current_price;
                } else {
                    return ($WorkFlowApproval->request_type == 'create') ? '' : $row->price;
                }
            });
            $table->addIndexColumn()->addColumn('updated_volume', function ($row) use ($WorkFlowApproval, $from) {
                if ($from == 'price_log') {
                    return $row->volume_to_change;
                } else if ($WorkFlowApproval->request_type == 'create') {
                    if ($row->update_volume) {
                        $volume = $row->volume . "(" . $row->update_volume . ")";
                    } else {
                        $volume = $row->volume;
                    }
                } else {
                    $volume =   $row->update_volume;
                }
                return $volume;
            });
            $table->editColumn('volume', function ($row) use ($WorkFlowApproval) {
                return ($WorkFlowApproval->request_type == 'create') ? '' : $row->volume;
            })
                ->rawColumns(['areaOffice', 'suplier', 'collPoint', 'source', 'updated_price', 'updated_volume']);
            return $table->toJson();
        }
        return view('content/base_pricing/batch_listing')->with(get_defined_vars());
    }

    public function batchDetail(Request $request, $code)
    {

        $user = auth()->user();
        $role = $user->role_ids[0];
        $from = '';
        $prices = Price::with('source', 'suplier', 'collPoint', 'areaOffice')->where('code', (int)$code)->orderBy('_id', 'desc')->get();
        if (count($prices) == 0) {
            $from = 'price_log';
            $prices =  PriceLog::where('code', (int)$code)->with('getPrice.source', 'getPrice.suplier', 'getPrice.collPoint', 'getPrice.areaOffice')->get();
        }

        $WorkFlowApproval = WorkFlowApproval::where('code', (int)$code)->first();
        if ($request->ajax()) {
            $table = datatables($prices);
            $table->addIndexColumn()->addColumn('areaOffice', function ($row) use ($from) {
                $data = '';
                if ($from == 'price_log' && $row->getPrice && $row->getPrice->area_office) {
                    $data = $row->getPrice->areaOffice->name;
                } else if ($row->area_office) {
                    $data = $row->areaOffice->name;
                }
                return $data;
            });
            $table->addIndexColumn()->addColumn('suplier',  function ($row) use ($from) {
                $data = '';
                if ($from == 'price_log' && $row->getPrice && $row->getPrice->supplier) {
                    $data = $row->getPrice->suplier->name;
                } else if ($row->supplier && $row->suplier) {
                    $data = $row->suplier->name;
                }
                return $data;
            });

            $table->addIndexColumn()->addColumn('collPoint', function ($row) use ($from) {
                $data = '';
                if ($from == 'price_log' && $row->getPrice && $row->getPrice->collection_point) {
                    $data = $row->getPrice->collPoint->name;
                } else if ($row->source && $row->collPoint) {
                    $data = $row->collPoint->name;
                }
                return $data;
            });
            $table->addIndexColumn()->addColumn('source', function ($row) use ($from) {
                $data = '';
                if ($from == 'price_log' && $row->getPrice && $row->getPrice->source) {
                    $data = $row->getPrice->source->name;
                } else if ($row->source && $row->source_type) {
                    $data = $row->source->name;
                }
                return $data;
            });
            $table->editColumn('updated_price', function ($row) use ($WorkFlowApproval, $from) {
                if ($from == 'price_log') {
                    return $row->price_to_change;
                } else {
                    return ($WorkFlowApproval->request_type == 'create') ? $row->price : $row->update_price;
                }
            });
            $table->addIndexColumn()->addColumn('updated_price', function ($row) use ($WorkFlowApproval) {
                return ($WorkFlowApproval->request_type == 'create') ? $row->price : $row->update_price;
            });
            $table->editColumn('price', function ($row) use ($WorkFlowApproval, $from) {
                if ($from == 'price_log') {
                    return $row->current_price;
                } else {
                    return ($WorkFlowApproval->request_type == 'create') ? '' : $row->price;
                }
            })
                ->rawColumns(['areaOffice', 'suplier', 'collPoint', 'source', 'updated_price']);
            return $table->toJson();
        }
        return view('content/base_pricing/batch_detail')->with(get_defined_vars());
    }
    public function savePrice(Request $request)
    {
        $supplier = $request->supplier;
        $source_type = $request->source_type;
        $collection_point = $request->collection_point;
        $plant = $request->plant;
        $area_office = $request->area_office;
        $wef = $request->wef;

        $query = Price::query()->whereNotIn('status', [3]);
        if ($supplier) {
            $query->where('supplier', $supplier);
        } else {
            $query->where('supplier', $supplier)->orWhereNull('supplier');
        }
        if ($source_type) {
            $query->where('source_type', $source_type);
        } else {
            $query->where('source_type', $source_type)->orwhereNUll('source_type');
        }
        if ($collection_point) {
            $query->where('collection_point', $collection_point);
        } else {
            $query->where('collection_point', $collection_point)->orwhereNUll('collection_point');
        }
        if ($area_office) {
            $query->where('area_office', $area_office);
        } else {
            $query->where('area_office', $area_office)->orwhereNUll('area_office');
        }
        if ($wef) {
            $query->where('wef', $wef);
        } else {
            $query->where('wef', $wef)->orwhereNUll('wef');
        }

        $pr = $query->first();
        if ($pr) {
            return response()->json([
                'success' => false,
                'message' => 'Price already set against the given set of data',
            ]);
        }

        $user = auth()->user();
        $price = new Price;
        $price->plant = $plant;
        $price->department = $request->department;
        $price->area_office = $request->area_office;
        $price->source_type = $source_type;
        $price->supplier = $supplier;
        $price->collection_point = $request->collection_point;
        $price->price = (float)$request->price;
        $price->volume = (float)$request->volume;
        $price->wef = $request->wef;
        $price->created_by = $user->id;
        $price->status = (int)0;
        $price->update_request = 0;
        $price->code = null;
        $price->save();
        $price = Price::with('source', 'suplier', 'collPoint', 'areaOffice')->where('_id', $price->id)->first();
        $source = $price->source ? $price->source->name : '';
        $supplier = $price->suplier ? $price->suplier->name : '';
        $collPoint = $price->collPoint ? $price->collPoint->name : '';
        $areaOffice = $price->areaOffice ? $price->areaOffice->name : '';

        $data = '<tr class="prices_tr" id="row_' . $price->id . '">
                    <td></td>
                    <td>' . $areaOffice . '</td>
                    <td>' . $source . '</td>
                    <td>' . $supplier . '</td>
                    <td>' . $collPoint . '</td>
                    <td>' . $price->price . '</td>
                    <td>' . $price->volume . '</td>
                    <td><button onclick="delRecord(\'' . $price->id . '\')"  class="btn btn-danger btn-sm waves-effect waves-float waves-light"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button></td>
                </tr>';
        return response()->json([
            'success' => true,
            'message' => 'Price added successfully',
            'data' => $data,
        ]);
    }

    public function delete(Request $request)
    {
        $price = Price::find($request->id);
        $price->forceDelete();
        return response()->json([
            'success' => true,
            'message' => 'Price deleted successfully'
        ]);
    }

    public function deleteRejected(Request $request)
    {
        $pricelog = PriceLog::find($request->id);
        $code = $pricelog->code;
        $pricelog->forceDelete();
        WorkFlowApproval::where('code', $code)->forceDelete();
        return response()->json([
            'success' => true,
            'message' => 'Price deleted successfully'
        ]);
    }

    public function sendForApproval(Request $request)
    {
        $user = auth()->user();
        $prices_count = Price::where(['created_by' => $user->id, 'status' => 0])->count();
        $workFlow = Workflow::where('document_type', 1)->first();
        if ($prices_count > 0 && $workFlow) {
            if ($workFlow) {
                $unique_code = $this->generateUniqueCode('App\Models\WorkFlowApproval');
                foreach ($workFlow->role_ids as $key => $role_id) {
                    $data[] = ['role_id' => $role_id, 'status' => 0, 'updated_by' => null];
                }
                WorkFlowApproval::create(['code' => $unique_code, 'type' => 'milk_base_pricing', 'request_type' => 'create', 'workflow_id' => $workFlow->id, 'data' => $data, 'created_by' => $user->id, 'status' => 0, 'current_step' => 1]);
                Price::where(['created_by' => $user->id, 'status' => 0])->update(['code' => $unique_code, 'status' => 2]);
                return response()->json([
                    'success' => true,
                    'message' => 'Updated successfully'
                ]);
            }
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Please create workflow first'
            ]);
        }
    }


    public function updatePrice(Request $request)
    {
        $user = auth()->user();
        foreach ($request->id as $key => $id) {
            $price = Price::where('_id', $id)->pluck('price')->first();
            if ($price == $request->price[$key]) {
                return response()->json([
                    'success' => false,
                    'message' => 'Updated price must not be same to the previous price'
                ]);
            } else if ($request->price[$key] == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Price must be greator then zero'
                ]);
            }
        }

        if ($request->type == 'revert' && $request->code) {
            foreach ($request->id as $key => $id) {
                Price::where(['_id' => $id])->update(['update_request' => 1, 'update_price' => (float)$request->price[$key], 'update_volume' => (float)$request->volume[$key]]);
            }
            $wfa = WorkFlowApproval::where('code', (int)$request->code)->first();
            $wfa->status = 0;
            $wfa->save();
            $data = [];

            foreach ($wfa->data as $key => $approval) {
                if ($approval['status'] == 4) {
                    $data[$key] = ['role_id' => $approval['role_id'], 'status' => 0, 'updated_by' => null];
                } else {
                    $data[$key] = $approval;
                }
            }
            $wfa->data = $data;
            $wfa->save();
        } else {
            $workFlow = Workflow::where('document_type', 1)->first();
            if ($workFlow) {
                $unique_code = $this->generateUniqueCode('App\Models\WorkFlowApproval');
                foreach ($workFlow->role_ids as $key => $role_id) {
                    $data[] = ['role_id' => $role_id, 'status' => 0, 'updated_by' => null];
                }
                WorkFlowApproval::create(['code' => $unique_code, 'type' => 'milk_base_pricing', 'request_type' => 'update', 'workflow_id' => $workFlow->id, 'data' => $data, 'created_by' => $user->id, 'status' => 0, 'current_step' => 1]);
                foreach ($request->id as $key => $id) {
                    Price::where(['_id' => $id])->update(['code' => $unique_code, 'update_request' => 1, 'update_price' => (float)$request->price[$key], 'update_volume' => (float)$request->volume[$key]]);
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Updated successfully'
        ]);
    }












































    //    old code needs to be remove
    //    public function supplierType(Request $request)
    //    {
    ////        $types = SupplierType::whereDoesntHave('price')->orderBy('created_at','desc')->get();
    //        $st = SupplierType::with('price')->whereHas('price')->orderBy('_id','desc');
    //
    //        if ($request->ajax()) {
    //            $table = datatables($st);
    //            $table->addIndexColumn()->addColumn('price', function ($row) {
    //                $btn = '';
    //                    if($row->price){
    //                        $price =$row->price->price;
    //                    }else{
    //                        $price ='';
    //                    }
    //                    $btn .= '<div class="price_'.$row->id.'"><span>'.$price.'</span></div>';
    //                    return $btn;
    //            });
    //            $table->addIndexColumn()->addColumn('volume', function ($row) {
    //                return $row->price?$row->price->volume:'';
    //            });
    //            $table->addIndexColumn()->addColumn('action', function ($row) {
    //                $btn = '';
    //                if (Auth::user()->can('Edit Source Type Pricing')) {
    //                if($row->price->is_approved==1) {
    //                $btn .= '<button  class="btn btn-icon btn-primary " onclick="setPrice(\''.$row->id.'\',\''.$row->price->price.'\',\''.$row->price->volume.'\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>&nbsp';
    //                }else{
    //                 $btn .= '<button disabled title="Price Pending For Approval" class="btn btn-icon btn-primary " ><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>&nbsp';
    //                }
    ////                if (Auth::user()->can('Delete Inventory Item')) {
    ////                $btn .= '<button class="btn btn-icon btn-danger" onclick="delRecord(\''.route('price.delete',$row->id).'\',\'supplier_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
    //                }
    //                return $btn;
    //            })
    //                ->rawColumns(['price','action','volume']);
    //            return $table->toJson();
    //        }
    //        return view('content/base_pricing/supp_type')->with(get_defined_vars());
    //    }

    //    public function collectionPoint(Request $request)
    //    {
    //        $cps = CollectionPoint::whereDoesntHave('price')->orderBy('created_at','desc')->get();
    //        if ($request->ajax()) {
    //            $table = datatables(CollectionPoint::with('price')->whereHas('price')->orderBy('_id','desc')->get());
    //            $table->addIndexColumn()->addColumn('price', function ($row) {
    //                $btn = '';
    //                  if($row->price){
    //                        $price =$row->price->price;
    //                    }else{
    //                        $price ='';
    //                    }
    //                    $btn .= '<div class="price_'.$row->id.'"><span>'.$price.'</span></div>';
    //                    return $btn;
    //            });
    //                $table->addIndexColumn()->addColumn('status', function ($row) {
    //                    $data='';
    //                    if ($row->price) {
    //                    $status = $row->price->status ? 'checked' : '';
    //                    $data = '<div class="form-switch">
    //                                  <input type="checkbox" class="form-check-input" id="status_' . $row->price->id . '" ' . $status . ' onclick="statusUpdate(this,\'' . $row->price->id . '\')">
    //                                  <label class="form-check-label" for="status_' . $row->price->id . '"  >
    //                                      <span class="switch-icon-left"><i data-feather="check"></i></span>
    //                                      <span class="switch-icon-right"><i data-feather="x"></i></span>
    //                                  </label>
    //                            </div>';
    //                     }
    //                    return $data;
    //                });
    //            $table->addIndexColumn()->addColumn('volume', function ($row) {
    //                return $row->price?$row->price->volume:'';
    //            });
    //
    //            $table->editColumn('is_mcc', function ($row) {
    //                return $row->is_mcc?'Yes':'No';
    //            });
    //            $table->addIndexColumn()->addColumn('action', function ($row) {
    //                $btn= '';
    //                if (Auth::user()->can('Edit Collection Point Pricing')) {
    //                    if ($row->price->is_approved == 1) {
    //                        $btn = '<button class="btn btn-icon price btn-primary" onclick="setPrice(\'' . $row->id . '\',\'' . $row->price->price . '\',\'' . $row->price->volume . '\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>';
    //
    //                    } else {
    //                        $btn = '<button title="Price Pending For Approval" class="btn btn-icon price btn-primary" disabled><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>';
    //                    }
    //                }
    //                return $btn;
    //            })
    //
    //                ->rawColumns(['price','status','action','volume']);
    //            return $table->toJson();
    //        }
    //        return view('content/base_pricing/collection_point')->with(get_defined_vars());
    //    }


    //    public function suppliers(Request $request)
    //    {
    //        $suppliers = Supplier::whereDoesntHave('price')->orderBy('created_at','desc')->select(['id','name'])->get();
    //
    //        $reg_suppliers = Supplier::with('price','supplier_type')->whereHas('price')->orderBy('created_at','desc');
    //        if ($request->ajax()) {
    //            $table = datatables($reg_suppliers);
    //            $table->addIndexColumn()->addColumn('price', function ($row) {
    //                $btn = '';
    //                    if($row->price){
    //                        $price = $row->price->price;
    //                    }else{
    //                        $price ='';
    //                    }
    //                    $btn .= '<div class="price_'.$row->id.'"><span>'.$price.'</span></div>';
    //                return $btn;
    //            });
    //
    //            $table->addIndexColumn()->addColumn('action', function ($row) {
    //                $btn = '';
    //                if (Auth::user()->can('Edit Suppliers Pricing')) {
    //                    if ($row->price->is_approved == 1) {
    //                        $btn = '<button id="price_' . $row->id . '" class="btn btn-icon btn-primary" onclick="setPrice(\'' . $row->id . '\',\'' . $row->price->price . '\',\'' . $row->price->volume . '\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>';
    //                } else {
    //                    $btn = '<button disabled class="btn btn-icon btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>';
    //                }
    //              }
    //                return $btn;
    //            });
    //
    //            $table->addIndexColumn()->addColumn('status', function ($row) {
    //                $status = $row->price->status?'checked':'';
    //                 $data =  '<div class="form-switch">
    //                              <input type="checkbox" class="form-check-input" id="status_'.$row->price->id.'" '.$status.' onclick="statusUpdate(this,\''.$row->price->id.'\')">
    //                              <label class="form-check-label" for="status_'.$row->price->id.'"  >
    //                                  <span class="switch-icon-left"><i data-feather="check"></i></span>
    //                                  <span class="switch-icon-right"><i data-feather="x"></i></span>
    //                              </label>
    //                        </div>';
    //                 return $data;
    //            });
    //            $table->editColumn('supplier_type_id', function ($row) {
    //                return ($row->supplier_type)? ucfirst($row->supplier_type->name):$row->supplier_type_id;
    //            });
    //                $table->addIndexColumn()->addColumn('volume', function ($row) {
    //                    return $row->price?$row->price->volume:'';
    //                })
    //                ->filter(function ($instance) use ($request) {
    //                    if ($request->get('area_office')) {
    //                        $instance->where('area_office', $request->get('area_office'));
    //                    }
    //                })
    //                ->rawColumns(['price','action','status','volume']);
    //            return $table->toJson();
    //        }
    //        return view('content/base_pricing/suppliers')->with(get_defined_vars());
    //    }

    //    public function supplierCollectionPoint(Request $request)
    //    {
    //        $suppliers = Supplier::orderBy('created_at','desc')->whereNotNUll('mcc')->orwhereHas('collectionPoints')->select('id','name')->get();
    //        if ($request->ajax()) {
    //            $table = datatables(Price::where('type','supplier_cp')->with('cp','supplier')->orderBy('_id','desc')->get());
    //            $table->editColumn('price', function ($row) {
    //                $btn = '';
    //                if($row->price){
    //                        $price = $row->price;
    //                    }else{
    //                        $price ='';
    //                    }
    //                    $btn .= '<span id="price_'.$row->_id.'">'.$price.'</span> ';
    //                return $btn;
    //            });
    //
    //            $table->addIndexColumn()->addColumn('name', function ($row) {
    //                   return $row->supplier?$row->supplier->name:'';
    //            });
    //
    //            $table->addIndexColumn()->addColumn('code', function ($row) {
    //                return $row->supplier?$row->supplier->code:'';
    //            });
    //
    //            $table->addIndexColumn()->addColumn('cp', function ($row) {
    //                return $row->cp?$row->cp->name:'';
    //            });
    //
    //            $table->addIndexColumn()->addColumn('action', function ($row) {
    //                $btn = '';
    //                if (Auth::user()->can('Edit Supplier + Collection Point Pricing')) {
    //                    $is_approved = $row->is_approved==1?"onclick='setPrice(\"$row->id\",\"$row->price\",\"$row->volume\")'":"disabled";
    //                    $btn .= '<button class="btn btn-icon btn-primary price_'.$row->id.'"  '.$is_approved.' ><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>&nbsp';
    //                }
    //                if (Auth::user()->can('Delete Supplier + Collection Point Pricing')) {
    //                    $btn .= '<button class="btn btn-icon btn-danger" onclick="delRecord(\''.route('price.delete',$row->id).'\',\'supplier_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
    //                }
    //                return $btn;
    //            })
    //
    //                ->rawColumns(['price','cp','action']);
    //            return $table->toJson();
    //        }
    //        return view('content/base_pricing/supplier_collection_points')->with(get_defined_vars());
    //    }

    //    public function sourceTypeCollectionPoint(Request $request)
    //    {
    //        $cps = CollectionPoint::get();
    //        $sts = SupplierType::get();
    //        if ($request->ajax()) {
    //            $table = datatables(Price::where('type','cp_type')->with('cp','source_type')->orderBy('_id','desc')->get());
    //            $table->addIndexColumn()->editColumn('price', function ($row) {
    //                $btn = '';
    //                    if($row->price){
    //                        $price = $row->price;
    //                    }else{
    //                        $price ='';
    //                    }
    //                    $btn .= '<span id="price_'.$row->_id.'">'.$price.'</span> ';
    //
    //                return $btn;
    //            });
    //            $table->addIndexColumn()->addColumn('name', function ($row) {
    //                $btn = '';
    //
    //                if($row->cp){
    //                    $supplier= $row->cp->name;
    //                }else{
    //                    $supplier ='';
    //                }
    //                $btn .= $supplier;
    ////                }
    //                return $btn;
    //            });
    //            $table->addIndexColumn()->addColumn('code', function ($row) {
    //                $supplier ='';
    //                if($row->cp){
    //                    $supplier= $row->cp->code;
    //                }
    //                return $supplier;
    //            });
    //
    //            $table->addIndexColumn()->addColumn('source_type', function ($row) {
    //                $btn = '';
    ////                if (Auth::user()->can('Edit Supplier')) {
    //                    if($row->source_type){
    //                        $cp = ($row->source_type->name)?ucfirst($row->source_type->name):'';
    //                    }else{
    //                        $cp ='';
    //                    }
    //                    $btn .= $cp;
    ////                }
    //                return $btn;
    //            });
    //
    //            $table->addIndexColumn()->addColumn('action', function ($row) {
    //                $btn = '';
    //
    //                if (Auth::user()->can('Edit Source Type & Collection Point Pricing')) {
    //                    $is_approved = $row->is_approved==1?"onclick='setPrice(\"$row->id\",\"$row->price\",\"$row->volume\")'":"disabled";
    //                    $btn .= '<button '.$is_approved.' class="btn btn-icon btn-primary price_'.$row->id.'"  ><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>&nbsp';
    //                }
    //                if (Auth::user()->can('Delete Source Type & Collection Point Pricing')) {
    //                    $btn .= '<button class="btn btn-icon btn-danger" onclick="delRecord(\''.route('price.delete',$row->id).'\',\'supplier_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
    //                }
    //                return $btn;
    //            })
    //
    ////            $table->editColumn('supplier_type_id', function ($row) {
    ////                return ($row->supplier_type)? ucfirst($row->supplier_type->name):$row->supplier_type_id;
    ////            })
    //                ->rawColumns(['price','cp','action','source_type']);
    //            return $table->toJson();
    //        }
    //        return view('content/base_pricing/source_types_collection_points')->with(get_defined_vars());
    //    }

    //    public function getSupplierCollectionpoints(Request $request)
    //    {
    //        $supplier = Supplier::where('_id',$request->id)->select('supplier_type_id','mcc')->first();
    //
    //        if($supplier->mcc){
    //            $cps = CollectionPoint::where('_id',$supplier->mcc)->get(['name', 'id', 'code']);
    //
    //        }else {
    //            $registered_cps = Price::where('type', 'supplier_cp')->where(['supplier_id' => $request->id])->pluck('from_id')->toArray();
    //            $cps = CollectionPoint::where('supplier', $request->id)->whereNotIn('_id', $registered_cps)->get(['name', 'id', 'code']);
    //        }
    //
    //            $is_vmca = SupplierType::where('_id', $supplier->supplier_type_id)->where('name', 'VMCA (Village Milk Collector Agent)')->first();
    //        return response()->json([
    //            'success' => true,
    //            'data' => $cps,
    //            'is_vmca' => ($is_vmca)?1:0,
    //        ]);
    //    }

    public function priceDelete($id)
    {
        $price =  Price::where('_id', $id)->first();
        $res = $price->delete();
        if ($res)
            return Response::json([
                'success' => true,
                'message' => 'Record deleted'
            ]);
    }

    public function addCpPrice(Request $request)
    {

        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'price' => 'required|numeric|min:1|max:10000',
                'supplier' => 'required',
                'volume' => 'required|numeric|min:0|max:100000',
                'collection_point' => 'required|unique:prices,from_id,' . $request->collection_point . ',id,supplier_id,' . $request->supplier,
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'key' => $validator->errors()->keys()[0]
                ]);
            }
        }
        $price =  new Price;
        $price->type = $request->type;
        $price->from_id = $request->collection_point;
        $price->price = null;
        $price->volume  = (int)$request->volume;
        $price->supplier_id = $request->supplier;
        $price->is_approved = 0;
        $price->status = 1;
        $price->save();

        $is_workflow_exist = $this->addWorkflowApprovel($price, $request, '4');
        if (!$is_workflow_exist) {
            $price->is_approved = 1;
            $price->price = (int)$request->price;
            $price->save();
        }
        return response()->json([
            'success' => true,
            'message' => 'Price set successfully',
        ]);
    }

    public function addCpSourceTypePrice(Request $request)
    {
        $request->validate(
            [
                'price' => 'required|integer|min:1',
                'source_type' => 'required',
                'collection_point' => 'required|unique:prices,from_id,' . $request->collection_point . ',id,st_id,' . $request->source_type,
                'volume' => 'required|numeric|min:0|max:100000'
            ],
            ['collection_point.unique' => 'Price already sets against this pair.']
        );

        $price =  new Price;
        $price->type = 'cp_type';
        $price->from_id = $request->collection_point;
        $price->st_id = $request->source_type;
        $price->is_approved = 0;
        $price->price = null;
        $price->volume  = (int)$request->volume;
        $price->status = 1;
        $price->save();

        $is_workflow_exist = $this->addWorkflowApprovel($price, $request, '3');
        if (!$is_workflow_exist) {
            $price->is_approved = 1;
            $price->price = (int)$request->price;
            $price->save();
        }

        return redirect()->back()->with('success', 'Price set successfully');
    }
    public function updateCpPrice(Request $request)
    {

        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'price' => 'required|numeric|min:0|max:10000',
                'volume' => 'required|numeric|min:0|max:100000',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ]);
            }
        }

        $price = Price::where(['_id' => $request->id])->first();
        if ($price->price == $request->price) {
            return response()->json([
                'success' => false,
                'message' => 'New and Previous price cannot be same',
            ]);
        }

        $doc_type = 0;
        if ($price && $price->type == 'type_cp') {
            $doc_type = '3';
        } else if ($price && $price->type == 'supplier_cp') {
            $doc_type = '4';
        }
        $is_workflow_exist = $this->addWorkflowApprovel($price, $request, $doc_type);

        $price->is_approved = $is_workflow_exist ? 0 : $price->is_approved;
        $price->price = (int)$is_workflow_exist ? $price->price : $request->price;
        $price->volume  = (int)$request->volume;
        $price->save();

        return Response::json([
            'success' => true,
            'message' => 'Price updated successfully'
        ]);
    }

    public function addWorkflowApprovel($price, $request, $document_type)
    {
        $user = auth()->user();
        $is_workflow_exist = 0;
        $workFlow = Workflow::where('document_type', $document_type)->first();
        if ($price && $workFlow) {
            $is_workflow_exist = 1;
            $unique_code = $this->generateUniqueCode('App\Models\WorkFlowApproval');
            foreach ($workFlow->role_ids as $key => $role_id) {
                WorkFlowApproval::create(['code' => $unique_code, 'role_id' => $role_id, 'type' => 'price', 'workflow_id' => $workFlow->id, 'updated_by' => null, 'created_by' => $user->id, 'table_id' => $price->id, 'status' => '0', 'step' => $key + 1, 'data' => ['curr_value' => $price->price, 'new_value' => $request->price]]);
            }
        }
        return  $is_workflow_exist;
    }
    public function statusUpdate(Request $request)
    {
        Price::updateOrCreate(array('_id' => $request->id), [
            'status' => $request->status,
        ]);
        return Response::json([
            'success' => true,
            'message' => 'Status updated successfully'

        ]);
    }

    public function generateUniqueCode($Class = null)
    {
        do {
            $random_code = random_int(1000, 9999);
        } while ($Class::where("code", "=", $random_code)->first());

        return $random_code;
    }




    public function getFilterDropdownData(Request $request)
    {
        $id = $request->id;
        $html = '';
        if ($request) {
            if ($request->type == 'department_search') {
                $html .= '<option value="" selected disabled>Select Department</option>';

                $dps = Department::where('plant_id', $id)->get(['name', 'id']);
                foreach ($dps as $data) {
                    $html .= '<option value="' . $data->id . '" >' . $data->name . '</option>';
                }
            } else if ($request->type == 'section_search') {
                $html .= '<option value="" selected disabled>Select Section</option>';
                $dps = Section::where('dept_id', $id)->get(['name', 'id']);
                foreach ($dps as $data) {
                    $html .= '<option value="' . $data->id . '" >' . $data->name . '</option>';
                }
            } else if ($request->type == 'zone_search') {
                $html .= '<option value="" selected disabled>Select Zone</option>';
                $dps = Zone::where('section_id', $id)->get(['name', 'id']);
                foreach ($dps as $data) {
                    $html .= '<option value="' . $data->id . '" >' . $data->name . '</option>';
                }
            } else if ($request->type == 'area_office_search') {
                $html .= '<option value="" selected disabled>Select Area Office</option>';
                $dps = AreaOffice::where('zone_id', $id)->get(['name', 'id']);
                foreach ($dps as $data) {
                    $html .= '<option value="' . $data->id . '" >' . $data->name . '</option>';
                }
            } else if ($request->type == 'get_cps') {
                $html .= '<option value="" selected disabled>Select Collection Point</option>';
                $html .= '<option value="" >All</option>';
                $dps = CollectionPoint::where('area_office_id', $id)->where('is_mcc', '1')->get(['name', 'id']);
                foreach ($dps as $data) {
                    $html .= '<option value="' . $data->id . '" >' . $data->name . '</option>';
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $html
        ]);
    }


    public function getDropdownData(Request $request)
    {
        $id = $request->id;

        $html = '';
        if ($request) {
            if ($request->type == 'suppliers') {
                $q = Supplier::select('name', 'id');
                ($request->id) ? $q->where('supplier_type_id', $id) : '';
                ($request->plant && $request->area_office == null) ? $q->where('plant', $request->plant) : '';
                //get suppliers across the shed according to the delivery configuration
                if ($request->area_office) {
                    //if supplier type id is @NULL then get all suppliers of selected area office
                    if ($request->id == null) {
                        $collectionPointIds = CollectionPoint::where('area_office_id', $request->area_office)->pluck('_id');
                        $q->where('area_office', $request->area_office)
                            ->orWhereIn('cp_ids', $collectionPointIds)
                            ->orWhereIn('mcc', $collectionPointIds);
                    } elseif ($request->id <> null) {
                        $source_type = supplierType::findorFail($id)->toArray();
                        //filter suppliers according to delivery configuration
                        if ($source_type['delivery_config']['at_mcc'] == 1) {
                            $collectionPointIds = CollectionPoint::where('area_office_id', $request->area_office)->pluck('_id');
                            $q->WhereIn('mcc', $collectionPointIds);
                        } elseif ($source_type['delivery_config']['at_area_office'] == 1 || $source_type['delivery_config']['by_mmt'] == 1) {
                            $q->where('area_office', $request->area_office);
                        }
                    elseif ($source_type['delivery_config']['at_plant'] == 1 || $source_type['delivery_config']['by_plant'] == 1) {
                        $q->where('area_office', $request->area_office);
                    }
                }
                }
                $suppliers = $q->get();
                $html .= (count($suppliers) > 0) ? '<option value="" selected disabled>Choose Supplier</option><option value="0" >All</option>' : '<option selected disabled>No Supplier</optionselectged>';
                foreach ($suppliers as $data) {
                    $html .= '<option value="' . $data->id . '" >' . $data->name . '</option>';
                }
            } else if ($request->type == 'collection_points') {
                $supplier = Supplier::where('_id', $id)->select('mcc', 'cp_ids')->first();
                $mccs = $supplier && gettype($supplier->mcc) == 'array' ? $supplier->mcc : [];
                $cps = $supplier && gettype($supplier->cp_ids) == 'array' ? $supplier->cp_ids : [];
                $cps = array_merge($mccs, $cps);
                $html .= '<option value="" selected disabled>Choose Collection Point</option>';
                if (count($cps) > 0) {
                    $cps = CollectionPoint::whereIn('_id', $cps)->get(['name', 'id']);
                    foreach ($cps as $cp) {
                        $html .= '<option value="' . $cp->id . '">' . $cp->name . '</option>';
                    }
                } else {
                    $html = '<option value="" selected disabled>No Collection Point</option>';
                }
            }
            return response()->json([
                'success' => true,
                'data' => $html
            ]);
        }
    }
    public function getCpsaccordingSourcetype(Request $request)
    {
        $supplierType = SupplierType::find($request->source_type);
        $cps = CollectionPoint::where(['area_office_id' => $request->area_office, 'category_id' => $supplierType->category_id])->get(['name', 'id'])->toArray();
        if (count($cps) > 0) {
            $html = '';
            foreach ($cps as $cp) {
                $html .= '<option value="' . $cp['_id'] . '">' . $cp['name'] . '</option>';
            }
        } else {
            $html = '<option value="" selected disabled>No Collection Point</option>';
        }
        return response()->json([
            'success' => true,
            'data' => $html
        ]);
    }

    public function getRemarks(Request $request)
    {
        $code = (int)$request->code;
        $html = '';
        $wfa = WorkFlowApproval::where('code', $code)->first();
        if ($wfa && $wfa->remarks) {
            foreach ($wfa->remarks as $key => $data) {
                $html .= '<tr><td>' . ++$key . '</td>';
                $html .= '<td>' . User::where('_id', $data['created_by'])->first()['name'] . '</td>';
                $html .= '<td>' . $data['remark'] . '</td>';
                $html .= '<td>' . $data['date'] . '</td></tr>';
            }
        } else {
            $html .= '<tr class="text-center"><td colspan="4">No data found</td></tr>';
        }

        return response()->json([
            'success' => true,
            'data' => $html

        ]);
    }

    public function getPrices(Request $request)
    {
        $code = (int)$request->code;
        $html = '';
        $prices = Price::where('code', $code)->get();
        if (count($prices) > 0) {
            foreach ($prices as $key => $data) {
                $html .= '<tr><td>' . ++$key . '</td>';
                $html .= '<td>' . User::where('_id', $data['created_by'])->first()['name'] . '</td>';
                $html .= '<td>' . $data['remark'] . '</td>';
                $html .= '<td>' . $data['date'] . '</td></tr>';
            }
        } else {
            $html .= '<tr class="text-center"><td colspan="4">No data found</td></tr>';
        }

        return response()->json([
            'success' => true,
            'data' => $html

        ]);
    }
}
