# 🔍 Hata Kontrol Rehberi - Telescope & Sentry

**Araçlar:** Telescope (Dev) + Sentry (Production)  
**Tarih:** 31 Ekim 2025

---

## 🎯 **2 TOOL, 2 ORTAM**

```yaml
TELESCOPE (Development - Local):
    Use: Kod yazarken, test ederken
    Dashboard: http://localhost:8000/telescope
    Sees: EVERYTHING (requests, queries, exceptions, logs)

SENTRY (Production - Live):
    Use: Canlı sitede
    Dashboard: https://sentry.io
    Sees: ONLY errors (exceptions, failed requests)
```

---

## 🔍 **TELESCOPE İLE HATA KONTROLÜ (LOCAL)**

### **Yöntem 1: Exceptions Tab**

```yaml
ADIM 1: Telescope'u Aç
  http://localhost:8000/telescope

ADIM 2: Sidebar → "Exceptions" tıkla

ADIM 3: Hataları Gör
  Liste:
    ❌ QueryException (Table already exists) - 1:40m ago
    ❌ ModelNotFoundException (Ilan not found) - 2h ago
    ❌ ValidationException (Form invalid) - 3h ago

ADIM 4: Hataya Tıkla
  Detaylar:
    - Exception type
    - Error message
    - Stack trace (hangi dosya, satır)
    - Request context
    - User
```

**Örnek:**

```
Exception: ModelNotFoundException
Message: "No query results for model [Ilan]"
File: IlanController.php:245
Line: $ilan = Ilan::findOrFail($id);
User: admin@yalihanemlak.com
Request: GET /admin/ilanlar/9999
Time: 2 hours ago

Fix: Add exists check before findOrFail
```

---

### **Yöntem 2: Requests Tab (İşlem Bazlı)**

```yaml
ADIM 1: Telescope → "Requests" tab

ADIM 2: Hatalı Request'i Bul
  Liste:
    ✅ GET /admin/ilanlar → 200 OK (125ms)
    ❌ POST /admin/ilanlar/store → 500 Error (2.3s)
    ✅ GET /admin/kisiler → 200 OK (98ms)

ADIM 3: Kırmızı (500) Olan'a Tıkla
  Detaylar:
    - Request: POST /admin/ilanlar/store
    - Status: 500 Internal Server Error
    - Duration: 2.3s
    - Exception: "Undefined variable $kategori_id"
    - User: admin@yalihanemlak.com

  Tabs:
    ✅ Request → POST data gör
    ✅ Response → Error message
    ✅ Queries → Hangi SQL'ler çalıştı
    ✅ Exception → Stack trace

ADIM 4: Stack Trace'e Bak
  IlanController.php:245
    $ilan->kategori_name = $kategori->name;

  Problem: $kategori null!

ADIM 5: Fix
  if ($kategori) {
      $ilan->kategori_name = $kategori->name;
  }
```

---

### **Yöntem 3: Queries Tab (Yavaş Sayfalar)**

```yaml
ADIM 1: Telescope → "Queries" tab

ADIM 2: Yavaş Query'leri Bul
  Liste:
    ✅ SELECT * FROM users WHERE id = ? (1.2ms) - HIZLI
    ⚠️ SELECT * FROM fotograflar WHERE ... (125ms) - YAVAS
    🚨 SELECT * FROM ilanlar ... (850ms) - ÇOK YAVAŞ!

ADIM 3: Kırmızı/Turuncu Query'e Tıkla
  Detaylar:
    - SQL: Full query
    - Bindings: [123, 'active']
    - Duration: 850ms
    - Location: IlanController.php:50
    - Slow query type: N+1 problem!

ADIM 4: Optimize Et
  Before:
    foreach($ilanlar as $ilan) {
        $ilan->fotograflar; // 100 extra queries!
    }

  After:
    $ilanlar = Ilan::with('fotograflar')->get(); // 1 query!

  Result: 850ms → 45ms! ✅
```

---

### **Yöntem 4: Logs Tab**

```yaml
ADIM 1: Telescope → "Logs" tab

ADIM 2: Log Seviyesine Göre Filtrele
  Filters:
    □ Debug (geliştirme)
    □ Info (bilgi)
    □ Warning (uyarı)
    ✅ Error (hata) ← Sadece bunu seç

ADIM 3: Error Log'larını İncele
  Liste:
    🚨 "AI API rate limit exceeded"
    🚨 "Photo upload failed: Disk full"
    🚨 "Email send failed: Invalid address"

ADIM 4: Tıkla ve İncele
  Log Entry:
    Level: error
    Message: "AI API rate limit exceeded"
    Context: {
      "provider": "OpenAI",
      "endpoint": "/v1/chat/completions",
      "limit": "60 requests/minute"
    }
    Time: 30 minutes ago

  Fix: Cache ekle veya rate limit artır
```

---

## 🚨 **SENTRY İLE HATA KONTROLÜ (PRODUCTION)**

### **Yöntem 1: Email Notification (Otomatik)**

```yaml
ADIM 1: Production'da Hata Oluşur
  User: Form submit → ERROR!

ADIM 2: Sentry Yakalar (0.5 saniye)
  Exception: QueryException
  Message: "Unknown column 'durum'"
  File: IlanController.php:156
  User: user@example.com
  Browser: Chrome 120, Windows 11

ADIM 3: Size Email Gelir (30 saniye)
  📧 From: Sentry <alerts@sentry.io>
  Subject: 🚨 [Production] Unknown column 'durum'
  Body:
    IlanController.php:156
    3 users affected
    [View Issue] [Assign] [Ignore]

ADIM 4: Dashboard'da İncele
  https://sentry.io → Issues

  Detaylar:
    - Full stack trace
    - User context
    - Browser/OS
    - Request data
    - Breadcrumbs (user journey)

ADIM 5: Fix & Resolve
  Code fix yap → Deploy
  Sentry'de "Resolve" tıkla
  Status: ✅ Fixed
```

---

### **Yöntem 2: Sentry Dashboard (Manuel Kontrol)**

```yaml
ADIM 1: Dashboard Aç
  https://sentry.io/organizations/your-org/issues/

ADIM 2: Issues Listesi
  Filtreler:
    - Unresolved (çözülmemiş)
    - Assigned to me
    - High priority

  Sıralama:
    - Most impacted users (en çok etkilenen)
    - Most frequent (en sık olan)
    - Newest

ADIM 3: Issue Seç
  Örnek:
    Title: "Property 'name' on null"
    Events: 15 (15 kez oluştu)
    Users: 8 (8 kullanıcı etkilendi)
    Last seen: 5 minutes ago
    First seen: 2 hours ago

ADIM 4: Issue Detayı
  Tabs:
    ✅ Details → Error message, stack trace
    ✅ Breadcrumbs → User'ın son 10 işlemi
    ✅ Tags → Environment, browser, release
    ✅ Comments → Ekip notları
    ✅ Activity → Kim ne yaptı

ADIM 5: Actions
  [Assign to me] → Sorumlu ben
  [Resolve] → Fix uygulandı
  [Ignore] → False positive
  [Delete] → Gereksiz
```

---

## 📊 **KARŞILAŞTIRMA: TELESCOPE vs SENTRY**

```yaml
Senaryo: Form Validation Hatası

TELESCOPE (Development):
  1. Form submit yap (local'de)
  2. Telescope → Requests
  3. POST /admin/ilanlar/store → 422
  4. Tıkla → Validation errors gör
  5. Fix → Test → Works!

  Time: 2 dakika
  Environment: Local

SENTRY (Production):
  1. User (production'da) form submit
  2. Validation hatası
  3. Sentry email gönderir (30 saniye)
  4. Dashboard'da incele
  5. Local'de reproduce et
  6. Fix → Deploy

  Time: 10 dakika
  Environment: Production

  Value: Kullanıcı şikayet etmeden sen düzelttin!
```

---

## 🎓 **BEST PRACTICES**

### **Development (Local):**

```yaml
Tool: Telescope ✅

Workflow:
  1. Kod yaz
  2. Test et
  3. Hata olursa:
     → Telescope → Exceptions
     → Stack trace gör
     → Fix
  4. Queries kontrol et:
     → Yavaş query var mı?
     → N+1 problem var mı?
  5. Tekrar test
  6. Production'a deploy

Daily Check:
  □ Telescope → Exceptions (sabah 09:00)
  □ Slow queries kontrol (her deploy öncesi)
```

---

### **Production (Live):**

```yaml
Tool: Sentry ✅

Workflow:
  1. Kod deploy
  2. Sentry izliyor (24/7)
  3. Hata olursa:
     → Email anında gelir
     → Dashboard'da incele
     → Local'de reproduce et
     → Fix → Deploy
  4. Sentry'de "Resolve" işaretle

Daily Check:
  □ Sentry dashboard (sabah 09:00)
  □ New issues var mı?
  □ High impact errors?
  □ Fix önceliklendir
```

---

## 🚀 **PRATIK ÖRNEKLER (Emlak Projesi)**

### **Örnek 1: Fotoğraf Upload Hatası**

#### **Development (Telescope):**

```yaml
1. Local'de 10 fotoğraf yükle
2. 2 tanesi hata verdi
3. Telescope → Exceptions:
   ❌ "Invalid image format" (2 kez)
4. Tıkla → Stack trace:
   PhotoController.php:78
   validate(['photo' => 'image|mimes:jpg,png'])
5. Fix: mimes:jpg,png,webp (WebP ekle)
6. Test → Works! ✅
```

#### **Production (Sentry):**

```yaml
1. User fotoğraf yükledi → Hata!
2. Sentry email:
   "Invalid image format (user@example.com)"
3. Dashboard → Issue detayı:
   - Uploaded file: photo.heic (iPhone format!)
   - Error: "Unsupported mime type"
4. Fix: Add heic support
5. Deploy → Resolve
```

---

### **Örnek 2: Database Error**

#### **Development (Telescope):**

```yaml
1. İlan kaydet → 500 Error
2. Telescope → Requests:
   POST /admin/ilanlar/store → 500
3. Tıkla → Queries tab:
   🚨 INSERT INTO ilanlar (durum, ...)
   ❌ Unknown column 'durum'
4. Fix: durum → status (Context7!)
5. Test → Works! ✅
```

#### **Production (Sentry):**

```yaml
1. User ilan kaydetti → Error
2. Sentry email (anında):
   "Unknown column 'durum'"
3. Dashboard:
   - 5 users affected
   - Query: INSERT INTO ilanlar (durum)
   - Context7 violation!
4. Fix: durum → status
5. Deploy → All users happy! ✅
```

---

### **Örnek 3: AI API Hatası**

#### **Development (Telescope):**

```yaml
1. AI içerik üret → Çalışmadı
2. Telescope → Jobs:
   GenerateAIContent → Failed
3. Tıkla → Exception:
   "API rate limit exceeded"
4. Telescope → Logs:
   "OpenAI: 429 Too Many Requests"
5. Fix: Cache ekle (1 saat)
6. Test → Works! ✅
```

#### **Production (Sentry):**

```yaml
1. 10 user AI kullandı → 7'si başarısız
2. Sentry email:
   "GenerateAIContent failed (7 times)"
3. Dashboard:
   - Error: "Rate limit exceeded"
   - Users: 7 affected
   - Time: Last 10 minutes
4. Fix: Implement queueing + cache
5. Deploy → Resolved
```

---

## 📋 **GÜNLÜK KONTROL RUTİNİ**

### **Her Sabah (09:00):**

```bash
# 1. Telescope Check (Local development)
http://localhost:8000/telescope/exceptions
→ Dün'kü exception'lar var mı?
→ Varsa fix planı yap

# 2. Sentry Check (Production)
https://sentry.io
→ Yeni issue var mı?
→ High impact error?
→ Önceliklendir ve fix

# 3. Horizon Check (Queue health)
http://localhost:8000/horizon
→ Failed jobs var mı?
→ Retry et veya fix

# Total: 5 dakika daily check ✅
```

---

### **Her Deploy Öncesi:**

```bash
# 1. Telescope → Queries
→ Yavaş query var mı? (>100ms)
→ N+1 problem var mı?
→ Optimize

# 2. Telescope → Exceptions
→ Son 1 saat exception var mı?
→ Hepsi fix'lendi mi?

# 3. Tests
php artisan test
→ Tüm testler geçiyor mu?

# Deploy ready! ✅
```

---

## 🎯 **HATA TİPLERİNE GÖRE KONTROL**

### **1. Form Validation Errors**

#### **Telescope:**

```
Requests → POST request bul
→ Status: 422
→ Response tab → Validation errors
```

#### **Fix:**

```php
// Controller validation rule'larını düzelt
'kategori_id' => 'required|exists:ilan_kategorileri,id',
```

---

### **2. Database Errors**

#### **Telescope:**

```
Exceptions → QueryException
→ Stack trace → Hangi query
→ Queries tab → Full SQL gör
```

#### **Fix:**

```php
// Table/column name düzelt
// Index ekle (yavaşsa)
// Validation ekle (null check)
```

---

### **3. API Integration Errors**

#### **Telescope:**

```
Logs → Filter by 'error'
→ "API call failed" bulJSON Response
→ Error code (429, 500, etc.)
```

#### **Fix:**

```php
// Rate limiting ekle
// Cache ekle
// Fallback provider
```

---

### **4. Performance Issues (Yavaş Sayfa)**

#### **Telescope:**

```
Requests → Slow request bul (>1s)
→ Queries tab → Kaç query?
→ 100+ query? N+1 problem!
```

#### **Fix:**

```php
// Eager loading
$ilanlar = Ilan::with(['fotograflar', 'kategori', 'il'])->get();

// Before: 150 queries, 3.5s
// After: 4 queries, 0.2s ✅
```

---

## 🔔 **SENTRY NOTIFICATION SETUP**

### **Email Alerts:**

```yaml
Sentry Dashboard → Settings → Alerts

Rules:
  1. All issues → Email (immediate)
  2. High priority → Email + Slack
  3. Low priority → Daily digest

Email'de gelecek:
  - Issue title
  - Users affected
  - First/last seen
  - [View Issue] button
```

---

### **Slack Integration:**

```yaml
Sentry → Integrations → Slack

Setup:
  1. Connect Slack workspace
  2. Select channel: #alerts
  3. Configure rules:
     - New issue → #alerts
     - Resolved → #alerts (optional)

Slack message:
  🚨 New error in Production
  Title: "Property 'name' on null"
  File: IlanController.php:245
  Users: 3 affected
  [View in Sentry]
```

---

## 📊 **HATA PRİORİTELENDİRME**

### **Sentry Dashboard - Sort by Impact:**

```yaml
High Priority (Hemen fix!):
  ❌ 50+ users affected
  ❌ Critical feature broken
  ❌ Data loss risk
  ❌ Security issue

  Example: "Payment processing failed" (15 users)
  → FIX IMMEDIATELY!

Medium Priority (Bugün içinde):
  ⚠️ 10-50 users affected
  ⚠️ Feature degraded
  ⚠️ Workaround exists

  Example: "Search not working" (25 users)
  → Fix today

Low Priority (Bu hafta):
  ℹ️ 1-10 users affected
  ℹ️ Edge case
  ℹ️ Minor UI issue

  Example: "Mobile menu animation" (3 users)
  → Backlog
```

---

## 🛠️ **DEBUGGING WORKFLOW**

### **Complete Workflow:**

```yaml
1. DETECT (Hata Tespit)
   Local: Telescope → Exceptions
   Production: Sentry → Email

2. ANALYZE (Analiz)
   Telescope/Sentry:
     - Stack trace
     - Request context
     - User info
     - Frequency

3. REPRODUCE (Tekrar Et)
   Local:
     - Aynı işlemi yap
     - Telescope'da izle
     - Hatayı gör

4. FIX (Düzelt)
   Code:
     - Validation ekle
     - Null check
     - Try-catch
     - Optimize

5. TEST (Test Et)
   Local:
     - Telescope temiz mi?
     - Exception kalmadı mı?
     - Tests pass?

6. DEPLOY (Yayınla)
   Production:
     - Deploy
     - Sentry izle
     - Hata tekrarladı mı?

7. RESOLVE (Çöz)
   Sentry:
     - Issue → [Resolve]
     - Comment: "Fixed by adding validation"
     - Close!
```

---

## 📈 **WEEKLY/MONTHLY REPORTS**

### **Telescope (Development):**

```yaml
Haftalık Check:
    □ Exception count: Azalıyor mu?
    □ Slow queries: Optimize edildi mi?
    □ Failed jobs: Tekrarlayan pattern var mı?

Monthly Review: □ Top 10 exceptions → Preventive fix
    □ Slowest queries → Index optimization
    □ Most failed jobs → Improve reliability
```

---

### **Sentry (Production):**

```yaml
Haftalık Report:
    - Total issues: 47
    - Resolved: 42 ✅
    - Open: 5 ⚠️
    - Users affected: 156
    - Average resolution time: 12 minutes

Monthly Trends:
    📉 Errors decreasing: ✅ Good!
    📈 Errors increasing: ⚠️ Problem!

    Actions:
        - Identify patterns
        - Preventive measures
        - Code quality improvement
```

---

## 🎯 **QUICK REFERENCE**

### **Hata Kontrolü Cheat Sheet:**

```yaml
Local Development:
    Tool: Telescope
    URL: http://localhost:8000/telescope

    Tabs: Exceptions → Hatalar
        Requests → 500 errors
        Queries → Yavaş query'ler
        Logs → Error logs
        Jobs → Failed jobs

Production:
    Tool: Sentry
    URL: https://sentry.io

    Alerts: Email → Anında (30s)
        Slack → Real-time
        Dashboard → 24/7 monitoring
```

---

### **Hızlı Komutlar:**

```bash
# Telescope temizle (eski kayıtlar)
php artisan telescope:clear

# Sentry test
php artisan sentry:test

# Horizon failed jobs
php artisan horizon:failed

# Queue retry
php artisan queue:retry all

# Logs görüntüle
tail -f storage/logs/laravel.log
```

---

## 🏆 **BAŞARI METRİKLERİ**

### **Before (Monitoring Yok):**

```yaml
Hata Tespit: Kullanıcı şikayet → 1-2 saat sonra
Hata Debug: Log grep → 30-60 dakika
Fix Time: 2-4 saat
Total: 3-6 saat! 😟
```

### **After (Telescope + Sentry):**

```yaml
Hata Tespit: Anında (email 30 saniye)
Hata Debug: Telescope/Sentry → 2-5 dakika
Fix Time: 5-15 dakika
Total: 10-20 dakika! 🚀

Improvement: 18x faster! ✅
```

---

## ✅ **ÖZET**

```yaml
Hata Kontrolü 2 Tool:
    Development:
        Tool: Telescope
        URL: http://localhost:8000/telescope
        Use: Her şeyi izle
        Tabs: Exceptions, Requests, Queries, Logs

    Production:
        Tool: Sentry
        URL: https://sentry.io
        Use: Sadece error'lar
        Alerts: Email, Slack

Daily Routine:
    09:00: Telescope exceptions check
    09:05: Sentry dashboard check
    09:10: Horizon failed jobs check

    Total: 10 dakika/gün ✅
```

---

**Şimdi Telescope'u dene! Requests tab'a git! 🔍**

```
http://localhost:8000/telescope
Sidebar → Requests → İlan sayfasını aç → Request'i gör!
```

Ne görüyorsun? 📊✨
