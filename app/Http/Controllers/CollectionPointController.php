<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\CollectionPoint;
use App\Models\AreaOffice;
use App\Models\Categories;
use App\Models\District;
use App\Models\InventoryItem;
use App\Models\InventoryItemType;
use App\Models\MilkPurchase;
use App\Models\MilkReception;
use App\Models\MilkRejection;
use App\Models\PurchasedMilkRejection;
use App\Models\Plant;
use App\Models\Supplier;
use App\Models\SupplierType;
use App\Models\Tehsil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Traits\JazzSMSTrait;
use Illuminate\Support\Carbon;
use App\Models\MilkDispatch;


class CollectionPointController extends Controller
{
    use JazzSMSTrait;
    public function __construct()
    {
        $this->middleware(['permission:View Collection Points'], ['only' => ['index']]);
        $this->middleware(['permission:Create Collection Points'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Collection Points'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Collection Points'], ['only' => ['destroy']]);
        $this->middleware(['permission:Milk Purchases'], ['only' => ['getPurchases']]);
        $this->middleware(['permission:Milk Receptions'], ['only' => ['getPurchaseReceptionMMT']]);
        $this->middleware(['permission:Receptions AO'], ['only' => ['getPurchaseReceptionAO']]);
        $this->middleware(['permission:Details Purchases'], ['only' => ['purchaseDetails']]);
        $this->middleware(['permission:Delete Purchases'], ['only' => ['deletePurchase']]);
        $this->middleware(['permission:Plant Receptions'], ['only' => ['getPlantReception']]);
        $this->middleware(['permission:Milk Rejections'], ['only' => ['rejection']]);
        $this->middleware(['permission:Milk Purchased Rejections'], ['only' => ['purchasedRejections']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(CollectionPoint::with(['area_office', 'collectionPointCategory']));

            $table->addIndexColumn()->addColumn('action', function (CollectionPoint $row) {
                $btn = '';
                if (Auth::user()->can('Edit Collection Points')) {
                    $btn .= '<a title="Edit" href="' . route('collection-point.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Collection Points')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('collection-point/' . $row->id . '') . '\',\'collection_point_datatable\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            });
            $table->editColumn('code', function ($row) {
                return $row->code . $row->short_name;
            });
            $table->editColumn('has_chiller', function ($row) {
                return $row->has_chiller ? 'Yes' : 'No';
            });
            $table->editColumn('is_mcc', function ($row) {
                return $row->is_mcc ? 'Yes' : 'No';
            })->editColumn('status', function ($row) {
                return Helper::addStatusColumn($row, 'collection_points');
            })->editColumn('area_office_id', function ($row) {
                return ($row->area_office) ? ucfirst($row->area_office->name) : $row->area_office_id;
            })->editColumn('category_id', function ($row) {
                return ($row->collectionPointCategory) ? ucfirst($row->collectionPointCategory->category_name) : 'NULL';
            })->rawColumns(['action', 'status', 'category_id']);
            return $table->toJson();
        }

        return view('content.ffl_collection_point.collection_point_list');
    }


    public function getPurchaseReceptionMMT(Request $request)
    {
        $cps = CollectionPoint::where(['is_mcc' => '1'])->select('id', 'name')->get();
        $mrs_query  = MilkReception::with('mcc');

        if ($request->ajax()) {
            $table = datatables($mrs_query);
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $btn = '';
                if (Auth::user()->can('Details Reception')) {
                    $btn .= '<a title="View" href="' . route('reception.view', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                }
                if (Auth::user()->can('Delete Reception')) {
                    $btn .= '<button  title="Void" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('delete-reception/' . $row->id . '') . '\',\'collection_point_datatable\')">Void</button>';
                }
                return $btn;
            });

            $table->addIndexColumn()->addColumn('mcc', function ($row) {

                return  $row->mcc ? $row->mcc->name : "N/A";
            });
            $table->addIndexColumn()->addColumn('ao', function ($row) {
                if ($row->ao <> null)
                    return $row->ao->name;
                elseif ($row->ao == null && $row->mcc_id <> null)
                    return $row->mcc->area_office->name;
                else
                    return "N/A";
            });
            $table->editColumn('opening_balance', function ($row) {
                return  $row->opening_balance1 ?? 0;
            });
            $table->editColumn('type', function ($row) {
                if ($row->type == 'mmt_reception') {
                    return '<span class="badge badge-glow bg-info">MMT Reception</span>';
                } elseif ($row->type == 'ao_lab_reception') {
                    return '<span class="badge badge-glow bg-warning">Area Office Lab Reception</span>';
                } elseif ($row->type == 'plant_reception') {
                    return '<span class="badge badge-glow bg-secondary">Plant Reception</span>';
                }
            });
            $table->addColumn('date', function ($row) {
                if ($row->type == 'plant_reception')
                    return $row->date;
                else
                    return $row->to_time;
            });
            $table->editColumn('serial_number', function ($row) {

                return 'MR-' . $row->serial_number;
            });
            $table->addIndexColumn()->addColumn('user', function ($row) {
                return ($row->mmt <> null) ? $row->mmt->name : "N/A";
            })
                ->filter(function ($instance) use ($request) {

                    if (!empty($request->get('cp_search'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['mcc_id'],  $request->get('cp_search')) ? true : false;
                        });
                    }
                    if ($request->filled('type')) {
                        $instance->where('type', $request->type);
                    }
                    if ($request->filled('collection_point')) {
                        $instance->where('mcc_id', $request->collection_point);
                    }
                    if ($request->filled('area_office')) {
                        $instance->where('area_office_id', $request->area_office);
                    }
                })

                ->rawColumns(['mcc', 'serial_number', 'date', 'action', 'type']);
            return $table->toJson();
        }

        $collectionPoints = CollectionPoint::withoutTrashed()->get();
        $areaOffices = AreaOffice::withoutTrashed()->get();
        return view('content/purchase/receptions')->with(get_defined_vars(), $collectionPoints, $areaOffices);
    }

    public function getPurchaseReceptionAO(Request $request)
    {
        $mrs_query  = MilkReception::whereType('ao_lab_reception')->with('mmt');

        if ($request->ajax()) {
            $table = datatables($mrs_query);
            $table->addIndexColumn()->addColumn('name', function ($row) {
                return  $row->mmt->name;
            });
            $table->editColumn('opening_balance', function ($row) {
                return  $row->opening_balance ?? 0;
            });
            $table->addColumn('date', function ($row) {
                return  date("d/m/y h:i a", $row->to_time);
            });
            $table->editColumn('serial_number', function ($row) {
                return '<a href="' . route('mr', $row->serial_number) . '">MR-' . $row->serial_number . '</a>';
            })
                ->rawColumns(['name', 'serial_number']);

            return $table->toJson();
        }
        return view('content/purchase/receptions_ao')->with(get_defined_vars());
    }

    public function getPurchases(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(MilkPurchase::with('mcc', 'supplier', 'cp')->orderBy('_id', 'desc'));

            $table->addIndexColumn()->addColumn('mcc', function ($row) {
                return ($row->mcc) ? $row->mcc->name : ($row->cp ? $row->cp->name : 'N/A');
            });
            $table->addIndexColumn()->addColumn('ao', function ($row) {
                if ($row->ao <> null)
                    return $row->ao->name;
                elseif ($row->mcc <> null)
                    return $row->mcc->area_office->name;
                elseif ($row->cp <> null)
                    return $row->cp->area_office->name;
                else
                    return "N/A";
            });
            $table->editColumn('serial_number', function ($row) {
                return ($row->serial_number) ? 'MPR-' . $row->serial_number : '';
            })
                ->addIndexColumn()->addColumn('supplier', function ($row) {
                    return ($row->supplier) ? $row->supplier->name : 'N/A';
                })
                ->editColumn('date', function ($row) {
                    return $row->time;
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'purchase_at_mcc')
                        return '<span class="badge badge-glow bg-info">MCC Purchase</span>';
                    elseif ($row->type == 'mmt_purchase')
                        return '<span class="badge badge-glow bg-warning">MMT Purchase</span>';

                    elseif ($row->type == 'purchase_at_ao')
                        return '<span class="badge badge-glow bg-secondary">Area Office Purchase</span>';

                    elseif ($row->type == 'purchase_at_plant')
                        return '<span class="badge badge-glow bg-success">Plant Purchase</span>';
                })
                ->addIndexColumn()->addColumn('action', function (MilkPurchase $row) {
                    $btn = '';
                    if (Auth::user()->can('Details Purchases')) {
                        $btn .= '<a title="View" href="' . route('purchase.view', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                    }
                    if (Auth::user()->can('Delete Purchases')) {
                        $btn .= '<button  title="Void" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('delete-purchase/' . $row->id . '') . '\',\'collection_point_datatable\')">Void</button>';
                    }
                    return $btn;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->filled('type')) {
                        $instance->where('type', $request->type);
                    }
                    if ($request->filled('collection_point') && $request->type == 'purchase_at_mcc') {
                        $instance->where('mcc_id', $request->collection_point);
                    }
                    if ($request->filled('collection_point') && $request->type == 'mmt_purchase') {
                        $instance->where('cp_id', $request->collection_point);
                    }
                    if ($request->filled('area_office')) {
                        $instance->where('area_office_id', $request->area_office);
                    }
                    if ($request->filled('supplier')) {
                        $instance->where('supplier_id', $request->supplier);
                    }
                })
                ->rawColumns(['mcc', 'supplier', 'action', 'type']);
            return $table->toJson();
        }
        $collectionPoints = CollectionPoint::withoutTrashed()->get();
        $areaOffices = AreaOffice::withoutTrashed()->get();
        $suppliers = Supplier::withoutTrashed()->get();
        $plants = Plant::withoutTrashed()->get();
        return view('content/purchase/index', compact('collectionPoints', 'areaOffices', 'suppliers','plants'));
    }

    public function mr($serial_number)
    {
        $mr = MilkReception::where('serial_number', (int)$serial_number)->with('mmt')->first();
        $from_time = $mr->from_time;
        $to_time = $mr->to_time;

        $milk_purchases = MilkReception::where(['mmt_id' => $mr->mmt_id, 'type' => 'mmt_reception'])->with('mcc')->whereBetween('to_time', [$from_time, $to_time])->orderBy('_id', 'desc')->get();
        $milk_purchase_at_cps = MilkPurchase::whereNotNUll('cp_id')->where(['created_by' => $mr->mmt_id])->with('cp')
            ->whereBetween('time', [$from_time, $to_time])
            ->orderBy('_id', 'desc')->get();

        return view('content/purchase/mr', compact('milk_purchases', 'mr', 'milk_purchase_at_cps'));
    }

    public function mps($serial_number)
    {
        $mr = MilkReception::where('serial_number', (int)$serial_number)->first();
        $from_time = $mr->from_time;
        $to_time = $mr->to_time;

        $milk_purchases = MilkPurchase::with('supplier')->where('mcc_id', $mr->mcc_id)->whereBetween('time', [$from_time, $to_time])->orderBy('_id', 'desc')->get();
        return view('content/purchase/mps', compact('milk_purchases', 'mr'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $code = Helper::generateCode('collection_points', 'CP');
        $areas = AreaOffice::active()->get();
        $suppliers = [];

        $by_mmt_types = [];
        $supplier_types = SupplierType::all();

        foreach ($supplier_types as $supplier_type) {
            if ($supplier_type->delivery_config && $supplier_type->delivery_config['by_mmt'] == 1) {
                $by_mmt_types[] = $supplier_type->id;
            }
        }
        //      $mcc_supplier_type = SupplierType::where('name','vmca')->first();
        if (count($by_mmt_types) > 0) {
            $suppliers = Supplier::whereIn('supplier_type_id', $by_mmt_types)->orderBy('created_at', 'desc')->get();
        }

        $generators = $this->getInventories('Generator');
        $chillers = $this->getInventories('Chiller');
        $categories = Categories::all();
        $districts = District::get();

        return view('content.ffl_collection_point.collection_point_create', compact('code', 'suppliers', 'areas', 'generators', 'chillers', 'categories', 'districts'));
    }
    public function getInventories($type)
    {
        $iT = InventoryItemType::where('name', $type)->select('id')->first();
        $cp_inventories = CollectionPoint::whereNotNull($type)->pluck($type);
        $inventories = [];
        foreach ($cp_inventories as $gens) {
            foreach ($gens as $gen) {
                if (isset($gen['id'])) {
                    array_push($inventories, $gen['id']);
                }
            }
        }
        $inventories = ($iT) ? InventoryItem::where('item_type', $iT->id)->whereNotIn('_id', $inventories)->get(['name', 'id']) : [];
        return $inventories;
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
            'name' => 'required|unique:collection_points|string|max:33',
            'category_id' => 'required',
            //           'code' => 'required|unique:collection_points|min:3|max:7|string',
            //            'supplier' => 'required_if:is_mcc,0',
            // 'agreement_period_to' => 'required_if:is_mcc,1', 'agreement_period_from' => 'required_if:is_mcc,1|nullable|before_or_equal:agreement_period_to',
            'area_office_id' => 'required|string',
            'is_mcc' => 'required|numeric',
            //           'chiller' => 'required_if:is_chiller_ffl_owned,1',
            // 'bank_account_no' => 'required_if:is_mcc,1',
            'address' => 'required|max:129',
            'district_id' => 'required',
            'tehsil_id' => 'required',
            // 'agreement_period_to' => 'required_if:is_mcc,1',
            // 'agreement_period_from' => 'required_if:is_mcc,1|nullable|before_or_equal:agreement_period_to',
            // 'agreement_period_wef' => 'nullable|required_with:agreement_period_from|after_or_equal:agreement_period_from|before_or_equal:agreement_period_to',
            'area_office_id' => 'required|string',
            'is_mcc' => 'required|numeric',
            // 'bank_account_no' => 'required_if:is_mcc,1',
            'address' => 'required|max:129',
            // 'paymentOption' => 'required_if:is_mcc,1'
        ], ['agreement_period_from.required_if' => 'Agreement from date is required', 'agreement_period_to.required_if' => 'Agreement to date is required', 'bank_account_no.required_if' => 'Bank Account number  is required']);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }

        if ($request->shop_owner_name || $request->owner_father_name || $request->owner_cnic || $request->owner_ntn || $request->owner_contact || $request->owner_whatsapp || $request->with_effective_date) {
            $owner_contact =  str_replace('-', '', str_replace('_', '', $request->owner_contact));
            $owner_whatsapp =  str_replace('-', '', str_replace('_', '', $request->owner_whatsapp));
            $owner_ntn =  str_replace('-', '', str_replace('_', '', $request->owner_ntn));
            $owner_cnic =  str_replace('-', '', str_replace('_', '', $request->owner_cnic));
            $request->merge([
                'owners' => [['name' => $request->shop_owner_name, 'father_name' => $request->owner_father_name, 'cnic' => $owner_cnic, 'ntn' => $owner_ntn, 'contact' => $owner_contact, 'whatsapp' => $owner_whatsapp, 'with_effective_date' => $request->with_effective_date]]
            ]);
        }

        if ($request->is_mcc) {
            $request->merge([
                'generators' => json_decode($request->generator_ids),
                'agreements' => [['from' => $request->agreement_period_from, 'to' => $request->agreement_period_to, 'wef' => $request->agreement_period_wef, 'refrence_no' => $request->ref_no, 'rent' => $request->rent, 'paymentOption'
                => $request->paymentOption]]
            ]);
        }
        $request->merge([
            'chillers' => json_decode($request->chiller_ids),
        ]);


        $request->request->remove('generator_ids');
        $request->request->remove('chiller_ids');
        $request->request->remove('agreement_period_from');
        $request->request->remove('agreement_period_to');
        $request->request->remove('agreement_period_wef');
        $request->request->remove('ref_no');
        $request->request->remove('rent');
        $request->request->remove('owner_ntn');
        $request->request->remove('owner_cnic');
        $request->request->remove('shop_owner_name');
        $request->request->remove('owner_contact');
        $request->request->remove('owner_father_name');
        $request->request->remove('paymentOption');

        $request->merge([
            'supplier' => $request->supplier ? $request->supplier : null,
            'created_by' => auth()->user()->id,
            'updated_by' => null,
        ]);
        $result = CollectionPoint::create($request->all());
        if ($result) {
            $code = Helper::generateCode('collection_points', 'CP');
            return response()->json([
                'success' => true,
                'message' => 'Collection point created successfully.',
                'code' => $code
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Record not save. Please check your information.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CollectionPoint  $collectionPoint
     * @return \Illuminate\Http\Response
     */
    public function show(CollectionPoint $collectionPoint)
    {
        return redirect()
            ->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CollectionPoint  $collectionPoint
     * @return \Illuminate\Http\Response
     */
    public function edit(CollectionPoint $collectionPoint)
    {
        $generators = $this->getInventories('Generator');
        $chillers = $this->getInventories('Chiller');
        $areas = AreaOffice::get();
        $suppliers = [];
        $by_mmt_types = [];
        $supplier_types = SupplierType::all();

        foreach ($supplier_types as $supplier_type) {
            if ($supplier_type->delivery_config && $supplier_type->delivery_config['by_mmt'] == 1) {
                $by_mmt_types[] = $supplier_type->id;
            }
        }
        if (count($by_mmt_types) > 0) {
            $suppliers = Supplier::whereIn('supplier_type_id', $by_mmt_types)->orderBy('created_at', 'desc')->get();
        }
        $categories = Categories::all();
        $districts = District::get();
        $tehsils = Tehsil::where('district_id', $collectionPoint->district_id)->get();

        return view('content/ffl_collection_point/collection_point_edit', compact('collectionPoint', 'areas', 'suppliers', 'generators', 'chillers', 'categories', 'districts', 'tehsils'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CollectionPoint  $collectionPoint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CollectionPoint $collectionPoint)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|string|max:33|unique:collection_points,name,' . $collectionPoint->id . ',_id',
            'category_id' => 'required',
            'area_office_id' => 'required|string',
            'is_mcc' => 'required|numeric',
            //            'no_of_chillers' => 'required_if:has_chiller,1',
            'bank_account_no' => 'required_if:is_mcc,1',
            'address' => 'required',
            'district_id' => 'required',
            'tehsil_id' => 'required',
        ]);

        if (!$request->is_mcc) {
            $request->request->remove('generators');
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }
        if (!$request->is_chiller_ffl_owned) {
            $request->merge([
                'chillers' => []
            ]);
        }
        if (!$request->is_mcc) {
            $request->merge([
                'agreements' => []
            ]);
        }

        $request->merge([
            'updated_by' => auth()->user()->id,
            'supplier' => $request->supplier ? $request->supplier : null,
        ]);
        $result = $collectionPoint->update($request->all());
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Record not save. Please check your information.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CollectionPoint  $collectionPoint
     * @return \Illuminate\Http\Response
     */
    public function destroy(CollectionPoint $collectionPoint)
    {
        try {
            $res = $collectionPoint->delete();
            if ($res)
                return Response::json([
                    'success' => true,
                    'message' => 'Record deleted'

                ]);
            else
                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted'

                ]);
        } catch (\Throwable $th) {
            return Response::json([
                'success' => false,
                'message' => 'Record not deleted'

            ]);
        }
    }

    public function addAgreement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'nullable|before_or_equal:to',
            'wef' => 'nullable|required_with:from|after_or_equal:from|before_or_equal:to',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $cp = CollectionPoint::find($request->id);
        $aggrement = (isset($cp->agreements)) ? $cp->agreements : [];
        if ($request->wef || $request->from || $request->to || $request->ref_no) {
            $aggrement[] = ['wef' => $request->wef, 'from' => $request->from, 'to' => $request->to, 'refrence_no' => $request->ref_no, 'rent' => $request->rent, 'status' => 1];
        }
        $cp->agreements = $aggrement;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Agreement added successfully',
            'count' => count($cp->agreements),
        ]);
    }

    public function addOwner(Request $request)
    {
        $owner_contact =  str_replace('-', '', str_replace('_', '', $request->owner_contact));
        $owner_whatsapp =  str_replace('-', '', str_replace('_', '', $request->owner_whatsapp));
        $owner_ntn =  str_replace('-', '', str_replace('_', '', $request->owner_ntn));
        $owner_cnic =  str_replace('-', '', str_replace('_', '', $request->owner_cnic));

        $cp = CollectionPoint::find($request->id);
        $owners = (isset($cp->owners)) ? $cp->owners : [];
        $owners[] = ['name' => $request->shop_owner_name, 'father_name' => $request->owner_father_name, 'cnic' => $owner_cnic, 'ntn' => $owner_ntn, 'contact' => $owner_contact, 'whatsapp' => $owner_whatsapp, 'with_effective_date' => $request->with_effective_date, 'status' => 0];
        $cp->owners = $owners;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Owner added successfully',
            'count' => count($cp->owners),
        ]);
    }


    public function addGenerator(Request $request)
    {
        $cp = CollectionPoint::find($request->id);
        $generators = (isset($cp->generators)) ? $cp->generators : [];
        if ($request->generator_id || $request->installation_date) {
            $generators[] = ['installation_date' => $request->installation_date, 'id' => $request->generator_id];
        }
        $cp->generators = $generators;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Generator added successfully',
            'count' => count($cp->generators),
            'index_key' => array_key_last($cp->generators),
        ]);
    }

    public function addChiller(Request $request)
    {
        $cp = CollectionPoint::find($request->id);
        $chillers = (isset($cp->chillers)) ? $cp->chillers : [];
        if ($request->chiller_id || $request->installation_date) {
            $chillers[] = ['installation_date' => $request->installation_date, 'id' => $request->chiller_id];
        }
        $cp->chillers = $chillers;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Chiller added successfully',
            'count' => count($cp->chillers),
            'index_key' => array_key_last($cp->chillers),
        ]);
    }

    public function agreementUpdateStatus(Request $request)
    {
        $cp = CollectionPoint::find($request->id);
        $aggrement = $cp->agreements;
        $aggrement[$request->key - 1]['status'] = (int)$request->status;
        $cp->agreements = $aggrement;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Updated successfully'

        ]);
    }

    public function generatorUpdateStatus(Request $request)
    {
        $cp = CollectionPoint::find($request->id);
        $aggrement = $cp->generators;
        $aggrement[$request->key]['status'] = (int)$request->status;
        $cp->generators = $aggrement;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Updated successfully'

        ]);
    }
    public function chillerUpdateStatus(Request $request)
    {
        $cp = CollectionPoint::find($request->id);
        $data = $cp->chillers;
        $data[$request->key]['status'] = (int)$request->status;
        $cp->chillers = $data;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Updated successfully'

        ]);
    }
    public function generatorDelete(Request $request)
    {
        $cp = CollectionPoint::find($request->id);
        $gen = $cp->generators;

        unset($gen[$request->key]);

        $cp->generators = $gen;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Deleted successfully'

        ]);
    }

    public function chillerDelete(Request $request)
    {
        $cp = CollectionPoint::find($request->id);
        $gen = $cp->chillers;
        unset($gen[$request->key]);
        $cp->chillers = $gen;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Deleted successfully'

        ]);
    }
    public function ownerUpdateStatus(Request $request)
    {
        $cp = CollectionPoint::find($request->id);
        $owners = $cp->owners;
        $owners[$request->key - 1]['status'] = (int)$request->status;
        $cp->owners = $owners;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Updated successfully'
        ]);
    }
    public function purchaseDetails($purchase)
    {
        $data['purchase'] = MilkPurchase::with('mcc', 'supplier', 'cp', 'user')->findorFail($purchase);

        return view('content.purchase.purchase_details', $data);
    }
    public function deletePurchase($purchase)
    {
        $purchase = MilkPurchase::findorFail($purchase);
        //if mcc purchase adjust balance and then soft delete purchase record
        if ($purchase->type == 'purchase_at_mcc') {
            $mr = MilkReception::where('mcc_id', $purchase->mcc_id)->orderBy('_id', 'DESC')->get()->first();
            $receptionDatetime = Carbon::createFromFormat('Y-m-d H:i:s', $mr->getAttributes()['to_time']);
            $purchaseDatetime = Carbon::createFromFormat('Y-m-d H:i:s', $purchase->getAttributes()['time']);
            if ($purchaseDatetime->lt($receptionDatetime))
                return Response::json([
                    'success' => false,
                    'message' => 'Purchase can not be voided after Reception'
                ]);
            $purchase->mcc->balance = round((float) $purchase->mcc->balance - $purchase->gross_volume, 2);
            $purchase->mcc->ts_balance = round((float) $purchase->mcc->ts_balance - $purchase->ts_volume, 2);
            $purchase->mcc->save();
            //if mmt purchase adjust balance and then soft delete purchase record
        } elseif ($purchase->type == 'mmt_purchase') {
            $mr = MilkReception::where('mmt_id', $purchase->created_by)->where('type', 'ao_lab_reception')->orderBy('_id', 'DESC')->get()->first();
            $receptionDatetime = Carbon::createFromFormat('Y-m-d H:i:s', $mr->getAttributes()['to_time']);
            $purchaseDatetime = Carbon::createFromFormat('Y-m-d H:i:s', $purchase->getAttributes()['time']);
            if ($purchaseDatetime->lt($receptionDatetime))
                return Response::json([
                    'success' => false,
                    'message' => 'Purchase can not be voided after Reception'
                ]);
            $purchase->user->balance = round((float) $purchase->user->balance - $purchase->gross_volume,2);
            $purchase->user->ts_balance = round((float) $purchase->user->ts_balance - $purchase->ts_volume,2);
            $purchase->user->save();
            //if area office purchase adjust balance and then soft delete purchase record
        } elseif ($purchase->type == 'purchase_at_ao') {
            $md = MilkDispatch::where('area_office_id', $purchase->area_office_id)->where('type','ao_dispatch_plant')->orderBy('_id', 'DESC')->get()->first();
            $receptionDatetime = Carbon::createFromFormat('Y-m-d H:i:s', $md->getAttributes()['time']);
            $purchaseDatetime = Carbon::createFromFormat('Y-m-d H:i:s', $purchase->getAttributes()['time']);
            if ($purchaseDatetime->lt($receptionDatetime))
                return Response::json([
                    'success' => false,
                    'message' => 'Purchase can not be voided after Milk Dispatch'
                ]);
            $purchase->ao->balance = round((float) $purchase->ao->balance - $purchase->gross_volume,2);
            $purchase->ao->ts_balance = round((float) $purchase->ao->ts_balance - $purchase->ts_volume,2);
            $purchase->ao->save();
        } elseif ($purchase->type == 'purchase_at_plant') {
            $purchase->plant->balance = round((float) $purchase->plant->balance - $purchase->gross_volume,2);
            $purchase->plant->save();
        }
        //prepare sms
        $tests = collect($purchase->tests);
        $lr = $tests->where('qa_test_name', 'LR')->pluck('value')->first();
        $fat = $tests->where('qa_test_name', 'Fat')->pluck('value')->first();
        $snf = $tests->where('qa_test_name', 'SNF')->pluck('value')->first();
        $prepareMsgBody = [];
        $prepareMsgBody['number'] = $purchase->supplier->contact ?? "N/A";
        $prepareMsgBody['business_name'] = $purchase->supplier->business_name ?? $purchase->supplier->name;
        $prepareMsgBody['collection_point'] = $purchase->cp->name ?? 'N/A';
        $prepareMsgBody['lr'] = $lr;
        $prepareMsgBody['fat'] = $fat;
        $prepareMsgBody['snf'] = $snf;
        $prepareMsgBody['gross_volume'] = $purchase->gross_volume;
        $prepareMsgBody['ts_volume'] = $purchase->ts_volume;
        $prepareMsgBody['message_time'] = $purchase->time;
        //send sms to supplier
        $this->sendSMSToSuplierVoid($prepareMsgBody);
        //delete the purchase
        $res = $purchase->delete();
        if ($res)
            return Response::json([
                'success' => true,
                'message' => 'Purchase is voided successfully'

            ]);
        else
            return Response::json([
                'success' => false,
                'message' => 'Purchase is voided successfully'
            ]);
    }

    public function deleteReception($id)
    {
        $mr = MilkReception::where('_id', $id)->with('mmt', 'mcc', 'ao')->first();

        if ($mr->type == 'ao_lab_reception') {
            //flow of voiding entry of reception of area office is not decided yet
            return Response::json([
                'success' => false,
                'message' => 'Lab Reception can not be deleted from the system yet'
            ]);
            //if area office lab reception then mmt balance will be added and area office balance will be minsued
            //add mmt balance 
            $mmt_balance = (float) ($mr->mmt->balance <> null) ? $mr->mmt->balance : 0;
            $mr->mmt->balance = (float)$mmt_balance + $mr->gross_volume;
            //minus area office balance
            $ao_balance = (float) ($mr->ao->balance <> null) ? $mr->ao->balance : 0;
            $mr->ao->balance = (float)$ao_balance - $mr->gross_volume;
        } elseif ($mr->type == 'mmt_reception') {
            //check if reception is done from mmt then throw error that reception can not be deleted
            $mr_ao = MilkReception::where('mmt_id', $mr->created_by)->where('type', 'ao_lab_reception')->orderBy('_id', 'DESC')->get()->first();
            $aoReceptionDatetime = Carbon::createFromFormat('Y-m-d H:i:s', $mr_ao->getAttributes()['to_time']);
            $mmtReceptiondatetime = Carbon::createFromFormat('Y-m-d H:i:s', $mr->getAttributes()['to_time']);
            if ($mmtReceptiondatetime->lt($aoReceptionDatetime))
                return Response::json([
                    'success' => false,
                    'message' => 'MMT Reception can not be voided after Reception is completed by area office'
                ]);
            //if mmt reception then mcc balance will be added and mmt balance will be minsued
            //add mcc balance 
            $mcc_balance = (float) ($mr->mcc->balance <> null) ? $mr->mcc->balance : 0;
            $mr->mcc->balance = round((float) $mcc_balance + $mr->gross_volume,2);
            $mr->mcc->ts_balance = round((float) $mr->mcc->ts_balance + $mr->volume_ts,2);
            $mr->mcc->save();
            //minus mmt balance
            $mmt_balance = (float) ($mr->mmt->balance <> null) ? $mr->mmt->balance : 0;
            $mr->mmt->balance = round((float)$mmt_balance - $mr->gross_volume,2);
            $mr->mmt->ts_balance = round((float) $mr->mmt->ts_balance - $mr->volume_ts,2);
            $mr->mmt->save();
        }
        //plant reception doucment delete pending 

        $res = $mr->delete();
        if ($res)
            return Response::json([
                'success' => true,
                'message' => 'Reception is deleted successfully'

            ]);
        else
            return Response::json([
                'success' => false,
                'message' => 'Reception is not deleted successfully'
            ]);
    }

    public function receptionDetails($id)
    {
        $data['purchase'] = MilkReception::with('mcc', 'mmt')->findorFail($id);
        return view('content.purchase.reception_details', $data);
    }

    public function rejection(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(MilkRejection::with('mcc', 'supplier')->orderBy('_id', 'desc'));

            $table->addIndexColumn()->addColumn('mcc', function ($row) {
                return ($row->mcc) ? $row->mcc->name : ($row->cp ? $row->cp->name : 'N/A');
            });
            $table->addIndexColumn()->addColumn('ao', function ($row) {
                if ($row->ao <> null)
                    return $row->ao->name;
                elseif ($row->mcc <> null)
                    return $row->mcc->area_office->name;
                elseif ($row->cp <> null)
                    return $row->cp->area_office->name;
                else
                    return "N/A";
            });
            $table->addIndexColumn()->addColumn('plant', function ($row) {
                if ($row->plant <> null)
                    return $row->plant->name;
                elseif ($row->mcc <> null)
                    return $row->mcc->area_office->zone->section->department->plant->name;
                elseif ($row->cp <> null)
                    return $row->cp->area_office->zone->section->department->plant->name;
                elseif ($row->ao <> null)
                    return $row->ao->zone->section->department->plant->name;
                else
                    return "N/A";
            });
            $table->editColumn('serial_number', function ($row) {
                return ($row->serial_number) ? 'MR-' . $row->serial_number : '';
            })
                ->addIndexColumn()->addColumn('supplier', function ($row) {
                    return ($row->supplier) ? $row->supplier->name : '';
                })
                ->editColumn('date', function ($row) {
                    return $row->time;
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'purchase_at_mcc')
                        return '<span class="badge badge-glow bg-info">MCC Purchase</span>';
                    elseif ($row->type == 'mmt_purchase')
                        return '<span class="badge badge-glow bg-warning">MMT Purchase</span>';

                    elseif ($row->type == 'purchase_at_ao')
                        return '<span class="badge badge-glow bg-secondary">Area Office Purchase</span>';

                    elseif ($row->type = 'purchase_at_plant')
                        return '<span class="badge badge-glow bg-success">Plant Purchase</span>';
                })
                ->addIndexColumn()->addColumn('action', function (MilkRejection $row) {
                    $btn = '';
                    // if (Auth::user()->can('Details Purchases')) {
                    //     $btn .= '<a title="View" href="' . route('purchase.view', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                    // }
                    // if (Auth::user()->can('Delete Purchases')) {
                    //     $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('delete-purchase/' . $row->id . '') . '\',\'collection_point_datatable\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                    // }
                    $btn .= '<a title="View" href="' . route('get.rejectionDetails', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                    return $btn;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->filled('type')) {
                        $instance->where('type', $request->type);
                    }
                    if ($request->filled('collection_point')) {
                        $instance->where('mcc_id', $request->collection_point);
                    }
                    if ($request->filled('area_office')) {
                        $instance->where('area_office_id', $request->area_office);
                    }
                    if ($request->filled('plant')) {
                        $instance->where('plant_id', $request->plant);
                    }
                    if ($request->filled('supplier')) {
                        $instance->where('supplier_id', $request->supplier);
                    }
                })
                ->rawColumns(['mcc', 'supplier', 'action', 'type']);
            return $table->toJson();
        }
        $collectionPoints = CollectionPoint::get();
        $areaOffices = AreaOffice::get();
        $plants = Plant::get();
        $suppliers = Supplier::get();
        return view('content/rejection/index', compact('collectionPoints', 'areaOffices', 'plants', 'suppliers'));
    }

    public function rejectionDetails(Request $request)
    {
        $id = $request->id;
        $data['rejection'] = MilkRejection::with('mcc', 'supplier')->findorFail($id);
        return view('content.rejection.rejection_detail', $data);
    }

    public function purchasedRejections(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(PurchasedMilkRejection::orderBy('_id', 'desc'));
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $btn = '';
                // if (Auth::user()->can('Details Purchases')) {
                //     $btn .= '<a title="View" href="' . route('reception.view', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                // }
                // if (Auth::user()->can('Delete Purchases')) {
                //     $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('delete-reception/' . $row->id . '') . '\',\'collection_point_datatable\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                // }
                $btn .= '<a title="View" href="' . route('get.purchasedRejectionDetails', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                return $btn;
            });

            $table->addIndexColumn()->addColumn('mcc', function ($row) {

                return  $row->mcc ? $row->mcc->name : "N/A";
            });
            $table->addIndexColumn()->addColumn('ao', function ($row) {
                if ($row->ao <> null)
                    return $row->ao->name;
                elseif ($row->ao == null && $row->mcc_id <> null)
                    return $row->mcc->area_office->name;
                else
                    return "N/A";
            });
            $table->editColumn('opening_balance', function ($row) {
                return  $row->opening_balance1 ?? 0;
            });
            $table->editColumn('type', function ($row) {
                if ($row->type == 'mmt_reception') {
                    return '<span class="badge badge-glow bg-info">MMT Reception</span>';
                } elseif ($row->type == 'ao_lab_reception') {
                    return '<span class="badge badge-glow bg-warning">Area Office Lab Reception</span>';
                } elseif ($row->type == 'plant_reception') {
                    return '<span class="badge badge-glow bg-secondary">Plant Reception</span>';
                }
            });
            $table->addColumn('date', function ($row) {
                if ($row->type == 'plant_reception')
                    return $row->date;
                else
                    return $row->to_time;
            });
            $table->editColumn('serial_number', function ($row) {

                return 'PMR-' . $row->serial_number;
            });
            $table->addIndexColumn()->addColumn('user', function ($row) {
                return ($row->mmt <> null) ? $row->mmt->name : "N/A";
            });
            $table->addIndexColumn()->addColumn('plant', function ($row) {
                if ($row->plant_id <> null)
                    return $row->plant->name;
                elseif ($row->mcc_id <> null)
                    return $row->mcc->area_office->zone->section->department->plant->name;
                elseif ($row->area_office_id <> null)
                    return $row->ao->zone->section->department->plant->name;
                else
                    return "N/A";
            })
                ->filter(function ($instance) use ($request) {

                    if (!empty($request->get('cp_search'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['mcc_id'],  $request->get('cp_search')) ? true : false;
                        });
                    }
                    if ($request->filled('type')) {
                        $instance->where('type', $request->type);
                    }
                    if ($request->filled('collection_point')) {
                        $instance->where('mcc_id', $request->collection_point);
                    }
                    if ($request->filled('area_office')) {
                        $instance->where('area_office_id', $request->area_office);
                    }
                })

                ->rawColumns(['mcc', 'serial_number', 'date', 'action', 'type']);
            return $table->toJson();
        }

        $collectionPoints = CollectionPoint::get();
        $areaOffices = AreaOffice::get();
        return view('content/rejection/purchased-index')->with(get_defined_vars(), $collectionPoints, $areaOffices);
    }

    public function purchasedRejectionDetails(Request $request)
    {
        $id = $request->id;
        $data['purchaseRejection'] = PurchasedMilkRejection::with('mcc', 'mmt', 'ao', 'plant')->findorFail($id);
        return view('content.rejection.purchase_rejection_detail', $data);
    }

    public function getPlantReception(Request $request)
    {
        $cps = CollectionPoint::where(['is_mcc' => '1'])->select('id', 'name')->get();
        $mrs_query  = MilkReception::where('type', 'plant_reception')->with('plant', 'user');

        if ($request->ajax()) {
            $table = datatables($mrs_query);
            $table->addIndexColumn()->addColumn('action', function ($row) {
                $btn = '';
                if (Auth::user()->can('Details Purchases')) {
                    $btn .= '<a title="View" href="' . route('plantreception.detail', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                }
                if (Auth::user()->can('Delete Purchases')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('delete-reception/' . $row->id . '') . '\',\'collection_point_datatable\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            });
            $table->addColumn('gross_volume', function ($row) {
                return  $row->gross_volume ?? 0;
            });
            $table->addColumn('opening_balance', function ($row) {
                return  $row->opening_balance ?? 0;
            });
            $table->editColumn('type', function ($row) {
                if ($row->type == 'mmt_reception') {
                    return '<span class="badge badge-glow bg-info">MMT Reception</span>';
                } elseif ($row->type == 'ao_lab_reception') {
                    return '<span class="badge badge-glow bg-warning">Area Office Lab Reception</span>';
                } elseif ($row->type == 'plant_reception') {
                    return '<span class="badge badge-glow bg-secondary">Plant Reception</span>';
                }
            });
            $table->addColumn('date', function ($row) {
                if ($row->type == 'plant_reception')
                    return $row->date;
                else
                    return $row->to_time;
            });
            $table->editColumn('serial_number', function ($row) {

                return 'MR-' . $row->serial_number;
            });
            $table->addColumn('user_name', function ($row) {

                return ($row->user <> null) ? $row->user->name : "N/A";
            });
            $table->addColumn('ts_volume', function ($row) {

                return $row->volume_ts ?? 0;
            });
            $table->addColumn('plant', function ($row) {
                if ($row->plant_id <> null)
                    return $row->plant->name;
                elseif ($row->mcc_id <> null)
                    return $row->mcc->area_office->zone->section->department->plant->name;
                elseif ($row->area_office_id <> null)
                    return $row->ao->zone->section->department->plant->name;
                else
                    return "N/A";
            })
                ->rawColumns(['serial_number', 'date', 'action', 'type', 'gross_volume', 'opening_balance', 'user_name', 'ts_volume']);
            return $table->toJson();
        }

        $collectionPoints = CollectionPoint::get();
        $areaOffices = AreaOffice::get();

        return view('content.plant_reception.index', compact('collectionPoints', 'areaOffices'));
    }

    public function plantReceptionDetail($id)
    {
        $plantDetail  = MilkReception::where('type', 'plant_reception')->where('_id', $id)->with('plant', 'user', 'gateinfo')->first();

        return view('content.plant_reception.plant_reception_detail', compact('plantDetail'));
    }
}
