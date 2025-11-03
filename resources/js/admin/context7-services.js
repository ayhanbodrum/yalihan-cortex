/**
 * Context7 Services - AI-First Architecture
 * Version: 2.0.0
 * Context7 Standard: C7-SERVICES-2025-01-30
 */

/**
 * Context7 AI Service
 */
class Context7AIService {
    constructor() {
        this.baseUrl = '/api/smart-ilan/ai';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    }

    async analyzeBasicInfo(formData) {
        try {
            const response = await fetch(`${this.baseUrl}/basic-analysis`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify(formData),
            });
            return await response.json();
        } catch (error) {
            console.error('AI Basic Analysis Error:', error);
            return { success: false, error: error.message };
        }
    }

    async getFeatureSuggestions(kategoriId, location) {
        try {
            const response = await fetch(`${this.baseUrl}/feature-suggestions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({ kategori_id: kategoriId, location }),
            });
            return await response.json();
        } catch (error) {
            console.error('AI Feature Suggestions Error:', error);
            return { success: false, error: error.message };
        }
    }

    async optimizePrice(formData) {
        try {
            const response = await fetch(`${this.baseUrl}/price-optimization`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify(formData),
            });
            return await response.json();
        } catch (error) {
            console.error('AI Price Optimization Error:', error);
            return { success: false, error: error.message };
        }
    }

    async generateDescription(formData) {
        try {
            const response = await fetch(`${this.baseUrl}/generate-description`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify(formData),
            });
            return await response.json();
        } catch (error) {
            console.error('AI Description Generation Error:', error);
            return { success: false, error: error.message };
        }
    }

    async analyzeImages(images) {
        try {
            const formData = new FormData();
            images.forEach((image, index) => {
                formData.append(`images[${index}]`, image);
            });

            const response = await fetch(`${this.baseUrl}/analyze-images`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: formData,
            });
            return await response.json();
        } catch (error) {
            console.error('AI Image Analysis Error:', error);
            return { success: false, error: error.message };
        }
    }
}

/**
 * Context7 Location Service
 */
class Context7LocationService {
    constructor() {
        this.map = null;
        this.marker = null;
    }

    initMap(containerId) {
        console.log('🗺️ Context7 Location Service initializing...');

        // Google Maps API key kontrolü
        if (typeof google === 'undefined' || !google.maps) {
            console.warn('Google Maps API not loaded');
            // Fallback: callback geldiğinde tekrar dene
            window.initGoogleMaps = () => {
                try {
                    this.initMap(containerId);
                } catch (e) {}
            };
            return;
        }

        // Harita container'ı
        const mapContainer = document.getElementById(containerId);
        if (!mapContainer) {
            console.warn(`Map container ${containerId} not found`);
            return;
        }

        // Türkiye merkezi koordinatları
        const turkeyCenter = { lat: 39.9334, lng: 32.8597 };

        // Harita oluştur
        this.map = new google.maps.Map(mapContainer, {
            zoom: 6,
            center: turkeyCenter,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
        });

        // Marker oluştur
        this.marker = new google.maps.Marker({
            position: turkeyCenter,
            map: this.map,
            draggable: true,
            title: 'İlan Konumu',
        });

        // Marker drag event
        this.marker.addListener('dragend', () => {
            const position = this.marker.getPosition();
            this.updateLocationFields(position.lat(), position.lng());
        });

        // Harita click event
        this.map.addListener('click', (event) => {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            this.marker.setPosition(event.latLng);
            this.updateLocationFields(lat, lng);
        });

        console.log('✅ Context7 Location Service initialized');
    }

    updateLocationFields(lat, lng) {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        if (latInput) latInput.value = lat.toFixed(6);
        if (lngInput) lngInput.value = lng.toFixed(6);

        // Geocoding ile adres bilgisi al
        this.reverseGeocode(lat, lng);
    }

    async reverseGeocode(lat, lng) {
        if (typeof google === 'undefined' || !google.maps) {
            return;
        }

        try {
            const geocoder = new google.maps.Geocoder();
            const latlng = { lat: lat, lng: lng };

            geocoder.geocode({ location: latlng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    const address = results[0].formatted_address;
                    const addressInput = document.getElementById('detayli_adres');
                    if (addressInput) {
                        addressInput.value = address;
                    }
                }
            });
        } catch (error) {
            console.error('Reverse geocoding error:', error);
        }
    }
}

/**
 * Context7 Media Service
 */
class Context7MediaService {
    constructor() {
        this.maxFileSize = 10 * 1024 * 1024; // 10MB
        this.allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        this.maxFiles = 20;
    }

    validateFile(file) {
        if (!this.allowedTypes.includes(file.type)) {
            throw new Error(
                'Geçersiz dosya formatı. Sadece JPEG, PNG, JPG, GIF, WEBP dosyaları kabul edilir.'
            );
        }

        if (file.size > this.maxFileSize) {
            throw new Error('Dosya boyutu çok büyük. Maksimum 10MB olabilir.');
        }

        return true;
    }

    createImagePreview(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                resolve({
                    file: file,
                    url: e.target.result,
                    name: file.name,
                    size: file.size,
                });
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    async handleFiles(files) {
        const validFiles = [];
        const errors = [];

        for (let i = 0; i < files.length; i++) {
            try {
                this.validateFile(files[i]);
                const preview = await this.createImagePreview(files[i]);
                validFiles.push(preview);
            } catch (error) {
                errors.push(`${files[i].name}: ${error.message}`);
            }
        }

        return { validFiles, errors };
    }
}

/**
 * Context7 Validation Service
 */
class Context7ValidationService {
    constructor() {
        this.rules = {
            required: (value) => value && value.trim() !== '',
            email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            phone: (value) => /^[\+]?[0-9\s\-\(\)]{10,}$/.test(value),
            numeric: (value) => !isNaN(value) && !isNaN(parseFloat(value)),
            min: (value, min) => parseFloat(value) >= min,
            max: (value, max) => parseFloat(value) <= max,
        };
    }

    validateField(field) {
        const value = field.value;
        const rules = field.dataset.validationRules
            ? JSON.parse(field.dataset.validationRules)
            : {};
        const errors = [];

        // Required rule
        if (rules.required && !this.rules.required(value)) {
            errors.push('Bu alan zorunludur.');
        }

        // Email rule
        if (rules.email && value && !this.rules.email(value)) {
            errors.push('Geçerli bir email adresi giriniz.');
        }

        // Phone rule
        if (rules.phone && value && !this.rules.phone(value)) {
            errors.push('Geçerli bir telefon numarası giriniz.');
        }

        // Numeric rule
        if (rules.numeric && value && !this.rules.numeric(value)) {
            errors.push('Sayısal bir değer giriniz.');
        }

        // Min rule
        if (rules.min && value && !this.rules.min(value, rules.min)) {
            errors.push(`Minimum değer ${rules.min} olmalıdır.`);
        }

        // Max rule
        if (rules.max && value && !this.rules.max(value, rules.max)) {
            errors.push(`Maksimum değer ${rules.max} olmalıdır.`);
        }

        this.displayFieldErrors(field, errors);
        return errors.length === 0;
    }

    displayFieldErrors(field, errors) {
        // Eski hata mesajlarını temizle
        const existingErrors = field.parentNode.querySelectorAll('.validation-error');
        existingErrors.forEach((error) => error.remove());

        // Yeni hata mesajlarını göster
        if (errors.length > 0) {
            const errorContainer = document.createElement('div');
            errorContainer.className = 'validation-error text-red-500 text-sm mt-1';
            errorContainer.innerHTML = errors.map((error) => `<div>${error}</div>`).join('');
            field.parentNode.appendChild(errorContainer);

            // Field'ı hatalı olarak işaretle
            field.classList.add('border-red-500');
            field.classList.remove('border-green-500');
        } else {
            // Field'ı başarılı olarak işaretle
            field.classList.add('border-green-500');
            field.classList.remove('border-red-500');
        }
    }

    validateForm(form) {
        const fields = form.querySelectorAll('[data-validation-rules]');
        let isValid = true;

        fields.forEach((field) => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }
}

// Global olarak erişilebilir hale getir
window.Context7AIService = Context7AIService;
window.Context7LocationService = Context7LocationService;
window.Context7MediaService = Context7MediaService;
window.Context7ValidationService = Context7ValidationService;
