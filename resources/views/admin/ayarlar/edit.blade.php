@extends('admin.layouts.neo')

@section('title', 'Ayar Düzenle')

@section('content')
    <div class="neo-container max-w-4xl mx-auto">
        <div class="neo-header">
            <div class="neo-header-content">
                <h1 class="neo-title">Ayar Düzenle</h1>
                <p class="neo-subtitle">Sistem ayarlarını yapılandırın</p>
            </div>
            <div class="neo-header-actions">
                <a href="{{ route('admin.ayarlar.index') }}" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">Geri Dön</a>
            </div>
        </div>

        <div class="neo-card">
            <div class="neo-card-header">
                <h2 class="neo-card-title">Sistem Ayarları</h2>
                <p class="neo-card-subtitle">Genel sistem konfigürasyonu</p>
            </div>
            <div class="neo-card-body">
                <form action="{{ route('admin.ayarlar.update', $ayar->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="neo-form-group">
                            <label for="key" class="neo-label">Ayar Anahtarı <span class="text-red-500">*</span></label>
                            <input id="key" name="key" type="text" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('key') neo-input-error @enderror" 
                                value="{{ old('key', $ayar->key) }}" required placeholder="Ayar anahtarını girin">
                            @error('key')
                                <div class="neo-error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="neo-form-group">
                            <label for="type" class="neo-label">Veri Tipi <span class="text-red-500">*</span></label>
                            <select style="color-scheme: light dark;" id="type" name="type" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('type') w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500-error @enderror transition-all duration-200" required>
                                <option value="">Tip Seçin</option>
                                <option value="string" {{ old('type', $ayar->type) == 'string' ? 'selected' : '' }}>Metin</option>
                                <option value="integer" {{ old('type', $ayar->type) == 'integer' ? 'selected' : '' }}>Sayı</option>
                                <option value="boolean" {{ old('type', $ayar->type) == 'boolean' ? 'selected' : '' }}>Evet/Hayır</option>
                                <option value="json" {{ old('type', $ayar->type) == 'json' ? 'selected' : '' }}>JSON</option>
                            </select>
                            @error('type')
                                <div class="neo-error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="neo-form-group">
                        <label for="value" class="neo-label">Değer <span class="text-red-500">*</span></label>
                        <textarea id="value" name="value" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 resize-vertical @error('value') neo-input-error @enderror" 
                            rows="4" placeholder="Ayar değerini girin">{{ old('value', $ayar->value) }}</textarea>
                        @error('value')
                            <div class="neo-error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="neo-form-group">
                        <label for="description" class="neo-label">Açıklama</label>
                        <textarea id="description" name="description" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 resize-vertical @error('description') neo-input-error @enderror" 
                            rows="3" placeholder="Ayar hakkında açıklama">{{ old('description', $ayar->description) }}</textarea>
                        @error('description')
                            <div class="neo-error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="neo-form-group">
                            <label for="group" class="neo-label">Grup</label>
                            <input id="group" name="group" type="text" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('group') neo-input-error @enderror" 
                                value="{{ old('group', $ayar->group) }}" placeholder="Ayar grubu">
                            @error('group')
                                <div class="neo-error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="neo-form-group">
                            <label for="order" class="neo-label">Sıra</label>
                            <input id="order" name="order" type="number" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('order') neo-input-error @enderror" 
                                value="{{ old('order', $ayar->order ?? 0) }}" min="0" placeholder="0">
                            @error('order')
                                <div class="neo-error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="neo-form-group">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="is_public" value="1" id="is_public" class="w-5 h-5 text-blue-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer"
                                {{ old('is_public', $ayar->is_public ?? false) ? 'checked' : '' }}>
                            <label for="is_public" class="w-5 h-5 text-blue-600 bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer-label">Herkese açık ayar</label>
                        </div>
                        @error('is_public')
                            <div class="neo-error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.ayarlar.index') }}" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">İptal</a>
                        <button type="submit" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                            <i class="neo-icon neo-icon-save mr-2"></i>Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
