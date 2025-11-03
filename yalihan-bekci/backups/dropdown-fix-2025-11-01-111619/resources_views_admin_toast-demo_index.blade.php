@extends('admin.layouts.neo')

@section('title', 'Toast Bildirim Demo')

@push('meta')
    <meta name="description" content="EmlakPro Toast Notification Demo - Interactive toast notification testing system">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div x-data="toastDemoManager()" x-init="init()">
        <!-- Header -->
        <div class="content-header mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z">
                                </path>
                            </svg>
                        </div>
                        🔔 Toast Bildirim Demo
                    </h1>
                    <p class="text-gray-600 mt-2">İnteraktif bildirim sistemi test arayüzü ve konfigürasyon yönetimi</p>
                </div>
                <div class="flex space-x-3">
                    <button @click="exportLogs()" :disabled="exporting" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span x-text="exporting ? 'Dışa Aktarılıyor...' : 'Log Export'"></span>
                    </button>
                    <button @click="toggleAnalyticsModal()" class="neo-btn neo-btn-success touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Analitik
                    </button>
                    <button @click="bulkTest()" :disabled="bulkTesting" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span x-text="bulkTesting ? 'Test Ediliyor...' : 'Toplu Test'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Toast</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_toasts']) }}</p>
                    </div>
                </div>
            </div>

            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Başarılı Gösterim</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['successful_displays']) }}</p>
                    </div>
                </div>
            </div>

            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Hata Sayısı</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['error_count']) }}</p>
                    </div>
                </div>
            </div>

            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Başarı Oranı</p>
                        <p class="text-2xl font-bold text-gray-900">%{{ number_format($stats['success_rate'], 1) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Type Testing Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Success Toast -->
            <div class="neo-card p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">✅ Başarı Toast</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Pozitif geri bildirim ve başarılı işlemler için kullanılan toast
                    bildirimleri.</p>

                <div class="space-y-2 mb-4">
                    <input x-model="toastConfig.success.message" type="text" placeholder="Başarı mesajı..."
                        class="neo-input w-full text-sm">
                    <div class="flex space-x-2">
                        <input x-model="toastConfig.success.duration" type="number" placeholder="Süre (ms)"
                            class="neo-input w-1/2 text-sm">
                        <select x-model="toastConfig.success.position" class="neo-select w-1/2 text-sm">
                            <option value="top-right">Sağ Üst</option>
                            <option value="top-left">Sol Üst</option>
                            <option value="bottom-right">Sağ Alt</option>
                            <option value="bottom-left">Sol Alt</option>
                        </select>
                    </div>
                </div>

                <button @click="showToast('success')" class="neo-btn neo-btn-success w-full touch-target-optimized touch-target-optimized">
                    🎯 Test Et
                </button>
            </div>

            <!-- Error Toast -->
            <div class="neo-card p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">❌ Hata Toast</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Hata durumları ve başarısız işlemler için kullanılan toast
                    bildirimleri.</p>

                <div class="space-y-2 mb-4">
                    <input x-model="toastConfig.error.message" type="text" placeholder="Hata mesajı..."
                        class="neo-input w-full text-sm">
                    <div class="flex space-x-2">
                        <input x-model="toastConfig.error.duration" type="number" placeholder="Süre (ms)"
                            class="neo-input w-1/2 text-sm">
                        <select x-model="toastConfig.error.position" class="neo-select w-1/2 text-sm">
                            <option value="top-right">Sağ Üst</option>
                            <option value="top-left">Sol Üst</option>
                            <option value="bottom-right">Sağ Alt</option>
                            <option value="bottom-left">Sol Alt</option>
                        </select>
                    </div>
                </div>

                <button @click="showToast('error')" class="neo-btn neo-btn-danger w-full touch-target-optimized touch-target-optimized">
                    🚨 Test Et
                </button>
            </div>

            <!-- Warning Toast -->
            <div class="neo-card p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">⚠️ Uyarı Toast</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Dikkat edilmesi gereken durumlar için kullanılan toast bildirimleri.
                </p>

                <div class="space-y-2 mb-4">
                    <input x-model="toastConfig.warning.message" type="text" placeholder="Uyarı mesajı..."
                        class="neo-input w-full text-sm">
                    <div class="flex space-x-2">
                        <input x-model="toastConfig.warning.duration" type="number" placeholder="Süre (ms)"
                            class="neo-input w-1/2 text-sm">
                        <select x-model="toastConfig.warning.position" class="neo-select w-1/2 text-sm">
                            <option value="top-right">Sağ Üst</option>
                            <option value="top-left">Sol Üst</option>
                            <option value="bottom-right">Sağ Alt</option>
                            <option value="bottom-left">Sol Alt</option>
                        </select>
                    </div>
                </div>

                <button @click="showToast('warning')" class="neo-btn neo-btn-warning w-full touch-target-optimized touch-target-optimized">
                    ⚡ Test Et
                </button>
            </div>

            <!-- Info Toast -->
            <div class="neo-card p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">ℹ️ Bilgi Toast</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Bilgilendirme amaçlı kullanılan toast bildirimleri.</p>

                <div class="space-y-2 mb-4">
                    <input x-model="toastConfig.info.message" type="text" placeholder="Bilgi mesajı..."
                        class="neo-input w-full text-sm">
                    <div class="flex space-x-2">
                        <input x-model="toastConfig.info.duration" type="number" placeholder="Süre (ms)"
                            class="neo-input w-1/2 text-sm">
                        <select x-model="toastConfig.info.position" class="neo-select w-1/2 text-sm">
                            <option value="top-right">Sağ Üst</option>
                            <option value="top-left">Sol Üst</option>
                            <option value="bottom-right">Sağ Alt</option>
                            <option value="bottom-left">Sol Alt</option>
                        </select>
                    </div>
                </div>

                <button @click="showToast('info')" class="neo-btn neo-btn-info w-full touch-target-optimized touch-target-optimized">
                    💡 Test Et
                </button>
            </div>

            <!-- Loading Toast -->
            <div class="neo-card p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">⏳ Yükleme Toast</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">İşlem devam ederken gösterilen toast bildirimleri.</p>

                <div class="space-y-2 mb-4">
                    <input x-model="toastConfig.loading.message" type="text" placeholder="Yükleme mesajı..."
                        class="neo-input w-full text-sm">
                    <div class="flex space-x-2">
                        <input x-model="toastConfig.loading.duration" type="number" placeholder="Süre (ms)"
                            class="neo-input w-1/2 text-sm">
                        <select x-model="toastConfig.loading.position" class="neo-select w-1/2 text-sm">
                            <option value="top-right">Sağ Üst</option>
                            <option value="top-left">Sol Üst</option>
                            <option value="bottom-right">Sağ Alt</option>
                            <option value="bottom-left">Sol Alt</option>
                        </select>
                    </div>
                </div>

                <button @click="showToast('loading')" class="neo-btn neo-btn neo-btn-secondary w-full touch-target-optimized touch-target-optimized">
                    🔄 Test Et
                </button>
            </div>

            <!-- Custom Toast -->
            <div class="neo-card p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">🎨 Özel Toast</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Özelleştirilebilir toast bildirimleri.</p>

                <div class="space-y-2 mb-4">
                    <input x-model="toastConfig.custom.message" type="text" placeholder="Özel mesaj..."
                        class="neo-input w-full text-sm">
                    <div class="flex space-x-2">
                        <input x-model="toastConfig.custom.duration" type="number" placeholder="Süre (ms)"
                            class="neo-input w-1/2 text-sm">
                        <input x-model="toastConfig.custom.color" type="color" class="neo-input w-1/2 text-sm">
                    </div>
                </div>

                <button @click="showToast('custom')" class="neo-btn neo-btn neo-btn-primary w-full touch-target-optimized touch-target-optimized">
                    ✨ Test Et
                </button>
            </div>
        </div>

        <!-- Configuration Panel -->
        <div class="neo-card p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">⚙️ Global Konfigürasyon</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Varsayılan Süre (ms)</label>
                    <input x-model="globalConfig.defaultDuration" type="number" class="neo-input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maksimum Toast Sayısı</label>
                    <input x-model="globalConfig.maxToasts" type="number" class="neo-input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Animasyon Süresi (ms)</label>
                    <input x-model="globalConfig.animationDuration" type="number" class="neo-input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Otomatik Kapanma</label>
                    <select x-model="globalConfig.autoClose" class="neo-select w-full">
                        <option value="true">Aktif</option>
                        <option value="false">Pasif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ses Bildirimi</label>
                    <select x-model="globalConfig.soundEnabled" class="neo-select w-full">
                        <option value="true">Aktif</option>
                        <option value="false">Pasif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vibrasyon</label>
                    <select x-model="globalConfig.vibrationEnabled" class="neo-select w-full">
                        <option value="true">Aktif</option>
                        <option value="false">Pasif</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-6 space-x-3">
                <button @click="resetConfig()" class="neo-btn neo-btn-outline touch-target-optimized touch-target-optimized">
                    🔄 Varsayılana Dön
                </button>
                <button @click="saveConfig()" :disabled="saving" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                    <span x-text="saving ? 'Kaydediliyor...' : '💾 Kaydet'"></span>
                </button>
            </div>
        </div>

        <!-- Test Results Log -->
        <div class="neo-card p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">📋 Test Sonuçları</h2>
                <button @click="clearLogs()" class="neo-btn neo-btn-sm neo-btn-outline touch-target-optimized touch-target-optimized">
                    🗑️ Temizle
                </button>
            </div>

            <div class="space-y-2 max-h-64 overflow-y-auto">
                <template x-for="(log, index) in testLogs" :key="index">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm">
                        <div class="flex items-center">
                            <span :class="getLogIcon(log.type)" class="mr-2"></span>
                            <span class="font-medium" x-text="log.type.toUpperCase()"></span>
                            <span class="ml-2 text-gray-600" x-text="log.message"></span>
                        </div>
                        <div class="text-xs text-gray-500" x-text="formatTime(log.timestamp)"></div>
                    </div>
                </template>

                <div x-show="testLogs.length === 0" class="text-center py-8 text-gray-500">
                    Henüz test kaydı bulunmuyor. Bir toast testi çalıştırın.
                </div>
            </div>
        </div>

        <!-- Toast Container -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
            <template x-for="toast in activeToasts" :key="toast.id">
                <div :class="getToastClasses(toast)" class="transform transition-all duration-300 ease-in-out"
                    x-show="toast.visible" x-transition:enter="translate-x-full opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="translate-x-0 opacity-100"
                    x-transition:leave-end="translate-x-full opacity-0">

                    <div class="flex items-center">
                        <span x-html="getToastIcon(toast.type)" class="mr-3"></span>
                        <div class="flex-1">
                            <p class="font-medium" x-text="toast.message"></p>
                            <div x-show="toast.progress !== undefined"
                                class="w-full bg-white bg-opacity-20 rounded-full h-1 mt-2">
                                <div class="bg-white h-1 rounded-full transition-all duration-100"
                                    :style="`width: ${toast.progress}%`"></div>
                            </div>
                        </div>
                        <button @click="closeToast(toast.id)" class="ml-3 text-white hover:text-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toastDemoManager() {
            return {
                saving: false,
                exporting: false,
                bulkTesting: false,
                analyticsModal: false,

                // Toast configuration
                toastConfig: {
                    success: {
                        message: 'İşlem başarıyla tamamlandı!',
                        duration: 3000,
                        position: 'top-right'
                    },
                    error: {
                        message: 'Bir hata oluştu!',
                        duration: 5000,
                        position: 'top-right'
                    },
                    warning: {
                        message: 'Dikkat edilmesi gereken bir durum var!',
                        duration: 4000,
                        position: 'top-right'
                    },
                    info: {
                        message: 'Bilgilendirme mesajı',
                        duration: 3000,
                        position: 'top-right'
                    },
                    loading: {
                        message: 'İşlem devam ediyor...',
                        duration: 0,
                        position: 'top-right'
                    },
                    custom: {
                        message: 'Özel bildirim mesajı',
                        duration: 3000,
                        color: '#8B5CF6'
                    }
                },

                // Global configuration
                globalConfig: {
                    defaultDuration: 3000,
                    maxToasts: 5,
                    animationDuration: 300,
                    autoClose: 'true',
                    soundEnabled: 'false',
                    vibrationEnabled: 'false'
                },

                // Active toasts and logs
                activeToasts: [],
                testLogs: [],
                toastIdCounter: 0,

                init() {
                    console.log('Toast Demo Manager initialized');
                    this.loadConfig();
                },

                // Toast management
                showToast(type) {
                    const config = this.toastConfig[type];
                    const toastId = ++this.toastIdCounter;

                    const toast = {
                        id: toastId,
                        type: type,
                        message: config.message,
                        duration: config.duration,
                        visible: true,
                        progress: config.duration > 0 ? 100 : undefined
                    };

                    // Manage max toasts
                    if (this.activeToasts.length >= parseInt(this.globalConfig.maxToasts)) {
                        this.activeToasts.shift();
                    }

                    this.activeToasts.push(toast);

                    // Auto close if duration > 0
                    if (toast.duration > 0) {
                        this.startToastTimer(toast);
                    }

                    // Log the test
                    this.logTest(type, config.message, 'success');

                    // Sound and vibration
                    if (this.globalConfig.soundEnabled === 'true') {
                        this.playNotificationSound();
                    }

                    if (this.globalConfig.vibrationEnabled === 'true' && navigator.vibrate) {
                        navigator.vibrate(200);
                    }
                },

                startToastTimer(toast) {
                    const interval = 100;
                    const totalSteps = toast.duration / interval;
                    let currentStep = 0;

                    const timer = setInterval(() => {
                        currentStep++;
                        toast.progress = ((totalSteps - currentStep) / totalSteps) * 100;

                        if (currentStep >= totalSteps) {
                            clearInterval(timer);
                            this.closeToast(toast.id);
                        }
                    }, interval);
                },

                closeToast(toastId) {
                    const index = this.activeToasts.findIndex(t => t.id === toastId);
                    if (index !== -1) {
                        this.activeToasts[index].visible = false;
                        setTimeout(() => {
                            this.activeToasts.splice(index, 1);
                        }, 300);
                    }
                },

                // Bulk testing
                async bulkTest() {
                    this.bulkTesting = true;
                    const types = ['success', 'error', 'warning', 'info', 'loading'];

                    for (let i = 0; i < types.length; i++) {
                        await new Promise(resolve => setTimeout(resolve, 500));
                        this.showToast(types[i]);
                    }

                    this.bulkTesting = false;
                },

                // Configuration management
                saveConfig() {
                    this.saving = true;

                    // Simulate API call
                    setTimeout(() => {
                        localStorage.setItem('toastDemoConfig', JSON.stringify({
                            toastConfig: this.toastConfig,
                            globalConfig: this.globalConfig
                        }));

                        this.saving = false;
                        this.showToast('success');
                    }, 1000);
                },

                loadConfig() {
                    const savedConfig = localStorage.getItem('toastDemoConfig');
                    if (savedConfig) {
                        const config = JSON.parse(savedConfig);
                        this.toastConfig = {
                            ...this.toastConfig,
                            ...config.toastConfig
                        };
                        this.globalConfig = {
                            ...this.globalConfig,
                            ...config.globalConfig
                        };
                    }
                },

                resetConfig() {
                    // Reset to defaults
                    this.toastConfig = {
                        success: {
                            message: 'İşlem başarıyla tamamlandı!',
                            duration: 3000,
                            position: 'top-right'
                        },
                        error: {
                            message: 'Bir hata oluştu!',
                            duration: 5000,
                            position: 'top-right'
                        },
                        warning: {
                            message: 'Dikkat edilmesi gereken bir durum var!',
                            duration: 4000,
                            position: 'top-right'
                        },
                        info: {
                            message: 'Bilgilendirme mesajı',
                            duration: 3000,
                            position: 'top-right'
                        },
                        loading: {
                            message: 'İşlem devam ediyor...',
                            duration: 0,
                            position: 'top-right'
                        },
                        custom: {
                            message: 'Özel bildirim mesajı',
                            duration: 3000,
                            color: '#8B5CF6'
                        }
                    };

                    this.globalConfig = {
                        defaultDuration: 3000,
                        maxToasts: 5,
                        animationDuration: 300,
                        autoClose: 'true',
                        soundEnabled: 'false',
                        vibrationEnabled: 'false'
                    };
                },

                // Logging
                logTest(type, message, result) {
                    this.testLogs.unshift({
                        type: type,
                        message: message,
                        result: result,
                        timestamp: new Date()
                    });

                    // Keep only last 50 logs
                    if (this.testLogs.length > 50) {
                        this.testLogs = this.testLogs.slice(0, 50);
                    }
                },

                clearLogs() {
                    this.testLogs = [];
                },

                // Export functionality
                async exportLogs() {
                    this.exporting = true;

                    try {
                        const data = {
                            logs: this.testLogs,
                            config: this.globalConfig,
                            export_date: new Date().toISOString()
                        };

                        const dataStr = JSON.stringify(data, null, 2);
                        const blob = new Blob([dataStr], {
                            type: 'application/json'
                        });
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'toast_demo_logs_' + new Date().toISOString().slice(0, 10) + '.json';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        this.showToast('success');
                    } catch (error) {
                        this.showToast('error');
                    } finally {
                        this.exporting = false;
                    }
                },

                // Modal controls
                toggleAnalyticsModal() {
                    this.analyticsModal = !this.analyticsModal;
                },

                // Utility methods
                getToastClasses(toast) {
                    const baseClasses = 'max-w-sm p-4 rounded-lg shadow-lg';
                    const typeClasses = {
                        success: 'bg-green-500 text-white',
                        error: 'bg-red-500 text-white',
                        warning: 'bg-yellow-500 text-white',
                        info: 'bg-blue-500 text-white',
                        loading: 'bg-gray-500 text-white',
                        custom: 'text-white'
                    };

                    let classes = baseClasses + ' ' + typeClasses[toast.type];

                    if (toast.type === 'custom') {
                        classes = baseClasses + ' text-white';
                    }

                    return classes;
                },

                getToastIcon(type) {
                    const icons = {
                        success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                        error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                        warning: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
                        info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                        loading: '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>',
                        custom: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path></svg>'
                    };

                    return icons[type] || icons.info;
                },

                getLogIcon(type) {
                    const icons = {
                        success: '✅',
                        error: '❌',
                        warning: '⚠️',
                        info: 'ℹ️',
                        loading: '⏳',
                        custom: '🎨'
                    };

                    return icons[type] || 'ℹ️';
                },

                formatTime(timestamp) {
                    return timestamp.toLocaleTimeString('tr-TR');
                },

                playNotificationSound() {
                    // Simple beep sound using Web Audio API
                    const audioContext = new(window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    oscillator.frequency.value = 800;
                    oscillator.type = 'sine';

                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.2);
                }
            };
        }
    </script>
@endpush
