<?php

namespace App\Http\Controllers;

use App\Exports\ExportAoCollectionSummary;
use App\Exports\ExportFreshMilkPurchaseSummary;
use App\Models\CollectionPoint;
use App\Models\AreaOffice;
use App\Models\MilkPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Excel;
use PDF;


class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:AO Collection Summary'], ['only' => ['areaOfficeCollectionSummary']]);
    }

    public function areaOfficeCollectionSummary(Request $request)
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
            $table->editColumn('fat', function ($row) {
                $tests = collect($row->tests);
                $fat = $tests->where('qa_test_name', 'Fat')->pluck('value')->first();
                return $fat;
            });
            $table->editColumn('snf', function ($row) {
                $tests = collect($row->tests);
                $snf = $tests->where('qa_test_name', 'SNF')->pluck('value')->first();
                return $snf;
            });
            $table->editColumn('lr', function ($row) {
                $tests = collect($row->tests);
                $lr = $tests->where('qa_test_name', 'LR')->pluck('value')->first();
                return $lr;
            });
            $table->editColumn('serial_number', function ($row) {
                return ($row->serial_number) ? 'MPR-' . $row->serial_number : '';
            })
                ->addIndexColumn()->addColumn('supplier', function ($row) {
                    return ($row->supplier) ? $row->supplier->name : 'N/A';
                })
                ->addIndexColumn()->addColumn('supplier_code', function ($row) {

                    return ($row->supplier) ?  $row->supplier->code : 'N/A';
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
                ->filter(function ($instance) use ($request) {
                    if ($request->filled('collection_point') && $request->collection_point <> 0) {
                        $instance->where('mcc_id', $request->collection_point)->orWhere('cp_id', $request->collection_point);
                    }
                    if ($request->filled('to_date')) {
                        $collectionPointIds = CollectionPoint::where('area_office_id', $request->area_office_id)->pluck('_id')->toArray();
                        $instance->where('area_office_id', $request->area_office_id)
                        ->orWhereIn('cp_id', $collectionPointIds)
                        ->orWhereIn('mcc_id', $collectionPointIds)
                        ->whereBetween('booked_at', [$request->from_date, $request->to_date]);
                    }
                })
                ->rawColumns(['mcc', 'supplier', 'action', 'type', 'supplier_code', 'fat', 'snf', 'lr']);
            return $table->toJson();
        }
        $areaOffices = AreaOffice::get();
        return view('content/reports/ao-collection-summary', compact('areaOffices'));
    }

    public function getCollectionPoints(Request $request)
    {
        $collectionPoints = CollectionPoint::where('area_office_id', $request->areaoffice_id)->get();
        $html = '';
        $html .= '<label class="form-label"> Collection Points</label>';

        $html .= '<select class="form-select search-input" id="collection_point" name="collection_point_id"><option selected disabled value="0">Please Select</option>';


        foreach ($collectionPoints as $cp) {
            $html .= '<option  value="' . $cp->id . '" >' . $cp->name . '</option>';
        }
        $html .= '</select>';
        return response()->json([
            'success' => true,
            'data' => $html
        ]);
    }

    public function exportFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_office_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required|after_or_equal:from_date',
            'export_to' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->export_to == 'PDF') {
            return $this->generatePDF($request->all());
        } else {
            return $this->generateEXCEL($request->all());
        }
    }

    public function generateEXCEL($details)
    {
        return Excel::download(new ExportAoCollectionSummary($details), 'area_office_collection_sumary.xlsx');
    }
    public function generatePDF($details)
    {
        $milkPurchaseRecords = '';
        $areaOffice = AreaOffice::find($details['area_office_id']);
        $fromDate = Carbon::createFromFormat('Y-m-d', $details['from_date'])->startOfDay();
        $toDate = Carbon::createFromFormat('Y-m-d', $details['to_date'])->endOfDay();
        $cpName = 'All';

        if (isset($details['collection_point_id'])) {
            $colps = CollectionPoint::find($details['collection_point_id']);
            $cpName = $colps->name;

            if ($colps->is_mcc == "1") {
                $milkPurchaseRecords = MilkPurchase::where('mcc_id', $details['collection_point_id'])->whereBetween('created_at', [$fromDate, $toDate])->get();
            } else {
                $milkPurchaseRecords = MilkPurchase::where('cp_id', $details['collection_point_id'])->whereBetween('created_at', [$fromDate, $toDate])->get();
            }
        } else {
            $collectionPointIds = CollectionPoint::where('area_office_id', $details['area_office_id'])->pluck('_id')->toArray();
            $milkPurchaseRecords =  MilkPurchase::where('area_office_id', $details['area_office_id'])
                ->orWhereIn('cp_id', $collectionPointIds)
                ->orWhereIn('mcc_id', $collectionPointIds)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->get();
        }
        $totalGross = $milkPurchaseRecords->sum('gross_volume');
        $totalTS = $milkPurchaseRecords->sum('ts_volume');
        $dateFrom = $details['from_date'];
        $dateTo = $details['to_date'];

        $pdf = PDF::loadView('content/reports/aopdf', compact('milkPurchaseRecords', 'areaOffice', 'cpName', 'totalGross', 'totalTS', 'dateFrom', 'dateTo'));
        $pdf->setPaper('A3', 'portrait');
        return $pdf->download('area_office_collection_sumary.pdf');
    }
    //fresh milk purchase report summary functions 
    public function freshMilkpurchaseSummary(Request $request)
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
            $table->editColumn('fat', function ($row) {
                $tests = collect($row->tests);
                $fat = $tests->where('qa_test_name', 'Fat')->pluck('value')->first();
                return $fat;
            });
            $table->editColumn('snf', function ($row) {
                $tests = collect($row->tests);
                $snf = $tests->where('qa_test_name', 'SNF')->pluck('value')->first();
                return $snf;
            });
            $table->editColumn('lr', function ($row) {
                $tests = collect($row->tests);
                $lr = $tests->where('qa_test_name', 'LR')->pluck('value')->first();
                return $lr;
            });
            $table->editColumn('serial_number', function ($row) {
                return ($row->serial_number) ? 'MPR-' . $row->serial_number : '';
            })
                ->addIndexColumn()->addColumn('supplier', function ($row) {
                    return ($row->supplier) ? $row->supplier->name : 'N/A';
                })
                ->addIndexColumn()->addColumn('supplier_code', function ($row) {

                    return ($row->supplier) ?  $row->supplier->code : 'N/A';
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
                ->filter(function ($instance) use ($request) {
                    if ($request->filled('collection_point')) {
                        $instance->where('mcc_id', $request->collection_point);
                    }
                    if ($request->filled('to_date')) {
                        $fromDate = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
                        $toDate = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
                        $instance->whereBetween('created_at', [$fromDate, $toDate]);
                    }
                })
                ->rawColumns(['mcc', 'supplier', 'action', 'type', 'supplier_code', 'fat', 'snf', 'lr']);
            return $table->toJson();
        }
        $areaOffices = AreaOffice::get();
        return view('content.reports.fresh-milk-purchase-summary', compact('areaOffices'));
    }
    public function freshMilkexportReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_office_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required|after_or_equal:from_date',
            'export_to' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->export_to == 'PDF') {
            return $this->generatePDF($request->all());
        } else {
            return $this->generateEXCELfreshMilkreport($request->all());
        }
    }
    public function generateEXCELfreshMilkreport($details)
    {
        return Excel::download(new ExportFreshMilkPurchaseSummary($details), 'fresh_milk_purchase_summary.xlsx');
    }
}
