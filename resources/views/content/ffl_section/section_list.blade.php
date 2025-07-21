
@extends('layouts/contentLayoutMaster')

@section('title', 'Section List')

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
          <h4 class="card-title">Sections List</h4>
      
          <a class="add-new-btn btn btn-primary mt-2 mr_30px" href="{{route("section.create")}}">Add New</a>
     
        </div>
        @if ($message = Session::get('success'))
        <div class="demo-spacing-0 m-2">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="alert-body">{{ $message }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif
          <div class="card-datatable table-responsive">
              <table class="table" id="section_table">
            <thead>
              <tr>
                <th>No.</th>
                <th>Section Code</th>
                <th>Name</th>
                <th>Department</th>
                <th>Address</th>

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
<!--/ Column Search -->
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
editData = "@php echo Auth::user()->can('Edit Section') @endphp";
deleteData = "@php echo Auth::user()->can('Delete Section') @endphp";



  showActioncolumn = true;


$('#section_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('section.index') }}",
    columns: [
    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
    {data: 'code', name: 'code'},
    {data: 'name', name: 'name'},
    {data: 'department_id', name: 'department_id'},
    {data: 'address', name: 'address'},
    {data: 'status', name: 'status'},
    { data: 'action' }
    ],
    columnDefs: [
    {
        // Actions
        targets: 6,
        title: 'Actions',
        orderable: false,
        visible:showActioncolumn
    },{
        targets: 5,
        orderable: false,
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
