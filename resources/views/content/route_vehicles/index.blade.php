@extends('layouts/contentLayoutMaster')

@section('title', 'Route Vehicles List')

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
    <section id="column-search-datatable">
        <div class="row">
            @if ($errorMessage = Session::get('errorMessage'))
                            <div class="demo-spacing-0 my-2">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <div class="alert-body">{{ $errorMessage }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Route Vehicles</h4>
                        <a class="add-new-btn btn btn-primary mt-2 mr_30px" href="#" data-bs-target="#addModal"
                            data-bs-toggle="modal">Attach Vehicle</a>
                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="table" id="route_table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Route</th>
                                    <th>User</th>
                                    <th>Vehicle</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Check in</th>
                                    <th>Check out</th>
                                    <th>Reception</th>
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
                        <h1 class="mb-1">Attach Vehicle</h1>
                    </div>
                    <form action="{{ route('route-vehicle.store') }}" method="POST" class="row" id="addForm">
                        @csrf

                        <div class="col-12">
                            <label class="form-label" for="route">Route</label><span class="text-danger">*</span>
                            <select name="route" class="select2 form-select" id="route" required>
                                <option disabled selected value="">Select Route</option>
                                @foreach ($routes as $data)
                                    <option value="{{ $data->id }}">{{ ucfirst($data->name) }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-message"></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="user">MMT User</label><span class="text-danger">*</span>
                            <select name="user" class="select2 form-select" id="user" required>
                                <option disabled selected value="">Select User</option>
                            </select>
                            <span class="text-danger error-message"></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="vehicle">Vehicle</label><span class="text-danger">*</span>
                            <select name="vehicle" class="select2 form-select" id="vehicle" required>
                                <option disabled selected value="">Select Vehicle</option>
                                @foreach ($vehicles as $data)
                                    <option value="{{ $data->id }}">{{ ucfirst($data->vehicle_number) }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-message"></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="vehicle">Date</label><span class="text-danger">*</span>
                            <input type="date" name="date" class="form-control search-input" id="date">
                            <span class="text-danger error-message"></span>
                        </div>

                        <div class="col-12">
                            <div id="diagram">
                            </div>
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
                        <h1 class="mb-1">Update Route</h1>
                    </div>
                    <form action="{{ route('route-vehicle.updatee') }}" method="POST" class="row" id="editForm">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <div class="col-12">
                            <label class="form-label" for="edit_route">Route</label><span class="text-danger">*</span>
                            <select name="route" class="select2 form-select" id="edit_route" required>
                                <option disabled selected value="">Select Route</option>
                                @foreach ($routes as $data)
                                    <option value="{{ $data->id }}">{{ ucfirst($data->name) }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-message"></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="user">MMT User</label><span class="text-danger">*</span>
                            <select name="user" class="select2 form-select" id="edit_user" required>
                            </select>
                            <span class="text-danger error-message"></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="edit_vehicle">Vehicle</label><span
                                class="text-danger">*</span>
                            <select name="vehicle" class="select2 form-select" id="edit_vehicle" required>
                                <option disabled selected value="">Select Vehicle</option>
                                @foreach ($vehicles as $data)
                                    <option value="{{ $data->id }}">{{ ucfirst($data->vehicle_number) }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-message"></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="vehicle">Date</label><span class="text-danger">*</span>
                            <input type="date" name="date" class="form-control search-input" id="edit_date">
                            <span class="text-danger error-message"></span>
                        </div>
                        <div class="col-12">
                            <div id="edit_diagram">
                            </div>
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

@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script>
        //  permissions checks variables
        editData = "@php echo Auth::user()->can('Edit Route Vehicles') @endphp";
        deleteData = "@php echo Auth::user()->can('Delete Route Vehicles') @endphp";

        if (editData == '' && deleteData == '') {
            showActioncolumn = false;
        } else {
            showActioncolumn = true;
        }

        $('#route_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('route-vehicle.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'route',
                    name: 'route'
                },
                {
                    data: 'user',
                    name: 'user'
                },
                {
                    data: 'vehicle',
                    name: 'vehicle'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'check_in',
                    name: 'check_in'
                },
                {
                    data: 'check_out',
                    name: 'check_out'
                },
                {
                    data: 'reception',
                    name: 'reception'
                },
                {
                    data: 'action'
                }
            ],
            columnDefs: [{
                // Status
                targets: 5,
                title: 'Status',
                orderable: false,


            }, {
                // Actions
                targets: 9,
                title: 'Actions',
                orderable: false,
                sortable: false,
                visible: showActioncolumn
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
        var arrow_image = '{{ asset('images/workflow/arrow.png') }}';
        var user_image = '{{ asset('images/workflow/car.png') }}';
        $('#route').on('change', function() {
            $.ajax({
                url: '{{ route('routes.show') }}',
                method: 'get',
                data: {
                    'id': $(this).val()
                },
                success: function(response) {
                    if (response.success) {
                        var result = response.data;
                        var users = response.users;
                        var data = `<div class="row mx-0">`;
                        var count = 0;
                        var total = result && result.cps ? result.cps.length : 0;
                        for (let i = 0; i < total; i++) {
                            count++;
                            data += `<div class='col-1 py-0 mt-1 text-center'  style="width: 88px"><span >${count}</span><img class="text-center" src="${user_image}" width="70px"/>
                               <div class="text-center">${result.cps[i]}</div></div>`;
                            if (count < total) {
                                data +=
                                    `<div class='col-1 p-0 my-auto' style="width: 50px"><img  src="${arrow_image}" width="50px"/></div>`;
                            }
                        }
                        data += `</div>`;
                        $('#diagram').html(data);

                        let txt = '<option disabled selected value="">Select User</option>';
                        $('#user').html('');
                        for (let i = 0; i < users.length; i++) {
                            txt += `<option value="${users[i]._id}">${users[i].name}</option>`;
                        }
                        $('#user').html(txt);
                    }
                }
            });
        });

        $('#edit_route').on('change', function() {
            $.ajax({
                url: '{{ route('routes.show') }}',
                method: 'get',
                data: {
                    'id': $(this).val()
                },
                success: function(response) {
                    if (response.success) {
                        var result = response.data;
                        var users = response.users;
                        var data = `<div class="row mx-0">`;
                        var count = 0;
                        var total = result && result.cps ? result.cps.length : 0;
                        for (let i = 0; i < total; i++) {
                            count++;
                            data += `<div class='col-1 py-0 mt-1 text-center'  style="width: 88px"><span >${count}</span><img class="text-center" src="${user_image}" width="70px"/>
                               <div class="text-center">${result.cps[i]}</div></div>`;
                            if (count < total) {
                                data +=
                                    `<div class='col-1 p-0 my-auto' style="width: 50px"><img  src="${arrow_image}" width="50px"/></div>`;
                            }
                        }
                        data += `</div>`;
                        $('#edit_diagram').html(data);

                        let txt = '<option disabled selected value="">Select User</option>';
                        $('#edit_user').html('');
                        for (let i = 0; i < users.length; i++) {
                            txt += `<option value="${users[i]._id}">${users[i].name}</option>`;
                        }
                        $('#edit_user').html(txt);
                    }
                }
            });
        });

        $('#edit_collection_point').on('change', function() {
            var selObj = document.getElementById('edit_collection_point');
            var data = `<div class="row mx-0">`;
            var count = 0;
            var total_selected = $("#edit_collection_point :selected").length;
            for (let i = 0; i < selObj.options.length; i++) {
                if (selObj.options[i].selected) {
                    count++;
                    data += `<div class='col-1 py-0 mt-1 text-center'  style="width: 88px"><span >${count}</span><img class="text-center" src="${user_image}" width="70px"/>
                     <div class="text-center">${selObj.options[i].text}</div></div>`;
                    if (count < total_selected) {
                        data +=
                            `<div class='col-1 p-0 my-auto' style="width: 50px"><img  src="${arrow_image}" width="50px"/></div>`;
                    }
                }
            }
            data += `</div>`;
            $('#diagram').html(data);
        });

        $(document).on('submit', '#addForm', function(e) {
            $(".error-message").text('');
            e.preventDefault();
            let data = $(this).serialize();
            let url = $(this).attr('action');
            let method = $(this).attr('method');
            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#addModal').modal('toggle');
                        $('#diagram').html('');
                        Swal.fire(
                            'Done!',
                            response.message,
                            'success'
                        )
                        $('#addForm')[0].reset();
                        $('#route_table').DataTable().ajax.reload();
                    } else {
                        $("#" + response.key).nextAll('.error-message').text(response.message);
                    }
                }
            });
        })
        $(document).on('submit', '#editForm', function(e) {
            $(".error-message").text('');
            e.preventDefault();
            let data = $(this).serialize();
            let url = $(this).attr('action');
            let method = $(this).attr('method');
            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#editModal').modal('toggle');
                        Swal.fire(
                            'Done!',
                            response.message,
                            'success'
                        )
                        $('#editForm')[0].reset();
                        $('#route_table').DataTable().ajax.reload();
                    } else {
                        $("#edit_" + response.key).nextAll('.error-message').text(response.message);
                    }
                }
            });
        })


        function editRecord(url) {
            $('.error-message').text('')
            $('#edit_diagram').html('');
            $.ajax({
                url: url,
                method: 'get',
                // data: {'id':id},
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        var users = response.users;
                        $('#edit_route').val(data.route_id)
                        $('#edit_vehicle').val(data.vehicle_id)
                        $('#id').val(data._id)
                        $('#edit_date').val(data.date)
                        $('#editModal').modal('toggle');

                        let txt = '<option disabled selected value="">Select User</option>';
                        $('#edit_user').html('');
                        for (let i = 0; i < users.length; i++) {
                            let is_selected = data.user_id && users[i]._id == data.user_id ? 'selected' : '';
                            txt += `<option value="${users[i]._id}" ${is_selected} >${users[i].name}</option>`;
                        }
                        $('#edit_user').html(txt);

                    } else{
                        Swal.fire(
                            'Oops!',
                            response.message,
                            'error'
                        )
                    }
                }
            });

        }

        //open closed route
        function openRoute(url) {
            $.ajax({
                url: url,
                method: 'get',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Done!',
                            response.message,
                            'success'
                        );
                        $('#route_table').DataTable().ajax.reload();
                    } else{
                        Swal.fire(
                            'Oops!',
                            response.message,
                            'error'
                        )
                    }
                }
            });

        }
    </script>
@endsection
