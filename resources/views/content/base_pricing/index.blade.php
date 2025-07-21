@extends('layouts/contentLayoutMaster')

@section('title', 'Prices List')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">

@endsection
@section('content')
    <!-- Column Search -->
    <section id="pricing_div">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row border-bottom py-2">
                        <div class="col-3">
                            <h4 class="card-title pt-1 my-auto">Approved Prices</h4>
                        </div>
                        <div class="col-1 offset-8 price_edit" style="display:none;">
                            <a class="add-new-btn btn btn-primary mr_30px" onclick='editPrices()' href="#"><i
                                    data-feather="edit"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tabs">
                            @include('content._partials._sections.base_price_tabs')
                        </div>

                        <div class="filters mb-1">
                            @include('content._partials._sections.base_price_filter')
                        </div>

                        <div class="card-datatable table-responsive">
                            <table class="table" id="pending_table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>No.</th>
                                        {{--                                 <th>Code</th> --}}
                                        <th>Area Office</th>
                                        <th>Source</th>
                                        <th>Supplier</th>
                                        <th>Collection Point</th>
                                        <th>Price</th>
                                        <th>Expected Volume</th>
                                        <th>Wef</th>
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
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script>

@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script src="{{ asset('/js/repeater.js') }}"></script>

    <script>
        $('.select2').select2();

        // permissions checks variables
        {{-- editData = "@php echo Auth::user()->can('Edit Source Type Pricing') @endphp"; --}}
        {{-- deleteData = "@php echo Auth::user()->can('Delete Source Type Pricing') @endphp"; --}}

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
            ajax: {
                url: "{{ route('price.index') }}",
                data: function(d) {
                    d.area_office = $('#area_office_search').val()
                }
            },
            columns: [{
                    data: 'edit',
                    name: 'edit'
                },
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                // {data: 'code', name: 'code'},
                {
                    data: 'areaOffice',
                    name: 'areaOffice'
                },
                {
                    data: 'source',
                    name: 'source'
                },
                {
                    data: 'suplier',
                    name: 'suplier'
                },
                {
                    data: 'collPoint',
                    name: 'collPoint'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'volume',
                    name: 'volume'
                },
                {
                    data: 'wef',
                    name: 'wef'
                },
            ],
            columnDefs: [{
                targets: 5,
                orderable: false,
                searchable: false,
                // visible:showActioncolumn
            }, {
                targets: 7,
                orderable: false,
                searchable: false,
                // visible:showActioncolumn
            }],
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

        $(document).on('click', '.row_checkbox ', function() {
            is_checked = 0;
            $('.row_checkbox').each(function() {
                if (this.checked == true) {
                    is_checked = 1;
                }
            });
            if (is_checked) {
                $('.price_edit').show()
            } else {
                $('.price_edit').hide()
            }
        });

        function editPrices() {
            let url = '{{ route('price.edit') }}';
            let prices = [];
            $('.row_checkbox').each(function() {
                if (this.checked == true) {
                    prices.push($(this).attr('id'));
                }
            });
            window.location.href = url + '?id=' + prices;
        }
    </script>
@endsection
