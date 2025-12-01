# 🚀 Market Intelligence - Hızlı Başlatma Rehberi

**Tarih:** 2025-11-29

---

## ✅ SUNUCU DURUMU

### 1. Laravel Sunucusu

**Durum:** ✅ **ÇALIŞIYOR**

**URL:** `http://127.0.0.1:8000`

**Kontrol:**
```bash
curl http://127.0.0.1:8000
```

---

### 2. Yalıhan Bekçi MCP Sunucusu

**Durum:** ✅ **BAŞLATILDI**

**URL:** `http://localhost:3334`

**Başlatma:**
```bash
cd yalihan-bekci
./bekci.sh start
```

**HTTP Sunucusu (Ayrı):**
```bash
cd yalihan-bekci/server
node index.js
```

**Kontrol:**
```bash
curl http://localhost:3334/
```

---

## 📋 MARKET INTELLIGENCE URL'LERİ

### Web Routes

```
http://127.0.0.1:8000/admin/market-intelligence/dashboard
http://127.0.0.1:8000/admin/market-intelligence/settings
http://127.0.0.1:8000/admin/market-intelligence/compare
http://127.0.0.1:8000/admin/market-intelligence/trends
```

### API Routes

```
GET  http://127.0.0.1:8000/api/market-intelligence/active-regions
POST http://127.0.0.1:8000/api/market-intelligence/settings
DELETE http://127.0.0.1:8000/api/market-intelligence/settings/{id}
PATCH http://127.0.0.1:8000/api/market-intelligence/settings/{id}/toggle
POST http://127.0.0.1:8000/api/admin/market-intelligence/sync
```

---

## 🛡️ YALIHAN BEKÇİ URL'LERİ

```
GET http://localhost:3334/
GET http://localhost:3334/context7/rules
GET http://localhost:3334/system/status
POST http://localhost:3334/run-tests
GET http://localhost:3334/knowledge
GET http://localhost:3334/reports
```

---

## 🧪 TEST

```bash
# Market Intelligence API Test
php tests/manual/test-market-intelligence.php

# Bekçi Durum Kontrol
cd yalihan-bekci
./bekci.sh status
```

---

**Son Güncelleme:** 2025-11-29






