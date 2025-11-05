@extends('admin.layouts.neo')

@section('title', 'Yeni Eşleştirme Oluştur')
@section('meta_description', 'Müşteri talepleri ile ilanları eşleştirin - AI destekli akıllı eşleştirme sistemi ile müşteri memnuniyetini artırın.')
@section('meta_keywords', 'eşleştirme oluştur, talep ilan eşleştirme, müşteri talep, emlak eşleştirme, ai eşleştirme')

@section('content')
    <!-- Context7: Eşleşme Form Component -->
    <div class="neo-content p-6" x-data="eslesmeForm()">

        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Yeni Eşleştirme Oluştur</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Müşteri talepleri ile uygun ilanları eşleştirin</p>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <form method="POST" action="{{ route('admin.eslesmeler.store') }}" class="space-y-6" @submit="loading = true">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sol Kolon -->
                <div class="space-y-6">
                    <!-- Müşteri Seçimi -->
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-header">
                            <h3 class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-title">👤 Müşteri Bilgileri</h3>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-body space-y-4">
                            <!-- Context7 Live Search: Müşteri -->
                            <div class="space-y-2 relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 required">Müşteri</label>
                                <div class="context7-live-search"
                                     data-endpoint="/api/admin/kisiler/search"
                                     data-target-input="kisi_id"
                                     data-placeholder="Ad, soyad veya telefon ile ara..."
                                     data-min-chars="2">
                                    <input type="text" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Müşteri ara...">
                                    <input type="hidden" name="kisi_id" id="kisi_id" x-model="form.kisi_id" required>
                                </div>
                                <button type="button" @click="clearKisi()"
                                    x-show="form.kisi_id"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-sm mt-2">
                                    🗑️ Temizle
                                </button>
                                @error('kisi_id')
                                    <p class="text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Danışman Seçimi -->
                            <div class="space-y-2 relative">
                                <label for="danisman_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Danışman</label>
                                <select style="color-scheme: light dark;" name="danisman_id" id="danisman_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200" x-model="form.danisman_id">
                                    <option value="">Danışman seçin...</option>
                                    @foreach($danismanlar ?? [] as $danisman)
                                        <option value="{{ $danisman->id }}">{{ $danisman->name }}</option>
                                    @endforeach
                                </select>
                                @error('danisman_id')
                                    <p class="text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Talep Seçimi -->
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-header">
                            <h3 class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-title">🎯 Talep Bilgileri</h3>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-body">
                            <!-- Context7 Live Search: Talep -->
                            <div class="space-y-2 relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Talep (İsteğe Bağlı)</label>
                                <div class="context7-live-search"
                                     data-endpoint="/api/admin/talepler/search"
                                     data-target-input="talep_id"
                                     data-placeholder="Talep başlığı veya lokasyon ile ara..."
                                     data-min-chars="2">
                                    <input type="text" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Talep ara...">
                                    <input type="hidden" name="talep_id" id="talep_id" x-model="form.talep_id">
                                </div>
                                <button type="button" @click="clearTalep()"
                                    x-show="form.talep_id"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-sm mt-2">
                                    🗑️ Temizle
                                </button>
                                @error('talep_id')
                                    <p class="text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon -->
                <div class="space-y-6">
                    <!-- İlan Seçimi -->
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-header">
                            <h3 class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-title">🏠 İlan Bilgileri</h3>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-body">
                            <!-- Context7 Live Search: İlan -->
                            <div class="space-y-2 relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 required">İlan</label>
                                <div class="context7-live-search"
                                     data-endpoint="/api/admin/ilanlar/search"
                                     data-target-input="ilan_id"
                                     data-placeholder="İlan başlığı veya lokasyon ile ara..."
                                     data-min-chars="2">
                                    <input type="text" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="İlan ara...">
                                    <input type="hidden" name="ilan_id" id="ilan_id" x-model="form.ilan_id" required>
                                </div>
                                <button type="button" @click="clearIlan()"
                                    x-show="form.ilan_id"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-sm mt-2">
                                    🗑️ Temizle
                                </button>
                                @error('ilan_id')
                                    <p class="text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Eşleştirme Detayları -->
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-header">
                            <h3 class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-title">⚙️ Eşleştirme Detayları</h3>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-body space-y-4">
                            <div class="space-y-2 relative">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 required">Durum</label>
                                <select style="color-scheme: light dark;" name="status" id="status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200" required x-model="form.status">
                                    <option value="">Durum seçin...</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Beklemede">Beklemede</option>
                                    <option value="İptal">İptal</option>
                                    <option value="Tamamlandı">Tamamlandı</option>
                                </select>
                                @error('status')
                                    <p class="text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2 relative">
                                <label class="w-5 h-5 text-blue-600 bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer-wrapper">
                                    <input type="checkbox" name="one_cikan" value="1" class="w-5 h-5 text-blue-600 bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer" x-model="form.one_cikan">
                                    <span class="w-5 h-5 text-blue-600 bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer-label">Öne Çıkan Eşleştirme</span>
                                </label>
                            </div>

                            <div class="space-y-2 relative">
                                <label for="eslesme_tarihi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Eşleştirme Tarihi</label>
                                <input type="datetime-local" name="eslesme_tarihi" id="eslesme_tarihi" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                                    x-model="form.eslesme_tarihi">
                                @error('eslesme_tarihi')
                                    <p class="text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notlar Bölümü -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-header">
                    <h3 class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-title">📝 Notlar ve Açıklamalar</h3>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-body">
                    <div class="space-y-2 relative">
                        <label for="notlar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Eşleştirme Notları</label>
                        <textarea name="notlar" id="notlar" rows="4" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 resize-vertical" x-model="form.notlar"
                            placeholder="Bu eşleştirme hakkında notlarınızı buraya yazabilirsiniz..."></textarea>
                        @error('notlar')
                            <p class="text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" :disabled="loading"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50" aria-label="Eşleştirmeyi kaydet">
                    <svg x-show="!loading" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg x-show="loading" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!loading">Eşleştirmeyi Kaydet</span>
                    <span x-show="loading">Kaydediliyor...</span>
                </button>

                <button type="button" @click="resetForm()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Formu Temizle
                </button>

                <a href="{{ route('admin.eslesmeler.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Geri Dön
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <!-- Context7 Live Search System -->
    <script src="{{ asset('js/context7-live-search.js') }}"></script>

    <!-- Context7: Eşleşme Form Logic (Vanilla JS + Alpine.js) -->
    <script>
        function eslesmeForm() {
            return {
                loading: false,
                form: {
                    kisi_id: '',
                    ilan_id: '',
                    talep_id: '',
                    danisman_id: '',
                    status: 'Aktif',
                    one_cikan: false,
                    eslesme_tarihi: '{{ now()->format("Y-m-d\TH:i") }}',
                    notlar: ''
                },

                init() {
                    console.log('✅ Eşleşme Create Form initialized (Context7)');
                    // Context7 Live Search otomatik başlar
                },

                clearKisi() {
                    this.form.kisi_id = '';
                    const searchInput = document.querySelector('[data-target-input="kisi_id"] input[type="text"]');
                    if (searchInput) {
                        searchInput.value = '';
                    }
                },

                clearIlan() {
                    this.form.ilan_id = '';
                    const searchInput = document.querySelector('[data-target-input="ilan_id"] input[type="text"]');
                    if (searchInput) {
                        searchInput.value = '';
                    }
                },

                clearTalep() {
                    this.form.talep_id = '';
                    const searchInput = document.querySelector('[data-target-input="talep_id"] input[type="text"]');
                    if (searchInput) {
                        searchInput.value = '';
                    }
                },

                resetForm() {
                    if (confirm('Formu temizlemek istediğinizden emin misiniz?')) {
                        this.form = {
                            kisi_id: '',
                            ilan_id: '',
                            talep_id: '',
                            danisman_id: '',
                            status: 'Aktif',
                            one_cikan: false,
                            eslesme_tarihi: '{{ now()->format("Y-m-d\TH:i") }}',
                            notlar: ''
                        };

                        // Tüm live search inputlarını temizle
                        document.querySelectorAll('.context7-live-search input[type="text"]').forEach(input => {
                            input.value = '';
                        });

                        console.log('🔄 Form temizlendi');
                    }
                }
            }
        }

        // Context7: Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ Eşleşme Create Page loaded (Context7 Vanilla JS)');
        });
    </script>
@endpush

@push('styles')
    <style>
        .neo-checkbox-wrapper {
            @apply flex items-center gap-2 cursor-pointer;
        }

        .neo-checkbox {
            @apply w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2;
        }

        .neo-checkbox-label {
            @apply text-sm font-medium text-gray-900 dark:text-white;
        }

        .required::after {
            content: ' *';
            @apply text-red-500;
        }
    </style>
@endpush
