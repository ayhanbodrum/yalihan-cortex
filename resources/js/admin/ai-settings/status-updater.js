/**
 * Status Updater Module
 * Context7: AI Settings status badge management
 *
 * SADECE AI Settings sayfası için kullanılır.
 */

export class StatusUpdater {
    /**
     * Update Status Badge
     * Provider status badge'ini güncelle
     *
     * @param {string} provider - Provider adı
     * @param {string} status - Status (success, error, testing)
     * @param {string} message - Status mesajı
     * @param {number} responseTime - Response time (ms)
     */
    static updateStatusBadge(provider, status, message, responseTime = 0) {
        const badge = document.getElementById(`${provider}-status-badge`);
        if (!badge) return;

        let icon, bgClass, textClass, statusText;

        switch (status) {
            case 'success':
                icon = 'fa-check-circle';
                bgClass = 'bg-green-100 dark:bg-green-900';
                textClass = 'text-green-700 dark:text-green-300';
                statusText = `✅ Aktif (${responseTime}ms)`;
                break;
            case 'error':
                icon = 'fa-times-circle';
                bgClass = 'bg-red-100 dark:bg-red-900';
                textClass = 'text-red-700 dark:text-red-300';
                statusText = '❌ Hata';
                break;
            case 'testing':
                icon = 'fa-spinner fa-spin';
                bgClass = 'bg-blue-100 dark:bg-blue-900';
                textClass = 'text-blue-700 dark:text-blue-300';
                statusText = '🔄 Test ediliyor...';
                break;
            default:
                icon = 'fa-circle';
                bgClass = 'bg-gray-100 dark:bg-gray-700';
                textClass = 'text-gray-600 dark:text-gray-400';
                statusText = 'Henüz Test Edilmedi';
        }

        badge.className = `px-3 py-1 text-xs font-medium ${bgClass} ${textClass} rounded-full flex items-center gap-1`;
        badge.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${statusText}</span>
        `;

        // Update title tooltip
        badge.title = message;
    }

    /**
     * Update All Provider Status
     * Tüm provider'ların status'unu güncelle
     *
     * @param {Object} statusData - Provider status data
     */
    static updateAllProviderStatus(statusData) {
        Object.keys(statusData).forEach((provider) => {
            const data = statusData[provider];
            this.updateStatusBadge(
                provider,
                data.status || 'unknown',
                data.message || '',
                data.response_time || 0
            );
        });
    }
}

export default StatusUpdater;
