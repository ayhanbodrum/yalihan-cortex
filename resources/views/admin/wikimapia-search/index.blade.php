@extends('admin.layouts.neo')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="wikimapiaManager()">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                WikiMapia Site/Apartman Arama
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Site bilgilerini WikiMapia'dan çekin ve ilanlarınıza ekleyin</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Map & Search --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Map --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        İnteraktif Harita
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Haritaya tıklayarak konum seçin</p>
                </div>
                <div id="map" class="w-full h-[500px]"></div>
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-xs text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Haritada tıkladığınız nokta için yakındaki yerler otomatik aranır</span>
                    </div>
                </div>
            </div>

            {{-- Search Form --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">🔍 Arama Kriterleri</h2>
                </div>
                <div class="p-6">
                    <form @submit.prevent="searchPlaces" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Site/Apartman Adı
                                </label>
                                <input 
                                    type="text" 
                                    x-model="searchQuery"
                                    placeholder="Örn: Bahçeşehir Sitesi"
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 transition-all duration-200">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Arama Yarıçapı
                                </label>
                                <div class="flex items-center gap-3">
                                    <input 
                                        type="range" 
                                        x-model="searchRadius" 
                                        min="0.01" 
                                        max="2" 
                                        step="0.1"
                                        class="flex-1">
                                    <span class="text-sm font-mono text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg min-w-[80px] text-center" x-text="(searchRadius * 100).toFixed(0) + ' km'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Latitude</label>
                                <input 
                                    type="number" 
                                    step="0.000001" 
                                    x-model="searchLat"
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 transition-all duration-200 font-mono text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Longitude</label>
                                <input 
                                    type="number" 
                                    step="0.000001" 
                                    x-model="searchLon"
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 transition-all duration-200 font-mono text-sm">
                            </div>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button 
                                type="submit"
                                :disabled="searching"
                                class="flex-1 px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg font-medium flex items-center justify-center gap-2 disabled:opacity-50">
                                <svg x-show="!searching" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <div x-show="searching" class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full"></div>
                                <span x-text="searching ? 'Aranıyor...' : 'Site/Apartman Ara'"></span>
                            </button>
                            
                            <button 
                                type="button"
                                @click="searchNearby"
                                :disabled="searching"
                                class="px-6 py-3 rounded-lg border-2 border-purple-600 text-purple-600 dark:text-purple-400 dark:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 font-medium flex items-center gap-2 disabled:opacity-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Yakındakiler
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Results --}}
            <div x-show="results.length > 0" x-cloak class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">📋 Sonuçlar</h2>
                        <span class="text-sm text-gray-600 dark:text-gray-400" x-text="`${results.length} yer bulundu`"></span>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <template x-for="(place, index) in results" :key="place.id">
                        <div class="group bg-gradient-to-r from-white to-gray-50 dark:from-gray-800 dark:to-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-400 transition-all duration-200 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors" x-text="place.title"></h3>
                                        
                                        <p x-show="place.description" class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2" x-text="place.description"></p>
                                        
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                                </svg>
                                                ID: <span x-text="place.id"></span>
                                            </span>
                                            <span x-show="place.location" class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full text-xs font-medium">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                </svg>
                                                <span x-text="`${place.location?.latitude?.toFixed(5)}, ${place.location?.longitude?.toFixed(5)}`"></span>
                                            </span>
                                        </div>
                                        
                                        <div class="flex gap-2">
                                            <button 
                                                type="button"
                                                @click="showPlaceDetail(place)"
                                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 text-sm font-medium flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Detay
                                            </button>
                                            
                                            <button 
                                                type="button"
                                                @click="selectSite(place)"
                                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 text-sm font-medium flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Seç
                                            </button>
                                            
                                            <a x-show="place.url" :href="place.url" target="_blank"
                                               class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 text-sm font-medium flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                                WikiMapia
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Empty State --}}
            <div x-show="!searching && results.length === 0" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-12 text-center">
                <div class="text-6xl mb-4">🗺️</div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aramaya Başlayın</h3>
                <p class="text-gray-600 dark:text-gray-400">Haritada bir konum seçin veya arama yapın</p>
            </div>
        </div>

        {{-- Right: Info & Stats --}}
        <div class="space-y-6">
            {{-- Quick Stats --}}
            <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-4">📊 İstatistikler</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span>Toplam Arama</span>
                        <span class="text-2xl font-bold" x-text="stats.totalSearches"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Bulunan Yer</span>
                        <span class="text-2xl font-bold" x-text="stats.totalPlaces"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Seçilen Site</span>
                        <span class="text-2xl font-bold" x-text="stats.selectedSites"></span>
                    </div>
                </div>
            </div>

            {{-- Selected Coordinates --}}
            <div x-show="searchLat && searchLon" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📍 Seçili Koordinat</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Latitude:</dt>
                        <dd class="font-mono text-gray-900 dark:text-white" x-text="searchLat"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Longitude:</dt>
                        <dd class="font-mono text-gray-900 dark:text-white" x-text="searchLon"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Yarıçap:</dt>
                        <dd class="font-mono text-gray-900 dark:text-white" x-text="(searchRadius * 100).toFixed(0) + ' km'"></dd>
                    </div>
                </dl>
            </div>

            {{-- Help --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Nasıl Kullanılır?
                </h3>
                <ol class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <li class="flex gap-2">
                        <span class="font-bold">1.</span>
                        <span>Haritada tıklayarak konum seçin</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="font-bold">2.</span>
                        <span>Site adı yazın ve arama yapın</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="font-bold">3.</span>
                        <span>"Detay" ile yer bilgilerini görün</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="font-bold">4.</span>
                        <span>"Seç" ile ilana ekleyin</span>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Place Detail Modal --}}
    <div x-show="selectedPlace" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="selectedPlace = null">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
             @click="selectedPlace = null"></div>
        
        {{-- Modal --}}
        <div class="flex min-h-screen items-center justify-center p-4">
            <div @click.stop
                 class="relative bg-white dark:bg-gray-900 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 w-full max-w-2xl transform transition-all">
                
                {{-- Header --}}
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white" x-text="selectedPlace?.title"></h3>
                        <button 
                            @click="selectedPlace = null"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-2 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                {{-- Body --}}
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <div class="space-y-4">
                        {{-- Description --}}
                        <div x-show="selectedPlace?.description">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">📝 Açıklama</h4>
                            <p class="text-gray-700 dark:text-gray-300 text-sm" x-text="selectedPlace?.description"></p>
                        </div>
                        
                        {{-- Location Info --}}
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">📍 Konum Bilgileri</h4>
                            <dl class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                    <dt class="text-gray-600 dark:text-gray-400 mb-1">Latitude</dt>
                                    <dd class="font-mono text-gray-900 dark:text-white" x-text="selectedPlace?.location?.latitude"></dd>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                    <dt class="text-gray-600 dark:text-gray-400 mb-1">Longitude</dt>
                                    <dd class="font-mono text-gray-900 dark:text-white" x-text="selectedPlace?.location?.longitude"></dd>
                                </div>
                            </dl>
                        </div>
                        
                        {{-- WikiMapia ID --}}
                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-xs text-purple-600 dark:text-purple-400 font-medium mb-1">WikiMapia Place ID</div>
                                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300" x-text="selectedPlace?.id"></div>
                                </div>
                                <svg class="w-12 h-12 text-purple-300 dark:text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Footer --}}
                <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-800 flex items-center justify-end gap-3">
                    <button 
                        @click="selectedPlace = null"
                        class="px-6 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 transition-colors duration-200 font-medium">
                        Kapat
                    </button>
                    <a x-show="selectedPlace?.url" :href="selectedPlace?.url" target="_blank"
                       class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors duration-200 font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        WikiMapia'da Aç
                    </a>
                    <button 
                        @click="selectSite(selectedPlace); selectedPlace = null"
                        class="px-6 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white transition-all duration-200 shadow-md hover:shadow-lg font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Site Olarak Seç
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
[x-cloak] { display: none !important; }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
function wikimapiaManager() {
    return {
        searchQuery: '',
        searchLat: 37.0345,
        searchLon: 27.4305,
        searchRadius: 0.5,
        searching: false,
        results: [],
        selectedPlace: null,
        map: null,
        marker: null,
        stats: {
            totalSearches: 0,
            totalPlaces: 0,
            selectedSites: 0
        },
        
        init() {
            this.$nextTick(() => {
                this.initMap();
            });
        },
        
        initMap() {
            // Initialize Leaflet map
            this.map = L.map('map').setView([this.searchLat, this.searchLon], 13);
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
            
            // Add click event
            this.map.on('click', (e) => {
                this.searchLat = e.latlng.lat.toFixed(6);
                this.searchLon = e.latlng.lng.toFixed(6);
                
                // Update marker
                if (this.marker) {
                    this.marker.setLatLng(e.latlng);
                } else {
                    this.marker = L.marker(e.latlng).addTo(this.map);
                }
                
                // Auto search nearby
                this.searchNearby();
            });
        },
        
        async searchPlaces() {
            if (!this.searchQuery) {
                if (window.toast) window.toast('warning', 'Lütfen arama terimi girin');
                return;
            }
            
            this.searching = true;
            this.stats.totalSearches++;
            
            try {
                const response = await fetch('{{ route("admin.wikimapia-search.search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        query: this.searchQuery,
                        lat: parseFloat(this.searchLat),
                        lon: parseFloat(this.searchLon),
                        radius: parseFloat(this.searchRadius)
                    })
                });
                
                const data = await response.json();
                
                if (data.success && data.data) {
                    this.results = data.data.places || [];
                    this.stats.totalPlaces += this.results.length;
                    
                    if (window.toast) {
                        window.toast('success', `${this.results.length} yer bulundu!`);
                    }
                } else {
                    if (window.toast) window.toast('error', 'Sonuç bulunamadı');
                }
            } catch (error) {
                console.error('Search error:', error);
                if (window.toast) window.toast('error', 'Arama hatası oluştu');
            } finally {
                this.searching = false;
            }
        },
        
        async searchNearby() {
            this.searching = true;
            this.stats.totalSearches++;
            
            try {
                const response = await fetch('{{ route("admin.wikimapia-search.nearby") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        lat: parseFloat(this.searchLat),
                        lon: parseFloat(this.searchLon),
                        radius: parseFloat(this.searchRadius)
                    })
                });
                
                const data = await response.json();
                
                if (data.success && data.data) {
                    this.results = data.data.places || [];
                    this.stats.totalPlaces += this.results.length;
                    
                    if (window.toast) {
                        window.toast('success', `${this.results.length} yer bulundu!`);
                    }
                }
            } catch (error) {
                console.error('Nearby search error:', error);
                if (window.toast) window.toast('error', 'Yakındaki yerler bulunamadı');
            } finally {
                this.searching = false;
            }
        },
        
        showPlaceDetail(place) {
            this.selectedPlace = place;
        },
        
        selectSite(place) {
            // LocalStorage'a kaydet
            localStorage.setItem('selectedWikimapiaSite', JSON.stringify({
                wikimapia_id: place.id,
                name: place.title,
                description: place.description,
                latitude: place.location?.latitude,
                longitude: place.location?.longitude,
                url: place.url
            }));
            
            this.stats.selectedSites++;
            
            if (window.toast) {
                window.toast('success', `✅ ${place.title} seçildi! İlan formunda kullanılabilir.`);
            }
            
            // Eğer iframe içindeyse parent'a mesaj gönder
            if (window.parent !== window) {
                window.parent.postMessage({
                    type: 'wikimapia_site_selected',
                    site: {
                        wikimapia_id: place.id,
                        name: place.title,
                        latitude: place.location?.latitude,
                        longitude: place.location?.longitude
                    }
                }, '*');
            }
        }
    };
}
</script>
@endpush
@endsection
