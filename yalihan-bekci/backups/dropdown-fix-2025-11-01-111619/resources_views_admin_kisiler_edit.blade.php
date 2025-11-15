@extends('admin.layouts.neo')

@section('title', 'Müşteri Düzenle')

@section('content_header')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                👤 {{ $kisi->ad }} {{ $kisi->soyad }} - Düzenle
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kişi bilgilerini güncelleyin</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.kisiler.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                ← Geri Dön
            </a>
            <button onclick="deleteKisi({{ $kisi->id }})" class="neo-btn neo-btn-danger">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Sil
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Lütfen aşağıdaki hataları düzeltin:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.kisiler.update', $kisi) }}" method="POST" x-data="kisiEditFormData({{ $kisi->id }})" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Temel Bilgiler -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        👤 Temel Bilgiler
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Ad -->
                        <div class="neo-form-group">
                            <label for="ad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">Ad *</span>
                            </label>
                            <input type="text" name="ad" id="ad" required
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" placeholder="Müşteri adını girin..." x-model="formData.ad"
                                @input.debounce.500ms="checkDuplicate('ad', $event.target.value)">
                        </div>

                        <!-- Soyad -->
                        <div class="neo-form-group">
                            <label for="soyad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">Soyad *</span>
                            </label>
                            <input type="text" name="soyad" id="soyad" required
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" placeholder="Müşteri soyadını girin..." x-model="formData.soyad"
                                @input.debounce.500ms="checkDuplicate('soyad', $event.target.value)">
                        </div>

                        <!-- Telefon -->
                        <div class="neo-form-group">
                            <label for="telefon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">Telefon *</span>
                            </label>
                            <input type="tel" name="telefon" id="telefon"
                                required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" placeholder="05XX XXX XXXX" x-model="formData.telefon"
                                @input.debounce.500ms="checkDuplicate('telefon', $event.target.value)">

                            <!-- Telefon Duplicate Warning -->
                            <div x-show="duplicateWarnings.telefon" class="mt-2">
                                <div class="flex items-start space-x-2 p-2 bg-red-50 border border-red-200 rounded">
                                    <svg class="w-4 h-4 text-red-500 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-red-800 text-sm font-medium">Bu telefon numarası başka bir müşteriye
                                            kayıtlı!</p>
                                        <p class="text-red-600 text-xs" x-text="duplicateWarnings.telefon"></p>
                                        <a :href="duplicateLinks.telefon" target="_blank"
                                            class="text-red-700 underline text-xs">Kayıtlı müşteriyi görüntüle</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- E-posta -->
                        <div class="neo-form-group">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">E-posta</span>
                            </label>
                            <input type="email" name="email" id="email"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" placeholder="ornek@email.com" x-model="formData.email"
                                @input.debounce.500ms="checkDuplicate('email', $event.target.value)">

                            <!-- Email Duplicate Warning -->
                            <div x-show="duplicateWarnings.email" class="mt-2">
                                <div class="flex items-start space-x-2 p-2 bg-red-50 border border-red-200 rounded">
                                    <svg class="w-4 h-4 text-red-500 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-red-800 text-sm font-medium">Bu e-posta adresi başka bir müşteriye
                                            kayıtlı!</p>
                                        <p class="text-red-600 text-xs" x-text="duplicateWarnings.email"></p>
                                        <a :href="duplicateLinks.email" target="_blank"
                                            class="text-red-700 underline text-xs">Kayıtlı müşteriyi görüntüle</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Durum -->
                        <div class="neo-form-group">
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">Durum *</span>
                            </label>
                            <select id="status" name="status" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" x-model="formData.status">
                                <option value="Potansiyel">Potansiyel</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Pasif">Pasif</option>
                            </select>
                        </div>

                        <!-- Kişi Tipi -->
                        <div class="neo-form-group">
                            <label for="kisi_tipi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">Kişi Tipi</span>
                            </label>
                            <select id="kisi_tipi" name="kisi_tipi" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" x-model="formData.kisi_tipi">
                                <option value="">Seçiniz...</option>
                                <option value="Müşteri">Müşteri</option>
                                <option value="Potansiyel">Potansiyel</option>
                                <option value="Ev Sahibi">Ev Sahibi</option>
                                <option value="Alıcı">Alıcı</option>
                                <option value="Kiracı">Kiracı</option>
                                <option value="Satıcı">Satıcı</option>
                                <option value="Yatırımcı">Yatırımcı</option>
                                <option value="Tedarikçi">Tedarikçi</option>
                            </select>
                        </div>

                        <!-- Kaynak (Context7: Field removed - database column doesn't exist) -->

                        <!-- Danışman -->
                        <div class="neo-form-group">
                            <label for="danisman_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">Danışman *</span>
                            </label>
                            <select id="danisman_id" name="danisman_id" x-model="formData.danisman_id" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200">
                                <option value="">Danışman seçin...</option>
                                @foreach ($danismanlar as $danisman)
                                    <option value="{{ $danisman->id }}">
                                        {{ $danisman->name }} ({{ $danisman->email }})
                                        @if (isset($danisman->source) && $danisman->source == 'danisman_model')
                                            - Danışman Modeli
                                        @elseif(isset($danisman->role))
                                            - {{ ucfirst($danisman->role->name) }}
                                        @else
                                            - Danışman
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('danisman_id'))
                                <div class="text-red-500 text-sm mt-1">{{ $errors->first('danisman_id') }}</div>
                            @endif
                        </div>

                        <!-- Etiketler (Multiple Select) -->
                        <div class="neo-form-group">
                            <label for="etiketler_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">Etiketler</span>
                            </label>
                            <select id="etiketler_ids" name="etiketler_ids[]" multiple class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200" style="min-height: 120px;">
                                @foreach ($etiketler as $etiket)
                                    <option value="{{ $etiket->id }}"
                                        {{ in_array($etiket->id, old('etiketler_ids', $kisiEtiketIds)) ? 'selected' : '' }}>
                                        {{ $etiket->ad }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-start gap-1">
                                <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Çoklu seçim:</strong> Ctrl (Windows) veya Cmd (Mac) tuşuna basılı tutarak birden fazla etiket seçebilirsiniz.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Adres Bilgileri -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-green-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        📍 Adres Bilgileri
                    </h2>

                    <!-- Context7 Standart: İl/İlçe/Mahalle Cascade -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- İl -->
                        <div class="neo-form-group">
                            <label for="il_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">İl</span>
                            </label>
                            <select id="il_id" name="il_id"
                                    x-model="formData.il_id"
                                    @change="formData.il_id = $event.target.value; onIlChange($event.target.value)"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200">
                                <option value="">İl Seçin</option>
                                @foreach($iller as $il)
                                    <option value="{{ $il->id }}" {{ old('il_id', $kisi->il_id) == $il->id ? 'selected' : '' }}>
                                        {{ $il->il_adi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- İlçe -->
                        <div class="neo-form-group">
                            <label for="ilce_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">İlçe</span>
                            </label>
                            <select id="ilce_id" name="ilce_id"
                                    x-model="formData.ilce_id"
                                    @change="formData.ilce_id = $event.target.value; onIlceChange($event.target.value)"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200">
                                <option value="">İlçe Seçin</option>
                            </select>
                        </div>

                        <!-- Mahalle (Context7: mahalle_id standardı - 2025-10-31) -->
                        <div class="neo-form-group">
                            <label for="mahalle_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">Mahalle</span>
                            </label>
                            <select id="mahalle_id" name="mahalle_id"
                                    x-model="formData.mahalle_id"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 disabled:bg-gray-100 dark:disabled:bg-gray-700 disabled:text-gray-500 dark:disabled:text-gray-400"
                                    :disabled="!formData.ilce_id">
                                <option value="">Mahalle Seçin</option>
                            </select>
                        </div>
                    </div>

                    <!-- Adres Detayı -->
                    <div class="neo-form-group mt-6">
                        <label for="adres_detay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">Adres Detayı</span>
                        </label>
                        <textarea name="adres_detay" rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 py-3 text-base"
                            placeholder="Sokak, cadde, bina numarası, daire no vb. detay bilgiler">{{ old('adres_detay', $kisi->adres_detay ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Notlar -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-purple-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        📝 Notlar
                    </h2>

                    <div class="neo-form-group">
                        <label for="notlar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300-text">Müşteri Notları</span>
                        </label>
                        <textarea id="notlar" name="notlar" rows="4" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                            placeholder="Müşteri ile ilgili notlar...">{{ old('notlar', $kisi->notlar) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Butonları -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.kisiler.index') }}" class="neo-btn neo-btn neo-btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    İptal
                </a>
                <button type="submit" class="neo-btn neo-btn neo-btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Location Cascade (Vanilla JS - Context7 Standard)
        // Note: mahalle_id field removed (column doesn't exist in kisiler table)
        async function loadIlceler(ilId) {
            console.log('📍 loadIlceler called with İl ID:', ilId);
            const ilceSelect = document.getElementById('ilce_id');

            if (!ilceSelect) {
                console.error('❌ İlçe select element bulunamadı!');
                return;
            }

            // Reset
            ilceSelect.innerHTML = '<option value="">İlçe Seçin</option>';

            if (!ilId) {
                console.log('⚠️ İl ID boş, işlem iptal');
                return;
            }

            try {
                console.log(`🔄 Fetching ilçeler from: /api/ilceler/${ilId}`);
                const response = await fetch(`/api/ilceler/${ilId}`);
                const data = await response.json();
                console.log('📦 İlçeler API response:', data);

                if (data.success && data.data) {
                    console.log(`✅ ${data.data.length} ilçe yüklendi`);
                    data.data.forEach(ilce => {
                        const option = document.createElement('option');
                        option.value = ilce.id;
                        option.textContent = ilce.ilce_adi;
                        ilceSelect.appendChild(option);
                    });
                } else {
                    console.warn('⚠️ İlçe data boş veya başarısız');
                }
            } catch (error) {
                console.error('❌ İlçeler yüklenemedi:', error);
            }
        }

        // Export to window
        window.loadIlceler = loadIlceler;
        console.log('✅ Location cascade function exported to window');

        // Alpine Component for Edit Form (Context7)
        function kisiEditFormData(kisiId) {
            return {
                kisiId: kisiId,
                formData: {
                    ad: '{{ old('ad', $kisi->ad) }}',
                    soyad: '{{ old('soyad', $kisi->soyad) }}',
                    telefon: '{{ old('telefon', $kisi->telefon) }}',
                    email: '{{ old('email', $kisi->email) }}',
                    kisi_tipi: '{{ old('kisi_tipi', $kisi->kisi_tipi) }}',
                    danisman_id: '{{ old('danisman_id', $kisi->danisman_id ?? '') }}',
                    status: '{{ old('status', $kisi->status) }}',
                    il_id: '{{ old('il_id', $kisi->il_id ?? '') }}',
                    ilce_id: '{{ old('ilce_id', $kisi->ilce_id ?? '') }}',
                    mahalle_id: '{{ old('mahalle_id', $kisi->mahalle_id ?? '') }}',
                    notlar: '{{ old('notlar', $kisi->notlar) }}'
                },
                isRestoringData: false, // Flag to prevent double loading
                duplicateWarnings: {
                    telefon: false,
                    email: false
                },
                duplicateLinks: {
                    telefon: '',
                    email: ''
                },

                init() {
                    console.log('✅ Kişi Edit Form initialized for ID:', this.kisiId);
                    console.log('📊 Form Data:', this.formData);

                    // Context7: Auto-load İlçeler and Mahalleler cascade (2025-10-31)
                    if (this.formData.il_id) {
                        console.log('🗺️ İl ID mevcut, cascade load başlıyor...');
                        this.isRestoringData = true; // Set flag
                        this.loadIlcelerInternal(this.formData.il_id, true);
                    } else {
                        console.log('⚠️ İl ID yok, cascade load atlanıyor');
                    }
                },

                // Alpine method for İl change
                async onIlChange(ilId) {
                    console.log('🔄 onIlChange called with:', ilId);
                    console.log('🔍 Current formData.il_id:', this.formData.il_id);
                    console.log('🔍 DOM il_id value:', document.getElementById('il_id').value);

                    // Reset ilce and mahalle
                    this.formData.ilce_id = '';
                    this.formData.mahalle_id = '';

                    if (ilId) {
                        await this.loadIlcelerInternal(ilId, false);
                    } else {
                        // Clear ilce dropdown
                        const ilceSelect = document.getElementById('ilce_id');
                        if (ilceSelect) {
                            ilceSelect.innerHTML = '<option value="">İlçe Seçin</option>';
                        }

                        // Clear mahalle dropdown
                        const mahalleSelect = document.getElementById('mahalle_id');
                        if (mahalleSelect) {
                            mahalleSelect.innerHTML = '<option value="">Mahalle Seçin</option>';
                        }
                    }
                },

                // Alpine method for İlçe change
                async onIlceChange(ilceId) {
                    // Skip if we're restoring data (to prevent double loading)
                    if (this.isRestoringData) {
                        console.log('⏭️ onIlceChange skipped (data restoration in progress)');
                        return;
                    }

                    console.log('🔄 onIlceChange called with:', ilceId);
                    console.log('🔍 Current formData.ilce_id:', this.formData.ilce_id);
                    console.log('🔍 DOM ilce_id value:', document.getElementById('ilce_id').value);

                    // Reset mahalle
                    this.formData.mahalle_id = '';

                    if (ilceId) {
                        await this.loadMahalleler(ilceId);
                    } else {
                        // Clear mahalle dropdown
                        const mahalleSelect = document.getElementById('mahalle_id');
                        if (mahalleSelect) {
                            mahalleSelect.innerHTML = '<option value="">Mahalle Seçin</option>';
                        }
                    }
                },

                // Internal method to load ilceler
                async loadIlcelerInternal(ilId, preserveSelection = false) {
                    console.log('📍 loadIlcelerInternal called with İl ID:', ilId, 'preserve:', preserveSelection);
                    const ilceSelect = document.getElementById('ilce_id');

                    if (!ilceSelect) {
                        console.error('❌ İlçe select element bulunamadı!');
                        return;
                    }

                    const savedIlceId = preserveSelection ? this.formData.ilce_id : null;
                    const savedMahalleId = preserveSelection ? this.formData.mahalle_id : null;

                    // Reset
                    ilceSelect.innerHTML = '<option value="">İlçe Seçin</option>';

                    if (!ilId) {
                        console.log('⚠️ İl ID boş, işlem iptal');
                        return;
                    }

                    try {
                        console.log(`🔄 Fetching ilçeler from: /api/ilceler/${ilId}`);
                        const response = await fetch(`/api/ilceler/${ilId}`);
                        const data = await response.json();
                        console.log('📦 İlçeler API response:', data);

                        if (data.success && data.data) {
                            console.log(`✅ ${data.data.length} ilçe yüklendi`);
                            data.data.forEach(ilce => {
                                const option = document.createElement('option');
                                option.value = ilce.id;
                                option.textContent = ilce.ilce_adi;
                                ilceSelect.appendChild(option);
                            });

                            // Restore selection if needed
                            if (preserveSelection && savedIlceId) {
                                // Wait for DOM to update, then set both DOM and Alpine model
                                setTimeout(() => {
                                    // CRITICAL: Set both Alpine model and DOM value (NO dispatchEvent!)
                                    this.formData.ilce_id = String(savedIlceId);
                                    ilceSelect.value = String(savedIlceId);

                                    console.log('✅ İlçe restored:', savedIlceId, '(DOM value:', ilceSelect.value + ')');

                                    // Load mahalleler
                                    this.loadMahalleler(savedIlceId).then(() => {
                                        // Restore mahalle selection
                                        if (savedMahalleId) {
                                            setTimeout(() => {
                                                const mahalleSelect = document.getElementById('mahalle_id');
                                                if (mahalleSelect) {
                                                    this.formData.mahalle_id = String(savedMahalleId);
                                                    mahalleSelect.value = String(savedMahalleId);
                                                    console.log('✅ Mahalle restored:', savedMahalleId, '(DOM value:', mahalleSelect.value + ')');
                                                }

                                                // CRITICAL: Reset flag after restoration complete
                                                this.isRestoringData = false;
                                                console.log('🎉 Data restoration complete, flag reset');
                                            }, 200);
                                        } else {
                                            // No mahalle to restore, reset flag now
                                            this.isRestoringData = false;
                                            console.log('🎉 Data restoration complete (no mahalle), flag reset');
                                        }
                                    });
                                }, 200);
                            } else {
                                // No data to restore, reset flag
                                this.isRestoringData = false;
                                console.log('ℹ️ No data to restore, flag reset');
                            }
                        } else {
                            console.warn('⚠️ İlçe data boş veya başarısız');
                        }
                    } catch (error) {
                        console.error('❌ İlçeler yüklenemedi:', error);
                    }
                },

                async loadMahalleler(ilceId) {
                    const mahalleSelect = document.getElementById('mahalle_id');
                    if (!mahalleSelect) {
                        console.log('⚠️ mahalle_id select elementi bulunamadı');
                        return;
                    }

                    // Reset
                    mahalleSelect.innerHTML = '<option value="">Mahalle Seçin</option>';

                    if (!ilceId) {
                        console.log('⚠️ İlçe ID boş, mahalle yüklenemedi');
                        return;
                    }

                    try {
                        console.log(`🔄 Fetching mahalleler from: /api/location/neighborhoods/${ilceId}`);
                        const response = await fetch(`/api/location/neighborhoods/${ilceId}`);
                        const data = await response.json();
                        console.log('📦 Mahalleler API response:', data);

                        if (data.success && data.data) {
                            console.log(`✅ ${data.data.length} mahalle yüklendi`);
                            data.data.forEach(mahalle => {
                                const option = document.createElement('option');
                                option.value = mahalle.id;
                                option.textContent = mahalle.mahalle || mahalle.name;
                                mahalleSelect.appendChild(option);
                            });

                            // Set selected mahalle if exists
                            if (this.formData.mahalle_id) {
                                mahalleSelect.value = this.formData.mahalle_id;
                                console.log('✅ Mahalle set edildi:', this.formData.mahalle_id);
                            }
                        } else {
                            console.warn('⚠️ Mahalle data boş veya başarısız');
                        }
                    } catch (error) {
                        console.error('❌ Mahalleler yüklenemedi:', error);
                    }
                },

                async checkDuplicate(field, value) {
                    if (!value || value.length < 3) {
                        this.duplicateWarnings[field] = false;
                        return;
                    }

                    try {
                        const response = await fetch(`/api/kisiler/check-duplicate`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                field: field,
                                value: value,
                                exclude_id: this.kisiId
                            })
                        });

                        const data = await response.json();
                        if (data.exists) {
                            this.duplicateWarnings[field] = data.message;
                            this.duplicateLinks[field] = `/admin/kisiler/${data.kisi_id}`;
                        } else {
                            this.duplicateWarnings[field] = false;
                        }
                    } catch (error) {
                        console.error('Duplicate check error:', error);
                    }
                }
            };
        }
        window.kisiEditFormData = kisiEditFormData;

        // Delete Function (Context7 Standard)
        async function deleteKisi(kisiId) {
            if (!confirm('Bu kişiyi silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz!')) {
                return;
            }

            try {
                const response = await fetch(`/admin/kisiler/${kisiId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Success toast
                    if (window.toast) {
                        window.toast.success('Kişi başarıyla silindi!');
                    }

                    // Redirect to index
                    setTimeout(() => {
                        window.location.href = '/admin/kisiler';
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Silme işlemi başarısız');
                }
            } catch (error) {
                console.error('Silme hatası:', error);

                // Error toast
                if (window.toast) {
                    window.toast.error('Silme işlemi başarısız: ' + error.message);
                } else {
                    alert('Silme işlemi başarısız: ' + error.message);
                }
            }
        }

        // Export to window
        window.deleteKisi = deleteKisi;

        console.log('✅ Kişiler edit page initialized (Context7 Vanilla JS)');
    </script>
@endpush
