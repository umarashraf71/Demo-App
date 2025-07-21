@extends('layouts/contentLayoutMaster')

@section('title', 'QA Lab Tests')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/fixedHeader.bootstrap5.min.css') }}">
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
                        <h4 class="card-title">QA Lab Tests</h4>
                        @can('Create QaLabTest')
                            <a class="add-new-btn btn btn-primary mt-2 mr_30px" href="{{ route('qa-labtest.create') }}">Add
                                New</a>
                        @endcan
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="demo-spacing-0 m-2">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <div class="alert-body">{{ $message }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                    @endif
                    <div class="card-datatable table-responsive">
                        <table class="table" id="qa_labtest_datatable">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>QA Test Name</th>
                                    <th>Description</th>
                                    <th>Data Type</th>
                                    <th>Test UOM</th>
                                    <th>Test Applied</th>
                                    <th>Exceptional</th>
                                    <th>Action</th>
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
    <script src="{{ asset('vendors/js/tables/datatable/fixedHeader.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script>
        // permissions checks variables
        editData = "@php echo Auth::user()->can('Edit QaLabTest') @endphp";
        deleteData = "@php echo Auth::user()->can('Delete QaLabTest') @endphp";

        if (editData == '' && deleteData == '') {
            showActioncolumn = false;
        } else {
            showActioncolumn = true;
        }

        var table = $('#qa_labtest_datatable').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: true,
            ajax: "{{ route('qa-labtest.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'qa_test_name',
                    name: 'qa_test_name'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'test_data_type',
                    name: 'test_data_type'
                },
                {
                    data: 'measurementunit_id',
                    name: 'measurementunit_id'
                },
                {
                    data: 'apply_test',
                    name: 'apply_test'
                },
                {
                    data: 'exceptional_release',
                    name: 'exceptional_release'
                },
                {
                    data: 'action'
                }
            ],
            columnDefs: [{
                    // Actions
                    targets: 7,
                    title: 'Actions',
                    orderable: false,
                    sortable: false,
                    visible: showActioncolumn
                },
                {
                    targets: 5,
                    data: "apply_test",
                    render: function(data, type, row, meta) {
                        let apply_test = [];
                        if (data.indexOf(1) !== -1)
                            apply_test.push('MCC');
                        if (data.indexOf(2) !== -1)
                            apply_test.push('MMT');
                        if (data.indexOf(3) !== -1)
                            apply_test.push('Area Lab');
                        if (data.indexOf(4) !== -1)
                            apply_test.push('Plant Lab');

                        return apply_test.join(',')
                    }
                }, {
                    targets: 3,
                    data: "test_data_type",
                    render: function(data, type, row, meta) {
                        if (data == 1)
                            return 'Range';
                        if (data == 2)
                            return 'Positive/Negative';
                        if (data == 3)
                            return 'Yes/No';
                        if (data == 4)
                            return 'Intact/Broken';
                    }
                },
                {
                    targets: 6,
                    data: "exceptional_release",
                    render: function(data, type, row, meta) {
                        if (data == 1)
                            return 'Yes';
                        else
                            return 'No';
                    }
                }
            ],
            // order: [[1, 'asc']],
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
