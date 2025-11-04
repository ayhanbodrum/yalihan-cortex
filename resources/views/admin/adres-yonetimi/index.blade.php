@extends('admin.layouts.neo')

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
                    <button @click="showStatsModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        İstatistikler
                    </button>
                    <button @click="showAddModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
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
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all p-6">
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

            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all p-6">
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

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all p-6">
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

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all p-6">
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Ülkeler - Basic Theme (Mavi) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
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
                    <button @click="addItem('ulke')" class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </button>
                </div>
                <div id="list-ulkeler" class="text-sm text-gray-700 dark:text-gray-300">Yükleniyor...</div>
            </div>

            <!-- İller - Location Theme (Yeşil) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
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
                    <button @click="addItem('il')" class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50" :disabled="!selectedUlke">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </button>
                </div>
                <div id="list-iller" class="text-sm text-gray-700 dark:text-gray-300">Ülke seçin</div>
            </div>

            <!-- İlçeler - Features Theme (Mor) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
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
                    <button @click="addItem('ilce')" class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50" :disabled="!selectedIl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </button>
                </div>
                <div id="list-ilceler" class="text-sm text-gray-700 dark:text-gray-300">İl seçin</div>
            </div>

            <!-- Mahalleler - Media Theme (Turuncu) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all p-6">
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
                    <button @click="addItem('mahalle')" class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50" :disabled="!selectedIlce">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </button>
                </div>
                <div id="list-mahalleler" class="text-sm text-gray-700 dark:text-gray-300">İlçe seçin</div>
            </div>
        </div>

        <!-- İstatistikler Modal -->
        <div x-show="showStatsModal" x-transition
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-2xl p-6 w-full max-w-4xl mx-4 max-h-96 overflow-y-auto">
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
                    <button @click="showStatsModal = false" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                        Kapat
                    </button>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div x-show="showAddModal" x-transition
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-2xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editingItem ? 'Düzenle' : 'Yeni Ekle'"></h3>

                <form name="addressForm" @submit.prevent="saveItem()" novalidate>
                    @csrf
                    <div class="space-y-2 mb-4">
                        <label for="address_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span x-text="getFieldLabel()"></span>
                        </label>
                        <input type="text" id="address_name" name="name" x-model="formData.name" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" required>
                    </div>

                    <div class="space-y-2 mb-4" x-show="formData.type === 'il'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ülke</label>
                        <select style="color-scheme: light dark;" name="parent_id" x-model="formData.parent_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                            :required="formData.type === 'il'">
                            <option value="">Ülke Seçin</option>
                            <template x-for="ulke in ulkeler" :key="ulke.id">
                                <option :value="ulke.id" x-text="ulke.ulke_adi"></option>
                            </template>
                        </select>
                    </div>

                    <div class="space-y-2 mb-4" x-show="formData.type === 'ilce'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">İl</label>
                        <select style="color-scheme: light dark;" name="parent_id" x-model="formData.parent_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                            :required="formData.type === 'ilce'">
                            <option value="">İl Seçin</option>
                            <template x-for="il in iller" :key="il.id">
                                <option :value="il.id" x-text="il.il_adi"></option>
                            </template>
                        </select>
                    </div>

                    <div class="space-y-2 mb-4" x-show="formData.type === 'mahalle'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">İlçe</label>
                        <select style="color-scheme: light dark;" name="parent_id" x-model="formData.parent_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                            :required="formData.type === 'mahalle'">
                            <option value="">İlçe Seçin</option>
                            <template x-for="ilce in ilceler" :key="ilce.id">
                                <option :value="ilce.id" x-text="ilce.ilce_adi"></option>
                            </template>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="closeModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            İptal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
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
                                console.log(`✅ ${this.ulkeler.length} ülke yüklendi`);
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
                                console.log(`✅ ${this.iller.length} il yüklendi`);
                            }
                        } catch (error) {
                            console.error('Error loading iller:', error);
                            window.toast?.error('İller yüklenemedi');
                        }
                    },

                    async loadAllIlceler() {
                        try {
                            const response = await fetch('/api/ilceler');
                            const data = await response.json();
                            if (data.success || data.data) {
                                this.ilceler = data.data || data.ilceler || [];
                                this.renderIlceler();
                                console.log(`✅ ${this.ilceler.length} ilçe yüklendi`);
                            }
                        } catch (error) {
                            console.error('Error loading ilceler:', error);
                            window.toast?.error('İlçeler yüklenemedi');
                        }
                    },

                    async loadAllMahalleler() {
                        try {
                            const response = await fetch('/api/mahalleler');
                            const data = await response.json();
                            if (data.success || data.data) {
                                this.mahalleler = data.data || data.mahalleler || [];
                                this.renderMahalleler();
                                console.log(`✅ ${this.mahalleler.length} mahalle yüklendi`);
                            }
                        } catch (error) {
                            console.error('Error loading mahalleler:', error);
                            window.toast?.error('Mahalleler yüklenemedi');
                        }
                    },

                    renderUlkeler() {
                        const container = document.getElementById('list-ulkeler');
                        container.innerHTML = this.ulkeler.map(ulke => `
                <div class="flex items-center justify-between px-2 py-1 rounded border mr-2 mb-2 bg-gray-50">
                    <button data-ulke="${ulke.id}" class="flex-1 text-left hover:text-blue-600" @click="selectUlke(${ulke.id})">
                        ${ulke.ulke_adi}
                    </button>
                    <div class="flex space-x-1">
                        <button @click="editItem(${JSON.stringify(ulke).replace(/"/g, '&quot;')}, 'ulke')" class="text-yellow-600 hover:text-yellow-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button @click="deleteItem(${ulke.id}, 'ulke')" class="text-red-600 hover:text-red-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `).join('');
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
                        container.innerHTML = this.iller.map(il => `
                <div class="flex items-center justify-between px-2 py-1 rounded border mr-2 mb-2 bg-gray-50">
                    <button data-il="${il.id}" class="flex-1 text-left hover:text-blue-600" @click="selectIl(${il.id})">
                        ${il.il_adi}
                    </button>
                    <div class="flex space-x-1">
                        <button @click="editItem(${JSON.stringify(il).replace(/"/g, '&quot;')}, 'il')" class="text-yellow-600 hover:text-yellow-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button @click="deleteItem(${il.id}, 'il')" class="text-red-600 hover:text-red-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `).join('');
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
                        container.innerHTML = this.ilceler.map(ilce => `
                <div class="flex items-center justify-between px-2 py-1 rounded border mr-2 mb-2 bg-gray-50">
                    <button data-ilce="${ilce.id}" class="flex-1 text-left hover:text-blue-600" @click="selectIlce(${ilce.id})">
                        ${ilce.ilce_adi}
                    </button>
                    <div class="flex space-x-1">
                        <button @click="editItem(${JSON.stringify(ilce).replace(/"/g, '&quot;')}, 'ilce')" class="text-yellow-600 hover:text-yellow-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button @click="deleteItem(${ilce.id}, 'ilce')" class="text-red-600 hover:text-red-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `).join('');
                    },

                    async selectIlce(ilceId) {
                        this.selectedIlce = ilceId;

                        try {
                            const response = await fetch(
                                `{{ route('admin.adres-yonetimi.mahalleler.by-ilce', '') }}/${ilceId}`);
                            const data = await response.json();
                            if (data.success) {
                                this.mahalleler = data.mahalleler;
                                this.renderMahalleler();
                            }
                        } catch (error) {
                            console.error('Error loading mahalleler:', error);
                        }
                    },

                    renderMahalleler() {
                        const container = document.getElementById('list-mahalleler');
                        container.innerHTML = this.mahalleler.map(mahalle => `
                <div class="flex items-center justify-between px-2 py-1 rounded border mr-2 mb-2 bg-gray-50">
                    <span class="flex-1">${mahalle.mahalle_adi}</span>
                    <div class="flex space-x-1">
                        <button @click="editItem(${JSON.stringify(mahalle).replace(/"/g, '&quot;')}, 'mahalle')" class="text-yellow-600 hover:text-yellow-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button @click="deleteItem(${mahalle.id}, 'mahalle')" class="text-red-600 hover:text-red-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `).join('');
                    },

                    refreshData() {
                        if (this.selectedUlke) {
                            this.selectUlke(this.selectedUlke);
                        } else {
                            this.loadUlkeler();
                        }
                    },

                    filterData() {
                        // Arama ve filtreleme mantığı
                        let allData = [];

                        // Tüm verileri birleştir
                        this.ulkeler.forEach(item => {
                            allData.push({
                                ...item,
                                type: 'ulke',
                                displayName: item.ulke_adi
                            });
                        });
                        this.iller.forEach(item => {
                            allData.push({
                                ...item,
                                type: 'il',
                                displayName: item.il_adi
                            });
                        });
                        this.ilceler.forEach(item => {
                            allData.push({
                                ...item,
                                type: 'ilce',
                                displayName: item.ilce_adi
                            });
                        });
                        this.mahalleler.forEach(item => {
                            allData.push({
                                ...item,
                                type: 'mahalle',
                                displayName: item.mahalle_adi
                            });
                        });

                        // Filtreleme
                        this.filteredData = allData.filter(item => {
                            const matchesSearch = !this.searchQuery ||
                                item.displayName.toLowerCase().includes(this.searchQuery.toLowerCase());
                            const matchesType = !this.filterType || item.type === this.filterType;
                            return matchesSearch && matchesType;
                        });

                        // Sıralama
                        this.sortData();
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
                    }
                }
            }
        </script>
    @endpush
@endsection
