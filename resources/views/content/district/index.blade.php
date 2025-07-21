@php
    $segment1 = request()->segment(1);
@endphp
@extends('layouts/contentLayoutMaster')

@section('title', 'Districts List')

@section('vendor-style')
    {{-- vendor css files --}}
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
                        <h4 class="card-title">Districts</h4>
                        @can('Create District')
                            <a class="add-new-btn btn btn-primary mt-2 mr_30px" href="#" data-bs-toggle="modal"
                                data-bs-target="#addModal">Add</a>
                        @endcan
                    </div>

                    <div class="card-datatable">
                        @if ($message = Session::get('success'))
                            <div class="demo-spacing-0 my-2">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <div class="alert-body">{{ $message }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        <table class="table" id="district_table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Short Name</th>
                                    <th>Name</th>
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
                        <h1 class="mb-1">Add District</h1>
                    </div>

                    <form class="form" id="createForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="name-column">Short Name</label>
                                    <input type="text" required id="name-column" class="form-control"
                                        placeholder="Short Name" name="short_name" />
                                    <span class="text-danger create_short_name"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="name-column">Full Name</label>
                                    <input type="text" required id="name-column" class="form-control" placeholder="Name"
                                        name="name" />
                                    <span class="text-danger create_name"></span>

                                </div>
                            </div>


                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-1">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
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
                        <h1 class="mb-1">Update District</h1>
                    </div>

                    <form class="form" action="#" id="editForm" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="edit_short_name">Name</label>
                                <input type="text" required id="edit_short_name" class="form-control"
                                    placeholder="Short Name" name="short_name" value="" />
                                <span class="text-danger edit_short_name"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="edit_name">Full Name</label>
                                    <input type="text" required id="edit_name" class="form-control"
                                        placeholder="Name" name="name" value="" />
                                    <span class="text-danger edit_name"></span>
                                </div>
                            </div>



                            <div class="col-12 text-center">
                                <button type="submit" id="updateForm" class="btn btn-primary me-1">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
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
@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script>
        // permissions checks variables
        {{--        editData = "@php echo Auth::user()->can('Edit Source Type') @endphp"; --}}
        {{--        deleteData = "@php echo Auth::user()->can('Delete Source Type') @endphp"; --}}

        // if(editData == '' && deleteData == '')
        // {
        //     showActioncolumn = false;
        // }
        // else
        // {
        //     showActioncolumn = true;
        // }

        $('#district_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('districts.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'short_name',
                    name: 'short_name'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'action'
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

        $(document).ready(function() {
            @if ($errors->any())
                $('#addModal').modal('show')
            @endif
        });


        function editRecord(id, name, sn) {
            $("#id").val(id);
            $("#edit_name").val(name);
            $("#edit_short_name").val(sn);
        }

        $(document).on('submit', '#editForm', function(e) {
            $('.text-danger').text('')
            e.preventDefault();
            let data = $(this).serialize();
            $.ajax({
                url: '{{ route('district.update') }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#district_table').DataTable().ajax.reload();
                        $('#editModal').modal('toggle');
                        Swal.fire(
                            'Done!',
                            response.message,
                            'success'
                        )
                    } else {
                        $('.edit_' + response.key).text(response.message)
                    }
                }
            });
        })

        $(document).on('submit', '#createForm', function(e) {
            $('.text-danger').text('')
            e.preventDefault();
            let data = $(this).serialize();
            console.log(data);
            $.ajax({
                url: '{{ route('district.store') }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#district_table').DataTable().ajax.reload();
                        $('#addModal').modal('toggle');
                        Swal.fire(
                            'Done!',
                            response.message,
                            'success'
                        )
                    } else {

                        $('.create_' + response.key).text(response.message)
                    }
                }
            });
        })
    </script>
@endsection
