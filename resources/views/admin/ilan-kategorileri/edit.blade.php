@extends('admin.layouts.neo')

@section('title', 'Kategori Düzenle')

@section('content')
    <div class="container mx-auto px-4 py-6" x-data="kategoriForm()">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Kategori Düzenle</h1>
                <a href="{{ route('admin.ilan-kategorileri.index') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                    ← Geri Dön
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30 rounded-lg p-4 mb-6">
                <span class="text-green-700 dark:text-green-200">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30 rounded-lg p-4 mb-6">
                <span class="text-red-700 dark:text-red-200">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="p-6">
                <form method="POST" action="{{ route('admin.ilan-kategorileri.update', $kategori) }}" @submit.prevent="submitForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Adı *</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" value="{{ old('name', $kategori->name) }}" required placeholder="Örn: Daire, Villa">
                            @error('name')
                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="seviye" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Seviye *</label>
                            <select style="color-scheme: light dark;" id="seviye" name="seviye" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200" required @change="updateParentOptions()">
                                <option value="">Seçiniz</option>
                                <option value="0" {{ old('seviye', $kategori->seviye) == 0 ? 'selected' : '' }}>Ana Kategori</option>
                                <option value="1" {{ old('seviye', $kategori->seviye) == 1 ? 'selected' : '' }}>Alt Kategori</option>
                                <option value="2" {{ old('seviye', $kategori->seviye) == 2 ? 'selected' : '' }}>Yayın Tipi</option>
                            </select>
                            @error('seviye')
                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2" id="parent-field" x-show="parentRequired" x-cloak>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Üst Kategori <span x-show="parentRequired" x-cloak>*</span></label>
                            <select style="color-scheme: light dark;" id="parent_id" name="parent_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Seçiniz</option>
                                @foreach ($parentCategories as $anaKategori)
                                    @if($anaKategori->id != $kategori->id)
                                        <option value="{{ $anaKategori->id }}" {{ old('parent_id', $kategori->parent_id) == $anaKategori->id ? 'selected' : '' }}>
                                            {{ $anaKategori->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sıra</label>
                            <input type="number" id="order" name="order" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" value="{{ old('order', $kategori->display_order) }}" min="0">
                            @error('order')
                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('status', $kategori->status) == 1 ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-900 dark:text-white">Active</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="0" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('status', $kategori->status) == 0 ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-900 dark:text-white">Inactive</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.ilan-kategorileri.index') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">İptal</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95" :disabled="loading">
                            <span x-show="!loading">Kaydet</span>
                            <span x-show="loading">Kaydediliyor...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function kategoriForm() {
            return {
                loading: false,
                parentRequired: {{ $kategori->seviye > 0 ? 'true' : 'false' }},
                updateParentOptions() {
                    const seviye = document.getElementById('seviye').value;

                    if (seviye == '1' || seviye == '2') {
                        this.parentRequired = true;
                    } else {
                        this.parentRequired = false;
                        document.getElementById('parent_id').value = '';
                    }
                },
                submitForm(event) {
                    if (this.parentRequired && !document.getElementById('parent_id').value) {
                        event.preventDefault();
                        alert('Üst Kategori seçmelisiniz!');
                        return false;
                    }

                    this.loading = true;
                    event.target.submit();
                }
            }
        }
    </script>
@endsection
