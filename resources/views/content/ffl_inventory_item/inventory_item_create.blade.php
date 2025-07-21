@extends('layouts/contentLayoutMaster')

@section('title', 'Add New Inventory Item')
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
                        <h4 class="card-title">Add New Inventory Item</h4>
                    </div>
                    <div class="card-body">
                        @if ($message = Session::get('success'))
                            <div class="demo-spacing-0 my-2">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <div class="alert-body">{{ $message }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        @if ($errorMessage = Session::get('errorMessage'))
                            <div class="demo-spacing-0 my-2">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <div class="alert-body">{{ $errorMessage }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        <form class="form" action="{{ route('inventory-item.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-1">Code *</label>
                                        <input type="text" id="column-1" class="form-control" name="code"
                                            value="{{ $code }}" disabled minlength="3" maxlength="10" />
                                        @error('code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="item_type1">Inventory Item Type *</label>
                                        <select class="select2 form-select" id="item_type" name="item_type">
                                            <option value="" selected disabled>Select Type</option>
                                            @foreach ($inventory_type as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ Input::old('item_type') == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('item_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-3">Name *</label>
                                        <input type="text" id="column-3" class="form-control" placeholder="Name"
                                            name="name" value="{{ old('name') }}" />
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="asset_number">Manufactured By</label>
                                        <input type="text" id="manufactured_by" class="form-control"
                                            placeholder="Manufactured By" name="manufactured_by"
                                            value="{{ old('manufactured_by') }}" />
                                        @error('manufactured_by')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="asset_number">SAP Tag# *</label>
                                        <input type="text" id="tag_number" class="form-control" placeholder="Tag Number"
                                            maxlength="10" minlength="1" name="tag_number"
                                            value="{{ old('tag_number') }}" />
                                        @error('tag_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="asset_number">Capacity </label>
                                        <input type="text" id="capacity" class="form-control" placeholder="Capacity"
                                            name="capacity" value="{{ old('capacity') }}" />
                                        @error('capacity')
                                            <span class="text-danger">{{ $message }}</span>
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
                                                    {{ Input::old('area_office') == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}</option>
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
                                        <select class="select2 form-select" id="nature_of_asset" name="nature_of_asset">
                                            <option value="1">Fixed</option>
                                            <option value="2">Revenue</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="status">Status *</label>
                                        <select class="select2 form-select" id="status" name="status">
                                            <option value="1">Active</option>
                                            <option value="0">In-Active</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-4">Description</label>
                                        <textarea name="description" id="column-4" class="form-control" cols="10" placeholder='Description'></textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary me-1">Submit</button>
                                    <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                                    <a class="btn btn-outline-secondary cancel-btn"
                                        href="{{ route('inventory-item.index') }}">Cancel</a>
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
