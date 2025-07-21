@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Inventory Item')
@section('vendor-style')
<link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection

@section('content')
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Edit Inventory Item</h4>
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
          <form class="form" action="{{ route('inventory-item.update',  $inventoryItem->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                    <label class="form-label" for="column-1">Item Code *</label>
                    <input
                      type="text"
                      id="column-1"
                      class="form-control"
                      name="code"
                      disabled
                      value="{{$inventoryItem->code}}"
                      minlength="3" maxlength="7"
                    />
                      @error('code')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                      <label class="form-label" for="column-2">Inventory Item Type *</label>
                      <select
                          class="select2 form-select"
                          id="column-2"
                          name="item_type"
                      >
                        @foreach ($inventory_type as $value)
                        <option
                        value="{{ $value->id }}"
                        {{ (old('item_type',$inventoryItem->item_type) == $value->id  ? "selected":"") }}
                        >{{ $value->name }}</option>
                        @endforeach
                      </select>
                      @error('item_type')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                    <label class="form-label" for="column-3">Name *</label>
                    <input
                      type="text"
                      id="column-3"
                      class="form-control"
                      placeholder="Name"
                      name="name"
                      value="{{old('name',$inventoryItem->name)}}"
                    />
                    @error('name')
                          <span class="text-danger">{{$message}}</span>
                    @enderror
                  </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="mb-1">
                        <label class="form-label" for="asset_number">Manufactured By</label>
                        <input
                            type="text"
                            id="manufactured_by"
                            class="form-control"
                            placeholder="Manufactured By"
                            name="manufactured_by"
                            value="{{old('manufactured_by',$inventoryItem->manufactured_by)}}"
                        />
                        @error('manufactured_by')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                    <label class="form-label" for="column-4">Tag# *</label>
                    <input
                      type="text"
                      id="column-4"
                      class="form-control"
                      placeholder="Tag Number"
                      name="tag_number"
                      value="{{old('tag_number',$inventoryItem->tag_number)}}"
                    />
                    @error('tag_number')
                          <span class="text-danger">{{$message}}</span>
                    @enderror
                  </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="mb-1">
                        <label class="form-label" for="asset_number">Capacity </label>
                        <input
                            type="text"
                            id="capacity"
                            class="form-control"
                            placeholder="Capacity"
                            name="capacity"
                            value="{{old('capacity',$inventoryItem->capacity)}}"
                        />
                        @error('capacity')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12">
                  <div class="mb-1">
                      <label class="form-label" for="item_type1">Area Office *</label>
                      <select class="select2 form-select" id="area_office" name="area_office_id">
                          <option value="" selected disabled>Select Area Office</option>
                          @foreach ($areaOffices as $value)
                              <option value="{{ $value->id }}"
                                  {{ (old('area_office',$inventoryItem->area_office_id) == $value->id  ? "selected":"") }}
                              >{{ $value->name }}</option>
                          @endforeach
                      </select>
                      @error('area_office')
                          <span class="text-danger">{{ $message }}</span>
                      @enderror
                  </div>
              </div>

                <div class="col-md-6 col-12">
                    <div class="mb-1">
                        <label class="form-label" for="status">Nature of Asset</label>
                        <select
                            class="select2 form-select"
                            id="nature_of_asset"
                            name="nature_of_asset"
                        >
                            <option value="1" {{old('nature_of_asset',$inventoryItem->nature_of_asset)==1?'selected':''}}>Fixed</option>
                            <option value="2" {{old('nature_of_asset',$inventoryItem->nature_of_asset)==2?'selected':''}}>Revenue</option>
                        </select>
                        @error('status')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                    <label class="form-label" for="column-sts">Status *</label>
                    <select
                          class="select2 form-select"
                          id="column-sts"
                          name="status"
                      >
                          <option value="1" {{ $inventoryItem->status == 1  ? "selected":"" }}>Active</option>
                          <option value="0" {{ $inventoryItem->status == 0  ? "selected":"" }}>In-Active</option>
                    </select>
                    @error('status')
                          <span class="text-danger">{{$message}}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="mb-1">
                    <label class="form-label" for="column-4">Description</label>
                    <textarea name="description" id="column-4" class="form-control" cols="10">{{$inventoryItem->description}}</textarea>
                    @error('description')
                          <span class="text-danger">{{$message}}</span>
                    @enderror
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary me-1">Submit</button>
                  <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                  <a class="btn btn-outline-secondary cancel-btn" href="{{ route('inventory-item.index') }}" >Cancel</a>
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
<!-- vendor files -->
<script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
@endsection
@section('page-script')
<!-- Page js files -->
<script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script>
@endsection
