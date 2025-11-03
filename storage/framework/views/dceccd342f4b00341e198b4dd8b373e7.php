
<div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 hover:shadow-2xl transition-shadow duration-300">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 text-white shadow-lg shadow-purple-500/50 font-bold text-lg">
            4
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                İlan Özellikleri
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kategori ve yayın tipine özgü alanlar</p>
        </div>
    </div>

    <div id="field-dependencies-container" class="space-y-6">
        
        <div id="fields-empty-state"
            class="flex items-start gap-4 p-6 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-2 border-blue-200 dark:border-blue-800/30 rounded-xl">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-blue-900 dark:text-blue-100 font-bold text-lg mb-1">
                    Kategori ve Yayın Tipi Seçimi Gerekli
                </p>
                <p class="text-blue-700 dark:text-blue-300 text-sm">
                    Özellikleri görmek için yukarıdaki "Kategori Sistemi" bölümünden ana kategori, alt kategori ve yayın tipini seçin.
                </p>
            </div>
        </div>

        
        <div id="fields-content" class="space-y-6 hidden"></div>

        
        <div id="fields-loading" class="hidden">
            <div class="flex flex-col items-center justify-center py-12">
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-purple-200 dark:border-purple-900 rounded-full"></div>
                    <div class="w-16 h-16 border-4 border-purple-600 dark:border-purple-400 rounded-full border-t-transparent animate-spin absolute top-0"></div>
                </div>
                <p class="mt-4 text-gray-600 dark:text-gray-400 font-medium">Alanlar yükleniyor...</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">Lütfen bekleyin</p>
            </div>
        </div>

        
        <div id="fields-error" class="hidden">
            <div class="flex items-start gap-4 p-6 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-2 border-red-200 dark:border-red-800/30 rounded-xl">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 text-white shadow-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-red-900 dark:text-red-100 font-bold text-lg mb-1">Hata Oluştu</p>
                    <p class="text-red-700 dark:text-red-300 text-sm" id="fields-error-message">Bir hata oluştu</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    const FieldDependenciesManager = {
        selectedKategoriSlug: null,
        selectedYayinTipi: null,
        fieldCategories: [],
        elements: {},

        init() {
            console.log('🔧 FieldDependenciesManager.init()');

            // Cache DOM elements
            this.elements = {
                emptyState: document.getElementById('fields-empty-state'),
                content: document.getElementById('fields-content'),
                loading: document.getElementById('fields-loading'),
                error: document.getElementById('fields-error'),
                errorMessage: document.getElementById('fields-error-message')
            };

            // Listen for category-changed event
            window.addEventListener('category-changed', (e) => {
                console.log('🎯 Field Dependencies: Category changed', e.detail);
                
                if (!e.detail || !e.detail.category) {
                    this.reset();
                    return;
                }

                // Get kategori slug
                const kategori = e.detail.category;
                const yayinTipi = e.detail.yayinTipiId || e.detail.yayinTipi || null;
                
                // ✅ FIX: Kategori slug'ı al - eğer slug boşsa option'dan al
                let kategoriSlug = kategori.parent_slug || kategori.slug;
                
                // Fallback: Eğer slug hâlâ boşsa, ana kategori select'ten al
                if (!kategoriSlug) {
                    const anaKategoriSelect = document.getElementById('ana_kategori');
                    if (anaKategoriSelect && anaKategoriSelect.value) {
                        const selectedOption = anaKategoriSelect.options[anaKategoriSelect.selectedIndex];
                        kategoriSlug = selectedOption?.getAttribute('data-slug') || '';
                        console.log('🔧 Fallback: Kategori slug retrieved from DOM:', kategoriSlug);
                    }
                }
                
                this.selectedKategoriSlug = kategoriSlug;
                this.selectedYayinTipi = yayinTipi;

                console.log('📋 Loading fields:', {
                    kategoriSlug: this.selectedKategoriSlug,
                    yayinTipi: this.selectedYayinTipi
                });

                if (this.selectedKategoriSlug) {
                    this.loadFields();
                } else {
                    this.reset();
                }
            });

            console.log('✅ FieldDependenciesManager initialized');
        },

        async loadFields() {
            if (!this.selectedKategoriSlug) {
                console.warn('⚠️ Kategori slug bulunamadı');
                return;
            }

            this.showLoading();

            try {
                let url = `/api/admin/field-dependencies?kategori_slug=${encodeURIComponent(this.selectedKategoriSlug)}`;
                if (this.selectedYayinTipi) {
                    url += `&yayin_tipi=${encodeURIComponent(this.selectedYayinTipi)}`; // ID veya slug
                }
                
                console.log('🔍 Field dependencies URL:', url);

                console.log('🌐 API Request:', url);

                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const result = await response.json();
                console.log('✅ API Response:', result);

                if (result.success && result.data) {
                    this.fieldCategories = Array.isArray(result.data) ? result.data : [];
                    this.renderFields();
                } else {
                    throw new Error(result.message || 'Invalid response');
                }
            } catch (err) {
                console.error('❌ Field Dependencies error:', err);
                this.showError(`Alanlar yüklenemedi: ${err.message}`);
                
                if (window.toast) {
                    window.toast.error('Alanlar yüklenemedi');
                }
            }
        },

        renderFields() {
            if (!this.elements.content) return;

            this.elements.content.innerHTML = '';

            if (this.fieldCategories.length === 0) {
                this.reset();
                return;
            }

            this.fieldCategories.forEach(category => {
                const categoryEl = this.createCategoryElement(category);
                this.elements.content.appendChild(categoryEl);
            });

            this.elements.emptyState.classList.add('hidden');
            this.elements.loading.classList.add('hidden');
            this.elements.error.classList.add('hidden');
            this.elements.content.classList.remove('hidden');

            console.log('✅ Fields rendered:', this.fieldCategories.length, 'categories');
        },

        createCategoryElement(category) {
            const div = document.createElement('div');
            const colorScheme = this.getCategoryColorScheme(category.name);
            div.className = `bg-gradient-to-br ${colorScheme.bg} rounded-xl border-2 ${colorScheme.border} overflow-hidden`;

            // Accordion Header (Clickable)
            const header = document.createElement('div');
            header.className = 'flex items-center justify-between p-5 cursor-pointer hover:bg-white/50 dark:hover:bg-gray-800/50 transition-colors';
            header.onclick = (e) => {
                e.preventDefault();
                const content = header.nextElementSibling;
                const chevron = header.querySelector('svg');
                
                if (content && content.classList.contains('category-content')) {
                    if (content.style.display === 'none') {
                        content.style.display = 'block';
                        if (chevron) chevron.style.transform = 'rotate(180deg)';
                    } else {
                        content.style.display = 'none';
                        if (chevron) chevron.style.transform = 'rotate(0deg)';
                    }
                }
            };
            
            // Calculate filled fields
            const filledCount = this.getFilledFieldsCount(category.fields);
            const totalCount = category.fields.length;
            const fillPercentage = totalCount > 0 ? Math.round((filledCount / totalCount) * 100) : 0;
            
            // Icon elementi oluştur (Emoji - always works!)
            const iconWrapper = document.createElement('div');
            iconWrapper.className = `flex items-center justify-center w-10 h-10 rounded-xl ${colorScheme.iconBg} text-white shadow-lg text-2xl`;
            iconWrapper.textContent = category.icon || '⭐';
            
            const titleDiv = document.createElement('div');
            titleDiv.innerHTML = `
                <h4 class="text-lg font-bold text-gray-900 dark:text-white">${this.escape(category.name)}</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400">${totalCount} alan • ${filledCount} dolu</p>
            `;
            
            const leftSection = document.createElement('div');
            leftSection.className = 'flex items-center gap-3 flex-1';
            leftSection.appendChild(iconWrapper);
            leftSection.appendChild(titleDiv);
            
            const rightSection = document.createElement('div');
            rightSection.className = 'flex items-center gap-4';
            rightSection.innerHTML = `
                <div class="text-right">
                    <div class="text-sm font-bold ${fillPercentage > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400'}">${fillPercentage}%</div>
                    <div class="w-24 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full ${colorScheme.progress} transition-all duration-300" style="width: ${fillPercentage}%"></div>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-500 transform transition-transform category-chevron-${category.name}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            `;
            
            header.appendChild(leftSection);
            header.appendChild(rightSection);
            div.appendChild(header);

            // Accordion Content
            const content = document.createElement('div');
            content.className = 'category-content p-5 pt-0 transition-all duration-300';
            content.dataset.categoryName = category.category || category.name;
            
            // Default: Fiyatlandırma ve Fiziksel açık, diğerleri kapalı
            const defaultOpen = ['fiyatlandirma', 'fiziksel_ozellikler', '💰 fiyatlandirma', '📐 fiziksel'];
            const shouldOpen = defaultOpen.some(key => 
                (category.category && category.category.toLowerCase().includes(key.toLowerCase())) ||
                (category.name && category.name.toLowerCase().includes(key.toLowerCase()))
            );
            content.style.display = shouldOpen ? 'block' : 'none';

            const grid = document.createElement('div');
            grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4';

            if (category.fields && Array.isArray(category.fields)) {
                category.fields.forEach(field => {
                    const fieldEl = this.createFieldElement(field);
                    if (fieldEl) grid.appendChild(fieldEl);
                });
            }

            content.appendChild(grid);
            div.appendChild(content);
            return div;
        },

        getCategoryColorScheme(categoryName) {
            const schemes = {
                // YENİ KATEGORİLER (6 ana kategori)
                'fiyatlandirma': {
                    bg: 'from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20',
                    border: 'border-blue-300 dark:border-blue-600',
                    iconBg: 'bg-gradient-to-br from-blue-500 to-blue-600',
                    progress: 'bg-blue-500'
                },
                'fiziksel': {
                    bg: 'from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/20',
                    border: 'border-purple-300 dark:border-purple-600',
                    iconBg: 'bg-gradient-to-br from-purple-500 to-purple-600',
                    progress: 'bg-purple-500'
                },
                'donanim': {
                    bg: 'from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/20',
                    border: 'border-green-300 dark:border-green-600',
                    iconBg: 'bg-gradient-to-br from-green-500 to-green-600',
                    progress: 'bg-green-500'
                },
                'dismekan': {
                    bg: 'from-yellow-50 to-yellow-100 dark:from-yellow-900/30 dark:to-yellow-800/20',
                    border: 'border-yellow-300 dark:border-yellow-600',
                    iconBg: 'bg-gradient-to-br from-yellow-500 to-yellow-600',
                    progress: 'bg-yellow-500'
                },
                'yatak': {
                    bg: 'from-pink-50 to-pink-100 dark:from-pink-900/30 dark:to-pink-800/20',
                    border: 'border-pink-300 dark:border-pink-600',
                    iconBg: 'bg-gradient-to-br from-pink-500 to-pink-600',
                    progress: 'bg-pink-500'
                },
                'ek_hizmetler': {
                    bg: 'from-indigo-50 to-indigo-100 dark:from-indigo-900/30 dark:to-indigo-800/20',
                    border: 'border-indigo-300 dark:border-indigo-600',
                    iconBg: 'bg-gradient-to-br from-indigo-500 to-indigo-600',
                    progress: 'bg-indigo-500'
                },
                // ESKİ KATEGORİLER (backward compatibility)
                'fiyat': {
                    bg: 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20',
                    border: 'border-green-300 dark:border-green-700',
                    iconBg: 'bg-gradient-to-br from-green-500 to-emerald-600',
                    progress: 'bg-green-500'
                },
                'sezonluk': {
                    bg: 'from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20',
                    border: 'border-purple-300 dark:border-purple-700',
                    iconBg: 'bg-gradient-to-br from-purple-500 to-pink-600',
                    progress: 'bg-purple-500'
                },
                'ozellik': {
                    bg: 'from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20',
                    border: 'border-blue-300 dark:border-blue-700',
                    iconBg: 'bg-gradient-to-br from-blue-500 to-cyan-600',
                    progress: 'bg-blue-500'
                },
                'olanaklar': {
                    bg: 'from-orange-50 to-yellow-50 dark:from-orange-900/20 dark:to-yellow-900/20',
                    border: 'border-orange-300 dark:border-orange-700',
                    iconBg: 'bg-gradient-to-br from-orange-500 to-yellow-600',
                    progress: 'bg-orange-500'
                }
            };
            
            // Find matching scheme (substring match for flexibility)
            const normalizedName = categoryName.toLowerCase();
            for (const [key, scheme] of Object.entries(schemes)) {
                if (normalizedName.includes(key)) return scheme;
            }
            
            // Default: lime/green
            return {
                bg: 'from-lime-50 to-green-50 dark:from-lime-900/20 dark:to-green-900/20',
                border: 'border-lime-300 dark:border-lime-700',
                iconBg: 'bg-gradient-to-br from-lime-500 to-green-600',
                progress: 'bg-lime-500'
            };
        },

        getFilledFieldsCount(fields) {
            let count = 0;
            fields.forEach(field => {
                const input = document.getElementById(`field_${field.slug}`);
                if (input && input.value && input.value !== '') {
                    count++;
                }
            });
            return count;
        },

        toggleCategory(categoryName) {
            const content = document.querySelector(`.category-content-${categoryName}`);
            const chevron = document.querySelector(`.category-chevron-${categoryName}`);
            
            if (content) {
                const isHidden = content.style.display === 'none';
                content.style.display = isHidden ? 'block' : 'none';
                
                if (chevron) {
                    chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
                }
                
                // Save state
                localStorage.setItem(`category-${categoryName}-open`, isHidden ? 'true' : 'false');
            }
        },

        isCategoryOpen(categoryName) {
            // Priority categories default open
            const priorityCategories = ['fiyat', 'sezonluk', 'general'];
            const isPriority = priorityCategories.some(cat => categoryName.toLowerCase().includes(cat));
            
            // Check saved state
            const saved = localStorage.getItem(`category-${categoryName}-open`);
            if (saved !== null) return saved === 'true';
            
            // Default: priority open, others closed
            return isPriority;
        },

        createFieldElement(field) {
            const div = document.createElement('div');
            div.className = 'space-y-2 field-group';
            div.dataset.fieldId = field.id;
            div.dataset.fieldSlug = field.slug;

            const label = document.createElement('label');
            label.htmlFor = `field_${field.slug}`;
            label.className = 'block text-sm font-extrabold text-gray-900 dark:text-white mb-1';
            
            const labelText = document.createElement('span');
            labelText.textContent = field.name + (field.required ? ' *' : '');
            label.appendChild(labelText);

            div.appendChild(label);

            // Field type göre input oluştur
            let input;
            switch (field.type) {
                case 'boolean':
                case 'checkbox':
                    input = this.createCheckbox(field);
                    break;
                case 'number':
                    input = this.createNumber(field);
                    break;
                case 'select':
                    input = this.createSelect(field);
                    break;
                case 'textarea':
                    input = this.createTextarea(field);
                    break;
                case 'date':
                    input = this.createDate(field);
                    break;
                default:
                    input = this.createText(field);
            }

            div.appendChild(input);

            // Help text
            if (field.help_text) {
                const help = document.createElement('small');
                help.className = 'text-xs text-gray-500 dark:text-gray-400';
                help.textContent = field.help_text;
                div.appendChild(help);
            }

            return div;
        },

        createCheckbox(field) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex items-center';

            const input = document.createElement('input');
            input.type = 'checkbox';
            input.name = `field_${field.slug}`;
            input.id = `field_${field.slug}`;
            input.value = '1';
            input.className = 'mr-2 rounded focus:ring-lime-500 text-lime-600';
            if (field.required) input.required = true;

            const label = document.createElement('label');
            label.htmlFor = `field_${field.slug}`;
            label.className = 'text-sm text-gray-900 dark:text-white';
            label.textContent = 'Evet';

            wrapper.appendChild(input);
            wrapper.appendChild(label);
            return wrapper;
        },

        createNumber(field) {
            const input = document.createElement('input');
            input.type = 'number';
            input.name = `field_${field.slug}`;
            input.id = `field_${field.slug}`;
            input.placeholder = field.placeholder || field.name;
            input.className = 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-black dark:text-white font-semibold rounded-lg focus:ring-2 focus:ring-lime-500 placeholder-gray-600 dark:placeholder-gray-500';
            if (field.required) input.required = true;
            if (field.unit) input.setAttribute('data-unit', field.unit);
            return input;
        },

        createSelect(field) {
            const select = document.createElement('select');
            select.name = `field_${field.slug}`;
            select.id = `field_${field.slug}`;
            select.className = 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-black dark:text-white font-semibold rounded-lg focus:ring-2 focus:ring-lime-500';
            if (field.required) select.required = true;

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Seçiniz...';
            placeholder.className = 'text-gray-500 dark:text-gray-400';
            select.appendChild(placeholder);

            // Özel saatler için default options
            let options = field.options;
            if (!options && (field.slug === 'check_in' || field.slug === 'check_out')) {
                options = [
                    '08:00', '09:00', '10:00', '11:00', '12:00',
                    '13:00', '14:00', '15:00', '16:00', '17:00',
                    '18:00', '19:00', '20:00'
                ];
            }

            if (options && Array.isArray(options)) {
                options.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt;
                    option.textContent = opt;
                    select.appendChild(option);
                });
            }

            return select;
        },

        createTextarea(field) {
            const textarea = document.createElement('textarea');
            textarea.name = `field_${field.slug}`;
            textarea.id = `field_${field.slug}`;
            textarea.placeholder = field.placeholder || field.name;
            textarea.rows = 3;
            textarea.className = 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-black dark:text-white font-semibold rounded-lg focus:ring-2 focus:ring-lime-500 placeholder-gray-600 dark:placeholder-gray-500';
            if (field.required) textarea.required = true;
            return textarea;
        },

        createDate(field) {
            const input = document.createElement('input');
            input.type = 'date';
            input.name = `field_${field.slug}`;
            input.id = `field_${field.slug}`;
            input.className = 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-black dark:text-white font-medium rounded-lg focus:ring-2 focus:ring-lime-500';
            if (field.required) input.required = true;
            return input;
        },

        createText(field) {
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `field_${field.slug}`;
            input.id = `field_${field.slug}`;
            input.placeholder = field.placeholder || field.name;
            input.className = 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-black dark:text-white font-semibold rounded-lg focus:ring-2 focus:ring-lime-500 placeholder-gray-600 dark:placeholder-gray-500';
            if (field.required) input.required = true;
            return input;
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
            this.fieldCategories = [];
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
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => FieldDependenciesManager.init());
    } else {
        FieldDependenciesManager.init();
    }

    // Expose globally
    window.FieldDependenciesManager = FieldDependenciesManager;
    console.log('📦 FieldDependenciesManager exposed');
})();
</script>

<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ilanlar/components/field-dependencies-dynamic.blade.php ENDPATH**/ ?>