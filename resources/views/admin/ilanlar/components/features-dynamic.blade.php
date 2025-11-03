{{-- Section 10: Dinamik Özellikler (Context7 Uyumlu + AI Powered) --}}
<div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2 flex items-center">
                <span class="bg-lime-100 dark:bg-lime-900 text-lime-600 dark:text-lime-400 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">9</span>
                ✨ İlan Özellikleri
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                İlanınıza özel özellikler ekleyin. Kategori seçtikten sonra ilgili özellikler görünecek.
            </p>
        </div>

        {{-- 🤖 AI ile Tümünü Doldur Butonu --}}
        <button type="button" id="ai-suggest-all-features"
            class="hidden px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center space-x-2"
            onclick="window.FeaturesAI && window.FeaturesAI.suggestAll(window.FeaturesAI.getFormContext())">
            <i class="fas fa-magic"></i>
            <span class="font-medium">AI ile Tümünü Doldur</span>
        </button>
    </div>

    {{-- ✅ VANILLA JS RENDER - Alpine.js nested loops KALDIRILDI --}}
    <div id="features-container" class="space-y-6">
        {{-- Kategori Seçimi Uyarısı --}}
        <div id="features-empty-state"
            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-3"></i>
                <div>
                    <p class="text-blue-800 dark:text-blue-200 font-medium">
                        Kategori Seçimi Gerekli
                    </p>
                    <p class="text-blue-600 dark:text-blue-400 text-sm mt-1">
                        Özellikleri görmek için önce "Kategori Sistemi" bölümünden kategori seçin.
                    </p>
                </div>
            </div>
        </div>

        {{-- Dinamik Özellik Kategorileri (Vanilla JS ile render edilecek) --}}
        <div id="features-content" class="space-y-6 hidden"></div>

        {{-- Type-based Fields Container (categories.js için) --}}
        <div id="type-based-fields-container" class="space-y-4 hidden"></div>

        {{-- Loading State --}}
        <div id="features-loading" class="text-center py-8 hidden">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
            <p class="text-gray-500 dark:text-gray-400">Özellikler yükleniyor...</p>
        </div>

        {{-- Error State --}}
        <div id="features-error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 hidden">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 mr-3"></i>
                <p class="text-red-800 dark:text-red-200" id="features-error-message"></p>
            </div>
        </div>
    </div>
</div>

<script>
    // ✅ VANILLA JS FEATURES MANAGER - NO ALPINE.JS DEPENDENCY
    (function() {
        'use strict';

        const FeaturesManager = {
            selectedCategory: null,
            featureCategories: [],
            elements: {},

            init() {
                console.log('🔧 FeaturesManager.init() called');

                // Cache DOM elements
                this.elements = {
                    emptyState: document.getElementById('features-empty-state'),
                    content: document.getElementById('features-content'),
                    loading: document.getElementById('features-loading'),
                    error: document.getElementById('features-error'),
                    errorMessage: document.getElementById('features-error-message')
                };

                console.log('🔧 FeaturesManager elements:', this.elements);

                window.addEventListener('category-changed', (e) => {
                    console.log('🎯 Features: Category changed event received', e.detail);
                    this.selectedCategory = e.detail.category;
                    const yayinTipi = e.detail.yayinTipi || null;

                    if (this.selectedCategory && this.selectedCategory.id) {
                        console.log('🎯 Loading features for category ID:', this.selectedCategory.id, 'yayinTipi:', yayinTipi);
                        this.loadFeatures(this.selectedCategory.id, yayinTipi);
                    } else {
                        console.log('🎯 No category ID, resetting');
                        this.reset();
                    }
                });

                console.log('✅ Vanilla JS Features Manager initialized');
            },

            async loadFeatures(categoryId, yayinTipi = null) {
                if (!categoryId) return;

                this.showLoading();

                try {
                    let url = `/api/admin/features?category_id=${categoryId}`;
                    if (yayinTipi) {
                        url += `&yayin_tipi=${encodeURIComponent(yayinTipi)}`;
                    }

                    const response = await fetch(url);
                    console.log('🎯 Features API Request URL:', url);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();
                    console.log('🎯 Features API Response:', data);

                    if (data.success && data.data) {
                        this.featureCategories = Array.isArray(data.data) ? data.data : [];
                        this.renderFeatures();
                    } else {
                        throw new Error(data.message || 'Invalid response');
                    }
                } catch (err) {
                    console.error('❌ Features error:', err);
                    this.showError(`Özellikler yüklenemedi: ${err.message}`);

                    if (window.toast) {
                        window.toast.error('Özellikler yüklenemedi');
                    }
                }
            },

            renderFeatures() {
                if (!this.elements.content) return;

                this.elements.content.innerHTML = '';

                if (this.featureCategories.length === 0) {
                    this.reset();
                    return;
                }

                this.featureCategories.forEach(category => {
                    const categoryEl = this.createCategoryElement(category);
                    this.elements.content.appendChild(categoryEl);
                });

                this.elements.emptyState.classList.add('hidden');
                this.elements.loading.classList.add('hidden');
                this.elements.error.classList.add('hidden');
                this.elements.content.classList.remove('hidden');

                // ✨ Show "AI ile Tümünü Doldur" button
                const aiSuggestAllBtn = document.getElementById('ai-suggest-all-features');
                if (aiSuggestAllBtn) {
                    aiSuggestAllBtn.classList.remove('hidden');
                }
            },

            createCategoryElement(category) {
                const div = document.createElement('div');
                div.className = 'bg-gradient-to-br from-lime-50 to-green-50 dark:from-lime-900/20 dark:to-green-900/20 rounded-lg p-6';

                const header = document.createElement('h4');
                header.className = 'text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center';
                header.innerHTML = `
                    <i class="${category.icon || 'fas fa-star'} mr-2 text-lime-600"></i>
                    <span>${this.escape(category.name)}</span>
                `;
                div.appendChild(header);

                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4';

                if (category.features && Array.isArray(category.features)) {
                    category.features.forEach(feature => {
                        const featureEl = this.createFeatureElement(feature);
                        if (featureEl) grid.appendChild(featureEl);
                    });
                }

                div.appendChild(grid);
                return div;
            },

            createFeatureElement(feature) {
                const div = document.createElement('div');
                div.className = 'space-y-2 feature-group';

                if (feature.type === 'boolean') {
                    div.innerHTML = `
                        <label class="flex items-center justify-between cursor-pointer group">
                            <div class="flex items-center">
                                <input type="checkbox"
                                    name="features[${this.escape(feature.slug)}]"
                                    value="${feature.id}"
                                    id="feature_${feature.id}"
                                    class="mr-3 rounded focus:ring-lime-500 text-lime-600">
                                <span class="text-sm text-gray-900 dark:text-white">${this.escape(feature.name)}</span>
                            </div>
                            <button type="button"
                                class="ai-suggest-btn opacity-0 group-hover:opacity-100 inline-flex items-center px-2 py-1 text-xs font-medium text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded transition-all"
                                onclick="window.FeaturesAI && window.FeaturesAI.suggestSingle('${this.escape(feature.slug)}', document.getElementById('feature_${feature.id}'))"
                                title="AI ile öner">
                                <i class="fas fa-magic"></i>
                            </button>
                        </label>
                    `;
                } else if (feature.type === 'number') {
                    div.innerHTML = `
                        <label for="feature_${feature.id}"
                            class="flex items-center justify-between text-sm font-medium text-gray-900 dark:text-white">
                            <span>${this.escape(feature.name)}</span>
                            <button type="button"
                                class="ai-suggest-btn inline-flex items-center px-2 py-1 text-xs font-medium text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded transition-all"
                                onclick="window.FeaturesAI && window.FeaturesAI.suggestSingle('${this.escape(feature.slug)}', document.getElementById('feature_${feature.id}'))"
                                title="AI ile öner">
                                <i class="fas fa-magic mr-1"></i><span class="hidden sm:inline">AI</span>
                            </button>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number"
                                name="features[${this.escape(feature.slug)}]"
                                id="feature_${feature.id}"
                                step="${feature.unit === 'm²' ? '0.01' : '1'}"
                                min="0"
                                class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-lime-500 transition-all">
                            ${feature.unit ? `<span class="text-sm text-gray-500 dark:text-gray-400">${this.escape(feature.unit)}</span>` : ''}
                        </div>
                    `;
                } else if (feature.type === 'select') {
                    const label = document.createElement('label');
                    label.htmlFor = `feature_${feature.id}`;
                    label.className = 'flex items-center justify-between text-sm font-medium text-gray-900 dark:text-white mb-1';

                    const labelText = document.createElement('span');
                    labelText.textContent = feature.name;

                    const aiBtn = document.createElement('button');
                    aiBtn.type = 'button';
                    aiBtn.className = 'ai-suggest-btn inline-flex items-center px-2 py-1 text-xs font-medium text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded transition-all';
                    aiBtn.title = 'AI ile öner';
                    aiBtn.innerHTML = '<i class="fas fa-magic mr-1"></i><span class="hidden sm:inline">AI</span>';
                    aiBtn.onclick = () => {
                        if (window.FeaturesAI) {
                            window.FeaturesAI.suggestSingle(feature.slug, document.getElementById(`feature_${feature.id}`));
                        }
                    };

                    label.appendChild(labelText);
                    label.appendChild(aiBtn);

                    const select = document.createElement('select');
                    select.name = `features[${feature.slug}]`;
                    select.id = `feature_${feature.id}`;
                    select.className = 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-lime-500 transition-all';

                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = 'Seçiniz...';
                    select.appendChild(placeholder);

                    if (feature.options && Array.isArray(feature.options)) {
                        feature.options.forEach(opt => {
                            const option = document.createElement('option');
                            option.value = opt;
                            option.textContent = opt;
                            select.appendChild(option);
                        });
                    }

                    div.appendChild(label);
                    div.appendChild(select);
                }

                return div;
            },

            showLoading() {
                if (this.elements.emptyState) this.elements.emptyState.classList.add('hidden');
                if (this.elements.content) this.elements.content.classList.add('hidden');
                if (this.elements.error) this.elements.error.classList.add('hidden');
                if (this.elements.loading) this.elements.loading.classList.remove('hidden');
            },

            showError(message) {
                if (this.elements.emptyState) this.elements.emptyState.classList.add('hidden');
                if (this.elements.content) this.elements.content.classList.add('hidden');
                if (this.elements.loading) this.elements.loading.classList.add('hidden');
                if (this.elements.error) this.elements.error.classList.remove('hidden');
                if (this.elements.errorMessage) this.elements.errorMessage.textContent = message;
            },

            reset() {
                this.featureCategories = [];
                if (this.elements.content) this.elements.content.innerHTML = '';
                if (this.elements.emptyState) this.elements.emptyState.classList.remove('hidden');
                if (this.elements.content) this.elements.content.classList.add('hidden');
                if (this.elements.loading) this.elements.loading.classList.add('hidden');
                if (this.elements.error) this.elements.error.classList.add('hidden');
            },

            escape(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }
        };

        // Initialize when DOM ready
        console.log('🚀 Features-dynamic blade script loaded, readyState:', document.readyState);

        if (document.readyState === 'loading') {
            console.log('⏳ Waiting for DOMContentLoaded...');
            document.addEventListener('DOMContentLoaded', () => {
                console.log('✅ DOMContentLoaded fired, initializing FeaturesManager...');
                FeaturesManager.init();
            });
        } else {
            console.log('✅ DOM already ready, initializing FeaturesManager immediately...');
            FeaturesManager.init();
        }

        // Expose globally
        window.FeaturesManager = FeaturesManager;
        console.log('📦 FeaturesManager exposed to window.FeaturesManager');
    })();
</script>

{{-- 🎨 AI Features Styling --}}
<style>
    /* AI Suggested Animation */
    @keyframes ai-pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(168, 85, 247, 0.4);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(168, 85, 247, 0);
        }
    }

    .ai-suggested {
        animation: ai-pulse 1s ease-in-out 2;
        border-color: #a855f7 !important;
    }

    /* AI Loading State */
    .ai-loading {
        position: relative;
        opacity: 0.6;
        pointer-events: none;
    }

    .ai-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 3px solid #f3f4f6;
        border-top-color: #a855f7;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    /* AI Button Hover Effects */
    .ai-suggest-btn {
        position: relative;
        overflow: hidden;
    }

    .ai-suggest-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(168, 85, 247, 0.1);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .ai-suggest-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    /* Feature Group Hover */
    .feature-group:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease;
    }
</style>
