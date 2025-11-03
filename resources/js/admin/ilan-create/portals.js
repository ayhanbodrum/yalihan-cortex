// portals.js - Portal Yönetimi Modülü

/**
 * Modern Portal Selector
 * 6 portal için entegrasyon yönetimi
 */
window.modernPortalSelector = function () {
    return {
        // Portal states
        portals: {
            sahibinden: { enabled: false, id: '', price: '' },
            hepsiemlak: { enabled: false, id: '', price: '' },
            emlakjet: { enabled: false, id: '', price: '' },
            zingat: { enabled: false, id: '', price: '' },
            hurriyetemlak: { enabled: false, id: '', price: '' },
            emlak365: { enabled: false, id: '', price: '' },
        },

        // Portal statuses (sync durumu)
        portalStatuses: {
            sahibinden: { status: 'pending', message: '', enabled: false },
            hepsiemlak: { status: 'pending', message: '', enabled: false },
            emlakjet: { status: 'pending', message: '', enabled: false },
            zingat: { status: 'pending', message: '', enabled: false },
            hurriyetemlak: { status: 'pending', message: '', enabled: false },
            emlak365: { status: 'pending', message: '', enabled: false },
        },

        allSelected: false,

        /**
         * Seçili portal sayısı
         */
        get selectedPortalCount() {
            return Object.values(this.portals).filter((p) => p.enabled).length;
        },

        /**
         * Tümünü seç/kaldır
         */
        toggleAll() {
            this.allSelected = !this.allSelected;
            Object.keys(this.portals).forEach((key) => {
                this.portals[key].enabled = this.allSelected;
            });
        },

        /**
         * Portal toggle
         */
        togglePortal(portalName) {
            this.portals[portalName].enabled = !this.portals[portalName].enabled;
            this.updateAllSelectedState();
        },

        /**
         * All selected state güncelle
         */
        updateAllSelectedState() {
            this.allSelected = Object.values(this.portals).every((p) => p.enabled);
        },

        /**
         * Portal fiyatı güncelle
         */
        updatePortalPrice(portalName, price) {
            this.portals[portalName].price = price;
        },

        /**
         * Senkronizasyon başlat
         */
        async syncToPortals() {
            const enabled = Object.entries(this.portals)
                .filter(([key, portal]) => portal.enabled)
                .map(([key]) => key);

            if (enabled.length === 0) {
                window.toast?.warning('Lütfen en az bir portal seçin');
                return;
            }

            window.toast?.info(`${enabled.length} portala senkronizasyon başlatılıyor...`);

            // Simulate sync (gerçek API entegrasyonu için)
            enabled.forEach((portalName) => {
                this.portalStatuses[portalName] = {
                    status: 'syncing',
                    message: 'Senkronize ediliyor...',
                    enabled: true,
                };
            });

            // TODO: Gerçek API çağrısı
            console.log('Syncing to portals:', enabled);
        },

        init() {
            console.log('Portal selector initialized');
        },
    };
};

// Export for Vite
export default window.modernPortalSelector;
