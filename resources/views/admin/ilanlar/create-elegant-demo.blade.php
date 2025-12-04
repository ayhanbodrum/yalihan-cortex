@extends('admin.layouts.app')

@section('title', 'Modern İlan Ekleme - Demo')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="mb-8 text-center">
                <h1
                    class="text-4xl font-bold bg-clip-text text-transparent
                       bg-gradient-to-r from-blue-600 to-purple-600
                       dark:from-blue-400 dark:to-purple-400
                       mb-2">
                    ✨ Modern İlan Ekleme
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">
                    Ultra-elegant form design system demo
                </p>
            </div>

            <form action="{{ route('admin.ilanlar.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Kategori Sistemi --}}
                @include('admin.ilanlar.components.category-system-elegant', [
                    'categories' => \App\Models\IlanKategori::whereNull('parent_id')->get(),
                ])

                {{-- Temel Bilgiler --}}
                @include('admin.ilanlar.components.basic-info-elegant')

                {{-- Demo: Price Input --}}
                <x-admin.ilanlar.components.elegant-form-wrapper sectionId="section-price" title="Fiyat Bilgileri"
                    subtitle="İlanın fiyat ve para birimi bilgilerini girin" badgeNumber="5" badgeColor="orange"
                    :icon="'<svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'>
                                                                                                                                                  <path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\'
                                                                                                                                                        d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\' />
                                                                                                                                                </svg>'" glassEffect="false">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-admin.ilanlar.components.elegant-input name="fiyat" type="number" label="Fiyat"
                            placeholder="0" :required="true" :floating="true" :icon="'<svg class=\'w-5 h-5\' fill=\'currentColor\' viewBox=\'0 0 20 20\'>
                                                                                                                                                                                                          <path d=\'M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z\' />
                                                                                                                                                                                                          <path fill-rule=\'evenodd\' d=\'M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.51-1.31c-.562-.649-1.413-1.076-2.353-1.253V5z\' clip-rule=\'evenodd\' />
                                                                                                                                                                                                        </svg>'"
                            helpText="İlanın satış veya kiralama fiyatını girin" />

                        <x-admin.ilanlar.components.elegant-input name="para_birimi" type="select" label="Para Birimi"
                            :required="true" :floating="true" :icon="'<svg class=\'w-5 h-5\' fill=\'currentColor\' viewBox=\'0 0 20 20\'>
                                                                                                                                                                                                          <path fill-rule=\'evenodd\' d=\'M10 18a8 8 0 100-16 8 8 0 000 16zM7 5a1 1 0 100 2h1a2 2 0 011.732 1H7a1 1 0 100 2h2.732A2 2 0 018 11H7a1 1 0 100 2h1a2 2 0 003.464 1H13a1 1 0 100-2h-1a2 2 0 00-1.732-1H13a1 1 0 100-2h-2.732A2 2 0 0112 7h1a1 1 0 100-2h-1a2 2 0 00-3.464-1H7z\' clip-rule=\'evenodd\' />
                                                                                                                                                                                                        </svg>'" helpText="Fiyatın para birimini seçin">
                            <option value="TRY">₺ Türk Lirası</option>
                            <option value="USD">$ Dolar</option>
                            <option value="EUR">€ Euro</option>
                            <option value="GBP">£ Sterlin</option>
                        </x-admin.ilanlar.components.elegant-input>
                    </div>
                </x-admin.ilanlar.components.elegant-form-wrapper>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-4 pt-6">
                    <a href="{{ route('admin.ilanlar.index') }}"
                        class="px-6 py-3 rounded-xl
                          border-2 border-gray-300 dark:border-gray-600
                          text-gray-700 dark:text-gray-300
                          hover:bg-gray-50 dark:hover:bg-gray-800
                          transition-all duration-300
                          font-semibold">
                        İptal
                    </a>

                    <button type="submit"
                        class="px-8 py-3 rounded-xl
                               bg-gradient-to-r from-blue-600 to-purple-600
                               hover:from-blue-700 hover:to-purple-700
                               text-white
                               shadow-lg shadow-blue-500/30
                               hover:shadow-xl hover:shadow-blue-500/40
                               transition-all duration-300
                               hover:scale-105
                               font-bold">
                        ✨ Kaydet ve Yayınla
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        {{-- Category functions inline (lightweight) --}}
        <script>
            // loadAltKategoriler function
            function loadAltKategoriler(anaKategoriId) {
                const altKategoriSelect = document.getElementById('alt_kategori_id');
                if (!altKategoriSelect) return;

                if (!anaKategoriId) {
                    altKategoriSelect.innerHTML = '<option value="">-- Önce Ana Kategori Seçin --</option>';
                    return;
                }

                // Clear current options
                altKategoriSelect.innerHTML = '<option value="">Yükleniyor...</option>';

                // Fetch sub-categories (✅ Doğru API endpoint)
                fetch(`/api/categories/sub/${anaKategoriId}`)
                    .then(response => response.json())
                    .then(data => {
                        altKategoriSelect.innerHTML = '<option value="">-- Alt Kategori Seçin --</option>';
                        const categories = data.data || data.categories || [];
                        if (categories.length > 0) {
                            categories.forEach(category => {
                                const option = document.createElement('option');
                                option.value = category.id;
                                option.textContent = category.name;
                                altKategoriSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Alt kategoriler yüklenemedi:', error);
                        altKategoriSelect.innerHTML = '<option value="">-- Hata oluştu --</option>';
                    });
            }

            // loadYayinTipleri function
            function loadYayinTipleri() {
                const yayinTipiSelect = document.getElementById('yayin_tipi_id');
                const altKategoriId = document.getElementById('alt_kategori_id')?.value;

                if (!yayinTipiSelect) return;

                if (!altKategoriId) {
                    yayinTipiSelect.innerHTML = '<option value="">-- Önce Alt Kategori Seçin --</option>';
                    return;
                }

                yayinTipiSelect.innerHTML = '<option value="">Yükleniyor...</option>';

                // ✅ Doğru API endpoint
                fetch(`/api/categories/publication-types/${altKategoriId}`)
                    .then(response => response.json())
                    .then(data => {
                        yayinTipiSelect.innerHTML = '<option value="">-- Yayın Tipi Seçin --</option>';
                        const types = data.data || data.publicationTypes || [];
                        if (types.length > 0) {
                            types.forEach(type => {
                                const option = document.createElement('option');
                                option.value = type.id;
                                option.textContent = type.name;
                                yayinTipiSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Yayın tipleri yüklenemedi:', error);
                        yayinTipiSelect.innerHTML = '<option value="">-- Hata oluştu --</option>';
                    });
            }

            // safeDispatchCategoryChanged (placeholder)
            function safeDispatchCategoryChanged() {
                console.log('Category changed event dispatched');
            }

            console.log('✅ Category functions loaded');
        </script>
    @endpush
@endsection
