@extends('layouts/contentLayoutMaster')

@section('title', 'Add New Vendor Profile')
@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">

@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-pickadate.css') }}">
@endsection
@section('content')
<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Add Vendor</h4>
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
          <form class="form" action="{{ route('vendor-profile.store') }}" method="POST">
            @csrf
            <div class="row">
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="code-column">Code *</label>
                  <input
                    type="text"
                    id="code-column"
                    class="form-control"
                    name="code"
                    value="{{ $code }}"
                    disabled
                  />
                    @error('code')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="name-column">Company Name *</label>
                  <input
                    type="text"
                    id="name-column"
                    class="form-control"
                    placeholder="Transporter's Company Name"
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
                  <label class="form-label" for="proprietor">Proprietor Name</label>
                  <input
                    type="text"
                    id="proprietor"
                    class="form-control"
                    placeholder="Proprietor Name"
                    name="proprietor"
                    value="{{ old('proprietor') }}"
                  />
                  @error('proprietor')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>
                <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="father_name">Father Name</label>
                  <input
                    type="text"
                    id="father_name"
                    class="form-control"
                    placeholder="Name"
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
                  <label class="form-label" for="cnic">CNIC #</label>
                  <input
                    type="text"
                    id="cnic"
                    class="form-control cnic"
                    placeholder="XXXXX-XXXXXXX-X"
                    name="cnic"
                    value="{{ old('cnic') }}"
                  />
                  @error('cnic')
                        <span class="text-danger">{{$message}}</span>
                  @enderror
                </div>
              </div>

                <div class="col-md-6 col-12">
                    <div class="mb-1">
                      <label class="form-label" for="ntn">NTN #</label>
                      <input
                        type="text"
                        class="form-control ntn"
                        placeholder="XXXXX-XXXXXXX-X"
                        name="ntn"
                        value="{{ old('ntn') }}"
                      />
                      @error('ntn')
                            <span class="text-danger">{{$message}}</span>
                      @enderror
                     </div>
                </div>


                <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="address">Address</label>
                  <input
                    type="text"
                    id="address"
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

                <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="contact_no">Contact# *</label>
                  <input
                    type="text"
                    id="contact_no"
                    class="form-control phone"
                    placeholder="+92-3XX-XXXXXXX"
                    name="contact_no"
                    value="{{ old('contact_no') }}"
                  />
                  @error('contact_no')
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
{{--                <div class="col-md-6 col-12">--}}
{{--                    <div class="mb-1">--}}
{{--                        <label class="form-label" for="column-ao-7">Agreement Period--}}
{{--                            (From-To) *</label>--}}
{{--                        <input--}}
{{--                            type="text"--}}
{{--                            id="column-ao-7"--}}
{{--                            class="form-control flatpickr-range"--}}
{{--                            placeholder="YYYY-MM-DD to YYYY-MM-DD"--}}
{{--                            name="agreement_period"--}}
{{--                            value="{{ old('agreement_period') }}"--}}
{{--                        />--}}
{{--                        @error('agreement_period')--}}
{{--                             <span class="text-danger">{{$message}}</span>--}}
{{--                        @enderror--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
              <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                      <ul class="nav nav-tabs scroller" id="myTab" role="tablist">

                          <li class="nav-item ">
                              <a class="nav-link active" id="bank-dtl-tab" data-toggle="tab"
                                 href="#bank-tab" role="tab" aria-controls="bank-tab"
                                 aria-selected="true">Bank Details</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link" id="contact-dtl-tab" data-toggle="tab"
                                 href="#contact-tab" role="tab" aria-controls="contact-tab"
                                 aria-selected="true">Contact Details</a>
                          </li>

                          <li class="nav-item">
                              <a class="nav-link" id="agreement-info-tab" data-toggle="tab"
                                 href="#agreement-info" role="tab" aria-controls="agreement-info"
                                 aria-selected="false">Agreement</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link" id="other-dtls-tab" data-toggle="tab" href="#other-tab"
                                 role="tab" aria-controls="other-tab" aria-selected="false">Other Details</a>
                          </li>
                      </ul>
                      <div class="tab-content tab-data" style="padding: 5px;" id="myTabContent">

                          <div class="tab-pane fade active show" id="bank-tab" role="tabpanel"
                               aria-labelledby="bank-tab">
                              <div class="row ">
                                  @include('content._partials._sections.add_bank')
                              </div>
                          </div>

                          <div class="tab-pane fade" id="contact-tab" role="tabpanel"
                               aria-labelledby="chiller-dtl-tab">
                              <div class="row ">
                                  <div class="col-md-6 col-12">
                                      <div class="mb-1">
                                          <label class="form-label" for="contact_person">Contact Person</label>
                                          <input
                                              type="text"
                                              id="contact_person"
                                              class="form-control"
                                              placeholder="Contact Person Name"
                                              name="contact_person"
                                              value="{{ old('contact_person') }}"
                                          />
                                          @error('contact_person')
                                          <span class="text-danger">{{$message}}</span>
                                          @enderror
                                      </div>
                                  </div>
                                  <div class="col-md-6 col-12">
                                      <div class="mb-1">
                                          <label class="form-label" for="contact_father_name">Father Name</label>
                                          <input
                                              type="text"
                                              id="contact_father_name"
                                              class="form-control"
                                              placeholder="Father Name"
                                              name="contact_father_name"
                                              value="{{ old('contact_father_name') }}"
                                          />
                                          @error('contact_father_name')
                                          <span class="text-danger">{{$message}}</span>
                                          @enderror
                                      </div>
                                  </div>
                                  <div class="col-md-6 col-12">
                                      <div class="mb-1">
                                          <label class="form-label" for="contact_person_relation">Relation</label>
                                          <input
                                              type="text"
                                              id="contact_person_relation"
                                              class="form-control"
                                              placeholder="Relation"
                                              name="contact_person_relation"
                                              value="{{ old('contact_person_relation') }}"
                                          />
                                          @error('contact_person_relation')
                                          <span class="text-danger">{{$message}}</span>
                                          @enderror
                                      </div>
                                  </div>
                                  <div class="col-md-6 col-12">
                                      <div class="mb-1">
                                          <label class="form-label" for="contact_person_designation">Designation</label>
                                          <input
                                              type="text"
                                              id="contact_person_designation"
                                              class="form-control"
                                              placeholder="Designation"
                                              name="contact_person_designation"
                                              value="{{ old('contact_person_designation') }}"
                                          />
                                          @error('contact_person_designation')
                                          <span class="text-danger">{{$message}}</span>
                                          @enderror
                                      </div>
                                  </div>
                                  <div class="col-md-6 col-12">
                                      <div class="mb-1">
                                          <label class="form-label" for="contact_person_phone">Contact #</label>
                                          <input
                                              type="text"
                                              id="contact_person_phone"
                                              class="form-control phone"
                                              placeholder="+92-3XX-XXXXXXX"
                                              name="contact_person_phone"
                                              value="{{ old('contact_person_phone') }}"
                                          />
                                          @error('contact_person_phone')
                                             <span class="text-danger">{{$message}}</span>
                                          @enderror
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="tab-pane fade" id="agreement-info" role="tabpanel"
                               aria-labelledby="agreement-info-tab">
                              <div class="row">
                                  <div class="col-4">
                                      <div class="mb-1">
                                          <label class="form-label" for="column-own-6">Ref #</label>
                                          <input type="text" id="column-own-6" class="form-control"
                                                 placeholder="Enter Ref #" name="ref_no"
                                                 value="{{ old('ref_no') }}" />
                                      </div>
                                  </div>


                                          <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                              <label class="form-label" for="column-own-6">Agreement From</label>
                                              <input type="text" id="column-ao-7"
                                                     class="form-control flatpickr-basic"
                                                     placeholder="From: (YYYY-MM-DD)"
                                                     name="agreement_period_from"
                                                     value="{{old('agreement_period_from')}}" />
                                              @error('agreement_period_from')
                                                <span class="text-danger">{{$message}}</span>
                                              @enderror
                                          </div>
                                          <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                              <label class="form-label" for="column-own-6">Agreement To</label>
                                              <input type="text" id="column-ao-7"
                                                     class="form-control flatpickr-basic"
                                                     placeholder="To: (YYYY-MM-DD)"
                                                     name="agreement_period_to"
                                                     value="{{old('agreement_period_to')}}" />
                                              @error('agreement_period_to')
                                                 <span class="text-danger">{{$message}}</span>
                                              @enderror
                                          </div>
                                          <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                              <label class="form-label" for="column-own-6">Effective From</label>
                                              <input type="text" id="column-ao-7"
                                                     class="form-control flatpickr-basic"
                                                     placeholder="W.E.F: (YYYY-MM-DD)"
                                                     name="agreement_period_wef"
                                                     value="{{old('agreement_period_wef')}}" />
                                              @error('agreement_period_wef')
                                                <span class="text-danger">{{$message}}</span>
                                              @enderror
                                          </div>
                              </div>
                          </div>
                          <div class="tab-pane fade" id="other-tab" role="tabpanel"
                               aria-labelledby="other-dtls-tab">
                              <div class="row">
                                  <div class="col-md-6 col-12">
                                      <div class="mb-1">
                                          <label class="form-label" for="column-own-13">Contract Type</label>
                                          <select name="contract_type" class="select2 form-select" >
                                              <option value="" selected disabled >Contract Type</option>
                                              <option value="1" {{ old('contract_type')==1?'selected':'' }} >Rental</option>
                                              <option value="2" {{ old('contract_type')==2?'selected':'' }}>Dedicated</option>
                                          </select>
                                          @error('contract_type')
                                          <span class="text-danger">{{$message}}</span>
                                          @enderror
                                      </div>
                                  </div>
{{--                                  <div class="col-md-6 col-12">--}}
{{--                                      <div class="mb-1">--}}
{{--                                          <label class="form-label" for="column-own-10">Tanker's Capacity</label>--}}
{{--                                          <input--}}
{{--                                              type="text"--}}
{{--                                              id="column-own-10"--}}
{{--                                              class="form-control"--}}
{{--                                              placeholder="Tanker's Capacity"--}}
{{--                                              name="tanker_capacity"--}}
{{--                                              value="{{ old('tanker_capacity') }}"--}}
{{--                                          />--}}
{{--                                          @error('tanker_capacity')--}}
{{--                                             <span class="text-danger">{{$message}}</span>--}}
{{--                                          @enderror--}}
{{--                                      </div>--}}
{{--                                  </div>--}}
                                  <div class="col-md-6 col-12">
                                      <div class="mb-1">
                                          <label class="form-label" for="column-own-11">Fixed Daily / Fixed Charges per Trip</label>
                                          <input
                                              type="number"
                                              id="column-own-11"
                                              class="form-control"
                                              placeholder="Charges per Trip"
                                              name="charges_per_trip"
                                              value="{{ old('charges_per_trip') }}"
                                          />
                                          @error('charges_per_trip')
                                          <span class="text-danger">{{$message}}</span>
                                          @enderror
                                      </div>
                                  </div>
                                  <div class="col-md-6 col-12">
                                      <div class="mb-1">
                                          <label class="form-label" for="per_km_rate">Per KM Rate</label>
                                          <input
                                              type="number" min="0" max="100000"
                                              id="per_km_rate"
                                              class="form-control"
                                              placeholder="KM's Rate"
                                              name="per_km_rate"
                                              value="{{ old('per_km_rate') }}"
                                          />
                                          @error('per_km_rate')
                                              <span class="text-danger">{{$message}}</span>
                                          @enderror
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="col-12">
                      <button type="submit" class="btn btn-primary me-1">Submit</button>
                      <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                      <a class="btn btn-outline-secondary cancel-btn" href="{{ route('vendor-profile.index') }}" >Cancel</a>
                  </div>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
@section('page-script')

    <script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script>

        // script for shifting tabs
        $(document).ready(function() {
            $('#myTab a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });

    $('.cnic').inputmask({mask: '99999-9999999-9'});
    $('.phone').inputmask({mask: '+\\92-399-9999999'});
        $('.ntn').inputmask({
            mask: '9999999-9'
        });

        $( document ).ready(function() {
            @if($errors->any())
             showAlert('error', '{{$errors->first()}}');
            @endif
        });
    </script>
@endsection
