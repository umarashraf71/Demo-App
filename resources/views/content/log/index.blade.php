@extends('layouts/contentLayoutMaster')

@section('title', 'Tehsils List')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">

@endsection

@section('content')
    <!-- Column Search -->
    <section id="column-search-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Logs</h4>

                    </div>

                    <div class="card-datatable">
                        <table class="table" id="logs_table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>Model</th>
                                    <th>Activity Performed By</th>
                                    <th>Record</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div style="float:right; margin-top:4px;">
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
      
$('#logs_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('logs.index') }}",
    columns: [
    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
    {data: 'action', name: 'action'},
    {data: 'module', name: 'module'},
    {data: 'model_name', name: 'model_name'},

    {data: 'created_by', name: 'created_by'},
    {data: 'record', name: 'record'},
    ],
   
    order: [[1, 'asc']],
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

    </script>
@endsection
