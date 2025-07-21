@extends('layouts/contentLayoutMaster')

@section('title', 'Dispatch Details')

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
                        <h4 class="card-title">Dispatches</h4>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>Serial Number</th>
                                    <td>MD-{{ $dispatch->serial_number }}</td>
                                    <th>Area Office</th>
                                    <td>
                                        @if($dispatch->type == 'mmt_dispatch_plant')
                                            {{ $dispatch->mmt->areaOffice ? $dispatch->mmt->areaOffice->ao_name : 'N/A' }}                                        
                                        @else
                                            {{ $dispatch->areaOffice ? $dispatch->areaOffice->ao_name : 'N/A' }}
                                        @endif

                                    </td>
                                </tr>
                                <tr>
                                    <th>Gross Volume</th>
                                    <td>{{ $dispatch->gross_volume }}</td>
                                    <th>TS Volume</th>
                                    <td>{{ $dispatch->volume_ts }}</td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>{{ $dispatch->user->name }}</td>
                                    <th>Time</th>
                                    <td>
                                        {{ $dispatch->time }}
                                    </td>
                                </tr>
                                @if ($dispatch->type == 'mmt_dispatch_plant')
                                    <tr>
                                        <th>Route Name</th>
                                        <td>
                                            {{ $dispatch->route->name }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Vehicle</th>
                                    <td>
                                        {{ $dispatch->routeVehicle->vehicle_number }}
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Tests</h4>
                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="table" id="test_table">
                            <thead>
                                <tr>
                                    <th>QA Test Name</th>
                                    <th>Test Data Type</th>
                                    <th>Test Type</th>
                                    <th>Unit of Measure</th>
                                    <th>Result</th>
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
    <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script>
        var tests = '<?php echo json_encode($dispatch->tests); ?>';
        $('#test_table').DataTable({
            processing: true,
            data: JSON.parse(tests),
            columns: [{
                    data: 'qa_test_name'
                },
                {
                    data: 'test_data_type'
                },
                {
                    data: 'test_type'
                },
                {
                    data: 'unit_of_measure'
                },
                {
                    data: 'value'
                }
            ],
            columnDefs: [{
                targets: 4,
                data: "value",
                render: function(data, type, row, meta) {
                    if (row['test_data_type'] == 'Yes/No' && row['value'] == 1)
                        return 'Yes';
                    else if (row['test_data_type'] == 'Yes/No' && row['value'] == 0)
                        return 'No';
                    else if (row['test_data_type'] == 'Positive/Negative' && row['value'] == 1)
                        return '+ve';
                    else if (row['test_data_type'] == 'Positive/Negative' && row['value'] == 0)
                        return '-ve';
                    else
                        return row['value'];
                }
            }],
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
    </script>
@endsection
