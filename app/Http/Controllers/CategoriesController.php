<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\SupplierType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Categories'], ['only' => ['index']]);
        $this->middleware(['permission:Create Categories'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Categories'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Categories'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(Categories::all());
            $table->addIndexColumn()->addColumn('action', function (Categories $row) {
                $btn = '';
                if (Auth::user()->can('Edit Categories')) {
                    $btn .= '<a title="Edit" href="' . route('categories.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Categories')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('categories/' . $row->_id . '') . '\',\'categories_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })
                ->rawColumns(['action']);
            return $table->toJson();
        }
        return view('content.ffl_categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('content.ffl_categories.create');
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
            'category_name' => 'required',

        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Categories::create([
                'category_name' => $request->category_name,

                'created_by' => auth()->user()->id,
                'updated_by' => null,
            ]);
            return redirect()->back()->with('success', 'New record added successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('errorMessage', 'Something Went wrong Please try again');
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Categories::where('_id', $id)->first();
        return view('content.ffl_categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'category_name' => 'required',

        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $category = Categories::where('_id', $id)->first();

            $category->update([
                'category_name' => $request->category_name,
                'updated_by' => auth()->user()->id,
            ]);
            return redirect()->back()->with('success', ' record updated successfully.');
        } catch (Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('errorMessage', 'Something Went wrong Please try again');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (SupplierType::where('category_id', $id)->exists())
                return Response::json([
                    'success' => false,
                    'message' => 'Record not deleted due to exist in other module'

                ]);
            Categories::where('_id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'

            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Category not deleted '

            ]);
        }
    }
}
