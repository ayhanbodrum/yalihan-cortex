{{-- Kategoriye Özel Dinamik Alanlar - Matrix Entegrasyonu --}}
<div x-data="matrixDynamicFields()" class="space-y-6">
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">
                🎯 Matrix-Based Dynamic Fields
            </h3>
            <div x-show="loading" class="flex items-center text-sm text-blue-600">
                <div class="animate-spin mr-2">⟳</div>
                Loading...
            </div>
        </div>

        {{-- Kategori + Yayın Tipi Seçimi Gerekli --}}
        <div x-show="!selectedKategori || !selectedYayinTipi" class="text-center py-8 text-gray-500 dark:text-gray-400">
            <p class="text-lg mb-2">👆 Önce kategori ve yayın tipi seçin</p>
            <p class="text-sm">Dynamic fields yüklenecek</p>
        </div>

        {{-- Dynamic Fields Container --}}
        <div x-show="selectedKategori && selectedYayinTipi && !loading" class="space-y-6">
            <template x-if="fields.length === 0 && !loading">
                <div class="text-center py-4 text-gray-400 text-sm">
                    Bu kategori + yayın tipi için alan tanımlı değil
                </div>
            </template>

            <template x-for="(category, catIndex) in groupedFields" :key="catIndex">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">
                        <span x-text="getCategoryLabel(category.category)"></span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="field in category.fields" :key="field.slug">
                            <div class="neo-form-group">
                                <label :for="field.slug" class="neo-label">
                                    <span class="neo-label-text" x-text="field.name"></span>
                                    <span x-show="field.required" class="text-red-500">*</span>
                                </label>

                                <template x-if="field.type === 'text' || field.type === 'number'">
                                    <input
                                        :type="field.type"
                                        :name="field.slug"
                                        :id="field.slug"
                                        :placeholder="field.placeholder || ''"
                                        :required="field.required"
                                        :min="field.type === 'number' ? 0 : undefined"
                                        class="neo-input"
                                        :data-context7-field="field.slug"
                                    >
                                </template>

                                <template x-if="field.type === 'select'">
                                    <select
                                        :name="field.slug"
                                        :id="field.slug"
                                        :required="field.required"
                                        class="neo-select"
                                        :data-context7-field="field.slug"
                                    >
                                        <option value="">Seçin...</option>
                                        <template x-if="field.options">
                                            <template x-for="option in field.options" :key="option">
                                                <option :value="option" x-text="option"></option>
                                            </template>
                                        </template>
                                    </select>
                                </template>

                                <template x-if="field.type === 'checkbox'">
                                    <div class="neo-checkbox-group">
                                        <label class="neo-checkbox">
                                            <input
                                                type="checkbox"
                                                :name="field.slug"
                                                value="1"
                                                :data-context7-field="field.slug"
                                            >
                                            <span class="neo-checkbox-text" x-text="field.name"></span>
                                        </label>
                                    </div>
                                </template>

                                <p x-show="field.help" class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="field.help"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function matrixDynamicFields() {
    return {
        selectedKategori: null,
        selectedYayinTipi: null,
        fields: [],
        loading: false,

        get groupedFields() {
            const grouped = {};
            this.fields.forEach(field => {
                const cat = field.category || 'other';
                if (!grouped[cat]) {
                    grouped[cat] = [];
                }
                grouped[cat].push(field);
            });
            return Object.keys(grouped).map(cat => ({
                category: cat,
                fields: grouped[cat]
            }));
        },

        init() {
            console.log('🎯 Matrix Dynamic Fields initialized');

            // Listen for category changes
            window.addEventListener('category-changed', (e) => {
                this.handleCategoryChange(e.detail);
            });

            // Listen for yayin tipi changes
            document.addEventListener('change', (e) => {
                if (e.target.id === 'yayin_tipi_id') {
                    this.handleYayinTipiChange(e.target.value);
                }
            });

            // Initial load check
            this.checkInitialState();
        },

        checkInitialState() {
            const altKategori = document.getElementById('alt_kategori');
            const yayinTipi = document.getElementById('yayin_tipi_id');

            if (altKategori?.value) {
                const selectedOption = altKategori.options[altKategori.selectedIndex];
                this.selectedKategori = this.extractCategorySlug(selectedOption?.text || '');
            }

            if (yayinTipi?.value) {
                this.selectedYayinTipi = yayinTipi.value;
            }

            if (this.selectedKategori && this.selectedYayinTipi) {
                this.loadFields();
            }
        },

        handleCategoryChange(detail) {
            if (detail?.category) {
                // Wait for alt kategori update
                setTimeout(() => {
                    const altKategori = document.getElementById('alt_kategori');
                    if (altKategori?.value && altKategori.selectedIndex > 0) {
                        const selectedOption = altKategori.options[altKategori.selectedIndex];
                        this.selectedKategori = this.extractCategorySlug(selectedOption.text);
                        console.log('✅ Kategori updated:', this.selectedKategori);

                        if (this.selectedKategori && this.selectedYayinTipi) {
                            this.loadFields();
                        }
                    }
                }, 500);
            }
        },

        handleYayinTipiChange(value) {
            if (value) {
                const yayinTipiSelect = document.getElementById('yayin_tipi_id');
                const selectedOption = yayinTipiSelect?.options[yayinTipiSelect.selectedIndex];
                this.selectedYayinTipi = selectedOption?.text || value;
                console.log('✅ Yayın Tipi updated:', this.selectedYayinTipi);

                if (this.selectedKategori && this.selectedYayinTipi) {
                    this.loadFields();
                }
            }
        },

        async loadFields() {
            if (!this.selectedKategori || !this.selectedYayinTipi) {
                return;
            }

            this.loading = true;
            console.log('🔄 Loading fields for:', this.selectedKategori, this.selectedYayinTipi);

            try {
                const response = await fetch(
                    `/api/admin/field-dependency/get-matrix/${this.selectedKategori}`
                );

                if (!response.ok) {
                    throw new Error('Failed to load fields');
                }

                const data = await response.json();
                console.log('📦 Matrix data received:', data);

                if (data.success && data.matrix) {
                    const yayinTipiFields = data.matrix[this.selectedYayinTipi];
                    if (yayinTipiFields) {
                        this.fields = Object.values(yayinTipiFields)
                            .filter(f => f.enabled)
                            .map(f => this.transformField(f));
                        console.log(`✅ Loaded ${this.fields.length} fields`);
                    } else {
                        this.fields = [];
                        console.log('⚠️ No fields defined for this yayin tipi');
                    }
                }
            } catch (error) {
                console.error('❌ Failed to load fields:', error);
                this.fields = [];
            } finally {
                this.loading = false;
            }
        },

        transformField(field) {
            return {
                slug: field.field_slug,
                name: field.field_name,
                type: field.field_type,
                category: field.field_category,
                options: typeof field.field_options === 'string'
                    ? JSON.parse(field.field_options)
                    : field.field_options,
                placeholder: field.field_placeholder || '',
                help: field.field_help || '',
                required: field.required,
                enabled: field.enabled
            };
        },

        extractCategorySlug(text) {
            const slugMap = {
                'Arsa': 'arsa',
                'İmarlı Arsa': 'arsa',
                'Tarla': 'arsa',
                'Yazlık': 'yazlik',
                'Villa': 'konut',
                'Daire': 'konut',
                'Residence': 'konut',
                'Ofis': 'isyeri',
                'Dükkan': 'isyeri',
                'Fabrika': 'isyeri',
                'Depo': 'isyeri'
            };

            for (const [key, slug] of Object.entries(slugMap)) {
                if (text.includes(key)) {
                    return slug;
                }
            }

            return 'konut'; // Default
        },

        getCategoryLabel(category) {
            const labels = {
                'fiyat': '💰 Fiyat',
                'ozellik': '🔧 Özellikler',
                'arsa': '🏞️ Arsa',
                'sezonluk': '🏖️ Sezonluk',
                'other': '📋 Diğer'
            };
            return labels[category] || category;
        }
    }
}

// Export to window
window.matrixDynamicFields = matrixDynamicFields;
</script>
