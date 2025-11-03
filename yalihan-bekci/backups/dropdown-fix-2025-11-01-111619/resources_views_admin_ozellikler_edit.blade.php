@extends('admin.layouts.neo')

@section('title', 'Özellik Düzenle')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Modern Header with Gradient -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-purple-600 rounded-2xl p-8 mb-8 text-white shadow-xl">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 backdrop-blur-lg rounded-xl p-4 shadow-lg">
                        <span class="text-3xl">✏️</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold mb-1">Özellik Düzenle</h1>
                        <p class="text-blue-100 text-sm">{{ $ozellik->name }} özelliğini düzenleyin</p>
                    </div>
                </div>
                <a href="{{ route('admin.ozellikler.index') }}"
                    class="bg-white/20 hover:bg-white/30 backdrop-blur-lg text-white px-6 py-3 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="font-medium">Geri Dön</span>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.ozellikler.update', $ozellik) }}" method="POST" x-data="{
            isSubmitting: false,
            status: {{ $ozellik->status ? 'true' : 'false' }},
            veriTipi: '{{ $ozellik->type ?? 'text' }}',
            selectedCategory: '{{ old('feature_category_id', $ozellik->feature_category_id) }}',
            selectedOrder: '{{ old('order', $ozellik->order) }}',
            showOptions: false,
            getCategoryName() {
                const select = document.getElementById('feature_category_id');
                const option = select ? select.options[select.selectedIndex] : null;
                return option && option.value ? option.text : 'Kategorisiz';
            }
        }"
            @submit.prevent="if(!isSubmitting){ isSubmitting=true; $el.submit(); }" x-init="updateOptionsVisibility()">
            @csrf
            @method('PUT')

            <!-- Temel Bilgiler -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 mb-8 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                        <span class="text-2xl mr-3">📝</span>
                        <span>Temel Bilgiler</span>
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Özellik Adı -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Özellik Adı <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required autofocus
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                placeholder="Örn: Balkon, Asansör, Güvenlik" value="{{ old('name', $ozellik->name) }}">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="feature_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select id="feature_category_id" name="feature_category_id" required
                                x-model="selectedCategory"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">Kategori Seçin</option>
                                @foreach ($kategoriler as $kategori)
                                    <option value="{{ $kategori->id }}"
                                        {{ old('feature_category_id', $ozellik->feature_category_id) == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('feature_category_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sıra -->
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                                Sıra
                            </label>
                            <input type="number" id="order" name="order" min="0"
                                x-model="selectedOrder"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                placeholder="Otomatik" value="{{ old('order', $ozellik->order) }}">
                            <p class="text-sm text-gray-500 mt-1">Boş bırakılırsa otomatik sıralanır</p>
                            @error('order')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Modern Status Toggle -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Durum
                            </label>
                            <div class="flex items-center space-x-3">
                                <!-- Toggle Switch -->
                                <div class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 cursor-pointer"
                                    :class="status ? 'bg-blue-600' : 'bg-gray-300'" @click="status = !status">
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200"
                                        :class="status ? 'translate-x-6' : 'translate-x-1'"></span>
                                </div>
                                <input type="hidden" name="status" :value="status ? '1' : '0'">
                                <span class="text-sm font-medium" :class="status ? 'text-blue-600' : 'text-gray-500'">
                                    <span x-text="status ? 'Aktif' : 'Pasif'"></span>
                                </span>
                            </div>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Veri Tipi ve Seçenekler -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                        <!-- Veri Tipi -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Veri Tipi <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                x-model="veriTipi" @change="updateOptionsVisibility()">
                                <option value="">Veri Tipi Seçin</option>
                                <option value="text"
                                    {{ old('type', $ozellik->type ?? 'text') == 'text' ? 'selected' : '' }}>Metin
                                    (Text)</option>
                                <option value="number"
                                    {{ old('type', $ozellik->type ?? 'text') == 'number' ? 'selected' : '' }}>
                                    Sayı (Number)</option>
                                <option value="select"
                                    {{ old('type', $ozellik->type ?? 'text') == 'select' ? 'selected' : '' }}>
                                    Seçim (Select)</option>
                                <option value="boolean"
                                    {{ old('type', $ozellik->type ?? 'text') == 'boolean' ? 'selected' : '' }}>
                                    Evet/Hayır (Boolean)</option>
                                <option value="multiselect"
                                    {{ old('type', $ozellik->type ?? 'text') == 'multiselect' ? 'selected' : '' }}>
                                    Çoklu Seçim (Multiselect)</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Birim -->
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                                Birim
                            </label>
                            <input type="text" id="unit" name="unit"
                                value="{{ old('unit', $ozellik->unit ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                placeholder="Örn: m², adet, TL, gün">
                            <p class="text-sm text-gray-500 mt-1">Sadece sayısal alanlar için</p>
                            @error('unit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Veri Seçenekleri (Select/Multiselect için) -->
                    <div x-show="showOptions" x-transition class="mt-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Veri Seçenekleri <span class="text-red-500">*</span>
                        </label>
                        <div x-data="{ options: @json(old('veri_secenekleri', $ozellik->veri_secenekleri ?? [])) }">
                            <template x-for="(option, index) in options" :key="index">
                                <div class="flex items-center space-x-2 mb-2">
                                    <input type="text" x-model="option.value"
                                        :name="'veri_secenekleri[' + index + '][value]'" placeholder="Seçenek değeri"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg">
                                    <input type="text" x-model="option.label"
                                        :name="'veri_secenekleri[' + index + '][label]'" placeholder="Seçenek etiketi"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg">
                                    <button type="button" @click="options.splice(index, 1)"
                                        class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                        ❌
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="options.push({value: '', label: ''})"
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                ➕ Seçenek Ekle
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Sadece Select/Multiselect için gerekli</p>
                    </div>

                    <!-- Zorunlu Alan -->
                    <div class="mt-8">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="zorunlu" value="1"
                                {{ old('zorunlu', $ozellik->zorunlu ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Bu özellik zorunlu alan olsun</span>
                        </label>
                    </div>

                    <!-- Açıklama -->
                    <div class="mt-8">
                        <label for="aciklama" class="block text-sm font-medium text-gray-700 mb-2">
                            Açıklama
                        </label>
                        <textarea id="aciklama" name="aciklama" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                            placeholder="Özellik hakkında detaylı açıklama...">{{ old('aciklama', $ozellik->aciklama) }}</textarea>
                        @error('aciklama')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Özellik Bilgileri (Real-time Updates) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 mb-8 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                        <span class="text-2xl mr-3">ℹ️</span>
                        <span>Özellik Bilgileri</span>
                        <span class="text-sm font-normal text-gray-600 dark:text-gray-400 ml-3">Güncel özellik detayları</span>
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Kategori</h3>
                            <p class="text-gray-600 dark:text-gray-300" x-text="getCategoryName()"></p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Durum</h3>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium transition-colors duration-200"
                                :class="status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                <span
                                    class="w-2 h-2 rounded-full mr-1"
                                    :class="status ? 'bg-green-400 animate-pulse' : 'bg-red-400'"></span>
                                <span x-text="status ? 'Aktif' : 'Pasif'"></span>
                            </span>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Sıra</h3>
                            <p class="text-gray-600 dark:text-gray-300" x-text="selectedOrder || 'Otomatik'"></p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Oluşturulma</h3>
                            <p class="text-gray-600 dark:text-gray-300">{{ $ozellik->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Form Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sticky bottom-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <!-- Status Badge -->
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full animate-pulse" :class="status ? 'bg-green-500' : 'bg-red-500'"></div>
                                <span class="text-sm font-semibold" :class="status ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'"
                                    x-text="status ? 'Aktif' : 'Pasif'"></span>
                            </div>
                            <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $ozellik->name }} düzenleniyor</span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('admin.ozellikler.index') }}"
                            class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 text-center font-medium">
                            İptal
                        </a>
                        <button type="submit" :disabled="isSubmitting"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 via-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:via-blue-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                :class="{ 'animate-spin': isSubmitting }">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="isSubmitting ? 'Kaydediliyor...' : '💾 Güncelle'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Keyboard shortcut: Cmd/Ctrl + S
        document.addEventListener('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 's') {
                e.preventDefault();
                document.querySelector('form').submit();
            }
        });

        // Veri tipi seçenekleri görünürlüğü
        function updateOptionsVisibility() {
            const veriTipi = document.getElementById('veri_tipi').value;
            const showOptions = veriTipi === 'select' || veriTipi === 'multiselect';

            // Alpine.js için
            if (window.Alpine && window.Alpine.store) {
                const component = Alpine.$data(document.querySelector('form'));
                if (component) {
                    component.showOptions = showOptions;
                }
            }

            // Normal JavaScript için
            const optionsDiv = document.querySelector('[x-show="showOptions"]');
            if (optionsDiv) {
                optionsDiv.style.display = showOptions ? 'block' : 'none';
            }
        }

        // Veri tipi değişikliği dinleyicisi
        document.addEventListener('DOMContentLoaded', function() {
            const veriTipiSelect = document.getElementById('veri_tipi');
            if (veriTipiSelect) {
                veriTipiSelect.addEventListener('change', updateOptionsVisibility);
                // Sayfa yüklendiğinde de kontrol et
                updateOptionsVisibility();
            }
        });
    </script>
@endpush
