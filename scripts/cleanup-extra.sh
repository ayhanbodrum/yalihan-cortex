#!/bin/bash

# Docs ve .context7 Temizlik Script'i
# Tarih: 30 Kasım 2025

echo "🧹 Ekstra Temizlik Başlıyor..."

# 1. docs/ Temizliği
echo "📂 docs/ temizleniyor..."

# Hatalı klasörü sil
rm -rf docs/active/docs/

# n8n-workflows taşıma
if [ -d "docs/n8n-workflows" ]; then
    mv docs/n8n-workflows/* docs/integrations/ 2>/dev/null
    rmdir docs/n8n-workflows
    echo "✅ n8n-workflows taşındı"
fi

# yalihan-bekci taşıma
if [ -d "docs/yalihan-bekci" ]; then
    mv docs/yalihan-bekci/* .yalihan-bekci/knowledge/ 2>/dev/null
    rmdir docs/yalihan-bekci
    echo "✅ yalihan-bekci docs taşındı"
fi

# prompts taşıma
mkdir -p docs/ai-training/prompts
if [ -d "docs/prompts" ]; then
    mv docs/prompts/* docs/ai-training/prompts/ 2>/dev/null
    rmdir docs/prompts
    echo "✅ prompts taşındı"
fi

# cleanup taşıma
if [ -d "docs/cleanup" ]; then
    mv docs/cleanup/* docs/archive/2025-11/ 2>/dev/null
    rmdir docs/cleanup
    echo "✅ cleanup taşındı"
fi

# 2. .context7/ Temizliği
echo "📂 .context7/ temizleniyor..."

# Standards klasörünü sil (dosyalar zaten ana dizinde var)
rm -rf .context7/standards/
echo "✅ .context7/standards/ silindi (Tekrar önlendi)"

# Eski log ve raporları sil
rm -f .context7/scan-output-*.log
rm -f .context7/ci-report-*.json
rm -f .context7/ACTIVATION_CHECKLIST_*.md
rm -f .context7/PREVENTION_MECHANISMS_*.md
echo "✅ Eski log ve raporlar silindi"

echo "🎉 Ekstra temizlik tamamlandı!"
