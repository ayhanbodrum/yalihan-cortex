@extends('layouts.app')

@section('title', 'Dinamik Form Oluşturucu')
@section('description', 'AI destekli dinamik form sistemi - Kategori ve yayın tipine göre otomatik form oluşturma')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="neo-container">
    <div class="neo-header">
        <h1 class="neo-title">🎯 Dinamik Form Oluşturucu</h1>
        <p class="neo-subtitle">AI destekli 2D Matrix sistemi ile otomatik form oluşturma</p>
    </div>

    <!-- Form Seçimi -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-header">
            <h2 class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-title">Form Parametrelerini Seçin</h2>
            <p class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-subtitle">Kategori ve yayın tipi seçerek dinamik form oluşturun</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-body">
            <form id="formSelector" class="neo-form-selector">
                <div class="neo-form-row">
                    <div class="neo-form-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="kategori">Kategori</label>
                        <select id="kategori" name="kategori" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 cursor-pointer appearance-none" required>
                            <option value="">Kategori Seçiniz...</option>
                            @foreach($kategoriler as $slug => $name)
                                <option value="{{ $slug }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="neo-form-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="yayin_tipi">Yayın Tipi</label>
                        <select id="yayin_tipi" name="yayin_tipi" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 cursor-pointer appearance-none" required>
                            <option value="">Yayın Tipi Seçiniz...</option>
                            @foreach($yayinTipleri as $slug => $name)
                                <option value="{{ $slug }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="neo-form-col neo-form-col-auto">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="neo-icon-generate"></i> Form Oluştur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Preview -->
    <div id="formPreview" class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800" style="display: none;">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-header">
            <h2 class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-title">Dinamik Form</h2>
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-actions">
                <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700" onclick="resetForm()">
                    <i class="neo-icon-reset"></i> Sıfırla
                </button>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-body">
            <div id="dynamicFormContainer">
                <!-- Dinamik form burada yüklenecek -->
            </div>
        </div>
    </div>

    <!-- AI Status -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-header">
            <h3 class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-title">🤖 AI Sistem Durumu</h3>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-body">
            <div class="neo-ai-status">
                <div class="neo-status-item">
                    <span class="neo-status-label">AI Model:</span>
                    <span class="neo-status-value" id="aiModel">Yükleniyor...</span>
                </div>
                <div class="neo-status-item">
                    <span class="neo-status-label">Bağlantı:</span>
                    <span class="neo-status-value" id="aiConnection">Kontrol ediliyor...</span>
                </div>
                <div class="neo-status-item">
                    <span class="neo-status-label">Özellikler:</span>
                    <span class="neo-status-value" id="aiFeatures">Hesaplanıyor...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Neo Design System - Dynamic Form Styles */
.neo-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.neo-header {
    text-align: center;
    margin-bottom: 30px;
}

.neo-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 10px;
}

.neo-subtitle {
    font-size: 1.1rem;
    color: #666;
    margin: 0;
}

.rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    overflow: hidden;
}

.rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-subtitle {
    font-size: 0.9rem;
    color: #666;
    margin: 5px 0 0 0;
}

.rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-actions {
    display: flex;
    gap: 10px;
}

.rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-body {
    padding: 24px;
}

.neo-form-selector {
    display: flex;
    gap: 20px;
    align-items: end;
}

.neo-form-row {
    display: flex;
    gap: 20px;
    align-items: end;
    width: 100%;
}

.neo-form-col {
    flex: 1;
}

.neo-form-col-auto {
    flex: 0 0 auto;
}

.block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {
    display: block;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
}

.neo-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
}

.neo-select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 {
    background: #f8f9fa;
    color: #495057;
    border: 1px solid #dee2e6;
}

.inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700:hover {
    background: #e9ecef;
    transform: translateY(-1px);
}

/* Dynamic Form Styles */
.neo-dynamic-form {
    max-width: 100%;
}

.neo-form-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
}

.neo-form-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 10px 0;
}

.neo-form-subtitle {
    font-size: 1rem;
    color: #666;
    margin: 0;
}

.neo-form-section {
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border-left: 4px solid #007bff;
}

.neo-section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 20px 0;
}

.space-y-2-group {
    margin-bottom: 20px;
}

.space-y-2-label {
    display: block;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
}

.neo-required {
    color: #dc3545;
    font-weight: bold;
}

.space-y-2-input {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
}

.w-full px-3 py-2 rounded-md border border-gray-200 bg-white text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-800 dark:text-gray-100 transition-colors, .neo-textarea, .neo-select {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
}

.neo-textarea {
    min-height: 100px;
    resize: vertical;
}

.w-full px-3 py-2 rounded-md border border-gray-200 bg-white text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-800 dark:text-gray-100 transition-colors:focus, .neo-textarea:focus, .neo-select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.space-y-2-unit {
    color: #666;
    font-weight: 500;
    white-space: nowrap;
}

.neo-checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.neo-checkbox {
    width: 18px;
    height: 18px;
    accent-color: #007bff;
}

.neo-checkbox-label {
    font-weight: 500;
    color: #333;
    cursor: pointer;
}

.neo-ai-features {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.neo-ai-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
}

.neo-ai-suggestion {
    color: #007bff;
    border-color: #007bff;
}

.neo-ai-suggestion:hover {
    background: #007bff;
    color: white;
}

.neo-ai-autofill {
    color: #28a745;
    border-color: #28a745;
}

.neo-ai-autofill:hover {
    background: #28a745;
    color: white;
}

.neo-ai-calculation {
    color: #ffc107;
    border-color: #ffc107;
}

.neo-ai-calculation:hover {
    background: #ffc107;
    color: #212529;
}

.neo-ai-actions {
    display: flex;
    gap: 15px;
    margin: 30px 0;
    padding: 20px;
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border-radius: 12px;
    border-left: 4px solid #2196f3;
}

.neo-form-actions {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

/* AI Status Styles */
.neo-ai-status {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.neo-status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.neo-status-label {
    font-weight: 500;
    color: #495057;
}

.neo-status-value {
    font-weight: 600;
    color: #007bff;
}

/* Responsive Design */
@media (max-width: 768px) {
    .neo-form-row {
        flex-direction: column;
        gap: 15px;
    }

    .neo-form-col {
        width: 100%;
    }

    .neo-ai-features {
        flex-direction: column;
    }

    .neo-ai-actions {
        flex-direction: column;
    }
}

/* Loading States */
.neo-loading {
    opacity: 0.6;
    pointer-events: none;
}

.neo-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Icons */
.neo-icon-generate::before { content: "⚡"; }
.neo-icon-reset::before { content: "🔄"; }
.neo-icon-save::before { content: "💾"; }
.neo-icon-suggestion::before { content: "💡"; }
.neo-icon-autofill::before { content: "🤖"; }
.neo-icon-calculation::before { content: "🧮"; }
.neo-icon-clear::before { content: "🗑️"; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form selector
    const formSelector = document.getElementById('formSelector');
    const formPreview = document.getElementById('formPreview');
    const dynamicFormContainer = document.getElementById('dynamicFormContainer');

    // AI Status elements
    const aiModel = document.getElementById('aiModel');
    const aiConnection = document.getElementById('aiConnection');
    const aiFeatures = document.getElementById('aiFeatures');

    // Load AI status
    loadAIStatus();

    // Form selector submit
    formSelector.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(formSelector);
        const kategori = formData.get('kategori');
        const yayinTipi = formData.get('yayin_tipi');

        if (!kategori || !yayinTipi) {
            alert('Lütfen kategori ve yayın tipi seçiniz.');
            return;
        }

        loadDynamicForm(kategori, yayinTipi);
    });

    // Load dynamic form
    async function loadDynamicForm(kategori, yayinTipi) {
        try {
            formPreview.style.display = 'block';
            dynamicFormContainer.innerHTML = '<div class="neo-spinner"></div> Form yükleniyor...';

            const response = await fetch('/dynamic-form/render', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    kategori: kategori,
                    yayin_tipi: yayinTipi
                })
            });

            const data = await response.json();

            if (data.success) {
                dynamicFormContainer.innerHTML = data.form_html;
                updateAIStatus(data.fields_count);
            } else {
                dynamicFormContainer.innerHTML = '<div class="neo-alert neo-alert-error">' + data.message + '</div>';
            }

        } catch (error) {
            console.error('Form yükleme hatası:', error);
            dynamicFormContainer.innerHTML = '<div class="neo-alert neo-alert-error">Form yüklenirken hata oluştu.</div>';
        }
    }

    // Load AI status
    async function loadAIStatus() {
        try {
            const response = await fetch('/api/ai/status');
            const data = await response.json();

            if (data.success) {
                aiModel.textContent = data.model || 'Ollama Qwen2.5';
                aiConnection.textContent = data.connected ? 'Bağlı' : 'Bağlantı Yok';
                aiFeatures.textContent = data.features + ' özellik';
            }
        } catch (error) {
            aiModel.textContent = 'Bilinmiyor';
            aiConnection.textContent = 'Hata';
            aiFeatures.textContent = '0 özellik';
        }
    }

    // Update AI status
    function updateAIStatus(fieldsCount) {
        aiFeatures.textContent = fieldsCount + ' field AI ile otomatik doldurulabilir';
    }

    // Reset form
    window.resetForm = function() {
        formPreview.style.display = 'none';
        formSelector.reset();
    };

    // AI Functions
    window.fillAllWithAI = function() {
        alert('AI ile tüm alanları doldurma özelliği yakında aktif olacak!');
    };

    window.clearAllFields = function() {
        const form = document.getElementById('dynamicForm');
        if (form) {
            form.reset();
        }
    };

    window.getAISuggestion = function(fieldSlug) {
        alert('AI öneri sistemi: ' + fieldSlug + ' alanı için öneri alınıyor...');
    };

    window.autoFillField = function(fieldSlug) {
        alert('Otomatik doldurma: ' + fieldSlug + ' alanı AI ile dolduruluyor...');
    };

    window.calculateField = function(fieldSlug) {
        alert('AI hesaplama: ' + fieldSlug + ' alanı hesaplanıyor...');
    };

    // Form submit handler
    document.addEventListener('submit', function(e) {
        if (e.target.id === 'dynamicForm') {
            e.preventDefault();
            handleFormSubmit(e.target);
        }
    });

    // Handle form submit
    async function handleFormSubmit(form) {
        try {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch('/dynamic-form/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('Form başarıyla kaydedildi!');
                console.log('Form data:', result.data);
            } else {
                alert('Hata: ' + result.message);
            }

        } catch (error) {
            console.error('Form submit hatası:', error);
            alert('Form kaydedilirken hata oluştu.');
        }
    }
});
</script>
@endsection
