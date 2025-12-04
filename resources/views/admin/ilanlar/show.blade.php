@extends('admin.layouts.admin')

@section('title', 'İlan Detayı')

@section('content')
    <div class="space-y-6">
        {{-- ✅ Kalite Kontrol Uyarı Mesajı --}}
        @if (session('warning'))
            <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-orange-800 dark:text-orange-300 mb-1">
                            İlan Kalite Uyarısı
                        </h3>
                        <p class="text-sm text-orange-700 dark:text-orange-200">
                            {{ session('warning') }}
                        </p>
                        @if (session('qualityCheck') && !empty(session('qualityCheck.missing_fields')))
                            <details class="mt-2">
                                <summary
                                    class="text-sm font-medium text-orange-700 dark:text-orange-200 cursor-pointer hover:underline">
                                    Eksik alanları göster ({{ count(session('qualityCheck.missing_fields')) }})
                                </summary>
                                <ul class="mt-2 space-y-1 text-sm text-orange-600 dark:text-orange-300 pl-4">
                                    @foreach (session('qualityCheck.missing_fields') as $field)
                                        <li class="list-disc">{{ $field['label'] }}</li>
                                    @endforeach
                                </ul>
                            </details>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- ✅ Success Mesajı --}}
        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Quick Actions Bar - İyileştirilmiş -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800 shadow-lg p-6"
            x-data="quickActions({{ $ilan->id }})">
            <div class="flex items-center justify-between flex-wrap gap-6">
                {{-- Sol Taraf: Başlık ve Açıklama --}}
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">Hızlı İşlemler</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">İlan yönetimi için hızlı erişim araçları</p>
                    </div>
                </div>

                {{-- Sağ Taraf: İyileştirilmiş Butonlar (Yatay Düzen) --}}
                <div class="flex items-center gap-3 flex-wrap">
                    {{-- İlanı Düzenle --}}
                    <a href="{{ route('admin.ilanlar.edit', $ilan->id) }}"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-xl hover:scale-105 active:scale-95 focus:ring-4 focus:ring-blue-500/50 transition-all duration-200"
                        title="İlan bilgilerini düzenle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>İlanı Düzenle</span>
                    </a>

                    {{-- İlanı Kopyala --}}
                    <button @click="duplicateListing()" :disabled="processing"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-xl hover:scale-105 active:scale-95 focus:ring-4 focus:ring-green-500/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        title="Bu ilanın bir kopyasını oluştur (Taslak olarak kaydedilir)" aria-label="İlanı kopyala">
                        <svg x-show="!processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <svg x-show="processing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="processing ? 'Kopyalanıyor...' : 'İlanı Kopyala'"></span>
                    </button>

                    {{-- Durum Değiştir --}}
                    <button @click="toggleStatus()" :disabled="processing"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-xl hover:scale-105 active:scale-95 focus:ring-4 focus:ring-yellow-500/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        title="İlan durumunu Aktif/Pasif arasında değiştir" aria-label="İlan durumunu değiştir">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span x-text="processing ? 'Değiştiriliyor...' : 'Durum Değiştir'"></span>
                    </button>

                    {{-- AI Analiz --}}
                    <button @click="analyzeWithAI()" :disabled="processing"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-xl hover:scale-105 active:scale-95 focus:ring-4 focus:ring-purple-500/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        title="AI ile ilan analizi yap (fiyat, başlık, SEO önerileri)" aria-label="AI ile ilan analizi yap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <span x-text="processing ? 'Analiz ediliyor...' : 'AI Analiz'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ✅ Yalihan Cortex: Analiz, Video, Fotoğraf Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Analiz Raporu Kartı --}}
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-xl border border-purple-200 dark:border-purple-800 shadow-lg p-6 hover:shadow-xl transition-all duration-200"
                x-data="cortexAnaliz({{ $ilan->id }})">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Analiz Raporu</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">AI destekli detaylı analiz</p>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                    İlan için çevresel analiz, fiyat tahmini ve pazar analizi raporu oluşturun.
                </p>
                <button @click="generateReport()" :disabled="loading"
                    class="w-full px-4 py-3 bg-purple-600 dark:bg-purple-500 text-white rounded-lg font-medium
                           hover:bg-purple-700 dark:hover:bg-purple-600 hover:scale-105 active:scale-95
                           focus:ring-2 focus:ring-purple-500 transition-all duration-200 shadow-md
                           disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Rapor Oluştur
                    </span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Oluşturuluyor...
                    </span>
                </button>
            </div>

            {{-- Video Oluştur Kartı --}}
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl border border-blue-200 dark:border-blue-800 shadow-lg p-6 hover:shadow-xl transition-all duration-200"
                x-data="cortexVideo({{ $ilan->id }})">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Video Oluştur</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">AI destekli video üretimi</p>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                    İlan için profesyonel tanıtım videosu oluşturun. Harita animasyonları ve POI bilgileri dahil.
                </p>
                <button @click="generateVideo()" :disabled="loading"
                    class="w-full px-4 py-3 bg-blue-600 dark:bg-blue-500 text-white rounded-lg font-medium
                           hover:bg-blue-700 dark:hover:bg-blue-600 hover:scale-105 active:scale-95
                           focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md
                           disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Video Oluştur
                    </span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Oluşturuluyor...
                    </span>
                </button>
            </div>

            {{-- Fotoğraf & PDF Kartı --}}
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800 shadow-lg p-6 hover:shadow-xl transition-all duration-200"
                x-data="cortexFoto({{ $ilan->id }})">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Fotoğraf & PDF</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Harita görüntüleri ve PDF</p>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                    Harita görüntülerini otomatik yakalayın ve profesyonel PDF raporu oluşturun.
                </p>
                <button @click="generatePhotos()" :disabled="loading"
                    class="w-full px-4 py-3 bg-green-600 dark:bg-green-500 text-white rounded-lg font-medium
                           hover:bg-green-700 dark:hover:bg-green-600 hover:scale-105 active:scale-95
                           focus:ring-2 focus:ring-green-500 transition-all duration-200 shadow-md
                           disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Fotoğraf & PDF Oluştur
                    </span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Oluşturuluyor...
                    </span>
                </button>
            </div>
        </div>

        <!-- Header with Status Badge -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $ilan->baslik }}</h1>
                        <!-- Status Badge -->
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
            @if ($ilan->status === 'Aktif') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
            @elseif($ilan->status === 'Pasif')
              bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
            @elseif($ilan->status === 'Taslak')
              bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
            @elseif($ilan->status === 'Beklemede')
              bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
            @else
              bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300 @endif">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $ilan->status }}
                        </span>
                    </div>
                    {{-- ✨ REFERANS BADGE (Gemini AI Önerisi - 3 Katmanlı Sistem) --}}
                    <div class="mt-2">
                        @include('admin.ilanlar.partials.referans-badge', ['ilan' => $ilan])
                    </div>
                </div>
            </div>

            <!-- İstatistikler -->
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($ilan->goruntulenme ?? 0) }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Görüntülenme</div>
                        </div>
                        <svg class="w-8 h-8 text-blue-400 dark:text-blue-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                {{ number_format($ilan->favorite_count ?? 0) }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Favori</div>
                        </div>
                        <svg class="w-8 h-8 text-red-400 dark:text-red-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($ilan->messages_count ?? 0) }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Mesaj</div>
                        </div>
                        <svg class="w-8 h-8 text-green-400 dark:text-green-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ ($ilan->sahibinden_id ? 1 : 0) + ($ilan->emlakjet_id ? 1 : 0) + ($ilan->hepsiemlak_id ? 1 : 0) + ($ilan->zingat_id ? 1 : 0) + ($ilan->hurriyetemlak_id ? 1 : 0) }}
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Portal Sync</div>
                        </div>
                        <svg class="w-8 h-8 text-purple-400 dark:text-purple-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Kategori</div>
                    <div class="text-base text-gray-900 dark:text-gray-100">{{ optional($ilan->kategori)->name ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Fiyat</div>
                    <div class="text-base text-gray-900 dark:text-gray-100">{{ number_format($ilan->fiyat) }}
                        {{ $ilan->para_birimi }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Lokasyon</div>
                    <div class="text-base text-gray-900 dark:text-gray-100">
                        {{ optional($ilan->il)->il_adi }}{{ optional($ilan->ilce)->ilce_adi ? ', ' . $ilan->ilce->ilce_adi : '' }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Yayın Tipi</div>
                    <div class="text-base text-gray-900 dark:text-gray-100">{{ optional($ilan->yayinTipi)->name ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ tab: 'genel' }"
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-6 pt-6">
                <div class="flex flex-wrap gap-2">
                    <button @click="tab='genel'"
                        :class="tab === 'genel' ? 'bg-blue-600 text-white' :
                            'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'"
                        class="px-4 py-2 rounded-lg">Genel</button>
                    <button @click="tab='kisiler'"
                        :class="tab === 'kisiler' ? 'bg-blue-600 text-white' :
                            'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'"
                        class="px-4 py-2 rounded-lg">Kişiler</button>
                    <button @click="tab='site'"
                        :class="tab === 'site' ? 'bg-blue-600 text-white' :
                            'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'"
                        class="px-4 py-2 rounded-lg">Site/Apartman</button>
                    <button @click="tab='fotograflar'"
                        :class="tab === 'fotograflar' ? 'bg-blue-600 text-white' :
                            'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'"
                        class="px-4 py-2 rounded-lg">Fotoğraflar</button>
                    <button @click="tab='belgeler'"
                        :class="tab === 'belgeler' ? 'bg-blue-600 text-white' :
                            'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'"
                        class="px-4 py-2 rounded-lg">Belgeler</button>
                    <button @click="tab='arka'"
                        :class="tab === 'arka' ? 'bg-blue-600 text-white' :
                            'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'"
                        class="px-4 py-2 rounded-lg">Arka Plan</button>
                    <button @click="tab='gecmis'"
                        :class="tab === 'gecmis' ? 'bg-blue-600 text-white' :
                            'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'"
                        class="px-4 py-2 rounded-lg">Geçmiş</button>
                    @php
                        $isArsa =
                            optional($ilan->altKategori)->slug === 'arsa' ||
                            optional($ilan->altKategori)->name === 'Arsa' ||
                            (optional($ilan->altKategori)->name && stripos($ilan->altKategori->name, 'arsa') !== false);
                    @endphp
                    @if ($isArsa)
                        <button @click="tab='video'"
                            :class="tab === 'video' ? 'bg-blue-600 text-white' :
                                'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'"
                            class="px-4 py-2 rounded-lg transition-all duration-200">Video</button>
                    @endif
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                <div x-show="tab==='genel'">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Portal ID’ler</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div><span class="text-gray-500 dark:text-gray-400">Sahibinden</span>
                            <div class="font-mono">{{ $ilan->sahibinden_id ?? '-' }}</div>
                        </div>
                        <div><span class="text-gray-500 dark:text-gray-400">Emlakjet</span>
                            <div class="font-mono">{{ $ilan->emlakjet_id ?? '-' }}</div>
                        </div>
                        <div><span class="text-gray-500 dark:text-gray-400">Hepsiemlak</span>
                            <div class="font-mono">{{ $ilan->hepsiemlak_id ?? '-' }}</div>
                        </div>
                        <div><span class="text-gray-500 dark:text-gray-400">Zingat</span>
                            <div class="font-mono">{{ $ilan->zingat_id ?? '-' }}</div>
                        </div>
                        <div><span class="text-gray-500 dark:text-gray-400">Hürriyet Emlak</span>
                            <div class="font-mono">{{ $ilan->hurriyetemlak_id ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                <div x-show="tab==='video'">
                    @include('admin.ilanlar.components.video-tab', ['ilan' => $ilan])
                </div>
                <div x-show="tab==='kisiler'">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">İlan Sahibi</div>
                            <div class="text-base text-gray-900 dark:text-gray-100">{{ optional($ilan->ilanSahibi)->ad }}
                                {{ optional($ilan->ilanSahibi)->soyad }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ optional($ilan->ilanSahibi)->telefon }}{{ optional($ilan->ilanSahibi)->email ? ' • ' . $ilan->ilanSahibi->email : '' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Danışman</div>
                            <div class="text-base text-gray-900 dark:text-gray-100">
                                {{ optional($ilan->userDanisman)->name }}{{ optional($ilan->userDanisman)->email ? ' • ' . $ilan->userDanisman->email : '' }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ optional($ilan->userDanisman)->phone_number }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">İlgili Kişi</div>
                            <div class="text-base text-gray-900 dark:text-gray-100">{{ optional($ilan->ilgiliKisi)->ad }}
                                {{ optional($ilan->ilgiliKisi)->soyad }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ optional($ilan->ilgiliKisi)->telefon }}{{ optional($ilan->ilgiliKisi)->email ? ' • ' . $ilan->ilgiliKisi->email : '' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div x-show="tab==='site'">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Site/Apartman</div>
                            <div class="text-base text-gray-900 dark:text-gray-100">
                                {{ optional($ilan->site)->name ?? '-' }}</div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Adres</div>
                            <div class="text-base text-gray-900 dark:text-gray-100">
                                {{ optional($ilan->site)->full_address ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                <div x-show="tab==='fotograflar'">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($ilan->fotograflar ?? [] as $photo)
                            <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                <img src="{{ Storage::url($photo->dosya_yolu) }}" alt="Fotoğraf"
                                    class="w-full h-32 object-cover">
                            </div>
                        @endforeach
                        @if (($ilan->fotograflar ?? collect())->count() === 0)
                            <div class="text-sm text-gray-500 dark:text-gray-400">Fotoğraf bulunamadı</div>
                        @endif
                    </div>
                </div>
                <div x-show="tab==='belgeler'">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Dosya Adı</div>
                            <div class="text-base text-gray-900 dark:text-gray-100">{{ $ilan->dosya_adi ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">YouTube</div>
                            @if ($ilan->youtube_video_url)
                                <a href="{{ $ilan->youtube_video_url }}" target="_blank" rel="noopener"
                                    class="text-blue-600 underline">Videoyu aç</a>
                            @else
                                <div class="text-sm text-gray-500 dark:text-gray-400">-</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Sanal Tur</div>
                            @if ($ilan->sanal_tur_url)
                                <a href="{{ $ilan->sanal_tur_url }}" target="_blank" rel="noopener"
                                    class="text-blue-600 underline">Turu aç</a>
                            @else
                                <div class="text-sm text-gray-500 dark:text-gray-400">-</div>
                            @endif
                        </div>
                    </div>
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dokümanlar</h3>
                        <div class="mb-2">
                            <a href="/api/admin/ilanlar/{{ $ilan->id }}/documents.csv"
                                class="px-3 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm">CSV
                                İndir</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                            Başlık</th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                            Tür</th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                            Bağlantı</th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                            Tarih</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($ilan->documents ?? [] as $doc)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $doc->title }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">
                                                {{ $doc->type ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                @if ($doc->url)
                                                    <a href="{{ $doc->url }}" target="_blank" rel="noopener"
                                                        class="text-blue-600 underline">Aç</a>
                                                @elseif($doc->path)
                                                    <a href="{{ Storage::url($doc->path) }}" target="_blank"
                                                        rel="noopener" class="text-blue-600 underline">İndir</a>
                                                @else
                                                    <span class="text-gray-500 dark:text-gray-400">-</span>
                                                @endif
                                                <button type="button" onclick="deleteDoc({{ $doc->id }})"
                                                    class="ml-3 px-2 py-1 rounded bg-red-600 text-white text-xs">Sil</button>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">
                                                {{ $doc->created_at ? $doc->created_at->format('d.m.Y H:i') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                    @if (($ilan->documents ?? collect())->count() === 0)
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-sm text-gray-500 dark:text-gray-400">
                                                Doküman bulunamadı</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Yeni Doküman Ekle</h3>
                        <form id="doc-upload" enctype="multipart/form-data"
                            class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <input type="hidden" id="doc-ilan-id" value="{{ $ilan->id }}" />
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400">Başlık</label>
                                <input id="doc-title" type="text"
                                    class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400">Tür</label>
                                <input id="doc-type" type="text"
                                    class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400">URL</label>
                                <input id="doc-url" type="url"
                                    class="w-full rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400">Dosya</label>
                                <input id="doc-file" type="file" class="w-full" />
                            </div>
                            <div class="md:col-span-4">
                                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Kaydet</button>
                                <span id="doc-status" class="ml-2 text-sm text-gray-600"></span>
                            </div>
                        </form>
                        <script>
                            (function() {
                                var form = document.getElementById('doc-upload');
                                if (!form) return;
                                form.addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    var ilanId = document.getElementById('doc-ilan-id').value;
                                    var fd = new FormData();
                                    fd.append('title', document.getElementById('doc-title').value || '');
                                    fd.append('type', document.getElementById('doc-type').value || '');
                                    var url = document.getElementById('doc-url').value || '';
                                    if (url) fd.append('url', url);
                                    var file = document.getElementById('doc-file').files[0];
                                    if (file) fd.append('file', file);
                                    var status = document.getElementById('doc-status');
                                    status.textContent = 'Yükleniyor...';
                                    fetch('/api/admin/ilanlar/' + encodeURIComponent(ilanId) + '/documents', {
                                        method: 'POST',
                                        body: fd
                                    }).then(function(r) {
                                        if (!r.ok) throw new Error('' + r.status);
                                        return r.json();
                                    }).then(function() {
                                        status.textContent = 'Kaydedildi';
                                        location.reload();
                                    }).catch(function(err) {
                                        status.textContent = 'Hata: ' + (err && err.message ? err.message : 'Yüklenemedi');
                                    });
                                });
                            })();

                            function deleteDoc(id) {
                                if (!confirm('Dokümanı silmek istediğinize emin misiniz?')) return;
                                fetch('/api/admin/documents/' + encodeURIComponent(id), {
                                        method: 'DELETE'
                                    })
                                    .then(function(r) {
                                        if (!r.ok) throw new Error('' + r.status);
                                        return r.json();
                                    })
                                    .then(function() {
                                        location.reload();
                                    })
                                    .catch(function(err) {
                                        alert('Silme hatası: ' + (err && err.message ? err.message : 'İstek başarısız'));
                                    });
                            }
                        </script>
                    </div>
                </div>
                <div x-show="tab==='arka'">
                    @can('view-private-listing-data', $ilan)
                        @php($priv = $ilan->owner_private_data)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">İstenen Fiyat Min</div>
                                <div class="text-base text-gray-900 dark:text-gray-100">
                                    {{ isset($priv['desired_price_min']) ? number_format($priv['desired_price_min']) : '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">İstenen Fiyat Max</div>
                                <div class="text-base text-gray-900 dark:text-gray-100">
                                    {{ isset($priv['desired_price_max']) ? number_format($priv['desired_price_max']) : '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Özel Notlar</div>
                                <div class="text-base text-gray-900 dark:text-gray-100">{{ $priv['notes'] ?? '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-gray-500 dark:text-gray-400">Arka plan bilgileri için yetki gerekli</div>
                    @endcan
                </div>
                <div x-show="tab==='gecmis'">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                        Tarih</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                        Fiyat</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                        Not</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($ilan->fiyatGecmisi ?? [] as $fh)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">
                                            {{ $fh->created_at ? $fh->created_at->format('d.m.Y H:i') : '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">
                                            {{ number_format($fh->fiyat ?? 0) }} {{ $ilan->para_birimi }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $fh->notlar ?? '-' }}</td>
                                    </tr>
                                @endforeach
                                @if (($ilan->fiyatGecmisi ?? collect())->count() === 0)
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-sm text-gray-500 dark:text-gray-400">
                                            Kayıt yok</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @php($points = ($ilan->fiyatGecmisi ?? collect())->reverse()->take(20))
                    @php($max = max($points->pluck('fiyat')->toArray() ?: [0]))
                    @php($min = min($points->pluck('fiyat')->toArray() ?: [0]))
                    @php($range = max(1, $max - $min))
                    <div class="mt-4">
                        <svg width="400" height="80" viewBox="0 0 400 80" preserveAspectRatio="none">
                            @php($idx = 0)
                            @php($step = max(1, intval(400 / max(1, $points->count() - 1))))
                            @php($path = '')
                            @foreach ($points as $fh)
                                @php($x = $idx * $step)
                                @php($y = 70 - intval(((($fh->fiyat ?? 0) - $min) / $range) * 60))
                                @php($path .= ($idx === 0 ? 'M' : ' L') . $x . ' ' . $y)
                                @php($idx++)
                            @endforeach
                            <path d="{{ $path }}" fill="none" stroke="#2563eb" stroke-width="2" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <div>
                @if ($previousIlan)
                    <a href="{{ route('admin.ilanlar.show', $previousIlan->id) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-800 dark:text-gray-200">Önceki</a>
                @endif
            </div>
            <div>
                @if ($nextIlan)
                    <a href="{{ route('admin.ilanlar.show', $nextIlan->id) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-800 dark:text-gray-200">Sonraki</a>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ✅ Yalihan Cortex: Analiz Raporu
            function cortexAnaliz(ilanId) {
                return {
                    loading: false,
                    async generateReport() {
                        this.loading = true;
                        try {
                            const url = window.APIConfig?.cortex?.analyze(ilanId) || `/api/admin/cortex/analyze/${ilanId}`;
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json',
                                },
                            });

                            const data = await response.json();
                            if (data.success) {
                                window.toast?.success('Analiz raporu oluşturuluyor...');
                                if (data.download_url) {
                                    window.open(data.download_url, '_blank');
                                }
                            } else {
                                throw new Error(data.message || 'Rapor oluşturulamadı');
                            }
                        } catch (error) {
                            console.error('Analiz hatası:', error);
                            window.toast?.error('Rapor oluşturulamadı: ' + error.message);
                        } finally {
                            this.loading = false;
                        }
                    },
                };
            }

            // ✅ Yalihan Cortex: Video Oluştur
            function cortexVideo(ilanId) {
                return {
                    loading: false,
                    async generateVideo() {
                        this.loading = true;
                        try {
                            const url = window.APIConfig?.cortex?.video(ilanId) || `/api/admin/cortex/video/${ilanId}`;
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json',
                                },
                            });

                            const data = await response.json();
                            if (data.success) {
                                window.toast?.success('Video oluşturuluyor...');
                                if (data.video_url) {
                                    window.open(data.video_url, '_blank');
                                }
                            } else {
                                throw new Error(data.message || 'Video oluşturulamadı');
                            }
                        } catch (error) {
                            console.error('Video hatası:', error);
                            window.toast?.error('Video oluşturulamadı: ' + error.message);
                        } finally {
                            this.loading = false;
                        }
                    },
                };
            }

            // ✅ Yalihan Cortex: Fotoğraf & PDF
            function cortexFoto(ilanId) {
                return {
                    loading: false,
                    async generatePhotos() {
                        this.loading = true;
                        try {
                            const url = window.APIConfig?.cortex?.photos(ilanId) || `/api/admin/cortex/photos/${ilanId}`;
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json',
                                },
                            });

                            const data = await response.json();
                            if (data.success) {
                                window.toast?.success('Fotoğraflar ve PDF oluşturuluyor...');
                                if (data.download_url) {
                                    window.open(data.download_url, '_blank');
                                }
                            } else {
                                throw new Error(data.message || 'Fotoğraf/PDF oluşturulamadı');
                            }
                        } catch (error) {
                            console.error('Fotoğraf hatası:', error);
                            window.toast?.error('Fotoğraf/PDF oluşturulamadı: ' + error.message);
                        } finally {
                            this.loading = false;
                        }
                    },
                };
            }

            function quickActions(ilanId) {
                return {
                    processing: false,
                    ilanId: ilanId,

                    async duplicateListing() {
                        if (!confirm('Bu ilanı kopyalamak istediğinize emin misiniz?')) {
                            return;
                        }

                        this.processing = true;
                        try {
                            const response = await fetch(`/admin/ilanlar/${this.ilanId}/duplicate`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json'
                                }
                            });

                            const data = await response.json();
                            if (data.success) {
                                if (window.MCP && window.MCP.toast && typeof window.MCP.toast.success === 'function') {
                                    window.MCP.toast.success('İlan kopyalandı');
                                } else if (window.toast && typeof window.toast.success === 'function') {
                                    window.toast.success('İlan kopyalandı');
                                }
                                if (data.redirect_url) {
                                    window.location.href = data.redirect_url;
                                } else {
                                    location.reload();
                                }
                            } else {
                                throw new Error(data.message || 'Kopyalama başarısız');
                            }
                        } catch (error) {
                            console.error('Duplicate error:', error);
                            if (window.MCP && window.MCP.toast && typeof window.MCP.toast.error === 'function') {
                                window.MCP.toast.error('Kopyalama başarısız: ' + error.message);
                            } else if (window.toast && typeof window.toast.error === 'function') {
                                window.toast.error('Kopyalama başarısız: ' + error.message);
                            } else {
                                alert('Kopyalama başarısız: ' + error.message);
                            }
                        } finally {
                            this.processing = false;
                        }
                    },

                    async toggleStatus() {
                        this.processing = true;
                        try {
                            const response = await fetch(`/admin/ilanlar/${this.ilanId}/toggle-status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json'
                                }
                            });

                            const data = await response.json();
                            if (data.success) {
                                if (window.toast) {
                                    window.toast.success('Durum güncellendi: ' + data.new_status);
                                }
                                location.reload();
                            } else {
                                throw new Error(data.message || 'Durum güncelleme başarısız');
                            }
                        } catch (error) {
                            console.error('Toggle status error:', error);
                            if (window.toast) {
                                window.toast.error('Durum güncelleme başarısız: ' + error.message);
                            } else {
                                alert('Durum güncelleme başarısız: ' + error.message);
                            }
                        } finally {
                            this.processing = false;
                        }
                    },

                    async analyzeWithAI() {
                        this.processing = true;
                        try {
                            const response = await fetch(`/admin/ilanlar/ai/bulk-analyze`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    ilan_ids: [this.ilanId],
                                    type: 'comprehensive'
                                })
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    ilan_ids: [this.ilanId],
                                    type: 'comprehensive'
                                })
                            });

                            const data = await response.json();
                            if (data.success) {
                                if (window.toast) {
                                    window.toast.success('AI analiz tamamlandı');
                                }
                                console.log('AI Analysis Results:', data.results);
                            } else {
                                throw new Error(data.message || 'AI analiz başarısız');
                            }
                        } catch (error) {
                            console.error('AI analysis error:', error);
                            if (window.toast) {
                                window.toast.error('AI analiz başarısız: ' + error.message);
                            } else {
                                alert('AI analiz başarısız: ' + error.message);
                            }
                        } finally {
                            this.processing = false;
                        }
                    }
                };
            }
        </script>
    @endpush
@endsection
