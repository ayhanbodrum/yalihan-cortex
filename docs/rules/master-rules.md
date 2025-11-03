# Master Kurallar Dosyası

**Context7 Standardı:** C7-MASTER-RULES-2025-10-11
**Versiyon:** 1.0.0
**Son Güncelleme:** 11 Ekim 2025
**Durum:** ✅ Aktif

---

Bu dosya tüm davranış kalıplarını, kuralları ve hata önleme mekanizmalarını içerir. Her işlemde referans alınacak.

## Temel Kurallar

1. **Dosya İsimlendirme:** Kısa, tutarlı, Türkçe isimler (örneğin, `ilan-sistemi.md`, `rehber.md`). İngilizce-Türkçe karışımı yasak. Gerekli teknik terimler (Context7, AI) hariç Türkçe tercih edilsin.
2. **Yapı:** 4 ana kategori (technical/, usage/, reports/, rules/).
3. **Güncelleme:** Her değişiklikte kurallar kontrolü.
4. **Birleştirme:** Yinelenen içerikler tek dosyada, hata olmadan.

## Davranış Kalıpları

- MD yazma: Standart başlık, tarih, içerik.
- Taşıma: Backup sonrası, link güncelleme.
- Hata Önleme: Tespit edilen hatalar kurallara ekle (tekrar önleme).

## Context7 Uyumluluk

- Kurallar Context7 standardına göre.
- Uzun isimler yasak, tutarlı format zorunlu.

## Hata Önleme

- Yinelenen dosya isimleri yasak.
- Taşıma hataları otomatik kontrol.
- **Hata Tespiti ve Hafıza:** Bir hata tespit edildiğinde, o hatanın tekrar yaşanmaması için gerekli dosya işlenmeyecek ve hafızaya alınacak. Tekrar önleme mekanizması aktif.

## Tasarım Tutarlılığı

- Tüm dosyalar tutarlı tasarım kurallarına uymalı.
- Modüller arası ilişkiler açıkça tanımlanmalı (örneğin, adres sistemi CRM sistemi bağlantıları, hangi veri nereden geliyor).

## Modül İlişkileri

- **Adres Sistemi:** CRM sistemi ile bağlantılı, konum verileri paylaşılır.
- **CRM Sistemi:** Kişi verileri adres sisteminden gelir, eşleştirme yapılır.
- **Veri Akışı:** Her modülün veri kaynakları ve hedefleri açıkça belirtilmeli.

## Sistem Klasörleri Kuralları

- **.context7 Klasörü:** Context7 merkezi otorite sistemi. İçerik: authority.json (kurallar, uyumluluk, entegrasyonlar), progress.json (ilerleme takibi), api.php (Context7 API), backups/ (yedekler). İşleyiş: Pre-commit hook'lar, CI/CD, IDE entegrasyonları için referans. Değişikliklerde authority.json güncellenmeli, otomatik sync aktif.
- **.cursor Klasörü:** Cursor AI editör konfigürasyonu. İçerik: settings.json (MCP sunucuları, Context7 sync kuralları), rules/ (context7-rules.md, context7.mdc), memory/ (hafıza dosyaları), backups/ (yedekler). Dosyalar: settings.json root'ta, rules/ alt klasörde. Kural: Cursor değişikliklerinde settings.json ve rules/ sync edilmeli, Context7 uyumlu kalınmalı.

## Hafıza ve Tasarım İyileştirmeleri

### Hafıza Sistemi Önerileri
- **Hata Hafızası Veritabanı:** Geçmiş hataları saklamak için ayrı tablo oluştur (error_memory: id, error_type, description, occurred_at, fixed_at, prevention_rule). Otomatik kayıt ve tekrar önleme.
- **AI Hafıza Entegrasyonu:** Context7 memory sistemini genişlet, geçmiş kararları ve önerileri sakla (.cursor/memory/ altında JSON dosyaları).
- **Otomatik Öğrenme:** Hata patern'lerini analiz et, benzer durumlar için otomatik öneri üret.
- **Backup Hafızası:** Kritik kararları .context7/backups/ altında tarihli sakla, geri dönüş için.

### Tasarım Tutarlılığı Önerileri
- **Neo Design System Genişletme:** Tüm component'ler için standart renk paleti, typography, spacing kuralları tanımla (docs/technical/design-system.md).
- **UI Pattern Kütüphanesi:** Sık kullanılan pattern'leri (modal, form, card) standartlaştır, örnek kodlarla dokümante et.
- **Responsive Tasarım Kuralları:** Mobil-first yaklaşım, breakpoint'ler sabit (sm: 640px, md: 768px, lg: 1024px).
- **Accessibility Standartları:** WCAG 2.1 AA uyumluluk, keyboard navigation, screen reader desteği zorunlu.
- **Dark Mode Entegrasyonu:** Tüm component'lerde dark mode desteği, otomatik theme switch.

### Verimlilik ve Otomasyon Önerileri
- **Context7 AI Asistan:** Kod yazım sırasında gerçek zamanlı öneriler, hata tespiti ve düzeltme.
- **Otomatik Dokümantasyon:** Kod değişikliklerinde otomatik MD güncelleme, cross-reference kontrolü.
- **CI/CD Pipeline Genişletme:** Context7 compliance check'leri, otomatik testler, deployment öncesi validation.
- **Modüler Mimari:** Sistemleri microservice'e böl, API gateway ile bağlantı.

### Kusursuz Sistem İçin Ek Kurallar
- **Zero Tolerance Policy:** Her hata için root cause analysis, tekrar önleme mekanizması.
- **Continuous Improvement:** Haftalık review toplantıları, metric tracking (error rate, compliance score).
- **Training Program:** Yeni developer'lar için Context7 eğitim programı, sertifika sistemi.
- **Audit Trail:** Tüm değişiklikler için detaylı log, kim ne zaman değiştirdi takip edilebilir.

## Kullanıcı Davranış Kuralları (Tüm İşlemler İçin)

### Genel Davranış İlkeleri
- **Context7 Uyumluluk Öncelikli:** Her işlemde authority.json ve master-rules.md'yi referans al. Uyumsuzluk durumunda işlem durdurulmalı.
- **AI Destekli Çalışma:** Tüm kod yazımında AI asistanı (Cursor/Claude) kullan, önerileri değerlendir ama manuel kontrol et.
- **Dokümantasyon Zorunlu:** Her değişiklik için MD güncelle, cross-reference kontrolü yap.
- **Test Öncelikli:** Kod yazmadan önce test case yaz, CI/CD'den geçmeden commit etme.

### Kod Yazma İşlemleri
- **Başlangıç:** AI ile kod taslağı oluştur, Context7 kurallarına göre düzenle.
- **Orta:** Hafıza sistemini kontrol et (geçmiş hatalar), benzer patern'leri uygula.
- **Son:** Otomatik test çalıştır, AI ile code review yap, compliance %100 olmalı.

### Hata Tespiti ve Düzeltme
- **Tespit:** AI ile otomatik tara, error_memory tablosuna logla.
- **Düzeltme:** Root cause analysis yap, prevention rule ekle, tekrar önleme mekanizmasını test et.
- **Öğrenme:** Hata patern'ini AI hafızasına ekle, gelecekte otomatik öneri üret.

### Tasarım ve UI İşlemleri
- **Standart Kullanım:** Neo Design System'i zorunlu, UI pattern kütüphanesinden seç.
- **Responsive ve Accessibility:** Mobil-first yaklaşım, WCAG AA uyumluluk kontrolü.
- **Dark Mode:** Otomatik theme switch, tüm component'lerde destek.

### Dokümantasyon İşlemleri
- **Otomatik Üretim:** Kod değişikliklerinde AI ile MD güncelle.
- **Tutarlılık:** Master kurallara göre isimlendir, Türkçe tercih et.
- **Audit:** Haftalık doc review, dead link kontrolü.

### Deployment ve Bakım
- **CI/CD:** Context7 compliance check zorunlu, otomatik testler geçmeli.
- **Backup:** Kritik değişikliklerde .context7/backups/ al.
- **Monitoring:** Error rate ve compliance score track et, threshold aşımında alarm.

### Eğitim ve İyileştirme
- **Onboarding:** Yeni kullanıcılar Context7 bootcamp'tan geçmeli.
- **Continuous Learning:** Haftalık review'de AI tespitlerini tartış, iyileştirme önerilerini uygula.
- **Feedback:** Tüm işlemler için feedback loop aktif, kusursuzluk hedefi.

Bu kurallar tüm işlemler için geçerli. Uygulama için adım adım plan: Önce error_memory migration oluştur, sonra design system genişlet, en son AI entegrasyonları.
