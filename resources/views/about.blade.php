@extends('layouts.frontend')

@section('title', 'Hakkımızda')

@section('content')
    <div class="min-h-screen">
        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <!-- Background animation -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary-600 via-primary-700 to-blue-900 z-0">
                <div class="absolute top-0 left-0 w-full h-full opacity-20">
                    <div class="absolute -top-40 -left-40 w-80 h-80 rounded-full bg-primary-400 animate-blob"></div>
                    <div
                        class="absolute top-20 right-10 w-72 h-72 rounded-full bg-blue-400 animate-blob animation-delay-2000">
                    </div>
                    <div
                        class="absolute -bottom-20 left-20 w-72 h-72 rounded-full bg-indigo-400 animate-blob animation-delay-4000">
                    </div>
                </div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-6 sm:px-8 py-24">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-extrabold mb-6 text-white drop-shadow-md">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-white to-blue-100">
                            Hakkımızda
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-6 text-blue-100 max-w-3xl mx-auto">
                        Yalıhan Emlak ile emlak dünyasında profesyonel ve yenilikçi çözümler
                    </p>
                </div>
            </div>
        </div>

        <!-- About Content -->
        <div class="max-w-7xl mx-auto px-6 sm:px-8 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-20">
                <div>
                    <div
                        class="inline-block px-4 py-1.5 bg-primary-50 text-primary-700 rounded-full text-sm font-semibold mb-6">
                        <span class="flex items-center"><i class="fas fa-star mr-2"></i> Hakkında</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-8 leading-tight">Emlak Sektöründe <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-blue-700">Teknoloji
                            Lideri</span></h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        Yalıhan Emlak, Bodrum/Yalıkavak bölgesinde emlak sektöründe güvenilir çözümler sunan bir firmadır.
                        Müşterilerimize en kaliteli hizmeti sunmak için sürekli gelişen hizmetlerimiz ve
                        uzman ekibimizle çalışıyoruz.
                    </p>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Gayrimenkul alım-satım ve kiralama işlemlerinde güvenilir ortaklığınız olan Yalıhan Emlak,
                        hem bireysel hem de kurumsal müşterilerine özel çözümler sunmaktadır.
                    </p>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                            <i class="fas fa-check-circle text-primary-600 text-xl mr-4"></i>
                            <span class="text-gray-700 font-medium">Güvenilir hizmet</span>
                        </div>
                        <div class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                            <i class="fas fa-users text-primary-600 text-xl mr-4"></i>
                            <span class="text-gray-700 font-medium">Profesyonel ekip</span>
                        </div>
                    </div>
                </div>
                <div class="relative order-first lg:order-last">
                    <div
                        class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-8 shadow-xl border border-gray-100 relative z-10">
                        <div
                            class="w-20 h-20 flex items-center justify-center mx-auto bg-primary-600 text-white rounded-2xl mb-6 shadow-lg transform -rotate-6 hover:rotate-0 transition-all">
                            <i class="fas fa-building text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">Modern Emlak Platformu</h3>
                        <p class="text-gray-600 text-center mb-6">Teknoloji odaklı çözümlerle emlak sektöründe fark
                            yaratıyoruz</p>

                        <div class="flex justify-center">
                            <a href="{{ route('contact') }}"
                                class="inline-flex items-center bg-primary-50 text-primary-700 px-4 py-2 rounded-lg font-medium hover:bg-primary-100 transition-colors">
                                <i class="fas fa-arrow-right mr-2"></i> Bizimle İletişime Geçin
                            </a>
                        </div>
                    </div>
                    <!-- Decorative elements -->
                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-primary-100 rounded-full -z-10"></div>
                    <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-blue-100 rounded-full -z-10"></div>
                </div>
            </div>

            <!-- Values Section -->
            <div class="relative my-20 py-16">
                <!-- Background pattern -->
                <div class="absolute inset-0 bg-pattern opacity-5 -z-10"></div>

                <div class="text-center mb-16">
                    <div class="inline-block px-4 py-1.5 bg-blue-50 text-blue-700 rounded-full text-sm font-semibold mb-4">
                        <span class="flex items-center justify-center"><i class="fas fa-heart mr-2"></i> Değerlerimiz</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Bizi Biz Yapan Değerler</h2>
                    <div class="w-20 h-1.5 bg-primary-500 mx-auto rounded-full mb-6"></div>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">Müşterilerimize en iyi hizmeti sunmak için
                        benimsediğimiz çalışma prensipleri</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-7xl mx-auto px-6 sm:px-8">
                    <div
                        class="bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100 transform transition-all hover:-translate-y-2 hover:shadow-2xl">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg transform rotate-6 group-hover:rotate-0 transition-transform">
                            <i class="fas fa-shield-alt text-2xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Güvenilirlik</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Tüm işlemlerimizde şeffaflık ve güvenilirlik ilkesini benimser,
                            müşterilerimizin haklarını koruruz. Dürüst ve açık iletişim bizim için esastır.
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100 transform transition-all hover:-translate-y-2 hover:shadow-2xl">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg transform rotate-6 group-hover:rotate-0 transition-transform">
                            <i class="fas fa-rocket text-2xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">İnovasyon</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Emlak sektöründe yenilikçi teknolojiler kullanarak
                            müşteri deneyimini sürekli iyileştiriyoruz. Kendimizi sürekli geliştirmeye odaklanıyoruz.
                        </p>
                    </div>
                    <div
                        class="bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100 transform transition-all hover:-translate-y-2 hover:shadow-2xl">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg transform rotate-6 group-hover:rotate-0 transition-transform">
                            <i class="fas fa-heart text-2xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Müşteri Odaklılık</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Her müşterimizin ihtiyacına özel çözümler üretir,
                            onların memnuniyetini ön planda tutarız. Müşteri memnuniyeti bizim önceliğimizdir.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Statistics Section -->
            <div
                class="bg-gradient-to-r from-primary-600 to-blue-700 rounded-3xl text-white p-12 mb-20 shadow-xl relative overflow-hidden">
                <!-- Decorative patterns -->
                <div class="absolute inset-0 bg-pattern opacity-10"></div>
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white bg-opacity-10 rounded-full"></div>
                <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white bg-opacity-10 rounded-full"></div>

                <div class="text-center mb-12 relative z-10">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Rakamlarla Yalıhan Emlak</h2>
                    <div class="w-20 h-1.5 bg-white mx-auto rounded-full mb-4 opacity-70"></div>
                    <p class="text-blue-100 text-lg">Başarılarımızı rakamlarla anlatıyoruz</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 relative z-10">
                    <div class="text-center bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm p-6 rounded-xl">
                        <div class="text-4xl font-bold mb-3">500<span class="text-blue-300">+</span></div>
                        <div class="text-blue-100 font-medium">Satış İşlemi</div>
                    </div>
                    <div class="text-center bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm p-6 rounded-xl">
                        <div class="text-4xl font-bold mb-3">1000<span class="text-blue-300">+</span></div>
                        <div class="text-blue-100 font-medium">Memnun Müşteri</div>
                    </div>
                    <div class="text-center bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm p-6 rounded-xl">
                        <div class="text-4xl font-bold mb-3">50<span class="text-blue-300">+</span></div>
                        <div class="text-blue-100 font-medium">Uzman Danışman</div>
                    </div>
                    <div class="text-center bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm p-6 rounded-xl">
                        <div class="text-4xl font-bold mb-3">5<span class="text-blue-300">+</span></div>
                        <div class="text-blue-100 font-medium">Yıllık Deneyim</div>
                    </div>
                </div>
            </div>

            <!-- Team Section -->
            <div class="text-center mb-20">
                <div class="inline-block px-4 py-1.5 bg-purple-50 text-purple-700 rounded-full text-sm font-semibold mb-6">
                    <span class="flex items-center"><i class="fas fa-users mr-2"></i> Profesyonel Ekip</span>
                </div>

                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Güçlü Ekibimizle Tanışın</h2>
                <div class="w-20 h-1.5 bg-primary-500 mx-auto rounded-full mb-6"></div>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-12">Alanında uzman profesyonellerden oluşan ekibimizle
                    sizlere en iyi hizmeti sunuyoruz</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div
                        class="bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100 group hover:bg-gradient-to-b hover:from-primary-600 hover:to-blue-700 transition-all duration-300">
                        <div
                            class="w-24 h-24 bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow group-hover:bg-white transition-all duration-300">
                            <i class="fas fa-user-tie text-3xl text-primary-600"></i>
                        </div>
                        <h3
                            class="text-xl font-bold text-gray-900 mb-4 group-hover:text-white transition-colors duration-300">
                            Satış Uzmanları</h3>
                        <p class="text-gray-600 group-hover:text-blue-100 transition-colors duration-300">
                            Deneyimli satış uzmanlarımız ile emlak alım-satım işlemlerinde profesyonel destek sunuyoruz.
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100 group hover:bg-gradient-to-b hover:from-primary-600 hover:to-blue-700 transition-all duration-300">
                        <div
                            class="w-24 h-24 bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow group-hover:bg-white transition-all duration-300">
                            <i class="fas fa-laptop-code text-3xl text-primary-600"></i>
                        </div>
                        <h3
                            class="text-xl font-bold text-gray-900 mb-4 group-hover:text-white transition-colors duration-300">
                            Teknoloji Ekibi</h3>
                        <p class="text-gray-600 group-hover:text-blue-100 transition-colors duration-300">
                            Platformumuzu sürekli geliştiren ve güncel tutan teknoloji uzmanlarımız ile hizmetinizdeyiz.
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100 group hover:bg-gradient-to-b hover:from-primary-600 hover:to-blue-700 transition-all duration-300">
                        <div
                            class="w-24 h-24 bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow group-hover:bg-white transition-all duration-300">
                            <i class="fas fa-headset text-3xl text-primary-600"></i>
                        </div>
                        <h3
                            class="text-xl font-bold text-gray-900 mb-4 group-hover:text-white transition-colors duration-300">
                            Müşteri Hizmetleri</h3>
                        <p class="text-gray-600 group-hover:text-blue-100 transition-colors duration-300">
                            7/24 müşteri desteği ile her zaman yanınızda olmaktan gurur duyuyoruz.
                        </p>
                    </div>
                </div>

                <div class="mt-12">
                    <a href="{{ route('contact') }}" class="neo-btn neo-btn-primary inline-flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        Bizimle İletişime Geçin
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
