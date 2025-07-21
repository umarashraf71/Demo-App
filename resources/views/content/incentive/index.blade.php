@extends('layouts/contentLayoutMaster')

@section('title', 'Incentive Rates')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
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
                        <h4 class="card-title">Incentive Rates</h4>
                        @can('Create Incentive Rates')
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
                            <label class="form-label">Source Type</label>
                            <div style="position: relative">
                                <select name="source_type" id="search_by_source" class="source_type form-control select2">
                                    <option value="" selected disabled>Source Type</option>
                                    @foreach ($sourceTypes as $type)
                                        <option value="{{ $type->id }}">{{ ucfirst($type->name) }}</option>
                                    @endforeach
                                </select>
                                <i data-feather="x-circle" class="cursor-pointer dropown-reset-icon d-none"
                                    onclick="reset_dropdown('search_by_source')"></i>
                            </div>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Incentive Type</label>
                            <div style="position: relative">
                                <select name="incentive_type" id="search_by_incentive"
                                    class="incentive_type form-control select2">
                                    <option value="" selected disabled>Incentive Type</option>
                                    @foreach ($incentiveTypes as $incentiveType)
                                        <option value="{{ $incentiveType->id }}">{{ ucfirst($incentiveType->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                <i data-feather="x-circle" class="cursor-pointer dropown-reset-icon d-none"
                                    onclick="reset_dropdown('search_by_incentive')"></i>

                            </div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="table" id="supplier_incentives">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Source</th>
                                    <th>Incentive</th>
                                    <th>Range</th>
                                    <th>Rate</th>
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
                        <h1 class="mb-1"><span class="form_title">Add</span> Incentive</h1>
                        {{--               <p>against collection points and source types.</p> --}}
                    </div>
                    <form action="{{ route('incentives.store') }}" method="POST" class="row" id="form">
                        <input type="hidden" name="id" id="id" value="{{ old('id', '') }}">
                        @csrf

                        <div class="col-12">
                            <label class="form-label" for="source_type">Source Type</label>
                            <select name="source_type" id="source_type" class="select2 form-select">
                                <option value="" selected disabled>Select Source Type</option>
                                @foreach ($sourceTypes as $type)
                                    <option {{ old('source_type') == $type->id ? 'selected' : '' }}
                                        value="{{ $type->id }}">
                                        {{ ucfirst($type->name) }}</option>
                                @endforeach
                            </select>
                            @error('source_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="incentive_type">Incentive Type</label>
                            <select name="incentive_type" id="incentive_type" class="form-select select2">
                                <option value="" selected disabled>Select Incentive Type</option>
                                @foreach ($incentiveTypes as $incentiveType)
                                    <option {{ old('incentive_type') == $incentiveType->id ? 'selected' : '' }}
                                        value="{{ $incentiveType->id }}">{{ ucfirst($incentiveType->name) }}</option>
                                @endforeach
                            </select>
                            @error('incentive_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="amount">Rate</label>
                            <input type="number" id="amount" min="0" step="0.01" max="99999"
                                value="{{ old('amount') }}" class="form-control" name="amount"
                                placeholder="Rate In Rupees">
                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            <div class="col-6">
                                <p class="mb-0">
                                    <label for="range" class="form-label mb-0">Range:</label>
                                    <input type="text" value="{{ old('range') }}" name="range" class="m-0"
                                        id="range" readonly style="border:0; color:#6f8bf5; font-weight:bold;">
                                </p>

                                <div id="slider-range" style="min-width: 200% !important;"></div>
                                @error('range')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary mt-2 me-1">Save</button>
                                <button type="reset" class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Close
                                </button>
                            </div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script>
        $('.select2').select2();

        // permissions checks variables
        editData = "@php echo Auth::user()->can('Edit Incentive Rates') @endphp";


        if (editData == '') {
            showActioncolumn = false;
        } else {
            showActioncolumn = true;
        }

        var table = $('#supplier_incentives').DataTable({
            processing: true,
            serverSide: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'source',
                    name: 'source'
                },
                {
                    data: 'incentive',
                    name: 'incentive'
                },
                {
                    data: 'range',
                    name: 'range'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action'
                },
                // { data: 'to' }
            ],
            columnDefs: [{
                // Actions
                targets: 5,
                orderable: false,
                sortable: false,
            }, {
                // Actions
                targets: 6,
                title: 'Action',
                orderable: false,
                sortable: false,
                visible: showActioncolumn
            }],
            ajax: {
                url: "{{ route('incentives.index') }}",
                data: function(d) {
                    d.incentive_type = $('.incentive_type').val();
                    d.source_type = $('.source_type').val();

                }
            },
            order: [[1, 'asc']],
            order: [
                [1, 'asc']
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

        $(".source_type").change(function() {
            $(this).next('.dropown-reset-icon').removeClass('d-none');
            table.draw();
        });
        $(".incentive_type").change(function() {
            $(this).next('.dropown-reset-icon').removeClass('d-none');
            table.draw();
        });
        $(document).ready(function() {
            @if ($errors->any())
                $('#addModal').modal('show');
                var id = $('#id').val();
                if (id) {
                    $('#form').attr('action', "{{ route('incentives.update') }}");
                    $('.form_title').text('Update');
                } else {
                    $('#form').attr('action', "{{ route('incentives.store') }}");
                    $('.form_title').text('Add');
                }
            @endif
            $(function() {
                var range1 = '{{ old('range') ? explode('-', old('range'))[0] : '300' }}';
                var range2 = '{{ old('range') ? explode('-', old('range'))[1] : '500' }}';
                $("#slider-range").slider({
                    range: true,
                    min: 0,
                    max: 10000,
                    values: [range1, range2],
                    slide: function(event, ui) {
                        $("#range").val(ui.values[0] + " - " + ui.values[1]);
                    }
                });

                $("#range").val($("#slider-range").slider("values", 0) +
                    " - " + $("#slider-range").slider("values", 1));
            });
        });

        function addForm() {
            $('#form').attr('action', "{{ route('incentives.store') }}");
            $('.form_title').text('Add');
            $('#id').val('');
            $('#form')[0].reset();
        }

        function editForm(id, amount, incentive_type, source_type, range, from, to) {
            $('#form').attr('action', "{{ route('incentives.update') }}");
            $('#amount').val(amount);
            $('#incentive_type').val(incentive_type);
            $('#source_type').val(source_type);
            $('#id').val(id);

            $("#slider-range").slider({
                range: true,
                min: 0,
                max: 10000,
                values: [from, to],
                slide: function(event, ui) {
                    $("#range").val(ui.values[0] + " - " + ui.values[1]);
                }
            });

            $("#range").val(from + " - " + to);
            $('#addModal').modal('show');
            $('.form_title').text('Update');
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
                url: '{{ route('incentives.status.update') }}',
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
