@extends('layouts/contentLayoutMaster')

@section('title', 'Add Test UOM')

@section('content')
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Add New Test UOM</h4>
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
          <form class="form" action="{{ route('test-uom.store') }}" method="POST">
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
                    value="{{ old('name') }}"
                  />
                  @error('name')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="description-colum">Description *</label>
                  <input
                    type="text"
                    id="description-colum"
                    class="form-control"
                    placeholder="Description"
                    name="description"
                    value="{{ old('description') }}"
                  />
                  @error('description')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary me-1">Submit</button>
                <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                <a class="btn btn-outline-secondary cancel-btn" href="{{ route('test-uom.index') }}" >Cancel</a>
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
