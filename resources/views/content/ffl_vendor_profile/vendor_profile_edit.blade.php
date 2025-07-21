@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Vendor')
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
          <h4 class="card-title">Edit Vendor</h4>
        </div>
        <div class="card-body">
            @if ($errorMessage = Session::get('errorMessage'))
            <div class="demo-spacing-0 my-2">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body">{{ $errorMessage }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif
          <form class="form" action="{{ route('vendor-profile.update',$vendorProfile->id) }}" method="POST">
            @csrf
            @method('PUT')
              <input type="hidden" name="id" value="{{$vendorProfile->id}}">
              <div class="row">
                  <div class="col-md-6 col-12">
                      <div class="mb-1">
                          <label class="form-label" for="code-column">Code *</label>
                          <input
                              type="text"
                              id="code-column"
                              class="form-control"
                              name="code"
                              value="{{$vendorProfile->code}}"
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
                              value="{{ old('name',$vendorProfile->name) }}"
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
                              value="{{old('proprietor',$vendorProfile->proprietor)}}"
                          />
                          @error('proprietor')
                          <span class="text-danger">{{$message}}</span>
                          @enderror
                      </div>
                  </div>
                  <div class="col-md-6 col-12">
                      <div class="mb-1">
                          <label class="form-label" for="father_name">Father's Name</label>
                          <input
                              type="text"
                              id="father_name"
                              class="form-control"
                              placeholder="Name"
                              name="father_name"
                              value="{{ old('father_name',$vendorProfile->father_name) }}"
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
                              value="{{ old('cnic',$vendorProfile->cnic) }}"
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
                              class="form-control"
                              placeholder="XXXXX-XXXXXXX-X"
                              name="ntn"
                              value="{{ old('ntn',$vendorProfile->ntn) }}"
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
                              value="{{ old('address',$vendorProfile->address) }}"
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
                              value="{{ old('contact_no',$vendorProfile->contact_no) }}"
                          />
                          @error('contact_no')
                          <span class="text-danger">{{$message}}</span>
                          @enderror
                      </div>
                  </div>

                  <div class="col-md-6 col-12">
                      <div class="mb-1">
                          <label class="form-label" for="column-ao5">WhatsApp #</label>
                          <input type="text" id="column-ao5" class="form-control phone" placeholder="+92-3XX-XXXXXXX" name="whatsapp" value="{{ old('whatsapp',$vendorProfile->whatsapp) }}"  />
                          @error('whatsapp')
                          <span class="text-danger">{{$message}}</span>
                          @enderror
                      </div>
                  </div>
{{--                  <div class="col-md-6 col-12">--}}
{{--                      <div class="mb-1">--}}
{{--                          <label class="form-label" for="column-ao-7">Agreement Period--}}
{{--                              (From-To) *</label>--}}
{{--                          <input--}}
{{--                              type="text"--}}
{{--                              id="column-ao-7"--}}
{{--                              class="form-control flatpickr-range"--}}
{{--                              placeholder="YYYY-MM-DD to YYYY-MM-DD"--}}
{{--                              name="agreement_period"--}}
{{--                              value="{{ old('agreement_period',$vendorProfile->agreement_period) }}"--}}
{{--                          />--}}
{{--                          @error('agreement_period')--}}
{{--                          <span class="text-danger">{{$message}}</span>--}}
{{--                          @enderror--}}
{{--                      </div>--}}
{{--                  </div>--}}
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
                                  @include('content._partials._sections.edit_bank',['bank_detail'=>$vendorProfile])
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
                                              value="{{ old('contact_person',$vendorProfile->contact_person) }}"
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
                                              value="{{ old('contact_father_name',$vendorProfile->contact_father_name) }}"
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
                                              value="{{ old('contact_person_relation',$vendorProfile->contact_person_relation) }}"
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
                                              value="{{ old('contact_person_designation',$vendorProfile->contact_person_designation) }}"
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
                                              value="{{ old('contact_person_phone',$vendorProfile->contact_person_phone) }}"
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
                              <section id="agrrement_div">
                                  <div class="row">
                                      <div class="col-12">
                                          <div class="card">
                                              <div class="row border-bottom">
                                                  <div class="col-10">
{{--                                                      <h4 class="card-title mt-1 my-auto">Agreement Details</h4>--}}
                                                  </div>
                                                  <div class="col-2 agreement-edit-btn">
                                                      <a class="add-new-btn btn btn-primary  mr_30px mb-1"
                                                         href="#" data-bs-toggle="modal"
                                                         data-bs-target="#addAgreementModal">Add</a>
                                                  </div>
                                              </div>
                                              <div class="card-body">
                                                  <div class="card-datatable">
                                                      <table class="table" id="agreement_details_table">
                                                          <thead>
                                                          <tr>
                                                              <th>No.</th>
                                                              <th>Ref. #</th>
                                                              <th>From</th>
                                                              <th>To</th>
                                                              <th>w.e.f</th>
                                                              <th>Status</th>
                                                          </tr>
                                                          </thead>
                                                          <tbody>
                                                          @isset($vendorProfile->agreements)
                                                              @forelse($vendorProfile->agreements as $key=>$agreement)
                                                                  <tr>
                                                                      <td>{{++$key}}</td>
                                                                      <td>{{$agreement['ref_no']}}</td>
                                                                      <td>{{$agreement['from']}}</td>
                                                                      <td>{{$agreement['to']}}</td>
                                                                      <td>{{$agreement['wef']}}</td>
                                                                      <td>

                                                                          <div class="form-check form-switch form-check-primary">
                                                                              <input type="checkbox" class="form-check-input"
                                                                                     {{isset($agreement['status']) && $agreement['status']==1?'checked':''}}
                                                                                     id="status_{{$key}}" name="status" onclick="updateAgrementStatus('{{$key}}')" value="1"/>
                                                                              <label class="form-check-label" for="status_{{$key}}">
                                                                                  <span class="switch-icon-left"><i data-feather="check"></i></span>
                                                                                  <span class="switch-icon-right"><i data-feather="x"></i></span>
                                                                              </label>
                                                                          </div>
                                                                      </td>
                                                                  </tr>
                                                          </tbody>
                                                          @empty
                                                              <tr>
                                                                  <td colspan="6" class="text-center empty">No data found</td>
                                                              </tr>
                                                          @endforelse
                                                          @endisset
                                                      </table>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </section>
                          </div>
                          <div class="tab-pane fade" id="other-tab" role="tabpanel"
                               aria-labelledby="other-dtls-tab">
                              <div class="row">
                                  <div class="col-md-6 col-12">
                                      <div class="mb-1">
                                          <label class="form-label" for="column-own-13">Contract Type</label>
                                          <select name="contract_type" class="select2 form-select" >
                                              <option value="" selected disabled >Contract Type</option>
                                              <option value="1" {{ old('contract_type',$vendorProfile->contract_type)==1?'selected':'' }}>Rental</option>
                                              <option value="2" {{ old('contract_type',$vendorProfile->contract_type)==2?'selected':'' }}>Dedicated</option>
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
{{--                                          <span class="text-danger">{{$message}}</span>--}}
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
                                              value="{{ old('charges_per_trip',$vendorProfile->charges_per_trip) }}"
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
                                              type="number" min="0"
                                              id="per_km_rate"
                                              class="form-control"
                                              placeholder="KM's Rate"
                                              name="per_km_rate"
                                              value="{{ old('per_km_rate',$vendorProfile->per_km_rate) }}"
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

<div class="modal fade" id="addAgreementModal" tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-sm-5 pb-5">
                <div class="text-center mb-2">
                    <h1 class="mb-1">Agreement Information</h1>
                </div>
                <div class="row">
                    {{--                                                                <form id="agreement_form" >--}}
                    <div class="col-md-12">
                        <div class="col-md-12 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="ref_no">Ref. #</label>
                                <input type="text" id="ref_no"  class="form-control" placeholder="Enter Ref. #" name="ref_no" value="" />
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12">
                            <label class="form-label" for="agrement_from">Agreement Duration (YYYY-MM-DD)</label>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                    <input type="text"  required id="agrement_from" class="form-control flatpickr-basic" placeholder="From" name="agreement_period_from"  />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                    <input type="text" required id="agrement_to" class="form-control flatpickr-basic" placeholder="To" name="agreement_period_to"  />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                    <input type="text" required id="wef" class="form-control flatpickr-basic" placeholder="w.e.f" name="agreement_period_wef"  />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <a  class="btn btn-primary mt-2 me-1" onclick="addAggrement()">Save</a>
                            <button class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal" aria-label="Close">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Basic Floating Label Form section end -->
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/pickadate/legacy.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset('js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script>
    <script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
    <script>
        // script for shifting tabs
        $(document).ready(function() {
            $('#myTab a').on('click', function(e) {

                e.preventDefault();
                $(this).tab('show');
            });
        });
    $('.cnic').inputmask({mask: '99999-9999999-9'});
    $('.phone').inputmask({mask: '+\\92-999-9999999'});




        function addAggrement(){
            if($('#agrement_from').val()==''){
                showAlert('error', 'From date is required');
                return;
            }
            if($('#agrement_agrement_to').val()==''){
                showAlert('error', 'Agreement to field is required');
                return;
            }
            var fd = new FormData();
            fd.append('ref_no', $('#ref_no').val());
            fd.append('from', $('#agrement_from').val());
            fd.append('to', $('#agrement_to').val());
            fd.append('wef', $('#wef').val());
            fd.append('id', '{{$vendorProfile->id}}');
            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                url: '{{route('add.vendor.agreement')}}',
                success: function (response) {
                    if (response.success) {
                        let  html=`<tr><td>${response.count}</td><td>${$('#ref_no').val()}</td><td>${$('#agrement_from').val()}</td>
                            <td>${$('#agrement_to').val()}</td><td>${$('#wef').val()}</td>
                            <td>
                                <div class="form-check form-switch form-check-primary">
                                    <input type="checkbox" id="status_${response.count}" class="form-check-input" onclick="updateAgrementStatus('${response.count}')"  name="status" value="1" checked>
                                    <label class="form-check-label" for="status_${response.count}">
                                        <span class="switch-icon-left"><i data-feather="check"></i></span>
                                        <span class="switch-icon-right"><i data-feather="x"></i></span>
                                    </label>
                                </div>
                            </td>
                            </tr>`;
                        $('.empty').html('')
                        $('#agreement_details_table > tbody:last-child').append(html)
                        $('#agrement_to,#ref_no,#wef,#agrement_from').val('');
                        $('#addAgreementModal').modal('hide');
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }


        function updateAgrementStatus(key){
            let status = $('#status_'+key).is(':checked');
            if(status){
                status = 1;
            }else{
                status = 0;
            }
            $.ajax({
                type: "get",
                data:{'id':'{{$vendorProfile->id}}','status':status,'key':key},
                url: '{{route('vendor.agreement.update.status')}}',
                success: function (response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }
            });
        }




    </script>
@endsection
