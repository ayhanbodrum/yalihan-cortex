/**
 * SEZONLUK KİRALAMA TAKVİMİ SİSTEMİ
 * EmlakPro için gelişmiş sezonluk kiralama takvimi
 * Version: 2.0
 */

window.SeasonalCalendar = {
    // Takvim yapılandırması
    config: {
        seasons: {
            yaz: {
                label: 'Yaz Sezonu',
                icon: '☀️',
                months: [6, 7, 8], // Haziran, Temmuz, Ağustos
                color: '#f59e0b',
                multiplier: 1.5,
            },
            ilkbahar: {
                label: 'İlkbahar',
                icon: '🌸',
                months: [3, 4, 5], // Mart, Nisan, Mayıs
                color: '#10b981',
                multiplier: 1.2,
            },
            sonbahar: {
                label: 'Sonbahar',
                icon: '🍂',
                months: [9, 10, 11], // Eylül, Ekim, Kasım
                color: '#f97316',
                multiplier: 1.0,
            },
            kis: {
                label: 'Kış',
                icon: '❄️',
                months: [12, 1, 2], // Aralık, Ocak, Şubat
                color: '#3b82f6',
                multiplier: 0.8,
            },
        },
        monthNames: [
            'Ocak',
            'Şubat',
            'Mart',
            'Nisan',
            'Mayıs',
            'Haziran',
            'Temmuz',
            'Ağustos',
            'Eylül',
            'Ekim',
            'Kasım',
            'Aralık',
        ],
        dayNames: ['Pz', 'Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct'],
    },

    // Rezervasyon durumları
    reservations: {},
    blockedDates: [],
    specialPrices: {},

    // Initialize calendar
    init: function () {
        this.setupEventListeners();
        this.loadReservationData();
        console.log('SeasonalCalendar initialized');
    },

    // Event listeners
    setupEventListeners: function () {
        document.addEventListener('click', (e) => {
            // Takvim hücre tıklamaları
            if (e.target.classList.contains('calendar-day')) {
                this.handleDayClick(e.target);
            }

            // Sezon butonları
            if (e.target.classList.contains('season-btn')) {
                this.selectSeason(e.target.dataset.season);
            }
        });

        // Fiyat değişikliklerini dinle
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('season-price')) {
                this.updateSeasonalPricing();
            }
        });
    },

    // Takvimi render et
    renderCalendar: function (containerId, year = new Date().getFullYear()) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = this.generateCalendarHTML(year);
        this.attachCalendarEvents(container);
    },

    // Takvim HTML'i oluştur
    generateCalendarHTML: function (year) {
        let html = `
            <div class="seasonal-calendar">
                <div class="calendar-header mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">📅 ${year} Sezonluk Kiralama Takvimi</h3>
                        <div class="flex space-x-2">
                            <button onclick="SeasonalCalendar.renderCalendar('${containerId}', ${
                                year - 1
                            })"
                                    class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button onclick="SeasonalCalendar.renderCalendar('${containerId}', ${
                                year + 1
                            })"
                                    class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Sezon Seçimi -->
                    <div class="season-selector grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
                        ${this.generateSeasonButtons()}
                    </div>

                    <!-- Takvim Açıklaması -->
                    <div class="calendar-legend flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center"><div class="w-4 h-4 bg-green-200 rounded mr-2"></div>Müsait</div>
                        <div class="flex items-center"><div class="w-4 h-4 bg-red-200 rounded mr-2"></div>Rezerve</div>
                        <div class="flex items-center"><div class="w-4 h-4 bg-gray-200 rounded mr-2"></div>Bloklu</div>
                        <div class="flex items-center"><div class="w-4 h-4 bg-blue-200 rounded mr-2"></div>Özel Fiyat</div>
                    </div>
                </div>

                <div class="calendar-grid grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    ${this.generateMonthsHTML(year)}
                </div>

                <!-- Fiyatlandırma Paneli -->
                <div class="pricing-panel mt-6">
                    ${this.generatePricingPanel()}
                </div>
            </div>
        `;

        return html;
    },

    // Sezon butonlarını oluştur
    generateSeasonButtons: function () {
        return Object.keys(this.config.seasons)
            .map((seasonKey) => {
                const season = this.config.seasons[seasonKey];
                return `
                <button class="season-btn px-4 py-2 rounded-lg border transition-colors hover:bg-gray-50"
                        data-season="${seasonKey}"
                        style="border-color: ${season.color}">
                    <span class="mr-2">${season.icon}</span>
                    ${season.label}
                </button>
            `;
            })
            .join('');
    },

    // Ayları oluştur
    generateMonthsHTML: function (year) {
        return Array.from({ length: 12 }, (_, monthIndex) => {
            return this.generateMonthHTML(year, monthIndex);
        }).join('');
    },

    // Tek ay HTML'i oluştur
    generateMonthHTML: function (year, monthIndex) {
        const monthName = this.config.monthNames[monthIndex];
        const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
        const firstDay = new Date(year, monthIndex, 1).getDay();
        const season = this.getSeasonForMonth(monthIndex + 1);

        let html = `
            <div class="month-container border rounded-lg p-3">
                <div class="month-header text-center mb-2">
                    <h4 class="font-semibold" style="color: ${this.config.seasons[season].color}">
                        ${this.config.seasons[season].icon} ${monthName}
                    </h4>
                </div>

                <div class="days-grid grid grid-cols-7 gap-1 text-xs">
                    ${this.config.dayNames
                        .map(
                            (day) =>
                                `<div class="day-name text-center font-medium text-gray-500 p-1">${day}</div>`
                        )
                        .join('')}

                    ${Array.from({ length: firstDay }, () => '<div class="empty-day"></div>').join(
                        ''
                    )}

                    ${Array.from({ length: daysInMonth }, (_, dayIndex) => {
                        const day = dayIndex + 1;
                        const dateKey = `${year}-${String(monthIndex + 1).padStart(
                            2,
                            '0'
                        )}-${String(day).padStart(2, '0')}`;
                        const status = this.getDayStatus(dateKey);
                        return this.generateDayHTML(day, dateKey, status);
                    }).join('')}
                </div>
            </div>
        `;

        return html;
    },

    // Gün HTML'i oluştur
    generateDayHTML: function (day, dateKey, status) {
        const statusClasses = {
            available: 'bg-green-100 hover:bg-green-200 text-green-800',
            reserved: 'bg-red-100 text-red-800 cursor-not-allowed',
            blocked: 'bg-gray-100 text-gray-500 cursor-not-allowed',
            special: 'bg-blue-100 hover:bg-blue-200 text-blue-800',
        };

        return `
            <div class="calendar-day p-1 text-center rounded cursor-pointer transition-colors ${statusClasses[status]}"
                 data-date="${dateKey}">
                ${day}
            </div>
        `;
    },

    // Fiyatlandırma paneli oluştur
    generatePricingPanel: function () {
        return `
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold mb-4">💰 Sezonluk Fiyatlandırma</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    ${Object.keys(this.config.seasons)
                        .map((seasonKey) => {
                            const season = this.config.seasons[seasonKey];
                            return `
                            <div class="season-pricing">
                                <label class="block text-sm font-medium mb-2">
                                    ${season.icon} ${season.label}
                                </label>
                                <div class="relative">
                                    <input type="number"
                                           class="season-price w-full px-3 py-2 border rounded-md"
                                           data-season="${seasonKey}"
                                           placeholder="Günlük fiyat">
                                    <span class="absolute right-3 top-2 text-gray-500">₺</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Çarpan: ${season.multiplier}x
                                </div>
                            </div>
                        `;
                        })
                        .join('')}
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded">
                    <h5 class="font-medium mb-2">📊 Otomatik Fiyat Hesaplama</h5>
                    <div class="text-sm text-gray-600">
                        <p>• Yaz sezonu: Temel fiyat × 1.5</p>
                        <p>• İlkbahar: Temel fiyat × 1.2</p>
                        <p>• Sonbahar: Temel fiyat × 1.0</p>
                        <p>• Kış: Temel fiyat × 0.8</p>
                    </div>
                </div>
            </div>
        `;
    },

    // Ayın hangi sezonda olduğunu bul
    getSeasonForMonth: function (month) {
        for (const [seasonKey, season] of Object.entries(this.config.seasons)) {
            if (season.months.includes(month)) {
                return seasonKey;
            }
        }
        return 'sonbahar'; // varsayılan
    },

    // Günün durumunu getir
    getDayStatus: function (dateKey) {
        if (this.blockedDates.includes(dateKey)) return 'blocked';
        if (this.reservations[dateKey]) return 'reserved';
        if (this.specialPrices[dateKey]) return 'special';
        return 'available';
    },

    // Gün tıklama işlemi
    handleDayClick: function (dayElement) {
        const dateKey = dayElement.dataset.date;
        const currentStatus = this.getDayStatus(dateKey);

        if (currentStatus === 'reserved') {
            this.showReservationDetails(dateKey);
        } else if (currentStatus === 'available' || currentStatus === 'special') {
            this.showDateOptions(dateKey, dayElement);
        }
    },

    // Tarih seçeneklerini göster
    showDateOptions: function (dateKey, element) {
        const popup = document.createElement('div');
        popup.className =
            'date-options-popup absolute bg-white border rounded-lg shadow-lg p-3 z-50';
        popup.style.minWidth = '200px';

        popup.innerHTML = `
            <div class="mb-2 font-medium">${dateKey}</div>
            <div class="space-y-2">
                <button class="block w-full text-left px-2 py-1 hover:bg-gray-100 rounded"
                        onclick="SeasonalCalendar.blockDate('${dateKey}')">
                    🚫 Tarihi Blokla
                </button>
                <button class="block w-full text-left px-2 py-1 hover:bg-gray-100 rounded"
                        onclick="SeasonalCalendar.setSpecialPrice('${dateKey}')">
                    💰 Özel Fiyat Belirle
                </button>
                <button class="block w-full text-left px-2 py-1 hover:bg-gray-100 rounded"
                        onclick="SeasonalCalendar.addReservation('${dateKey}')">
                    📅 Rezervasyon Ekle
                </button>
            </div>
        `;

        // Popup pozisyonunu ayarla
        const rect = element.getBoundingClientRect();
        popup.style.top = rect.bottom + window.scrollY + 'px';
        popup.style.left = rect.left + 'px';

        document.body.appendChild(popup);

        // Dışarı tıklanınca kapat
        setTimeout(() => {
            document.addEventListener('click', function closePopup(e) {
                if (!popup.contains(e.target)) {
                    popup.remove();
                    document.removeEventListener('click', closePopup);
                }
            });
        }, 100);
    },

    // Tarihi blokla
    blockDate: function (dateKey) {
        if (!this.blockedDates.includes(dateKey)) {
            this.blockedDates.push(dateKey);
            this.saveData();
            this.refreshCalendar();
        }
    },

    // Özel fiyat belirle
    setSpecialPrice: function (dateKey) {
        const price = prompt('Bu tarih için özel fiyat (₺):');
        if (price && !isNaN(price)) {
            this.specialPrices[dateKey] = parseFloat(price);
            this.saveData();
            this.refreshCalendar();
        }
    },

    // Rezervasyon ekle
    addReservation: function (dateKey) {
        const guestName = prompt('Misafir adı:');
        if (guestName) {
            this.reservations[dateKey] = {
                guest: guestName,
                date: new Date().toISOString(),
            };
            this.saveData();
            this.refreshCalendar();
        }
    },

    // Sezonluk fiyatlandırmayı güncelle
    updateSeasonalPricing: function () {
        const basePriceInput = document.querySelector('[name="gunluk_fiyat"]');
        if (!basePriceInput) return;

        const basePrice = parseFloat(basePriceInput.value) || 0;

        document.querySelectorAll('.season-price').forEach((input) => {
            const season = input.dataset.season;
            const multiplier = this.config.seasons[season].multiplier;

            if (!input.value && basePrice > 0) {
                input.value = Math.round(basePrice * multiplier);
            }
        });
    },

    // Takvimi yenile
    refreshCalendar: function () {
        const container = document.querySelector('.seasonal-calendar');
        if (container) {
            const year = new Date().getFullYear(); // veya mevcut yıl
            container.outerHTML = this.generateCalendarHTML(year);
        }
    },

    // Verileri kaydet
    saveData: function () {
        const data = {
            reservations: this.reservations,
            blockedDates: this.blockedDates,
            specialPrices: this.specialPrices,
        };

        localStorage.setItem('seasonalCalendar_data', JSON.stringify(data));
    },

    // Verileri yükle
    loadReservationData: function () {
        const savedData = localStorage.getItem('seasonalCalendar_data');
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                this.reservations = data.reservations || {};
                this.blockedDates = data.blockedDates || [];
                this.specialPrices = data.specialPrices || {};
            } catch (e) {
                console.warn('Calendar data could not be loaded:', e);
            }
        }
    },

    // Sezonluk istatistikleri hesapla
    calculateSeasonStats: function () {
        const stats = {};

        Object.keys(this.config.seasons).forEach((season) => {
            stats[season] = {
                totalDays: 0,
                reservedDays: 0,
                blockedDays: 0,
                availableDays: 0,
                revenue: 0,
            };
        });

        // İstatistikleri hesapla...
        return stats;
    },
};

// DOM yüklendiğinde initialize et
document.addEventListener('DOMContentLoaded', function () {
    window.SeasonalCalendar.init();
});

// Global fonksiyonlar
window.renderSeasonalCalendar = function (containerId, year) {
    window.SeasonalCalendar.renderCalendar(containerId, year);
};

window.updateSeasonalPrices = function () {
    window.SeasonalCalendar.updateSeasonalPricing();
};
