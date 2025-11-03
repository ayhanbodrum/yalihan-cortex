@extends('admin.layouts.neo')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">🧪 Kategori Sistemi Test Sayfası</h1>

    {{-- Test Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Kategori Cascade Test</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Ana Kategori --}}
            <div>
                <label class="block text-sm font-medium mb-2">Ana Kategori</label>
                <select id="test_ana_kategori" class="neo-select w-full">
                    <option value="">Seçiniz...</option>
                    @foreach($anaKategoriler as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Alt Kategori --}}
            <div>
                <label class="block text-sm font-medium mb-2">Alt Kategori</label>
                <select id="test_alt_kategori" class="neo-select w-full" disabled>
                    <option value="">Önce ana kategori seçin...</option>
                </select>
            </div>

            {{-- Yayın Tipi --}}
            <div>
                <label class="block text-sm font-medium mb-2">Yayın Tipi</label>
                <select id="test_yayin_tipi" class="neo-select w-full" disabled>
                    <option value="">Önce alt kategori seçin...</option>
                </select>
            </div>
        </div>

        {{-- Test Butonları --}}
        <div class="mt-4 flex gap-2">
            <button onclick="testDirectAPI()" class="neo-btn neo-btn-primary">
                🔍 Direkt API Test
            </button>
            <button onclick="testCategoriesJS()" class="neo-btn neo-btn-primary">
                ⚙️ Categories.js Test
            </button>
            <button onclick="checkEventListeners()" class="neo-btn neo-btn-info">
                📊 Event Listener Durumu
            </button>
            <button onclick="clearLogs()" class="neo-btn neo-btn-secondary">
                🗑️ Logları Temizle
            </button>
        </div>
    </div>

    {{-- Debug Console --}}
    <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm max-h-96 overflow-y-auto" id="debugConsole">
        <div class="mb-2 text-gray-500">=== Debug Console ===</div>
    </div>

    {{-- Event Listener Status --}}
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
        <h3 class="font-semibold mb-2">📊 Event Listener Durumu</h3>
        <div id="statusDisplay" class="space-y-2"></div>
    </div>

    {{-- Features Test Section --}}
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">✨ Features Dinamik Yükleme Test</h2>
        <div id="features-test-container" class="min-h-48 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4">
            <p class="text-gray-500">Alt kategori seçildiğinde features yüklenecek...</p>
        </div>
    </div>
</div>

{{-- Test Scripts --}}
<script>
// Debug logger (DEFINED FIRST)
function debugLog(message, type = 'info') {
    const console = document.getElementById('debugConsole');
    const timestamp = new Date().toLocaleTimeString();
    const colors = {
        info: 'text-blue-400',
        success: 'text-green-400',
        error: 'text-red-400',
        warning: 'text-yellow-400'
    };

    const line = document.createElement('div');
    line.className = colors[type] || colors.info;
    line.textContent = `[${timestamp}] ${message}`;
    console.appendChild(line);
    console.scrollTop = console.scrollHeight;
}

// Clear logs
function clearLogs() {
    document.getElementById('debugConsole').innerHTML = '<div class="mb-2 text-gray-500">=== Debug Console ===</div>';
}

// Check element status
function checkStatus() {
    const status = document.getElementById('statusDisplay');
    status.innerHTML = '';

    const elements = [
        { id: 'test_ana_kategori', name: 'Ana Kategori' },
        { id: 'test_alt_kategori', name: 'Alt Kategori' },
        { id: 'test_yayin_tipi', name: 'Yayın Tipi' }
    ];

    elements.forEach(el => {
        const element = document.getElementById(el.id);
        const exists = element !== null;
        const hasListener = element && element.hasAttribute('data-has-listener');

        status.innerHTML += `
            <div class="flex items-center gap-2">
                <span class="w-32">${el.name}:</span>
                <span class="${exists ? 'text-green-600' : 'text-red-600'}">${exists ? '✅ Var' : '❌ Yok'}</span>
                ${exists ? `<span class="${hasListener ? 'text-green-600' : 'text-yellow-600'}">${hasListener ? '✅ Listener Var' : '⚠️ Listener Yok'}</span>` : ''}
            </div>
        `;
    });
}

function checkEventListeners() {
    debugLog('📊 Event Listener Durumu:', 'info');

    const anaKategori = document.getElementById('test_ana_kategori');
    const altKategori = document.getElementById('test_alt_kategori');
    const yayinTipi = document.getElementById('test_yayin_tipi');

    // Check if elements exist
    if (!anaKategori || !altKategori || !yayinTipi) {
        debugLog('❌ Select element\'ler bulunamadı!', 'error');
        return;
    }

    // Check has-listener attribute
    const anaHasListener = anaKategori.getAttribute('data-has-listener');
    const altHasListener = altKategori.getAttribute('data-has-listener');
    const yayinHasListener = yayinTipi.getAttribute('data-has-listener');

    debugLog(`Ana Kategori: listener ${anaHasListener ? '✅ Var' : '❌ Yok'}`, anaHasListener ? 'success' : 'error');
    debugLog(`Alt Kategori: listener ${altHasListener ? '✅ Var' : '❌ Yok'}`, altHasListener ? 'success' : 'error');
    debugLog(`Yayın Tipi: listener ${yayinHasListener ? '✅ Var' : '❌ Yok'}`, yayinHasListener ? 'success' : 'error');

    // Check disabled states
    debugLog(`Ana Kategori: ${anaKategori.disabled ? '🔒 Disabled' : '🔓 Enabled'}`, 'info');
    debugLog(`Alt Kategori: ${altKategori.disabled ? '🔒 Disabled' : '🔓 Enabled'}`, 'info');
    debugLog(`Yayın Tipi: ${yayinTipi.disabled ? '🔒 Disabled' : '🔓 Enabled'}`, 'info');

    // Check values
    debugLog(`Ana Kategori seçili: ${anaKategori.value || 'Boş'}`, anaKategori.value ? 'success' : 'warning');
    debugLog(`Alt Kategori seçili: ${altKategori.value || 'Boş'}`, altKategori.value ? 'success' : 'warning');
    debugLog(`Yayın Tipi seçili: ${yayinTipi.value || 'Boş'}`, yayinTipi.value ? 'success' : 'warning');

    // Check option counts
    debugLog(`Ana Kategori seçenekleri: ${anaKategori.options.length}`, 'info');
    debugLog(`Alt Kategori seçenekleri: ${altKategori.options.length}`, 'info');
    debugLog(`Yayın Tipi seçenekleri: ${yayinTipi.options.length}`, 'info');
}

// Direct API Test
async function testDirectAPI() {
    debugLog('🔍 Direkt API test başlıyor...', 'info');
    const altKategoriId = document.getElementById('test_alt_kategori').value;

    if (!altKategoriId) {
        debugLog('❌ Alt kategori seçilmemiş!', 'error');
        return;
    }

    try {
        debugLog(`📡 API çağrısı: /api/categories/publication-types/${altKategoriId}`, 'info');
        const response = await fetch(`/api/categories/publication-types/${altKategoriId}`);
        debugLog(`📥 Response status: ${response.status}`, response.ok ? 'success' : 'error');

        const data = await response.json();
        debugLog(`📊 Response data:`, 'info');
        debugLog(JSON.stringify(data, null, 2), data.success ? 'success' : 'error');

        if (data.success && data.types && data.types.length > 0) {
            debugLog(`✅ ${data.types.length} yayın tipi bulundu!`, 'success');
            populateYayinTipi(data.types);
        } else {
            debugLog('⚠️ Yayın tipi bulunamadı', 'warning');
        }
    } catch (error) {
        debugLog(`❌ Hata: ${error.message}`, 'error');
    }
}

// Test Categories.js functions
function testCategoriesJS() {
    debugLog('⚙️ Categories.js test başlıyor...', 'info');

    // Check if functions exist
    const functions = ['loadAltKategoriler', 'loadYayinTipleri', 'loadYayinTipleri'];
    functions.forEach(funcName => {
        const exists = typeof window[funcName] === 'function';
        debugLog(`${funcName}: ${exists ? '✅ Var' : '❌ Yok'}`, exists ? 'success' : 'error');
    });

    // Check IlanCreateCategories
    if (window.IlanCreateCategories) {
        debugLog('✅ IlanCreateCategories object var', 'success');
        debugLog(`Fonksiyonlar: ${Object.keys(window.IlanCreateCategories).join(', ')}`, 'info');
    } else {
        debugLog('❌ IlanCreateCategories object yok!', 'error');
    }
}

// Populate yayin tipi
function populateYayinTipi(types) {
    const select = document.getElementById('test_yayin_tipi');
    select.innerHTML = '<option value="">Yayın tipi seçin...</option>';

    types.forEach(type => {
        const option = document.createElement('option');
        option.value = type.id;
        option.textContent = type.name;
        select.appendChild(option);
    });

    select.disabled = false;
    debugLog(`✅ ${types.length} yayın tipi eklendi`, 'success');
}

// Initialize test categories after modules load
function initializeTestCategories() {
    debugLog('🚀 Initializing test categories...', 'success');

    // Clear existing listeners
    document.querySelectorAll('[data-has-listener]').forEach(el => {
        el.removeAttribute('data-has-listener');
    });

    // Ana kategori listener
    const anaKategori = document.getElementById('test_ana_kategori');
    anaKategori.addEventListener('change', async function() {
        debugLog(`🔵 Ana kategori değişti: ${this.value}`, 'info');

        if (!this.value) return;

        // Load subcategories
        try {
            debugLog(`📡 API çağrısı: /api/categories/sub/${this.value}`, 'info');
            const response = await fetch(`/api/categories/sub/${this.value}`, {
                cache: 'no-cache',
                headers: {
                    'Cache-Control': 'no-cache'
                }
            });
            const data = await response.json();

            if (data.success && data.subcategories && data.subcategories.length > 0) {
                const altKategori = document.getElementById('test_alt_kategori');
                altKategori.innerHTML = '<option value="">Seçiniz...</option>';
                data.subcategories.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.name;
                    altKategori.appendChild(option);
                });
                altKategori.disabled = false;
                debugLog(`✅ ${data.subcategories.length} alt kategori yüklendi`, 'success');
            } else {
                debugLog(`⚠️ Alt kategori bulunamadı (count: ${data.subcategories ? data.subcategories.length : 0})`, 'warning');
                if (data.message) {
                    debugLog(`📋 Mesaj: ${data.message}`, 'warning');
                }
            }
        } catch (error) {
            debugLog(`❌ Hata: ${error.message}`, 'error');
        }
    });
    anaKategori.setAttribute('data-has-listener', 'true');

        // Alt kategori listener
    const altKategori = document.getElementById('test_alt_kategori');
    altKategori.addEventListener('change', async function() {
        debugLog(`🔵 Alt kategori değişti: ${this.value}`, 'info');

        if (!this.value) return;

        // Direct API call (categories.js uses different IDs)
        try {
            debugLog(`📡 API çağrısı: /api/categories/publication-types/${this.value}`, 'info');
            const response = await fetch(`/api/categories/publication-types/${this.value}`, {
                cache: 'no-cache',
                headers: {
                    'Cache-Control': 'no-cache'
                }
            });
            const data = await response.json();

            debugLog(`📥 Response: ${JSON.stringify(data)}`, data.success ? 'success' : 'error');

            if (data.success && data.types && data.types.length > 0) {
                populateYayinTipi(data.types);
            } else {
                debugLog(`⚠️ Yayın tipi bulunamadı (${data.types ? data.types.length : 0} adet)`, 'warning');

                // Show the response for debugging
                if (data.message) {
                    debugLog(`📋 Mesaj: ${data.message}`, 'warning');
                }
            }
        } catch (error) {
            debugLog(`❌ Hata: ${error.message}`, 'error');
        }
    });
    altKategori.setAttribute('data-has-listener', 'true');
}

// Features event listener for test
window.addEventListener('category-changed', function(e) {
    debugLog('🎯 Features: Category changed event received!', 'success');
    debugLog(`📋 Category ID: ${e.detail.category.id}`, 'info');

    const container = document.getElementById('features-test-container');
    if (container) {
        container.innerHTML = `
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-green-800">✅ Features event tetiklendi!</p>
                <p class="text-sm text-green-600 mt-2">Category ID: ${e.detail.category.id}</p>
            </div>
        `;
    }
});

// Manual event listeners for test
document.addEventListener('DOMContentLoaded', function() {
    debugLog('✅ Test sayfası yüklendi', 'success');
    checkStatus();
    setInterval(checkStatus, 2000);
    debugLog('Test sayfası hazır!', 'success');
    debugLog('Ana kategori seçerek test başlatın', 'info');

    // Wait for modules to load
    setTimeout(function() {
        debugLog('🔍 Checking for categories.js...', 'info');

        if (window.IlanCreateCategories) {
            debugLog('✅ IlanCreateCategories loaded!', 'success');
            initializeTestCategories();
        } else {
            debugLog('❌ IlanCreateCategories NOT loaded!', 'error');
            const ilanKeys = Object.keys(window).filter(k => k.includes('Ilan') || k.includes('Category'));
            debugLog('Available keys: ' + (ilanKeys.length > 0 ? ilanKeys.join(', ') : 'NONE'), 'warning');

            // Try to load manually
            debugLog('⚠️ Attempting fallback manual load...', 'warning');
            initializeTestCategories();
        }
    }, 1500);
});
</script>
@push('scripts')
@vite(['resources/js/admin/ilan-create.js'])
@endpush

@section('scripts')
@parent
<script>
console.log('✅ Test scripts loaded');
</script>
@endsection
@endsection
