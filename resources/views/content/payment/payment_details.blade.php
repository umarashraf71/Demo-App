@extends('layouts/contentLayoutMaster')

@section('title', 'Payment Details')

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
                        <h4 class="card-title">
                                Payment Calculation Details
                        </h4>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="table" id="payments">
                            <thead>
                                <tr>
                                    <th>Supplier Code</th>
                                    <th>Supplier</th>
                                    <th>Supplier Type</th>
                                    <th>Area Office</th>
                                    <th>Total TS Volume</th>
                                    <th>Payable Without Incentives</th>
                                    <th>Payable</th>
                                    <th>Volume Incentive Rate</th>
                                    <th>Total Volume Incentive</th>
                                    <th>Chilling Incentive Rate</th>
                                    <th>Total Chilling Incentive</th>
                                    <th>Actions</th>
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
        var payments = '<?php echo json_encode($payments,JSON_HEX_APOS); ?>';
        baseUrl = '<?php URL::to("/"); ?>';
        $('#payments').DataTable({
            processing: true,
            data: JSON.parse(payments),
            columns: [
                {
                    data: 'supplier_code'
                },
                {
                    data: 'supplier_id'
                },
                {
                    data: 'supplier_type'
                },
                {
                    data: 'area_office'
                },
                {
                    data: 'total_ts_volume'
                },
                {
                    data: 'payable_without_incentives'
                },
                {
                    data: 'payable'
                },
                {
                    data: 'volume_incentive_rate'
                },
                {
                    data: 'total_volume_incentive'
                },
                {
                    data: 'chilling_incentive_rate'
                },
                {
                    data: 'total_chilling_incentive'
                },
                {
                    data: '_id'
                }
            ],
            columnDefs: [{
                    // Actions
                    targets: 11,
                    title: 'Actions',
                    orderable: false,
                    sortable: false,
                    visible: true
                },
                {
                    targets: 11,
                    data: "_id",
                    render: function(data, type, row, meta) {
                        return '<a title="View" href="'+baseUrl+'/payment-details/'+row['_id']+'" target="_blank" class="btn btn-icon btn-primary mr_5px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
                    }
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
    </script>
@endsection
