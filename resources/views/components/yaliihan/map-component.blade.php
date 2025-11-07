@props([
    'center' => ['lat' => 37.4220656, 'lng' => -122.0840897], // Default: Google HQ
    'zoom' => 10,
    'markers' => [],
    'height' => '400px',
    'width' => '100%',
    'mapId' => 'DEMO_MAP_ID',
    'showControls' => true,
    'showTraffic' => false,
    'showTransit' => false,
    'showBicycling' => false,
    'class' => '',
    'apiKey' => null,
])

@php
    $uniqueId = uniqid('map_');
    $mapElementId = 'map-' . $uniqueId;
    $loadingId = 'map-loading-' . $uniqueId;
    $wrapperId = 'map-wrapper-' . $uniqueId;
    $trafficToggleId = 'traffic-toggle-' . $uniqueId;
    $transitToggleId = 'transit-toggle-' . $uniqueId;
    $bicyclingToggleId = 'bicycling-toggle-' . $uniqueId;

    // Default markers if none provided
    $defaultMarkers = [
        [
            'position' => ['lat' => 37.4220656, 'lng' => -122.0840897],
            'title' => 'Google HQ',
            'content' => 'Google Headquarters',
            'icon' => null,
        ],
    ];

    $mapMarkers = !empty($markers) ? $markers : $defaultMarkers;
    $resolvedApiKey = $apiKey ?? config('services.google_maps.api_key', '');
    $hasValidKey = $resolvedApiKey && !in_array($resolvedApiKey, ['your-google-maps-api-key-here', 'AIzaSyBvOkBwZvUvOkBwZvUvOkBwZvUvOkBwZvU']);
@endphp

<div id="{{ $wrapperId }}" class="yaliihan-map-container {{ $class }}" style="width: {{ $width }}; height: {{ $height }};">
    <!-- Map Container -->
    <div id="{{ $mapElementId }}" class="w-full h-full rounded-2xl shadow-lg overflow-hidden"
        style="width: {{ $width }}; height: {{ $height }};">
    </div>

    <!-- Map Controls -->
    @if ($showControls)
        <div class="map-controls absolute top-4 right-4 z-10 flex flex-col gap-2">
            @if ($showTraffic)
                <button id="{{ $trafficToggleId }}"
                    class="bg-white rounded-lg p-2 shadow-lg hover:bg-gray-50 transition-colors"
                    title="Trafik Durumu">
                    🚦
                </button>
            @endif

            @if ($showTransit)
                <button id="{{ $transitToggleId }}"
                    class="bg-white rounded-lg p-2 shadow-lg hover:bg-gray-50 transition-colors"
                    title="Toplu Taşıma">
                    🚌
                </button>
            @endif

            @if ($showBicycling)
                <button id="{{ $bicyclingToggleId }}"
                    class="bg-white rounded-lg p-2 shadow-lg hover:bg-gray-50 transition-colors"
                    title="Bisiklet Yolları">
                    🚴
                </button>
            @endif
        </div>
    @endif

    <!-- Loading Overlay -->
    <div id="{{ $loadingId }}" class="absolute inset-0 bg-gray-100 flex items-center justify-center z-20">
        <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500 mx-auto mb-2"></div>
            <p class="text-gray-600">Harita yükleniyor...</p>
        </div>
    </div>
</div>

@if ($hasValidKey)
    <script>
        function initMap{{ $uniqueId }}() {
            try {
                const mapElement = document.getElementById("{{ $mapElementId }}");
                if (!mapElement) {
                    console.warn('Context7: Map element bulunamadı ({{ $mapElementId }})');
                    return;
                }

                const mapConfig = {
                    center: {
                        lat: {{ $center['lat'] }},
                        lng: {{ $center['lng'] }}
                    },
                    zoom: {{ $zoom }},
                    mapId: "{{ $mapId }}",
                    styles: [{
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{ visibility: 'off' }]
                    }],
                    mapTypeControl: true,
                    streetViewControl: true,
                    fullscreenControl: true,
                    zoomControl: true
                };

                const map = new google.maps.Map(mapElement, mapConfig);

                const markers = [];
                const infoWindows = [];

                @foreach ($mapMarkers as $index => $marker)
                    const marker{{ $index + 1 }} = new google.maps.Marker({
                        map: map,
                        position: {
                            lat: {{ $marker['position']['lat'] }},
                            lng: {{ $marker['position']['lng'] }}
                        },
                        title: "{{ $marker['title'] }}"
                    });

                    const infoWindow{{ $index + 1 }} = new google.maps.InfoWindow({
                        content: `
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $marker['title'] }}</h3>
                                <p class="text-gray-600">{{ $marker['content'] }}</p>
                                <div class="mt-3">
                                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        Detayları Gör
                                    </button>
                                </div>
                            </div>
                        `
                    });

                    marker{{ $index + 1 }}.addListener('click', () => {
                        infoWindows.forEach(iw => iw.close());
                        infoWindow{{ $index + 1 }}.open({ map, anchor: marker{{ $index + 1 }} });
                    });

                    markers.push(marker{{ $index + 1 }});
                    infoWindows.push(infoWindow{{ $index + 1 }});
                @endforeach

                if (markers.length > 1) {
                    const bounds = new google.maps.LatLngBounds();
                    markers.forEach(marker => bounds.extend(marker.getPosition()));
                    map.fitBounds(bounds);
                }

                @if ($showTraffic)
                    const trafficLayer = new google.maps.TrafficLayer();
                    let trafficVisible = false;
                    const trafficToggle = document.getElementById("{{ $trafficToggleId }}");
                    trafficToggle?.addEventListener('click', () => {
                        trafficVisible = !trafficVisible;
                        if (trafficVisible) {
                            trafficLayer.setMap(map);
                            trafficToggle.classList.add('bg-blue-600', 'text-white');
                        } else {
                            trafficLayer.setMap(null);
                            trafficToggle.classList.remove('bg-blue-600', 'text-white');
                        }
                    });
                @endif

                @if ($showTransit)
                    const transitLayer = new google.maps.TransitLayer();
                    let transitVisible = false;
                    const transitToggle = document.getElementById("{{ $transitToggleId }}");
                    transitToggle?.addEventListener('click', () => {
                        transitVisible = !transitVisible;
                        if (transitVisible) {
                            transitLayer.setMap(map);
                            transitToggle.classList.add('bg-blue-600', 'text-white');
                        } else {
                            transitLayer.setMap(null);
                            transitToggle.classList.remove('bg-blue-600', 'text-white');
                        }
                    });
                @endif

                @if ($showBicycling)
                    const bicyclingLayer = new google.maps.BicyclingLayer();
                    let bicyclingVisible = false;
                    const bicyclingToggle = document.getElementById("{{ $bicyclingToggleId }}");
                    bicyclingToggle?.addEventListener('click', () => {
                        bicyclingVisible = !bicyclingVisible;
                        if (bicyclingVisible) {
                            bicyclingLayer.setMap(map);
                            bicyclingToggle.classList.add('bg-blue-600', 'text-white');
                        } else {
                            bicyclingLayer.setMap(null);
                            bicyclingToggle.classList.remove('bg-blue-600', 'text-white');
                        }
                    });
                @endif

                const loadingOverlay = document.getElementById("{{ $loadingId }}");
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }

                window[`yaliihanMap_${@json($uniqueId)}`] = map;
            } catch (error) {
                console.error('Google Maps API Error:', error);
                const loadingOverlay = document.getElementById("{{ $loadingId }}");
                if (loadingOverlay) {
                    loadingOverlay.innerHTML = `
                        <div class="text-center text-red-500">
                            <div class="text-4xl mb-2">⚠️</div>
                            <p>Harita yüklenirken hata oluştu</p>
                            <button onclick="location.reload()" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Tekrar Dene
                            </button>
                        </div>
                    `;
                }
            }
        }

        window[`initYaliihanMap_${@json($uniqueId)}`] = initMap{{ $uniqueId }};
    </script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ $resolvedApiKey }}&callback=initMap{{ $uniqueId }}&libraries=maps,marker,places,geometry"></script>
@else
    <script>
        console.warn('⚠️ Google Maps API key not configured. Please set GOOGLE_MAPS_API_KEY in .env file');
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('{{ $wrapperId }}');
            if (!wrapper) {
                return;
            }
            wrapper.innerHTML = `
                <div class="flex items-center justify-center h-full bg-gray-100 dark:bg-gray-800 rounded-2xl">
                    <div class="text-center text-gray-600 dark:text-gray-300 px-6 py-8">
                        <div class="text-4xl mb-3">🗺️</div>
                        <p class="font-semibold">Google Maps API key gerekli</p>
                        <p class="text-sm mt-2 text-gray-500 dark:text-gray-400">Lütfen .env dosyasında <code>GOOGLE_MAPS_API_KEY</code> değerini tanımlayın.</p>
                    </div>
                </div>
            `;
        });
    </script>
@endif

<style>
    .yaliihan-map-container {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .map-controls button {
        transition: all 0.3s ease;
    }

    .map-controls button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 768px) {
        .map-controls {
            top: 1rem;
            right: 1rem;
        }

        .map-controls button {
            padding: 0.5rem;
            font-size: 0.875rem;
        }
    }

    .gm-style .gm-style-iw-c {
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .gm-style .gm-style-iw-d {
        overflow: hidden !important;
    }
</style>
