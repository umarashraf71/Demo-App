<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\MilkCollectionVehicle;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class VendorProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Vendor Profile'], ['only' => ['index']]);
        $this->middleware(['permission:Create Vendor Profile'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Vendor Profile'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Vendor Profile'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(VendorProfile::orderBy('created_at', 'desc'));
            $table->addIndexColumn()->addColumn('action', function (VendorProfile $row) {
                $btn = '';
                if (Auth::user()->can('Edit Vendor Profile')) {
                    $btn .= '<a title="Edit" href="' . route('vendor-profile.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Vendor Profile')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('vendor-profile/' . $row->id . '') . '\',\'vendor_profile_datatable\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })
                ->rawColumns(['action']);
            return $table->toJson();
        }
        return view('content.ffl_vendor_profile.vendor_profile_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $code = Helper::generateCode('vendor_profiles', 'VP');
        return view('content.ffl_vendor_profile.vendor_profile_create', compact('code'));
    }
    //    public function generateUniqueCode()
    //    {
    //        do {
    //            $random_code = random_int(1000, 9999);
    //        } while (VendorProfile::where("code", "=", $random_code)->first());
    //
    //        return $random_code;
    //    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //          'code' => 'required|unique:vendor_profiles|integer',
            'name' => 'required|unique:vendor_profiles|string',
            //            'agreement_period' => 'required|string',
            'contact_no' => 'required|string|min:10|min:6',
            'agreement_period_from' => 'required|before_or_equal:agreement_period_to',
            'agreement_period_to' => 'required',
            'agreement_period_wef' => 'nullable|required_with:agreement_period_from|after_or_equal:agreement_period_from|before_or_equal:agreement_period_to',
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $aggrement = [];
        if ($request->agreement_period_wef || $request->agreement_period_from || $request->agreement_period_to || $request->ref_no) {
            $aggrement[] = ['wef' => $request->agreement_period_wef, 'from' => $request->agreement_period_from, 'to' => $request->agreement_period_to, 'ref_no' => $request->ref_no, 'status' => 1];
            $request->merge(['agreements' => $aggrement]);
        }
        $request->request->remove('agreement_period_wef');
        $request->request->remove('agreement_period_from');
        $request->request->remove('agreement_period_to');
        $request->request->remove('ref_no');

        $cnic = str_replace('-', '', $request->cnic);
        $contact_no = str_replace('-', '', $request->contact_no);
        $contact_no = str_replace('_', '', $contact_no);
        $contact_person_phone = str_replace('-', '', $request->contact_person_phone);
        $contact_person_phone = str_replace('_', '', $contact_person_phone);
        $whatsapp = str_replace('-', '', $request->whatsapp);
        $whatsapp = str_replace('_', '', $whatsapp);
        $request->merge([
            'cnic' => $cnic,
            'contact_no' => $contact_no,
            'contact_person_phone' => $contact_person_phone,
            'whatsapp' => $whatsapp,
        ]);



        $request->merge([
            'created_by' => auth()->user()->id,
            'updated_by' => null,
        ]);

        $result = VendorProfile::create($request->all());
        if ($result) {
            return redirect()->route('vendor-profile.index')
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
    public function edit(VendorProfile $vendorProfile)
    {
        return view('content.ffl_vendor_profile.vendor_profile_edit', compact('vendorProfile'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VendorProfile $vendorProfile)
    {
        $validator = Validator::make($request->all(), [
            //            'code' => 'required|integer|unique:vendor_profiles,code,'.$request->id.',_id',
            'name' => 'required|unique:vendor_profiles,name,' . $request->id . ',_id',
            //            'agreement_period' => 'required|string',
            'contact_no' => 'required|string|min:5',
        ]);

        $contact_no = str_replace('-', '', $request->contact_no);
        $contact_no = str_replace('_', '', $contact_no);
        $contact_person_phone = str_replace('-', '', $request->contact_person_phone);
        $contact_person_phone = str_replace('_', '', $contact_person_phone);

        $whatsapp = str_replace('-', '', $request->whatsapp);
        $whatsapp = str_replace('_', '', $whatsapp);

        $cnic = str_replace('-', '', $request->cnic);
        $request->merge([
            'cnic' => $cnic,
            'contact_no' => $contact_no,
            'contact_person_phone' => $contact_person_phone,
            'whatsapp' => $whatsapp,
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

        $result = $vendorProfile->update($request->all());
        if ($result) {
            return redirect()->route('vendor-profile.index')
                ->with('success', 'Record updated successfully');
        } else {
            return redirect()
                ->back()
                ->with('errorMessage', 'Record not save. Please check your information.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorProfile $vendorProfile)
    {
        try {
            if (MilkCollectionVehicle::where('company', $vendorProfile->id)->exists())
                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted due to exist in other module'

                ]);
            $res = $vendorProfile->delete();
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
        $vendor = VendorProfile::find($request->id);

        $aggrement = (isset($vendor->agreements)) ? $vendor->agreements : [];
        if ($request->wef || $request->from || $request->to || $request->ref_no) {
            $aggrement[] = ['wef' => $request->wef, 'from' => $request->from, 'to' => $request->to, 'ref_no' => $request->ref_no, 'status' => 1];
        }
        $vendor->agreements = $aggrement;
        $vendor->save();
        return Response::json([
            'success' => true,
            'message' => 'Agreement added successfully',
            'count' => count($vendor->agreements),
        ]);
    }

    public function agreementUpdateStatus(Request $request)
    {
        $vendor = VendorProfile::find($request->id);
        $aggrement = $vendor->agreements;
        $aggrement[$request->key - 1]['status'] = (int)$request->status;
        $vendor->agreements = $aggrement;
        $vendor->save();
        return Response::json([
            'success' => true,
            'message' => 'Updated successfully'

        ]);
    }
}
