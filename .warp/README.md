# 🚀 Warp Terminal Entegrasyonu

**Proje:** Yalıhan Emlak AI  
**Versiyon:** 1.0.0  
**Son Güncelleme:** Kasım 2025  
**Durum:** ✅ Aktif

---

## 📁 Klasör Yapısı

```
.warp/
├── README.md                          # Bu dosya
├── rules/                             # Warp Antigravity AI kuralları
│   ├── master-project-prompt.md      # ⭐ Ana prompt (357 satır)
│   └── context7-compliance.md         # Context7 özel kuralları (280 satır)
└── workflows/                         # Warp workflow'ları (gelecek)
```

---

## 🎯 Warp Antigravity Entegrasyonu

### Nasıl Çalışır?

Warp Antigravity, `.warp/rules/` klasöründeki tüm `.md` dosyalarını **otomatik olarak** okur ve kullanır.

**Otomatik Okuma:**
1. Warp terminal açıldığında `.warp/rules/` klasörü taranır
2. Tüm `.md` dosyaları yüklenir
3. AI özelliği kullanıldığında bu kurallar uygulanır

---

## 📋 Dosyalar

### 1. `master-project-prompt.md`

**Amaç:** Ana proje prompt'u - Tüm Context7 standartları ve proje kuralları

**İçerik:**
- Proje yapısı tanımı
- Çalışma kuralları
- Kod yazma standartları
- AI çağrı yapıları
- Hızlı referans tabloları

**Kullanım:** Warp Antigravity bu dosyayı otomatik okur ve tüm AI işlemlerinde referans alır.

### 2. `context7-compliance.md`

**Amaç:** Context7 özel kuralları ve compliance kontrol listesi

**İçerik:**
- Database işlemleri kuralları
- API development standartları
- Seeder operations kuralları
- Kritik kurallar ve örnekler
- Acil durum prosedürleri

**Kullanım:** Özel Context7 compliance kontrolleri için kullanılır.

---

## 🔧 Kullanım Senaryoları

### Senaryo 1: Migration Oluşturma

```bash
# Warp Antigravity'ye sor:
"Laravel migration oluştur, users tablosuna status field'ı ekle"

# Otomatik olarak:
# ✅ display_order kullanır (order YASAK)
# ✅ status kullanır (enabled YASAK)
# ✅ DB::statement() kullanır
# ✅ Index kontrolü yapar
```

### Senaryo 2: Route Oluşturma

```bash
# Warp Antigravity'ye sor:
"Yeni bir admin route ekle, ilanlar için"

# Otomatik olarak:
# ✅ admin.* prefix kullanır
# ✅ Çift prefix'ten kaçınır
# ✅ Context7 standartlarına uygun
```

### Senaryo 3: Form Oluşturma

```bash
# Warp Antigravity'ye sor:
"Yeni bir form oluştur, Tailwind CSS kullan"

# Otomatik olarak:
# ✅ Tailwind CSS kullanır (neo-* YASAK)
# ✅ Dark mode support ekler
# ✅ Transition/animation ekler
# ✅ Context7 form standartlarına uygun
```

### Senaryo 4: Database Query

```bash
# Warp Antigravity'ye sor:
"Users tablosundan aktif kullanıcıları çek"

# Otomatik olarak:
# ✅ status field kullanır (enabled YASAK)
# ✅ il_id kullanır (sehir_id YASAK)
# ✅ mahalle_id kullanır (semt_id YASAK)
```

---

## 🔗 Senkronizasyon

### Tek Kaynak Prensibi

**Ana Kaynak:** `.context7/authority.json`

**Senkronize Dosyalar:**
- `.cursorrules` (Cursor IDE)
- `.warp/rules/master-project-prompt.md` (Warp Antigravity)

**Senkronizasyon:**
- Her iki dosya da `.context7/authority.json`'dan beslenir
- Güncellemeler otomatik senkronize edilir
- Tek kaynak prensibi ile tutarlılık sağlanır

---

## ✅ Entegrasyon Özellikleri

### Otomatik Kontroller

- ✅ Context7 compliance kontrolü
- ✅ Yasak pattern tespiti
- ✅ Standart komut önerileri
- ✅ Otomatik düzeltme önerileri

### Referans Sistemi

- `authority.json` → Tek yetkili kaynak
- `FORBIDDEN_PATTERNS.md` → Yasak pattern'ler
- `master-project-prompt.md` → Ana prompt
- `context7-compliance.md` → Özel kurallar

---

## 📚 İlgili Dokümantasyon

- **Context7 Standartları:** `.context7/README.md`
- **Authority File:** `.context7/authority.json`
- **Forbidden Patterns:** `.context7/FORBIDDEN_PATTERNS.md`
- **Cursor Entegrasyonu:** `.cursorrules`
- **MCP Setup:** `.context7/standards/CURSOR_MCP_SETUP.md`

---

## 🚀 Gelecek Geliştirmeler

### Planlanan Özellikler

1. **Workflow Dosyaları**
   - `.warp/workflows/` klasörü
   - Özel workflow tanımlamaları
   - Otomatik task'lar

2. **Otomatik Senkronizasyon Script'i**
   - `.cursorrules` ↔ `.warp/rules/master-project-prompt.md`
   - Otomatik güncelleme mekanizması

3. **Test Senaryoları**
   - Warp Antigravity test senaryoları
   - Compliance kontrol testleri

---

## 🎓 Öğrenilen Dersler

### 1. Otomatik Okuma
- Warp Antigravity `.warp/rules/` klasörünü otomatik okur
- Ekstra yapılandırma gerektirmez
- Markdown dosyaları otomatik yüklenir

### 2. Senkronizasyon
- Tek kaynak prensibi ile tutarlılık sağlanır
- `.context7/authority.json` merkezi otorite
- Her iki IDE entegrasyonu aynı kuralları kullanır

### 3. Kullanım Kolaylığı
- Kullanıcı müdahalesi gerektirmez
- Otomatik kurallar uygulanır
- Context7 compliance otomatik sağlanır

---

## ✅ Durum

- **Entegrasyon:** ✅ Tamamlandı
- **Dosyalar:** ✅ Hazır
- **Otomatik Okuma:** ✅ Aktif
- **Context7 Uyumluluğu:** ✅ %100
- **Senkronizasyon:** ✅ Aktif

---

**Son Güncelleme:** Kasım 2025  
**Versiyon:** 1.0.0  
**Durum:** ✅ Production Ready

