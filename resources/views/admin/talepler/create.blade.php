@extends('admin.layouts.admin')

@section('title', 'Yeni Talep Oluştur')
@section('meta_description',
    'Yeni müşteri talebi oluşturun - Emlak talebi formu, kişi bilgileri, lokasyon seçimi ve
    talep detayları')
@section('meta_keywords', 'yeni talep, müşteri talebi, emlak talebi, talep formu, yalıhan emlak')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="talepForm()">
        <!-- Page Header - Neo Design System -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <nav class="flex items-center space-x-2 text-sm mb-3">
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </a>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <a href="{{ route('admin.talepler.index') }}"
                            class="text-gray-500 hover:text-orange-600 dark:text-gray-400 dark:hover:text-orange-400 transition-colors font-medium">Talepler</a>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="text-orange-600 dark:text-orange-400 font-semibold">Yeni Talep</span>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Yeni Talep Oluştur</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Müşteri talebi bilgilerini girin ve sisteme kaydedin
                    </p>
                </div>
                <a href="{{ route('admin.talepler.index') }}"
                    class="inline-flex items-center px-4 py-2.5
                          bg-gray-200 text-gray-700 font-medium
                          rounded-lg shadow-sm
                          hover:bg-gray-300 hover:scale-105 hover:shadow-md
                          active:scale-95
                          focus:ring-2 focus:ring-gray-400 focus:ring-offset-2
                          transition-all duration-200 ease-in-out
                          dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Geri Dön
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.talepler.store') }}" class="space-y-6" x-ref="form"
            @submit="loading = true; submitBtn = $el.querySelector('#talep-submit-btn'); if(submitBtn) { submitBtn.disabled = true; submitBtn.querySelector('#talep-submit-icon').classList.add('hidden'); submitBtn.querySelector('#talep-submit-spinner').classList.remove('hidden'); submitBtn.querySelector('#talep-submit-text').textContent = 'Kaydediliyor...'; }">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sol Kolon - Temel Bilgiler -->
                <div class="space-y-6">
                    <!-- Temel Bilgiler Card - Tailwind CSS + Transitions -->
                        class="bg-gray-50 dark:bg-gray-800
                                rounded-lg shadow-sm
                                border border-gray-200 dark:border-gray-700
                                transition-all duration-300 ease-in-out
                                hover:shadow-md hover:-translate-y-1">
                        <div
                            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700
                                    bg-gradient-to-r from-blue-50 to-indigo-50
                                    dark:from-gray-800 dark:to-gray-700
                                    rounded-t-lg">
                            <h2
                                class="text-xl font-bold text-gray-900 dark:text-white
                                       flex items-center">
                                <svg class="w-6 h-6 mr-3 text-blue-600 dark:text-blue-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Temel Bilgiler
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Talep başlığı ve detayları</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Talep Başlığı -->
                            <div class="mb-6">
                                <label for="baslik"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-2
                                              transition-colors duration-200">
                                    Talep Başlığı
                                    <span class="text-red-500 font-bold ml-1">*</span>
                                </label>
                                <input type="text" id="baslik" name="baslik"
                                    class="w-full px-4 py-2.5
                                              border @error('baslik') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror
                                              rounded-lg
                                              bg-gray-50 dark:bg-gray-800
                                              text-gray-900 dark:text-white
                                              placeholder-gray-500 dark:placeholder-gray-400
                                              focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                              transition-all duration-200 ease-in-out
                                              hover:border-blue-400
                                              disabled:bg-gray-100 dark:disabled:bg-gray-700
                                              disabled:cursor-not-allowed"
                                    value="{{ old('baslik') }}" placeholder="Örn: 3+1 Daire Aranıyor - Merkez" required
                                    x-model="form.baslik">
                                @error('baslik')
                                    <div
                                        class="mt-2 text-sm text-red-600 dark:text-red-400
                                                flex items-start gap-1
                                                animate-shake">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Açıklama -->
                            <div class="mb-6">
                                <label for="aciklama"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-2
                                              transition-colors duration-200">
                                    Açıklama
                                </label>
                                <textarea id="aciklama" name="aciklama"
                                    class="w-full px-4 py-2.5
                                                 border @error('aciklama') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror
                                                 rounded-lg
                                                 bg-gray-50 dark:bg-gray-800
                                                 text-gray-900 dark:text-white
                                                 placeholder-gray-500 dark:placeholder-gray-400
                                                 focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                                 transition-all duration-200 ease-in-out
                                                 hover:border-blue-400
                                                 resize-y"
                                    rows="4" placeholder="Talep detaylarını açıklayın..." x-model="form.aciklama">{{ old('aciklama') }}</textarea>
                                @error('aciklama')
                                    <div class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-start gap-1">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Talep Tipi ve Kategoriler Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <!-- Talep Tipi -->
                                <div>
                                    <label for="tip"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Talep Tipi <span class="text-red-500 font-bold ml-1">*</span>
                                    </label>
                                    <select id="tip" name="tip"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200 cursor-pointer hover:border-blue-400"
                                        style="color-scheme: light dark;" required x-model="form.tip">
                                        <option value=""
                                            class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 py-2">
                                            Seçiniz</option>
                                        <option value="Satılık"
                                            class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 font-medium">
                                            Satılık</option>
                                        <option value="Kiralık"
                                            class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 font-medium">
                                            Kiralık</option>
                                        <option value="Günlük Kiralık"
                                            class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 font-medium">
                                            Günlük Kiralık</option>
                                        <option value="Devren"
                                            class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 font-medium">
                                            Devren</option>
                                    </select>
                                </div>

                                <!-- Ana Kategori -->
                                <div>
                                    <label for="kategori_id"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Ana Kategori
                                    </label>
                                    <select id="kategori_id" name="kategori_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200 cursor-pointer hover:border-blue-400"
                                        style="color-scheme: light dark;" x-model="form.kategori_id"
                                        @change="loadAltKategoriler()">
                                        <option value=""
                                            class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 py-2">Ana
                                            Kategori Seçiniz</option>
                                        @foreach ($kategoriler ?? [] as $kategori)
                                            <option value="{{ $kategori->id }}"
                                                class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 font-medium">
                                                {{ $kategori->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Alt Kategori -->
                                <div>
                                    <label for="alt_kategori_id"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Alt Kategori
                                    </label>
                                    <select id="alt_kategori_id" name="alt_kategori_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200 cursor-pointer hover:border-blue-400 disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:cursor-not-allowed"
                                        style="color-scheme: light dark;" x-model="form.alt_kategori_id"
                                        :disabled="!form.kategori_id">
                                        <option value=""
                                            class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 py-2">Alt
                                            Kategori Seçiniz</option>
                                        <template x-for="alt in altKategoriler" :key="alt.id">
                                            <option :value="alt.id" x-text="alt.name"
                                                class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 font-medium">
                                            </option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- Status ve Checkbox Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Durum -->
                                <div>
                                    <label for="status"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2
                                                  transition-colors duration-200">
                                        Durum <span class="text-red-500 font-bold ml-1">*</span>
                                    </label>
                                    <select style="color-scheme: light dark;" id="status" name="status"
                                        class="w-full px-4 py-2.5
                                                   border @error('status') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror
                                                   rounded-lg
                                                   bg-gray-50 dark:bg-gray-800
                                                   text-gray-900 dark:text-white
                                                   focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                                   transition-all duration-200 ease-in-out
                                                   cursor-pointer hover:border-blue-400"
                                        required x-model="form.status">
                                        <option value="">Seçiniz</option>
                                        <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="Beklemede" {{ old('status') == 'Beklemede' ? 'selected' : '' }}>
                                            Beklemede</option>
                                        <option value="İptal" {{ old('status') == 'İptal' ? 'selected' : '' }}>İptal
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-start gap-1">
                                            <svg class="w-4 h-4 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Öne Çıkan -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2
                                                  transition-colors duration-200">
                                        Öne Çıkan
                                    </label>
                                    <div class="flex items-center h-11">
                                        <input type="hidden" name="one_cikan" value="0">
                                        <input type="checkbox" id="one_cikan" name="one_cikan" value="1"
                                            class="w-5 h-5
                                                      text-orange-600
                                                      border-gray-300 dark:border-gray-600
                                                      rounded
                                                      focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                                      transition-all duration-200
                                                      cursor-pointer
                                                      hover:scale-110"
                                            {{ old('one_cikan') ? 'checked' : '' }} x-model="form.one_cikan">
                                        <label for="one_cikan"
                                            class="ml-3 text-sm text-gray-900 dark:text-white
                                                      cursor-pointer select-none
                                                      hover:text-orange-600 dark:hover:text-orange-400
                                                      transition-colors duration-200">
                                            Bu talep öne çıkansın
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon - Lokasyon ve Kişi Bilgileri -->
                <div class="space-y-6">
                    <!-- 📍 Lokasyon Bilgileri -->
                    <div
                        class="bg-gray-50 dark:bg-gray-800
                                rounded-lg shadow-sm
                                border border-gray-200 dark:border-gray-600
                                transition-all duration-300 ease-in-out
                                hover:shadow-md hover:-translate-y-1">

                        <!-- Card Header -->
                        <div
                            class="px-6 py-4 border-b border-gray-200 dark:border-gray-600
                                    bg-gradient-to-r from-green-50 via-teal-50 to-emerald-50
                                    dark:from-gray-700 dark:via-gray-700 dark:to-gray-700
                                    rounded-t-lg">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                    <div
                                        class="w-10 h-10 bg-green-500 dark:bg-green-600 rounded-lg flex items-center justify-center mr-3
                                                shadow-lg transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-gray-900 dark:text-white">Lokasyon Bilgileri</span>
                                </h2>
                                <span
                                    class="px-3 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">
                                    İl / İlçe / Mahalle
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-b-lg">
                            <div class="space-y-6">
                                <!-- İl ve İlçe Row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- İl -->
                                    <div class="space-y-2">
                                        <label for="il_id"
                                            class="block text-sm font-medium text-gray-800 dark:text-gray-100 mb-2
                                                                 flex items-center gap-2">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                İl
                                            </span>
                                            <span
                                                class="text-red-600 dark:text-red-400 font-extrabold text-base ml-1">*</span>
                                        </label>
                                        <select id="il_id" name="il_id"
                                            class="w-full px-4 py-2.5
                                                       border @error('il_id') border-red-500 dark:border-red-400 @else border-gray-300 dark:border-gray-500 @enderror
                                                       rounded-lg
                                                       bg-gray-50 dark:bg-gray-800
                                                       text-gray-900 dark:text-white
                                                       font-medium
                                                       placeholder-gray-500 dark:placeholder-gray-400
                                                       focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/50 focus:border-green-500
                                                       hover:border-green-400 dark:hover:border-green-500
                                                       transition-all duration-200 ease-in-out
                                                       cursor-pointer
                                                       shadow-sm hover:shadow-md"
                                            style="color-scheme: light dark;" required x-model="form.il_id"
                                            @change="loadIlceler()">
                                            <option value=""
                                                class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 py-2">
                                                -- İl Seçiniz --</option>
                                            @foreach ($iller ?? [] as $il)
                                                <option value="{{ $il->id }}"
                                                    class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 font-medium">
                                                    {{ $il->il_adi }}</option>
                                            @endforeach
                                        </select>
                                        @error('il_id')
                                            <div
                                                class="mt-2 px-4 py-2.5 text-sm font-medium
                                                        text-red-700 dark:text-red-300
                                                        bg-red-50 dark:bg-red-900/30
                                                        border border-red-200 dark:border-red-800
                                                        rounded-lg
                                                        flex items-start gap-2">
                                                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span>{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- İlçe -->
                                    <div class="space-y-2">
                                        <label for="ilce_id"
                                            class="block text-sm font-medium text-gray-800 dark:text-gray-100 mb-2
                                                                     flex items-center gap-2">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 10a2 2 0 114 0 2 2 0 01-4 0z" />
                                                </svg>
                                                İlçe
                                            </span>
                                        </label>
                                        <select id="ilce_id" name="ilce_id"
                                            class="w-full px-4 py-2.5
                                                       border border-gray-300 dark:border-gray-500
                                                       rounded-lg
                                                       bg-gray-50 dark:bg-gray-800
                                                       text-gray-900 dark:text-white
                                                       font-medium
                                                       placeholder-gray-500 dark:placeholder-gray-400
                                                       focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/50 focus:border-green-500
                                                       hover:border-green-400 dark:hover:border-green-500
                                                       disabled:bg-gray-100 dark:disabled:bg-gray-700
                                                       disabled:border-gray-200 dark:disabled:border-gray-600
                                                       disabled:text-gray-400 dark:disabled:text-gray-500
                                                       disabled:cursor-not-allowed
                                                       transition-all duration-200 ease-in-out
                                                       cursor-pointer
                                                       shadow-sm hover:shadow-md"
                                            style="color-scheme: light dark;" x-model="form.ilce_id"
                                            :disabled="!form.il_id" @change="loadMahalleler()">
                                            <option value=""
                                                class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 py-2">
                                                -- İlçe Seçiniz --</option>
                                            <template x-for="ilce in ilceler" :key="ilce.id || ilce.temp_id">
                                                <option :value="ilce.id || ''"
                                                    x-text="(ilce.ilce || ilce.ilce_adi || ilce.name) + (ilce._from_turkiyeapi ? ' (TurkiyeAPI)' : '')"
                                                    class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 font-medium">
                                                </option>
                                            </template>
                                        </select>
                                        @error('ilce_id')
                                            <div
                                                class="mt-2 px-4 py-2.5 text-sm font-medium
                                                        text-red-700 dark:text-red-300
                                                        bg-red-50 dark:bg-red-900/30
                                                        border border-red-200 dark:border-red-800
                                                        rounded-lg
                                                        flex items-start gap-2">
                                                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span>{{ $message }}</span>
                                            </div>
                                        @enderror
                                        <div x-show="!form.il_id"
                                            class="mt-2 px-4 py-2.5 text-xs font-medium
                                                    text-gray-600 dark:text-gray-400
                                                    bg-gray-100 dark:bg-gray-800
                                                    border border-gray-200 dark:border-gray-700
                                                    rounded-lg flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>Önce <strong>İl</strong> seçmelisiniz</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mahalle alanı -->
                                <div class="space-y-2">
                                    <label for="mahalle_id"
                                        class="block text-sm font-medium text-gray-800 dark:text-gray-100 mb-2
                                                                 flex items-center gap-2">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                            </svg>
                                            Mahalle
                                        </span>
                                    </label>
                                    <select id="mahalle_id" name="mahalle_id"
                                        class="w-full px-4 py-2.5
                                                   border border-gray-300 dark:border-gray-500
                                                   rounded-lg
                                                   bg-gray-50 dark:bg-gray-800
                                                   text-gray-900 dark:text-white
                                                   font-medium
                                                   placeholder-gray-500 dark:placeholder-gray-400
                                                   focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/50 focus:border-green-500
                                                   hover:border-green-400 dark:hover:border-green-500
                                                   disabled:bg-gray-100 dark:disabled:bg-gray-700
                                                   disabled:border-gray-200 dark:disabled:border-gray-600
                                                   disabled:text-gray-400 dark:disabled:text-gray-500
                                                   disabled:cursor-not-allowed
                                                   transition-all duration-200 ease-in-out
                                                   cursor-pointer
                                                   shadow-sm hover:shadow-md"
                                        style="color-scheme: light dark;" x-model="form.mahalle_id"
                                        :disabled="!form.ilce_id">
                                        <option value=""
                                            class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 py-2">--
                                            Mahalle Seçiniz --</option>
                                        <template x-for="mahalle in mahalleler" :key="mahalle.id || mahalle.temp_id">
                                            <option :value="mahalle.id || ''"
                                                x-text="(mahalle.mahalle || mahalle.mahalle_adi || mahalle.name) + (mahalle._from_turkiyeapi ? ' (TurkiyeAPI)' : '')"
                                                class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 font-medium">
                                            </option>
                                        </template>
                                    </select>
                                    @error('mahalle_id')
                                        <div
                                            class="mt-2 px-4 py-2.5 text-sm font-medium
                                                    text-red-700 dark:text-red-300
                                                    bg-red-50 dark:bg-red-900/30
                                                    border border-red-200 dark:border-red-800
                                                    rounded-lg
                                                    flex items-start gap-2">
                                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                    <div x-show="!form.ilce_id"
                                        class="mt-2 px-4 py-2.5 text-xs font-medium
                                                text-gray-600 dark:text-gray-400
                                                bg-gray-100 dark:bg-gray-800
                                                border border-gray-200 dark:border-gray-700
                                                rounded-lg flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Önce <strong>İlçe</strong> seçmelisiniz</span>
                                    </div>
                                    <div
                                        class="mt-2 px-4 py-2.5 text-xs font-medium
                                                text-blue-700 dark:text-blue-300
                                                bg-blue-50 dark:bg-blue-900/30
                                                border border-blue-200 dark:border-blue-800
                                                rounded-lg flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 text-blue-600 dark:text-blue-400 flex-shrink-0"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span><strong>Bilgi:</strong> Mahalle verileri veritabanından dinamik olarak
                                            yüklenir</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kişi Bilgileri - Tailwind CSS + Transitions -->
                    <div
                        class="bg-gray-50 dark:bg-gray-800
                                rounded-lg shadow-sm
                                border border-gray-200 dark:border-gray-700
                                transition-all duration-300 ease-in-out
                                hover:shadow-md hover:-translate-y-1">
                        <div
                            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700
                                    bg-gradient-to-r from-purple-50 to-pink-50
                                    dark:from-gray-800 dark:to-gray-700
                                    rounded-t-lg">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-6 h-6 mr-3 text-purple-600 dark:text-purple-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Kişi Bilgileri
                            </h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Kişi Seç (Live Search) -->
                            <div class="mb-6 space-y-2">
                                <label for="kisi_id"
                                    class="block text-sm font-medium text-gray-800 dark:text-gray-100 mb-2
                                                             flex items-center gap-2">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z" />
                                        </svg>
                                        Kişi Seç (Canlı Arama)
                                    </span>
                                </label>
                                <!-- CRITICAL FIX: relative position for absolute dropdown -->
                                <div class="context7-live-search relative" data-search-type="kisiler"
                                    data-max-results="20">
                                    <input type="hidden" id="kisi_id" name="kisi_id" x-model="form.kisi_id">
                                    <input type="text"
                                        class="w-full px-4 py-2.5
                                                  border border-gray-300 dark:border-gray-500
                                                  rounded-lg
                                                  bg-gray-50 dark:bg-gray-800
                                                  text-gray-900 dark:text-white
                                                  font-medium
                                                  placeholder-gray-500 dark:placeholder-gray-400
                                                  focus:ring-4 focus:ring-purple-500/50 focus:border-purple-500
                                                  hover:border-purple-400 dark:hover:border-purple-500
                                                  transition-all duration-200 ease-in-out
                                                  shadow-sm hover:shadow-md"
                                        placeholder="🔍 Ad, soyad, telefon ile ara..." autocomplete="off">
                                    <!-- Dropdown Results - Absolute positioned -->
                                    <div
                                        class="context7-search-results
                                                absolute z-[9999] w-full mt-1
                                                bg-gray-50 dark:bg-gray-800
                                                border border-purple-300 dark:border-purple-700
                                                rounded-lg shadow-2xl
                                                hidden max-h-96 overflow-y-auto
                                                animate-fadeIn">
                                    </div>
                                </div>
                                <!-- Info Badge -->
                                <div
                                    class="mt-2 px-4 py-2.5 text-xs font-medium
                                            text-purple-700 dark:text-purple-300
                                            bg-purple-50 dark:bg-purple-900/30
                                            border border-purple-200 dark:border-purple-800
                                            rounded-lg flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 text-purple-600 dark:text-purple-400 flex-shrink-0"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Yazmaya başlayın, sonuçlar anlık görünecek</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 mt-3">
                                <button type="button"
                                    class="inline-flex items-center px-4 py-2.5 text-sm
                                                   bg-purple-100 text-purple-700 font-medium
                                                   rounded-lg
                                                   hover:bg-purple-200 hover:scale-105
                                                   active:scale-95
                                                   focus:ring-2 focus:ring-purple-500
                                                   transition-all duration-200
                                                   dark:bg-purple-900 dark:text-purple-200 dark:hover:bg-purple-800"
                                    @click="showNewKisiForm = !showNewKisiForm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Yeni Kişi Ekle
                                </button>
                                <button type="button"
                                    class="inline-flex items-center px-4 py-2.5 text-sm
                                                   border border-gray-300 text-gray-700 font-medium
                                                   rounded-lg
                                                   hover:bg-gray-50 hover:border-gray-400 hover:scale-105
                                                   active:scale-95
                                                   focus:ring-2 focus:ring-gray-400
                                                   transition-all duration-200
                                                   dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-800"
                                    x-show="form.kisi_id" @click="clearKisi()">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Temizle
                                </button>
                            </div>

                            @error('kisi_id')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-start gap-1">
                                    <svg class="w-4 h-4 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Yeni Kişi Formu (Collapsible) -->
                        <div x-show="showNewKisiForm" x-collapse
                            class="mt-6 p-6
                                        bg-purple-50 dark:bg-gray-800
                                        border border-purple-200 dark:border-purple-800
                                        rounded-lg
                                        transition-all duration-500">
                            <h4 class="text-lg font-bold text-purple-900 dark:text-purple-200 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Yeni Kişi Bilgileri
                            </h4>

                            <!-- Ad ve Soyad Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="kisi_ad"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Ad
                                    </label>
                                    <input type="text" id="kisi_ad" name="kisi_ad"
                                        class="w-full px-4 py-2.5
                                                      border border-gray-300 dark:border-gray-600
                                                      rounded-lg
                                                      bg-gray-50 dark:bg-gray-800
                                                      text-gray-900 dark:text-white
                                                      placeholder-gray-400
                                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                                      transition-all duration-200"
                                        value="{{ old('kisi_ad') }}" placeholder="Ad" x-model="form.kisi_ad">
                                </div>

                                <div>
                                    <label for="kisi_soyad"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Soyad
                                    </label>
                                    <input type="text" id="kisi_soyad" name="kisi_soyad"
                                        class="w-full px-4 py-2.5
                                                      border border-gray-300 dark:border-gray-600
                                                      rounded-lg
                                                      bg-gray-50 dark:bg-gray-800
                                                      text-gray-900 dark:text-white
                                                      placeholder-gray-400
                                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                                      transition-all duration-200"
                                        value="{{ old('kisi_soyad') }}" placeholder="Soyad" x-model="form.kisi_soyad">
                                </div>
                            </div>

                            <!-- Telefon ve Email Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="kisi_telefon"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Telefon
                                    </label>
                                    <input type="tel" id="kisi_telefon" name="kisi_telefon"
                                        class="w-full px-4 py-2.5
                                                      border border-gray-300 dark:border-gray-600
                                                      rounded-lg
                                                      bg-gray-50 dark:bg-gray-800
                                                      text-gray-900 dark:text-white
                                                      placeholder-gray-400
                                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                                      transition-all duration-200"
                                        value="{{ old('kisi_telefon') }}" placeholder="0xxx xxx xx xx"
                                        x-model="form.kisi_telefon">
                                </div>

                                <div>
                                    <label for="kisi_email"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        E-posta
                                    </label>
                                    <input type="email" id="kisi_email" name="kisi_email"
                                        class="w-full px-4 py-2.5
                                                      border border-gray-300 dark:border-gray-600
                                                      rounded-lg
                                                      bg-gray-50 dark:bg-gray-800
                                                      text-gray-900 dark:text-white
                                                      placeholder-gray-400
                                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                                      transition-all duration-200"
                                        value="{{ old('kisi_email') }}" placeholder="ornek@email.com"
                                        x-model="form.kisi_email">
                                </div>
                            </div>
                        </div>

                        <!-- Sorumlu Danışman -->
                        <div class="mb-6">
                            <label for="danisman_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Sorumlu Danışman
                            </label>
                            <select style="color-scheme: light dark;" id="danisman_id" name="danisman_id"
                                class="w-full px-4 py-2.5
                                               border border-gray-300 dark:border-gray-600
                                               rounded-lg
                                               bg-gray-50 dark:bg-gray-800
                                               text-gray-900 dark:text-white
                                               focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                               transition-all duration-200 ease-in-out
                                               cursor-pointer hover:border-purple-400"
                                x-model="form.danisman_id">
                                <option value="">Danışman Seçiniz</option>
                                @foreach ($danismanlar ?? [] as $danisman)
                                    <option value="{{ $danisman->id }}">{{ $danisman->name }}</option>
                                @endforeach
                            </select>
                            @error('danisman_id')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-start gap-1">
                                    <svg class="w-4 h-4 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- 🤖 AI ASSISTANT SECTION - Tailwind CSS + Transitions -->
    <div class="mt-8 space-y-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center
                                    shadow-lg transform hover:scale-110 transition-all duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">AI Yardımcı</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Akıllı öneriler ve analiz</p>
                </div>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-green-100 dark:bg-green-900 rounded-lg">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-sm font-medium text-green-800 dark:text-green-200">AI Aktif</span>
            </div>
        </div>

        <!-- AI Widget Grid - INTEGRATED with parent form -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- 1. AI Talep Analizi -->
            <div
                class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700
                                rounded-xl border-2 border-blue-200 dark:border-blue-800
                                shadow-lg hover:shadow-xl
                                transform hover:-translate-y-1
                                transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Talep Analizi</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">AI değerlendirme</p>
                            </div>
                        </div>
                        <button @click="analyzeRequest()" :disabled="aiLoading.analysis"
                            class="px-4 py-2
                                               bg-blue-600 text-white text-sm font-medium
                                               rounded-lg shadow
                                               hover:bg-blue-700 hover:scale-105
                                               active:scale-95
                                               disabled:opacity-50 disabled:cursor-not-allowed
                                               transition-all duration-200">
                            <span x-show="!aiLoading.analysis">Analiz Et</span>
                            <span x-show="aiLoading.analysis" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Analiz...
                            </span>
                        </button>
                    </div>
                    <div x-show="aiResults.analysis" x-transition
                        class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-blue-200 dark:border-blue-700">
                        <div x-html="aiResults.analysis"
                            class="text-sm text-gray-900 dark:text-white prose prose-sm max-w-none"></div>
                    </div>
                    <div x-show="!aiResults.analysis && !aiLoading.analysis"
                        class="mt-4 p-4 bg-blue-100 dark:bg-blue-900 rounded-lg text-center">
                        <p class="text-sm text-blue-800 dark:text-blue-200">Form doldurun, AI analiz etsin! 🎯</p>
                    </div>
                </div>
            </div>

            <!-- 2. AI Fiyat Önerisi -->
            <div
                class="bg-gradient-to-br from-green-50 to-teal-50 dark:from-gray-800 dark:to-gray-700
                                rounded-xl border-2 border-green-200 dark:border-green-800
                                shadow-lg hover:shadow-xl
                                transform hover:-translate-y-1
                                transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Fiyat Önerisi</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Pazar analizi</p>
                            </div>
                        </div>
                        <button @click="suggestPrice()" :disabled="aiLoading.price || !form.il_id"
                            class="px-4 py-2
                                               bg-green-600 text-white text-sm font-medium
                                               rounded-lg shadow
                                               hover:bg-green-700 hover:scale-105
                                               active:scale-95
                                               disabled:opacity-50 disabled:cursor-not-allowed
                                               transition-all duration-200">
                            <span x-show="!aiLoading.price">Fiyat Öner</span>
                            <span x-show="aiLoading.price" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Hesap...
                            </span>
                        </button>
                    </div>
                    <div x-show="aiResults.price" x-transition
                        class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-green-200 dark:border-green-700">
                        <div x-html="aiResults.price" class="text-sm text-gray-900 dark:text-white"></div>
                    </div>
                    <div x-show="!aiResults.price && !aiLoading.price"
                        class="mt-4 p-4 bg-green-100 dark:bg-green-900 rounded-lg text-center">
                        <p class="text-sm text-green-800 dark:text-green-200">Lokasyon seçin, AI fiyat önersin! 💰</p>
                    </div>
                </div>
            </div>

            <!-- 3. AI İlan Eşleştirme -->
            <div
                class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-800 dark:to-gray-700
                                rounded-xl border-2 border-purple-200 dark:border-purple-800
                                shadow-lg hover:shadow-xl
                                transform hover:-translate-y-1
                                transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">İlan Eşleştirme</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Uygun ilanlar</p>
                            </div>
                        </div>
                        <button @click="findMatches()" :disabled="aiLoading.matches || !form.kategori_id"
                            class="px-4 py-2
                                               bg-purple-600 text-white text-sm font-medium
                                               rounded-lg shadow
                                               hover:bg-purple-700 hover:scale-105
                                               active:scale-95
                                               disabled:opacity-50 disabled:cursor-not-allowed
                                               transition-all duration-200">
                            <span x-show="!aiLoading.matches">Eşleştir</span>
                            <span x-show="aiLoading.matches" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Aranıyor...
                            </span>
                        </button>
                    </div>
                    <div x-show="aiResults.matches && aiResults.matches.length > 0" x-transition class="mt-4 space-y-2">
                        <template x-for="match in aiResults.matches" :key="match.id">
                            <div
                                class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-purple-200 dark:border-purple-700
                                                hover:shadow-md transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white"
                                            x-text="match.title"></h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400" x-text="match.location"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-purple-600 dark:text-purple-400"
                                            x-text="match.price"></p>
                                        <div class="flex items-center gap-1 mt-1">
                                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="text-xs font-medium text-gray-900 dark:text-white"
                                                x-text="`${Math.round(match.score * 100)}%`"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="!aiResults.matches || aiResults.matches.length === 0" x-show="!aiLoading.matches"
                        class="mt-4 p-4 bg-purple-100 dark:bg-purple-900 rounded-lg text-center">
                        <p class="text-sm text-purple-800 dark:text-purple-200">Kategori seçin, AI uygun ilanları bulsun!
                            🏠</p>
                    </div>
                </div>
            </div>

            <!-- 4. AI Akıllı Açıklama -->
            <div
                class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-gray-800 dark:to-gray-700
                                rounded-xl border-2 border-orange-200 dark:border-orange-800
                                shadow-lg hover:shadow-xl
                                transform hover:-translate-y-1
                                transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Akıllı Açıklama</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">AI metin oluştur</p>
                            </div>
                        </div>
                        <button @click="generateDescription()" :disabled="aiLoading.description || !form.baslik"
                            class="px-4 py-2
                                               bg-orange-600 text-white text-sm font-medium
                                               rounded-lg shadow
                                               hover:bg-orange-700 hover:scale-105
                                               active:scale-95
                                               disabled:opacity-50 disabled:cursor-not-allowed
                                               transition-all duration-200">
                            <span x-show="!aiLoading.description">Oluştur</span>
                            <span x-show="aiLoading.description" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Yazıyor...
                            </span>
                        </button>
                    </div>
                    <div x-show="aiResults.description" x-transition
                        class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-orange-200 dark:border-orange-700">
                        <p x-text="aiResults.description" class="text-sm text-gray-900 dark:text-white leading-relaxed">
                        </p>
                        <button @click="applyDescription()"
                            class="mt-3 px-3 py-1.5
                                               bg-orange-600 text-white text-xs font-medium
                                               rounded-lg
                                               hover:bg-orange-700 hover:scale-105
                                               transition-all duration-200">
                            Açıklamaya Uygula ✨
                        </button>
                    </div>
                    <div x-show="!aiResults.description && !aiLoading.description"
                        class="mt-4 p-4 bg-orange-100 dark:bg-orange-900 rounded-lg text-center">
                        <p class="text-sm text-orange-800 dark:text-orange-200">Başlık girin, AI açıklama yazsın! ✍️</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Form Actions - Tailwind CSS + Transitions -->
    <div class="flex items-center justify-end gap-4 py-6">
        <button type="submit"
            class="inline-flex items-center px-6 py-3
                               bg-orange-600 text-white font-semibold
                               rounded-lg shadow-md
                               hover:bg-orange-700 hover:scale-105 hover:shadow-lg
                               active:scale-95
                               focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:ring-offset-2
                               transition-all duration-200 ease-in-out
                               disabled:opacity-50 disabled:cursor-not-allowed
                               dark:bg-orange-500 dark:hover:bg-orange-600"
            :disabled="loading" aria-label="Talebi kaydet">
            <span x-show="!loading" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Talebi Kaydet
            </span>
            <span x-show="loading" class="flex items-center">
                <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Kaydediliyor...
            </span>
        </button>

        <button type="button"
            class="inline-flex items-center px-6 py-3
                               bg-gray-200 text-gray-700 font-semibold
                               rounded-lg shadow-sm
                               hover:bg-gray-300 hover:scale-105
                               active:scale-95
                               focus:ring-2 focus:ring-gray-400 focus:ring-offset-2
                               transition-all duration-200 ease-in-out
                               dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-600"
            @click="resetForm()" aria-label="Formu temizle">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Temizle
        </button>

        <a href="{{ route('admin.talepler.index') }}"
            class="inline-flex items-center px-6 py-3
                          border-2 border-gray-300 text-gray-700 font-semibold
                          rounded-lg
                          hover:bg-gray-50 hover:border-gray-400 hover:scale-105
                          active:scale-95
                          focus:ring-2 focus:ring-gray-400 focus:ring-offset-2
                          transition-all duration-200 ease-in-out
                          dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:border-gray-500"
            aria-label="İptal et ve geri dön">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            İptal
        </a>
    </div>
    </form>
    </div>

    {{-- Form scripts (deferred for proper loading order) --}}
    @push('scripts')
        <script src="{{ asset('js/context7-live-search.js') }}"></script>
        <script>
            function talepForm() {
                return {
                    loading: false,
                    showNewKisiForm: false,
                    altKategoriler: [],
                    loadingAltKategoriler: false,
                    ilceler: [],
                    mahalleler: [],
                    loadingIlceler: false,
                    loadingMahalleler: false,
                    form: {
                        baslik: '{{ old('baslik') }}',
                        aciklama: '{{ old('aciklama') }}',
                        tip: '{{ old('tip') }}',
                        kategori_id: '{{ old('kategori_id') }}',
                        alt_kategori_id: '{{ old('alt_kategori_id') }}',
                        status: '{{ old('status', 'Aktif') }}',
                        one_cikan: {{ old('one_cikan') ? 'true' : 'false' }},
                        il_id: '{{ old('il_id') }}',
                        ilce_id: '{{ old('ilce_id') }}',
                        mahalle_id: '{{ old('mahalle_id') }}',
                        kisi_id: '{{ old('kisi_id') }}',
                        kisi_ad: '{{ old('kisi_ad') }}',
                        kisi_soyad: '{{ old('kisi_soyad') }}',
                        kisi_telefon: '{{ old('kisi_telefon') }}',
                        kisi_email: '{{ old('kisi_email') }}',
                        danisman_id: '{{ old('danisman_id') }}'
                    },
                    aiLoading: {
                        analysis: false,
                        price: false,
                        matches: false,
                        description: false
                    },
                    aiResults: {
                        analysis: '',
                        price: '',
                        matches: [],
                        description: ''
                    },

                    init() {
                        console.log('✅ Talep Create Form initialized (Context7)');
                        // Load ilçeler if il_id is pre-filled
                        if (this.form.il_id) {
                            this.loadIlceler();
                        }
                        // Load mahalleler if ilce_id is pre-filled
                        if (this.form.ilce_id) {
                            setTimeout(() => this.loadMahalleler(), 500);
                        }
                        // Load alt kategoriler if kategori_id is pre-filled
                        if (this.form.kategori_id) {
                            this.loadAltKategoriler();
                        }
                    },

                    async loadAltKategoriler() {
                        if (!this.form.kategori_id) {
                            this.altKategoriler = [];
                            this.form.alt_kategori_id = '';
                            this.loadingAltKategoriler = false;
                            return;
                        }

                        this.loadingAltKategoriler = true;
                        this.form.alt_kategori_id = ''; // Ana kategori değiştiğinde alt kategoriyi temizle

                        try {
                            // ✅ Context7: Standart API endpoint kullan
                            const response = await fetch(`/api/categories/sub/${this.form.kategori_id}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}`);
                            }

                            const result = await response.json();

                            // ✅ Context7: ResponseService format'ına uygun parse
                            // ResponseService format: {success: true, data: {subcategories: [...]}, message: "..."}
                            if (result.success) {
                                if (result.data) {
                                    // ResponseService format: data içinde subcategories var
                                    this.altKategoriler = result.data.subcategories || result.data.alt_kategoriler || (Array.isArray(result.data) ? result.data : []);
                                } else if (result.subcategories) {
                                    // Direct format (backward compatibility)
                                    this.altKategoriler = result.subcategories;
                                } else {
                                    this.altKategoriler = [];
                                }
                            } else {
                                this.altKategoriler = [];
                            }

                            // Kategori seçildiğinde alt kategori seçimini temizle
                            if (this.altKategoriler.length === 0) {
                                this.form.alt_kategori_id = '';
                                console.log('⚠️ Bu kategori için alt kategori bulunamadı');
                            } else {
                                console.log(`✅ ${this.altKategoriler.length} alt kategori yüklendi`);
                            }
                        } catch (error) {
                            console.error('❌ Alt kategoriler yüklenemedi:', error);
                            this.altKategoriler = [];
                            this.form.alt_kategori_id = '';
                            window.toast?.error('Alt kategoriler yüklenirken bir hata oluştu');
                        } finally {
                            this.loadingAltKategoriler = false;
                        }
                    },

                    // Context7 Location System - Load İlçeler (Otomatik TurkiyeAPI desteği)
                    async loadIlceler() {
                        if (!this.form.il_id) {
                            this.ilceler = [];
                            this.mahalleler = [];
                            this.form.ilce_id = '';
                            this.form.mahalle_id = '';
                            return;
                        }

                        this.loadingIlceler = true;
                        this.form.ilce_id = '';
                        this.form.mahalle_id = '';
                        this.mahalleler = [];

                        try {
                            // Context7: Sadece veritabanından veri çek
                            const response = await fetch(`/api/location/districts/${this.form.il_id}`);
                            const data = await response.json();
                            const dbIlceler = data.data || data.districts || [];

                            console.log(`✅ DB'den ${dbIlceler.length} ilçe yüklendi`);
                            this.ilceler = dbIlceler;

                            if (dbIlceler.length === 0) {
                                console.log('⚠️ DB\'de ilçe bulunamadı');
                                window.toast?.info('Bu il için ilçe bulunamadı');
                            }
                        } catch (error) {
                            console.error('❌ İlçeler yüklenemedi:', error);
                            this.ilceler = [];
                            window.toast?.error('İlçeler yüklenemedi');
                        } finally {
                            this.loadingIlceler = false;
                        }
                    },

                    // Context7: TurkiyeAPI kullanımı kaldırıldı - Sadece veritabanından veri çekiliyor

                    // Context7 Location System - Load Mahalleler (Otomatik TurkiyeAPI desteği)
                    async loadMahalleler() {
                        if (!this.form.ilce_id) {
                            this.mahalleler = [];
                            this.form.mahalle_id = '';
                            return;
                        }

                        this.loadingMahalleler = true;
                        this.form.mahalle_id = '';

                        try {
                            // Context7: Sadece veritabanından veri çek
                            const response = await fetch(`/api/location/neighborhoods/${this.form.ilce_id}`);
                            const data = await response.json();
                            const dbMahalleler = data.data || data.neighborhoods || [];

                            console.log(`✅ DB'den ${dbMahalleler.length} mahalle yüklendi`);
                            this.mahalleler = dbMahalleler;

                            if (dbMahalleler.length === 0) {
                                console.log('⚠️ DB\'de mahalle bulunamadı');
                                window.toast?.info('Bu ilçe için mahalle bulunamadı');
                            }
                        } catch (error) {
                            console.error('❌ Mahalleler yüklenemedi:', error);
                            this.mahalleler = [];
                            window.toast?.error('Mahalleler yüklenemedi');
                        } finally {
                            this.loadingMahalleler = false;
                        }
                    },

                    // Context7: TurkiyeAPI kullanımı kaldırıldı - Sadece veritabanından veri çekiliyor

                    clearKisi() {
                        this.form.kisi_id = '';
                        const searchInput = document.querySelector('[data-search-type="kisiler"] input[type="text"]');
                        if (searchInput) {
                            searchInput.value = '';
                        }
                    },

                    resetForm() {
                        if (confirm('Tüm form verilerini temizlemek istediğinizden emin misiniz?')) {
                            this.form = {
                                baslik: '',
                                aciklama: '',
                                tip: '',
                                kategori_id: '',
                                alt_kategori_id: '',
                                status: 'Aktif',
                                one_cikan: false,
                                il_id: '',
                                ilce_id: '',
                                mahalle_id: '',
                                kisi_id: '',
                                kisi_ad: '',
                                kisi_soyad: '',
                                kisi_telefon: '',
                                kisi_email: '',
                                danisman_id: '',
                            };
                            this.ilceler = [];
                            this.mahalleler = [];
                            this.altKategoriler = [];
                            this.aiResults = {
                                analysis: '',
                                price: '',
                                matches: [],
                                description: '',
                            };
                        }
                    },

                    // AI Functions (placeholder)
                    async analyzeRequest() {
                        this.aiLoading.analysis = true;
                        // AI implementation
                        setTimeout(() => {
                            this.aiResults.analysis = '<p>AI analiz özelliği yakında eklenecek.</p>';
                            this.aiLoading.analysis = false;
                        }, 1000);
                    },

                    async suggestPrice() {
                        this.aiLoading.price = true;
                        // AI implementation
                        setTimeout(() => {
                            this.aiResults.price = '<p>AI fiyat önerisi yakında eklenecek.</p>';
                            this.aiLoading.price = false;
                        }, 1000);
                    },

                    async findMatches() {
                        this.aiLoading.matches = true;
                        // AI implementation
                        setTimeout(() => {
                            this.aiResults.matches = [];
                            this.aiLoading.matches = false;
                        }, 1000);
                    },

                    async generateDescription() {
                        this.aiLoading.description = true;
                        // AI implementation
                        setTimeout(() => {
                            this.aiResults.description = 'AI açıklama özelliği yakında eklenecek.';
                            this.aiLoading.description = false;
                        }, 1000);
                    },

                    applyDescription() {
                        const aciklamaField = document.getElementById('aciklama');
                        if (aciklamaField && this.aiResults.description) {
                            aciklamaField.value = this.aiResults.description;
                            this.form.aciklama = this.aiResults.description;
                            window.toast?.success('Açıklama uygulandı!');
                        }
                    },
                };
            }
        </script>
    @endpush

@endsection
