<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\District;
use App\Models\MilkPurchase;
use App\Models\Plant;
use App\Models\Price;
use App\Models\Supplier;
use App\Models\SupplierType;
use App\Models\Tehsil;
use App\Models\Workflow;
use App\Models\WorkFlowApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Supplier'], ['only' => ['index']]);
        $this->middleware(['permission:Create Supplier'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Supplier'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Supplier'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(Supplier::with('supplier_type')->orderBy('created_at', 'desc')->get());
            $table->addIndexColumn()->addColumn('action', function (Supplier $row) {
                $btn = '';
                if (Auth::user()->can('Edit Supplier')) {
                    $btn .= '<a title="Edit" href="' . route('supplier.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Supplier')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('supplier/' . $row->id . '') . '\',\'supplier_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })

                ->editColumn('status', function ($row) {
                    return Helper::addStatusColumn($row, 'suppliers');
                })->editColumn('payment_process', function ($row) {
                    $checked = ($row->payment_process == 1) ? 'checked' : '';
                    $statusBtn = '<div class="form-check form-switch form-check-primary">
                                    <input type="checkbox" class="form-check-input" id="payment_process_' . $row->id . '" name="payment_process" value="1" ' . $checked . ' onchange="updatePaymentProcessStatus(this, \'' . $row->id . '\')"/>
                                    <label class="form-check-label" for="payment_process_' . $row->id . '">
                                     <span class="switch-icon-left"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                                     <span class="switch-icon-right"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>
                                    </label>
                                 </div>';
                    return $statusBtn;
                });

                $table->addIndexColumn()->addColumn('area_office', function ($row) {
    
                    if($row->area_office <> null)
                    {
                        $ao = AreaOffice::where('_id', $row->area_office)->first();
    
                        return $ao ? $ao->ao_name : $row->area_office;
                    }
                    else if((!$row->area_office || $row->area_office == null) && $row->cp_ids == null && $row->mcc <> null)
                    {
                        $mccId = $row->mcc[0];
                        $mcc = CollectionPoint::where('_id',$mccId)->first();
                        return $mcc->area_office->ao_name;
                    }
                    else if((!$row->area_office || $row->area_office == null) && $row->mcc == null && $row->cp_ids <> null)
                    {
                        $cpId = $row->cp_ids[0];
                        $cp = CollectionPoint::where('_id',$cpId)->first();
                        return $cp->area_office->ao_name;
                    }
                    else
                    {
                        return "N/A";
                    }
    
                });

            $table->editColumn('supplier_type_id', function ($row) {
                return ($row->supplier_type) ? ucfirst($row->supplier_type->name) : $row->supplier_type_id;
            })
                ->rawColumns(['action', 'status', 'payment_process','area_office']);
            return $table->toJson();
        }
        return view('content.ffl_supplier.supplier_list');
    }

    public function create()
    {
        $code = Helper::generateCode('suppliers', 'SP');
        $fetch_data = SupplierType::active()->get();
        $cps = CollectionPoint::where('is_mcc', '0')->get(['name', 'id']);
        $districts = District::get();

        return view('content.ffl_supplier.supplier_create', compact('code', 'fetch_data', 'cps', 'districts'));
    }
    //    public function generateUniqueCode($Class='App\Models\Supplier')
    //    {
    //        do {
    //            $random_code = random_int(1000, 9999);
    //        } while ($Class::where("code", "=", $random_code)->first());
    //
    //        return $random_code;
    //    }


    protected function removeSupplierIdFromCps($supplier)
    {
        if ($supplier && $supplier->cp_ids) {
            $records = CollectionPoint::find($supplier->cp_ids);
            foreach ($records as $record) {
                $record->pull('supplier_ids', $supplier->id);
            }
        }
    }
    protected function removeSupplierIdFromMCCs($supplier)
    {
        if ($supplier && $supplier->mcc) {
            $mccs = $supplier->mcc && gettype($supplier->mcc) == 'array' ? $supplier->mcc : [];
            $records = CollectionPoint::whereIn('_id', $mccs)->get();

            foreach ($records as $record) {
                $record->pull('supplier_ids', $supplier->id);
            }
        }
    }

    protected function assignSupplierIdToCps($cps, $supplier_id)
    {
        foreach ($cps as $cp) {
            $record = CollectionPoint::where('_id', $cp)->first();
            $record->push('supplier_ids', $supplier_id, true);
        }
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'nullable|email',
            'bank_branch_code' => 'required',
            'supplier_type_id' => 'required|string',
            'father_name' => 'required|string',
            'whatsapp' => 'nullable|string|min:5|max:15',
            'contact' => 'required|string|min:8|max:15',
            'cnic' => 'required|string|min:12|max:15',
            'agreement_period_wef' => 'nullable|required_with:agreement_period_from|after_or_equal:agreement_period_from|before_or_equal:agreement_period_to',
            'agreement_period_from' => 'nullable|before_or_equal:agreement_period_to',
            'address' => 'required|string',
            'bank_account_no' => 'required|fixed_iban',
            // 'business_name' => 'required',
            'district_id' => 'required',
            'tehsil_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }
        $aggrement = [];
        if ($request->agreement_period_wef || $request->agreement_period_from || $request->agreement_period_to || $request->ref_no) {
            $aggrement[] = ['effective_from' => $request->agreement_period_wef, 'from' => $request->agreement_period_from, 'to' => $request->agreement_period_to, 'ref_no' => $request->ref_no, 'status' => 1];
        }

        $cps = [];
        if ($request->cp_ids && $request->by_mmt) {
            if ($request->by_mmt) {
                $cps = explode(',', $request->cp_ids);
                $request->merge(['cp_ids' => $cps]);
            } else {
                $request->request->remove('cp_ids');
            }
        }
        $mccs = [];
        if ($request->mcc) {
            if ($request->mcc) {
                $mccs = $request->mcc;
            } else {
                $request->request->remove('mcc');
            }
        }

        $request->request->remove('agreement_period_wef');
        $request->request->remove('agreement_period_from');
        $request->request->remove('agreement_period_to');
        $request->request->remove('ref_no');
        $request->request->remove('by_mmt');

        $type = SupplierType::find($request->supplier_type_id);
        $config = $type->delivery_config;
        $name = $type->name;

        if ($config['at_mcc']) {
            $request->merge([
                'plant' => null,
                'area_office' => null,
            ]);
        } else if ($config['at_area_office'] || $name == 'ms' || $name == 'lf') {
            $request->merge([
                'plant' => null,
                'mcc' => null,
            ]);
        } else if ($config['at_plant'] || $config['by_plant']) {
            $request->merge([
                'area_office' => null,
                'mcc' => null,
            ]);
        }

        $request->merge([
            'agreements' => $aggrement,
            'created_by' => auth()->user()->id,
            'updated_by' => null,
        ]);

        $result = Supplier::create($request->all());

        if (count($cps) > 0) {
            $this->assignSupplierIdToCps($cps, $result->id);
        }
        if (count($mccs) > 0) {
            $this->assignSupplierIdToCps($mccs, $result->id);
        }
        if ($result) {
            $code = Helper::generateCode('suppliers', 'SP');
            return response()->json([
                'success' => true,
                'message' => 'Supplier added successfully.',
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
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        return redirect()
            ->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        $type = SupplierType::find($supplier->supplier_type_id);
        $supplier_cps = $supplier->cp_ids ?? [];
       
        $cps = CollectionPoint::where('is_mcc', '0')->whereNotIN('_id', $supplier_cps)->get(['name', 'id']);

        $config = $type->delivery_config;

        $name = $type->name;
        $html = '';

        if ($config) {
            if ($config['at_mcc']) {
                $selected_mcc = $supplier->mcc && gettype($supplier->mcc) == 'array' ? $supplier->mcc : [$supplier->mcc];
                $html .= '<label class="form-label">Collection Center</label>';
                $html .= '<select class="select2 form-select"  multiple name="mcc[]" data-placeholder="Choose MCC" >';
                $mccs = CollectionPoint::where(['is_mcc' => '1', 'status' => 1])->orWhereIn('_id', $selected_mcc)->get(['name', 'id']);

                foreach ($mccs as $data) {
                    $selected = $supplier->mcc && gettype($supplier->mcc) == 'array' && in_array($data->id, $supplier->mcc) ? 'selected' : '';
                    $html .= '<option  value="' . $data->id . '"  ' . $selected . '>' . $data->name . '</option>';
                }
                $html .= '</select>';
            } else if ($config['at_area_office'] || $name == 'ms' || $name == 'lf') {
                $html .= '<label class="form-label">Area Office</label>';
                $html .= '<select class="select2 form-select checkcollectionpoint " name="area_office"><option value="" selected disabled>Choose Area Office</option>';
                $areaOffices = AreaOffice::active()->orWhere('_id', $supplier->area_office)->get(['name', 'id']);
                foreach ($areaOffices as $data) {
                    $selected = $data->id == $supplier->area_office ? 'selected' : '';
                    $html .= '<option value="' . $data->id . '"  ' . $selected . '>' . $data->name . '</option>';
                }
                $html .= '</select>';
            } else if ($config['at_plant'] || $config['by_plant']) {
                $html .= '<label class="form-label">Plant</label>';
                $html .= '<select class="select2 form-select" name="plant"><option value="" selected disabled>Choose Plant</option>';
                $plants = Plant::active()->orWhere('_id', $supplier->plant)->get(['name', 'id']);
                foreach ($plants as $data) {
                    $selected = $data->id == $supplier->plant ? 'selected' : '';
                    $html .= '<option value="' . $data->id . '"  ' . $selected . '>' . $data->name . '</option>';
                }
                $html .= '</select>';
            }
        }

        $fetch_data = SupplierType::all();
        $districts = District::get();
        $tehsils = Tehsil::where('district_id', $supplier->district_id)->get();
        return view('content.ffl_supplier.supplier_edit', compact('supplier', 'fetch_data', 'html', 'cps', 'type', 'districts', 'tehsils'));
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'bank_account_no' => 'required|fixed_iban',
            'bank_branch_code' => 'required',
            'cnic' => 'required|string|min:12|max:15',
            // 'name' => 'required|string|unique:suppliers,name,' . $request->id . ',_id,deleted_at,NULL',
            'email' => 'nullable|email|unique:suppliers,email,' . $request->id . ',_id,deleted_at,NULL',
            'supplier_type_id' => 'required|string',
            'father_name' => 'required|string',
            'whatsapp' => 'nullable|string|min:5|max:15',
            'contact' => 'required|string|min:8|max:15',
            'address' => 'required|string',
            'business_name' => 'required',
            'district_id' => 'required',
            'tehsil_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }


        $supplier_type = SupplierType::find($request->supplier_type_id);
        $config = $supplier_type->delivery_config;
        $name = $supplier_type->name;
        if ($config['at_mcc']) {
            $request->merge([
                'plant' => null,
                'area_office' => null,
            ]);
        } else if ($config['at_area_office'] || $name == 'ms' || $name == 'lf') {
            $request->merge([
                'plant' => null,
                'mcc' => null,
            ]);
        } else if ($config['at_plant'] || $config['by_plant']) {
            $request->merge([
                'area_office' => null,
                'mcc' => null,
            ]);
        }
        $cps = [];
        if ($request->cp_ids && $request->by_mmt && $config['by_mmt']) {
            $cps = explode(',', $request->cp_ids);
            $request->merge(['cp_ids' => $cps]);
        } else {
            $request->merge([
                'cp_ids' => null,
            ]);
        }
        if (!$request->mcc) {
            $request->merge([
                'mcc' => null,
            ]);
        }

        $request->request->remove('agreement_period_wef');
        $request->request->remove('agreement_period_from');
        $request->request->remove('agreement_period_to');
        $request->request->remove('ref_no');
        $request->request->remove('by_mmt');
        $request->merge([
            'updated_by' => auth()->user()->id,
        ]);

        $result = $supplier->update($request->all());

        $mccs = ($request->mcc) ? $request->mcc : [];
        $this->removeSupplierIdFromMCCs($supplier);
        $this->assignSupplierIdToCps($mccs, $supplier->id);

        $this->removeSupplierIdFromCps($supplier);
        $this->assignSupplierIdToCps($cps, $supplier->id);
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
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        try {
            if (MilkPurchase::where('supplier_id', $supplier->id)->exists())
                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted due to exist in other module'

                ]);
            $res = $supplier->delete();
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

    public function updatePrice(Request $request)
    {

        if (!$request->ajax()) {
            $request->validate([
                'price' => 'required|numeric|min:0|max:10000',
                'id' => 'required',
                'volume' => 'numeric|min:0|max:100000',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'price' => 'required|numeric|min:0|max:1000',
                'volume' => 'required|numeric|min:0|max:100000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ]);
            }
        }
        $is_update_req = 1;
        $user = auth()->user();
        $doc_type = '';
        $price = Price::where('from_id', $request->id)->first();

        if (!$price) {
            $price =  new Price;
            $price->source_type = $request->id;
            //          $price->from_id = $request->id;
            $price->volume = (int)$request->volume;
            $price->is_approved = 0;
            $price->price = null;
            $price->status = 0;
            $price->save();
            $is_update_req = 0;
        }

        if ($is_update_req && $price->price == $request->price && $request->from != 'other') {
            return Response::json([
                'success' => false,
                'message' => 'New and Previous price cannot be same'
            ]);
        }

        if ($price && $price->type == 'supplier') {
            $doc_type = '5';
        } else if ($price && $price->type == 'cp') {
            $doc_type = '2';
        } else if ($price && $price->type == 'supplier_type') {
            $doc_type = '1';
        }
        $workFlow = Workflow::where('document_type', $doc_type)->first();
        $is_workflow_exist = 0;
        if ($price && $workFlow) {
            $is_workflow_exist = 1;
            $unique_code = $this->generateUniqueCode('App\Models\WorkFlowApproval');
            foreach ($workFlow->role_ids as $key => $role_id) {
                WorkFlowApproval::create(['code' => $unique_code, 'role_id' => $role_id, 'type' => 'price', 'workflow_id' => $workFlow->id, 'updated_by' => null, 'created_by' => $user->id, 'table_id' => $price->id, 'status' => '0', 'step' => $key + 1, 'data' => ['curr_value' => $price->price, 'new_value' => $request->price]]);
            }
        }

        if ($request->ajax()) {
            $price->is_approved = (int)$is_workflow_exist ? 0 : $price->is_approved;
            $price->price = (int)$is_workflow_exist ? $price->price : $request->price;
            $price->volume  = (int)$request->volume;
            $price->save();

            return Response::json([
                'success' => true,
                'message' => 'Price updated successfully'
            ]);
        } else {
            //          for new entry
            if (!$is_workflow_exist) {
                $price->price = (int)$request->price;
                $price->is_approved = 1;
                $price->status = 1;
                $price->save();
            }
        }
        return redirect()->back()->with('success', 'Price set successfully');
    }

    public function getTypeWiseData(Request $request)
    {
        $type = SupplierType::find($request->id);

        $config = $type->delivery_config;

        $name = $type->name;
        $html = '';
        if ($config) {
            if ($config['at_mcc']) {
                $html .= '<label class="form-label">Collection Center</label>';
                $html .= '<select class="select3 checkcollectionpoint form-select" name="mcc[]" multiple data-placeholder="Choose MCC">';
                $mccs = CollectionPoint::where('category_id', $type->category_id)->where('is_mcc', '1')->active()->get(['name', 'id']);
                foreach ($mccs as $mcc) {
                    $html .= '<option value="' . $mcc->id . '" >' . $mcc->name . '</option>';
                }
                $html .= '</select>';
            } else if ($config['at_area_office'] || $name == 'ms' || $name == 'lf') {
                $html .= '<label class="form-label">Area Office</label>';
                $html .= '<select class="select2 checkcollectionpoint form-select" name="area_office"><option value="" selected disabled>Choose Area Office</option>';
                $areaOffices = AreaOffice::active()->get(['name', 'id']);
                foreach ($areaOffices as $data) {
                    $html .= '<option value="' . $data->id . '" >' . $data->name . '</option>';
                }
                $html .= '</select>';
            } else if ($config['at_plant'] || $config['by_plant']) {
                $html .= '<label class="form-label">Plant</label>';
                $html .= '<select class="select2 checkcollectionpoint form-select" name="plant"><option value="" selected disabled>Choose Plant</option>';
                $plants = Plant::active()->get(['name', 'id']);
                foreach ($plants as $data) {
                    $html .= '<option value="' . $data->id . '" >' . $data->name . '</option>';
                }
                $html .= '</select>';
            }
        }


        return response()->json([
            'success' => true,
            'data' => $html,
            'by_mmt' => $config['by_mmt']
        ]);
    }

    public function getCollectionPointData(Request $request)
    {
        $syplierType = SupplierType::find($request->sourcetype);

        $results = CollectionPoint::where('is_mcc', '0')->where('category_id', $syplierType->category_id)->where('area_office_id', $request->area_office_id)->get();
        $html = '';
        if ($results->isNotEmpty()) {
            $html .= '<label class="form-label">Collection Points</label>';
            $html .= '<select class="select2   form-select" id="cp_field" name="cp"><option value="" selected disabled>Choose Collection Point</option>';

            foreach ($results as $result) {
                $html .= '<option value="' . $result->id . '" >' . $result->name . '</option>';
            }

            $html .= '</select>';
        } else {
            $html .= "<h5 style='text-align:center'>No Record Found, Select Another Area Office</h5>";
        }



        return response()->json([
            'success' => true,
            'data' => $html,

        ]);
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

        $supplier = Supplier::find($request->id);
        $aggrement = (isset($supplier->agreements)) ? $supplier->agreements : [];
        if ($request->wef || $request->from || $request->to || $request->ref_no) {
            $aggrement[] = ['effective_from' => $request->wef, 'from' => $request->from, 'to' => $request->to, 'ref_no' => $request->ref_no, 'status' => 0];
        }
        $supplier->agreements = $aggrement;
        $supplier->save();
        return Response::json([
            'success' => true,
            'message' => 'Agreement added successfully',
            'count' => count($supplier->agreements),
        ]);
    }

    public function agreementUpdateStatus(Request $request)
    {
        $supplier = Supplier::find($request->id);
        $aggrement = $supplier->agreements;
        if ($request->status == 1) {
            foreach ($aggrement as $key => $data) {
                if ($data['status'] == 1 && $key != $request->key - 1) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Agreement already active'
                        ]
                    );
                }
            }
        }


        $aggrement[$request->key - 1]['status'] = (int)$request->status;
        $supplier->agreements = $aggrement;
        $supplier->save();
        return Response::json([
            'success' => true,
            'message' => 'Updated successfully'

        ]);
    }

    public function updatePaymentProcess(Request $request)
    {
        $supplier = Supplier::find($request->id);
        $supplier->payment_process = (int)$request->status;
        $supplier->save();
        return response()->json([
            'success' => true,
            'message' => 'Updated successfully'

        ]);
    }
}
