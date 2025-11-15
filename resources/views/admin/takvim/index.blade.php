@extends('admin.layouts.neo')

@section('title', 'Kiralama Takvimi - ' . $ilan->baslik)

@section('content')
    <div class="container mx-auto px-6 py-6">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kiralama Takvimi</h1>
                    <p class="text-gray-600 mt-2">{{ $ilan->baslik }} - Rezervasyon yönetimi</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="oncekiAy()" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Önceki Ay
                    </button>
                    <button onclick="sonrakiAy()" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                        Sonraki Ay
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button onclick="buguneGit()" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Bugüne Git
                    </button>
                    <button onclick="buAyaGit()" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                        Bu Aya Git
                    </button>
                </div>
            </div>
        </div>

    @section('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

    @endsection

    @section('content')
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            📅 Kiralama Takvimi
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">
                            {{ $ilan->baslik }} - {{ $ilan->emlak_turu }}
                        </p>
                    </div>

                    <div class="flex space-x-3">
                        <button onclick="topluGuncelleModal()" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                            <i class="fas fa-edit mr-2"></i>
                            Toplu Güncelle
                        </button>

                        <button onclick="rezervasyonEkleModal()" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-plus mr-2"></i>
                            Rezervasyon Ekle
                        </button>
                    </div>
                </div>

                <!-- Takvim Kontrolleri -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Ay/Yıl Seçimi -->
                        <div class="flex items-center space-x-4">
                            <button onclick="oncekiAy()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                                <i class="fas fa-chevron-left"></i>
                            </button>

                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200" id="currentMonth">
                                {{ \Carbon\Carbon::now()->format('F Y') }}
                            </h2>

                            <button onclick="sonrakiAy()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Hızlı Navigasyon -->
                        <div class="flex items-center space-x-2">
                            <button onclick="buguneGit()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                                Bugün
                            </button>
                            <button onclick="buAyaGit()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                                Bu Ay
                            </button>
                        </div>

                        <!-- Filtreler -->
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="showPrices" checked class="form-checkbox">
                                <span class="ml-2 text-sm">Fiyatları Göster</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" id="showSeasons" checked class="form-checkbox">
                                <span class="ml-2 text-sm">Sezonları Göster</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Takvim -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div id="calendar" class="grid grid-cols-7 gap-1">
                        <!-- Gün başlıkları -->
                        <div class="text-center font-semibold text-gray-600 dark:text-gray-400 p-2">Pzt</div>
                        <div class="text-center font-semibold text-gray-600 dark:text-gray-400 p-2">Sal</div>
                        <div class="text-center font-semibold text-gray-600 dark:text-gray-400 p-2">Çar</div>
                        <div class="text-center font-semibold text-gray-600 dark:text-gray-400 p-2">Per</div>
                        <div class="text-center font-semibold text-gray-600 dark:text-gray-400 p-2">Cum</div>
                        <div class="text-center font-semibold text-gray-600 dark:text-gray-400 p-2">Cmt</div>
                        <div class="text-center font-semibold text-gray-600 dark:text-gray-400 p-2">Paz</div>

                        <!-- Takvim günleri buraya JavaScript ile eklenecek -->
                    </div>
                </div>

                <!-- İstatistikler -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-calendar-check text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Müsait Günler</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="musaitGunler">0</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <i class="fas fa-calendar-times text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rezerve Günler</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="rezerveGunler">0</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ortalama Fiyat</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="ortalamaFiyat">₺0</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-percentage text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Doluluk Oranı</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="dolulukOrani">0%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toplu Güncelleme Modal -->
        <div id="topluGuncelleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Toplu Tarih Güncelleme
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Tarih Aralığı
                                </label>
                                <input type="text" id="topluTarihAraligi" class="admin-input w-full"
                                    placeholder="Tarih aralığı seçin">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Durum
                                </label>
                                <select style="color-scheme: light dark;" id="topluDurum" class="admin-input w-full transition-all duration-200">
                                    <option value="musait">Müsait</option>
                                    <option value="rezerve">Rezerve</option>
                                    <option value="bakimda">Bakımda</option>
                                    <option value="kapali">Kapalı</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Günlük Fiyat (₺)
                                </label>
                                <input type="number" id="topluFiyat" class="admin-input w-full"
                                    placeholder="Fiyat girin">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Not
                                </label>
                                <textarea id="topluNot" class="admin-input w-full" rows="3" placeholder="Özel not ekleyin"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button onclick="topluGuncelleModalKapat()" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                                İptal
                            </button>
                            <button onclick="topluGuncelleUygula()" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                                Uygula
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rezervasyon Ekleme Modal -->
        <div id="rezervasyonEkleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Yeni Rezervasyon
                        </h3>

                        <form id="rezervasyonForm" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Giriş Tarihi
                                    </label>
                                    <input type="date" id="rezervasyonGiris" class="admin-input w-full" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Çıkış Tarihi
                                    </label>
                                    <input type="date" id="rezervasyonCikis" class="admin-input w-full" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Müşteri Adı
                                </label>
                                <input type="text" id="rezervasyonMusteri" class="admin-input w-full" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Telefon
                                    </label>
                                    <input type="tel" id="rezervasyonTelefon" class="admin-input w-full" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Email
                                    </label>
                                    <input type="email" id="rezervasyonEmail" class="admin-input w-full">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Not
                                </label>
                                <textarea id="rezervasyonNot" class="admin-input w-full" rows="3" placeholder="Özel not ekleyin"></textarea>
                            </div>
                        </form>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button onclick="rezervasyonEkleModalKapat()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 touch-target-optimized touch-target-optimized">
                                İptal
                            </button>
                            <button onclick="rezervasyonEkleKaydet()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg touch-target-optimized touch-target-optimized">
                                Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js"></script>
        <script>
            let currentDate = new Date();
            let calendarData = {};

            // Takvim başlatma
            document.addEventListener('DOMContentLoaded', function() {
                initializeCalendar();
                loadCalendarData();

                // Flatpickr başlat
                flatpickr("#topluTarihAraligi", {
                    mode: "range",
                    locale: "tr",
                    dateFormat: "Y-m-d",
                    placeholder: "Tarih aralığı seçin"
                });

                flatpickr("#rezervasyonGiris", {
                    locale: "tr",
                    dateFormat: "Y-m-d",
                    minDate: "today"
                });

                flatpickr("#rezervasyonCikis", {
                    locale: "tr",
                    dateFormat: "Y-m-d",
                    minDate: "today"
                });
            });

            // Takvim başlatma
            function initializeCalendar() {
                renderCalendar();
            }

            // Takvim render
            function renderCalendar() {
                const calendar = document.getElementById('calendar');
                const currentMonth = document.getElementById('currentMonth');

                // Ay adını güncelle
                currentMonth.textContent = currentDate.toLocaleDateString('tr-TR', {
                    month: 'long',
                    year: 'numeric'
                });

                // Gün başlıklarından sonraki tüm içeriği temizle
                const dayHeaders = calendar.querySelectorAll('div:nth-child(-n+7)');
                calendar.innerHTML = '';
                dayHeaders.forEach(header => calendar.appendChild(header));

                // Ayın ilk günü
                const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

                // Haftanın ilk günü (Pazartesi = 1)
                const firstDayOfWeek = firstDay.getDay() === 0 ? 7 : firstDay.getDay();

                // Boş günler ekle
                for (let i = 1; i < firstDayOfWeek; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'takvim-gun bg-gray-100 dark:bg-gray-800';
                    calendar.appendChild(emptyDay);
                }

                // Ayın günlerini ekle
                for (let day = 1; day <= lastDay.getDate(); day++) {
                    const date = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
                    const dayElement = createDayElement(date, day);
                    calendar.appendChild(dayElement);
                }
            }

            // Gün elementi oluştur
            function createDayElement(date, day) {
                const dayElement = document.createElement('div');
                dayElement.className = 'takvim-gun';
                dayElement.setAttribute('data-date', date.toISOString().split('T')[0]);

                // Gün numarası
                const dayNumber = document.createElement('div');
                dayNumber.className = 'text-lg font-semibold text-gray-800 dark:text-gray-200 p-2';
                dayNumber.textContent = day;
                dayElement.appendChild(dayNumber);

                // Geçmiş gün kontrolü
                if (date < new Date().setHours(0, 0, 0, 0)) {
                    dayElement.classList.add('gecmis');
                    dayElement.style.opacity = '0.5';
                    dayElement.style.cursor = 'not-allowed';
                } else {
                    dayElement.addEventListener('click', () => gunTikla(date));
                }

                // Takvim verisi varsa göster
                const dateStr = date.toISOString().split('T')[0];
                if (calendarData[dateStr]) {
                    updateDayElement(dayElement, calendarData[dateStr]);
                }

                return dayElement;
            }

            // Gün elementini güncelle
            function updateDayElement(dayElement, data) {
                // Durum sınıfını ekle
                dayElement.className = `takvim-gun ${data.status}`;

                // Fiyat badge'i
                if (data.gunluk_fiyat && document.getElementById('showPrices').checked) {
                    const priceBadge = document.createElement('div');
                    priceBadge.className = 'fiyat-badge';
                    priceBadge.textContent = `₺${data.gunluk_fiyat}`;
                    dayElement.appendChild(priceBadge);
                }

                // Minimum gece bilgisi
                if (data.minimum_gece && data.minimum_gece > 1) {
                    const minBadge = document.createElement('div');
                    minBadge.className = 'minimum-gece';
                    minBadge.textContent = `Min ${data.minimum_gece}`;
                    dayElement.appendChild(minBadge);
                }

                // Sezon bilgisi
                if (data.sezon && document.getElementById('showSeasons').checked) {
                    const seasonIndicator = document.createElement('div');
                    seasonIndicator.className = `sezon-indicator sezon-${data.sezon}`;
                    dayElement.appendChild(seasonIndicator);
                }
            }

            // Takvim verilerini yükle
            async function loadCalendarData() {
                try {
                    const response = await fetch(`/admin/ilanlar/${@json($ilan->id)}/takvim/api`);
                    const data = await response.json();

                    if (data.success) {
                        calendarData = data.data;
                        updateStatistics();
                    }
                } catch (error) {
                    console.error('Takvim verisi yüklenemedi:', error);
                }
            }

            // İstatistikleri güncelle
            function updateStatistics() {
                let musait = 0,
                    rezerve = 0,
                    toplamFiyat = 0,
                    fiyatliGun = 0;

                Object.values(calendarData).forEach(day => {
                    if (day.status === 'musait') musait++;
                    if (day.status === 'rezerve') rezerve++;
                    if (day.gunluk_fiyat) {
                        toplamFiyat += parseFloat(day.gunluk_fiyat);
                        fiyatliGun++;
                    }
                });

                const toplamGun = Object.keys(calendarData).length;
                const dolulukOrani = toplamGun > 0 ? Math.round((rezerve / toplamGun) * 100) : 0;
                const ortalamaFiyat = fiyatliGun > 0 ? Math.round(toplamFiyat / fiyatliGun) : 0;

                document.getElementById('musaitGunler').textContent = musait;
                document.getElementById('rezerveGunler').textContent = rezerve;
                document.getElementById('ortalamaFiyat').textContent = `₺${ortalamaFiyat}`;
                document.getElementById('dolulukOrani').textContent = `${dolulukOrani}%`;
            }

            // Navigasyon fonksiyonları
            function oncekiAy() {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
                loadCalendarData();
            }

            function sonrakiAy() {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
                loadCalendarData();
            }

            function buguneGit() {
                currentDate = new Date();
                renderCalendar();
                loadCalendarData();
            }

            function buAyaGit() {
                currentDate = new Date();
                renderCalendar();
                loadCalendarData();
            }

            // Modal fonksiyonları
            function topluGuncelleModal() {
                document.getElementById('topluGuncelleModal').classList.remove('hidden');
            }

            function topluGuncelleModalKapat() {
                document.getElementById('topluGuncelleModal').classList.add('hidden');
            }

            function rezervasyonEkleModal() {
                document.getElementById('rezervasyonEkleModal').classList.remove('hidden');
            }

            function rezervasyonEkleModalKapat() {
                document.getElementById('rezervasyonEkleModal').classList.add('hidden');
            }

            // Gün tıklama
            function gunTikla(date) {
                const dateStr = date.toISOString().split('T')[0];
                const dayData = calendarData[dateStr] || {};

                // Gün detay modal'ı açılabilir
                console.log('Tıklanan gün:', dateStr, dayData);
            }

            // Toplu güncelleme
            async function topluGuncelleUygula() {
                const tarihAraligi = document.getElementById('topluTarihAraligi').value;
                const status = document.getElementById('topluDurum').value;
                const fiyat = document.getElementById('topluFiyat').value;
                const not = document.getElementById('topluNot').value;

                if (!tarihAraligi) {
                    alert('Lütfen tarih aralığı seçin');
                    return;
                }

                try {
                    const response = await fetch(`/admin/ilanlar/${@json($ilan->id)}/takvim/toplu-guncelle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            tarih_araligi: tarihAraligi,
                            status: status,
                            fiyat: fiyat,
                            not: not
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        topluGuncelleModalKapat();
                        loadCalendarData();
                        alert('Toplu güncelleme başarılı');
                    } else {
                        alert('Hata: ' + result.message);
                    }
                } catch (error) {
                    console.error('Toplu güncelleme hatası:', error);
                    alert('Güncelleme sırasında hata oluştu');
                }
            }

            // Rezervasyon ekleme
            async function rezervasyonEkleKaydet() {
                const form = document.getElementById('rezervasyonForm');
                const formData = new FormData(form);

                try {
                    const response = await fetch(`/admin/ilanlar/${@json($ilan->id)}/takvim/rezervasyon`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        rezervasyonEkleModalKapat();
                        loadCalendarData();
                        alert('Rezervasyon başarıyla eklendi');
                    } else {
                        alert('Hata: ' + result.message);
                    }
                } catch (error) {
                    console.error('Rezervasyon ekleme hatası:', error);
                    alert('Rezervasyon eklenirken hata oluştu');
                }
            }

            // Filtre değişikliklerini dinle
            document.getElementById('showPrices').addEventListener('change', renderCalendar);
            document.getElementById('showSeasons').addEventListener('change', renderCalendar);
        </script>
    @endsection
