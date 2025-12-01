@extends('admin.layouts.admin')

@section('title', 'Ayarlar')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Sistem Ayarları
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Sistem genelinde tüm ayarları yönetin ve yapılandırın
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="resetToDefaults()"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow-md active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Varsayılanlara Dön
                    </button>
                    {{-- AI Ayarları linki kaldırıldı - Sidebar menüde zaten var (2025-11-30) --}}
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Settings Form --}}
        <form method="POST" action="{{ route('admin.ayarlar.bulk-update') }}" id="settingsForm" class="space-y-6">
            @csrf
            @method('POST')

            {{-- Modern Tab Navigation --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex flex-wrap -mb-px px-6" aria-label="Tabs">
                        <button type="button" data-tab="genel"
                            class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 flex items-center gap-2 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Genel
                        </button>
                        <button type="button" data-tab="bildirim"
                            class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Bildirimler
                        </button>
                        <button type="button" data-tab="portal"
                            class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            Portal Entegrasyonları
                        </button>
                        <button type="button" data-tab="fiyat"
                            class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Fiyatlandırma
                        </button>
                        <button type="button" data-tab="qrcode"
                            class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            QR Kod
                        </button>
                        <button type="button" data-tab="navigation"
                            class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            Navigasyon
                        </button>
                        <button type="button" data-tab="kullanici"
                            class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Kullanıcı Yönetimi
                        </button>
                    </nav>
                </div>

                {{-- Tab Content Container --}}
                <div class="p-6">
                    {{-- Genel Ayarlar Tab --}}
                    <div id="genel" class="tab-content">
                        <div class="space-y-6">
                            <div>
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Genel Ayarlar
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Sitenin temel ayarlarını buradan
                                    yönetebilirsiniz.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="site_title"
                                        class="block text-sm font-medium text-gray-900 dark:text-white">
                                        Site Başlığı <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="site_title" name="site_title"
                                        value="{{ $settings['site_title'] ?? '' }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm hover:shadow-md"
                                        placeholder="Yalıhan Emlak">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sitenin ana başlığı</p>
                                </div>

                                <div class="space-y-2">
                                    <label for="default_currency"
                                        class="block text-sm font-medium text-gray-900 dark:text-white">
                                        Varsayılan Para Birimi <span class="text-red-500">*</span>
                                    </label>
                                    <select style="color-scheme: light dark;" id="default_currency"
                                        name="default_currency"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm hover:shadow-md">
                                        <option value="TRY"
                                            {{ ($settings['default_currency'] ?? 'TRY') == 'TRY' ? 'selected' : '' }}>₺ Türk
                                            Lirası (TRY)</option>
                                        <option value="USD"
                                            {{ ($settings['default_currency'] ?? '') == 'USD' ? 'selected' : '' }}>$ Dolar
                                            (USD)</option>
                                        <option value="EUR"
                                            {{ ($settings['default_currency'] ?? '') == 'EUR' ? 'selected' : '' }}>€ Euro
                                            (EUR)</option>
                                    </select>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sistem genelinde kullanılacak
                                        para birimi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bildirim Ayarları Tab --}}
                    <div id="bildirim" class="tab-content hidden">
                        <div class="space-y-6">
                            <div>
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    Bildirim Ayarları
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Sistem bildirimlerini nasıl almak
                                    istediğinizi seçin.</p>
                            </div>

                            <div class="space-y-6">
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                                    <div class="flex-1">
                                        <label for="email_notifications"
                                            class="text-sm font-medium text-gray-900 dark:text-white block mb-1">
                                            E-posta Bildirimleri
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Önemli sistem olayları için
                                            e-posta bildirimleri gönder</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="email_notifications" name="email_notifications"
                                            value="1" {{ $settings['email_notifications'] ?? false ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>

                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                                    <div class="flex-1">
                                        <label for="sms_notifications"
                                            class="text-sm font-medium text-gray-900 dark:text-white block mb-1">
                                            SMS Bildirimleri
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Acil durumlar için SMS
                                            bildirimleri gönder</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="sms_notifications" name="sms_notifications"
                                            value="1" {{ $settings['sms_notifications'] ?? false ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Portal Entegrasyonları Tab --}}
                    <div id="portal" class="tab-content hidden">
                        <div class="space-y-6">
                            <div>
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    Portal Entegrasyonları
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Harici emlak portalları ile
                                    entegrasyon ayarları.</p>
                            </div>

                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label for="sahibinden_api_key"
                                        class="block text-sm font-medium text-gray-900 dark:text-white">
                                        Sahibinden.com API Anahtarı
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="sahibinden_api_key" name="sahibinden_api_key"
                                            value="{{ $settings['sahibinden_api_key'] ?? '' }}"
                                            class="w-full px-4 py-2.5 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm hover:shadow-md"
                                            placeholder="API anahtarınızı girin">
                                        <button type="button" onclick="togglePasswordVisibility('sahibinden_api_key')"
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sahibinden.com API anahtarınız
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <label for="hepsiemlak_api_key"
                                        class="block text-sm font-medium text-gray-900 dark:text-white">
                                        Hepsiemlak API Anahtarı
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="hepsiemlak_api_key" name="hepsiemlak_api_key"
                                            value="{{ $settings['hepsiemlak_api_key'] ?? '' }}"
                                            class="w-full px-4 py-2.5 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm hover:shadow-md"
                                            placeholder="API anahtarınızı girin">
                                        <button type="button" onclick="togglePasswordVisibility('hepsiemlak_api_key')"
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hepsiemlak API anahtarınız</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Fiyatlandırma Ayarları Tab --}}
                    <div id="fiyat" class="tab-content hidden">
                        <div class="space-y-6">
                            <div>
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Fiyatlandırma Ayarları
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Fiyat gösterim ve hesaplama
                                    ayarları.</p>
                            </div>

                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label for="price_rounding"
                                        class="block text-sm font-medium text-gray-900 dark:text-white">
                                        Fiyat Yuvarlama
                                    </label>
                                    <select style="color-scheme: light dark;" id="price_rounding" name="price_rounding"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm hover:shadow-md">
                                        <option value="none"
                                            {{ ($settings['price_rounding'] ?? 'none') == 'none' ? 'selected' : '' }}>
                                            Yuvarlama Yok</option>
                                        <option value="nearest_1000"
                                            {{ ($settings['price_rounding'] ?? '') == 'nearest_1000' ? 'selected' : '' }}>
                                            En Yakın 1.000</option>
                                        <option value="nearest_10000"
                                            {{ ($settings['price_rounding'] ?? '') == 'nearest_10000' ? 'selected' : '' }}>
                                            En Yakın 10.000</option>
                                    </select>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Fiyatların nasıl
                                        yuvarlanacağını seçin</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- QR Code Settings Tab --}}
                    <div id="qrcode" class="tab-content hidden">
                        <div class="space-y-6">
                            <div
                                class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">QR Kod
                                            Özellikleri</h3>
                                        <p class="text-sm text-blue-700 dark:text-blue-300">İlanlar için QR kod oluşturma
                                            ve yönetim ayarları. QR kodlar mobil cihazlarla hızlı erişim sağlar.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                                    <div class="flex-1">
                                        <label for="qrcode_enabled"
                                            class="text-sm font-medium text-gray-900 dark:text-white block mb-1">
                                            QR Kod Özelliğini Aktif Et
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">QR kod özelliğini tüm sistemde
                                            aktif/pasif yapar</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="qrcode_enabled" name="qrcode_enabled" value="1"
                                            {{ ($settings['qrcode_enabled'] ?? 'true') == 'true' || ($settings['qrcode_enabled'] ?? true) === true ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label for="qrcode_default_size"
                                            class="block text-sm font-medium text-gray-900 dark:text-white">
                                            Varsayılan QR Kod Boyutu (Piksel)
                                        </label>
                                        <input type="number" id="qrcode_default_size" name="qrcode_default_size"
                                            min="100" max="1000" step="50"
                                            value="{{ $settings['qrcode_default_size'] ?? '300' }}"
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm hover:shadow-md">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Önerilen: 200 (küçük), 300
                                            (orta), 400 (büyük)</p>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                                    <div class="flex-1">
                                        <label for="qrcode_show_on_cards"
                                            class="text-sm font-medium text-gray-900 dark:text-white block mb-1">
                                            İlan Kartlarında QR Kod Göster
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">İlan listesi kartlarında QR kod
                                            butonu gösterilir</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="qrcode_show_on_cards" name="qrcode_show_on_cards"
                                            value="1"
                                            {{ ($settings['qrcode_show_on_cards'] ?? 'true') == 'true' || ($settings['qrcode_show_on_cards'] ?? true) === true ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>

                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                                    <div class="flex-1">
                                        <label for="qrcode_show_on_detail"
                                            class="text-sm font-medium text-gray-900 dark:text-white block mb-1">
                                            İlan Detay Sayfasında QR Kod Göster
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">İlan detay sayfalarında QR kod
                                            widget'ı gösterilir</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="qrcode_show_on_detail" name="qrcode_show_on_detail"
                                            value="1"
                                            {{ ($settings['qrcode_show_on_detail'] ?? 'true') == 'true' || ($settings['qrcode_show_on_detail'] ?? true) === true ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation Settings Tab --}}
                    <div id="navigation" class="tab-content hidden">
                        <div class="space-y-6">
                            <div
                                class="bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-semibold text-green-900 dark:text-green-100 mb-1">İlan
                                            Navigasyon Özellikleri</h3>
                                        <p class="text-sm text-green-700 dark:text-green-300">İlanlar arasında gezinme,
                                            önceki/sonraki ilan ve benzer ilanlar özellikleri.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                                    <div class="flex-1">
                                        <label for="navigation_enabled"
                                            class="text-sm font-medium text-gray-900 dark:text-white block mb-1">
                                            Navigasyon Özelliğini Aktif Et
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">İlan navigasyon özelliğini tüm
                                            sistemde aktif/pasif yapar</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="navigation_enabled" name="navigation_enabled"
                                            value="1"
                                            {{ ($settings['navigation_enabled'] ?? 'true') == 'true' || ($settings['navigation_enabled'] ?? true) === true ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label for="navigation_default_mode"
                                            class="block text-sm font-medium text-gray-900 dark:text-white">
                                            Varsayılan Navigasyon Modu
                                        </label>
                                        <select style="color-scheme: light dark;" id="navigation_default_mode"
                                            name="navigation_default_mode"
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm hover:shadow-md">
                                            <option value="default"
                                                {{ ($settings['navigation_default_mode'] ?? 'default') == 'default' ? 'selected' : '' }}>
                                                Varsayılan (Tüm ilanlar)</option>
                                            <option value="category"
                                                {{ ($settings['navigation_default_mode'] ?? '') == 'category' ? 'selected' : '' }}>
                                                Kategori Bazlı</option>
                                            <option value="location"
                                                {{ ($settings['navigation_default_mode'] ?? '') == 'location' ? 'selected' : '' }}>
                                                Konum Bazlı</option>
                                        </select>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Önceki/sonraki ilan
                                            gösterim yöntemi</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="navigation_similar_limit"
                                            class="block text-sm font-medium text-gray-900 dark:text-white">
                                            Benzer İlanlar Gösterim Sayısı
                                        </label>
                                        <input type="number" id="navigation_similar_limit"
                                            name="navigation_similar_limit" min="1" max="12" step="1"
                                            value="{{ $settings['navigation_similar_limit'] ?? '4' }}"
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm hover:shadow-md">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Benzer ilanlar bölümünde
                                            gösterilecek ilan sayısı (1-12 arası)</p>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                                    <div class="flex-1">
                                        <label for="navigation_show_similar"
                                            class="text-sm font-medium text-gray-900 dark:text-white block mb-1">
                                            Benzer İlanlar Bölümünü Göster
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">İlan detay sayfalarında benzer
                                            ilanlar bölümü gösterilir</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="navigation_show_similar"
                                            name="navigation_show_similar" value="1"
                                            {{ ($settings['navigation_show_similar'] ?? 'true') == 'true' || ($settings['navigation_show_similar'] ?? true) === true ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kullanıcı Yönetimi Tab --}}
                    <div id="kullanici" class="tab-content hidden">
                        <div class="space-y-6">
                            <div>
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Kullanıcı Yönetimi
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Kullanıcı kayıt ve güvenlik
                                    ayarları.</p>
                            </div>

                            <div class="space-y-6">
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                                    <div class="flex-1">
                                        <label for="user_registration"
                                            class="text-sm font-medium text-gray-900 dark:text-white block mb-1">
                                            Yeni Kullanıcı Kaydı
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Yeni kullanıcıların kendi
                                            başlarına kayıt olmasına izin ver</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="user_registration" name="user_registration"
                                            value="1" {{ $settings['user_registration'] ?? false ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>

                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                                    <div class="flex-1">
                                        <label for="password_strength"
                                            class="text-sm font-medium text-gray-900 dark:text-white block mb-1">
                                            Şifre Güçlülük Zorunluluğu
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Kullanıcıların güçlü şifre
                                            kullanmasını zorunlu kıl</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="password_strength" name="password_strength"
                                            value="1" {{ $settings['password_strength'] ?? false ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Değişiklikleri kaydetmek için aşağıdaki butona tıklayın
                        </p>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Tüm Değişiklikleri Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Modern Tab Navigation
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active class from all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-blue-500', 'text-blue-600',
                            'dark:text-blue-400');
                        btn.classList.add('border-transparent', 'text-gray-500',
                            'dark:text-gray-400');
                    });

                    // Add active class to clicked button
                    this.classList.add('active', 'border-blue-500', 'text-blue-600',
                        'dark:text-blue-400');
                    this.classList.remove('border-transparent', 'text-gray-500',
                        'dark:text-gray-400');

                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });

                    // Show target tab content
                    const targetContent = document.getElementById(targetTab);
                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                    }
                });
            });
        });

        // Toggle Password Visibility
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const svg = button.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                input.type = 'password';
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        // Reset to Defaults
        function resetToDefaults() {
            if (confirm(
                    'Tüm ayarları varsayılan değerlere döndürmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
                // TODO: Implement reset functionality
                alert('Bu özellik yakında eklenecek.');
            }
        }

        // Form Validation
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            const siteTitle = document.getElementById('site_title').value.trim();
            if (!siteTitle) {
                e.preventDefault();
                alert('Site başlığı zorunludur.');
                document.getElementById('site_title').focus();
                return false;
            }
        });
    </script>
@endsection
