@extends('layouts.frontend')

@section('title', $ilan->baslik . ' - Yalıhan Emlak')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Breadcrumb -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('ilanlar.index') }}" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="{{ route('ilanlar.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">İlanlar</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-900 dark:text-white">{{ $ilan->baslik }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Property Images -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden mb-6 transition-all duration-200 hover:shadow-md">
                    @if($ilan->fotograflar && $ilan->fotograflar->count() > 0)
                        <div class="relative">
                            <img src="{{ Storage::url($ilan->fotograflar->first()->dosya_yolu) }}"
                                alt="{{ $ilan->baslik }}"
                                class="w-full h-96 object-cover transition-all duration-300 hover:scale-105">

                            <!-- Image Counter -->
                            <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm backdrop-blur-sm">
                                {{ $ilan->fotograflar->count() }} Fotoğraf
                            </div>
                        </div>
                    @else
                        <div class="h-96 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-home text-6xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                    @endif
                </div>

                <!-- Property Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 transition-all duration-200 hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $ilan->baslik }}</h1>
                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span>
                                    @if($ilan->il)
                                        {{ $ilan->il->il_adi }}
                                        @if($ilan->ilce)
                                            / {{ $ilan->ilce->ilce_adi }}
                                        @endif
                                        @if($ilan->mahalle)
                                            / {{ $ilan->mahalle->mahalle_adi }}
                                        @endif
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($ilan->fiyat, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TRY' }}
                                @if ($ilan->kiralama_turu)
                                    @switch($ilan->kiralama_turu)
                                        @case('gunluk')
                                            <span class="text-lg font-normal text-gray-600 dark:text-gray-400">/Gün</span>
                                        @break
                                        @case('haftalik')
                                            <span class="text-lg font-normal text-gray-600 dark:text-gray-400">/Hafta</span>
                                        @break
                                        @case('aylik')
                                        @case('uzun_donem')
                                            <span class="text-lg font-normal text-gray-600 dark:text-gray-400">/Ay</span>
                                        @break
                                        @case('sezonluk')
                                            <span class="text-lg font-normal text-gray-600 dark:text-gray-400">/Sezon</span>
                                        @break
                                    @endswitch
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($ilan->aciklama)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Açıklama</h3>
                            <div class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                {!! nl2br(e($ilan->aciklama)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Property Features -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Özellikler</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @if($ilan->metrekare)
                                <div class="flex items-center">
                                    <i class="fas fa-ruler-combined text-blue-600 dark:text-blue-400 mr-2"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $ilan->metrekare }} m²</span>
                                </div>
                            @endif

                            @if($ilan->oda_sayisi)
                                <div class="flex items-center">
                                    <i class="fas fa-bed text-blue-600 dark:text-blue-400 mr-2"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $ilan->oda_sayisi }} Oda</span>
                                </div>
                            @endif

                            @if($ilan->banyo_sayisi)
                                <div class="flex items-center">
                                    <i class="fas fa-bath text-blue-600 dark:text-blue-400 mr-2"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $ilan->banyo_sayisi }} Banyo</span>
                                </div>
                            @endif

                            @if($ilan->kat)
                                <div class="flex items-center">
                                    <i class="fas fa-building text-blue-600 dark:text-blue-400 mr-2"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $ilan->kat }}. Kat</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location Details -->
                    @if($ilan->adres)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Adres</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $ilan->adres }}</p>
                        </div>
                    @endif
                </div>

                <!-- Contact Agent -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-all duration-200 hover:shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">İletişim</h3>

                    @if($ilan->danisman)
                        <div class="mb-4">
                            <div class="flex items-center mb-3">
                                @php
                                    $avatarUrl = $ilan->danisman->profile_photo_path
                                        ? Storage::url($ilan->danisman->profile_photo_path)
                                        : asset('images/default-avatar.png');
                                @endphp
                                <img src="{{ $avatarUrl }}"
                                    alt="{{ $ilan->danisman->name }}"
                                    class="w-12 h-12 rounded-full object-cover border-2 border-blue-200 mr-4">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $ilan->danisman->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $ilan->danisman->title ?? 'Emlak Danışmanı' }}
                                    </p>
                                    @if($ilan->danisman->phone_number)
                                        <a href="tel:{{ $ilan->danisman->phone_number }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                            <i class="fas fa-phone mr-1"></i>{{ $ilan->danisman->phone_number }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Social Media Links --}}
                            @php
                                $hasSocialMedia = !empty($ilan->danisman->instagram_profile) ||
                                                 !empty($ilan->danisman->whatsapp_number) ||
                                                 !empty($ilan->danisman->telegram_username);
                            @endphp
                            @if($hasSocialMedia)
                                <div class="ml-16 pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Sosyal Medya</p>
                                    <x-frontend.danisman-social-links :danisman="$ilan->danisman" size="xs" variant="outline" />
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($ilan->ilanSahibi)
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $ilan->ilanSahibi->ad }} {{ $ilan->ilanSahibi->soyad }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">İlan Sahibi</p>
                                @if($ilan->ilanSahibi->telefon)
                                    <p class="text-sm text-blue-600 dark:text-blue-400">{{ $ilan->ilanSahibi->telefon }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($ilan->danisman)
                        <div class="flex gap-3">
                            @if($ilan->danisman->phone_number)
                                <a href="tel:{{ $ilan->danisman->phone_number }}" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 hover:scale-105 active:scale-95 text-center">
                                    <i class="fas fa-phone mr-2"></i>Telefon Et
                                </a>
                            @endif
                            @if($ilan->danisman->whatsapp_number)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ilan->danisman->whatsapp_number) }}"
                                   target="_blank"
                                   class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-200 hover:scale-105 active:scale-95 text-center">
                                    <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                                </a>
                            @endif
                            @if($ilan->danisman->email)
                                <a href="mailto:{{ $ilan->danisman->email }}" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-all duration-200 hover:scale-105 active:scale-95 text-center">
                                    <i class="fas fa-envelope mr-2"></i>E-posta
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- QR Code --}}
                    <div class="mt-4">
                        <x-qr-code-display :ilan="$ilan" :size="'medium'" :showLabel="true" :showDownload="true" />
                    </div>
                </div>

                {{-- Listing Navigation --}}
                <div class="mt-6">
                    <x-listing-navigation :ilan="$ilan" :mode="'default'" :showSimilar="true" :similarLimit="4" />
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- QR Code Widget -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 transition-all duration-200 hover:shadow-md">
                    <x-qr-code-display :ilan="$ilan" :size="'small'" :showLabel="true" :showDownload="true" />
                </div>

                <!-- Similar Properties -->
                @if($similar && $similar->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 transition-all duration-200 hover:shadow-md">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Benzer İlanlar</h3>
                        <div class="space-y-4">
                            @foreach($similar as $benzerIlan)
                                <div class="flex gap-3 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 p-2 rounded-lg">
                                    <div class="w-20 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex-shrink-0 overflow-hidden">
                                        @if($benzerIlan->fotograflar && $benzerIlan->fotograflar->count() > 0)
                                            <img src="{{ Storage::url($benzerIlan->fotograflar->first()->dosya_yolu) }}"
                                                alt="{{ $benzerIlan->baslik }}"
                                                class="w-full h-full object-cover rounded-lg transition-all duration-300 hover:scale-110">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-home text-gray-400 dark:text-gray-500"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">
                                            <a href="{{ route('ilanlar.show', $benzerIlan->id) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                                {{ $benzerIlan->baslik ?? 'İlan #' . $benzerIlan->id }}
                                            </a>
                                        </h4>
                                        <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                                            @if($benzerIlan->fiyat)
                                                {{ number_format($benzerIlan->fiyat, 0, ',', '.') }} {{ $benzerIlan->para_birimi ?? 'TRY' }}
                                                @if ($benzerIlan->kiralama_turu)
                                                    @switch($benzerIlan->kiralama_turu)
                                                        @case('gunluk')
                                                            <span class="text-xs text-gray-600 dark:text-gray-400">/Gün</span>
                                                        @break
                                                        @case('haftalik')
                                                            <span class="text-xs text-gray-600 dark:text-gray-400">/Hafta</span>
                                                        @break
                                                        @case('aylik')
                                                        @case('uzun_donem')
                                                            <span class="text-xs text-gray-600 dark:text-gray-400">/Ay</span>
                                                        @break
                                                        @case('sezonluk')
                                                            <span class="text-xs text-gray-600 dark:text-gray-400">/Sezon</span>
                                                        @break
                                                    @endswitch
                                                @endif
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            @if($benzerIlan->il)
                                                {{ $benzerIlan->il->il_adi }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Property Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-all duration-200 hover:shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">İlan Bilgileri</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">İlan No:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $ilan->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Kategori:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $ilan->kategori->name ?? 'Genel' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Yayın Tarihi:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $ilan->created_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Durum:</span>
                            <span class="font-medium text-green-600 dark:text-green-400">{{ $ilan->status?->label() ?? $ilan->status }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
