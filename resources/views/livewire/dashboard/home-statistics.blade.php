<div>
    @can('home')
    <!-- General System Statistics -->
    <div class="row match-height mb-2">
        <div class="col-12">
            <div class="card card-statistics">
                <div class="card-header border-bottom mb-2">
                    <h4 class="card-title text-primary"><i data-feather="grid" class="me-50"></i>{{ __('dashboard.system_statistics') }}</h4>
                </div>
                <div class="card-body statistics-body">
                    <div class="row">
                        <!-- Total Vendors -->
                        <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                            <div class="d-flex flex-row">
                                <div class="avatar bg-light-primary me-2">
                                    <div class="avatar-content">
                                        <i data-feather="users" class="avatar-icon"></i>
                                    </div>
                                </div>
                                <div class="my-auto">
                                    <h4 class="fw-bolder mb-0">{{ $totalVendors }}</h4>
                                    <p class="card-text font-small-3 mb-0">{{ __('dashboard.vendors') }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Total Deliveries -->
                        <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                            <div class="d-flex flex-row">
                                <div class="avatar bg-light-primary me-2">
                                    <div class="avatar-content">
                                        <i data-feather="truck" class="avatar-icon"></i>
                                    </div>
                                </div>
                                <div class="my-auto">
                                    <h4 class="fw-bolder mb-0">{{ $totalDeliveries }}</h4>
                                    <p class="card-text font-small-3 mb-0">{{ __('dashboard.deliveries') }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Total Users -->
                        <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
                            <div class="d-flex flex-row">
                                <div class="avatar bg-light-primary me-2">
                                    <div class="avatar-content">
                                        <i data-feather="user" class="avatar-icon"></i>
                                    </div>
                                </div>
                                <div class="my-auto">
                                    <h4 class="fw-bolder mb-0">{{ $totalUsers }}</h4>
                                    <p class="card-text font-small-3 mb-0">{{ __('dashboard.users') }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Total Orders -->
                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="d-flex flex-row">
                                <div class="avatar bg-light-primary me-2">
                                    <div class="avatar-content">
                                        <i data-feather="shopping-bag" class="avatar-icon"></i>
                                    </div>
                                </div>
                                <div class="my-auto">
                                    <h4 class="fw-bolder mb-0">{{ $totalOrders }}</h4>
                                    <p class="card-text font-small-3 mb-0">{{ __('dashboard.orders') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Statistics -->
    <div class="row match-height mb-2">
        <div class="col-md-6 col-12">
            <div class="card card-statistics">
                <div class="card-header border-bottom mb-2">
                    <h4 class="card-title text-success"><i data-feather="calendar" class="me-50"></i>{{ __('dashboard.today_statistics') }}</h4>
                </div>
                <div class="card-body statistics-body">
                    <div class="row">
                        <!-- Delivered Orders Today -->
                        <div class="col-md-6 col-12 mb-2 mb-md-0">
                            <div class="d-flex flex-row">
                                <div class="avatar bg-light-success me-2">
                                    <div class="avatar-content">
                                        <i data-feather="check-circle" class="avatar-icon"></i>
                                    </div>
                                </div>
                                <div class="my-auto">
                                    <h4 class="fw-bolder mb-0">{{ $deliveredTodayCount }}</h4>
                                    <p class="card-text font-small-3 mb-0">{{ __('dashboard.delivered_today') }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Today's Revenue -->
                        <div class="col-md-6 col-12">
                            <div class="d-flex flex-row">
                                <div class="avatar bg-light-success me-2">
                                    <div class="avatar-content">
                                        <i data-feather="dollar-sign" class="avatar-icon"></i>
                                    </div>
                                </div>
                                <div class="my-auto">
                                    <h4 class="fw-bolder mb-0">{{ number_format($deliveredTodaySum, 2) }}</h4>
                                    <p class="card-text font-small-3 mb-0">{{ __('dashboard.today_revenue') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Selection and Statistics -->
        <div class="col-md-6 col-12">
            <div class="card card-statistics">
                <div class="card-header border-bottom mb-2 d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-info"><i data-feather="bar-chart-2" class="me-50"></i>{{ __('dashboard.monthly_revenue') }}</h4>
                    <div class="d-flex align-items-center">
                        <select wire:model.live="selectedMonth" class="form-select form-select-sm">
                            @foreach ($months as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body statistics-body">
                    <div class="row">
                        <!-- Monthly Delivered Orders -->
                        <div class="col-md-6 col-12 mb-2 mb-md-0">
                            <div class="d-flex flex-row">
                                <div class="avatar bg-light-info me-2">
                                    <div class="avatar-content">
                                        <i data-feather="box" class="avatar-icon"></i>
                                    </div>
                                </div>
                                <div class="my-auto">
                                    <h4 class="fw-bolder mb-0">{{ $monthlyDeliveredCount }}</h4>
                                    <p class="card-text font-small-3 mb-0">{{ __('dashboard.monthly_orders') }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Monthly Revenue -->
                        <div class="col-md-6 col-12">
                            <div class="d-flex flex-row">
                                <div class="avatar bg-light-info me-2">
                                    <div class="avatar-content">
                                        <i data-feather="dollar-sign" class="avatar-icon"></i>
                                    </div>
                                </div>
                                <div class="my-auto">
                                    <h4 class="fw-bolder mb-0">{{ number_format($monthlyDeliveredSum, 2) }}</h4>
                                    <p class="card-text font-small-3 mb-0">{{ __('dashboard.monthly_revenue') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row match-height">
        <!-- Weekly Orders and Revenue Chart -->
        <div class="col-lg-8 col-12 mb-2">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary">
                        <i data-feather="trending-up" class="me-50"></i>
                        {{ app()->getLocale() == 'ar' ? 'حركة المبيعات والإيرادات (آخر 7 أيام)' : 'Sales & Revenue Trend (Last 7 Days)' }}
                    </h4>
                </div>
                <div class="card-body">
                    <div id="weekly-orders-revenue-chart" wire:ignore style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Order Status Distribution Chart -->
        <div class="col-lg-4 col-12 mb-2">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title text-primary">
                        <i data-feather="pie-chart" class="me-50"></i>
                        {{ app()->getLocale() == 'ar' ? 'توزيع حالات الطلبات' : 'Order Status Breakdown' }}
                    </h4>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div id="order-status-distribution-chart" wire:ignore style="min-height: 350px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- Script to ensure Feather Icons are updated during Livewire renders -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', ({ el, component }) => {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });
        });
    </script>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var weeklyDates = @json($weeklyDates);
        var weeklyCounts = @json($weeklyCounts);
        var weeklyRevenues = @json($weeklyRevenues);

        var statusLabels = @json($statusLabels);
        var statusSeries = @json($statusSeries);

        // 1. Weekly Sales & Revenue Trend Chart
        var optionsTrend = {
            series: [{
                name: "{{ app()->getLocale() == 'ar' ? 'الإيرادات' : 'Revenue' }}",
                type: 'column',
                data: weeklyRevenues
            }, {
                name: "{{ app()->getLocale() == 'ar' ? 'عدد الطلبات' : 'Orders Count' }}",
                type: 'line',
                data: weeklyCounts
            }],
            chart: {
                height: 350,
                type: 'line',
                toolbar: { show: false }
            },
            stroke: {
                width: [0, 4],
                curve: 'smooth'
            },
            colors: ['#7367F0', '#28C76F'], // Primary & Success
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1]
            },
            labels: weeklyDates,
            xaxis: {
                type: 'category'
            },
            yaxis: [{
                title: {
                    text: "{{ app()->getLocale() == 'ar' ? 'الإيرادات' : 'Revenue' }}"
                },
            }, {
                opposite: true,
                title: {
                    text: "{{ app()->getLocale() == 'ar' ? 'عدد الطلبات' : 'Orders Count' }}"
                }
            }],
            tooltip: {
                shared: true,
                intersect: false,
            }
        };

        var trendEl = document.querySelector("#weekly-orders-revenue-chart");
        if (trendEl) {
            var chartTrend = new ApexCharts(trendEl, optionsTrend);
            chartTrend.render();
        }

        // 2. Order Status Distribution Chart
        var optionsStatus = {
            series: statusSeries,
            chart: {
                height: 350,
                type: 'donut',
            },
            labels: statusLabels,
            colors: ['#FF9F43', '#82868B', '#7367F0', '#00CFE8', '#1E1E1E', '#28C76F', '#EA5455'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            legend: {
                position: 'bottom'
            }
        };

        var statusEl = document.querySelector("#order-status-distribution-chart");
        if (statusEl) {
            var chartStatus = new ApexCharts(statusEl, optionsStatus);
            chartStatus.render();
        }
    });
</script>
@endpush
