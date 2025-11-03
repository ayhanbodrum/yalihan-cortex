// publication.js - Yayın Yönetimi

/**
 * Publication Manager
 * İlan yayınlama ve durum yönetimi
 */
window.publicationManager = function () {
    return {
        // Yayın durumu
        status: 'Taslak',
        publishDate: null,
        expiryDate: null,

        // Görünürlük ayarları
        isVisible: true,
        showOnWebsite: true,
        showOnMobile: true,

        // Öncelik
        priority: 'normal',
        isFeatured: false,
        isUrgent: false,

        // Portal durumları (Context7 uyumlu)
        portalStatuses: {
            sahibinden: { enabled: false, status: 'pending', message: '' },
            hepsiemlak: { enabled: false, status: 'pending', message: '' },
            emlakjet: { enabled: false, status: 'pending', message: '' },
            zingat: { enabled: false, status: 'pending', message: '' },
            hurriyetemlak: { enabled: false, status: 'pending', message: '' },
            emlak365: { enabled: false, status: 'pending', message: '' },
        },

        /**
         * Durumu değiştir
         */
        changeStatus(newStatus) {
            const validStatuses = ['Taslak', 'Aktif', 'Pasif', 'Beklemede'];

            if (validStatuses.includes(newStatus)) {
                this.status = newStatus;

                if (newStatus === 'Aktif' && !this.publishDate) {
                    this.publishDate = new Date().toISOString().split('T')[0];
                }

                window.toast?.success(`Durum: ${newStatus}`);
            }
        },

        /**
         * Hemen yayınla
         */
        publishNow() {
            this.status = 'Aktif';
            this.publishDate = new Date().toISOString().split('T')[0];
            this.isVisible = true;
            this.showOnWebsite = true;

            window.toast?.success('İlan aktif olarak ayarlandı');
        },

        /**
         * Taslak olarak kaydet
         */
        saveAsDraft() {
            this.status = 'Taslak';
            window.toast?.info('Taslak olarak kaydedilecek');
        },

        /**
         * Öne çıkar
         */
        toggleFeatured() {
            this.isFeatured = !this.isFeatured;

            if (this.isFeatured) {
                window.toast?.success('İlan öne çıkarıldı');
            }
        },

        /**
         * Acil ilan yap
         */
        toggleUrgent() {
            this.isUrgent = !this.isUrgent;

            if (this.isUrgent) {
                this.priority = 'high';
                window.toast?.success('Acil ilan olarak işaretlendi');
            } else {
                this.priority = 'normal';
            }
        },

        init() {
            console.log('Publication manager initialized');
        },
    };
};

// Export
export default window.publicationManager;
