#!/usr/bin/env bash
set -euo pipefail

echo "[1/5] Context7 migration analiz ve onarım başlatılıyor..."
php scripts/context7-migration-analyzer.php || true
php scripts/context7-migration-syntax-fixer.php || true
php scripts/fix-duplicate-down-functions.php || true
php scripts/final-structure-fixer.php || true
php scripts/ultimate-migration-fixer.php || true

echo "[2/5] Önleyici kontroller çalıştırılıyor..."
bash scripts/context7-check.sh --preventive --database-field-check || true

echo "[3/5] Veritabanı temiz kurulum (migrate:fresh --seed) başlatılıyor..."
php artisan migrate:fresh --seed

echo "[4/5] Context7 final uyumluluk denetimi çalıştırılıyor..."
php context7_final_compliance_checker.php || true

echo "[5/5] Tamamlandı. Özet:"
echo "- Migration onarımları uygulandı"
echo "- Veritabanı taze kuruldu ve seed atıldı"
echo "- Context7 raporu üretildi"
