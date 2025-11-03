@props([
    'title' => '🏠 Yalıhan Emlak',
    'subtitle' => 'Bodrum\'un en güzel emlakları burada!',
    'showSearch' => true,
    'backgroundImage' => null,
    'overlay' => true,
])

<section
    class="hero-section relative {{ $backgroundImage ? 'bg-cover bg-center' : 'bg-gradient-to-br from-orange-600 to-red-600' }} py-16 md:py-24 text-white overflow-hidden">
    @if ($backgroundImage)
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $backgroundImage }}')"></div>
    @endif

    @if ($overlay)
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    @endif

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <!-- Main Title -->
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                {{ $title }}
            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl mb-8 opacity-90">
                {{ $subtitle }}
            </p>

            @if ($showSearch)
                <!-- Search Form -->
                <div class="max-w-4xl mx-auto">
                    <x-yaliihan.search-form :show-advanced="true" :show-sort="false" class="hero-search" />
                </div>
            @endif

            <!-- Stats -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">500+</div>
                    <div class="text-lg opacity-90">Aktif İlan</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">20+</div>
                    <div class="text-lg opacity-90">Yıl Deneyim</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">1000+</div>
                    <div class="text-lg opacity-90">Mutlu Müşteri</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Elements -->
    <div class="absolute top-20 left-10 w-20 h-20 bg-white bg-opacity-10 rounded-full animate-pulse"></div>
    <div class="absolute bottom-20 right-10 w-16 h-16 bg-white bg-opacity-10 rounded-full animate-pulse delay-1000">
    </div>
    <div class="absolute top-1/2 left-20 w-12 h-12 bg-white bg-opacity-10 rounded-full animate-pulse delay-500"></div>
</section>

<style>
    .hero-section {
        position: relative;
    }

    .hero-search .search-form {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .hero-search .search-form input,
    .hero-search .search-form select {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .hero-search .search-form input:focus,
    .hero-search .search-form select:focus {
        background: white;
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    .animate-float:nth-child(2) {
        animation-delay: 2s;
    }

    .animate-float:nth-child(3) {
        animation-delay: 4s;
    }
</style>
