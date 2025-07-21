@extends('layouts/contentLayoutMaster')

@section('title', 'Fresh Milk Purchase Summary')

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
    <section id="column-search-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Fresh Milk Purchase Summary</h4>
                    </div>
                    <form action=" {{route('milk.purchase.summary.exportfile')}}" method="post">
                        @csrf
                       
                          <div class="row" style="margin: 10px">
                            <div class="col-md-4">
                                <label class="form-label"> Area Office</label>

                                <select class="form-control" name="area_office_id" id="area_office_id">
                                    <option selected disabled>Please Slect AreaOffice</option>
                                    @foreach ($areaOffices as $areaOffice)
                                        <option  value="{{ $areaOffice->id }}">{{ $areaOffice->name }}
                                        </option>
                                    @endforeach
                                    
                                   
                                </select>
                                @error('area_office_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                            <div class="col-md-4">
                            <div id="append_collection_points">
                               
                            </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Export In</label>
                                <select class="form-control"  name="export_to" id="export_to">
                                    <option class="form-control" selected value="EXCEL">Excel</option>
                                </select>
                                @error('export_to')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"> From Date</label>
                                <input type="date" name="from_date" class="form-control" id="from_date">
                                @error('from_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to_date" class="form-control search-input" id="to_date">
                                @error('to_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                            
                            <div class="col-md-4">
                                

                                <button type="submit" class="btn btn-primary btn-block" style="margin-top:23px">Generate Report</button>
                            </div>
                          </div>
                           
                    </form>
                    <div class="card-datatable table-responsive">
                        
                        <table class="table" id="collection_point_datatable">
                            <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Area Office</th>
                                    <th>Type</th>
                                    <th>Time</th>
                                    <th>Collection Point</th>
                                    <th>Supplier Code</th>
                                    <th>Supplier</th>
                                    <th>Gross volume</th>
                                    <th>Ts Volume</th>
                                    <th>FAT</th>
                                    <th>Standar LR</th>
                                    <th>SNF</th>
                                </tr>
                            </thead>

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
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
@endsection

@section('page-script')
<script src="{{ asset('/js/custom.js') }}"></script>
    <script type="text/javascript">
    var table;
    $("#area_office_id").on('change',function(){
        var areaofficeId = $(this).val();
        var _token = $("input[name='_token']").val();
        $.ajax({
            url: '{{route('getcollectionpoints')}}',
            method: 'POST',
            data:{_token:_token, areaoffice_id: areaofficeId},
            success: function(response){
                if(response.success == true){
                    $('#append_collection_points').html(response.data)
                }
            }
        });
    })
    $(document).ready(function() {
        $(".select2").select2({});
        table = $('#collection_point_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ao.collection.summary') }}",
                data: function(d) {
                    d.collection_point = $('#collection_point').val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                }
            },
            columns: [{
                data: 'serial_number',
                name: 'serial_number'
            },
            {
                data: 'ao',
                name: 'ao'
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
                data: 'mcc',
                name: 'mcc'
            },
            {
                data: 'supplier_code',
                name: 'supplier_code'
            },
            {
                data: 'supplier',
                name: 'supplier'
            },
            {
                data: 'gross_volume',
                name: 'gross_volume'
            },
            {
                data: 'ts_volume',
                name: 'ts_volume'
            },
            {
                data: 'fat',
                name: 'fat'
            },
            {
                data: 'lr',
                name: 'lr'
            },
            {
                data: 'snf',
                name: 'snf'
            },
        ],
        dom: '<"d-flex justify-content-between align-items-center header-actions text-nowrap mx-1 row mt-75"' +
        '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" l>' +
        '<"col-sm-12 col-lg-8"<"dt-action-buttons d-flex align-items-center justify-content-lg-end justify-content-center flex-md-nowrap flex-wrap"<"me-1"f>>>' +'><"text-nowrap" t>' +
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
    $('body').on('change', ".search-input", function() {
        table.ajax.reload();
    });
});
</script>
@endsection
