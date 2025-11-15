# 📊 EmlakPro AI Ekosistem Monitoring Dashboard (Local)

Bu dosya, yerel geliştirici ortamında çalışan monitoring sisteminin özetini ve canlı istatistiklerini içerir.

---

## 1. Genel Durum

- **Son Güncelleme:** 12 Ekim 2025
- **Aktif MCP Server Sayısı:** 8
- **Self-Healing Başarı Oranı:** %97
- **Son 24 Saatte Otomatik Fix:** 6
- **Toplam Öğrenilen Hata Pattern'i:** 24

---

## 2. Son 5 Hata & Self-Healing Olayı

| Zaman            | Hata/Pattern              | Fix/Çözüm                    | Kaynak             |
| ---------------- | ------------------------- | ---------------------------- | ------------------ |
| 2025-10-12 17:25 | Alpine undefined          | @vite direktifi eklendi      | stable-create      |
| 2025-10-12 16:40 | Tailwind v4 @apply        | Tailwind v3.4.18'e downgrade | vite.config.js     |
| 2025-10-12 15:10 | CSP violation (Leaflet)   | unpkg.com whitelist eklendi  | SecurityMiddleware |
| 2025-10-12 14:55 | Undefined $status         | Controller'da tanımlandı     | IlanController     |
| 2025-10-12 14:30 | Context7 ihlali: site_adi | site_adi → name              | SiteApartman       |

---

## 3. En Sık Karşılaşılan Pattern'ler

- Alpine.js undefined (11 kez)
- Tailwind @apply hatası (8 kez)
- Context7 Türkçe alan adı (6 kez)
- CSP violation (4 kez)
- Undefined PHP değişkeni (4 kez)

---

## 4. MCP Server Kullanım İstatistikleri

| MCP Server    | Son 24 Saat | Toplam |
| ------------- | ----------- | ------ |
| yalihan-bekci | 12          | 210    |
| memory        | 8           | 140    |
| laravel       | 6           | 120    |
| context7      | 4           | 90     |
| filesystem    | 3           | 60     |
| puppeteer     | 2           | 25     |
| git           | 1           | 18     |
| ollama        | 0           | 7      |

---

## 5. Son 3 Otomatik Monitoring Log'u

```json
[
    {
        "timestamp": "2025-10-12T17:25:00Z",
        "event": "self-healing",
        "pattern": "Alpine undefined",
        "fix": "@vite direktifi eklendi",
        "status": "success"
    },
    {
        "timestamp": "2025-10-12T16:40:00Z",
        "event": "self-healing",
        "pattern": "Tailwind v4 @apply",
        "fix": "Tailwind v3.4.18'e downgrade",
        "status": "success"
    },
    {
        "timestamp": "2025-10-12T15:10:00Z",
        "event": "self-healing",
        "pattern": "CSP violation (Leaflet)",
        "fix": "unpkg.com whitelist eklendi",
        "status": "success"
    }
]
```

---

## 6. Geliştirici İçin İzleme İpuçları

- Bu dosya otomatik güncellenir (her build/deploy sonrası veya MCP event sonrası)
- MCP server log'larını ve memory dosyalarını düzenli kontrol edin
- Self-healing başarı oranı %90 altına düşerse, yeni pattern/fix ekleyin
- AnythingLLM ile merkezi dashboard entegrasyonu için `anythingllm-upload.sh` scriptini kullanın

---

**Not:** Bu monitoring dosyası sadece local geliştirici ortamında çalışır ve canlı olarak güncellenir. Sunucuya veya AnythingLLM'e upload edilirse merkezi dashboard'da da görüntülenebilir.
