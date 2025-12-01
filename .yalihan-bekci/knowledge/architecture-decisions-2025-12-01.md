# 🏗️ Mimari Kararlar - Mağaza ve Depo Ayrımı

**Tarih:** 01 Aralık 2025  
**Versiyon:** 2.1.0  
**Context7 Standard:** C7-ARCHITECTURE-2025-12-01

---

## 📋 KARAR: Mağaza ve Depo Ayrımı

### Problem

Yalıhan Emlak OS'un hem CRM (Panel) hem de Vitrin (WWW) ihtiyacı var. İki farklı kullanıcı kitlesi ve farklı performans gereksinimleri mevcut.

### Çözüm

**"Mağaza" ve "Depo" ayrımına dayalı mimari:**

- **Panel (Depo):** Karmaşık, ağır, güvenlik önlemleri yüksek
- **Vitrin (Mağaza):** Hızlı, hafif, SEO uyumlu

### Gerekçe

1. **Ayrıştırılmış Mimari:**
   - Panel ve Vitrin farklı teknolojiler kullanabilir
   - Bakım ve ölçekleme kolay
   - Güvenlik katmanları ayrı yönetilebilir

2. **Performans:**
   - Vitrin sadece "published" ilanları gösterir
   - Caching stratejisi farklı olabilir
   - Database sorguları optimize edilebilir

3. **Güvenlik:**
   - Panel'e sadece admin erişir
   - Vitrin public API kullanır (internal)
   - Docker network üzerinden güvenli iletişim

---

## 🔗 API KÖPRÜSÜ KARARI

### Problem

Vitrin'in Panel'deki verilere erişmesi gerekiyor, ama güvenli bir şekilde.

### Çözüm

**Internal API Bridge:**
- Docker network üzerinden iletişim
- Internal API Key authentication
- IP whitelisting
- Rate limiting

### Gerekçe

1. **Güvenlik:**
   - Internal API key ile authentication
   - IP whitelisting (Docker network)
   - Rate limiting (API abuse önleme)

2. **Performans:**
   - Docker network internal (hızlı)
   - Redis caching (5-10 dakika TTL)
   - Eager loading (N+1 önleme)

3. **Ölçeklenebilirlik:**
   - Horizontal scaling mümkün
   - Load balancer entegrasyonu
   - CDN desteği

---

## ⚡ CACHING STRATEJİSİ KARARI

### Problem

Her istekte database sorgusu yavaş ve maliyetli.

### Çözüm

**Redis Cache with Tags:**
- Cache tags: `frontend-properties`
- TTL: 5-10 dakika
- Event-based invalidation

### Gerekçe

1. **Performans:**
   - %70+ response time azalması
   - Database yükü azalması
   - API maliyeti azalması

2. **Invalidation:**
   - Event-based (IlanUpdated)
   - Cache tags ile selective flush
   - TTL ile otomatik expire

---

## 🔒 GÜVENLİK KATMANLARI KARARI

### Problem

Docker network içinde olsa bile güvenlik önemli.

### Çözüm

**Multi-Layer Security:**
1. Internal API Key
2. IP Whitelisting
3. Rate Limiting
4. CORS Configuration

### Gerekçe

1. **Defense in Depth:**
   - Birden fazla güvenlik katmanı
   - Bir katman başarısız olsa bile diğerleri korur

2. **API Abuse Önleme:**
   - Rate limiting (60 req/min)
   - IP whitelisting
   - Request logging

---

## 🐳 DOCKER NETWORK KARARI

### Problem

Panel ve Vitrin'in güvenli bir şekilde iletişim kurması gerekiyor.

### Çözüm

**Docker Bridge Network:**
- Network: `yalihan-network`
- Driver: `bridge`
- Internal: `false` (external erişim için)

### Gerekçe

1. **Güvenlik:**
   - Internal network (public erişim yok)
   - Container'lar arası güvenli iletişim
   - IP whitelisting ile ekstra koruma

2. **Performans:**
   - Internal network (hızlı)
   - Service discovery (container name)
   - Load balancing desteği

---

## 🌐 CLOUDFLARE TUNNEL KARARI

### Problem

Panel ve Vitrin'in public erişilebilir olması gerekiyor, ama güvenli bir şekilde.

### Çözüm

**Cloudflare Tunnel:**
- Tek tunnel, iki domain
- SSL otomatik
- DDoS koruması
- CDN desteği

### Gerekçe

1. **Güvenlik:**
   - SSL otomatik (Let's Encrypt)
   - DDoS koruması
   - WAF (Web Application Firewall)

2. **Performans:**
   - CDN desteği
   - Global edge network
   - Caching katmanı

3. **Yönetim:**
   - Tek tunnel, iki domain
   - Kolay yapılandırma
   - Monitoring ve analytics

---

## 📊 SONUÇ

### Başarı Metrikleri

- ✅ Mimari ayrıştırıldı (Mağaza + Depo)
- ✅ API köprüsü kuruldu (Internal)
- ✅ Caching stratejisi uygulandı (Redis)
- ✅ Güvenlik katmanları eklendi (Multi-layer)
- ✅ Docker network yapılandırıldı
- ✅ Cloudflare Tunnel entegrasyonu

### Öğrenilen Dersler

1. **Ayrıştırılmış Mimari:**
   - Bakım ve ölçekleme kolay
   - Güvenlik katmanları ayrı yönetilebilir
   - Performans optimizasyonu mümkün

2. **Internal API:**
   - Docker network içinde bile authentication gerekli
   - Rate limiting API abuse'u önler
   - Caching performansı artırır

3. **Cloudflare Tunnel:**
   - Tek tunnel, iki domain yönetimi
   - SSL ve DDoS koruması otomatik
   - CDN desteği performansı artırır

---

**Son Güncelleme:** 01 Aralık 2025

