
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
                        <div class="col-2">
                            <h4 class="card-title pt-1 my-auto">Prices
                                <small class="text-danger status_text">({{$WorkFlowApproval->status==1?"Approved":($WorkFlowApproval->status==3?'Rejected':($WorkFlowApproval->status==4?'Reverted':'Pending'))}})</small>
                            </h4>
                        </div>


                        <div class="col-1 offset-7 text-end pt-1 ">
                             <a class="remarks_btn" style="display: {{$WorkFlowApproval && $WorkFlowApproval->remarks?'block':'none'}}" href="#" onclick="getRemarks('{{request('code')}}')">remarks</a>
                        </div>
                        {{-- @php
                        dd($is_curr_user_on_curr_step);
                    @endphp --}}
                        @if($is_curr_user_on_curr_step)
                            <div class="col-2 text-start" id="action_btn">
                                <a class="add-new-btn btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#updateModal" href="#" ><i class="fa fa-edit"></i>Update</a>
                            </div>
                            @else
                                <div class="col-2 text-start" >
                                    <h4 class="card-title pt-1 my-auto">
                                        <a href="{{route('workflow.approvals.index')}}" class="card-title" style="padding:8px;border-radius:35%; background: #f3f2f7"><i data-feather="corner-up-left"></i> back</a>
                                    </h4>
                               </div>
                        @endif
                    </div>
                    <div class="card-body pt-0">
                    <div class="card-datatable table-responsive">
                        <table class="table" id="pending_table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Code</th>
                                <th>Area Office</th>
                                <th>Source</th>
                                <th>Supplier</th>
                                <th>Collection Point</th>
                                <th>Current Price</th>
                                <th>New Price</th>
                                <th>Current Volume</th>
                                <th>New Volume</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="updateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                    <div class="text-center mb-2">
                        <h1 class="mb-1">Price Approvel</h1>
                    </div>
                    <form class="form" id="updateStatus">
                        @csrf
                        <input type="hidden" name="code" value="{{request('code')}}">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="status">Status</label>
                                    <select name="status" class="form-control" id="status" required>
                                        <option value="" selected disabled>Select Status</option>
                                        <option value="1">Approve</option>
                                        <option value="3">Reject</option>
                                        <option value="4">Revert</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="remark">Remarks</label>
                                    <textarea required name="remark" class="form-control"  placeholder="Remark " id="remark"></textarea>
                                </div>
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" id="save_button" class="btn btn-primary me-1">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="remarksModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                    <div class=" mb-1">
                        <h1 class="mb-1">Remarks</h1>
                    </div>
                    <div class="table-responsive">
                        <table class="table " id="remarks_table">
                            <thead><tr><td>#</td><td>From</td><td>Remark</td><td>Time</td></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
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
@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js')}}"></script>
{{--    <script src="{{ asset('/js/repeater.js')}}"></script>--}}

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
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                {data: 'code', name: 'code', orderable: false },
                {data: 'areaOffice', name: 'areaOffice', orderable: false },
                {data: 'source', name: 'source', orderable: false },
                {data: 'suplier', name: 'suplier', orderable: false },
                {data: 'collPoint', name: 'collPoint', orderable: false },
                {data: 'price', name: 'price', orderable: false },
                {data: 'updated_price', name: 'updated_price', orderable: false },
                {data: 'volume', name: 'volume', orderable: false },
                {data: 'updated_volume', name: 'updated_volume', orderable: false },
            ],
            columnDefs: [
                {
                    targets: 9,
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

        $(document).on('submit', '#updateStatus', function(e) {
            e.preventDefault();
            $('#save_button').addClass('btn-primary')
            $('#save_button').removeClass('btn-danger')
            let msg = $('#status option:selected').text()
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
                    let data = $(this).serialize();
                    $.ajax({
                        url: '{{route('workflow.approvals.status.update')}}',
                        method: 'get',
                        data: data,
                        success: function (response) {
                            if (response.success) {
                                showAlert('success', response.message);
                                $('#save_button').removeClass('btn-primary')
                                $('#save_button').addClass('btn-danger')
                                $('#action_btn').hide();
                                $('#updateModal').modal('hide');
                                $('.status_text').text('('+msg+')');
                                $('.remarks_btn').show()
                            } else {
                                showAlert('error', response.message);
                                // if (response.key) {
                                //     $("*[name='" + response.key + "']").css('border', '1px solid red')
                                // }
                                $('#save_button').addClass('btn-primary')
                                $('#save_button').removeClass('btn-danger')
                            }
                        }
                    });
                }

        });
        })

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
                                $('.status_text').text('('+msg+')');
                                showAlert('success', response.message);

                            } else {
                                showAlert('error', response.message);
                            }
                        }
                    });

                }
            })
        }


        function  getRemarks(code){
            $('#remarksModal').modal('show')
            $.ajax({
                type: "get",
                dataType: 'JSON',
                url: '{{route('get.remarks.ajax')}}',
                data:{code:code},
                method:'get',
                success: function (response) {
                    if (response.success == true) {
                        $('#remarks_table > tbody').html('')
                        $('#remarks_table > tbody').html(response.data)
                    } else {
                        $('#remarks_table > tbody').html('<tr class="text-center"><td colspan="4">No data found</td></tr>')
                    }
                }
            });
        }



    </script>
@endsection
