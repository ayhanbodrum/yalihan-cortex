/**
 * Context7 Live Search System
 *
 * Bu dosya Context7 standartlarına uygun canlı arama sistemini yönetir.
 * Kişi, danışman ve site/apartman aramaları için birleşik arayüz sağlar.
 *
 * @version 2.0.0
 * @since 2025-10-05
 * @author Context7 System
 */

// Prevent multiple class declarations
if (typeof window.Context7LiveSearch === 'undefined') {
    window.Context7LiveSearch = class Context7LiveSearch {
        constructor(options = {}) {
            this.defaultOptions = {
                debounceDelay: 300,
                minQueryLength: 2,
                maxResults: 20,
                apiBaseUrl: '/api/live-search',
                animationDuration: 200,
                showSearchHints: true,
                enableKeyboardNavigation: true,
                context7Compliant: true,
            };

            this.options = { ...this.defaultOptions, ...options };
            this.searchCache = new Map();
            this.activeInstances = new Map();
            this.debounceTimers = new Map();

            this.initializeSystem();
        }

        /**
         * Sistem başlatma
         */
        initializeSystem() {
            this.setupGlobalEventListeners();
            this.initializeSearchComponents();
            console.log('🔍 Context7 Live Search System initialized');
        }

        /**
         * Global event listener'ları kur
         */
        setupGlobalEventListeners() {
            // ESC tuşu ile dropdown'ları kapat
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.hideAllDropdowns();
                }
            });

            // Sayfa dışına tıklama ile dropdown'ları kapat
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.context7-live-search')) {
                    this.hideAllDropdowns();
                }
            });
        }

        /**
         * Mevcut arama bileşenlerini başlat
         */
        initializeSearchComponents() {
            // Kişi arama bileşenleri
            document.querySelectorAll('[data-context7-search="kisiler"]').forEach((element) => {
                this.initializeSearchInstance(element, 'kisiler');
            });

            // Danışman arama bileşenleri
            document.querySelectorAll('[data-context7-search="danismanlar"]').forEach((element) => {
                this.initializeSearchInstance(element, 'danismanlar');
            });

            // Site arama bileşenleri
            document.querySelectorAll('[data-context7-search="sites"]').forEach((element) => {
                this.initializeSearchInstance(element, 'sites');
            });

            // Birleşik arama bileşenleri
            document.querySelectorAll('[data-context7-search="unified"]').forEach((element) => {
                this.initializeSearchInstance(element, 'unified');
            });
        }

        /**
         * Arama instance'ı başlat
         */
        initializeSearchInstance(element, searchType) {
            const instanceId = this.generateInstanceId();
            const instance = {
                id: instanceId,
                element: element,
                searchType: searchType,
                isLoading: false,
                currentQuery: '',
                currentResults: [],
                selectedIndex: -1,
                dropdown: null,
                hiddenInput: null,
                selectedValue: null,
                config: this.extractConfig(element),
            };

            this.activeInstances.set(instanceId, instance);
            this.setupInstanceEventListeners(instance);
            this.createDropdown(instance);

            return instanceId;
        }

        /**
         * Instance event listener'ları kur
         */
        setupInstanceEventListeners(instance) {
            const input = instance.element;

            // Input event'leri
            input.addEventListener('input', (e) => {
                this.handleInput(instance, e.target.value);
            });

            input.addEventListener('keydown', (e) => {
                this.handleKeyDown(instance, e);
            });

            input.addEventListener('focus', () => {
                this.showDropdown(instance);
            });

            input.addEventListener('blur', (e) => {
                // Dropdown'a tıklama kontrolü için gecikme
                setTimeout(() => {
                    if (!e.relatedTarget || !e.relatedTarget.closest('.context7-search-dropdown')) {
                        this.hideDropdown(instance);
                    }
                }, 150);
            });
        }

        /**
         * Input değişikliği işle
         */
        handleInput(instance, query) {
            instance.currentQuery = query.trim();
            instance.selectedIndex = -1;

            if (instance.currentQuery.length < this.options.minQueryLength) {
                this.hideDropdown(instance);
                return;
            }

            this.debounceSearch(instance);
        }

        /**
         * Debounce ile arama yap
         */
        debounceSearch(instance) {
            clearTimeout(this.debounceTimers.get(instance.id));

            const timer = setTimeout(() => {
                this.performSearch(instance);
            }, this.options.debounceDelay);

            this.debounceTimers.set(instance.id, timer);
        }

        /**
         * Arama gerçekleştir
         */
        async performSearch(instance) {
            if (instance.isLoading) return;

            instance.isLoading = true;
            this.updateLoadingState(instance, true);

            try {
                const apiUrl = this.buildApiUrl(instance);
                const response = await fetch(apiUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    instance.currentResults = this.processResults(data, instance);
                    this.renderResults(instance);
                    this.showDropdown(instance);
                } else {
                    console.error('Context7 Live Search Error:', data.error);
                    this.hideDropdown(instance);
                }
            } catch (error) {
                console.error('Context7 Live Search Request Failed:', error);
                this.hideDropdown(instance);
            } finally {
                instance.isLoading = false;
                this.updateLoadingState(instance, false);
            }
        }

        /**
         * API URL oluştur
         */
        buildApiUrl(instance) {
            // ✅ Context7: Merkezi API config kullan (api-config.js)
            const endpointMap = window.APIConfig?.liveSearch || {
                kisiler: '/api/kisiler/search',
                danismanlar: '/api/users/search',
                sites: '/api/sites/search',
                unified: '/api/search/unified',
            };

            const baseUrl = endpointMap[instance.searchType] || '/api/kisiler/search';
            const params = new URLSearchParams({
                q: instance.currentQuery,
                limit: instance.config.maxResults || this.options.maxResults,
            });

            // Context7 uyumlu ek parametreler
            if (instance.config.filters) {
                Object.entries(instance.config.filters).forEach(([key, value]) => {
                    if (value !== null && value !== undefined) {
                        params.append(key, value);
                    }
                });
            }

            return `${baseUrl}?${params.toString()}`;
        }

        /**
         * Sonuçları işle
         */
        processResults(data, instance) {
            if (instance.searchType === 'unified') {
                return this.processUnifiedResults(data.results);
            } else {
                return data.data || [];
            }
        }

        /**
         * Birleşik arama sonuçlarını işle
         */
        processUnifiedResults(results) {
            const processedResults = [];

            Object.entries(results).forEach(([type, typeResults]) => {
                if (typeResults.data && typeResults.data.length > 0) {
                    typeResults.data.forEach((item) => {
                        processedResults.push({
                            ...item,
                            resultType: type,
                            displayText: this.getDisplayText(item, type),
                        });
                    });
                }
            });

            return processedResults;
        }

        /**
         * Görüntüleme metni al
         */
        getDisplayText(item, type) {
            switch (type) {
                case 'kisiler':
                    return item.display_text || item.tam_ad;
                case 'danismanlar':
                    return item.display_text || item.name;
                case 'sites':
                    return item.display_text || item.name;
                default:
                    return item.display_text || item.name || item.tam_ad;
            }
        }

        /**
         * Sonuçları render et
         */
        renderResults(instance) {
            const dropdown = instance.dropdown;
            const resultsContainer = dropdown.querySelector('.results-container');

            if (!resultsContainer) return;

            if (instance.currentResults.length === 0) {
                resultsContainer.innerHTML = this.createNoResultsHTML(instance);
                return;
            }

            let html = instance.currentResults
                .map((result, index) => {
                    return this.createResultItemHTML(result, index, instance);
                })
                .join('');

            // Site arama için "Yeni Site Ekle" butonu ekle
            if (instance.searchType === 'sites') {
                html += this.createAddSiteButtonHTML();
            }

            resultsContainer.innerHTML = html;
        }

        /**
         * Sonuç öğesi HTML'i oluştur
         */
        createResultItemHTML(result, index, instance) {
            const isSelected = index === instance.selectedIndex;
            const selectedClass = isSelected ? 'selected' : '';

            let resultTypeBadge = '';
            if (instance.searchType === 'unified' && result.resultType) {
                const typeLabels = {
                    kisiler: '👤 Kişi',
                    danismanlar: '👨‍💼 Danışman',
                    sites: '🏢 Site',
                };
                resultTypeBadge = `<span class="result-type-badge">${
                    typeLabels[result.resultType] || result.resultType
                }</span>`;
            }

            let searchHint = '';
            if (this.options.showSearchHints && result.search_hint) {
                searchHint = `<div class="search-hint">${result.search_hint}</div>`;
            }

            return `
            <div class="result-item ${selectedClass}" data-index="${index}" data-value='${JSON.stringify(
                result
            )}'>
                <div class="result-content">
                    <div class="result-main">
                        ${resultTypeBadge}
                        <span class="result-text">${this.getDisplayText(
                            result,
                            result.resultType
                        )}</span>
                    </div>
                    ${searchHint}
                </div>
                <div class="result-actions">
                    <button type="button" class="select-btn" title="Seç">✓</button>
                </div>
            </div>
        `;
        }

        /**
         * Sonuç bulunamadı HTML'i oluştur
         */
        createNoResultsHTML(instance) {
            if (instance.searchType === 'sites') {
                return `
                <div class="no-results">
                    <div class="no-results-icon">🏢</div>
                    <div class="no-results-text">Site bulunamadı</div>
                    <div class="no-results-hint">Farklı anahtar kelimeler deneyin veya yeni site ekleyin</div>
                    <button type="button" class="add-new-btn" data-action="add-site">
                        <span class="add-icon">+</span>
                        Yeni Site Ekle
                    </button>
                </div>
            `;
            } else {
                return `
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <div class="no-results-text">Sonuç bulunamadı</div>
                    <div class="no-results-hint">Farklı anahtar kelimeler deneyin</div>
                </div>
            `;
            }
        }

        /**
         * "Yeni Site Ekle" butonu HTML'i oluştur
         */
        createAddSiteButtonHTML() {
            return `
            <div class="result-item add-new-item" data-action="add-site">
                <div class="result-content">
                    <div class="result-main">
                        <span class="add-icon">+</span>
                        <span class="result-text">Yeni Site Ekle</span>
                    </div>
                    <div class="search-hint">Aradığınız site bulunamadı mı? Yeni site ekleyin</div>
                </div>
            </div>
        `;
        }

        /**
         * Dropdown oluştur
         */
        createDropdown(instance) {
            const dropdown = document.createElement('div');
            dropdown.className = 'context7-search-dropdown';
            dropdown.innerHTML = `
            <div class="dropdown-header">
                <span class="search-type-label">${this.getSearchTypeLabel(
                    instance.searchType
                )}</span>
                <span class="results-count"></span>
            </div>
            <div class="results-container"></div>
            <div class="dropdown-footer">
                <div class="search-tips">
                    <span class="tip">↑↓ Navigate</span>
                    <span class="tip">Enter Select</span>
                    <span class="tip">Esc Close</span>
                </div>
            </div>
        `;

            // Dropdown'ı context7-live-search container'ının içine yerleştir
            const container = instance.element.closest('.context7-live-search');
            if (container) {
                container.appendChild(dropdown);
            } else {
                instance.element.parentNode.appendChild(dropdown);
            }
            instance.dropdown = dropdown;

            // Dropdown event listener'ları
            dropdown.addEventListener('click', (e) => {
                const resultItem = e.target.closest('.result-item');
                if (resultItem) {
                    // "Yeni Site Ekle" butonu kontrolü
                    if (resultItem.dataset.action === 'add-site') {
                        e.preventDefault();
                        this.showAddSiteModal(element);
                        return;
                    }

                    const index = parseInt(resultItem.dataset.index);
                    this.selectResult(instance, index);
                }

                // "Yeni Site Ekle" butonu kontrolü (no-results içinde)
                const addBtn = e.target.closest('.add-new-btn');
                if (addBtn && addBtn.dataset.action === 'add-site') {
                    e.preventDefault();
                    this.showAddSiteModal(container);
                }
            });

            // Hidden input oluştur
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = instance.config.hiddenInputName || instance.element.name + '_id';
            instance.element.parentNode.appendChild(hiddenInput);
            instance.hiddenInput = hiddenInput;
        }

        /**
         * Arama tipi etiketi al
         */
        getSearchTypeLabel(searchType) {
            const labels = {
                kisiler: '👤 Kişi Arama',
                danismanlar: '👨‍💼 Danışman Arama',
                sites: '🏢 Site/Apartman Arama',
                unified: '🔍 Birleşik Arama',
            };
            return labels[searchType] || 'Arama';
        }

        /**
         * Klavye olaylarını işle
         */
        handleKeyDown(instance, event) {
            if (!this.options.enableKeyboardNavigation) return;

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    this.navigateResults(instance, 1);
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    this.navigateResults(instance, -1);
                    break;
                case 'Enter':
                    event.preventDefault();
                    if (instance.selectedIndex >= 0) {
                        this.selectResult(instance, instance.selectedIndex);
                    }
                    break;
                case 'Escape':
                    this.hideDropdown(instance);
                    break;
            }
        }

        /**
         * Sonuçlar arasında gezin
         */
        navigateResults(instance, direction) {
            const maxIndex = instance.currentResults.length - 1;
            instance.selectedIndex += direction;

            if (instance.selectedIndex < 0) {
                instance.selectedIndex = maxIndex;
            } else if (instance.selectedIndex > maxIndex) {
                instance.selectedIndex = -1;
            }

            this.updateSelection(instance);
        }

        /**
         * Seçimi güncelle
         */
        updateSelection(instance) {
            const items = instance.dropdown.querySelectorAll('.result-item');

            items.forEach((item, index) => {
                item.classList.toggle('selected', index === instance.selectedIndex);
            });
        }

        /**
         * Sonucu seç
         */
        selectResult(instance, index) {
            const result = instance.currentResults[index];
            if (!result) return;

            // Input değerini güncelle
            instance.element.value = this.getDisplayText(result, result.resultType);

            // Hidden input değerini güncelle
            if (instance.hiddenInput) {
                instance.hiddenInput.value = result.id;
            }

            // Seçilen değeri sakla
            instance.selectedValue = result;

            // Dropdown'ı gizle
            this.hideDropdown(instance);

            // Custom event tetikle
            this.triggerSelectionEvent(instance, result);
        }

        /**
         * Seçim event'i tetikle
         */
        triggerSelectionEvent(instance, result) {
            const event = new CustomEvent('context7:search:selected', {
                detail: {
                    instance: instance,
                    result: result,
                    searchType: instance.searchType,
                },
            });

            instance.element.dispatchEvent(event);
        }

        /**
         * Dropdown'ı göster
         */
        showDropdown(instance) {
            if (!instance.dropdown || instance.currentResults.length === 0) return;

            instance.dropdown.classList.add('active');

            // Dropdown pozisyonunu ayarla
            this.positionDropdown(instance);
        }

        /**
         * Dropdown'ı gizle
         */
        hideDropdown(instance) {
            if (instance.dropdown) {
                instance.dropdown.classList.remove('active');
            }
            instance.selectedIndex = -1;
        }

        /**
         * Tüm dropdown'ları gizle
         */
        hideAllDropdowns() {
            this.activeInstances.forEach((instance) => {
                this.hideDropdown(instance);
            });
        }

        /**
         * Dropdown pozisyonunu ayarla
         */
        positionDropdown(instance) {
            const input = instance.element;
            const dropdown = instance.dropdown;
            const container = instance.element.closest('.context7-live-search');

            if (!container) return;

            const inputRect = input.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();
            const viewportHeight = window.innerHeight;
            const dropdownHeight = 300; // Tahmini yükseklik

            // Dropdown'ı container'a göre konumlandır
            dropdown.style.position = 'absolute';
            dropdown.style.top = '100%'; // Container'ın altında
            dropdown.style.left = '0';
            dropdown.style.right = '0';
            dropdown.style.width = '100%';

            // Viewport sınırlarını kontrol et
            const spaceBelow = viewportHeight - inputRect.bottom;
            const spaceAbove = inputRect.top;

            if (spaceBelow < dropdownHeight && spaceAbove > dropdownHeight) {
                // Dropdown'ı yukarıda göster
                dropdown.style.top = 'auto';
                dropdown.style.bottom = '100%';
            }
        }

        /**
         * Yükleme durumunu güncelle
         */
        updateLoadingState(instance, isLoading) {
            const input = instance.element;

            if (isLoading) {
                input.classList.add('loading');
                input.setAttribute('data-loading', 'true');
            } else {
                input.classList.remove('loading');
                input.removeAttribute('data-loading');
            }
        }

        /**
         * Instance ID oluştur
         */
        generateInstanceId() {
            return 'context7-search-' + Math.random().toString(36).substr(2, 9);
        }

        /**
         * Element'ten config çıkar
         */
        extractConfig(element) {
            const config = {};

            // Data attribute'leri oku
            Object.keys(element.dataset).forEach((key) => {
                if (key.startsWith('context7')) {
                    const configKey = key.replace('context7', '').toLowerCase();
                    config[configKey] = element.dataset[key];
                }
            });

            return config;
        }

        /**
         * Yeni arama instance'ı ekle
         */
        addSearchInstance(element, searchType, config = {}) {
            element.setAttribute('data-context7-search', searchType);

            // Config'i data attribute'lara yaz
            Object.entries(config).forEach(([key, value]) => {
                element.dataset[`context7${key.charAt(0).toUpperCase() + key.slice(1)}`] = value;
            });

            return this.initializeSearchInstance(element, searchType);
        }

        /**
         * Instance'ı kaldır
         */
        removeSearchInstance(instanceId) {
            const instance = this.activeInstances.get(instanceId);
            if (instance) {
                if (instance.dropdown) {
                    instance.dropdown.remove();
                }
                if (instance.hiddenInput) {
                    instance.hiddenInput.remove();
                }
                this.activeInstances.delete(instanceId);
            }
        }

        /**
         * Sistem durumunu al
         */
        getSystemStatus() {
            return {
                activeInstances: this.activeInstances.size,
                searchCache: this.searchCache.size,
                options: this.options,
                context7Compliant: true,
            };
        }

        /**
         * "Yeni Site Ekle" modal'ını göster
         */
        showAddSiteModal(container) {
            // Modal HTML'i oluştur
            const modalHTML = `
            <div id="addSiteModal" class="context7-modal">
                <div class="context7-modal-overlay"></div>
                <div class="context7-modal-content">
                    <div class="context7-modal-header">
                        <h3>Yeni Site Ekle</h3>
                        <button type="button" class="context7-modal-close">&times;</button>
                    </div>
                    <div class="context7-modal-body">
                        <form id="addSiteForm">
                            <div class="context7-form-group">
                                <label for="siteName">Site Adı *</label>
                                <input type="text" id="siteName" name="name" required
                                       placeholder="Örn: Bahçeşehir Sitesi" class="context7-input">
                            </div>
                            <div class="context7-form-group">
                                <label for="siteAddress">Adres</label>
                                <input type="text" id="siteAddress" name="address"
                                       placeholder="Örn: Bahçeşehir Mahallesi, Başakşehir/İstanbul" class="context7-input">
                            </div>
                            <div class="context7-form-group">
                                <label for="siteDescription">Açıklama</label>
                                <textarea id="siteDescription" name="description"
                                          placeholder="Site hakkında kısa açıklama..." class="context7-textarea"></textarea>
                            </div>
                            <div class="context7-form-group">
                                <label for="siteIl">İl</label>
                                <select id="siteIl" name="il_id" class="context7-select">
                                    <option value="">İl Seçin</option>
                                </select>
                            </div>
                            <div class="context7-form-group">
                                <label for="siteIlce">İlçe</label>
                                <select id="siteIlce" name="ilce_id" class="context7-select">
                                    <option value="">İlçe Seçin</option>
                                </select>
                            </div>
                            <div class="context7-form-group">
                                <label for="siteMahalle">Mahalle</label>
                                <select id="siteMahalle" name="mahalle_id" class="context7-select">
                                    <option value="">Mahalle Seçin</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="context7-modal-footer">
                        <button type="button" class="context7-btn context7-neo-btn neo-btn-secondary" onclick="this.closest('.context7-modal').remove()">
                            İptal
                        </button>
                        <button type="button" class="context7-btn context7-neo-btn neo-btn-primary" onclick="window.Context7LiveSearch.createSite()">
                            Site Ekle
                        </button>
                    </div>
                </div>
            </div>
        `;

            // Modal'ı DOM'a ekle
            document.body.insertAdjacentHTML('beforeend', modalHTML);

            // Modal event listener'ları
            const modal = document.getElementById('addSiteModal');
            const closeBtn = modal.querySelector('.context7-modal-close');
            const overlay = modal.querySelector('.context7-modal-overlay');

            closeBtn.addEventListener('click', () => modal.remove());
            overlay.addEventListener('click', () => modal.remove());

            // İlleri yükle
            this.loadIller();

            // İl değişikliği
            document.getElementById('siteIl').addEventListener('change', (e) => {
                this.loadIlceler(e.target.value);
            });

            // İlçe değişikliği
            document.getElementById('siteIlce').addEventListener('change', (e) => {
                this.loadMahalleler(e.target.value);
            });

            // Modal'ı göster
            modal.style.display = 'flex';
        }

        /**
         * İlleri yükle
         */
        async loadIller() {
            try {
                const response = await fetch('/api/location/provinces');
                const data = await response.json();

                const select = document.getElementById('siteIl');
                select.innerHTML = '<option value="">İl Seçin</option>';

                data.forEach((il) => {
                    const option = document.createElement('option');
                    option.value = il.id;
                    option.textContent = il.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('İller yüklenirken hata:', error);
            }
        }

        /**
         * İlçeleri yükle
         */
        async loadIlceler(ilId) {
            if (!ilId) {
                document.getElementById('siteIlce').innerHTML =
                    '<option value="">İlçe Seçin</option>';
                return;
            }

            try {
                const response = await fetch(`/api/location/districts-by-province/${ilId}`);
                const data = await response.json();

                const select = document.getElementById('siteIlce');
                select.innerHTML = '<option value="">İlçe Seçin</option>';

                data.forEach((ilce) => {
                    const option = document.createElement('option');
                    option.value = ilce.id;
                    option.textContent = ilce.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('İlçeler yüklenirken hata:', error);
            }
        }

        /**
         * Mahalleleri yükle
         */
        async loadMahalleler(ilceId) {
            if (!ilceId) {
                document.getElementById('siteMahalle').innerHTML =
                    '<option value="">Mahalle Seçin</option>';
                return;
            }

            try {
                const response = await fetch(`/api/location/neighborhoods-by-district/${ilceId}`);
                const data = await response.json();

                const select = document.getElementById('siteMahalle');
                select.innerHTML = '<option value="">Mahalle Seçin</option>';

                data.forEach((mahalle) => {
                    const option = document.createElement('option');
                    option.value = mahalle.id;
                    option.textContent = mahalle.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Mahalleler yüklenirken hata:', error);
            }
        }

        /**
         * Yeni site oluştur
         * PHASE 2.1: AJAX + Toast modernization
         */
        async createSite() {
            const form = document.getElementById('addSiteForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Boş değerleri temizle
            Object.keys(data).forEach((key) => {
                if (data[key] === '') {
                    delete data[key];
                }
            });

            // Validation
            if (!data.name || !data.il_id || !data.ilce_id) {
                window.toast?.error('Lütfen zorunlu alanları doldurun') ||
                    this.showNotification('Lütfen zorunlu alanları doldurun', 'error');
                return;
            }

            try {
                // PHASE 2.1: AjaxHelper kullan (eğer varsa)
                const result = window.AjaxHelper
                    ? await window.AjaxHelper.post('/api/admin/sites/create', data)
                    : await this.legacyAjaxPost('/api/sites/create', data);

                if (result.success) {
                    // PHASE 2.1: Toast notification (modern!)
                    if (window.toast) {
                        window.toast.success('Site başarıyla eklendi!');
                    } else {
                        this.showNotification('Site başarıyla eklendi!', 'success');
                    }

                    // Modal'ı kapat
                    document.getElementById('addSiteModal')?.remove();

                    // Arama alanını güncelle
                    this.updateSearchWithNewSite(result.data);

                    // PHASE 2.1: Smooth scroll + highlight (eğer UIHelpers varsa)
                    if (window.smoothScroll && result.data.id) {
                        setTimeout(() => {
                            window.smoothScroll(`site-${result.data.id}`);
                        }, 100);
                    }
                } else {
                    window.toast?.error(result.message) ||
                        this.showNotification('Site eklenirken hata: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Site oluşturma hatası:', error);
                window.toast?.error('Site eklenirken hata oluştu') ||
                    this.showNotification('Site eklenirken hata oluştu', 'error');
            }
        }

        /**
         * Legacy AJAX post (fallback)
         */
        async legacyAjaxPost(url, data) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content'),
                },
                body: JSON.stringify(data),
            });
            return await response.json();
        }

        /**
         * Arama alanını yeni site ile güncelle
         */
        updateSearchWithNewSite(siteData) {
            // Tüm arama instance'larını bul ve güncelle
            this.activeInstances.forEach((instance, instanceId) => {
                if (instance.searchType === 'sites') {
                    // Input değerini güncelle
                    instance.element.value = siteData.display;

                    // Hidden input'u güncelle
                    const hiddenInput = document.querySelector(
                        `[name="${instance.config.hiddenInputName}"]`
                    );
                    if (hiddenInput) {
                        hiddenInput.value = siteData.id;
                    }

                    // Dropdown'ı kapat
                    this.hideDropdown(instance);
                }
            });
        }

        /**
         * Bildirim göster
         */
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `context7-notification context7-notification-${type}`;
            notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button type="button" class="notification-close">&times;</button>
            </div>
        `;

            document.body.appendChild(notification);

            // Otomatik kapatma
            setTimeout(() => {
                notification.remove();
            }, 5000);

            // Manuel kapatma
            notification.querySelector('.notification-close').addEventListener('click', () => {
                notification.remove();
            });
        }
    };

    // Global instance oluştur
    window.context7LiveSearchInstance = new window.Context7LiveSearch();
}

// Otomatik başlatma
document.addEventListener('DOMContentLoaded', () => {
    console.log('🔍 Context7 Live Search System ready');
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.Context7LiveSearch;
}
