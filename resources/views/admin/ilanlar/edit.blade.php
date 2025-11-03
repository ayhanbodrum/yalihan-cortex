@extends('admin.layouts.neo')

@section('title', 'İlan Düzenle')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-edit text-blue-600 mr-3"></i>
                İlan Düzenle
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                "{{ $ilan->baslik ?? 'İsimsiz İlan' }}" adlı ilanı düzenleyin
            </p>
        </div>

        <!-- Modern Component-Based Form (create.blade.php ile aynı yapı) -->
        <form action="{{ route('admin.ilanlar.update', $ilan->id) }}" method="POST" enctype="multipart/form-data"
              class="space-y-8" id="ilanEditForm">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                <!-- Section 1: Basic Info -->
                @include('admin.ilanlar.components.basic-info', ['ilan' => $ilan])

                <!-- Section 2: Category System -->
                @include('admin.ilanlar.components.category-system', ['ilan' => $ilan])

                <!-- Section 3: Type-based Fields -->
                @include('admin.ilanlar.components.type-fields', ['ilan' => $ilan])

                <!-- Section 3.5: Arsa Calculation -->
                @include('admin.ilanlar.components.arsa-calculation', ['ilan' => $ilan])

                <!-- Section 4: Location & Map -->
                @include('admin.ilanlar.components.location-map', ['ilan' => $ilan])

                <!-- Section 5: Price Management -->
                @include('admin.ilanlar.components.price-management', ['ilan' => $ilan])

                <!-- Section 6: Site Selection -->
                @include('admin.ilanlar.components.site-selection', ['ilan' => $ilan])

                <!-- Section 7: AI Content -->
                @include('admin.ilanlar.components.ai-content', ['ilan' => $ilan])

                <!-- Section 8: Person Info (Context7 Live Search) -->
                @include('admin.ilanlar.partials.stable._kisi-secimi', ['ilan' => $ilan])

                <!-- Section 9: Publication Status -->
                @include('admin.ilanlar.components.publication-status', ['ilan' => $ilan])

                <!-- Section 10: Photos -->
                @include('admin.ilanlar.components.listing-photos', ['ilan' => $ilan])

                <!-- Section 11: Features -->
                @include('admin.ilanlar.components.features-dynamic', ['ilan' => $ilan])

                <!-- Section 12: Key Management -->
                @include('admin.ilanlar.components.key-management', ['ilan' => $ilan])
            </div>

            <!-- Form Actions -->
            <div class="mt-12 bg-white dark:bg-gray-800 rounded-lg p-6 shadow flex justify-between items-center">
                <a href="{{ route('admin.ilanlar.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    İptal
                </a>

                <div class="space-x-4">
                    <button type="submit" name="save_draft" value="1"
                            class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Taslak Kaydet
                    </button>

                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200">
                        <i class="fas fa-check mr-2"></i>
                        Güncelle ve Yayınla
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- Modular JavaScript (create.blade.php ile aynı) --}}
    @vite(['resources/js/admin/ilan-create.js'])

    {{-- Context7 Live Search (Vanilla JS - 3KB) --}}
    <script src="{{ asset('js/context7-live-search-simple.js') }}"></script>

    <!-- Leaflet.js OpenStreetMap Integration -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    {{-- Google Maps API (Geocoding için) --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') ?? env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initializeGoogleMaps"
        async defer></script>

    <script>
        // Edit mode için initialize
        window.editMode = true;
        window.ilanData = @json($ilan);

        // Initialize Google Maps callback
        window.initializeGoogleMaps = function() {
            console.log('✅ Google Maps loaded for edit mode');
        };
    </script>
@endpush
