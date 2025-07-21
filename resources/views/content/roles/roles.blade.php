@extends('layouts/contentLayoutMaster')

@section('title', 'Roles')

@section('vendor-style')
  <!-- Vendor css files -->
  <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <script src="{{ asset('/js/custom.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/form-validation.css') }}">
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <h3 style="display:inline;">Roles List</h3>
    @can('Create Roles')
    <a class="add-new btn btn-primary mt-50" style="float: right;display:inline;" href="{{route("roles.create")}}">Add Role</a>
    @endcan
  </div>
</div><br>

<!-- Permission Table -->
<div class="card">
  @if ($message = Session::get('error'))
  <div class="demo-spacing-0 my-2">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <div class="alert-body">{{ $message }}</div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  @endif
  @if ($message = Session::get('success'))
  <div class="demo-spacing-0 my-2">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <div class="alert-body">{{ $message }}</div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  @endif
  <div class="card-datatable table-responsive">
    <table class="table" id="roles_table">
      <thead class="table-light">
        <tr>
          <th>Name</th>
          <th>Access Level</th>
          <th>Created Date</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Permission Table -->
@endsection
@section('vendor-script')
  <!-- Vendor js files -->
  <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
  <script src="{{ asset('vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
  <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
  <script src="{{ asset('vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset('/js/custom.js') }}"></script>
  <script type="text/javascript">
   // permissions checks variables 
   editPermission = "@php echo Auth::user()->can('Edit Roles') @endphp";
  deletePermission = "@php echo Auth::user()->can('Delete Roles') @endphp";
  
  if(editPermission == '' && deletePermission == '')
  showActioncolumn = false;
  else
  showActioncolumn = true;

  $('#roles_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: 'roles',
      columns: [
        // columns according to JSON
        { data: 'name' },
        { data: 'access_level' },
        { data: 'created_at' },
        { data: 'action' }
      ],
      columnDefs: [
        {
          // Actions
          targets: 3,
          title: 'Actions',
          orderable: false,
          visible:showActioncolumn
        },
        {
          targets: 1,
          data: "access_level",
          render: function ( data, type, row, meta ) {
            if(data == 1)
            return 'Collection Points (FFL)';
            if(data == 2)
            return 'Area Office';
            if(data == 3)
            return 'Zones';
            if(data == 4)
            return 'Sections';
            if(data == 5)
            return 'Departments';
            if(data == 6)
            return 'Plant';
        }
      }
      ],
      order: [[1, 'asc']],
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
</script>
@endsection

 