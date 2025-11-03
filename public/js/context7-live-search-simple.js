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
        this.resultsContainer = element.querySelector(
            ".context7-search-results"
        );

        this.debounceTimer = null;
        this.minChars = 2;
        this.maxResults = parseInt(element.dataset.maxResults) || 20;

        this.init();
    }

    init() {
        // Arama eventi
        this.input.addEventListener("input", (e) =>
            this.handleSearch(e.target.value)
        );

        // Dışarı tıklayınca kapat
        document.addEventListener("click", (e) => {
            if (!this.element.contains(e.target)) {
                this.hideResults();
            }
        });

        // Focus'ta mevcut değeri göster
        this.input.addEventListener("focus", () => {
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
        try {
            const response = await fetch(
                `/api/${this.searchType}/search?q=${encodeURIComponent(
                    query
                )}&limit=${this.maxResults}`
            );
            const data = await response.json();

            if (data.success) {
                this.renderResults(data.data);
            } else {
                this.showError(data.message || "Arama başarısız");
            }
        } catch (error) {
            console.error("Arama hatası:", error);
            this.showError("Arama sırasında hata oluştu");
        }
    }

    renderResults(results) {
        if (!results || results.length === 0) {
            this.resultsContainer.innerHTML = `
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-search mb-2"></i>
                    <p class="text-sm">Sonuç bulunamadı</p>
                </div>
            `;
            this.showResults();
            return;
        }

        let html = "";
        results.forEach((result) => {
            // Context7: Kişi, Site, İlan için dinamik gösterim
            const subtitle = result.kisi_tipi
                ? `📋 ${result.kisi_tipi}`
                : result.daire_sayisi
                ? `🏢 ${result.daire_sayisi} daire`
                : result.kategori
                ? `🏷️ ${result.kategori} - ${result.fiyat}`
                : "";

            html += `
                <div class="context7-result-item px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0"
                    data-id="${result.id}"
                    data-text="${result.text}">
                    <div class="font-medium text-gray-900 dark:text-gray-100">${
                        result.text
                    }</div>
                    ${
                        subtitle
                            ? `<div class="text-xs text-gray-500 dark:text-gray-400">${subtitle}</div>`
                            : ""
                    }
                </div>
            `;
        });

        this.resultsContainer.innerHTML = html;
        this.showResults();

        // Sonuç tıklama
        this.resultsContainer
            .querySelectorAll(".context7-result-item")
            .forEach((item) => {
                item.addEventListener("click", () => this.selectResult(item));
            });
    }

    selectResult(item) {
        const id = item.dataset.id;
        const text = item.dataset.text;

        this.hiddenInput.value = id;
        this.input.value = text;
        this.hideResults();

        // Toast bildirim
        if (window.toast) {
            window.toast.success("Seçildi: " + text);
        }
    }

    showResults() {
        this.resultsContainer.classList.remove("hidden");
    }

    hideResults() {
        this.resultsContainer.classList.add("hidden");
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
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".context7-live-search").forEach((element) => {
        new Context7LiveSearch(element);
    });

    console.log("✅ Context7 Live Search initialized");
});
