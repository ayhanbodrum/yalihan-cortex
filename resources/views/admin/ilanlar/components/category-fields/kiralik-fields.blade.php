{{-- 🏖️ Kiralık-Specific Fields Component --}}
{{-- Context7: Category-specific component for Rental (Kiralık) properties --}}
<div 
    class="category-fields-kiralik space-y-6"
    data-category="kiralik"
    x-show="selectedYayinTipi && selectedYayinTipi.toLowerCase().includes('kiralik')"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100"
>
    {{-- Category Indicator --}}
    <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border-2 border-purple-200 dark:border-purple-800/30 rounded-xl">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 text-white shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-purple-900 dark:text-purple-100">Kiralık Özellikleri</h3>
            <p class="text-sm text-purple-700 dark:text-purple-300">Kiralık yayın tipine özel alanlar</p>
        </div>
    </div>

    {{-- Critical Fields Info --}}
    <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 dark:border-amber-400 p-4 rounded-r-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">Önemli Bilgiler</p>
                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">Depozito, Aidat ve Eşyalı mı? bilgileri kiralık ilanlar için önemlidir.</p>
            </div>
        </div>
    </div>

    {{-- Field Dependency Hint --}}
    <div class="text-sm text-gray-600 dark:text-gray-400 italic">
        💡 <strong>İpucu:</strong> Kira süresi belirtilmediğinde "Belirsiz" olarak işaretlenir.
    </div>
</div>

