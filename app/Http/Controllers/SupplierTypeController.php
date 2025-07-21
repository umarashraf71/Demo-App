<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Categories;
use App\Models\Incentive;
use App\Models\Price;
use App\Models\Supplier;
use App\Models\SupplierType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class SupplierTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Source Type'], ['only' => ['index']]);
        $this->middleware(['permission:Create Source Type'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Source Type'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Source Type'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(SupplierType::with('supliercategory')->orderBy('_id', 'desc'));
            $table->addIndexColumn()->addColumn('action', function (SupplierType $row) {
                $btn = '';
                if (Auth::user()->can('Edit Source Type')) {
                    $btn .= '<a title="Edit" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editRecord(\'' . $row->id . '' . '\',\'' . $row->name . '' . '\',\'' . $row->description . '\',\'' . $row->domain . '\', \'' . $row->category_id . '\')" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Source Type')) {
                    $btn .= '<button title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('source-type/' . $row->id . '') . '\',\'supplier_type_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })
                ->editColumn('category_id', function ($row) {
                    return $row->supliercategory->category_name ?? 'NULL';
                })
                ->editColumn('status', function ($row) {
                    return Helper::addStatusColumn($row, 'supplier_types');
                })
                //                $table->addIndexColumn()->addColumn('price', function ($row) {
                //                    $btn = '';
                //                    if (Auth::user()->can('Edit Supplier Type')) {
                //                        if($row->price){
                //                            $price =$row->price->price;
                //                        }else{
                //                            $price ='';
                //                        }
                //                        $btn .= '<div class="price_'.$row->id.'"><span>'.$price.'</span> <a href="#" onclick="setPrice(\''.$row->id.'\',\''.$price.'\')" ><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a></div>';
                //                    }
                //                    return $btn;
                //                })
                ->rawColumns(['category_id', 'action', 'status']);
            return $table->toJson();
        }
        $categories = Categories::all();
        return view('content/ffl_supplier_type/supp_type_list', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('content.ffl_supplier_type.supp_type_create', compact('categories'));
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
            'name' => 'required|unique:supplier_types|string|max:55|min:2',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }


        $result = SupplierType::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'domain' => $request->domain,
            'created_by' => auth()->user()->id,
            'updated_by' => null,
            'delivery_config' => ['at_mcc' => 0, 'by_mmt' => 0, 'at_area_office' => 0, 'at_plant' => 0, 'by_plant' => 0],
        ]);

        //         Price::create([
        //            'source_type'=>$result->id,
        //            'price'=>$request->price,
        //            'status'=>1
        //        ]);

        $request->merge(['id' => $result->id, 'from' => 'other']);

        $setPrice = new SupplierController();
        $setPrice->updatePrice($request);

        if ($result) {
            return redirect()->back()
                ->with('success', 'Source added successfully');
        } else {

            return redirect()
                ->back()
                ->with('errorMessage', 'Record not save. Please check your information.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupplierType  $supplierType
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierType $supplierType)
    {
        return redirect()
            ->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SupplierType  $supplierType
     * @return \Illuminate\Http\Response
     */
    //    public function edit($id)
    //    {
    //        $supplierType  = SupplierType::with('price')->find($id);
    //        return view('content.ffl_supplier_type/supp_type_edit',compact('supplierType'));
    //    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupplierType  $supplierType
     * @return \Illuminate\Http\Response
     */
    //    public function update(Request $request, $id)
    //    {
    //        $validator = Validator::make($request->all(), [
    //            'name' => 'required|string|max:55|min:2|unique:supplier_types,name,' . $id . ',_id',
    ////            'price' => 'required|numeric|min:0',
    //        ]);
    //
    ////        Price::updateOrCreate(array('from_id' => $id,'type' =>'supplier_type'),[
    ////            'price'=>$request->price,
    ////        ]);
    //
    //        if ($validator->fails()) {
    //            return redirect()
    //                        ->back()
    //                        ->withErrors($validator)
    //                        ->withInput();
    //        }
    //
    //
    //        $result = SupplierType::where('_id', $id)->update(['name'=>$request->name,'updated_by' => auth()->user()->id,'description' => $request->description]);
    //        if($result){
    //            return redirect()->route('source-type.index')
    //                            ->with('success','Record updated successfully');
    //        }else{
    //            return redirect()
    //                        ->back()
    //                        ->with('errorMessage','Record not save. Please check your information.');
    //        }
    //    }
    public function sourceTypeUpdate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:55|min:2|unique:supplier_types,name,' . $request->id . ',_id',
            'category_id' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }

        $result = SupplierType::where('_id',  $request->id)->update(['name' => $request->name, 'updated_by' => auth()->user()->id, 'description' => $request->description,  'category_id' => $request->category_id, 'domain' => $request->domain]);
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupplierType  $supplierType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Supplier::where('supplier_type_id', $id)->exists()) {
            return Response::json([
                'success' => false,
                'message' => 'Record not deleted due to exist in other module'

            ]);
        }
        try {
            $res = SupplierType::where('_id', $id)->delete();
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
    public function deliveryConfigview()
    {
        $supplierTypes = SupplierType::all();
        return view('content.ffl_supplier_type.supp_type_delivery_config', compact($supplierTypes, 'supplierTypes'));
    }

    public function incentiveConfigview()
    {
        $incentives = \DB::table('incentives')->get();

        return view('content.ffl_supplier.incentive_config', compact($incentives, 'incentives'));
    }
    public function deliveryConfigstore(Request $request)
    {
        $input = $request->all();
        $supplierTypes = SupplierType::all();
        //build an milk delivery configuration array to be saved agaist every supplier type
        $delivery_config = array();
        foreach ($supplierTypes as $supplierType) {
            if (isset($input['at_mcc']) && in_array($supplierType->id, $input['at_mcc']))
                $delivery_config['at_mcc'] = (int) 1;
            else
                $delivery_config['at_mcc'] = (int) 0;

            if (isset($input['by_mmt']) && in_array($supplierType->id, $input['by_mmt']))
                $delivery_config['by_mmt'] = (int) 1;
            else
                $delivery_config['by_mmt'] = (int) 0;

            if (isset($input['at_area_office']) && in_array($supplierType->id, $input['at_area_office']))
                $delivery_config['at_area_office'] = (int) 1;
            else
                $delivery_config['at_area_office'] = (int) 0;

            if (isset($input['at_plant']) && in_array($supplierType->id, $input['at_plant']))
                $delivery_config['at_plant'] = (int) 1;
            else
                $delivery_config['at_plant'] = (int) 0;

            if (isset($input['by_plant']) && in_array($supplierType->id, $input['by_plant']))
                $delivery_config['by_plant'] = (int) 1;
            else
                $delivery_config['by_plant'] = (int) 0;

            $supplierType->delivery_config = (array) $delivery_config;
            $supplierType->save();
        }
        return redirect()->back()->with('success', 'Configuration updated successfully');
    }

    public function incentiveConfigstore(Request $request)
    {
        $input = $request->all();
        $incentives = Incentive::all();
        $delivery_config = array();
        foreach ($incentives as $incentive) {
            if (isset($input['cdf']) && in_array($incentive->id, $input['cdf']))
                $delivery_config['cdf'] = (int) 1;
            else
                $delivery_config['cdf'] = (int) 0;

            if (isset($input['mvmc']) && in_array($incentive->id, $input['mvmc']))
                $delivery_config['mvmc'] = (int) 1;
            else
                $delivery_config['mvmc'] = (int) 0;

            if (isset($input['lf_ffl_chiller']) && in_array($incentive->id, $input['lf_ffl_chiller']))
                $delivery_config['lf_ffl_chiller'] = (int) 1;
            else
                $delivery_config['lf_ffl_chiller'] = (int) 0;

            if (isset($input['lf_own_chiller']) && in_array($incentive->id, $input['lf_own_chiller']))
                $delivery_config['lf_own_chiller'] = (int) 1;
            else
                $delivery_config['lf_own_chiller'] = (int) 0;

            if (isset($input['vmca_ffl_chiller']) && in_array($incentive->id, $input['vmca_ffl_chiller']))
                $delivery_config['vmca_ffl_chiller'] = (int) 1;
            else
                $delivery_config['vmca_ffl_chiller'] = (int) 0;

            if (isset($input['vmca_own_chiller']) && in_array($incentive->id, $input['vmca_own_chiller']))
                $delivery_config['vmca_own_chiller'] = (int) 1;
            else
                $delivery_config['vmca_own_chiller'] = (int) 0;

            if (isset($input['cf']) && in_array($incentive->id, $input['cf']))
                $delivery_config['cf'] = (int) 1;
            else
                $delivery_config['cf'] = (int) 0;

            $incentive->config = (array) $delivery_config;
            $incentive->save();
        }
        return redirect()->back()->with('success', 'Configuration updated successfully');
    }
}
