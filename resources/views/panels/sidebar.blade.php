@php
    $configData = Helper::applClasses();
    $user = auth()->user();
    $segment1 = request()->segment(1);
    $segment2 = request()->segment(2);
@endphp
<div class="main-menu menu-fixed {{ $configData['theme'] === 'dark' || $configData['theme'] === 'semi-dark' ? 'menu-dark' : 'menu-light' }} menu-accordion menu-shadow"
    data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <span class="brand-logo">
                        @if ($configData['theme'] === 'dark')
                            <img class="img-fluid" src="{{ asset('images/logo/white-ffl.png') }}" alt="Login V2"
                                width="90" id="logo" />
                        @else
                            <img class="img-fluid" src="{{ asset('images/logo/ffl.png') }}" alt="Login V2"
                                width="90" id="logo" />
                        @endif
                    </span>
                    <h2 class="brand-text">MCAS</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pe-0" data-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc"
                        data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            {{-- dashboard --}}
            <li class="nav-item @if ('dashboard' === $segment1) active @endif">
                <a href="{{ url('dashboard') }}" class="d-flex align-items-center">
                    <i data-feather="home"></i>
                    <span class="menu-title text-truncate">Dashboard</span>
                </a>
                {{-- <ul class="menu-content">
                  <li @if ('dashboard-analytics' === $segment1) class="active" @endif>
                    <a href="{{url('dashboard/analytics')}}" class="d-flex align-items-center">
                      <i data-feather="circle"></i>
                      <span class="menu-item text-truncate">Analytics</span>
                    </a>
                  </li>
                  <li @if ('dashboard-ecommerce' === $segment1) class="active" @endif>
                    <a href="{{url('dashboard/ecommerce')}}" class="d-flex align-items-center">
                      <i data-feather="circle"></i>
                      <span class="menu-item text-truncate">eCommerce</span>
                    </a>
                  </li>
                </ul> --}}
            </li>

            <li class="navigation-header">
                {{--  <i data-feather="tool"></i> --}}
                <span>Setup</span>
                <i data-feather="settings"></i>
            </li>
         
                {{-- Organization --}}
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="briefcase"></i>
                        <span class="menu-title text-truncate">Organization</span>
                    </a>
                    <ul class="menu-content">
                            <li @if ('plant' === $segment1) class="active" @endif>
                                <a href="{{ route('plant.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Plants</span>
                                </a>
                            </li>
                            <li @if ('dept' === $segment1) class="active" @endif>
                                <a href="{{ route('dept.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Departments</span>
                                </a>
                            </li>
                            <li @if ('section' === $segment1) class="active" @endif>
                                <a href="{{ route('section.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Sections</span>
                                </a>
                            </li>
                            <li @if ('zone' === $segment1) class="active" @endif>
                                <a href="{{ route('zone.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Zones</span>
                                </a>
                            </li>
                            <li @if ('area-office' === $segment1) class="active" @endif>
                                <a href="{{ route('area-office.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Area Offices</span>
                                </a>
                            </li>
                             <li @if ('collection-point' === $segment1) class="active" @endif>
                                <a href="{{ route('collection-point.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Collection Points</span>
                                </a>
                            </li>
                        

                    </ul>
                </li>
          

            {{-- Supplier --}}
            @canany(['View Source Type', 'View Supplier', 'View Categories', 'View Delivery Configuration'])
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="user"></i>
                        <span class="menu-title text-truncate">Suppliers</span>
                    </a>
                    <ul class="menu-content">
                        @can('View Supplier')
                            <li @if ('supplier' === $segment1) class="active" @endif>
                                <a href="{{ route('supplier.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Supplier Profiles</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Categories')
                            <li @if ('categories' === $segment1) class="active" @endif>
                                <a href="{{ route('categories.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Categories</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Source Type')
                            <li @if ('source-type' === $segment1) class="active" @endif>
                                <a href="{{ route('source-type.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Source Types</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Delivery Configuration')
                            <li @if ('supplier-delivery-confoguration' === $segment1) class="active" @endif>
                                <a href="{{ route('supplier.type.delivery.configuration') }}"
                                    class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Delivery Config</span>
                                </a>
                            </li>
                        @endcan

                        {{--            @canany(['View Incentive Configuration']) --}}
                        {{--            <li @if ('supplier-incentive-configuration' === $segment1) class="active" @endif> --}}
                        {{--              <a href="{{route('supplier.incentive.configuration')}}" class="d-flex align-items-center"> --}}
                        {{--                <i data-feather="circle"></i> --}}
                        {{--                <span class="menu-item text-truncate">Incentives Config</span> --}}
                        {{--              </a> --}}
                        {{--            </li> --}}
                        {{--            @endcanany --}}

                    </ul>
                </li>
            @endcanany

            {{-- Logistics --}}
            @canany(['View MCVehicle', 'View Vendor Profile'])
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="truck"></i>
                        <span class="menu-title text-truncate">Inbound Logistics</span>
                    </a>
                    <ul class="menu-content">
                        @can('View MCVehicle')
                            <li @if ('mc-vehicle' === $segment1) class="active" @endif>
                                <a href="{{ route('mc-vehicle.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Vehicles</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Vendor Profile')
                            <li @if ('vendor-profile' === $segment1) class="active" @endif>
                                <a href="{{ route('vendor-profile.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Vendors</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany
            @canany(['View Route Plan', 'View Route Vehicles'])
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="file-text"></i>
                        <span class="menu-title text-truncate">Route Plan</span>
                    </a>
                    <ul class="menu-content">
                        @can('View Route Vehicles')
                            <li @if ('route-vehicle' === $segment1) class="active" @endif>
                                <a href="{{ route('route-vehicle.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Route Vehicles</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Route Plan')
                            <li @if ('routes' === $segment1) class="active" @endif>
                                <a href="{{ route('routes.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Routes</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            {{--  Inventory --}}
            @if ($user->canany(['View Inventory Item', 'View Inventory Item Type']))
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="package"></i>
                        <span class="menu-title text-truncate">Inventory</span>
                    </a>
                    @can('View Inventory Item')
                        <ul class="menu-content">
                            <li @if ('inventory-item' === $segment1) class="active" @endif>
                                <a href="{{ route('inventory-item.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Items</span>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    @can('View Inventory Item Type')
                        <ul class="menu-content">
                            <li @if ('inventory-item-type' === $segment1) class="active" @endif>
                                <a href="{{ route('inventory-item-type.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Item Types</span>
                                </a>
                            </li>
                        </ul>
                    @endcan
                </li>
            @endif

            @canany(['Milk Purchases', 'Milk Receptions', 'Milk Rejections', 'Milk Transfers','Milk Dispatches','Plant Receptions'])
                <li class="nav-item">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="package"></i>
                        <span class="menu-title text-truncate">Operations</span>
                    </a>
                    @can('Milk Purchases')
                        <ul class="menu-content">
                            <li class="@if ('purchases' === $segment1) active @endif">
                                <a href="{{ route('get.purchases') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-title text-truncate">Purchases</span>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    @can('Milk Receptions')
                        <ul class="menu-content">
                            <li class="@if ('purchase-receptions' === $segment1) active @endif">
                                <a href="{{ route('get.purchase.receptions') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-title text-truncate">Receptions</span>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    @can('Plant Receptions')
                    <ul class="menu-content">
                        <li class="@if ('plant-receptions' === $segment1) active @endif">
                            <a href="{{ route('get.plant.receptions') }}" class="d-flex align-items-center">
                                <i data-feather="circle"></i>
                                <span class="menu-title text-truncate">Plant Receptions</span>
                            </a>
                        </li>
                    </ul>
                @endcan
                    @can('Milk Transfers')
                        <ul class="menu-content">
                            <li class="@if ('transfers' === $segment1) active @endif">
                                <a href="{{ route('transfers') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-title text-truncate">Transfers</span>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    @can('Milk Dispatches')
                    <ul class="menu-content">
                        <li class="@if ('dispatches' === $segment1) active @endif">
                            <a href="{{ route('dispatches') }}" class="d-flex align-items-center">
                                <i data-feather="circle"></i>
                                <span class="menu-title text-truncate">Dispatches</span>
                            </a>
                        </li>
                    </ul>
                    @endcan
                    @can('Milk Rejections')
                    <ul class="menu-content">
                        <li class="@if ('rejections' === $segment1) active @endif">
                            <a href="{{ route('get.rejections') }}" class="d-flex align-items-center">
                                <i data-feather="circle"></i>
                                <span class="menu-title text-truncate">Rejections</span>
                            </a>
                        </li>
                    </ul>
                    @endcan
                    @can('Milk Purchased Rejections')
                    <ul class="menu-content">
                        <li class="@if ('purchased-rejections' === $segment1) active @endif">
                            <a href="{{ route('get.purchasedRejections') }}" class="d-flex align-items-center">
                                <i data-feather="circle"></i>
                                <span class="menu-title text-truncate">Purch Rejections</span>
                            </a>
                        </li>
                    </ul>
                    @endcan
                </li>
            @endcanany

            @can('View Base Pricing')
                <li class="nav-item @if ('base-pricing' === $segment1) active @endif">
                    <a href="{{ route('price.index') }}" class="d-flex align-items-center">
                        <i data-feather="dollar-sign"></i>
                        <span class="menu-title text-truncate">Base Pricing</span>
                    </a>
                </li>
            @endcan

            {{--  Customers --}}
            @if ($user->can('View Customer'))
                <li class="nav-item @if ('customer' === $segment1) active @endif">
                    <a href="{{ route('customer.index') }}" class="d-flex align-items-center">
                        <i data-feather="user"></i>
                        <span class="menu-title text-truncate">Customers</span>
                    </a>
                </li>
            @endif
            {{-- QA Lab Test --}}
            @canany(['View Incentive Rates', 'View Test Based Supplier Incentives', 'View Incentive Type'])
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="database"></i>
                        <span class="menu-title text-truncate">Incentives</span>
                    </a>
                    <ul class="menu-content">

                        @can('View Incentive Rates')
                            <li @if ('index' === $segment2) class="active" @endif>
                                <a href="{{ route('incentives.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Incentives Rates</span>
                                </a>
                            </li>
                        @endcan

                        @can('View Test Based Supplier Incentives')
                            <li @if ('test-based-suppliers-incentives' === $segment2) class="active" @endif>
                                <a href="{{ route('incentives.test.based') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Test Base Incentives</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Incentive Type')
                            <li @if ('types' === $segment2) class="active" @endif>
                                <a href="{{ route('incentives.types') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Incentive Types</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @if ($user->can('View QaLabTest') || $user->can('View Test UOM'))
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="thermometer"></i>
                        <span class="menu-title text-truncate">QA Tests</span>
                    </a>
                    <ul class="menu-content">
                        @can('View QaLabTest')
                            <li @if ('qa-labtest' === $segment1) class="active" @endif>
                                <a href="{{ route('qa-labtest.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">QA Tests</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Test UOM')
                            <li @if ('test-uom' === $segment1) class="active" @endif>
                                <a href="{{ route('test-uom.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Test UOM</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            {{--- Reports Start  ---}}
             @if ($user->canany(['AO Collection Summary']))
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="package"></i>
                        <span class="menu-title text-truncate">Reports</span>
                    </a>
                    @can('AO Collection Summary')
                        <ul class="menu-content">
                            <li @if ('area-office-collection-summary' === $segment1) class="active" @endif>
                                <a href="{{ route('ao.collection.summary') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">AO Collection Summry</span>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    <ul class="menu-content">
                        <li @if ('fresh-milk-purchase-summary' === $segment1) class="active" @endif>
                            <a href="{{ route('milk.purchase.summary') }}" class="d-flex align-items-center">
                                <i data-feather="circle"></i>
                                <span class="menu-item text-truncate">Milk Purchase Summary</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            {{--- Reports End  ---}}
            @can('Payment Process View')
                <li class="nav-item @if ('payment-calculation' === $segment1) active @endif">
                    <a href="{{ route('payment-calculation.index') }}" class="d-flex align-items-center">
                        <i data-feather="dollar-sign"></i>
                        <span class="menu-title text-truncate">Payment Calculation</span>
                    </a>
                </li>
            @endcan


            @if (
                $user->can('View Users') ||
                    $user->can('Create Users') ||
                    $user->can('View Roles') ||
                    $user->can('Create Roles') ||
                    $user->can('View Permissions') ||
                    $user->can('Create Permissions'))
                <li class="navigation-header">
                    <span>Settings</span>
                    <i data-feather="tool"></i>
                </li>
            @endif

            {{-- users --}}
            @if ($user->can('View Users') || $user->can('Create Users'))
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="users"></i>
                        <span class="menu-title text-truncate">User Management</span>
                    </a>
                    <ul class="menu-content">
                        @can('View Users')
                            <li @if ('users' === $segment1) class="active" @endif>
                                <a href="{{ route('users.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Users Profile</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Roles')
                            <li @if ('roles' === $segment1) class="active" @endif>
                                <a href="{{ route('roles.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Roles</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Permissions')
                            <li @if ('permissions' === $segment1) class="active" @endif>
                                <a href="{{ route('permissions.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Permissions</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            @canany(['View Workflow', 'Milk Base Price', 'Milk Transfer (mcc to mcc)', 'Milk Transfer (ao to ao)'])
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="users"></i>
                        <span class="menu-title text-truncate">Workflows</span>
                    </a>

                    <ul class="menu-content">
                        @canany(['Milk Base Price', 'Milk Transfer (mcc to mcc)', 'Milk Transfer (ao to ao)'])
                            <li @if ('workflow-approvals' === $segment1 || $segment2 === 'transfer' || $segment2 == 'batch-prices') class="active" @endif>
                                <a href="{{ route('workflow.approvals.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Approvals</span>
                                </a>
                            </li>
                        @endcanany
                        @can('View Workflow')
                            <li @if ('workflow' === $segment1 && $segment2 === null) class="active" @endif>
                                <a href="{{ route('workflow.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Configuration</span>
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcanany

            @canany(['View Bank', 'View District', 'View Tehsil'])
                <li class="nav-item ">
                    <a href="#" class="d-flex align-items-center">
                        <i data-feather="users"></i>
                        <span class="menu-title text-truncate">Settings</span>
                    </a>
                    <ul class="menu-content">
                        @can('View Bank')
                            <li @if ('banks' === $segment1) class="active" @endif>
                                <a href="{{ route('banks.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Banks</span>
                                </a>
                            </li>
                        @endcan
                        @can('View District')
                            <li @if ('districts' === $segment1) class="active" @endif>
                                <a href="{{ route('districts.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Districts</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Tehsil')
                            <li @if ('tehsils' === $segment1) class="active" @endif>
                                <a href="{{ route('tehsils.index') }}" class="d-flex align-items-center">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">Tehsils</span>
                                </a>
                            </li>
                        @endcan
                        <li @if ('logs' === $segment1) class="active" @endif>
                            <a href="{{ route('logs.index') }}" class="d-flex align-items-center">
                                <i data-feather="circle"></i>
                                <span class="menu-item text-truncate">logs</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcanany
        </ul>
    </div>
</div>
<script type="text/javascript">
    function changeThememode(element) {
        var mode = $(element).prop('id');
        formData = new FormData();
        formData.append('mode', mode);
        $.ajax({
            url: "{{ route('chnage.theme.mode') }}",
            processData: false,
            contentType: false,
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (mode == 'dark') {
                    $(element).prop('id', 'light');
                    if (window.location.origin == 'http://devdigital.exdnow.com')
                        $('#logo').prop('src', '' + window.location.origin +
                            '/mcas/images/logo/white-ffl.png');
                    else
                        $('#logo').prop('src', '' + window.location.origin + '/images/logo/white-ffl.png');
                } else {
                    $(element).prop('id', 'dark');
                    if (window.location.origin == 'http://devdigital.exdnow.com')
                        $('#logo').prop('src', '' + window.location.origin + '/mcas/images/logo/ffl.png');
                    else
                        $('#logo').prop('src', '' + window.location.origin + '/images/logo/ffl.png');
                }
            }
        });
    }
</script>
<!-- END: Main Menu-->
