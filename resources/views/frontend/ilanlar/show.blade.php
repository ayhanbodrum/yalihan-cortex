@extends('layouts.frontend')

@section('title', $ilan->baslik . ' - Yalıhan Emlak')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('ilanlar.index') }}" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="{{ route('ilanlar.index') }}" class="text-gray-500 hover:text-gray-700">İlanlar</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-900">{{ $ilan->baslik }}</span>
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
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                    @if($ilan->ilanFotograflari && $ilan->ilanFotograflari->count() > 0)
                        <div class="relative">
                            <img src="{{ Storage::url($ilan->ilanFotograflari->first()->dosya_yolu) }}" 
                                alt="{{ $ilan->baslik }}" 
                                class="w-full h-96 object-cover">
                            
                            <!-- Image Counter -->
                            <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                {{ $ilan->ilanFotograflari->count() }} Fotoğraf
                            </div>
                        </div>
                    @else
                        <div class="h-96 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-home text-6xl text-gray-400"></i>
                        </div>
                    @endif
                </div>

                <!-- Property Details -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $ilan->baslik }}</h1>
                            <div class="flex items-center text-gray-600">
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
                            <div class="text-3xl font-bold text-blue-600">
                                {{ number_format($ilan->fiyat, 0, ',', '.') }}
                            </div>
                            <div class="text-sm text-gray-500">{{ $ilan->para_birimi }}</div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($ilan->aciklama)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Açıklama</h3>
                            <div class="text-gray-700 leading-relaxed">
                                {!! nl2br(e($ilan->aciklama)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Property Features -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Özellikler</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @if($ilan->metrekare)
                                <div class="flex items-center">
                                    <i class="fas fa-ruler-combined text-blue-600 mr-2"></i>
                                    <span class="text-gray-700">{{ $ilan->metrekare }} m²</span>
                                </div>
                            @endif
                            
                            @if($ilan->oda_sayisi)
                                <div class="flex items-center">
                                    <i class="fas fa-bed text-blue-600 mr-2"></i>
                                    <span class="text-gray-700">{{ $ilan->oda_sayisi }} Oda</span>
                                </div>
                            @endif
                            
                            @if($ilan->banyo_sayisi)
                                <div class="flex items-center">
                                    <i class="fas fa-bath text-blue-600 mr-2"></i>
                                    <span class="text-gray-700">{{ $ilan->banyo_sayisi }} Banyo</span>
                                </div>
                            @endif
                            
                            @if($ilan->kat)
                                <div class="flex items-center">
                                    <i class="fas fa-building text-blue-600 mr-2"></i>
                                    <span class="text-gray-700">{{ $ilan->kat }}. Kat</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location Details -->
                    @if($ilan->adres)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Adres</h3>
                            <p class="text-gray-700">{{ $ilan->adres }}</p>
                        </div>
                    @endif
                </div>

                <!-- Contact Agent -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">İletişim</h3>
                    
                    @if($ilan->danisman)
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $ilan->danisman->name }}</h4>
                                <p class="text-sm text-gray-600">Danışman</p>
                            </div>
                        </div>
                    @endif

                    @if($ilan->ilanSahibi)
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $ilan->ilanSahibi->ad }} {{ $ilan->ilanSahibi->soyad }}</h4>
                                <p class="text-sm text-gray-600">İlan Sahibi</p>
                                @if($ilan->ilanSahibi->telefon)
                                    <p class="text-sm text-blue-600">{{ $ilan->ilanSahibi->telefon }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-3">
                        <button class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-phone mr-2"></i>Telefon Et
                        </button>
                        <button class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-whatsapp mr-2"></i>WhatsApp
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Similar Properties -->
                @if($benzerIlanlar->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Benzer İlanlar</h3>
                        <div class="space-y-4">
                            @foreach($benzerIlanlar as $benzerIlan)
                                <div class="flex gap-3">
                                    <div class="w-20 h-16 bg-gray-200 rounded-lg flex-shrink-0">
                                        @if($benzerIlan->ilanFotograflari && $benzerIlan->ilanFotograflari->count() > 0)
                                            <img src="{{ Storage::url($benzerIlan->ilanFotograflari->first()->dosya_yolu) }}" 
                                                alt="{{ $benzerIlan->baslik }}" 
                                                class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-home text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 line-clamp-2">
                                            <a href="{{ route('ilanlar.show', $benzerIlan->id) }}" class="hover:text-blue-600">
                                                {{ $benzerIlan->baslik }}
                                            </a>
                                        </h4>
                                        <p class="text-sm text-blue-600 font-medium">
                                            {{ number_format($benzerIlan->fiyat, 0, ',', '.') }} {{ $benzerIlan->para_birimi }}
                                        </p>
                                        <p class="text-xs text-gray-500">
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
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">İlan Bilgileri</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">İlan No:</span>
                            <span class="font-medium">{{ $ilan->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kategori:</span>
                            <span class="font-medium">{{ $ilan->kategori->name ?? 'Genel' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Yayın Tarihi:</span>
                            <span class="font-medium">{{ $ilan->created_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Durum:</span>
                            <span class="font-medium text-green-600">{{ $ilan->status }}</span>
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
