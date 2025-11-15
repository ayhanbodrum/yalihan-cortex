<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'mapId' => 'map',
    'latField' => 'enlem',
    'lngField' => 'boylam',
    'addressField' => 'adres',
    'structuredFields' => [
        'street' => 'sokak',
        'avenue' => 'cadde',
        'boulevard' => 'bulvar',
        'building' => 'bina_no',
        'postalCode' => 'posta_kodu',
    ],
    'height' => '500px',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'mapId' => 'map',
    'latField' => 'enlem',
    'lngField' => 'boylam',
    'addressField' => 'adres',
    'structuredFields' => [
        'street' => 'sokak',
        'avenue' => 'cadde',
        'boulevard' => 'bulvar',
        'building' => 'bina_no',
        'postalCode' => 'posta_kodu',
    ],
    'height' => '500px',
]); ?>
<?php foreach (array_filter(([
    'mapId' => 'map',
    'latField' => 'enlem',
    'lngField' => 'boylam',
    'addressField' => 'adres',
    'structuredFields' => [
        'street' => 'sokak',
        'avenue' => 'cadde',
        'boulevard' => 'bulvar',
        'building' => 'bina_no',
        'postalCode' => 'posta_kodu',
    ],
    'height' => '500px',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="relative group">
    <div id="<?php echo e($mapId); ?>" data-lat-field="<?php echo e($latField); ?>" data-lng-field="<?php echo e($lngField); ?>"
        data-address-field="<?php echo e($addressField); ?>" data-structured-fields='<?php echo json_encode($structuredFields, 15, 512) ?>'
        class="w-full rounded-2xl border-4 border-white dark:border-gray-700 overflow-hidden shadow-2xl ring-4 ring-green-500/10"
        role="application" aria-label="Harita" style="height: <?php echo e($height); ?>;">
        <div
            class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800"
            role="status" aria-live="polite" aria-busy="true">
            <div class="text-center">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white dark:bg-gray-800 shadow-xl mb-4">
                    <svg class="w-10 h-10 text-green-500 animate-pulse" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <p class="text-gray-600 dark:text-gray-400 font-medium">Harita yükleniyor...</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">OpenStreetMap</p>
            </div>
        </div>
    </div>

    
    <div class="absolute top-4 right-4 flex flex-col gap-3 pointer-events-auto z-[9999]">
        
        <div
            class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-xl shadow-xl border-2 border-gray-200 dark:border-gray-700 p-1.5">
            <div class="flex gap-1">
                <button type="button" onclick="(function(){
                        const s=document.getElementById('btn-map-standard');
                        const t=document.getElementById('btn-map-satellite');
                        if(s){s.setAttribute('aria-pressed','true');}
                        if(t){t.setAttribute('aria-pressed','false');}
                    })(); VanillaLocationManager?.setMapType?.('standard')" id="btn-map-standard"
                    class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-bold bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 motion-reduce:transform-none motion-reduce:transition-none"
                    title="Standart Harita" aria-label="Standart Harita" aria-controls="<?php echo e($mapId); ?>" aria-pressed="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span class="hidden sm:inline">Standart</span>
                </button>
                <button type="button" onclick="(function(){
                        const s=document.getElementById('btn-map-standard');
                        const t=document.getElementById('btn-map-satellite');
                        if(s){s.setAttribute('aria-pressed','false');}
                        if(t){t.setAttribute('aria-pressed','true');}
                    })(); VanillaLocationManager?.setMapType?.('satellite')"
                    id="btn-map-satellite"
                    class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-bold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 hover:scale-105 active:scale-95 motion-reduce:transform-none motion-reduce:transition-none"
                    title="Uydu Görüntüsü" aria-label="Uydu Görüntüsü" aria-controls="<?php echo e($mapId); ?>" aria-pressed="false">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="hidden sm:inline">Uydu</span>
                </button>
            </div>
        </div>

        
        <div
            class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-xl shadow-xl border-2 border-gray-200 dark:border-gray-700 p-1.5">
            <div class="flex flex-col gap-1">
                
                <button type="button" onclick="VanillaLocationManager?.zoomIn?.()"
                    class="group flex items-center justify-center w-11 h-11 md:w-9 md:h-9 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:text-white hover:bg-gradient-to-br hover:from-blue-500 hover:to-blue-600 transition-all duration-200 hover:shadow-lg hover:scale-105 active:scale-95 motion-reduce:transform-none motion-reduce:transition-none"
                    title="Yakınlaştır" aria-label="Yakınlaştır" aria-controls="<?php echo e($mapId); ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>

                
                <div class="h-px bg-gray-200 dark:bg-gray-600 mx-1"></div>

                
                <button type="button" onclick="VanillaLocationManager?.zoomOut?.()"
                    class="group flex items-center justify-center w-11 h-11 md:w-9 md:h-9 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:text-white hover:bg-gradient-to-br hover:from-blue-500 hover:to-blue-600 transition-all duration-200 hover:shadow-lg hover:scale-105 active:scale-95 motion-reduce:transform-none motion-reduce:transition-none"
                    title="Uzaklaştır" aria-label="Uzaklaştır" aria-controls="<?php echo e($mapId); ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 12H6" />
                    </svg>
                </button>

                
                <div class="h-px bg-gray-200 dark:bg-gray-600 mx-1"></div>

                
                <button type="button" onclick="VanillaLocationManager?.getCurrentLocation?.()"
                    class="group flex items-center justify-center w-11 h-11 md:w-9 md:h-9 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:text-white hover:bg-gradient-to-br hover:from-green-500 hover:to-emerald-600 transition-all duration-200 hover:shadow-lg hover:scale-105 active:scale-95 motion-reduce:transform-none motion-reduce:transition-none"
                    title="Mevcut Konumum" aria-label="Mevcut Konumum" aria-controls="<?php echo e($mapId); ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <script>
        (function(){
            const el=document.getElementById(<?php echo json_encode($mapId, 15, 512) ?>);
            if(!el) return;
            const init=()=>{
                if(window.VanillaLocationManager && typeof window.VanillaLocationManager.initMap==='function'){
                    window.VanillaLocationManager.initMap(<?php echo json_encode($mapId, 15, 512) ?>);
                } else {
                    el.dispatchEvent(new CustomEvent('map:visible', { detail: { id: <?php echo json_encode($mapId, 15, 512) ?> } }));
                }
            };
            if('IntersectionObserver' in window){
                const io=new IntersectionObserver((entries)=>{
                    entries.forEach(e=>{ if(e.isIntersecting){ init(); io.disconnect(); } });
                }, { rootMargin: '0px', threshold: 0.1 });
                io.observe(el);
            } else {
                init();
            }
        })();
    </script>
</div>
<?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/components/context7/map-picker.blade.php ENDPATH**/ ?>