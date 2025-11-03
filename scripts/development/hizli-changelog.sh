#!/bin/bash

# Hızlı Changelog Ekleme Scripti
# Kullanım: ./hizli-changelog.sh

tarih=$(date +%Y-%m-%d)
degisiklik_tipi="Geliştirme"
degisiklik_baslik="Talep Analiz Modülü ve Admin Panel CSS Standardizasyonu"
degisiklik_sorumlu="EmlakPro Geliştirme Ekibi"

degisiklik_aciklama="Bootstrap ve Tailwind CSS karışımı düzeltilerek admin panel standardize edildi. Talep modülünün modüler yapısı geliştirildi ve CSS için dokümantasyon oluşturuldu."

degisen_dosyalar="- /app/Modules/Talep/TalepServiceProvider.php\n- /app/Modules/Talep/Routes/web.php\n- /public/css/admin/admin-theme.css\n- /public/css/admin/tailwind-bootstrap-bridge.css\n- /resources/views/admin/layouts/app.blade.php\n- /tailwind.config.js\n- /Documents/Teknik/admin-panel-css-standardizasyonu.md"

degisiklik_ozeti="- Tailwind CSS ve Bootstrap karışımı için köprü CSS dosyası oluşturuldu\n- TalepServiceProvider, route tanımlamaları ve modül yapısı geliştirildi\n- Admin panel CSS standardizasyon dokümantasyonu oluşturuldu\n- Tailwind konfigürasyonu eklendi ve tema renkleri standardize edildi\n- Admin panel component'leri hem Tailwind hem de Bootstrap sınıflarıyla çalışır hale getirildi"

# Değişiklikleri Changelog dosyasına ekle
echo -e "\n### ${tarih} - ${degisiklik_tipi} - ${degisiklik_baslik}\n#### Sorumlu: ${degisiklik_sorumlu}\n\n- ${degisiklik_aciklama}\n\n**Eklenen/Değiştirilen Dosyalar:**\n${degisen_dosyalar}\n\n**Değişiklik Özeti:**\n${degisiklik_ozeti}" >> /Users/macbookpro/Projects/emlakpro/Documents/changelog.md

echo "Değişiklikler başarıyla Changelog'a eklendi!"
