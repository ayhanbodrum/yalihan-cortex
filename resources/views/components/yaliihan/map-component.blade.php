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
    $apiKey = $apiKey ?? config('services.google_maps.api_key', '');
@endphp

<div class="yaliihan-map-container {{ $class }}" style="width: {{ $width }}; height: {{ $height }};">
    <!-- Map Container -->
    <div id="map-{{ uniqid() }}" class="w-full h-full rounded-2xl shadow-lg overflow-hidden"
        style="width: {{ $width }}; height: {{ $height }};">
    </div>

    <!-- Map Controls -->
    @if ($showControls)
        <div class="map-controls absolute top-4 right-4 z-10 flex flex-col gap-2">
            @if ($showTraffic)
                <button id="traffic-toggle" class="bg-white rounded-lg p-2 shadow-lg hover:bg-gray-50 transition-colors"
                    title="Trafik Durumu">
                    🚦
                </button>
            @endif

            @if ($showTransit)
                <button id="transit-toggle" class="bg-white rounded-lg p-2 shadow-lg hover:bg-gray-50 transition-colors"
                    title="Toplu Taşıma">
                    🚌
                </button>
            @endif

            @if ($showBicycling)
                <button id="bicycling-toggle"
                    class="bg-white rounded-lg p-2 shadow-lg hover:bg-gray-50 transition-colors"
                    title="Bisiklet Yolları">
                    🚴
                </button>
            @endif
        </div>
    @endif

    <!-- Loading Overlay -->
    <div id="map-loading" class="absolute inset-0 bg-gray-100 flex items-center justify-center z-20">
        <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500 mx-auto mb-2"></div>
            <p class="text-gray-600">Harita yükleniyor...</p>
        </div>
    </div>
</div>

<!-- Google Maps API Script -->
@if($apiKey && $apiKey !== 'your-google-maps-api-key-here' && $apiKey !== 'AIzaSyBvOkBwZvUvOkBwZvUvOkBwZvUvOkBwZvU')
<script>
    (g => {
        var h, a, k, p = "The Google Maps JavaScript API",
            c = "google",
            l = "importLibrary",
            q = "__ib__",
            m = document,
            b = window;
        b = b[c] || (b[c] = {});
        var d = b.maps || (b.maps = {}),
            r = new Set,
            e = new URLSearchParams,
            u = () => h || (h = new Promise(async (f, n) => {
                await (a = m.createElement("script"));
                e.set("libraries", [...r] + "");
                for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                e.set("callback", c + ".maps." + q);
                a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                d[q] = f;
                a.onerror = () => h = n(Error(p + " could not load."));
                a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                m.head.append(a)
            }));
        d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() =>
            d[l](f, ...n))
    })({
        key: "{{ $apiKey }}",
        v: "weekly",
        libraries: ["maps", "marker", "places", "geometry"]
    });
</script>
@else
<script>
    console.warn('⚠️ Google Maps API key not configured. Please set GOOGLE_MAPS_API_KEY in .env file');
    // Show error message in map container
    document.addEventListener('DOMContentLoaded', function() {
        const mapContainer = document.querySelector('.yaliihan-map-container');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="flex items-center justify-center h-full bg-gray-100 rounded-2xl">
                    <div class="text-center text-gray-600">
                        <div class="text-4xl mb-2">🗺️</div>
                        <p class="text-sm">Google Maps API key gerekli</p>
                        <p class="text-xs text-gray-500 mt-1">Lütfen .env dosyasında GOOGLE_MAPS_API_KEY ayarlayın</p>
                    </div>
                </div>
            `;
        }
    });
</script>
@endif

<script>
    // Initialize Map
    async function initMap{{ uniqid() }}() {
        try {
            const {
                Map
            } = await google.maps.importLibrary("maps");
            const {
                AdvancedMarkerElement
            } = await google.maps.importLibrary("marker");
            const {
                InfoWindow
            } = await google.maps.importLibrary("maps");

            // Map configuration
            const mapConfig = {
                center: {
                    lat: {{ $center['lat'] }},
                    lng: {{ $center['lng'] }}
                },
                zoom: {{ $zoom }},
                mapId: "{{ $mapId }}",
                styles: [{
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{
                        visibility: "off"
                    }]
                }],
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: true,
                zoomControl: true
            };

            // Create map
            const map = new Map(document.getElementById("map-{{ uniqid() }}"), mapConfig);

            // Add markers
            const markers = [];
            const infoWindows = [];

            @foreach ($mapMarkers as $index => $marker)
                // Marker {{ $index + 1 }}
                const marker{{ $index + 1 }} = new AdvancedMarkerElement({
                    map: map,
                    position: {
                        lat: {{ $marker['position']['lat'] }},
                        lng: {{ $marker['position']['lng'] }}
                    },
                    title: "{{ $marker['title'] }}",
                    @if (isset($marker['icon']))
                        content: {!! json_encode($marker['icon']) !!}
                    @endif
                });

                // Info Window
                const infoWindow{{ $index + 1 }} = new InfoWindow({
                    content: `
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $marker['title'] }}</h3>
                            <p class="text-gray-600">{{ $marker['content'] }}</p>
                            <div class="mt-3">
                                <button class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors text-sm">
                                    Detayları Gör
                                </button>
                            </div>
                        </div>
                    `
                });

                // Click event
                marker{{ $index + 1 }}.addEventListener('gmp-click', () => {
                    // Close other info windows
                    infoWindows.forEach(iw => iw.close());

                    // Open this info window
                    infoWindow{{ $index + 1 }}.open({
                        anchor: marker{{ $index + 1 }}
                    });
                });

                markers.push(marker{{ $index + 1 }});
                infoWindows.push(infoWindow{{ $index + 1 }});
            @endforeach

            // Fit bounds to show all markers
            if (markers.length > 1) {
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => {
                    bounds.extend(marker.position);
                });
                map.fitBounds(bounds);
            }

            // Map controls
            @if ($showTraffic)
                const trafficLayer = new google.maps.TrafficLayer();
                let trafficVisible = false;

                document.getElementById('traffic-toggle').addEventListener('click', () => {
                    trafficVisible = !trafficVisible;
                    if (trafficVisible) {
                        trafficLayer.setMap(map);
                        document.getElementById('traffic-toggle').classList.add('bg-orange-500',
                            'text-white');
                    } else {
                        trafficLayer.setMap(null);
                        document.getElementById('traffic-toggle').classList.remove('bg-orange-500',
                            'text-white');
                    }
                });
            @endif

            @if ($showTransit)
                const transitLayer = new google.maps.TransitLayer();
                let transitVisible = false;

                document.getElementById('transit-toggle').addEventListener('click', () => {
                    transitVisible = !transitVisible;
                    if (transitVisible) {
                        transitLayer.setMap(map);
                        document.getElementById('transit-toggle').classList.add('bg-orange-500',
                            'text-white');
                    } else {
                        transitLayer.setMap(null);
                        document.getElementById('transit-toggle').classList.remove('bg-orange-500',
                            'text-white');
                    }
                });
            @endif

            @if ($showBicycling)
                const bicyclingLayer = new google.maps.BicyclingLayer();
                let bicyclingVisible = false;

                document.getElementById('bicycling-toggle').addEventListener('click', () => {
                    bicyclingVisible = !bicyclingVisible;
                    if (bicyclingVisible) {
                        bicyclingLayer.setMap(map);
                        document.getElementById('bicycling-toggle').classList.add('bg-orange-500',
                            'text-white');
                    } else {
                        bicyclingLayer.setMap(null);
                        document.getElementById('bicycling-toggle').classList.remove('bg-orange-500',
                            'text-white');
                    }
                });
            @endif

            // Hide loading overlay
            document.getElementById('map-loading').style.display = 'none';

            // Store map instance globally for external access
            window.yaliihanMap = map;

        } catch (error) {
            console.error('Google Maps API Error:', error);
            document.getElementById('map-loading').innerHTML = `
                <div class="text-center text-red-500">
                    <div class="text-4xl mb-2">⚠️</div>
                    <p>Harita yüklenirken hata oluştu</p>
                    <button onclick="location.reload()" class="mt-2 bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                        Tekrar Dene
                    </button>
                </div>
            `;
        }
    }

    // Initialize map when Google Maps API is loaded
    window.initMap = initMap{{ uniqid() }};
</script>

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

    /* Responsive adjustments */
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

    /* Google Maps InfoWindow customization */
    .gm-style .gm-style-iw-c {
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .gm-style .gm-style-iw-d {
        overflow: hidden !important;
    }
</style>
