@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Supplier')
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-pickadate.css') }}">
@endsection
<style>
    .error {
        color: red;
    }
</style>
@section('content')

    <!-- Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Supplier</h4>
                    </div>
                    <div class="card-body">
                        {{--                        @if ($errorMessage = Session::get('errorMessage')) --}}
                        {{--                            <div class="demo-spacing-0 my-2"> --}}
                        {{--                                <div class="alert alert-danger alert-dismissible fade show" role="alert"> --}}
                        {{--                                    <div class="alert-body">{{ $errorMessage }}</div> --}}
                        {{--                                    <button type="button" class="btn-close" data-bs-dismiss="alert" --}}
                        {{--                                        aria-label="Close"></button> --}}
                        {{--                                </div> --}}
                        {{--                            </div> --}}
                        {{--                        @endif --}}
                        <form class="form" action="{{ route('supplier.update', $supplier->id) }}" method="POST"
                            id="createForm">
                            @csrf
                            <input type="hidden" name="by_mmt" value="{{ $type['delivery_config']['by_mmt'] ? 1 : 0 }}"
                                id="by_mmt">
                            <input type="hidden" name="cp_ids"
                                value="{{ $supplier->cp_ids ? implode(',', $supplier->cp_ids) : '' }}" id="cp_ids">
                            <input type="hidden" id="supplier_id" name="id" value="{{ old('id', $supplier->id) }}">


                            @method('PUT')
                            <div class="row">
                                <div class="col-2 mb-1">
                                    <label class="form-label" for="code-column">Supplier Details</label>
                                    <input type="text" id="code-column" class="form-control" placeholder="Supplier Code"
                                        name="code" maxlength="7" value="{{ old('code', $supplier->code) }}"
                                        disabled />

                                </div>
                                <div class="col-lg-4 col-12 mb-1" id="hidesourcetype">
                                    <label class="form-label" for="select2-multiple">Source Type</label><span
                                        class="text-danger">*</span>
                                    <select class="select2 form-select source_type" id="select2-multiple"
                                        onchange="getData(this.value)" name="supplier_type_id">
                                        <option value="" selected disabled>Select supplier type</option>
                                        @foreach ($fetch_data as $value)
                                            <option by-plant="{{ $value->delivery_config['by_plant'] }}"
                                                value="{{ $value->id }}"{{ $supplier->supplier_type_id == $value->id ? 'selected' : '' }}>
                                                {{ ucfirst($value->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 col-12 mb-1" id="appended_data"
                                    style="display: {{ $html ? '' : 'none' }}">
                                    {!! $html !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name-column">Name</label><span
                                            class="text-danger">*</span>
                                        <input type="text" id="name-column" class="form-control" placeholder="Name"
                                            name="name" value="{{ old('name', $supplier->name) }}" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name-column-email">Email</label>
                                        <input type="text" id="name-column-email" class="form-control"
                                            placeholder="Email" name="email"
                                            value="{{ old('email', $supplier->email) }}" />
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12 mb-1">
                                    <label class="form-label" for="column-own-3">CNIC#</label>
                                    <input type="text" id="column-own-3" class="form-control cnic"
                                        placeholder="XXXXX-XXXXXXX-X" name="cnic"
                                        value="{{ old('cnic', $supplier->cnic) }}" />
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="father_name">Father Name</label><span
                                            class="text-danger">*</span>
                                        <input type="text" id="father_name" class="form-control"
                                            placeholder="Father Name" name="father_name"
                                            value="{{ old('father_name', $supplier->father_name) }}" />
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-own-3">NTN#</label>
                                        <input type="text" id="column-own-3" class="form-control ntn"
                                            placeholder="XXXXXXX-X" name="ntn"
                                            value="{{ old('ntn', $supplier->ntn) }}" />
                                    </div>

                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-own-4">Business Name</label>
                                        <input type="text" id="column-own-4" class="form-control"
                                            placeholder="Business Name" name="business_name"
                                            value="{{ old('business_name', $supplier->business_name) }}" />
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-6">
                                        <div class="col-lg-12 ">
                                            <label class="form-label" for="mcc-contact">Contact# </label><span
                                                class="text-danger">*</span>
                                            <input type="text" id="mcc-contact" class="form-control phone"
                                                name="contact" placeholder="+92-3XX-XXXXXXX"
                                                value="{{ old('contact', $supplier->contact) }}" />
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="mt-1">
                                                <label class="form-label" for="column-ao5">WhatsApp #</label>
                                                <input type="text" id="column-ao5" class="form-control phone"
                                                    placeholder="+92-3XX-XXXXXXX" name="whatsapp"
                                                    value="{{ old('whatsapp', $supplier->whatsapp) }}" />

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4 ms-2">
                                        <label class="form-label" for="plant-longitude">Address</label><span
                                            class="text-danger">*</span>
                                        <textarea rows="4" name="address" class="form-control custom_adress" type="text" placeholder="Location">{{ old('address', $supplier->address) }}</textarea>
                                        <div class="invalid-feedback address"></div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="district">District</label>
                                        <select class="form-control select2" name="district_id" required
                                            id="districtSelect">
                                            <option value="">District</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}"
                                                    {{ $district->_id == $supplier->district_id ? 'Selected' : '' }}>
                                                    {{ $district->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('district_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="tehsil">Tehsil</label>
                                        <select class="form-control select2" name="tehsil_id" required id="tehsilSelect">
                                            <option value="">Tehsil</option>
                                            @foreach ($tehsils as $tehsil)
                                                <option value="{{ $tehsil->id }}"
                                                    {{ $tehsil->_id == $supplier->tehsil_id ? 'Selected' : '' }}>
                                                    {{ $tehsil->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('tehsil_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <ul class="nav nav-tabs scroller" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="bank-dtl-tab" data-toggle="tab"
                                                href="#bank-tab" role="tab" aria-controls="bank-tab"
                                                aria-selected="true">Bank Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="next-of-kin-dtls-tab" data-toggle="tab"
                                                href="#next-of-kin-tab" role="tab" aria-controls="next-of-kin-tab"
                                                aria-selected="false">Next of Kin</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="agreement-info-tab" data-toggle="tab"
                                                href="#agreement-info" role="tab" aria-controls="agreement-info"
                                                aria-selected="false">Agreement</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="other-dtls-tab" data-toggle="tab" href="#other-tab"
                                                role="tab" aria-controls="other-tab" aria-selected="false">Other
                                                Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " id="additional-info-tab" data-toggle="tab"
                                                href="#additional-info" role="tab" aria-controls="additional-info"
                                                aria-selected="false">Additional Info.</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link  cps {{ $type['delivery_config']['by_mmt'] ? '' : 'disabled' }}"
                                                data-toggle="tab" href="#cps-tab" role="tab" aria-controls="cps-tab"
                                                aria-selected="true">Collection Points</a>
                                        </li>
                                        <li class="nav-item locations" style="display: none">
                                            <a class="nav-link" data-toggle="tab" href="#location-tab" role="tab"
                                                aria-controls="location-tab" aria-selected="true">Location Details</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content tab-data" style="padding: 5px;" id="myTabContent">
                                        <div class="tab-pane fade show active" id="bank-tab" role="tabpanel"
                                            aria-labelledby="bank-dtl-tab">
                                            <div class="row">
                                                @include('content._partials._sections.edit_bank', [
                                                    'bank_detail' => $supplier,
                                                ])
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="next-of-kin-tab" role="tabpanel"
                                            aria-labelledby="next-of-kin-dtls-tab">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-10">Name</label>
                                                        <input type="text" id="column-own-10" class="form-control"
                                                            placeholder="Name" name="next_of_kin_name"
                                                            value="{{ old('next_of_kin_name', $supplier->next_of_kin_name) }}" />
                                                        @error('next_of_kin_name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-11">Father Name</label>
                                                        <input type="text" id="column-own-11" class="form-control"
                                                            placeholder="Father Name" name="next_of_kin_father_name"
                                                            value="{{ old('next_of_kin_father_name', $supplier->next_of_kin_father_name) }}" />
                                                        @error('next_of_kin_father_name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-12">Relation with
                                                            Supplier</label>
                                                        <input type="text" id="column-own-12" class="form-control"
                                                            placeholder="Relation" name="relation"
                                                            value="{{ old('relation', $supplier->relation) }}" />

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-13">Contact #</label>
                                                        <input type="text" id="column-own-13"
                                                            class="form-control phone" placeholder="+92-3XX-XXXXXXX"
                                                            name="next_of_kin_contact"
                                                            value="{{ old('next_of_kin_contact', $supplier->next_of_kin_contact) }}" />

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-13">Cnic</label>
                                                        <input type="text" id="column-own-13"
                                                            class="form-control cnic" placeholder="XXXXX-XXXXXX-X"
                                                            name="next_of_kin_cnic"
                                                            value="{{ old('next_of_kin_cnic', $supplier->next_of_kin_cnic) }}" />
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
                                                                    {{--                                                                    <h4 class="card-title mt-1 my-auto">Agreement Details</h4> --}}
                                                                </div>
                                                                <div class="col-2 agreement-edit-btn mb-1">
                                                                    <a class="add-new-btn btn btn-primary  mr_30px"
                                                                        href="#" data-bs-toggle="modal"
                                                                        data-bs-target="#addAgreementModal">Add</a>
                                                                </div>
                                                            </div>
                                                            {{--                    <div class="card-header border-bottom"> --}}
                                                            {{--                        <h4 class="card-title">Agreement</h4> --}}
                                                            {{--                        @can('Create Agreement') --}}
                                                            {{--                        <a class="add-new-btn btn btn-primary mt-2 mr_30px" href="#" data-bs-toggle="modal" data-bs-target="#addAgreementModal">Add</a> --}}
                                                            {{--                        @endcan --}}
                                                            {{--                    </div> --}}
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
                                                                            @isset($supplier->agreements)
                                                                                @forelse($supplier->agreements as $key=>$agreement)
                                                                                    <tr>
                                                                                        <td>{{ ++$key }}</td>
                                                                                        <td>{{ @$agreement['ref_no'] }}</td>
                                                                                        <td>{{ @$agreement['from'] }}</td>
                                                                                        <td>{{ @$agreement['to'] }}</td>
                                                                                        <td>{{ @$agreement['effective_from'] }}
                                                                                        </td>
                                                                                        <td>

                                                                                            <div
                                                                                                class="form-check form-switch form-check-primary">
                                                                                                <input type="checkbox"
                                                                                                    class="form-check-input"
                                                                                                    {{ isset($agreement['status']) && $agreement['status'] == 1 ? 'checked' : '' }}
                                                                                                    id="status_{{ $key }}"
                                                                                                    name="status"
                                                                                                    onclick="updateAgrementStatus('{{ $key }}')"
                                                                                                    value="1" />
                                                                                                <label class="form-check-label"
                                                                                                    for="status_{{ $key }}">
                                                                                                    <span
                                                                                                        class="switch-icon-left"><i
                                                                                                            data-feather="check"></i></span>
                                                                                                    <span
                                                                                                        class="switch-icon-right"><i
                                                                                                            data-feather="x"></i></span>
                                                                                                </label>
                                                                                            </div>

                                                                                        </td>
                                                                                    </tr>
                                                                            </tbody>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="6" class="text-center empty">
                                                                                    No data found</td>
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
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="row">
                                                    <div class="col-lg-2 col-md-4 col-sm-3">
                                                        <h4>Milk Details</h4>
                                                    </div>
                                                    <div class="col-lg-10 col-md-8 col-sm-9">
                                                        <hr style="border-top: 1px solid #B7B6BE;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-10">Total
                                                            Production</label>
                                                        <div class="d-flex">
                                                            <input type="number" min="0" max="50000"
                                                                id="column-own-10" class="form-control"
                                                                placeholder="Total Production"
                                                                name="total_milk_production"
                                                                value="{{ old('total_milk_production', $supplier->total_milk_production) }}" />
                                                            <span><br>&nbsp;Ltr.</span>
                                                            @error('total_milk_production')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-12">Available for
                                                            Sale</label>
                                                        <div class="d-flex">
                                                            <input type="number" min="0" max="50000"
                                                                id="column-own-12" class="form-control"
                                                                placeholder="Available for Sale"
                                                                name="milk_available_for_sale"
                                                                value="{{ old('milk_available_for_sale', $supplier->milk_available_for_sale) }}" />
                                                            <span><br>&nbsp;Ltr.</span>
                                                            @error('milk_available_for_sale')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-12">Current
                                                            Price</label>
                                                        <div class="d-flex">
                                                            <input type="number" min="1" max="10000"
                                                                id="column-own-12" class="form-control"
                                                                placeholder="Price per litre" name="price_per_litre"
                                                                value="{{ old('price_per_litre', $supplier->price_per_litre) }}" />
                                                            <span><br>&nbsp;Rs./Ltr.</span>
                                                            @error('price_per_litre')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="currently_supplying_to">Currently
                                                            Supplying To *</label>
                                                        <input type="text" id="currently_supplying_to"
                                                            class="form-control" name="currently_supplying_to"
                                                            placeholder="Currently Supplying To"
                                                            value="{{ old('currently_supplying_to', $supplier->currently_supplying_to) }}" />
                                                        @error('currently_supplying_to')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-4 col-sm-4">
                                                        <h4>Animal Details</h4>
                                                    </div>
                                                    <div class="col-lg-9 col-md-8 col-sm-8">
                                                        <hr style="border-top: 1px solid #B7B6BE;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-3">Milking</label>
                                                        <input type="number" min="0" max="1000"
                                                            id="column-own-3" class="form-control"
                                                            placeholder="Milking Animals" name="milking_animals"
                                                            value="{{ old('milking_animals', $supplier->milking_animals) }}" />
                                                        @error('milking_animals')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-a">Dry</label>
                                                        <input type="number" min="0" max="1000"
                                                            id="column-own-a" class="form-control"
                                                            placeholder="Dry Animals" name="dry_animals"
                                                            value="{{ old('dry_animals', $supplier->dry_animals) }}" />
                                                        @error('dry_animals')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-2">Total</label>
                                                        <input type="number" min="1" max="1000"
                                                            id="column-own-2" class="form-control"
                                                            placeholder="No. of Animals" name="no_of_animals"
                                                            value="{{ old('no_of_animals', $supplier->no_of_animals) }}" />
                                                        @error('no_of_animals')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-b">Heifers</label>
                                                        <input type="number" min="0" max="1000"
                                                            id="column-own-b" class="form-control"
                                                            placeholder="No. of Heifers" name="heifers"
                                                            value="{{ old('heifers', $supplier->heifers) }}" />
                                                        @error('heifers')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-q">Young Stock</label>
                                                        <input type="number" min="0" max="1000"
                                                            id="column-own-q" class="form-control"
                                                            placeholder="No. of Young Stock" name="young_stock"
                                                            value="{{ old('young_stock', $supplier->young_stock) }}" />
                                                        @error('young_stock')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="columnn132">Vaccination &
                                                            Deworming</label>
                                                        <select name="have_vaccinated" class="select2 form-select">
                                                            <option value="1"
                                                                {{ old('have_vaccinated', $supplier->have_vaccinated) == 1 ? 'selected' : '' }}>
                                                                Yes</option>
                                                            <option
                                                                value="0"{{ old('have_vaccinated', $supplier->have_vaccinated) == 0 ? 'selected' : '' }}>
                                                                No</option>
                                                        </select>
                                                        @error('have_vaccinated')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="row">
                                                    <div class="col-lg-2 col-md-4 col-sm-4">
                                                        <h4>Shed Details</h4>
                                                    </div>
                                                    <div class="col-lg-10 col-md-8 col-sm-8">
                                                        <hr style="border-top: 1px solid #B7B6BE;">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="column-own-aq">Dimensions of
                                                            shed</label>
                                                        <div class="col-lg-12 col-md-12 col-12 shed-dimensions">
                                                            <div class="row">
                                                                <div class="col-lg-5 col-md-5 col-sm-5">
                                                                    <input type="number" id="column-own-aq"
                                                                        class="form-control" placeholder="Width"
                                                                        name="shed_dimension_width"
                                                                        value="{{ old('shed_dimension_width', $supplier->shed_dimension_width) }}" />
                                                                </div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 shed-dimensions"
                                                                    style="margin-top: 10px; text-align:center; font-weight: bold;">
                                                                    <p>X</p>
                                                                </div>
                                                                <div class="col-lg-5 col-md-5 col-sm-5">
                                                                    <input type="number" id="column-own-aq"
                                                                        class="form-control" placeholder="Height"
                                                                        name="shed_dimension_height"
                                                                        value="{{ old('shed_dimension_height', $supplier->shed_dimension_height) }}" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="columnn132">Direction</label>
                                                        <input type="text"
                                                            id="columnn132" class="form-control"
                                                            placeholder="Direction Of Shed" name="direction_of_shed"
                                                            value="{{ old('direction_of_shed', $supplier->direction_of_shed) }}" />
                                                        @error('direction_of_shed')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="columnn2">Open Area (Square
                                                            Meter)</label>
                                                        <input type="text"
                                                            id="columnn2" class="form-control"
                                                            placeholder="Open Area in SqrMtr. " name="open_area"
                                                            value="{{ old('open_area', $supplier->open_area) }}" />
                                                        @error('open_area')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="columnn32">No. of Water
                                                            Troughs</label>
                                                        <input type="number" min="0" max="10000"
                                                            id="columnn32" class="form-control"
                                                            placeholder="Total Water Troughs" name="water_trough"
                                                            value="{{ old('water_trough', $supplier->water_trough) }}" />

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="additional-info" role="tabpanel"
                                            aria-labelledby="additional-info-tab">
                                            <div class="col-lg-12 col-md-12 col-12">
                                                <div class="row">
                                                    <div class="col-lg-1 col-md-1 col-sm-1"
                                                        style="margin-top: 10px; text-align:center; font-weight: bold;">
                                                        <p>FF-1</p>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <input name="ff_1" value="{{ old('ff_1', $supplier->ff_1) }}"
                                                            type="text" class="form-control" placeholder="" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-12 mt-1">
                                                <div class="row">
                                                    <div class="col-lg-1 col-md-1 col-sm-1"
                                                        style="margin-top: 10px; text-align:center; font-weight: bold;">
                                                        <p>FF-2</p>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <input type="text" name="ff_2"
                                                            value="{{ old('ff_2', $supplier->ff_2) }}"
                                                            class="form-control" placeholder="" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-12 mt-1">
                                                <div class="row">
                                                    <div class="col-lg-1 col-md-1 col-sm-1"
                                                        style="margin-top: 10px; text-align:center; font-weight: bold;">
                                                        <p>FF-3</p>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <input type="text" name="ff_3"
                                                            value="{{ old('ff_3', $supplier->ff_3) }}"
                                                            class="form-control" placeholder="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="cps-tab" role="tabpanel"
                                            aria-labelledby="cps-tab">
                                            <div class="row cps_div">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="row border-bottom pb-1">
                                                            <div class="col-10">
                                                            </div>
                                                            <div class="col-2 add_cp-btn">
                                                                <a class="add-new-btn btn btn-primary mr_30px"
                                                                    href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#addCpModal">Add</a>
                                                            </div>
                                                        </div>

                                                        <div class="card-body">
                                                            <div class="card-datatable">
                                                                <table class="table" id="cps_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No.</th>
                                                                            <th>Collection Point</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @if ($supplier->cp_ids)
                                                                            @foreach ($supplier->cp_ids as $key => $data)
                                                                                @php
                                                                                    $cp = $data ? App\Helpers\Helper::getCpById($data) : '';
                                                                                    $i = 0;
                                                                                @endphp
                                                                                @if ($cp)
                                                                                    <tr id="cp_row_{{ $cp->id }}">
                                                                                        <td>{{ ++$key }}</td>
                                                                                        <td>{{ $cp ? $cp->name : '' }}</td>
                                                                                        <td><span
                                                                                                class="cursor-pointer text-danger fa fa-trash"
                                                                                                onclick="deleteCP('{{ $cp->id }}','{{ $cp->name }}')"></span>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endif
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="4"
                                                                                    class="text-center empty">No data found
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="location-tab" role="tabpanel"
                                            aria-labelledby="location-tab">
                                            <div class="row locations" style="display: none">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="plant-latitude">Lattitude</label>
                                                        <input type="text" id="latitude" class="form-control"
                                                            name="latitude" disabled placeholder="Latitude" readonly
                                                            value="{{ old('latitide', $supplier->latitude) }}" />

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="plant-longitude">Longitude</label>
                                                        <input type="text" id="longitude" class="form-control"
                                                            name="longitude" disabled placeholder="Longitude" readonly
                                                            value="{{ old('longitude', $supplier->longitude) }}" />

                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="plant-longitude">Location *</label>
                                                        <input disabled autocomplete="off" id="address" name="address"
                                                            class="form-control" type="text"
                                                            value="{{ old('address', $supplier->address) }}"
                                                            placeholder="Location">
                                                        <div class="invalid-feedback address"></div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div id="map" class="home-map">
                                                        <div id="map-canvas" class="map-canvas-event"
                                                            style="height: 260px; margin-bottom: 1%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="new_supplier_type_id">
                            <input type="hidden" id="new_areaoffice_id" >

                            <div class="row mt-1">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary me-1" id="save_button">Submit</button>
                                    <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                                    <a class="btn btn-outline-secondary cancel-btn"
                                        href="{{ route('supplier.index') }}">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="addAgreementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                    <div class="text-center mb-2">
                        <h1 class="mb-1">Agreement Information</h1>
                    </div>
                    <div class="row">
                        {{--                                                                <form id="agreement_form" > --}}
                        <div class="col-md-12">
                            <div class="col-md-12 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="ref_no">Ref. #</label>
                                    <input type="text" id="ref_no" class="form-control" placeholder="Enter Ref. #"
                                        name="ref_no" value="" />
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-12">
                                <label class="form-label" for="agrement_from">Agreement Duration (YYYY-MM-DD)</label>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                        <input type="text" required id="agrement_from"
                                            class="form-control flatpickr-basic" placeholder="From"
                                            name="agreement_period_from" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                        <input type="text" required id="agrement_to"
                                            class="form-control flatpickr-basic" placeholder="To"
                                            name="agreement_period_to" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
                                        <input type="text" required id="wef"
                                            class="form-control flatpickr-basic" placeholder="W.E.F"
                                            name="agreement_period_wef" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <a class="btn btn-primary mt-2 me-1" onclick="addAggrement()">Save</a>
                                <button class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                    <div class="text-center mb-2">
                        <h1 class="mb-1">Add Collection Point</h1>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12 col-12" id="appended_dataofCPS">
                                {{-- <div class="mb-1">
                                    <label class="form-label" for="cp_field">Collection Points</label>
                                    <select name="cp" id="cp_field" class="form-control select2">
                                        <option value="" selected disabled>Select Collection Point</option>
                                        @foreach ($cps as $cp)
                                            <option value="{{$cp->id}}">{{$cp->name}}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            </div>

                            <div class="col-md-12 text-center">
                                <a class="btn btn-primary mt-2 me-1" onclick="addCp()">Add</a>
                                <button class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>

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

    <script src="{{ asset('js/scripts/forms/form-select2.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css"
        integrity="sha512-8D+M+7Y6jVsEa7RD6Kv/Z7EImSpNpQllgaEIQAtqHcI0H6F4iZknRj0Nx1DCdB+TwBaS+702BGWYC0Ze2hpExQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"
        integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAfh-Jh-Vn1Lf2TeP9g9cf5bzRbX1gnFZ4&libraries=places&callback=initAutocomplete&libraries=places"
        async defer></script>
    <script>
    function initAutocomplete() {
            let zoom = 15;
            var lat = parseFloat($("#latitude").val());
            var long = parseFloat($("#longitude").val());
            const map = new google.maps.Map(document.getElementById("map-canvas"), {
                center: {
                    lat: lat,
                    lng: long
                },
                zoom: zoom,
                mapTypeControl: false,
            });

            const input = document.getElementById("address");
            const options = {
                fields: ["formatted_address", "geometry", "name"],
                strictBounds: false,
                types: ["establishment"],
                componentRestrictions: {
                    country: "pk"
                },
            };

            const autocomplete = new google.maps.places.Autocomplete(input, options);
            autocomplete.bindTo("bounds", map);
            const marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: long
                },
                map,
                draggable: true,
            });

            google.maps.event.addListener(marker, 'dragend', function(evt) {
                $("#latitude").val(evt.latLng.lat().toFixed(8));
                $("#longitude").val(evt.latLng.lng().toFixed(8));
            })

            autocomplete.addListener("place_changed", () => {
                marker.setVisible(false);
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) {
                    alert("No details available for input: '" + place.name + "'");
                    return;
                }
                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    $("#latitude").val(place.geometry.location.lat().toFixed(8));
                    $("#longitude").val(place.geometry.location.lng().toFixed(8));
                    // console.log(place.geometry.location.lng()+''+place.geometry.location.lat());
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                place.formatted_address;
            });
        }

        function showHideLOcationTab() {
            let is_by_mmt = $(".source_type option:selected").attr('by-plant');
            if (is_by_mmt == 1) {
                $('.locations').show()
                $('#latitude,#address,#longitude').removeAttr('disabled')
                $('.custom_adress_div').hide()
                $('.custom_adress').attr('disabled', 'true')
            } else {
                $('#bank-dtl-tab').click();
                $('.locations').hide();
                $('#latitude,#address,#longitude').attr('disabled', 'true')
                $('.custom_adress_div').show()
                $('.custom_adress').removeAttr('disabled')
            }
        }

        $(document).ready(function() {
            var supliertypeId = ($('.source_type').find(":selected").val())
            var areaofficeId = ($('.checkcollectionpoint').find(":selected").val())
            $('#new_supplier_type_id').val(supliertypeId)
            $('#new_areaoffice_id').val(areaofficeId)

            // let ids = $('#cp_ids').val();
            // ids = ids.split(',');
            // if (ids != '') {
            //     $('.source_type').prop('disabled', true)
            //     $(document).find('.checkcollectionpoint').prop('disabled', true)
            // }

            $('#districtSelect').change(function() {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        url: '{{ route('get.tehsils', ':id') }}'.replace(':id', id),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#tehsilSelect').empty();
                            $('#tehsilSelect').append(
                                '<option value="">Tehsil</option>');
                            $.each(data, function(key, value) {
                                $('#tehsilSelect').append('<option value="' + key +
                                    '">' +
                                    value + '</option>');
                            });
                        }
                    });
                }
            });
            //for getting collection points while dropdowns are locked
            $('.checkcollectionpoint').trigger('change')
        })

        function getData(value) {
            $('#appended_data').html('');
            showHideLOcationTab();
            $.ajax('{{ route('get.type.wise.data') }}', {
                type: 'get',
                data: {
                    id: value
                },
                success: function(data) {
                    if (data.success == true) {
                        if (data.by_mmt == '1') {
                            $('.cps').removeClass('disabled')
                        } else {
                            $('.cps').addClass('disabled')
                        }
                        $('#by_mmt').val(data.by_mmt)

                        $('#appended_data').html(data.data);
                        $('.select3').select2();
                    }
                },
                error: function(jqXhr, textStatus, errorMessage) {
                    alert('Error Occurred')
                }
            });
            $('#appended_data').show();
        }
        $(document).on('change', '.checkcollectionpoint', function() {
            var cpvalue = $(this).val()
            var sourceType = $('.source_type').val();
            // console.log(sourceType);

            var newcpvalue = cpvalue.toString();
            if (newcpvalue) {
                $('.cps').removeClass('disabled')
            } else {
                console.log("no record")
                return false;
            }


            $.ajax({
                url: '{{ route('getcollectionpointdata') }}',
                method: 'get',
                data: {
                    sourcetype: sourceType,
                    area_office_id: newcpvalue,
                },
                success: function(data) {
                    if (data.success == true) {
                        $('#appended_dataofCPS').html(data.data);

                        // // if (data.by_mmt == '1') {
                        // //     $('.cps').removeClass('disabled')
                        // // } else {
                        // //     $('.cps').addClass('disabled')
                        // // }
                        //  $('.cps').addClass('disabled')
                        // $('#by_mmt').val(data.by_mmt)

                        $('#appended_dataofCPS').show();
                        // $('.select3').select2();
                    }
                }
            })
        })
        $(document).on('submit', '#createForm', function(e) {
            e.preventDefault();
            $('#save_button').prop('disabled',true);
            $('#save_button').removeClass('btn-primary')
            $('#save_button').addClass('btn-danger')

            let data = $(this).serialize();
            $.ajax({
                url: '{{ route('supplier.update', $supplier->id) }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#save_button').prop('disabled',true);
                        showAlert('success', response.message);
                        $(window).scrollTop(20);
                        $("input").css('border', '1px solid #d8d6de')
                        $("select").css('border', '1px solid #d8d6de')

                        $('#save_button').addClass('btn-primary')
                        $('#save_button').removeClass('btn-danger')
                    } else {
                        $('#save_button').prop('disabled',false);
                        $("input").css('border', '1px solid #d8d6de')
                        $("select").css('border', '1px solid #d8d6de')

                        $.toast({
                            text: response.message,
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 7000,
                            // bgColor : 'red'
                        })
                        if (response.key) {
                            $("*[name='" + response.key + "']").focus();
                            $('#createForm').animate({
                                scrollTop: ($('input').offset().top - 10)
                            }, 1);
                            $("*[name='" + response.key + "']").css('border', '1px solid red')
                        }
                        $('#save_button').addClass('btn-primary')
                        $('#save_button').removeClass('btn-danger')
                    }
                }
            });
        });
    </script>
    <script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
    <script>
        $('.cnic').inputmask({
            mask: '99999-9999999-9'
        });
        $('.phone').inputmask({
            mask: '+\\92-999-9999999'
        });
        $('.ntn').inputmask({
            mask: '99999-9999999-9'
        });

        // script for shifting tabs
        $(document).ready(function() {
            showHideLOcationTab()

            $('#myTab a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });

        $.validator.addMethod("fixed_iban", function(value, element) {
            var iban = value.replace(/[^a-zA-Z0-9]/g, '');
            return iban.length === 24;
        }, "IBAN must be a valid 24 charcters.");

        $('#createForm').validate({
            rules: {
                bank_account_no: {
                    required: true,
                    fixed_iban: true
                }
            },
            messages: {
                bank_account_no: {
                    required: "Please enter your IBAN."
                }
                // Add other error messages for your form fields
            },
        });

        function addAggrement() {
            if ($('#agrement_from').val() == '') {
                showAlert('error', 'Atleast one field is required');
                return;
            }
            if ($('#agrement_agrement_to').val() == '') {
                showAlert('error', 'Agreement to field is required');
                return;
            }
            var fd = new FormData();
            fd.append('ref_no', $('#ref_no').val());
            fd.append('from', $('#agrement_from').val());
            fd.append('to', $('#agrement_to').val());
            fd.append('wef', $('#wef').val());
            fd.append('id', $('#supplier_id').val());
            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                url: '{{ route('add.agreement') }}',
                success: function(response) {
                    if (response.success) {
                        let html = `<tr><td>${response.count}</td><td>${$('#ref_no').val()}</td><td>${$('#agrement_from').val()}</td>
                            <td>${$('#agrement_to').val()}</td><td>${$('#wef').val()}</td>
                            <td>
                                <div class="form-check form-switch form-check-primary">
                                    <input type="checkbox" id="status_${response.count}" class="form-check-input" onclick="updateAgrementStatus('${response.count}')"  name="status" value="0">
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

        function updateAgrementStatus(key) {
            let status = $('#status_' + key).is(':checked');
            if (status) {
                status = 1;
            } else {
                status = 0;
            }
            $.ajax({
                type: "get",
                data: {
                    'id': $('#supplier_id').val(),
                    'status': status,
                    'key': key
                },
                url: '{{ route('agreement.update.status') }}',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        $('#status_' + key).prop('checked', !($('#status_' + key).is(':checked')));
                        showAlert('error', response.message);
                    }
                }
            });

        }

        var count = '{{ $supplier->cp_ids ? count($supplier->cp_ids) : 0 }}';

        function addCp() {
            if (!$('#cp_field').val()) {
                showAlert('error', 'Collection point is required');
                return;
            }
            let text = $('#cp_field option:selected').text()
            let cp_id = $('#cp_field').val()
            $('#cp_field option:selected').remove();
            let html = `<tr id="cp_row_${cp_id}"><td>${++count}</td><td>${text}</td><td><span  onclick="deleteCP(${cp_id} ,${text})" class="text-danger cursor-pointer fa fa-trash"></span></td>
                        </tr>`;
            $('.empty').html('')
            $('#cps_table > tbody:last-child').append(html)
            $("#cp_field").val('').trigger('change')
            let cps = $('#cp_ids').val();
            if (cps) {
                cps += ',' + cp_id;
            } else {
                cps = cp_id;
            }
            $('#cp_ids').val(cps)
            $('#addACpModal').modal('hide');
        }

        function deleteCP(id, name) {

            let ids = $('#cp_ids').val()
            ids = ids.split(',');
            $("#cp_field").append(new Option(name, id));
            $('#cp_row_' + id).remove()
            ids = ids.filter(function(item) {
                return item != id
            })
            if (ids == '') {
                $('.source_type').prop('disabled', false)
                $('.checkcollectionpoint').prop('disabled', false)
            }

            $('#cp_ids').val(ids.toString())
            showAlert('success', 'Deleted. Please save supplier');
        }
    </script>
@endsection
