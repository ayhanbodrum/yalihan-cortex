@extends('admin.layouts.neo')

@section('title', 'AI Destekli Kategori Yönetimi')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">🤖 AI Destekli Kategori Yönetimi</h1>
        <p class="text-gray-600 dark:text-gray-400">AI ile kategori analizi, öneriler ve hibrit sıralama</p>
    </div>

    <div class="space-y-6">
        <!-- Kategori Listesi -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">📊 Kategoriler</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($categories as $category)
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $category->name }}</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $category->features->count() }} özellik
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $category->ilanlar->count() }} ilan</span>
                            <div class="flex flex-wrap gap-2">
                                <button onclick="analyzeCategory('{{ $category->slug }}')"
                                        class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-sm rounded-md bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                    🤖 Analiz Et
                                </button>
                                <button onclick="getSuggestions('{{ $category->slug }}')"
                                        class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-sm rounded-md bg-gradient-to-r from-green-500 to-emerald-600 text-white hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                                    💡 Öneriler
                                </button>
                                <button onclick="generateHibritSiralama('{{ $category->slug }}')"
                                        class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-sm rounded-md bg-yellow-500 text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-200">
                                    📊 Hibrit Sıralama
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- AI Analiz Sonuçları -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">🧠 AI Analiz Sonuçları</h2>
            </div>
            <div class="p-6">
                <div class="bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500 dark:border-blue-400 rounded-md p-4 mb-4">
                    <p class="text-blue-800 dark:text-blue-200"><strong>💡 Kullanım:</strong> Kategorilerdeki butonlara tıklayarak AI analizi başlatabilirsiniz.</p>
                </div>

                <div id="aiAnalysisResult" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mt-4 hidden">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">AI Analizi:</h3>
                    <div id="analysisContent" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-3 font-mono text-sm whitespace-pre-wrap max-h-[300px] overflow-y-auto text-gray-900 dark:text-gray-100"></div>
                </div>

                <div id="aiSuggestionsResult" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mt-4 hidden">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">AI Önerileri:</h3>
                    <div id="suggestionsContent" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-3 font-mono text-sm whitespace-pre-wrap max-h-[300px] overflow-y-auto text-gray-900 dark:text-gray-100"></div>
                </div>

                <div id="hibritSiralamaResult" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mt-4 hidden">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Hibrit Sıralama:</h3>
                    <div id="siralamaContent" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-3 font-mono text-sm whitespace-pre-wrap max-h-[300px] overflow-y-auto text-gray-900 dark:text-gray-100"></div>
                </div>
            </div>
        </div>

        <!-- AI Öğretimi -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">📚 AI Öğretimi</h2>
            </div>
            <div class="p-6">
                <form id="aiTeachForm" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="teachCategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                        <select style="color-scheme: light dark;" name="category_slug" id="teachCategory" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                            @foreach($categories as $category)
                            <option value="{{ $category->slug }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Örnek 1</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="example0_task" class="sr-only">Görev</label>
                                <input type="text" id="example0_task" name="examples[0][task]" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Görev...">
                            </div>
                            <div>
                                <label for="example0_expected" class="sr-only">Beklenen çıkış</label>
                                <input type="text" id="example0_expected" name="examples[0][expected_output]" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Beklenen çıkış...">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Örnek 2</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="example1_task" class="sr-only">Görev</label>
                                <input type="text" id="example1_task" name="examples[1][task]" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Görev...">
                            </div>
                            <div>
                                <label for="example1_expected" class="sr-only">Beklenen çıkış</label>
                                <input type="text" id="example1_expected" name="examples[1][expected_output]" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Beklenen çıkış...">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-gradient-to-r from-green-500 to-emerald-600 text-white hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                        📚 AI'yi Öğret
                    </button>
                </form>

                <div id="aiTeachResult" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mt-4 hidden">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Öğretim Sonucu:</h3>
                    <div id="teachResponse" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-3 font-mono text-sm whitespace-pre-wrap max-h-[300px] overflow-y-auto text-gray-900 dark:text-gray-100"></div>
                </div>
            </div>
        </div>

        <!-- Tüm Kategoriler Analizi -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">📊 Tüm Kategoriler Analizi</h2>
            </div>
            <div class="p-6">
                <button id="analyzeAllCategories" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                    📊 Tüm Kategorileri Analiz Et
                </button>

                <div id="allCategoriesResult" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mt-4 hidden">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Tüm Kategoriler Analizi:</h3>
                    <div id="allCategoriesContent" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-3 font-mono text-sm whitespace-pre-wrap max-h-[300px] overflow-y-auto text-gray-900 dark:text-gray-100"></div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                resultDiv.classList.remove('hidden');
                console.log('🎯 Sonuç div gösterildi');
            } else {
                console.log('❌ AI Analiz hatası:', data.error);
                contentDiv.textContent = 'Hata: ' + data.error;
                resultDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.log('💥 JavaScript hatası:', error);
            contentDiv.textContent = 'Hata: ' + error.message;
            resultDiv.classList.remove('hidden');
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
                resultDiv.classList.remove('hidden');
            } else {
                contentDiv.textContent = 'Hata: ' + data.error;
                resultDiv.classList.remove('hidden');
            }
        } catch (error) {
            contentDiv.textContent = 'Hata: ' + error.message;
            resultDiv.classList.remove('hidden');
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
                resultDiv.classList.remove('hidden');
            } else {
                contentDiv.textContent = 'Hata: ' + data.error;
                resultDiv.classList.remove('hidden');
            }
        } catch (error) {
            contentDiv.textContent = 'Hata: ' + error.message;
            resultDiv.classList.remove('hidden');
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
                resultDiv.classList.remove('hidden');
            } else {
                responseDiv.textContent = 'Hata: ' + data.error;
                resultDiv.classList.remove('hidden');
            }
        } catch (error) {
            responseDiv.textContent = 'Hata: ' + error.message;
            resultDiv.classList.remove('hidden');
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
                resultDiv.classList.remove('hidden');
            } else {
                contentDiv.textContent = 'Hata: ' + data.error;
                resultDiv.classList.remove('hidden');
            }
        } catch (error) {
            contentDiv.textContent = 'Hata: ' + error.message;
            resultDiv.classList.remove('hidden');
        }
    });
});
</script>
@endsection
