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
                                    <td colspan="2">
                                        <select class="select2 search-input" id="type">
                                            <option value="">Type</option>
                                            <option value="purchase_at_mcc">MCC Purchase</option>
                                            <option value="mmt_purchase">MMT Purchase</option>
                                            <option value="purchase_at_ao">Area Office Purchase</option>
                                            <option value="purchase_at_plant">Plant Purchase</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <select class="select2 search-input" id="collection_point">
                                            <option value="">Collection Point</option>
                                            @foreach ($collectionPoints as $collectionPoint)
                                                <option value="{{ $collectionPoint->id }}">{{ $collectionPoint->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td colspan="1">
                                        <select class="select2 search-input" id="area_office">
                                            <option value="">Area Office</option>
                                            @foreach ($areaOffices as $areaOffice)
                                                <option value="{{ $areaOffice->id }}">{{ $areaOffice->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td colspan="1">
                                        <select class="select2 search-input" id="plant">
                                            <option value="">Plant</option>
                                            @foreach ($plants as $plant)
                                                <option value="{{ $plant->id }}">{{ $plant->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <select class="select2 search-input" id="supplier">
                                            <option value="">Supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
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
        // permissions checks variables
        editData = "@php echo Auth::user()->can('Edit Collection Points') @endphp";
        deleteData = "@php echo Auth::user()->can('Delete Collection Points') @endphp";

        if (editData == '' && deleteData == '') {
            showActioncolumn = false;
        } else {
            showActioncolumn = true;
        }
        var table;
        $(document).ready(function() {
            $(".select2").select2({});
            table = $('#collection_point_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('get.purchases') }}",
                    data: function(d) {
                        // '_token': '{{ csrf_token() }}',
                        d.type = $('#type option:selected').val();
                        d.collection_point = $('#collection_point option:selected').val();
                        d.area_office = $('#area_office option:selected').val();
                        d.plant = $('#plant option:selected').val();
                        d.supplier = $('#supplier option:selected').val();
                    }
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
                        data: 'action',
                        name: 'action'
                    },
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
