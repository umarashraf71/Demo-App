
@extends('layouts/contentLayoutMaster')

@section('title', 'Incentive Types List')

@section('vendor-style')
{{-- vendor css files --}}
<link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
@endsection


@section('content')
    <style>
        #is_test_base
        {
            width: 1.2rem;
            height: 1.2rem;
        }
    </style>
<!-- Column Search -->
<section id="column-search-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Incentive Types</h4>
          @can('Create Incentive Type')
                 <a class="add-new-btn btn btn-primary mt-2 mr_30px" href="#" onclick="addForm()" data-bs-toggle="modal" data-bs-target="#addModal">Add</a>
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
        <div class="card-datatable">
          <table class="table" id="supplier_incentives">
            <thead>
              <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Description</th>
                <th>Qa Test</th>
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
                    <h1 class="mb-1"><span class="form_title">Add</span> Incentive Type</h1>
{{--                <p>against collection points and source types.</p>--}}
                </div>
                <form action="{{route('incentives.type.store')}}" method="POST"  id="form">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{old('id','')}}">
                    <div class="row">
                    <div class="col-12">
                        <label class="form-label" for="modalPermissionName">Name *</label>
                        <input  id="name" type="text" value="{{old('name')}}" class="form-control" name="name" placeholder="Incentive Name">
                        @error('name')
                             <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="modalPermissionName">Description</label>
                        <input id="description" type="text" value="{{old('description')}}" class="form-control" name="description" placeholder="Description">
                        @error('description')
                             <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="modalPermissionName">From *</label>
                        <input id="from" min="{{date('Y-m-d')}}" type="date" value="{{old('from')}}" class="form-control" name="from" placeholder="From">
                        @error('from')
                             <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="To">To</label>
                        <input id="to" min="{{date('Y-m-d')}}" type="date" value="{{old('to')}}" class="form-control" name="to" placeholder="To">
                        @error('to')
                             <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label cursor-pointer" for="is_test_base">Is Test Base Incentive?  &nbsp
                            <input type="checkbox" class="mt-2 cursor-pointer" id="is_test_base" value="1" {{old('is_test_base')==1?'checked':''}}  name="is_test_base">
                        </label>
                        @error('is_test_base')
                             <span class="text-danger">{{$message}}</span>
                         @enderror
                    </div>
                    <div class="col-12" style="display: {{$errors->has('qa_test') || (old('is_test_base') && old('qa_test'))?'block':'none'}}" id="qa_test_div">
                        <label class="form-label">QA Test *</label>
                        <select name="qa_test" class="qa_test form-control">
                            <option value="" selected disabled>Select Test</option>
                            @foreach($tests as $test)
                                <option {{old('qa_test')==$test->id?'selected':''}} value="{{$test->id}}">{{$test->qa_test_name}}</option>
                            @endforeach
                        </select>
                        @error('qa_test')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                      </div>
                    </div>
                        <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary mt-2 me-1">Save</button>
                            <button type="reset" class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal" aria-label="Close">
                                Close
                            </button>
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
editData = "@php echo Auth::user()->can('Edit Incentive Type') @endphp";


if(editData == '' ) {
    showActioncolumn = false;
} else {
    showActioncolumn = true;
}

$('#supplier_incentives').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('incentives.types') }}",
    columns: [
    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
    {data: 'name', name: 'name'},
    {data: 'description', name: 'description'},
    {data: 'qa_test', name: 'qa_test'},
    { data: 'status' },
    {data: 'action', name: 'action'}
    ],
    columnDefs: [
    {
        // Actions
        targets:5,
        orderable: false,
        sortable: false,
        visible:showActioncolumn
    },{
        // Actions
        targets:4,
        orderable: false,
        sortable: false,
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


$( document ).ready(function() {
    @if($errors->any())
    $('#addModal').modal('show');
    var id = $('#id').val();
    if(id){
        $('#form').attr('action',"{{route('incentives.type.update')}}");
        $('.form_title').text('Update');
    }else{
        $('#form').attr('action',"{{route('incentives.type.store')}}");
        $('.form_title').text('Add');
    }
    @endif
});
function addForm(){
    $('#form').attr('action',"{{route('incentives.type.store')}}");
    $('.form_title').text('Add');
    $('#id').val('');
    $('#form')[0].reset();
}
function editForm(id,name,description,from,to,is_test_base,qa_test_id){
    $('#form')[0].reset();
    $('#form').attr('action',"{{route('incentives.type.update')}}");
    $('#name').val(name);
    $('#description').val(description);
    $('#from').val(from);
    $('#id').val(id);
    $('#to').val(to);
    $('.qa_test').val(qa_test_id);
    if(is_test_base>0){
        $('#is_test_base').attr('checked',true);
        $('#qa_test_div').show()
    }else{
        $('#is_test_base').removeAttr('checked');
        $('#qa_test_div').hide()
    }

    $('#addModal').modal('show');
    $('.form_title').text('Update');
}

function delRecord(url) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "get",
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function (response) {
                    if (response.success) {
                        $('#supplier_incentives').DataTable().ajax.reload();
                        Swal.fire(
                            'Done!',
                            response.message,
                            'success'
                        )
                    } else {
                        Swal.fire(
                            'Oops!',
                            response.message,
                            'error'
                        )
                    }
                }
            });
        }
    })
}
function statusUpdate(element,id) {
    if($(element).prop('checked') == true)
    {
        var status = 1;
    }
    else
    {
        var status = 0;
    }
    var fd = new FormData();
    fd.append('status', status);
    fd.append('id', id);
    $.ajax({
        type: "POST",
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: fd,
        url: '{{route('incentives.type.status.update')}}',
        success: function (response) {
            if (response.success) {
                Swal.fire(
                    'Done!',
                    response.message,
                    'success'
                )
            } else {
                Swal.fire(
                    'Oops!',
                    response.message,
                    'error'
                )
            }
        }
    });
}

    $('#is_test_base').change(function() {
        $('.qa_test').find('option:eq(0)').prop('selected', true);
        if(this.checked) {
            $('#qa_test_div').show(300)
        }else{
           $('#qa_test_div').hide(300)
        }
    });


</script>
@endsection
