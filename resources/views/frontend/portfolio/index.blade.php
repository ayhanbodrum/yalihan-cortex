@extends('layouts.frontend')

@section('title', 'Portföy - Yalıhan Emlak')

@section('styles')
    @parent
    <style>
        .portfolio-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .portfolio-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }
        
        .portfolio-image {
            height: 250px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .portfolio-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .price-tag {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 18px;
        }
        
        .filter-tabs {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .filter-tab {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .filter-tab.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .filter-tab:hover:not(.active) {
            background: #f3f4f6;
            border-color: #e5e7eb;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .stats-label {
            font-size: 14px;
            opacity: 0.9;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-4 fw-bold text-primary mb-3">Portföy</h1>
                <p class="lead text-muted">Yalıhan Emlak'ın seçkin gayrimenkul portföyünü keşfedin</p>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number">{{ $stats['total_properties'] ?? 0 }}</div>
                <div class="stats-label">Toplam İlan</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number">{{ $stats['active_properties'] ?? 0 }}</div>
                <div class="stats-label">Aktif İlan</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number">{{ $stats['total_value'] ?? 0 }}</div>
                <div class="stats-label">Toplam Değer (M₺)</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number">{{ $stats['locations'] ?? 0 }}</div>
                <div class="stats-label">Lokasyon</div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-tabs">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex flex-wrap gap-2">
                    <div class="filter-tab active" data-filter="all">Tümü</div>
                    <div class="filter-tab" data-filter="satilik">Satılık</div>
                    <div class="filter-tab" data-filter="kiralik">Kiralık</div>
                    <div class="filter-tab" data-filter="villa">Villa</div>
                    <div class="filter-tab" data-filter="daire">Daire</div>
                    <div class="filter-tab" data-filter="arsa">Arsa</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Lokasyon ara..." id="location-search">
                    <button class="btn neo-btn neo-btn-primary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio Grid -->
    <div class="row" id="portfolio-grid">
        @forelse($properties as $property)
        <div class="col-lg-4 col-md-6 mb-4 portfolio-item" 
             data-type="{{ $property->ilan_turu ?? 'satilik' }}" 
             data-category="{{ $property->emlak_turu ?? 'daire' }}"
             data-location="{{ strtolower($property->il_adi ?? '') }}">
            <div class="portfolio-card">
                <!-- Property Image -->
                <div class="portfolio-image" 
                     style="background-image: url('{{ $property->featured_image ?? '/images/default-property.jpg' }}')">
                    <div class="portfolio-badge bg-success">
                        {{ ucfirst($property->ilan_turu ?? 'Satılık') }}
                    </div>
                </div>
                
                <!-- Property Details -->
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $property->baslik ?? 'Başlık Yok' }}</h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $property->il_adi ?? '' }}, {{ $property->ilce_adi ?? '' }}
                            </p>
                        </div>
                        <div class="price-tag">
                            {{ number_format($property->fiyat ?? 0, 0, ',', '.') }} ₺
                        </div>
                    </div>
                    
                    <!-- Property Features -->
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="border-end">
                                <div class="fw-bold">{{ $property->brut_metrekare ?? '-' }}</div>
                                <small class="text-muted">m²</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <div class="fw-bold">{{ $property->oda_sayisi ?? '-' }}</div>
                                <small class="text-muted">Oda</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold">{{ $property->banyo_sayisi ?? '-' }}</div>
                            <small class="text-muted">Banyo</small>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('frontend.property.detail', $property->id) }}" 
                           class="btn neo-btn neo-btn-primary">
                            <i class="fas fa-eye me-2"></i>Detayları Gör
                        </a>
                        <button class="btn btn-outline-primary" onclick="shareProperty({{ $property->id }})">
                            <i class="fas fa-share-alt me-2"></i>Paylaş
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-home fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Henüz portföy bulunmuyor</h4>
                <p class="text-muted">Yakında seçkin gayrimenkul portföyümüzü burada görebileceksiniz.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Load More Button -->
    @if($properties->hasPages())
    <div class="row mt-5">
        <div class="col-12 text-center">
            <button class="btn btn-outline-primary btn-lg" onclick="loadMore()">
                <i class="fas fa-plus me-2"></i>Daha Fazla Göster
            </button>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
    @parent
    <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterTabs = document.querySelectorAll('.filter-tab');
            const portfolioItems = document.querySelectorAll('.portfolio-item');
            const searchInput = document.getElementById('location-search');

            // Filter by category
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Update active tab
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const filter = this.dataset.filter;
                    
                    portfolioItems.forEach(item => {
                        if (filter === 'all' || 
                            item.dataset.type === filter || 
                            item.dataset.category === filter) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            // Search functionality
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                
                portfolioItems.forEach(item => {
                    const location = item.dataset.location;
                    if (location.includes(query)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Share property function
        function shareProperty(propertyId) {
            if (navigator.share) {
                navigator.share({
                    title: 'Yalıhan Emlak - Gayrimenkul',
                    text: 'Bu gayrimenkul ilginizi çekebilir',
                    url: `${window.location.origin}/portfolio/${propertyId}`
                });
            } else {
                // Fallback: Copy to clipboard
                const url = `${window.location.origin}/portfolio/${propertyId}`;
                navigator.clipboard.writeText(url).then(() => {
                    alert('Link panoya kopyalandı!');
                });
            }
        }

        // Load more functionality
        function loadMore() {
            // Implement AJAX loading for more properties
            console.log('Loading more properties...');
        }
    </script>
@endsection
