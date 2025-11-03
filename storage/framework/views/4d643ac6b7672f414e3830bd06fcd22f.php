<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'showAdvanced' => true,
    'showSort' => true,
    'class' => '',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'showAdvanced' => true,
    'showSort' => true,
    'class' => '',
]); ?>
<?php foreach (array_filter(([
    'showAdvanced' => true,
    'showSort' => true,
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="search-form <?php echo e($class); ?>">
    <!-- Main Search Form -->
    <div class="bg-white rounded-2xl p-6 shadow-2xl">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- İlan Türü -->
            <div class="space-y-2">
                <label for="listing_type" class="block text-sm font-medium text-gray-700">İlan Türü</label>
                <select id="listing_type" name="listing_type"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors form-input"
                    aria-label="İlan türü seçiniz">
                    <option value="">Tümü</option>
                    <option value="sale">Satılık</option>
                    <option value="rent">Kiralık</option>
                </select>
            </div>

            <!-- Emlak Türü -->
            <div class="space-y-2">
                <label for="property_type" class="block text-sm font-medium text-gray-700">Emlak Türü</label>
                <select id="property_type" name="property_type"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors form-input"
                    aria-label="Emlak türü seçiniz">
                    <option value="">Tümü</option>
                    <option value="villa">Villa</option>
                    <option value="apartment">Daire</option>
                    <option value="land">Arsa</option>
                    <option value="commercial">İşyeri</option>
                </select>
            </div>

            <!-- Lokasyon -->
            <div class="space-y-2">
                <label for="location" class="block text-sm font-medium text-gray-700">Lokasyon</label>
                <input type="text" id="location" name="location" placeholder="Şehir, ilçe veya mahalle"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors form-input"
                    aria-label="Lokasyon giriniz" autocomplete="address-level2">
            </div>

            <!-- Search Button -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-transparent">Ara</label>
                <button type="submit"
                    class="w-full bg-orange-600 text-white p-3 rounded-lg hover:bg-orange-700 transition-colors font-semibold flex items-center justify-center gap-2 min-h-[48px] touch-manipulation"
                    onclick="performSearch()" aria-label="Emlak ara">
                    <span class="search-icon">🔍</span>
                    <span class="search-text">Ara</span>
                </button>
            </div>
        </div>

        <?php if($showAdvanced): ?>
            <!-- Advanced Search Toggle -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <button class="text-orange-600 hover:text-orange-700 font-medium flex items-center gap-2"
                    onclick="toggleAdvancedSearch()">
                    <span>🔧</span>
                    <span>Gelişmiş Arama</span>
                    <span id="advancedToggleIcon">▼</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if($showAdvanced): ?>
        <!-- Advanced Search Panel -->
        <div id="advancedSearchPanel" class="hidden mt-4 bg-white rounded-2xl p-6 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Yatak Odası -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Yatak Odası</label>
                    <select
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Herhangi</option>
                        <option value="1">1+</option>
                        <option value="2">2+</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="5">5+</option>
                    </select>
                </div>

                <!-- Banyo -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Banyo</label>
                    <select
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Herhangi</option>
                        <option value="1">1+</option>
                        <option value="2">2+</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                    </select>
                </div>

                <!-- Min Fiyat -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Min. Fiyat</label>
                    <input type="number" placeholder="0"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>

                <!-- Max Fiyat -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Max. Fiyat</label>
                    <input type="number" placeholder="Sınırsız"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>

                <!-- Min Alan -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Min. Alan (m²)</label>
                    <input type="number" placeholder="0"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>

                <!-- Max Alan -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Max. Alan (m²)</label>
                    <input type="number" placeholder="Sınırsız"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>

                <!-- Özellikler -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Özellikler</label>
                    <select
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Seçiniz</option>
                        <option value="pool">Havuz</option>
                        <option value="garden">Bahçe</option>
                        <option value="garage">Garaj</option>
                        <option value="balcony">Balkon</option>
                    </select>
                </div>

                <!-- Tapu Durumu -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Tapu Durumu</label>
                    <select name="tapu_statusu"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Herhangi</option>
                        <option>Hisseli Tapu</option>
                        <option>Müstakil Parsel</option>
                        <option>Tahsis</option>
                        <option>Zilliyet</option>
                        <option>Belirtilmemiş</option>
                        <option>Yabancıdan</option>
                        <option>Tapu yok</option>
                        <option>Kıbrıs Tapulu</option>
                        <option>Kooperatiften Hisseli Tapu</option>
                        <option>Müstakil Tapulu</option>
                        <option>İntifa Hakkı Tesisli</option>
                    </select>
                </div>

                <!-- İmar Durumu -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">İmar Durumu</label>
                    <select name="imar_statusu"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Herhangi</option>
                        <option>Konut İmarlı Arsa</option>
                        <option>Ticari İmarlı Arsa</option>
                        <option>Tarla / Bağ / Bahçe</option>
                        <option>Sanayi İmarlı Arsa</option>
                        <option>Turizm İmarlı Arsa</option>
                        <option>İmarlı Ticari + Konut</option>
                    </select>
                </div>

                <!-- Temizle Butonu -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-transparent">Temizle</label>
                    <button
                        class="w-full bg-gray-500 text-white p-3 rounded-lg hover:bg-gray-600 transition-colors font-medium"
                        onclick="clearAdvancedSearch()">
                        🗑️ Temizle
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if($showSort): ?>
        <!-- Sort Section -->
        <div class="mt-4 flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Sırala:</span>
                <select
                    class="p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="default">Varsayılan</option>
                    <option value="price_asc">Fiyat (Düşük → Yüksek)</option>
                    <option value="price_desc">Fiyat (Yüksek → Düşük)</option>
                    <option value="featured">Öne Çıkanlar</option>
                    <option value="date_asc">Tarih (Eski → Yeni)</option>
                    <option value="date_desc">Tarih (Yeni → Eski)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Görünüm:</span>
                <button
                    class="p-2 border border-gray-300 rounded-lg hover:bg-orange-500 hover:text-white transition-colors"
                    title="Grid">
                    ⊞
                </button>
                <button
                    class="p-2 border border-gray-300 rounded-lg hover:bg-orange-500 hover:text-white transition-colors"
                    title="Liste">
                    ☰
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Search functionality moved to search-optimizer.js -->
<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/components/yaliihan/search-form.blade.php ENDPATH**/ ?>