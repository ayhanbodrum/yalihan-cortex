import autoprefixer from "autoprefixer";
import laravel from "laravel-vite-plugin";
import tailwindcss from "tailwindcss";
import { defineConfig } from "vite";

export default defineConfig({
    server: {
        hmr: {
            host: 'localhost',
        },
        port: 5173, // CSP'de izinli port
    },
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/leaflet.css",
                "public/css/advanced-leaflet.css",
                "resources/js/app.js",
                "resources/js/admin/global.js",
                "resources/js/admin/feature-list.js",
                "resources/js/admin/neo.js",
                "resources/js/leaflet-loader.js",
                // Person selector components
                "resources/js/components/UnifiedPersonSelector.js",

                // Enhanced Location Manager (Context7 compliant)
                "resources/js/components/LocationManager.js",
                "resources/js/components/LocationSystemTester.js",

                "resources/js/admin/services/ValidationManager.js",
                "resources/js/admin/services/AutoSaveManager.js",
                // İlan Create Modular JS
                "resources/js/admin/ilan-create.js",
                // AI Settings Modular JS (Hybrid Architecture)
                "resources/js/admin/ai-settings/core.js",

                // Advanced OpenStreetMap Integration
                "resources/js/leaflet-integration.js",
                "public/js/advanced-leaflet-integration.js",
            ],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer],
        },
    },
    build: {
        // Production optimizations
        rollupOptions: {
            output: {
                // Asset file names
                assetFileNames: (assetInfo) => {
                    let extType = assetInfo.name.split(".").at(1);
                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
                        extType = "img";
                    }
                    return `assets/${extType}/[name]-[hash][extname]`;
                },
                chunkFileNames: "assets/js/[name]-[hash].js",
                entryFileNames: "assets/js/[name]-[hash].js",
            },
        },
        // Minification and optimization
        minify: "terser",
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.logs in production
                drop_debugger: true,
            },
        },
        // Chunk size warnings
        chunkSizeWarningLimit: 1000,
        // Source maps for debugging
        sourcemap: false, // Set to true for debugging in production
    },
    // Development server settings
    server: {
        hmr: {
            host: "localhost",
        },
        host: "0.0.0.0",
        port: 5175,
    },
    // Resolve aliases
    resolve: {
        alias: {
            "@": "/resources",
            "@js": "/resources/js",
            "@css": "/resources/css",
            "@components": "/resources/js/components",
        },
    },
});
