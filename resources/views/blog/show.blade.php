@extends('layouts.frontend')

@section('title', $post->meta_title ?: $post->title)
@section('meta-description', $post->meta_description ?: $post->excerpt_or_content)
@section('meta-keywords', $post->meta_keywords)

@push('styles')
    <style>
        /* Blog post specific styles */
        .blog-post-header {
            @apply bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 relative overflow-hidden;
        }

        .blog-post-content {
            @apply prose prose-lg prose-gray dark:prose-invert max-w-none;
        }

        .blog-post-content img {
            @apply rounded-lg shadow-lg my-8 w-full h-auto;
        }

        .blog-post-content h1 {
            @apply text-3xl font-bold text-gray-900 dark:text-white mt-8 mb-4;
        }

        .blog-post-content h2 {
            @apply text-2xl font-bold text-gray-900 dark:text-white mt-8 mb-4;
        }

        .blog-post-content h3 {
            @apply text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3;
        }

        .blog-post-content p {
            @apply text-gray-700 dark:text-gray-300 leading-relaxed mb-6;
        }

        .blog-post-content blockquote {
            @apply border-l-4 border-orange-500 pl-6 py-4 my-8 bg-orange-50 dark:bg-orange-900/20 italic text-lg;
        }

        .blog-post-content ul,
        .blog-post-content ol {
            @apply my-6 pl-6 space-y-2;
        }

        .blog-post-content li {
            @apply text-gray-700 dark:text-gray-300;
        }

        .blog-post-content a {
            @apply text-orange-600 dark:text-orange-400 hover:underline;
        }

        .blog-post-content table {
            @apply w-full border-collapse border border-gray-300 dark:border-gray-600 my-8;
        }

        .blog-post-content th,
        .blog-post-content td {
            @apply border border-gray-300 dark:border-gray-600 px-4 py-2 text-left;
        }

        .blog-post-content th {
            @apply bg-gray-50 dark:bg-gray-800 font-semibold;
        }

        .comment-form {
            @apply bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6;
        }

        .comment-card {
            @apply bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6;
        }

        .comment-reply {
            @apply ml-12 mt-4;
        }

        .social-share-btn {
            @apply inline-flex items-center justify-center w-10 h-10 rounded-full text-white transition-all duration-200 hover:scale-110;
        }
    </style>
@endpush

@section('content')
    <!-- Post Header -->
    <section class="blog-post-header py-16 lg:py-20">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative public-container">
            <div class="max-w-4xl mx-auto text-center text-white">
                @if ($post->category)
                    <div class="mb-4">
                        <a href="{{ route('blog.category', $post->category->slug) }}"
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium text-white border border-white/30 hover:bg-white/10 transition-colors"
                            style="border-color: {{ $post->category->color ?? '#ffffff' }}; color: {{ $post->category->color ?? '#ffffff' }}">
                            {{ $post->category->name }}
                        </a>
                    </div>
                @endif

                <h1 class="text-3xl lg:text-5xl font-bold mb-6">{{ $post->title }}</h1>

                @if ($post->excerpt)
                    <p class="text-xl lg:text-2xl mb-8 opacity-90">{{ $post->excerpt }}</p>
                @endif

                <div class="flex flex-wrap items-center justify-center text-sm space-x-6 opacity-90">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-user"></i>
                        <span>{{ $post->user->name }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar"></i>
                        <span>{{ $post->published_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock"></i>
                        <span>{{ $post->reading_time_formatted }} okuma</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-eye"></i>
                        <span>{{ number_format($post->view_count) }} görüntülenme</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Post Content -->
    <section class="public-section bg-white dark:bg-gray-900">
        <div class="public-container">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <article
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        @if ($post->featured_image)
                            <div class="aspect-video overflow-hidden">
                                <img src="{{ $post->featured_image }}"
                                    alt="{{ $post->featured_image_alt ?? $post->title }}"
                                    class="w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="p-8 lg:p-12">
                            <!-- Breaking News Badge -->
                            @if ($post->is_breaking_news)
                                <div class="mb-6">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        Son Dakika
                                    </span>
                                </div>
                            @endif

                            <!-- Content -->
                            <div class="blog-post-content">
                                {!! $post->content !!}
                            </div>

                            <!-- Tags -->
                            @if ($post->tags->isNotEmpty())
                                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Etiketler</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($post->tags as $tag)
                                            <a href="{{ route('blog.tag', $tag->slug) }}"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 hover:bg-orange-100 hover:text-orange-800 dark:hover:bg-orange-900/30 dark:hover:text-orange-400 transition-colors">
                                                #{{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Social Share -->
                            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Paylaş</h3>
                                <div class="flex items-center space-x-3">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                        target="_blank" class="social-share-btn bg-blue-600 hover:bg-blue-700"
                                        title="Facebook'ta Paylaş">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}"
                                        target="_blank" class="social-share-btn bg-sky-500 hover:bg-sky-600"
                                        title="Twitter'da Paylaş">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                                        target="_blank" class="social-share-btn bg-blue-700 hover:bg-blue-800"
                                        title="LinkedIn'de Paylaş">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="https://wa.me/?text={{ urlencode($post->title . ' - ' . request()->url()) }}"
                                        target="_blank" class="social-share-btn bg-green-500 hover:bg-green-600"
                                        title="WhatsApp'ta Paylaş">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <button onclick='copyToClipboard(@json(request()->url()))'
                                        class="social-share-btn bg-gray-600 hover:bg-gray-700" title="Linki Kopyala">
                                        <i class="fas fa-link"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Post Navigation -->
                            @if ($prevPost || $nextPost)
                                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @if ($prevPost)
                                            <a href="{{ route('blog.show', $prevPost->slug) }}"
                                                class="group block p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 transition-colors">
                                                <div
                                                    class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-2">
                                                    <i class="fas fa-arrow-left mr-2"></i>
                                                    Önceki Yazı
                                                </div>
                                                <h4
                                                    class="font-medium text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                                    {{ Str::limit($prevPost->title, 60) }}
                                                </h4>
                                            </a>
                                        @endif

                                        @if ($nextPost)
                                            <a href="{{ route('blog.show', $nextPost->slug) }}"
                                                class="group block p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 transition-colors text-right">
                                                <div
                                                    class="flex items-center justify-end text-sm text-gray-500 dark:text-gray-400 mb-2">
                                                    Sonraki Yazı
                                                    <i class="fas fa-arrow-right ml-2"></i>
                                                </div>
                                                <h4
                                                    class="font-medium text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                                    {{ Str::limit($nextPost->title, 60) }}
                                                </h4>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </article>

                    <!-- Related Posts -->
                    @if ($relatedPosts->isNotEmpty())
                        <div class="mt-12">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">İlgili Yazılar</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach ($relatedPosts as $relatedPost)
                                    <article
                                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow">
                                        @if ($relatedPost->featured_image)
                                            <div class="aspect-video overflow-hidden">
                                                <img src="{{ $relatedPost->featured_image }}"
                                                    alt="{{ $relatedPost->title }}"
                                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                            </div>
                                        @endif

                                        <div class="p-6">
                                            @if ($relatedPost->category)
                                                <div class="mb-3">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white"
                                                        style="background-color: {{ $relatedPost->category->color ?? '#6366f1' }}">
                                                        {{ $relatedPost->category->name }}
                                                    </span>
                                                </div>
                                            @endif

                                            <h3
                                                class="text-lg font-bold text-gray-900 dark:text-white mb-3 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                                <a
                                                    href="{{ route('blog.show', $relatedPost->slug) }}">{{ $relatedPost->title }}</a>
                                            </h3>

                                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                                {{ Str::limit($relatedPost->excerpt, 100) }}</p>

                                            <div
                                                class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                                <span><i
                                                        class="fas fa-calendar mr-1"></i>{{ $relatedPost->published_at->format('d.m.Y') }}</span>
                                                <span><i class="fas fa-eye mr-1"></i>{{ $relatedPost->view_count }}</span>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Comments Section -->
                    @if ($post->allow_comments)
                        <div class="mt-12" id="comments">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">
                                Yorumlar ({{ $comments->total() }})
                            </h2>

                            <!-- Comment Form -->
                            <div class="comment-form mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Yorum Yap</h3>

                                <form method="POST" action="{{ route('blog.comments.store', $post) }}"
                                    class="space-y-6">
                                    @csrf

                                    @guest
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <x-form.input name="guest_name" label="Adınız" required placeholder="Adınız" />
                                            <x-form.input type="email" name="guest_email" label="E-posta" required
                                                placeholder="ornek@mail.com" />
                                        </div>
                                        <x-form.input name="guest_website" label="Web Sitesi (Opsiyonel)" type="url"
                                            placeholder="https://..." />
                                    @endguest

                                    <x-form.textarea name="content" label="Yorumunuz" rows="4" required
                                        placeholder="Yorumunuzu yazın..." />

                                    <div>
                                        <button type="submit" class="neo-btn neo-btn-primary">
                                            <i class="fas fa-comment mr-2"></i>
                                            Yorum Gönder
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Comments List -->
                            @if ($comments->isNotEmpty())
                                <div class="space-y-6">
                                    @foreach ($comments as $comment)
                                        <div class="comment-card" id="comment-{{ $comment->id }}">
                                            <div class="flex items-start space-x-4">
                                                <div class="flex-shrink-0">
                                                    @if ($comment->user)
                                                        <div
                                                            class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                                                            <span
                                                                class="text-lg font-medium text-orange-600 dark:text-orange-400">
                                                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-user text-gray-400"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-3 mb-2">
                                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                                            {{ $comment->author_name }}</h4>
                                                        <span
                                                            class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                                        @if ($comment->user)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                                Üye
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                                                        {{ $comment->content }}</p>

                                                    <div class="flex items-center space-x-4 text-sm">
                                                        <button onclick="likeComment({{ $comment->id }})"
                                                            class="flex items-center space-x-1 text-gray-500 hover:text-green-600 transition-colors">
                                                            <i class="fas fa-thumbs-up"></i>
                                                            <span
                                                                id="likes-{{ $comment->id }}">{{ $comment->like_count }}</span>
                                                        </button>

                                                        <button onclick="dislikeComment({{ $comment->id }})"
                                                            class="flex items-center space-x-1 text-gray-500 hover:text-red-600 transition-colors">
                                                            <i class="fas fa-thumbs-down"></i>
                                                            <span
                                                                id="dislikes-{{ $comment->id }}">{{ $comment->dislike_count }}</span>
                                                        </button>

                                                        <button onclick="toggleReplyForm({{ $comment->id }})"
                                                            class="text-gray-500 hover:text-orange-600 transition-colors">
                                                            <i class="fas fa-reply mr-1"></i>
                                                            Yanıtla
                                                        </button>
                                                    </div>

                                                    <!-- Reply Form -->
                                                    <div id="reply-form-{{ $comment->id }}"
                                                        class="hidden mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                                        <form method="POST"
                                                            action="{{ route('blog.comments.store', $post) }}">
                                                            @csrf
                                                            <input type="hidden" name="parent_id"
                                                                value="{{ $comment->id }}">

                                                            @guest
                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                                    <x-form.input name="guest_name" label="Adınız" required />
                                                                    <x-form.input type="email" name="guest_email"
                                                                        label="E-posta" required />
                                                                </div>
                                                            @endguest

                                                            <x-form.textarea name="content" label="Yanıt" rows="3"
                                                                required placeholder="Yanıtınızı yazın..." />

                                                            <div class="flex space-x-3">
                                                                <button type="submit"
                                                                    class="neo-btn neo-btn-primary btn-sm inline-flex items-center gap-1">
                                                                    <i class="fas fa-paper-plane text-xs"></i>
                                                                    <span>Yanıt Gönder</span>
                                                                </button>
                                                                <button type="button"
                                                                    onclick="toggleReplyForm({{ $comment->id }})"
                                                                    class="neo-btn neo-btn-secondary btn-sm inline-flex items-center gap-1">
                                                                    <i class="fas fa-times text-xs"></i>
                                                                    <span>İptal</span>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <!-- Replies -->
                                                    @if ($comment->replies->isNotEmpty())
                                                        <div class="comment-reply">
                                                            @foreach ($comment->replies as $reply)
                                                                <div class="comment-card mt-4">
                                                                    <div class="flex items-start space-x-4">
                                                                        <div class="flex-shrink-0">
                                                                            @if ($reply->user)
                                                                                <div
                                                                                    class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                                                                                    <span
                                                                                        class="text-sm font-medium text-orange-600 dark:text-orange-400">
                                                                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                                                    </span>
                                                                                </div>
                                                                            @else
                                                                                <div
                                                                                    class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                                                                    <i
                                                                                        class="fas fa-user text-gray-400 text-sm"></i>
                                                                                </div>
                                                                            @endif
                                                                        </div>

                                                                        <div class="flex-1">
                                                                            <div class="flex items-center space-x-3 mb-2">
                                                                                <h5
                                                                                    class="font-medium text-gray-900 dark:text-white">
                                                                                    {{ $reply->author_name }}</h5>
                                                                                <span
                                                                                    class="text-sm text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                                                @if ($reply->user)
                                                                                    <span
                                                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                                                        Üye
                                                                                    </span>
                                                                                @endif
                                                                            </div>

                                                                            <p class="text-gray-700 dark:text-gray-300">
                                                                                {{ $reply->content }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                @if ($comments->hasPages())
                                    <div class="mt-8">
                                        {{ $comments->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-comments text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Henüz yorum yok</h3>
                                    <p class="text-gray-600 dark:text-gray-400">İlk yorumu siz yazın!</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8 space-y-8">
                        <!-- Categories -->
                        @if ($sidebarData['categories']->isNotEmpty())
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Kategoriler</h3>
                                <div class="space-y-2">
                                    @foreach ($sidebarData['categories'] as $category)
                                        <a href="{{ route('blog.category', $category->slug) }}"
                                            class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-3 h-3 rounded-full"
                                                    style="background-color: {{ $category->color ?? '#6366f1' }}"></div>
                                                <span
                                                    class="text-gray-700 dark:text-gray-300 group-hover:text-orange-600 dark:group-hover:text-orange-400">{{ $category->name }}</span>
                                            </div>
                                            <span
                                                class="text-sm text-gray-500 dark:text-gray-400">{{ $category->posts_count }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Popular Posts -->
                        @if ($sidebarData['popular_posts']->isNotEmpty())
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Popüler Yazılar</h3>
                                <div class="space-y-4">
                                    @foreach ($sidebarData['popular_posts'] as $popularPost)
                                        <div class="flex space-x-3">
                                            @if ($popularPost->featured_image)
                                                <img src="{{ $popularPost->featured_image }}"
                                                    alt="{{ $popularPost->title }}"
                                                    class="w-16 h-12 object-cover rounded">
                                            @else
                                                <div
                                                    class="w-16 h-12 bg-gray-200 dark:bg-gray-800 rounded flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <h4
                                                    class="text-sm font-medium text-gray-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                                    <a
                                                        href="{{ route('blog.show', $popularPost->slug) }}">{{ Str::limit($popularPost->title, 50) }}</a>
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
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Popüler Etiketler</h3>
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
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const button = event.target.closest('button');
                const icon = button.querySelector('i');
                icon.className = 'fas fa-check';
                setTimeout(() => {
                    icon.className = 'fas fa-link';
                }, 2000);
            });
        }

        // Like comment function
        function likeComment(commentId) {
            fetch(`/blog/comments/${commentId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`likes-${commentId}`).textContent = data.likes;
                    }
                });
        }

        // Dislike comment function
        function dislikeComment(commentId) {
            fetch(`/blog/comments/${commentId}/dislike`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`dislikes-${commentId}`).textContent = data.dislikes;
                    }
                });
        }

        // Toggle reply form
        function toggleReplyForm(commentId) {
            const form = document.getElementById(`reply-form-${commentId}`);
            form.classList.toggle('hidden');
        }

        // Reading progress indicator (optional)
        document.addEventListener('DOMContentLoaded', function() {
            const article = document.querySelector('article');
            if (article) {
                // Track reading progress for analytics
                let readingStarted = false;
                let readingTime = 0;

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !readingStarted) {
                            readingStarted = true;
                            const startTime = Date.now();

                            // Track reading time every 5 seconds
                            setInterval(() => {
                                readingTime += 5;
                                // Send reading time to server (optional)
                            }, 5000);
                        }
                    });
                });

                observer.observe(article);
            }
        });
    </script>
@endpush
