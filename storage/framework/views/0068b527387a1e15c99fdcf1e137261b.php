<?php $__env->startSection('title', 'Analytics Dashboard'); ?>

<?php $__env->startSection('content'); ?>
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
                    <option value="7d" <?php echo e($period === '7d' ? 'selected' : ''); ?>>Last 7 Days</option>
                    <option value="30d" <?php echo e($period === '30d' ? 'selected' : ''); ?>>Last 30 Days</option>
                    <option value="90d" <?php echo e($period === '90d' ? 'selected' : ''); ?>>Last 90 Days</option>
                    <option value="1y" <?php echo e($period === '1y' ? 'selected' : ''); ?>>Last Year</option>
                </select>

                <!-- Export Button -->
                <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200" @click="exportOpen = true">
                    <i class="fas fa-download"></i>
                    <span>Export</span>
                </button>

                <!-- Refresh Button -->
                <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50" :disabled="refreshing"
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
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Total Submissions</div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="total-submissions">
                            <?php echo e(number_format($analytics['form_analytics']['total_submissions'] ?? 0)); ?>

                        </div>
                    </div>
                    <i class="fas fa-clipboard-list text-2xl text-gray-300 dark:text-gray-600"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Completion Rate</div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="completion-rate">
                            <?php echo e(number_format($analytics['form_analytics']['completion_rate'] ?? 0, 1)); ?>%
                        </div>
                    </div>
                    <i class="fas fa-percentage text-2xl text-gray-300 dark:text-gray-600"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Avg. Completion Time
                        </div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="avg-completion-time">
                            <?php echo e(number_format($analytics['form_analytics']['average_completion_time'] ?? 0, 1)); ?> min
                        </div>
                    </div>
                    <i class="fas fa-clock text-2xl text-gray-300 dark:text-gray-600"></i>
                </div>
            </div>

            <!-- EmlakLoc v3.0 Analytics Cards -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Map Interactions</div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="map-interactions">
                            <?php echo e(number_format($analytics['emlakloc_analytics']['map_interactions'] ?? 0)); ?>

                        </div>
                    </div>
                    <i class="fas fa-map-marked-alt text-2xl text-gray-300 dark:text-gray-600"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide mb-1">
                            Popular Locations
                        </div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="popular-locations">
                            <?php echo e(number_format($analytics['emlakloc_analytics']['popular_locations_used'] ?? 0)); ?>

                        </div>
                    </div>
                    <i class="fas fa-star text-2xl text-gray-300 dark:text-gray-600"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                            Address Searches
                        </div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="address-searches">
                            <?php echo e(number_format($analytics['emlakloc_analytics']['address_searches'] ?? 0)); ?>

                        </div>
                    </div>
                    <i class="fas fa-search text-2xl text-gray-300 dark:text-gray-600"></i>
                </div>
            </div>

            <!-- Context7 Analytics Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wide mb-1">
                            Context7 Aramalar
                        </div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="space-y-2">
                            <?php echo e(number_format($analytics['context7_analytics']['total_searches'] ?? 0)); ?>

                        </div>
                    </div>
                    <i class="fas fa-search text-2xl text-gray-300 dark:text-gray-600"></i>
                </div>
            </div>

            <!-- AI Analytics Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-1">
                            AI Kullanımı
                        </div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white" id="ai-usage">
                            <?php echo e(number_format($analytics['ai_analytics']['total_requests'] ?? 0)); ?>

                        </div>
                    </div>
                    <i class="fas fa-robot text-2xl text-gray-300 dark:text-gray-600"></i>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Conversion Funnel -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                    <h6 class="mb-3 font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-funnel-dollar mr-2 text-orange-500"></i>
                        Conversion Funnel
                    </h6>
                    <canvas id="conversion-funnel-chart" width="400" height="200"></canvas>
                </div>

                <!-- Step Abandonment -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                    <h6 class="mb-3 font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-exclamation-triangle mr-2 text-orange-500"></i>
                        Step Abandonment
                    </h6>
                    <canvas id="step-abandonment-chart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Context7 Analytics Section -->
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
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
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <div class="flex-1">
                                            <div class="h-2 rounded-full bg-gray-200 dark:bg-gray-700 mb-2">
                                                <div class="h-2 rounded-full bg-blue-500" id="search-accuracy" style="width: 0%"
                                                    role="progressbar"></div>
                                            </div>
                                            <small class="text-sm text-gray-500 dark:text-gray-400">
                                                Doğruluk Oranı: <span id="search-accuracy-percentage">0%</span>
                                            </small>
                                        </div>
                                        <div class="ml-3 text-right">
                                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400" id="successful-searches">0</div>
                                            <small class="text-sm text-gray-500 dark:text-gray-400">Başarılı</small>
                                        </div>
                                    </div>
                                    <div class="flex items-center mt-2">
                                        <div class="flex-1">
                                            <small class="text-sm text-gray-500 dark:text-gray-400">Başarısız</small>
                                        </div>
                                        <div class="ml-3 text-right">
                                            <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="failed-searches">0</div>
                                            <small class="text-sm text-gray-500 dark:text-gray-400">Başarısız</small>
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
                                <div class="space-y-2">
                                    <div class="grid grid-cols-2 text-center gap-4">
                                        <div>
                                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400" id="avg-search-time">0</div>
                                            <small class="text-sm text-gray-500 dark:text-gray-400">Ort. Arama Süresi (sn)</small>
                                        </div>
                                        <div>
                                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="avg-results-clicked">0</div>
                                            <small class="text-sm text-gray-500 dark:text-gray-400">Ort. Sonuç Tıklama</small>
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
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-4">
                <h6 class="mb-4 font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-tachometer-alt mr-2 text-orange-500"></i>
                    Performance Metrics
                </h6>
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white" id="page-load-time">
                            <?php echo e(number_format($analytics['form_analytics']['performance_metrics']['page_load_time'] ?? 0, 0)); ?>ms
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Page Load Time</div>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white" id="api-response-time">
                            <?php echo e(number_format($analytics['form_analytics']['performance_metrics']['api_response_time'] ?? 0, 0)); ?>ms
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">API Response Time</div>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white" id="error-rate">
                            <?php echo e(number_format($analytics['form_analytics']['performance_metrics']['error_rate'] ?? 0, 2)); ?>%
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Error Rate</div>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white" id="uptime">
                            <?php echo e(number_format($analytics['form_analytics']['performance_metrics']['uptime'] ?? 0, 2)); ?>%
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Uptime</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Behavior -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Device Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-3">
                    <h6 class="font-semibold text-blue-600 dark:text-blue-400">
                        <i class="fas fa-mobile-alt mr-2"></i>
                        Device Usage
                    </h6>
                </div>
                <div class="p-4">
                    <canvas id="device-usage-chart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Browser Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-3">
                    <h6 class="font-semibold text-blue-600 dark:text-blue-400">
                        <i class="fas fa-globe mr-2"></i>
                        Browser Usage
                    </h6>
                </div>
                <div class="p-4">
                    <canvas id="browser-usage-chart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Session Duration -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-3">
                    <h6 class="font-semibold text-blue-600 dark:text-blue-400">
                        <i class="fas fa-hourglass-half mr-2"></i>
                        Session Duration
                    </h6>
                </div>
                <div class="p-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400" id="avg-session-duration">
                            <?php echo e(number_format($analytics['form_analytics']['user_behavior']['session_duration'] ?? 0, 1)); ?>

                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Average Minutes</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Activity -->
        <div class="mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-3">
                    <h6 class="font-semibold text-blue-600 dark:text-blue-400">
                        <i class="fas fa-broadcast-tower mr-2"></i>
                        Real-time Activity
                    </h6>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="current-sessions">
                                    <?php echo e(number_format($analytics['real_time_analytics']['current_sessions'] ?? 0)); ?>

                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Current Sessions</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="recent-submissions">
                                    <?php echo e(number_format($analytics['real_time_analytics']['recent_submissions'] ?? 0)); ?>

                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Recent Submissions</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400" id="cpu-usage">
                                    <?php echo e(number_format($analytics['real_time_analytics']['system_health']['cpu_usage'] ?? 0)); ?>%
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">CPU Usage</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="memory-usage">
                                    <?php echo e(number_format($analytics['real_time_analytics']['system_health']['memory_usage'] ?? 0)); ?>%
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Memory Usage</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EmlakLoc v3.0 Analytics Section -->
        <div class="mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-3">
                    <h5 class="font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-map-marked-alt mr-2"></i>
                        EmlakLoc v3.0 Performance Dashboard
                    </h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Top Neighborhoods -->
                            <div>
                                <h6 class="text-blue-600 dark:text-blue-400 mb-3">
                                    <i class="fas fa-star mr-1"></i>
                                    En Popüler Mahalleler
                                </h6>
                                <div id="top-neighborhoods-chart" class="h-[300px]">
                                    <!-- Chart will be loaded here -->
                                </div>
                            </div>

                            <!-- API Response Times -->
                            <div>
                                <h6 class="text-green-600 dark:text-green-400 mb-3">
                                    <i class="fas fa-tachometer-alt mr-1"></i>
                                    API Response Times
                                </h6>
                                <div id="response-times-chart" class="h-[300px]">
                                    <!-- Chart will be loaded here -->
                                </div>
                            </div>

                            <!-- Cache Performance -->
                            <div>
                                <h6 class="text-blue-600 dark:text-blue-400 mb-3">
                                    <i class="fas fa-database mr-1"></i>
                                    Cache Performance
                                </h6>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <div class="flex-1">
                                            <div class="h-2 rounded-full bg-gray-200 dark:bg-gray-700 mb-2">
                                                <div class="h-2 rounded-full bg-green-500" id="cache-hit-rate" style="width: 0%"
                                                    role="progressbar"></div>
                                            </div>
                                            <small class="text-sm text-gray-500 dark:text-gray-400">
                                                Hit Rate: <span id="cache-hit-percentage">0%</span>
                                            </small>
                                        </div>
                                        <div class="ml-3 text-right">
                                            <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="cache-hits">0</div>
                                            <small class="text-sm text-gray-500 dark:text-gray-400">Hits</small>
                                        </div>
                                    </div>
                                    <div class="flex items-center mt-2">
                                        <div class="flex-1">
                                            <small class="text-sm text-gray-500 dark:text-gray-400">Misses</small>
                                        </div>
                                        <div class="ml-3 text-right">
                                            <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="cache-misses">0</div>
                                            <small class="text-sm text-gray-500 dark:text-gray-400">Misses</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Behavior -->
                            <div>
                                <h6 class="text-yellow-600 dark:text-yellow-400 mb-3">
                                    <i class="fas fa-users mr-1"></i>
                                    Kullanıcı Davranışları
                                </h6>
                                <div class="grid grid-cols-2 text-center gap-4">
                                    <div>
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="avg-session-duration">0</div>
                                        <small class="text-sm text-gray-500 dark:text-gray-400">Ort. Oturum Süresi (dk)</small>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="avg-interactions">0</div>
                                        <small class="text-sm text-gray-500 dark:text-gray-400">Ort. Etkileşim/Session</small>
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
            <div class="relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-4 py-2.5">
                <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Export Analytics Data</h5>
                <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" @click="exportOpen=false" aria-label="Kapat">
                    <svg class="w-5 h-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
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
            <div class="flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700 px-4 py-2.5">
                <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200" @click="exportOpen=false">Cancel</button>
                <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50" :disabled="loading"
                    @click="loading = true; downloadExport()">
                    <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-download'"></i>
                    <span x-text="loading ? 'İndiriliyor...' : 'Download'"></span>
                </button>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
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
                            <?php echo e($analytics['form_analytics']['user_behavior']['conversion_funnel']['step_1'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['conversion_funnel']['step_2'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['conversion_funnel']['step_3'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['conversion_funnel']['step_4'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['conversion_funnel']['step_5'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['conversion_funnel']['completed'] ?? 0); ?>

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
                            <?php echo e($analytics['form_analytics']['step_abandonment']['step_1'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['step_abandonment']['step_2'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['step_abandonment']['step_3'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['step_abandonment']['step_4'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['step_abandonment']['step_5'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['step_abandonment']['step_6'] ?? 0); ?>

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
                            <?php echo e($analytics['form_analytics']['user_behavior']['device_usage']['Desktop'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['device_usage']['Mobile'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['device_usage']['Tablet'] ?? 0); ?>

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
                            <?php echo e($analytics['form_analytics']['user_behavior']['browser_usage']['Chrome'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['browser_usage']['Firefox'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['browser_usage']['Safari'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['browser_usage']['Edge'] ?? 0); ?>,
                            <?php echo e($analytics['form_analytics']['user_behavior']['browser_usage']['Other'] ?? 0); ?>

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
            if (document.getElementById('space-y-2')) {
                document.getElementById('space-y-2').textContent = (context7Data.total_searches || 0)
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/analytics/dashboard.blade.php ENDPATH**/ ?>