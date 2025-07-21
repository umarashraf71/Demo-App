@extends('layouts/contentLayoutMaster')

@section('title', 'Edit User')

@section('vendor-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection

@section('content')
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Edit User</h4>
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
            @if ($message = Session::get('error'))
            <div class="demo-spacing-0 my-2">
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="alert-body">{{ $message }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            </div>
            @endif
          <form class="form" action="{{ route('users.update',$user->id) }}" method="POST">
            @method('PUT')
            @csrf
            <div class="row">
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="full_name">Full Name</label>
                  <input
                    type="text"
                    id="full_name"
                    class="form-control"
                    placeholder="Enter Full Name"
                    name="name"
                    value="{{@$user->name}}"
                  />
                    @error('name')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="email">Email</label>
                  <input
                    type="email"
                    id="email"
                    class="form-control"
                    placeholder="Enter Email"
                    name="email"
                    value="{{@$user->email}}"
                  />
                  @error('email')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="user_name">User Name</label>
                  <input
                    type="text"
                    id="user_name"
                    class="form-control"
                    placeholder="Enter User Name"
                    name="user_name"
                    value="{{old('user_name',$user->user_name)}}"
                  />
                  @error('user_name')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="password">Password</label>
                  <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    placeholder="Enter Password"
                  />
                  @error('password')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="mcc-latitude">Role</label>
                  <select id="role_dropdown" name="role_id">
                      <option value="" selected disabled>Select Role</option>
                    @foreach ($roles as $role)
                         <option value="{{$role->name}}" data-access="{{$role->access_level}}" @if($user->role_ids && count($user->role_ids)>0 && $role->id == $user->role_ids[0]) selected @endif>{{$role->name}}</option>
                    @endforeach
                  </select>
                  @error('role_dropdown')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="parent_id">Choose Parent for Access Level</label>
                  <select id="parent_id" name="parent_id"></select>
              </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="access_level_id">Choose Access Level</label>
                  <select id="access_level_id" name="access_level_ids[]" multiple required>
                    {{-- <option disabled>Choose
                      @if($accessLevel == 1) MCC @endif
                      @if($accessLevel == 2) Area Office @endif
                      @if($accessLevel == 3) Zones @endif
                      @if($accessLevel == 4) Sections @endif
                      @if($accessLevel == 5) Departments @endif
                      @if($accessLevel == 6) Plant @endif
                    </option>
                    @foreach ($records as $record)
                    @if($accessLevel == 2) Area Office
                    <option value="{{$record['_id']}}" @if(in_array($record['_id'],$user->access_level_ids)) selected @endif >{{$record['name']}}</option>
                    @else
                    <option @if(in_array($record['_id'],$user->access_level_ids)) selected @endif value="{{$record['_id']}}">{{$record['name']}}</option>
                    @endif
                    @endforeach --}}
                  </select>
                  @error('access_level_ids')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="phone">Phone Number *</label>
                  <input
                    type="text"
                    id="phone"
                    class="form-control phone"
                    name="phone"
                    placeholder="+92-3XX-XXXXXXX"
                    value="{{@old('phone',$user->phone)}}"
                    />
                  @error('phone')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
                <div class="col-md-6 col-12">
                    <div class="mb-1">
                        <label class="form-label" for="column-ao5">WhatsApp #</label>
                        <input type="text" id="column-ao5" class="form-control phone" placeholder="+92-3XX-XXXXXXX" name="whatsapp" value="{{ old('whatsapp',$user->whatsapp) }}"  />
                        @error('whatsapp')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                      <label class="form-label" for="column-ao5">Tracking No</label>
                      <input type="text" id="column-ao5" class="form-control" placeholder="Enter Jazz FFM Portal Tracking Number" name="tracking_no" value="{{@old('tracking_no',$user->tracking_no)}}" />
                      @error('tracking_no')
                      <span class="text-danger">{{$message}}</span>
                      @enderror
                  </div>
              </div>
              <div class="col-md-6 col-12">
                <label for="">Mobile User Only</label><br>
                <div class="form-check form-check-inline mt-1 mb-1">
                  <input class="form-check-input" type="checkbox" id="mobile_user_only" name="mobile_user_only" value="1" @if ($user->mobile_user_only == 1) checked @endif>
                  <label class="form-check-label" for="mobile_user_only">Mobile User</label>
                </div>
              </div>

              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="status">Status</label>
                  <div class="form-check form-switch form-check-primary">
                    <input type="checkbox" class="form-check-input" id="status" name="status" value="1" @if ($user->status == 1) checked @endif>
                    <label class="form-check-label" for="status">
                      <span class="switch-icon-left"><i data-feather="check"></i></span>
                      <span class="switch-icon-right"><i data-feather="x"></i></span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary me-1">Submit</button>
                <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                <a class="btn btn-outline-secondary cancel-btn" href="{{ route('users.index') }}" >Cancel</a>
              </div>
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
  {{-- Vendor js files --}}
  <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
@endsection

@section('page-script')
<script type="text/javascript">
  $(document).ready(function() {
    $('#role_dropdown').select2();
    $('#access_level_id').select2();
    $('#access_level_id').select2({ placeholder : "Access level"});
    $('#parent_id').select2({ placeholder : "Choose Parent"});
    $('#role_dropdown').trigger('change');
    setTimeout(function(){
      $('#parent_id').val('{{@$user->parent_id}}');
      $('#parent_id').trigger('change');
    }, 1500);
    setTimeout(function(){
      var access_level_ids = '@php echo json_encode($user->access_level_ids); @endphp';
      access_level_ids = JSON.parse(access_level_ids);
      $('#access_level_id').val(access_level_ids).trigger('change');
    }, 2500);
});
$('#role_dropdown').change(function (e) {
  var accessLevel = $(this).find(':selected').data('access');
  var formData = new FormData();
  formData.append('access_level',accessLevel);
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': "{{csrf_token()}}"
    },
    method:'POST',
    url:"{{route('get.access.level.parent')}}",
    contentType: false,
    processData:false,
    data: formData,
    success : function(response){
      if(response.success)
      {
        $('#parent_id').html('');
        $.each(response.records , function(index, val) {
          $('#parent_id').append('<option value="'+val['_id']+'">'+val['name']+'</option>');
        });
      }
      $('#parent_id').trigger('change');
    }
  });
});

$('#parent_id').change(function (e) {
  var accessLevel = $("#role_dropdown").find(':selected').data('access');
  var isSingle = $("#role_dropdown").find(':selected').data('single');
  var parent_id = $(this).find(':selected').val();
  var formData = new FormData();
  formData.append('access_level',accessLevel);
  formData.append('parent_id',parent_id);
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': "{{csrf_token()}}"
    },
    url:"{{route('get.access.level.dropdown')}}",
    method:'POST',
    contentType: false,
    processData:false,
    data: formData,
    success : function(response){
      if(response.success)
      {
        $('#access_level_id').html('');
        $.each(response.records , function(index, val) {
          if(isSingle == 1)
          $('#access_level_id').removeAttr('multiple');
          $('#access_level_id').append('<option value="'+val['_id']+'">'+val['name']+'</option>');
        });
      }
    }
  });
});
</script>

    <!-- Page js files -->
    <script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
    <script>
      $('.phone').inputmask({
            mask: '+\\92-399-9999999'
        });
    </script>
@endsection
