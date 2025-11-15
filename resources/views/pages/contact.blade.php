@extends('layouts.frontend')

@section('title', 'İletişim')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <div class="container mx-auto px-4 py-12">
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 transition-colors duration-300">İletişim</h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 transition-colors duration-300">Size nasıl yardımcı olabiliriz?</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Contact Form -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700 transition-all duration-300">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white transition-colors duration-300">Bize Ulaşın</h2>

                        <form action="#" method="POST" class="space-y-6" id="contactForm">
                            @csrf

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 transition-colors duration-300">
                                    Adınız Soyadınız
                                </label>
                                <input type="text" id="name" name="name" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200"
                                    aria-label="Adınız ve soyadınız">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 transition-colors duration-300">
                                    E-posta Adresiniz
                                </label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200"
                                    aria-label="E-posta adresiniz">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 transition-colors duration-300">
                                    Telefon Numaranız
                                </label>
                                <input type="tel" id="phone" name="phone" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200"
                                    aria-label="Telefon numaranız">
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 transition-colors duration-300">
                                    Konu
                                </label>
                                <select id="subject" name="subject"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200" style="color-scheme: light dark;" aria-label="Konu seçiniz">
                                    <option value="">Konu Seçiniz</option>
                                    <option value="satilik">Satılık İlanlar</option>
                                    <option value="kiralik">Kiralık İlanlar</option>
                                    <option value="danisman">Danışmanlık Hizmeti</option>
                                    <option value="diger">Diğer</option>
                                </select>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 transition-colors duration-300">
                                    Mesajınız
                                </label>
                                <textarea id="message" name="message" rows="4" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200 resize-y"
                                    aria-label="Mesajınız"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-500 dark:to-purple-500 text-white font-semibold rounded-lg hover:shadow-lg transform hover:scale-105 active:scale-95 transition-all duration-300 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Mesaj Gönder
                            </button>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-6">
                        <!-- Office Info -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700 transition-all duration-300">
                            <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white transition-colors duration-300">Ofis Bilgileri</h2>

                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-blue-600 dark:text-blue-400 text-xl mt-1 transition-colors duration-300"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">Adres</h3>
                                        <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">
                                            Yalıkavak, Şeyhül İslam Ömer Lütfi Cd. No:10 D:C,<br>
                                            48400 Bodrum/Muğla
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-phone text-blue-600 dark:text-blue-400 text-xl mt-1 transition-colors duration-300"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">Telefon</h3>
                                        <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">
                                            <a href="tel:05332090302" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">0533 209 03 02</a>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-envelope text-blue-600 dark:text-blue-400 text-xl mt-1 transition-colors duration-300"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">E-posta</h3>
                                        <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">
                                            <a href="mailto:info@yalihanemlak.com"
                                                class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">info@yalihanemlak.com</a>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-clock text-blue-600 dark:text-blue-400 text-xl mt-1 transition-colors duration-300"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">Çalışma Saatleri</h3>
                                        <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">
                                            Pazartesi - Cumartesi: 09:00 - 19:00<br>
                                            Pazar: 10:00 - 17:00
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Contact -->
                        <div class="bg-gradient-to-r from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl shadow-lg p-8 text-white transition-all duration-300">
                            <h3 class="text-xl font-bold mb-4">Hızlı İletişim</h3>
                            <p class="mb-6 opacity-95">WhatsApp üzerinden bize ulaşabilirsiniz!</p>
                            <a href="https://wa.me/905332090302" target="_blank"
                                class="inline-flex items-center px-6 py-3 bg-white text-green-600 dark:bg-gray-800 dark:text-green-400 font-semibold rounded-lg hover:shadow-lg transform hover:scale-105 active:scale-95 transition-all duration-300 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600">
                                <i class="fab fa-whatsapp text-2xl mr-2"></i>
                                WhatsApp'tan Yaz
                            </a>
                        </div>

                        <!-- Map -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700 transition-all duration-300">
                            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white transition-colors duration-300">Harita</h3>
                            <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center transition-colors duration-300">
                                <div class="text-center">
                                    <i class="fas fa-map-marked-alt text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
                                    <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">Harita yükleniyor...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Form submission with loading state
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contactForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Loading state
                submitBtn.innerHTML = `
                    <svg class="w-5 h-5 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Gönderiliyor...
                `;
                submitBtn.disabled = true;

                // Simulate form submission
                setTimeout(() => {
                    showToast('Mesajınız başarıyla gönderildi!', 'success');
                    form.reset();
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 1500);
            });
        }
    });

    // Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';

        toast.className =
            `fixed top-4 right-4 ${bgColor} text-white rounded-xl p-4 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 max-w-sm`;
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
</script>
@endpush
