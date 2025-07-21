@extends('layouts/contentLayoutMaster')

@section('title', 'Incentive Configuration')

@section('content')
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Supplier Incentives Configuration</h4>
            @can('Create Incentive Configuration')
                <a class="add-new-btn btn btn-primary mt-2 mr_30px" href="#" onclick="openModal('addModal')">Add Type</a>
            @endcan
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
          <form class="form" action="{{ route('supplier.incentive.config.store') }}" method="POST">
            @csrf
            <div class="row">
              {{-- supplier type --}}
              <div class="col-12">
                  <div class="table-responsive mb-2 ">
{{--              <label class="form-label">Purchasing milk from that source type</label>--}}
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Incentives</th>
                          <th>CDF</th>
                          <th>MVMC</th>
                          <th>LF (FFL Chiller)</th>
                          <th>LF (Own Chiller)</th>
                          <th>VMCA (FFL Chiller)</th>
                          <th>VMCA (Own Chiller)</th>
                          <th>CF</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($incentives as $incentive)

                        <tr>
                          <td>
                            <span class="fw-bold">{{@ucfirst($incentive['name'])}}</span>
                          </td>
                          <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="cdf[]" value="{{@$incentive['_id']}}" @if(isset($incentive['config']) && @$incentive['config']['cdf'] == 1) checked @endif/>
                            </div>
                          </td>
                          <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="mvmc[]" value="{{@$incentive['_id']}}" @if(isset($incentive['config']) && @$incentive['config']['mvmc'] == 1) checked @endif/>

                            </div>
                          </td>
                          <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="lf_ffl_chiller[]" value="{{@$incentive['_id']}}" @if(isset($incentive[ 'config']) &&@$incentive['config']['lf_ffl_chiller'] == 1) checked @endif/>

                            </div>
                          </td>
                          <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="lf_own_chiller[]" value="{{@$incentive['_id']}}" @if(isset($incentive['config']) && @$incentive['config']['lf_own_chiller'] == 1) checked @endif/>

                            </div>
                          </td>
                          <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="vmca_ffl_chiller[]" value="{{@$incentive['_id']}}" @if(isset($incentive['config']) && @$incentive['config']['vmca_ffl_chiller'] == 1) checked @endif/>
                            </div>
                          </td>
                            <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="vmca_own_chiller[]" value="{{@$incentive['_id']}}" @if(isset($incentive['config']) && @$incentive['config']['vmca_own_chiller'] == 1) checked @endif/>
                            </div>
                          </td>
                            <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="cf[]" value="{{@$incentive['_id']}}" @if(isset($incentive['config']) && @$incentive['config']['cf'] == 1) checked @endif/>
                            </div>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                </div>
              </div>
              {{-- end supplier type --}}
                @can('Create Incentive Configuration')
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary me-1">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                      </div>
                 @endcan
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>


<div class="modal" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add Incentive Type</h4>
           </div>
            <form action='{{route('add.incentive')}}' method="post">
                @csrf
            <!-- Modal body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input required type="text" name="name" maxlength="22" class="form-control"  value="{{old('name')}}" placeholder="Name" >
                    </div>
                    @error('name')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <input class="btn btn-primary" type="submit" name="submit" value="save" >
            </div>
            </form>

        </div>
    </div>
</div>
<!-- Basic Floating Label Form section end -->

@endsection

@section('vendor-script')
    <script>

        $(document).ready(function() {
            @error('name')
                 $('#addModal').modal('show')
            @enderror
        });
        function openModal(id){
            $('#'+id).modal('show')
        }
    </script>
@endsection
