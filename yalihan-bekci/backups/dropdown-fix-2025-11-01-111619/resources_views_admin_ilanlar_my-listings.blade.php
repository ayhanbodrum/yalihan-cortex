@extends('admin.layouts.neo')

@section('title', 'İlanlarım')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
        <!-- Modern Header -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        🏠 İlanlarım
                    </h1>
                    <p class="mt-3 text-lg text-gray-600">
                        Tüm ilanlarınızı yönetin ve performanslarını takip edin
                    </p>
                </div>
                <div class="flex gap-4">
                    <button class="neo-btn neo-btn-secondary" onclick="refreshListings()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.001 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Yenile
                    </button>
                    <a href="{{ route('admin.ilanlar.create') }}" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Yeni İlan
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card">
                <div class="stat-value text-blue-600">{{ $stats['total_listings'] ?? 0 }}</div>
                <div class="stat-label">Toplam İlan</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-green-600">{{ $stats['active_listings'] ?? 0 }}</div>
                <div class="stat-label">Aktif İlanlar</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-purple-600">{{ $stats['pending_listings'] ?? 0 }}</div>
                <div class="stat-label">Bekleyen</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-orange-600">{{ $stats['total_views'] ?? 0 }}</div>
                <div class="stat-label">Toplam Görüntülenme</div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                    <select id="status-filter" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tümü</option>
                        <option value="active">Aktif</option>
                        <option value="pending">Beklemede</option>
                        <option value="inactive">Pasif</option>
                        <option value="draft">Taslak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select id="category-filter" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tümü</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Arama</label>
                    <input type="text" id="search-input" placeholder="İlan ara..."
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button onclick="applyFilters()" class="neo-btn neo-btn-primary w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrele
                    </button>
                </div>
            </div>
        </div>

        <!-- Listings Table -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    İlan Listesi
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="admin-table-th">İlan</th>
                            <th class="admin-table-th">Kategori</th>
                            <th class="admin-table-th">Durum</th>
                            <th class="admin-table-th">Fiyat</th>
                            <th class="admin-table-th">Görüntülenme</th>
                            <th class="admin-table-th">Tarih</th>
                            <th class="admin-table-th">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="listings-table-body">
                        @forelse($listings ?? [] as $listing)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <img class="h-12 w-12 rounded-lg object-cover"
                                                 src="{{ $listing->featured_image ?? asset('images/default-property.jpg') }}"
                                                 alt="{{ $listing->title ?? 'İlan' }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ Str::limit($listing->title ?? 'Başlık Yok', 40) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                #{{ $listing->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ optional($listing->altKategori)->name ?? optional($listing->anaKategori)->name ?? 'Kategori Yok' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(($listing->status ?? 'draft') === 'active')
                                        <span class="status-badge active">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Aktif
                                        </span>
                                    @elseif(($listing->status ?? 'draft') === 'pending')
                                        <span class="status-badge pending">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Beklemede
                                        </span>
                                    @elseif(($listing->status ?? 'draft') === 'inactive')
                                        <span class="status-badge draft">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Pasif
                                        </span>
                                    @else
                                        <span class="status-badge draft">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Taslak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($listing->price ?? 0) }} {{ $listing->currency ?? 'TL' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($listing->views ?? 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $listing->created_at ? $listing->created_at->format('d.m.Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.ilanlar.edit', $listing->id) }}"
                                           class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.ilanlar.show', $listing->id) }}"
                                           class="text-green-600 hover:text-green-900 transition-colors duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <p class="text-lg font-medium">Henüz ilan bulunmuyor</p>
                                        <p class="text-sm">İlk ilanınızı oluşturmak için "Yeni İlan" butonuna tıklayın</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if(isset($listings) && $listings->hasPages())
            <div class="mt-8">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
@endsection

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

        .stat-card {
            @apply text-center p-6 bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow duration-200;
        }

        .stat-value {
            @apply text-3xl font-bold mb-2;
        }

        .stat-label {
            @apply text-gray-600 font-medium;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function refreshListings() {
            location.reload();
        }

        // ✅ AJAX Filter Implementation (No Page Reload)
        async function applyFilters() {
            const status = document.getElementById('status-filter').value;
            const category = document.getElementById('category-filter').value;
            const search = document.getElementById('search-input').value;

            try {
                // Show loading state
                const tbody = document.getElementById('listings-table-body');
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center text-gray-500"><svg class="animate-spin h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading...</td></tr>';

                const response = await fetch('{{ route("admin.my-listings.search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status, category, search })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                if (data.success) {
                    updateTableWithListings(data.data);
                    window.toast?.success('Filtreleme başarılı');
                } else {
                    throw new Error(data.message || 'Filtreleme başarısız');
                }
            } catch (error) {
                console.error('Filter error:', error);
                window.toast?.error('Filtreleme başarısız: ' + error.message);
                // Fallback to page reload on error
                location.reload();
            }
        }

        function updateTableWithListings(paginatedData) {
            const tbody = document.getElementById('listings-table-body');
            tbody.innerHTML = '';

            if (!paginatedData.data || paginatedData.data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <p class="text-lg font-medium">İlan bulunamadı</p>
                                <p class="text-sm">Filtreleri değiştirmeyi deneyin</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            paginatedData.data.forEach(listing => {
                const row = createListingRow(listing);
                tbody.appendChild(row);
            });

            // Update pagination if needed
            updatePagination(paginatedData);
        }

        function createListingRow(listing) {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors duration-200';
            
            // Format price
            const price = new Intl.NumberFormat('tr-TR').format(listing.price || 0);
            const views = new Intl.NumberFormat('tr-TR').format(listing.views || 0);
            
            // Category name (with fallback)
            const categoryName = listing.alt_kategori?.name || listing.ana_kategori?.name || 'Kategori Yok';
            
            // Status badge
            let statusHTML = '';
            if (listing.status === 'active') {
                statusHTML = `<span class="status-badge active"><svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>Aktif</span>`;
            } else if (listing.status === 'pending') {
                statusHTML = `<span class="status-badge pending"><svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>Beklemede</span>`;
            } else if (listing.status === 'inactive') {
                statusHTML = `<span class="status-badge draft"><svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>Pasif</span>`;
            } else {
                statusHTML = `<span class="status-badge draft"><svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>Taslak</span>`;
            }
            
            // Format date
            const date = listing.created_at ? new Date(listing.created_at).toLocaleDateString('tr-TR') : 'N/A';
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <img class="h-12 w-12 rounded-lg object-cover"
                                 src="${listing.featured_image || '{{ asset("images/default-property.jpg") }}'}"
                                 alt="${listing.title || 'İlan'}">
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                ${listing.title ? listing.title.substring(0, 40) : 'Başlık Yok'}${listing.title && listing.title.length > 40 ? '...' : ''}
                            </div>
                            <div class="text-sm text-gray-500">
                                #${listing.id}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${categoryName}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${statusHTML}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${price} ${listing.currency || 'TL'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${views}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${date}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="/admin/ilanlar/${listing.id}/edit"
                           class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <a href="/admin/ilanlar/${listing.id}"
                           class="text-green-600 hover:text-green-900 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </div>
                </td>
            `;
            
            return row;
        }

        function updatePagination(paginatedData) {
            // TODO: Implement pagination update if needed
            // For now, we'll just log it
            console.log('Pagination:', {
                current_page: paginatedData.current_page,
                last_page: paginatedData.last_page,
                total: paginatedData.total
            });
        }

        // Enter tuşu ile arama
        document.getElementById('search-input')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    </script>
@endpush
