
@extends('layouts/contentLayoutMaster')

@section('title', 'Area office Lab Receptions')

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
          <h4 class="card-title">At Area office Lab(MMT Receptions)</h4>
        </div>
          <form action="">
          <div class="row mt-2">
              <div class="col-3 offset-4">
                      <select name="cp" data-placeholder="Collection point" class="form-control select2" id="cp_search" onchange="getDropdownData('area_office_search',this.value)">
                          <option value="" selected="" disabled="">MMT</option>

                      </select>
              </div>
              <div class="col-1">
                 <button disabled class="btn btn-primary">Search</button>
             </div>
          </div>
          </form>
        <div class="card-datatable table-responsive">
          <table class="table" id="collection_point_datatable">
            <thead>
              <tr>
                  <th>Sr. No.</th>
                  <th>MMT</th>
                  <th>Opening Balance</th>
                  <th>Volume</th>
                  <th>Left Over</th>
                  <th>Gain/Loss</th>
                  <th>Date</th>
              </tr>
            </thead>
              <tfoot>
              <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
              </tr>
              </tfoot>
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
<script>

var table = $('#collection_point_datatable').DataTable({
    processing: true,
    serverSide: true,
    columns: [
    {data: 'serial_number', name: 'serial_number'},
    {data: 'name', name: 'name'},
    {data: 'opening_balance', name: 'opening_balance'},
    {data: 'gross_volume', name: 'gross_volume'},
    {data: 'left_over_milk', name: 'left_over_milk'},
    {data: 'gain_loss', name: 'gain_loss'},
    {data: 'date', name: 'date'},
    ],
    ajax: {
        url: "{{ route('get.purchase.receptions.ao')}}",
        data: function (d) {
            d.cp_search = $('#cp_search').val();
        }
    },

    "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;

        // var intVal = function ( i ) {
        //     return typeof i === 'string' ?
        //         i.replace(/[\$,]/g, '')*1 :
        //         typeof i === 'number' ?
        //             i : 0;
        // };
        //
        // // computing column Total of the complete result
        let get = 0;
        let  left_over=  0;
        let   gain_loss  = 0;
        let  gross_volume = 0;
        data.forEach(function(item) {
            // get += item.opening_balance;
            gain_loss += item.gain_loss;
            gross_volume += item.gross_volume;
            left_over += item.left_over_milk;
        });
        // $( api.column( 2 ).footer() ).html(get);
        $( api.column( 3 ).footer() ).html(gross_volume);
        $( api.column( 4 ).footer() ).html(left_over);
        $( api.column( 5 ).footer() ).html(gain_loss);
    },
    dom:
    '<"d-flex justify-content-between align-items-center header-actions text-nowrap mx-1 row mt-75"' +
    '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" l>' +
    '<"col-sm-12 col-lg-8"<"dt-action-buttons d-flex align-items-center justify-content-lg-end justify-content-center flex-md-nowrap flex-wrap"<"me-1"f>>>' +
    '><"text-nowrap" t>' +
    '<"d-flex justify-content-between mx-2 row mb-1"' +
    '<"col-sm-12 col-md-6"i>' +
    '<"col-sm-12 col-md-6"p>' +
    '>',
    language: {
    paginate: {
        // remove previous & next text from pagination
        previous: '&nbsp;',
        next: '&nbsp;'
    }
    }
});

$("#cp_search").change(function(){
    table.draw();
});
</script>
@endsection
