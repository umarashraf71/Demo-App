@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Workflow')
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
          <h4 class="card-title">Edit Workflow</h4>
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
          <form class="form" action="{{ route('workflow.update',$workflow->id) }}" method="POST">
            @method('PUT')
            @csrf
            <div class="row">
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="name-column">Name *</label>
                  <input
                    type="text"
                    id="name-column"
                    class="form-control"
                    placeholder="Name"
                    name="name"
                    value="{{@$workflow->name}}"
                  />
                  @error('name')
                     <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
                @php($document_types = \App\Models\Workflow::$types)
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="document_type">Document Type *</label>
                  <select class="form-control" name="document_type" id="document_type" disabled>
                      <option value="" selected disabled>Select Document Type</option>
                      @foreach($document_types as $key=>$dt)
                          <option value="{{$key}}" {{old('document_type',$workflow->document_type)==$key?'selected':''}}>
                              {{$dt['name']}}
                          </option>
                      @endforeach
                  </select>
                    @error('document_type')
                     <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="role_ids">Roles* <b style="color: red">(Select workflow Approval in Precedence Order)</b></label>
                  <select multiple="multiple"
                  class="select2 form-select"
                  id="select2-multiple" data-placeholder="Select Role"
                  name="role_ids[]">
                  @foreach ($roles as $role)
                     <option value="{{ $role['_id'] }}" {{(Input::old("role_ids") == $role['_id']  ? "selected":"") }} >{{ucfirst($role['name'])}}</option>
                  @endforeach
              </select>
                    @error('role_ids')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>

                <div class="col-12" style="margin-left: 15px">
                    <div id="workflow_digram">
{{--                        <div class="row ml-1">--}}
{{--                            <div class="col-1 p-0" style="width: 75px">--}}
{{--                                <img class="text-center" src="http://127.0.0.1:8000/images/workflow/user.png" width="70px">--}}
{{--                                <div class="">abc</div>--}}
{{--                            </div>--}}
{{--                            <div class="col-1 p-0 my-auto" style="width: 50px">--}}
{{--                                <img src="http://127.0.0.1:8000/images/workflow/arrow.png" width="50px">--}}
{{--                            </div>--}}
{{--                            <div class="col-1 p-0" style="width: 75px">--}}
{{--                                <img class="text-center" src="http://127.0.0.1:8000/images/workflow/user.png" width="70px">--}}
{{--                                <div class="">Nostrum facilis lore</div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>
                @can('Edit Workflow')
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
<script type="text/javascript">
$(document).ready(function () {
  //get dropdown options values from php
  role_ids = '<?php echo json_encode($workflow->role_ids); ?>';
  role_ids = JSON.parse(role_ids);

  $('#select2-multiple').select2();
  //select options using jquery by deattaching and selecting not seletced by php because select2 V4 sprting it alphabatecially be default
  $.each(role_ids, function( index, value ) {
    var element =  $("#select2-multiple option[value="+value+"]");
    $(element).detach();
    $("#select2-multiple").append(element);
    $(element).attr("selected", true);
    $("#select2-multiple").trigger("change");
  });
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
        $('#workflow_digram').html(data)
        $(this).hide(0);
    });


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
    $('#workflow_digram').html(data)
  });
</script>
@endsection
