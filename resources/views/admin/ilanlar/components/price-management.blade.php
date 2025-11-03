{{-- 🎨 Section 5: Fiyat Yönetimi (Tailwind Modernized) --}}
<div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 hover:shadow-2xl transition-shadow duration-300">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-500 to-orange-600 text-white shadow-lg shadow-yellow-500/50 font-bold text-lg">
            5
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Fiyat Yönetimi
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Fiyat ve para birimi bilgileri</p>
        </div>
    </div>

    <div x-data="advancedPriceManager()" class="space-y-6">
        {{-- Ana Fiyat ve Para Birimi - Enhanced --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="group">
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 text-xs font-bold">
                        1
                    </span>
                    Ana Fiyat
                    <span class="text-red-500 font-bold">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="fiyat" 
                        id="fiyat" 
                        x-model="mainPriceInput"
                        @input="onPriceInputChange()" 
                        @blur="onPriceBlur()" 
                        required
                        placeholder="450000 veya 450-"
                        class="w-full px-5 py-4 pr-32
                               border-2 border-gray-300 dark:border-gray-600 
                               rounded-xl 
                               bg-white dark:bg-gray-800 
                               text-black dark:text-white text-lg font-semibold
                               placeholder-gray-400 dark:placeholder-gray-500
                               focus:ring-4 focus:ring-yellow-500/20 focus:border-yellow-500 dark:focus:border-yellow-400
                               transition-all duration-200
                               hover:border-gray-400 dark:hover:border-gray-500
                               shadow-sm hover:shadow-md focus:shadow-lg">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <select x-model="mainCurrency" 
                            @change="onCurrencyChange()" 
                            name="para_birimi" 
                            required
                            class="px-4 py-2.5 
                                   border-0 border-l-2 border-gray-200 dark:border-gray-600
                                   bg-white dark:bg-gray-800 
                                   text-black dark:text-white 
                                   font-semibold text-sm rounded-r-lg
                                   focus:outline-none focus:ring-2 focus:ring-yellow-500/50
                                   cursor-pointer transition-all duration-200">
                            <option value="TRY">₺ TL</option>
                            <option value="USD">$ USD</option>
                            <option value="EUR">€ EUR</option>
                            <option value="GBP">£ GBP</option>
                        </select>
                    </div>
                </div>
                
                <!-- Price Display - Enhanced -->
                <div class="mt-4 space-y-3">
                    <div class="flex items-center gap-2 p-3 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800/30">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-sm font-bold text-yellow-900 dark:text-yellow-100" x-text="formatPrice(mainPrice, mainCurrency)"></span>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800/30">
                        <p class="text-xs text-blue-800 dark:text-blue-200 capitalize">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            <span x-text="numberToWords(mainPrice)"></span>
                            <span class="font-semibold ml-1" x-text="mainCurrency"></span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Döviz Çevirici --}}
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Otomatik
                    Döviz Çevirimi</label>
                <div
                    class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between items-center text-sm" x-show="mainCurrency !== 'TRY'">
                        <span class="text-gray-600 dark:text-gray-400">₺ TL:</span>
                        <span class="font-bold text-gray-900 dark:text-white" x-text="convertedPrices.TRY"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm" x-show="mainCurrency !== 'USD'">
                        <span class="text-gray-600 dark:text-gray-400">$ USD:</span>
                        <span class="font-bold text-gray-900 dark:text-white" x-text="convertedPrices.USD"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm" x-show="mainCurrency !== 'EUR'">
                        <span class="text-gray-600 dark:text-gray-400">€ EUR:</span>
                        <span class="font-bold text-gray-900 dark:text-white" x-text="convertedPrices.EUR"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm" x-show="mainCurrency !== 'GBP'">
                        <span class="text-gray-600 dark:text-gray-400">£ GBP:</span>
                        <span class="font-bold text-gray-900 dark:text-white" x-text="convertedPrices.GBP"></span>
                    </div>
                    <div
                        class="mt-2 pt-2 border-t border-yellow-200 dark:border-yellow-700 flex items-center justify-between">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-sync-alt mr-1"></i>
                            <span x-text="'Son güncelleme: ' + lastRateUpdate"></span>
                        </div>
                        <button type="button" @click="loadExchangeRates()"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                            <i class="fas fa-redo mr-1"></i>Yenile
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- AI Fiyat Önerileri --}}
        <div x-show="aiSuggestions.length > 0"
            class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">🤖 AI Fiyat Önerileri
                </h4>
                <button type="button" @click="refreshAISuggestions()"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                    <i class="fas fa-sync-alt mr-1"></i>Yenile
                </button>
            </div>
            <div class="space-y-2">
                <template x-for="(suggestion, index) in aiSuggestions" :key="index">
                    <div
                        class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:shadow-md transition-shadow">
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="suggestion.label">
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="suggestion.reason"></div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="font-bold text-blue-600 dark:text-blue-400"
                                x-text="suggestion.formatted"></span>
                            <button type="button" @click="applySuggestion(suggestion)"
                                class="px-3 py-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all">
                                Uygula
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- M² Başı Fiyat (Otomatik Hesaplanan) --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4"
            x-show="metrekare > 0 && mainPrice > 0"
            @metrekare-changed.window="metrekare = $event.detail.value; calculatePricePerSqm()">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-900 dark:text-white">📐 M² Başı Fiyat:</span>
                <span class="text-lg font-bold text-blue-600 dark:text-blue-400"
                    x-text="pricePerSqm + ' ' + mainCurrency"></span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Toplam fiyat / Metrekare ile otomatik hesaplanır
            </p>
        </div>

        {{-- Ek Fiyat Seçenekleri --}}
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">Ek
                Fiyatlandırma</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center mb-2 cursor-pointer">
                        <input type="checkbox" x-model="showStartingPrice" class="mr-2 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-900 dark:text-white">Başlangıç Fiyatı
                            (Pazarlık)</span>
                    </label>
                    <div x-show="showStartingPrice" x-collapse class="mt-2">
                        <input type="text" x-model="startingPriceFormatted" @input="formatStartingPrice()"
                            name="baslangic_fiyati"
                            class="w-full px-4 py-2.5
                                   border-2 border-gray-300 dark:border-gray-600 
                                   rounded-xl 
                                   bg-white dark:bg-gray-800 
                                   text-black dark:text-white
                                   placeholder-gray-400 dark:placeholder-gray-500
                                   focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/20 focus:border-green-500 dark:focus:border-green-400
                                   transition-all duration-200
                                   shadow-sm hover:shadow-md focus:shadow-lg"
                            placeholder="Pazarlık için başlangıç fiyatı (örn: 4.500.000)">
                    </div>
                </div>

                <div>
                    <label class="flex items-center mb-2 cursor-pointer">
                        <input type="checkbox" x-model="showDailyPrice" class="mr-2 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-900 dark:text-white">Günlük Fiyat (Yazlık)</span>
                    </label>
                    <div x-show="showDailyPrice" x-collapse class="mt-2">
                        <input type="text" x-model="dailyPriceFormatted" @input="formatDailyPrice()"
                            name="gunluk_fiyat"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg"
                            placeholder="Günlük kiralama fiyatı">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
