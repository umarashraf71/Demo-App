@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/plugins/extensions/ext-component-toastr.css') }}">
@endsection
@section('page-style')
@endsection
@php
    $configData = Helper::applClasses();
@endphp

@section('content')
    <!-- Dashboard Analytics Start -->
    <section id="dashboard-analytics">
        <section id="chartjs-chart">
            <div class="row charts">
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                            <div class="header-left">
                                <h4 class="card-title">Balances As On
                                    {{ \Carbon\Carbon::now()->format('d-M-y') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="stackedChart" class="bar-chart-ex chartjs" data-height="1200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                            <div class="header-left">
                                <h4 class="card-title">Balances Of
                                    {{ \Carbon\Carbon::now()->format('d-M-y') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="milkPurchasesAtDate" class="bar-chart-ex chartjs" data-height="1200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row charts">
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                            <div class="header-left">
                                <h4 class="card-title">Area Office Collection History (7 Days)
                                </h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="myChart" class="bar-chart-ex chartjs" data-height="1200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                            <div class="header-left">
                                <h4 class="card-title">Over All Collection Trend (7 Days)</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="lineChart" class="bar-chart-ex chartjs" data-height="1200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row charts">
                <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                            <div class="header-left">
                                <h4 class="card-title">Active vs. Inactive Users</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="donutChart" style="height: 199px;" class="bar-chart-ex chartjs"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <!-- Dashboard Analytics end -->
@endsection

@section('vendor-script')
    <script src="{{ asset('vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
@section('page-script')
    <script>
        @if (Session::has('message'))
            toastr['success']('{{ Session::get('message') }}', 'Success!', {
                closeButton: true,
                tapToDismiss: false,
                rtl: false
            });
        @endif

        var areaOffices = {!! json_encode($areaOffices) !!};
        console.warn(areaOffices);

        var data = {
            labels: areaOffices.map(a => a.name),
            datasets: [{
                    label: 'Area Office',
                    data: {!! json_encode($milkPurchases->pluck('areaOfficeGrossVolume')) !!},
                    backgroundColor: function(context) {
                        var gradient = context.chart.ctx.createLinearGradient(0, 0, 0, context.chart.height);
                        gradient.addColorStop(0, 'rgb(0, 255, 0)');
                        gradient.addColorStop(1, 'rgba(0, 255, 0, 0.2)');
                        return gradient;
                    }
                },
                {
                    label: 'MMT',
                    data: {!! json_encode($milkPurchases->pluck('mmt')) !!},
                    backgroundColor: function(context) {
                        var gradient = context.chart.ctx.createLinearGradient(0, 0, 0, context.chart.height);
                        gradient.addColorStop(0, 'rgb(255, 0, 0)');
                        gradient.addColorStop(1, 'rgba(255, 0, 0, 0.2)');
                        return gradient;
                    }
                },
                {
                    label: 'MCC',
                    data: {!! json_encode($milkPurchases->pluck('mcc')) !!},
                    backgroundColor: function(context) {
                        var gradient = context.chart.ctx.createLinearGradient(0, 0, 0, context.chart.height);
                        gradient.addColorStop(0, 'rgb(255, 165, 0)');
                        gradient.addColorStop(1, 'rgba(255, 165, 0, 0.2)');
                        return gradient;
                    }
                }
            ]
        };

        var ctx = document.getElementById('stackedChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            color: 'rgb(0, 0, 0)',
                        }
                    },
                    y: {
                        stacked: true,
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                borderRadius: 10,
            }
        });



        // Get the canvas element
        const chartCanvas = document.getElementById('milkPurchasesAtDate');

        // Get the JSON data passed from the server-side
        const milkPurchasesAtDateData = {!! $milkPurchasesAtDate !!};

        // Extract the data for the chart
        const areaOfficeIds = milkPurchasesAtDateData.map(data => data.area_office_id);
        const areaOfficeGrossVolumes = milkPurchasesAtDateData.map(data => data.areaOfficeGrossVolume);
        const mmtVolumes = milkPurchasesAtDateData.map(data => data.mmt);
        const mccVolumes = milkPurchasesAtDateData.map(data => data.mcc);

        // Create the chart
        new Chart(chartCanvas, {
            type: 'bar',
            data: {
                labels: areaOfficeIds,
                datasets: [{
                        label: 'Area Office',
                        data: areaOfficeGrossVolumes,
                        backgroundColor: function(context) {
                            var gradient = context.chart.ctx.createLinearGradient(0, 0, 0, context.chart
                                .height);
                            gradient.addColorStop(0, 'rgb(0, 255, 0)');
                            gradient.addColorStop(1, 'rgba(0, 255, 0, 0.2)');
                            return gradient;
                        }
                    },
                    {
                        label: 'MMT',
                        data: mmtVolumes,
                        backgroundColor: function(context) {
                            var gradient = context.chart.ctx.createLinearGradient(0, 0, 0, context.chart
                                .height);
                            gradient.addColorStop(0, 'rgb(255, 0, 0)');
                            gradient.addColorStop(1, 'rgba(255, 0, 0, 0.2)');
                            return gradient;
                        }
                    },
                    {
                        label: 'MCC',
                        data: mccVolumes,
                        backgroundColor: function(context) {
                            var gradient = context.chart.ctx.createLinearGradient(0, 0, 0, context.chart
                                .height);
                            gradient.addColorStop(0, 'rgb(255, 165, 0)');
                            gradient.addColorStop(1, 'rgba(255, 165, 0, 0.2)');
                            return gradient;
                        }
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                borderRadius: 10,
            }
        });



        var areaOfficePurchases = <?php echo json_encode($data['areaOfficePurchases']); ?>;

        var dates = areaOfficePurchases.map(function(item) {
            return item.date;
        });

        var areaOfficeVolumes = areaOfficePurchases.map(function(item) {
            return item.areaOfficeGrossVolume;
        });

        var areaOfficeNames = areaOfficePurchases[0].areaOfficeNames;

        var ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: areaOfficeNames.map(function(_, index) {
                    var gradient = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height);
                    var colors = [
                        ['rgb(0, 0, 255)', 'rgba(0, 0, 255, 0.2)'],
                        ['rgb(255, 0, 0)', 'rgba(255, 0, 0, 0.2)'],
                        ['rgb(0, 255, 0)', 'rgba(0, 255, 0, 0.2)'],
                        ['rgb(255, 165, 0)', 'rgba(255, 165, 0, 0.2)']
                    ];
                    gradient.addColorStop(0, colors[index][0]);
                    gradient.addColorStop(1, colors[index][1]);
                    return {
                        label: areaOfficeNames[index],
                        data: areaOfficeVolumes.map(function(volumes) {
                            return volumes[index];
                        }),
                        backgroundColor: gradient,
                        borderColor: colors[index][0],
                        borderWidth: 2,
                        borderRadius: 10,
                    };
                })
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            color: 'rgb(0, 0, 0)',
                        }
                    },
                    y: {
                        beginAtZero: true,
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 10
                    }
                }
            }
        });

        var totalMilkPurchasesGrossVolume = <?php echo json_encode($data['totalMilkPurchasesGrossVolume']); ?>;

        var dates = totalMilkPurchasesGrossVolume.map(function(item) {
            return item.date;
        }).reverse();

        var volumes = totalMilkPurchasesGrossVolume.map(function(item) {
            return item.totalVolume;
        }).reverse();

        var ctx = document.getElementById('lineChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Total Gross Volume',
                    data: volumes,
                    backgroundColor: 'rgba(0, 0, 255, 0.7)',
                    borderColor: 'rgba(0, 0, 255, 1)',
                    fill: false,
                    borderWidth: 2,
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 10
                    }
                }
            }
        });


        var activeUsers = <?php echo $activeUsers; ?>;
        var inActiveUsers = <?php echo $inActiveUsers; ?>;

        var ctx = document.getElementById('donutChart').getContext('2d');
        var donutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Active Users', 'Inactive Users'],
                datasets: [{
                    data: [activeUsers, inActiveUsers],
                    backgroundColor: ['#0000FF', '#FF0000'],
                    hoverBackgroundColor: ['#0000FF', '#FF0000']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        $('.changeTheme').click(function() {
            var id = $(this).attr("id");
            if (id == 'dark') {
                var chart = Chart.getChart('stackedChart');
                chart.options.scales.x.ticks.color = '#ffffff';
                chart.options.scales.y.ticks.color = '#ffffff';
                chart.options.plugins.legend.labels.color = '#ffffff';
                chart.update();

                var chartCanvas = Chart.getChart('milkPurchasesAtDate');
                chartCanvas.options.scales.x.ticks.color = '#ffffff';
                chartCanvas.options.scales.y.ticks.color = '#ffffff';
                chartCanvas.options.plugins.legend.labels.color = '#ffffff';
                chartCanvas.update();

                var myChart = Chart.getChart('myChart');
                myChart.options.scales.x.ticks.color = '#ffffff';
                myChart.options.scales.y.ticks.color = '#ffffff';
                myChart.options.plugins.legend.labels.color = '#ffffff';
                myChart.update();

                var lineChart = Chart.getChart('lineChart');
                lineChart.options.scales.x.ticks.color = '#ffffff';
                lineChart.options.scales.y.ticks.color = '#ffffff';
                lineChart.options.plugins.legend.labels.color = '#ffffff';
                lineChart.update();

                var donutChart = Chart.getChart('donutChart');
                donutChart.options.plugins.legend.labels.color = '#ffffff';
                donutChart.update();
            } else {
                var chart = Chart.getChart('stackedChart');
                chart.options.scales.x.ticks.color = '#000000';
                chart.options.scales.y.ticks.color = '#000000';
                chart.options.plugins.legend.labels.color = '#000000';
                chart.update();

                var chartCanvas = Chart.getChart('milkPurchasesAtDate');
                chartCanvas.options.scales.x.ticks.color = '#000000';
                chartCanvas.options.scales.y.ticks.color = '#000000';
                chartCanvas.options.plugins.legend.labels.color = '#000000';
                chartCanvas.update();

                var myChart = Chart.getChart('myChart');
                myChart.options.scales.x.ticks.color = '#000000';
                myChart.options.scales.y.ticks.color = '#000000';
                myChart.options.plugins.legend.labels.color = '#000000';
                myChart.update();

                var lineChart = Chart.getChart('lineChart');
                lineChart.options.scales.x.ticks.color = '#000000';
                lineChart.options.scales.y.ticks.color = '#000000';
                lineChart.options.plugins.legend.labels.color = '#000000';
                lineChart.update();

                var donutChart = Chart.getChart('donutChart');
                donutChart.options.plugins.legend.labels.color = '#000000';
                donutChart.update();
            }
        });
    </script>
@endsection
