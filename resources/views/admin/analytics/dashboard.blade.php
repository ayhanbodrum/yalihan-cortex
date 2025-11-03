@extends('admin.layouts.neo')

@section('title', 'Analytics Dashboard')

@section('content')
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ exportOpen: false, loading: false, refreshing: false }">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-chart-line text-orange-500 mr-2"></i>
                    Analytics Dashboard
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-0">Form performance and user behavior insights</p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Period Selector -->
                <select style="color-scheme: light dark;" id="period-selector" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 w-auto transition-all duration-200">
                    <option value="7d" {{ $period === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30d" {{ $period === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90d" {{ $period === '90d' ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="1y" {{ $period === '1y' ? 'selected' : '' }}>Last Year</option>
                </select>

                <!-- Export Button -->
                <button class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized" @click="exportOpen = true">
                    <i class="fas fa-download"></i>
                    <span>Export</span>
                </button>

                <!-- Refresh Button -->
                <button class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized" :disabled="refreshing"
                    @click="refreshing = true; refreshAnalytics()">
                    <i class="fas" :class="refreshing ? 'fa-spinner fa-spin' : 'fa-sync-alt'"></i>
                    <span x-text="refreshing ? 'Yenileniyor...' : 'Refresh'"></span>
                </button>
            </div>
        </div>

        <!-- Alerts Section -->
        <div id="alerts-section" class="mb-4">
            <!-- Alerts will be loaded here -->
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="neo-card p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Submissions</div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="total-submissions">
                            {{ number_format($analytics['form_analytics']['total_submissions'] ?? 0) }}
                        </div>
                    </div>
                    <i class="fas fa-clipboard-list text-2xl text-gray-300"></i>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Completion Rate</div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="completion-rate">
                            {{ number_format($analytics['form_analytics']['completion_rate'] ?? 0, 1) }}%
                        </div>
                    </div>
                    <i class="fas fa-percentage text-2xl text-gray-300"></i>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Avg. Completion Time
                        </div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="avg-completion-time">
                            {{ number_format($analytics['form_analytics']['average_completion_time'] ?? 0, 1) }} min
                        </div>
                    </div>
                    <i class="fas fa-clock text-2xl text-gray-300"></i>
                </div>
            </div>

            <!-- EmlakLoc v3.0 Analytics Cards -->
            <div class="neo-card p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Map Interactions</div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="map-interactions">
                            {{ number_format($analytics['emlakloc_analytics']['map_interactions'] ?? 0) }}
                        </div>
                    </div>
                    <i class="fas fa-map-marked-alt text-2xl text-gray-300"></i>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="neo-card p-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Popular Locations
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="popular-locations">
                                    {{ number_format($analytics['emlakloc_analytics']['popular_locations_used'] ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-star fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="neo-card p-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Address Searches
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="address-searches">
                                    {{ number_format($analytics['emlakloc_analytics']['address_searches'] ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-search fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Context7 Analytics Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="neo-card p-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Context7 Aramalar
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="neo-searches">
                                    {{ number_format($analytics['context7_analytics']['total_searches'] ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-search fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Analytics Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="neo-card p-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">
                                    AI Kullanımı
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="ai-usage">
                                    {{ number_format($analytics['ai_analytics']['total_requests'] ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-robot fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Conversion Funnel -->
                <div class="neo-card p-4">
                    <h6 class="mb-3 font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-funnel-dollar mr-2 text-orange-500"></i>
                        Conversion Funnel
                    </h6>
                    <canvas id="conversion-funnel-chart" width="400" height="200"></canvas>
                </div>

                <!-- Step Abandonment -->
                <div class="neo-card p-4">
                    <h6 class="mb-3 font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-exclamation-triangle mr-2 text-orange-500"></i>
                        Step Abandonment
                    </h6>
                    <canvas id="step-abandonment-chart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Context7 Analytics Section -->
            <div class="mb-6">
                <div class="neo-card p-4">
                    <h5 class="mb-4 font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-search mr-2 text-orange-500"></i>
                        Context7 Performans Dashboard
                    </h5>
                    <div>
                        <div class="row">
                            <!-- Search Performance -->
                            <div class="col-md-6 mb-4">
                                <h6 class="text-warning mb-3">
                                    <i class="fas fa-tachometer-alt mr-1"></i>
                                    Arama Performansı
                                </h6>
                                <div class="neo-metric">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="neo-progress mb-2">
                                                <div class="neo-progress-bar" id="search-accuracy" style="width: 0%"
                                                    role="progressbar"></div>
                                            </div>
                                            <small class="text-muted">
                                                Doğruluk Oranı: <span id="search-accuracy-percentage">0%</span>
                                            </small>
                                        </div>
                                        <div class="ml-3 text-right">
                                            <div class="h4 mb-0 text-warning" id="successful-searches">0</div>
                                            <small class="text-muted">Başarılı</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mt-2">
                                        <div class="flex-grow-1">
                                            <small class="text-muted">Başarısız</small>
                                        </div>
                                        <div class="ml-3 text-right">
                                            <div class="h4 mb-0 text-danger" id="failed-searches">0</div>
                                            <small class="text-muted">Başarısız</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Behavior -->
                            <div class="col-md-6 mb-4">
                                <h6 class="text-info mb-3">
                                    <i class="fas fa-users mr-1"></i>
                                    Kullanıcı Davranışları
                                </h6>
                                <div class="neo-metric">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="h3 mb-0 text-warning" id="avg-search-time">0</div>
                                            <small class="text-muted">Ort. Arama Süresi (sn)</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="h3 mb-0 text-info" id="avg-results-clicked">0</div>
                                            <small class="text-muted">Ort. Sonuç Tıklama</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="mb-6">
            <div class="neo-card p-4">
                <h6 class="mb-4 font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-tachometer-alt mr-2 text-orange-500"></i>
                    Performance Metrics
                </h6>
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="text-2xl font-bold" id="page-load-time">
                            {{ number_format($analytics['form_analytics']['performance_metrics']['page_load_time'] ?? 0, 0) }}ms
                        </div>
                        <div class="text-xs text-gray-500">Page Load Time</div>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="text-2xl font-bold" id="api-response-time">
                            {{ number_format($analytics['form_analytics']['performance_metrics']['api_response_time'] ?? 0, 0) }}ms
                        </div>
                        <div class="text-xs text-gray-500">API Response Time</div>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="text-2xl font-bold" id="error-rate">
                            {{ number_format($analytics['form_analytics']['performance_metrics']['error_rate'] ?? 0, 2) }}%
                        </div>
                        <div class="text-xs text-gray-500">Error Rate</div>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="text-2xl font-bold" id="uptime">
                            {{ number_format($analytics['form_analytics']['performance_metrics']['uptime'] ?? 0, 2) }}%
                        </div>
                        <div class="text-xs text-gray-500">Uptime</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Behavior -->
        <div class="row mb-5">
            <!-- Device Usage -->
            <div class="col-xl-4 mb-4">
                <div class="neo-card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-mobile-alt mr-2"></i>
                            Device Usage
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="device-usage-chart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Browser Usage -->
            <div class="col-xl-4 mb-4">
                <div class="neo-card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-globe mr-2"></i>
                            Browser Usage
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="browser-usage-chart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Session Duration -->
            <div class="col-xl-4 mb-4">
                <div class="neo-card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-hourglass-half mr-2"></i>
                            Session Duration
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="h2 text-primary" id="avg-session-duration">
                                {{ number_format($analytics['form_analytics']['user_behavior']['session_duration'] ?? 0, 1) }}
                            </div>
                            <div class="text-muted">Average Minutes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Activity -->
        <div class="row">
            <div class="col-12">
                <div class="neo-card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-broadcast-tower mr-2"></i>
                            Real-time Activity
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="realtime-metric">
                                    <div class="metric-value text-success" id="current-sessions">
                                        {{ number_format($analytics['real_time_analytics']['current_sessions'] ?? 0) }}
                                    </div>
                                    <div class="metric-label">Current Sessions</div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="realtime-metric">
                                    <div class="metric-value text-info" id="recent-submissions">
                                        {{ number_format($analytics['real_time_analytics']['recent_submissions'] ?? 0) }}
                                    </div>
                                    <div class="metric-label">Recent Submissions</div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="realtime-metric">
                                    <div class="metric-value text-warning" id="cpu-usage">
                                        {{ number_format($analytics['real_time_analytics']['system_health']['cpu_usage'] ?? 0) }}%
                                    </div>
                                    <div class="metric-label">CPU Usage</div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="realtime-metric">
                                    <div class="metric-value text-danger" id="memory-usage">
                                        {{ number_format($analytics['real_time_analytics']['system_health']['memory_usage'] ?? 0) }}%
                                    </div>
                                    <div class="metric-label">Memory Usage</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EmlakLoc v3.0 Analytics Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="neo-card">
                    <div class="card-header py-3">
                        <h5 class="mb-0 font-semibold">
                            <i class="fas fa-map-marked-alt mr-2"></i>
                            EmlakLoc v3.0 Performance Dashboard
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Top Neighborhoods -->
                            <div class="col-md-6 mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-star mr-1"></i>
                                    En Popüler Mahalleler
                                </h6>
                                <div id="top-neighborhoods-chart" style="height: 300px;">
                                    <!-- Chart will be loaded here -->
                                </div>
                            </div>

                            <!-- API Response Times -->
                            <div class="col-md-6 mb-4">
                                <h6 class="text-success mb-3">
                                    <i class="fas fa-tachometer-alt mr-1"></i>
                                    API Response Times
                                </h6>
                                <div id="response-times-chart" style="height: 300px;">
                                    <!-- Chart will be loaded here -->
                                </div>
                            </div>

                            <!-- Cache Performance -->
                            <div class="col-md-6 mb-4">
                                <h6 class="text-info mb-3">
                                    <i class="fas fa-database mr-1"></i>
                                    Cache Performance
                                </h6>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="neo-progress mb-2">
                                            <div class="neo-progress-bar" id="cache-hit-rate" style="width: 0%"
                                                role="progressbar"></div>
                                        </div>
                                        <small class="text-muted">
                                            Hit Rate: <span id="cache-hit-percentage">0%</span>
                                        </small>
                                    </div>
                                    <div class="ml-3 text-right">
                                        <div class="h4 mb-0 text-success" id="cache-hits">0</div>
                                        <small class="text-muted">Hits</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <div class="flex-grow-1">
                                        <small class="text-muted">Misses</small>
                                    </div>
                                    <div class="ml-3 text-right">
                                        <div class="h4 mb-0 text-danger" id="cache-misses">0</div>
                                        <small class="text-muted">Misses</small>
                                    </div>
                                </div>
                            </div>

                            <!-- User Behavior -->
                            <div class="col-md-6 mb-4">
                                <h6 class="text-warning mb-3">
                                    <i class="fas fa-users mr-1"></i>
                                    Kullanıcı Davranışları
                                </h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="h3 mb-0 text-primary" id="avg-session-duration">0</div>
                                        <small class="text-muted">Ort. Oturum Süresi (dk)</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="h3 mb-0 text-info" id="avg-interactions">0</div>
                                        <small class="text-muted">Ort. Etkileşim/Session</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal (Alpine) -->
    <div x-show="exportOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="exportOpen=false"></div>
        <div class="relative neo-card w-full max-w-md">
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 px-4 py-2.5">
                <h5 class="text-sm font-semibold">Export Analytics Data</h5>
                <button class="neo-icon-btn touch-target-optimized touch-target-optimized" @click="exportOpen=false" aria-label="Kapat">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-4 py-4 space-y-4">
                <div>
                    <label for="export-type" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Data
                        Type</label>
                    <select style="color-scheme: light dark;" id="export-type" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 w-full transition-all duration-200">
                        <option value="form_analytics">Form Analytics</option>
                        <option value="conversion_analytics">Conversion Analytics</option>
                        <option value="performance_metrics">Performance Metrics</option>
                    </select>
                </div>
                <div>
                    <label for="export-period"
                        class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Time Period</label>
                    <select style="color-scheme: light dark;" id="export-period" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 w-full transition-all duration-200">
                        <option value="7d">Last 7 Days</option>
                        <option value="30d">Last 30 Days</option>
                        <option value="90d">Last 90 Days</option>
                        <option value="1y">Last Year</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-200 dark:border-gray-800 px-4 py-2.5">
                <button type="button" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized" @click="exportOpen=false">Cancel</button>
                <button type="button" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized" :disabled="loading"
                    @click="loading = true; downloadExport()">
                    <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-download'"></i>
                    <span x-text="loading ? 'İndiriliyor...' : 'Download'"></span>
                </button>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let charts = {};

        function isDark() {
            return document.body.classList.contains('dark');
        }

        function applyChartTheme() {
            const text = isDark() ? '#e5e7eb' : '#374151';
            const grid = isDark() ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
            const border = isDark() ? 'rgba(255,255,255,0.15)' : 'rgba(0,0,0,0.1)';
            // Global defaults
            Chart.defaults.color = text;
            Chart.defaults.borderColor = border;
            Chart.defaults.plugins.legend = Chart.defaults.plugins.legend || {};
            Chart.defaults.plugins.legend.labels = Chart.defaults.plugins.legend.labels || {};
            Chart.defaults.plugins.legend.labels.color = text;

            // Update existing charts
            Object.values(charts).forEach((chart) => {
                chart.options.scales && Object.values(chart.options.scales).forEach((scale) => {
                    scale.grid = scale.grid || {};
                    scale.grid.color = grid;
                    scale.ticks = scale.ticks || {};
                    scale.ticks.color = text;
                });
                if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                    chart.options.plugins.legend.labels.color = text;
                }
                chart.update('none');
            });
        }

        // Observe dark mode changes on <body>
        const neoDarkObserver = new MutationObserver(() => applyChartTheme());
        document.addEventListener('DOMContentLoaded', function() {
            neoDarkObserver.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });
            initializeCharts();
            applyChartTheme();
            loadAlerts();
            startRealTimeUpdates();
        });

        // Initialize all charts
        function initializeCharts() {
            // Conversion Funnel Chart
            const funnelCtx = document.getElementById('conversion-funnel-chart').getContext('2d');
            charts.funnel = new Chart(funnelCtx, {
                type: 'bar',
                data: {
                    labels: ['Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5', 'Completed'],
                    datasets: [{
                        label: 'Users',
                        data: [
                            {{ $analytics['form_analytics']['user_behavior']['conversion_funnel']['step_1'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['conversion_funnel']['step_2'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['conversion_funnel']['step_3'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['conversion_funnel']['step_4'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['conversion_funnel']['step_5'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['conversion_funnel']['completed'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#4e73df',
                            '#1cc88a',
                            '#36b9cc',
                            '#f6c23e',
                            '#e74a3b',
                            '#858796'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(0,0,0,0.06)'
                            },
                            ticks: {
                                color: Chart.defaults.color
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.06)'
                            },
                            ticks: {
                                color: Chart.defaults.color
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: Chart.defaults.color
                            }
                        }
                    }
                }
            });

            // Step Abandonment Chart
            const abandonmentCtx = document.getElementById('step-abandonment-chart').getContext('2d');
            charts.abandonment = new Chart(abandonmentCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5', 'Step 6'],
                    datasets: [{
                        data: [
                            {{ $analytics['form_analytics']['step_abandonment']['step_1'] ?? 0 }},
                            {{ $analytics['form_analytics']['step_abandonment']['step_2'] ?? 0 }},
                            {{ $analytics['form_analytics']['step_abandonment']['step_3'] ?? 0 }},
                            {{ $analytics['form_analytics']['step_abandonment']['step_4'] ?? 0 }},
                            {{ $analytics['form_analytics']['step_abandonment']['step_5'] ?? 0 }},
                            {{ $analytics['form_analytics']['step_abandonment']['step_6'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#e74a3b',
                            '#f6c23e',
                            '#36b9cc',
                            '#1cc88a',
                            '#4e73df',
                            '#858796'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Device Usage Chart
            const deviceCtx = document.getElementById('device-usage-chart').getContext('2d');
            charts.device = new Chart(deviceCtx, {
                type: 'pie',
                data: {
                    labels: ['Desktop', 'Mobile', 'Tablet'],
                    datasets: [{
                        data: [
                            {{ $analytics['form_analytics']['user_behavior']['device_usage']['Desktop'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['device_usage']['Mobile'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['device_usage']['Tablet'] ?? 0 }}
                        ],
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc']
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Browser Usage Chart
            const browserCtx = document.getElementById('browser-usage-chart').getContext('2d');
            charts.browser = new Chart(browserCtx, {
                type: 'pie',
                data: {
                    labels: ['Chrome', 'Firefox', 'Safari', 'Edge', 'Other'],
                    datasets: [{
                        data: [
                            {{ $analytics['form_analytics']['user_behavior']['browser_usage']['Chrome'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['browser_usage']['Firefox'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['browser_usage']['Safari'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['browser_usage']['Edge'] ?? 0 }},
                            {{ $analytics['form_analytics']['user_behavior']['browser_usage']['Other'] ?? 0 }}
                        ],
                        backgroundColor: ['#4285f4', '#ff6b35', '#4caf50', '#2196f3', '#9c27b0']
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }

        // Load alerts
        function loadAlerts() {
            fetch('/admin/analytics/alerts')
                .then(response => response.json())
                .then(alerts => {
                    const alertsSection = document.getElementById('alerts-section');
                    alertsSection.innerHTML = '';

                    Object.entries(alerts).forEach(([key, alert]) => {
                        if (alert.active) {
                            const alertDiv = document.createElement('div');
                            alertDiv.className = `alert alert-${alert.severity} alert-dismissible fade show`;
                            alertDiv.innerHTML = `
                        <strong>${key.replace(/_/g, ' ').toUpperCase()}:</strong> ${alert.message}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    `;
                            alertsSection.appendChild(alertDiv);
                        }
                    });
                })
                .catch(error => console.error('Error loading alerts:', error));
        }

        // Start real-time updates
        function startRealTimeUpdates() {
            setInterval(() => {
                updateRealTimeMetrics();
            }, 30000); // Update every 30 seconds
        }

        // Update real-time metrics
        function updateRealTimeMetrics() {
            fetch('/admin/analytics/real-time')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('active-users').textContent = data.active_users.toLocaleString();
                    document.getElementById('current-sessions').textContent = data.current_sessions.toLocaleString();
                    document.getElementById('recent-submissions').textContent = data.recent_submissions
                        .toLocaleString();
                    document.getElementById('cpu-usage').textContent = data.system_health.cpu_usage + '%';
                    document.getElementById('memory-usage').textContent = data.system_health.memory_usage + '%';

                    // Context7 Analytics Updates
                    if (data.context7_analytics) {
                        updateContext7Metrics(data.context7_analytics);
                    }
                })
                .catch(error => console.error('Error updating real-time metrics:', error));
        }

        // Update Context7 metrics
        function updateContext7Metrics(context7Data) {
            // Update search accuracy
            const accuracyPercentage = context7Data.search_accuracy || 0;
            document.getElementById('search-accuracy').style.width = accuracyPercentage + '%';
            document.getElementById('search-accuracy-percentage').textContent = accuracyPercentage + '%';

            // Update search counts
            document.getElementById('successful-searches').textContent = (context7Data.successful_searches || 0)
                .toLocaleString();
            document.getElementById('failed-searches').textContent = (context7Data.failed_searches || 0).toLocaleString();

            // Update user behavior metrics
            document.getElementById('avg-search-time').textContent = (context7Data.avg_search_time || 0).toFixed(1);
            document.getElementById('avg-results-clicked').textContent = (context7Data.avg_results_clicked || 0).toFixed(1);

            // Update total searches
            if (document.getElementById('neo-searches')) {
                document.getElementById('neo-searches').textContent = (context7Data.total_searches || 0)
                    .toLocaleString();
            }
        }

        // Refresh analytics
        function refreshAnalytics() {
            const period = document.getElementById('period-selector').value;

            fetch(`/admin/analytics/form-analytics?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    updateMetrics(data);
                    updateCharts(data);

                    // Update Context7 analytics
                    if (data.context7_analytics) {
                        updateContext7Metrics(data.context7_analytics);
                    }
                })
                .catch(error => console.error('Error refreshing analytics:', error));
        }

        // Update metrics
        function updateMetrics(data) {
            document.getElementById('total-submissions').textContent = data.total_submissions.toLocaleString();
            document.getElementById('completion-rate').textContent = data.completion_rate.toFixed(1) + '%';
            document.getElementById('avg-completion-time').textContent = data.average_completion_time.toFixed(1) + ' min';
            document.getElementById('page-load-time').textContent = data.performance_metrics.page_load_time.toFixed(0) +
                'ms';
            document.getElementById('api-response-time').textContent = data.performance_metrics.api_response_time.toFixed(
                0) + 'ms';
            document.getElementById('error-rate').textContent = data.performance_metrics.error_rate.toFixed(2) + '%';
            document.getElementById('uptime').textContent = data.performance_metrics.uptime.toFixed(2) + '%';
            document.getElementById('avg-session-duration').textContent = data.user_behavior.session_duration.toFixed(1);
        }

        // Update charts
        function updateCharts(data) {
            // Update funnel chart
            charts.funnel.data.datasets[0].data = [
                data.user_behavior.conversion_funnel.step_1,
                data.user_behavior.conversion_funnel.step_2,
                data.user_behavior.conversion_funnel.step_3,
                data.user_behavior.conversion_funnel.step_4,
                data.user_behavior.conversion_funnel.step_5,
                data.user_behavior.conversion_funnel.completed
            ];
            charts.funnel.update();

            // Update abandonment chart
            charts.abandonment.data.datasets[0].data = [
                data.step_abandonment.step_1,
                data.step_abandonment.step_2,
                data.step_abandonment.step_3,
                data.step_abandonment.step_4,
                data.step_abandonment.step_5,
                data.step_abandonment.step_6
            ];
            charts.abandonment.update();
        }

        // Export analytics
        function exportAnalytics() {
            $('#exportModal').modal('show');
        }

        // Download export
        function downloadExport() {
            const type = document.getElementById('export-type').value;
            const period = document.getElementById('export-period').value;

            window.location.href = `/admin/analytics/export?type=${type}&period=${period}`;

            $('#exportModal').modal('hide');
        }

        // Period selector change
        document.getElementById('period-selector').addEventListener('change', function() {
            refreshAnalytics();
        });
    </script>
@endpush
