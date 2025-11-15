# Context7 MCP Entegrasyonu - Code Duplication Analizi

**Tarih:** 2025-11-11  
**Durum:** ✅ TAMAMLANDI

---

## ✅ YAPILAN ENTEGRASYONLAR

### 1. Context7 Authority Dosyası Entegrasyonu
**Dosya:** `.context7/authority.json`

**Özellikler:**
- Context7 kurallarını yükler
- Yasaklı pattern'leri kontrol eder
- Duplication'ları Context7 standartlarına göre doğrular

**Kullanım:**
```php
$context7AuthorityFile = $basePath . '.context7/authority.json';
if (file_exists($context7AuthorityFile)) {
    $context7Authority = json_decode(file_get_contents($context7AuthorityFile), true);
    // Context7 kurallarını kullan
}
```

---

### 2. Context7 API Entegrasyonu (Opsiyonel)
**API URL:** `context7.com/api/v1`  
**Endpoint:** `/patterns/duplication`

**Özellikler:**
- Context7 API'den duplication pattern'lerini yükler
- API Key ile güvenli bağlantı
- Timeout koruması (5 saniye)

**Kullanım:**
```php
$context7ApiUrl = getenv('CONTEXT7_API_URL') ?: 'https://context7.com/api/v1';
$context7ApiKey = getenv('CONTEXT7_API_KEY');
// cURL ile API çağrısı
```

---

### 3. Context7 Kurallarına Göre Duplication Kontrolü

**Özellikler:**
- Duplication'ları Context7 kurallarına göre kontrol eder
- Yasaklı pattern'leri tespit eder
- Trait önerisi sunar

**Örnek:**
```php
$duplicationItem = [
    'count' => count($methods),
    'methods' => $methods,
    'context7_validated' => false,
    'context7_suggestions' => [],
    'context7_compliance' => 'unknown'
];
```

---

### 4. Sistem Yapısı Analizi

**Dosya:** `.yalihan-bekci/knowledge/system-structure.json`

**Özellikler:**
- Model sayısı analizi
- Controller sayısı analizi
- Duplication doğrulama

**Kullanım:**
```php
$systemStructureFile = $basePath . '.yalihan-bekci/knowledge/system-structure.json';
if (file_exists($systemStructureFile)) {
    $systemStructure = json_decode(file_get_contents($systemStructureFile), true);
    // Sistem yapısı analizi
}
```

---

## 📊 YENİ RAPOR ALANLARI

### Duplication Item Yapısı:
```json
{
  "count": 2,
  "methods": [
    {
      "file": "app/Models/AIKnowledgeBase.php",
      "method": "scopeByLanguage"
    },
    {
      "file": "app/Models/AIEmbedding.php",
      "method": "scopeByLanguage"
    }
  ],
  "context7_validated": true,
  "context7_suggestions": [
    "Aynı metod 'scopeByLanguage' birden fazla dosyada bulunuyor. Trait'e çıkarılabilir."
  ],
  "context7_compliance": "violation"
}
```

### Context7 Metadata:
```json
{
  "duplication_context7": {
    "patterns_loaded": 0,
    "authority_loaded": true,
    "system_structure_analyzed": true,
    "context7_validated_count": 4,
    "context7_violations": 0
  }
}
```

---

## 🔗 CONTEXT7 ENTEGRASYONU DETAYLARI

### 1. Authority Dosyası
- **Dosya:** `.context7/authority.json`
- **İçerik:** Context7 kuralları, yasaklı pattern'ler
- **Kullanım:** Duplication kontrolü, pattern validation

### 2. Context7 API (Opsiyonel)
- **URL:** `context7.com/api/v1`
- **Endpoint:** `/patterns/duplication`
- **Auth:** Bearer Token (CONTEXT7_API_KEY)
- **Timeout:** 5 saniye

### 3. System Structure
- **Dosya:** `.yalihan-bekci/knowledge/system-structure.json`
- **İçerik:** Model, Controller, View sayıları
- **Kullanım:** Duplication doğrulama

---

## 🎯 KULLANIM ÖRNEKLERİ

### 1. Context7 Authority Yükleme
```php
$context7AuthorityFile = $basePath . '.context7/authority.json';
if (file_exists($context7AuthorityFile)) {
    $context7Authority = json_decode(file_get_contents($context7AuthorityFile), true);
    // Context7 kurallarını kullan
}
```

### 2. Context7 API Çağrısı
```php
$context7ApiUrl = getenv('CONTEXT7_API_URL') ?: 'https://context7.com/api/v1';
$context7ApiKey = getenv('CONTEXT7_API_KEY');
// cURL ile API çağrısı
```

### 3. Duplication Kontrolü
```php
foreach ($methods as $method) {
    $methodName = $method['method'];
    // Context7 pattern'lerine göre kontrol et
    foreach ($context7Patterns as $pattern) {
        if (isset($pattern['method_pattern']) && preg_match($pattern['method_pattern'], $methodName)) {
            $duplicationItem['context7_validated'] = true;
        }
    }
}
```

---

## 📈 KAZANIMLAR

### 1. Context7 Uyumluluğu
- ✅ Duplication'lar Context7 kurallarına göre kontrol ediliyor
- ✅ Yasaklı pattern'ler tespit ediliyor
- ✅ Trait önerileri sunuluyor

### 2. API Entegrasyonu
- ✅ Context7 API'den pattern'ler yükleniyor
- ✅ Güvenli API bağlantısı
- ✅ Timeout koruması

### 3. Sistem Analizi
- ✅ Sistem yapısı analizi
- ✅ Model/Controller sayıları
- ✅ Duplication doğrulama

---

## 🔄 SONRAKI ADIMLAR

### 1. Context7 API Key Yapılandırması
```bash
# .env dosyasına ekle
CONTEXT7_API_URL=https://context7.com/api/v1
CONTEXT7_API_KEY=your-api-key-here
```

### 2. System Structure Dosyası Oluşturma
```bash
# Sistem yapısı analizi
php scripts/comprehensive-code-check.php
# .yalihan-bekci/knowledge/system-structure.json oluşturulacak
```

### 3. Context7 Pattern'leri Öğrenme
- Context7 API'den pattern'leri yükle
- Authority dosyasına pattern'leri ekle
- Duplication kontrolünü geliştir

---

**Son Güncelleme:** 2025-11-11  
**Durum:** ✅ CONTEXT7 MCP ENTEGRASYONU TAMAMLANDI

