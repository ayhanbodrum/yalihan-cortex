// ilan-create-core.js - Main functionality for stable create form

document.addEventListener('DOMContentLoaded', () => {
    // Initialize all components
    initializeForm();
    initializeValidation();
    initializePreview();
    initializeSaveDraft();
});

function initializeForm() {
    // Form initialization
    const form = document.getElementById('ilan-create-form');
    if (!form) return;

    // Auto-save functionality
    let autoSaveTimer;
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach((input) => {
        input.addEventListener('input', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(saveDraft, 30000); // Auto-save every 30 seconds
        });
    });

    // Form submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (validateForm()) {
            showLoading('İlan kaydediliyor...');
            submitForm();
        }
    });
}

function initializeValidation() {
    // Real-time validation
    const requiredFields = document.querySelectorAll('[required]');
    requiredFields.forEach((field) => {
        field.addEventListener('blur', function () {
            validateField(this);
        });
    });

    // Price validation
    const priceInputs = document.querySelectorAll(
        'input[name="fiyat"], input[name="baslangic_fiyati"], input[name="gunluk_fiyat"]'
    );
    priceInputs.forEach((input) => {
        input.addEventListener('input', function () {
            validatePrice(this);
        });
    });
}

function validateForm() {
    let isValid = true;
    const requiredFields = document.querySelectorAll('[required]');

    requiredFields.forEach((field) => {
        if (!validateField(field)) {
            isValid = false;
        }
    });

    // Category validation
    if (!validateCategories()) {
        isValid = false;
    }

    // Location validation
    if (!validateLocation()) {
        isValid = false;
    }

    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;

    // Remove existing error messages
    removeFieldError(field);

    // Required field validation
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'Bu alan zorunludur.');
        return false;
    }

    // Email validation
    if (field.type === 'email' && value && !isValidEmail(value)) {
        showFieldError(field, 'Geçerli bir e-posta adresi giriniz.');
        return false;
    }

    // Phone validation
    if (fieldName === 'person_telefon' && value && !isValidPhone(value)) {
        showFieldError(field, 'Geçerli bir telefon numarası giriniz.');
        return false;
    }

    // TC validation
    if (fieldName === 'person_tc' && value && !isValidTC(value)) {
        showFieldError(field, 'Geçerli bir TC kimlik numarası giriniz.');
        return false;
    }

    return true;
}

function validatePrice(input) {
    const value = input.value.replace(/[^\d]/g, '');
    if (value) {
        // Format price with thousands separator
        const formatted = new Intl.NumberFormat('tr-TR').format(value);
        input.value = formatted;
    }
}

function showFieldError(field, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-red-500 text-sm mt-1';
    errorDiv.textContent = message;

    field.classList.add('border-red-500');
    field.parentNode.appendChild(errorDiv);
}

function removeFieldError(field) {
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
    field.classList.remove('border-red-500');
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^(\+90|0)?[5][0-9]{9}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

function isValidTC(tc) {
    if (tc.length !== 11 || tc[0] === '0') return false;

    const digits = tc.split('').map(Number);
    const sum = digits.slice(0, 10).reduce((a, b) => a + b, 0);
    const check1 = (sum * 7 - digits[9]) % 10;
    const check2 = digits.slice(0, 10).reduce((a, b, i) => a + b * ((i % 9) + 1), 0) % 10;

    return check1 === digits[9] && check2 === digits[10];
}

function initializePreview() {
    const previewBtn = document.getElementById('preview-btn');
    if (previewBtn) {
        previewBtn.addEventListener('click', showPreview);
    }
}

function showPreview() {
    // Collect form data
    const formData = new FormData(document.getElementById('ilan-create-form'));
    const previewData = {};

    for (const [key, value] of formData.entries()) {
        previewData[key] = value;
    }

    // Show preview modal
    showPreviewModal(previewData);
}

function showPreviewModal(data) {
    const modal = document.getElementById('preview-modal');
    const content = document.getElementById('preview-content');

    if (!modal || !content) return;

    // Generate preview HTML
    content.innerHTML = generatePreviewHTML(data);

    modal.classList.remove('hidden');
}

function generatePreviewHTML(data) {
    return `
        <div class="preview-header">
            <h2 class="text-2xl font-bold">${data.baslik || 'İlan Başlığı'}</h2>
            <div class="price text-3xl font-bold text-green-600">
                ${
                    data.fiyat
                        ? new Intl.NumberFormat('tr-TR', {
                              style: 'currency',
                              currency: 'TRY',
                          }).format(data.fiyat)
                        : 'Fiyat Belirtilmemiş'
                }
            </div>
        </div>
        <div class="preview-content mt-6">
            <p class="text-gray-700">${data.aciklama || 'Açıklama girilmemiş'}</p>
        </div>
    `;
}

function initializeSaveDraft() {
    const saveDraftBtn = document.getElementById('save-draft-btn');
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', saveDraft);
    }
}

function saveDraft() {
    const formData = new FormData(document.getElementById('ilan-create-form'));

    fetch('/admin/ilanlar/draft', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content'),
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showNotification('Taslak kaydedildi', 'success');
            } else {
                showNotification('Taslak kaydedilemedi', 'error');
            }
        })
        .catch((error) => {
            console.error('Draft save error:', error);
            showNotification('Taslak kaydedilemedi', 'error');
        });
}

function submitForm() {
    const form = document.getElementById('ilan-create-form');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content'),
        },
    })
        .then((response) => response.json())
        .then((data) => {
            hideLoading();
            if (data.success) {
                showNotification('İlan başarıyla oluşturuldu', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || '/admin/ilanlar';
                }, 2000);
            } else {
                showNotification(data.message || 'İlan oluşturulamadı', 'error');
                if (data.errors) {
                    displayFormErrors(data.errors);
                }
            }
        })
        .catch((error) => {
            hideLoading();
            console.error('Form submission error:', error);
            showNotification('Bir hata oluştu', 'error');
        });
}

function displayFormErrors(errors) {
    // Clear existing errors
    document.querySelectorAll('.field-error').forEach((el) => el.remove());

    // Display new errors
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            showFieldError(input, messages[0]);
        }
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
        type === 'success'
            ? 'bg-green-500'
            : type === 'error'
              ? 'bg-red-500'
              : type === 'warning'
                ? 'bg-yellow-500'
                : 'bg-blue-500'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

function showLoading(message = 'Yükleniyor...') {
    const loading = document.createElement('div');
    loading.id = 'loading-overlay';
    loading.className =
        'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loading.innerHTML = `
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(loading);
}

function hideLoading() {
    const loading = document.getElementById('loading-overlay');
    if (loading) {
        loading.remove();
    }
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function () {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
}

// Site/Apartman ekleme modalını açar
function openSiteAddModal() {
    showLoading('Site bilgileri hazırlanıyor...');

    // Modal HTML içeriği
    const modalHTML = `
        <div id="siteAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        🏢 Yeni Site/Apartman Ekle
                    </h3>
                    <button onclick="closeSiteAddModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="siteAddForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Site/Apartman Adı *
                        </label>
                        <input type="text" id="site_name" name="site_name" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Örn: Acıbadem Residences">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Adres
                        </label>
                        <textarea id="site_address" name="site_address" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Site adresi (opsiyonel)"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Toplam Blok
                            </label>
                            <input type="number" id="site_blocks" name="site_blocks" min="1"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Toplam Daire
                            </label>
                            <input type="number" id="site_units" name="site_units" min="1"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="50">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="site_has_elevator" name="site_has_elevator"
                            class="mr-2 rounded focus:ring-blue-500">
                        <label for="site_has_elevator" class="text-sm text-gray-700 dark:text-gray-300">
                            Asansör var
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeSiteAddModal()"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                            İptal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-1"></i>
                            Site Ekle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;

    // Modal'ı sayfaya ekle
    setTimeout(() => {
        hideLoading();
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Form submit handler
        document.getElementById('siteAddForm').addEventListener('submit', (e) => {
            e.preventDefault();
            handleSiteAdd();
        });

        // Focus to first input
        document.getElementById('site_name').focus();
    }, 500);
}

// Site/Apartman ekleme modalını kapatır
function closeSiteAddModal() {
    const modal = document.getElementById('siteAddModal');
    if (modal) {
        modal.remove();
    }
}

// Site ekleme işlemini gerçekleştirir
function handleSiteAdd() {
    const form = document.getElementById('siteAddForm');
    const formData = new FormData(form);

    showLoading('Site ekleniyor...');

    // Simulated API call - gerçek API endpoint ile değiştirin
    setTimeout(() => {
        hideLoading();

        const siteName = formData.get('site_name');

        // Site search input'una eklenen siteyi yaz
        const siteSearchInput = document.getElementById('site_search');
        const siteIdInput = document.getElementById('site_id');

        if (siteSearchInput && siteName) {
            siteSearchInput.value = siteName;
            // Gerçek implementasyonda API'den dönen ID kullanılacak
            if (siteIdInput) {
                siteIdInput.value = 'new_site_' + Date.now();
            }
        }

        showNotification('Site başarıyla eklendi: ' + siteName, 'success');
        closeSiteAddModal();
    }, 1000);
}

// Export functions for use in other modules
window.IlanCreateCore = {
    validateForm,
    showNotification,
    showLoading,
    hideLoading,
    debounce,
    throttle,
    openSiteAddModal,
    closeSiteAddModal,
};

// Global fonksiyonları window objesine ekle (backward compatibility için)
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.showNotification = showNotification;
window.openSiteAddModal = openSiteAddModal;
window.closeSiteAddModal = closeSiteAddModal;
