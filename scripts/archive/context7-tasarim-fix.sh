#!/bin/bash

# Context7 Tasarım Standardizasyonu Script'i
# Bu script, tespit edilen CSS class ve pattern hatalarını otomatik düzeltir

echo "🎨 Context7 Tasarım Standardizasyonu Başlatılıyor..."
echo "=================================================="
echo ""

# Backup dizini oluştur
BACKUP_DIR=".context7/backups/tasarim-fix-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo "📁 Backup oluşturuluyor: $BACKUP_DIR"

# 1. GÖREVLER SAYFASI DÜZELTMELERİ
echo ""
echo "📝 1/3 - Görevler Sayfası Düzeltiliyor..."
GOREV_VIEW="resources/views/admin/takim-yonetimi/gorevler/index.blade.php"

if [ -f "$GOREV_VIEW" ]; then
    # Backup al
    cp "$GOREV_VIEW" "$BACKUP_DIR/gorevler-index.blade.php.backup"

    # CSS class'ları düzelt
    sed -i.tmp 's/class="admin-input"/class="neo-input"/g' "$GOREV_VIEW"
    sed -i.tmp 's/class="admin-table-th"/class="neo-table-th"/g' "$GOREV_VIEW"
    sed -i.tmp 's/class="neo-btn-primary\b/class="neo-btn neo-btn-primary/g' "$GOREV_VIEW"
    sed -i.tmp 's/class="neo-btn-secondary\b/class="neo-btn neo-btn-secondary/g' "$GOREV_VIEW"
    sed -i.tmp 's/class="neo-btn-success\b/class="neo-btn neo-btn-success/g' "$GOREV_VIEW"
    sed -i.tmp 's/class="neo-btn-danger\b/class="neo-btn neo-btn-danger/g' "$GOREV_VIEW"

    # Geçici dosyaları temizle
    rm -f "$GOREV_VIEW.tmp"

    echo "   ✅ Görevler sayfası düzeltildi"
    echo "   📊 Değişiklikler:"
    echo "      - admin-input → neo-input"
    echo "      - admin-table-th → neo-table-th"
    echo "      - neo-btn-{variant} → neo-btn neo-btn-{variant}"
else
    echo "   ⚠️  Dosya bulunamadı: $GOREV_VIEW"
fi

# 2. ADRES YÖNETİMİ DÜZELTMELERİ
echo ""
echo "🏠 2/3 - Adres Yönetimi Düzeltiliyor..."
ADRES_VIEW="resources/views/admin/adres-yonetimi/index.blade.php"

if [ -f "$ADRES_VIEW" ]; then
    # Backup al
    cp "$ADRES_VIEW" "$BACKUP_DIR/adres-yonetimi-index.blade.php.backup"

    # CSS class'ları düzelt
    sed -i.tmp 's/class="neo-btn-primary\b/class="neo-btn neo-btn-primary/g' "$ADRES_VIEW"
    sed -i.tmp 's/class="neo-btn-secondary\b/class="neo-btn neo-btn-secondary/g' "$ADRES_VIEW"
    sed -i.tmp 's/class="sv-form-group"/class="neo-form-group"/g' "$ADRES_VIEW"

    # Geçici dosyaları temizle
    rm -f "$ADRES_VIEW.tmp"

    echo "   ✅ Adres yönetimi düzeltildi"
    echo "   📊 Değişiklikler:"
    echo "      - neo-btn-{variant} → neo-btn neo-btn-{variant}"
    echo "      - sv-form-group → neo-form-group"
else
    echo "   ⚠️  Dosya bulunamadı: $ADRES_VIEW"
fi

# 3. İLAN KATEGORİLERİ DUPLICATE TOAST TEMİZLEME
echo ""
echo "📋 3/3 - İlan Kategorileri Temizleniyor..."
KATEGORI_VIEW="resources/views/admin/ilan-kategorileri/index.blade.php"

if [ -f "$KATEGORI_VIEW" ]; then
    # Backup al
    cp "$KATEGORI_VIEW" "$BACKUP_DIR/ilan-kategorileri-index.blade.php.backup"

    echo "   ⚠️  Duplicate toast messages tespit edildi"
    echo "   📝 Manuel düzeltme önerisi: Satır 415-428 duplicate toast'ı silin"
    echo "   ✅ Backup alındı, manuel düzeltme yapabilirsiniz"
else
    echo "   ⚠️  Dosya bulunamadı: $KATEGORI_VIEW"
fi

echo ""
echo "=================================================="
echo "✅ Context7 Tasarım Standardizasyonu Tamamlandı!"
echo ""
echo "📊 Özet:"
echo "   - Düzeltilen dosya: 2"
echo "   - Backup lokasyonu: $BACKUP_DIR"
echo "   - Manuel düzeltme: 1 (İlan Kategorileri duplicate toast)"
echo ""
echo "🔍 Sonraki Adımlar:"
echo "   1. Değişiklikleri kontrol edin: git diff"
echo "   2. Sayfaları test edin: php artisan serve"
echo "   3. Sorun yoksa commit edin: git add . && git commit -m 'fix: Context7 tasarım standardizasyonu'"
echo ""
echo "📚 Detaylı rapor: .context7/TASARIM_ANALIZ_RAPORU_2025-10-11.md"
