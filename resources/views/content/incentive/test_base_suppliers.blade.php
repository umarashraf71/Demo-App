@extends('layouts/contentLayoutMaster')

@section('title', 'Test Supplier Incentives List')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

@endsection


@section('content')
    <style>
        #supplier_incentives_filter {
            display: none;
        }

        .ui-state-hover,
        .ui-widget-content .ui-state-hover,
        .ui-widget-header .ui-state-hover,
        .ui-state-focus,
        .ui-widget-content .ui-state-focus,
        .ui-widget-header .ui-state-focus,
        .ui-button:hover,
        .ui-button:focus {
            max-height: 26px !important;
        }

        .ui-state-default:hover {
            max-height: 26px !important;
        }

        .ui-state-default {
            max-height: 26px !important;
        }

        .ui-slider-range {
            max-height: 18px !important;
        }

        #slider-range {
            max-height: 18px !important;
        }
    </style>
    <!-- Column Search -->
    <section id="column-search-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Test Base Supplier Incentives</h4>
                        @can('Create Test Based Supplier Incentives')
                            <a class="add-new-btn btn btn-primary mt-2 mr_30px" onclick="addForm()" href="#"
                                data-bs-toggle="modal" data-bs-target="#addModal">Add</a>
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
                    <div class="row mt-2">
                        <div class="col-4 offset-2">
                            <label class="form-label">Supplier</label>
                            <div style="position: relative">
                                <select name="supplier" id="search_by_supplier" class="form-control select2">
                                    <option value="" selected disabled>Supplier</option>
                                    @foreach ($suppliers as $data)
                                        <option value="{{ $data->id }}">{{ ucfirst($data->name) }}</option>
                                    @endforeach
                                </select>
                                <i data-feather="x-circle" class="cursor-pointer dropown-reset-icon d-none"
                                    onclick="reset_dropdown('search_by_supplier')"></i>
                            </div>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Incentive Type</label>
                            <div style="position: relative">
                                <select name="incentive_type" class="incentive_type form-control select2"
                                    id="search_by_inc">
                                    <option value="" selected disabled>Incentive Type</option>
                                    @foreach ($incentiveTypes as $incentiveType)
                                        <option value="{{ $incentiveType->id }}">{{ ucfirst($incentiveType->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                <i data-feather="x-circle" class="cursor-pointer dropown-reset-icon d-none"
                                    onclick="reset_dropdown('search_by_inc')"></i>

                            </div>
                        </div>
                        <div class="card-datatable table-responsive">
                            <table class="table" id="supplier_incentives">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Supplier</th>
                                        <th>Incentive Type</th>
                                        <th>QA Test</th>
                                        <th>Value</th>
                                        <th>Rate</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                    <div class="text-center mb-2">
                        <h1 class="mb-1"><span class="form_title">Add</span> Test Based Incentive</h1>
                        {{--               <p>against collection points and source types.</p> --}}
                    </div>
                    <form method="POST" class="row" id="form">
                        <input type="hidden" name="test_id" id="test_id">
                        @csrf

                        <div class="col-12">
                            <label class="form-label" for="supplier">Suppliers</label>
                            <select name="supplier" id="supplier" onchange="getincentiveType()" class="select2 form-select"
                                required>
                                <option value="" selected disabled>Select Supplier</option>
                                @foreach ($suppliers as $data)
                                    <option {{ old('supplier') == $data->id ? 'selected' : '' }}
                                        data-type="{{ $data->supplier_type_id }}" value="{{ $data->id }}">
                                        {{ ucfirst($data->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="incentive_type">Incentive Type</label>
                            <select name="incentive_type" id="incentive_type" class="select2 form-select"
                                onchange="setPassingValue()" required>
                                <option value="" selected disabled>Select Incentive Type</option>
                                {{--                            @foreach ($test_base_incentive_types as $incentiveType) --}}
                                {{--                                <option {{old('incentive_type')==$incentiveType->id?'selected':''}}  value="{{$incentiveType->id}}">{{ucfirst($incentiveType->name)}}</option> --}}
                                {{--                            @endforeach --}}
                            </select>
                            <span class="text-danger"></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="value">Value <small class="rang text-danger"></small></label>
                            <input required type="number" id="value" min="0" step="1" max="0"
                                value="{{ old('passing_value') }}" class="form-control" name="passing_value"
                                placeholder="Passing Value">
                            <span class="text-danger"></span>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="modalPermissionName">From</label>
                            <input id="from" type="date" min="{{ date('Y-m-d') }}" value=""
                                class="form-control" name="from" placeholder="From" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="To">To</label>
                            <input id="to" type="date" min="{{ date('Y-m-d') }}" value=""
                                class="form-control" name="to" placeholder="To" required>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary mt-2 me-1">Save</button>
                            <button type="reset" class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal"
                                aria-label="Close">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                    <div class="text-center mb-2">
                        <h1 class="mb-1">Update Test Based Incentive</h1>
                        {{--               <p>against collection points and source types.</p> --}}
                    </div>
                    <form method="POST" class="row" id="updateForm">
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="test_id" id="test_idd">
                        @csrf

                        <div class="col-12">
                            <label class="form-label" for="supplier">Suppliers</label>
                            <select name="supplier" id="supplierr" onchange="getincentiveTypee()"
                                class="select2 form-select" required>
                                <option value="" selected disabled>Select Supplier</option>
                                @foreach ($suppliers as $data)
                                    <option data-type="{{ $data->supplier_type_id }}" value="{{ $data->id }}">
                                        {{ ucfirst($data->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="incentive_typee">Incentive Type</label>
                            <select name="incentive_type" id="incentive_typee" class="select2 form-select"
                                onchange="setPassingValuee()" required>
                                <option value="" selected disabled>Select Incentive Type</option>
                                {{--                             {!! $incentive_type_options !!} --}}
                            </select>

                        </div>
                        <div class="col-12">
                            <label class="form-label" for="valuee">Value <small
                                    class="rangg text-danger"></small></label>
                            <input required type="number" id="valuee" min="0" step="1" max="0"
                                class="form-control" name="passing_value" placeholder="Passing Value">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="modalPermissionName">From</label>
                            <input id="fromm" type="date" min="{{ date('Y-m-d') }}" value=""
                                class="form-control" name="from" placeholder="From" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="too">To</label>
                            <input id="too" type="date" min="{{ date('Y-m-d') }}" value=""
                                class="form-control" name="to" placeholder="To" required>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary mt-2 me-1">Save</button>
                            <button type="reset" class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal"
                                aria-label="Close">
                                Close
                            </button>
                        </div>
                    </form>
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
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script>
        $('.select2').select2();

        editData = "@php echo Auth::user()->can('Edit Test Based Supplier Incentives') @endphp";

        if (editData == '') {
            showActioncolumn = false;
        } else {
            showActioncolumn = true;
        }

        var table = $('#supplier_incentives').DataTable({
            processing: true,
            serverSide: true,
            {{-- ajax: "{{ route('incentives.index') }}", --}}
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'supplier',
                    name: 'supplier'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'test',
                    name: 'test'
                },
                {
                    data: 'passing_value',
                    name: 'passing_value'
                },
                {
                    data: 'incentive_amount',
                    name: 'incentive_amount'
                },
                {
                    data: 'from',
                    name: 'from'
                },
                {
                    data: 'to',
                    name: 'to'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ],
            columnDefs: [{
                // Actions
                targets: 8,
                orderable: false,
                sortable: false,
            }, {
                // Actions
                targets: 4,
                orderable: false,
                sortable: false,
            }, {
                // Actions
                targets: 9,
                title: 'Action',
                orderable: false,
                sortable: false,
                visible: showActioncolumn
            }],
            ajax: {
                url: "{{ route('incentives.test.based') }}",
                data: function(d) {
                    d.incentive_type = $('.incentive_type').val();
                    d.supplier = $('#search_by_supplier').val();
                }
            },
            // order: [[1, 'asc']],
            dom: '<"d-flex justify-content-between align-items-center header-actions text-nowrap mx-1 row mt-75"' +
                '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" >' +
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

        $("#search_by_supplier").change(function() {
            $(this).next('.dropown-reset-icon').removeClass('d-none');
            table.draw();
        });
        $(".incentive_type").change(function() {
            $(this).next('.dropown-reset-icon').removeClass('d-none');
            table.draw();
        });





        $(document).on('submit', '#form', function(e) {
            e.preventDefault();
            let data = $(this).serialize();
            $.ajax({
                url: "{{ route('incentives.save.test.base.supplier.incentive') }}",
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#form')[0].reset();
                        $('#supplier_incentives').DataTable().ajax.reload();
                        $('#addModal').modal('toggle');
                        Swal.fire(
                            'Done!',
                            response.message,
                            'success'
                        )
                    } else {
                        // $('#addModal').modal('toggle');
                        Swal.fire(
                            'Oops!',
                            response.message,
                            'error'
                        )
                    }
                }
            });
        })
        $(document).on('submit', '#updateForm', function(e) {
            e.preventDefault();
            let data = $(this).serialize();
            $.ajax({
                url: "{{ route('incentives.update.test.base.supplier.incentive') }}",
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#updateForm')[0].reset();
                        $('#supplier_incentives').DataTable().ajax.reload();
                        $('#editModal').modal('toggle');
                        Swal.fire(
                            'Done!',
                            response.message,
                            'success'
                        )
                    } else {
                        Swal.fire(
                            'Oops!',
                            response.message,
                            'error'
                        )
                    }
                }
            });
        })


        function editForm(id, supplier, incentive_type, fromm, to, passing_value, range, min, max, test_id) {
            $('#supplierr').val(supplier);
            $('#valuee').val(passing_value);
            $('#valuee').attr('min', min);
            $('#valuee').attr('max', max);
            $('.rangg').text(range);
            $('#id').val(id);
            $('#fromm').val(fromm);
            $('#too').val(to);
            $('#test_idd').val(test_id);
            $('#editModal').modal('show');
            getincentiveTypee(incentive_type);
        }


        function getincentiveType() {
            let source_type = $("#supplier option:selected").attr('data-type');
            $.ajax({
                type: "get",
                data: {
                    source_type: source_type
                },
                url: '{{ route('incentives.get.types') }}',
                success: function(data) {
                    if (data.success == true) {
                        $('#incentive_type').html(data.data);
                    }
                },
                error: function(jqXhr, textStatus, errorMessage) {
                    alert('Error Occurred')
                }

            });
        }

        function getincentiveTypee(type = null) {
            let source_type = $("#supplierr option:selected").attr('data-type');
            $.ajax({
                type: "get",
                data: {
                    source_type: source_type
                },
                url: '{{ route('incentives.get.types') }}',
                success: function(data) {
                    if (data.success == true) {
                        $('#incentive_typee').html(data.data);
                        if (type) {
                            $('#incentive_typee').val(type)
                        } else {
                            $('#incentive_typee').val('')
                        }
                    }
                },
                error: function(jqXhr, textStatus, errorMessage) {
                    alert('Error Occurred')
                }
            });
        }


        function setPassingValue() {
            $('#value').attr('min', $("#incentive_type option:selected").attr('min-range'));
            $('#value').attr('max', $("#incentive_type option:selected").attr('max-range'));
            $('.rang').text('(' + $("#incentive_type option:selected").attr('range') + ')');
            $('#test_id').val($("#incentive_type option:selected").attr('test-id'));
        }

        function setPassingValuee() {
            $('#valuee').attr('min', $("#incentive_typee option:selected").attr('min-range'));
            $('#valuee').attr('max', $("#incentive_typee option:selected").attr('max-range'));
            $('.rangg').text('(' + $("#incentive_typee option:selected").attr('range') + ')');
            $('#test_idd').val($("#incentive_typee option:selected").attr('test-id'));
        }

        function statusUpdate(element, id) {
            if ($(element).prop('checked') == true) {
                var status = 1;
            } else {
                var status = 0;
            }
            var fd = new FormData();
            fd.append('status', status);
            fd.append('id', id);
            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                url: '{{ route('incentives.test.base.supplier.status.update') }}',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Done!',
                            response.message,
                            'success'
                        )
                    } else {
                        Swal.fire(
                            'Oops!',
                            response.message,
                            'error'
                        )
                    }
                }
            });
        }

        function reset_dropdown(id) {
            $('#' + id).val('')
            $('#' + id).next('.dropown-reset-icon').addClass('d-none');
            table.draw();
        }
    </script>
@endsection
