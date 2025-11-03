#!/bin/bash
# Bu betik MAMP MySQL veritabanının yedeğini alır

# Yedek dosyası için tarih formatı oluştur
TARIH=$(date +%Y%m%d_%H%M%S)
YEDEK_DIZIN="/Users/macbookpro/Projects/Cursor Emlak Pro/backups"
YEDEK_DOSYA="$YEDEK_DIZIN/emlakpro_backup_$TARIH.sql"

# Yedek dizinini oluştur (yoksa)
mkdir -p $YEDEK_DIZIN

echo "MySQL veritabanı yedekleniyor..."
/Applications/MAMP/Library/bin/mysql80/bin/mysqldump --user=root --password=root --host=127.0.0.1 --port=8889 emlakpro > $YEDEK_DOSYA

# Sonucu kontrol et
if [ $? -eq 0 ]; then
    echo "Veritabanı yedeği başarıyla oluşturuldu: $YEDEK_DOSYA"
    echo "Yedek dosyası boyutu: $(du -h $YEDEK_DOSYA | cut -f1)"
else
    echo "Hata: Veritabanı yedeği alınamadı!"
    exit 1
fi

# Eski yedekleri listele
echo "Mevcut yedekler:"
ls -lh $YEDEK_DIZIN
