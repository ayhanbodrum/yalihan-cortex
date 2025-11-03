@props([
    'showMap' => true,
    'showForm' => true,
    'showOfficeInfo' => true,
    'class' => '',
])

<div class="contact-page {{ $class }}">
    <!-- Hero Section -->
    <x-yaliihan.hero-section title="📞 İletişim"
        subtitle="Bizimle iletişime geçin, size yardımcı olmaktan mutluluk duyarız" :show-search="false" />

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            @if ($showForm)
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Mesaj Gönderin</h2>
                    <p class="text-gray-600 mb-8">Sorularınız, önerileriniz veya işbirliği teklifleriniz için bize
                        ulaşabilirsiniz.</p>

                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
                                <input type="text" required
                                    class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                    placeholder="Adınız ve soyadınız">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">E-posta *</label>
                                <input type="email" required
                                    class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                    placeholder="E-posta adresiniz">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                                <input type="tel"
                                    class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                    placeholder="Telefon numaranız">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Konu</label>
                                <select
                                    class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                                    <option value="">Konu seçiniz</option>
                                    <option value="general">Genel Bilgi</option>
                                    <option value="property">Emlak Danışmanlığı</option>
                                    <option value="valuation">Değerleme Hizmeti</option>
                                    <option value="legal">Hukuki Danışmanlık</option>
                                    <option value="finance">Finansman Desteği</option>
                                    <option value="other">Diğer</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj *</label>
                            <textarea rows="6" required
                                class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                placeholder="Mesajınızı detaylı bir şekilde yazın..."></textarea>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="privacy" required
                                class="h-4 w-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                            <label for="privacy" class="ml-2 text-sm text-gray-600">
                                <a href="#" class="text-orange-500 hover:text-orange-600">Gizlilik
                                    Politikası</a>'nı okudum ve kabul ediyorum.
                            </label>
                        </div>

                        <button type="submit"
                            class="w-full bg-orange-500 text-white py-4 px-6 rounded-lg font-semibold hover:bg-orange-600 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                            📤 Mesaj Gönder
                        </button>
                    </form>
                </div>
            @endif

            <!-- Office Info & Map -->
            <div class="space-y-8">
                @if ($showOfficeInfo)
                    <!-- Office Information -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">Ofis Bilgileri</h2>

                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-orange-500 text-xl">📍</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">Adres</h3>
                                    <p class="text-gray-600">
                                        Yalıkavak, Şeyhül İslam Ömer Lütfi Cd.<br>
                                        No:10 D:C, 48400 Bodrum/Muğla
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-orange-500 text-xl">📞</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">Telefon</h3>
                                    <p class="text-gray-600">
                                        <a href="tel:+905332090302" class="hover:text-orange-500 transition-colors">
                                            0533 209 03 02
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-orange-500 text-xl">✉️</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">E-posta</h3>
                                    <p class="text-gray-600">
                                        <a href="mailto:info@yalihanemlak.com"
                                            class="hover:text-orange-500 transition-colors">
                                            info@yalihanemlak.com
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-orange-500 text-xl">🕒</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">Çalışma Saatleri</h3>
                                    <p class="text-gray-600">
                                        Pazartesi - Cuma: 09:00 - 18:00<br>
                                        Cumartesi: 09:00 - 16:00<br>
                                        Pazar: Kapalı
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showMap)
                    <!-- Map -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">Konum</h2>
                        <x-yaliihan.map-component :center="[
                            'lat' => 37.0581,
                            'lng' => 27.258,
                        ]" :zoom="15" :markers="[
                            [
                                'position' => [
                                    'lat' => 37.0581,
                                    'lng' => 27.258,
                                ],
                                'title' => 'Yalıhan Emlak',
                                'content' => 'Yalıkavak, Şeyhül İslam Ömer Lütfi Cd. No:10 D:C, 48400 Bodrum/Muğla',
                                'icon' => null,
                            ],
                        ]" height="400px"
                            :show-traffic="true" :show-transit="true" :show-bicycling="false" class="contact-map" />
                    </div>
                @endif
            </div>
        </div>

        <!-- Team Section -->
        <div class="mt-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Ekibimiz</h2>
                <p class="text-gray-600">Deneyimli ve profesyonel ekibimizle tanışın</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Team Member 1 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face"
                        alt="Ahmet Yılmaz" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Ahmet Yılmaz</h3>
                    <p class="text-orange-500 font-medium mb-2">Genel Müdür</p>
                    <p class="text-gray-600 text-sm mb-4">20+ yıllık emlak deneyimi</p>
                    <div class="flex justify-center gap-2">
                        <a href="tel:+905332090302"
                            class="p-2 bg-orange-100 text-orange-500 rounded-full hover:bg-orange-500 hover:text-white transition-colors">
                            📞
                        </a>
                        <a href="mailto:ahmet@yalihanemlak.com"
                            class="p-2 bg-orange-100 text-orange-500 rounded-full hover:bg-orange-500 hover:text-white transition-colors">
                            ✉️
                        </a>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face"
                        alt="Ayşe Demir" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Ayşe Demir</h3>
                    <p class="text-orange-500 font-medium mb-2">Emlak Danışmanı</p>
                    <p class="text-gray-600 text-sm mb-4">15+ yıllık deneyim</p>
                    <div class="flex justify-center gap-2">
                        <a href="tel:+905332090303"
                            class="p-2 bg-orange-100 text-orange-500 rounded-full hover:bg-orange-500 hover:text-white transition-colors">
                            📞
                        </a>
                        <a href="mailto:ayse@yalihanemlak.com"
                            class="p-2 bg-orange-100 text-orange-500 rounded-full hover:bg-orange-500 hover:text-white transition-colors">
                            ✉️
                        </a>
                    </div>
                </div>

                <!-- Team Member 3 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face"
                        alt="Mehmet Kaya" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Mehmet Kaya</h3>
                    <p class="text-orange-500 font-medium mb-2">Hukuk Danışmanı</p>
                    <p class="text-gray-600 text-sm mb-4">10+ yıllık hukuk deneyimi</p>
                    <div class="flex justify-center gap-2">
                        <a href="tel:+905332090304"
                            class="p-2 bg-orange-100 text-orange-500 rounded-full hover:bg-orange-500 hover:text-white transition-colors">
                            📞
                        </a>
                        <a href="mailto:mehmet@yalihanemlak.com"
                            class="p-2 bg-orange-100 text-orange-500 rounded-full hover:bg-orange-500 hover:text-white transition-colors">
                            ✉️
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Sıkça Sorulan Sorular</h2>
                <p class="text-gray-600">Merak ettiğiniz konular hakkında bilgi alın</p>
            </div>

            <div class="max-w-3xl mx-auto">
                <div class="space-y-4">
                    <!-- FAQ Item 1 -->
                    <div class="bg-white rounded-2xl shadow-lg">
                        <button
                            class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors"
                            onclick="toggleFAQ(1)">
                            <span class="font-semibold text-gray-900">Emlak danışmanlık hizmeti ücreti nedir?</span>
                            <span class="text-orange-500 text-xl" id="faq-icon-1">+</span>
                        </button>
                        <div id="faq-content-1" class="hidden px-6 pb-6">
                            <p class="text-gray-600">Emlak danışmanlık hizmetimiz genellikle satış bedelinin %2-3'ü
                                oranında ücretlendirilir. Detaylı bilgi için bizimle iletişime geçebilirsiniz.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="bg-white rounded-2xl shadow-lg">
                        <button
                            class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors"
                            onclick="toggleFAQ(2)">
                            <span class="font-semibold text-gray-900">Emlak değerleme raporu ne kadar sürer?</span>
                            <span class="text-orange-500 text-xl" id="faq-icon-2">+</span>
                        </button>
                        <div id="faq-content-2" class="hidden px-6 pb-6">
                            <p class="text-gray-600">Emlak değerleme raporu genellikle 3-5 iş günü içinde hazırlanır.
                                Acil statuslar için hızlı değerleme hizmeti de sunmaktayız.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="bg-white rounded-2xl shadow-lg">
                        <button
                            class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors"
                            onclick="toggleFAQ(3)">
                            <span class="font-semibold text-gray-900">Hangi bölgelerde hizmet veriyorsunuz?</span>
                            <span class="text-orange-500 text-xl" id="faq-icon-3">+</span>
                        </button>
                        <div id="faq-content-3" class="hidden px-6 pb-6">
                            <p class="text-gray-600">Bodrum'un tüm bölgelerinde hizmet vermekteyiz. Yalıkavak, Gümbet,
                                Bitez, Bodrum Merkez, Türkbükü, Göltürkbükü ve çevre bölgelerde status olarak
                                çalışmaktayız.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // FAQ Toggle Function
    function toggleFAQ(id) {
        const content = document.getElementById(`faq-content-${id}`);
        const icon = document.getElementById(`faq-icon-${id}`);

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.textContent = '-';
        } else {
            content.classList.add('hidden');
            icon.textContent = '+';
        }
    }

    // Form Submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                showToast('Mesajınız başarıyla gönderildi!', 'success');
                form.reset();
            });
        }
    });

    // Toast Notification
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
    .contact-page {
        min-height: 100vh;
        background-color: #f8fafc;
    }

    /* Form focus states */
    .contact-page input:focus,
    .contact-page textarea:focus,
    .contact-page select:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.15);
    }

    /* FAQ animations */
    .contact-page .faq-content {
        transition: all 0.3s ease;
    }

    /* Team member hover effects */
    .contact-page .team-member {
        transition: transform 0.3s ease;
    }

    .contact-page .team-member:hover {
        transform: translateY(-5px);
    }
</style>
