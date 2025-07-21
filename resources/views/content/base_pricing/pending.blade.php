
@extends('layouts/contentLayoutMaster')

@section('title', ' Pending Prices List')

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
                    <div class="row border-bottom py-2">
                        <div class="col-3">
                            <h4 class="card-title pt-1 my-auto">Pending Prices</h4>
                        </div>
                        <div class="col-1 offset-8">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tabs">
                            @include('content._partials._sections.base_price_tabs')
                        </div>

{{--                        <div class="filters mb-1">--}}
{{--                            @include('content._partials._sections.base_price_filter')--}}
{{--                        </div>--}}
                        <div class="card-datatable table-responsive">
                            <table class="table" id="pending_table">
                                <thead class="table-light">
                                <tr>
                                    <th>Id</th>
                                    <th>Request Type</th>
                                    <th>No. of Prices</th>
                                    {{--            <th>Approved/Rejected BY</th>--}}
                                    <th>Created Date</th>
                                    {{--          <th>Actions</th>--}}
                                </tr>
                                </thead>
                            </table>
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
    <script src="{{ asset('/js/custom.js')}}"></script>
    <script src="{{ asset('/js/repeater.js')}}"></script>

    <script>
        // permissions checks variables
        {{--editData = "@php echo Auth::user()->can('Edit Source Type Pricing') @endphp";--}}
        {{--deleteData = "@php echo Auth::user()->can('Delete Source Type Pricing') @endphp";--}}

        // if(editData == '' && deleteData=='')
        // {
        //     showActioncolumn = false;
        // }
        // else
        // {
        //     showActioncolumn = true;
        // }

        $('#pending_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{route('price.pending')}}', // JSON file to add data
            columns: [
                // columns according to JSON
                {  data: 'code' },
                {  data: 'request_type' },
                {  data: 'count' },

                { data: 'created_at' },
                // { data: 'action' }
            ],
            // columnDefs: [
            //   {
            //     // Actions
            //     targets:8,
            //     title: 'Actions',
            //       orderable: false,
            //       sortable: false,
            //     // visible:showActioncolumn
            //   }
            // ],
            // order: [[1, 'asc']],
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
