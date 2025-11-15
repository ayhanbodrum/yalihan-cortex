@extends('admin.layouts.neo')

@section('title', 'Yeni Kişi Ekle')

@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection

@section('content')
    <div class="container mx-auto py-6" x-data="kisiCreateForm()">
        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl flex items-center">
                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl flex items-center">
                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg p-6">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6 border-b pb-4">
                <h1 class="text-2xl font-bold text-gray-800">👤 Yeni Kişi Ekle</h1>
                <a href="{{ route('admin.kisiler.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-2 focus:ring-blue-500 transition-all duration-200 flex items-center space-x-2">
                    <span>← Geri Dön</span>
                </a>
            </div>

            {{-- Form --}}
            <form action="{{ route('admin.kisiler.store') }}" method="POST" @submit="loading = true">
                @csrf

                {{-- Section 1: Temel Bilgiler --}}
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="bg-blue-100 text-blue-600 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold mr-2">1</span>
                        👤 Temel Bilgiler
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Ad --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ad *</label>
                            <input type="text" name="ad" x-model="formData.ad" required
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('ad') border-red-500 @enderror"
                                placeholder="Adını girin...">
                            @error('ad')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Soyad --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Soyad *</label>
                            <input type="text" name="soyad" x-model="formData.soyad" required
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('soyad') border-red-500 @enderror"
                                placeholder="Soyadını girin...">
                            @error('soyad')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Telefon --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                            <input type="tel" name="telefon" x-model="formData.telefon" @blur="validateTelefon()"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('telefon') border-red-500 @enderror"
                                :class="{ 'border-red-500': errors.telefon }" placeholder="0555 123 45 67">
                            <p x-show="errors.telefon" x-text="errors.telefon" class="mt-1 text-sm text-red-600" x-cloak>
                            </p>
                            @error('telefon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                            <input type="email" name="email" x-model="formData.email" @blur="validateEmail()"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('email') border-red-500 @enderror"
                                :class="{ 'border-red-500': errors.email }" placeholder="ornek@email.com">
                            <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-600" x-cloak></p>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- TC Kimlik --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">TC Kimlik No</label>
                            <input type="text" name="tc_kimlik" x-model="formData.tc_kimlik" maxlength="11"
                                @blur="validateTcKimlik()"
                                @input="formData.tc_kimlik = formData.tc_kimlik.replace(/\D/g, '')"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('tc_kimlik') border-red-500 @enderror"
                                :class="{ 'border-red-500': errors.tc_kimlik }" placeholder="12345678901">
                            <p x-show="errors.tc_kimlik" x-text="errors.tc_kimlik" class="mt-1 text-sm text-red-600"
                                x-cloak></p>
                            @error('tc_kimlik')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kişi Tipi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Kişi Tipi *</label>
                            <select style="color-scheme: light dark;" name="kisi_tipi" x-model="formData.kisi_tipi" required
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('kisi_tipi') border-red-500 @enderror">
                                <option value="">Seçin...</option>
                                <option value="Müşteri">Müşteri</option>
                                <option value="Potansiyel">Potansiyel</option>
                                <option value="Ev Sahibi">Ev Sahibi</option>
                                <option value="Alıcı">Alıcı</option>
                                <option value="Kiracı">Kiracı</option>
                                <option value="Satıcı">Satıcı</option>
                                <option value="Yatırımcı">Yatırımcı</option>
                                <option value="Tedarikçi">Tedarikçi</option>
                            </select>
                            @error('kisi_tipi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section 2: Durum ve Danışman --}}
                <div class="bg-white rounded-xl border p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="bg-green-100 text-green-600 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold mr-2">2</span>
                        ⚙️ Durum ve Danışman
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Durum *</label>
                            <select style="color-scheme: light dark;" name="status" x-model="formData.status" required
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('status') border-red-500 @enderror">
                                <option value="">Seçin...</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Pasif">Pasif</option>
                                <option value="Beklemede">Beklemede</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Danışman --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Danışman</label>
                            <select style="color-scheme: light dark;" name="danisman_id" x-model="formData.danisman_id"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('danisman_id') border-red-500 @enderror">
                                <option value="">Seçin...</option>
                                @foreach ($danismanlar ?? [] as $danisman)
                                    <option value="{{ $danisman->id }}">{{ $danisman->name }}</option>
                                @endforeach
                            </select>
                            @error('danisman_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section 3: Context7 Location System --}}
                <div class="bg-white rounded-xl border p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="bg-purple-100 text-purple-600 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold mr-2">3</span>
                        📍 Adres Bilgileri
                    </h2>

                    {{-- Context7: TurkiyeAPI Fetch Butonu --}}
                    <div
                        class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="text-sm font-medium text-blue-800 dark:text-blue-200">TurkiyeAPI'den Veri
                                Çek:</span>
                            <button type="button" @click="fetchFromTurkiyeAPI()"
                                :disabled="fetching || (!formData.il_id && !formData.ilce_id)"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                                <svg x-show="!fetching" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <div x-show="fetching"
                                    class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full">
                                </div>
                                <span x-text="fetching ? 'Çekiliyor...' : 'TurkiyeAPI\'den Çek'"></span>
                            </button>
                            <span x-show="fetching" class="text-xs text-blue-600 dark:text-blue-400">
                                <span x-text="fetchingMessage"></span>
                            </span>
                        </div>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                            💡 İl veya İlçe seçerek TurkiyeAPI'den güncel verileri çekebilirsiniz.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        {{-- İl --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">İl</label>
                            <select style="color-scheme: light dark;" name="il_id" x-model="formData.il_id"
                                @change="loadIlceler()"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('il_id') border-red-500 @enderror">
                                <option value="">Seçin...</option>
                                @foreach ($iller as $il)
                                    <option value="{{ $il->id }}">{{ $il->il_adi }}</option>
                                @endforeach
                            </select>
                            @error('il_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- İlçe --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">İlçe</label>
                            <select style="color-scheme: light dark;" name="ilce_id" x-model="formData.ilce_id"
                                @change="loadMahalleler()"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('ilce_id') border-red-500 @enderror"
                                :disabled="!formData.il_id || loadingIlceler">
                                <option value=""
                                    x-text="!formData.il_id ? 'Önce il seçin...' : (loadingIlceler ? 'Yükleniyor...' : 'İlçe seçin...')">
                                </option>
                                <template x-for="ilce in ilceler" :key="ilce.id || ilce.temp_id">
                                    <option :value="ilce.id || ''"
                                        x-text="ilce.ilce_adi + (ilce._from_turkiyeapi ? ' (TurkiyeAPI)' : '')"></option>
                                </template>
                            </select>
                            @error('ilce_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mahalle --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mahalle</label>
                            <select style="color-scheme: light dark;" name="mahalle_id" x-model="formData.mahalle_id"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('mahalle_id') border-red-500 @enderror"
                                :disabled="!formData.ilce_id || loadingMahalleler">
                                <option value=""
                                    x-text="!formData.ilce_id ? 'Önce ilçe seçin...' : (loadingMahalleler ? 'Yükleniyor...' : 'Mahalle seçin...')">
                                </option>
                                <template x-for="mahalle in mahalleler" :key="mahalle.id || mahalle.temp_id">
                                    <option :value="mahalle.id || ''"
                                        x-text="mahalle.mahalle_adi + (mahalle._from_turkiyeapi ? ' (TurkiyeAPI)' : '')">
                                    </option>
                                </template>
                            </select>
                            @error('mahalle_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Adres --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adres (Detay)</label>
                        <textarea name="adres_detay" x-model="formData.adres_detay" rows="2"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('adres_detay') border-red-500 @enderror"
                            placeholder="Sokak, cadde, bina no, daire no..."></textarea>
                        @error('adres_detay')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Section 4: Notlar --}}
                <div class="bg-white rounded-xl border p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="bg-yellow-100 text-yellow-600 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold mr-2">4</span>
                        📝 Notlar
                    </h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kişi Hakkında Notlar</label>
                        <textarea name="notlar" x-model="formData.notlar" rows="4"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('notlar') border-red-500 @enderror"
                            placeholder="Kişi hakkında özel notlar, tercihler, önemli bilgiler..."></textarea>
                        @error('notlar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-between items-center pt-6 border-t">
                    <button type="button" @click="resetForm()"
                        class="inline-flex items-center px-6 py-3 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg hover:scale-105 transition-all duration-200 text-gray-600 hover:text-gray-800">
                        🔄 Formu Temizle
                    </button>

                    <div class="flex space-x-3">
                        <a href="{{ route('admin.kisiler.index') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                            ← Geri Dön
                        </a>
                        <button type="submit"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 shadow-lg hover:shadow-xl"
                            :disabled="loading">
                            <span x-show="!loading">✅ Kişiyi Kaydet</span>
                            <span x-show="loading" x-cloak>⏳ Kaydediliyor...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function kisiCreateForm() {
            return {
                loading: false,
                loadingIlceler: false,
                loadingMahalleler: false,
                fetching: false,
                fetchingMessage: '',
                errors: {
                    telefon: '',
                    email: '',
                    tc_kimlik: ''
                },
                formData: {
                    ad: '{{ old('ad') }}',
                    soyad: '{{ old('soyad') }}',
                    telefon: '{{ old('telefon') }}',
                    email: '{{ old('email') }}',
                    tc_kimlik: '{{ old('tc_kimlik') }}',
                    kisi_tipi: '{{ old('kisi_tipi') }}',
                    status: '{{ old('status', 'Aktif') }}',
                    danisman_id: '{{ old('danisman_id') }}',
                    il_id: '{{ old('il_id') }}',
                    ilce_id: '{{ old('ilce_id') }}',
                    mahalle_id: '{{ old('mahalle_id') }}',
                    adres_detay: '{{ old('adres_detay') }}',
                    notlar: '{{ old('notlar') }}'
                },
                ilceler: [],
                mahalleler: [],

                // Validation Methods
                validateTelefon() {
                    if (!this.formData.telefon) {
                        this.errors.telefon = '';
                        return true;
                    }
                    const phonePattern = /^[0-9\s\-\(\)]+$/;
                    if (!phonePattern.test(this.formData.telefon)) {
                        this.errors.telefon = 'Geçersiz telefon formatı';
                        return false;
                    }
                    this.errors.telefon = '';
                    return true;
                },

                validateEmail() {
                    if (!this.formData.email) {
                        this.errors.email = '';
                        return true;
                    }
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(this.formData.email)) {
                        this.errors.email = 'Geçersiz e-posta formatı';
                        return false;
                    }
                    this.errors.email = '';
                    return true;
                },

                validateTcKimlik() {
                    if (!this.formData.tc_kimlik) {
                        this.errors.tc_kimlik = '';
                        return true;
                    }
                    const cleaned = this.formData.tc_kimlik.replace(/\D/g, '');
                    if (cleaned.length !== 11) {
                        this.errors.tc_kimlik = 'TC Kimlik No 11 haneli olmalıdır';
                        return false;
                    }
                    if (cleaned[0] === '0') {
                        this.errors.tc_kimlik = 'TC Kimlik No 0 ile başlayamaz';
                        return false;
                    }
                    this.errors.tc_kimlik = '';
                    return true;
                },

                // Context7 Location System - Load İlçeler (Otomatik TurkiyeAPI desteği)
                async loadIlceler() {
                    if (!this.formData.il_id) {
                        this.ilceler = [];
                        this.mahalleler = [];
                        this.formData.ilce_id = '';
                        this.formData.mahalle_id = '';
                        return;
                    }

                    this.loadingIlceler = true;
                    this.formData.ilce_id = '';
                    this.formData.mahalle_id = '';
                    this.mahalleler = [];

                    try {
                        // Context7: Sadece veritabanından veri çek
                        const response = await fetch(`/api/ilceler/${this.formData.il_id}`);
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
                    if (!this.formData.ilce_id) {
                        this.mahalleler = [];
                        this.formData.mahalle_id = '';
                        return;
                    }

                    this.loadingMahalleler = true;
                    this.formData.mahalle_id = '';

                    try {
                        // Context7: Sadece veritabanından veri çek
                        const response = await fetch(`/api/mahalleler/${this.formData.ilce_id}`);
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

                // Context7: TurkiyeAPI'den Veri Çekme
                async fetchFromTurkiyeAPI() {
                    if (!this.formData.il_id && !this.formData.ilce_id) {
                        window.toast?.error('Lütfen en az bir il veya ilçe seçin');
                        return;
                    }

                    this.fetching = true;
                    this.fetchingMessage = 'Veriler çekiliyor...';

                    try {
                        const requestBody = {
                            type: 'auto'
                        };

                        if (this.formData.il_id) {
                            requestBody.province_id = parseInt(this.formData.il_id);
                        }

                        if (this.formData.ilce_id) {
                            requestBody.district_id = parseInt(this.formData.ilce_id);
                        }

                        const response = await fetch('{{ route('admin.adres-yonetimi.fetch-from-turkiyeapi') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify(requestBody)
                        });

                        const data = await response.json();

                        if (data.success) {
                            const counts = data.counts || {};
                            let message = '✅ TurkiyeAPI\'den veriler çekildi!\n\n';

                            // İlçeleri ekle
                            if (data.data.districts && data.data.districts.length > 0 && this.formData.il_id) {
                                // Önce fetch'ten gelen ilçeleri temizle
                                this.ilceler = this.ilceler.filter(ilce =>
                                    !ilce._from_turkiyeapi || ilce.il_id != this.formData.il_id
                                );

                                // Yeni çekilen ilçeleri ekle
                                data.data.districts.forEach((turkiyeIlce, index) => {
                                    const existingIlce = this.ilceler.find(ilce =>
                                        ilce.il_id == this.formData.il_id &&
                                        ilce.ilce_adi.toLowerCase() === turkiyeIlce.name.toLowerCase() &&
                                        ilce.id !== null
                                    );

                                    if (!existingIlce) {
                                        this.ilceler.push({
                                            id: null,
                                            il_id: this.formData.il_id,
                                            ilce_adi: turkiyeIlce.name,
                                            _from_turkiyeapi: true,
                                            temp_id: `temp_ilce_${index}`
                                        });
                                    }
                                });
                                message += `📊 İlçeler: ${counts.districts}\n`;
                            }

                            // Mahalleleri ekle
                            if (data.data.neighborhoods && data.data.neighborhoods.length > 0 && this.formData
                                .ilce_id) {
                                // Önce fetch'ten gelen mahalleleri temizle
                                this.mahalleler = this.mahalleler.filter(mahalle =>
                                    !mahalle._from_turkiyeapi || mahalle.ilce_id != this.formData.ilce_id
                                );

                                // Yeni çekilen mahalleleri ekle
                                data.data.neighborhoods.forEach((turkiyeMahalle, index) => {
                                    const existingMahalle = this.mahalleler.find(mahalle =>
                                        mahalle.ilce_id == this.formData.ilce_id &&
                                        mahalle.mahalle_adi.toLowerCase() === turkiyeMahalle.name
                                        .toLowerCase() &&
                                        mahalle.id !== null
                                    );

                                    if (!existingMahalle) {
                                        this.mahalleler.push({
                                            id: null,
                                            ilce_id: this.formData.ilce_id,
                                            mahalle_adi: turkiyeMahalle.name,
                                            _from_turkiyeapi: true,
                                            temp_id: `temp_mahalle_${index}`
                                        });
                                    }
                                });
                                message += `📍 Mahalleler: ${counts.neighborhoods}\n`;
                            }

                            message +=
                                `\n💡 Bu verileri veritabanına kaydetmek için Adres Yönetimi sayfasını kullanabilirsiniz.`;
                            window.toast?.success(message);
                            this.fetchingMessage =
                                `✅ ${counts.districts || 0} ilçe, ${counts.neighborhoods || 0} mahalle çekildi`;
                        } else {
                            window.toast?.error('Veri çekme hatası: ' + (data.message || 'Bilinmeyen hata'));
                            this.fetchingMessage = 'Hata oluştu';
                        }
                    } catch (error) {
                        console.error('Fetch error:', error);
                        window.toast?.error('Veri çekme işlemi sırasında hata oluştu: ' + error.message);
                        this.fetchingMessage = 'Hata oluştu';
                    } finally {
                        setTimeout(() => {
                            this.fetching = false;
                            this.fetchingMessage = '';
                        }, 2000);
                    }
                },

                // Reset Form
                resetForm() {
                    if (confirm('Tüm form verilerini temizlemek istediğinizden emin misiniz?')) {
                        this.formData = {
                            ad: '',
                            soyad: '',
                            telefon: '',
                            email: '',
                            tc_kimlik: '',
                            kisi_tipi: '',
                            status: 'Aktif',
                            danisman_id: '',
                            il_id: '',
                            ilce_id: '',
                            mahalle_id: '',
                            adres_detay: '',
                            notlar: ''
                        };
                        this.ilceler = [];
                        this.mahalleler = [];
                    }
                },

                // Initialize
                init() {
                    console.log('✅ Kişi Create Form initialized (Context7 Standards)');

                    // Load ilçeler if il_id is pre-filled
                    if (this.formData.il_id) {
                        this.loadIlceler();
                    }

                    // Load mahalleler if ilce_id is pre-filled
                    if (this.formData.ilce_id) {
                        setTimeout(() => this.loadMahalleler(), 500);
                    }
                }
            }
        }

        console.log('📍 Context7 Location System ready for Kişi Create');
    </script>
@endpush
