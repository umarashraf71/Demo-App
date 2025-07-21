@extends('layouts/contentLayoutMaster')

@section('title', 'Approvals')

@section('vendor-style')
  <!-- Vendor css files -->
  <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
@section('page-style')
  <!-- Page css files -->
  <script src="{{ asset('/js/custom.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/form-validation.css') }}">
@endsection

@section('content')


<!-- Permission Table -->
<div class="card">
    <div class="row border-bottom py-2 ">
        <div class="col-3">
            <h4 class="card-title pt-1 my-auto ms-2">Approvals</h4>
        </div>

    </div>
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
    <table class="table" id="workflow_table">
      <thead class="table-light">
        <tr>
          <th>Id</th>
            <th>Document Type</th>
            <th>Type</th>
          <th>Created By</th>
{{--            <th>Approved/Rejected BY</th>--}}
            <th>Steps</th>
            <th>Status</th>
            <th>Created Date</th>
{{--          <th>Actions</th>--}}
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
  <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>

@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset('/js/custom.js') }}"></script>
  <script type="text/javascript">
   // permissions checks variables
  {{-- editPermission = "@php echo Auth::user()->can('edit Workflow') @endphp";--}}
  {{--deletePermission = "@php echo Auth::user()->can('delete Workflow') @endphp";--}}

  {{--if(editPermission == '' && deletePermission == '')--}}
  {{--showActioncolumn = false;--}}
  {{--else--}}
  {{--showActioncolumn = true;--}}

  $('#workflow_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: 'workflow-approvals', // JSON file to add data
      columns: [
        // columns according to JSON
          {  data: 'code' },
          {  data: 'document_type' },
          {  data: 'type' },
          { data: 'created_by' },
          {data: 'steps', name: 'steps'},
          { data: 'status' },
          { data: 'created_at' },
          // { data: 'action' }
      ],
      // columnDefs: [
      //   {
      //     // Actions
      //     targets:8,
      //     title: 'Actions',
      //       orderable: false,
      //       sortable: false,
      //     // visible:showActioncolumn
      //   }
      // ],
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


  function updateStatus(url) {
      $.ajax({
          url:url,
          method:'get',
          success : function(response){
              if(response.success)
              {
                  Swal.fire(
                      'Done!',
                      response.message,
                      'success'
                  )
                  $('#workflow_table').DataTable().ajax.reload();
              } else
              {
                  Swal.fire(
                      'Error Occured!',
                      response.message,
                      'success'
                  )
              }
          }
      });

  }

</script>
@endsection

