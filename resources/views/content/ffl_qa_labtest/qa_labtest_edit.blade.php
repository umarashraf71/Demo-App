@extends('layouts/contentLayoutMaster')

@section('title', 'Edit QA Lab Test')
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
@endsection

<style>
    .error {
        color: red
    }
</style>
@section('content')
    <!-- Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit QA Lab Test</h4>
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
                        <form class="form" action="{{ route('qa-labtest.update', $qaLabTest->id) }}" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-1">QA Test Name</label>
                                        <input type="text" id="column-1" class="form-control" placeholder="QA Test Name"
                                            name="qa_test_name" value="{{ @old('qa_test_name', $qaLabTest->qa_test_name) }}"
                                            readonly />
                                        @error('labtest_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="column-1">Description</label><span
                                            class="text-danger">*</span>
                                        <input type="text" id="column-1" class="form-control"
                                            placeholder="QA Test Description" name="description"
                                            value="{{ @old('description', $qaLabTest->description) }}" />
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-basic">Test Type</label><span
                                            class="text-danger">*</span>
                                        <select class="select2 form-select" id="select2-basic" name="test_type">
                                            <option value="1"
                                                {{ old('test_type', $qaLabTest->test_type) == '1' ? 'selected' : '' }}>
                                                Quantitative</option>
                                            <option value="2"
                                                {{ old('test_type', $qaLabTest->test_type) == '2' ? 'selected' : '' }}>
                                                Qualitative</option>
                                        </select>
                                        @error('test_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-basic">Test Data Type</label><span
                                            class="text-danger">*</span>
                                        <select class="select2 form-select change_test_type" id="select2"
                                            name="test_data_type">
                                            <option value="1"
                                                {{ $qaLabTest->test_data_type == '1' ? 'selected' : '' }} data-status="1">
                                                Range</option>
                                            <option value="2"
                                                {{ $qaLabTest->test_data_type == '2' ? 'selected' : '' }} data-status="2">
                                                Positive/Negative</option>
                                            <option value="3"
                                                {{ $qaLabTest->test_data_type == '3' ? 'selected' : '' }} data-status="3">
                                                Yes/No</option>
                                        </select>
                                        @error('test_data_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 range_value">
                                    <div class="mb-1">
                                        <label class="form-label" for="range_value">Min Value</label>
                                        <input class="form-control" min="0.01" max="580"
                                            placeholder="Enter Min Value" type="number" name="min" id="min"
                                            value="{{ $qaLabTest->min }}">
                                        @error('min')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 range_value">
                                    <div class="mb-1">
                                        <label class="form-label" for="range_value">Max Value</label>
                                        <input class="form-control" min="0.01" max="580"
                                            placeholder="Enter Max Value" type="number" name="max" id="max"
                                            value="{{ $qaLabTest->max }}">
                                        @error('max')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 positive_negative">
                                    <div class="mb-1">
                                        <label class="form-label" for="positive_negative">Positive OR Negative</label>
                                        <select class="select2 form-select" id="positive_negative"
                                            name="positive_negative">
                                            <option value="1"
                                                {{ old('positive_negative', $qaLabTest->positive_negative) == '1' ? 'selected' : '' }}>
                                                +ve</option>
                                            <option value="0"
                                                {{ old('positive_negative', $qaLabTest->positive_negative) == '0' ? 'selected' : '' }}>
                                                -ve</option>
                                        </select>
                                        @error('positive_negative')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 yes_or_no">
                                    <div class="mb-1">
                                        <label class="form-label" for="yes_or_no">Default Value</label>
                                        <select class="select2 form-select" id="yes_or_no" name="yes_or_no">
                                            <option value="1"
                                                {{ old('yes_or_no', $qaLabTest->yes_or_no) == '1' ? 'selected' : '' }}>Yes
                                            </option>
                                            <option value="0"
                                                {{ old('yes_or_no', $qaLabTest->yes_or_no) == '0' ? 'selected' : '' }}>No
                                            </option>
                                        </select>
                                        @error('value')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-multiple">Test UOM *</label>
                                        <select class="select2 form-select" id="select2-multiple"
                                            name="measurementunit_id">
                                            @foreach ($test_uom as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ old('measurementunit_id', $qaLabTest->measurementunit_id) == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('measurementunit_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-basic">Test Applied *</label>
                                        {{--                    @dd($qaLabTest->apply_test) --}}
                                        <select class="select2 form-select" id="select2-basic" name="apply_test[]"
                                            multiple>
                                            <option value="1" @if (isset($qaLabTest->apply_test) && in_array(1, $qaLabTest->apply_test)) selected @endif>MCC
                                            </option>
                                            <option value="2" @if (isset($qaLabTest->apply_test) && in_array(2, $qaLabTest->apply_test)) selected @endif>MMT
                                            </option>
                                            <option value="3" @if (isset($qaLabTest->apply_test) && in_array(3, $qaLabTest->apply_test)) selected @endif>Area
                                                Lab</option>
                                            <option value="4" @if (isset($qaLabTest->apply_test) && in_array(4, $qaLabTest->apply_test)) selected @endif>Plant
                                                Lab</option>
                                        </select>
                                        @error('apply_test')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 col-12" id="range_rejection">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-basic">Rejection</label><span
                                            class="text-danger">*</span>
                                        <select class="select2 form-select select2-basic" name="rejection"
                                            data-placeholder="Rejection">
                                            <option value="3" {{ $qaLabTest->rejection == '3' ? 'selected' : '' }}>
                                                Greater than Maximum Value</option>
                                            <option value="4" {{ $qaLabTest->rejection == '4' ? 'selected' : '' }}>
                                                Less than Minimum Value</option>
                                            <option value="5" {{ $qaLabTest->rejection == '5' ? 'selected' : '' }}>
                                                Out
                                                of Value Range</option>
                                            <option value="8" {{ $qaLabTest->rejection == '8' ? 'selected' : '' }}>No
                                                Rejection</option>
                                        </select>
                                        @error('rejection')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12" id="positive_negative_rejection" style="display: none">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-basic">Rejection *</label>
                                        <select class="select2 form-select select2-basic" name="rejection">
                                            <option id="psv" value="1"
                                                {{ $qaLabTest->rejection == '1' ? 'selected' : '' }}>+ve</option>
                                            <option value="2" {{ $qaLabTest->rejection == '2' ? 'selected' : '' }}>
                                                -ve
                                            </option>
                                            <option value="8" {{ $qaLabTest->rejection == '8' ? 'selected' : '' }}>No
                                                Rejection</option>
                                        </select>
                                        @error('rejection')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12" id="yes_no_rejection" style="display: none">
                                    <div class="mb-1">
                                        <label class="form-label" for="select2-basic">Rejection *</label>
                                        <select class="select2 form-select select2-basic" name="rejection">
                                            <option value="6" {{ $qaLabTest->rejection == '6' ? 'selected' : '' }}>
                                                Yes
                                            </option>
                                            <option value="7" {{ $qaLabTest->rejection == '7' ? 'selected' : '' }}>No
                                            </option>
                                            <option value="8" {{ $qaLabTest->rejection == '8' ? 'selected' : '' }}>No
                                                Rejection</option>
                                        </select>
                                        @error('rejection')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-1">
                                        <div class="form-check form-check-inline mt-1">

                                            <input class="form-check-input" type="checkbox" id="exceptional_release"
                                                name="exceptional_release" value="1"
                                                {{ old('exceptional_release', $qaLabTest->exceptional_release) == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="exceptional_release">Exceptional Release
                                                @FFL Plant And Area Office Only</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-1">
                                        <div class="form-check form-check-inline mt-1 is_test_base_div">
                                            <input class="form-check-input"
                                                {{ $qaLabTest->is_test_based == 1 ? 'checked' : '' }} type="checkbox"
                                                id="is_test_based" name="is_test_based" value="1">
                                            <label class="form-check-label" for="is_test_based">Is Test Based</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary me-1">Submit</button>
                                    <button type="reset" class="btn btn-outline-secondary me-1">Reset</button>
                                    <a class="btn btn-outline-secondary cancel-btn"
                                        href="{{ route('qa-labtest.index') }}">Cancel</a>
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
    <script src="{{ asset('vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2-basic').select2();
        });

        //   change type
        $('.positive_negative, .yes_or_no').hide();
        $(document).on('change', '.change_test_type', function() {
            var opt = $(':selected', this).attr("data-status");
            if (opt == 1) {
                $('.range_value').show(300);
                $('.positive_negative, .yes_or_no').hide(300);
                $('.is_test_base_div').show(300);

                $('#range_rejection').show(300);
                $('#range_rejection').find('option').removeAttr('disabled');
                $('#positive_negative_rejection, #yes_no_rejection').find('option').attr('disabled', true);
                $('#positive_negative_rejection, #yes_no_rejection').hide(300);
            } else if (opt == 2) {
                $('.positive_negative').show(300);
                $('.yes_or_no, .range_value').hide(300);
                $('.is_test_base_div').hide(300);

                $('#positive_negative_rejection').show(300);
                $('#positive_negative_rejection').find('option').removeAttr('disabled');
                $('#range_rejection, #yes_no_rejection').find('option').attr('disabled', true);
                $('#range_rejection, #yes_no_rejection').hide(300);
            } else if (opt == 3) {
                $('.yes_or_no').show(300);
                $('.positive_negative, .range_value').hide(300);
                $('.is_test_base_div').hide(300);

                $('#yes_no_rejection').show(300);
                $('#yes_no_rejection').find('option').removeAttr('disabled');
                $('#range_rejection, #positive_negative_rejection').find('option').attr('disabled', true);
                $('#range_rejection, #positive_negative_rejection').hide(300);
            }
        });
        $(document).ready(function() {
            $(".change_test_type").trigger('change');
        });


        $('.form').validate({ // initialize the plugin
            rules: {
                min: {
                    required: true,
                    number: true,
                    max: 580,
                    min: 0,
                },
                max: {
                    required: true,
                    number: true,
                    max: 580,
                    min: 0,
                    greaterThan: '#min'
                }
            },
            messages: {
                max: {
                    greaterThan: "Max must be greater than min"
                }
            }
        });

        $.validator.addMethod("greaterThan",
            function(value, element, params) {
                var minVal = parseFloat($(params).val());
                return parseFloat(value) > minVal;
            }, "Max must be greater than min"
        );
    </script>
@endsection
