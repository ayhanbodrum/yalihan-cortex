#!/bin/bash

# EmlakPro Geliştirme Ortamı Başlatıcısı
# Neo Design System ile optimize edilmiş

echo "🚀 EMLAKPRO GELİŞTİRME ORTAMI"
echo "============================="
echo ""

# Renkli çıktı fonksiyonları
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[OK]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Sistem durumu kontrolü
print_status "Sistem durumu kontrol ediliyor..."

# Neo CSS varlığını kontrol et
if [ -f "resources/css/admin/neo.css" ]; then
    print_success "Neo Design System hazır"
else
    print_warning "Neo CSS dosyası bulunamadı"
fi

# Laravel env dosyasını kontrol et
if [ -f ".env" ]; then
    print_success ".env dosyası mevcut"
else
    print_warning ".env dosyası bulunamadı, örnek dosyadan kopyalanıyor..."
    cp .env.example .env
    php artisan key:generate
fi

# Veritabanı bağlantısını kontrol et
print_status "Veritabanı bağlantısı kontrol ediliyor..."
if php artisan migrate:status > /dev/null 2>&1; then
    print_success "Veritabanı bağlantısı başarılı"
else
    print_warning "Veritabanı bağlantısı sorunu var"
    echo "MAMP veya veritabanı sunucunuzun çalıştığından emin olun"
fi

echo ""
echo "🔧 SUNUCU BAŞLATMA SEÇENEKLERİ:"
echo "1. Laravel + Vite (tam geliştirme ortamı)"
echo "2. Sadece Laravel sunucusu"
echo "3. Sadece Vite dev server"
echo "4. MAMP başlat"
echo ""

read -p "Seçiminizi yapın [1-4]: " choice

case $choice in
    1)
        print_status "Laravel + Vite başlatılıyor..."
        echo "Laravel sunucusu: http://localhost:8000"
        echo "Vite dev server otomatik başlayacak"
        echo ""
        echo "Çıkmak için her iki terminalde de Ctrl+C kullanın"
        echo ""

        # Paralel olarak çalıştır
        (php artisan serve --port=8000) &
        LARAVEL_PID=$!

        sleep 2
        npm run dev &
        VITE_PID=$!

        # Cleanup function
        cleanup() {
            print_status "Sunucular kapatılıyor..."
            kill $LARAVEL_PID 2>/dev/null
            kill $VITE_PID 2>/dev/null
            exit 0
        }

        trap cleanup SIGINT
        wait
        ;;
    2)
        print_status "Laravel sunucusu başlatılıyor..."
        echo "Sunucu adresi: http://localhost:8000"
        php artisan serve --port=8000
        ;;
    3)
        print_status "Vite dev server başlatılıyor..."
        npm run dev
        ;;
    4)
        print_status "MAMP başlatılıyor..."
        open -a MAMP
        echo "MAMP açıldı. MySQL: 127.0.0.1:8889"
        ;;
    *)
        print_warning "Geçersiz seçim"
        exit 1
        ;;
esac
