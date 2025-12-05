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

/* global window */

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
            provinces: '/api/v1/location/provinces',
            districts: (id) => `/api/v1/location/districts/${id}`,
            neighborhoods: (id) => `/api/v1/location/neighborhoods/${id}`,
            geocode: '/api/v1/location/geocode',
            reverseGeocode: '/api/v1/location/reverse-geocode',
            nearby: (lat, lng, radius = 1000) => `/api/v1/location/nearby/${lat}/${lng}/${radius}`,
        },

        /**
         * Geo Proxy API Endpoints (CORS-free Nominatim/Overpass)
         */
        geo: {
            geocode: '/api/v1/geo/geocode',
            reverseGeocode: '/api/v1/geo/reverse-geocode',
            nearby: '/api/v1/geo/nearby',
        },

        /**
         * Categories API Endpoints
         */
        categories: {
            subcategories: (parentId) => `/api/v1/categories/sub/${parentId}`,
            publicationTypes: (categoryId) => `/api/v1/categories/publication-types/${categoryId}`,
            fields: (categoryId, publicationTypeId = null) => {
                if (publicationTypeId) {
                    return `/api/v1/categories/fields/${categoryId}/${publicationTypeId}`;
                }
                return `/api/v1/categories/fields/${categoryId}`;
            },
            detail: (id) => `/api/v1/categories/${id}`,
        },

        /**
         * Live Search API Endpoints
         */
        liveSearch: {
            kisiler: '/api/v1/kisiler/search',
            danismanlar: '/api/v1/users/search',
            sites: '/api/v1/sites/search',
            unified: '/api/v1/search/unified',
        },

        /**
         * TKGM API Endpoints
         */
        tkgm: {
            parselSorgu: '/api/v1/tkgm/parsel-sorgu',
            yatirimAnalizi: '/api/v1/tkgm/yatirim-analizi',
            health: '/api/v1/tkgm/health',
        },

        /**
         * Properties API Endpoints
         */
        properties: {
            tkgmLookup: '/api/v1/properties/tkgm-lookup',
            calculate: '/api/v1/properties/calculate',
        },

        /**
         * Environment API Endpoints
         */
        environment: {
            analyze: '/api/v1/environment/analyze',
            category: (category) => `/api/v1/environment/category/${category}`,
            valuePrediction: '/api/v1/environment/value-prediction',
            pois: (lat, lng, radius = 2000, types = null) => {
                let url = `/api/v1/environment/pois?lat=${lat}&lng=${lng}&radius=${radius}`;
                if (types) {
                    url += `&types=${Array.isArray(types) ? types.join(',') : types}`;
                }
                return url;
            },
        },

        /**
         * AI API Endpoints
         */
        ai: {
            analyze: '/api/v1/ai/analyze',
            suggest: '/api/v1/ai/suggest',
            generate: '/api/v1/ai/generate',
            health: '/api/v1/ai/health',
            startVideoRender: (ilanId) => `/api/v1/ai/start-video-render/${ilanId}`,
            videoStatus: (ilanId) => `/api/v1/ai/video-status/${ilanId}`,
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
         * Yalihan Cortex API Endpoints
         */
        cortex: {
            analyze: (id) => `/api/admin/cortex/analyze/${id}`,
            video: (id) => `/api/admin/cortex/video/${id}`,
            photos: (id) => `/api/admin/cortex/photos/${id}`,
        },

        /**
         * Market Analysis API (TKGM Learning Engine)
         */
        marketAnalysis: {
            predictPrice: '/api/v1/market-analysis/predict-price',
            analysis: (ilId, ilceId = null) => {
                const suffix = ilceId ? `/${ilceId}` : '';
                return `/api/v1/market-analysis/${ilId}${suffix}`;
            },
            hotspots: (ilId) => `/api/v1/market-analysis/hotspots/${ilId}`,
            stats: '/api/v1/market-analysis/stats',
        },

        /**
         * Helper: Replace parameters in endpoint
         */
        replaceParams: function (endpoint, params = {}) {
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
        getUrl: function (endpoint, params = {}) {
            const url =
                typeof endpoint === 'function'
                    ? endpoint(...Object.values(params))
                    : this.replaceParams(endpoint, params);
            return `${this.baseUrl}${url}`;
        },
    };
}
