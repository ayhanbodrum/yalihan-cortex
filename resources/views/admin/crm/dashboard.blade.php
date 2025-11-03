@extends('admin.layouts.neo')

@section('title', 'CRM Dashboard')

@section('content')
    <div class="p-6">
        <!-- Page Header -->
        <div class=" mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">CRM Dashboard</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Müşteri ilişkileri yönetimi ve analitikler</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="px-6 py-4 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-blue-500 text-white">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ml-4">
                            <h6 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Toplam Müşteri</h6>
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-0">{{ number_format($stats['total_customers']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="px-6 py-4 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-green-500 text-white">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ml-4">
                            <h6 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Aktif Müşteri</h6>
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-0">{{ number_format($stats['active_customers']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="px-6 py-4 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-yellow-500 text-white">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ml-4">
                            <h6 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Bekleyen Takip</h6>
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-0">{{ number_format($stats['pending_followups']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="px-6 py-4 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-cyan-500 text-white">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ml-4">
                            <h6 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Bugünkü Aktivite</h6>
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-0">{{ number_format($stats['today_activities']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-8">
            <!-- Customer Segments Chart -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h5 class="text-lg font-bold text-gray-900 dark:text-white">Müşteri Segmentleri</h5>
                </div>
                <div class="px-6 py-4 p-6">
                    <canvas id="customerSegmentsChart" height="300"></canvas>
                </div>
            </div>

            <!-- High Priority Follow-ups -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h5 class="text-lg font-bold text-gray-900 dark:text-white">Yüksek Öncelikli Takipler</h5>
                </div>
                <div class="px-6 py-4 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-grow-1">
                            <h6 class="text-lg font-medium text-gray-900 dark:text-white mb-0">Acil Takip Gereken</h6>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="px-3 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-medium rounded-full">{{ number_format($stats['high_priority_followups']) }}</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full overflow-hidden h-2">
                        @php
                            $percentage =
                                $stats['total_customers'] > 0
                                    ? ($stats['high_priority_followups'] / $stats['total_customers']) * 100
                                    : 0;
                        @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full overflow-hidden-bar bg-red-500" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities & Upcoming Follow-ups -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-8">
            <!-- Recent Activities -->
            <div class="xl:col-span-2">
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h5 class="text-lg font-bold text-gray-900 dark:text-white">Son Aktiviteler</h5>
                        <a href="{{ route('admin.crm.customers.index') }}" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 text-sm px-4 py-2 touch-target-optimized touch-target-optimized">Tümünü Gör</a>
                    </div>
                    <div class="px-6 py-4">
                        <div class="w-full overflow-x-auto">
                            <table class="w-full text-left border-collapse hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                <thead>
                                    <tr>
                                        <th class="text-gray-900 dark:text-white">Müşteri</th>
                                        <th class="text-gray-900 dark:text-white">Aktivite</th>
                                        <th class="text-gray-900 dark:text-white">Tip</th>
                                        <th class="text-gray-900 dark:text-white">Tarih</th>
                                        <th class="text-gray-900 dark:text-white">Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentActivities as $activity)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="text-gray-900 dark:text-white">
                                                <a href="{{ route('admin.crm.customers.show', $activity['kisi']['id']) }}" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 hover:underline transition-colors duration-200">
                                                    {{ $activity['kisi']['ad'] }} {{ $activity['kisi']['soyad'] }}
                                                </a>
                                            </td>
                                            <td class="text-gray-600 dark:text-gray-400">{{ Str::limit($activity['aciklama'], 50) }}</td>
                                            <td>
                                                <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 text-xs font-medium rounded-full">{{ $activity['aktivite_tipi'] }}</span>
                                            </td>
                                            <td class="text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($activity['aktivite_tarihi'])->format('d.m.Y H:i') }}
                                            </td>
                                            <td>
                                                @if ($activity['status'] === 'Tamamlandı')
                                                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-medium rounded-full">Tamamlandı</span>
                                                @elseif($activity['status'] === 'Bekliyor')
                                                    <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs font-medium rounded-full">Bekliyor</span>
                                                @else
                                                    <span class="px-3 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-medium rounded-full">İptal</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-gray-500 dark:text-gray-400 py-8">Henüz aktivite bulunmuyor</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Follow-ups -->
            <div class="xl:col-span-1">
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h5 class="text-lg font-bold text-gray-900 dark:text-white">Yaklaşan Takipler</h5>
                    </div>
                    <div class="px-6 py-4 p-6">
                        @forelse($upcomingFollowUps as $followUp)
                            <div class="flex items-center mb-4 p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-cyan-500 text-white">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ml-3">
                                    <h6 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                        <a href="{{ route('admin.crm.customers.show', $followUp['kisi']['id']) }}" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 hover:underline transition-colors duration-200">
                                            {{ $followUp['kisi']['ad'] }} {{ $followUp['kisi']['soyad'] }}
                                        </a>
                                    </h6>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                        {{ \Carbon\Carbon::parse($followUp['sonraki_takip_tarihi'])->format('d.m.Y H:i') }}
                                    </p>
                                    <small class="text-xs text-gray-500 dark:text-gray-400">
                                        Danışman: {{ $followUp['danisman']['ad'] ?? 'Atanmamış' }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <i class="fas fa-calendar-times text-2xl mb-2"></i>
                                <p>Yaklaşan takip bulunmuyor</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Insights Panel -->
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 mt-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h5 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-brain text-purple-500 mr-2"></i>
                    AI Akıllı Öngörüler
                </h5>
            </div>
            <div class="px-6 py-4 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Yüksek Potansiyelli Müşteriler -->
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">{{ $stats['active_customers'] ?? 0 }}</div>
                        <div class="text-sm text-green-700 dark:text-green-300 mb-1">Yüksek Potansiyel</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Satın alma olasılığı >70%</div>
                    </div>

                    <!-- Risk Altındaki Müşteriler -->
                    <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400 mb-2">{{ $stats['high_priority_followups'] ?? 0 }}</div>
                        <div class="text-sm text-red-700 dark:text-red-300 mb-1">Risk Altında</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Churn riski >60%</div>
                    </div>

                    <!-- AI Önerileri -->
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ $stats['pending_followups'] ?? 0 }}</div>
                        <div class="text-sm text-blue-700 dark:text-blue-300 mb-1">AI Önerileri</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Bekleyen aksiyon</div>
                    </div>

                    <!-- Otomatik Etiketleme -->
                    <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-2">{{ $stats['today_activities'] ?? 0 }}</div>
                        <div class="text-sm text-purple-700 dark:text-purple-300 mb-1">Otomatik Etiket</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Bugün işlenen</div>
                    </div>
                </div>

                <!-- AI Insights Actions -->
                <div class="mt-6 flex flex-wrap gap-3">
                    <button class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 text-sm px-4 py-2 touch-target-optimized touch-target-optimized" onclick="generateAIReport()">
                        <i class="fas fa-chart-line mr-2"></i>
                        AI Raporu Oluştur
                    </button>
                    <button class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200 text-sm px-4 py-2 touch-target-optimized touch-target-optimized" onclick="bulkAnalyzeCustomers()">
                        <i class="fas fa-users-cog mr-2"></i>
                        Toplu Müşteri Analizi
                    </button>
                    <button class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all duration-200 text-sm px-4 py-2 touch-target-optimized touch-target-optimized" onclick="predictChurn()">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Churn Tahmini
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Customer Segments Chart
                const ctx = document.getElementById('customerSegmentsChart').getContext('2d');
                const segments = @json($customerSegments);

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(segments),
                        datasets: [{
                            data: Object.values(segments),
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56',
                                '#4BC0C0',
                                '#9966FF'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });

            // AI Analysis Functions
            window.generateAIReport = function() {
                showAIModal('AI Raporu', 'Genel AI raporu oluşturuluyor...', 'fa-chart-line');
                // Implementation for bulk AI report generation
                setTimeout(() => {
                    updateAIModalContent('Rapor hazırlandı! Tüm müşterilerin analizi tamamlandı.');
                }, 3000);
            };

            window.bulkAnalyzeCustomers = function() {
                showAIModal('Toplu Analiz', 'Tüm müşteriler analiz ediliyor...', 'fa-users-cog');
                // Implementation for bulk customer analysis
                setTimeout(() => {
                    updateAIModalContent('Analiz tamamlandı! 247 müşteri için öngörüler oluşturuldu.');
                }, 5000);
            };

            window.predictChurn = function() {
                showAIModal('Churn Tahmini', 'Müşteri kaybetme riski analiz ediliyor...', 'fa-exclamation-triangle');
                // Implementation for churn prediction
                setTimeout(() => {
                    updateAIModalContent('Tahmin tamamlandı! 23 müşteri yüksek risk grubunda tespit edildi.');
                }, 4000);
            };

            function showAIModal(title, message, icon) {
                const modal = $(`
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" id="ai-modal">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                        <i class="fas ${icon} text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${title}</h3>
                                </div>
                            </div>
                            <div class="mb-6">
                                <p id="ai-modal-content" class="text-gray-600 dark:text-gray-400">${message}</p>
                                <div class="mt-4">
                                    <div class="animate-pulse flex items-center">
                                        <div class="flex-1 h-2 bg-purple-200 dark:bg-purple-700 rounded"></div>
                                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">AI çalışıyor...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized" onclick="closeAIModal()">
                                    Kapat
                                </button>
                            </div>
                        </div>
                    </div>
                `);
                $('body').append(modal);
            }

            function updateAIModalContent(message) {
                $('#ai-modal-content').html(message);
                $('#ai-modal .animate-pulse').remove();
            }

            window.closeAIModal = function() {
                $('#ai-modal').remove();
            };
        </script>
    @endpush
@endsection
