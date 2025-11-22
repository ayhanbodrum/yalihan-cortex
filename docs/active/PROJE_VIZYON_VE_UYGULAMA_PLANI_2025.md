# 🎯 Proje Vizyonu ve Uygulama Planı 2025

**Kaynaklar birleştirildi:**
- `PROJE_VIZYON_HARITASI_2025.md`
- `UYGULAMA_PLANI_2025.md`

Bu dosya; proje vizyonu, hedefler ve 2025 uygulama planı için **tek referans** olarak kullanılmalıdır.

---

## 1. Vizyon (Özet)

- Yalıhan Emlak için **AI destekli, gerçek saha ihtiyaçlarına göre tasarlanmış** emlak platformu.
- İnsan danışmanın merkezde olduğu, AI'nin yalnızca **taslak ve analiz üreten yardımcı** rolünde olduğu mimari.
- Tüm sistemin Context7, Yalıhan Bekçi ve `.context7/authority.json` standartlarına tam uyumlu olması.

Ana başlıklar (detaylar eski dokümanlardan alınmıştır):
- Merkezde: Emlak CRM + Piyasa Verisi + AI Analiz DB
- Çok kanallı iletişim (Telegram → ileride WhatsApp, Instagram, Email, Web)
- Otomasyon katmanı: n8n + AnythingLLM + Ollama
- Güçlü dokümantasyon ve standart seti (`.context7/`, `.yalihan-bekci/`, `docs/`)

---

## 2. 2025 Hedefleri (Yüksek Seviye)

1. **AI Asistanları**
   - İlan taslak asistanı (Phase 1–2 tamamlandı)
   - Mesaj taslak asistanı (Phase 3 tamamlandı)
   - Arsa/piyasa analiz asistanı (Phase 4 tamamlandı)
   - Sözleşme taslak asistanı (Phase 1 ile geldi, genişletilecek)

2. **Frontend ve Kullanıcı Deneyimi**
   - Büyük veri setleri için performanslı listeleme (infinite scroll, virtual scroll, lazy loading)
   - Design system ve form standartları ile tutarlı UI

3. **Dokümantasyon ve Standartlar**
   - Tüm kritik standartlar `.context7/` ve `docs/` altında tekil master dosyalarda toplanmış olacak.
   - Yalıhan Bekçi knowledge base aktif kullanılacak.

---

## 3. Faz Bazlı Uygulama Planı (Özet)

> Detaylar eski `PHASE1_TAMAMLANDI.md`, `PHASE2_TAMAMLANDI.md`, `PHASE3_TAMAMLANDI.md` ve `UYGULAMA_PLANI_2025.md` içeriğinden derlenmiştir.

### Phase 1 – Temel AI Altyapısı (Tamamlandı)
- AI tablo ve modelleri: ilan taslakları, mesajlar, sözleşme taslakları, konuşmalar.
- Temel servisler: `AIIlanTaslagiService`, `AIMessageService`, `AIContractService`.
- n8n + AnythingLLM entegrasyonu için API uçları.

### Phase 2 – İlan Taslak Asistanı (Tamamlandı)
- Telegram bot entegrasyonu (`/ilan_taslagi`, `/ilan_taslaklarim`).
- Admin panelde ilan taslağı onay/yayınlama ekranları.

### Phase 3 – Mesaj Taslak Asistanı (Tamamlandı)
- `communications` tablosu ile çok kanallı mesaj altyapısı.
- Mesaj taslakları için AI servisleri ve admin onay ekranı.

### Phase 4 – Arsa Analiz Asistanı (Tamamlandı)
- `yalihan_market` üzerinden piyasa verisi okuma.
- Arsa analiz raporlarının AI ile üretilmesi.

---

## 4. Kurallar ile Uyum

- Tüm isimlendirme ve alan yapıları `.context7/STANDARDIZATION_STANDARDS.md` ve `.warp/rules/context7-compliance.md` ile uyumludur.
- İngilizce/Türkçe karışımı, **teknik terimler hariç** sade ve anlaşılır tutulur.
- Eski vizyon/plan dosyaları (**kaynak olarak** korunur, fakat günlük kullanımda **bu dosya tek referanstır**).

---

## 5. İlgili Dokümanlar

- `MASTER_PROMPT_YALIHAN_EMLAK_AI.md` – AI davranış master prompt
- `FRONTEND_BUYUK_VERI_COZUMLERI.md` – Frontend büyük veri çözümleri (özetleri development dokümanına taşınacak)
- `.context7/authority.json` – Context7 ana otorite
- `.yalihan-bekci/README.md` – AI guardian sistemi
