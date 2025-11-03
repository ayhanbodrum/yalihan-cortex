@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('admin.layouts.neo')

@section('title', $danisman->name . ' - Danışman Detayı')

@section('content')
    <div class="content-header mb-8">
        <div class="container-fluid">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                <div class="space-y-2">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
                        <div
                            class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-3 lg:mr-4">
                            @php
                                $pp = $danisman->profile_photo_path;
                                $ppUrl = ($pp && Storage::exists($pp)) ? Storage::url($pp) : null;
                            @endphp
                            @if ($ppUrl)
                                <img class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl object-cover"
                                    src="{{ $ppUrl }}" alt="{{ $danisman->name }}"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl bg-gray-200 flex items-center justify-center" style="display: none;">
                                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @else
                                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            @endif
                        </div>
                        <span class="hidden sm:inline">👤</span> {{ $danisman->name }}
                    </h1>
                    <p class="text-base lg:text-lg text-gray-600 dark:text-gray-400">
                        {{ $danisman->title ?? 'Danışman' }} - Danışman Detayları ve Performans Analizi
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                    <x-neo.button variant="primary" size="sm" class="w-full sm:w-auto"
                        href="{{ route('admin.danisman.edit', $danisman) }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span class="hidden sm:inline">Düzenle</span>
                        <span class="sm:hidden">Düzenle</span>
                    </x-neo.button>
                    <x-neo.button variant="secondary" size="sm" class="w-full sm:w-auto"
                        href="{{ route('admin.danisman.index') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span class="hidden sm:inline">Geri Dön</span>
                        <span class="sm:hidden">Geri</span>
                    </x-neo.button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🤖 Context7 AI Destekli Performans İstatistikleri -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6 mb-8">
        <!-- İlan Metrikleri -->
        <x-neo.card variant="primary" class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-medium text-blue-800">📊 Toplam İlan</h4>
                    <p class="text-2xl font-bold text-blue-900">{{ $performans['toplam_ilan'] }}</p>
                </div>
            </div>
        </x-neo.card>

        <x-neo.card variant="success" class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-medium text-green-800">✅ Aktif İlan</h4>
                    <p class="text-2xl font-bold text-green-900">{{ $performans['status_ilan'] }}</p>
                </div>
            </div>
        </x-neo.card>

        <!-- Talep Metrikleri -->
        <x-neo.card variant="purple" class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-purple-500 to-violet-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-medium text-purple-800">🎯 Toplam Talep</h4>
                    <p class="text-2xl font-bold text-purple-900">{{ $performans['toplam_talep'] }}</p>
                </div>
            </div>
        </x-neo.card>

        <!-- AI Başarı Analizi -->
        <x-neo.card variant="warning" class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-orange-500 to-amber-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-medium text-orange-800">🤖 AI Başarı Oranı</h4>
                    <p class="text-2xl font-bold text-orange-900">{{ number_format($performans['basari_orani'], 1) }}%</p>
                </div>
            </div>
        </x-neo.card>

        <!-- AI Performans Puanı -->
        <x-neo.card variant="danger" class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-medium text-red-800">⭐ Performans Puanı</h4>
                    <p class="text-2xl font-bold text-red-900">{{ $performans['performans_puani'] }}</p>
                </div>
            </div>
        </x-neo.card>
    </div>

    <!-- 🤖 AI Değerlendirmesi ve Önerileri -->
    @if (isset($aiOneriler) && count($aiOneriler) > 0)
        <div class="mb-8">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-200 shadow-sm p-6">
                <h2 class="text-xl font-bold text-indigo-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    🤖 AI Değerlendirmesi: {{ $performans['ai_degerlendirme'] }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($aiOneriler as $oneri)
                        <div class="bg-white rounded-lg border border-indigo-100 p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $oneri['mesaj'] }}</p>
                                    <div class="mt-2">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if ($oneri['oncelik'] === 'yuksek') bg-red-100 text-red-800 border border-red-200
                                    @elseif($oneri['oncelik'] === 'orta') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-green-100 text-green-800 border border-green-200 @endif">
                                            {{ ucfirst($oneri['oncelik']) }} Öncelik
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8">
        <!-- 👤 Temel Bilgiler -->
        <x-neo.card variant="primary" class="p-6">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                👤 Temel Bilgiler
            </h2>

            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-blue-100">
                    <span class="text-sm font-medium text-blue-700">Ad Soyad</span>
                    <span class="text-sm text-blue-900 font-semibold">{{ $danisman->name }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-blue-100">
                    <span class="text-sm font-medium text-blue-700">E-posta</span>
                    <span class="text-sm text-blue-900">{{ $danisman->email }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-blue-100">
                    <span class="text-sm font-medium text-blue-700">Telefon</span>
                    <span class="text-sm text-blue-900">{{ $danisman->phone_number ?? 'Belirtilmemiş' }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-blue-100">
                    <span class="text-sm font-medium text-blue-700">Ünvan</span>
                    <span class="text-sm text-blue-900">{{ $danisman->title ?? 'Danışman' }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-blue-100">
                    <span class="text-sm font-medium text-blue-700">Deneyim Yılı</span>
                    <span class="text-sm text-blue-900">{{ $danisman->deneyim_yili ?? 0 }} yıl</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-blue-100">
                    <span class="text-sm font-medium text-blue-700">Lisans No</span>
                    <span class="text-sm text-blue-900">{{ $danisman->lisans_no ?? 'Belirtilmemiş' }}</span>
                </div>

                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-medium text-blue-700">Durum</span>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if ($danisman->status) bg-green-100 text-green-800 border border-green-200
                            @else bg-red-100 text-red-800 border border-red-200 @endif">
                        {{ $danisman->status ? 'Aktif' : 'Pasif' }}
                    </span>
                </div>

                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-medium text-blue-700">Online Durum</span>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if ($danisman->online) bg-blue-100 text-blue-800 border border-blue-200
                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                        {{ $danisman->online ? 'Online' : 'Offline' }}
                    </span>
                </div>
            </div>
        </x-neo.card>

        <!-- ⭐ Uzmanlık Alanları -->
        <x-neo.card variant="warning" class="p-6">
            <h2 class="text-xl font-bold text-yellow-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                ⭐ Uzmanlık Alanları
            </h2>

            <div class="space-y-6">
                <div>
                    <h4 class="text-sm font-medium text-yellow-700 mb-3">Uzmanlık Alanları</h4>
                    <div class="flex flex-wrap gap-2">
                        @if ($danisman->uzmanlik_alanlari)
                            @foreach ($danisman->uzmanlik_alanlari as $alan)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    {{ $alan }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-sm text-yellow-600">Uzmanlık alanı belirtilmemiş</span>
                        @endif
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-yellow-700 mb-3">Bölge Uzmanlıkları</h4>
                    <div class="flex flex-wrap gap-2">
                        @if ($danisman->bolge_uzmanliklari)
                            @foreach ($danisman->bolge_uzmanliklari as $bolge)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                    {{ $bolge }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-sm text-yellow-600">Bölge uzmanlığı belirtilmemiş</span>
                        @endif
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-yellow-700 mb-3">Diller</h4>
                    <div class="flex flex-wrap gap-2">
                        @if ($danisman->diller)
                            @foreach ($danisman->diller as $dil)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ $dil }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-sm text-yellow-600">Dil bilgisi belirtilmemiş</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-neo.card>

        <!-- 📈 Performans Metrikleri -->
        <x-neo.card variant="purple" class="p-6">
            <h2 class="text-xl font-bold text-purple-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                📈 Performans Metrikleri
            </h2>

            <div class="space-y-6">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-purple-700">Başarı Oranı</span>
                        <span
                            class="text-sm font-semibold text-purple-900">{{ number_format($performans['basari_orani'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-purple-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-purple-500 to-violet-600 h-2 rounded-full transition-all duration-1000"
                            style="width: {{ $performans['basari_orani'] }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-purple-700">Müşteri Memnuniyeti</span>
                        <span
                            class="text-sm font-semibold text-purple-900">{{ number_format($performans['musteri_memnuniyeti'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-purple-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-2 rounded-full transition-all duration-1000"
                            style="width: {{ $performans['musteri_memnuniyeti'] }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-purple-700">AI Skor</span>
                        <span
                            class="text-sm font-semibold text-purple-900">{{ number_format($performans['ai_skor'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-purple-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-1000"
                            style="width: {{ $performans['ai_skor'] }}%"></div>
                    </div>
                </div>
            </div>
        </x-neo.card>

        <!-- 🕒 İletişim ve Çalışma Bilgileri -->
        <x-neo.card variant="success" class="p-6">
            <h2 class="text-xl font-bold text-green-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                🕒 İletişim ve Çalışma Bilgileri
            </h2>

            <div class="space-y-6">
                <div>
                    <h4 class="text-sm font-medium text-green-700 mb-3">İletişim Tercihleri</h4>
                    <div class="flex flex-wrap gap-2">
                        @if ($danisman->iletisim_tercihleri)
                            @foreach ($danisman->iletisim_tercihleri as $tercih)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    {{ ucfirst($tercih) }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-sm text-green-600">İletişim tercihi belirtilmemiş</span>
                        @endif
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-green-700 mb-3">Çalışma Saatleri</h4>
                    <div class="flex flex-wrap gap-2">
                        @if ($danisman->calisma_saatleri)
                            @foreach ($danisman->calisma_saatleri as $saat)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                    {{ $saat }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-sm text-green-600">Çalışma saati belirtilmemiş</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-neo.card>

        <!-- ⚙️ Sistem Bilgileri -->
        <x-neo.card variant="secondary" class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                ⚙️ Sistem Bilgileri
            </h2>

            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-700">Kullanıcı Hesabı</span>
                    <span class="text-sm text-gray-900">
                        @if ($danisman->user)
                            {{ $danisman->user->name }} ({{ $danisman->user->email }})
                        @else
                            <span class="text-gray-500">Hesap bağlı değil</span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-700">Yönetici</span>
                    <span class="text-sm text-gray-900">
                        @if ($danisman->yonetici)
                            {{ $danisman->yonetici->name }}
                        @else
                            <span class="text-gray-500">Yönetici atanmamış</span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-700">Kayıt Tarihi</span>
                    <span class="text-sm text-gray-900">{{ $danisman->created_at->format('d.m.Y H:i') }}</span>
                </div>

                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-medium text-gray-700">Son Güncelleme</span>
                    <span class="text-sm text-gray-900">{{ $danisman->updated_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>
        </x-neo.card>

        <!-- 📝 Notlar -->
        @if ($danisman->notlar)
            <x-neo.card variant="warning" class="p-6">
                <h2 class="text-xl font-bold text-orange-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    📝 Notlar
                </h2>

                <div class="bg-white rounded-lg p-4 border border-orange-200">
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $danisman->notlar }}</p>
                </div>
            </x-neo.card>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Performans grafikleri için animasyon
        document.addEventListener('DOMContentLoaded', function() {
            // Progress bar animasyonu
            const progressBars = document.querySelectorAll('.bg-gradient-to-r');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });
        });
    </script>
@endpush
