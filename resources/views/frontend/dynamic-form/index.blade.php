@extends('admin.layouts.neo') {{-- Context7: layouts.app → admin.layouts.neo --}}

@section('title', 'Dinamik Form Oluşturucu')
@section('description', 'AI destekli dinamik form sistemi - Kategori ve yayın tipine göre otomatik form oluşturma')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">🎯 Dinamik Form Oluşturucu</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">AI destekli 2D Matrix sistemi ile otomatik form oluşturma</p>
        </div>
    </div>

    <!-- Form Seçimi -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200 mb-6">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Form Parametrelerini Seçin</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Kategori ve yayın tipi seçerek dinamik form oluşturun</p>
        </div>
        <div class="p-6">
            <form id="formSelector" class="space-y-4">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="kategori">Kategori</label>
                        <select id="kategori" name="kategori" class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 cursor-pointer" style="color-scheme: light dark;" required aria-label="Kategori seçiniz">
                            <option value="">Kategori Seçiniz...</option>
                            @foreach($kategoriler as $slug => $name)
                                <option value="{{ $slug }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="yayin_tipi">Yayın Tipi</label>
                        <select id="yayin_tipi" name="yayin_tipi" class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 cursor-pointer" style="color-scheme: light dark;" required aria-label="Yayın tipi seçiniz">
                            <option value="">Yayın Tipi Seçiniz...</option>
                            @foreach($yayinTipleri as $slug => $name)
                                <option value="{{ $slug }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 shadow-md hover:shadow-lg font-medium" aria-label="Form oluştur">
                            <span>⚡</span>
                            <span>Form Oluştur</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Preview -->
    <div id="formPreview" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200 mb-6 hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Dinamik Form</h2>
            <button type="button" onclick="resetForm()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 font-medium" aria-label="Formu sıfırla">
                <span>🔄</span>
                <span>Sıfırla</span>
            </button>
        </div>
        <div class="p-6">
            <div id="dynamicFormContainer">
                <!-- Dinamik form burada yüklenecek -->
            </div>
        </div>
    </div>

    <!-- AI Status -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">🤖 AI Sistem Durumu</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border-l-4 border-blue-500">
                    <span class="font-medium text-gray-700 dark:text-gray-300">AI Model:</span>
                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400" id="aiModel">Yükleniyor...</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border-l-4 border-green-500">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Bağlantı:</span>
                    <span class="text-sm font-semibold text-green-600 dark:text-green-400" id="aiConnection">Kontrol ediliyor...</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border-l-4 border-purple-500">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Özellikler:</span>
                    <span class="text-sm font-semibold text-purple-600 dark:text-purple-400" id="aiFeatures">Hesaplanıyor...</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Loading spinner animation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endpush

@push('scripts')
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
            formPreview.classList.remove('hidden');
            dynamicFormContainer.innerHTML = `
                <div class="flex items-center justify-center gap-3 py-8">
                    <div class="w-8 h-8 border-4 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>
                    <span class="text-gray-600 dark:text-gray-400">Form yükleniyor...</span>
                </div>
            `;

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
                dynamicFormContainer.innerHTML = `
                    <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 p-4 text-red-800 dark:text-red-200">
                        ${data.message}
                    </div>
                `;
            }

        } catch (error) {
            console.error('Form yükleme hatası:', error);
            dynamicFormContainer.innerHTML = `
                <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 p-4 text-red-800 dark:text-red-200">
                    Form yüklenirken hata oluştu. Lütfen tekrar deneyin.
                </div>
            `;
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
        formPreview.classList.add('hidden');
        formSelector.reset();
        dynamicFormContainer.innerHTML = '';
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
@endpush
@endsection
