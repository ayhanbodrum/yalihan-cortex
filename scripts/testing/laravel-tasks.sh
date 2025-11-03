#!/bin/bash
# Laravel Tasks Runner - Hızlı Laravel Komutları

if [ "$1" == "" ]; then
    echo "Kullanım: ./laravel-tasks.sh [komut]"
    echo "Komutlar:"
    echo "  serve     - Laravel sunucusunu başlatır"
    echo "  migrate   - Migrasyonları çalıştırır"
    echo "  fresh     - Veritabanını sıfırlar ve yeniden oluşturur"
    echo "  clear     - Önbellekleri temizler"
    echo "  routes    - Rotaları listeler"
    echo "  models    - Model ilişkilerini çıkarır"
    echo "  test      - PHPUnit testlerini çalıştırır"
    echo "  admin-ui  - Admin panel tema dosyalarını günceller"
    echo "  theme     - Admin panel CSS temasını oluşturur"
    echo "  ai-setup  - AI talep analiz sistemini kurar"
    echo "  docs      - Modernizasyon dökümanlarını oluşturur"
    exit 1
fi

case "$1" in
    "serve")
        php artisan serve --port=8002
        ;;
    "migrate")
        php artisan migrate
        ;;
    "fresh")
        php artisan migrate:fresh --seed
        ;;
    "clear")
        php artisan optimize:clear
        ;;
    "routes")
        php artisan route:list
        ;;
    "models")
        php artisan model:show --all
        ;;
    "test")
        vendor/bin/phpunit
        ;;
    "admin-ui")
        echo "Admin panel tema dosyaları güncelleniyor..."
        cp -r resources/assets/admin-theme/css/* public/css/admin/
        echo "Admin panel CSS dosyaları güncellendi"
        ;;
    "theme")
        echo "Admin panel CSS teması oluşturuluyor..."
        mkdir -p public/css/admin
        touch public/css/admin/admin-theme.css
        echo "Admin panel CSS teması oluşturuldu, şimdi içeriği dolduruluyor..."
        ./laravel-tasks.sh admin-ui
        echo "İşlem tamamlandı!"
        ;;
    "ai-setup")
        echo "AI talep analiz sistemi kuruluyor..."
        mkdir -p app/Http/Controllers/Admin
        mkdir -p prompts
        touch app/Http/Controllers/Admin/TalepAnalizController.php
        touch prompts/talep-analizi.prompt.md
        echo "AI talep analiz sistemi dosyaları oluşturuldu!"
        ;;
    "docs")
        echo "Modernizasyon dökümanları oluşturuluyor..."
        mkdir -p docs/admin
        mkdir -p docs/ai
        touch docs/admin/modernizasyon-plan.md
        touch docs/ai/talep-analiz-sistemi.md
        echo "Dökümanlar oluşturuldu!"
        ;;
    *)
        echo "Bilinmeyen komut: $1"
        exit 1
        ;;
esac
