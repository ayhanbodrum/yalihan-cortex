/**
 * AI Register - AdminAIService Global Export
 * Context7: AI Settings sayfası için AdminAIService'i window'a export eder
 */

import { AIService } from './services/AIService.js';

// ✅ AdminAIService'i window'a export et (backward compatibility)
if (typeof window !== 'undefined') {
    window.AdminAIService = AIService;

    // DOM yüklendiğinde provider status'u otomatik yükle
    document.addEventListener('DOMContentLoaded', () => {
        const statusEl = document.getElementById('ai-provider-status');
        if (
            statusEl &&
            window.AdminAIService &&
            typeof window.AdminAIService.getProviderStatus === 'function'
        ) {
            window.AdminAIService.getProviderStatus()
                .then((res) => {
                    if (res && res.success) {
                        const d = res.data || res;
                        statusEl.textContent =
                            'Sağlayıcı: ' + (d.provider || '-') + ' • Model: ' + (d.model || '-');
                    } else {
                        statusEl.textContent = 'Durum alınamadı';
                    }
                })
                .catch(() => {
                    if (statusEl) statusEl.textContent = 'Durum alınamadı';
                });
        }
    });
}

export default AIService;
