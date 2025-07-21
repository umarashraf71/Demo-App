<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\InventoryItemType;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class InventoryItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Inventory Item'], ['only' => ['index']]);
        $this->middleware(['permission:Create Inventory Item'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Inventory Item'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Inventory Item'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables(InventoryItem::with('type')->get())
            ->addIndexColumn()
            ->addColumn('action', function (InventoryItem $row) {
                $btn = '';
                if (Auth::user()->can('Edit Inventory Item')) {
                    $btn .= '<a title="Edit" href="' . route('inventory-item.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Inventory Item')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('inventory-item/' . $row->id . '') . '\',\'inventory_item_datatable\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })
            ->editColumn('item_type', function ($row) {
                return ($row->type) ? $row->type->name : $row->item_type;
            })
            ->editColumn('status', function ($row) {
                return ($row->status) ? 'Active' : 'In Active';
            })
            ->editColumn('area_office_id', function ($row) {
                return $row->area_office->name;
            })
            ->rawColumns(['action'])->toJson();
        }
        return view('content.ffl_inventory_item.inventory_item_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $code = Helper::generateCode('inventory_items', 'I');
        $inventory_type = InventoryItemType::get();
        $areaOffices = AreaOffice::get();
        return view('content.ffl_inventory_item.inventory_item_create', compact('code', 'inventory_type', 'areaOffices'));
    }
    public function generateUniqueCode()
    {
        do {
            $random_code = random_int(1000, 9999);
        } while (InventoryItem::where("code", "=", $random_code)->first());

        return $random_code;
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
            //            'code' => 'required|max:7|min:3|unique:inventory_items',
            'item_type' => 'required|string',
            'name' => 'required|string',
            'tag_number' => 'required',
            'status' => 'required|numeric',
            'area_office' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $status = (int)$request->status;
        $area_office_id = $request->area_office;
        $request->merge([
            'status' => $status,
            'area_office_id' => $area_office_id,
            'created_by' => auth()->user()->id,
            'updated_by' => null,
        ]);
        $result = InventoryItem::create($request->all());
        if ($result) {
            return redirect()
                ->back()
                ->with('success', 'New record added successfully.');
        } else {
            return redirect()
                ->back()
                ->with('errorMessage', 'Record Not Save. Please check your information.')
                ->withInput();
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
        return redirect()
            ->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(InventoryItem $inventoryItem)
    {

        $inventory_type = InventoryItemType::get();
        $areaOffices = AreaOffice::get();
        return view('content/ffl_inventory_item/inventory_item_edit', compact('inventoryItem', 'inventory_type', 'areaOffices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventoryItem $inventoryItem)
    {
        //        dd( $inventoryItem->id);
        $validator = Validator::make($request->all(), [
            //            'code' => 'required|string|min:3|max:7|unique:inventory_items,code,' . $inventoryItem->id.',_id',
            'item_type' => 'required|string',
            'name' => 'required|string',
            'tag_number' => 'required',
            'status' => 'required|numeric',
            'area_office' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $status = (int)$request->status;
        $area_office_id = $request->area_office;
        $request->merge([
            'status' => $status,
            'area_office_id' => $area_office_id,
            'updated_by' => auth()->user()->id,
        ]);
        $result = $inventoryItem->update($request->all());
        if ($result) {
            return redirect()->route('inventory-item.index')
                ->with('success', 'Record updated successfully');
        } else {
            return redirect()
                ->back()
                ->with('errorMessage', 'Record Not Save. Please check your information.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryItem $inventoryItem)
    {
        try {
            $collections = CollectionPoint::get();
            foreach ($collections as $collection) {
                if (!empty($collection->chillers)) {
                    for ($i = 0; $i < count($collection->chillers); $i++) {
                        if ($collection->chillers[$i]['id'] == $inventoryItem->id) {
                            return Response::json([
                                'success' => false,
                                'message' => 'Record not deleted due to exist in other module'

                            ]);
                        } elseif ($collection->generators[$i]['id'] == $inventoryItem->id) {
                            return Response::json([
                                'success' => false,
                                'message' => 'Record not deleted due to exist in other module'

                            ]);
                        }
                    }
                }
            }
            // if (CollectionPoint::whereHas('chillers', $inventoryItem->id)->exists() || CollectionPoint::where('generators', $inventoryItem->id)->exists()) {
            //     dd("ko");
            //     return Response::json([
            //         'success' => false,
            //         'message' => 'Record not deleted due to exist in other module'

            //     ]);
            // }
            $res = $inventoryItem->delete();
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

    public function chillerDetail(InventoryItem $inventoryItem)
    {
        return response()->json($inventoryItem);
    }
}
