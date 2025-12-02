    <nav class="flex-1 px-4 py-6 overflow-y-auto" x-data="{ query: '' }">
        <div class="mb-4">
            <label for="sidebar-search" class="sr-only">Menüde ara</label>
            <input id="sidebar-search" x-model.debounce.200ms="query" type="text" placeholder="Menüde ara..."
                class="w-full h-9 px-3 rounded-lg bg-slate-800 placeholder:text-slate-400 text-slate-100 border border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                autocomplete="off" maxlength="50" pattern="[a-zA-ZğüşıöçĞÜŞİÖÇ0-9\s\-_]+"
                title="Sadece harf, rakam ve temel karakterler kullanın" />
        </div>
        <ul class="space-y-1" role="menu">
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())">
                <a href="{{ route('admin.dashboard.index') }}"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.dashboard.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}"
                    role="menuitem">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="9" />
                        <rect x="14" y="3" width="7" height="5" />
                        <rect x="14" y="12" width="7" height="9" />
                        <rect x="3" y="16" width="7" height="5" />
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())">
                <a href="{{ route('admin.kullanicilar.index') }}"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.kullanicilar.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}"
                    role="menuitem">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                    </svg>
                    <span>Kullanıcılar</span>
                </a>
            </li>
            <!-- İlan Yönetimi Dropdown (Mantıksal Gruplama) -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: {{ request()->routeIs('admin.ilanlar.*', 'admin.ilan-kategorileri.*', 'admin.ozellikler.*', 'admin.property_types.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="ilan-yonetimi-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ilanlar.*', 'admin.ilan-kategorileri.*', 'admin.ozellikler.*', 'admin.property_types.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14,2 14,8 20,8" />
                    </svg>
                    <span>İlan Yönetimi</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="ilan-yonetimi-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">

                    <!-- İlan İşlemleri Grubu -->
                    <div class="pt-2 pb-1">
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1">📋 İlan
                            İşlemleri</div>
                        <a href="{{ route('admin.ilanlar.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ilanlar.index') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Tüm İlanlar</span>
                        </a>
                        <a href="{{ route('admin.ilanlar.create') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ilanlar.create') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9v-2h2v2zm0-4H9V5h2v4z" />
                            </svg>
                            <span>Yeni İlan</span>
                            <span
                                class="ml-auto text-xs bg-green-500/20 text-green-600 dark:text-green-200 px-1.5 py-0.5 rounded transition-colors">AI</span>
                        </a>
                    </div>

                    <!-- Kategori Sistemi Grubu -->
                    <div class="pt-2 pb-1 border-t border-slate-700">
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1">🏷️
                            Kategori Sistemi</div>
                        <a href="{{ route('admin.ilan-kategorileri.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ilan-kategorileri.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>İlan Kategorileri</span>
                        </a>
                        <a href="{{ route('admin.property_types.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.property_types.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Yayın Tipi Yöneticisi</span>
                            <span
                                class="ml-auto text-xs bg-green-500/20 text-green-600 dark:text-green-200 px-1.5 py-0.5 rounded transition-colors">Yeni</span>
                        </a>
                    </div>

                    <!-- Özellik Sistemi Grubu -->
                    <div class="pt-2 pb-1 border-t border-slate-700">
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1">⚙️ Özellik
                            Sistemi</div>
                        <a href="{{ route('admin.ozellikler.kategoriler.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ozellikler.kategoriler.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Özellik Grupları</span>
                        </a>
                        <a href="{{ route('admin.ozellikler.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ozellikler.*') && !request()->routeIs('admin.ozellikler.kategoriler.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Özellikler</span>
                        </a>
                        {{-- 🏘️ Site Özellikleri - Yeni Polymorphic System'e yönlendir --}}
                        <a href="{{ route('admin.ozellikler.kategoriler.show', 5) }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ozellikler.kategoriler.show') && request()->route('id') == 5 ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem" title="Site Özellikleri (Polymorphic Sistem)">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="CurrentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Site Özellikleri</span>
                        </a>
                    </div>
                </div>
            </li>
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())">
                <a href="{{ route('admin.danisman.index') }}"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.danisman.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}"
                    role="menuitem">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="8.5" cy="7" r="4" />
                        <polyline points="17,11 19,13 23,9" />
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    </svg>
                    <span>Danışmanlar</span>
                </a>
            </li>
            <!-- CRM Dropdown Menüsü -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: {{ request()->routeIs('admin.crm.*', 'admin.kisiler.*', 'admin.talepler.*', 'admin.eslesmeler.*', 'admin.talep-portfolyo.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="crm-yonetimi-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.crm.*', 'admin.kisiler.*', 'admin.talepler.*', 'admin.eslesmeler.*', 'admin.talep-portfolyo.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                    <span>CRM Yönetimi</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="crm-yonetimi-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">
                    @if (\Illuminate\Support\Facades\Route::has('admin.crm.dashboard'))
                        <a href="{{ route('admin.crm.dashboard') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.crm.dashboard') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>CRM Dashboard</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.kisiler.index'))
                        <a href="{{ route('admin.kisiler.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.kisiler.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Kişiler</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.talepler.index'))
                        <a href="{{ route('admin.talepler.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.talepler.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Talepler</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.eslesmeler.index'))
                        <a href="{{ route('admin.eslesmeler.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.eslesmeler.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Eşleştirmeler</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.talep-portfolyo.index'))
                        <a href="{{ route('admin.talep-portfolyo.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.talep-portfolyo.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Talep-Portföy</span>
                        </a>
                    @endif
                </div>
            </li>
            <!-- Finans Yönetimi Dropdown -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: {{ request()->routeIs('admin.finans.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="finans-yonetimi-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.finans.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Finans Yönetimi</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="finans-yonetimi-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">
                    @if (\Illuminate\Support\Facades\Route::has('admin.finans.islemler.index'))
                        <a href="{{ route('admin.finans.islemler.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.finans.islemler.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4 text-blue-500 dark:text-blue-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>Finansal İşlemler</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.finans.islemler.create'))
                        <a href="{{ route('admin.finans.islemler.create') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.finans.islemler.create') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4 text-green-500 dark:text-green-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Yeni İşlem</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.finans.komisyonlar.index'))
                        <a href="{{ route('admin.finans.komisyonlar.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.finans.komisyonlar.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4 text-yellow-500 dark:text-yellow-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Komisyonlar</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.finans.komisyonlar.create'))
                        <a href="{{ route('admin.finans.komisyonlar.create') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.finans.komisyonlar.create') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4 text-green-500 dark:text-green-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Yeni Komisyon</span>
                        </a>
                    @endif
                </div>
            </li>
            <!-- Yazlık Kiralama Dropdown -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: {{ request()->routeIs('admin.yazlik-kiralama.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="yazlik-kiralama-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.yazlik-kiralama.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Yazlık Kiralama</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="yazlik-kiralama-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">
                    @if (\Illuminate\Support\Facades\Route::has('admin.yazlik-kiralama.index'))
                        <a href="{{ route('admin.yazlik-kiralama.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.yazlik-kiralama.index') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4 text-blue-500 dark:text-blue-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span>Yazlık İlanları</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.yazlik-kiralama.takvim.index'))
                        <a href="{{ route('admin.yazlik-kiralama.takvim.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.yazlik-kiralama.takvim.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4 text-green-500 dark:text-green-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            <span>Takvim & Sezonlar</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.yazlik-kiralama.bookings'))
                        <a href="{{ route('admin.yazlik-kiralama.bookings') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.yazlik-kiralama.bookings') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4 text-purple-500 dark:text-purple-300" fill="none"
                                stroke="CurrentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>Rezervasyonlar</span>
                        </a>
                    @endif
                </div>
            </li>
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())">
                <a href="{{ route('admin.reports.index') }}"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}"
                    role="menuitem">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3v18h18" />
                        <path d="M18 17V9" />
                        <path d="M13 17V5" />
                        <path d="M8 17v-3" />
                    </svg>
                    <span>Raporlar</span>
                </a>
            </li>
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())">
                <a href="{{ route('admin.notifications.index') }}"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 {{ request()->routeIs('admin.notifications.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}"
                    role="menuitem">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                    </svg>
                    <span>Bildirimler</span>
                </a>
            </li>
            <!-- AI Sistemi Dropdown -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: {{ request()->routeIs('admin.ai-settings.*', 'admin.ai-monitor.*', 'admin.ai.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="ai-sistemi-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ai-settings.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 5a3 3 0 1 0-5.997.125 4 4 0 0 0-2.526 5.77 4 4 0 0 0 .556 6.588A4 4 0 1 0 12 18Z" />
                    </svg>
                    <span>AI Sistemi</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="ai-sistemi-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">
                    @if (\Illuminate\Support\Facades\Route::has('admin.ai.dashboard'))
                        <a href="{{ route('admin.ai.dashboard') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ai.dashboard') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>AI Command Center</span>
                            <span
                                class="ml-auto text-xs bg-green-500/20 text-green-600 dark:text-green-200 px-1.5 py-0.5 rounded transition-colors">Yeni</span>
                        </a>
                    @endif
                    <a href="{{ route('admin.ai-settings.index') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ai-settings.index') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <span>AI Ayarları</span>
                    </a>
                    <a href="{{ route('admin.ai-settings.analytics') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ai-settings.analytics') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>AI Analytics</span>
                    </a>
                    @if (\Illuminate\Support\Facades\Route::has('admin.ai-monitor.index'))
                        <a href="{{ route('admin.ai-monitor.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.ai-monitor.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>AI Monitoring</span>
                        </a>
                    @endif
                </div>
            </li>
            <!-- Takım Yönetimi Dropdown -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: {{ request()->routeIs('admin.takim-yonetimi.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="takim-yonetimi-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.takim-yonetimi.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857" />
                        <circle cx="12" cy="7" r="3" />
                    </svg>
                    <span>Takım Yönetimi</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="takim-yonetimi-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">
                    <a href="{{ route('admin.takim-yonetimi.takim.index') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.takim-yonetimi.takim.*') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <span>Takım Üyeleri</span>
                    </a>
                    <a href="{{ route('admin.takim-yonetimi.gorevler.index') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.takim-yonetimi.gorevler.*') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <span>Görevler</span>
                    </a>
                    <a href="{{ route('admin.takim-yonetimi.takim.performans') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.takim-yonetimi.takim.performans') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <span>Performans</span>
                    </a>
                </div>
            </li>
            <!-- Analytics Dropdown -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: {{ request()->routeIs('admin.analytics.*', 'admin.reports.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="analytics-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.analytics.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M16 8v8m-4-5v5m-4-2v2" />
                    </svg>
                    <span>Analytics</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="CurrentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="analytics-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">
                    <a href="{{ route('admin.analytics.index') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.analytics.index') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <span>Genel Analytics</span>
                    </a>
                    @if (\Illuminate\Support\Facades\Route::has('admin.analytics.dashboard'))
                        <a href="{{ route('admin.analytics.dashboard') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.analytics.dashboard') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4 text-blue-500 dark:text-blue-300" fill="none"
                                stroke="CurrentColor" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="9" />
                                <rect x="14" y="3" width="7" height="5" />
                                <rect x="14" y="12" width="7" height="9" />
                                <rect x="3" y="16" width="7" height="5" />
                            </svg>
                            <span>Analytics Dashboard</span>
                        </a>
                    @endif
                    <a href="{{ route('admin.reports.index') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem" title="Raporlar">
                        <span>Raporlar</span>
                    </a>
                </div>
            </li>
            <!-- Telegram Bot Dropdown -->
            <li x-data="{ open: {{ request()->routeIs('admin.telegram.*', 'admin.telegram-bot.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="telegram-bot-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.telegram.*', 'admin.telegram-bot.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M22 2L11 13" />
                        <path d="M22 2l-7 20-4-9-9-4 20-7z" />
                    </svg>
                    <span>Telegram Bot</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="telegram-bot-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">
                    <a href="{{ route('admin.telegram-bot.index') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.telegram-bot.index') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <span>Genel</span>
                    </a>
                    <a href="{{ route('admin.telegram-bot.status') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.telegram-bot.status') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <span>Durum</span>
                    </a>
                    <a href="{{ route('admin.telegram-bot.webhook-info') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.telegram-bot.webhook-info') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <span>Webhook Bilgisi</span>
                    </a>
                </div>
            </li>
            <!-- Blog Yönetimi Dropdown -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: {{ request()->routeIs('admin.blog.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="blog-yonetimi-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.blog.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <span>Blog Yönetimi</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="blog-yonetimi-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">
                    @if (\Illuminate\Support\Facades\Route::has('admin.blog.posts.index'))
                        <a href="{{ route('admin.blog.posts.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.blog.posts.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Yazılar</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.blog.categories.index'))
                        <a href="{{ route('admin.blog.categories.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.blog.categories.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Kategoriler</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.blog.comments.index'))
                        <a href="{{ route('admin.blog.comments.index') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.blog.comments.*') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <span>Yorumlar</span>
                        </a>
                    @endif
                </div>
            </li>
            <!-- Adres Yönetimi Dropdown -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: {{ request()->routeIs('admin.adres-yonetimi.*', 'admin.wikimapia-search.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="adres-yonetimi-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.adres-yonetimi.*', 'admin.wikimapia-search.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Adres Yönetimi</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="adres-yonetimi-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">
                    <a href="{{ route('admin.adres-yonetimi.index') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.adres-yonetimi.index') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <span>Adres Yönetimi</span>
                    </a>
                    <a href="{{ route('admin.wikimapia-search.index') }}"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.wikimapia-search.*') ? 'bg-blue-500 text-white' : '' }}"
                        role="menuitem">
                        <span>Wikimapia Arama</span>
                        <span
                            class="ml-auto text-xs bg-green-500/20 text-green-600 dark:text-green-200 px-1.5 py-0.5 rounded transition-colors">Yeni</span>
                    </a>
                </div>
            </li>
            <!-- My Listings (Benim İlanlarım) -->
            @if (\Illuminate\Support\Facades\Route::has('admin.my-listings.index'))
                <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())">
                    <a href="{{ route('admin.my-listings.index') }}"
                        class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 {{ request()->routeIs('admin.my-listings.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}"
                        role="menuitem">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                        </svg>
                        <span>Benim İlanlarım</span>
                    </a>
                </li>
            @endif
            <!-- Harita -->
            @if (\Illuminate\Support\Facades\Route::has('admin.map.index'))
                <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())">
                    <a href="{{ route('admin.map.index') }}"
                        class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 {{ request()->routeIs('admin.map.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}"
                        role="menuitem">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <span>Harita</span>
                    </a>
                </li>
            @endif
            <!-- Pazar İstihbaratı (Market Intelligence) Dropdown -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: {{ request()->routeIs('admin.market-intelligence.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="market-intelligence-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.market-intelligence.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Pazar İstihbaratı</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="market-intelligence-menu"
                    role="menu" class="ml-6 mt-1 space-y-1">
                    @if (\Illuminate\Support\Facades\Route::has('admin.market-intelligence.dashboard'))
                        <a href="{{ route('admin.market-intelligence.dashboard') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.market-intelligence.dashboard') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.market-intelligence.settings'))
                        <a href="{{ route('admin.market-intelligence.settings') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.market-intelligence.settings') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Bölge Ayarları</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.market-intelligence.compare'))
                        <a href="{{ route('admin.market-intelligence.compare') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.market-intelligence.compare') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Fiyat Karşılaştırma</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('admin.market-intelligence.trends'))
                        <a href="{{ route('admin.market-intelligence.trends') }}"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ request()->routeIs('admin.market-intelligence.trends') ? 'bg-blue-500 text-white' : '' }}"
                            role="menuitem">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            <span>Piyasa Trendleri</span>
                        </a>
                    @endif
                </div>
            </li>
            <!-- Smart Calculator -->
            @if (\Illuminate\Support\Facades\Route::has('admin.smart-calculator'))
                <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())">
                    <a href="{{ route('admin.smart-calculator') }}"
                        class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 {{ request()->routeIs('admin.smart-calculator') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}"
                        role="menuitem">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Akıllı Hesaplayıcı</span>
                    </a>
                </li>
            @endif
            <!-- System Tools / Developer Tools -->
            <li x-show="!query || ($el.innerText||'').toLowerCase().includes(query.toLowerCase())"
                x-data="{ open: false }">
                <button type="button" @click="open = !open" @keydown.enter.prevent="open = !open"
                    x-bind:aria-expanded="open" aria-controls="system-tools-menu" aria-haspopup="true"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 w-full transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>System Tools</span>
                    <svg class="w-4 h-4 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="CurrentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" id="system-tools-menu" role="menu"
                    class="ml-6 mt-1 space-y-1">

                    <!-- Laravel Horizon (Queue Monitoring) -->
                    <a href="/horizon" target="_blank" rel="noopener noreferrer"
                        class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm group transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        role="menuitem">
                        <svg class="w-4 h-4 text-purple-500 dark:text-purple-300" fill="none"
                            stroke="CurrentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Horizon (Queue)</span>
                        <span
                            class="ml-auto text-xs bg-purple-500/20 text-purple-400 px-1.5 py-0.5 rounded">FREE</span>
                    </a>

                    <!-- Laravel Telescope (Dev Debug) -->
                    @if (config('app.debug'))
                        <a href="/telescope" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm group transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                            role="menuitem">
                            <svg class="w-4 h-4 text-blue-500 dark:text-blue-300" fill="none"
                                stroke="CurrentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Telescope (Debug)</span>
                            <span
                                class="ml-auto text-xs bg-yellow-500/20 text-yellow-400 px-1.5 py-0.5 rounded">DEV</span>
                        </a>
                    @endif

                    <!-- Sentry (Error Tracking) -->
                    @if (config('sentry.dsn'))
                        <a href="https://sentry.io" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-2 h-9 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm group transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                            role="menuitem">
                            <svg class="w-4 h-4 text-red-500 dark:text-red-300" fill="none" stroke="CurrentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Sentry (Errors)</span>
                            <span class="ml-auto text-xs bg-red-500/20 text-red-400 px-1.5 py-0.5 rounded">FREE</span>
                        </a>
                    @endif

                    <!-- System Info -->
                    <div class="pt-2 mt-2 border-t border-slate-700">
                        <div class="px-4 py-2.5 text-xs text-slate-500">
                            <div class="flex justify-between mb-1">
                                <span>Laravel:</span>
                                <span class="text-slate-400">{{ app()->version() }}</span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span>PHP:</span>
                                <span class="text-slate-400">{{ PHP_VERSION }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Env:</span>
                                <span class="text-slate-400">{{ app()->environment() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>

            <li>
                <a href="{{ route('admin.ayarlar.index') }}"
                    class="flex items-center gap-3 h-11 px-3 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 {{ request()->routeIs('admin.ayarlar.*') ? 'bg-blue-600 text-white hover:bg-blue-700' : '' }}"
                    role="menuitem">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .69.28 1.32.73 1.77.45.45 1.08.73 1.77.73H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                    </svg>
                    <span>Ayarlar</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="p-4 border-t border-slate-800" x-data="{ userOpen: false }">
        <button @click="userOpen = !userOpen"
            class="flex items-center gap-3 w-full hover:bg-slate-800 rounded-lg p-2 transition-colors">
            <div
                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium">{{ strtoupper(substr(auth()->user()->name ?? 'AK', 0, 2)) }}</span>
            </div>
            <div class="min-w-0 flex-1 text-left">
                <p class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'Admin Kullanıcı' }}</p>
                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
            <svg class="w-4 h-4 transition-transform duration-200 text-slate-400" :class="{ 'rotate-180': userOpen }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- User Dropdown Menu -->
        <div x-show="userOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95" class="mt-2 space-y-1">
            @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-all duration-200 ease-in-out hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    role="menuitem">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Profil</span>
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 text-sm transition-colors w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Çıkış Yap</span>
                </button>
            </form>
        </div>
    </div>
</aside>
