@extends('layouts.frontend')

@section('title', 'Deniz Manzaralı Lüks Villa')
@section('description', 'Yalıkavak\'ta deniz manzaralı, havuzlu, 5+1 lüks villa')

@section('content')
    <!-- Gallery Section -->
    <section class="relative">
        <div class="grid lg:grid-cols-4 lg:grid-rows-2 gap-2 h-[600px]" x-data="{
            showGallery: false,
            currentImage: 0,
            images: [
                'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1200',
                'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200',
                'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200',
                'https://images.unsplash.com/photo-1600607687644-c7171b42498b?w=1200',
                'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=1200'
            ]
        }">
            <!-- Main Image -->
            <div class="lg:col-span-2 lg:row-span-2 relative overflow-hidden cursor-pointer"
                @click="showGallery = true; currentImage = 0">
                <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1200" alt="Ana Görsel"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
            </div>

            <!-- Side Images -->
            <div class="relative overflow-hidden cursor-pointer" @click="showGallery = true; currentImage = 1">
                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600" alt="Görsel 2"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            <div class="relative overflow-hidden cursor-pointer" @click="showGallery = true; currentImage = 2">
                <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=600" alt="Görsel 3"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            <div class="relative overflow-hidden cursor-pointer" @click="showGallery = true; currentImage = 3">
                <img src="https://images.unsplash.com/photo-1600607687644-c7171b42498b?w=600" alt="Görsel 4"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            <div class="relative overflow-hidden cursor-pointer group" @click="showGallery = true; currentImage = 4">
                <img src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=600" alt="Görsel 5"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                    <div class="text-white text-center">
                        <i class="fas fa-images text-4xl mb-2"></i>
                        <p class="text-lg font-semibold">+12 Fotoğraf</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="absolute top-4 right-4 flex space-x-2 z-10">
                <button class="px-4 py-2 bg-white/90 backdrop-blur rounded-xl shadow-lg hover:bg-white transition-colors">
                    <i class="far fa-heart text-gray-700 mr-2"></i> Favorilere Ekle
                </button>
                <button class="px-4 py-2 bg-white/90 backdrop-blur rounded-xl shadow-lg hover:bg-white transition-colors">
                    <i class="fas fa-share-alt text-gray-700 mr-2"></i> Paylaş
                </button>
                <button class="px-4 py-2 bg-white/90 backdrop-blur rounded-xl shadow-lg hover:bg-white transition-colors">
                    <i class="fas fa-print text-gray-700 mr-2"></i> Yazdır
                </button>
            </div>

            <!-- Full Screen Gallery Modal -->
            <div x-show="showGallery" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/95 z-50 flex items-center justify-center"
                @click.self="showGallery = false">

                <!-- Close Button -->
                <button @click="showGallery = false" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Image Display -->
                <div class="relative max-w-7xl mx-auto px-4">
                    <img :src="images[currentImage]" alt="Gallery Image" class="max-h-[80vh] mx-auto">

                    <!-- Navigation -->
                    <button @click="currentImage = currentImage > 0 ? currentImage - 1 : images.length - 1"
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-white hover:bg-white/30">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button @click="currentImage = currentImage < images.length - 1 ? currentImage + 1 : 0"
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-white hover:bg-white/30">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <!-- Thumbnails -->
                    <div class="flex justify-center space-x-2 mt-4">
                        <template x-for="(image, index) in images" :key="index">
                            <button @click="currentImage = index" :class="{ 'ring-2 ring-white': currentImage === index }"
                                class="w-20 h-20 overflow-hidden rounded-lg opacity-70 hover:opacity-100 transition-opacity">
                                <img :src="image" alt="Thumb" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Left Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Title & Basic Info -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                            <a href="/" class="hover:text-blue-600 transition-colors">Ana Sayfa</a>
                            <i class="fas fa-chevron-right text-xs"></i>
                            <a href="/ilanlar" class="hover:text-blue-600 transition-colors">İlanlar</a>
                            <i class="fas fa-chevron-right text-xs"></i>
                            <a href="/ilanlar?kategori=satilik" class="hover:text-blue-600 transition-colors">Satılık</a>
                            <i class="fas fa-chevron-right text-xs"></i>
                            <span class="text-gray-700">Villa</span>
                        </nav>

                        <!-- Title -->
                        <h1 class="text-3xl lg:text-4xl font-bold mb-4">Deniz Manzaralı Lüks Villa</h1>

                        <!-- Location & ID -->
                        <div class="flex flex-wrap items-center gap-4 mb-6">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                <span>Yalıkavak, Bodrum / Muğla</span>
                            </div>
                            <div class="text-gray-500">
                                <span>İlan No: #2024001234</span>
                            </div>
                            <div class="text-gray-500">
                                <span>İlan Tarihi: 15 Ocak 2024</span>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="flex items-end justify-between pb-6 border-b border-gray-200">
                            <div>
                                <div class="text-4xl font-bold gradient-text">₺12.500.000</div>
                                <div class="text-gray-500 mt-1">₺23.585 / m²</div>
                            </div>
                            <div class="flex space-x-2">
                                <span
                                    class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">SATILIK</span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">KREDİYE
                                    UYGUN</span>
                            </div>
                        </div>

                        <!-- Quick Features -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-6">
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <i class="fas fa-bed text-2xl text-blue-600 mb-2"></i>
                                <div class="font-semibold">5+1</div>
                                <div class="text-sm text-gray-500">Oda Sayısı</div>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <i class="fas fa-expand-arrows-alt text-2xl text-green-600 mb-2"></i>
                                <div class="font-semibold">530 m²</div>
                                <div class="text-sm text-gray-500">Brüt Alan</div>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <i class="fas fa-ruler-combined text-2xl text-purple-600 mb-2"></i>
                                <div class="font-semibold">450 m²</div>
                                <div class="text-sm text-gray-500">Net Alan</div>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <i class="fas fa-building text-2xl text-orange-600 mb-2"></i>
                                <div class="font-semibold">3 Katlı</div>
                                <div class="text-sm text-gray-500">Bina</div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <i class="fas fa-file-alt mr-3 text-blue-600"></i>
                            İlan Açıklaması
                        </h2>
                        <div class="prose prose-lg max-w-none text-gray-600">
                            <p class="mb-4">
                                Bodrum Yalıkavak'ta, denize sıfır konumda bulunan lüks villamız satılıktır.
                                530 m² kapalı alana sahip villa, 1000 m² arsa üzerine konumlanmıştır.
                            </p>
                            <p class="mb-4">
                                Villa 3 katlı olup, 5 yatak odası, 1 salon, 3 banyo, 2 WC, geniş Amerikan mutfak,
                                çamaşır odası ve depolardan oluşmaktadır. Master yatak odasında jakuzi ve giyinme odası
                                bulunmaktadır.
                            </p>
                            <p class="mb-4">
                                Özel havuz, geniş bahçe, barbekü alanı, kapalı garaj (3 araçlık) ve güvenlik sistemi
                                mevcuttur.
                                Villa full eşyalı olarak satılmaktadır. Smart home sistemi ile donatılmıştır.
                            </p>
                            <p>
                                Merkeze 5 dakika, havalimanına 45 dakika mesafededir. Denize sıfır konumu ve
                                eşsiz manzarası ile yatırım için ideal bir fırsattır.
                            </p>
                        </div>
                    </div>

                    <!-- Detailed Features -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <i class="fas fa-list-ul mr-3 text-blue-600"></i>
                            Detaylı Özellikler
                        </h2>

                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- General Info -->
                            <div>
                                <h3 class="font-semibold text-lg mb-4 text-gray-700">Genel Bilgiler</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">İlan Tipi</span>
                                        <span class="font-medium">Satılık</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Emlak Tipi</span>
                                        <span class="font-medium">Villa</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Oda Sayısı</span>
                                        <span class="font-medium">5+1</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Bina Yaşı</span>
                                        <span class="font-medium">0 (Yeni)</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Kat Sayısı</span>
                                        <span class="font-medium">3</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Isıtma</span>
                                        <span class="font-medium">Merkezi</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Area Info -->
                            <div>
                                <h3 class="font-semibold text-lg mb-4 text-gray-700">Alan Bilgileri</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Brüt Alan</span>
                                        <span class="font-medium">530 m²</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Net Alan</span>
                                        <span class="font-medium">450 m²</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Arsa Alanı</span>
                                        <span class="font-medium">1000 m²</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Banyo Sayısı</span>
                                        <span class="font-medium">3</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">WC Sayısı</span>
                                        <span class="font-medium">2</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Balkon</span>
                                        <span class="font-medium">Var (3)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Features Tags -->
                        <div class="mt-8">
                            <h3 class="font-semibold text-lg mb-4 text-gray-700">Özellikler</h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm">Deniz
                                    Manzaralı</span>
                                <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm">Özel Havuz</span>
                                <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-full text-sm">Bahçeli</span>
                                <span class="px-4 py-2 bg-orange-100 text-orange-700 rounded-full text-sm">Garajlı</span>
                                <span class="px-4 py-2 bg-pink-100 text-pink-700 rounded-full text-sm">Eşyalı</span>
                                <span
                                    class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm">Güvenlikli</span>
                                <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full text-sm">Jakuzi</span>
                                <span class="px-4 py-2 bg-red-100 text-red-700 rounded-full text-sm">Barbekü</span>
                                <span class="px-4 py-2 bg-teal-100 text-teal-700 rounded-full text-sm">Smart Home</span>
                                <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm">Fiber
                                    İnternet</span>
                            </div>
                        </div>
                    </div>

                    <!-- Location Map -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <i class="fas fa-map-marked-alt mr-3 text-blue-600"></i>
                            Konum
                        </h2>

                        <!-- Map Container -->
                        <div class="relative h-96 bg-gray-200 rounded-xl overflow-hidden mb-6">
                            <!-- Placeholder for actual map -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fas fa-map-marker-alt text-6xl text-blue-600 mb-4"></i>
                                    <p class="text-gray-600">Harita yükleniyor...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Nearby Places -->
                        <div>
                            <h3 class="font-semibold text-lg mb-4 text-gray-700">Yakın Çevre</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-water text-blue-500"></i>
                                    <span class="text-sm">Deniz: 0 m</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-shopping-cart text-green-500"></i>
                                    <span class="text-sm">Market: 200 m</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-school text-purple-500"></i>
                                    <span class="text-sm">Okul: 500 m</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-hospital text-red-500"></i>
                                    <span class="text-sm">Hastane: 2 km</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-plane text-orange-500"></i>
                                    <span class="text-sm">Havalimanı: 45 km</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-utensils text-pink-500"></i>
                                    <span class="text-sm">Restoran: 100 m</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3D Virtual Tour -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <i class="fas fa-vr-cardboard mr-3 text-blue-600"></i>
                            3D Sanal Tur
                        </h2>
                        <div class="relative h-96 bg-gray-900 rounded-xl overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <button
                                    class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all">
                                    <i class="fas fa-play mr-2"></i> Sanal Turu Başlat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        <!-- Agent Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">İlan Sahibi</h3>

                            <div class="flex items-center space-x-4 mb-6">
                                <img src="https://ui-avatars.com/api/?name=Ahmet+Yilmaz&background=667eea&color=fff&size=80"
                                    alt="Agent" class="w-20 h-20 rounded-full">
                                <div>
                                    <h4 class="font-semibold text-lg">Ahmet Yılmaz</h4>
                                    <p class="text-gray-600">Emlak Danışmanı</p>
                                    <div class="flex items-center mt-1">
                                        <div class="flex text-yellow-400">
                                            <i class="fas fa-star text-sm"></i>
                                            <i class="fas fa-star text-sm"></i>
                                            <i class="fas fa-star text-sm"></i>
                                            <i class="fas fa-star text-sm"></i>
                                            <i class="fas fa-star-half-alt text-sm"></i>
                                        </div>
                                        <span class="text-sm text-gray-500 ml-2">(4.8)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Buttons -->
                            <div class="space-y-3">
                                <a href="tel:05332090302"
                                    class="w-full py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all flex items-center justify-center">
                                    <i class="fas fa-phone mr-2"></i> 0533 209 03 02
                                </a>
                                <a href="https://wa.me/905332090302"
                                    class="w-full py-3 bg-green-500 text-white font-semibold rounded-xl hover:bg-green-600 transition-colors flex items-center justify-center">
                                    <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                                </a>
                                <button
                                    class="w-full py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:border-blue-500 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-envelope mr-2"></i> Mesaj Gönder
                                </button>
                            </div>

                            <!-- Agent Stats -->
                            <div class="grid grid-cols-3 gap-2 mt-6 pt-6 border-t border-gray-200">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">45</div>
                                    <div class="text-xs text-gray-500">Aktif İlan</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">120</div>
                                    <div class="text-xs text-gray-500">Satış</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-600">5</div>
                                    <div class="text-xs text-gray-500">Yıl Deneyim</div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Form -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Hızlı İletişim Formu</h3>

                            <form class="space-y-4">
                                <div>
                                    <input type="text" placeholder="Adınız Soyadınız"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                                </div>
                                <div>
                                    <input type="tel" placeholder="Telefon Numaranız"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                                </div>
                                <div>
                                    <input type="email" placeholder="E-posta Adresiniz"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                                </div>
                                <div>
                                    <textarea placeholder="Mesajınız" rows="4"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all resize-none"></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all">
                                    Mesaj Gönder
                                </button>
                            </form>

                            <p class="text-xs text-gray-500 mt-4 text-center">
                                Gönder'e tıklayarak <a href="#" class="text-blue-600">KVKK şartlarını</a> kabul
                                etmiş olursunuz.
                            </p>
                        </div>

                        <!-- Loan Calculator -->
                        <div class="bg-gradient-to-br from-blue-100 to-purple-100 rounded-2xl p-6">
                            <h3 class="text-xl font-bold mb-4">Kredi Hesaplama</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm text-gray-600">Kredi Tutarı</label>
                                    <input type="number" value="8750000"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-white focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Vade (Ay)</label>
                                    <select
                                        class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-white focus:border-blue-500">
                                        <option>120 Ay</option>
                                        <option>180 Ay</option>
                                        <option>240 Ay</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Faiz Oranı (%)</label>
                                    <input type="number" value="2.79" step="0.01"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-white focus:border-blue-500">
                                </div>

                                <div class="bg-white rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Aylık Taksit</div>
                                    <div class="text-2xl font-bold gradient-text">₺118.456</div>
                                </div>

                                <button
                                    class="w-full py-2 bg-white rounded-lg font-semibold hover:shadow-md transition-shadow">
                                    Detaylı Hesapla
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar Properties -->
            <div class="mt-12">
                <h2 class="text-3xl font-bold mb-8">Benzer İlanlar</h2>

                <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @for ($i = 1; $i <= 4; $i++)
                        <div
                            class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group cursor-pointer">
                            <div class="relative h-48 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-{{ 1600000000000 + $i * 2000000 }}-9991f1c4c750?w=400"
                                    alt="Property {{ $i }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute top-3 left-3">
                                    <span class="px-2 py-1 bg-white/90 backdrop-blur text-xs font-semibold rounded-full">
                                        {{ ['SATILIK', 'KİRALIK'][$i % 2] }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold mb-2 group-hover:text-blue-600 transition-colors">
                                    {{ ['Modern Villa', 'Lüks Daire', 'Deniz Manzaralı Ev', 'Taş Villa'][$i - 1] }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-3">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ ['Yalıkavak', 'Türkbükü', 'Gümüşlük', 'Bitez'][$i - 1] }}
                                </p>
                                <div class="text-xl font-bold gradient-text">
                                    ₺{{ number_format(8000000 + $i * 1000000, 0, ',', '.') }}</div>
                                <div
                                    class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 text-sm text-gray-500">
                                    <span>{{ 3 + $i }}+1</span>
                                    <span>{{ 150 + $i * 30 }} m²</span>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Initialize gallery
        document.addEventListener('DOMContentLoaded', function() {
            // Gallery functionality here
        });
    </script>
@endpush
