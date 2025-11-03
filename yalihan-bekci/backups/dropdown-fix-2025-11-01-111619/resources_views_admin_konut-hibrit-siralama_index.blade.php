@extends('admin.layouts.neo')

@section('title', 'Konut Özellikleri Hibrit Sıralama')

@section('content')
<div class="neo-container">
    <!-- Header -->
    <div class="neo-header">
        <div class="neo-header-content">
            <h1 class="neo-title">🏠 Konut Özellikleri Hibrit Sıralama</h1>
            <p class="neo-subtitle">AI destekli akıllı özellik sıralama sistemi</p>
        </div>
        <div class="neo-header-actions">
            <button class="neo-btn neo-btn-primary" onclick="refreshData()">
                <i class="neo-icon-refresh"></i> Yenile
            </button>
            <button class="neo-btn neo-btn-success" onclick="exportData()">
                <i class="neo-icon-download"></i> Dışa Aktar
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="neo-stats-grid">
        <div class="neo-stat-card">
            <div class="neo-stat-icon">📊</div>
            <div class="neo-stat-content">
                <div class="neo-stat-value" id="total-ozellikler">{{ count($hibritSiralama) }}</div>
                <div class="neo-stat-label">Toplam Özellik</div>
            </div>
        </div>
        <div class="neo-stat-card">
            <div class="neo-stat-icon">🎯</div>
            <div class="neo-stat-content">
                <div class="neo-stat-value" id="cok-onemli-count">{{ collect($hibritSiralama)->where('onem_seviyesi', 'cok_onemli')->count() }}</div>
                <div class="neo-stat-label">Çok Önemli</div>
            </div>
        </div>
        <div class="neo-stat-card">
            <div class="neo-stat-icon">🤖</div>
            <div class="neo-stat-content">
                <div class="neo-stat-value" id="ai-ortalama">{{ round(collect($hibritSiralama)->avg('ai_oneri_yuzdesi'), 1) }}%</div>
                <div class="neo-stat-label">AI Ortalama</div>
            </div>
        </div>
        <div class="neo-stat-card">
            <div class="neo-stat-icon">👤</div>
            <div class="neo-stat-content">
                <div class="neo-stat-value" id="kullanici-ortalama">{{ round(collect($hibritSiralama)->avg('kullanici_tercih_yuzdesi'), 1) }}%</div>
                <div class="neo-stat-label">Kullanıcı Ortalama</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="neo-filters">
        <div class="neo-filter-group">
            <label class="neo-label">Önem Seviyesi:</label>
            <select class="neo-select" id="onem-filter" onchange="filterByOnem()">
                <option value="">Tümü</option>
                <option value="cok_onemli">Çok Önemli</option>
                <option value="onemli">Önemli</option>
                <option value="orta_onemli">Orta Önemli</option>
                <option value="dusuk_onemli">Düşük Önemli</option>
            </select>
        </div>
        <div class="neo-filter-group">
            <label class="neo-label">Hibrit Skor:</label>
            <select class="neo-select" id="skor-filter" onchange="filterBySkor()">
                <option value="">Tümü</option>
                <option value="80-100">80-100 (Çok Yüksek)</option>
                <option value="60-80">60-80 (Yüksek)</option>
                <option value="40-60">40-60 (Orta)</option>
                <option value="0-40">0-40 (Düşük)</option>
            </select>
        </div>
        <div class="neo-filter-group">
            <label class="neo-label">Arama:</label>
            <input type="text" class="neo-input" id="search-input" placeholder="Özellik ara..." onkeyup="searchOzellikler()">
        </div>
    </div>

    <!-- Hibrit Sıralama Table -->
    <div class="neo-table-container">
        <table class="neo-table" id="hibrit-table">
            <thead>
                <tr>
                    <th>Sıra</th>
                    <th>Özellik Adı</th>
                    <th>Kullanım Sıklığı</th>
                    <th>AI Öneri %</th>
                    <th>Kullanıcı Tercih %</th>
                    <th>Hibrit Skor</th>
                    <th>Önem Seviyesi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hibritSiralama as $index => $ozellik)
                <tr class="ozellik-row" data-onem="{{ $ozellik->onem_seviyesi }}" data-skor="{{ $ozellik->hibrit_skor }}">
                    <td class="neo-text-center">
                        <span class="neo-badge neo-badge-primary">{{ $ozellik->siralama }}</span>
                    </td>
                    <td>
                        <div class="neo-ozellik-info">
                            <div class="neo-ozellik-name">{{ $ozellik->ozellik_adi }}</div>
                            <div class="neo-ozellik-slug">{{ $ozellik->ozellik_slug }}</div>
                        </div>
                    </td>
                    <td class="neo-text-center">
                        <span class="neo-number">{{ number_format($ozellik->kullanim_sikligi) }}</span>
                    </td>
                    <td class="neo-text-center">
                        <div class="neo-progress-bar">
                            <div class="neo-progress-fill" style="width: {{ $ozellik->ai_oneri_yuzdesi }}%"></div>
                            <span class="neo-progress-text">{{ $ozellik->ai_oneri_yuzdesi }}%</span>
                        </div>
                    </td>
                    <td class="neo-text-center">
                        <div class="neo-progress-bar">
                            <div class="neo-progress-fill" style="width: {{ $ozellik->kullanici_tercih_yuzdesi }}%"></div>
                            <span class="neo-progress-text">{{ $ozellik->kullanici_tercih_yuzdesi }}%</span>
                        </div>
                    </td>
                    <td class="neo-text-center">
                        <span class="neo-score neo-score-{{ $ozellik->onem_seviyesi }}">{{ $ozellik->hibrit_skor }}</span>
                    </td>
                    <td class="neo-text-center">
                        <span class="neo-badge neo-badge-{{ $ozellik->onem_seviyesi }}">
                            {{ ucfirst(str_replace('_', ' ', $ozellik->onem_seviyesi)) }}
                        </span>
                    </td>
                    <td class="neo-text-center">
                        <button class="neo-btn neo-btn-sm neo-btn-outline" onclick="editOzellik({{ $ozellik->id }})">
                            <i class="neo-icon-edit"></i> Düzenle
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- AI Önerileri -->
    <div class="neo-section">
        <div class="neo-section-header">
            <h3 class="neo-section-title">🤖 AI Önerileri</h3>
            <button class="neo-btn neo-btn-primary" onclick="getAIOnerileri()">
                <i class="neo-icon-ai"></i> AI Önerilerini Getir
            </button>
        </div>
        <div class="neo-ai-suggestions" id="ai-suggestions">
            <div class="neo-placeholder">
                <i class="neo-icon-ai"></i>
                <p>AI önerilerini görmek için butona tıklayın</p>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="neo-modal" id="edit-modal">
    <div class="neo-modal-content">
        <div class="neo-modal-header">
            <h3 class="neo-modal-title">Özellik Düzenle</h3>
            <button class="neo-modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="neo-modal-body">
            <form id="edit-form">
                <input type="hidden" id="edit-ozellik-id">

                <div class="neo-form-group">
                    <label class="neo-label">Kullanım Sıklığı:</label>
                    <input type="number" class="neo-input" id="edit-kullanim-sikligi" min="0" required>
                </div>

                <div class="neo-form-group">
                    <label class="neo-label">AI Öneri Yüzdesi:</label>
                    <input type="number" class="neo-input" id="edit-ai-oneri" min="0" max="100" step="0.1" required>
                </div>

                <div class="neo-form-group">
                    <label class="neo-label">Kullanıcı Tercih Yüzdesi:</label>
                    <input type="number" class="neo-input" id="edit-kullanici-tercih" min="0" max="100" step="0.1" required>
                </div>

                <div class="neo-form-group">
                    <label class="neo-label">Hibrit Skor:</label>
                    <input type="text" class="neo-input" id="edit-hibrit-skor" readonly>
                </div>

                <div class="neo-form-group">
                    <label class="neo-label">Önem Seviyesi:</label>
                    <input type="text" class="neo-input" id="edit-onem-seviyesi" readonly>
                </div>
            </form>
        </div>
        <div class="neo-modal-footer">
            <button class="neo-btn neo-btn-secondary" onclick="closeEditModal()">İptal</button>
            <button class="neo-btn neo-btn-primary" onclick="saveOzellik()">Kaydet</button>
        </div>
    </div>
</div>

<style>
.neo-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.neo-stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.neo-stat-icon {
    font-size: 2rem;
}

.neo-stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
}

.neo-stat-label {
    color: #666;
    font-size: 0.9rem;
}

.neo-filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.neo-filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.neo-ozellik-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.neo-ozellik-name {
    font-weight: 500;
    color: #333;
}

.neo-ozellik-slug {
    font-size: 0.8rem;
    color: #666;
    font-family: monospace;
}

.neo-progress-bar {
    position: relative;
    background: #f0f0f0;
    border-radius: 4px;
    height: 20px;
    overflow: hidden;
}

.neo-progress-fill {
    background: linear-gradient(90deg, #4CAF50, #8BC34A);
    height: 100%;
    transition: width 0.3s ease;
}

.neo-progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.8rem;
    font-weight: 500;
    color: #333;
}

.neo-score {
    font-weight: bold;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.neo-score-cok_onemli { background: #4CAF50; color: white; }
.neo-score-onemli { background: #2196F3; color: white; }
.neo-score-orta_onemli { background: #FF9800; color: white; }
.neo-score-dusuk_onemli { background: #9E9E9E; color: white; }

.neo-badge-cok_onemli { background: #4CAF50; color: white; }
.neo-badge-onemli { background: #2196F3; color: white; }
.neo-badge-orta_onemli { background: #FF9800; color: white; }
.neo-badge-dusuk_onemli { background: #9E9E9E; color: white; }

.neo-ai-suggestions {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    min-height: 200px;
}

.neo-placeholder {
    text-align: center;
    color: #666;
    padding: 2rem;
}

.neo-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.neo-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.neo-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #eee;
}

.neo-modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}

.neo-modal-body {
    padding: 1rem;
}

.neo-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 1rem;
    border-top: 1px solid #eee;
}
</style>

<script>
// Global variables
let currentData = @json($hibritSiralama);

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('Konut Hibrit Sıralama sistemi yüklendi');
});

// Filter functions
function filterByOnem() {
    const filter = document.getElementById('onem-filter').value;
    const rows = document.querySelectorAll('.ozellik-row');

    rows.forEach(row => {
        if (!filter || row.dataset.onem === filter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterBySkor() {
    const filter = document.getElementById('skor-filter').value;
    const rows = document.querySelectorAll('.ozellik-row');

    rows.forEach(row => {
        const skor = parseFloat(row.dataset.skor);
        let show = true;

        if (filter) {
            const [min, max] = filter.split('-').map(Number);
            show = skor >= min && skor <= max;
        }

        row.style.display = show ? '' : 'none';
    });
}

function searchOzellikler() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const rows = document.querySelectorAll('.ozellik-row');

    rows.forEach(row => {
        const name = row.querySelector('.neo-ozellik-name').textContent.toLowerCase();
        const slug = row.querySelector('.neo-ozellik-slug').textContent.toLowerCase();

        if (name.includes(search) || slug.includes(search)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// AI Suggestions
async function getAIOnerileri() {
    try {
        const response = await fetch('/admin/konut-hibrit-siralama/ai-onerileri', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                kategori: 'konut',
                mevcut_ozellikler: []
            })
        });

        const data = await response.json();

        if (data.success) {
            displayAISuggestions(data.data);
        }
    } catch (error) {
        console.error('AI önerileri alınırken hata:', error);
    }
}

function displayAISuggestions(suggestions) {
    const container = document.getElementById('ai-suggestions');

    if (suggestions.length === 0) {
        container.innerHTML = '<div class="neo-placeholder"><p>Öneri bulunamadı</p></div>';
        return;
    }

    let html = '<div class="neo-suggestions-grid">';
    suggestions.forEach(suggestion => {
        html += `
            <div class="neo-suggestion-card">
                <div class="neo-suggestion-name">${suggestion.ozellik_adi}</div>
                <div class="neo-suggestion-score">Hibrit Skor: ${suggestion.hibrit_skor}</div>
                <div class="neo-suggestion-level">${suggestion.onem_seviyesi.replace('_', ' ')}</div>
            </div>
        `;
    });
    html += '</div>';

    container.innerHTML = html;
}

// Edit functions
function editOzellik(id) {
    const ozellik = currentData.find(o => o.id == id);
    if (!ozellik) return;

    document.getElementById('edit-ozellik-id').value = id;
    document.getElementById('edit-kullanim-sikligi').value = ozellik.kullanim_sikligi;
    document.getElementById('edit-ai-oneri').value = ozellik.ai_oneri_yuzdesi;
    document.getElementById('edit-kullanici-tercih').value = ozellik.kullanici_tercih_yuzdesi;

    // Calculate and display hibrit skor
    calculateHibritSkor();

    document.getElementById('edit-modal').style.display = 'block';
}

function calculateHibritSkor() {
    const kullanim = parseFloat(document.getElementById('edit-kullanim-sikligi').value) || 0;
    const ai = parseFloat(document.getElementById('edit-ai-oneri').value) || 0;
    const kullanici = parseFloat(document.getElementById('edit-kullanici-tercih').value) || 0;

    // Simple calculation (same as backend)
    const normalizedKullanim = Math.min(100, kullanim / 6);
    const hibritSkor = (normalizedKullanim * 0.4) + (ai * 0.3) + (kullanici * 0.3);

    document.getElementById('edit-hibrit-skor').value = hibritSkor.toFixed(2);

    // Determine importance level
    let onemSeviyesi = 'dusuk_onemli';
    if (hibritSkor >= 80) onemSeviyesi = 'cok_onemli';
    else if (hibritSkor >= 60) onemSeviyesi = 'onemli';
    else if (hibritSkor >= 40) onemSeviyesi = 'orta_onemli';

    document.getElementById('edit-onem-seviyesi').value = onemSeviyesi.replace('_', ' ');
}

// Add event listeners for real-time calculation
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['edit-kullanim-sikligi', 'edit-ai-oneri', 'edit-kullanici-tercih'];
    inputs.forEach(id => {
        document.getElementById(id).addEventListener('input', calculateHibritSkor);
    });
});

async function saveOzellik() {
    const id = document.getElementById('edit-ozellik-id').value;
    const kullanim = document.getElementById('edit-kullanim-sikligi').value;
    const ai = document.getElementById('edit-ai-oneri').value;
    const kullanici = document.getElementById('edit-kullanici-tercih').value;

    try {
        const response = await fetch('/admin/konut-hibrit-siralama/update-hibrit-skor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ozellik_id: id,
                kullanim_sikligi: kullanim,
                ai_oneri_yuzdesi: ai,
                kullanici_tercih_yuzdesi: kullanici
            })
        });

        const data = await response.json();

        if (data.success) {
            closeEditModal();
            location.reload(); // Refresh to show updated data
        } else {
            alert('Güncelleme sırasında hata oluştu');
        }
    } catch (error) {
        console.error('Güncelleme hatası:', error);
        alert('Güncelleme sırasında hata oluştu');
    }
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

// Utility functions
function refreshData() {
    location.reload();
}

function exportData() {
    // Simple CSV export
    const rows = document.querySelectorAll('.ozellik-row');
    let csv = 'Sıra,Özellik Adı,Kullanım Sıklığı,AI Öneri %,Kullanıcı Tercih %,Hibrit Skor,Önem Seviyesi\n';

    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cells = row.querySelectorAll('td');
            const sira = cells[0].textContent.trim();
            const ad = cells[1].querySelector('.neo-ozellik-name').textContent.trim();
            const kullanim = cells[2].textContent.trim();
            const ai = cells[3].querySelector('.neo-progress-text').textContent.trim();
            const kullanici = cells[4].querySelector('.neo-progress-text').textContent.trim();
            const skor = cells[5].textContent.trim();
            const onem = cells[6].textContent.trim();

            csv += `${sira},"${ad}",${kullanim},${ai},${kullanici},${skor},"${onem}"\n`;
        }
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'konut-hibrit-siralama.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
@endsection
