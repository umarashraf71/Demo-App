<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\AreaOffice;
use App\Models\Zone;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class ZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Zone'], ['only' => ['index']]);
        $this->middleware(['permission:Create Zone'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Edit Zone'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Delete Zone'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(Zone::with('section'));
            $table->addIndexColumn()->addColumn('action', function (Zone $row) {
                $btn = '';
                if (Auth::user()->can('Edit Zone')) {
                    $btn .= '<a title="Edit" href="' . route('zone.edit', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
                }
                if (Auth::user()->can('Delete Zone')) {
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\''.URL::to('zone/'.$row->id.'').'\',\'zone_table\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            })
            ->editColumn('status', function ($row) {
                return Helper::addStatusColumn($row,'zones');
            })
            ->editColumn('section_id', function ($row) {
                return ($row->section)?ucfirst($row->section->name):$row->section_id;
            })
                    ->rawColumns(['action','status']);
            return $table->toJson();
        }
        return view('content.ffl_zone.zone_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $code = Helper::generateCode('zones','Z');
        $all_section = Section::active()->get();
        return view('content.ffl_zone.zone_create', compact('code', 'all_section'));
    }
    public function generateUniqueCode()
    {
        do {
            $random_code = random_int(1000, 9999);
        } while (Zone::where("code", "=", $random_code)->first());

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
//            'code' => 'required|integer|unique:zones',
            'name' => 'required|string|unique:zones',
            'section_id' => 'required|string',
            'address' => 'required|string',
            'contact' => 'required|string|min:5|max:15',
        ]);

        $contact = str_replace('-','', $request->contact);
        $request->merge([
            'contact' => $contact,
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
        $result = Zone::create($request->all());
        if($result){
            return redirect()->route('zone.index')
                            ->with('success','New record added successfully.');
        }else{
            return redirect()
                        ->back()
                        ->with('errorMessage','Record not save. Please check your information.')
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function show(Zone $zone)
    {
        return redirect()
        ->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function edit(Zone $zone)
    {
        $all_section = Section::active()->orWhere('_id', $zone->section_id)->get();
        return view('content.ffl_zone.zone_edit',compact('zone', 'all_section'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Zone $zone)
    {
        $validator = Validator::make($request->all(), [
//            'code' => 'required|integer|unique:zones,'.$request->id,
            'name' => 'required|string|unique:zones,'.$request->id,
            'section_id' => 'required|string',
            'address' => 'required|string',
            'contact' => 'required|string|min:5|max:15',
        ]);

        $contact = str_replace('-','', $request->contact);
        $request->merge([
            'contact' => $contact,
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
        $result = $zone->update($request->all());
        if($result){
            return redirect()->route('zone.index')
                            ->with('success','Record updated successfully');
        }else{
            return redirect()
                        ->back()
                        ->with('errorMessage','Record not save. Please check your information.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function destroy(Zone $zone)
    {
        if (AreaOffice::where('zone_id',$zone->id)->exists()) {
            return Response::json([
                'success' => false,
                'message' => 'Record not deleted due to exist in other module'

            ]);
        }
        try {
            $res = $zone->delete();
            if($res)
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
