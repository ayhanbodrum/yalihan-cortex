@extends('admin.layouts.neo')

@section('title', 'Component Library Demo')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">🎨 Component Library Demo</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Modern, reusable Tailwind components</p>
    </div>

    {{-- TABS DEMO --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-8 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">📑 Tabs Component</h2>
        
        <div x-data="{ activeTab: 1 }" class="w-full">
            {{-- Tab Navigation --}}
            <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700 mb-6" role="tablist">
                <button
                    @click="activeTab = 1"
                    :class="activeTab === 1 ? 'border-blue-600 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600'"
                    class="px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    role="tab">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Genel Bilgiler
                </button>
                
                <button
                    @click="activeTab = 2"
                    :class="activeTab === 2 ? 'border-blue-600 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600'"
                    class="px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    role="tab">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Özellikler
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">5</span>
                </button>
                
                <button
                    @click="activeTab = 3"
                    :class="activeTab === 3 ? 'border-blue-600 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600'"
                    class="px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    role="tab">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Konum
                </button>
            </div>
            
            {{-- Tab Content --}}
            <div>
                <div x-show="activeTab === 1" x-transition class="text-gray-700 dark:text-gray-300">
                    <h3 class="text-lg font-semibold mb-3">Genel Bilgiler</h3>
                    <p>Bu tab'da genel bilgiler gösterilir. Form elementleri, metin alanları vs.</p>
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-sm">✨ Tab 1 içeriği - Modern Tailwind tasarım!</p>
                    </div>
                </div>
                
                <div x-show="activeTab === 2" x-transition class="text-gray-700 dark:text-gray-300">
                    <h3 class="text-lg font-semibold mb-3">Özellikler</h3>
                    <p>Bu tab'da özellikler listelenir. 5 adet özellik var (badge'de gösteriliyor).</p>
                    <ul class="mt-4 space-y-2">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Dark mode support
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Accessibility (ARIA)
                        </li>
                    </ul>
                </div>
                
                <div x-show="activeTab === 3" x-transition class="text-gray-700 dark:text-gray-300">
                    <h3 class="text-lg font-semibold mb-3">Konum Bilgileri</h3>
                    <p>Bu tab'da konum ve harita bilgileri gösterilir.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ACCORDION DEMO --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">📂 Accordion Component</h2>
        
        <div 
            x-data="{ 
                activeItems: null,
                allowMultiple: false,
                toggle(id) {
                    if (this.allowMultiple) {
                        if (this.activeItems.includes(id)) {
                            this.activeItems = this.activeItems.filter(item => item !== id);
                        } else {
                            this.activeItems.push(id);
                        }
                    } else {
                        this.activeItems = this.activeItems === id ? null : id;
                    }
                },
                isOpen(id) {
                    if (this.allowMultiple) {
                        return this.activeItems.includes(id);
                    }
                    return this.activeItems === id;
                }
            }" 
            class="space-y-4">
            
            {{-- Accordion Item 1 --}}
            <div 
                x-data="{ id: 'item-1', isOpen: false }"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                
                <button
                    @click="$parent.toggle(id)"
                    :aria-expanded="$parent.isOpen(id)"
                    class="w-full flex items-center justify-between px-6 py-4 text-left transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                    type="button">
                    
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        🏠 Genel Özellikler
                    </span>
                    
                    <svg 
                        :class="$parent.isOpen(id) ? 'rotate-180' : ''" 
                        class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-200" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div
                    x-show="$parent.isOpen(id)"
                    x-transition
                    x-cloak
                    class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    
                    <div class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                        <p>Bu bölümde genel özellikler listelenebilir:</p>
                        <ul class="mt-2 space-y-1">
                            <li>• Oda sayısı</li>
                            <li>• Metrekare</li>
                            <li>• Kat bilgisi</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            {{-- Accordion Item 2 --}}
            <div 
                x-data="{ id: 'item-2', isOpen: false }"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                
                <button
                    @click="$parent.toggle(id)"
                    :aria-expanded="$parent.isOpen(id)"
                    class="w-full flex items-center justify-between px-6 py-4 text-left transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                    type="button">
                    
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        ⚙️ Teknik Detaylar
                    </span>
                    
                    <svg 
                        :class="$parent.isOpen(id) ? 'rotate-180' : ''" 
                        class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-200" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div
                    x-show="$parent.isOpen(id)"
                    x-transition
                    x-cloak
                    class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    
                    <div class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                        <p>Teknik özellikler ve detaylar bu bölümde gösterilir.</p>
                        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <p class="text-xs">💡 Alpine.js ile smooth açılma/kapanma animasyonları!</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Accordion Item 3 --}}
            <div 
                x-data="{ id: 'item-3', isOpen: false }"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                
                <button
                    @click="$parent.toggle(id)"
                    :aria-expanded="$parent.isOpen(id)"
                    class="w-full flex items-center justify-between px-6 py-4 text-left transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                    type="button">
                    
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        📸 Medya Galerisi
                    </span>
                    
                    <svg 
                        :class="$parent.isOpen(id) ? 'rotate-180' : ''" 
                        class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-200" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div
                    x-show="$parent.isOpen(id)"
                    x-transition
                    x-cloak
                    class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    
                    <div class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                        <p>Fotoğraflar ve videolar bu bölümde gösterilir.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- COMPONENT LIST --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">📦 Mevcut Component'ler</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30 rounded-lg">
                <h3 class="font-semibold text-green-900 dark:text-green-100">Form Components</h3>
                <ul class="mt-2 text-sm text-green-800 dark:text-green-200 space-y-1">
                    <li>✓ Input</li>
                    <li>✓ Select</li>
                    <li>✓ Textarea</li>
                    <li>✓ Checkbox</li>
                    <li>✓ Radio</li>
                    <li>✓ Toggle</li>
                </ul>
            </div>
            
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 rounded-lg">
                <h3 class="font-semibold text-blue-900 dark:text-blue-100">UI Components</h3>
                <ul class="mt-2 text-sm text-blue-800 dark:text-blue-200 space-y-1">
                    <li>✓ Button</li>
                    <li>✓ Modal</li>
                    <li>✓ Dropdown</li>
                    <li>✓ Alert</li>
                    <li>✓ Badge</li>
                    <li>✓ Toast</li>
                    <li>✓ Tabs ⭐ YENİ!</li>
                    <li>✓ Accordion ⭐ YENİ!</li>
                </ul>
            </div>
            
            <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/30 rounded-lg">
                <h3 class="font-semibold text-purple-900 dark:text-purple-100">Utility Components</h3>
                <ul class="mt-2 text-sm text-purple-800 dark:text-purple-200 space-y-1">
                    <li>✓ Loading</li>
                    <li>✓ Skeleton</li>
                    <li>✓ Table</li>
                    <li>✓ Bulk Actions</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

