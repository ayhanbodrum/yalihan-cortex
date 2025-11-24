{{-- 🏠 Konut-Specific Fields Component --}}
{{-- Context7: Category-specific component for Residential (Konut) properties --}}
<div
    class="category-fields-konut space-y-6"
    data-category="konut"
    x-show="selectedKategoriSlug && (selectedKategoriSlug.toLowerCase().includes('konut') || selectedKategoriSlug.toLowerCase().includes('daire') || selectedKategoriSlug.toLowerCase().includes('villa'))"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100"
>
    {{-- Category Indicator --}}
    <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-2 border-blue-200 dark:border-blue-800/30 rounded-xl">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100">Konut Özellikleri</h3>
            <p class="text-sm text-blue-700 dark:text-blue-300">Konut kategorisine özel alanlar</p>
        </div>
    </div>

    {{-- Critical Fields Info --}}
    <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 dark:border-amber-400 p-4 rounded-r-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">Zorunlu Alanlar</p>
                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">Oda Sayısı, Brüt Metrekare ve Kat bilgileri konut ilanları için kritik öneme sahiptir.</p>
            </div>
        </div>
    </div>

    {{-- Field Dependency Hint --}}
    <div class="text-sm text-gray-600 dark:text-gray-400 italic">
        💡 <strong>İpucu:</strong> Site/Apartman seçildiğinde otomatik olarak site özellikleri yüklenir.
    </div>
</div>

