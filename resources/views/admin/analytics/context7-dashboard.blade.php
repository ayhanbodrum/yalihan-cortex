@extends('admin.layouts.app')

@section('title', 'Context7 Analytics Dashboard - Yalıhan Bekçi')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-all duration-200 ease-in-out">
        <!-- Header Section -->
        <div
            class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 transition-all duration-200 ease-in-out">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white transition-all duration-200 ease-in-out">
                            📊 Context7 Analytics Dashboard
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2 transition-all duration-200 ease-in-out">
                            Yalıhan Bekçi - Real-time Project Analytics & AI Learning
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <button id="refresh-data"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:scale-95 transition-all duration-200 ease-in-out hover:scale-105 shadow-lg">
                            🔄 Yenile
                        </button>
                        <button id="recalculate-health"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 active:scale-95 transition-all duration-200 ease-in-out hover:scale-105 shadow-lg">
                            🔬 Sağlık Hesapla
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard -->
        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Real-time Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Overall Health Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg transition-all duration-200 ease-in-out hover:scale-105 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-3xl">🎯</div>
                        <div id="health-trend"
                            class="text-sm font-semibold px-2 py-1 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                            📈 Improving
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Genel Sağlık</h3>
                    <div class="flex items-end space-x-2">
                        <span id="health-score" class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                            85.2
                        </span>
                        <span class="text-gray-500 dark:text-gray-400">%</span>
                    </div>
                    <p id="health-status" class="text-sm text-gray-600 dark:text-gray-300 mt-2 capitalize">
                        Good Health
                    </p>
                </div>

                <!-- Context7 Compliance Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg transition-all duration-200 ease-in-out hover:scale-105 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-3xl">⚖️</div>
                        <div class="text-sm text-green-600 dark:text-green-400 font-semibold">Context7</div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Uyumluluk</h3>
                    <div class="flex items-end space-x-2">
                        <span id="context7-score" class="text-3xl font-bold text-green-600 dark:text-green-400">
                            92.8
                        </span>
                        <span class="text-gray-500 dark:text-gray-400">%</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                        <span id="active-violations" class="font-semibold text-red-600 dark:text-red-400">3</span> aktif
                        ihlal
                    </p>
                </div>

                <!-- Today's Activity Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg transition-all duration-200 ease-in-out hover:scale-105 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-3xl">⚡</div>
                        <div class="text-sm text-purple-600 dark:text-purple-400 font-semibold">Bugün</div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aktivite</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Commit</span>
                            <span id="commits-today" class="font-semibold text-purple-600 dark:text-purple-400">12</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Build</span>
                            <span id="builds-today" class="font-semibold text-purple-600 dark:text-purple-400">8</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Test</span>
                            <span id="tests-today" class="font-semibold text-purple-600 dark:text-purple-400">45</span>
                        </div>
                    </div>
                </div>

                <!-- AI Learning Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg transition-all duration-200 ease-in-out hover:scale-105 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-3xl">🧠</div>
                        <div class="text-sm text-orange-600 dark:text-orange-400 font-semibold">AI Bekçi</div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Öğrenme</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Oturum</span>
                            <span id="ai-sessions" class="font-semibold text-orange-600 dark:text-orange-400">7</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Kalıp</span>
                            <span id="patterns-learned" class="font-semibold text-orange-600 dark:text-orange-400">15</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Fikir</span>
                            <span id="ideas-generated" class="font-semibold text-orange-600 dark:text-orange-400">23</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Context7 Compliance Chart -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg transition-all duration-200 ease-in-out border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📈 Context7 Uyumluluk Trendi</h3>
                    <div class="h-64">
                        <canvas id="complianceChart"></canvas>
                    </div>
                </div>

                <!-- Development Velocity Chart -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg transition-all duration-200 ease-in-out border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🚀 Geliştirme Hızı</h3>
                    <div class="h-64">
                        <canvas id="velocityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Detailed Analytics Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Violations Summary -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg transition-all duration-200 ease-in-out border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">⚠️ İhlaller (7 Gün)</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Toplam</span>
                            <span id="total-violations" class="font-bold text-red-600 dark:text-red-400">8</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Otomatik Düzeltilen</span>
                            <span id="auto-fixed" class="font-bold text-green-600 dark:text-green-400">6</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Kritik</span>
                            <span id="critical-violations" class="font-bold text-red-800 dark:text-red-300">1</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Düzeltme Oranı</span>
                            <span id="fix-rate" class="font-bold text-blue-600 dark:text-blue-400">75.0%</span>
                        </div>
                    </div>
                </div>

                <!-- Velocity Metrics -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg transition-all duration-200 ease-in-out border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 Hız Metrikleri</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Commit/Gün</span>
                            <span id="velocity-commits" class="font-bold text-purple-600 dark:text-purple-400">24</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Dosya Değişimi</span>
                            <span id="velocity-files" class="font-bold text-purple-600 dark:text-purple-400">156</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Satır (+/-)</span>
                            <span id="velocity-lines" class="font-bold text-purple-600 dark:text-purple-400">+2,847</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Üretkenlik Skoru</span>
                            <span id="productivity-score" class="font-bold text-green-600 dark:text-green-400">88.5</span>
                        </div>
                    </div>
                </div>

                <!-- Live Status -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg transition-all duration-200 ease-in-out border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🔴 Canlı Durum</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div id="mcp-status" class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-gray-600 dark:text-gray-300">MCP Server</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div id="git-status" class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-gray-600 dark:text-gray-300">Git Hooks</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div id="context7-status" class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-gray-600 dark:text-gray-300">Context7 Validator</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div id="bekci-status" class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-gray-600 dark:text-gray-300">Yalıhan Bekçi</span>
                        </div>
                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                Son güncelleme: <span id="last-update">{{ now()->format('H:i:s') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notification" class="fixed top-4 right-4 z-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4 transition-all duration-200 ease-in-out">
            <div class="flex items-center space-x-3">
                <div id="notification-icon" class="text-2xl"></div>
                <div>
                    <p id="notification-title" class="font-semibold text-gray-900 dark:text-white"></p>
                    <p id="notification-message" class="text-sm text-gray-600 dark:text-gray-300"></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize charts
            const complianceCtx = document.getElementById('complianceChart').getContext('2d');
            const velocityCtx = document.getElementById('velocityChart').getContext('2d');

            let complianceChart, velocityChart;

            // Chart colors for dark mode support
            const isDarkMode = document.documentElement.classList.contains('dark') || document.body.classList
                .contains('dark');
            const textColor = isDarkMode ? '#f3f4f6' : '#374151';
            const gridColor = isDarkMode ? '#374151' : '#e5e7eb';

            function initializeCharts() {
                // Compliance Chart
                complianceChart = new Chart(complianceCtx, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Context7 Uyumluluk %',
                            data: [85, 87, 92, 88, 94, 96, 93],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: textColor
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: textColor
                                },
                                grid: {
                                    color: gridColor
                                }
                            },
                            y: {
                                ticks: {
                                    color: textColor
                                },
                                grid: {
                                    color: gridColor
                                },
                                min: 0,
                                max: 100
                            }
                        }
                    }
                });

                // Velocity Chart
                velocityChart = new Chart(velocityCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Commits', 'Files', 'Lines', 'Fixes'],
                        datasets: [{
                            label: 'Bu Hafta',
                            data: [24, 156, 284, 19],
                            backgroundColor: ['#8b5cf6', '#06b6d4', '#f59e0b', '#10b981'],
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: textColor
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: textColor
                                },
                                grid: {
                                    color: gridColor
                                }
                            },
                            y: {
                                ticks: {
                                    color: textColor
                                },
                                grid: {
                                    color: gridColor
                                }
                            }
                        }
                    }
                });
            }

            function showNotification(title, message, type = 'info') {
                const notification = document.getElementById('notification');
                const icon = document.getElementById('notification-icon');
                const titleEl = document.getElementById('notification-title');
                const messageEl = document.getElementById('notification-message');

                const icons = {
                    success: '✅',
                    error: '❌',
                    warning: '⚠️',
                    info: 'ℹ️'
                };

                icon.textContent = icons[type];
                titleEl.textContent = title;
                messageEl.textContent = message;

                notification.classList.remove('hidden');
                setTimeout(() => notification.classList.add('hidden'), 5000);
            }

            function updateDashboard() {
                // Mock data update - replace with actual API call
                const mockData = {
                    health: {
                        overall_score: 85.2 + (Math.random() * 10 - 5),
                        context7_score: 92.8 + (Math.random() * 5 - 2.5),
                        status: 'good',
                        trend: 'improving'
                    },
                    violations: {
                        active: Math.floor(Math.random() * 5) + 1,
                        today: Math.floor(Math.random() * 10),
                        auto_fixed_today: Math.floor(Math.random() * 8)
                    },
                    activity: {
                        commits_today: Math.floor(Math.random() * 20) + 5,
                        builds_today: Math.floor(Math.random() * 15) + 3,
                        tests_run: Math.floor(Math.random() * 100) + 20
                    },
                    ai_learning: {
                        sessions_today: Math.floor(Math.random() * 10) + 2,
                        patterns_learned: Math.floor(Math.random() * 20) + 10,
                        ideas_generated: Math.floor(Math.random() * 30) + 15
                    }
                };

                // Update dashboard cards
                document.getElementById('health-score').textContent = mockData.health.overall_score.toFixed(1);
                document.getElementById('context7-score').textContent = mockData.health.context7_score.toFixed(1);
                document.getElementById('active-violations').textContent = mockData.violations.active;
                document.getElementById('commits-today').textContent = mockData.activity.commits_today;
                document.getElementById('builds-today').textContent = mockData.activity.builds_today;
                document.getElementById('tests-today').textContent = mockData.activity.tests_run;

                // Update AI learning metrics
                document.getElementById('ai-sessions').textContent = mockData.ai_learning.sessions_today;
                document.getElementById('patterns-learned').textContent = mockData.ai_learning.patterns_learned;
                document.getElementById('ideas-generated').textContent = mockData.ai_learning.ideas_generated;

                // Update last update time
                document.getElementById('last-update').textContent = new Date().toLocaleTimeString();

                console.log('Dashboard updated successfully');
            }

            // Event listeners
            document.getElementById('refresh-data').addEventListener('click', function() {
                this.classList.add('animate-spin');
                updateDashboard();
                setTimeout(() => this.classList.remove('animate-spin'), 1000);
                showNotification('Yenileme', 'Veriler güncellendi', 'success');
            });

            document.getElementById('recalculate-health').addEventListener('click', function() {
                showNotification('Sağlık Hesaplanıyor', 'Proje sağlığı yeniden hesaplanıyor...', 'info');
                setTimeout(() => {
                    updateDashboard();
                    showNotification('Sağlık Hesaplandı', 'Proje sağlığı başarıyla güncellendi',
                        'success');
                }, 2000);
            });

            // Initialize
            initializeCharts();
            updateDashboard();

            // Auto-refresh every 30 seconds
            setInterval(updateDashboard, 30000);
        });
    </script>
@endpush
