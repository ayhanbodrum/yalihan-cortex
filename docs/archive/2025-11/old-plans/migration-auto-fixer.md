# Migration Syntax Auto-Fixer

## Kullanım

Migration dosyalarınızdaki syntax hatalarını otomatik olarak düzeltmek için:

### Yöntem 1: Bash Script

```bash
./scripts/fix-migrations.sh
```

### Yöntem 2: PHP Script (doğrudan)

```bash
php scripts/migration-syntax-auto-fixer.php
```

### Yöntem 3: VS Code Task

- VS Code'da `Ctrl+Shift+P` (Windows/Linux) veya `Cmd+Shift+P` (Mac)
- "Tasks: Run Task" yazın
- "Migration: Auto Fix Syntax" seçin

## Ne Yapar?

1. **Fazla kapama parantezlerini kaldırır** (`}` fazlalıkları)
2. **Yanlış semicolon kullanımlarını düzeltir** (fonksiyon sonunda `;` yerine `}`)
3. **Eksik fonksiyon kapatmalarını düzeltir** (down() fonksiyonu eksikse ekler)
4. **Bozuk class yapılarını düzeltir** (başta fazla parantezler)
5. **PHPDoc blokları kaldırır** (Context7 uyumu için)
6. **Fazla boşlukları temizler**

## Sonrası

Script çalıştıktan sonra otomatik olarak:

- Context7 uyumluluk kontrolü yapar (`php artisan context7:check`)
- Migration syntax kontrolü yapar (`php artisan migrate --pretend`)

## Örnek Çıktı

```
🚀 Migration Syntax Auto-Fixer başlatılıyor...
🔍 Kontrol ediliyor: 2025_06_05_150001_create_blog_tags_table.php
✅ Düzeltildi: 2025_06_05_150001_create_blog_tags_table.php
✨ Zaten temiz: 2025_06_05_150002_create_blog_posts_table.php

📊 Özet Rapor:
✅ Düzeltilen dosyalar: 1
❌ Hata alan dosyalar: 0

🎉 Migration syntax auto-fixer tamamlandı!
```

## Güvenlik

- Orijinal dosyalar üzerine yazılır (backup önerilir)
- Sadece syntax hatalarını düzeltir, mantık değişikliği yapmaz
- Dry-run özelliği için PHP script'i değiştirilebilir
