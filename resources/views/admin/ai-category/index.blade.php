@extends('admin.layouts.neo')

@section('title', 'AI Destekli Kategori Yönetimi')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="neo-container">
    <div class="neo-header">
        <h1 class="neo-title">🤖 AI Destekli Kategori Yönetimi</h1>
        <p class="neo-subtitle">AI ile kategori analizi, öneriler ve hibrit sıralama</p>
    </div>

    <div class="neo-grid">
        <!-- Kategori Listesi -->
        <div class="neo-card">
            <div class="neo-card-header">
                <h2 class="neo-card-title">📊 Kategoriler</h2>
            </div>
            <div class="neo-card-body">
                <div class="neo-grid grid-cols-2 gap-4">
                    @foreach($categories as $category)
                    <div class="neo-card neo-card-sm">
                        <div class="neo-card-header">
                            <h3 class="neo-card-title">{{ $category->name }}</h3>
                            <span class="neo-badge neo-badge-primary">{{ $category->features->count() }} özellik</span>
                        </div>
                        <div class="neo-card-body">
                            <div class="neo-flex neo-justify-between neo-items-center">
                                <span class="neo-text-sm neo-text-gray-600">{{ $category->ilanlar->count() }} ilan</span>
                                <div class="neo-flex neo-gap-2">
                                    <button onclick="analyzeCategory('{{ $category->slug }}')"
                                            class="neo-btn neo-btn-sm neo-btn-primary">
                                        🤖 Analiz Et
                                    </button>
                                    <button onclick="getSuggestions('{{ $category->slug }}')"
                                            class="neo-btn neo-btn-sm neo-btn-success">
                                        💡 Öneriler
                                    </button>
                                    <button onclick="generateHibritSiralama('{{ $category->slug }}')"
                                            class="neo-btn neo-btn-sm neo-btn-warning">
                                        📊 Hibrit Sıralama
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- AI Analiz Sonuçları -->
        <div class="neo-card">
            <div class="neo-card-header">
                <h2 class="neo-card-title">🧠 AI Analiz Sonuçları</h2>
            </div>
            <div class="neo-card-body">
                <div class="neo-alert neo-alert-info">
                    <p><strong>💡 Kullanım:</strong> Kategorilerdeki butonlara tıklayarak AI analizi başlatabilirsiniz.</p>
                </div>

                <div id="aiAnalysisResult" class="neo-result" style="display: none;">
                    <h3 class="neo-result-title">AI Analizi:</h3>
                    <div id="analysisContent" class="neo-result-content"></div>
                </div>

                <div id="aiSuggestionsResult" class="neo-result" style="display: none;">
                    <h3 class="neo-result-title">AI Önerileri:</h3>
                    <div id="suggestionsContent" class="neo-result-content"></div>
                </div>

                <div id="hibritSiralamaResult" class="neo-result" style="display: none;">
                    <h3 class="neo-result-title">Hibrit Sıralama:</h3>
                    <div id="siralamaContent" class="neo-result-content"></div>
                </div>
            </div>
        </div>

        <!-- AI Öğretimi -->
        <div class="neo-card">
            <div class="neo-card-header">
                <h2 class="neo-card-title">📚 AI Öğretimi</h2>
            </div>
            <div class="neo-card-body">
                <form id="aiTeachForm" class="neo-form">
                    <div class="neo-form-group">
                        <label class="neo-label">Kategori</label>
                        <select style="color-scheme: light dark;" name="category_slug" id="teachCategory" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                            @foreach($categories as $category)
                            <option value="{{ $category->slug }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Örnek 1</label>
                        <div class="neo-grid grid-cols-2 gap-2">
                            <input type="text" name="examples[0][task]" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Görev...">
                            <input type="text" name="examples[0][expected_output]" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Beklenen çıkış...">
                        </div>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Örnek 2</label>
                        <div class="neo-grid grid-cols-2 gap-2">
                            <input type="text" name="examples[1][task]" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Görev...">
                            <input type="text" name="examples[1][expected_output]" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Beklenen çıkış...">
                        </div>
                    </div>

                    <button type="submit" class="neo-btn neo-btn-success">
                        📚 AI'yi Öğret
                    </button>
                </form>

                <div id="aiTeachResult" class="neo-result mt-4" style="display: none;">
                    <h3 class="neo-result-title">Öğretim Sonucu:</h3>
                    <div id="teachResponse" class="neo-result-content"></div>
                </div>
            </div>
        </div>

        <!-- Tüm Kategoriler Analizi -->
        <div class="neo-card">
            <div class="neo-card-header">
                <h2 class="neo-card-title">📊 Tüm Kategoriler Analizi</h2>
            </div>
            <div class="neo-card-body">
                <button id="analyzeAllCategories" class="neo-btn neo-btn-info">
                    📊 Tüm Kategorileri Analiz Et
                </button>

                <div id="allCategoriesResult" class="neo-result mt-4" style="display: none;">
                    <h3 class="neo-result-title">Tüm Kategoriler Analizi:</h3>
                    <div id="allCategoriesContent" class="neo-result-content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.neo-result {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 16px;
    margin-top: 16px;
}

.neo-result-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #495057;
}

.neo-result-content {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 12px;
    font-family: monospace;
    font-size: 14px;
    white-space: pre-wrap;
    max-height: 300px;
    overflow-y: auto;
}

.neo-card-sm {
    padding: 12px;
}

.neo-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.neo-badge-primary {
    background: #007bff;
    color: white;
}

.neo-alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 16px;
    border-left: 4px solid;
}

.neo-alert-info {
    background: #e3f2fd;
    border-left-color: #2196f3;
    color: #0d47a1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kategori analizi
    window.analyzeCategory = async function(categorySlug) {
        console.log('🔍 AI Analiz başlatılıyor:', categorySlug);
        const resultDiv = document.getElementById('aiAnalysisResult');
        const contentDiv = document.getElementById('analysisContent');

        try {
            console.log('📡 API çağrısı başlatılıyor...');
            const response = await fetch('/admin/ai-category/analyze', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    category_slug: categorySlug
                }),
                timeout: 30000 // 30 saniye timeout
            });

            console.log('📡 API yanıtı alındı:', response.status);

            const data = await response.json();
            console.log('📊 AI Analiz sonucu:', data);

            if (data.success) {
                console.log('✅ AI Analiz başarılı, sonuç gösteriliyor');
                contentDiv.textContent = JSON.stringify(data.analysis, null, 2);
                resultDiv.style.display = 'block';
                console.log('🎯 Sonuç div gösterildi:', resultDiv.style.display);
            } else {
                console.log('❌ AI Analiz hatası:', data.error);
                contentDiv.textContent = 'Hata: ' + data.error;
                resultDiv.style.display = 'block';
            }
        } catch (error) {
            console.log('💥 JavaScript hatası:', error);
            contentDiv.textContent = 'Hata: ' + error.message;
            resultDiv.style.display = 'block';
        }
    };

    // Kategori önerileri
    window.getSuggestions = async function(categorySlug) {
        const resultDiv = document.getElementById('aiSuggestionsResult');
        const contentDiv = document.getElementById('suggestionsContent');

        try {
            const response = await fetch('/admin/ai-category/suggestions', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    category_slug: categorySlug
                })
            });

            const data = await response.json();

            if (data.success) {
                contentDiv.textContent = JSON.stringify(data.suggestions, null, 2);
                resultDiv.style.display = 'block';
            } else {
                contentDiv.textContent = 'Hata: ' + data.error;
                resultDiv.style.display = 'block';
            }
        } catch (error) {
            contentDiv.textContent = 'Hata: ' + error.message;
            resultDiv.style.display = 'block';
        }
    };

    // Hibrit sıralama
    window.generateHibritSiralama = async function(categorySlug) {
        const resultDiv = document.getElementById('hibritSiralamaResult');
        const contentDiv = document.getElementById('siralamaContent');

        try {
            const response = await fetch('/admin/ai-category/hibrit-siralama', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    category_slug: categorySlug
                })
            });

            const data = await response.json();

            if (data.success) {
                contentDiv.textContent = JSON.stringify(data.siralama, null, 2);
                resultDiv.style.display = 'block';
            } else {
                contentDiv.textContent = 'Hata: ' + data.error;
                resultDiv.style.display = 'block';
            }
        } catch (error) {
            contentDiv.textContent = 'Hata: ' + error.message;
            resultDiv.style.display = 'block';
        }
    };

    // AI öğretimi
    document.getElementById('aiTeachForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const resultDiv = document.getElementById('aiTeachResult');
        const responseDiv = document.getElementById('teachResponse');

        const examples = [];
        for (let i = 0; i < 2; i++) {
            const task = formData.get(`examples[${i}][task]`);
            const expected = formData.get(`examples[${i}][expected_output]`);
            if (task && expected) {
                examples.push({ task, expected_output: expected });
            }
        }

        try {
            const response = await fetch('/admin/ai-category/teach', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    category_slug: formData.get('category_slug'),
                    examples: examples
                })
            });

            const data = await response.json();

            if (data.success) {
                responseDiv.textContent = data.message;
                resultDiv.style.display = 'block';
            } else {
                responseDiv.textContent = 'Hata: ' + data.error;
                resultDiv.style.display = 'block';
            }
        } catch (error) {
            responseDiv.textContent = 'Hata: ' + error.message;
            resultDiv.style.display = 'block';
        }
    });

    // Tüm kategoriler analizi
    document.getElementById('analyzeAllCategories').addEventListener('click', async function() {
        const resultDiv = document.getElementById('allCategoriesResult');
        const contentDiv = document.getElementById('allCategoriesContent');

        try {
            const response = await fetch('/admin/ai-category/analyze-all', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                contentDiv.textContent = JSON.stringify(data.results, null, 2);
                resultDiv.style.display = 'block';
            } else {
                contentDiv.textContent = 'Hata: ' + data.error;
                resultDiv.style.display = 'block';
            }
        } catch (error) {
            contentDiv.textContent = 'Hata: ' + error.message;
            resultDiv.style.display = 'block';
        }
    });
});
</script>
@endsection
