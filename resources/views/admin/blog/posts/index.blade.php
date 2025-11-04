@extends('admin.layouts.neo')

@section('title', 'Blog Yazıları')
@section('page-title', 'Blog Yazıları')

@push('styles')
    <style>
        /* Modern Dashboard Styles */
        .btn-modern {
            @apply inline-flex items-center px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg;
        }

        .btn-modern-primary {
            @apply bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 shadow-blue-500/25;
        }

        .btn-modern-secondary {
            @apply bg-gradient-to-r from-gray-600 to-gray-700 text-white hover:from-gray-700 hover:to-gray-800 shadow-gray-500/25;
        }

        .form-field {
            @apply space-y-2;
        }

        .admin-label {
            @apply block text-sm font-medium text-gray-700 mb-1;
        }

        .admin-input,
        .admin-input {
            @apply w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 focus:outline-none transition-all duration-200;
        }

        .admin-input:focus,
        .admin-input:focus {
            @apply ring-2 ring-blue-200;
        }

        /* Table Styles */
        .min-w-full {
            @apply w-full;
        }

        .divide-y> :not([hidden])~ :not([hidden]) {
            @apply border-t border-gray-200;
        }

        .bg-gray-50 {
            @apply bg-gray-50;
        }

        .hover\:bg-gray-50:hover {
            @apply bg-gray-50;
        }

        .transition-colors {
            @apply transition-colors;
        }

        .duration-200 {
            @apply duration-200;
        }

        /* Pagination Styles */
        .pagination {
            @apply flex items-center justify-between;
        }

        .pagination .page-link {
            @apply px-4 py-2.5 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700;
        }

        .pagination .page-item.active .page-link {
            @apply bg-blue-600 text-white border-blue-600;
        }

        .pagination .page-item.disabled .page-link {
            @apply text-gray-300 cursor-not-allowed;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
        <!-- Header with Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8 p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h1
                        class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        📝 Blog Yazıları
                    </h1>
                    <p class="mt-3 text-lg text-gray-600">Tüm blog yazılarınızı yönetin</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.blog.posts.create') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Yeni Yazı
                    </a>
                    <a href="{{ route('blog.index') }}" target="_blank" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Blogu Görüntüle
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8 p-6">
            <form method="GET" action="{{ route('admin.blog.posts.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-field">
                    <label class="admin-label">Ara</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Başlık veya içerik ara..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200">
                </div>

                <div class="form-field">
                    <label class="admin-label">Durum</label>
                    <select style="color-scheme: light dark;" name="status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tüm Durumlar</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Yayınlanan
                        </option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Taslak</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Programlı
                        </option>
                    </select>
                </div>

                <div class="form-field">
                    <label class="admin-label">Kategori</label>
                    <select style="color-scheme: light dark;" name="category" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tüm Kategoriler</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label class="admin-label">&nbsp;</label>
                    <div class="flex space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Filtrele
                        </button>
                        <a href="{{ route('admin.blog.posts.index') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Temizle
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Posts Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-6">
                @if ($posts->isEmpty())
                    <x-neo.empty-state title="Henüz yazı yok" description="İlk blog yazınızı oluşturarak başlayın"
                        :actionHref="route('admin.blog.posts.create')" actionText="Yeni Yazı Oluştur" />
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="admin-table-th">
                                        Yazı</th>
                                    <th
                                        class="admin-table-th">
                                        Kategori</th>
                                    <th
                                        class="admin-table-th">
                                        Durum</th>
                                    <th
                                        class="admin-table-th">
                                        Görüntülenme</th>
                                    <th
                                        class="admin-table-th">
                                        Yazar</th>
                                    <th
                                        class="admin-table-th">
                                        Tarih</th>
                                    <th
                                        class="admin-table-th">
                                        İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($posts as $post)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3">
                                                @if ($post->featured_image)
                                                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                                                        class="w-12 h-12 object-cover rounded-lg">
                                                @else
                                                    <div
                                                        class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="min-w-0 flex-1">
                                                    <h4 class="font-medium text-gray-900 truncate">
                                                        <a href="{{ route('admin.blog.posts.show', $post) }}" class="hover:underline">
                                                            {{ $post->title }}
                                                        </a>
                                                    </h4>
                                                    <p class="text-sm text-gray-500">
                                                        {{ Str::limit($post->excerpt, 50) }}</p>
                                                    @if ($post->featured)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path
                                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                                </path>
                                                            </svg>Öne Çıkan
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($post->category)
                                                <a href="{{ route('admin.blog.posts.index', array_merge(request()->except('page'), ['category' => $post->category->id])) }}" class="hover:opacity-90">
                                                    <x-neo.status-badge :value="$post->category->name" category="category" />
                                                </a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusLabel = $post->status === 'published' ? 'Yayında' : ($post->status === 'draft' ? 'Taslak' : 'Programlı');
                                            @endphp
                                            <a href="{{ route('admin.blog.posts.index', array_merge(request()->except('page'), ['status' => $post->status])) }}" class="hover:opacity-90">
                                                <x-neo.status-badge :value="$statusLabel" category="status" />
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                                {{ number_format($post->view_count) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                <div class="font-medium text-gray-900">
                                                    {{ $post->user->name }}</div>
                                                <div class="text-gray-500">{{ $post->user->email }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">
                                                <div>{{ $post->created_at->format('d.m.Y') }}</div>
                                                <div>{{ $post->created_at->format('H:i') }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.blog.posts.show', $post) }}"
                                                    class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized" title="Görüntüle">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('admin.blog.posts.edit', $post) }}"
                                                    class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized" title="Düzenle">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                                                    class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized" title="Ön izleme">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                        </path>
                                                    </svg>
                                                </a>

                                                <!-- Status Toggle -->
                                                @if ($post->status === 'published')
                                                    <form method="POST"
                                                        action="{{ route('admin.blog.posts.unpublish', $post) }}"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit" class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized"
                                                            title="Yayından Kaldır">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST"
                                                        action="{{ route('admin.blog.posts.publish', $post) }}"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit" class="neo-btn neo-btn-primary touch-target-optimized touch-target-optimized"
                                                            title="Yayınla">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                                </path>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form method="POST"
                                                    action="{{ route('admin.blog.posts.destroy', $post) }}"
                                                    onsubmit="return confirm('Bu yazıyı silmek istediğinizden emin misiniz?')"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="neo-btn-danger touch-target-optimized touch-target-optimized"
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
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($posts->hasPages())
                        <div class="mt-6">
                            {{ $posts->appends(request()->query())->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Auto-submit search form on enter
        document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });

        // Auto-submit filters on change
        document.querySelectorAll('select[name="status"], select[name="category"]').forEach(function(select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
@endsection
