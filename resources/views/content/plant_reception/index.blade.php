@extends('layouts/contentLayoutMaster')

@section('title', 'Plant Receptions')

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
                        <h4 class="card-title">Plant Receptions</h4>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="table" id="collection_point_datatable">
                            <thead>
                              
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>User</th>
                                    <th>Plant</th>
                                    <th>Type</th>
                                    <th>Opening Balance</th>
                                    <th>Gross</th>
                                    <th>TS</th>
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
                        data: 'user_name',
                        name: 'user_name'
                    },
                   
                    {
                        data: 'plant',
                        name: 'plant'
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
                        data: 'ts_volume',
                        name: 'ts_volume'
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
                    url: "{{ route('get.plant.receptions') }}",
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
                   
                    let ts_volume = 0.0;
                   
                    let gross_volume = 0;
                    data.forEach(function(item) {
                       
                      
                        gross_volume += item.gross_volume;
                        ts_volume += item.ts_volume;
                    });
                    // $( api.column( 2 ).footer() ).html(get);
                    $(api.column(5).footer()).html(gross_volume);
                    $(api.column(6).footer()).html(ts_volume);
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
