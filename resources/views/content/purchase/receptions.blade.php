@extends('layouts/contentLayoutMaster')

@section('title', 'Receptions')

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
                        <h4 class="card-title">Receptions</h4>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="table" id="collection_point_datatable">
                            <thead>
                                <tr>
                                    <td></td>
                                    <td colspan="2">
                                        <select class="select2 search-input" id="type" colspan="2">
                                            <option value="">Type</option>
                                            <option value="mmt_reception">MMT</option>
                                            <option value="ao_lab_reception">Area Office Lab</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <select class="select2 search-input" id="collection_point" colspan="4">
                                            <option value="">Collection Point</option>
                                            @foreach ($collectionPoints as $collectionPoint)
                                                <option value="{{ $collectionPoint->id }}">{{ $collectionPoint->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <select class="select2 search-input" id="area_office" colspan="4">
                                            <option value="">Area Office</option>
                                            @foreach ($areaOffices as $areaOffice)
                                                <option value="{{ $areaOffice->id }}">{{ $areaOffice->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>User</th>
                                    <th>Collection Point</th>
                                    <th>Area Office</th>
                                    <th>Type</th>
                                    <th>Opening Balance</th>
                                    <th>Volume</th>
                                    <th>Left Over</th>
                                    <th>Gain/Loss</th>
                                    <th>Date</th>
                                    <th>Action</th>
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
        $(document).ready(function() {
            $(".select2").select2({});
            var table = $('#collection_point_datatable').DataTable({
                processing: true,
                serverSide: true,
                columns: [{
                        data: 'serial_number',
                        name: 'serial_number'
                    },
                    {
                        data: 'user',
                        name: 'user'
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
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'opening_balance',
                        name: 'opening_balance'
                    },
                    {
                        data: 'gross_volume',
                        name: 'gross_volume'
                    },
                    {
                        data: 'left_over_milk',
                        name: 'left_over_milk'
                    },
                    {
                        data: 'gain_loss_ts',
                        name: 'gain_loss_ts'
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
                ajax: {
                    url: "{{ route('get.purchase.receptions') }}",
                    data: function(d) {
                        d.cp_search = $('#cp_search').val();
                        d.type = $('#type option:selected').val();
                        d.area_office = $('#area_office option:selected').val();
                        d.collection_point = $('#collection_point option:selected').val();
                    }
                },

                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api(),
                        data;
                    // // computing column Total of the complete result
                    let get = 0;
                    let left_over = 0;
                    let gain_loss = 0;
                    let gross_volume = 0;
                    data.forEach(function(item) {
                        // get += item.opening_balance;
                        gain_loss += item.gain_loss;
                        gross_volume += item.gross_volume;
                        left_over += item.left_over_milk;
                    });
                    // $( api.column( 2 ).footer() ).html(get);
                    $(api.column(6).footer()).html(gross_volume);
                    $(api.column(7).footer()).html(left_over);
                    $(api.column(8).footer()).html(gain_loss);
                },
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

            $("#cp_search").change(function() {
                table.draw();
            });

            $(".search-input").on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
