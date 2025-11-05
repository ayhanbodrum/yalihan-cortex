{{-- ========================================
     PRICE HISTORY CHART COMPONENT
     Fiyat geçmişi grafik görselleştirme
     ======================================== --}}

@props([
    'ilan' => null,
    'height' => '400px',
    'showStats' => true,
    'showTable' => true,
])

<div class="price-history-chart bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6" 
     x-data="priceHistoryChart({{ $ilan->id ?? 0 }})"
     x-init="loadPriceHistory()">
    
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Fiyat Geçmişi</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Zaman içindeki fiyat değişimleri</p>
            </div>
        </div>
        
        {{-- Filtreler --}}
        <div class="flex items-center gap-2">
            <select x-model="timeRange" @change="loadPriceHistory()" 
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                <option value="all">Tüm Zamanlar</option>
                <option value="7">Son 7 Gün</option>
                <option value="30">Son 30 Gün</option>
                <option value="90">Son 3 Ay</option>
                <option value="365">Son 1 Yıl</option>
            </select>
        </div>
    </div>

    {{-- Loading State --}}
    <div x-show="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4 text-gray-600 dark:text-gray-400">Yükleniyor...</p>
    </div>

    {{-- Error State --}}
    <div x-show="error && !loading" class="text-center py-12">
        <div class="text-red-500 mb-2">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-red-600 dark:text-red-400" x-text="error"></p>
    </div>

    {{-- Chart Container --}}
    <div x-show="!loading && !error" class="space-y-6">
        {{-- İstatistikler --}}
        @if($showStats)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                <div class="text-sm text-blue-600 dark:text-blue-400 font-medium mb-1">Başlangıç Fiyatı</div>
                <div class="text-2xl font-bold text-blue-900 dark:text-blue-100" x-text="formatPrice(stats.initialPrice)"></div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                <div class="text-sm text-green-600 dark:text-green-400 font-medium mb-1">Güncel Fiyat</div>
                <div class="text-2xl font-bold text-green-900 dark:text-green-100" x-text="formatPrice(stats.currentPrice)"></div>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-lg p-4 border border-orange-200 dark:border-orange-800">
                <div class="text-sm text-orange-600 dark:text-orange-400 font-medium mb-1">En Yüksek</div>
                <div class="text-2xl font-bold text-orange-900 dark:text-orange-100" x-text="formatPrice(stats.maxPrice)"></div>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                <div class="text-sm text-purple-600 dark:text-purple-400 font-medium mb-1">Fiyat Değişimi</div>
                <div class="text-2xl font-bold" 
                     :class="stats.priceChange >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                     x-text="formatPriceChange(stats.priceChange, stats.priceChangePercent)"></div>
            </div>
        </div>
        @endif

        {{-- Chart Canvas --}}
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
            <canvas id="priceHistoryChart-{{ $ilan->id ?? 'default' }}" style="height: {{ $height }};"></canvas>
        </div>

        {{-- Fiyat Geçmişi Tablosu --}}
        @if($showTable)
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Fiyat Değişim Geçmişi</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Eski Fiyat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Yeni Fiyat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Değişim</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Neden</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="(item, index) in history" :key="index">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="formatDate(item.created_at)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400" x-text="formatPrice(item.old_price)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white" x-text="formatPrice(item.new_price)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" 
                                    :class="getPriceChangeClass(item.new_price - item.old_price)">
                                    <span x-text="formatPriceChange(item.new_price - item.old_price, ((item.new_price - item.old_price) / item.old_price * 100))"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="getChangeReason(item.change_reason)"></td>
                            </tr>
                        </template>
                        <tr x-show="history.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                Henüz fiyat geçmişi kaydı bulunmamaktadır.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
function priceHistoryChart(ilanId) {
    let chartInstance = null;
    
    return {
        ilanId: ilanId,
        loading: true,
        error: null,
        history: [],
        stats: {
            initialPrice: 0,
            currentPrice: 0,
            maxPrice: 0,
            minPrice: 0,
            priceChange: 0,
            priceChangePercent: 0
        },
        timeRange: 'all',
        
        async loadPriceHistory() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await fetch(`/admin/ilanlar/${this.ilanId}/price-history?range=${this.timeRange}`);
                const data = await response.json();
                
                if (data.success) {
                    this.history = data.data.reverse(); // Tarih sırasına göre düzenle
                    this.calculateStats();
                    this.renderChart();
                } else {
                    this.error = data.message || 'Fiyat geçmişi yüklenirken bir hata oluştu.';
                }
            } catch (error) {
                this.error = 'Fiyat geçmişi yüklenirken bir hata oluştu.';
                console.error('Price history error:', error);
            } finally {
                this.loading = false;
            }
        },
        
        calculateStats() {
            if (this.history.length === 0) return;
            
            const prices = this.history.map(h => h.new_price);
            this.stats.initialPrice = this.history[0]?.old_price || this.history[0]?.new_price || 0;
            this.stats.currentPrice = this.history[this.history.length - 1]?.new_price || 0;
            this.stats.maxPrice = Math.max(...prices);
            this.stats.minPrice = Math.min(...prices);
            this.stats.priceChange = this.stats.currentPrice - this.stats.initialPrice;
            this.stats.priceChangePercent = this.stats.initialPrice > 0 
                ? (this.stats.priceChange / this.stats.initialPrice) * 100 
                : 0;
        },
        
        renderChart() {
            const ctx = document.getElementById(`priceHistoryChart-${this.ilanId}`);
            if (!ctx) return;
            
            // Önceki chart'ı temizle
            if (chartInstance) {
                chartInstance.destroy();
            }
            
            const labels = this.history.map(h => this.formatDateShort(h.created_at));
            const prices = this.history.map(h => h.new_price);
            const changes = this.history.map((h, i) => {
                if (i === 0) return 0;
                return h.new_price - this.history[i - 1].new_price;
            });
            
            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Fiyat',
                            data: prices,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#fff' : '#374151'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: (context) => {
                                    const index = context.dataIndex;
                                    const change = changes[index];
                                    const changePercent = index > 0 && this.history[index - 1].new_price > 0
                                        ? ((change / this.history[index - 1].new_price) * 100).toFixed(2)
                                        : 0;
                                    return [
                                        `Fiyat: ${this.formatPrice(context.parsed.y)}`,
                                        change !== 0 ? `Değişim: ${this.formatPrice(change)} (${changePercent > 0 ? '+' : ''}${changePercent}%)` : ''
                                    ].filter(Boolean);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: (value) => this.formatPrice(value),
                                font: {
                                    size: 12
                                },
                                color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#9CA3AF' : '#6B7280'
                            },
                            grid: {
                                color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 12
                                },
                                color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#9CA3AF' : '#6B7280'
                            },
                            grid: {
                                color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        },
        
        formatPrice(price) {
            if (!price) return '0 ₺';
            return new Intl.NumberFormat('tr-TR', {
                style: 'currency',
                currency: 'TRY',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(price).replace('TRY', '₺');
        },
        
        formatPriceChange(change, percent) {
            const sign = change >= 0 ? '+' : '';
            return `${sign}${this.formatPrice(change)} (${sign}${percent.toFixed(2)}%)`;
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('tr-TR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        },
        
        formatDateShort(dateString) {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('tr-TR', {
                month: 'short',
                day: 'numeric'
            }).format(date);
        },
        
        getPriceChangeClass(change) {
            if (change > 0) return 'text-green-600 dark:text-green-400 font-semibold';
            if (change < 0) return 'text-red-600 dark:text-red-400 font-semibold';
            return 'text-gray-600 dark:text-gray-400';
        },
        
        getChangeReason(reason) {
            const reasons = {
                'manual_update': 'Manuel Güncelleme',
                'price_adjustment': 'Fiyat Ayarlaması',
                'market_change': 'Piyasa Değişimi',
                'seasonal': 'Sezonluk Değişim',
                'negotiation': 'Pazarlık',
                'promotion': 'Promosyon'
            };
            return reasons[reason] || reason || '-';
        }
    }
}
</script>

