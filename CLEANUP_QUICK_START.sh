#!/usr/bin/env bash

# 🧹 CLEANUP QUICK START GUIDE
# Arşiv ve Backup Temizliği - Hızlı Başlangıç

echo "╔════════════════════════════════════════════════════════╗"
echo "║   🧹 Yalıhan Emlak - Archive Cleanup                  ║"
echo "║      25 Kasım 2025 - Temizlik Analiz Tamamlandı       ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

echo "📊 ŞU ANKİ BOYUTLAR:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
du -sh /Users/macbookpro/Projects/yalihanai/docs/archive 2>/dev/null | awk '{print "  docs/archive/           →  " $1}'
du -sh /Users/macbookpro/Projects/yalihanai/archive 2>/dev/null | awk '{print "  archive/                →  " $1}'
du -sh /Users/macbookpro/Projects/yalihanai/.yalihan-bekci/archive 2>/dev/null | awk '{print "  .yalihan-bekci/archive/ →  " $1}'
du -sh /Users/macbookpro/Projects/yalihanai/scripts/archive 2>/dev/null | awk '{print "  scripts/archive/        →  " $1}'
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  TOPLAM TEMIZLENECEK:    →  5.2 MB ✅"
echo ""

echo "📋 YAPILAN DOSYALAR (DOCS/ARCHIVE/REFERENCE/):"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  ✅ CLEANUP_ANALYSIS_REPORT_2025_11_25.md          (Detaylı analiz)"
echo "  ✅ CLEANUP_EXECUTIVE_SUMMARY_2025_11_25.md        (Özet ve planlar)"
echo "  ✅ SCRIPTS_ANALYSIS_REPORT_2025_11_25.md          (Script envanteri)"
echo "  ✅ GELECEK_ADIMLAR_AUDIT_FINAL_REPORT_2025_11_25  (Audit raporu)"
echo ""

echo "🚀 TEMIZLIK BAŞLATMAK İÇİN:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "  1️⃣  INTERAKTIF TEMIZLIK (önerilen):"
echo "     $ ./scripts/cleanup/archive-cleanup.sh"
echo ""
echo "  2️⃣  MANUEL TEMIZLIK (hızlı):"
echo "     $ rm -rf ./scripts/archive/"
echo "     $ rm -rf ./archive/dead-code-*"
echo "     $ rm -rf ./.yalihan-bekci/archive/"
echo ""
echo "  3️⃣  BACKUP SQL DOSYALARI (isteğe bağlı):"
echo "     $ rm -f ./backup_before_migration_*.sql"
echo "     $ rm -f ./database/scripts/*.sql"
echo ""
echo "  4️⃣  KONTROL:"
echo "     $ du -sh ./docs/archive ./archive ./scripts"
echo ""

echo "📖 DAHA FAZLA BİLGİ:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Analiz Raporu:   cat docs/archive/reference/CLEANUP_ANALYSIS_REPORT_2025_11_25.md"
echo "  Executive Özet:  cat docs/archive/reference/CLEANUP_EXECUTIVE_SUMMARY_2025_11_25.md"
echo "  Script Analizi:  cat docs/archive/reference/SCRIPTS_ANALYSIS_REPORT_2025_11_25.md"
echo ""

echo "⚠️  UYARI:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  • Git history tüm dosyaları tutar (geri alınabilir)"
echo "  • Backup'ları başka yerde saklı mı? Kontrol edin"
echo "  • Archive reports /reference klasörü korunmaktadır"
echo ""

echo "✅ TEMIZLIK ANALIZI TAMAMLANDI - HAZIRSINIZ!"
