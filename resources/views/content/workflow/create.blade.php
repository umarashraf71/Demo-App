@extends('layouts/contentLayoutMaster')

@section('title', 'Add New Workflow')
@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection
@section('content')
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Add Workflow</h4>
        </div>
        <div class="card-body">
            @if ($message = Session::get('success'))
            <div class="demo-spacing-0 my-2">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body">{{ $message }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif
            @if ($errorMessage = Session::get('errorMessage'))
            <div class="demo-spacing-0 my-2">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body">{{ $errorMessage }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif
          <form class="form" action="{{ route('workflow.store') }}" method="POST">
            @csrf
            <div class="row">
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="name-column">Name </label><span class="text-danger">*</span>
                  <input
                    type="text"
                    id="name-column"
                    class="form-control"
                    placeholder="Name"
                    name="name"
                    value="{{ old('name')}}"
                  />
                  @error('name')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>

                @php($document_types = \App\Models\Workflow::$types)
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="document_type">Document Type</label><span class="text-danger">*</span>
                  <select class="select2 form-select"  name="document_type" id="document_type">
                    <option value="" selected disabled>Select Document Type</option>
                      @foreach($document_types as $key=>$dt)
                      <option value="{{$key}}" {{old('document_type')==$key?'selected':''}}>
                          {{$dt['name']}}
                      </option>
                     @endforeach
                  </select>
                    @error('document_type')
                         <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>
              {{--  <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="role_ids">Roles<span class="text-danger">*</span><b style="color: red">(Select workflow Approval in Precedence Order)</b></label>
                  <select multiple="multiple"
                  class="select2 form-select" data-placeholder="Select Roles"
                  id="select2-multiple"
                  name="role_ids[]">
                  @foreach ($roles as $role)
                      <option value="{{ $role['_id'] }}" {{old("role_ids") && in_array($role['_id'],old("role_ids"))  ? "selected":"" }} >{{ucfirst($role['name'])}}</option>
                  @endforeach
              </select>
                    @error('role_ids')
                      <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>  --}}
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="role_ids">Roles<span class="text-danger">*</span><b style="color: red">(Select workflow Approval in Precedence Order)</b></label>
                  <select multiple="multiple"
                  class="select2 form-select" data-placeholder="Select Roles"
                  id="select2-multiple"
                  name="role_ids[]" id="select2-multiple" required ></select>
              </div>
              </div>

                <div class="col-12" style="margin-left: 15px">
                    <div id="workflow_digram">
                    </div>
                </div>
              {{-- <div class="col-md-12 col-12">
                <b id="roles_presentation"></b>
              </div> --}}
                @can('Create Workflow')
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary me-1">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                    <a class="btn btn-outline-secondary cancel-btn" href="{{ route('workflow.index') }}" >Cancel</a>
                  </div>
               @endcan
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Basic Floating Label Form section end -->
@endsection
@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  {{-- <script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script> --}}
  <script type="text/javascript">
  $('#select2-multiple').select2();
  //event to re arrange dropdown not to sort alphabatecally
  $("#select2-multiple").on("select2:select", function (evt) {
  var element = evt.params.data.element;
  var $element = $(element);
  $element.detach();
  $(this).append($element);
  $(this).trigger("change");
});
  var arrow_image = '{{asset('images/workflow/arrow.png')}}';
  var user_image = '{{asset('images/workflow/user.png')}}';
  $('#select2-multiple').on('change', function() {
      var selObj = document.getElementById('select2-multiple');
      var data = `<div class="row ml-1">`;
      var count = 0;
      var total_selected = $("#select2-multiple :selected").length;

      for (let i=0; i<selObj.options.length; i++) {
          if (selObj.options[i].selected) {
              count++;
              data += `<div class='col-1 p-0'  style="width: 75px"><img class="text-center" src="${user_image}" width="70px"/>
                     <div class="text-center">${selObj.options[i].text}</div></div>`;
              if(count<total_selected){
                  data += `<div class='col-1 p-0 my-auto' style="width: 50px"><img  src="${arrow_image}" width="50px"/></div>`;
              }
          }
      }
      data += `</div>`;
      $('#workflow_digram').html(data);
      $(this).hide(0);
  });


/*
  {{--function getRoles(id){--}}
  {{--    // $("#"+div_id+" > option").remove();--}}
  {{--    // $("#edit_diagram,diagram").html('');--}}
  {{--    $.ajax({--}}
  {{--        url:'{{route('ajax.get.roles')}}',--}}
  {{--        method:'get',--}}
  {{--        data: {'id':id},--}}
  {{--        success : function(response){--}}
  {{--            if(response.success) {--}}
  {{--                var data = response.data;--}}
  {{--                $.each(data, function( index, value ) {--}}
  {{--                    $("#"+div_id).append("<option value='"+value._id+"' >"+value.name+"</option>");--}}
  {{--                });--}}
  {{--                // $.each(selected_cps, function( index, value ) {--}}
  {{--                //     var element =  $("#edit_collection_point option[value="+value+"]");--}}
  {{--                //     $(element).detach();--}}
  {{--                //     $("#edit_collection_point").append(element);--}}
  {{--                //     $(element).attr("selected", true);--}}
  {{--                //     $("#edit_collection_point").trigger("change");--}}
  {{--                // });--}}
  {{--            }--}}
  {{--        }--}}
  {{--    });--}}
  {{--}--}} */

  $(document).ready(function() {
    $('#document_type').select2();
    $('#select2-multiple').select2({ placeholder : "Select Roles"});
});


$('#document_type').change(function (e) {

  var value = $(this).find(':selected').val();

  $.ajax("{{ route('get.workflow.documentType.roles') }}", {
    type: 'get',
    data: {
        id: value
    },
    success : function(response){
      if(response.success)
      {
        $('#select2-multiple').html('');
        $.each(response.roles , function(index, val) {
          $('#select2-multiple').append('<option value="'+val['_id']+'">'+val['name']+'</option>');
        });
      }
      $('#select2-multiple').trigger('change');
    }
  }); 
});

</script>
@endsection
