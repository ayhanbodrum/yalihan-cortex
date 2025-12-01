@extends('admin.layouts.admin')

@section('title', 'Özellik Kategorisi Düzenle')

@section('content_header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Kategori Düzenle: {{ $kategori->name }}</h1>
        <a href="{{ route('admin.ozellikler.kategoriler.index') }}"
            class="px-4 py-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-outline  rounded-lg shadow-sm transition-colors duration-200 touch-target-optimized touch-target-optimized">
            <i class="fas fa-arrow-left mr-2"></i> Geri Dön
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.ozellikler.kategoriler.update', $kategori->id) }}" method="POST"
                x-data="{ showAdvanced: false }">
                @csrf
                @method('PUT')
                @if ($errors->any())
                    <x-admin.alert type="error">
                        <p class="font-semibold mb-1">Lütfen aşağıdaki hataları düzeltin:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-admin.alert>
                @endif
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <x-admin.input name="name" label="Kategori Adı" :required="true" :value="$kategori->name" />
                        <x-admin.textarea name="description" label="Açıklama" :value="$kategori->description" rows="4" />

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Uygulama Alanı
                            </label>
                            <select name="applies_to" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Tüm Emlak Türleri</option>
                                <option value="konut" {{ old('applies_to', $kategori->applies_to) == 'konut' ? 'selected' : '' }}>Konut</option>
                                <option value="arsa" {{ old('applies_to', $kategori->applies_to) == 'arsa' ? 'selected' : '' }}>Arsa</option>
                                <option value="yazlik" {{ old('applies_to', $kategori->applies_to) == 'yazlik' ? 'selected' : '' }}>Yazlık</option>
                                <option value="isyeri" {{ old('applies_to', $kategori->applies_to) == 'isyeri' ? 'selected' : '' }}>İşyeri</option>
                                <option value="konut,arsa" {{ old('applies_to', $kategori->applies_to) == 'konut,arsa' ? 'selected' : '' }}>Konut + Arsa</option>
                                <option value="konut,arsa,yazlik,isyeri" {{ old('applies_to', $kategori->applies_to) == 'konut,arsa,yazlik,isyeri' ? 'selected' : '' }}>Tüm Türler</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Bu kategori hangi emlak türleri için geçerli olsun?</p>
                        </div>

                        <x-admin.input name="order" type="number" label="Sıra" :value="$kategori->order" />
                        <x-admin.toggle name="status" label="Aktif" :checked="$kategori->status" />
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
                            <div>
                                <x-admin.input name="slug" label="Slug" :value="$kategori->slug"
                                    help="Boş ise addan türetilir" />
                                <p id="slug-feedback" class="mt-1 text-xs"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('admin.ozellikler.kategoriler.index') }}"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow-sm text-sm">İptal</a>
                    <button type="submit"
                        class="px-5 py-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg rounded-lg text-sm font-medium touch-target-optimized touch-target-optimized">Güncelle</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kategori İstatistikleri -->
    <div class="mt-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Kategori İstatistikleri</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h3 class="admin-h3">Özellik Sayısı</h3>
                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $kategori->features->count() }}
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h3 class="admin-h3">Oluşturulma Tarihi</h3>
                    <p class="text-md font-medium text-gray-600 dark:text-gray-400">
                        {{ $kategori->created_at->format('d.m.Y H:i') }}</p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h3 class="admin-h3">Son Güncelleme</h3>
                    <p class="text-md font-medium text-gray-600 dark:text-gray-400">
                        {{ $kategori->updated_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.ozellikler.kategoriler.ozellikler', $kategori->id) }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:text-indigo-100 dark:bg-indigo-900 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-list mr-2"></i> Bu Kategorideki Özellikleri Görüntüle
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const adInput = document.getElementById('ad');
            const slugInput = document.getElementById('slug');
            const slugFeedback = document.getElementById('slug-feedback');
            let lastCheckedSlug = '';

            function generateSlug(t) {
                return t.toLowerCase().replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's').replace(/ı/g, 'i')
                    .replace(/ö/g, 'o').replace(/ç/g, 'c').replace(/[^a-z0-9]+/g, '-').replace(/-+/g, '-').replace(
                        /^-|-$/g, '');
            }

            function debounce(fn, wait = 400) {
                let to;
                return (...a) => {
                    clearTimeout(to);
                    to = setTimeout(() => fn(...a), wait);
                }
            }

            function setFeedback(state, msg) {
                if (!slugFeedback) return;
                slugFeedback.textContent = msg || '';
                slugFeedback.className = 'mt-1 text-xs ' + (state === 'ok' ? 'text-green-600 dark:text-green-400' :
                    state === 'err' ? 'text-red-600 dark:text-red-400' : 'text-gray-400');
                if (slugInput) {
                    slugInput.classList.remove('border-red-500', 'border-green-500');
                    if (state === 'ok') slugInput.classList.add('border-green-500');
                    if (state === 'err') slugInput.classList.add('border-red-500');
                }
            }
            adInput?.addEventListener('input', () => {
                if (slugInput && !slugInput.value) {
                    slugInput.value = generateSlug(adInput.value);
                }
            });
            const checkSlug = debounce(() => {
                if (!slugInput) return;
                const val = slugInput.value.trim();
                if (!val) {
                    setFeedback('neutral', '');
                    return;
                }
                if (val === lastCheckedSlug) return;
                lastCheckedSlug = val;
                setFeedback('neutral', 'Kontrol ediliyor...');
                fetch(
                        `{{ route('admin.ozellikler.kategoriler.slug.check') }}?slug=${encodeURIComponent(val)}&ignore={{ $kategori->id }}`
                        )
                    .then(r => r.json()).then(d => {
                        if (d.unique) {
                            setFeedback('ok', 'Uygun');
                        } else {
                            setFeedback('err', 'Slug kullanımda');
                        }
                    })
                    .catch(() => setFeedback('err', 'Kontrol hatası'));
            }, 500);
            slugInput?.addEventListener('input', checkSlug);
        });
    </script>
@endpush
