@csrf
<div class="space-y-8">

    {{-- Hata Mesajları --}}
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-semibold">Form Hataları!</p>
            </div>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Temel Bilgiler --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm">
        <div class="p-6">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                👤 Temel Talep Bilgileri
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="form-field">
                    <label for="kisi_id" class="admin-label">
                        Müşteri <span class="text-red-500">*</span>
                    </label>
                    <select id="kisi_id" name="kisi_id" class="admin-input" required>
                        <option value="">Müşteri Seçin</option>
                        @foreach ($kisiler as $kisi)
                            <option value="{{ $kisi->id }}" @selected(old('kisi_id', $talep->kisi_id ?? '') == $kisi->id)>
                                {{ $kisi->tam_ad }} ({{ $kisi->telefon }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label for="talep_tipi" class="admin-label">
                        Talep Tipi <span class="text-red-500">*</span>
                    </label>
                    <select id="talep_tipi" name="talep_tipi" class="admin-input" required>
                        @foreach ($talepTipleri as $tip)
                            <option value="{{ $tip }}" @selected(old('talep_tipi', $talep->talep_tipi ?? '') == $tip)>{{ $tip }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label for="category_id" class="admin-label">
                        Emlak Kategorisi <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" class="admin-input" required>
                        <option value="">Kategori Seçin</option>
                        @foreach ($kategoriler as $kategori)
                            <option value="{{ $kategori->id }}" @selected(old('category_id', $talep->category_id ?? '') == $kategori->id)>{{ $kategori->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Adres Bilgileri --}}
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 shadow-sm">
        <div class="p-6">
            <h2 class="text-xl font-bold text-green-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                📍 Lokasyon Tercihi
            </h2>
            <x-location-selector-unified :selected-country="old('ulke_id', $talep->ulke_id ?? '1')" :selected-province="old('il_id', $talep->il_id ?? '')" :selected-district="old('ilce_id', $talep->ilce_id ?? '')" :selected-neighborhood="old('mahalle_id', $talep->mahalle_id ?? '')"
                :countries="$ulkeler ?? []" :required="true" :show-country="true" :show-neighborhood="true"
                grid-cols="grid-cols-1 md:grid-cols-2 lg:grid-cols-4" name-prefix="" class="" />

            <!-- Adres Detayı -->
            <div class="form-field mt-6">
                <label class="admin-label font-semibold text-gray-700 mb-2">Detay Adres</label>
                <textarea name="detay_adres" rows="3"
                    class="admin-input border-2 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 py-3 text-base"
                    placeholder="Sokak, cadde, bina numarası, daire no vb. detay bilgiler">{{ old('detay_adres', $talep->detay_adres ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Kriterler --}}
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-200 shadow-sm">
        <div class="p-6">
            <h2 class="text-xl font-bold text-purple-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                💰 Fiyat ve Diğer Kriterler
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="form-field">
                    <label for="min_fiyat" class="admin-label">Minimum Fiyat</label>
                    <input type="number" name="min_fiyat" id="min_fiyat"
                        value="{{ old('min_fiyat', $talep->min_fiyat ?? '') }}" class="admin-input"
                        placeholder="Örn: 500000">
                </div>
                <div class="form-field">
                    <label for="max_fiyat" class="admin-label">Maksimum Fiyat</label>
                    <input type="number" name="max_fiyat" id="max_fiyat"
                        value="{{ old('max_fiyat', $talep->max_fiyat ?? '') }}" class="admin-input"
                        placeholder="Örn: 1500000">
                </div>
                <div class="form-field">
                    <label for="para_birimi" class="admin-label">Para Birimi</label>
                    <select name="para_birimi" id="para_birimi" class="admin-input">
                        <option value="TRY" @selected(old('para_birimi', $talep->para_birimi ?? 'TRY') == 'TRY')>TRY</option>
                        <option value="USD" @selected(old('para_birimi', $talep->para_birimi ?? '') == 'USD')>USD</option>
                        <option value="EUR" @selected(old('para_birimi', $talep->para_birimi ?? '') == 'EUR')>EUR</option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="status" class="admin-label">
                        Durum <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" class="admin-input" required>
                        @foreach ($statuslar as $status)
                            <option value="{{ $status }}" @selected(old('status', $talep->status ?? 'active') == $status)>{{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label for="min_metrekare" class="admin-label">Min. Metrekare</label>
                    <input type="number" name="min_metrekare" id="min_metrekare"
                        value="{{ old('min_metrekare', $talep->min_metrekare ?? '') }}" class="admin-input"
                        placeholder="Örn: 80">
                </div>
                <div class="form-field">
                    <label for="max_metrekare" class="admin-label">Maks. Metrekare</label>
                    <input type="number" name="max_metrekare" id="max_metrekare"
                        value="{{ old('max_metrekare', $talep->max_metrekare ?? '') }}" class="admin-input"
                        placeholder="Örn: 200">
                </div>
                <div class="form-field">
                    <label for="min_oda_sayisi" class="admin-label">Min. Oda Sayısı</label>
                    <input type="number" name="min_oda_sayisi" id="min_oda_sayisi"
                        value="{{ old('min_oda_sayisi', $talep->min_oda_sayisi ?? '') }}" class="admin-input"
                        placeholder="Örn: 2">
                </div>
                <div class="form-field">
                    <label for="max_oda_sayisi" class="admin-label">Maks. Oda Sayısı</label>
                    <input type="number" name="max_oda_sayisi" id="max_oda_sayisi"
                        value="{{ old('max_oda_sayisi', $talep->max_oda_sayisi ?? '') }}" class="admin-input"
                        placeholder="Örn: 5">
                </div>
            </div>
            <div class="mt-6">
                <label for="aciklama" class="admin-label">Açıklama / Notlar</label>
                <textarea name="aciklama" id="aciklama" rows="4" class="admin-input"
                    placeholder="Müşteri talebi hakkında detaylı bilgi...">{{ old('aciklama', $talep->aciklama ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Form Eylemleri --}}
    <div class="flex justify-end gap-4 pt-6">
        <a href="{{ route('admin.talepler.index') }}" class="neo-btn neo-btn-secondary touch-target-optimized">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            İptal
        </a>
        <button type="submit" class="neo-btn neo-btn-primary touch-target-optimized">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ $submitText ?? 'Kaydet' }}
        </button>
    </div>
</div>

@push('styles')
    <style>
        /* Context7 Form Field Standards */
        .form-field {
            @apply space-y-2;
        }

        .admin-label {
            @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1;
        }

        .admin-input,
        .admin-input,
        .admin-input {
            @apply w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg;
            @apply bg-white dark:bg-gray-800 text-gray-900 dark:text-white;
            @apply focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900;
            @apply focus:outline-none transition-all duration-200;
            @apply placeholder-gray-400 dark:placeholder-gray-500 resize-vertical;
        }

        .admin-input:hover {
            @apply border-gray-400 dark:border-gray-500;
        }

        .admin-input:disabled {
            @apply bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed;
        }

        /* Context7 Button Standards */
        .neo-btn.neo-btn-primary {
            @apply inline-flex items-center px-6 py-3;
            @apply bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800;
            @apply text-white font-semibold rounded-lg shadow-md hover:shadow-lg;
            @apply focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2;
            @apply transition-all duration-200 transform hover:scale-105 active:scale-95;
        }

        .neo-btn.neo-btn-secondary {
            @apply inline-flex items-center px-6 py-3;
            @apply bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800;
            @apply text-white font-semibold rounded-lg shadow-md hover:shadow-lg;
            @apply focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2;
            @apply transition-all duration-200 transform hover:scale-105 active:scale-95;
        }

        /* Touch Target Optimization */
        .touch-target-optimized {
            @apply min-h-[44px] min-w-[44px];
        }
    </style>
@endpush

@push('scripts')
    <!-- Context7 Select2 Legacy Manager -->
    <script src="{{ asset('js/admin/select2-legacy-manager.js') }}"></script>
    <link href="{{ asset('css/context7-select2-theme.css') }}" rel="stylesheet">

    <script>
        // LEGACY_SELECT2 - 2025-01-30 - Context7'e geçiş yapılacak
        $(document).ready(function() {
            // Legacy mode enable et
            document.body.classList.add('legacy-select2');

            // Select2 Legacy Manager otomatik başlatılacak
            console.log('🔧 Legacy Select2 form yüklendi - Legacy Manager enable');
        });

        // Eski addressSelector kodu kaldırıldı - Artık unified component kullanılıyor
    </script>
@endpush
