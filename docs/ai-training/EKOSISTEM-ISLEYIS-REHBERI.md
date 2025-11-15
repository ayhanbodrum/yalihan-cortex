# 🧠 EmlakPro AI Ekosistemi İşleyiş Rehberi

## 1. Büyük Resim: Ekosistem Mimarisi

- **Amaç:** Hataları otomatik tespit, öğrenme ve tekrarını önleme; AI ile sürekli gelişen, kendi kendini iyileştiren bir yazılım sistemi kurmak.
- **Bileşenler:**
    - **VS Code/Cursor**: Kullanıcı arayüzü, AI entegrasyonu
    - **MCP Server'lar**: Her biri farklı işlev (Context7, Memory, Laravel, Puppeteer, Filesystem, Git, Ollama...)
    - **AnythingLLM**: Bilgi tabanı, embedding, prompt yönetimi
    - **Dokümantasyon**: AI ve geliştirici için referans

## 2. Veri Akışı ve Otomasyon

1. **Kullanıcı bir işlem başlatır** (ör: kod yazar, hata alır, soru sorar)
2. **AI (Copilot/Cursor) uygun MCP tool'unu seçer**
3. **MCP server ilgili işlemi yapar** (ör: kodu kontrol eder, migration çalıştırır, test yapar)
4. **Sonuç AI'ya döner, kullanıcıya gösterilir**
5. **Hata veya yeni pattern tespit edilirse**: "eko sisteme öğret" komutu ile hafızaya kaydedilir
6. **AnythingLLM** embedding ve bilgi tabanını günceller

## 3. Otomatik Hata Öğrenme ve Önleme

- Her hata, çözümüyle birlikte kaydedilir (örn: `.cursor/memory/context7-memory.md`)
- Aynı hata tekrar yaşanırsa AI otomatik uyarı ve çözüm önerir
- Pattern tabanlı öğrenme: Sık karşılaşılan hatalar otomatik kategorize edilir
- Geliştirici isterse "bu hatayı eko sisteme öğret" diyerek yeni pattern ekleyebilir

## 4. AnythingLLM ile Bilgi Akışı

- Tüm dokümantasyon, embedding ve promptlar AnythingLLM ile senkronize edilir
- AI, dokümanlardan ve geçmiş hatalardan öğrenir
- Bilgi tabanı güncel tutulur, yeni öğrenilenler embedding'e eklenir

## 5. Self-Healing (Kendi Kendini Onarma)

- Sistem bir hata tespit ettiğinde otomatik düzeltme önerir
- Geliştirici onaylarsa fix otomatik uygulanır
- Fix ve pattern hafızaya kaydedilir

## 6. Sıkça Sorulanlar

- **Yeni bir hata nasıl öğretilir?**
    - "eko sisteme öğret" veya "bu hatayı hafızaya kaydet" komutu ile
- **AI neden bazı hataları otomatik çözüyor?**
    - Daha önce öğrenilmiş ve çözümü hafızada olduğu için
- **AnythingLLM ne işe yarar?**
    - Bilgi tabanını, embedding'i ve promptları yönetir; AI'nın daha akıllı olmasını sağlar

## 7. Geliştirici İçin İpuçları

- Hata gördüğünüzde "eko sisteme öğret" demekten çekinmeyin
- Dokümantasyonu güncel tutun, AnythingLLM ile embedding'i sık sık yenileyin
- MCP server'ları gereksiz yere çoğaltmayın, sadece ihtiyacınız olanları aktif tutun
- Hafıza dosyalarını (memory/context7-memory.md gibi) düzenli yedekleyin

---

**Bu rehber, EmlakPro AI ekosisteminin sürdürülebilir, hatasız ve sürekli gelişen bir yapıda kalmasını sağlar.**
