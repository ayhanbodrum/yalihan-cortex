<?php $__env->startSection('title', 'İletişim'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">İletişim</h1>
                <p class="text-xl text-gray-600">Size nasıl yardımcı olabiliriz?</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Contact Form -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-900">Bize Ulaşın</h2>

                    <form action="#" method="POST" class="space-y-6">
                        <?php echo csrf_field(); ?>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Adınız Soyadınız
                            </label>
                            <input type="text" id="name" name="name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                E-posta Adresiniz
                            </label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Telefon Numaranız
                            </label>
                            <input type="tel" id="phone" name="phone" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                                Konu
                            </label>
                            <select id="subject" name="subject"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Konu Seçiniz</option>
                                <option value="satilik">Satılık İlanlar</option>
                                <option value="kiralik">Kiralık İlanlar</option>
                                <option value="danisman">Danışmanlık Hizmeti</option>
                                <option value="diger">Diğer</option>
                            </select>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                                Mesajınız
                            </label>
                            <textarea id="message" name="message" rows="4" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <button type="submit"
                            class="w-full py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                            Mesaj Gönder
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="space-y-6">
                    <!-- Office Info -->
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900">Ofis Bilgileri</h2>

                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-blue-600 text-xl mt-1"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-semibold text-gray-900">Adres</h3>
                                    <p class="text-gray-600">
                                        Yalıkavak, Şeyhül İslam Ömer Lütfi Cd. No:10 D:C,<br>
                                        48400 Bodrum/Muğla
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-phone text-blue-600 text-xl mt-1"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-semibold text-gray-900">Telefon</h3>
                                    <p class="text-gray-600">
                                        <a href="tel:05332090302" class="hover:text-blue-600">0533 209 03 02</a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-envelope text-blue-600 text-xl mt-1"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-semibold text-gray-900">E-posta</h3>
                                    <p class="text-gray-600">
                                        <a href="mailto:info@yalihanemlak.com"
                                            class="hover:text-blue-600">info@yalihanemlak.com</a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock text-blue-600 text-xl mt-1"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-semibold text-gray-900">Çalışma Saatleri</h3>
                                    <p class="text-gray-600">
                                        Pazartesi - Cumartesi: 09:00 - 19:00<br>
                                        Pazar: 10:00 - 17:00
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Contact -->
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-8 text-white">
                        <h3 class="text-xl font-bold mb-4">Hızlı İletişim</h3>
                        <p class="mb-6">WhatsApp üzerinden bize ulaşabilirsiniz!</p>
                        <a href="https://wa.me/905332090302" target="_blank"
                            class="inline-flex items-center px-6 py-3 bg-white text-green-600 font-semibold rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                            <i class="fab fa-whatsapp text-2xl mr-2"></i>
                            WhatsApp'tan Yaz
                        </a>
                    </div>

                    <!-- Map -->
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h3 class="text-xl font-bold mb-4 text-gray-900">Harita</h3>
                        <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt text-6xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600">Harita yükleniyor...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/pages/contact.blade.php ENDPATH**/ ?>