@extends('layouts/contentLayoutMaster')

@section('title', 'Create Role')
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection
@section('content')


    <style>
        .row_checkbox{
            background-size: 65%;
        }

    </style>
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Add New Role</h4>
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
          <!-- Add role form -->
          <form id="addRoleForm" class="row" method="POST" action="{{route('roles.store')}}">
            @csrf
            <div class="col-md-6 col-12">
              <label class="form-label" for="modalRoleName">Role Name</label>
              <input
                type="text"
                id="role"
                name="role" required
                class="form-control"
                placeholder="Enter role name"
                tabindex="-1"
                data-msg="Please enter role name"
                value="{{ old('role') }}"
              />
              @error('role')
              <span class="text-danger">{{$message}}</span>
          @enderror
            </div>
            <div class="col-md-6 col-12">
              <label class="form-label" for="modalRoleName">Role Access Level</label>
              <select
              class="select2 form-select" data-placeholder="Access Level"
              name="access_level">
              <option value="" selected disabled>Access Level</option>
              <option value="1" {{ (Input::old("access_level") == 1  ? "selected":"") }}>Collection Points (FFL)</option>
              <option value="2" {{ (Input::old("access_level") == 2  ? "selected":"") }}>Area Office</option>
              <option value="3" {{ (Input::old("access_level") == 3  ? "selected":"") }}>Zones</option>
              <option value="4" {{ (Input::old("access_level") == 4  ? "selected":"") }}>Sections</option>
              <option value="5" {{ (Input::old("access_level") == 5  ? "selected":"") }}>Departments</option>
              <option value="6" {{ (Input::old("access_level") == 6  ? "selected":"") }}>Plant</option>
          </select>
          @error('access_level')
              <span class="text-danger">{{$message}}</span>
          @enderror
            </div>
            <div class="col-md-6 col-12 mt-1">
              <label for="">Is Single Level?</label><br>
              <div class="form-check form-check-inline mt-1">
                <input class="form-check-input cursor-pointer" type="checkbox" id="is_single" name="is_single" value="1">
                <label class="form-check-label " for="is_single">Single Level</label>
              </div>
            </div>
            <div class="col-12">
              <h4 class="mt-2 pt-50">Give Permissions to Role</h4><br>
              <span class="btn btn-primary" id="check_all">Check All</span>
              <!-- Permission table -->
              <div class="table-responsive">
                <table class="table table-flush-spacing">
                  <tbody>
                    <?php $iteration_no = 1 ?>
                    @foreach ($permissions as $key => $permission)
                    @php
                      if($key == 'Permissions')
                      continue;
                    @endphp
                    <tr>
                      <td class="text-nowrap fw-bolder">{{$key}}</td>
                      <td class="p-0">
                          <input title="check all row elements " onclick="setRow('{{$iteration_no}}')" class="cursor-pointer form-check-input row_checkbox" type="checkbox"/>
                      </td>
                      @foreach ($permission as $specificPermission)
                      <td>
                        <div class="d-flex">
                          <div class="form-check me-3 me-lg-5">
                            <input class="form-check-input cursor-pointer row_fields_{{$iteration_no}}" type="checkbox" id="{{$specificPermission['name']}}" name="permissions[]" value="{{$specificPermission['name']}}"/>
                            <label class="form-check-label" for="{{$specificPermission['name']}}"> {{$specificPermission['name']}} </label>
                          </div>
                        </div>
                      </td>
                      @endforeach
                     @php($iteration_no++)
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- Permission table -->
            </div>
            <div class="col-12 mt-2">
              <button type="submit" class="btn btn-primary me-1">Submit</button>
              <button type="reset" class="btn btn-outline-secondary waves-effect me-1">Reset</button>
              <a class="btn btn-outline-secondary cancel-btn" href="{{ route('roles.index') }}" >Cancel</a>
            </div>
          </form>
          <!--/ Add role form -->
      </div>
    </div>
  </div>
  </div>
</section>
<!-- Basic Floating Label Form section end -->


@endsection
@section('vendor-script')
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>


    @endsection
@section('page-script')


    <script>
        var count=1;
        $(document).on('click', '#check_all', function(){
            checked=true;
            if(count%2==0) {
                checked =false;
                $('#check_all').text('Check all')
            }else{
                $('#check_all').text('Un Check all')
            }
            $('.table-responsive :checkbox').each(function () {
                this.checked = checked;
            });
            count++;
        });


        function setRow(row_num){
            $('.row_fields_'+row_num).each(function () {
                 this.checked = !this.checked;
            });
        }


        $('.select2').select2();

    </script>
@endsection
