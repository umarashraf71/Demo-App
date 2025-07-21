<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Customer'], ['only' => ['index']]);
        $this->middleware(['permission:Create Customer'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Customer'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Customer'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(Customer::all());
            $table->addIndexColumn()->addColumn('action', function (Customer $row) {
                $btn = '';
                if (Auth::user()->can('Edit Customer')) {
                    $btn .= '<a title="Edit" href="' . route('customer.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Customer')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('customer/' . $row->id . '') . '\',\'customer_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })
                ->rawColumns(['action']);
            return $table->toJson();
        }
        return view('content.ffl_customer.customer_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $code = Helper::generateCode('customers', 'C');
        return view('content.ffl_customer.customer_create', compact('code'));
    }
    public function generateUniqueCode()
    {
        do {
            $random_code = random_int(1000, 9999);
        } while (Customer::where("code", "=", $random_code)->first());

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
        $cnic = str_replace('-', '', $request->cnic);
        $contact = str_replace('-', '', $request->contact);
        $whatsapp = str_replace('-', '', $request->whatsapp);
        $whatsapp = str_replace('_', '', $whatsapp);
        $request->merge([
            'cnic' => $cnic,
            'contact' => $contact,
            'whatsapp' => $whatsapp,
        ]);
        $validator = Validator::make($request->all(), [
            //            'code' => 'required|string|max:7|min:3|unique:customers',
            'name' => 'required|unique:customers|string',
            'address' => 'required|string',
            'contact' => 'required|string|min:3',
            'cnic' => 'required|min:3|numeric',
        ]);



        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $request->merge([
            'created_by' => auth()->user()->id,
            'updated_by' => null,
        ]);

        $result = Customer::create($request->all());
        if ($result) {
            return redirect()
                ->back()
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
    public function edit(Customer $customer)
    {
        return view('content.ffl_customer.customer_edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $cnic = str_replace('-', '', $request->cnic);
        $contact = str_replace('-', '', $request->contact);
        $whatsapp = str_replace('-', '', $request->whatsapp);
        $whatsapp = str_replace('_', '', $whatsapp);
        $request->merge([
            'cnic' => $cnic,
            'contact' => $contact,
            'whatsapp' => $whatsapp,
        ]);
        $validator = Validator::make($request->all(), [
            //            'code' => 'required|string|min:3|max:7|unique:customers,code,' . $customer->id. ',_id',
            'name' => 'required|string|min:3|max:19|unique:customers,name,' . $customer->id . ',_id',
            'address' => 'required|string',
            'contact' => 'required|string',
            'cnic' => 'required|numeric',
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

        $result = $customer->update($request->all());

        if ($result) {
            return redirect()->route('customer.index')
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
    public function destroy(Customer $customer)
    {
        try {
            $res = $customer->delete();
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
}
