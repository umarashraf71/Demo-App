@extends('layouts/contentLayoutMaster')

@section('title', 'User List')

@section('vendor-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
@endsection

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/form-validation.css') }}">
@endsection

@section('content')
<!-- users list start -->
<section class="app-user-list">
  <div class="row">
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bolder mb-75">{{@$total_users}}</h3>
            <span>Total Users</span>
          </div>
          <div class="avatar bg-light-primary p-50">
            <span class="avatar-content">
              <i data-feather="user" class="font-medium-4"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bolder mb-75">{{@$active_users}}</h3>
            <span>Active Users</span>
          </div>
          <div class="avatar bg-light-success p-50">
            <span class="avatar-content">
              <i data-feather="user-check" class="font-medium-4"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bolder mb-75">{{@$inactive_users}}</h3>
            <span>Inactive Users</span>
          </div>
          <div class="avatar bg-light-warning p-50">
            <span class="avatar-content">
              <i data-feather="user-x" class="font-medium-4"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- list and filter start -->
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
    <div class="card-body border-bottom">
      <div class="row">
        <div class="col-md-12">
          <h3 style="display:inline;">Users List</h3>
          @can('Create Users')
              <a class="add-new btn btn-primary mt-50" style="float: right;display:inline;" href="{{route("users.create")}}">Add User</a>
          @endcan
        </div>
      </div><br>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table" id="users_table">
        <thead class="table-light">
          <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>User Name</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
  <!-- list and filter end -->
</section>
<!-- users list ends -->
@endsection

@section('vendor-script')
  {{-- Vendor js files --}}
  <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
  <script src="{{ asset('vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
  <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
  <script src="{{ asset('vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
@endsection

@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset('/js/custom.js') }}"></script>
  <script type="text/javascript">
  // permissions checks variables
  let editPermission = "@php echo Auth::user()->can('Edit Users') @endphp";
 let deletePermission = "@php echo Auth::user()->can('Delete Users') @endphp";

  if(editPermission == '' && deletePermission == '')
  showActioncolumn = false;
  else
  showActioncolumn = true;

    $('#users_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'users', // JSON file to add data
        columns: [
          // columns according to JSON
          { data: 'name' },
          { data: 'email' },
          { data: 'user_name' },
          { data: 'role_id' },
          { data: 'status' },
          { data: 'action' }
        ],
        columnDefs: [
          {
            // Actions
            targets: 5,
            title: 'Actions',
            orderable: false,
            visible: showActioncolumn
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
