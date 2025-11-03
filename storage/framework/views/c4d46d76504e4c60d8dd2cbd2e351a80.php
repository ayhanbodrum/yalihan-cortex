<?php $__env->startSection('title', 'Ana Sayfa - Yalıhan Emlak'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Hero Section -->
    <?php if (isset($component)) { $__componentOriginal133586c74d686ad1a44b9e93a31d3949 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal133586c74d686ad1a44b9e93a31d3949 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.yaliihan.hero-section','data' => ['title' => '🏠 Yalıhan Emlak','subtitle' => 'Bodrum\'un en güzel emlakları burada!','showSearch' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('yaliihan.hero-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => '🏠 Yalıhan Emlak','subtitle' => 'Bodrum\'un en güzel emlakları burada!','show-search' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal133586c74d686ad1a44b9e93a31d3949)): ?>
<?php $attributes = $__attributesOriginal133586c74d686ad1a44b9e93a31d3949; ?>
<?php unset($__attributesOriginal133586c74d686ad1a44b9e93a31d3949); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal133586c74d686ad1a44b9e93a31d3949)): ?>
<?php $component = $__componentOriginal133586c74d686ad1a44b9e93a31d3949; ?>
<?php unset($__componentOriginal133586c74d686ad1a44b9e93a31d3949); ?>
<?php endif; ?>

    <!-- Properties Grid -->
    <section class="py-20 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold text-gray-900 mb-6">Öne Çıkan İlanlar</h2>
                <p class="text-2xl text-gray-600 max-w-2xl mx-auto mb-8">En güzel emlak seçenekleri ile hayalinizdeki evi
                    bulun</p>
                <a href="<?php echo e(route('frontend.portfolio.index')); ?>"
                    class="inline-flex items-center px-8 py-4 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-building mr-3"></i>
                    Tüm Portföyü Görüntüle
                    <i class="fas fa-arrow-right ml-3"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <!-- Property 1 -->
                <div class="neo-card">
                    <div class="property-image">
                        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=400&h=300&fit=crop"
                            alt="Modern Villa - Yalıkavak">
                        <div class="gradient-overlay"></div>
                        <div class="badge bg-green-500 text-white">Satılık</div>
                        <div class="favorite-btn" onclick="toggleFavorite(this)">
                            <span class="text-gray-600 text-xl">🤍</span>
                        </div>
                        <div class="action-overlay">
                            <div class="action-buttons">
                                <button class="action-btn" onclick="openModal('virtualTour')">
                                    <div class="text-lg mb-1">🔄</div>
                                    <div class="text-xs">Sanal Tur</div>
                                </button>
                                <button class="action-btn" onclick="openModal('gallery')">
                                    <div class="text-lg mb-1">📸</div>
                                    <div class="text-xs">Galeri</div>
                                </button>
                                <button class="action-btn" onclick="openModal('map')">
                                    <div class="text-lg mb-1">🗺️</div>
                                    <div class="text-xs">Harita</div>
                                </button>
                                <button class="action-btn" onclick="shareProperty()">
                                    <div class="text-lg mb-1">📤</div>
                                    <div class="text-xs">Paylaş</div>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="property-content">
                        <h3 class="property-title">Modern Villa - Yalıkavak</h3>
                        <p class="property-location">
                            <span class="icon">📍</span> Yalıkavak, Bodrum
                        </p>
                        <div class="property-details">
                            <div class="detail-item">
                                <div class="detail-icon">🛏️</div>
                                <div class="detail-label">Yatak</div>
                                <div class="detail-value">4</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon">🚿</div>
                                <div class="detail-label">Banyo</div>
                                <div class="detail-value">3</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon">📐</div>
                                <div class="detail-label">m²</div>
                                <div class="detail-value">250</div>
                            </div>
                        </div>
                        <div class="property-price">₺8,500,000</div>
                        <div class="action-buttons-main">
                            <button class="btn-outline" onclick="openModal('propertyDetail')">Detayları Gör</button>
                            <button class="neo-btn neo-btn-primary" onclick="contactProperty()">İletişime Geç</button>
                        </div>
                    </div>
                </div>

                <!-- Property 2 -->
                <div class="neo-card">
                    <div class="property-image">
                        <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=400&h=300&fit=crop"
                            alt="Lüks Daire - Gümbet">
                        <div class="gradient-overlay"></div>
                        <div class="badge bg-blue-500 text-white">Kiralık</div>
                        <div class="favorite-btn" onclick="toggleFavorite(this)">
                            <span class="text-gray-600 text-xl">🤍</span>
                        </div>
                        <div class="action-overlay">
                            <div class="action-buttons">
                                <button class="action-btn" onclick="openModal('virtualTour')">
                                    <div class="text-lg mb-1">🔄</div>
                                    <div class="text-xs">Sanal Tur</div>
                                </button>
                                <button class="action-btn" onclick="openModal('gallery')">
                                    <div class="text-lg mb-1">📸</div>
                                    <div class="text-xs">Galeri</div>
                                </button>
                                <button class="action-btn" onclick="openModal('map')">
                                    <div class="text-lg mb-1">🗺️</div>
                                    <div class="text-xs">Harita</div>
                                </button>
                                <button class="action-btn" onclick="shareProperty()">
                                    <div class="text-lg mb-1">📤</div>
                                    <div class="text-xs">Paylaş</div>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="property-content">
                        <h3 class="property-title">Lüks Daire - Gümbet</h3>
                        <p class="property-location">
                            <span class="icon">📍</span> Gümbet, Bodrum
                        </p>
                        <div class="property-details">
                            <div class="detail-item">
                                <div class="detail-icon">🛏️</div>
                                <div class="detail-label">Yatak</div>
                                <div class="detail-value">2</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon">🚿</div>
                                <div class="detail-label">Banyo</div>
                                <div class="detail-value">2</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon">📐</div>
                                <div class="detail-label">m²</div>
                                <div class="detail-value">120</div>
                            </div>
                        </div>
                        <div class="property-price">₺15,000 <span class="price-period">/ay</span></div>
                        <div class="action-buttons-main">
                            <button class="btn-outline" onclick="openModal('propertyDetail')">Detayları Gör</button>
                            <button class="neo-btn neo-btn-primary" onclick="contactProperty()">İletişime Geç</button>
                        </div>
                    </div>
                </div>

                <!-- Property 3 -->
                <div class="neo-card">
                    <div class="property-image">
                        <img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=400&h=300&fit=crop"
                            alt="Deniz Manzaralı Villa - Bitez">
                        <div class="gradient-overlay"></div>
                        <div class="badge bg-yellow-500 text-white">Öne Çıkan</div>
                        <div class="favorite-btn" onclick="toggleFavorite(this)">
                            <span class="text-gray-600 text-xl">🤍</span>
                        </div>
                        <div class="action-overlay">
                            <div class="action-buttons">
                                <button class="action-btn" onclick="openModal('virtualTour')">
                                    <div class="text-lg mb-1">🔄</div>
                                    <div class="text-xs">Sanal Tur</div>
                                </button>
                                <button class="action-btn" onclick="openModal('gallery')">
                                    <div class="text-lg mb-1">📸</div>
                                    <div class="text-xs">Galeri</div>
                                </button>
                                <button class="action-btn" onclick="openModal('map')">
                                    <div class="text-lg mb-1">🗺️</div>
                                    <div class="text-xs">Harita</div>
                                </button>
                                <button class="action-btn" onclick="shareProperty()">
                                    <div class="text-lg mb-1">📤</div>
                                    <div class="text-xs">Paylaş</div>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="property-content">
                        <h3 class="property-title">Deniz Manzaralı Villa - Bitez</h3>
                        <p class="property-location">
                            <span class="icon">📍</span> Bitez, Bodrum
                        </p>
                        <div class="property-details">
                            <div class="detail-item">
                                <div class="detail-icon">🛏️</div>
                                <div class="detail-label">Yatak</div>
                                <div class="detail-value">5</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon">🚿</div>
                                <div class="detail-label">Banyo</div>
                                <div class="detail-value">4</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon">📐</div>
                                <div class="detail-label">m²</div>
                                <div class="detail-value">350</div>
                            </div>
                        </div>
                        <div class="property-price">₺12,500,000</div>
                        <div class="action-buttons-main">
                            <button class="btn-outline" onclick="openModal('propertyDetail')">Detayları Gör</button>
                            <button class="neo-btn neo-btn-primary" onclick="contactProperty()">İletişime Geç</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold text-gray-900 mb-6">Neden Yalıhan Emlak?</h2>
                <p class="text-2xl text-gray-600 max-w-2xl mx-auto">Profesyonel hizmet, güvenilir çözümler</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-6xl mb-4">🏠</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Geniş Portföy</h3>
                    <p class="text-gray-600">Bodrum'un her bölgesinde binlerce emlak seçeneği</p>
                </div>
                <div class="text-center">
                    <div class="text-6xl mb-4">🤝</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Güvenilir Hizmet</h3>
                    <p class="text-gray-600">20+ yıllık deneyim ve müşteri memnuniyeti</p>
                </div>
                <div class="text-center">
                    <div class="text-6xl mb-4">📱</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Modern Teknoloji</h3>
                    <p class="text-gray-600">360° sanal tur, harita entegrasyonu ve daha fazlası</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-orange-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Hayalinizdeki Evi Bulun!</h2>
            <p class="text-xl mb-8">Uzman ekibimiz size yardımcı olmaya hazır</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button
                    class="bg-white text-orange-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    📞 Hemen Ara: 0533 209 03 02
                </button>
                <button
                    class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-orange-600 transition-colors">
                    📧 İletişim Formu
                </button>
            </div>
        </div>
    </section>

    <!-- JavaScript Functions -->
    <script>
        // Favorite Toggle Function
        function toggleFavorite(element) {
            const isFavorited = element.querySelector('span').textContent === '❤️';
            element.querySelector('span').textContent = isFavorited ? '🤍' : '❤️';
            element.querySelector('span').className = isFavorited ? 'text-gray-600 text-xl' : 'text-red-500 text-xl';

            // Toast notification
            showToast(isFavorited ? 'Favorilerden çıkarıldı' : 'Favorilere eklendi', 'success');
        }

        // Modal Functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
            } else {
                showToast('Modal açılıyor...', 'info');
            }
        }

        // Share Property Function
        function shareProperty() {
            if (navigator.share) {
                navigator.share({
                    title: 'Yalıhan Emlak - Modern Villa',
                    text: 'Bu harika emlakı inceleyin!',
                    url: window.location.href
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                navigator.clipboard.writeText(window.location.href).then(() => {
                    showToast('Paylaşım linki kopyalandı!', 'success');
                });
            }
        }

        // Contact Property Function
        function contactProperty() {
            showToast('İletişim formu açılıyor...', 'success');
            // Here you can add logic to open contact form or redirect to contact page
        }

        // Toast Notification Function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';

            toast.className =
                `fixed top-4 right-4 ${bgColor} text-white rounded-2xl p-4 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 max-w-sm`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <span class="text-2xl mr-3">${icon}</span>
                    <span class="font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            // Show toast
            setTimeout(() => toast.classList.remove('translate-x-full'), 100);

            // Hide toast after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }

        // Smooth scroll for anchor links
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling to all anchor links
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            anchorLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add intersection observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            }, observerOptions);

            // Observe all property cards
            const propertyCards = document.querySelectorAll('.neo-card');
            propertyCards.forEach(card => {
                observer.observe(card);
            });
        });

        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            .animate-fade-in {
                animation: fadeInUp 0.6s ease-out forwards;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/yaliihan-home-clean.blade.php ENDPATH**/ ?>