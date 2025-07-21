@extends('layouts/contentLayoutMaster')

@section('title', 'Payment Process')

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
                    @if ($message = Session::get('error'))
                    <div class="demo-spacing-0 my-2">
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="alert-body">{{ $message }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    </div>
                @endif
                @if ($message = Session::get('success'))
                <div class="demo-spacing-0 my-2">
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body">{{ $message }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                </div>
                @endif
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Payment Calculation Process</h4>
                    </div>
                    <form action=" {{route('payment.process')}}" method="post">
                        @csrf
                       
                          <div class="row" style="margin: 10px">
                            <div class="col-md-4">
                                <label class="form-label">From</label>
                                <input type="date" name="from" class="form-control search-input">
                                @error('from')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to" class="form-control search-input">
                                @error('to')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-block" style="margin-top:23px">Run Payment Calculation</button>
                            </div>
                          </div>
                           
                    </form>
                    <div class="card-datatable table-responsive">
                        <table class="table" id="payment_process">
                            <thead>
                                <tr>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
    $('#payment_process').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('payment-calculation.index') }}",
    columns: [
    {data: 'from', name: 'from'},
    {data: 'to', name: 'to'},
    {data: 'status', name: 'status'},
    {name: 'action',data: 'action' }
    ],
    columnDefs: [
    {
        // Actions
        targets: 3,
        title: 'Actions',
        orderable: false,
        visible:true
    }
    ],
    order: [[1, 'asc']],
    dom:
    '<"d-flex justify-content-between align-items-center header-actions text-nowrap mx-1 row mt-75"' +
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
