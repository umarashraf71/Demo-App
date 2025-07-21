@extends('layouts/contentLayoutMaster')

@section('title', 'Delivery Configuration')

@section('content')
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Delivery Configuration</h4>
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
          <form class="form" action="{{ route('supp.type.config.store') }}" method="POST">
            @csrf
            <div class="row">
              <div class="col-12">
                  <div class="table-responsive my-2">
{{--                    <label class="form-label">Purchasing milk from that source type</label>--}}
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Supplier Type</th>
                          <th>At MCC</th>
                          <th>By MMT</th>
                          <th>At Area Office</th>
                          <th>At Plant</th>
                          <th>By Plant</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($supplierTypes as $supplierType)
                        <tr>
                          <td>
                            <span class="fw-bold">{{@ucfirst($supplierType->name)}}</span>
                          </td>
                          <td class="text-center">
                              @if($supplierType->domain==1)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input cursor-pointer" type="checkbox" name="at_mcc[]" value="{{@$supplierType->id}}" @if(isset($supplierType->delivery_config) && $supplierType->delivery_config['at_mcc'] == 1) checked @endif/>
                                </div>
                              @endif
                          </td>
                          <td class="text-center">
                              @if($supplierType->domain==2)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input cursor-pointer" type="checkbox" name="by_mmt[]" value="{{@$supplierType->id}}" @if(isset($supplierType->delivery_config) && $supplierType->delivery_config['by_mmt'] == 1) checked @endif/>
                                </div>
                              @endif
                          </td>
                          <td class="text-center">
                              @if($supplierType->domain==2)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input cursor-pointer" type="checkbox" name="at_area_office[]" value="{{@$supplierType->id}}" @if(isset($supplierType->delivery_config) &&$supplierType->delivery_config['at_area_office'] == 1) checked @endif/>
                                </div>
                              @endif
                          </td>
                          <td class="text-center">
                              @if($supplierType->domain==3)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input cursor-pointer" type="checkbox" name="at_plant[]" value="{{@$supplierType->id}}" @if(isset($supplierType->delivery_config) &&$supplierType->delivery_config['at_plant'] == 1) checked @endif/>
                                </div>
                              @endif
                          </td>
                          <td class="text-center">
                              @if($supplierType->domain==3)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input cursor-pointer" type="checkbox" name="by_plant[]" value="{{@$supplierType->id}}" @if(isset($supplierType->delivery_config) &&$supplierType->delivery_config['by_plant'] == 1) checked @endif/>
                                </div>
                              @endif
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                </div>
              </div>
              {{-- end supplier type --}}
                @can('Edit Delivery Configuration')
              <div class="col-12">
                <button type="submit" class="btn btn-primary me-1">Submit</button>
                <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
{{--                <a class="btn btn-outline-secondary cancel-btn" href="{{ route('') }}" >Cancel</a>--}}
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
