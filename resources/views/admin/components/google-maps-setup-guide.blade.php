{{-- Google Maps API Setup Guide Modal --}}
<div id="mapsSetupModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-map-marked-alt mr-2 text-blue-600"></i>
                    Google Maps API Kurulum Rehberi
                </h3>
                <button onclick="closeMapsSetupModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-6">
                {{-- Current Status --}}
                <div
                    class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                API Yetkilendirme Sorunu
                            </h4>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                Geocoding API: Bu API projesi bu API'yi kullanma yetkisine sahip değil.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Step by step guide --}}
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                        Çözüm Adımları:
                    </h4>

                    <div class="space-y-3">
                        {{-- Step 1 --}}
                        <div class="flex items-start p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="flex-shrink-0">
                                <span
                                    class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">1</span>
                            </div>
                            <div class="ml-3">
                                <h5 class="font-medium text-gray-900 dark:text-white">Google Cloud Console'a Giriş
                                </h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <a href="https://console.cloud.google.com/" target="_blank"
                                        class="text-blue-600 hover:underline">
                                        https://console.cloud.google.com/
                                    </a> adresine gidin
                                </p>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex items-start p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="flex-shrink-0">
                                <span
                                    class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">2</span>
                            </div>
                            <div class="ml-3">
                                <h5 class="font-medium text-gray-900 dark:text-white">API'leri Etkinleştirin</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    "APIs & Services" > "Library" bölümünden şu API'leri etkinleştirin:
                                </p>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 mt-2 space-y-1">
                                    <li>• <strong>Maps JavaScript API</strong> ✅ (zaten aktif)</li>
                                    <li>• <strong>Geocoding API</strong> ❌ (etkinleştirin)</li>
                                    <li>• <strong>Places API (New)</strong> 📍 (önerilen)</li>
                                    <li>• <strong>Geolocation API</strong> 📱 (isteğe bağlı)</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex items-start p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <div class="flex-shrink-0">
                                <span
                                    class="bg-purple-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">3</span>
                            </div>
                            <div class="ml-3">
                                <h5 class="font-medium text-gray-900 dark:text-white">API Key Kısıtlamaları</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    "Credentials" > API Key > "Restrict key" ayarları:
                                </p>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    <strong>HTTP referrers:</strong><br>
                                    <code class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-xs">
                                        http://localhost:8000/*<br>
                                        https://yourdomain.com/*
                                    </code>
                                </div>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="flex items-start p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <div class="flex-shrink-0">
                                <span
                                    class="bg-orange-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">4</span>
                            </div>
                            <div class="ml-3">
                                <h5 class="font-medium text-gray-900 dark:text-white">Test Edin</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Ayarları kaydedin ve 2-3 dakika bekledikten sonra sayfayı yenileyin.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Current API Key Info --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">
                        Mevcut API Key:
                    </h5>
                    <code class="text-xs bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded block">
                        {{ config('services.google_maps.api_key', 'AIzaSy...') }}
                    </code>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Bu key Google Cloud Console'da yapılandırılmalı
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button onclick="closeMapsSetupModal()"
                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        Kapat
                    </button>
                    <a href="https://console.cloud.google.com/apis/library" target="_blank"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        Google Cloud Console'a Git
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openMapsSetupModal() {
            const modal = document.getElementById('mapsSetupModal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }

        function closeMapsSetupModal() {
            const modal = document.getElementById('mapsSetupModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
        } // Auto-show modal if geocoding errors detected
        document.addEventListener('DOMContentLoaded', function() {
            // Check console for geocoding errors after 2 seconds
            setTimeout(() => {
                const hasGeocodingError = console.log.toString().includes(
                    'Geocoding Service: This API project is not authorized');
                if (hasGeocodingError) {
                    // Auto-show setup guide if there are API errors
                    // Uncomment next line if you want auto-show
                    // openMapsSetupModal();
                }
            }, 2000);
        });
    </script>
