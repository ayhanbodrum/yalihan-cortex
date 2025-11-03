@extends('admin.layouts.neo')

@section('title', 'AI Core System Test')

@section('content')
<div class="neo-container">
    <div class="neo-header">
        <h1 class="neo-title">🤖 AI Core System Test</h1>
        <p class="neo-subtitle">AI sistemini test et ve öğret</p>
    </div>

    <div class="neo-grid">
        <!-- AI Test Section -->
        <div class="neo-card">
            <div class="neo-card-header">
                <h2 class="neo-card-title">🧠 AI Test</h2>
            </div>
            <div class="neo-card-body">
                <form id="aiTestForm" class="neo-form">
                    <div class="neo-form-group">
                        <label class="neo-label">Context</label>
                        <select name="context" id="context" class="neo-select">
                            <option value="konut">Konut</option>
                            <option value="arsa">Arsa</option>
                            <option value="yazlik">Yazlık</option>
                            <option value="isyeri">İşyeri</option>
                        </select>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Input</label>
                        <textarea name="input" id="input" class="neo-textarea" placeholder="AI'ye soru sor..."></textarea>
                    </div>

                    <button type="submit" class="neo-btn neo-btn-primary">
                        🤖 AI'yi Test Et
                    </button>
                </form>

                <div id="aiTestResult" class="neo-result mt-4" style="display: none;">
                    <h3 class="neo-result-title">AI Yanıtı:</h3>
                    <div id="aiResponse" class="neo-result-content"></div>
                </div>
            </div>
        </div>

        <!-- AI Teaching Section -->
        <div class="neo-card">
            <div class="neo-card-header">
                <h2 class="neo-card-title">📚 AI Öğretimi</h2>
            </div>
            <div class="neo-card-body">
                <form id="aiTeachForm" class="neo-form">
                    <div class="neo-form-group">
                        <label class="neo-label">Context</label>
                        <select name="context" id="teachContext" class="neo-select">
                            <option value="konut">Konut</option>
                            <option value="arsa">Arsa</option>
                            <option value="yazlik">Yazlık</option>
                            <option value="isyeri">İşyeri</option>
                        </select>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Task</label>
                        <input type="text" name="task" id="task" class="neo-input" placeholder="AI'ye öğretilecek görev...">
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Expected Output</label>
                        <textarea name="expected_output" id="expected_output" class="neo-textarea" placeholder="Beklenen çıkış..."></textarea>
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

        <!-- Storage Test Section -->
        <div class="neo-card">
            <div class="neo-card-header">
                <h2 class="neo-card-title">💾 Storage Test</h2>
            </div>
            <div class="neo-card-body">
                <form id="storageTestForm" class="neo-form">
                    <div class="neo-form-group">
                        <label class="neo-label">Key</label>
                        <input type="text" name="key" id="storageKey" class="neo-input" placeholder="Storage key...">
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Data</label>
                        <textarea name="data" id="storageData" class="neo-textarea" placeholder="Storage data..."></textarea>
                    </div>

                    <button type="submit" class="neo-btn neo-btn-info">
                        💾 Storage Test
                    </button>
                </form>

                <div id="storageTestResult" class="neo-result mt-4" style="display: none;">
                    <h3 class="neo-result-title">Storage Sonucu:</h3>
                    <div id="storageResponse" class="neo-result-content"></div>
                </div>
            </div>
        </div>

        <!-- System Status Section -->
        <div class="neo-card">
            <div class="neo-card-header">
                <h2 class="neo-card-title">📊 System Status</h2>
            </div>
            <div class="neo-card-body">
                <button id="checkStatus" class="neo-btn neo-btn-warning">
                    📊 Status Kontrol Et
                </button>

                <div id="systemStatus" class="neo-result mt-4" style="display: none;">
                    <h3 class="neo-result-title">Sistem Durumu:</h3>
                    <div id="statusContent" class="neo-result-content"></div>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // AI Test Form
    document.getElementById('aiTestForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const resultDiv = document.getElementById('aiTestResult');
        const responseDiv = document.getElementById('aiResponse');

        try {
            const response = await fetch('/admin/ai-core-test/test-ai', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    context: formData.get('context'),
                    input: formData.get('input')
                })
            });

            const data = await response.json();

            if (data.success) {
                responseDiv.textContent = data.response;
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

    // AI Teaching Form
    document.getElementById('aiTeachForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const resultDiv = document.getElementById('aiTeachResult');
        const responseDiv = document.getElementById('teachResponse');

        try {
            const response = await fetch('/admin/ai-core-test/teach-ai', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    context: formData.get('context'),
                    task: formData.get('task'),
                    expected_output: formData.get('expected_output')
                })
            });

            const data = await response.json();

            if (data.success) {
                responseDiv.textContent = data.result;
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

    // Storage Test Form
    document.getElementById('storageTestForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const resultDiv = document.getElementById('storageTestResult');
        const responseDiv = document.getElementById('storageResponse');

        try {
            const response = await fetch('/admin/ai-core-test/test-storage', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    key: formData.get('key'),
                    data: formData.get('data')
                })
            });

            const data = await response.json();

            if (data.success) {
                responseDiv.textContent = `Stored: ${data.stored}\nRetrieved: ${JSON.stringify(data.retrieved, null, 2)}`;
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

    // System Status Check
    document.getElementById('checkStatus').addEventListener('click', async function() {
        const resultDiv = document.getElementById('systemStatus');
        const responseDiv = document.getElementById('statusContent');

        try {
            const response = await fetch('/admin/ai-core-test/system-status', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                responseDiv.textContent = JSON.stringify(data.status, null, 2);
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
});
</script>
@endsection
