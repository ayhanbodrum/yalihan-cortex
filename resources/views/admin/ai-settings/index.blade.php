<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Ayarları</title>
</head>
<body class="p-6">
    <div class="max-w-3xl mx-auto space-y-6">
        <h1 class="text-2xl font-semibold">AI Ayarları</h1>

        <section aria-labelledby="provider" class="border rounded p-4">
            <h2 id="provider" class="text-lg font-medium">Sağlayıcı Durumu</h2>
            <div id="ai-provider-status" role="status" aria-live="polite" class="mt-2 text-sm"></div>
            <div class="mt-3">
                <button id="ai-test-provider" class="px-4 py-2 bg-blue-600 text-white rounded">Sağlayıcı Test</button>
            </div>
        </section>

        <section aria-labelledby="locale" class="border rounded p-4">
            <h2 id="locale" class="text-lg font-medium">Dil</h2>
            <div class="mt-2 flex gap-2">
                <button data-ai-locale="tr" class="px-3 py-2 bg-gray-200 rounded">Türkçe</button>
                <button data-ai-locale="en" class="px-3 py-2 bg-gray-200 rounded">English</button>
            </div>
        </section>

        <section aria-labelledby="currency" class="border rounded p-4">
            <h2 id="currency" class="text-lg font-medium">Para Birimi</h2>
            <div class="mt-2 flex gap-2">
                <button data-ai-currency="TRY" class="px-3 py-2 bg-gray-200 rounded">TRY</button>
                <button data-ai-currency="USD" class="px-3 py-2 bg-gray-200 rounded">USD</button>
                <button data-ai-currency="EUR" class="px-3 py-2 bg-gray-200 rounded">EUR</button>
                <button data-ai-currency="GBP" class="px-3 py-2 bg-gray-200 rounded">GBP</button>
            </div>
        </section>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var statusEl = document.getElementById('ai-provider-status')
        if (window.AdminAIService && typeof window.AdminAIService.getProviderStatus === 'function') {
            window.AdminAIService.getProviderStatus().then(function(res){
                var txt = ''
                if (res && res.success) {
                    var d = res.data || res
                    txt = 'Sağlayıcı: ' + (d.provider || '-') + ' • Model: ' + (d.model || '-')
                } else {
                    txt = 'Durum alınamadı'
                }
                statusEl.textContent = txt
            })
        }
        var btn = document.getElementById('ai-test-provider')
        if (btn && window.AdminAIService && typeof window.AdminAIService.getProviderStatus === 'function') {
            btn.addEventListener('click', function(){
                window.showToast('Test başlatıldı', 'info')
                window.AdminAIService.getProviderStatus().then(function(){
                    window.showToast('Sağlayıcı çalışıyor', 'success')
                }).catch(function(){
                    window.showToast('Sağlayıcı hatası', 'error')
                })
            })
        }
    })
    </script>
</body>
</html>
