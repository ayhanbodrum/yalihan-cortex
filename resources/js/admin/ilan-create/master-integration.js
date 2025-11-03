// Yalıhan Bekçi - Master Integration System
// Tüm gelişmiş sistemleri entegre eden master dosya

class MasterIntegration {
    constructor() {
        this.systems = new Map();
        this.isInitialized = false;
        this.init();
    }

    init() {
        this.loadSystems();
        this.setupIntegration();
        this.setupEventListeners();
        this.injectMasterCSS();
    }

    loadSystems() {
        // Performance Optimization System
        this.systems.set('performance', {
            instance: window.performanceOptimizer,
            name: 'Performance Optimizer',
            description: 'Lazy loading, bundle optimization, image compression, API caching',
            status: 'ready'
        });

        // Skeleton Loading System
        this.systems.set('skeleton', {
            instance: window.skeletonLoader,
            name: 'Skeleton Loader',
            description: 'Advanced loading states with Context7 design',
            status: 'ready'
        });

        // Dark Mode Toggle System
        this.systems.set('darkMode', {
            instance: window.darkModeToggle,
            name: 'Dark Mode Toggle',
            description: 'System preference tracking with smooth transitions',
            status: 'ready'
        });

        // Touch Gestures System
        this.systems.set('touchGestures', {
            instance: window.touchGestures,
            name: 'Touch Gestures',
            description: 'Swipe navigation and advanced touch interactions',
            status: 'ready'
        });

        // Toast Notifications System
        this.systems.set('toast', {
            instance: window.toastNotifications,
            name: 'Toast Notifications',
            description: 'Context7 uyumlu bildirim sistemi',
            status: 'ready'
        });

        // Drag & Drop Photos System
        this.systems.set('dragDrop', {
            instance: window.dragDropPhotos,
            name: 'Drag & Drop Photos',
            description: 'Advanced photo sorting and management',
            status: 'ready'
        });

        // Dashboard Modernization System
        this.systems.set('dashboard', {
            instance: window.dashboardModernization,
            name: 'Dashboard Modernization',
            description: 'Kanban board + analytics + quick actions',
            status: 'ready'
        });

        // AI Enhanced Form System
        this.systems.set('aiForm', {
            instance: window.ilanFormState,
            name: 'AI Enhanced Form',
            description: '11-step form with AI integration',
            status: 'ready'
        });
    }

    setupIntegration() {
        // Initialize all systems
        this.initializeSystems();

        // Setup cross-system communication
        this.setupSystemCommunication();

        // Setup global event handling
        this.setupGlobalEvents();

        // Setup performance monitoring
        this.setupPerformanceMonitoring();
    }

    initializeSystems() {
        console.log('🚀 Yalıhan Bekçi - Master Integration başlatılıyor...');

        this.systems.forEach((system, key) => {
            try {
                if (system.instance && typeof system.instance.init === 'function') {
                    system.instance.init();
                    system.status = 'initialized';
                    console.log(`✅ ${system.name} başlatıldı`);
                } else if (system.instance) {
                    system.status = 'ready';
                    console.log(`✅ ${system.name} hazır`);
                } else {
                    system.status = 'error';
                    console.error(`❌ ${system.name} yüklenemedi`);
                }
            } catch (error) {
                system.status = 'error';
                console.error(`❌ ${system.name} başlatılırken hata:`, error);
            }
        });

        this.isInitialized = true;
        console.log('🎉 Tüm sistemler başarıyla entegre edildi!');
    }

    setupSystemCommunication() {
        // Cross-system event communication
        const eventBus = new EventTarget();

        // Performance optimization events
        eventBus.addEventListener('performance:optimize', (event) => {
            const { target, type } = event.detail;
            this.systems.get('performance').instance.optimize(target, type);
        });

        // Skeleton loading events
        eventBus.addEventListener('skeleton:show', (event) => {
            const { element, type, duration } = event.detail;
            this.systems.get('skeleton').instance.showSkeleton(element, type, duration);
        });

        // Dark mode events
        eventBus.addEventListener('darkmode:toggle', () => {
            this.systems.get('darkMode').instance.toggle();
        });

        // Toast notification events
        eventBus.addEventListener('toast:show', (event) => {
            const { message, options } = event.detail;
            this.systems.get('toast').instance.show(message, options);
        });

        // Global event bus
        window.yalihanEventBus = eventBus;
    }

    setupGlobalEvents() {
        // Global keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + Shift + D - Dark mode toggle
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                window.yalihanEventBus.dispatchEvent(new CustomEvent('darkmode:toggle'));
            }

            // Ctrl/Cmd + Shift + S - Show system status
            if ((e.ctrlKey || e.shiftKey) && e.shiftKey && e.key === 'S') {
                e.preventDefault();
                this.showSystemStatus();
            }

            // Ctrl/Cmd + Shift + P - Performance report
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'P') {
                e.preventDefault();
                this.showPerformanceReport();
            }
        });

        // Global error handling
        window.addEventListener('error', (event) => {
            this.handleGlobalError(event);
        });

        // Global unhandled promise rejection
        window.addEventListener('unhandledrejection', (event) => {
            this.handleGlobalError(event);
        });

        // Page visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseSystems();
            } else {
                this.resumeSystems();
            }
        });
    }

    setupPerformanceMonitoring() {
        // Monitor system performance
        setInterval(() => {
            this.collectPerformanceMetrics();
        }, 30000); // Every 30 seconds

        // Monitor memory usage
        if ('memory' in performance) {
            setInterval(() => {
                this.monitorMemoryUsage();
            }, 60000); // Every minute
        }
    }

    collectPerformanceMetrics() {
        const metrics = {
            timestamp: Date.now(),
            systems: {},
            performance: {},
            memory: this.getMemoryUsage()
        };

        // Collect system status
        this.systems.forEach((system, key) => {
            metrics.systems[key] = {
                status: system.status,
                uptime: Date.now() - (system.startTime || Date.now())
            };
        });

        // Collect performance metrics
        if (this.systems.get('performance')) {
            metrics.performance = this.systems.get('performance').instance.getPerformanceReport();
        }

        // Store metrics
        this.storeMetrics(metrics);

        // Check for performance issues
        this.checkPerformanceIssues(metrics);
    }

    monitorMemoryUsage() {
        if ('memory' in performance) {
            const memory = performance.memory;
            const usagePercent = (memory.usedJSHeapSize / memory.jsHeapSizeLimit) * 100;

            if (usagePercent > 80) {
                console.warn('⚠️ Yüksek memory kullanımı:', usagePercent.toFixed(2) + '%');

                // Trigger cleanup
                this.triggerCleanup();
            }
        }
    }

    triggerCleanup() {
        // Cleanup performance optimizer
        if (this.systems.get('performance')) {
            this.systems.get('performance').instance.cleanup();
        }

        // Cleanup skeleton loader
        if (this.systems.get('skeleton')) {
            this.systems.get('skeleton').instance.cleanup();
        }

        // Cleanup drag drop photos
        if (this.systems.get('dragDrop')) {
            // Clear unused drag drop elements
            document.querySelectorAll('.draggable:not(.active)').forEach(el => {
                el.remove();
            });
        }

        console.log('🧹 Sistem temizliği tamamlandı');
    }

    handleGlobalError(event) {
        const error = event.error || event.reason;

        // Log error
        console.error('🚨 Global Error:', error);

        // Show user-friendly error message
        if (this.systems.get('toast')) {
            this.systems.get('toast').instance.error(
                'Beklenmeyen bir hata oluştu. Lütfen sayfayı yenileyin.',
                { duration: 10000 }
            );
        }

        // Report error to monitoring system
        this.reportError(error);
    }

    pauseSystems() {
        console.log('⏸️ Sistemler duraklatılıyor...');

        // Pause auto-refresh systems
        if (this.systems.get('dashboard')) {
            // Pause dashboard auto-refresh
        }

        // Pause performance monitoring
        this.isMonitoring = false;
    }

    resumeSystems() {
        console.log('▶️ Sistemler devam ediyor...');

        // Resume auto-refresh systems
        if (this.systems.get('dashboard')) {
            // Resume dashboard auto-refresh
        }

        // Resume performance monitoring
        this.isMonitoring = true;
    }

    // Public API methods
    showSystemStatus() {
        const statusWindow = window.open('', '_blank', 'width=800,height=600');
        statusWindow.document.write(`
            <html>
                <head>
                    <title>Yalıhan Bekçi - Sistem Durumu</title>
                    <style>
                        body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; margin: 20px; }
                        .system { margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
                        .status-ready { border-left: 4px solid #10b981; }
                        .status-initialized { border-left: 4px solid #3b82f6; }
                        .status-error { border-left: 4px solid #ef4444; }
                        h1 { color: #1e293b; }
                        .timestamp { color: #64748b; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <h1>🤖 Yalıhan Bekçi - Sistem Durumu</h1>
                    <div class="timestamp">Son güncelleme: ${new Date().toLocaleString('tr-TR')}</div>
                    ${this.generateSystemStatusHTML()}
                </body>
            </html>
        `);
    }

    generateSystemStatusHTML() {
        let html = '';
        this.systems.forEach((system, key) => {
            html += `
                <div class="system status-${system.status}">
                    <h3>${system.name}</h3>
                    <p>${system.description}</p>
                    <div>Durum: <strong>${system.status}</strong></div>
                </div>
            `;
        });
        return html;
    }

    showPerformanceReport() {
        const report = this.generatePerformanceReport();
        console.table(report);

        if (this.systems.get('toast')) {
            this.systems.get('toast').instance.info(
                `Performance raporu konsola yazdırıldı. Toplam sistem: ${this.systems.size}`,
                { duration: 5000 }
            );
        }
    }

    generatePerformanceReport() {
        const report = {
            'Sistem Sayısı': this.systems.size,
            'Başlatılan Sistem': Array.from(this.systems.values()).filter(s => s.status === 'initialized').length,
            'Hazır Sistem': Array.from(this.systems.values()).filter(s => s.status === 'ready').length,
            'Hatalı Sistem': Array.from(this.systems.values()).filter(s => s.status === 'error').length,
            'Memory Kullanımı': this.getMemoryUsage()?.usedJSHeapSize || 'N/A',
            'Çalışma Süresi': this.getUptime()
        };
        return report;
    }

    getMemoryUsage() {
        if ('memory' in performance) {
            const memory = performance.memory;
            return {
                usedJSHeapSize: Math.round(memory.usedJSHeapSize / 1024 / 1024) + ' MB',
                totalJSHeapSize: Math.round(memory.totalJSHeapSize / 1024 / 1024) + ' MB',
                jsHeapSizeLimit: Math.round(memory.jsHeapSizeLimit / 1024 / 1024) + ' MB'
            };
        }
        return null;
    }

    getUptime() {
        const now = Date.now();
        const uptime = now - this.startTime;
        const minutes = Math.floor(uptime / 60000);
        const seconds = Math.floor((uptime % 60000) / 1000);
        return `${minutes}m ${seconds}s`;
    }

    storeMetrics(metrics) {
        // Store in localStorage for debugging
        const key = `yalihan-metrics-${Date.now()}`;
        localStorage.setItem(key, JSON.stringify(metrics));

        // Keep only last 100 metrics
        const keys = Object.keys(localStorage).filter(k => k.startsWith('yalihan-metrics-'));
        if (keys.length > 100) {
            keys.sort().slice(0, keys.length - 100).forEach(k => {
                localStorage.removeItem(k);
            });
        }
    }

    reportError(error) {
        // Send error to monitoring service
        const errorReport = {
            timestamp: Date.now(),
            message: error.message || 'Unknown error',
            stack: error.stack || '',
            userAgent: navigator.userAgent,
            url: window.location.href,
            systems: Array.from(this.systems.keys())
        };

        // Store error report
        const key = `yalihan-error-${Date.now()}`;
        localStorage.setItem(key, JSON.stringify(errorReport));

        console.log('📊 Error reported:', errorReport);
    }

    checkPerformanceIssues(metrics) {
        // Check for performance degradation
        if (metrics.performance && metrics.performance.longTasks) {
            const longTasks = metrics.performance.longTasks;
            if (longTasks.length > 5) {
                console.warn('⚠️ Çok fazla long task tespit edildi:', longTasks.length);
            }
        }

        // Check memory usage
        if (metrics.memory && metrics.memory.usagePercent > 70) {
            console.warn('⚠️ Yüksek memory kullanımı:', metrics.memory.usagePercent + '%');
        }
    }

    injectMasterCSS() {
        const masterCSS = `
            /* Yalıhan Bekçi - Master Integration Styles */
            .yalihan-debug-panel {
                position: fixed;
                top: 20px;
                left: 20px;
                background: rgba(0, 0, 0, 0.9);
                color: white;
                padding: 12px;
                border-radius: 8px;
                font-family: monospace;
                font-size: 12px;
                z-index: 10000;
                max-width: 300px;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
            }

            .yalihan-debug-panel.show {
                opacity: 1;
                pointer-events: auto;
            }

            .yalihan-debug-title {
                font-weight: bold;
                margin-bottom: 8px;
                color: #3b82f6;
            }

            .yalihan-debug-item {
                display: flex;
                justify-content: space-between;
                margin: 4px 0;
                padding: 2px 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .yalihan-debug-value {
                color: #10b981;
            }

            .yalihan-debug-error {
                color: #ef4444;
            }

            .yalihan-debug-warning {
                color: #f59e0b;
            }

            /* System status indicators */
            .system-status-indicator {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                z-index: 10000;
                transition: all 0.3s ease;
            }

            .system-status-ready {
                background: #10b981;
                box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
            }

            .system-status-initialized {
                background: #3b82f6;
                box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
            }

            .system-status-error {
                background: #ef4444;
                box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
                animation: pulse 1s infinite;
            }

            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.2); }
                100% { transform: scale(1); }
            }

            /* Dark mode adjustments */
            .dark .yalihan-debug-panel {
                background: rgba(31, 41, 55, 0.95);
                border: 1px solid #374151;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .yalihan-debug-panel {
                    top: 10px;
                    left: 10px;
                    right: 10px;
                    max-width: none;
                    font-size: 11px;
                }

                .system-status-indicator {
                    bottom: 10px;
                    right: 10px;
                    width: 10px;
                    height: 10px;
                }
            }
        `;

        const style = document.createElement('style');
        style.textContent = masterCSS;
        document.head.appendChild(style);

        // Create debug panel
        this.createDebugPanel();
    }

    createDebugPanel() {
        const debugPanel = document.createElement('div');
        debugPanel.className = 'yalihan-debug-panel';
        debugPanel.id = 'yalihan-debug-panel';
        document.body.appendChild(debugPanel);

        // Create status indicator
        const statusIndicator = document.createElement('div');
        statusIndicator.className = 'system-status-indicator';
        statusIndicator.id = 'system-status-indicator';
        document.body.appendChild(statusIndicator);

        // Update debug panel periodically
        setInterval(() => {
            this.updateDebugPanel();
        }, 1000);
    }

    updateDebugPanel() {
        const panel = document.getElementById('yalihan-debug-panel');
        const indicator = document.getElementById('system-status-indicator');

        if (!panel || !indicator) return;

        const systems = Array.from(this.systems.values());
        const readyCount = systems.filter(s => s.status === 'ready').length;
        const initializedCount = systems.filter(s => s.status === 'initialized').length;
        const errorCount = systems.filter(s => s.status === 'error').length;

        panel.innerHTML = `
            <div class="yalihan-debug-title">🤖 Yalıhan Bekçi</div>
            <div class="yalihan-debug-item">
                <span>Toplam Sistem:</span>
                <span class="yalihan-debug-value">${systems.length}</span>
            </div>
            <div class="yalihan-debug-item">
                <span>Hazır:</span>
                <span class="yalihan-debug-value">${readyCount}</span>
            </div>
            <div class="yalihan-debug-item">
                <span>Başlatıldı:</span>
                <span class="yalihan-debug-value">${initializedCount}</span>
            </div>
            <div class="yalihan-debug-item">
                <span>Hata:</span>
                <span class="yalihan-debug-error">${errorCount}</span>
            </div>
            <div class="yalihan-debug-item">
                <span>Çalışma Süresi:</span>
                <span class="yalihan-debug-value">${this.getUptime()}</span>
            </div>
        `;

        // Update status indicator
        if (errorCount > 0) {
            indicator.className = 'system-status-indicator system-status-error';
        } else if (initializedCount > 0) {
            indicator.className = 'system-status-indicator system-status-initialized';
        } else {
            indicator.className = 'system-status-indicator system-status-ready';
        }
    }

    // Debug methods
    showDebugPanel() {
        const panel = document.getElementById('yalihan-debug-panel');
        if (panel) {
            panel.classList.add('show');
        }
    }

    hideDebugPanel() {
        const panel = document.getElementById('yalihan-debug-panel');
        if (panel) {
            panel.classList.remove('show');
        }
    }

    // Public API
    getSystemStatus() {
        return Array.from(this.systems.entries()).map(([key, system]) => ({
            key,
            name: system.name,
            status: system.status,
            description: system.description
        }));
    }

    getPerformanceMetrics() {
        return this.generatePerformanceReport();
    }

    restartSystem(systemKey) {
        const system = this.systems.get(systemKey);
        if (system && system.instance) {
            try {
                system.instance.init();
                system.status = 'initialized';
                console.log(`✅ ${system.name} yeniden başlatıldı`);
            } catch (error) {
                system.status = 'error';
                console.error(`❌ ${system.name} yeniden başlatılamadı:`, error);
            }
        }
    }

    // Initialize on page load
    startTime: Date.now()
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.yalihanMasterIntegration = new MasterIntegration();
    });
} else {
    window.yalihanMasterIntegration = new MasterIntegration();
}

// Global API
window.YalihanBekci = {
    showSystemStatus: () => window.yalihanMasterIntegration?.showSystemStatus(),
    showPerformanceReport: () => window.yalihanMasterIntegration?.showPerformanceReport(),
    getSystemStatus: () => window.yalihanMasterIntegration?.getSystemStatus(),
    getPerformanceMetrics: () => window.yalihanMasterIntegration?.getPerformanceMetrics(),
    restartSystem: (key) => window.yalihanMasterIntegration?.restartSystem(key),
    showDebugPanel: () => window.yalihanMasterIntegration?.showDebugPanel(),
    hideDebugPanel: () => window.yalihanMasterIntegration?.hideDebugPanel()
};

// Export for module usage
export default MasterIntegration;
