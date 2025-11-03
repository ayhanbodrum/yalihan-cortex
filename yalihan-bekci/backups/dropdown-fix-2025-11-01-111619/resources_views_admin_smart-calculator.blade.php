@extends('admin.layouts.neo')

@section('title', 'Akıllı Hesaplayıcı')

@section('content')
    <div class="content-header mb-8">
        <div class="container-fluid">
            <div class="flex justify-between items-center">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        Akıllı Hesaplayıcı
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Emlak sektörü için gerekli tüm hesaplamaları kolayca yapın.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Hesaplama Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Konut Kredisi Hesaplama -->
            <div
                class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-blue-800">Konut Kredisi</h3>
                    </div>
                    <p class="text-sm text-blue-600 mb-4">Kredi tutarı, vade ve faiz oranına göre aylık taksit hesaplama</p>
                    <button onclick="openCalculator('mortgage')" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>
                </div>
            </div>

            <!-- Tapu Harcı Hesaplama -->
            <div
                class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 shadow-sm hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-green-800">Tapu Harcı</h3>
                    </div>
                    <p class="text-sm text-green-600 mb-4">Emlak değerine göre tapu harcı ve vergi hesaplama</p>
                    <button onclick="openCalculator('deed')" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>
                </div>
            </div>

            <!-- Gelir Vergisi Hesaplama -->
            <div
                class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-200 shadow-sm hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-purple-800">Gelir Vergisi</h3>
                    </div>
                    <p class="text-sm text-purple-600 mb-4">Kira geliri ve emlak satışı gelir vergisi hesaplama</p>
                    <button onclick="openCalculator('income-tax')" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>
                </div>
            </div>

            <!-- Kira Vergisi Hesaplama -->
            <div
                class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl border border-orange-200 shadow-sm hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-orange-800">Kira Vergisi</h3>
                    </div>
                    <p class="text-sm text-orange-600 mb-4">Kira geliri üzerinden alınan vergi hesaplama</p>
                    <button onclick="openCalculator('rent-tax')" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>
                </div>
            </div>

            <!-- Kira Artış Oranı Hesaplama -->
            <div
                class="bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl border border-teal-200 shadow-sm hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-teal-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-teal-800">Kira Artış Oranı</h3>
                    </div>
                    <p class="text-sm text-teal-600 mb-4">TÜFE oranına göre kira artış hesaplama</p>
                    <button onclick="openCalculator('rent-increase')" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>
                </div>
            </div>

            <!-- Yatırım Getirisi Hesaplama -->
            <div
                class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl border border-yellow-200 shadow-sm hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-yellow-800">Yatırım Getirisi</h3>
                    </div>
                    <p class="text-sm text-yellow-600 mb-4">ROI ve yıllık getiri oranı hesaplama</p>
                    <button onclick="openCalculator('roi')" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>
                </div>
            </div>
        </div>

        <!-- Son Hesaplamalar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    📊 Son Hesaplamalar
                </h2>
                <div id="recent-calculations" class="space-y-3">
                    <div class="text-center text-gray-500 py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Henüz hesaplama yapılmadı</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hesaplayıcı Modal -->
    <div id="calculator-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 id="modal-title" class="text-xl font-bold text-gray-800 dark:text-gray-200">Hesaplayıcı</h3>
                        <button onclick="closeCalculator()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div id="calculator-content">
                        <!-- Hesaplayıcı içeriği buraya gelecek -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Button Standartları */
        .neo-btn neo-btn-primary {
            @apply inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95;
        }

        /* Hover Efektleri */
        .hover\:shadow-lg:hover {
            @apply shadow-lg;
        }

        /* Transition */
        .transition-shadow {
            @apply transition-all duration-200;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let currentCalculator = null;
        let calculations = [];

        function openCalculator(type) {
            currentCalculator = type;
            const modal = document.getElementById('calculator-modal');
            const content = document.getElementById('calculator-content');
            const title = document.getElementById('modal-title');

            // Hesaplayıcı içeriğini yükle
            switch (type) {
                case 'mortgage':
                    title.textContent = 'Konut Kredisi Hesaplama';
                    content.innerHTML = getMortgageCalculator();
                    break;
                case 'deed':
                    title.textContent = 'Tapu Harcı Hesaplama';
                    content.innerHTML = getDeedCalculator();
                    break;
                case 'income-tax':
                    title.textContent = 'Gelir Vergisi Hesaplama';
                    content.innerHTML = getIncomeTaxCalculator();
                    break;
                case 'rent-tax':
                    title.textContent = 'Kira Vergisi Hesaplama';
                    content.innerHTML = getRentTaxCalculator();
                    break;
                case 'rent-increase':
                    title.textContent = 'Kira Artış Oranı Hesaplama';
                    content.innerHTML = getRentIncreaseCalculator();
                    break;
                case 'roi':
                    title.textContent = 'Yatırım Getirisi Hesaplama';
                    content.innerHTML = getROICalculator();
                    break;
            }

            modal.classList.remove('hidden');
        }

        function closeCalculator() {
            document.getElementById('calculator-modal').classList.add('hidden');
            currentCalculator = null;
        }

        function getMortgageCalculator() {
            return `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kredi Tutarı (TL)</label>
                            <input type="number" id="loan-amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="500000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vade (Ay)</label>
                            <input type="number" id="loan-term" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="120">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Yıllık Faiz Oranı (%)</label>
                            <input type="number" id="interest-rate" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi</label>
                            <input type="date" id="start-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>

                    <button onclick="calculateMortgage()" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>

                    <div id="mortgage-result" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <!-- Sonuçlar buraya gelecek -->
                    </div>
                </div>
            `;
        }

        function getDeedCalculator() {
            return `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Emlak Değeri (TL)</label>
                            <input type="number" id="property-value" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="1000000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">İşlem Türü</label>
                            <select id="transaction-type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="sale">Satış</option>
                                <option value="purchase">Alım</option>
                                <option value="inheritance">Miras</option>
                                <option value="donation">Bağış</option>
                            </select>
                        </div>
                    </div>

                    <button onclick="calculateDeed()" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>

                    <div id="deed-result" class="hidden bg-green-50 border border-green-200 rounded-lg p-4">
                        <!-- Sonuçlar buraya gelecek -->
                    </div>
                </div>
            `;
        }

        function getIncomeTaxCalculator() {
            return `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Yıllık Gelir (TL)</label>
                            <input type="number" id="annual-income" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="120000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gelir Türü</label>
                            <select id="income-type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="rental">Kira Geliri</option>
                                <option value="sale">Emlak Satış Geliri</option>
                                <option value="other">Diğer Gelir</option>
                            </select>
                        </div>
                    </div>

                    <button onclick="calculateIncomeTax()" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>

                    <div id="income-tax-result" class="hidden bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <!-- Sonuçlar buraya gelecek -->
                    </div>
                </div>
            `;
        }

        function getRentTaxCalculator() {
            return `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Aylık Kira Geliri (TL)</label>
                            <input type="number" id="monthly-rent" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="5000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Yıl</label>
                            <input type="number" id="tax-year" class="w-full px-3 py-2 border border-gray-300 rounded-lg" value="2024">
                        </div>
                    </div>

                    <button onclick="calculateRentTax()" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>

                    <div id="rent-tax-result" class="hidden bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <!-- Sonuçlar buraya gelecek -->
                    </div>
                </div>
            `;
        }

        function getRentIncreaseCalculator() {
            return `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mevcut Kira (TL)</label>
                            <input type="number" id="current-rent" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="5000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">TÜFE Oranı (%)</label>
                            <input type="number" id="tufe-rate" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="25.5">
                        </div>
                    </div>

                    <button onclick="calculateRentIncrease()" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>

                    <div id="rent-increase-result" class="hidden bg-teal-50 border border-teal-200 rounded-lg p-4">
                        <!-- Sonuçlar buraya gelecek -->
                    </div>
                </div>
            `;
        }

        function getROICalculator() {
            return `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Yatırım Tutarı (TL)</label>
                            <input type="number" id="investment-amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="1000000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Yıllık Gelir (TL)</label>
                            <input type="number" id="annual-return" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="60000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Yatırım Süresi (Yıl)</label>
                            <input type="number" id="investment-period" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Satış Değeri (TL)</label>
                            <input type="number" id="sale-value" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="1200000">
                        </div>
                    </div>

                    <button onclick="calculateROI()" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Hesapla
                    </button>

                    <div id="roi-result" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <!-- Sonuçlar buraya gelecek -->
                    </div>
                </div>
            `;
        }

        // Hesaplama fonksiyonları
        function calculateMortgage() {
            const amount = parseFloat(document.getElementById('loan-amount').value);
            const term = parseInt(document.getElementById('loan-term').value);
            const rate = parseFloat(document.getElementById('interest-rate').value);

            if (!amount || !term || !rate) {
                alert('Lütfen tüm alanları doldurun');
                return;
            }

            const monthlyRate = rate / 100 / 12;
            const monthlyPayment = amount * (monthlyRate * Math.pow(1 + monthlyRate, term)) / (Math.pow(1 + monthlyRate,
                term) - 1);
            const totalPayment = monthlyPayment * term;
            const totalInterest = totalPayment - amount;

            const result = `
                <h4 class="font-semibold text-blue-800 mb-3">Hesaplama Sonuçları</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p><strong>Aylık Taksit:</strong> ${monthlyPayment.toLocaleString('tr-TR', {style: 'currency', currency: 'TRY'})}</p>
                        <p><strong>Toplam Ödeme:</strong> ${totalPayment.toLocaleString('tr-TR', {style: 'currency', currency: 'TRY'})}</p>
                    </div>
                    <div>
                        <p><strong>Toplam Faiz:</strong> ${totalInterest.toLocaleString('tr-TR', {style: 'currency', currency: 'TRY'})}</p>
                        <p><strong>Faiz Oranı:</strong> ${((totalInterest / amount) * 100).toFixed(2)}%</p>
                    </div>
                </div>
            `;

            document.getElementById('mortgage-result').innerHTML = result;
            document.getElementById('mortgage-result').classList.remove('hidden');

            // Hesaplamayı kaydet
            saveCalculation('Konut Kredisi', {
                amount: amount,
                term: term,
                rate: rate,
                monthlyPayment: monthlyPayment,
                totalPayment: totalPayment
            });
        }

        function saveCalculation(type, data) {
            const calculation = {
                type: type,
                data: data,
                timestamp: new Date().toLocaleString('tr-TR')
            };

            calculations.unshift(calculation);
            if (calculations.length > 10) calculations.pop();

            updateRecentCalculations();
        }

        function updateRecentCalculations() {
            const container = document.getElementById('recent-calculations');

            if (calculations.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Henüz hesaplama yapılmadı</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = calculations.map(calc => `
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800">${calc.type}</p>
                        <p class="text-sm text-gray-500">${calc.timestamp}</p>
                    </div>
                    <button onclick="viewCalculation('${calc.type}')" class="text-blue-600 hover:text-blue-800 text-sm">
                        Görüntüle
                    </button>
                </div>
            `).join('');
        }

        // Sayfa yüklendiğinde tarih alanını bugünün tarihi ile doldur
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            if (document.getElementById('start-date')) {
                document.getElementById('start-date').value = today;
            }
        });
    </script>
@endpush
