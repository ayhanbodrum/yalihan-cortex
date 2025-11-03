@extends('admin.layouts.neo')

@section('title', 'API Token Setup')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="stat-card-value mb-6">🔑 API Token Setup</h1>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-3">Auto-save için API Token Kurulumu</h2>
                    <p class="text-gray-600 mb-4">
                        Bu sayfa ile auto-save fonksiyonunu session authentication olmadan kullanabilirsiniz.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h3 class="font-medium text-gray-900 mb-2">1. API Token Oluştur</h3>
                        <p class="text-sm text-gray-600 mb-3">
                            Terminal'de şu komutu çalıştırın:
                        </p>
                        <code class="block bg-gray-800 text-green-400 p-3 rounded text-sm font-mono">
                            php artisan api:token admin@yalihan.com --name="Admin Auto-save Token"
                        </code>
                    </div>

                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h3 class="font-medium text-gray-900 mb-2">2. Token'ı Buraya Yapıştırın</h3>
                        <input type="text" id="apiToken"
                            placeholder="1|G5wplPB8b8tm2SKZhc3pwIMRmlppXPST26P24whb6fab6be4"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button onclick="saveApiToken()"
                            class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Token'ı Kaydet
                        </button>
                    </div>

                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h3 class="font-medium text-gray-900 mb-2">3. Test Et</h3>
                        <button onclick="testApiToken()"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            Auto-save API'yi Test Et
                        </button>
                        <div id="testResult" class="mt-3 p-3 rounded-lg hidden"></div>
                    </div>

                    <div class="border rounded-lg p-4 bg-yellow-50 border-yellow-200">
                        <h3 class="font-medium text-yellow-900 mb-2">⚠️ Güvenlik Notu</h3>
                        <p class="text-sm text-yellow-700">
                            API token'ı güvenli tutun. Bu token ile admin panelindeki tüm işlemleri yapabilirsiniz.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function saveApiToken() {
            const token = document.getElementById('apiToken').value.trim();
            if (!token) {
                alert('Lütfen API token girin!');
                return;
            }

            localStorage.setItem('admin_api_token', token);
            alert('✅ API Token kaydedildi! Artık auto-save çalışacak.');
        }

        function testApiToken() {
            const token = localStorage.getItem('admin_api_token');
            if (!token) {
                alert('Önce API token kaydedin!');
                return;
            }

            const musteriTipi = document.getElementById('musteri_tipi').value;
            const resultDiv = document.getElementById('testResult');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="text-blue-600">🔄 Test ediliyor...</div>';

            const testData = {
                formData: {
                    baslik: 'Test İlan',
                    ana_kategori_id: 1,
                    yayin_tipi_id: 'satilik'
                },
                stage: 'category',
                completion_percentage: 25
            };

            // Eğer musteri_tipi seçilmişse ekle
            if (musteriTipi) {
                testData.formData.musteri_tipi = musteriTipi;
            }

            fetch('/api/admin/ilanlar/auto-save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(testData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resultDiv.innerHTML = '<div class="text-green-600">✅ API çalışıyor! Auto-save başarılı.</div>';
                    } else {
                        resultDiv.innerHTML = `<div class="text-red-600">❌ API hatası: ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML = `<div class="text-red-600">❌ Bağlantı hatası: ${error.message}</div>`;
                });
        }

        // Sayfa yüklendiğinde mevcut token'ı göster
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('admin_api_token');
            if (token) {
                document.getElementById('apiToken').value = token;
            }
        });
    </script>
@endsection
