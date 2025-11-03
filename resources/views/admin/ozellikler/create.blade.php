@extends('admin.layouts.neo')

@section('title', 'Yeni Özellik Ekle')

@section('content')
    <!-- Context7: Temiz Özellik Create Form -->
    <div class="neo-container" x-data="ozellikForm()">

        <!-- Header -->
        <div class="neo-header">
            <div class="neo-header-content">
                <h1 class="neo-title">Yeni Özellik Ekle</h1>
            </div>
            <div class="neo-header-actions">
                <a href="{{ route('admin.ozellikler.index') }}" class="neo-btn neo-btn-secondary">
                    ← Geri Dön
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="neo-alert neo-alert-success mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Card -->
        <div class="neo-card">
            <div class="neo-card-body">
                <form method="POST" action="{{ route('admin.ozellikler.store') }}" @submit="loading = true">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Özellik Adı -->
                        <div class="neo-form-group">
                            <label for="name" class="neo-label">Özellik Adı *</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                                value="{{ old('name') }}" required maxlength="255"
                                placeholder="Örn: Havuz, Asansör, Güvenlik">
                            @error('name')
                                <div class="neo-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Özellik Tipi -->
                        <div class="neo-form-group">
                            <label for="type" class="neo-label">Özellik Tipi *</label>
                            <select style="color-scheme: light dark;" id="type" name="type" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                                <option value="">Seçiniz</option>
                                <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Evet/Hayır</option>
                                <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Metin</option>
                                <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Sayı</option>
                                <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Seçenekli</option>
                            </select>
                            @error('type')
                                <div class="neo-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div class="neo-form-group">
                            <label for="feature_category_id" class="neo-label">Kategori</label>
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
                                <div class="neo-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sıra -->
                        <div class="neo-form-group">
                            <label for="order" class="neo-label">Sıra</label>
                            <input type="number" id="order" name="order" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                                value="{{ old('order', 0) }}" min="0">
                            @error('order')
                                <div class="neo-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Durum -->
                    <div class="neo-form-group">
                        <label class="neo-label">Durum</label>
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
                            <div class="neo-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.ozellikler.index') }}" class="neo-btn neo-btn-secondary">
                            İptal
                        </a>
                        <button type="submit" class="neo-btn neo-btn-primary" :disabled="loading">
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
