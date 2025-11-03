<!-- İlan Kartı Component -->
@props([
    'ilan',
    'showCheckbox' => true,
    'showQuickActions' => true,
    'showPrice' => true,
    'showDansman' => true,
    'compactMode' => false,
])

<div class="modern-listing-card {{ $compactMode ? 'compact' : '' }}">
    @if ($showCheckbox)
        <!-- Selection Checkbox -->
        <div class="absolute top-3 left-3 z-20">
            <input type="checkbox" value="{{ $ilan->id }}"
                class="listing-checkbox w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
        </div>
    @endif

    @if ($showQuickActions)
        <!-- Quick Actions -->
        <div class="listing-quick-actions">
            <div class="flex space-x-2">
                <a href="{{ route('ilanlar.show', $ilan->id) }}" class="action-btn action-btn-view" title="Görüntüle">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('ilanlar.edit', $ilan->id) }}" class="action-btn action-btn-edit" title="Düzenle">
                    <i class="fas fa-edit"></i>
                </a>
                <button onclick="deleteListing({{ $ilan->id }})" class="action-btn action-btn-delete"
                    title="Sil">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Status Badges -->
    <div class="mb-3">
        @if ($ilan->status_mi)
            <span class="status-badge yayinda">
                <i class="fas fa-eye mr-1"></i> Aktif
            </span>
        @else
            <span class="status-badge pasif">
                <i class="fas fa-eye-slash mr-1"></i> Pasif
            </span>
        @endif

        @if ($ilan->status == 'satildi')
            <span class="status-badge satildi ml-2">
                <i class="fas fa-handshake mr-1"></i> Satıldı
            </span>
        @elseif ($ilan->status == 'kiralandi')
            <span class="status-badge kiraland ml-2">
                <i class="fas fa-key mr-1"></i> Kiralandı
            </span>
        @endif
    </div>

    <!-- Content -->
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
            {{ $ilan->baslik ?? 'Başlık Yok' }}
        </h3>

        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
            <!-- Konum -->
            <div class="flex items-center">
                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                <span>{{ $ilan->adres_il ?? 'İl' }} / {{ $ilan->adres_ilce ?? 'İlçe' }}</span>
            </div>

            <!-- Danışman -->
            @if ($showDansman && $ilan->danisman)
                <div class="flex items-center">
                    <i class="fas fa-user text-gray-400 mr-2"></i>
                    <span>{{ $ilan->danisman->ad }} {{ $ilan->danisman->soyad }}</span>
                </div>
            @endif

            <!-- Tarih -->
            <div class="flex items-center">
                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                <span>{{ $ilan->created_at->format('d.m.Y') }}</span>
            </div>

            <!-- İlan Türü -->
            @if ($ilan->ilan_turu)
                <div class="flex items-center">
                    <i class="fas fa-tag text-gray-400 mr-2"></i>
                    <span>{{ $ilan->ilan_turu }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Price -->
    @if ($showPrice && $ilan->fiyat)
        <div class="mb-4">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                {{ number_format($ilan->fiyat, 0, ',', '.') }} ₺
            </div>
            @if ($ilan->fiyat_tipi)
                <div class="text-sm text-gray-500">{{ $ilan->fiyat_tipi }}</div>
            @endif
        </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
        <div class="flex space-x-2">
            <span class="text-xs text-gray-500">ID: {{ $ilan->id }}</span>
            @if ($ilan->kategori)
                <span class="text-xs text-gray-500">{{ $ilan->kategori->name ?? '' }}</span>
            @endif
        </div>
        <div class="flex space-x-2">
            <button onclick="toggleListingStatus({{ $ilan->id }})"
                class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                {{ $ilan->status_mi ? 'Pasifleştir' : 'Aktifleştir' }}
            </button>
            <button onclick="duplicateListing({{ $ilan->id }})"
                class="text-xs text-green-600 hover:text-green-800 dark:text-green-400 font-medium">
                Kopyala
            </button>
        </div>
    </div>
</div>

<style>
    .action-btn {
        @apply p-2 rounded-lg text-sm transition-colors;
    }

    .action-btn-view {
        @apply bg-blue-500 hover:bg-blue-600 text-white;
    }

    .action-btn-edit {
        @apply bg-green-500 hover:bg-green-600 text-white;
    }

    .action-btn-delete {
        @apply bg-red-500 hover:bg-red-600 text-white;
    }

    .status-badge {
        @apply px-2 py-1 rounded-full text-xs font-medium;
    }

    .status-badge.yayinda {
        @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200;
    }

    .status-badge.pasif {
        @apply bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200;
    }

    .status-badge.satildi {
        @apply bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200;
    }

    .status-badge.kiraland {
        @apply bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200;
    }

    .modern-listing-card.compact {
        @apply p-3;
    }

    .modern-listing-card.compact h3 {
        @apply text-base;
    }

    .modern-listing-card.compact .text-2xl {
        @apply text-xl;
    }
</style>
