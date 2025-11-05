@php
    $user = auth()->user();
    $userRole = 'user'; // Default role

    if ($user) {
        try {
            // Spatie Permission roles kontrolü
            if (method_exists($user, 'roles') && $user->roles && $user->roles->isNotEmpty()) {
                $userRole = $user->roles->first()->name ?? 'user';
            }
            // Eski role ilişkisi kontrolü
            elseif (method_exists($user, 'role') && $user->role) {
                $userRole = $user->role->name ?? 'user';
            }
        } catch (Exception $e) {
            // Hata statusunda default role kullan
            $userRole = 'user';
        }
    }
@endphp

<!-- Context7 Sidebar Component -->
<aside class="flex flex-col h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 w-64 bg-gray-900 text-white transition-all duration-300 ease-in-out dark:bg-gray-950"
    x-data="{
        open: (localStorage.getItem('sidebar_open') ?? 'false') === 'true',
        activeMenu: localStorage.getItem('sidebar_active_menu') || null,
        toggleMenu(menu) {
            this.activeMenu = this.activeMenu === menu ? null : menu;
            localStorage.setItem('sidebar_active_menu', this.activeMenu ?? '');
        }
    }" @toggle-sidebar.window="open = !open; localStorage.setItem('sidebar_open', open.toString())"
    :class="{ 'w-16': !open, 'w-64': open }" aria-label="Ana menü">

    <!-- Sidebar Header -->
    <div class="flex flex-col h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700-header p-6 border-b border-gray-700 dark:border-gray-800">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 bg-gradient-to-r from-orange-500 to-amber-500 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95">
                <h1 class="text-xl font-bold text-white">Admin Panel</h1>
                <p class="text-sm text-gray-400">Yalıhan Emlak</p>
            </div>
        </div>

        <!-- Toggle Button -->
        <button @click="open = !open; $dispatch('toggle-sidebar')"
            class="absolute top-6 right-4 p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700 transition-colors duration-200">
            <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': !open }" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex flex-col h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700-nav p-4 space-y-2">

        <!-- Hızlı Erişim -->
        <div class="text-xs uppercase tracking-wide text-gray-400 px-2" x-show="open">Hızlı Erişim</div>
        <div class="space-y-1 mb-3">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('admin.dashboard.*') ? 'page' : '' }}"
               :data-tip="open ? '' : 'Dashboard'" :title="open ? '' : 'Dashboard'">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                </svg>
                <span x-show="open">Dashboard</span>
            </a>

            <!-- İlanlar -->
            @if (\Illuminate\Support\Facades\Route::has('admin.ilanlar.index'))
            <a href="{{ route('admin.ilanlar.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.ilanlar.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('admin.ilanlar.*') ? 'page' : '' }}"
               :data-tip="open ? '' : 'İlanlar'" :title="open ? '' : 'İlanlar'">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M3.75 5.25h16.5m-16.5 0h16.5m-16.5 0V21m16.5-15.75V21" />
                </svg>
                <span x-show="open">İlanlar</span>
            </a>
            @endif

            <!-- CRM (Kişiler) -->
            @if (\Illuminate\Support\Facades\Route::has('admin.kisiler.index'))
            <a href="{{ route('admin.kisiler.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.kisiler.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('admin.kisiler.*') ? 'page' : '' }}"
               :data-tip="open ? '' : 'Kişiler'" :title="open ? '' : 'Kişiler'">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span x-show="open">Kişiler</span>
            </a>
            @endif

            <!-- Raporlar -->
            @if (\Illuminate\Support\Facades\Route::has('admin.reports.index'))
            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('admin.reports.*') ? 'page' : '' }}"
               :data-tip="open ? '' : 'Raporlar'" :title="open ? '' : 'Raporlar'">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
                <span x-show="open">Raporlar</span>
            </a>
            @endif

            <!-- Bildirimler -->
            @if (\Illuminate\Support\Facades\Route::has('admin.notifications.index'))
            <a href="{{ route('admin.notifications.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('admin.notifications.*') ? 'page' : '' }}"
               :data-tip="open ? '' : 'Bildirimler'" :title="open ? '' : 'Bildirimler'">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <span x-show="open">Bildirimler</span>
            </a>
            @endif

            <!-- Ayarlar -->
            @if (\Illuminate\Support\Facades\Route::has('admin.ayarlar.index'))
            <a href="{{ route('admin.ayarlar.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.ayarlar.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('admin.ayarlar.*') ? 'page' : '' }}"
               :data-tip="open ? '' : 'Ayarlar'" :title="open ? '' : 'Ayarlar'">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span x-show="open">Ayarlar</span>
            </a>
            @endif
        </div>



        <!-- Kullanıcılar -->
        <a href="{{ route('admin.kullanicilar.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.kullanicilar.*') ? 'active' : '' }}"
            :data-tip="open ? '' : 'Kullanıcılar'" :title="open ? '' : 'Kullanıcılar'">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
            <span x-show="open">Kullanıcılar</span>
        </a>

        <!-- Danışmanlar -->
        <a href="{{ route('admin.danisman.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.danisman.*') ? 'active' : '' }}"
            :data-tip="open ? '' : 'Danışmanlar'" :title="open ? '' : 'Danışmanlar'">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Danışmanlar
        </a>

        <!-- CRM Yönetimi -->
        <div class="space-y-1 mb-4">
            <button @click="toggleMenu('crm')"
                class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip w-full flex items-center justify-between"
                :aria-expanded="activeMenu === 'crm'"
                aria-controls="menu-crm"
                :data-tip="open ? '' : 'CRM Yönetimi'" :title="open ? '' : 'CRM Yönetimi'">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    CRM Yönetimi
                </div>
                <svg class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': activeMenu === 'crm' }" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div id="menu-crm" x-show="activeMenu === 'crm'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" class="ml-6 space-y-1">

                <a href="{{ route('admin.kisiler.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.kisiler.*') ? 'active' : '' }}"
                    :data-tip="open ? '' : 'Kişiler'" :title="open ? '' : 'Kişiler'">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Kişiler
                </a>

                <a href="{{ route('admin.talepler.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.talepler.*') ? 'active' : '' }}"
                    :data-tip="open ? '' : 'Talepler'" :title="open ? '' : 'Talepler'">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192" />
                    </svg>
                    Talepler
                </a>

                <a href="{{ route('admin.eslesmeler.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.eslesmeler.*') ? 'active' : '' }}"
                    :data-tip="open ? '' : 'Eşleşmeler'" :title="open ? '' : 'Eşleşmeler'">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    Eşleşmeler
                </a>

                <a href="{{ route('admin.crm.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.crm.*') ? 'active' : '' }}"
                    :data-tip="open ? '' : 'AI Önerileri'" :title="open ? '' : 'AI Önerileri'">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                    </svg>
                    AI Önerileri
                </a>
            </div>
        </div>

        <!-- Talepler -->
        <a href="{{ route('admin.talepler.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.talepler.*') ? 'active' : '' }}"
            :data-tip="open ? '' : 'Talepler'" :title="open ? '' : 'Talepler'">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
            </svg>
            Talepler
        </a>

        <!-- İlanlar -->
        <a href="{{ route('admin.ilanlar.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.ilanlar.*') ? 'active' : '' }}"
            :data-tip="open ? '' : 'İlanlar'" :title="open ? '' : 'İlanlar'">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M3.75 5.25h16.5m-16.5 0h16.5m-16.5 0V21m16.5-15.75V21" />
            </svg>
            İlanlar
        </a>

        <!-- İlan Yönetimi -->
        <div class="space-y-1 mb-4">
            <button @click="toggleMenu('ilan-yonetimi')"
                class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip w-full flex items-center justify-between"
                :aria-expanded="activeMenu === 'ilan-yonetimi'"
                aria-controls="menu-ilan-yonetimi"
                :data-tip="open ? '' : 'İlan Yönetimi'" :title="open ? '' : 'İlan Yönetimi'">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    İlan Yönetimi
                </div>
                <svg class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': activeMenu === 'ilan-yonetimi' }" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div id="menu-ilan-yonetimi" x-show="activeMenu === 'ilan-yonetimi'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" class="ml-6 space-y-1">

                <a href="{{ route('admin.ilan-kategorileri.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.ilan-kategorileri.*') ? 'active' : '' }}"
                    :data-tip="open ? '' : 'Kategoriler'" :title="open ? '' : 'Kategoriler'">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Kategoriler
                </a>

                <a href="{{ route('module.ozellikler.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('module.ozellikler.*') ? 'active' : '' }}"
                    :data-tip="open ? '' : 'Özellikler'" :title="open ? '' : 'Özellikler'">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 4v12m0-8h8m-8 0H4" />
                    </svg>
                    Özellikler
                </a>

                <a href="{{ route('admin.property-type-manager.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.property-type-manager.*') ? 'active' : '' }}"
                    :data-tip="open ? '' : 'Yayın Tipi Yöneticisi'" :title="open ? '' : 'Yayın Tipi Yöneticisi'">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14M5 6h14M5 18h14" />
                    </svg>
                    Yayın Tipi Yöneticisi
                </a>
            </div>
        </div>

        <!-- Raporlar -->
        <a href="{{ route('admin.reports.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
            :data-tip="open ? '' : 'Raporlar'" :title="open ? '' : 'Raporlar'">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            </svg>
            Raporlar
        </a>

        <!-- Bildirimler -->
        <a href="{{ route('admin.notifications.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
            :data-tip="open ? '' : 'Bildirimler'" :title="open ? '' : 'Bildirimler'">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            Bildirimler
        </a>

        <!-- AI Sistemi -->
        <div class="space-y-1 mb-4">
            <button @click="toggleMenu('ai')"
                class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip w-full flex items-center justify-between"
                :aria-expanded="activeMenu === 'ai'"
                aria-controls="menu-ai"
                :data-tip="open ? '' : 'AI Sistemi'" :title="open ? '' : 'AI Sistemi'">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                    </svg>
                    AI Sistemi
                </div>
                <svg class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': activeMenu === 'ai' }" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="menu-ai" x-show="activeMenu === 'ai'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" class="ml-6 space-y-1">
                @if (\Illuminate\Support\Facades\Route::has('admin.ai.dashboard'))
                    <a href="{{ route('admin.ai.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">AI Dashboard</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.ai.wizard'))
                    <a href="{{ route('admin.ai.wizard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Wizard</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.ai.settings'))
                    <a href="{{ route('admin.ai.settings') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">AI Ayarları</a>
                @endif
            </div>
        </div>

        <!-- Takım Yönetimi -->
        <div class="space-y-1 mb-4">
            <button @click="toggleMenu('team')"
                class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip w-full flex items-center justify-between"
                :aria-expanded="activeMenu === 'team'"
                aria-controls="menu-team"
                :data-tip="open ? '' : 'Takım Yönetimi'" :title="open ? '' : 'Takım Yönetimi'">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857" />
                    </svg>
                    Takım Yönetimi
                </div>
                <svg class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': activeMenu === 'team' }" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="menu-team" x-show="activeMenu === 'team'" x-transition class="ml-6 space-y-1">
                @if (\Illuminate\Support\Facades\Route::has('admin.team.members'))
                    <a href="{{ route('admin.team.members') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Takım
                        Üyeleri</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.team.tasks'))
                    <a href="{{ route('admin.team.tasks') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Görevler</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.team.performance'))
                    <a href="{{ route('admin.team.performance') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Performans</a>
                @endif
            </div>
        </div>

        <!-- Analytics -->
        <div class="space-y-1 mb-4">
            <button @click="toggleMenu('analytics')"
                class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip w-full flex items-center justify-between"
                :aria-expanded="activeMenu === 'analytics'"
                aria-controls="menu-analytics"
                :data-tip="open ? '' : 'Analytics'" :title="open ? '' : 'Analytics'">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 11V3m4 8V5m4 6V7M7 11v8m4-8v8m4-8v8m4-8v8" />
                    </svg>
                    Analytics
                </div>
                <svg class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': activeMenu === 'analytics' }" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="menu-analytics" x-show="activeMenu === 'analytics'" x-transition class="ml-6 space-y-1">
                @if (\Illuminate\Support\Facades\Route::has('admin.analytics.index'))
                    <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Genel
                        Analytics</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.analytics.telegram'))
                    <a href="{{ route('admin.analytics.telegram') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Telegram
                        Bot</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.analytics.reports'))
                    <a href="{{ route('admin.analytics.reports') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Raporlar</a>
                @endif
            </div>
        </div>

        <!-- Telegram Bot -->
        <div class="space-y-1 mb-4">
            <button @click="toggleMenu('telegram')"
                class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip w-full flex items-center justify-between"
                :aria-expanded="activeMenu === 'telegram'"
                aria-controls="menu-telegram"
                :data-tip="open ? '' : 'Telegram Bot'" :title="open ? '' : 'Telegram Bot'">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10l18-7-7 18-3-7-8-4z" />
                    </svg>
                    Telegram Bot
                </div>
                <svg class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': activeMenu === 'telegram' }" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="menu-telegram" x-show="activeMenu === 'telegram'" x-transition class="ml-6 space-y-1">
                @if (\Illuminate\Support\Facades\Route::has('admin.telegram-bot.index'))
                    <a href="{{ route('admin.telegram-bot.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Genel</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.telegram-bot.status'))
                    <a href="{{ route('admin.telegram-bot.status') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Durum</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.telegram-bot.webhook-info'))
                    <a href="{{ route('admin.telegram-bot.webhook-info') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Webhook Bilgisi</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.telegram-bot.index'))
                    <a href="{{ route('admin.telegram-bot.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Eylemler</a>
                @endif
            </div>
        </div>

        <!-- Ayarlar -->
        <a href="{{ route('admin.ayarlar.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.ayarlar.*') ? 'active' : '' }}"
            :data-tip="open ? '' : 'Ayarlar'" :title="open ? '' : 'Ayarlar'">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Ayarlar
        </a>

        <!-- Blog Yönetimi -->
        <div class="space-y-1 mb-4">
            <button @click="toggleMenu('blog')"
                class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip w-full flex items-center justify-between"
                :aria-expanded="activeMenu === 'blog'"
                aria-controls="menu-blog"
                :data-tip="open ? '' : 'Blog Yönetimi'" :title="open ? '' : 'Blog Yönetimi'">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    Blog Yönetimi
                </div>
                <svg class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': activeMenu === 'blog' }" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="menu-blog" x-show="activeMenu === 'blog'" x-transition class="ml-6 space-y-1">
                @if (\Illuminate\Support\Facades\Route::has('admin.blog.posts.index'))
                    <a href="{{ route('admin.blog.posts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Yazılar</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.blog.categories.index'))
                    <a href="{{ route('admin.blog.categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Kategoriler</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::has('admin.blog.comments.index'))
                    <a href="{{ route('admin.blog.comments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip">Yorumlar</a>
                @endif
            </div>
        </div>

        <!-- Adres Yönetimi -->
        @if (\Illuminate\Support\Facades\Route::has('admin.adres-yonetimi.index'))
        <a href="{{ route('admin.adres-yonetimi.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.adres-yonetimi.*') ? 'active' : '' }}"
            :data-tip="open ? '' : 'Adres Yönetimi'" :title="open ? '' : 'Adres Yönetimi'">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span x-show="open">Adres Yönetimi</span>
        </a>
        @endif

        <!-- Test Sayfaları -->
        <div class="space-y-1 mb-4">
            <button @click="toggleMenu('test')"
                class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip w-full flex items-center justify-between"
                :aria-expanded="activeMenu === 'test'"
                aria-controls="menu-test"
                :data-tip="open ? '' : 'Test Sayfaları'" :title="open ? '' : 'Test Sayfaları'">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                    </svg>
                    Test Sayfaları
                </div>
                <svg class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': activeMenu === 'test' }" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div id="menu-test" x-show="activeMenu === 'test'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" class="ml-6 space-y-1">

                <a href="{{ route('admin.notifications.test-page') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800 c7-tip {{ request()->routeIs('admin.notifications.test-page') ? 'active' : '' }}"
                    :data-tip="open ? '' : 'Pusher Test'" :title="open ? '' : 'Pusher Test'">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                    </svg>
                    Pusher Test
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar Footer -->
    <div class="flex flex-col h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700-footer p-4 border-t border-gray-700 mt-auto">
        <div class="flex items-center space-x-3">
            <div
                class="w-8 h-8 bg-gradient-to-r from-orange-500 to-amber-500 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-white">{{ substr($user->name ?? 'A', 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ $user->name ?? 'Admin' }}</p>
                <p class="text-xs text-gray-400 truncate">{{ $user->email ?? 'admin@example.com' }}</p>
            </div>
        </div>
    </div>
</aside>
