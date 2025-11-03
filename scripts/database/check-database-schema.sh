#!/bin/bash

# Veritabanı şema kontrol scripti
SCHEMA_FILE="database-schema.md"
BACKUP_DIR="schema-backups"
LOG_FILE="schema-check.log"
PROJECT_DIR="/Users/macbookpro/Projects/Cursor Emlak Pro"

# Proje dizinine git
cd "$PROJECT_DIR" || exit 1

# Backup dizinini oluştur
mkdir -p "$BACKUP_DIR"

# Log başlangıcı
echo "[$(date)] Şema kontrolü başlatılıyor..." >> "$LOG_FILE"

if [ -f "$SCHEMA_FILE" ]; then
    # Mevcut şemayı yedekle
    cp "$SCHEMA_FILE" "$BACKUP_DIR/schema-$(date +%Y%m%d-%H%M%S).md"

    # Şema uyumluluğunu kontrol et
    php database-schema-dump.php --check "$SCHEMA_FILE" >> "$LOG_FILE" 2>&1

    if [ $? -ne 0 ]; then
        echo "[$(date)] ⚠️  Şema değişiklikleri tespit edildi!" >> "$LOG_FILE"

        # Yeni şemayı oluştur
        php database-schema-dump.php --markdown --output "database-schema-new.md" >> "$LOG_FILE" 2>&1

        echo "Şema değişiklikleri tespit edildi!"
        echo "Yeni şema: database-schema-new.md"
        echo "Eski şema yedeği: $BACKUP_DIR/schema-$(date +%Y%m%d-%H%M%S).md"

        # Git'e commit (opsiyonel)
        if command -v git &> /dev/null && [ -d ".git" ]; then
            git add database-schema-new.md >> "$LOG_FILE" 2>&1
            git commit -m "Database schema updated - $(date)" >> "$LOG_FILE" 2>&1
        fi
    else
        echo "[$(date)] ✅ Şema uyumlu" >> "$LOG_FILE"
        echo "✅ Şema uyumlu - değişiklik yok"
    fi
else
    echo "[$(date)] İlk şema dosyası oluşturuluyor..." >> "$LOG_FILE"
    php database-schema-dump.php --markdown --output "$SCHEMA_FILE" >> "$LOG_FILE" 2>&1

    if [ $? -eq 0 ]; then
        echo "İlk şema dosyası oluşturuldu: $SCHEMA_FILE"
        echo "[$(date)] İlk şema dosyası oluşturuldu" >> "$LOG_FILE"
    else
        echo "Şema dosyası oluşturulurken hata oluştu!"
        echo "[$(date)] Şema dosyası oluşturma hatası" >> "$LOG_FILE"
    fi
fi

echo "[$(date)] Şema kontrolü tamamlandı" >> "$LOG_FILE"
