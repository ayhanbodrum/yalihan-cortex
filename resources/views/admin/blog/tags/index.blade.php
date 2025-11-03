@extends('admin.layouts.neo')

@section('title', 'Blog Etiketleri')
@section('page-title', 'Blog Etiketleri')

@section('content')
    <div class="container mx-auto">
        <!-- Header with Actions -->
        <div class="neo-card mb-6">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div>
                        <h1 class="admin-h1">Blog Etiketleri</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Blog yazılarınızı organize etmek için etiketler
                            oluşturun</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.blog.tags.create') }}" class="neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                            <i class="fas fa-plus mr-2"></i>
                            Yeni Etiket
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tags Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tags List -->
            <div class="neo-card">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-lg font-semibold">Etiketler</h3>
                </div>
                <div class="p-6">
                    @if ($tags->isEmpty())
                        <x-neo.empty-state title="Henüz etiket yok" description="Blog yazılarınızı organize etmek için yeni etiket oluşturun"
                            :actionHref="route('admin.blog.tags.create')" actionText="Etiket Oluştur" />
                    @else
                        <div class="space-y-4">
                            @foreach ($tags as $tag)
                                <div
                                    class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white"
                                            style="background-color: {{ $tag->color ?? '#6366f1' }}">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('blog.tag', $tag->slug) }}" target="_blank" class="hover:underline">#{{ $tag->name }}</a>
                                            </h4>
                                            <div class="flex items-center space-x-4 mt-1">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $tag->posts_count }} yazı
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $tag->usage_count }} kullanım
                                                </span>
                                                <x-neo.status-badge :value="$item->status ? 'Aktif' : 'Pasif'" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.blog.tags.edit', $tag) }}"
                                            class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('blog.tag', $tag->slug) }}" target="_blank"
                                            class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized" title="Görüntüle">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>

                                        <!-- Status Toggle -->
                                        <form method="POST" action="{{ route('admin.blog.tags.toggle', $tag) }}"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized"
                                                title="{{ $item->status ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                <i class="fas fa-{{ $item->status ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                        </form>

                                        @if ($tag->posts_count == 0)
                                            <form method="POST" action="{{ route('admin.blog.tags.destroy', $tag) }}"
                                                onsubmit="return confirm('Bu etiketi silmek istediğinizden emin misiniz?')"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="neo-btn-danger touch-target-optimized touch-target-optimized" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($tags->hasPages())
                            <div class="mt-6">
                                {{ $tags->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Quick Create Form -->
            <div class="neo-card">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-lg font-semibold">Hızlı Etiket Oluştur</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.blog.tags.store') }}" x-data="tagForm()">
                        @csrf

                        <div class="space-y-4">
                            <!-- Name -->
                            <div class="form-group">
                                <label class="admin-label admin-label-required">Etiket Adı</label>
                                <input type="text" name="name" value="{{ old('name') }}" x-model="name"
                                    @input="generateSlug()" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('name') admin-input-error @enderror"
                                    placeholder="Etiket adı..." required>
                                @error('name')
                                    <p class="form-error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div class="form-group">
                                <label class="admin-label">URL Slug</label>
                                <input type="text" name="slug" value="{{ old('slug') }}" x-model="slug"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('slug') admin-input-error @enderror" placeholder="url-slug">
                                @error('slug')
                                    <p class="form-error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Color -->
                            <div class="form-group">
                                <label class="admin-label">Renk</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                                        x-model="color"
                                        class="w-12 h-12 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                                    <input type="text" x-model="color" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 flex-1"
                                        placeholder="#6366f1">
                                </div>
                                @error('color')
                                    <p class="form-error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label class="flex items-center">
                                    <input type="checkbox" name="status" value="1"
                                        {{ old('status', true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm">Aktif</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 mt-6">
                            <button type="reset" class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                                <i class="fas fa-times mr-2"></i>
                                Temizle
                            </button>
                            <button type="submit" class="neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                                <i class="fas fa-save mr-2"></i>
                                Etiket Oluştur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        @if (!$tags->isEmpty())
            <div class="neo-card mt-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-lg font-semibold">Etiket İstatistikleri</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="stat-card-value text-blue-600 dark:text-blue-400">{{ $tags->count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Toplam Etiket</div>
                        </div>
                        <div class="text-center">
                            <div class="stat-card-value text-green-600 dark:text-green-400">
                                {{ $tags->where('status', true)->count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Aktif Etiket</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $tags->sum('posts_count') }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Toplam Kullanım</div>
                        </div>
                        <div class="text-center">
                            <div class="stat-card-value text-purple-600 dark:text-purple-400">
                                {{ $tags->where('posts_count', '>', 0)->count() }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Kullanılan Etiket</div>
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
        function tagForm() {
            return {
                name: '',
                slug: '',
                color: '#6366f1',
                generateSlug() {
                    if (this.name) {
                        // Türkçe karakterleri dönüştür ve slug oluştur
                        const turkishChars = {
                            'ç': 'c',
                            'ğ': 'g',
                            'ı': 'i',
                            'ö': 'o',
                            'ş': 's',
                            'ü': 'u',
                            'Ç': 'C',
                            'Ğ': 'G',
                            'İ': 'I',
                            'Ö': 'O',
                            'Ş': 'S',
                            'Ü': 'U'
                        };

                        this.slug = this.name
                            .replace(/[çğıöşüÇĞİÖŞÜ]/g, function(char) {
                                return turkishChars[char] || char;
                            })
                            .toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .trim()
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-');
                    }
                }
            }
        }
    </script>
@endsection
