<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\SupplierType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Create Bank'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:View Bank'], ['only' => ['index']]);
        $this->middleware(['permission:Edit Bank'], ['only' => ['update']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(Bank::all());
            $table->addIndexColumn()->addColumn('action', function (Bank $row) {
                $btn = '';
                if (Auth::user()->can('Edit Bank')) {
                    $btn .= '<a title="Edit" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editRecord(\'' . $row->id . '' . '\',\'' . $row->name . '' . '\',\'' . $row->short_name . '' . '\')" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                return $btn;
            })
                ->rawColumns(['action']);
            return $table->toJson();
        }
        return view('content.bank.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:banks|string|max:55|min:2',
            'short_name' => 'required|string|max:55|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }

        $result = Bank::create([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'created_by' => auth()->user()->id,
            'status' => 1,
            'updated_by' => null,
        ]);


        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Bank created successfully',
            ]);
        } else {
            return redirect()
                ->back()
                ->with('errorMessage', 'Bank not save. Please check your information.')
                ->withInput();
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:55|min:2|unique:banks,name,' . $request->id . ',_id',
            'short_name' => 'required|string|max:55|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'key' => $validator->errors()->keys()[0]
            ]);
        }
        $bank = Bank::where('_id', $request->id)->first();
        $result = $bank->update(['name' => $request->name, 'short_name' => $request->short_name, 'updated_by' => auth()->user()->id]);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully',
            ]);
        }
    }
}
