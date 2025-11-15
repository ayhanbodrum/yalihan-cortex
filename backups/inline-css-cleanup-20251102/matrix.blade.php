@extends('layouts.admin')

@section('title', 'Field Dependency Matrix')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    2D Matrix - Field Dependency
                </h1>
                <p class="text-gray-600 mt-2">
                    Kategori × Yayın Tipi kombinasyonları için field'ları yönetin
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="text-3xl font-bold text-blue-600">{{ $totalFields }}</div>
            <div class="text-sm text-gray-600">Toplam Field</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="text-3xl font-bold text-purple-600">{{ $aiEnabledFields }}</div>
            <div class="text-sm text-gray-600">AI-Powered Field</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="text-3xl font-bold text-green-600">{{ $requiredFields }}</div>
            <div class="text-sm text-gray-600">Zorunlu Field</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="text-3xl font-bold text-orange-600">{{ $kategoriler->count() }}</div>
            <div class="text-sm text-gray-600">Aktif Kategori</div>
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="mb-6">
        <div class="flex gap-2 overflow-x-auto pb-2">
            @foreach(['konut', 'arsa', 'yazlik', 'isyeri'] as $index => $katSlug)
                <button
                    onclick="showKategori('{{ $katSlug }}')"
                    id="tab-{{ $katSlug }}"
                    class="kategori-tab px-6 py-3 rounded-lg font-medium transition-all {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ ucfirst($katSlug) }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Matrix Containers -->
    @foreach(['konut', 'arsa', 'yazlik', 'isyeri'] as $index => $katSlug)
        <div id="matrix-{{ $katSlug }}" class="matrix-container {{ $index !== 0 ? 'hidden' : '' }}">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        {{ ucfirst($katSlug) }} - Field Dependency Matrix
                        <span class="ml-auto text-sm font-normal text-gray-600" id="field-count-{{ $katSlug }}">
                            @php
                                $count = \Illuminate\Support\Facades\DB::table('kategori_yayin_tipi_field_dependencies')
                                    ->where('kategori_slug', $katSlug)
                                    ->count();
                            @endphp
                            {{ $count }} field
                        </span>
                    </h2>
                </div>

                <!-- Matrix Table -->
                <div class="p-6 overflow-x-auto">
                    <table class="w-full" id="table-{{ $katSlug }}">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Field</th>
                                <th class="text-center py-3 px-4 font-semibold text-blue-700 w-32">Satılık</th>
                                <th class="text-center py-3 px-4 font-semibold text-purple-700 w-32">Kiralık</th>
                                <th class="text-center py-3 px-4 font-semibold text-orange-700 w-32">Sezonluk Kiralık</th>
                                <th class="text-center py-3 px-4 font-semibold text-green-700 w-32">Devren Satış</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-{{ $katSlug }}">
                            @php
                                $fields = \Illuminate\Support\Facades\DB::table('kategori_yayin_tipi_field_dependencies')
                                    ->where('kategori_slug', $katSlug)
                                    ->get()
                                    ->groupBy('yayin_tipi');

                                $yayinTipleri = ['Satılık', 'Kiralık', 'Sezonluk Kiralık', 'Devren Satış'];
                                $allFields = [];

                                foreach($fields as $yayinTipi => $fieldList) {
                                    foreach($fieldList as $field) {
                                        if (!isset($allFields[$field->field_slug])) {
                                            $allFields[$field->field_slug] = [
                                                'name' => $field->field_name,
                                                'icon' => $field->field_icon,
                                                'category' => $field->field_category
                                            ];
                                        }
                                    }
                                }

                                $byCategory = [];
                                foreach($allFields as $slug => $field) {
                                    if (!isset($byCategory[$field['category']])) {
                                        $byCategory[$field['category']] = [];
                                    }
                                    $byCategory[$field['category']][] = ['slug' => $slug, ...$field];
                                }
                            @endphp

                            @foreach($byCategory as $category => $categoryFields)
                                <tr class="bg-gray-50">
                                    <td colspan="5" class="py-2 px-4 font-semibold text-gray-700 uppercase text-sm">
                                        @if($category === 'fiyat') Fiyat Alanları
                                        @elseif($category === 'ozellik') Özellikler
                                        @elseif($category === 'arsa') Arsa Bilgileri
                                        @elseif($category === 'yazlik') Yazlık Özellikleri
                                        @else {{ ucfirst($category) }}
                                        @endif
                                    </td>
                                </tr>
                                @foreach($categoryFields as $field)
                                    <tr class="border-b border-gray-100 hover:bg-blue-50 transition-colors">
                                        <td class="py-3 px-4 font-medium text-gray-900">{{ $field['name'] }}</td>
                                        @foreach($yayinTipleri as $yayin)
                                            @php
                                                $fieldData = $fields[$yayin] ?? collect();
                                                $fieldExists = $fieldData->where('field_slug', $field['slug'])->first();
                                                $enabled = $fieldExists ? $fieldExists->enabled : 0;
                                                $aiEnabled = $fieldExists ? $fieldExists->ai_suggestion : 0;
                                            @endphp
                                            <td class="text-center py-3 px-4">
                                                <input type="checkbox"
                                                    {{ $enabled ? 'checked' : '' }}
                                                    {{ $aiEnabled ? 'data-ai="true"' : '' }}
                                                    onchange="toggleField('{{ $katSlug }}', '{{ $yayin }}', '{{ $field['slug'] }}', this.checked)"
                                                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 {{ $aiEnabled ? 'border-purple-500 bg-purple-50' : '' }}">
                                                @if($aiEnabled)
                                                    <div class="text-xs text-purple-600 mt-1">AI</div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- AI Features Section -->
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">AI-Powered Features</h3>
                            <p class="text-sm text-gray-600">
                                @php
                                    $aiCount = \Illuminate\Support\Facades\DB::table('kategori_yayin_tipi_field_dependencies')
                                        ->where('kategori_slug', $katSlug)
                                        ->where(function($query) {
                                            $query->where('ai_suggestion', 1)
                                                  ->orWhere('ai_auto_fill', 1);
                                        })
                                        ->count();
                                @endphp
                                <span id="ai-count-{{ $katSlug }}">{{ $aiCount }}</span> field AI ile otomatik doldurulabilir
                            </p>
                        </div>
                        <button
                            onclick="testAI('{{ $katSlug }}')"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                            AI Test Et
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
// CSRF Token helper
function getCSRFToken() {
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const inputToken = document.querySelector('input[name="_token"]')?.value;
    const windowToken = window.Laravel?.csrfToken;

    const token = metaToken || inputToken || windowToken || '';

    if (!token) {
        console.error('❌ CSRF Token bulunamadı!');
        console.log('Meta token:', metaToken);
        console.log('Input token:', inputToken);
        console.log('Window token:', windowToken);
    }

    return token;
}

// Matrix data cache
let matrixCache = {};

// Show kategori - Global scope
window.showKategori = function(katSlug) {
    console.log(`🎯 Showing kategori: ${katSlug}`);

    // Update tabs
    document.querySelectorAll('.kategori-tab').forEach(tab => {
        tab.classList.remove('bg-blue-600', 'text-white');
        tab.classList.add('bg-gray-100', 'text-gray-700');
    });
    document.getElementById(`tab-${katSlug}`).classList.remove('bg-gray-100', 'text-gray-700');
    document.getElementById(`tab-${katSlug}`).classList.add('bg-blue-600', 'text-white');

    // Update containers
    document.querySelectorAll('.matrix-container').forEach(container => {
        container.classList.add('hidden');
    });
    document.getElementById(`matrix-${katSlug}`).classList.remove('hidden');

    // Load matrix if not cached
    if (!matrixCache[katSlug]) {
        console.log(`📥 Loading matrix for ${katSlug} (not cached)`);
        loadMatrix(katSlug);
    } else {
        console.log(`✅ Matrix for ${katSlug} already cached`);
        // Re-render cached matrix
        renderMatrix(katSlug, matrixCache[katSlug]);
    }
}

// Load matrix from API
async function loadMatrix(katSlug) {
    console.log(`🔄 Loading matrix for: ${katSlug}`);
    try {
        const response = await fetch(`/api/admin/ai/field-dependency/get-matrix/${katSlug}`);
        console.log(`📡 Response status: ${response.status}`);

        const result = await response.json();
        console.log(`📊 API Result:`, result);

        if (result.success) {
            console.log(`✅ Matrix loaded for ${katSlug}:`, Object.keys(result.matrix));
            matrixCache[katSlug] = result.matrix;
            renderMatrix(katSlug, result.matrix);
        } else {
            console.error(`❌ Matrix load failed for ${katSlug}:`, result);
            showError(katSlug, 'Matrix yüklenemedi');
        }
    } catch (error) {
        console.error('❌ Matrix load error:', error);
        showError(katSlug, 'Bağlantı hatası');
    }
}

// Render matrix table
function renderMatrix(katSlug, matrix) {
    console.log(`🎨 Rendering matrix for ${katSlug}:`, matrix);
    const tbody = document.getElementById(`tbody-${katSlug}`);
    const yayinTipleri = ['Satılık', 'Kiralık', 'Sezonluk Kiralık', 'Devren Satış'];

    if (!tbody) {
        console.error(`❌ tbody element not found for ${katSlug}`);
        console.log(`🔍 Available elements with tbody:`, document.querySelectorAll('[id*="tbody"]'));
        console.log(`🔍 Looking for: tbody-${katSlug}`);
        return;
    }

    console.log(`✅ tbody element found for ${katSlug}:`, tbody);

    // Get all unique fields
    const allFields = {};
    Object.values(matrix).forEach(fields => {
        fields.forEach(field => {
            if (!allFields[field.field_slug]) {
                allFields[field.field_slug] = {
                    name: field.field_name,
                    icon: field.field_icon,
                    category: field.field_category
                };
            }
        });
    });

    console.log(`📋 All fields for ${katSlug}:`, allFields);

    // Count AI-enabled fields will be done later in the function

    // Group by category
    const byCategory = {};
    Object.entries(allFields).forEach(([slug, field]) => {
        if (!byCategory[field.category]) {
            byCategory[field.category] = [];
        }
        byCategory[field.category].push({ slug, ...field });
    });

    // Count AI-enabled fields first
    let totalAi = 0;
    Object.values(matrix).forEach(fields => {
        fields.forEach(field => {
            if (field.ai_suggestion || field.ai_auto_fill) {
                totalAi++;
            }
        });
    });

    console.log(`🤖 Total AI fields for ${katSlug}: ${totalAi}`);

    // Render rows
    let html = '';

    Object.entries(byCategory).forEach(([category, fields]) => {
        // Category header
        html += `<tr class="bg-gray-50"><td colspan="5" class="py-2 px-4 font-semibold text-gray-700 uppercase text-sm">${getCategoryName(category)}</td></tr>`;

        fields.forEach(field => {
            html += '<tr class="border-b border-gray-100 hover:bg-blue-50 transition-colors">';
            html += `<td class="py-3 px-4 font-medium text-gray-900">${field.name}</td>`;

            yayinTipleri.forEach(yayin => {
                const fieldData = matrix[yayin]?.find(f => f.field_slug === field.slug);
                const enabled = fieldData?.enabled || 0;
                const aiEnabled = fieldData?.ai_suggestion || 0;

                html += `<td class="text-center py-3 px-4">
                    <input type="checkbox"
                        ${enabled ? 'checked' : ''}
                        ${aiEnabled ? 'data-ai="true"' : ''}
                        onchange="toggleField('${katSlug}', '${yayin}', '${field.slug}', this.checked)"
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 ${aiEnabled ? 'border-purple-500 bg-purple-50' : ''}">
                    ${aiEnabled ? '<div class="text-xs text-purple-600 mt-1">AI</div>' : ''}
                </td>`;
            });

            html += '</tr>';
        });
    });

    console.log(`📝 Setting tbody HTML for ${katSlug}:`, html.substring(0, 200) + '...');

    // Force DOM update with balanced approach
    tbody.innerHTML = html;
    tbody.style.display = 'table-row-group';
    tbody.style.visibility = 'visible';
    tbody.style.opacity = '1';

    // Force re-render
    tbody.offsetHeight; // Trigger reflow

    // Hide loading text immediately
    const loadingRows = tbody.querySelectorAll('tr td[colspan="5"]');
    loadingRows.forEach(row => {
        row.style.display = 'none';
        row.style.visibility = 'hidden';
        row.style.opacity = '0';
    });

    // Ensure proper table structure
    const tableElement = tbody.closest('table');
    if (tableElement) {
        tableElement.style.display = 'table';
        tableElement.style.visibility = 'visible';
        tableElement.style.opacity = '1';
        tableElement.style.width = '100%';
        tableElement.style.borderCollapse = 'collapse';
    }

    // Alternative DOM manipulation
    setTimeout(() => {
        console.log(`🔄 Alternative DOM update for ${katSlug}`);
        tbody.innerHTML = '';
        tbody.innerHTML = html;
        tbody.style.display = 'table-row-group';
        tbody.style.visibility = 'visible';
        tbody.style.opacity = '1';

        // Ensure all table elements are properly displayed
        const allRows = tbody.querySelectorAll('tr');
        allRows.forEach(row => {
            row.style.display = 'table-row';
            row.style.visibility = 'visible';
            row.style.opacity = '1';
        });

        const allCells = tbody.querySelectorAll('td');
        allCells.forEach(cell => {
            cell.style.display = 'table-cell';
            cell.style.visibility = 'visible';
            cell.style.opacity = '1';
        });
    }, 100);

    console.log(`✅ tbody HTML set for ${katSlug}`);
    console.log(`🔍 tbody display:`, getComputedStyle(tbody).display);
    console.log(`🔍 tbody visibility:`, getComputedStyle(tbody).visibility);
    console.log(`🔍 tbody opacity:`, getComputedStyle(tbody).opacity);
    console.log(`🔍 tbody children count:`, tbody.children.length);

    // Force CSS override
    const table = tbody.closest('table');
    if (table) {
        table.style.display = 'table !important';
        table.style.visibility = 'visible !important';
        table.style.opacity = '1 !important';
        table.style.width = '100% !important';
        console.log(`🔍 table display:`, getComputedStyle(table).display);
    }

    // Force parent container visibility
    const container = tbody.closest('.matrix-container');
    if (container) {
        container.style.display = 'block !important';
        container.style.visibility = 'visible !important';
        container.style.opacity = '1 !important';
        console.log(`🔍 container display:`, getComputedStyle(container).display);
    }

    // Force all parent elements
    let parent = tbody.parentElement;
    while (parent && parent !== document.body) {
        parent.style.display = parent.style.display || 'block';
        parent.style.visibility = parent.style.visibility || 'visible';
        parent.style.opacity = parent.style.opacity || '1';
        parent = parent.parentElement;
    }

    // Update stats
    const fieldCountElement = document.getElementById(`field-count-${katSlug}`);
    if (fieldCountElement) {
        fieldCountElement.textContent = `${Object.keys(allFields).length} field`;
        console.log(`📊 Updated field count for ${katSlug}: ${Object.keys(allFields).length}`);
    }

    const aiCountElement = document.getElementById(`ai-count-${katSlug}`);
    if (aiCountElement) {
        aiCountElement.textContent = totalAi;
        console.log(`📊 Updated AI count for ${katSlug}: ${totalAi}`);
    } else {
        console.error(`❌ AI count element not found for ${katSlug}`);
    }
}

// Toggle field - Global scope
window.toggleField = async function(katSlug, yayinTipi, fieldSlug, enabled) {
    try {
        const response = await fetch('/api/admin/ai/field-dependency/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
            },
            body: JSON.stringify({
                kategori_slug: katSlug,
                yayin_tipi: yayinTipi,
                field_slug: fieldSlug,
                enabled: enabled
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast(enabled ? '✅ Field aktif edildi' : '⚠️ Field pasif edildi', 'success');
            // Refresh cache
            delete matrixCache[katSlug];
        } else {
            showToast('❌ Hata: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Toggle error:', error);
        showToast('❌ Bağlantı hatası', 'error');
    }
}

// AI Test - Global scope
window.testAI = async function(katSlug) {
    showToast('🤖 AI testi başlatıldı...', 'info');

    try {
        const response = await fetch('/api/admin/ai/field-dependency/auto-fill', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
            },
            body: JSON.stringify({
                kategori_slug: katSlug,
                yayin_tipi: 'Satılık',
                existing_data: {
                    baslik: 'Test İlan',
                    lokasyon: 'Bodrum, Muğla'
                }
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast(`✅ AI: ${result.filled_count} field önerildi!`, 'success');
            console.log('AI Suggestions:', result.suggestions);
        } else {
            showToast('❌ AI hatası', 'error');
        }
    } catch (error) {
        console.error('AI test error:', error);
        showToast('❌ AI test başarısız', 'error');
    }
}

// Helper: Category name
function getCategoryName(category) {
    const names = {
        'fiyat': 'Fiyat Alanları',
        'arsa': 'Arsa Özellikleri',
        'sezonluk': 'Sezonluk Özellikler',
        'ozellik': 'Genel Özellikler',
        'dokuман': 'Dökümanlar'
    };
    return names[category] || category;
}

// Toast
function showToast(message, type = 'success') {
    const colors = {
        success: 'bg-green-600',
        error: 'bg-red-600',
        info: 'bg-blue-600'
    };

    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white ${colors[type]} z-50 transition-opacity`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Show error
function showError(katSlug, message) {
    const tbody = document.getElementById(`tbody-${katSlug}`);
    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-600"><p>${message}</p></td></tr>`;
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    loadMatrix('konut'); // Load first tab
});
</script>

<style>
/* Force matrix table visibility - BALANCED APPROACH */
.matrix-container {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.matrix-container table {
    display: table !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: 100% !important;
    border-collapse: collapse !important;
}

.matrix-container tbody {
    display: table-row-group !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.matrix-container tr {
    display: table-row !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.matrix-container td {
    display: table-cell !important;
    visibility: visible !important;
    opacity: 1 !important;
    padding: 12px 16px !important;
    border-bottom: 1px solid #e5e7eb !important;
}

/* Force all matrix elements to be visible */
#matrix-konut, #matrix-arsa, #matrix-yazlik, #matrix-isyeri {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Hide loading text */
.matrix-container tbody tr td[colspan="5"] {
    display: none !important;
}

/* Ensure proper table structure */
.matrix-container table thead {
    display: table-header-group !important;
}

.matrix-container table thead tr {
    display: table-row !important;
}

.matrix-container table thead th {
    display: table-cell !important;
    padding: 12px 16px !important;
    font-weight: 600 !important;
    text-align: center !important;
    border-bottom: 2px solid #e5e7eb !important;
}

/* Force content visibility */
.matrix-container tbody tr:not(:has(td[colspan="5"])) {
    display: table-row !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Category header styling */
.matrix-container tbody tr.bg-gray-50 td {
    background-color: #f9fafb !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    font-size: 0.875rem !important;
    color: #374151 !important;
}

/* Field row styling */
.matrix-container tbody tr.border-b td {
    border-bottom: 1px solid #e5e7eb !important;
}

.matrix-container tbody tr:hover {
    background-color: #eff6ff !important;
}

/* Checkbox styling */
.matrix-container input[type="checkbox"] {
    width: 20px !important;
    height: 20px !important;
    accent-color: #3b82f6 !important;
}

/* AI indicator styling */
.matrix-container .text-xs {
    font-size: 0.75rem !important;
    color: #7c3aed !important;
    font-weight: 500 !important;
}

// Auto-load all matrices on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Auto-loading all matrices...');
    const kategoriler = ['konut', 'arsa', 'yazlik', 'isyeri'];

    kategoriler.forEach((katSlug, index) => {
        setTimeout(() => {
            console.log(`🔄 Auto-loading ${katSlug}...`);
            loadMatrix(katSlug);
        }, index * 500); // Stagger loading
    });
});
</style>
@endpush
@endsection
