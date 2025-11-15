/**
 * Context7 Live Search - Basit ve Hafif (3KB!)
 * Kişi ve Site araması için
 *
 * AVANTAJLAR:
 * - Vanilla JS (bağımlılık yok)
 * - 3KB (React-Select: 170KB!)
 * - Context7 uyumlu
 * - Tailwind ile uyumlu
 */

class Context7LiveSearch {
    constructor(element) {
        this.element = element;
        this.searchType = element.dataset.searchType; // 'kisiler' or 'sites'
        this.input = element.querySelector('input[type="text"]');
        this.hiddenInput = element.querySelector('input[type="hidden"]');
        this.resultsContainer = element.querySelector('.context7-search-results');

        this.debounceTimer = null;
        this.minChars = 2;
        this.maxResults = parseInt(element.dataset.maxResults) || 20;

        this.init();
    }

    init() {
        if (!this.input) {
            console.error('❌ Context7 Live Search: Input element bulunamadı!', this.element);
            return;
        }

        if (!this.hiddenInput) {
            console.error('❌ Context7 Live Search: Hidden input bulunamadı!', this.element);
            return;
        }

        if (!this.resultsContainer) {
            console.error('❌ Context7 Live Search: Results container bulunamadı!', this.element);
            return;
        }

        console.log('✅ Context7 Live Search initialized:', {
            searchType: this.searchType,
            inputId: this.input.id,
            hiddenInputId: this.hiddenInput.id,
        });

        // Arama eventi
        this.input.addEventListener('input', (e) => this.handleSearch(e.target.value));

        // Dışarı tıklayınca kapat
        document.addEventListener('click', (e) => {
            if (!this.element.contains(e.target)) {
                this.hideResults();
            }
        });

        // Focus'ta mevcut değeri göster
        this.input.addEventListener('focus', () => {
            if (this.input.value.length >= this.minChars) {
                this.search(this.input.value);
            }
        });
    }

    handleSearch(query) {
        clearTimeout(this.debounceTimer);

        if (query.length < this.minChars) {
            this.hideResults();
            return;
        }

        // Debounce 300ms
        this.debounceTimer = setTimeout(() => {
            this.search(query);
        }, 300);
    }

    async search(query) {
        const url = `/api/${this.searchType}/search?q=${encodeURIComponent(query)}&limit=${this.maxResults}`;
        console.log('🔍 Context7 Live Search:', {
            searchType: this.searchType,
            query: query,
            url: url,
        });

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            console.log('📡 Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('✅ API Response:', data);

            if (data.success) {
                // ✅ Context7: data.data her zaman array olmalı
                const results = Array.isArray(data.data) ? data.data : [];
                console.log('📊 Results count:', results.length);
                this.renderResults(results);
            } else {
                console.error('❌ API Error:', data.message);
                this.showError(data.message || 'Arama başarısız');
            }
        } catch (error) {
            console.error('❌ Arama hatası:', error);
            this.showError('Arama sırasında hata oluştu: ' + error.message);
        }
    }

    renderResults(results) {
        console.log('🎨 Rendering results:', results);

        // ✅ Context7: results her zaman array olmalı
        if (!Array.isArray(results)) {
            console.error('❌ Results is not an array:', typeof results);
            results = [];
        }

        if (!results || results.length === 0) {
            this.resultsContainer.innerHTML = `
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="text-sm">Sonuç bulunamadı</p>
                </div>
            `;
            this.showResults();
            return;
        }

        let html = '';
        results.forEach((result, index) => {
            // Context7: Kişi, Site, İlan için dinamik gösterim
            // Fallback: text yoksa ad + soyad + telefon oluştur
            const displayText =
                result.text ||
                (result.ad && result.soyad
                    ? `${result.ad} ${result.soyad}${result.telefon ? ' - ' + result.telefon : ''}`
                    : result.name || result.baslik || 'İsimsiz');

            const subtitle = result.kisi_tipi
                ? `📋 ${result.kisi_tipi}`
                : result.daire_sayisi
                  ? `🏢 ${result.daire_sayisi} daire`
                  : result.kategori
                    ? `🏷️ ${result.kategori} - ${result.fiyat}`
                    : '';

            html += `
                <div class="context7-result-item px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0 transition-colors duration-200"
                    data-id="${result.id}"
                    data-text="${displayText}">
                    <div class="font-medium text-gray-900 dark:text-gray-100">${displayText}</div>
                    ${
                        subtitle
                            ? `<div class="text-xs text-gray-500 dark:text-gray-400 mt-1">${subtitle}</div>`
                            : ''
                    }
                </div>
            `;
        });

        this.resultsContainer.innerHTML = html;
        this.showResults();

        // Sonuç tıklama
        this.resultsContainer.querySelectorAll('.context7-result-item').forEach((item) => {
            item.addEventListener('click', () => {
                console.log('✅ Result selected:', {
                    id: item.dataset.id,
                    text: item.dataset.text,
                });
                this.selectResult(item);
            });
        });
    }

    selectResult(item) {
        const id = item.dataset.id;
        const text = item.dataset.text;

        if (!id || !text) {
            console.error('❌ Invalid result data:', { id, text });
            return;
        }

        this.hiddenInput.value = id;
        this.input.value = text;
        this.hideResults();

        // Input change event'ini tetikle (Alpine.js reactivity için)
        this.input.dispatchEvent(new Event('change', { bubbles: true }));
        this.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));

        // Toast bildirim
        if (window.toast?.success) {
            window.toast.success('Seçildi: ' + text);
        } else {
            console.log('✅ Selected:', text);
        }
    }

    showResults() {
        this.resultsContainer.classList.remove('hidden');
    }

    hideResults() {
        this.resultsContainer.classList.add('hidden');
    }

    showError(message) {
        this.resultsContainer.innerHTML = `
            <div class="p-4 text-center text-red-500">
                <i class="fas fa-exclamation-circle mb-2"></i>
                <p class="text-sm">${message}</p>
            </div>
        `;
        this.showResults();
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Context7 Live Search: DOMContentLoaded fired');

    const searchElements = document.querySelectorAll('.context7-live-search');
    console.log('🔍 Found', searchElements.length, 'live search elements');

    searchElements.forEach((element, index) => {
        try {
            console.log(`📝 Initializing search element ${index + 1}:`, {
                searchType: element.dataset.searchType,
                hasInput: !!element.querySelector('input[type="text"]'),
                hasHiddenInput: !!element.querySelector('input[type="hidden"]'),
                hasResultsContainer: !!element.querySelector('.context7-search-results'),
            });

            new Context7LiveSearch(element);
        } catch (error) {
            console.error(`❌ Error initializing search element ${index + 1}:`, error);
        }
    });

    console.log('✅ Context7 Live Search initialization complete');
});

// Fallback: Eğer DOMContentLoaded zaten geçtiyse, hemen çalıştır
if (document.readyState === 'loading') {
    // DOMContentLoaded bekleniyor, yukarıdaki kod çalışacak
} else {
    // DOMContentLoaded zaten geçti, hemen çalıştır
    console.log('⚠️ DOMContentLoaded already fired, initializing immediately');
    document.querySelectorAll('.context7-live-search').forEach((element) => {
        try {
            new Context7LiveSearch(element);
        } catch (error) {
            console.error('❌ Error initializing search element:', error);
        }
    });
}
