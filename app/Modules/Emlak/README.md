# 🏠 Emlak Modülü

**Versiyon:** 1.0.0  
**Context7 Standardı:** C7-EMLAK-MODULE-2025-12-01  
**Durum:** ✅ Aktif

---

## 📋 Genel Bakış

Emlak modülü, ilan yönetimi, özellik sistemi ve proje yönetimi için temel altyapıyı sağlar.

## 🎯 Sorumluluklar

- **İlan Yönetimi:** İlanların CRUD işlemleri, kategorilendirme, özellik yönetimi
- **Özellik Sistemi:** İlan özellikleri, özellik kategorileri, çoklu dil desteği
- **Proje Yönetimi:** Proje tanımları, görseller, çeviriler

## 📁 Yapı

```
Emlak/
├── Controllers/
│   ├── FeatureController.php      # Özellik yönetimi
│   └── ProjeController.php        # Proje yönetimi
├── Models/
│   ├── Ilan.php                   # Ana ilan modeli
│   ├── Feature.php                # Özellik modeli
│   ├── FeatureCategory.php        # Özellik kategorisi
│   └── Proje.php                  # Proje modeli
├── Services/
│   └── IlanService.php            # İlan iş mantığı
├── routes/
│   └── web.php                    # Web route'ları
└── Database/
    └── Migrations/                # Veritabanı migration'ları
```

## 🔗 Bağımlılıklar

- **Crm Modülü:** Kişi (Kisi) ilişkileri için
- **Auth Modülü:** Kullanıcı (User) ilişkileri için
- **BaseModule:** Temel model ve controller sınıfları

## 🚀 Kullanım

### İlan Oluşturma

```php
use App\Modules\Emlak\Models\Ilan;

$ilan = Ilan::create([
    'baslik' => 'Örnek İlan',
    'kategori_id' => 1,
    'fiyat' => 1000000,
    // ...
]);
```

### Özellik Ekleme

```php
use App\Modules\Emlak\Models\Feature;

$ozellik = Feature::create([
    'name' => 'Havuz',
    'category_id' => 1,
    // ...
]);
```

## 📊 Route'lar

- `GET /admin/ilanlar` - İlan listesi
- `GET /admin/ilanlar/create` - Yeni ilan oluştur
- `GET /admin/ozellikler` - Özellik listesi
- `GET /admin/projeler` - Proje listesi

## 🔧 Yapılandırma

Modül, `EmlakServiceProvider` üzerinden yüklenir ve `ModuleServiceProvider` tarafından kaydedilir.

## 📝 Notlar

- İlan modeli, çoklu dil desteği için `IlanTranslation` modeli ile ilişkilidir
- Özellik sistemi, polymorphic ilişkiler kullanır
- Proje yönetimi, görsel yönetimi için `ProjeGorsel` modeli kullanır

---

**Son Güncelleme:** 01 Aralık 2025
