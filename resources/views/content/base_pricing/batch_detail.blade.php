
@extends('layouts/contentLayoutMaster')

@section('title', ' Pending Prices List')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
@endsection
@section('content')

    <!-- Column Search -->
    <section id="pricing_div">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row border-bottom py-2">
                        <div class="col-3">
                            <h4 class="card-title pt-1 my-auto">Pending Prices  <small>({{request('code')}})</small></h4>
                        </div>
                            <div class="col-2 offset-7" >
                                <h4 class="card-title pt-1 my-auto">
                                    <a href="{{route('price.pending')}}" class="card-title" style="padding:8px;border-radius:35%; background: #f3f2f7"><i data-feather="corner-up-left"></i> back</a>
                                </h4>
                           </div>
                    </div>
                    <div class="card-body pt-0">
{{--                        <div class="tabs">--}}
{{--                            @include('content._partials._sections.base_price_tabs')--}}
{{--                        </div>--}}

{{--                        <div class="filters mb-1">--}}
{{--                            @include('content._partials._sections.base_price_filter')--}}
{{--                        </div>--}}
                    <div class="card-datatable table-responsive">
                        <table class="table" id="pending_table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Area Office</th>
                                <th>Source</th>
                                <th>Supplier</th>
                                <th>Collection Point</th>
                                <th>Current Price</th>
                                <th>New Price</th>
{{--                                <th>Current Volume</th>--}}
{{--                                <th>New Volume</th>--}}
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
@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js')}}"></script>
    <script src="{{ asset('/js/repeater.js')}}"></script>

    <script>
        // permissions checks variables
        {{--editData = "@php echo Auth::user()->can('Edit Source Type Pricing') @endphp";--}}
        {{--deleteData = "@php echo Auth::user()->can('Delete Source Type Pricing') @endphp";--}}

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
                url: "{{ route('approval.show',request('code')) }}",
                data: function (d) {
                    // d.area_office = $('#area_office_search').val()
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},

                {data: 'areaOffice', name: 'areaOffice'},
                {data: 'source', name: 'source'},
                {data: 'suplier', name: 'suplier'},
                {data: 'collPoint', name: 'collPoint'},
                {data: 'price', name: 'price'},
                {data: 'updated_price', name: 'updated_price'},
                // {data: 'volume', name: 'volume'},
                // {data: 'updated_volume', name: 'updated_volume'},
            ],
            columnDefs: [
                {
                    targets: 6,
                    orderable: false,
                    searchable: false,
                    // visible:showActioncolumn
                }
            ],
            // order: [[1, 'asc']],
            dom:
                '<"d-flex justify-content-between align-items-center header-actions text-nowrap mx-1 row mt-75"' +
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


        function updateStatus(status) {
            let code = '{{request('code')}}';
            let msg = (status==1)?'approve':'reject';
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't to "+msg+" these prices?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "get",
                        dataType: 'JSON',
                        url: '{{route('workflow.approvals.status.update')}}',
                        data:{code:code,status:status},
                        method:'get',
                        success: function (response) {
                            if (response.success == true) {
                                $('#action_btn').hide();
                                $('#pending_table').DataTable().ajax.reload();
                                showAlert('success', response.message);
                            } else {
                                showAlert('error', response.message);
                            }
                        }
                    });

                }
            })

        }


    </script>
@endsection
