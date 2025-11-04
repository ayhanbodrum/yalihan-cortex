@props([
    'size' => 'default', // compact, default, full
    'showConverter' => true,
    'showHistory' => false
])

<div x-data="exchangeRateWidget()" x-init="init()" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-r from-green-600 to-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Güncel Döviz Kurları</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400" x-show="lastUpdate" x-text="'Son: ' + new Date(lastUpdate).toLocaleTimeString('tr-TR')"></p>
                </div>
            </div>
            
            <button 
                @click="loadRates()"
                :disabled="loading"
                class="p-2 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800 transition-colors duration-200"
                title="Yenile">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>
    
    {{-- Error State --}}
    <div x-show="error" class="px-4 py-3 bg-red-50 dark:bg-red-900/20 border-b border-red-200 dark:border-red-800">
        <p class="text-sm text-red-600 dark:text-red-400" x-text="error"></p>
    </div>
    
    {{-- Loading State --}}
    <div x-show="loading && rates.length === 0" class="px-4 py-8 text-center">
        <div class="inline-block w-8 h-8 border-4 border-green-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Kurlar yükleniyor...</p>
    </div>
    
    {{-- Rates List --}}
    <div x-show="!loading || rates.length > 0" class="p-4 space-y-2">
        <template x-for="rate in rates" :key="rate.code">
            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-green-600 to-blue-600 flex items-center justify-center text-white text-xs font-bold" x-text="rate.code"></div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="rate.name"></div>
                        <div class="text-xs text-gray-600 dark:text-gray-400" x-text="rate.source"></div>
                    </div>
                </div>
                
                <div class="text-right">
                    <div class="text-lg font-bold text-gray-900 dark:text-white" x-text="'₺' + rate.forex_selling.toFixed(4)"></div>
                    <div class="flex items-center gap-1 text-xs">
                        <span class="text-gray-600 dark:text-gray-400">Alış:</span>
                        <span class="font-mono text-gray-700 dark:text-gray-300" x-text="rate.forex_buying.toFixed(4)"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>
    
    @if($showConverter)
    {{-- Currency Converter --}}
    <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">💱 Hızlı Çevirici</h4>
        
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Miktar</label>
                <input 
                    type="number" 
                    x-model.number="amount"
                    @input.debounce.500ms="calculateConversion()"
                    min="0"
                    step="0.01"
                    class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
            </div>
            
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Para Birimi</label>
                <select 
                    x-model="selectedCurrency"
                    @change="calculateConversion()"
                    class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                    <template x-for="rate in rates" :key="rate.code">
                        <option :value="rate.code" x-text="rate.code"></option>
                    </template>
                </select>
            </div>
        </div>
        
        <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Türk Lirası Karşılığı</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="formatCurrency(convertedAmount, 'TRY')"></div>
        </div>
    </div>
    @endif
    
    {{-- Footer Info --}}
    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-2 bg-gray-50 dark:bg-gray-800">
        <p class="text-xs text-gray-600 dark:text-gray-400 text-center">
            <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Kaynak: TCMB (Türkiye Cumhuriyet Merkez Bankası) • Otomatik güncellenme: Her gün 10:00
        </p>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/exchange-rate-widget.js') }}"></script>
@endpush

