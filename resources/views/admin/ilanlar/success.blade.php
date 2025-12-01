@extends('admin.layouts.admin')

@section('title', 'İlan Başarıyla Oluşturuldu!')

@section('content')
    <div class="container-fluid py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Success Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
                    <i class="fas fa-check text-4xl text-green-600"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    🎉 Tebrikler!
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    İlanınız başarıyla oluşturuldu ve sisteme kaydedildi.
                </p>
            </div>

            <!-- İlan Detayları -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    İlan Detayları
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Temel Bilgiler -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Temel Bilgiler</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">İlan ID:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">#{{ $ilan->id ?? 'YENİ' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Başlık:</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $ilan->baslik ?? 'Başlık Yok' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">İlan Türü:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ ucfirst($ilan->ilan_turu ?? 'Belirtilmemiş') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Emlak Türü:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ ucfirst($ilan->emlak_turu ?? 'Belirtilmemiş') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Fiyat Bilgileri -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Fiyat Bilgileri</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Fiyat:</span>
                                <span class="font-semibold text-green-600 text-lg">
                                    {{ number_format($ilan->fiyat ?? 0, 0, ',', '.') }} ₺
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Para Birimi:</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $ilan->para_birimi ?? 'TRY' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Metrekare:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $ilan->brut_metrekare ?? '-' }}
                                    m²</span>
                            </div>
                        </div>
                    </div>

                    <!-- Lokasyon -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Lokasyon</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">İl:</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $ilan->il->il_adi ?? 'Belirtilmemiş' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">İlçe:</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $ilan->ilce->ilce_adi ?? 'Belirtilmemiş' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Mahalle:</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $ilan->mahalle->mahalle_adi ?? 'Belirtilmemiş' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İlan Sahibi -->
                @if ($ilan->ilanSahibi ?? false)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">İlan Sahibi</h3>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $ilan->ilanSahibi->ad }} {{ $ilan->ilanSahibi->soyad }}
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $ilan->ilanSahibi->telefon }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Aksiyon Butonları -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Düzenleme -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center">
                    <div
                        class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-edit text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Düzenle</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">İlan bilgilerini güncelleyin</p>
                    <a href="{{ route('admin.ilanlar.edit', $ilan->id ?? 1) }}"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Düzenle
                    </a>
                </div>

                <!-- Frontend'de Görüntüle -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center">
                    <div
                        class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-eye text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Websitesinde Görüntüle</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">İlanı müşteri görünümünde görün</p>
                    <a href="{{ route('ilanlar.show', $ilan->id ?? 1) }}" target="_blank"
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Görüntüle
                    </a>
                </div>

                <!-- Paylaş -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center">
                    <div
                        class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-share-alt text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Paylaş</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">İlanı sosyal medyada paylaşın</p>
                    <button onclick="shareProperty()"
                        class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-share-alt mr-2"></i>
                        Paylaş
                    </button>
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div
                class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Hızlı İşlemler</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('admin.ilanlar.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-list mr-2"></i>
                        Tüm İlanlar
                    </a>
                    <a href="{{ route('admin.ilanlar.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Yeni İlan
                    </a>
                    <a href="{{ route('frontend.portfolio.index') }}" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-building mr-2"></i>
                        Portföy Sayfası
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function shareProperty() {
            const url = window.location.origin + '/ilanlar/{{ $ilan->id ?? 1 }}';
            const title = '{{ $ilan->baslik ?? 'Yalıhan Emlak İlanı' }}';

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
