@extends('admin.layouts.neo')

@section('title', 'Yeni Özellik Ekle')

@section('content')
    <!-- Context7: Temiz Özellik Create Form -->
    <div class="container mx-auto" x-data="ozellikForm()">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center justify-between mb-6-content">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Yeni Özellik Ekle</h1>
            </div>
            <div class="flex items-center justify-between mb-6-actions">
                <a href="{{ route('admin.ozellikler.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                    ← Geri Dön
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-blue-800 dark:bg-blue-900 dark:border-blue-800 dark:text-blue-200 rounded-lg border border-blue-200 bg-blue-50 p-4 text-blue-800 dark:bg-blue-900 dark:border-blue-800 dark:text-blue-200-success mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Card -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-body">
                <form method="POST" action="{{ route('admin.ozellikler.store') }}" @submit="loading = true">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Özellik Adı -->
                        <div class="space-y-2 relative">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Özellik Adı *</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                                value="{{ old('name') }}" required maxlength="255"
                                placeholder="Örn: Havuz, Asansör, Güvenlik">
                            @error('name')
                                <div class="text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Özellik Tipi -->
                        <div class="space-y-2 relative">
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Özellik Tipi *</label>
                            <select style="color-scheme: light dark;" id="type" name="type" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                                <option value="">Seçiniz</option>
                                <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Evet/Hayır</option>
                                <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Metin</option>
                                <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Sayı</option>
                                <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Seçenekli</option>
                            </select>
                            @error('type')
                                <div class="text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div class="space-y-2 relative">
                            <label for="feature_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                            <select style="color-scheme: light dark;" id="feature_category_id" name="feature_category_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Kategori Seçiniz</option>
                                @foreach ($kategoriler ?? [] as $kategori)
                                    <option value="{{ $kategori->id }}"
                                        {{ old('feature_category_id') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('feature_category_id')
                                <div class="text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sıra -->
                        <div class="space-y-2 relative">
                            <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sıra</label>
                            <input type="number" id="order" name="order" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                                value="{{ old('order', 0) }}" min="0">
                            @error('order')
                                <div class="text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Durum -->
                    <div class="space-y-2 relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    {{ old('status', 1) == 1 ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-900 dark:text-white">Aktif</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="0" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    {{ old('status') == 0 ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-900 dark:text-white">Pasif</span>
                            </label>
                        </div>
                        @error('status')
                            <div class="text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.ozellikler.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                            İptal
                        </a>
                        <button type="submit"
                                id="ozellik-submit-btn"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                :disabled="loading">
                            <svg id="ozellik-submit-icon" x-show="!loading" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg id="ozellik-submit-spinner" x-show="loading" class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!loading">Kaydet</span>
                            <span x-show="loading">Kaydediliyor...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function ozellikForm() {
            return {
                loading: false
            }
        }
    </script>
@endsection
