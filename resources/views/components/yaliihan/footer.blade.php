@props([
    'showNewsletter' => true,
    'showSocial' => true,
    'showContact' => true,
    'class' => '',
])

<footer class="yaliihan-footer bg-gray-900 text-white {{ $class }}">
    <!-- Newsletter Section -->
    @if ($showNewsletter)
        <div class="bg-orange-600 py-12">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <h3 class="text-3xl font-bold mb-4">📧 Haberlerden Haberdar Olun</h3>
                    <p class="text-xl mb-8 opacity-90">Yeni ilanlar ve özel fırsatlar hakkında bilgi alın</p>

                    <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                        <input type="email" placeholder="E-posta adresiniz"
                            class="flex-1 p-4 rounded-lg border-0 text-gray-900 focus:ring-2 focus:ring-white focus:outline-none">
                        <button
                            class="bg-white text-orange-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            Abone Ol
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Footer Content -->
    <div class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="lg:col-span-1">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-orange-500 mb-4">🏠 Yalıhan Emlak</h3>
                        <p class="text-gray-300 leading-relaxed">
                            Bodrum'un en güvenilir emlak danışmanlık firması. 20+ yıllık deneyimimizle
                            hayalinizdeki evi bulmanızda yanınızdayız.
                        </p>
                    </div>

                    @if ($showContact)
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="text-orange-500">📍</span>
                                <span class="text-gray-300">Yalıkavak, Şeyhül İslam Ömer Lütfi Cd. No:10 D:C, 48400
                                    Bodrum/Muğla</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-orange-500">📞</span>
                                <a href="tel:+905332090302"
                                    class="text-gray-300 hover:text-orange-500 transition-colors">
                                    0533 209 03 02
                                </a>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-orange-500">✉️</span>
                                <a href="mailto:info@yalihanemlak.com"
                                    class="text-gray-300 hover:text-orange-500 transition-colors">
                                    info@yalihanemlak.com
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-xl font-semibold mb-6">Hızlı Linkler</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Ana
                                Sayfa</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Satılık
                                İlanlar</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Kiralık
                                İlanlar</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Villalar</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Daireler</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Arsalar</a>
                        </li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="text-xl font-semibold mb-6">Hizmetlerimiz</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Emlak
                                Danışmanlığı</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Gayrimenkul
                                Değerleme</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Hukuki
                                Danışmanlık</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Finansman
                                Desteği</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Sigorta
                                Hizmetleri</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">360° Sanal
                                Tur</a></li>
                    </ul>
                </div>

                <!-- Locations -->
                <div>
                    <h4 class="text-xl font-semibold mb-6">Popüler Bölgeler</h4>
                    <ul class="space-y-3">
                        <li><a href="#"
                                class="text-gray-300 hover:text-orange-500 transition-colors">Yalıkavak</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Gümbet</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Bitez</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Bodrum
                                Merkez</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-orange-500 transition-colors">Türkbükü</a>
                        </li>
                        <li><a href="#"
                                class="text-gray-300 hover:text-orange-500 transition-colors">Göltürkbükü</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Media & Bottom Bar -->
    <div class="border-t border-gray-800 py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <!-- Social Media -->
                @if ($showSocial)
                    <div class="flex items-center gap-4">
                        <span class="text-gray-400">Bizi Takip Edin:</span>
                        <div class="flex gap-3">
                            <a href="https://www.facebook.com/yalihanemlak/" target="_blank"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-orange-500 transition-colors"
                                title="Facebook">
                                📘
                            </a>
                            <a href="https://www.instagram.com/yalihanemlak/" target="_blank"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-orange-500 transition-colors"
                                title="Instagram">
                                📷
                            </a>
                            <a href="https://twitter.com/yalihanemlak/" target="_blank"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-orange-500 transition-colors"
                                title="Twitter">
                                🐦
                            </a>
                            <a href="https://wa.me/905332090302" target="_blank"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-orange-500 transition-colors"
                                title="WhatsApp">
                                💬
                            </a>
                            <a href="https://t.me/ayhankucuk" target="_blank"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-orange-500 transition-colors"
                                title="Telegram">
                                ✈️
                            </a>
                            <a href="https://vk.com/yalihanemlak" target="_blank"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-orange-500 transition-colors"
                                title="VKontakte">
                                🔵
                            </a>
                            <a href="#"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-orange-500 transition-colors"
                                title="YouTube">
                                📺
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Copyright -->
                <div class="text-center md:text-right">
                    <p class="text-gray-400 text-sm">
                        © {{ date('Y') }} Yalıhan Emlak. Tüm hakları saklıdır.
                    </p>
                    <div class="flex flex-wrap justify-center md:justify-end gap-4 mt-2 text-xs text-gray-500">
                        <a href="#" class="hover:text-orange-500 transition-colors">Gizlilik Politikası</a>
                        <a href="#" class="hover:text-orange-500 transition-colors">Kullanım Şartları</a>
                        <a href="#" class="hover:text-orange-500 transition-colors">Çerez Politikası</a>
                        <a href="#" class="hover:text-orange-500 transition-colors">KVKK</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="backToTop"
        class="fixed bottom-6 right-6 w-12 h-12 bg-orange-500 text-white rounded-full shadow-lg hover:bg-orange-600 transition-all duration-300 opacity-0 invisible z-50"
        onclick="scrollToTop()">
        ⬆️
    </button>
</footer>

<script>
    // Back to Top Button
    window.addEventListener('scroll', function() {
        const backToTop = document.getElementById('backToTop');
        if (window.pageYOffset > 300) {
            backToTop.classList.remove('opacity-0', 'invisible');
            backToTop.classList.add('opacity-100', 'visible');
        } else {
            backToTop.classList.add('opacity-0', 'invisible');
            backToTop.classList.remove('opacity-100', 'visible');
        }
    });

    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Newsletter Subscription
    function subscribeNewsletter() {
        const email = document.querySelector('input[type="email"]').value;
        if (email) {
            showToast('Başarıyla abone oldunuz!', 'success');
            document.querySelector('input[type="email"]').value = '';
        } else {
            showToast('Lütfen geçerli bir e-posta adresi girin.', 'error');
        }
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 bg-white rounded-lg p-4 shadow-lg border-l-4 ${
        type === 'success' ? 'border-green-500' : 'border-red-500'
    } z-50 transform translate-x-full transition-transform duration-300`;
        toast.innerHTML = message;
        document.body.appendChild(toast);

        setTimeout(() => toast.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }
</script>

<style>
    .yaliihan-footer {
        position: relative;
    }

    .yaliihan-footer a {
        transition: all 0.3s ease;
    }

    .yaliihan-footer button {
        transition: all 0.3s ease;
    }

    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }

    /* Newsletter form focus states */
    .yaliihan-footer input[type="email"]:focus {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Social media hover effects */
    .yaliihan-footer .social-link {
        transform: translateY(0);
        transition: all 0.3s ease;
    }

    .yaliihan-footer .social-link:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
    }
</style>
