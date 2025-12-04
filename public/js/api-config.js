/**
 * API Endpoint Configuration
 * 
 * Context7 Standard: C7-API-CONFIG-JS-2025-12-03
 * 
 * Merkezi API endpoint yönetimi için JavaScript config dosyası.
 * Tüm endpoint'ler buradan alınır, hardcoded endpoint'ler yasaktır.
 * 
 * @version 1.0.0
 * @since 2025-12-03
 */

// Prevent multiple declarations
if (typeof window.APIConfig === 'undefined') {
    window.APIConfig = {
        /**
         * Base URLs
         */
        baseUrl: window.location.origin,
        apiPrefix: '/api',
        apiV1Prefix: '/api/v1',

        /**
         * Location API Endpoints
         */
        location: {
            provinces: '/api/location/iller',
            districts: (id) => `/api/location/districts/${id}`,
            neighborhoods: (id) => `/api/location/neighborhoods/${id}`,
            geocode: '/api/location/geocode',
            reverseGeocode: '/api/location/reverse-geocode',
            nearby: (lat, lng, radius = 1000) => `/api/location/nearby/${lat}/${lng}/${radius}`,
        },

        /**
         * Geo Proxy API Endpoints (CORS-free Nominatim/Overpass)
         */
        geo: {
            geocode: '/api/geo/geocode',
            reverseGeocode: '/api/geo/reverse-geocode',
            nearby: '/api/geo/nearby',
        },

        /**
         * Categories API Endpoints
         */
        categories: {
            subcategories: (parentId) => `/api/categories/sub/${parentId}`,
            publicationTypes: (categoryId) => `/api/categories/publication-types/${categoryId}`,
            fields: (categoryId, publicationTypeId = null) => {
                if (publicationTypeId) {
                    return `/api/categories/fields/${categoryId}/${publicationTypeId}`;
                }
                return `/api/categories/fields/${categoryId}`;
            },
            detail: (id) => `/api/categories/${id}`,
        },

        /**
         * Live Search API Endpoints
         */
        liveSearch: {
            kisiler: '/api/kisiler/search',
            danismanlar: '/api/users/search',
            sites: '/api/sites/search',
            unified: '/api/search/unified',
        },

        /**
         * TKGM API Endpoints
         */
        tkgm: {
            parselSorgu: '/api/tkgm/parsel-sorgu',
            yatirimAnalizi: '/api/tkgm/yatirim-analizi',
            health: '/api/tkgm/health',
        },

        /**
         * Properties API Endpoints
         */
        properties: {
            tkgmLookup: '/api/properties/tkgm-lookup',
            calculate: '/api/properties/calculate',
        },

        /**
         * AI API Endpoints
         */
        ai: {
            analyze: '/api/ai/analyze',
            suggest: '/api/ai/suggest',
            generate: '/api/ai/generate',
            health: '/api/ai/health',
            startVideoRender: (ilanId) => `/api/ai/start-video-render/${ilanId}`,
            videoStatus: (ilanId) => `/api/ai/video-status/${ilanId}`,
        },

        /**
         * Admin API Endpoints
         */
        admin: {
            generateAiTitle: '/admin/ilanlar/generate-ai-title',
            generateAiDescription: '/admin/ilanlar/generate-ai-description',
            convertPriceToText: '/admin/ilanlar/convert-price-to-text',
            liveSearch: '/admin/ilanlar/live-search',
        },

        /**
         * Helper: Replace parameters in endpoint
         */
        replaceParams: function(endpoint, params = {}) {
            let url = endpoint;
            for (const [key, value] of Object.entries(params)) {
                url = url.replace(`{${key}}`, value);
                url = url.replace(`{${key}?}`, value);
            }
            // Remove optional parameters that weren't replaced
            url = url.replace(/\{[^}]+\?\}/g, '');
            return url;
        },

        /**
         * Helper: Get full URL
         */
        getUrl: function(endpoint, params = {}) {
            const url = typeof endpoint === 'function' 
                ? endpoint(...Object.values(params))
                : this.replaceParams(endpoint, params);
            return `${this.baseUrl}${url}`;
        },
    };

    console.log('✅ API Config loaded - All endpoints centralized');
}

