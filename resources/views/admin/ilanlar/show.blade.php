@extends('admin.layouts.neo')

@section('title', 'İlan Detayları')

@section('content')
    <div class="container-fluid py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $ilan->baslik ?? 'İlan Detayları' }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">İlan ID: #{{ $ilan->id ?? 'N/A' }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.ilanlar.edit', $ilan->id) }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Düzenle
                    </a>
                    <a href="{{ route('ilanlar.show', $ilan->id) }}" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        Websitesinde Görüntüle
                    </a>
                    {{-- QR Code Button --}}
                    <div class="relative" x-data="{ showQR: false }">
                        <button @click="showQR = !showQR"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-qrcode mr-2"></i>
                            QR Kod
                        </button>
                        <div x-show="showQR"
                             @click.away="showQR = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 z-50">
                            <x-qr-code-display :ilan="$ilan" :size="'medium'" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Ana İçerik -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Fotoğraflar -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-images text-blue-600 mr-3"></i>
                            Fotoğraflar
                        </h2>

                        @if ($ilan->fotoğraflar && count($ilan->fotoğraflar) > 0)
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach ($ilan->fotoğraflar as $index => $foto)
                                    <div class="relative group cursor-pointer">
                                        <img src="{{ $foto }}" alt="İlan fotoğrafı {{ $index + 1 }}"
                                            class="w-full h-48 object-cover rounded-lg">
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg flex items-center justify-center">
                                            <i
                                                class="fas fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-image text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">Henüz fotoğraf eklenmemiş</p>
                            </div>
                        @endif
                    </div>

                    <!-- Açıklama -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-align-left text-green-600 mr-3"></i>
                            Açıklama
                        </h2>
                        <div class="prose prose-lg max-w-none dark:prose-invert">
                            {!! nl2br(e($ilan->aciklama ?? 'Açıklama eklenmemiş')) !!}
                        </div>
                    </div>

                    <!-- Özellikler -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-list text-purple-600 mr-3"></i>
                            Özellikler
                        </h2>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @if ($ilan->oda_sayisi)
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-bed text-blue-600"></i>
                                    <span class="text-gray-600 dark:text-gray-400">Oda:</span>
                                    <span class="font-semibold">{{ $ilan->oda_sayisi }}</span>
                                </div>
                            @endif

                            @if ($ilan->banyo_sayisi)
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-bath text-blue-600"></i>
                                    <span class="text-gray-600 dark:text-gray-400">Banyo:</span>
                                    <span class="font-semibold">{{ $ilan->banyo_sayisi }}</span>
                                </div>
                            @endif

                            @if ($ilan->brut_metrekare)
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-ruler-combined text-blue-600"></i>
                                    <span class="text-gray-600 dark:text-gray-400">Brüt:</span>
                                    <span class="font-semibold">{{ $ilan->brut_metrekare }} m²</span>
                                </div>
                            @endif

                            @if ($ilan->net_metrekare)
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-square text-blue-600"></i>
                                    <span class="text-gray-600 dark:text-gray-400">Net:</span>
                                    <span class="font-semibold">{{ $ilan->net_metrekare }} m²</span>
                                </div>
                            @endif

                            @if ($ilan->bina_yasi)
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-calendar text-blue-600"></i>
                                    <span class="text-gray-600 dark:text-gray-400">Bina Yaşı:</span>
                                    <span class="font-semibold">{{ $ilan->bina_yasi }} yıl</span>
                                </div>
                            @endif

                            @if ($ilan->kat)
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-layer-group text-blue-600"></i>
                                    <span class="text-gray-600 dark:text-gray-400">Kat:</span>
                                    <span class="font-semibold">{{ $ilan->kat }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(($ilan->ilan_turu ?? null) === 'kiralik' && $ilan->demirbaslar && $ilan->demirbaslar->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-boxes text-orange-600 mr-3"></i>
                            Demirbaşlar
                        </h2>
                        <div class="space-y-6">
                            @php $grouped = $ilan->demirbaslar->groupBy('kategori_id'); @endphp
                            @foreach($grouped as $kategoriId => $items)
                                @php $kategori = \App\Models\DemirbasKategori::find($kategoriId); @endphp
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3 flex items-center">
                                        <i class="fas fa-cube text-orange-600 mr-2"></i>
                                        {{ $kategori->name ?? 'Genel' }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($items as $d)
                                            <div class="flex items-center justify-between p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $d->name }}</span>
                                                    @if($d->pivot->brand)
                                                        <span class="text-xs text-gray-500">{{ $d->pivot->brand }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm font-semibold text-orange-700 dark:text-orange-400">
                                                    {{ $d->pivot->quantity ?? 1 }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- İlan Features (Dinamik Özellikler) -->
                    @if($ilan->ozellikler && $ilan->ozellikler->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-star text-lime-600 mr-3"></i>
                            İlan Özellikleri
                        </h2>

                        @php
                            $featureCategories = $ilan->ozellikler->groupBy('feature_category_id');
                        @endphp

                        @foreach($featureCategories as $categoryId => $features)
                            @php
                                $category = \App\Models\FeatureCategory::find($categoryId);
                            @endphp

                            @if($category)
                            <div class="mb-6 last:mb-0">
                                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3 flex items-center">
                                    <i class="{{ $category->icon ?? 'fas fa-circle' }} text-lime-600 mr-2"></i>
                                    {{ $category->name }}
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($features as $feature)
                                        <div class="flex items-center space-x-2 p-2 rounded-lg bg-lime-50 dark:bg-lime-900/20">
                                            @if($feature->type === 'boolean')
                                                <i class="fas fa-check-circle text-lime-600"></i>
                                                <span class="text-sm text-gray-900 dark:text-white">{{ $feature->name }}</span>
                                            @elseif($feature->type === 'number')
                                                <span class="text-sm font-semibold text-lime-600">{{ $feature->pivot->value ?? 0 }}</span>
                                                <span class="text-xs text-gray-500">{{ $feature->unit }}</span>
                                                <span class="text-sm text-gray-900 dark:text-white">{{ $feature->name }}</span>
                                            @else
                                                <i class="fas fa-check text-lime-600"></i>
                                                <span class="text-sm text-gray-900 dark:text-white">{{ $feature->name }}: {{ $feature->pivot->value ?? '-' }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Yan Panel -->
                <div class="space-y-6">
                    <!-- Fiyat Bilgileri -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Fiyat Bilgileri</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Fiyat:</span>
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ number_format($ilan->fiyat ?? 0, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TRY' }}
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
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Para Birimi:</span>
                                <span class="font-semibold">{{ $ilan->para_birimi ?? 'TRY' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">İlan Türü:</span>
                                <span class="font-semibold">{{ ucfirst($ilan->ilan_turu ?? 'Belirtilmemiş') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Lokasyon -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lokasyon</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">İl:</span>
                                <span class="font-semibold">{{ $ilan->il->il_adi ?? 'Belirtilmemiş' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">İlçe:</span>
                                <span class="font-semibold">{{ $ilan->ilce->ilce_adi ?? 'Belirtilmemiş' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Mahalle:</span>
                                <span class="font-semibold">{{ $ilan->mahalle->mahalle_adi ?? 'Belirtilmemiş' }}</span>
                            </div>
                            @if ($ilan->adres)
                                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">Adres:</span>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $ilan->adres }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- İlan Sahibi -->
                    @if ($ilan->ilanSahibi)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">İlan Sahibi</h3>
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $ilan->ilanSahibi->ad }} {{ $ilan->ilanSahibi->soyad }}
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $ilan->ilanSahibi->telefon }}
                                    </p>
                                    @if ($ilan->ilanSahibi->email)
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $ilan->ilanSahibi->email }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Danışman -->
                    @if ($ilan->danisman)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sorumlu Danışman</h3>
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-tie text-green-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $ilan->danisman->name }}
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $ilan->danisman->email }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Fiyat Geçmişi Grafik -->
                    <div>
                        <x-price-history-chart :ilan="$ilan" :showStats="true" :showTable="true" :height="'350px'" />
                    </div>

                    <!-- Yazlık Detayları -->
                    @if($ilan->yazlikDetail)
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-xl shadow-lg p-6 border border-blue-200 dark:border-blue-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-umbrella-beach text-blue-600 mr-2"></i>
                            Yazlık Kiralama Detayları
                        </h3>
                        <div class="space-y-3">
                            @if($ilan->yazlikDetail->min_konaklama)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Minimum Konaklama:</span>
                                <span class="font-semibold">{{ $ilan->yazlikDetail->min_konaklama }} gün</span>
                            </div>
                            @endif

                            @if($ilan->yazlikDetail->max_misafir)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Max. Misafir:</span>
                                <span class="font-semibold">{{ $ilan->yazlikDetail->max_misafir }} kişi</span>
                            </div>
                            @endif

                            @if($ilan->yazlikDetail->havuz)
                            <div class="flex items-center justify-between p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <span class="text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-swimming-pool text-blue-600 mr-2"></i>
                                    Havuz
                                </span>
                                <span class="font-semibold text-green-600">Var</span>
                            </div>
                            @endif

                            @if($ilan->yazlikDetail->gunluk_fiyat)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Günlük Fiyat:</span>
                                <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($ilan->yazlikDetail->gunluk_fiyat, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TRY' }}/Gün</span>
                            </div>
                            @endif

                            @if($ilan->yazlikDetail->haftalik_fiyat)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Haftalık Fiyat:</span>
                                <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($ilan->yazlikDetail->haftalik_fiyat, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TRY' }}/Hafta</span>
                            </div>
                            @endif

                            @if($ilan->yazlikDetail->aylik_fiyat)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Aylık Fiyat:</span>
                                <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($ilan->yazlikDetail->aylik_fiyat, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TRY' }}/Ay</span>
                            </div>
                            @endif

                            @if($ilan->yazlikDetail->sezon_baslangic)
                            <div class="flex justify-between pt-3 border-t border-blue-200 dark:border-blue-800">
                                <span class="text-gray-600 dark:text-gray-400">Sezon:</span>
                                <span class="font-semibold text-sm">
                                    {{ \Carbon\Carbon::parse($ilan->yazlikDetail->sezon_baslangic)->format('d.m.Y') }} -
                                    {{ \Carbon\Carbon::parse($ilan->yazlikDetail->sezon_bitis)->format('d.m.Y') }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- İlan Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">İlan Status</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                <span
                                    class="px-3 py-1 rounded-full text-sm font-medium
                                {{ ($ilan->status instanceof \App\Enums\IlanStatus && $ilan->status->value === 'yayinda') || $ilan->status === 'yayinda' || $ilan->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    @if($ilan->status instanceof \App\Enums\IlanStatus)
                                        {{ $ilan->status->label() }}
                                    @else
                                        {{ ucfirst($ilan->status ?? 'inactive') }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Oluşturulma:</span>
                                <span class="font-semibold">{{ $ilan->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Güncellenme:</span>
                                <span class="font-semibold">{{ $ilan->updated_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hızlı İşlemler -->
                    <div
                        class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Hızlı İşlemler</h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.ilanlar.edit', $ilan->id) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-edit mr-2"></i>
                                Düzenle
                            </a>
                            <a href="{{ route('ilanlar.show', $ilan->id) }}" target="_blank"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>
                                Websitesinde Görüntüle
                            </a>
                            <button onclick="shareProperty()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-share-alt mr-2"></i>
                                Paylaş
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Listing Navigation --}}
        <div class="mt-8">
            <x-listing-navigation :ilan="$ilan" :mode="'default'" :showSimilar="true" :similarLimit="4" />
        </div>
    </div>

    <script>
        function shareProperty() {
            const url = window.location.origin + '/ilanlar/{{ $ilan->id }}';
            const title = '{{ $ilan->baslik }}';

            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: 'Bu gayrimenkul ilginizi çekebilir',
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    alert('İlan linki panoya kopyalandı!');
                });
            }
        }
    </script>
@endsection
