// ilan-create-categories.js - Category management functionality

// Context7 uyumlu - Helper functions (core.js'den import edilecek)
function showLoading(message) {
    window.toast?.info(message, 2000);
}

function hideLoading() {
    // Toast otomatik kapanır
}

function showNotification(message, type = 'info') {
    if (window.toast) {
        switch (type) {
            case 'success':
                window.toast.success(message);
                break;
            case 'error':
                window.toast.error(message);
                break;
            case 'warning':
                window.toast.warning(message);
                break;
            default:
                window.toast.info(message);
        }
    } else {
        console.log(`${type.toUpperCase()}: ${message}`);
    }
}

function loadAltKategoriler(anaKategoriId) {
    if (!anaKategoriId) {
        clearAltKategoriler();
        return;
    }

    showLoading('Alt kategoriler yükleniyor...');

    // Context7: /api/categories/sub/{id} endpoint'i routes/api.php'de mevcut
    fetch(`/api/categories/sub/${anaKategoriId}`, {
        cache: 'no-cache',
        headers: { 'Cache-Control': 'no-cache' },
    })
        .then((response) => response.json())
        .then((data) => {
            hideLoading();
            if (data.success) {
                // API response'da 'subcategories' key'i var
                populateAltKategoriler(data.subcategories || data.kategoriler || []);
            } else {
                showNotification('Alt kategoriler yüklenemedi', 'error');
            }
        })
        .catch((error) => {
            hideLoading();
            console.error('Alt kategori yükleme hatası:', error);
            showNotification('Alt kategoriler yüklenemedi', 'error');
        });
}

function clearAltKategoriler() {
    const altKategoriSelect = document.getElementById('alt_kategori');
    const yayinTipiSelect = document.getElementById('yayin_tipi_id');

    altKategoriSelect.innerHTML = '<option value="">Önce ana kategori seçin...</option>';
    yayinTipiSelect.innerHTML = '<option value="">Önce alt kategori seçin...</option>';

    // Clear type-based fields
    clearTypeBasedFields();
}

function populateAltKategoriler(categories) {
    const altKategoriSelect = document.getElementById('alt_kategori');
    const yayinTipiSelect = document.getElementById('yayin_tipi_id');

    altKategoriSelect.innerHTML = '<option value="">Alt kategori seçin...</option>';
    yayinTipiSelect.innerHTML = '<option value="">Önce alt kategori seçin...</option>';

    categories.forEach((category) => {
        const option = document.createElement('option');
        option.value = category.id;
        option.textContent = category.name;
        altKategoriSelect.appendChild(option);
    });
}

function loadYayinTipleri(altKategoriId) {
    if (!altKategoriId) {
        clearYayinTipleri();
        return;
    }

    showLoading('Yayın tipleri yükleniyor...');

    // ✅ FIX: Alt kategori ID'sini kullan (pivot tablo filtresi için!)
    const anaKategoriSelect = document.getElementById('ana_kategori');
    const anaKategoriSlug =
        anaKategoriSelect?.options[anaKategoriSelect.selectedIndex]?.dataset?.slug;

    // Context7: Alt kategori ID'sine göre filtrelenmiş yayın tiplerini yükle
    fetch(`/api/categories/publication-types/${altKategoriId}`, {
        cache: 'no-cache',
        headers: { 'Cache-Control': 'no-cache' },
    })
        .then((response) => response.json())
        .then((data) => {
            hideLoading();
            if (data.success) {
                const types = data.types || data.publication_types || data.yayinTipleri || [];
                console.log('✅ Yayın tipleri yüklendi:', types.length, 'adet', types);
                populateYayinTipleri(types);

                // ⚠️ Event'i henüz dispatch etme - Kullanıcı yayın tipi seçince dispatch edilecek
                console.log('⏳ Yayın tipi yüklendi, kullanıcı seçimi bekleniyor...');
            } else {
                showNotification('Yayın tipleri yüklenemedi', 'error');
            }
        })
        .catch((error) => {
            hideLoading();
            console.error('Yayın tipi yükleme hatası:', error);
            showNotification('Yayın tipleri yüklenemedi', 'error');
        });
}

function clearYayinTipleri() {
    const yayinTipiSelect = document.getElementById('yayin_tipi_id');
    yayinTipiSelect.innerHTML = '<option value="">Önce alt kategori seçin...</option>';

    clearTypeBasedFields();
}

function populateYayinTipleri(types) {
    console.log('📊 populateYayinTipleri called with:', types);
    const yayinTipiSelect = document.getElementById('yayin_tipi_id');

    if (!yayinTipiSelect) {
        console.error('❌ yayin_tipi_id element not found!');
        return;
    }

    yayinTipiSelect.innerHTML = '<option value="">Yayın tipi seçin...</option>';

    if (!types || types.length === 0) {
        console.warn('⚠️ No types provided to populateYayinTipleri');
        return;
    }

    types.forEach((type) => {
        const option = document.createElement('option');
        option.value = type.id;
        option.textContent = type.name;
        yayinTipiSelect.appendChild(option);
        console.log(`✅ Added option: ${type.name} (ID: ${type.id})`);
    });

    console.log(`✅ Total options added: ${types.length}`);
}

function loadTypeBasedFields() {
    const anaKategoriId = document.getElementById('ana_kategori').value;
    const altKategoriId = document.getElementById('alt_kategori').value;
    const yayinTipiId = document.getElementById('yayin_tipi_id').value;

    if (!anaKategoriId || !altKategoriId || !yayinTipiId) {
        clearTypeBasedFields();
        return;
    }

    showLoading('Özel alanlar yükleniyor...');

    fetch(`/api/categories/fields/${anaKategoriId}/${altKategoriId}/${yayinTipiId}`)
        .then((response) => response.json())
        .then((data) => {
            hideLoading();
            if (data.success) {
                renderTypeBasedFields(data.fields);
            } else {
                showNotification('Özel alanlar yüklenemedi', 'error');
            }
        })
        .catch((error) => {
            hideLoading();
            console.error('Özel alan yükleme hatası:', error);
            showNotification('Özel alanlar yüklenemedi', 'error');
        });
}

function clearTypeBasedFields() {
    const container = document.getElementById('type-based-fields-container');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                <p class="text-gray-500 dark:text-gray-400">Kategori seçimine göre alanlar yükleniyor...</p>
            </div>
        `;
    }
}

function renderTypeBasedFields(fields) {
    const container = document.getElementById('type-based-fields-container');

    // ✅ NULL CHECK - Element yoksa skip et (Property Type Manager sayfasında bu element yok, normal)
    if (!container) {
        return;
    }

    if (!fields || fields.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-info-circle text-2xl text-blue-400 mb-2"></i>
                <p class="text-gray-500 dark:text-gray-400">Bu kategori için özel alan bulunmuyor.</p>
            </div>
        `;
        return;
    }

    let html = '';

    fields.forEach((field) => {
        html += generateFieldHTML(field);
    });

    container.innerHTML = html;

    // Initialize field interactions
    initializeFieldInteractions();
}

function generateFieldHTML(field) {
    const fieldName = `type_fields[${field.id}]`;
    const fieldId = `field_${field.id}`;

    let html = '<div class="mb-4">';

    // Label
    html += `<label for="${fieldId}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${field.label}`;
    if (field.required) {
        html += ' <span class="text-red-500">*</span>';
    }
    html += '</label>';

    // Field input based on type
    switch (field.type) {
        case 'text':
            html += `<input type="text" id="${fieldId}" name="${fieldName}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"`;
            if (field.placeholder) html += ` placeholder="${field.placeholder}"`;
            if (field.required) html += ' required';
            html += '>';
            break;

        case 'number':
            html += `<input type="number" id="${fieldId}" name="${fieldName}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"`;
            if (field.placeholder) html += ` placeholder="${field.placeholder}"`;
            if (field.min !== undefined) html += ` min="${field.min}"`;
            if (field.max !== undefined) html += ` max="${field.max}"`;
            if (field.required) html += ' required';
            html += '>';
            break;

        case 'select':
            html += `<select id="${fieldId}" name="${fieldName}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"`;
            if (field.required) html += ' required';
            html += '>';
            html += '<option value="">Seçin...</option>';
            if (field.options) {
                field.options.forEach((option) => {
                    html += `<option value="${option.value}">${option.label}</option>`;
                });
            }
            html += '</select>';
            break;

        case 'checkbox':
            html += '<div class="space-y-2">';
            if (field.options) {
                field.options.forEach((option, index) => {
                    const checkboxName = `${fieldName}[]`;
                    const checkboxId = `${fieldId}_${index}`;
                    html += `
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="${checkboxId}" name="${checkboxName}" value="${option.value}" class="mr-3 rounded focus:ring-purple-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">${option.label}</span>
                        </label>
                    `;
                });
            }
            html += '</div>';
            break;

        case 'radio':
            html += '<div class="space-y-2">';
            if (field.options) {
                field.options.forEach((option, index) => {
                    const radioId = `${fieldId}_${index}`;
                    html += `
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" id="${radioId}" name="${fieldName}" value="${option.value}" class="mr-3 focus:ring-purple-500"`;
                    if (field.required) html += ' required';
                    html += `>
                            <span class="text-sm text-gray-700 dark:text-gray-300">${option.label}</span>
                        </label>
                    `;
                });
            }
            html += '</div>';
            break;

        case 'textarea':
            html += `<textarea id="${fieldId}" name="${fieldName}" rows="4" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"`;
            if (field.placeholder) html += ` placeholder="${field.placeholder}"`;
            if (field.required) html += ' required';
            html += '></textarea>';
            break;

        case 'date':
            html += `<input type="date" id="${fieldId}" name="${fieldName}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"`;
            if (field.required) html += ' required';
            html += '>';
            break;

        case 'datetime':
            html += `<input type="datetime-local" id="${fieldId}" name="${fieldName}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"`;
            if (field.required) html += ' required';
            html += '>';
            break;
    }

    // Help text
    if (field.help_text) {
        html += `<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${field.help_text}</p>`;
    }

    html += '</div>';

    return html;
}

function initializeFieldInteractions() {
    // Add any special interactions for dynamic fields
    const typeFields = document.querySelectorAll(
        '#type-based-fields-container input, #type-based-fields-container select, #type-based-fields-container textarea'
    );

    typeFields.forEach((field) => {
        // Add validation
        field.addEventListener('blur', function () {
            validateField(this);
        });

        // Add conditional logic if needed
        if (field.hasAttribute('data-conditional')) {
            initializeConditionalField(field);
        }
    });
}

function initializeConditionalField(field) {
    const condition = JSON.parse(field.getAttribute('data-conditional'));
    const targetField = document.querySelector(`[name="${condition.field}"]`);

    if (targetField) {
        targetField.addEventListener('change', () => {
            checkConditionalVisibility(field, condition);
        });

        // Initial check
        checkConditionalVisibility(field, condition);
    }
}

function checkConditionalVisibility(field, condition) {
    const targetField = document.querySelector(`[name="${condition.field}"]`);
    const targetValue = targetField ? targetField.value : '';
    const fieldContainer = field.closest('.mb-4');

    let shouldShow = false;

    switch (condition.operator) {
        case 'equals':
            shouldShow = targetValue === condition.value;
            break;
        case 'not_equals':
            shouldShow = targetValue !== condition.value;
            break;
        case 'contains':
            shouldShow = targetValue.includes(condition.value);
            break;
        case 'in':
            shouldShow = condition.value.includes(targetValue);
            break;
    }

    if (shouldShow) {
        fieldContainer.style.display = 'block';
        field.required = field.hasAttribute('data-original-required') || field.required;
    } else {
        fieldContainer.style.display = 'none';
        field.required = false;
    }
}

function validateCategories() {
    const anaKategori = document.getElementById('ana_kategori').value;
    const altKategori = document.getElementById('alt_kategori').value;
    const yayinTipi = document.getElementById('yayin_tipi_id').value;

    if (!anaKategori) {
        showFieldError(document.getElementById('ana_kategori'), 'Ana kategori seçimi zorunludur.');
        return false;
    }

    if (!altKategori) {
        showFieldError(document.getElementById('alt_kategori'), 'Alt kategori seçimi zorunludur.');
        return false;
    }

    if (!yayinTipi) {
        showFieldError(document.getElementById('yayin_tipi_id'), 'Yayın tipi seçimi zorunludur.');
        return false;
    }

    return true;
}

// Initialize category event listeners
let categoryListenersInitialized = false;

document.addEventListener('DOMContentLoaded', () => {
    // Prevent duplicate initialization
    if (categoryListenersInitialized) {
        console.log('⚠️ Category listeners already initialized, skipping...');
        return;
    }

    console.log('✅ Initializing category event listeners...');

    const anaKategoriSelect = document.getElementById('ana_kategori');
    const altKategoriSelect = document.getElementById('alt_kategori');
    const yayinTipiSelect = document.getElementById('yayin_tipi_id');

    if (anaKategoriSelect) {
        anaKategoriSelect.addEventListener('change', function () {
            console.log('🔵 Ana kategori change:', this.value);
            loadAltKategoriler(this.value);

            // ✅ FIX: Ana kategori seçilince category-changed event dispatch et
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const anaKategoriSlug = selectedOption?.getAttribute('data-slug') || '';
                console.log('🎯 Dispatching category-changed event (ana kategori):', {
                    kategoriId: this.value,
                    kategoriSlug: anaKategoriSlug,
                    selectedOption: selectedOption,
                });

                const event = new CustomEvent('category-changed', {
                    detail: {
                        category: {
                            id: this.value,
                            slug: anaKategoriSlug,
                            parent_slug: anaKategoriSlug,
                        },
                        yayinTipi: null,
                        yayinTipiId: null,
                    },
                });
                window.dispatchEvent(event);
            }
        });
        console.log('✅ Ana kategori listener added');
    }

    if (altKategoriSelect) {
        altKategoriSelect.addEventListener('change', function () {
            console.log('🔵 Alt kategori change:', this.value);
            loadYayinTipleri(this.value);

            // ✅ FIX: Alt kategori seçilince category-changed event dispatch et
            if (this.value && anaKategoriSelect?.value) {
                const selectedAnaOption =
                    anaKategoriSelect.options[anaKategoriSelect.selectedIndex];
                const anaKategoriSlug = selectedAnaOption?.getAttribute('data-slug') || '';
                console.log('🎯 Dispatching category-changed event (alt kategori):', {
                    kategoriId: anaKategoriSelect.value,
                    kategoriSlug: anaKategoriSlug,
                    altKategoriId: this.value,
                });

                const event = new CustomEvent('category-changed', {
                    detail: {
                        category: {
                            id: anaKategoriSelect.value,
                            slug: anaKategoriSlug,
                            parent_slug: anaKategoriSlug,
                        },
                        altKategoriId: this.value,
                        yayinTipi: null,
                        yayinTipiId: null,
                    },
                });
                window.dispatchEvent(event);
            }
        });
        console.log('✅ Alt kategori listener added');
    }

    if (yayinTipiSelect) {
        yayinTipiSelect.addEventListener('change', function () {
            console.log('🔵 Yayın tipi change:', this.value);

            // ✅ FIX: Yayın tipi seçildiğinde category-changed event'i dispatch et
            const anaKategoriSelect = document.getElementById('ana_kategori');
            const anaKategoriId = anaKategoriSelect?.value;
            const anaKategoriSlug =
                anaKategoriSelect?.options[anaKategoriSelect.selectedIndex]?.dataset?.slug;

            const yayinTipiText = this.options[this.selectedIndex]?.text;
            const yayinTipiId = this.value;

            if (anaKategoriId && yayinTipiId) {
                console.log('🎯 Dispatching category-changed event:', {
                    kategoriId: anaKategoriId,
                    kategoriSlug: anaKategoriSlug,
                    yayinTipi: yayinTipiText,
                    yayinTipiId: yayinTipiId,
                });

                const event = new CustomEvent('category-changed', {
                    detail: {
                        category: {
                            id: anaKategoriId,
                            slug: anaKategoriSlug,
                            parent_slug: anaKategoriSlug,
                        },
                        yayinTipi: yayinTipiText,
                        yayinTipiId: yayinTipiId,
                    },
                });
                window.dispatchEvent(event);
            }

            loadTypeBasedFields();
        });
        console.log('✅ Yayın tipi listener added');
    }

    categoryListenersInitialized = true;
    console.log('✅ Category listeners initialization complete');

    // 🆕 Auto-dispatch on preselected values (page restored, back/forward cache vb.)
    try {
        const hasAll =
            anaKategoriSelect?.value && altKategoriSelect?.value && yayinTipiSelect?.value;
        if (hasAll) {
            const anaKategoriSlugAuto =
                anaKategoriSelect.options[anaKategoriSelect.selectedIndex]?.dataset?.slug;
            const yayinTipiTextAuto = yayinTipiSelect.options[yayinTipiSelect.selectedIndex]?.text;
            const eventAuto = new CustomEvent('category-changed', {
                detail: {
                    category: {
                        id: anaKategoriSelect.value,
                        slug: anaKategoriSlugAuto,
                        parent_slug: anaKategoriSlugAuto,
                    },
                    yayinTipi: yayinTipiTextAuto,
                    yayinTipiId: yayinTipiSelect.value,
                },
            });
            console.log('⚡ Auto-dispatching category-changed (preselected values)');
            window.dispatchEvent(eventAuto);
        }
    } catch (e) {
        console.warn('Auto-dispatch skipped:', e);
    }
});

/**
 * Kategori Dinamik Alanlar (Alpine için)
 */
window.kategoriDinamikAlanlar = function () {
    return {
        selectedKategori: null,
        selectedAltKategori: null,
        selectedYayinTipi: null,

        hasRequiredFields: false,
        hasRecommendedFields: false,
        requiredFieldsHtml: '',
        recommendedFieldsHtml: '',
        fieldInfo: null,

        init() {
            console.log('Kategori dinamik alanlar initialized');
        },
    };
};

// Export functions for use in other modules
window.IlanCreateCategories = {
    loadAltKategoriler,
    loadYayinTipleri,
    loadTypeBasedFields,
    validateCategories,
    kategoriDinamikAlanlar: window.kategoriDinamikAlanlar,
    initializeCategories: function () {
        console.log('✅ IlanCreateCategories.initializeCategories() called');
        // Event listeners already set up in DOMContentLoaded
        // This method is just for consistency
    },
    // 🆕 Simple dispatcher for inline fallback
    dispatchCategoryChanged: function () {
        try {
            const ana = document.getElementById('ana_kategori');
            const yayin = document.getElementById('yayin_tipi_id');
            if (!ana || !yayin || !ana.value || !yayin.value) return;
            const anaSlug = ana.options[ana.selectedIndex]?.dataset?.slug;
            const yayinText = yayin.options[yayin.selectedIndex]?.text;
            const ev = new CustomEvent('category-changed', {
                detail: {
                    category: { id: ana.value, slug: anaSlug, parent_slug: anaSlug },
                    yayinTipi: yayinText,
                    yayinTipiId: yayin.value,
                },
            });
            window.dispatchEvent(ev);
        } catch (e) {
            console.warn('dispatchCategoryChanged failed:', e);
        }
    },
};

// Context7: Global scope export for inline onclick handlers
window.loadAltKategoriler = loadAltKategoriler;
window.loadYayinTipleri = loadYayinTipleri;
window.dispatchCategoryChanged = window.IlanCreateCategories.dispatchCategoryChanged;
