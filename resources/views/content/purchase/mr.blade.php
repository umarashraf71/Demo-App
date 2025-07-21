@extends('layouts/contentLayoutMaster')

@section('title', 'Mobile Report')

@section('vendor-style')
{{-- vendor css files --}}
<link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
@endsection
@section('content')

<!-- Column Search -->
<section id="column-search-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Mobile Report</h4>

        </div>
          <div class="row p-1">
              <div class="col-2 pb-1">MMT</div>
              <div class="col-1 pb-1">{{$mr->mmt?$mr->mmt->name:''}}</div>
              <div class="col-2 pb-1">Duration</div>
              <div class="col-7 pb-1">{{date("d-m-Y H:i a", $mr->from_time)}} to {{ date("d-m-Y H:i a", $mr->to_time)}}</div>
              <div class=" col-2 pb-1">Opening balance</div>
              <div class="col-1 pb-1">{{$mr->opening_balance}}</div>
              <div class="col-2 pb-1">Closing Balance</div>
              <div class="col-7 pb-1">{{$mr->left_over_milk}}</div>
              <div class="col-2 pb-1">Gain / Loss</div>
              <div class="col-7 pb-1">{{$mr->gain_loss}}</div>
          </div>
        <div class="card-datatable ">
            <div class="row m-1">
                <div class="col-12 ">
                 <h4 class="">At MCCS</h4>
                    <table class="table" >
                        <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Collection Center</th>
                            <th>Gross volume</th>
                            <th>Ts Volume</th>
                            <th>Time</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($milk_purchases as $mpr)
                            <tr>
                                <td>MPS-{{$mpr->serial_number}}</td>
                                <td>{{$mpr->mcc?$mpr->mcc->name:''}}</td>
                                <td>{{$mpr->gross_volume}}</td>
                                <td>{{$mpr->ts_volume}}</td>
                                <td>{{$mpr->created_at}}</td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No data found</td>

                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row m-1">
                <div class="col-12">
                 <h4>At CP's</h4>
                    <table class="table" >
                        <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Collection Point</th>
                            <th>Gross volume</th>
                            <th>Ts Volume</th>
                            <th>Time</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($milk_purchase_at_cps as $mpr)
                            <tr>
                                <td>MPR-{{$mpr->serial_number}}</td>
                                <td>{{$mpr->cp?$mpr->cp->name:''}}</td>
                                <td>{{$mpr->gross_volume}}</td>
                                <td>{{$mpr->ts_volume}}</td>
                                <td>{{$mpr->created_at}}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No data found</td>

                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>


        </div>
      </div>
    </div>
  </div>
</section>
<!--/ Column Search -->
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
<script src="{{ asset('/js/custom.js') }}"></script>
@endsection
