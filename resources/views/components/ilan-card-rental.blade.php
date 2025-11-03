{{-- ========================================
     İLAN CARD - RENTAL VERSION
     Günlük, haftalık, sezonluk kiralama için
     ======================================== --}}

@props([
    'ilan' => null,
    'showImage' => true,
    'showActions' => true,
    'showDetails' => true,
])

<div class="ilan-card-rental bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
    {{-- İlan Görseli --}}
    @if ($showImage && $ilan->kapak_fotografi_url)
        <div class="relative">
            <img src="{{ $ilan->kapak_fotografi_url }}" alt="{{ $ilan->baslik ?? 'İlan Görseli' }}"
                class="w-full h-48 object-cover">

            {{-- Kiralama Türü Badge --}}
            @if ($ilan->kiralama_turu)
                <div class="absolute top-3 left-3">
                    @switch($ilan->kiralama_turu)
                        @case('gunluk')
                            <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                🏠 Günlük Kiralama
                            </span>
                        @break

                        @case('haftalik')
                            <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                🏡 Haftalık Kiralama
                            </span>
                        @break

                        @case('sezonluk')
                            <span class="bg-purple-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                🌅 Sezonluk Kiralama
                            </span>
                        @break

                        @case('uzun_donem')
                            <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                📅 Uzun Dönem Kiralama
                            </span>
                        @break
                    @endswitch
                </div>
            @endif

            {{-- Favori Butonu --}}
            @if ($showActions)
                <button
                    class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition-colors"
                    @click="toggleFavorite({{ $ilan->id }})">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            @endif
        </div>
    @endif

    {{-- İlan Bilgileri --}}
    <div class="p-4">
        {{-- Başlık ve Konum --}}
        <div class="mb-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-2">
                {{ $ilan->baslik ?? 'İlan Başlığı' }}
            </h3>
            <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ $ilan->il->il_adi ?? '' }}, {{ $ilan->ilce->ilce_adi ?? '' }}</span>
            </div>
        </div>

        {{-- Kiralama Detayları --}}
        @if ($ilan->kiralama_turu)
            <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                <div class="text-sm font-medium text-blue-800 mb-2">Kiralama Bilgileri:</div>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    @if ($ilan->min_kiralama_suresi)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Min. Süre:</span>
                            <span class="font-medium">{{ $ilan->min_kiralama_suresi }} gün</span>
                        </div>
                    @endif
                    @if ($ilan->sezon_baslangic && $ilan->sezon_bitis)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sezon:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($ilan->sezon_baslangic)->format('d.m') }}
                                - {{ \Carbon\Carbon::parse($ilan->sezon_bitis)->format('d.m') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Fiyat Görünümü --}}
        <div class="mb-4">
            @if ($ilan->fiyat_gorunumu)
                <x-price-display :price="$ilan->fiyat" :currency="$ilan->para_birimi ?? 'TRY'" :displayType="$ilan->fiyat_gorunumu" :startingPrice="$ilan->baslayan_fiyat"
                    :minPrice="$ilan->min_fiyat" :maxPrice="$ilan->max_fiyat" :rentalType="$ilan->kiralama_turu" :dailyPrice="$ilan->gunluk_fiyat" :weeklyPrice="$ilan->haftalik_fiyat"
                    :seasonalPrice="$ilan->sezonluk_fiyat" />
            @else
                {{-- Varsayılan fiyat gösterimi --}}
                <div class="text-2xl font-bold text-gray-900">
                    {{ number_format($ilan->fiyat, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TRY' }}
                </div>
            @endif
        </div>

        {{-- İlan Detayları --}}
        @if ($showDetails)
            <div class="grid grid-cols-2 gap-3 text-sm text-gray-600 mb-4">
                @if ($ilan->oda_sayisi)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ $ilan->oda_sayisi }} Oda</span>
                    </div>
                @endif
                @if ($ilan->metrekare)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                        <span>{{ $ilan->metrekare }} m²</span>
                    </div>
                @endif
                @if ($ilan->banyo_sayisi)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $ilan->banyo_sayisi }} Banyo</span>
                    </div>
                @endif
                @if ($ilan->arsa_alani)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ $ilan->arsa_alani }} m² Arsa</span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Aksiyon Butonları --}}
        @if ($showActions)
            <div class="flex space-x-2">
                <a href="{{ route('ilanlar.show', $ilan->id) }}"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition-colors">
                    Detayları Gör
                </a>
                <button @click="whatsappForInquiry({{ $ilan->id }})"
                    class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                    </svg>
                </button>
            </div>
        @endif
    </div>
</div>

<script>
    // Favori ekleme/çıkarma
    function toggleFavorite(ilanId) {
        // Favori işlemi
        console.log('Favori toggle:', ilanId);
    }

    // WhatsApp ile iletişim
    function whatsappForInquiry(ilanId) {
        const message = encodeURIComponent(`Merhaba, ${ilanId} numaralı ilan hakkında bilgi almak istiyorum.`);
        const phone = '905332090302';
        window.open(`https://wa.me/${phone}?text=${message}`, '_blank');
    }
</script>
