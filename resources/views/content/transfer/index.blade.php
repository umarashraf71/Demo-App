
@extends('layouts.contentLayoutMaster')

@section('title', ' Transfer Detail')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
@endsection
@section('content')

    <!-- Column Search -->
    <section id="pricing_div">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row border-bottom py-2 m-1">
                        <div class="col-2">
                            <h4 class="card-title pt-1 my-auto">Transfers
                        </h4>
                        </div>

                    </div>

                    <div class="card-body ">
                        <div class="card-datatable table-responsive">
                            <table class="table" >
                                <thead>
                                <tr>
                                    <th>SR.No.</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Type</th>
                                    <th>Volume To Send</th>
                                    <th>Volume Sent</th>
                                    <th>From Deducted</th>
                                    <th>To Added</th>
                                    <th>Opening balance</th>
                                    <th>Opening balance1</th>
                                    <th>Gain/loss</th>
                                    <th>Status</th>
                                    <th style="min-width: 250px">Note</th>
                                </tr>
                                </thead>
                                <tbody>
                                    
                                @foreach($mts as $mt)
                                    @php
                                     $deduction = ($mt->volume_received*-1)+$mt->gain_loss
                                    @endphp
                                    <tr>
                                        <td>{{$mt->serial_number}}</td>
                                        <td>{{$mt->type=='mcc' && $mt->fromCp?$mt->fromCp->name:($mt->type=='ao' && $mt->fromAo?$mt->fromAo->name:'')}}</td>
                                        <td>{{$mt->type=='mcc' && $mt->toCp?$mt->toCp->name:($mt->type=='ao' && $mt->toAo?$mt->toAo->name:'')}}</td>
                                        <td>{{$mt->type=='mcc'?'Collection Point':($mt->type=='ao'?'Area Office':'MMT to Area Office')}}</td>
                                        <td>{{$mt->volume}}</td>
                                        <td>{{($mt->status!=0)?$mt->volume_received:''}}</td>
                                        <td>{{($mt->status!=0)?$deduction:''}}</td>
                                        <td>{{($mt->status!=0)?+$mt->volume_received:''}}</td>
                                        <td>{{($mt->status!=0)?$mt->opening_balance_from:''}}</td>
                                        <td>{{($mt->status!=0)?$mt->opening_balance_to:''}}</td>
                                        <td>{{($mt->status!=0)?$mt->gain_loss:''}}</td>
                                        <td>{{$mt->status==0?'Approved':($mt->status==3?'Rejected':($mt->status==1?'Transferred':'Pending'))}}</td>
                                        <td>{{$mt->note}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="remarksModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                    <div class=" mb-1">
                        <h1 class="mb-1">Remarks</h1>
                    </div>
                    <div class="table-responsive">
                        <table class="table " id="remarks_table">
                            <thead><tr><td>#</td><td>From</td><td>Remark</td><td>Time</td></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    {{-- vendor files table data --}}
    <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js')}}"></script>

    <script>




        function  getRemarks(code){
            $('#remarksModal').modal('show')
            $.ajax({
                type: "get",
                dataType: 'JSON',
                url: '{{route('get.remarks.ajax')}}',
                data:{code:code},
                method:'get',
                success: function (response) {
                    if (response.success == true) {
                        $('#remarks_table > tbody').html('')
                        $('#remarks_table > tbody').html(response.data)
                    } else {
                        $('#remarks_table > tbody').html('<tr class="text-center"><td colspan="4">No data found</td></tr>')
                    }
                }
            });
        }



    </script>
@endsection
