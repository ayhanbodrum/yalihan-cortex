@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6 pb-32" x-data="ilanWizard()">
        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Yeni İlan Oluştur
                </h1>
                <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-400">
                    Adım adım ilan bilgilerinizi doldurun
                </p>
            </div>
            <a href="{{ route('admin.ilanlar.index') }}"
                class="inline-flex items-center px-4 py-2.5 bg-gray-600 text-white font-medium rounded-lg shadow-sm hover:bg-gray-700 hover:shadow-md active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Geri Dön
            </a>
        </div>

        {{-- Progress Bar --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-900 dark:text-white">İlerleme</span>
                <span class="text-sm text-gray-500 dark:text-gray-400"
                    x-text="`%${Math.round((currentStep / totalSteps) * 100)}`"></span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="h-2 bg-blue-600 rounded-full transition-all duration-500"
                    :style="`width: ${(currentStep / totalSteps) * 100}%`"></div>
            </div>
        </div>

        {{-- Wizard Steps Navigation --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <button @click="goToStep(1)"
                    :class="currentStep === 1 ? 'bg-blue-600 text-white' :
                        'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                    class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:scale-105">
                    1. Temel Bilgiler
                </button>
                <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600 mx-2"></div>
                <button @click="goToStep(2)"
                    :class="currentStep === 2 ? 'bg-blue-600 text-white' : completedSteps.has(1) ?
                        'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' :
                        'bg-gray-50 dark:bg-gray-900 text-gray-400 dark:text-gray-600 cursor-not-allowed'"
                    :disabled="!completedSteps.has(1)"
                    class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:scale-105 disabled:opacity-50">
                    2. Detaylar
                </button>
                <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600 mx-2"></div>
                <button @click="goToStep(3)"
                    :class="currentStep === 3 ? 'bg-blue-600 text-white' : completedSteps.has(2) ?
                        'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' :
                        'bg-gray-50 dark:bg-gray-900 text-gray-400 dark:text-gray-600 cursor-not-allowed'"
                    :disabled="!completedSteps.has(2)"
                    class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:scale-105 disabled:opacity-50">
                    3. Ek Bilgiler
                </button>
            </div>
        </div>

        {{-- Main Form --}}
        <form id="ilan-wizard-form" method="POST" action="{{ route('admin.ilanlar.store') }}" enctype="multipart/form-data"
            @submit.prevent="submitForm()">
            @csrf

            {{-- STEP 1: TEMEL BİLGİLER --}}
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">

                @include('admin.ilanlar.wizard.step-1-basic-info')

                {{-- Navigation Buttons --}}
                <div class="flex justify-end gap-4 mt-8">
                    <button type="button" @click="nextStep()"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all duration-200 font-medium">
                        İleri →
                    </button>
                </div>
            </div>

            {{-- STEP 2: DETAYLAR (Kategoriye Özel) --}}
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">

                @include('admin.ilanlar.wizard.step-2-details')

                {{-- Navigation Buttons --}}
                <div class="flex justify-between gap-4 mt-8">
                    <button type="button" @click="prevStep()"
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 hover:scale-105 active:scale-95 transition-all duration-200 font-medium">
                        ← Geri
                    </button>
                    <button type="button" @click="nextStep()"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all duration-200 font-medium">
                        İleri →
                    </button>
                </div>
            </div>

            {{-- STEP 3: EK BİLGİLER --}}
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">

                @include('admin.ilanlar.wizard.step-3-additional')

                {{-- Navigation Buttons --}}
                <div class="flex justify-between gap-4 mt-8">
                    <button type="button" @click="prevStep()"
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 hover:scale-105 active:scale-95 transition-all duration-200 font-medium">
                        ← Geri
                    </button>
                    <div class="flex gap-4">
                        <button type="button" @click="saveDraft()"
                            class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 hover:scale-105 active:scale-95 transition-all duration-200 font-medium">
                            💾 Taslak Kaydet
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 hover:scale-105 active:scale-95 transition-all duration-200 font-medium">
                            ✅ Yayınla
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // ✅ Context7: Global scope - Alpine.js erişimi için
            window.ilanWizard = function() {
                return {
                    currentStep: 1,
                    totalSteps: 3,
                    completedSteps: new Set(),
                    formData: {},

                    init() {
                        this.loadDraft();
                        this.setupValidation();
                    },

                    goToStep(step) {
                        if (step < this.currentStep || this.completedSteps.has(step - 1)) {
                            this.currentStep = step;
                            this.scrollToTop();
                            // Step 2'ye geçildiğinde kategori kontrolünü tetikle
                            if (step === 2) {
                                setTimeout(() => {
                                    document.dispatchEvent(new CustomEvent('wizard-step-changed', {
                                        detail: {
                                            step: 2
                                        }
                                    }));
                                }, 100);
                            }
                        }
                    },

                    nextStep() {
                        if (this.validateStep(this.currentStep)) {
                            this.completedSteps.add(this.currentStep);
                            if (this.currentStep < this.totalSteps) {
                                this.currentStep++;
                                this.scrollToTop();
                                // Step 2'ye geçildiğinde kategori kontrolünü tetikle
                                if (this.currentStep === 2) {
                                    setTimeout(() => {
                                        document.dispatchEvent(new CustomEvent('wizard-step-changed', {
                                            detail: {
                                                step: 2
                                            }
                                        }));
                                    }, 100);
                                }
                            }
                        }
                    },

                    prevStep() {
                        if (this.currentStep > 1) {
                            this.currentStep--;
                            this.scrollToTop();
                        }
                    },

                    validateStep(step) {
                        const form = document.getElementById('ilan-wizard-form');
                        const stepFields = this.getStepFields(step);

                        let isValid = true;
                        stepFields.forEach(fieldName => {
                            const field = form.querySelector(`[name="${fieldName}"]`);
                            if (field && field.hasAttribute('required')) {
                                if (!field.value || field.value.trim() === '') {
                                    isValid = false;
                                    this.showFieldError(field, 'Bu alan zorunludur');
                                } else {
                                    this.hideFieldError(field);
                                }
                            }
                        });

                        if (!isValid) {
                            this.showNotification('Lütfen tüm zorunlu alanları doldurun', 'error');
                        }

                        return isValid;
                    },

                    getStepFields(step) {
                        const stepFieldsMap = {
                            1: ['ana_kategori_id', 'alt_kategori_id', 'yayin_tipi_id', 'baslik', 'fiyat', 'para_birimi',
                                'il_id', 'ilce_id', 'adres'
                            ],
                            2: [], // Kategoriye göre değişir
                            3: ['aciklama', 'ilan_sahibi_id', 'status']
                        };

                        // Step 2 için kategoriye göre alanlar
                        if (step === 2) {
                            const altKategoriSelect = document.querySelector('[name="alt_kategori_id"]');
                            if (altKategoriSelect && altKategoriSelect.value) {
                                const selectedOption = altKategoriSelect.options[altKategoriSelect.selectedIndex];
                                const categorySlug = selectedOption?.getAttribute('data-slug') || '';
                                const categoryName = selectedOption?.text.toLowerCase() || '';

                                // Arsa kontrolü
                                if (categorySlug.includes('arsa') || categoryName.includes('arsa')) {
                                    return ['ada_no', 'parsel_no'];
                                }
                                // Konut kontrolü
                                else if (categorySlug.includes('konut') || categorySlug.includes('daire') ||
                                    categorySlug.includes('villa') || categoryName.includes('konut') ||
                                    categoryName.includes('daire') || categoryName.includes('villa')) {
                                    return ['oda_sayisi', 'brut_alan', 'net_alan'];
                                }
                            }
                            return []; // Kategori seçilmemişse boş
                        }

                        return stepFieldsMap[step] || [];
                    },

                    showFieldError(field, message) {
                        field.classList.add('border-red-500', 'dark:border-red-500');
                        let errorDiv = field.parentElement.querySelector('.field-error');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.className = 'field-error text-sm text-red-600 dark:text-red-400 mt-1';
                            field.parentElement.appendChild(errorDiv);
                        }
                        errorDiv.textContent = message;
                    },

                    hideFieldError(field) {
                        field.classList.remove('border-red-500', 'dark:border-red-500');
                        const errorDiv = field.parentElement.querySelector('.field-error');
                        if (errorDiv) {
                            errorDiv.remove();
                        }
                    },

                    showNotification(message, type = 'info') {
                        // Simple toast notification
                        const toast = document.createElement('div');
                        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                type === 'error' ? 'bg-red-600 text-white' :
                type === 'success' ? 'bg-green-600 text-white' :
                'bg-blue-600 text-white'
            }`;
                        toast.textContent = message;
                        document.body.appendChild(toast);

                        setTimeout(() => {
                            toast.remove();
                        }, 3000);
                    },

                    scrollToTop() {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    },

                    saveDraft() {
                        const form = document.getElementById('ilan-wizard-form');
                        const formData = new FormData(form);

                        // Save to localStorage
                        const draftData = {};
                        for (let [key, value] of formData.entries()) {
                            draftData[key] = value;
                        }
                        localStorage.setItem('ilan_wizard_draft', JSON.stringify(draftData));

                        this.showNotification('Taslak kaydedildi', 'success');
                    },

                    loadDraft() {
                        const draftData = localStorage.getItem('ilan_wizard_draft');
                        if (draftData) {
                            try {
                                const data = JSON.parse(draftData);
                                Object.keys(data).forEach(key => {
                                    const field = document.querySelector(`[name="${key}"]`);
                                    if (field) {
                                        field.value = data[key];
                                    }
                                });
                            } catch (e) {
                                console.error('Draft yüklenemedi:', e);
                            }
                        }
                    },

                    setupValidation() {
                        // Real-time validation
                        const form = document.getElementById('ilan-wizard-form');
                        form.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
                            field.addEventListener('blur', () => {
                                if (field.value.trim() === '') {
                                    this.showFieldError(field, 'Bu alan zorunludur');
                                } else {
                                    this.hideFieldError(field);
                                }
                            });
                        });
                    },

                    async submitForm() {
                        if (!this.validateStep(3)) {
                            return;
                        }

                        const form = document.getElementById('ilan-wizard-form');
                        const submitButton = form.querySelector('button[type="submit"]');

                        // Disable submit button
                        if (submitButton) {
                            submitButton.disabled = true;
                            submitButton.textContent = 'Kaydediliyor...';
                        }

                        // ✅ FIX: Formatlanmış fiyatı raw değere çevir (5.000.000 -> 5000000)
                        const fiyatInput = document.getElementById('fiyat');
                        const fiyatRawInput = document.getElementById('fiyat_raw');
                        if (fiyatInput && fiyatRawInput) {
                            const rawValue = fiyatRawInput.value || fiyatInput.value.replace(/\./g, '');
                            fiyatInput.value = rawValue; // Form submit'te raw değer gönder
                        }

                        const formData = new FormData(form);

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                        '',
                                    'Accept': 'application/json'
                                }
                            });

                            if (response.ok) {
                                // Check if response is JSON or HTML redirect
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    const result = await response.json();
                                    this.showNotification('İlan başarıyla oluşturuldu!', 'success');
                                    setTimeout(() => {
                                        window.location.href = result.redirect || result.url ||
                                            '{{ route('admin.ilanlar.index') }}';
                                    }, 1500);
                                } else {
                                    // HTML response (redirect)
                                    this.showNotification('İlan başarıyla oluşturuldu!', 'success');
                                    setTimeout(() => {
                                        window.location.href = response.url || '{{ route('admin.ilanlar.index') }}';
                                    }, 1000);
                                }
                            } else {
                                // Handle validation errors
                                const errorData = await response.json().catch(() => ({
                                    message: 'Bir hata oluştu'
                                }));

                                if (errorData.errors) {
                                    // Show field errors
                                    Object.keys(errorData.errors).forEach(field => {
                                        const fieldElement = form.querySelector(`[name="${field}"]`);
                                        if (fieldElement) {
                                            this.showFieldError(fieldElement, errorData.errors[field][0]);
                                        }
                                    });
                                    this.showNotification('Lütfen form hatalarını düzeltin', 'error');
                                } else {
                                    this.showNotification(errorData.message || 'Bir hata oluştu', 'error');
                                }

                                // Re-enable submit button
                                if (submitButton) {
                                    submitButton.disabled = false;
                                    submitButton.textContent = '✅ Yayınla';
                                }
                            }
                        } catch (error) {
                            this.showNotification('Bağlantı hatası: ' + error.message, 'error');

                            // Re-enable submit button
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.textContent = '✅ Yayınla';
                            }
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
