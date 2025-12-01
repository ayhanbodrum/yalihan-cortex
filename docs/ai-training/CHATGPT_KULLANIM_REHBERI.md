# ChatGPT / LLM Kullanım Rehberi – Yalihan Emlak Projesi

Bu dosya, bu repo içinde çalışan veya bu repoyu inceleyen **ChatGPT / LLM ajanlarına** yöneliktir. Amaç, senin (ajan olarak) **daha isabetli, tutarlı ve güvenli** çıktılar üretmeni sağlamak.

## 1. Genel Çalışma Prensibi

1. İş isteğini anlamak için önce ilgili **dokümantasyonu**, sonra **kodu** oku.
2. Her zaman şu sırayı düşün:
   - İş kuralı / niyet → `docs/*`
   - HTTP girişi → `routes/*`
   - İş mantığı → `app/Http/Controllers`, `app/Services`, `app/Modules`
   - Veritabanı → `app/Models`, `database/migrations`
3. AI ile ilgili konularda ek olarak `ai/` ve `docs/ai*`, `docs/ai-training*` klasörlerini incele.

## 2. AI ile İlgili Görevlerde İzlenecek Yol

AI/ChatGPT özellikleri ile ilgili bir görev geldiğinde (örneğin yeni bir AI endpoint eklemek, prompt iyileştirmek, hata ayıklamak), şu sırayı takip et:

1. **Endpoint'i bul:**
   - `routes/ai.php` ve gerekiyorsa `routes/ai-advanced.php`, `routes/admin-ai.php.ARCHIVED` dosyalarını tara.
   - İstenen URL veya route name (`ai.chat`, `ai.predict-price` vb.) üzerinden ilgili satırı bul.
2. **Controller'ı aç:**
   - Genellikle `App\Http\Controllers\Api\AIController` içindeki metotlara bak.
   - Parametreleri, validation mantığını ve hangi servisleri kullandığını analiz et.
3. **Service ve domain mantığını incele:**
   - `app/Services` ve gerekiyorsa `app/Modules` altındaki ilgili sınıfları bul.
   - AI sağlayıcısına nasıl istek gittiğini, prompt'un nasıl hazırlandığını ve response'un nasıl işlendiğini takip et.
4. **Prompt ve kuralları oku:**
   - `ai/prompts/` içindeki dosyaları ve
   - `docs/ai/COPILOT_PROMPTS_GUIDE.md`, `docs/ai/AI_KULLANIM_ORNEKLERI.md` dokümanlarını tara.
5. **Eğitim dokümanlarını referans al:**
   - `docs/ai-training/00-BASLA-BURADAN.md`
   - `docs/ai-training/AI-TRAINING-SUMMARY.md`
   - `docs/ai-training/05-USE-CASES-AND-SCENARIOS.md`

Bu akış sayesinde, sadece kodu değil, **kullanım senaryolarını ve tasarım niyetini** de anlamış olursun.

## 3. Hazır Görev Şablonları (Prompt Taslakları)

Aşağıdaki şablonlar, kullanıcı seni bu projede kullanırken rahatlıkla uyarlayabileceği örneklerdir.

### 3.1. AI Endpoint Akışını Analiz Etme

"`routes/ai.php` içindeki `{ENDPOINT}` endpoint'inin tam akışını çıkar. Hangi controller metoduna gidiyor, hangi service'leri çağırıyor, hangi prompt dosyalarını kullanıyor ve response nasıl oluşturuluyor? Adım adım açıkla."

### 3.2. Yeni AI Özelliği Ekleme

"Yalihan Emlak projesine yeni bir AI özelliği eklemek istiyorum. Örneğin `/ai/recommend-projects` adında bir endpoint olsun ve kullanıcının tercihlerini analiz ederek uygun projeleri önersin. 

Lütfen:
- Uygun route tanımını (`routes/ai.php`) yaz,
- `AIController` içinde eklenecek metodu tasarla,
- Gerekli `AIService` veya ek servis katmanını öner,
- Kullanılacak prompt yapısını `ai/prompts/` için örnekle,
- Gerekirse hangi dokümanları güncellemem gerektiğini (ör. `docs/ai-training`) belirt."

### 3.3. Mevcut AI Özelliğini İyileştirme

"`AIController@{METHOD}` metodundaki iş akışını inceleyip olası iyileştirmeleri öner. Service katmanına taşınabilecek mantık, tekrar eden kodlar, daha iyi loglama noktaları ve hata yönetimi için somut öneriler ver. Gerekirse kod taslaklarıyla göster."

### 3.4. Prompt İyileştirme

"`ai/prompts/{DOSYA_ADI}` içindeki prompt'u inceleyip, projenin `docs/ai/COPILOT_PROMPTS_GUIDE.md` rehberine uygun şekilde daha net, tutarlı ve güvenli hale getir. Rol, bağlam, kurallar ve örnekler açısından yeniden yaz ve değişiklikleri madde madde açıkla."

## 4. Güvenlik, Rate Limit ve Yetkilendirme

- AI endpoint'lerinde **throttle** middleware'leri vardır (ör. `/ai/chat` için dakikada 30 istek).
- Bazı endpoint'ler (özellikle admin AI endpoint'leri) için:
  - `auth:sanctum`
  - `role:admin`
  zorunludur.
- Değişiklik/öneri yaparken:
  - Kullanıcı verilerini gereksiz yere loglama,
  - Gizli anahtarları (API key) açıkta yazma,
  - Rate limit'i aşırı gevşetme
  gibi riskli önerilerden kaçın.

## 5. Kod Değişikliği Önerirken Dikkat Edilecek Noktalar

1. Mümkün olduğunca **minimal ve modüler** değişiklik öner.
2. Var olan pattern'lere uy:
   - Controller'lar ince olsun, iş mantığı Service/Module katmanında toplansın.
   - Validation için Request sınıfları veya mevcut pattern kullanılsın.
3. Testleri düşün:
   - Değişiklik önerirken ilgili test senaryolarını da tarif et (nerede, hangi isimle eklenmeli?).
4. Dokümantasyonu unutma:
   - Önemli bir davranış değişikliği varsa, ilgili `docs/` dosyalarının da güncellenmesini öner.

## 6. Bu Rehberi Nasıl Kullanmalısın?

- Bir kullanıcı bu repoda senden yardım istediğinde, önce **bu dosyayı** ve `aiegitim/PROJE_MIMARISI_CHATGPT.md` dosyasını referans al.
- Görev AI ile ilgiliyse, mutlaka:
  - `routes/ai.php`
  - `app/Http/Controllers/Api/AIController.php`
  - `app/Services` içindeki ilgili servisler
  - `ai/prompts/` klasörü
  - `docs/ai*` ve `docs/ai-training*`
  dosyalarına göz at.

Bu rehber, zaman içinde proje geliştikçe güncellenebilir; yeni AI özellikleri eklendikçe buraya ek görev şablonları ve yol haritaları eklemek teşvik edilir.