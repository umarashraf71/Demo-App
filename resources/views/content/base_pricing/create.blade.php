
@extends('layouts/contentLayoutMaster')

@section('title', 'Create Pricing ')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
@endsection
@section('content')

    <!-- Column Search -->
    <section id="pricing_div">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row border-bottom py-2">
                        <div class="col-3">
                            <h4 class="card-title pt-1 my-auto">Add Pricing</h4>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="tabs">
                            @include('content._partials._sections.base_price_tabs')
                        </div>

                        <div class="filters mb-1">
                            @include('content._partials._sections.base_price_filter')
                        </div>
                        <div>
                             @include('content._partials._sections.add_base_price_form')
                        </div>

                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="table" id="price_table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Area Office</th>
                                <th>Source</th>
                                <th>Supplier</th>
                                <th>Collection Point</th>
                                <th>Price</th>
                                <th>Expected Volume</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $count = count($prices); @endphp
                            @foreach($prices as $key=>$price)
                                <tr id="row_{{$price->id}}" class="prices_tr">
                                    <td>{{$key+1 }}</td>
                                    <td>{{$price->areaOffice?$price->areaOffice->name:''}}</td>
                                    <td>{{$price->source?$price->source->name:''}}</td>
                                    <td>{{$price->suplier?$price->suplier->name:''}}</td>
                                    <td>{{$price->collPoint?$price->collPoint->name:''}}</td>
                                    <td>{{$price->price}}</td>
                                    <td>{{$price->volume}}</td>
                                    <td><button onclick="delRecord('{{$price->id}}')" class="btn btn-danger btn-sm"><i data-feather="trash"></i></button></td>
                                </tr>
                                @endforeach

                                    <tr id="empty_tr" style="display: {{$count==0?'':'none'}}">
                                        <td colspan="8" class="text-center">No Data Found</td>
                                    </tr>


                                    <tr id="sbt_tr" style="display: {{$count>0?'':'none'}}" >
                                        <td colspan="7"></td>
                                        <td><input type="submit" value="Submit" onclick="submitForbatch()" id="sbt_approval_btn"></td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection

@section('vendor-script')

    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('/js/custom.js')}}"></script>
    <script>
        $(document).ready(function() {
            $(".select2").select2({
                allowClear: true,
             // dropdownPosition: 'TOP'
            });
        });


        $('#created_counter').text('{{$count}}');
        function delRecord(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "get",
                        dataType: 'JSON',
                        url: '{{route('price.del')}}',
                        data: {id:id},
                            success: function (response) {
                            if (response.success) {
                                $('#row_'+ id).remove();
                               let  counter = parseInt($('#created_counter').text())-1;
                                $('#created_counter').text(counter);
                                if(counter<1){
                                    $('#empty_tr').show();
                                    $('#sbt_tr').hide();
                                }

                                Swal.fire(
                                    'Done!',
                                    response.message,
                                    'success'
                                )
                            } else {
                                Swal.fire(
                                    'Oops!',
                                    response.message,
                                    'error'
                                )
                            }
                        }
                    });
                }
            })
        }

        function submitForbatch() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't to send these entries for approval?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "get",
                        dataType: 'JSON',
                        url: '{{route('price.send.for.approval')}}',
                        success: function (response) {
                            if (response.success == true) {
                                 $('.prices_tr').remove();
                                 $('#sbt_tr').hide();
                                 $('#empty_tr').show();
                                 $('#created_counter').text('0');

                                showAlert('success', response.message);
                            } else {
                                showAlert('error', response.message);
                            }
                        }
                    });

                }
            })
        }


    </script>
@endsection
