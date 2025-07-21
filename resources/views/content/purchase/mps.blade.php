
@extends('layouts/contentLayoutMaster')

@section('title', 'Purchases')

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
          <h4 class="card-title">Milk Purchase Sheet</h4>

        </div>
          <div class="row p-1">
              <div class="col-2 pb-1">MMT</div>
              <div class="col-1 pb-1">{{$mr->mmt?$mr->mmt->name:''}}</div>
              <div class="col-2 pb-1">Duration</div>
              <div class="col-7 pb-1">{{ date("d-m-Y H:i a", $mr->from_time)}} to {{ date("d-m-Y H:i a", $mr->to_time)}}</div>
              <div class=" col-2 pb-1">Opening balance</div>
              <div class="col-1 pb-1">{{$mr->opening_balance}}</div>
              <div class="col-2 pb-1">Closing Balance</div>
              <div class="col-7 pb-1">{{$mr->left_over_milk}}</div>
              <div class="col-2 pb-1">Gain / Loss</div>
              <div class="col-7 pb-1">{{$mr->gain_loss}}</div>
          </div>
        <div class="card-datatable table-responsive">
          <table class="table" id="collection_point_datatable">
            <thead>
              <tr>
                  <th>Sr. No.</th>
                  <th>Supplier</th>
                  <th>Gross volume</th>
                  <th>Ts Volume</th>
                  <th>Time</th>
              </tr>
            </thead>
              <tbody>
              @foreach($milk_purchases as $mpr)
              <tr>
                  <td>MPR-{{$mpr->serial_number}}</td>
                  <td>{{$mpr->supplier?$mpr->supplier->name:''}}</td>
                  <td>{{$mpr->gross_volume}}</td>
                  <td>{{$mpr->ts_volume}}</td>
                  <td>{{$mpr->created_at}}</td>
              </tr>
              @endforeach
              </tbody>
          </table>
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
