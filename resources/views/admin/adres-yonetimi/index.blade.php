@extends('admin.layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="addressManager()">
        <div class="content-header mb-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    🏠 Adres Yönetimi
                </h1>
                <div class="flex space-x-3">
                    <button @click="showStatsModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        İstatistikler
                    </button>
                    <button @click="showAllDataInConsole()"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Console'da Göster
                    </button>
                    <!-- Context7: TurkiyeAPI'den Seçimli Veri Çekme -->
                    <div class="flex items-center gap-3">
                        <!-- Seçim Dropdown'ları -->
                        <div
                            class="flex items-center gap-2 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800">
                            <label class="text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap font-medium">İl:</label>
                            <select x-model="fetchSelectedIlId" @change="fetchSelectedIlceId = null; fetchSelectedMahalleId = null"
                                class="text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white min-w-[150px] px-2 py-1 transition-all duration-200">
                                <option value="">Seçiniz</option>
                                <template x-for="il in iller" :key="il.id">
                                    <option :value="il.id" x-text="il.il_adi"></option>
                                </template>
                            </select>

                            <label
                                class="text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap font-medium ml-2">İlçe:</label>
                            <select x-model="fetchSelectedIlceId" @change="fetchSelectedMahalleId = null"
                                :disabled="!fetchSelectedIlId"
                                class="text-sm border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white min-w-[150px] px-2 py-1 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                <option value="">Seçiniz</option>
                                <template x-for="ilce in ilceler.filter(i => i.il_id == fetchSelectedIlId)"
                                    :key="ilce.id">
                                    <option :value="ilce.id" x-text="ilce.ilce_adi"></option>
                                </template>
                            </select>
                        </div>

                        <!-- TurkiyeAPI'den Çek Butonu -->
                        <button @click="fetchFromTurkiyeAPI()" :disabled="fetching || (!fetchSelectedIlId && !fetchSelectedIlceId)"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="!fetching" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <div x-show="fetching"
                                class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full"></div>
                            <span x-text="fetching ? 'Çekiliyor...' : 'TurkiyeAPI\'den Çek'"></span>
                        </button>

                        <!-- Sync Butonu (Mevcut) -->
                        <button @click="syncFromTurkiyeAPI()" :disabled="syncing || (!syncSelectedIlId && !syncSelectedIlceId && !fetchSelectedIlId && !fetchSelectedIlceId)"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Lütfen önce bir il veya ilçe seçin">
                            <svg x-show="!syncing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <div x-show="syncing"
                                class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full"></div>
                            <span x-text="syncing ? 'Sync Ediliyor...' : 'Seçime Göre Sync Et'"></span>
                        </button>
                    </div>
                    <button @click="showAddModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Yeni Ekle
                    </button>
                </div>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Toplam Ülke</p>
                        <p class="text-2xl font-bold" x-text="ulkeler.length">0</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-400 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Toplam İl</p>
                        <p class="text-2xl font-bold" x-text="iller.length">0</p>
                    </div>
                    <div class="w-12 h-12 bg-green-400 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Toplam İlçe</p>
                        <p class="text-2xl font-bold" x-text="ilceler.length">0</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-400 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm">Toplam Mahalle</p>
                        <p class="text-2xl font-bold" x-text="mahalleler.length">0</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-400 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Arama ve Filtreleme -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Arama</label>
                    <input type="text" x-model="searchQuery" @input="filterData()"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                        placeholder="Ülke, il, ilçe veya mahalle ara...">
                </div>
                <div class="md:w-48">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                    <select style="color-scheme: light dark;" x-model="filterType" @change="filterData()"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tümü</option>
                        <option value="ulke">Ülkeler</option>
                        <option value="il">İller</option>
                        <option value="ilce">İlçeler</option>
                        <option value="mahalle">Mahalleler</option>
                    </select>
                </div>
                <div class="md:w-32">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sıralama</label>
                    <select style="color-scheme: light dark;" x-model="sortOrder" @change="sortData()"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="asc">A-Z</option>
                        <option value="desc">Z-A</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Toolbar -->
        <div x-show="selectedItems.length > 0" x-transition
            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        <span x-text="selectedItems.length"></span> öğe seçildi
                    </span>
                    <button @click="bulkDelete()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="selectedItems.length === 0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Seçilenleri Sil
                    </button>
                </div>
                <button @click="clearSelection()"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors duration-200">
                    Seçimi Temizle
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Ülkeler - Basic Theme (Mavi) -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-200 flex items-center">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-2">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        Ülkeler
                    </h2>
                    <div class="flex items-center gap-2">
                        <label class="flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400 cursor-pointer">
                            <input type="checkbox" @change="toggleSelectAll('ulke')"
                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            Tümü
                        </label>
                        <button @click="addItem('ulke')"
                            class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="list-ulkeler" class="text-sm text-gray-700 dark:text-gray-300">Yükleniyor...</div>
            </div>

            <!-- İller - Location Theme (Yeşil) -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-green-800 dark:text-green-200 flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-2">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        İller
                    </h2>
                    <div class="flex items-center gap-2">
                        <label class="flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400 cursor-pointer"
                            :class="{ 'opacity-50': !selectedUlke }">
                            <input type="checkbox" @change="toggleSelectAll('il')"
                                class="rounded border-gray-300 dark:border-gray-600 text-green-600 focus:ring-green-500"
                                :disabled="!selectedUlke">
                            Tümü
                        </label>
                        <button @click="addItem('il')"
                            class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50"
                            :disabled="!selectedUlke">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="list-iller" class="text-sm text-gray-700 dark:text-gray-300">Ülke seçin</div>
            </div>

            <!-- İlçeler - Features Theme (Mor) -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-purple-800 dark:text-purple-200 flex items-center">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-2">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        İlçeler
                    </h2>
                    <div class="flex items-center gap-2">
                        <label class="flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400 cursor-pointer"
                            :class="{ 'opacity-50': !selectedIl }">
                            <input type="checkbox" @change="toggleSelectAll('ilce')"
                                class="rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500"
                                :disabled="!selectedIl">
                            Tümü
                        </label>
                        <button @click="addItem('ilce')"
                            class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50"
                            :disabled="!selectedIl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="list-ilceler" class="text-sm text-gray-700 dark:text-gray-300">İl seçin</div>
            </div>

            <!-- Mahalleler - Media Theme (Turuncu) -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-orange-800 dark:text-orange-200 flex items-center">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-2">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        Mahalleler
                    </h2>
                    <div class="flex items-center gap-2">
                        <label class="flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400 cursor-pointer"
                            :class="{ 'opacity-50': !selectedIlce }">
                            <input type="checkbox" @change="toggleSelectAll('mahalle')"
                                class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500"
                                :disabled="!selectedIlce">
                            Tümü
                        </label>
                        <button @click="addItem('mahalle')"
                            class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50"
                            :disabled="!selectedIlce">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="list-mahalleler" class="text-sm text-gray-700 dark:text-gray-300">İlçe seçin</div>
            </div>
        </div>

        <!-- Context7: TurkiyeAPI'den Çekilen Veriler Bilgi Kutusu -->
        <div x-show="fetchedData && ((fetchedData.districts && fetchedData.districts.length > 0) || (fetchedData.neighborhoods && fetchedData.neighborhoods.length > 0) || (fetchedData.towns && fetchedData.towns.length > 0) || (fetchedData.villages && fetchedData.villages.length > 0))" x-transition
            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">
                        📥 TurkiyeAPI'den Çekilen Veriler
                    </h3>
                    <div class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                        <template x-if="fetchedData && fetchedData.districts && fetchedData.districts.length > 0">
                            <div>
                                <span class="font-medium">İlçeler:</span>
                                <span x-text="fetchedData.districts.length"></span> adet
                                <span class="text-xs text-blue-600 dark:text-blue-400 ml-2">
                                    (Henüz veritabanına kaydedilmedi)
                                </span>
                            </div>
                        </template>
                        <template x-if="fetchedData && fetchedData.neighborhoods && fetchedData.neighborhoods.length > 0">
                            <div>
                                <span class="font-medium">Mahalleler:</span>
                                <span x-text="fetchedData.neighborhoods.length"></span> adet
                            </div>
                        </template>
                        <template x-if="fetchedData && fetchedData.towns && fetchedData.towns.length > 0">
                            <div>
                                <span class="font-medium">Beldeler:</span>
                                <span x-text="fetchedData.towns.length"></span> adet
                            </div>
                        </template>
                        <template x-if="fetchedData && fetchedData.villages && fetchedData.villages.length > 0">
                            <div>
                                <span class="font-medium">Köyler:</span>
                                <span x-text="fetchedData.villages.length"></span> adet
                            </div>
                        </template>
                    </div>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                        💡 Bu verileri veritabanına kaydetmek için "Sync Et" butonunu kullanabilirsiniz.
                    </p>
                </div>
                <button @click="fetchedData = null"
                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- İstatistikler Modal -->
        <div x-show="showStatsModal" x-transition
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-2xl p-6 w-full max-w-4xl mx-4 max-h-96 overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 Adres İstatistikleri</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-800 dark:text-gray-200">Genel İstatistikler</h4>
                        <div class="space-y-2 text-gray-700 dark:text-gray-300">
                            <div class="flex justify-between">
                                <span>Toplam Ülke:</span>
                                <span class="font-semibold" x-text="ulkeler.length">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Toplam İl:</span>
                                <span class="font-semibold" x-text="iller.length">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Toplam İlçe:</span>
                                <span class="font-semibold" x-text="ilceler.length">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Toplam Mahalle:</span>
                                <span class="font-semibold" x-text="mahalleler.length">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-800 dark:text-gray-200">En Popüler</h4>
                        <div class="space-y-2 text-gray-700 dark:text-gray-300">
                            <div class="flex justify-between">
                                <span>En Çok İlçeli İl:</span>
                                <span class="font-semibold">Muğla</span>
                            </div>
                            <div class="flex justify-between">
                                <span>En Çok Mahalleli İlçe:</span>
                                <span class="font-semibold">Bodrum</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button @click="showStatsModal = false"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                        Kapat
                    </button>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div x-show="showAddModal" x-transition
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-2xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    x-text="editingItem ? 'Düzenle' : 'Yeni Ekle'"></h3>

                <form name="addressForm" @submit.prevent="saveItem()" novalidate>
                    @csrf
                    <div class="space-y-2 mb-4">
                        <label for="address_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span x-text="getFieldLabel()"></span>
                        </label>
                        <input type="text" id="address_name" name="name" x-model="formData.name"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                            required>
                    </div>

                    <div class="space-y-2 mb-4" x-show="formData.type === 'il'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ülke</label>
                        <select style="color-scheme: light dark;" name="parent_id" x-model="formData.parent_id"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                            :required="formData.type === 'il'">
                            <option value="">Ülke Seçin</option>
                            <template x-for="ulke in ulkeler" :key="ulke.id">
                                <option :value="ulke.id" x-text="ulke.ulke_adi"></option>
                            </template>
                        </select>
                    </div>

                    <div class="space-y-2 mb-4" x-show="formData.type === 'ilce'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">İl</label>
                        <select style="color-scheme: light dark;" name="parent_id" x-model="formData.parent_id"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                            :required="formData.type === 'ilce'">
                            <option value="">İl Seçin</option>
                            <template x-for="il in iller" :key="il.id">
                                <option :value="il.id" x-text="il.il_adi"></option>
                            </template>
                        </select>
                    </div>

                    <div class="space-y-2 mb-4" x-show="formData.type === 'mahalle'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">İlçe</label>
                        <select style="color-scheme: light dark;" name="parent_id" x-model="formData.parent_id"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                            :required="formData.type === 'mahalle'">
                            <option value="">İlçe Seçin</option>
                            <template x-for="ilce in ilceler" :key="ilce.id">
                                <option :value="ilce.id" x-text="ilce.ilce_adi"></option>
                            </template>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="closeModal()"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            İptal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            <span x-text="editingItem ? 'Güncelle' : 'Kaydet'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function addressManager() {
                return {
                    showAddModal: false,
                    showStatsModal: false,
                    editingItem: null,
                    selectedUlke: null,
                    selectedIl: null,
                    selectedIlce: null,
                    searchQuery: '',
                    filterType: '',
                    sortOrder: 'asc',
                    ulkeler: [],
                    iller: [],
                    ilceler: [],
                    mahalleler: [],
                    filteredData: [],
                    selectedItems: [], // Bulk selection
                    selectedItemsType: null, // 'ulke', 'il', 'ilce', 'mahalle'
                    syncing: false, // TurkiyeAPI sync durumu
                    syncSelectedIlId: null, // Sync için seçilen il
                    syncSelectedIlceId: null, // Sync için seçilen ilçe
                    fetching: false, // TurkiyeAPI fetch durumu
                    fetchSelectedIlId: null, // Fetch için seçilen il
                    fetchSelectedIlceId: null, // Fetch için seçilen ilçe
                    fetchSelectedMahalleId: null, // Fetch için seçilen mahalle
                    fetchedData: null, // Çekilen veriler
                    filterTimeout: null, // Debounce için timeout
                    // Context7: Performance optimization - Pagination
                    mahallePage: 1,
                    mahallePerPage: 100, // Sayfa başına mahalle sayısı (200'den 100'e düşürüldü)
                    ilcePage: 1,
                    ilcePerPage: 100, // Sayfa başına ilçe sayısı (200'den 100'e düşürüldü)
                    formData: {
                        type: '',
                        name: '',
                        parent_id: '',
                        id: null
                    },

                    init() {
                        // Context7: Load all address data on init
                        this.loadUlkeler();
                        this.loadIller();
                        this.loadAllIlceler();
                        this.loadAllMahalleler();
                        console.log('✅ Adres Yönetimi initialized');

                        // Context7: Global instance for pagination buttons
                        window.addressManagerInstance = this;

                        // Context7: Tüm verileri console'da göster (deferred, non-blocking)
                        this.$nextTick(() => {
                            // Use requestAnimationFrame for better performance
                            requestAnimationFrame(() => {
                                setTimeout(() => {
                                    this.showAllDataInConsole();
                                }, 1000);
                            });
                        });
                    },

                    /**
                     * Tüm çekilen verileri console'da göster
                     * Context7: Debug ve veri kontrolü için
                     */
                    showAllDataInConsole() {
                        console.group('📊 Adres Yönetimi - Çekilen Tüm Veriler');
                        console.log('🌍 Ülkeler:', this.ulkeler);
                        console.log(`   Toplam: ${this.ulkeler.length} ülke`);
                        console.log('🏛️ İller:', this.iller);
                        console.log(`   Toplam: ${this.iller.length} il`);
                        console.log('🏘️ İlçeler:', this.ilceler);
                        console.log(`   Toplam: ${this.ilceler.length} ilçe`);
                        console.log('📍 Mahalleler:', this.mahalleler);
                        console.log(`   Toplam: ${this.mahalleler.length} mahalle`);
                        console.groupEnd();

                        // Detaylı istatistikler
                        console.group('📈 Detaylı İstatistikler');
                        console.table({
                            'Ülkeler': {
                                sayi: this.ulkeler.length,
                                veri: this.ulkeler.map(u => u.ulke_adi).join(', ')
                            },
                            'İller': {
                                sayi: this.iller.length,
                                veri: this.iller.map(i => i.il_adi).slice(0, 10).join(', ') + (this.iller.length > 10 ?
                                    '...' : '')
                            },
                            'İlçeler': {
                                sayi: this.ilceler.length,
                                veri: this.ilceler.map(ic => ic.ilce_adi).slice(0, 10).join(', ') + (this.ilceler
                                    .length > 10 ? '...' : '')
                            },
                            'Mahalleler': {
                                sayi: this.mahalleler.length,
                                veri: this.mahalleler.map(m => m.mahalle_adi).slice(0, 10).join(', ') + (this.mahalleler
                                    .length > 10 ? '...' : '')
                            }
                        });
                        console.groupEnd();
                    },

                    getFieldLabel() {
                        const labels = {
                            'ulke': 'Ülke Adı',
                            'il': 'İl Adı',
                            'ilce': 'İlçe Adı',
                            'mahalle': 'Mahalle Adı'
                        };
                        return labels[this.formData.type] || 'Ad';
                    },

                    addItem(type) {
                        this.formData = {
                            type: type,
                            name: '',
                            parent_id: '',
                            id: null
                        };
                        this.editingItem = null;
                        this.showAddModal = true;

                        // Context-aware toast notification
                        this.showAddItemToast(type);
                    },

                    showAddItemToast(type) {
                        const typeNames = {
                            'ulke': 'Ülke',
                            'il': 'İl',
                            'ilce': 'İlçe',
                            'mahalle': 'Mahalle'
                        };

                        const typeName = typeNames[type] || type;

                        // Context7: Merkezi toast sistemi kullan
                        window.toast.info(`${typeName} ekleme formu açıldı`);
                    },

                    showSaveSuccessToast(type, name) {
                        const typeNames = {
                            'ulke': 'Ülke',
                            'il': 'İl',
                            'ilce': 'İlçe',
                            'mahalle': 'Mahalle'
                        };

                        const typeName = typeNames[type] || type;
                        const action = this.editingItem ? 'güncellendi' : 'eklendi';

                        // Context7: Merkezi toast sistemi kullan
                        window.toast.success(`${typeName} "${name}" ${action}`);
                    },

                    showSaveErrorToast(type, error) {
                        const typeNames = {
                            'ulke': 'Ülke',
                            'il': 'İl',
                            'ilce': 'İlçe',
                            'mahalle': 'Mahalle'
                        };

                        const typeName = typeNames[type] || type;

                        // Context7: Merkezi toast sistemi kullan
                        window.toast.error(`${typeName} işlemi başarısız: ${error}`);
                    },

                    editItem(item, type) {
                        this.formData = {
                            type: type,
                            name: item.name || item.ulke_adi || item.il_adi || item.ilce_adi || item.mahalle_adi,
                            parent_id: item.parent_id || item.ulke_id || item.il_id || item.ilce_id || '',
                            id: item.id
                        };
                        this.editingItem = item;
                        this.showAddModal = true;
                    },

                    async saveItem() {
                        try {
                            // Form validasyonu
                            if (!this.formData.name || this.formData.name.trim() === '') {
                                alert('Ad alanı zorunludur');
                                return;
                            }

                            // Parent ID kontrolü (ülke, il, ilçe için)
                            if (this.formData.type !== 'ulke' && (!this.formData.parent_id || this.formData.parent_id ===
                                    '')) {
                                alert('Üst kategori seçimi zorunludur');
                                return;
                            }

                            const url = this.editingItem ?
                                `/admin/adres-yonetimi/${this.formData.type}/${this.formData.id}` :
                                `/admin/adres-yonetimi/${this.formData.type}`;

                            const method = this.editingItem ? 'PUT' : 'POST';

                            const response = await fetch(url, {
                                method: method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify(this.formData)
                            });

                            if (response.ok) {
                                const result = await response.json();
                                if (result.success) {
                                    this.closeModal();
                                    this.refreshData();
                                    this.showSaveSuccessToast(this.formData.type, this.formData.name);
                                } else {
                                    this.showSaveErrorToast(this.formData.type, result.message || 'Hata oluştu');
                                }
                            } else {
                                const errorData = await response.json();
                                this.showSaveErrorToast(this.formData.type, errorData.message || 'Hata oluştu');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Bağlantı hatası oluştu');
                        }
                    },

                    async deleteItem(id, type) {
                        if (!confirm('Silmek istediğinizden emin misiniz?')) return;

                        try {
                            const response = await fetch(`/admin/adres-yonetimi/${type}/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            if (response.ok) {
                                this.refreshData();
                            } else {
                                alert('Hata oluştu');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Hata oluştu');
                        }
                    },

                    closeModal() {
                        this.showAddModal = false;
                        this.editingItem = null;
                        this.formData = {
                            type: '',
                            name: '',
                            parent_id: '',
                            id: null
                        };
                    },

                    async loadUlkeler() {
                        try {
                            const response = await fetch('{{ route('admin.adres-yonetimi.ulkeler') }}');
                            const data = await response.json();
                            if (data.success) {
                                this.ulkeler = data.ulkeler || [];
                                this.renderUlkeler();
                                console.log(`✅ ${this.ulkeler.length} ülke yüklendi`, this.ulkeler);
                            }
                        } catch (error) {
                            console.error('Error loading ulkeler:', error);
                            window.toast?.error('Ülkeler yüklenemedi');
                        }
                    },

                    async loadIller() {
                        try {
                            const response = await fetch('{{ route('admin.adres-yonetimi.iller') }}');
                            const data = await response.json();
                            if (data.success) {
                                this.iller = data.iller || [];
                                this.renderIller();
                                console.log(`✅ ${this.iller.length} il yüklendi`, this.iller);
                            }
                        } catch (error) {
                            console.error('Error loading iller:', error);
                            window.toast?.error('İller yüklenemedi');
                        }
                    },

                    async loadAllIlceler() {
                        try {
                            const response = await fetch('{{ route('admin.adres-yonetimi.ilceler') }}');
                            const data = await response.json();
                            if (data.success) {
                                this.ilceler = data.ilceler || [];
                                // Context7: Reset pagination when data loads
                                this.ilcePage = 1;
                                this.renderIlceler();
                                console.log(`✅ ${this.ilceler.length} ilçe yüklendi`);
                            } else {
                                console.warn('⚠️ İlçeler yüklenemedi:', data);
                                document.getElementById('list-ilceler').textContent = 'İlçeler yüklenemedi';
                            }
                        } catch (error) {
                            console.error('❌ Error loading ilceler:', error);
                            window.toast?.error('İlçeler yüklenemedi');
                            document.getElementById('list-ilceler').textContent = 'Hata: İlçeler yüklenemedi';
                        }
                    },

                    async loadAllMahalleler() {
                        try {
                            const response = await fetch('{{ route('admin.adres-yonetimi.mahalleler') }}');
                            const data = await response.json();
                            console.log('🔍 Mahalleler API Response:', data);

                            if (data.success) {
                                this.mahalleler = data.mahalleler || [];

                                if (this.mahalleler.length > 0) {
                                    // Context7: Reset pagination when data loads
                                    this.mahallePage = 1;
                                    this.renderMahalleler();
                                    console.log(`✅ ${this.mahalleler.length} mahalle yüklendi`);
                                } else {
                                    console.warn('⚠️ Mahalleler boş geldi');
                                    document.getElementById('list-mahalleler').innerHTML =
                                        '<div class="text-sm text-gray-500 dark:text-gray-400">Henüz mahalle kaydı yok. İlçe seçerek mahalle ekleyebilirsiniz.</div>';
                                }
                            } else {
                                console.warn('⚠️ Mahalleler yüklenemedi:', data);
                                document.getElementById('list-mahalleler').textContent = 'Mahalleler yüklenemedi: ' + (data
                                    .message || 'Bilinmeyen hata');
                            }
                        } catch (error) {
                            console.error('❌ Error loading mahalleler:', error);
                            window.toast?.error('Mahalleler yüklenemedi: ' + error.message);
                            document.getElementById('list-mahalleler').innerHTML =
                                '<div class="text-sm text-red-500">Hata: Mahalleler yüklenemedi</div>';
                        }
                    },

                    renderUlkeler() {
                        const container = document.getElementById('list-ulkeler');
                        container.innerHTML = this.ulkeler.map(ulke => {
                            const isSelected = this.selectedItems.includes(ulke.id) && this.selectedItemsType ===
                                'ulke';
                            return `
                <div class="flex items-center justify-between px-2 py-1 rounded border mr-2 mb-2 ${isSelected ? 'bg-blue-100 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700' : 'bg-gray-50 dark:bg-gray-700'}">
                    <div class="flex items-center gap-2 flex-1">
                        <input type="checkbox" value="${ulke.id}"
                               @change="toggleItemSelection(${ulke.id}, 'ulke')"
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                               ${isSelected ? 'checked' : ''}>
                        <button data-ulke="${ulke.id}" class="flex-1 text-left hover:text-blue-600 dark:hover:text-blue-400" @click="selectUlke(${ulke.id})">
                            ${ulke.ulke_adi}
                        </button>
                    </div>
                    <div class="flex space-x-1">
                        <button @click="editItem(${JSON.stringify(ulke).replace(/"/g, '&quot;')}, 'ulke')" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button @click="deleteItem(${ulke.id}, 'ulke')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
                        }).join('');
                    },

                    async selectUlke(ulkeId) {
                        this.selectedUlke = ulkeId;
                        this.selectedIl = null;
                        this.selectedIlce = null;

                        try {
                            const response = await fetch(`{{ route('admin.adres-yonetimi.iller.by-ulke', '') }}/${ulkeId}`);
                            const data = await response.json();
                            if (data.success) {
                                this.iller = data.iller;
                                this.renderIller();
                            }
                        } catch (error) {
                            console.error('Error loading iller:', error);
                        }

                        document.getElementById('list-ilceler').textContent = 'İl seçin';
                        document.getElementById('list-mahalleler').textContent = 'İlçe seçin';
                    },

                    renderIller() {
                        const container = document.getElementById('list-iller');
                        container.innerHTML = this.iller.map(il => {
                            const isSelected = this.selectedItems.includes(il.id) && this.selectedItemsType === 'il';
                            return `
                <div class="flex items-center justify-between px-2 py-1 rounded border mr-2 mb-2 ${isSelected ? 'bg-green-100 dark:bg-green-900/30 border-green-300 dark:border-green-700' : 'bg-gray-50 dark:bg-gray-700'}">
                    <div class="flex items-center gap-2 flex-1">
                        <input type="checkbox" value="${il.id}"
                               @change="toggleItemSelection(${il.id}, 'il')"
                               class="rounded border-gray-300 dark:border-gray-600 text-green-600 focus:ring-green-500"
                               ${isSelected ? 'checked' : ''}>
                        <button data-il="${il.id}" class="flex-1 text-left hover:text-green-600 dark:hover:text-green-400" @click="selectIl(${il.id})">
                            ${il.il_adi}
                        </button>
                    </div>
                    <div class="flex space-x-1">
                        <button @click="editItem(${JSON.stringify(il).replace(/"/g, '&quot;')}, 'il')" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button @click="deleteItem(${il.id}, 'il')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
                        }).join('');
                    },

                    async selectIl(ilId) {
                        this.selectedIl = ilId;
                        this.selectedIlce = null;

                        try {
                            const response = await fetch(`{{ route('admin.adres-yonetimi.ilceler.by-il', '') }}/${ilId}`);
                            const data = await response.json();
                            if (data.success) {
                                this.ilceler = data.ilceler;
                                this.renderIlceler();
                            }
                        } catch (error) {
                            console.error('Error loading ilceler:', error);
                        }

                        document.getElementById('list-mahalleler').textContent = 'İlçe seçin';
                    },

                    renderIlceler() {
                        const container = document.getElementById('list-ilceler');

                        if (!container) {
                            console.error('❌ list-ilceler container bulunamadı!');
                            return;
                        }

                        // Context7: Filter ilçeler by selected il if available
                        let filteredIlceler = this.ilceler;
                        if (this.selectedIl) {
                            filteredIlceler = this.ilceler.filter(ilce => ilce.il_id == this.selectedIl);
                        }

                        if (!filteredIlceler || filteredIlceler.length === 0) {
                            container.innerHTML =
                                '<div class="text-sm text-gray-500 dark:text-gray-400">' + 
                                (this.selectedIl ? 'Bu il için henüz ilçe kaydı yok.' : 'Henüz ilçe kaydı yok.') + 
                                '</div>';
                            return;
                        }

                        // Context7: Performance optimization - Pagination
                        const totalPages = Math.ceil(filteredIlceler.length / this.ilcePerPage);
                        const startIndex = (this.ilcePage - 1) * this.ilcePerPage;
                        const endIndex = startIndex + this.ilcePerPage;
                        const paginatedIlceler = filteredIlceler.slice(startIndex, endIndex);

                        // Context7: Optimize rendering - Use template literals efficiently (avoid JSON.stringify)
                        const html = paginatedIlceler.map(ilce => {
                            const isSelected = this.selectedItems.includes(ilce.id) && this.selectedItemsType === 'ilce';
                            const selectedClass = isSelected ? 'bg-purple-100 dark:bg-purple-900/30 border-purple-300 dark:border-purple-700' : 'bg-gray-50 dark:bg-gray-700';
                            const ilceName = ilce.ilce_adi.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                            const ilceId = ilce.id;
                            
                            return `<div class="flex items-center justify-between px-2 py-1 rounded border mr-2 mb-2 ${selectedClass}">
                                <div class="flex items-center gap-2 flex-1">
                                    <input type="checkbox" value="${ilceId}"
                                           @change="toggleItemSelection(${ilceId}, 'ilce')"
                                           class="rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500"
                                           ${isSelected ? 'checked' : ''}>
                                    <button data-ilce="${ilceId}" class="flex-1 text-left hover:text-purple-600 dark:hover:text-purple-400" @click="selectIlce(${ilceId})">
                                        ${ilceName}
                                    </button>
                                </div>
                                <div class="flex space-x-1">
                                    <button @click="editItem({id:${ilceId},ilce_adi:'${ilceName}',il_id:${ilce.il_id}}, 'ilce')" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button @click="deleteItem(${ilceId}, 'ilce')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>`;
                        }).join('');
                        
                        // Context7: Single DOM update with requestAnimationFrame for better performance
                        requestAnimationFrame(() => {
                            container.innerHTML = html;
                        });

                        // Context7: Pagination controls (Alpine.js compatible)
                        if (totalPages > 1) {
                            // Remove existing pagination if any
                            const existingPagination = container.querySelector('.pagination-controls');
                            if (existingPagination) {
                                existingPagination.remove();
                            }

                            const paginationDiv = document.createElement('div');
                            paginationDiv.className =
                                'mt-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4 pagination-controls';
                            paginationDiv.innerHTML = `
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Gösterilen: ${startIndex + 1}-${Math.min(endIndex, filteredIlceler.length)} / ${filteredIlceler.length}
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="window.addressManagerInstance.ilcePage = Math.max(1, window.addressManagerInstance.ilcePage - 1); window.addressManagerInstance.renderIlceler();"
                                            ${this.ilcePage === 1 ? 'disabled' : ''}
                                            class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                        Önceki
                                    </button>
                                    <span class="px-3 py-1 text-sm text-gray-700 dark:text-gray-300">
                                        Sayfa ${this.ilcePage} / ${totalPages}
                                    </span>
                                    <button onclick="window.addressManagerInstance.ilcePage = Math.min(${totalPages}, window.addressManagerInstance.ilcePage + 1); window.addressManagerInstance.renderIlceler();"
                                            ${this.ilcePage === totalPages ? 'disabled' : ''}
                                            class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                        Sonraki
                                    </button>
                                </div>
                            `;
                            container.appendChild(paginationDiv);
                        }

                        console.log(
                            `✅ ${paginatedIlceler.length} ilçe render edildi (Sayfa ${this.ilcePage}/${totalPages}, Toplam: ${filteredIlceler.length}${this.selectedIl ? `, İl ID: ${this.selectedIl}` : ''})`
                        );
                    },

                    async selectIlce(ilceId) {
                        this.selectedIlce = ilceId;
                        console.log('🔍 selectIlce çağrıldı - İlçe ID:', ilceId);

                        try {
                            const response = await fetch(
                                `{{ route('admin.adres-yonetimi.mahalleler.by-ilce', '') }}/${ilceId}`);
                            const data = await response.json();
                            console.log('🔍 selectIlce API Response:', data);
                            
                            if (data.success) {
                                // Context7: Fetch'ten gelen mahalleleri koru, sadece DB'den gelenleri ekle
                                // İlçe ID'sini number'a çevir (karşılaştırma için)
                                const ilceIdNum = parseInt(ilceId);
                                
                                // Tüm fetch'ten gelen mahalleleri koru (hepsi)
                                const fetchMahalleler = this.mahalleler.filter(m => m._from_turkiyeapi);
                                
                                // DB'den gelen mahalleler (sadece bu ilçeye ait)
                                const dbMahalleler = data.mahalleler || [];
                                
                                console.log('🔍 Mevcut toplam mahalle sayısı:', this.mahalleler.length);
                                console.log('🔍 Fetch mahalleleri (tümü):', fetchMahalleler.length);
                                console.log('🔍 DB mahalleleri:', dbMahalleler.length);
                                
                                // Duplicate kontrolü yaparak birleştir
                                // Önce fetch'ten gelenleri ekle
                                const allMahalleler = [...fetchMahalleler];
                                
                                // Sonra DB'den gelenleri ekle (duplicate kontrolü ile)
                                dbMahalleler.forEach(dbMahalle => {
                                    const exists = allMahalleler.find(m => 
                                        m.id === dbMahalle.id || 
                                        (m.ilce_id == dbMahalle.ilce_id && 
                                         m.mahalle_adi.toLowerCase() === dbMahalle.mahalle_adi.toLowerCase())
                                    );
                                    if (!exists) {
                                        allMahalleler.push(dbMahalle);
                                    }
                                });
                                
                                console.log('🔍 Birleştirilmiş mahalle sayısı:', allMahalleler.length);
                                console.log('🔍 Bu ilçeye ait mahalleler:', allMahalleler.filter(m => {
                                    const mahalleIlceId = parseInt(m.ilce_id);
                                    return mahalleIlceId == ilceIdNum || m.ilce_id == ilceIdNum || m.ilce_id == ilceId;
                                }).length);
                                
                                this.mahalleler = allMahalleler;
                                // Context7: Reset pagination when filtering by district
                                this.mahallePage = 1;
                                this.renderMahalleler();
                            } else {
                                console.error('❌ selectIlce API hatası:', data);
                            }
                        } catch (error) {
                            console.error('❌ Error loading mahalleler:', error);
                        }
                    },

                    renderMahalleler() {
                        const container = document.getElementById('list-mahalleler');

                        if (!container) {
                            console.error('❌ list-mahalleler container bulunamadı!');
                            return;
                        }

                        // Context7: Debug - Mahalle sayısını kontrol et
                        console.log('🔍 renderMahalleler - Toplam mahalle sayısı:', this.mahalleler?.length);
                        console.log('🔍 renderMahalleler - selectedIlce:', this.selectedIlce);
                        console.log('🔍 renderMahalleler - fetchSelectedIlceId:', this.fetchSelectedIlceId);
                        
                        if (!this.mahalleler || this.mahalleler.length === 0) {
                            container.innerHTML =
                                '<div class="text-sm text-gray-500 dark:text-gray-400">Henüz mahalle kaydı yok. İlçe seçerek mahalle ekleyebilirsiniz.</div>';
                            return;
                        }
                        
                        // Context7: Eğer ilçe seçiliyse, sadece o ilçeye ait mahalleleri göster
                        let filteredMahalleler = this.mahalleler;
                        if (this.selectedIlce) {
                            const selectedIlceNum = parseInt(this.selectedIlce);
                            filteredMahalleler = this.mahalleler.filter(m => {
                                // İlçe ID karşılaştırması (hem string hem number için)
                                const mahalleIlceId = parseInt(m.ilce_id);
                                const matches = mahalleIlceId == selectedIlceNum || m.ilce_id == selectedIlceNum || m.ilce_id == this.selectedIlce;
                                
                                if (!matches && m._from_turkiyeapi) {
                                    console.log('🔍 Mahalle filtrelendi (ilçe uyuşmuyor):', {
                                        mahalle: m.mahalle_adi,
                                        mahalleIlceId: m.ilce_id,
                                        mahalleIlceIdParsed: mahalleIlceId,
                                        selectedIlce: this.selectedIlce,
                                        selectedIlceNum: selectedIlceNum,
                                        type: typeof m.ilce_id,
                                        selectedType: typeof this.selectedIlce
                                    });
                                }
                                return matches;
                            });
                            console.log('🔍 renderMahalleler - Filtrelenmiş mahalle sayısı:', filteredMahalleler.length);
                            console.log('🔍 renderMahalleler - Tüm mahalleler (ilk 10):', this.mahalleler.slice(0, 10).map(m => ({
                                name: m.mahalle_adi,
                                ilce_id: m.ilce_id,
                                ilce_id_parsed: parseInt(m.ilce_id),
                                _from_turkiyeapi: m._from_turkiyeapi
                            })));
                        }
                        
                        // Eğer filtrelenmiş mahalleler boşsa ama fetch'ten gelenler varsa, onları göster
                        if (filteredMahalleler.length === 0 && this.fetchSelectedIlceId) {
                            filteredMahalleler = this.mahalleler.filter(m => 
                                m.ilce_id == this.fetchSelectedIlceId || m._from_turkiyeapi
                            );
                            console.log('🔍 renderMahalleler - Fetch mahalleleri gösteriliyor:', filteredMahalleler.length);
                        }

                        // Context7: Performance optimization - Pagination (filtrelenmiş mahalleler üzerinden)
                        const totalPages = Math.ceil(filteredMahalleler.length / this.mahallePerPage);
                        const startIndex = (this.mahallePage - 1) * this.mahallePerPage;
                        const endIndex = startIndex + this.mahallePerPage;
                        const paginatedMahalleler = filteredMahalleler.slice(startIndex, endIndex);

                        // Context7: Optimize rendering - Use template literals efficiently (avoid JSON.stringify)
                        const html = paginatedMahalleler.map(mahalle => {
                            const isSelected = mahalle.id && this.selectedItems.includes(mahalle.id) && this.selectedItemsType === 'mahalle';
                            const selectedClass = isSelected ? 'bg-orange-100 dark:bg-orange-900/30 border-orange-300 dark:border-orange-700' : 'bg-gray-50 dark:bg-gray-700';
                            const mahalleName = (mahalle.mahalle_adi || mahalle.name || 'İsimsiz Mahalle').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                            const mahalleId = mahalle.id || `temp_${mahalle.ilce_id}_${mahalle.mahalle_adi}`; // Context7: Geçici ID için
                            const isFromTurkiyeAPI = mahalle._from_turkiyeapi;
                            const turkiyeAPIBadge = isFromTurkiyeAPI ? '<span class="ml-2 px-1.5 py-0.5 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded">TurkiyeAPI</span>' : '';
                            
                            return `<div class="flex items-center justify-between px-2 py-1 rounded border mr-2 mb-2 ${selectedClass}">
                                <div class="flex items-center gap-2 flex-1">
                                    ${mahalle.id ? `<input type="checkbox" value="${mahalleId}"
                                           @change="toggleItemSelection(${mahalleId}, 'mahalle')"
                                           class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500"
                                           ${isSelected ? 'checked' : ''}>` : '<span class="w-4"></span>'}
                                    <span class="flex-1 text-gray-700 dark:text-gray-300">${mahalleName}${turkiyeAPIBadge}</span>
                                </div>
                                <div class="flex space-x-1">
                                    ${mahalle.id ? `<button @click="editItem({id:${mahalleId},mahalle_adi:'${mahalleName}',ilce_id:${mahalle.ilce_id || ''}}, 'mahalle')" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button @click="deleteItem(${mahalleId}, 'mahalle')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-yellow-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>` : '<span class="text-xs text-blue-600 dark:text-blue-400">Sync edilmemiş</span>'}
                                </div>
                            </div>`;
                        }).join('');

                        // Context7: Single DOM update with requestAnimationFrame for better performance
                        requestAnimationFrame(() => {
                            container.innerHTML = html;
                        });

                        // Context7: Pagination controls (Alpine.js compatible)
                        if (totalPages > 1) {
                            // Remove existing pagination if any
                            const existingPagination = container.querySelector('.pagination-controls');
                            if (existingPagination) {
                                existingPagination.remove();
                            }

                            const paginationDiv = document.createElement('div');
                            paginationDiv.className =
                                'mt-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4 pagination-controls';
                            paginationDiv.innerHTML = `
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Gösterilen: ${startIndex + 1}-${Math.min(endIndex, filteredMahalleler.length)} / ${filteredMahalleler.length}
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="window.addressManagerInstance.mahallePage = Math.max(1, window.addressManagerInstance.mahallePage - 1); window.addressManagerInstance.renderMahalleler();"
                                            ${this.mahallePage === 1 ? 'disabled' : ''}
                                            class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                        Önceki
                                    </button>
                                    <span class="px-3 py-1 text-sm text-gray-700 dark:text-gray-300">
                                        Sayfa ${this.mahallePage} / ${totalPages}
                                    </span>
                                    <button onclick="window.addressManagerInstance.mahallePage = Math.min(${totalPages}, window.addressManagerInstance.mahallePage + 1); window.addressManagerInstance.renderMahalleler();"
                                            ${this.mahallePage === totalPages ? 'disabled' : ''}
                                            class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                        Sonraki
                                    </button>
                                </div>
                            `;
                            container.appendChild(paginationDiv);
                        }

                        console.log(
                            `✅ ${paginatedMahalleler.length} mahalle render edildi (Sayfa ${this.mahallePage}/${totalPages}, Toplam: ${filteredMahalleler.length})`
                        );
                    },

                    refreshData() {
                        // Context7: Tüm verileri yeniden yükle
                        this.loadUlkeler();
                        this.loadIller();
                        this.loadAllIlceler();
                        this.loadAllMahalleler();
                    },

                    filterData() {
                        // Context7: Debounce filter operations for better performance
                        if (this.filterTimeout) {
                            clearTimeout(this.filterTimeout);
                        }

                        this.filterTimeout = setTimeout(() => {
                            // Arama ve filtreleme mantığı
                            let allData = [];

                            // Tüm verileri birleştir (optimized with map)
                            allData.push(...this.ulkeler.map(item => ({
                                ...item,
                                type: 'ulke',
                                displayName: item.ulke_adi
                            })));
                            allData.push(...this.iller.map(item => ({
                                ...item,
                                type: 'il',
                                displayName: item.il_adi
                            })));
                            allData.push(...this.ilceler.map(item => ({
                                ...item,
                                type: 'ilce',
                                displayName: item.ilce_adi
                            })));
                            allData.push(...this.mahalleler.map(item => ({
                                ...item,
                                type: 'mahalle',
                                displayName: item.mahalle_adi
                            })));

                            // Filtreleme
                            const searchLower = this.searchQuery ? this.searchQuery.toLowerCase() : '';
                            this.filteredData = allData.filter(item => {
                                const matchesSearch = !searchLower ||
                                    item.displayName.toLowerCase().includes(searchLower);
                                const matchesType = !this.filterType || item.type === this.filterType;
                                return matchesSearch && matchesType;
                            });

                            // Sıralama
                            this.sortData();
                        }, 150); // 150ms debounce
                    },

                    sortData() {
                        if (this.filteredData.length > 0) {
                            this.filteredData.sort((a, b) => {
                                const comparison = a.displayName.localeCompare(b.displayName, 'tr');
                                return this.sortOrder === 'asc' ? comparison : -comparison;
                            });
                        }
                    },

                    showFilteredResults() {
                        // Filtrelenmiş sonuçları göster
                        if (this.filteredData.length > 0) {
                            // Modal veya dropdown ile sonuçları göster
                            console.log('Filtrelenmiş sonuçlar:', this.filteredData);
                        }
                    },

                    // Bulk Actions Functions
                    toggleItemSelection(id, type) {
                        if (this.selectedItemsType && this.selectedItemsType !== type) {
                            // Farklı tipte seçim yapılıyorsa, önceki seçimi temizle
                            this.selectedItems = [];
                        }

                        this.selectedItemsType = type;

                        const index = this.selectedItems.indexOf(id);
                        if (index > -1) {
                            this.selectedItems.splice(index, 1);
                        } else {
                            this.selectedItems.push(id);
                        }

                        // Eğer hiç seçim yoksa, type'ı da temizle
                        if (this.selectedItems.length === 0) {
                            this.selectedItemsType = null;
                        }
                    },

                    toggleSelectAll(type) {
                        let items = [];
                        switch (type) {
                            case 'ulke':
                                items = this.ulkeler.map(u => u.id);
                                break;
                            case 'il':
                                items = this.iller.map(i => i.id);
                                break;
                            case 'ilce':
                                items = this.ilceler.map(i => i.id);
                                break;
                            case 'mahalle':
                                items = this.mahalleler.map(m => m.id);
                                break;
                        }

                        // Tümünü seç veya seçimi kaldır
                        const allSelected = items.every(id => this.selectedItems.includes(id) && this.selectedItemsType ===
                            type);

                        if (allSelected) {
                            // Tümünü kaldır
                            this.selectedItems = this.selectedItems.filter(id => !items.includes(id));
                        } else {
                            // Tümünü seç
                            if (this.selectedItemsType !== type) {
                                this.selectedItems = [];
                            }
                            this.selectedItemsType = type;
                            items.forEach(id => {
                                if (!this.selectedItems.includes(id)) {
                                    this.selectedItems.push(id);
                                }
                            });
                        }

                        // Render'ları güncelle
                        this.renderUlkeler();
                        this.renderIller();
                        this.renderIlceler();
                        this.renderMahalleler();
                    },

                    clearSelection() {
                        this.selectedItems = [];
                        this.selectedItemsType = null;
                        // Render'ları güncelle
                        this.renderUlkeler();
                        this.renderIller();
                        this.renderIlceler();
                        this.renderMahalleler();
                    },

                    async bulkDelete() {
                        if (this.selectedItems.length === 0 || !this.selectedItemsType) {
                            window.toast?.error('Lütfen silmek için öğe seçin');
                            return;
                        }

                        const typeName = {
                            'ulke': 'ülke',
                            'il': 'il',
                            'ilce': 'ilçe',
                            'mahalle': 'mahalle'
                        } [this.selectedItemsType] || 'öğe';

                        if (!confirm(`Seçili ${this.selectedItems.length} ${typeName} silinecek. Emin misiniz?`)) {
                            return;
                        }

                        try {
                            // Context7: ID'leri integer'a çevir (string ID'ler için)
                            const ids = this.selectedItems.map(id => parseInt(id, 10)).filter(id => !isNaN(id));

                            if (ids.length === 0) {
                                window.toast?.error('Geçerli ID bulunamadı');
                                return;
                            }

                            const response = await fetch('{{ route('admin.adres-yonetimi.bulk-delete') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    type: this.selectedItemsType,
                                    ids: ids
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                window.toast?.success(data.message || `${ids.length} ${typeName} başarıyla silindi`);
                                this.clearSelection();
                                this.refreshData();
                            } else {
                                // Context7: Validation hatalarını göster
                                let errorMessage = data.message || 'Silme işlemi başarısız';
                                if (data.errors) {
                                    const errorDetails = Object.values(data.errors).flat().join(', ');
                                    errorMessage += ': ' + errorDetails;
                                }
                                window.toast?.error(errorMessage);
                                console.error('Bulk delete validation errors:', data.errors);
                            }
                        } catch (error) {
                            console.error('Bulk delete error:', error);
                            window.toast?.error('Silme işlemi sırasında hata oluştu: ' + error.message);
                        }
                    },

                    /**
                     * TurkiyeAPI'den seçili il/ilçe/mahalleleri çek (sync etmeden sadece göster)
                     * Context7: Seçimli veri çekme - Kullanıcı istediği lokasyonları seçerek çekebilir
                     */
                    async fetchFromTurkiyeAPI() {
                        if (!this.fetchSelectedIlId && !this.fetchSelectedIlceId) {
                            window.toast?.error('Lütfen en az bir il veya ilçe seçin');
                            return;
                        }

                        // Context7: Önce mevcut fetch verilerini temizle
                        this.fetchedData = null;
                        
                        // Eğer ilçe seçildiyse, önce o ilçeye ait mahalleleri temizle (sadece fetch'ten gelenler)
                        if (this.fetchSelectedIlceId) {
                            this.mahalleler = this.mahalleler.filter(m => 
                                !m._from_turkiyeapi || m.ilce_id != this.fetchSelectedIlceId
                            );
                        }
                        
                        // Eğer il seçildiyse, önce o ile ait ilçeleri temizle (sadece fetch'ten gelenler)
                        if (this.fetchSelectedIlId) {
                            this.ilceler = this.ilceler.filter(i => 
                                !i._from_turkiyeapi || i.il_id != this.fetchSelectedIlId
                            );
                        }

                        this.fetching = true;

                        try {
                            const requestBody = {
                                type: 'auto'
                            };

                            // Seçilen il varsa ekle
                            if (this.fetchSelectedIlId) {
                                requestBody.province_id = parseInt(this.fetchSelectedIlId);
                            }

                            // Seçilen ilçe varsa ekle
                            if (this.fetchSelectedIlceId) {
                                requestBody.district_id = parseInt(this.fetchSelectedIlceId);
                            }

                            const response = await fetch('{{ route('admin.adres-yonetimi.fetch-from-turkiyeapi') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(requestBody)
                            });

                            const data = await response.json();

                            // Debug: API response'unu console'a yazdır
                            console.log('🔍 TurkiyeAPI Fetch Response:', data);
                            console.log('🔍 Raw Data:', JSON.stringify(data, null, 2));

                            if (data.success) {
                                this.fetchedData = data.data || {};
                                const counts = data.counts || {};

                                // Debug: Çekilen verileri detaylı göster
                                console.log('📊 Çekilen Veriler:', this.fetchedData);
                                console.log('📊 Neighborhoods Array:', this.fetchedData.neighborhoods);
                                console.log('📊 Neighborhoods Length:', this.fetchedData.neighborhoods?.length);
                                console.log('📈 İstatistikler:', counts);
                                console.log('📈 Neighborhoods Count:', counts.neighborhoods);

                                // Çekilen verileri göster
                                let message = `✅ TurkiyeAPI'den veriler çekildi!\n\n`;
                                
                                if (counts.districts > 0) {
                                    message += `📊 İlçeler: ${counts.districts}\n`;
                                    // Context7: Çekilen ilçeleri listeye ekle (önce temizlendi, şimdi yeni verileri ekle)
                                    if (data.data.districts && data.data.districts.length > 0) {
                                        // Önce mevcut fetch'ten gelen ilçeleri kaldır (zaten temizlendi ama emin olmak için)
                                        this.ilceler = this.ilceler.filter(ilce => 
                                            !ilce._from_turkiyeapi || ilce.il_id != this.fetchSelectedIlId
                                        );
                                        
                                        // Yeni çekilen ilçeleri ekle
                                        data.data.districts.forEach(turkiyeIlce => {
                                            // Local DB'de var mı kontrol et
                                            const existingIlce = this.ilceler.find(ilce => 
                                                ilce.il_id == this.fetchSelectedIlId && 
                                                ilce.ilce_adi.toLowerCase() === turkiyeIlce.name.toLowerCase() &&
                                                ilce.id !== null // DB'de kayıtlı olanlar
                                            );
                                            
                                            if (!existingIlce) {
                                                // Yeni ilçe - listeye ekle
                                                this.ilceler.push({
                                                    id: null, // Henüz DB'de yok
                                                    il_id: this.fetchSelectedIlId,
                                                    ilce_adi: turkiyeIlce.name,
                                                    _from_turkiyeapi: true // İşaretle
                                                });
                                            }
                                        });
                                        this.renderIlceler();
                                    }
                                }

                                // Debug: Mahalle kontrolü
                                console.log('🔍 Mahalle Kontrolü:');
                                console.log('  - counts.neighborhoods:', counts.neighborhoods);
                                console.log('  - counts.towns:', counts.towns);
                                console.log('  - counts.villages:', counts.villages);
                                console.log('  - data.data.neighborhoods:', data.data?.neighborhoods);
                                console.log('  - data.data.neighborhoods?.length:', data.data?.neighborhoods?.length);
                                console.log('  - fetchSelectedIlId:', this.fetchSelectedIlId);
                                console.log('  - fetchSelectedIlceId:', this.fetchSelectedIlceId);

                                // Context7: İl seçildiyse ama ilçe seçilmemişse, çekilen mahalleleri tüm ilçelere dağıt
                                if (this.fetchSelectedIlId && !this.fetchSelectedIlceId && data.data?.neighborhoods?.length > 0) {
                                    console.log('⚠️ İl seçildi ama ilçe seçilmedi - Mahalleler ilçelere dağıtılamıyor');
                                    console.log('💡 Lütfen bir ilçe seçin veya ilçe seçerek mahalleleri çekin');
                                }

                                if (counts.neighborhoods > 0 || counts.towns > 0 || counts.villages > 0) {
                                    message += `📍 Mahalleler: ${counts.neighborhoods}\n`;
                                    message += `🏖️ Beldeler: ${counts.towns}\n`;
                                    message += `🏘️ Köyler: ${counts.villages}\n`;
                                    message += `📊 Toplam: ${counts.total} lokasyon\n\n`;
                                    message += `💡 Bu verileri veritabanına kaydetmek için "Sync Et" butonunu kullanabilirsiniz.`;

                                    // Context7: Çekilen mahalleleri listeye ekle (önce temizlendi, şimdi yeni verileri ekle)
                                    const neighborhoods = data.data?.neighborhoods || [];
                                    console.log('🔍 Neighborhoods Array:', neighborhoods);
                                    console.log('🔍 Neighborhoods Length:', neighborhoods.length);
                                    
                                    if (neighborhoods.length > 0) {
                                        console.log('✅ Mahalleler bulundu, listeye ekleniyor...');
                                        
                                        // Context7: İlçe seçildiyse sadece o ilçeye ait mahalleleri temizle
                                        // İl seçildiyse tüm fetch'ten gelen mahalleleri temizle
                                        if (this.fetchSelectedIlceId) {
                                            this.mahalleler = this.mahalleler.filter(mahalle => 
                                                !mahalle._from_turkiyeapi || mahalle.ilce_id != this.fetchSelectedIlceId
                                            );
                                        } else if (this.fetchSelectedIlId) {
                                            // İl seçildiyse, bu ile ait tüm fetch'ten gelen mahalleleri temizle
                                            const selectedIlIlceler = this.ilceler.filter(i => i.il_id == this.fetchSelectedIlId);
                                            const selectedIlIlceIds = selectedIlIlceler.map(i => i.id).filter(id => id !== null);
                                            this.mahalleler = this.mahalleler.filter(mahalle => 
                                                !mahalle._from_turkiyeapi || !selectedIlIlceIds.includes(mahalle.ilce_id)
                                            );
                                        }
                                        
                                        console.log('🔍 Temizleme sonrası mahalle sayısı:', this.mahalleler.length);
                                        
                                        // Yeni çekilen mahalleleri ekle
                                        neighborhoods.forEach((turkiyeMahalle, index) => {
                                            console.log(`🔍 Mahalle ${index + 1}:`, turkiyeMahalle);
                                            
                                            // İlçe ID'sini belirle
                                            let ilceId = this.fetchSelectedIlceId;
                                            
                                            // Eğer ilçe seçilmemişse ama il seçilmişse, mahallenin hangi ilçeye ait olduğunu bul
                                            if (!ilceId && this.fetchSelectedIlId && turkiyeMahalle.districtId) {
                                                ilceId = turkiyeMahalle.districtId;
                                            }
                                            
                                            // Eğer hala ilçe ID yoksa, districts array'inden bul
                                            if (!ilceId && this.fetchSelectedIlId && data.data?.districts) {
                                                // İlk district'i kullan (genellikle tek ilçe seçildiğinde)
                                                const firstDistrict = data.data.districts.find(d => d.id == this.fetchSelectedIlceId || d.neighborhoods?.some(n => n.name === turkiyeMahalle.name));
                                                if (firstDistrict) {
                                                    ilceId = firstDistrict.id;
                                                }
                                            }
                                            
                                            if (!ilceId) {
                                                console.warn('⚠️ Mahalle için ilçe ID bulunamadı:', turkiyeMahalle);
                                                console.warn('⚠️ fetchSelectedIlceId:', this.fetchSelectedIlceId);
                                                console.warn('⚠️ fetchSelectedIlId:', this.fetchSelectedIlId);
                                                return;
                                            }
                                            
                                            // Local DB'de var mı kontrol et
                                            const existingMahalle = this.mahalleler.find(mahalle => 
                                                mahalle.ilce_id == ilceId && 
                                                mahalle.mahalle_adi.toLowerCase() === turkiyeMahalle.name.toLowerCase() &&
                                                mahalle.id !== null // DB'de kayıtlı olanlar
                                            );
                                            
                                            if (!existingMahalle) {
                                                // Yeni mahalle - listeye ekle
                                                const newMahalle = {
                                                    id: null, // Henüz DB'de yok
                                                    ilce_id: ilceId,
                                                    mahalle_adi: turkiyeMahalle.name,
                                                    _from_turkiyeapi: true // İşaretle
                                                };
                                                console.log('✅ Yeni mahalle eklendi:', newMahalle);
                                                this.mahalleler.push(newMahalle);
                                            } else {
                                                console.log('⚠️ Mahalle zaten var:', existingMahalle);
                                            }
                                        });
                                        
                                        console.log('🔍 Eklenme sonrası mahalle sayısı:', this.mahalleler.length);
                                        console.log('🔍 Render ediliyor...');
                                        this.renderMahalleler();
                                        console.log('✅ Render tamamlandı');
                                    } else {
                                        console.warn('⚠️ Mahalleler bulunamadı veya boş array');
                                        if (this.fetchSelectedIlId && !this.fetchSelectedIlceId) {
                                            console.warn('💡 İpucu: Mahalleler için bir ilçe seçmeniz gerekiyor');
                                        }
                                    }
                                } else {
                                    console.warn('⚠️ Counts kontrolü başarısız - mahalleler, beldeler veya köyler yok');
                                }

                                window.toast?.success(message);
                                
                                // Console'da detaylı bilgi göster
                                console.group('📥 TurkiyeAPI\'den Çekilen Veriler');
                                console.log('Seçilen İl ID:', this.fetchSelectedIlId);
                                console.log('Seçilen İlçe ID:', this.fetchSelectedIlceId);
                                console.log('Çekilen Veriler:', data.data);
                                console.log('İstatistikler:', counts);
                                console.groupEnd();
                            } else {
                                window.toast?.error('Veri çekme hatası: ' + (data.message || 'Bilinmeyen hata'));
                            }
                        } catch (error) {
                            console.error('Fetch error:', error);
                            window.toast?.error('Veri çekme işlemi sırasında hata oluştu: ' + error.message);
                        } finally {
                            this.fetching = false;
                        }
                    },

                    /**
                     * TurkiyeAPI'den veri sync et
                     * Context7: Hybrid Approach - TurkiyeAPI sync + Local DB CRUD
                     * Seçime göre sync: İl ve/veya İlçe seçilerek sadece seçilenler için sync yapılabilir
                     */
                    async syncFromTurkiyeAPI() {
                        // Context7: Debug - Sync başlangıç logları
                        console.log('🔍 syncFromTurkiyeAPI çağrıldı');
                        console.log('🔍 syncSelectedIlId:', this.syncSelectedIlId);
                        console.log('🔍 syncSelectedIlceId:', this.syncSelectedIlceId);
                        console.log('🔍 fetchSelectedIlId:', this.fetchSelectedIlId);
                        console.log('🔍 fetchSelectedIlceId:', this.fetchSelectedIlceId);
                        
                        // Context7: Fetch'ten sonra sync yapılıyorsa fetch seçimlerini kullan
                        let provinceId = this.syncSelectedIlId || this.fetchSelectedIlId;
                        let districtId = this.syncSelectedIlceId || this.fetchSelectedIlceId;

                        console.log('🔍 Belirlenen provinceId:', provinceId);
                        console.log('🔍 Belirlenen districtId:', districtId);

                        // Context7: Eğer hiçbir seçim yapılmamışsa, kullanıcıya uyarı göster
                        if (!provinceId && !districtId) {
                            console.warn('⚠️ Sync iptal edildi: Hiçbir seçim yapılmamış');
                            window.toast?.error('Lütfen önce bir il veya ilçe seçin. Tüm verileri sync etmek çok uzun sürebilir ve zaman aşımına neden olabilir.');
                            return;
                        }

                        // Sync tipini belirle
                        let syncType = 'all';
                        let syncMessage =
                            'TurkiyeAPI\'den tüm lokasyon verilerini sync etmek istediğinizden emin misiniz?\n\nBu işlem:\n- İlleri sync edecek\n- İlçeleri sync edecek\n- Mahalleleri sync edecek\n\nBu işlem biraz zaman alabilir.';

                        if (districtId) {
                            // Sadece seçilen ilçe için mahalleler sync edilecek
                            syncType = 'neighborhoods';
                            const selectedIlce = this.ilceler.find(i => i.id == districtId);
                            syncMessage =
                                `TurkiyeAPI'den "${selectedIlce?.ilce_adi || 'Seçilen İlçe'}" için mahalleleri sync etmek istediğinizden emin misiniz?`;
                        } else if (provinceId) {
                            // Sadece seçilen il için ilçeler ve mahalleler sync edilecek
                            syncType = 'districts';
                            const selectedIl = this.iller.find(i => i.id == provinceId);
                            syncMessage =
                                `TurkiyeAPI'den "${selectedIl?.il_adi || 'Seçilen İl'}" için ilçeleri ve mahalleleri sync etmek istediğinizden emin misiniz?\n\nBu işlem biraz zaman alabilir.`;
                        }

                        if (!confirm(syncMessage)) {
                            return;
                        }

                        this.syncing = true;

                        try {
                            const requestBody = {
                                type: syncType
                            };

                            // Seçilen il varsa ekle (sync veya fetch seçimlerinden)
                            if (provinceId) {
                                requestBody.province_id = parseInt(provinceId);
                            }

                            // Seçilen ilçe varsa ekle (sync veya fetch seçimlerinden)
                            if (districtId) {
                                // Context7: districtId TurkiyeAPI ID'si olabilir, veritabanındaki ID'ye çevir
                                let dbDistrictId = parseInt(districtId);
                                
                                // Eğer fetch'ten gelen bir ilçe ID'si ise, veritabanındaki ID'yi bul
                                const fetchIlce = this.ilceler.find(i => 
                                    i.id == districtId || 
                                    (i._from_turkiyeapi && i.il_id == provinceId && this.fetchSelectedIlceId == districtId)
                                );
                                
                                if (fetchIlce && fetchIlce.id === null) {
                                    // Fetch'ten gelen ilçe henüz DB'de yok, ilçe adına göre bul
                                    const dbIlce = this.ilceler.find(i => 
                                        i.id !== null && 
                                        i.il_id == provinceId && 
                                        i.ilce_adi.toLowerCase() === fetchIlce.ilce_adi.toLowerCase()
                                    );
                                    
                                    if (dbIlce) {
                                        dbDistrictId = dbIlce.id;
                                        console.log(`🔍 İlçe ID eşleştirildi - TurkiyeAPI ID: ${districtId}, DB ID: ${dbDistrictId}`);
                                    } else {
                                        console.warn(`⚠️ İlçe bulunamadı - TurkiyeAPI ID: ${districtId}, İlçe Adı: ${fetchIlce.ilce_adi}`);
                                        // İlçe DB'de yoksa, sync işlemi ilçeyi de oluşturmalı
                                        // Şimdilik TurkiyeAPI ID'sini gönder, backend'de çözülsün
                                    }
                                }
                                
                                requestBody.district_id = dbDistrictId;
                                console.log(`🔍 Sync için district_id: ${dbDistrictId} (orijinal: ${districtId})`);
                            }

                            const response = await fetch('{{ route('admin.adres-yonetimi.sync-from-turkiyeapi') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify(requestBody)
                            });

                            // Context7: Response'un JSON olup olmadığını kontrol et
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                const text = await response.text();
                                console.error('Sync error - Non-JSON response:', text);
                                window.toast?.error('Sync hatası: Sunucu hatası (500). Lütfen daha küçük bir seçim yapın veya tekrar deneyin.');
                                return;
                            }

                            const data = await response.json();
                            
                            // Context7: Debug - Sync response'unu logla
                            console.log('🔍 Sync Response:', data);
                            console.log('🔍 Sync Success:', data.success);
                            console.log('🔍 Sync Results:', data.results);

                            if (data.success) {
                                const results = data.results || {};
                                let message = `✅ Sync tamamlandı!\n\n`;

                                if (results.provinces) {
                                    message += `İller: ${results.provinces}\n`;
                                }
                                if (results.districts) {
                                    message += `İlçeler: ${results.districts}\n`;
                                }
                                if (results.neighborhoods) {
                                    message += `Mahalleler: ${results.neighborhoods}\n`;
                                }

                                window.toast?.success(message);
                                
                                // Context7: Sync tamamlandıktan sonra fetch verilerini temizle
                                this.fetchedData = null;
                                
                                // Context7: Seçili ilçeyi koru ki sync edilen mahalleler görünsün
                                const selectedIlceId = this.selectedIlce || districtId;
                                
                                this.fetchSelectedIlId = null;
                                this.fetchSelectedIlceId = null;
                                
                                // Context7: Verileri yeniden yükle
                                this.refreshData();
                                
                                // Context7: Sync sonrası seçili ilçeyi tekrar seç ki mahalleler görünsün
                                if (selectedIlceId) {
                                    setTimeout(() => {
                                        this.selectIlce(selectedIlceId);
                                        console.log(`🔍 Sync sonrası ilçe tekrar seçildi: ${selectedIlceId}`);
                                    }, 500); // Veriler yüklendikten sonra seç
                                }

                                // Seçimleri temizle
                                this.syncSelectedIlId = null;
                                this.syncSelectedIlceId = null;
                            } else {
                                console.error('❌ Sync hatası:', data);
                                window.toast?.error('Sync hatası: ' + (data.message || 'Bilinmeyen hata'));
                            }
                        } catch (error) {
                            console.error('Sync error:', error);
                            window.toast?.error('Sync işlemi sırasında hata oluştu: ' + error.message);
                        } finally {
                            this.syncing = false;
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
