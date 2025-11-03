@extends('layouts.frontend')

@section('title', $category->name . ' - Blog')
@section('description', $category->description ?: $category->name . ' kategorisindeki yazılar')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Category Header -->
        <div class="bg-gradient-to-r from-orange-500 to-pink-500 text-white py-16">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl"
                            style="background-color: rgba(255,255,255,0.2)">
                            <i class="fas fa-folder"></i>
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $category->name }}</h1>
                    @if ($category->description)
                        <p class="text-xl text-white/90 max-w-2xl mx-auto">{{ $category->description }}</p>
                    @endif
                    <div class="mt-6 flex items-center justify-center space-x-6 text-white/80">
                        <span><i class="fas fa-file-alt mr-2"></i>{{ $posts->total() }} yazı</span>
                        <span><i class="fas fa-eye mr-2"></i>{{ $category->posts->sum('view_count') }} görüntülenme</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-12">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <!-- Sorting Options -->
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $category->name }} Yazıları
                            </h2>

                            <div class="flex items-center space-x-4">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sırala:</label>
                                <select onchange="window.location.href=this.value" class="form-select-sm">
                                    <option
                                        value="{{ route('blog.category', ['slug' => $category->slug, 'sort' => 'latest']) }}"
                                        {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>En Yeni</option>
                                    <option
                                        value="{{ route('blog.category', ['slug' => $category->slug, 'sort' => 'popular']) }}"
                                        {{ request('sort') === 'popular' ? 'selected' : '' }}>En Popüler</option>
                                    <option
                                        value="{{ route('blog.category', ['slug' => $category->slug, 'sort' => 'oldest']) }}"
                                        {{ request('sort') === 'oldest' ? 'selected' : '' }}>En Eski</option>
                                </select>
                            </div>
                        </div>

                        <!-- Posts Grid -->
                        @if ($posts->isEmpty())
                            <div class="blog-card p-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Bu kategoride henüz yazı
                                    yok</h3>
                                <p class="text-gray-500 dark:text-gray-400">Bu kategori için yeni yazılar yayınlandığında
                                    burada görünecek.</p>
                                <div class="mt-6">
                                    <a href="{{ route('blog.index') }}" class="neo-btn neo-btn-primary">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Tüm Yazılara Dön
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                @foreach ($posts as $post)
                                    <article class="blog-card group">
                                        @if ($post->featured_image)
                                            <div class="aspect-w-16 aspect-h-9 mb-6">
                                                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                                                    class="w-full h-48 object-cover rounded-lg group-hover:scale-105 transition-transform duration-300">
                                            </div>
                                        @endif

                                        <div class="p-6">
                                            <div class="flex items-center justify-between mb-4">
                                                <span class="category-badge text-white"
                                                    style="background-color: {{ $category->color ?? '#6366f1' }}">
                                                    {{ $category->name }}
                                                </span>
                                                @if ($post->is_featured)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                        <i class="fas fa-star mr-1"></i>
                                                        Öne Çıkan
                                                    </span>
                                                @endif
                                            </div>

                                            <h3
                                                class="text-xl font-bold text-gray-900 dark:text-white mb-3 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                                <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                            </h3>

                                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $post->excerpt }}</p>

                                            <div class="blog-meta">
                                                <span><i
                                                        class="fas fa-calendar mr-1"></i>{{ $post->published_at->format('d.m.Y') }}</span>
                                                <span><i class="fas fa-user mr-1"></i>{{ $post->user->name }}</span>
                                                <span><i class="fas fa-eye mr-1"></i>{{ $post->view_count }}</span>
                                            </div>

                                            @if ($post->tags->isNotEmpty())
                                                <div class="mt-4 flex flex-wrap gap-2">
                                                    @foreach ($post->tags->take(3) as $tag)
                                                        <a href="{{ route('blog.tag', $tag->slug) }}"
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 hover:bg-orange-100 hover:text-orange-800 dark:hover:bg-orange-900/30 dark:hover:text-orange-400 transition-colors">
                                                            #{{ $tag->name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            @if ($posts->hasPages())
                                <div class="mt-12">
                                    {{ $posts->appends(request()->query())->links() }}
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="space-y-8">
                            <!-- Related Categories -->
                            @if ($sidebarData['categories']->where('id', '!=', $category->id)->isNotEmpty())
                                <div class="blog-card p-6">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Diğer Kategoriler</h3>
                                    <div class="space-y-2">
                                        @foreach ($sidebarData['categories']->where('id', '!=', $category->id) as $otherCategory)
<a href="{{ route('blog.category', $otherCategory->slug) }}"
                                           class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $otherCategory->color ?? '#6366f1' }}"></div>
                                                <span class="text-gray-700 dark:text-gray-300 group-hover:text-orange-600 dark:group-hover:text-orange-400">{{ $otherCategory->name }}</span>
                                            </div>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $otherCategory->posts_count }}</span>
                                        </a>
@endforeach
                                </div>
                            </div>
@endif

                        <!-- Popular Posts -->
                        @if ($sidebarData['popular_posts']->isNotEmpty())
                            <div class="blog-card p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Popüler Yazılar</h3>
                                <div class="space-y-4">
                                    @foreach ($sidebarData['popular_posts'] as $popularPost)
    <div class="flex items-start space-x-3">
                                                    @if ($popularPost->featured_image)
    <img src="{{ $popularPost->featured_image }}"
                                                             alt="{{ $popularPost->title }}"
                                                             class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
@else
    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center flex-shrink-0">
                                                            <i class="fas fa-image text-gray-400"></i>
                                                        </div>
     @endif
                                            <div class="flex-1 min-w-0">
                                            <h4
                                            class="text-sm font-medium text-gray-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                            <a
                                            href="{{ route('blog.show', $popularPost->slug) }}">{{ $popularPost->title }}</a>
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="fas fa-eye mr-1"></i>{{ $popularPost->view_count }}
                                            </p>
                                            </div>
                                            </div>
                                        @endforeach
                                        </div>
                                        </div>
                            @endif

                            <!-- Popular Tags -->
                            @if ($sidebarData['popular_tags']->isNotEmpty())
                            <div class="blog-card p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Popüler
                            Etiketler</h3>
                            <div class="flex flex-wrap gap-2">
                            @foreach ($sidebarData['popular_tags'] as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}"
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 hover:bg-orange-100 hover:text-orange-800 dark:hover:bg-orange-900/30 dark:hover:text-orange-400 transition-colors">
                            #{{ $tag->name }}
                            <span class="ml-1 text-xs text-gray-500">{{ $tag->posts_count }}</span>
                            </a>
                            @endforeach
                            </div>
                            </div>
                            @endif

                            <!-- Newsletter Signup -->
                            <div class="blog-card p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Bülten
                            Aboneliği</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                            Yeni yazılarımızdan haberdar olmak için e-posta adresinizi girin.
                            </p>
                            <form class="space-y-3">
                            <input type="email" placeholder="E-posta adresiniz"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            <button type="submit" class="w-full neo-btn neo-btn-primary btn-sm">
                            <i class="fas fa-envelope mr-2"></i>
                            Abone Ol
                            </button>
                            </form>
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
                        @endsection

                        @push('styles')
                            <style>
                            .blog-card {
                            @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100
                            dark:border-gray-700 transition-all duration-300;
                            }

                            .blog-card:hover {
                            @apply shadow-lg transform -translate-y-1;
                            }

                            .category-badge {
                            @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-medium;
                            }

                            .blog-meta {
                            @apply flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400;
                            }

                            .blog-meta span {
                            @apply flex items-center;
                            }
                            </style>
                        @endpush)
