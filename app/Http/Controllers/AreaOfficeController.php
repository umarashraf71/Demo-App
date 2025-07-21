<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class AreaOfficeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Area Office'], ['only' => ['index']]);
        $this->middleware(['permission:Create Area Office'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Area Office'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Area Office'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(AreaOffice::with('zone')->orderBy('_id', 'desc'));
            $table->addIndexColumn()->addColumn('action', function (AreaOffice $row) {
                $btn = '';
                if (Auth::user()->can('Edit Area Office')) {
                    $btn .= '<a href="' . route('area-office.edit', $row->id) . '" title="Edit" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Area Office')) {
                    $btn .= '<button class="btn btn-icon btn-danger" title="Delete" onclick="DeleteRecord(\'' . URL::to('area-office/' . $row->id . '') . '\',\'area_office_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            });

            $table->editColumn('zone_id', function ($row) {
                return ($row->zone) ? $row->zone->name : $row->zone_id;
            })->editColumn('status', function ($row) {
                return Helper::addStatusColumn($row, 'area_offices');
            })
                ->rawColumns(['action', 'status']);
            return $table->toJson();
        }
        return view('content.ffl_area_office.area_office_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $code = Helper::generateCode('area_offices', 'AO');
        $districts = District::get();
        $all_zone = Zone::active()->get();
        return view('content.ffl_area_office.area_office_create', compact('code', 'all_zone', 'districts'));
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
            //           'code' => 'required|string|max:7|min:3|unique:area_offices',
            'zone_id' => 'required|string',
            'name' => 'required|string|unique:area_offices',
            'contact' => 'required|string|min:5|max:15',
            'whatsapp' => 'nullable|string|min:5|max:15',
            'address' => 'required|string',
            'agreement_period_wef' => 'nullable|required_with:agreement_period_from|after_or_equal:agreement_period_from|before_or_equal:agreement_period_to',
            'rent' => 'required|integer',
            'agreement_period_from' => 'nullable|before_or_equal:agreement_period_to',
            'owner_name' => 'required|string',
            'owner_father_name' => 'required|string',
            'owner_contact' => 'required|string|min:5|max:15',
            'bank_account_title' => 'required|string',
            'bank_id' => 'required|string',
            'bank_branch_code' => 'required|string',
            'bank_account_no' => 'required|string|min:5|max:25',
            'next_of_kin_name' => 'required|string',
            'next_of_kin_father_name' => 'required|string',
            'relation' => 'required|string',
            'next_of_kin_contact' => 'required|string',
            'with_effective_date' => 'required',
            'district_id' => 'required',
            'tehsil_id' => 'required',
        ]);

        $cnic = str_replace('-', '', $request->cnic);
        $cnic = str_replace('_', '', $cnic);
        $contact = str_replace('-', '', $request->contact);
        $contact = str_replace('_', '', $contact);
        $owner_contact = str_replace('-', '', $request->owner_contact);
        $next_of_kin_contact = str_replace('-', '', $request->next_of_kin_contact);
        $request->merge([
            'cnic' => $cnic,
            'contact' => $contact,
            'next_of_kin_contact' => $next_of_kin_contact,
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        if ($request->shop_owner_name || $request->owner_father_name || $request->owner_cnic || $request->owner_ntn || $request->owner_contact || $request->with_effective_date) {
            $request->merge([
                'owners' => [['name' => $request->owner_name, 'father_name' => $request->owner_father_name, 'cnic' => $request->owner_cnic, 'ntn' => $request->owner_ntn, 'contact' => $owner_contact, 'with_effective_date' => $request->with_effective_date]]
            ]);
        }

        $aggrement = [];
        if ($request->agreement_period_wef || $request->agreement_period_from || $request->agreement_period_to || $request->ref_no) {
            $aggrement[] = ['wef' => $request->agreement_period_wef, 'from' => $request->agreement_period_from, 'to' => $request->agreement_period_to, 'refrence_no' => $request->ref_no, 'rent' => $request->rent, 'status' => 1, 'paymentOption'
            => $request->paymentOption];
        }

        $request->request->remove('agreement_period_from');
        $request->request->remove('agreement_period_to');
        $request->request->remove('agreement_period_wef');
        $request->request->remove('ref_no');
        $request->request->remove('rent');
        $request->request->remove('owner_ntn');
        $request->request->remove('owner_cnic');
        $request->request->remove('owner_name');
        $request->request->remove('owner_contact');
        $request->request->remove('owner_father_name');
        $request->request->remove('paymentOption');

        $request->merge([
            'created_by' => auth()->user()->id,
            'updated_by' => null,
            'agreements' => $aggrement
        ]);
        $result = AreaOffice::create($request->all());
        if ($result) {
            return redirect()->back()
                ->with('success', 'New record added successfully.');
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
     * @param  \App\Models\AreaOffice  $areaOffice
     * @return \Illuminate\Http\Response
     */
    public function show(AreaOffice $areaOffice)
    {
        return redirect()
            ->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AreaOffice  $areaOffice
     * @return \Illuminate\Http\Response
     */
    public function edit(AreaOffice $areaOffice)
    {
        $all_zone = Zone::active()->orWhere('_id', $areaOffice->zone_id)->get();
        $districts = District::get();
        $tehsils = Tehsil::where('district_id', $areaOffice->district_id)->get();

        return view('content.ffl_area_office.area_office_edit', compact('areaOffice', 'all_zone', 'districts', 'tehsils'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AreaOffice  $areaOffice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AreaOffice $areaOffice)
    {
        $validator = Validator::make($request->all(), [
            //            'code' => 'required|string|min:3|max:7|unique:area_offices,code,' . $request->id. ',_id',
            'name' => 'required|string|unique:area_offices,name,' . $request->id . ',_id',
            'zone_id' => 'required|string',
            'address' => 'required|string',
            'contact' => 'required|string|min:5|max:15',
            'whatsapp' => 'nullable|string|min:5|max:15',
            //            'rent' => 'required|integer',
            //            'agreement_period_from' => 'required|string',
            //            'agreement_period_to' => 'required|string',
            //            'owner_name' => 'required|string',
            //            'father_name' => 'required|string',
            'cnic' => 'nullable|string|min:5|max:25',
            'ntn' => 'nullable|string|min:5|max:25',
            //            'owner_contact' => 'required|string|min:5|max:15',
            'bank_account_title' => 'required|string',
            'bank_id' => 'required|string',
            'bank_branch_code' => 'required|string',
            'bank_account_no' => 'required|string|min:5|max:25',
            'next_of_kin_name' => 'required|string',
            'next_of_kin_father_name' => 'required|string',
            'relation' => 'required|string',
            'next_of_kin_contact' => 'required|string',
            'district_id' => 'required',
            'tehsil_id' => 'required',
        ]);

        $contact = str_replace('-', '', $request->contact);
        $contact = str_replace('_', '', $contact);
        $cnic = str_replace('-', '', $request->cnic);
        $owner_contact = str_replace('-', '', $request->owner_contact);
        $owner_contact = str_replace('_', '', $owner_contact);
        $next_of_kin_contact = str_replace('-', '', $request->next_of_kin_contact);
        $next_of_kin_contact = str_replace('_', '', $next_of_kin_contact);
        $request->merge([
            'cnic' => $cnic,
            'contact' => $contact,
            'owner_contact' => $owner_contact,
            'next_of_kin_contact' => $next_of_kin_contact,
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $request->merge([
            'updated_by' => auth()->user()->id,
        ]);
        $result = $areaOffice->update($request->all());

        if ($result) {
            return redirect()->route('area-office.index')
                ->with('success', 'Record updated successfully.');
        } else {
            return redirect()
                ->back()
                ->with('errorMessage', 'Record not save. Please check your information.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AreaOffice  $areaOffice
     * @return \Illuminate\Http\Response
     */
    public function destroy(AreaOffice $areaOffice)
    {
        if (CollectionPoint::where('area_office_id', $areaOffice->id)->exists()) {
            return Response::json([
                'success' => false,
                'message' => 'Record not deleted due to exist in other module'

            ]);
        }

        try {
            $res = $areaOffice->delete();
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

    public function addOwner(Request $request)
    {
        $ao = AreaOffice::find($request->id);
        $owners = (isset($ao->owners)) ? $ao->owners : [];
        $owners[] = ['name' => $request->owner_name, 'father_name' => $request->owner_father_name, 'cnic' => $request->owner_cnic, 'ntn' => $request->owner_ntn, 'contact' => $request->owner_contact, 'with_effective_date' => $request->with_effective_date, 'status' => 0];
        $ao->owners = $owners;
        $ao->save();
        return Response::json([
            'success' => true,
            'message' => 'Owner added successfully',
            'count' => count($ao->owners),
        ]);
    }
    public function ownerUpdateStatus(Request $request)
    {
        $cp = AreaOffice::find($request->id);
        $owners = $cp->owners;
        $owners[$request->key - 1]['status'] = (int)$request->status;
        $cp->owners = $owners;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Updated successfully'

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

        $cp = AreaOffice::find($request->id);
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
    public function agreementUpdateStatus(Request $request)
    {
        $cp = AreaOffice::find($request->id);
        $aggrement = $cp->agreements;
        $aggrement[$request->key - 1]['status'] = (int)$request->status;
        $cp->agreements = $aggrement;
        $cp->save();
        return Response::json([
            'success' => true,
            'message' => 'Updated successfully'

        ]);
    }
}
