<?php

namespace App\Http\Controllers;

use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use Illuminate\Http\Request;
use App\Traits\PaymentCalculationTrait;
use App\Models\PaymentProcess;
use App\Models\Payment;
use App\Models\MilkPurchase;
use URL;
use Response;


class PaymentProcessController extends Controller
{
    use PaymentCalculationTrait;
    public function __construct()
    {
        $this->middleware(['permission:Payment Process View'], ['only' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $table = datatables(PaymentProcess::get(['from', 'to', 'status']));
            $table->addColumn('action', function (PaymentProcess $row) {
                $btn = '';
                $btn .= '<a title="View" href="' . route('payment-calculation.show', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                if ($row->status == 0) {
                    $btn .= '<a title="View" href="' . route('payment.calculation.approve', $row->id) . '" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg></a>';
                    $btn .= '<button  title="Delete" class="btn btn-icon btn-danger" onclick="DeleteRecord(\'' . URL::to('payment-calculation/' . $row->id . '') . '\',\'payment_process\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-delete"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg></button>';
                }
                return $btn;
            });
            $table->editColumn('status', function ($row) {
                if ($row->status == 0)
                    return '<span class="badge badge-glow bg-warning">Created</span>';
                else
                    return '<span class="badge badge-glow bg-success">Approved</span>';
            })
                ->rawColumns(['action', 'status']);
            return $table->toJson();
        }
        return view('content.payment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payments = Payment::select('supplier_id', 'total_ts_volume', 'payable','incentives','payable_without_incentives')->with(array('supplier' => function ($q) {

            $q->select('name', 'code', 'mcc', 'cp_ids', 'area_office', 'supplier_type_id')->with(array('supplier_type' => function ($j) {

                $j->select('name');
            }));
        }))
            ->where('payment_process_id', $id)->get()->toArray();

        foreach ($payments as $key => $payment) {

            $payments[$key]['supplier_code'] = $payment['supplier']['code'];
            $payments[$key]['supplier_id'] = $payment['supplier']['name'];
            $payments[$key]['supplier_type'] = $payment['supplier']['supplier_type']['name'];
            //volume incentive
            $payments[$key]['volume_incentive_rate'] = $payment['incentives']['volume']['volume_incentive_rate'];
            $payments[$key]['total_volume_incentive'] = $payment['incentives']['volume']['total_volume_incentive'];
            //chilling incentive
            $payments[$key]['chilling_incentive_rate'] = $payment['incentives']['chilling']['chilling_incentive_rate'];
            $payments[$key]['total_chilling_incentive'] = $payment['incentives']['chilling']['total_chilling_incentive'];

            $cp_ids = array_key_exists('cp_ids', $payment['supplier']) ? $payment['supplier']['cp_ids'] : null;
            $mcc =  array_key_exists('mcc', $payment['supplier']) ? $payment['supplier']['mcc'] : null;
            $area_office =  array_key_exists('area_office', $payment['supplier']) ? $payment['supplier']['area_office'] : null;

            if ($area_office <> null) {
                $ao = AreaOffice::where('_id', $area_office)->first();

                $payments[$key]['area_office'] = $ao ? $ao->ao_name : $area_office;
            } else if ((!$area_office || $area_office == null) && $cp_ids == null && $mcc <> null) {
                $mccId = $mcc[0];
                $mcc = CollectionPoint::where('_id', $mccId)->first();
                $payments[$key]['area_office'] = $mcc->area_office->ao_name;
            } else if ((!$area_office || $area_office == null) && $mcc == null && $cp_ids <> null) {
                $cpId = $cp_ids[0];
                $cp = CollectionPoint::where('_id', $cpId)->first();
                $payments[$key]['area_office'] = $cp->area_office->ao_name;
            } else {
                $payments[$key]['area_office'] = "N/A";
            }

            unset($payments[$key]['supplier']);
        }
        $data['payments'] = $payments;
        return view('content.payment.payment_details', $data);
    }
    public function showPurchases(Request $request, $id)
    {
        if ($request->ajax()) {
            $payment = Payment::find($id);
            $purchaseCollection = collect($payment->purchases);
            $table = datatables(MilkPurchase::whereIn('_id', $purchaseCollection->pluck('id')->toArray())->with('mcc', 'supplier', 'cp')->orderBy('_id', 'desc'));

            $table->addColumn('mcc', function ($row) {
                return ($row->mcc) ? $row->mcc->name : ($row->cp ? $row->cp->name : 'N/A');
            });
            $table->addColumn('level', function ($row) use ($purchaseCollection) {
                $level = $purchaseCollection->where('id', $row->id)->pluck('level')->first();
                return $level;
            });
            $table->addColumn('base_price', function ($row) use ($purchaseCollection) {
                $base_price = $purchaseCollection->where('id', $row->id)->pluck('base_price')->first();
                return $base_price;
            });
            $table->addColumn('ao', function ($row) {
                if ($row->ao <> null)
                    return $row->ao->name;
                elseif ($row->mcc <> null)
                    return $row->mcc->area_office->name;
                elseif ($row->cp <> null)
                    return $row->cp->area_office->name;
                else
                    return "N/A";
            });
            $table->editColumn('serial_number', function ($row) {
                return ($row->serial_number) ? 'MPR-' . $row->serial_number : '';
            })
                ->addIndexColumn()->addColumn('supplier', function ($row) {
                    return ($row->supplier) ? $row->supplier->name : 'N/A';
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
                ->rawColumns(['mcc', 'supplier', 'action', 'type']);
            return $table->toJson();
        }
        return view('content.payment.payment_purchases_details');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Payment::where('payment_process_id', $id)->delete();
        PaymentProcess::where('_id',$id)->delete();
        return Response::json([
            'success' => true,
            'message' => 'Record deleted'
        ]);
    }

    public function paymentCalculationapprove($id)
    {
        $res = PaymentProcess::where('_id', $id)->update(['status' => (int) 1]);
        if ($res)
            return
                redirect()
                ->back()
                ->with('success', 'Payment calculation is approved');
        else
            return
                redirect()
                ->back()
                ->with('error', 'Something went wrong!');
    }

    public function paymentProcess(Request $request)
    {
        ini_set('max_execution_time', 360000);
        if (paymentProcess::where('status', 0)->exists())
            return
                redirect()
                ->back()
                ->with('error', 'Can not run payment calculation due to previous calculation is in created status');
        $payments = $this->Paymentcalculation($request->from, $request->to);
        if (gettype($payments) == 'string')
            return
                redirect()
                ->back()
                ->with('error', $payments);
        $paymentProcess = PaymentProcess::create(
            [
                'from' => $request->from,
                'to' => $request->to,
                'status' => (int) 0, //0=created 1=approved
            ]
        );
        foreach ($payments as $key => $payment) {
            $payments[$key]['payment_process_id'] = $paymentProcess->id;
        }
        Payment::insert($payments);
        return
            redirect()
            ->back()
            ->with('success', 'Payments are successfully generated');
    }
}
