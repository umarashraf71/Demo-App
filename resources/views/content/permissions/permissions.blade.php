@extends('layouts/contentLayoutMaster')

@section('title', 'Permission')

@section('vendor-style')
  <!-- Vendor css files -->
  <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/form-validation.css') }}">
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <h3 style="display:inline;">Permissions List</h3>
    @can('Create Permissions')
    <button class="add-new btn btn-primary mb-50" style="float: right;display:inline;"
    data-bs-toggle="modal" data-bs-target="#addPermissionModal">Add Permission</button>
    @endcan
  </div>
<!-- Permission Table -->
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="table" id="permissions_table">
      <thead class="table-light">
        <tr>
          <th>Name</th>
          <th>Module</th>
          <th>Order#</th>
          <th>Created Date</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>

</div>
<!--/ Permission Table -->

@include('content/_partials/_modals/modal-add-permission')
@include('content/_partials/_modals/modal-edit-permission')
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
  editPermissionaccess = "@php echo Auth::user()->can('Edit Permissions') @endphp";
  deletePermission = "@php echo Auth::user()->can('Delete Permissions') @endphp";

  if(editPermissionaccess == '' && editPermissionaccess == '')
  showActioncolumn = false;
  else
  showActioncolumn = true;

  var addPermissionForm = $('#addPermissionForm');

  // jQuery Validation
  // --------------------------------------------------------------------
  if (addPermissionForm.length) {
    addPermissionForm.validate({
      rules: {
        modalPermissionName: {
          required: true
        }
      }
    });
  }

  // reset form on modal hidden
  $('.modal').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
  });

  $('#permissions_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: 'permissions',
      columns: [
        // columns according to JSON
        { data: 'name' },
        { data: 'module' },
        { data: 'order' },
        { data: 'created_at' },
        { data: 'action' }
      ],
      columnDefs: [
        {
          // Actions
          targets: 4,
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
 // add permission
 $(document).on('submit','#addPermissionForm',function(e){
  e.preventDefault();
  let data  = $(this).serialize();
  $.ajax({
    url:"permissions",
    method:'POST',
    data: data,
    success : function(response){
      if(response.success)
      {
        $('#permissions_table').DataTable().ajax.reload();
        $('#addPermissionModal').modal('toggle');
        Swal.fire(
          'Done!',
          response.message,
          'success'
        )
      } else
      {
        $('#addPermissionModal').modal('toggle');
        Swal.fire(
          'Oops!',
          response.message,
          'error'
        )
      }
    }
  });
})
// edit permission
function editPermission(id)
{
  $.ajax({
    url:'permissions/'+id+'/edit',
    method:'GET',
    success : function(response){
      if(response.success)
      {
        $("#permissionId").val(response.record._id);
        $("#editPermission").val(response.record.name);
        $("#editmodalPermissionModulename").val(response.record.module);
        $("#editOrder").val(response.record.order);
        $('#editPermissionModal').modal('toggle');
      } else
      {
        Swal.fire(
          'Oops!',
          response.message,
          'error'
        )
      }
    }
  });
}
//update permission
$(document).on('submit','#editPermissionForm',function(e){
  e.preventDefault();
  let data  = $(this).serialize();
  $.ajax({
    url:'permissions/'+$("#permissionId").val()+'/',
    method:'PUT',
    data: data,
    success : function(response){
      if(response.success)
      {
        $('#permissions_table').DataTable().ajax.reload();
        $('#editPermissionModal').modal('toggle');
        Swal.fire(
          'Done!',
          response.message,
          'success'
        )
      } else
      {
        $('#editPermissionModal').modal('toggle');
        Swal.fire(
          'Oops!',
          response.message,
          'error'
        )
      }
    }
  });
})
</script>
@endsection

