{{--
    Kategori Bazlı Dinamik Alanlar Component
    Context7 v3.4.0 - Kural #66

    Kullanım: Kategori seçiminden SONRA include et
--}}

<div x-data="kategoriDinamikAlanlar()" x-show="selectedKategori" x-cloak class="mt-4">
    {{-- Dinamik Alan Bildirimi --}}
    <div x-show="hasRequiredFields || hasRecommendedFields"
        class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-start gap-2">
            <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
            <div class="text-sm">
                <strong class="text-blue-800 dark:text-blue-300">Kategori Özel Alanlar</strong>
                <p class="text-gray-900 dark:text-white mt-1" x-text="fieldInfo"></p>
            </div>
        </div>
    </div>

    {{-- Zorunlu Alanlar --}}
    <div x-show="hasRequiredFields" x-cloak x-transition class="mb-6">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
            <h4 class="text-sm font-bold text-red-800 dark:text-red-300 mb-3 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
                Zorunlu Alanlar
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-html="requiredFieldsHtml"></div>
        </div>
    </div>

    {{-- Önerilen Alanlar --}}
    <div x-show="hasRecommendedFields" x-cloak x-transition class="mb-6">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
            <h4 class="text-sm font-bold text-yellow-800 dark:text-yellow-300 mb-3 flex items-center gap-2">
                <i class="fas fa-lightbulb text-yellow-600"></i>
                Önerilen Alanlar (Daha İyi İlan İçin)
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-html="recommendedFieldsHtml"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Alpine.js Component: kategoriDinamikAlanlar
        if (typeof Alpine !== 'undefined') {
            document.addEventListener('alpine:init', () => {
                Alpine.data('kategoriDinamikAlanlar', () => ({
                    selectedKategori: null,
                    hasRequiredFields: false,
                    hasRecommendedFields: false,
                    requiredFieldsHtml: '',
                    recommendedFieldsHtml: '',
                    fieldInfo: '',

                    // Kategori seçildiğinde otomatik çağrılır
                    async loadFieldsByKategori(kategoriId, kategoriName) {
                        this.selectedKategori = kategoriName;

                        // ✅ API'den özellikleri çek (Database-driven)
                        try {
                            const response = await fetch(`/admin/ilan-kategorileri/${kategoriId}/ozellikler`);
                            const result = await response.json();

                            if (!result.success || !result.data || result.data.length === 0) {
                                this.hasRequiredFields = false;
                                this.hasRecommendedFields = false;
                                this.fieldInfo = `${kategoriName} kategorisi için henüz özellik tanımlanmamış.`;
                                return;
                            }

                            // Özellikleri zorunlu/opsiyonel olarak ayır
                            const requiredFields = {};
                            const recommendedFields = {};

                            result.data.forEach(field => {
                                const fieldDef = {
                                    label: field.name,
                                    type: this.mapFieldType(field.type),
                                    help: field.help || '',
                                    icon: this.getIconForField(field.slug),
                                    options: field.options,
                                    unit: field.unit
                                };

                                if (field.required) {
                                    requiredFields[field.slug] = fieldDef;
                                } else {
                                    recommendedFields[field.slug] = fieldDef;
                                }
                            });

                            this.hasRequiredFields = Object.keys(requiredFields).length > 0;
                            this.hasRecommendedFields = Object.keys(recommendedFields).length > 0;

                            // HTML oluştur
                            this.requiredFieldsHtml = this.generateFieldsHtml(requiredFields);
                            this.recommendedFieldsHtml = this.generateFieldsHtml(recommendedFields);

                            this.fieldInfo = `${kategoriName} kategorisi için ${this.hasRequiredFields ? 'zorunlu' : ''} ${this.hasRecommendedFields ? 've önerilen' : ''} alanlar gösteriliyor.`;

                        } catch (error) {
                            console.error('❌ Özellik yükleme hatası:', error);
                            this.hasRequiredFields = false;
                            this.hasRecommendedFields = false;
                            this.fieldInfo = 'Özellikler yüklenemedi. Lütfen sayfayı yenileyin.';
                        }
                    },

                    // Field type mapping (database → HTML input type)
                    mapFieldType(dbType) {
                        const typeMap = {
                            'text': 'text',
                            'number': 'number',
                            'boolean': 'checkbox',
                            'select': 'select',
                            'multiselect': 'multiselect',
                            'textarea': 'textarea',
                            'date': 'date'
                        };
                        return typeMap[dbType] || 'text';
                    },

                    // Icon mapping (field slug → FontAwesome icon)
                    getIconForField(slug) {
                        const iconMap = {
                            'oda-sayisi': 'door-open',
                            'brut-metrekare': 'ruler-combined',
                            'net-metrekare': 'ruler',
                            'banyo-sayisi': 'bath',
                            'bina-yasi': 'calendar-alt',
                            'kat': 'layer-group',
                            'toplam-kat': 'building',
                            'isitma': 'fire',
                            'cephe': 'compass',
                            'balkon': 'home',
                            'asansor': 'elevator',
                            'otopark': 'parking',
                            'esyali': 'couch',
                            'kullanim-durumu': 'key',
                            'site-icerisinde': 'city',
                            'ada-no': 'map-marked-alt',
                            'parsel-no': 'map-pin',
                            'taks': 'percentage',
                            'kaks': 'layer-group',
                            'imar-durumu': 'building',
                            'havuz': 'swimming-pool',
                            'min-konaklama': 'calendar-day',
                            'max-kisi': 'users'
                        };
                        return iconMap[slug] || 'info-circle';
                    },

                    // ❌ DEPRECATED: getFieldsByCategory metodu kaldırıldı
                    // ✅ Artık API'den dinamik olarak çekiliyor (loadFieldsByKategori)

                    generateFieldsHtml(fields) {
                        if (!fields || Object.keys(fields).length === 0) return '';

                        let html = '';
                        for (const [name, config] of Object.entries(fields)) {
                            // Eğer alan zaten formda varsa, tekrar gösterme
                            if (document.getElementById(name)) continue;

                            html += `
                    <div>
                        <label for="${name}" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            <i class="fas fa-${config.icon} text-blue-500 mr-1"></i>
                            ${config.label}
                        </label>
                        ${this.generateInputHtml(name, config)}
                        ${config.help ? `<small class="text-gray-500 dark:text-gray-400 text-xs mt-1 block">${config.help}</small>` : ''}
                    </div>
                `;
                        }
                        return html;
                    },

                    generateInputHtml(name, config) {
                        const baseClass =
                            "w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent";

                        const unit = config.unit ? ` (${config.unit})` : '';
                        const placeholder = config.label + unit;

                        switch (config.type) {
                            case 'text':
                                return `<input type="text" name="${name}" id="${name}" class="${baseClass}" placeholder="${placeholder}">`;

                            case 'number':
                                return `<input type="number" name="${name}" id="${name}" class="${baseClass}" placeholder="${placeholder}" step="0.01" min="0">`;

                            case 'checkbox':
                                return `
                        <div class="flex items-center">
                            <input type="checkbox" name="${name}" id="${name}" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="${name}" class="ml-2 text-sm text-gray-900 dark:text-white">Evet</label>
                        </div>
                    `;

                            case 'select':
                                let selectOptions = '<option value="">Seçiniz...</option>';

                                // ✅ Dinamik options (API'den gelen)
                                if (config.options && typeof config.options === 'object') {
                                    for (const [value, label] of Object.entries(config.options)) {
                                        selectOptions += `<option value="${this.escapeHtml(value)}">${this.escapeHtml(label)}</option>`;
                                    }
                                }

                                return `<select  name="${name}" id="${name}" class="${baseClass} transition-all duration-200">${selectOptions}</select>`;

                            case 'textarea':
                                return `<textarea name="${name}" id="${name}" class="${baseClass}" rows="3" placeholder="${placeholder}"></textarea>`;

                            case 'date':
                                return `<input type="date" name="${name}" id="${name}" class="${baseClass}">`;

                            default:
                                return `<input type="text" name="${name}" id="${name}" class="${baseClass}">`;
                        }
                    },

                    escapeHtml(text) {
                        const div = document.createElement('div');
                        div.textContent = text;
                        return div.innerHTML;
                    }
                }));
            });
        }

        // Global TKGM auto-query fonksiyonu
        window.tkgmAutoQuery = function() {
            const ada = document.getElementById('ada_no')?.value;
            const parsel = document.getElementById('parsel_no')?.value;
            const ilSelect = document.getElementById('il_id');
            const ilceSelect = document.getElementById('ilce_id');

            if (!ada || !parsel || !ilSelect || !ilceSelect) return;

            const il = ilSelect.options[ilSelect.selectedIndex]?.text;
            const ilce = ilceSelect.options[ilceSelect.selectedIndex]?.text;

            // Alpine component'e gönder (eğer varsa)
            const tkgmComponent = document.querySelector('[x-data*="tkgmSorgu"]');
            if (tkgmComponent && tkgmComponent.__x) {
                tkgmComponent.__x.$data.autoQuery(ada, parsel, il, ilce);
            }
        };

        // ✅ CATEGORY-CHANGED EVENT LISTENER (Kategoriye Özel Alanları Yükle)
        window.addEventListener('category-changed', (e) => {
            console.log('🎯 Kategori değişti:', e.detail);

            if (!e.detail || !e.detail.category) {
                console.log('❌ Kategori bilgisi yok');
                return;
            }

            // Ana kategori ID'sini al
            const categoryId = e.detail.category.id;

            // Ana kategori adını al (select'ten)
            const anaKategoriSelect = document.getElementById('ana_kategori');
            if (!anaKategoriSelect) {
                console.warn('⚠️ Ana kategori select bulunamadı');
                return;
            }

            const selectedOption = anaKategoriSelect.options[anaKategoriSelect.selectedIndex];
            const kategoriName = selectedOption ? selectedOption.text : '';

            console.log('📋 Yüklenecek kategori:', kategoriName, '(ID:', categoryId, ')');

            // Alpine component'i bul ve trigger et (yoksa sessiz fallback)
            const dinamikAlanComponent = document.querySelector('[x-data*="kategoriDinamikAlanlar"]');
            if (dinamikAlanComponent && dinamikAlanComponent.__x) {
                console.log('✅ Alpine component bulundu, alanlar yükleniyor...');
                dinamikAlanComponent.__x.$data.loadFieldsByKategori(categoryId, kategoriName);
            } else {
                console.log('ℹ️ Alpine component yok, FieldDependenciesManager fallback kullanılıyor');
                // FieldDependenciesManager zaten category-changed eventini dinliyor ve render ediyor
                // Ek bir işlem gerekmiyor; yalnızca bilgi amaçlı log bırakıyoruz
            }
        });

        console.log('✅ Kategori dinamik alanlar sistemi hazır');
    </script>
@endpush
