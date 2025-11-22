# 📖 Warp Antigravity Kullanım Örnekleri

**Proje:** Yalıhan Emlak AI  
**Versiyon:** 1.0.0  
**Son Güncelleme:** Kasım 2025

---

## 🎯 Bu Dosya Nedir?

Bu dosya, Warp Antigravity AI ile çalışırken kullanabileceğiniz **pratik örnekler** içerir. Her örnek Context7 standartlarına uygun şekilde hazırlanmıştır.

---

## 📋 Kullanım Senaryoları

### 1. Laravel Migration Oluşturma

#### ❌ YANLIŞ Örnek:
```bash
# Warp Antigravity'ye sor:
"create migration add_order_to_categories"
```

**Sorun:** `order` field'ı Context7'de YASAK (display_order kullanılmalı)

#### ✅ DOĞRU Örnek:
```bash
# Warp Antigravity'ye sor:
"create migration add_display_order_to_categories"
```

**Sonuç:** Otomatik olarak Context7 uyumlu migration oluşturur:
- ✅ `display_order` field'ı kullanır
- ✅ `DB::statement()` kullanır
- ✅ Index kontrolü yapar
- ✅ `status` field'ı ekler (eğer yoksa)

---

### 2. Database Query Oluşturma

#### ❌ YANLIŞ Örnek:
```bash
# Warp Antigravity'ye sor:
"aktif kullanıcıları çek, sehir_id ile filtrele"
```

**Sorun:** `aktif` ve `sehir_id` Context7'de YASAK

#### ✅ DOĞRU Örnek:
```bash
# Warp Antigravity'ye sor:
"status='active' olan kullanıcıları çek, il_id ile filtrele"
```

**Sonuç:** Otomatik olarak Context7 uyumlu query oluşturur:
- ✅ `status` field'ı kullanır
- ✅ `il_id` field'ı kullanır
- ✅ Optimize edilmiş query

---

### 3. Route Oluşturma

#### ❌ YANLIŞ Örnek:
```bash
# Warp Antigravity'ye sor:
"admin.crm.ilanlar route'u oluştur"
```

**Sorun:** Çift prefix (`admin.crm.*`) Context7'de YASAK

#### ✅ DOĞRU Örnek:
```bash
# Warp Antigravity'ye sor:
"admin.ilanlar route'u oluştur"
```

**Sonuç:** Otomatik olarak Context7 uyumlu route oluşturur:
- ✅ `admin.*` prefix kullanır
- ✅ Tek prefix (çift prefix YASAK)
- ✅ Context7 route naming standardına uygun

---

### 4. Form Oluşturma

#### ❌ YANLIŞ Örnek:
```bash
# Warp Antigravity'ye sor:
"neo-btn kullanarak form oluştur"
```

**Sorun:** `neo-*` class'ları Context7'de YASAK

#### ✅ DOĞRU Örnek:
```bash
# Warp Antigravity'ye sor:
"Tailwind CSS kullanarak form oluştur, dark mode desteği ekle"
```

**Sonuç:** Otomatik olarak Context7 uyumlu form oluşturur:
- ✅ Pure Tailwind CSS kullanır
- ✅ Dark mode support ekler
- ✅ Transition/animation ekler
- ✅ Context7 form standartlarına uygun

---

### 5. Model Oluşturma

#### ❌ YANLIŞ Örnek:
```bash
# Warp Antigravity'ye sor:
"User model'ine enabled field'ı ekle"
```

**Sorun:** `enabled` field'ı Context7'de YASAK

#### ✅ DOĞRU Örnek:
```bash
# Warp Antigravity'ye sor:
"User model'ine status field'ı ekle"
```

**Sonuç:** Otomatik olarak Context7 uyumlu model oluşturur:
- ✅ `status` field'ı kullanır
- ✅ Enum değerleri ekler
- ✅ Context7 field naming standardına uygun

---

## 🔧 Gelişmiş Senaryolar

### Senaryo 1: Migration + Seeder

```bash
# Warp Antigravity'ye sor:
"create migration add_status_to_users ve seeder oluştur"
```

**Sonuç:**
- ✅ Migration: `status` field'ı ekler
- ✅ Seeder: Context7 uyumlu veri ekler
- ✅ Enum değerleri: active, inactive, pending

### Senaryo 2: Controller + Route + View

```bash
# Warp Antigravity'ye sor:
"ilanlar için controller, route ve view oluştur"
```

**Sonuç:**
- ✅ Controller: Context7 standartlarına uygun
- ✅ Route: `admin.ilanlar.*` prefix
- ✅ View: Tailwind CSS, dark mode, transitions

### Senaryo 3: API Endpoint

```bash
# Warp Antigravity'ye sor:
"API endpoint oluştur, ilanlar listesi için"
```

**Sonuç:**
- ✅ Response format: Context7 standardına uygun
- ✅ Query optimization: N+1 query önleme
- ✅ Error handling: Standart error response

---

## 📚 İpuçları

### 1. Açık İstekler Yapın

**❌ Belirsiz:**
```bash
"migration oluştur"
```

**✅ Açık:**
```bash
"create migration add_display_order_to_categories table"
```

### 2. Context7 Standartlarını Belirtin

**✅ İyi:**
```bash
"Context7 standartlarına uygun migration oluştur"
```

### 3. Özel Gereksinimleri Belirtin

**✅ İyi:**
```bash
"dark mode desteği olan form oluştur, Tailwind CSS kullan"
```

---

## 🚨 Yaygın Hatalar

### Hata 1: Eski Field İsimleri

**Sorun:** `enabled`, `aktif`, `order`, `sehir_id` kullanımı

**Çözüm:** Warp Antigravity otomatik olarak uyarır ve doğru field'ları önerir

### Hata 2: Neo Design System

**Sorun:** `neo-*` class'ları kullanımı

**Çözüm:** Warp Antigravity otomatik olarak Tailwind CSS'e çevirir

### Hata 3: Çift Prefix

**Sorun:** `admin.crm.*` gibi çift prefix kullanımı

**Çözüm:** Warp Antigravity otomatik olarak tek prefix'e çevirir

---

## ✅ Başarı Kriterleri

### Doğru Kullanım İçin:

1. ✅ Context7 standartlarını belirtin
2. ✅ Açık ve net istekler yapın
3. ✅ Özel gereksinimleri belirtin
4. ✅ Warp Antigravity'nin önerilerini dinleyin

---

**Son Güncelleme:** Kasım 2025  
**Versiyon:** 1.0.0  
**Durum:** ✅ Aktif

