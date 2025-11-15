@extends('admin.layouts.neo')

@section('title', 'Blog Kategorileri')
@section('page-title', 'Blog Kategorileri')

@push('styles')
    {{-- ✅ DUPLICATE REMOVED: Common styles moved to resources/css/admin/common-styles.css --}}
    {{-- Bu sayfada sadece sayfa-spesifik stiller varsa buraya eklenebilir --}}
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
        <!-- Header with Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8 p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h1
                        class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        📁 Blog Kategorileri
                    </h1>
                    <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">Blog yazılarınızı organize etmek için kategoriler oluşturun</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.blog.categories.create') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95 touch-target-optimized touch-target-optimized">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Yeni Kategori
                    </a>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Categories List -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        Kategoriler
                    </h3>
                </div>
                <div class="p-6">
                    @if ($categories->isEmpty())
                        <x-neo.empty-state title="Henüz kategori yok" description="İlk kategoriyi oluşturarak başlayın"
                            :actionHref="route('admin.blog.categories.create')" actionText="Kategori Oluştur" />
                    @else
                        <div class="space-y-4">
                            @foreach ($categories as $category)
                                <div
                                    class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white"
                                            style="background-color: {{ $category->color ?? '#6366f1' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('admin.blog.categories.edit', $category) }}" class="hover:underline">
                                                    {{ $category->name }}
                                                </a>
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $category->description ?? 'Açıklama yok' }}
                                            </p>
                                            <div class="flex items-center space-x-4 mt-1">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $category->posts_count }} yazı
                                                </span>
                                                <x-neo.status-badge :value="$item->status ? 'Aktif' : 'Pasif'" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.blog.categories.edit', $category) }}"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 touch-target-optimized touch-target-optimized" title="Düzenle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('blog.category', $category->slug) }}" target="_blank"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 touch-target-optimized touch-target-optimized" title="Görüntüle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                </path>
                                            </svg>
                                        </a>

                                        <!-- Status Toggle -->
                                        <form method="POST"
                                            action="{{ route('admin.blog.categories.toggle', $category) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 touch-target-optimized touch-target-optimized"
                                                title="{{ $item->status ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                @if ($item->status)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>

                                        @if ($category->posts_count == 0)
                                            <form method="POST"
                                                action="{{ route('admin.blog.categories.destroy', $category) }}"
                                                onsubmit="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-danger touch-target-optimized touch-target-optimized"
                                                    title="Sil">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($categories->hasPages())
                            <div class="mt-6">
                                {{ $categories->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Quick Create Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Hızlı Kategori Oluştur
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.blog.categories.store') }}" x-data="categoryForm()">
                        @csrf

                        <div class="space-y-4">
                            <!-- Name -->
                            <div class="form-field">
                                <label class="admin-label admin-label-required">Kategori Adı</label>
                                <input type="text" name="name" value="{{ old('name') }}" x-model="name"
                                    @input="generateSlug()" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('name') admin-input-error @enderror"
                                    placeholder="Kategori adı..." required>
                                @error('name')
                                    <p class="form-error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div class="form-field">
                                <label class="admin-label">URL Slug</label>
                                <input type="text" name="slug" value="{{ old('slug') }}" x-model="slug"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('slug') admin-input-error @enderror" placeholder="url-slug">
                                @error('slug')
                                    <p class="form-error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="form-field">
                                <label class="admin-label">Açıklama</label>
                                <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('description') admin-input-error @enderror"
                                    placeholder="Kategori açıklaması...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="form-error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Color -->
                            <div class="form-field">
                                <label class="admin-label">Renk</label>
                                <div class="flex items-center space-x-2">
                                    <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                                        class="w-12 h-10 rounded border border-gray-300">
                                    <input type="text" name="color_hex" value="{{ old('color', '#6366f1') }}"
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 flex-1 @error('color') admin-input-error @enderror"
                                        placeholder="#6366f1">
                                </div>
                                @error('color')
                                    <p class="form-error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Active Status -->
                            <div class="form-field">
                                <label class="flex items-center">
                                    <input type="checkbox" name="status" value="1"
                                        {{ old('status', '1') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 admin-label">Aktif</span>
                                </label>
                            </div>

                            <!-- SEO Fields -->
                            <div class="form-field">
                                <label class="admin-label">Meta Açıklama</label>
                                <textarea name="meta_description" rows="2"
                                    class="admin-input @error('meta_description') admin-input-error @enderror" placeholder="SEO için meta açıklama...">{{ old('meta_description') }}</textarea>
                                @error('meta_description')
                                    <p class="form-error-message">{{ $message }}</p>
                                @enderror
        </div>

        <!-- Quick Create Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Hızlı Kategori Oluştur
                </h3>
        @if (!$categories->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mt-8 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 012 2v6a2 2 0 002 2h2a2 2 0 002-2v-6">
                            </path>
                        </svg>
                        Kategori İstatistikleri
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="stat-card-value text-blue-600 dark:text-blue-400">{{ $categories->count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Toplam Kategori</div>
                        </div>
                        <div class="text-center">
                            <div class="stat-card-value text-green-600 dark:text-green-400">
                                {{ $categories->where('status', true)->count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Aktif Kategori</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $categories->sum('posts_count') }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Toplam Yazı</div>
                        </div>
                        <div class="text-center">
                            <div class="stat-card-value text-purple-600 dark:text-purple-400">
                                {{ $categories->where('posts_count', '>', 0)->count() }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Yazı İçeren Kategori</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        // Alpine.js data
        function categoryForm() {
            return {
                name: '',
                slug: '',
                generateSlug() {
                    if (this.name) {
                        this.slug = this.name
                            .toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-')
                            .trim();
                    }
                }
            }
        }

        // Color picker sync
        document.querySelectorAll('input[type="color"]').forEach(colorInput => {
            const textInput = colorInput.nextElementSibling;

            colorInput.addEventListener('change', function() {
                textInput.value = this.value;
            });

            textInput.addEventListener('input', function() {
                if (this.value.match(/^#[0-9A-F]{6}$/i)) {
                    colorInput.value = this.value;
                }
            });
        });
    </script>
@endsection
