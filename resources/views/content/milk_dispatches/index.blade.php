
@extends('layouts.contentLayoutMaster')

@section('title', ' Milk Dispatches')

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
                    <div class="row border-bottom py-2 m-1">
                        <div class="col-2">
                            <h4 class="card-title pt-1 my-auto">Dispatches
                        </h4>
                        </div>
                    </div>
                    <div class="card-body ">
                        <div class="card-datatable table-responsive">
                            <table class="table" id="milk_purchase_datatable">
                                <thead>
                                    <tr>
                                        <td colspan="3">
                                            <select class="select2 search-input" id="type">
                                                <option value="">Type</option>
                                                <option value="mmt_dispatch_plant">MMT Purchase</option>
                                                <option value="ao_dispatch_plant">Area Office </option>
                                            </select>
                                        </td>
                                     
                                        <td colspan="3">
                                            <select class="select2 search-input" id="area_office">
                                                <option value="">Area Office</option>
                                                @foreach ($areaOffices as $areaOffice)
                                                    <option value="{{ $areaOffice->id }}">{{ $areaOffice->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td colspan="3">
                                            <select class="select2 search-input" id="plant">
                                                <option value="">Plant</option>
                                                @foreach ($plants as $plant)
                                                    <option value="{{ $plant->id }}">{{ $plant->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                       
                                    </tr>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Serial No</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Route</th>
                                        <th>Area Office</th>
                                        <th>Plant</th>
                                        <th>Gross Vol</th>
                                        <th>TS Vol</th>
                                        <th>Actions</th>
                                        
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    
                               
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="remarksModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                    <div class=" mb-1">
                        <h1 class="mb-1">Remarks</h1>
                    </div>
                    <div class="table-responsive">
                        <table class="table " id="remarks_table">
                            <thead><tr><td>#</td><td>From</td><td>Remark</td><td>Time</td></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    {{-- vendor files table data --}}
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js')}}"></script>

    <script>
        // permissions checks variables
      
        var table;
        $(document).ready(function() {
            $(".select2").select2({});
            table = $('#milk_purchase_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('get.dispatches') }}",
                    data: function(d) {
                        // '_token': '{{ csrf_token() }}',
                        d.type = $('#type option:selected').val();
                        d.area_office = $('#area_office option:selected').val();
                        d.plant = $('#plant option:selected').val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },  
                    {
                        data: 'serial_number',
                        name: 'serial_number'
                    },  
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'route_name',
                        name: 'route_name'
                    },
                    {
                        data: 'area_office',
                        name: 'area_office'
                    },
                    {
                        data: 'plant_name',
                        name: 'plant_name'
                    },
                    {
                        data: 'gross_vol',
                        name: 'gross_vol'
                    },
                    {
                        data: 'ts_vol',
                        name: 'ts_vol'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                   
                ],
                dom: '<"d-flex justify-content-between align-items-center header-actions text-nowrap mx-1 row mt-75"' +
                    '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" l>' +
                    '<"col-sm-12 col-lg-8"<"dt-action-buttons d-flex align-items-center justify-content-lg-end justify-content-center flex-md-nowrap flex-wrap"<"me-1"f>>>' +
                    '><"text-nowrap" t>' +
                    '<"d-flex justify-content-between mx-2 row mb-1"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                language: {
                    paginate: {
                        // remove previous & next text from pagination
                        previous: '&nbsp;',
                        next: '&nbsp;'
                    }
                }
            });
            $(".search-input").on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
