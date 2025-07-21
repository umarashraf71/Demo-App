@extends('layouts/contentLayoutMaster')

@section('title', 'Source Types List')

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
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Source Types</h4>
                        @can('Create Source Type')
                            <a class="add-new-btn btn btn-primary mt-2 mr_30px" href="#" data-bs-toggle="modal"
                                data-bs-target="#addModal">Add</a>
                            {{--             <button type="button"class="button" data-dismiss="modal" aria-label="Close">&times;</button> --}}
                            {{--                <a class="add-new-btn btn btn-primary mt-2 mr_30px" href="{{route("supp-type.create")}}">Add New</a> --}}
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
                    <div class="card-datatable">
                        <table class="table" id="supplier_type_table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Category</th>
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
                        <h1 class="mb-1">Add Source</h1>
                    </div>

                    <form class="form" action="{{ route('source-type.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="name-column">Name</label>
                                    <input type="text" required id="name-column" class="form-control" placeholder="Name"
                                        name="name" value="{{ old('name') }}" />
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="description-colum">Description</label>
                                    <input type="text" id="description-colum" class="form-control" required
                                        placeholder="Description" name="description" value="{{ old('description') }}" />
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="description-colum">Domain</label>
                                    <select name="domain" required class="form-control select2">
                                        <option value="" selected disabled>Select Domain</option>
                                        <option value="1" {{ old('domain') == 1 ? 'selected' : '' }}>MCC</option>
                                        <option value="2" {{ old('domain') == 2 ? 'selected' : '' }}>Area Office
                                        </option>
                                        <option value="3" {{ old('domain') == 3 ? 'selected' : '' }}>Plant</option>
                                    </select>
                                    @error('doamin')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="description-colum">Categories</label>
                                    <select name="category_id" required class="form-control select2">
                                        <option value="" selected disabled>Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->_id }}">{{ $category->category_name }}</option>
                                        @endforeach

                                    </select>
                                    @error('doamin')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="description-colum">Price</label>
                                    <input type="number" required min="1" max="1000" name="price"
                                        value="{{ old('price') }}" class="form-control" placeholder="Price">
                                    @error('price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-1">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                {{--            <div class="modal-footer"> --}}
                {{--                <input class="btn btn-primary" type="submit" name="submit" value="save" id="updatePriceBtn" onclick="savePrice()"> --}}
                {{--            </div> --}}
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
                        <h1 class="mb-1">Update Source</h1>
                    </div>

                    <form class="form" action="#" id="editForm" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="edit_name">Name</label>
                                    <input type="text" required id="edit_name" class="form-control"
                                        placeholder="Name" name="name" value="" />
                                    <span class="text-danger edit_name"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="edit_description">Description</label>
                                    <input type="text" id="edit_description" class="form-control" required
                                        placeholder="Description" name="description" value="" />
                                    <span class="text-danger edit_description"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="categories-colum">Categories</label>
                                    <select name="category_id" id="edit_category" required class="form-control select2">
                                        <option value="" selected disabled>Select Category</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->_id }}">{{ $cat->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="description-colum">Domain</label>
                                    <select name="domain" id="edit_domain" required class="form-control select2">
                                        <option value="" selected disabled>Select Domain</option>
                                        <option value="1">MCC</option>
                                        <option value="2">Area Office</option>
                                        <option value="3">Plant</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" id="updateForm" class="btn btn-primary me-1">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                {{--            <div class="modal-footer"> --}}
                {{--                <input class="btn btn-primary" type="submit" name="submit" value="save" id="updatePriceBtn" onclick="savePrice()"> --}}
                {{--            </div> --}}

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
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script>

@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script>
        // permissions checks variables
        editData = "@php echo Auth::user()->can('Edit Source Type') @endphp";
        deleteData = "@php echo Auth::user()->can('Delete Source Type') @endphp";

        if (editData == '' && deleteData == '') {
            showActioncolumn = false;
        } else {
            showActioncolumn = true;
        }

        $('#supplier_type_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('source-type.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'category_id',
                    name: 'category_id'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action'
                }
            ],
            columnDefs: [{
                // Actions
                targets: 5,
                title: 'Action',
                orderable: false,
                sortable: false,
                visible: showActioncolumn
            }, {
                targets: 3,
                orderable: false,
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

        $(document).ready(function() {
            @if ($errors->any())
                $('#addModal').modal('show')
            @endif
        });


        function editRecord(id, name, description, domain, category_id) {
            $("#id").val(id);
            $("#edit_name").val(name);
            $("#edit_description").val(description);
            $("#edit_category").val(category_id);
            $("#edit_domain").val(domain);
        }



        $(document).on('submit', '#editForm', function(e) {
            $('.text-danger').text('')
            e.preventDefault();
            let data = $(this).serialize();
            console.log(data)
            $.ajax({
                url: '{{ route('source.type.update') }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#supplier_type_table').DataTable().ajax.reload();
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
    </script>
@endsection
