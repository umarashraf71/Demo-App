@extends('layouts/contentLayoutMaster')

@section('title', 'Purchase Details')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
@endsection
@section('content')

    <!-- Column Search -->
    <section id="column-search-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Plant Reception Detail</h4>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>Serial Number</th>
                                    <td>MR-{{$plantDetail->serial_number ?? 'N/A'}}</td>
                                    
                                </tr>
                               
                                <tr>
                                    <th>User</th>
                                    <td>{{ $plantDetail->user->name }}</td>
                                    <th>Total Vehicle Weight</th>
                                    <td>
                                        @if( isset($plantDetail->total_vehicle_weight))
                                        {{$plantDetail->total_vehicle_weight}} 
                                        @endif 
                                    </td>                               
                                </tr>
                                <tr>
                                    <th>Opening Balance
                                       
                                    </th>
                                    <td>{{$plantDetail->opening_balance ?? '0'}}
                                   
                                    </td>
                                    <th>Type</th>
                                    <td> <span class="badge badge-glow bg-info">Plant Reception</span></td>

                                </tr>
                                <tr>
                                    <th>Time</th>
                                    <td>
                                        {{$plantDetail->date}}  
                                    </td>
                                    <th>Plant</th>
                                    <td>
                                        {{$plantDetail->plant->name ?? 'N/A'}}  
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="4" style="text-align: center; font-size:20px; color:rgb(47, 18, 195)">Gate Pass Info</th>
                                </tr>
                                <tr>
                                    <th>Gate Token</th>
                                    <td>
                                       {{$plantDetail->gate_pass_token_id ?? 'N/A'}}
                                    </td>
                                    <th>Type</th>
                                    <td>
                                        <span class="badge badge-glow bg-primary"> {{$plantDetail->gateinfo->type ?? 'N/A'}}</span>
                                       
                                    </td>
                                </tr>
                                <tr>
                                    <th>Gross Volume</th>
                                    <td>
                                       {{$plantDetail->gateinfo->gross_volume ?? 'N/A'}}
                                    </td>
                                    <th>Ts Volume</th>
                                    <td>
                                       {{$plantDetail->gateinfo->volume_ts ?? 'N/A'}}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Gate Time In</th>
                                    <td>
                                        {{date('d-M-Y H:i:s', strtotime($plantDetail->gateinfo->date_time_in) )}}
                                    </td>
                                    <th>Gate Time Out</th>
                                    <td>
                                        {{date('d-M-Y H:i:s', strtotime($plantDetail->gateinfo->gate_out_date_time) )}}

                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="4" style="text-align: center; font-size:20px; color:rgb(47, 18, 195)">Volumes</th>
                                </tr>
                                <tr>
                                    <th>Gross Volume</th>
                                    <td>{{$plantDetail->gross_volume ?? '0'}}</td>
                                    <th>TS Volume</th>
                                    <td>{{$plantDetail->volume_ts ?? '0'}}</td>
                                    
                                </tr>
                                <tr>
                                    <th>Left Over Milk</th>
                                    <td>{{ $plantDetail->left_over_milk }}</td>
                                    <th>Gain Loss</th>
                                    <td>{{ $plantDetail->gain_loss }}</td>
                                                             
                                </tr>
                               
                                <tr>
                                    <th colspan="4" style="text-align: center; font-size:20px; color:rgb(47, 18, 195)">Compartments  ({{ $plantDetail->compartments }})</th>
                                </tr>
                                <tr>
                                    <th>Compartment Status</th>
                                    <td>
                                        @if($plantDetail->compartment_status_0 == 1)
                                        <span class="badge badge-glow bg-success">Accepted</span>
                                        @else
                                        <span class="badge badge-glow bg-warning">Rejected</span>
                                        @endif
                                    </td>
                                    <th></th>
                                    <td>
                                       


                                    </td>                                   
                                </tr>
                                <tr>
                                    @if(isset($plantDetail->opening_balance_compartment_1))
                                    <th>Compartment 1 Balance</th>
                                    <td>
                                        {{$plantDetail->opening_balance_compartment_1}}  
                                    </td>
                                    @endif

                                    @if(isset($plantDetail->compartment_status_1))
                                    <th>Compartment 1 Status</th>
                                    <td>
                                        @if($plantDetail->compartment_status_1 == 1)
                                        <span class="badge badge-glow bg-success">Accepted</span>
                                        @else
                                        <span class="badge badge-glow bg-warning">Rejected</span>
                                        @endif
                                    </td>
                                    @endif
                                    
                                </tr>
                                <tr>
                                    @if(isset($plantDetail->opening_balance_compartment_2))
                                    <th>Compartment 2 Balance</th>
                                    <td>
                                        {{$plantDetail->opening_balance_compartment_2}}  
                                    </td>
                                    @endif
                                    @if(isset($plantDetail->compartment_status_2))
                                    <th>Compartment 2 Status</th>
                                    <td>
                                        @if($plantDetail->compartment_status_2 == 1)
                                        <span class="badge badge-glow bg-success">Accepted</span>
                                        @else
                                        <span class="badge badge-glow bg-warning">Rejected</span>
                                        @endif
                                    </td>
                                    @endif
                                    
                                </tr>
                                <tr>
                                    @if(isset($plantDetail->opening_balance_compartment_3))
                                    <th>Compartment 3 Balance</th>
                                    <td>
                                        {{$plantDetail->opening_balance_compartment_3}}  
                                    </td>
                                    @endif
                                    @if(isset($plantDetail->compartment_status_3))
                                    <th>Compartment 3 Status</th>
                                    <td>
                                        @if($plantDetail->compartment_status_3 == 1)
                                        <span class="badge badge-glow bg-success">Accepted</span>
                                        @else
                                        <span class="badge badge-glow bg-warning">Rejected</span>
                                        @endif
                                    </td>
                                    @endif
                                    
                                </tr>
                               
                                <tr>
                                    <th colspan="4" style="text-align: center; font-size:20px; color:rgb(47, 18, 195)">Vehicles Information</th>
                                </tr>
                                <tr>
                                   
                                    <th>Vehicle Number</th>
                                    <td>
                                       {{$plantDetail->gateinfo->vehicle->vehicle_number ?? 'N/A'}}
                                    </td>
                                   
                                    <th>Vehicle Type</th>
                                    <td>
                                        {{$plantDetail->gateinfo->vehicle->vehicle_type ?? 'N/A'}}

                                    </td>
                                </tr>
                                <tr>
                                   
                                    <th>Vehicle Capacity</th>
                                    <td>
                                        {{$plantDetail->gateinfo->vehicle->capacity ?? 'N/A'}}

                                    </td>
                                   
                                    <th>Vehicle Portion</th>
                                    <td>
                                        {{$plantDetail->gateinfo->vehicle->portion ?? 'N/A'}}

                                    </td>
                                </tr>
                                <tr>
                                   
                                    <th>Vehicle Make</th>
                                    <td>
                                        {{$plantDetail->gateinfo->vehicle->make ?? 'N/A'}}

                                    </td>
                                   
                                    <th>Vehicle Model</th>
                                    <td>
                                        {{$plantDetail->gateinfo->vehicle->model ?? 'N/A'}}

                                    </td>
                                </tr>
                                <tr>
                                   
                                    <th>Vehicle Tag No</th>
                                    <td>
                                        {{$plantDetail->gateinfo->vehicle->tag_no ?? 'N/A'}}

                                    </td>
                                   
                                    <th>Vehicle Tanker Capacity</th>
                                    <td>
                                        {{$plantDetail->gateinfo->vehicle->tanker_capacity ?? 'N/A'}}

                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="4" style="text-align: center; font-size:20px; color:rgb(47, 18, 195)">Vehicles Weight</th>
                                </tr>
                                <tr>
                                    @if(isset($plantDetail->total_vehicle_weight_after_c1))
                                    <th>Vehicle Weight After c1</th>
                                    <td>
                                        {{$plantDetail->total_vehicle_weight_after_c1}}  
                                    </td>
                                    @endif
                                    @if(isset($plantDetail->total_vehicle_weight_after_c2))
                                    <th>Vehicle Weight After c2</th>
                                    <td>
                                        {{$plantDetail->total_vehicle_weight_after_c2}}  
                                    </td>
                                    @endif

                                    
                                </tr>
                                <tr>
                                    @if(isset($plantDetail->total_vehicle_weight_after_c3))
                                    <th>Vehicle Weight After c3</th>
                                    <td>
                                        {{$plantDetail->total_vehicle_weight_after_c3}}  
                                    </td>
                                    @endif
                                   
                                </tr>
                              
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Milk Dispatches</h4>
                    </div>

                  
                    <div class="card-datatable table-responsive">
                        <table class="table" id="">
                            <thead>
                                <tr>
                                   
                                    <th>Serial No</th>
                                    <th>Created By</th>
                                    <th>Type</th>
                                    <th>Gross Volume</th>
                                    <th>TS Volume</th>
                                    <th>Plant</th>
                                    <th>Area Office</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plantDetail->gateinfo->milkDispatches as $mlkdisptchs)
                                <tr>
                                    {{-- @dd($mlkdisptchs->areaOffice->ao_name) --}}
                                   
                                    <td>{{$mlkdisptchs->serial_number ?? 'N/A'}}</td>
                                    <td>{{$mlkdisptchs->user->name ?? 'N/A'}}</td>
                                    <td>
                                        @if($mlkdisptchs->type == 'ao_dispatch_plant')

                                    <span class="badge badge-glow bg-success">Ao Dispatch Plant</span>
                                    @endif


                                      
                                    </td>
                                    <td>{{$mlkdisptchs->gross_volume ?? '0'}}</td>
                                    <td>{{$mlkdisptchs->ts_volume ?? '0'}}</td>
                                    <td>{{$mlkdisptchs->plant->name ?? 'N/A'}}</td>
                                    <td>{{$mlkdisptchs->areaOffice->name ?? 'N/A'}}</td>
                                   
                                </tr>
                                @endforeach
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Tests</h4>
                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="table" id="test_table">
                            <thead>
                                <tr>
                                   
                                    <th>QA Test Name</th>
                               
                                    <th>Test Data Type</th>
                                    <th>Test Type</th>
                                    <th>Compartment</th>
                                    <th>Unit of Measure</th>
                                    <th>Value</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            {{-- <tbody>
                                @foreach($plantDetail->tests as $key => $value)
                                <tr>
                                    <td>{{$value['referenceNumber'] }}</td>
                                    <td>{{$value['qa_test_name'] }}</td>
                                  
                                    <td>{{$value['test_data_type']}}</td>
                                    <td>{{$value['test_type']}}</td>
                                    <td>{{$value['compartment']}}</td>
                                    <td>{{$value['unit_of_measure']}}</td>
                                    <td>{{$value['value']}}</td>
                                    <td>{{$value['status']}}</td>
                                   
                                </tr>
                                @endforeach
                               
                            </tbody> --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Column Search -->
@endsection


@section('vendor-script')
    {{-- vendor files table data --}}
    <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script>
        var tests = '<?php echo json_encode($plantDetail->tests); ?>';
        $('#test_table').DataTable({
            processing: true,
            data: JSON.parse(tests),
            columns: [{
                    data: 'qa_test_name'
                },
                {
                    data: 'test_data_type'
                },
                {
                    data: 'test_type'
                },
                {
                    data: 'compartment'
                },
                {
                    data: 'unit_of_measure'
                },
                {
                    data: 'value'
                },
                {
                    data: 'status'
                }
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
    </script>
@endsection
