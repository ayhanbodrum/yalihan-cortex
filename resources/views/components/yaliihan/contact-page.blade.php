@props([
    'showMap' => true,
    'showForm' => true,
    'showOfficeInfo' => true,
    'class' => '',
])

<div class="contact-page {{ $class }} bg-slate-50 dark:bg-gray-950 min-h-screen">
    <!-- Hero Section -->
    <x-yaliihan.hero-section title="📞 İletişim"
        subtitle="Bizimle iletişime geçin, size yardımcı olmaktan mutluluk duyarız" :show-search="false" />

    <!-- AI Assistant CTA -->
    <div class="container mx-auto px-4 -mt-10 sm:-mt-14">
        <div class="bg-white dark:bg-gray-900 border border-blue-100 dark:border-blue-800/40 shadow-xl rounded-3xl p-6 sm:p-8 flex flex-col lg:flex-row items-start gap-6 relative overflow-hidden transition-all duration-300">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-transparent to-purple-50 dark:from-blue-900/40 dark:via-transparent dark:to-purple-900/30 pointer-events-none"></div>
            <div class="relative flex items-center gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-purple-600 text-white flex items-center justify-center shadow-lg">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c1.657 0 3-1.567 3-3.5S13.657 4 12 4 9 5.567 9 7.5 10.343 11 12 11zM9.5 13a4.5 4.5 0 00-4.5 4.5V19a1 1 0 001 1h10a1 1 0 001-1v-1.5A4.5 4.5 0 0012.5 13h-3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7h3m-1.5-1.5V9" />
                    </svg>
                </div>
                <div>
                    <p class="inline-flex items-center gap-2 text-xs font-semibold tracking-widest uppercase text-blue-600 dark:text-blue-300 mb-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                        Yapay Zeka Destekli Yardım
                    </p>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-1">Sanal Danışmanımız 7/24 Hizmetinizde</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Sorularınıza anında yanıt alın, portföy önerileri oluşturun ve iletişim sürecinizi hızlandırın.</p>
                </div>
            </div>
            <div class="relative flex flex-col sm:flex-row items-center gap-3 ml-auto">
                <a href="{{ url('/ai/explore') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 hover:shadow-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    Sanal Danışmanla Sohbet Et
                    <i class="fas fa-arrow-right text-sm"></i>
                </a>
                <a href="tel:+905332090302" class="inline-flex items-center justify-center gap-2 px-5 py-3 border border-blue-200 dark:border-blue-700 text-blue-600 dark:text-blue-300 font-semibold rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    📞 0533 209 03 02
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            @if ($showForm)
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl rounded-3xl p-8 sm:p-10 transition-all duration-300">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Mesaj Gönderin</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">Sorularınız, önerileriniz veya işbirliği talepleriniz için formu doldurun. Ekibimiz en kısa sürede size dönüş yapar.</p>

                    <form method="POST" action="#" class="space-y-6" novalidate>
                        @csrf
                        <div class="hidden">
                            <label for="contact_hp">Lütfen bu alanı boş bırakın</label>
                            <input id="contact_hp" name="contact_hp" type="text" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ad Soyad *</label>
                                <input id="name" name="name" type="text" required autocomplete="name"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Adınız ve soyadınız">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">E-posta *</label>
                                <input id="email" name="email" type="email" required autocomplete="email"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="E-posta adresiniz">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Telefon</label>
                                <input id="phone" name="phone" type="tel" autocomplete="tel"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Telefon numaranız">
                            </div>

                            <div>
                                <label for="topic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Konu</label>
                                <select id="topic" name="topic" style="color-scheme: light dark;"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
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
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mesaj *</label>
                            <textarea id="message" name="message" rows="6" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="Mesajınızı detaylı bir şekilde yazın..."></textarea>
                        </div>

                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="privacy" required
                                class="mt-1 h-5 w-5 text-blue-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500">
                            <label for="privacy" class="text-sm text-gray-600 dark:text-gray-400">Gizlilik metnini okudum ve kabul ediyorum. <a href="#" class="font-semibold text-blue-600 dark:text-blue-300 hover:underline">Gizlilik Politikası</a></label>
                        </div>

                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-3.5 px-6 rounded-xl font-semibold transition-all duration-200 hover:shadow-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            <i class="fas fa-paper-plane"></i>
                            Mesaj Gönder
                        </button>
                    </form>
                </div>
            @endif

            <!-- Office Info & Map -->
            <div class="space-y-8">
                @if ($showOfficeInfo)
                    <!-- Office Information -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl rounded-3xl p-8 transition-all duration-300">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Ofis Bilgileri</h2>

                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0 text-blue-600 dark:text-blue-300 text-xl">
                                    📍
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Adres</h3>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        Yalıkavak, Şeyhül İslam Ömer Lütfi Cd.<br>
                                        No:10 D:C, 48400 Bodrum/Muğla
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0 text-blue-600 dark:text-blue-300 text-xl">
                                    📞
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Telefon</h3>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        <a href="tel:+905332090302" class="hover:text-blue-600 dark:hover:text-blue-300 transition-colors">
                                            0533 209 03 02
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0 text-blue-600 dark:text-blue-300 text-xl">
                                    ✉️
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">E-posta</h3>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        <a href="mailto:info@yalihanemlak.com"
                                            class="hover:text-blue-600 dark:hover:text-blue-300 transition-colors">
                                            info@yalihanemlak.com
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0 text-blue-600 dark:text-blue-300 text-xl">
                                    🕒
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Çalışma Saatleri</h3>
                                    <p class="text-gray-600 dark:text-gray-400">
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
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl rounded-3xl p-8 transition-all duration-300">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Konum</h2>
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
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Ekibimiz</h2>
                <p class="text-gray-600 dark:text-gray-400">Deneyimli ve profesyonel ekibimizle tanışın</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Team Member 1 -->
                <div class="team-member bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl rounded-3xl p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face"
                        alt="Ahmet Yılmaz" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Ahmet Yılmaz</h3>
                    <p class="text-blue-600 dark:text-blue-300 font-medium mb-2">Genel Müdür</p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">20+ yıllık emlak deneyimi</p>
                    <div class="flex justify-center gap-2">
                        <a href="tel:+905332090302"
                            class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-full hover:bg-blue-600 hover:text-white dark:hover:bg-blue-700 transition-colors">
                            📞
                        </a>
                        <a href="mailto:ahmet@yalihanemlak.com"
                            class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-full hover:bg-blue-600 hover:text-white dark:hover:bg-blue-700 transition-colors">
                            ✉️
                        </a>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="team-member bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl rounded-3xl p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face"
                        alt="Ayşe Demir" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Ayşe Demir</h3>
                    <p class="text-blue-600 dark:text-blue-300 font-medium mb-2">Emlak Danışmanı</p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">15+ yıllık deneyim</p>
                    <div class="flex justify-center gap-2">
                        <a href="tel:+905332090303"
                            class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-full hover:bg-blue-600 hover:text-white dark:hover:bg-blue-700 transition-colors">
                            📞
                        </a>
                        <a href="mailto:ayse@yalihanemlak.com"
                            class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-full hover:bg-blue-600 hover:text-white dark:hover:bg-blue-700 transition-colors">
                            ✉️
                        </a>
                    </div>
                </div>

                <!-- Team Member 3 -->
                <div class="team-member bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl rounded-3xl p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face"
                        alt="Mehmet Kaya" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Mehmet Kaya</h3>
                    <p class="text-blue-600 dark:text-blue-300 font-medium mb-2">Hukuk Danışmanı</p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">10+ yıllık hukuk deneyimi</p>
                    <div class="flex justify-center gap-2">
                        <a href="tel:+905332090304"
                            class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-full hover:bg-blue-600 hover:text-white dark:hover:bg-blue-700 transition-colors">
                            📞
                        </a>
                        <a href="mailto:mehmet@yalihanemlak.com"
                            class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-full hover:bg-blue-600 hover:text-white dark:hover:bg-blue-700 transition-colors">
                            ✉️
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Sıkça Sorulan Sorular</h2>
                <p class="text-gray-600 dark:text-gray-400">Merak ettiğiniz konular hakkında bilgi alın</p>
            </div>

            <div class="max-w-3xl mx-auto">
                <div class="space-y-4">
                    <!-- FAQ Item 1 -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl transition-all duration-300">
                        <button
                            class="w-full p-6 text-left flex items-center justify-between hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                            onclick="toggleFAQ(1)">
                            <span class="font-semibold text-gray-900 dark:text-white">Emlak danışmanlık hizmeti ücreti nedir?</span>
                            <span class="text-blue-600 dark:text-blue-300 text-xl" id="faq-icon-1">+</span>
                        </button>
                        <div id="faq-content-1" class="hidden px-6 pb-6">
                            <p class="text-gray-600 dark:text-gray-400">Emlak danışmanlık hizmetimiz genellikle satış bedelinin %2-3'
                                oranında ücretlendirilir. Detaylı bilgi için bizimle iletişime geçebilirsiniz.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl transition-all duration-300">
                        <button
                            class="w-full p-6 text-left flex items-center justify-between hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                            onclick="toggleFAQ(2)">
                            <span class="font-semibold text-gray-900 dark:text-white">Emlak değerleme raporu ne kadar sürer?</span>
                            <span class="text-blue-600 dark:text-blue-300 text-xl" id="faq-icon-2">+</span>
                        </button>
                        <div id="faq-content-2" class="hidden px-6 pb-6">
                            <p class="text-gray-600 dark:text-gray-400">Emlak değerleme raporu genellikle 3-5 iş günü içinde hazırlanır.
                                Acil statuslar için hızlı değerleme hizmeti de sunmaktayız.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl transition-all duration-300">
                        <button
                            class="w-full p-6 text-left flex items-center justify-between hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                            onclick="toggleFAQ(3)">
                            <span class="font-semibold text-gray-900 dark:text-white">Hangi bölgelerde hizmet veriyorsunuz?</span>
                            <span class="text-blue-600 dark:text-blue-300 text-xl" id="faq-icon-3">+</span>
                        </button>
                        <div id="faq-content-3" class="hidden px-6 pb-6">
                            <p class="text-gray-600 dark:text-gray-400">Bodrum'un tüm bölgelerinde hizmet vermekteyiz. Yalıkavak, Gümbet,
                                Bitez, Bodrum Merkez, Türkbükü, Göltürkbükü ve çevre bölgelerde aktif olarak
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
