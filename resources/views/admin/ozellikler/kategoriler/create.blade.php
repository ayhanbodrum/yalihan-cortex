@extends('admin.layouts.neo')

@section('title', 'Yeni Özellik Kategorisi')

@section('content_header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Yeni Özellik Kategorisi</h1>
        <a href="{{ route('admin.ozellikler.kategoriler.index') }}"
            class="px-4 py-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-outline rounded-lg shadow-sm transition-colors duration-200 touch-target-optimized touch-target-optimized">
            <i class="fas fa-arrow-left mr-2"></i> Geri Dön
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.ozellikler.kategoriler.store') }}" method="POST"
                x-data="{ showAdvanced: false }">
                @csrf
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2.5 rounded mb-4">
                        <p class="font-semibold mb-1">Lütfen aşağıdaki hataları düzeltin:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Kategori Adı <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Açıklama
                            </label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Uygulama Alanı
                            </label>
                            <select style="color-scheme: light dark;" name="applies_to" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500 w-full transition-all duration-200">
                                <option value="">Tüm Emlak Türleri</option>
                                <option value="konut" {{ old('applies_to') == 'konut' ? 'selected' : '' }}>Konut</option>
                                <option value="arsa" {{ old('applies_to') == 'arsa' ? 'selected' : '' }}>Arsa</option>
                                <option value="yazlik" {{ old('applies_to') == 'yazlik' ? 'selected' : '' }}>Yazlık</option>
                                <option value="isyeri" {{ old('applies_to') == 'isyeri' ? 'selected' : '' }}>İşyeri</option>
                                <option value="konut,arsa" {{ old('applies_to') == 'konut,arsa' ? 'selected' : '' }}>Konut + Arsa</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Bu kategori hangi emlak türleri için geçerli olsun?</p>
                        </div>

                        <div class="mb-4">
                            <label for="order" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Sıra
                            </label>
                            <input type="number" id="order" name="order" value="{{ old('order', 0) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">Aktif</span>
                            </label>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <button type="button" @click="showAdvanced=!showAdvanced"
                                class="text-sm text-indigo-600 hover:underline flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Gelişmiş Alanlar
                            </button>
                        </div>
                        <div x-show="showAdvanced" x-cloak>
                            <div class="mb-4">
                                <label for="icon" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    İkon
                                </label>
                                <input type="text" id="icon" name="icon" value="{{ old('icon') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Örn: fas fa-home">
                                @error('icon')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="renk" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Renk
                                </label>
                                <input type="color" id="renk" name="renk" value="{{ old('renk', '#3B82F6') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('renk')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('admin.ozellikler.kategoriler.index') }}"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        İptal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
