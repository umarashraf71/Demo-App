
@extends('layouts/contentLayoutMaster')

@section('title', 'Suppliers Records')

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
          <h4 class="card-title">Suppliers</h4>
          @can('Create Supplier')
            <a class="add-new-btn btn btn-primary mt-2 mr_30px" href="{{route("supplier.create")}}">Add New</a>
          @endcan
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
          <table class="table " id="supplier_table">
            <thead>
              <tr>
                <th>No.</th>
                <th>Supplier Code</th>
                <th>Name</th>
                <th>Father Name</th>
                <th>Contact</th>
                <th>Type</th>
                <th>Area Office</th>
                <th>Status</th>
                <th>Payment Process</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
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
<script src="{{ asset('/js/custom.js') }}"></script>
<script>
// permissions checks variables
editData = "@php echo Auth::user()->can('Edit Supplier') @endphp";
deleteData = "@php echo Auth::user()->can('Delete Supplier') @endphp";

if(editData == '' && deleteData == '')
{
    showActioncolumn = false;
}
else
{
    showActioncolumn = true;
}

$('#supplier_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('supplier.index') }}",
    columns: [
    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
    {data: 'code', name: 'code'},
    {data: 'name', name: 'name'},
    {data: 'father_name', name: 'father_name'},
    {data: 'contact', name: 'Contact'},
    {data: 'supplier_type_id', name: 'supplier_type_id'},
    {data: 'area_office', name: 'area_office'},
    {data: 'status', name: 'status'},
    {data: 'payment_process', name: 'payment_process'},
    { data: 'action' }
    ],
    columnDefs: [
    {
        targets:8,
        title: 'Actions',
        orderable: false,
        sortable: false,
        visible:showActioncolumn
    }
    ],
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


function setPrice(id,price){
   $('#price').val(price)
   $('#supplier_id').val(id)
   $('#priceModal').modal('show')
}

function updatePaymentProcessStatus(element, id) {
    var status = 0;
    if($(element).prop('checked') == true) {
        status = 1;
    }

    $.ajax('{{route('payment.process.update.status')}}', {
        type: 'get',
        data: { id: id, status:status },
        success: function (data) {
          if(data.success== true){
              Swal.fire(
                  'Done!',
                  data.message,
                  'success'
              );
          }else{
                Swal.fire(
                'Error!',
                data.message,
                'warning'
                );
            }
        },
        error: function (jqXhr, textStatus, errorMessage) {
            alert('Error Occurred')
        }
    });
}




</script>
@endsection
