@extends('layouts/contentLayoutMaster')

@section('title', 'Add Customer')

@section('content')
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Add Customer</h4>
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
          <form class="form" action="{{ route('customer.store') }}" method="POST">
            @csrf
            <div class="row">
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="code-column">Code <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="code-column"
                    class="form-control"
                    name="code"
                    placeholder="Code" disabled
                    value="{{ old('code',$code)}}"
                    maxlength="7"
                  />
                    @error('code')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="name-column">Name <span class="text-danger">*</span></label>
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
                  <label class="form-label" for="name-column1">C/O Name</label>
                  <input
                    type="text"
                    id="name-column1"
                    class="form-control"
                    placeholder="c/o Name"
                    name="c_o_name"
                    value="{{ old('c_o_name') }}"
                  />
                  @error('c_o_name')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
                <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="father_name"> Father's Name</label>
                  <input
                    type="text"
                    id="father_name"
                    class="form-control"
                    placeholder="Father's Name"
                    name="father_name"
                    value="{{ old('father_name') }}"
                  />
                  @error('father_name')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
                <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="cnic"> CNIC# <span class="text-danger">*</span></label>
                  <input
                    maxlength="15"
                    type="text"
                    id="cnic"
                    class="form-control cnic"
                    placeholder="XXXXX-XXXXXXX-X"
                    name="cnic" required
                    value="{{ old('cnic') }}"
                  />
                  @error('cnic')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
                <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="ntn"> NTN#</label>
                  <input
                    type="text"
                    id="ntn"
                    class="form-control"
                    placeholder="NTN"
                    name="ntn" required
                    value="{{ old('ntn') }}"
                  />
                  @error('ntn')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
                <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="gst">GST#</label>
                  <input
                    type="text"
                    id="gst"
                    class="form-control"
                    placeholder="Gst"
                    name="gst" required
                    value="{{ old('gst') }}"
                  />
                  @error('gst')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
                <div class="col-md-6 col-12">
                    <div class="mb-1">
                        <label class="form-label" for="contact"> Contact# <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="contact" required
                            class="form-control phone"
                            name="contact"
                            placeholder="+92-3XX-XXXXXXX"
                            value="{{ old('contact') }}"
                        />
                        @error('contact')
                           <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="mb-1">
                        <label class="form-label" for="column-ao5">WhatsApp #</label>
                        <input type="text" id="column-ao5" class="form-control phone" placeholder="+92-3XX-XXXXXXX" name="whatsapp" value="{{ old('whatsapp') }}"  />
                        @error('whatsapp')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="address-column">Address <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="address-column"
                    class="form-control"
                    placeholder="Address"
                    name="address"
                    value="{{ old('address') }}"
                  />
                  @error('address')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>

              <div class="col-12">
                <button type="submit" class="btn btn-primary me-1">Submit</button>
                <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                <a class="btn btn-outline-secondary cancel-btn" href="{{ route('customer.index')}}" >Cancel</a>
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
@section('page-script')
    <!-- Page js files -->
    <script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
    <script>
    $('.cnic').inputmask({mask: '99999-9999999-9'});
    $('.phone').inputmask({mask: '+\\92-399-9999999'});
    </script>
@endsection
