#!/usr/bin/env node

/**
 * Bugün Yaşanan Hataları Öğren
 * 12 Ekim 2025 - Alpine.js + Vite + Tailwind Hataları
 */

const errorLearner = require('./knowledge/error-learner');

console.log('🎓 Bugün yaşanan hatalar öğreniliyor...\n');

// 1. @vite direktifi eksikliği
errorLearner.learnManualError('vite_directive_missing', {
    name: '@vite_missing_in_blade',
    pattern: '11 Alpine fonksiyon undefined',
    cause: '@vite direktifi blade\'de eksik',
    solution: '@vite([\'resources/js/admin/stable-create.js\']) ekle',
    location: '@push(\'scripts\') içinde',
    severity: 'critical',
    fix_time: '10 dakika',
    files_affected: ['stable-create.blade.php']
});

// 2. Vite cache sorunu
errorLearner.learnManualError('vite_cache_issue', {
    name: 'vite_cache_old_modules',
    pattern: 'Eski modül yükleniyor, yeni fonksiyonlar tanımsız',
    cause: 'node_modules/.vite/ cache eski',
    solution: 'rm -rf node_modules/.vite && vite restart',
    severity: 'high',
    fix_time: '5 dakika'
});

// 3. Tailwind @tailwind direktifi eksikliği
errorLearner.learnManualError('tailwind_errors', {
    name: '@tailwind_directives_missing',
    pattern: '@layer base is used but no @tailwind base',
    cause: 'neo-unified.css\'de @tailwind base/components/utilities eksik',
    solution: '@tailwind base; @tailwind components; @tailwind utilities; ekle (dosya başına)',
    location: 'resources/css/neo-unified.css',
    severity: 'critical',
    fix_time: '2 dakika'
});

// 4. Tailwind v4 @apply sorunu
errorLearner.learnManualError('tailwind_errors', {
    name: 'tailwind_v4_apply_restriction',
    pattern: 'Cannot apply unknown utility class',
    cause: 'Tailwind v4 @apply\'ı kısıtlamış, Neo\'da 138 @apply var',
    solution: 'Tailwind v4 → v3.4.18 downgrade: npm install -D tailwindcss@^3.4.18',
    severity: 'critical',
    fix_time: '15 dakika',
    breaking_change: true,
    version_lock: 'tailwindcss: ^3.4.18 (package.json\'da kilitle)'
});

// 5. CSP violation (Leaflet CSS)
errorLearner.learnManualError('csp_violations', {
    name: 'leaflet_css_blocked',
    pattern: 'Refused to load stylesheet from unpkg.com',
    cause: 'CSP style-src directive\'de unpkg.com yok',
    solution: 'SecurityMiddleware.php → style-src\'e https://unpkg.com ekle',
    location: 'app/Http/Middleware/SecurityMiddleware.php',
    severity: 'medium',
    fix_time: '5 dakika'
});

console.log('\n✅ Tüm bugünkü hatalar Yalıhan Bekçi hafızasına kaydedildi!');
console.log('\n📊 Özet Rapor:\n');

const report = errorLearner.generateReport();
console.log(`Toplam Kategori: ${Object.keys(report.details).length}`);
console.log(`Alpine Undefined: ${report.summary.alpine_undefined}`);
console.log(`Vite Sorunları: ${report.summary.vite_directive_missing + report.summary.vite_cache_issue}`);
console.log(`Tailwind Sorunları: ${report.summary.tailwind_errors}`);
console.log(`\nSon Tarama: ${report.summary.lastScan || 'Şimdi'}`);
