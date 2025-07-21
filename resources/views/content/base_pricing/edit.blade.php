@extends('layouts/contentLayoutMaster')
@section('title', 'Edit Pricing')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
@endsection
@section('content')
    @php
        $is_update = 0;
    @endphp
    <!-- Column Search -->
    <section id="pricing_div">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row border-bottom py-2">
                        <div class="col-3">
                            <h4 class="card-title pt-1 my-auto">Update Price</h4>
                        </div>
                        <div class="col-2 offset-7  text-center" >
                            <h4 class="card-title pt-1 my-auto">
                                 <a href="{{request('code')?route('price.reverted'):route('price.index')}}" class="card-title" style="padding:8px;border-radius:35%; background: #f3f2f7"><i data-feather="corner-up-left"></i> back</a>
                            </h4>
                        </div>
                    </div>
                    <div class="card-body">

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
                            <form action="#" id="edit_price">
                            @foreach($prices as $key=>$price)

                                @php
                                    $is_update = ($price->update_request)?1:0;
                                @endphp
                                <tr id="row_{{$price->id}}" class="prices_tr">
                                    <td>{{$key+1}}</td>
                                    <td>{{$price->areaOffice?$price->areaOffice->name:''}}</td>
                                    <td>{{$price->source?$price->source->name:''}}</td>
                                    <td>{{$price->suplier?$price->suplier->name:''}}</td>
                                    <td>{{$price->collPoint?$price->collPoint->name:''}}</td>
                                    <td>
                                        @if($is_update && !$is_reverted_request)
                                            {{$price->price}}({{$price->update_price}})
                                        @else
                                            <input name="price[]" required type="number" class="form-control width-100" value="{{$is_reverted_request && $price->update_price?$price->update_price:$price->price}}" min="0" max="10000" step=".01">
                                            <input type="hidden" name="id[]" value="{{$price->id}}">
                                            <input type="hidden" name="type" value="{{request('code')?"revert":""}}">
                                            <input type="hidden" name="code" value="{{request('code')}}">
                                        @endif
                                    </td>
                                    <td>
                                        @if($is_update && !$is_reverted_request)
                                            {{$price->volume}}({{$price->update_volume}})
                                        @else
                                          <input name="volume[]" required type="number" class="form-control width-100" value="{{$is_reverted_request && $price->update_volume?$price->update_volume:$price->volume}}" min="0" max="100000" step=".01"></td>
                                        @endif
                                    <td>
                                    @if(!$is_update  && !request('code'))
                                        <a onclick="delRow('{{$price->id}}')"  class="btn btn-danger btn-sm del_btn"><i data-feather="trash"></i></a>
                                    @endif
                                </td>
                                </tr>
                                @endforeach
                                @if(!$is_update || $is_reverted_request)
                                    <tr id="sbt_tr" >
                                        <td colspan="7"></td>
                                        <td><input type="submit" value="Submit" id="sbt_approval_btn"></td>
                                    </tr>
                                @endif
                            </form>
                            </tbody>
                        </table>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-script')
    <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('/js/custom.js')}}"></script>

    <script>
        $( "#edit_price").submit(function( e ) {
            e.preventDefault();
            let code = '{{request('code')}}';
            let msg =''
            if(!code){
               msg =' for approval';
            }

            let data  = $(this).serialize();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't to send these prices"+msg+"?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        data:data,
                        type: "get",
                        dataType: 'JSON',
                        url: '{{route('price.send.for.update')}}',
                        success: function (response) {
                            if (response.success == true) {
                                showAlert('success', response.message);
                                $('#sbt_approval_btn').hide()
                                $('.del_btn').hide()
                            } else {
                                showAlert('error', response.message);
                            }
                        }
                    });
                }
            })
            })



    function delRow(id){
        if($('.prices_tr').length>1){
             $('#row_'+id).remove();
        }else{
            showAlert('error', 'At least 1 entry is required');
        }

    }

    </script>






@endsection
