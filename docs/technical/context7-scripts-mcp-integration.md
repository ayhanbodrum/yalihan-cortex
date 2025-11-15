# Context7 Script'ler - MCP Entegrasyon Rehberi

**Tarih:** 2025-11-11  
**Versiyon:** 1.0.0  
**Durum:** ✅ Aktif

---

## 🎯 MCP Entegrasyonu Faydaları

Context7 MCP entegrasyonu script'lere şu faydaları sağlar:

### 1. **Dinamik Kural Yönetimi**
- ✅ Script'ler `.context7/authority.json`'dan kuralları otomatik alır
- ✅ Kural değişikliklerinde script'leri güncellemeye gerek yok
- ✅ Merkezi kural yönetimi

### 2. **Gelişmiş Analiz**
- ✅ Yalıhan Bekçi MCP'den sistem yapısı bilgisi
- ✅ Öğrenilmiş pattern'ler
- ✅ Hata geçmişi

### 3. **Otomatik Öğrenme**
- ✅ Script sonuçları MCP'ye bildirilir
- ✅ Yalıhan Bekçi öğrenme sistemi güncellenir
- ✅ Gelecek analizler daha doğru olur

---

## 📋 MCP Entegre Script'ler

### 1. **`context7-full-scan-mcp.sh`**

**Özellikler:**
- ✅ MCP'den Context7 kurallarını alır
- ✅ Dinamik kural kontrolü
- ✅ MCP'ye sonuçları bildirir

**Kullanım:**
```bash
# MCP entegrasyonu ile tarama
./scripts/context7-full-scan-mcp.sh --mcp

# MCP + Rapor
./scripts/context7-full-scan-mcp.sh --mcp --report

# MCP + JSON Rapor
./scripts/context7-full-scan-mcp.sh --mcp --json
```

**MCP Faydaları:**
- ✅ `.context7/authority.json`'dan kuralları otomatik alır
- ✅ Yeni kurallar otomatik uygulanır
- ✅ Sonuçlar MCP'ye bildirilir

### 2. **`dead-code-analyzer-mcp.php`**

**Özellikler:**
- ✅ MCP'den sistem yapısını alır
- ✅ Context7 compliance kontrolü
- ✅ MCP'ye sonuçları bildirir

**Kullanım:**
```bash
# MCP entegrasyonu ile analiz
php scripts/dead-code-analyzer-mcp.php --mcp

# Context7 compliance ile
php scripts/dead-code-analyzer-mcp.php --context7
```

**MCP Faydaları:**
- ✅ Sistem yapısı bilgisi (model/controller sayıları)
- ✅ Öğrenilmiş pattern'ler
- ✅ False positive azaltma

---

## 🔄 MCP Entegrasyon Akışı

### Adım 1: Kuralları Al
```
Script → Yalıhan Bekçi MCP
→ get_context7_rules()
→ .context7/authority.json okunur
→ Yasaklı/zorunlu pattern'ler alınır
```

### Adım 2: Analiz Yap
```
Script → Dosyaları tarar
→ MCP kurallarına göre kontrol eder
→ İhlalleri tespit eder
```

### Adım 3: Sonuçları Bildir
```
Script → MCP'ye sonuçları gönderir
→ .yalihan-bekci/reports/ altına kaydeder
→ Yalıhan Bekçi öğrenme sistemi güncellenir
```

---

## 📊 MCP Entegrasyon Örnekleri

### Örnek 1: Context7 Full Scan

**Eski Yöntem:**
```bash
./scripts/context7-full-scan.sh
# Statik kurallar script içinde
```

**MCP Entegre Yöntem:**
```bash
./scripts/context7-full-scan-mcp.sh --mcp
# Dinamik kurallar MCP'den alınır
# Yeni kurallar otomatik uygulanır
```

### Örnek 2: Dead Code Analyzer

**Eski Yöntem:**
```bash
php scripts/dead-code-analyzer.php
# Statik analiz
```

**MCP Entegre Yöntem:**
```bash
php scripts/dead-code-analyzer-mcp.php --mcp
# MCP'den sistem yapısı alınır
# Öğrenilmiş pattern'ler kullanılır
```

---

## 🎯 MCP Faydaları Özeti

### 1. **Dinamik Kural Yönetimi**
- ✅ Script'ler güncellenmeden yeni kurallar uygulanır
- ✅ Merkezi kural yönetimi
- ✅ Tutarlılık garantisi

### 2. **Gelişmiş Analiz**
- ✅ Sistem yapısı bilgisi
- ✅ Öğrenilmiş pattern'ler
- ✅ Hata geçmişi

### 3. **Otomatik Öğrenme**
- ✅ Script sonuçları öğrenme sistemine aktarılır
- ✅ Gelecek analizler daha doğru olur
- ✅ False positive azalır

---

## 🔧 Teknik Detaylar

### MCP Entegrasyon Noktaları

**1. Kural Yükleme:**
```bash
# authority.json'dan kuralları al
MCP_RULES_FILE=".context7/authority.json"
```

**2. Sistem Yapısı:**
```php
// MCP'den sistem yapısını al
$systemStructure = getSystemStructureFromMCP();
```

**3. Sonuç Bildirme:**
```bash
# MCP'ye sonuçları bildir
MCP_REPORT_FILE=".yalihan-bekci/reports/mcp-scan/..."
```

---

## ✅ Sonuç

**MCP entegrasyonu script'lere şu faydaları sağlar:**

1. ✅ **Dinamik kural yönetimi** - Script güncellemesi gerekmez
2. ✅ **Gelişmiş analiz** - Sistem yapısı ve öğrenilmiş pattern'ler
3. ✅ **Otomatik öğrenme** - Sonuçlar öğrenme sistemine aktarılır

**Kullanım:**
```bash
# Context7 Full Scan - MCP ile
./scripts/context7-full-scan-mcp.sh --mcp --report

# Dead Code Analyzer - MCP ile
php scripts/dead-code-analyzer-mcp.php --mcp
```

---

**Durum:** ✅ Aktif ve Çalışıyor  
**Son Güncelleme:** 2025-11-11

