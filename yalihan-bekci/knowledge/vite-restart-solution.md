# 🔧 VITE RESTART ÇÖZÜMÜ

**Tarih:** 12 Ekim 2025 16:50

---

## ❌ HATA:

```
Unable to locate file in Vite manifest: resources/js/admin/stable-create.js
```

---

## 🔍 SEBEP:

Vite dev server kapanmış veya restart edilmemiş.

Yeni modüller eklendiğinde Vite server yeniden başlatılmalı:

- ✅ 6 yeni modül eklendi (portals, price, fields, crm, publication, key-manager)
- ✅ 4 modül güncellendi (categories, location, ai, photos)
- ❌ Vite restart edilmedi → Manifest güncel değil

---

## ✅ ÇÖZÜM:

### **1. Eski Process'leri Temizle:**

```bash
ps aux | grep -E "vite|node" | grep -v grep | awk '{print $2}' | xargs kill -9
```

### **2. Vite'ı Yeniden Başlat:**

```bash
cd /Users/macbookpro/Projects/yalihanemlakwarp
npx vite --host 0.0.0.0 --port 5175 &
```

### **3. Doğrula:**

```bash
# Process kontrolü
ps aux | grep vite | grep -v grep

# Port kontrolü
curl -I http://localhost:5175/@vite/client

# Sayfa kontrolü
curl -s http://localhost:8000/stable-create | grep "Vite manifest"
# Sonuç: Hata görünmemeli
```

---

## 📚 YALİHAN BEKÇİ ÖĞRENDİ:

```yaml
Vite Restart Gereken Durumlar: 1. Yeni JS modülü eklenmesi
    2. vite.config.js değişikliği
    3. Tailwind config değişikliği
    4. Build hatası sonrası

Komut:
    Kill: ps aux | grep vite | awk '{print $2}' | xargs kill -9
    Start: npx vite --host 0.0.0.0 --port 5175 &

Kontrol: ps aux | grep vite
    curl http://localhost:5175/@vite/client
```

---

## 🎯 SONUÇ:

```
✅ Vite process temizlendi
✅ Vite dev server başlatıldı (port 5175)
✅ stable-create.js manifest'e eklendi
✅ Sayfa yükleniyor

Background Process: 2 (npm + node)
Port: 5175
Status: ACTIVE
```

---

**Pattern:** Yeni modül → Vite restart → Sayfa yenile
