#!/bin/bash

# EmlakPro Dokümantasyon Reorganizasyon Script
# Bu script MD dosyalarını organize eder ve backup alır

set -e

PROJECT_ROOT="/Users/macbookpro/Projects/yalihanemlakwarp"
BACKUP_DIR="$PROJECT_ROOT/archive/docs-backup-$(date +%Y%m%d-%H%M%S)"
NEW_DOCS_DIR="$PROJECT_ROOT/docs"

echo "🚀 EmlakPro Dokümantasyon Reorganizasyonu başlıyor..."

# 1. Backup dizini oluştur
echo "📦 Backup dizini oluşturuluyor: $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"

# 2. Mevcut MD dosyalarının backup'ını al
echo "💾 Mevcut MD dosyaları backup alınıyor..."

# Root level MD dosyalarını backup al
cp "$PROJECT_ROOT"/*.md "$BACKUP_DIR/" 2>/dev/null || echo "Root level MD yok"

# Documents klasörünü backup al
if [ -d "$PROJECT_ROOT/Documents" ]; then
    cp -r "$PROJECT_ROOT/Documents" "$BACKUP_DIR/"
    echo "✅ Documents klasörü backup alındı"
fi

# Prompts klasörünü backup al
if [ -d "$PROJECT_ROOT/prompts" ]; then
    cp -r "$PROJECT_ROOT/prompts" "$BACKUP_DIR/"
    echo "✅ Prompts klasörü backup alındı"
fi

# Templates klasörünü backup al
if [ -d "$PROJECT_ROOT/templates" ]; then
    cp -r "$PROJECT_ROOT/templates" "$BACKUP_DIR/"
    echo "✅ Templates klasörü backup alındı"
fi

# 3. Yeni docs yapısını oluştur
echo "📁 Yeni docs yapısı oluşturuluyor..."
mkdir -p "$NEW_DOCS_DIR"/{modules,technical,ai,admin,api,development,archive}
mkdir -p "$NEW_DOCS_DIR/ai/prompts"

# 4. Dosyaları kategorilere göre taşı

echo "🔄 Modül dokümantasyonları taşınıyor..."
# Modül dosyalarını taşı
if [ -d "$PROJECT_ROOT/Documents" ]; then
    find "$PROJECT_ROOT/Documents" -name "*-modul-*.md" -exec cp {} "$NEW_DOCS_DIR/modules/" \;
fi

echo "🔄 AI dokümantasyonları taşınıyor..."
# AI belgelerini taşı
[ -f "$PROJECT_ROOT/Documents/ai-veri-kurallari.md" ] && cp "$PROJECT_ROOT/Documents/ai-veri-kurallari.md" "$NEW_DOCS_DIR/ai/"
[ -f "$PROJECT_ROOT/Documents/agent-prompts.md" ] && cp "$PROJECT_ROOT/Documents/agent-prompts.md" "$NEW_DOCS_DIR/ai/"
[ -f "$PROJECT_ROOT/Documents/agent-training.md" ] && cp "$PROJECT_ROOT/Documents/agent-training.md" "$NEW_DOCS_DIR/ai/"
[ -f "$PROJECT_ROOT/Documents/copilot-rehberi.md" ] && cp "$PROJECT_ROOT/Documents/copilot-rehberi.md" "$NEW_DOCS_DIR/ai/"

# AI Copilot rehberini taşı
if [ -d "$PROJECT_ROOT/Documents/AI" ]; then
    cp -r "$PROJECT_ROOT/Documents/AI"/* "$NEW_DOCS_DIR/ai/"
fi

echo "🔄 Prompt dosyaları taşınıyor..."
# Prompt dosyalarını taşı
if [ -d "$PROJECT_ROOT/prompts" ]; then
    cp "$PROJECT_ROOT/prompts"/*.md "$NEW_DOCS_DIR/ai/prompts/"
fi

echo "🔄 Teknik dokümantasyon taşınıyor..."
# Teknik belgeleri taşı
if [ -d "$PROJECT_ROOT/Documents/Teknik" ]; then
    cp "$PROJECT_ROOT/Documents/Teknik"/*.md "$NEW_DOCS_DIR/technical/"
fi

echo "🔄 Development dosyaları taşınıyor..."
# Development dökümanlarını taşı
[ -f "$PROJECT_ROOT/Documents/development-phases.md" ] && cp "$PROJECT_ROOT/Documents/development-phases.md" "$NEW_DOCS_DIR/development/"
[ -f "$PROJECT_ROOT/Documents/implementation-plan.md" ] && cp "$PROJECT_ROOT/Documents/implementation-plan.md" "$NEW_DOCS_DIR/development/"
[ -f "$PROJECT_ROOT/Documents/global_rules.md" ] && cp "$PROJECT_ROOT/Documents/global_rules.md" "$NEW_DOCS_DIR/development/"

echo "🔄 API dokümantasyonu taşınıyor..."
# API dokümantasyonlarını taşı
[ -f "$PROJECT_ROOT/Documents/EMLAK_LOC_LIBRARY.md" ] && cp "$PROJECT_ROOT/Documents/EMLAK_LOC_LIBRARY.md" "$NEW_DOCS_DIR/api/"
[ -f "$PROJECT_ROOT/EMLAK_LOC_FINAL_STATUS.md" ] && cp "$PROJECT_ROOT/EMLAK_LOC_FINAL_STATUS.md" "$NEW_DOCS_DIR/api/"
[ -f "$PROJECT_ROOT/GETCURRENTLOCATION_ADDED.md" ] && cp "$PROJECT_ROOT/GETCURRENTLOCATION_ADDED.md" "$NEW_DOCS_DIR/api/"
[ -f "$PROJECT_ROOT/MODERN_ADDRESS_SYSTEM_COMPLETE.md" ] && cp "$PROJECT_ROOT/MODERN_ADDRESS_SYSTEM_COMPLETE.md" "$NEW_DOCS_DIR/api/"

# 5. Ana index dosyası oluştur
echo "📄 Ana index dosyası oluşturuluyor..."
cat > "$NEW_DOCS_DIR/index.md" << 'EOF'
# 📚 EmlakPro Dokümantasyon

Hoş geldiniz! Bu dokümantasyon EmlakPro sisteminin tüm bileşenlerini kapsar.

## 📂 Kategoriler

### 🧩 [Modüller](modules/)
Sistem modüllerinin detaylı dokümantasyonu
- Auth (Kimlik Doğrulama)
- Emlaklar (İlan Yönetimi)
- CRM (Müşteri İlişkileri)
- Dashboard & Raporlar

### 🔧 [Teknik](technical/)
Teknik dokümantasyon ve rehberler
- Migration yönetimi
- CSS standartları
- Database şema

### 🤖 [AI Entegrasyonu](ai/)
Yapay zeka ve Copilot rehberleri
- AI kuralları ve prensipler
- Prompt koleksiyonu
- Copilot entegrasyon rehberi

### 🛠️ [API](api/)
API dokümantasyonu ve entegrasyonlar
- EmlakLoc Address System
- Dış servis entegrasyonları

### 💻 [Geliştirme](development/)
Geliştirme süreçleri ve standartlar
- Kurulum rehberi
- Kod standartları
- İş akış prosedürleri

---

**Son Güncelleme:** $(date +"%d %B %Y")
**Versiyon:** 2.0
EOF

echo "📋 README dosyasını güncelle..."
# Ana README'yi güncelle
cat > "$NEW_DOCS_DIR/README.md" << 'EOF'
# EmlakPro Dokümantasyon Sistemi

Bu klasör EmlakPro projesinin tüm dokümantasyonunu kategorilere ayrılmış şekilde içerir.

## Hızlı Erişim
- 📖 [Ana Döküman İndeksi](index.md)
- 🧩 [Modül Dokümantasyonları](modules/)
- 🤖 [AI & Copilot Rehberleri](ai/)
- 🔧 [Teknik Dokümantasyon](technical/)

## Reorganizasyon
Bu dokümantasyon sistemi 13 Haziran 2025 tarihinde yeniden organize edilmiştir.
Eski dosyaların backup'ı `archive/docs-backup-*` klasörlerinde bulunabilir.
EOF

echo "✅ Reorganizasyon tamamlandı!"
echo ""
echo "📍 Yeni dokümantasyon lokasyonu: $NEW_DOCS_DIR"
echo "📦 Backup lokasyonu: $BACKUP_DIR"
echo ""
echo "🔗 Sonraki adımlar:"
echo "   1. docs/index.md dosyasını inceleyin"
echo "   2. Eski lokasyonlardaki dosyaları kaldırmayı düşünün"
echo "   3. Git'te değişiklikleri commit edin"
