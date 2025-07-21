@extends('layouts/contentLayoutMaster')

@section('title', 'Purchases')

@section('vendor-style')

    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
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
                        <h4 class="card-title">Purchases</h4>
                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="table" id="collection_point_datatable">
                            <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Type</th>
                                    <th>Collection Point</th>
                                    <th>Area Office</th>
                                    <th>Supplier</th>
                                    <th>Gross volume</th>
                                    <th>Ts Volume</th>
                                    <th>Opening Balance</th>
                                    <th>Time</th>
                                    <th>Level</th>
                                    <th>Base Price</th>
                                </tr>
                            </thead>

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
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script>
        url = "@php echo route('payment.details',request()->segment(count(request()->segments()))); @endphp"

        var table;
        $(document).ready(function() {
            table = $('#collection_point_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: url,
                },
                columns: [{
                        data: 'serial_number',
                        name: 'serial_number'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'mcc',
                        name: 'mcc'
                    },
                    {
                        data: 'ao',
                        name: 'ao'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'gross_volume',
                        name: 'gross_volume'
                    },
                    {
                        data: 'ts_volume',
                        name: 'ts_volume'
                    },
                    {
                        data: 'opening_balance',
                        name: 'opening_balance'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'level',
                        name: 'level'
                    },
                    {
                        data: 'base_price',
                        name: 'base_price'
                    }
                ],
                dom: '<"d-flex justify-content-between align-items-center header-actions text-nowrap mx-1 row mt-75"' +
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
            $(".search-input").on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
