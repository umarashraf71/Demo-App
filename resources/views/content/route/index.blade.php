
@extends('layouts/contentLayoutMaster')

@section('title', 'Routes List')

@section('vendor-style')
{{-- vendor css files --}}
<link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

@endsection
@section('content')
    <style>
        .fa-truck{
            font-size: 26px;
        }
    </style>
<!-- Column Search -->
<section id="column-search-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Routes</h4>
          @can('Create Route Plan')
             <a class="add-new-btn btn btn-primary mt-2 mr_30px" onclick="formReset('addForm')" href="#" data-bs-target="#addModal" data-bs-toggle="modal">Add New</a>
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
          <table class="table" id="route_table">
            <thead>
              <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Route</th>
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
                    <h1 class="mb-1">Add Route</h1>
{{--                    <p>against vehicles and collection points.</p>--}}
                </div>
                <form action="{{route('routes.save')}}" method="POST" class="row" id="addForm">
                    @csrf
                    <div class="col-12">
                        <label class="form-label" for="name">Route Name</label><span class="text-danger">*</span>
                        <input required type="text" id="name" class="form-control" name="name" placeholder="Name">
                        <span class="text-danger error-message"></span>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="name">Area Office</label><span class="text-danger">*</span>
                        <select name="area_office"  onchange="getCPS(this.value,'collection_point')" class="select2 form-select" data-placeholder="Select area office"
                                id="area_office" required>
                              <option  disabled value="" selected>Select Area Office</option>
                            @foreach($areaOffices as $data)
                                <option value="{{$data->id}}">{{ucfirst($data->name)}}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-message"></span>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="collection_point">Collection Points</label><span class="text-danger">*</span>
                        <select name="collection_point[]" multiple="multiple"  class="select2 form-select" data-placeholder="Select Cp"
                                id="collection_point">
{{--                            <option  disabled>Select Collection Points</option>--}}
{{--                            @foreach($cps as $data)--}}
{{--                                <option value="{{$data->id}}">{{ucfirst($data->name)}}</option>--}}
{{--                            @endforeach--}}
                        </select>
                        <span class="text-danger error-message"></span>
                    </div>

                    <div class="col-12" >
                        <div id="digram">
                        </div>
                    </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary mt-2 me-1">Save</button>
                            <button type="reset" class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal" aria-label="Close">
                                Close
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-sm-5 pb-5">
                <div class="text-center mb-2">
                    <h1 class="mb-1">Update Route</h1>
{{--                    <p>against vehicles and collection points.</p>--}}
                </div>
                <form action="{{route('routes.update')}}" method="POST" class="row" id="editForm">
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <div class="col-12">
                        <label class="form-label" for="edit_name">Route Name</label><span class="text-danger">*</span>
                        <input required type="text" id="edit_name" class="form-control" name="name" placeholder="Name">
                        <span class="text-danger error-message"></span>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="edit_area_office">Area Office</label><span class="text-danger">*</span>
                        <select name="area_office"  onchange="getCPS(this.value,'edit_collection_point')" class="select2 form-select" data-placeholder="Select area office"
                                id="edit_area_office" required>
                            <option  disabled value="" selected>Select Area Office</option>
                            @foreach($areaOffices as $data)
                                <option value="{{$data->id}}">{{ucfirst($data->name)}}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-message"></span>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="edit_collection_point">Collection Points</label><span class="text-danger">*</span>
                        <select name="collection_point[]" multiple="multiple"
                                class="select2 form-select"
                                id="edit_collection_point">
{{--                            <option  disabled>Select Collection Points</option>--}}
{{--                            @foreach($cps as $data)--}}
{{--                                <option value="{{$data->id}}">{{ucfirst($data->name)}}</option>--}}
{{--                            @endforeach--}}
                        </select>
                        <span class="text-danger error-message"></span>
                    </div>

                    <div class="col-12" >
                        <div id="edit_diagram">
                        </div>
                    </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary mt-2 me-1">Save</button>
                            <button type="reset" class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal" aria-label="Close">
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
<script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>


@endsection

@section('page-script')
<script src="{{ asset('/js/custom.js') }}"></script>
<script>

    $('#collection_point,#edit_collection_point').select2({
        placeholder: "Select Collection Points",
        allowClear: true
    });
    //event to re arrange dropdown not to sort alphabatecally
    $('#collection_point,#edit_collection_point').on("select2:select", function (evt) {
        var element = evt.params.data.element;
        var $element = $(element);
        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
    });


// permissions checks variables
editData = "@php echo Auth::user()->can('Edit Route Plan') @endphp";
deleteData = "@php echo Auth::user()->can('Delete Route Plan') @endphp";

if(editData == '' && deleteData == '')
{
    showActioncolumn = false;
}
else
{
    showActioncolumn = true;
}

$('#route_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('routes.index') }}",
    columns: [
    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
    {data: 'name', name: 'name'},
    {data: 'collection_points', name: 'collection_points'},
    {data: 'status', name: 'status'},
    { data: 'action' }
    ],
    columnDefs: [
    {
        // Status
        targets: 3,
        title: 'Status',
        orderable: false,

    },{
        // Actions
        targets:4,
        title: 'Actions',
        orderable: false,
        searchable: false,
        visible:showActioncolumn
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
    var arrow_image = '{{asset('images/workflow/arrow.png')}}';
    var user_image = '{{asset('images/workflow/car.png')}}';
    $('#collection_point').on('change', function() {
        var selObj = document.getElementById('collection_point');
        var data = `<div class="row mx-0">`;
        var count = 0;
        var total_selected = $("#collection_point :selected").length;
        for (let i=0; i<selObj.options.length; i++) {
            if (selObj.options[i].selected) {
                count++;
                data += `<div class='col-1 p-0 mt-1 text-center'  style="width: 55px"><span >${count}</span><div><i class="fa-solid fa-truck text-primary"></i></div>
                     <div class="text-center">${selObj.options[i].text}</div></div>`;
                if(count<total_selected){
                    data += `<div class='col-1 p-0 my-auto' style="width: 35px"><img  src="${arrow_image}" width="30px"/></div>`;
                }
            }
        }
        data += `</div>`;
        $('#digram').html(data);
        $(this).hide(0);
    });

    $('#edit_collection_point').on('change', function() {
        var selObj = document.getElementById('edit_collection_point');
        var data = `<div class="row mx-0">`;
        var count = 0;
        var total_selected = $("#edit_collection_point :selected").length;
        for (let i=0; i<selObj.options.length; i++) {
            if (selObj.options[i].selected) {
                count++;
                data += `<div class='col-1 p-0 mt-1 text-center'  style="width: 58px"><span >${count}</span><div><i class="fa-solid fa-truck text-primary"></i></div>
                     <div class="text-center">${selObj.options[i].text}</div></div>`;
                if(count<total_selected){
                    data += `<div class='col-1 p-0 my-auto' style="width: 35px"><img  src="${arrow_image}" width="30px"/></div>`;
                }
            }
        }
        data += `</div>`;
        $('#edit_diagram').html(data);
        $(this).hide(0);
    });

    $(document).on('submit','#addForm',function(e){
        $(".error-message").text('');
        e.preventDefault();
        let data  = $(this).serialize();
        let url = $(this).attr('action');
        let  method = $(this).attr('method');
        $.ajax({
            url:url,
            method:method,
            data: data,
            success : function(response){
                if(response.success)
                {
                    $('#addModal').modal('toggle');
                    Swal.fire(
                        'Done!',
                        response.message,
                        'success'
                    )
                    $('#addForm')[0].reset();
                    $('#route_table').DataTable().ajax.reload();
                } else
                {
                    $("#"+response.key).nextAll('.error-message').text(response.message);
                }
            }
        });
    })
    $(document).on('submit','#editForm',function(e){
        $(".error-message").text('');
        e.preventDefault();
        let data  = $(this).serialize();
        let url = $(this).attr('action');
        let  method = $(this).attr('method');
        $.ajax({
            url:url,
            method:method,
            data: data,
            success : function(response){
                if(response.success)
                {
                    $('#editModal').modal('toggle');
                    Swal.fire(
                        'Done!',
                        response.message,
                        'success'
                    )
                    $('#editForm')[0].reset();
                    $('#route_table').DataTable().ajax.reload();
                } else
                {
                    $("#edit_"+response.key).nextAll('.error-message').text(response.message);
                }
            }
        });
    })

    function editRecord(id) {
       $("#edit_collection_point > option").removeAttr("selected");
        $.ajax({
            url:'{{route('routes.show')}}',
            method:'get',
            data: {'id':id},
            success : function(response){
                if(response.success) {
                    var data = response.data;
                    $('#edit_name').val(data.name)
                    $('#edit_area_office').val(data.area_office_id)
                    $('#id').val(id)
                    $('#editModal').modal('toggle');
                    getCPS(data.area_office_id,'edit_collection_point',data.collection_points)

                    var selObj = document.getElementById('edit_collection_point');
                    var data = `<div class="row mx-0">`;
                    var count = 0;
                    var total_selected = $("#edit_collection_point :selected").length;

                    for (let i=0; i<selObj.options.length; i++) {
                        if (selObj.options[i].selected) {
                            count++;
                            data += `<div class='col-1 py-0 mt-1 text-center'  style="width: 88px"><span >${count}</span><img class="text-center" src="${user_image}" width="70px"/>
                             <div class="text-center">${selObj.options[i].text}</div></div>`;
                            if(count<total_selected){
                                data += `<div class='col-1 p-0 my-auto' style="width: 50px"><img  src="${arrow_image}" width="50px"/></div>`;
                            }
                        }
                    }
                    data += `</div>`;
                    $('#edit_diagram').html(data);
                    $(this).hide(0);
                }
            }
        });
    }


    function getCPS(cp_id,div_id,selected_cps=[]) {
       $("#"+div_id+" > option").remove();
       $("#edit_diagram,diagram").html('');
       let form_type= ''
       if(div_id=='collection_point'){
           form_type= 'create';
       }
        $.ajax({
            url:'{{route('routes.get.cps')}}',
            method:'get',
            data: {'id':cp_id,'form_type':form_type},
            success : function(response){
                if(response.success) {
                    var data = response.data;
                    $.each(data, function( index, value ) {
                        // let is_selected = '';
                        // if(selected_cps.length>0 && $.inArray(value._id, selected_cps)!== -1) {
                        //     is_selected = 'selected';
                        // }
                        $("#"+div_id).append("<option value='"+value._id+"' >"+value.name+"</option>");
                    });
                    $.each(selected_cps, function( index, value ) {
                         var element =  $("#edit_collection_point option[value="+value+"]");
                         $(element).detach();
                         $("#edit_collection_point").append(element);
                         $(element).attr("selected", true);
                         $("#edit_collection_point").trigger("change");
                     });
                }
            }
        });
    }

    function formReset(id){
        $('#'+id)[0].reset();
        $("#collection_point > option").remove();
        $('#digram').html('');
    }

</script>
@endsection
