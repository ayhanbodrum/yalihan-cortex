# 🗄️ Market Intelligence - Çoklu Veritabanı Kurulumu

**Tarih:** 2025-11-27  
**Versiyon:** 1.0.0

---

## 🎯 Amaç

Market Intelligence (Pazar İstihbaratı) verilerini **ayrı bir veritabanında** tutmak. Bu sayede:

- ✅ Ana veritabanı performansı korunur
- ✅ Büyük veri setleri ayrı yönetilir
- ✅ Backup/restore işlemleri kolaylaşır
- ✅ Aynı proje içinde kolay erişim

---

## 📋 KURULUM ADIMLARI

### 1. Veritabanı Oluşturma

MySQL'de yeni veritabanı oluşturun:

```sql
CREATE DATABASE yalihan_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Veya phpMyAdmin/MySQL Workbench üzerinden:**
- Database Name: `yalihan_market`
- Charset: `utf8mb4`
- Collation: `utf8mb4_unicode_ci`

### 2. .env Dosyasına Ekleyin

`.env` dosyanıza aşağıdaki satırları ekleyin:

```env
# Market Intelligence Database (Ayrı Veritabanı)
MARKET_DB_HOST=127.0.0.1
MARKET_DB_PORT=3306
MARKET_DB_DATABASE=yalihan_market
MARKET_DB_USERNAME=root
MARKET_DB_PASSWORD=
MARKET_DB_CHARSET=utf8mb4
MARKET_DB_COLLATION=utf8mb4_unicode_ci
```

**Not:** Eğer aynı MySQL sunucusunda farklı bir veritabanı kullanıyorsanız, sadece `MARKET_DB_DATABASE` değiştirmeniz yeterli. Diğer ayarlar ana veritabanı ile aynı olabilir.

### 3. Config Cache Temizleme

```bash
php artisan config:clear
php artisan config:cache
```

### 4. Migration Çalıştırma

**ÖNEMLİ:** Migration'ı **market_intelligence** connection'ına çalıştırmak için:

```bash
php artisan migrate --database=market_intelligence
```

**Veya sadece market_listings migration'ını çalıştırmak için:**

```bash
php artisan migrate --path=database/migrations/2025_11_27_011644_create_market_listings_table.php --database=market_intelligence
```

---

## 🔍 DOĞRULAMA

### Veritabanı Bağlantısını Test Edin

```php
// Tinker'da test edin
php artisan tinker

// Bağlantıyı kontrol edin
DB::connection('market_intelligence')->getPdo();

// Veritabanı adını kontrol edin
DB::connection('market_intelligence')->getDatabaseName();
// Çıktı: "yalihan_market"

// Tabloyu kontrol edin
Schema::connection('market_intelligence')->hasTable('market_listings');
// Çıktı: true
```

### Model ile Test

```php
// Tinker'da
use App\Models\MarketListing;

// Yeni kayıt oluştur
MarketListing::create([
    'source' => 'sahibinden',
    'external_id' => 'test-123',
    'title' => 'Test İlan',
    'price' => 1500000,
    'currency' => 'TRY',
    'location_il' => 'Antalya',
    'status' => 1,
]);

// Kayıtları listele
MarketListing::all();
```

---

## 📊 VERİTABANI YAPISI

### Ana Veritabanı (yalihanemlak_ultra)
```
├── ilanlar
├── kisiler
├── talepler
├── gorevler
└── ... (diğer tablolar)
```

### Market Intelligence Veritabanı (yalihan_market)
```
└── market_listings
    ├── id
    ├── source
    ├── external_id
    ├── price
    ├── price_history (JSON)
    ├── snapshot_data (JSON)
    └── ... (diğer alanlar)
```

---

## 💡 KULLANIM ÖRNEKLERİ

### Model Kullanımı (Otomatik Connection)

```php
use App\Models\MarketListing;

// Model otomatik olarak market_intelligence connection'ını kullanır
$listings = MarketListing::active()->get();

// Yeni kayıt
MarketListing::create([...]);

// Güncelleme
$listing = MarketListing::find(1);
$listing->addPriceHistory(1500000);
```

### Manuel Connection Kullanımı

```php
use Illuminate\Support\Facades\DB;

// Direkt query
$listings = DB::connection('market_intelligence')
    ->table('market_listings')
    ->where('status', 1)
    ->get();

// Raw query
$count = DB::connection('market_intelligence')
    ->select('SELECT COUNT(*) as count FROM market_listings WHERE status = 1');
```

### Migration'ları Farklı Connection'a Çalıştırma

```bash
# Tüm migration'ları market_intelligence'a çalıştır
php artisan migrate --database=market_intelligence

# Belirli bir migration'ı çalıştır
php artisan migrate --path=database/migrations/2025_11_27_011644_create_market_listings_table.php --database=market_intelligence

# Rollback
php artisan migrate:rollback --database=market_intelligence
```

---

## 🔧 SORUN GİDERME

### Hata: "Connection refused"

**Çözüm:**
```bash
# .env dosyasını kontrol edin
MARKET_DB_HOST=127.0.0.1
MARKET_DB_DATABASE=yalihan_market

# Config cache temizleyin
php artisan config:clear
```

### Hata: "Database doesn't exist"

**Çözüm:**
```sql
-- MySQL'de veritabanını oluşturun
CREATE DATABASE yalihan_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Hata: "Table doesn't exist"

**Çözüm:**
```bash
# Migration'ı çalıştırın
php artisan migrate --database=market_intelligence
```

### Model Yanlış Veritabanını Kullanıyor

**Kontrol:**
```php
// Model'in connection'ını kontrol edin
$model = new MarketListing();
echo $model->getConnectionName(); // "market_intelligence" olmalı
```

---

## 📈 PERFORMANS İPUÇLARI

### 1. Connection Pooling

Aynı MySQL sunucusunda farklı veritabanları kullanıyorsanız, connection pooling otomatik olarak çalışır.

### 2. Index Optimizasyonu

Migration'da zaten index'ler tanımlı:
- `source` (tek)
- `external_id` (tek)
- `['source', 'external_id']` (composite, unique)
- `status`
- `last_seen_at`
- `['location_il', 'location_ilce']` (composite)

### 3. Query Optimization

```php
// İyi: Index kullanır
MarketListing::where('source', 'sahibinden')
    ->where('status', 1)
    ->get();

// Kötü: Full table scan
MarketListing::where('title', 'LIKE', '%villa%')->get();
```

---

## 🔄 BACKUP VE RESTORE

### Backup

```bash
# Market Intelligence veritabanını yedekle
mysqldump -u root -p yalihan_market > backup_market_$(date +%Y%m%d).sql
```

### Restore

```bash
# Yedekten geri yükle
mysql -u root -p yalihan_market < backup_market_20251127.sql
```

---

## ✅ AVANTAJLAR

### Aynı Proje İçinde (Önerilen)

✅ **Avantajlar:**
- Aynı kod tabanı
- Kolay erişim (Model kullanımı)
- Aynı deployment
- Migration'lar aynı yerde
- Transaction yönetimi kolay

❌ **Dezavantajlar:**
- Aynı sunucuda olmalı (genelde sorun değil)

### Ayrı Proje/Mikroservis

✅ **Avantajlar:**
- Tamamen izole
- Farklı sunucuda olabilir
- Bağımsız scaling

❌ **Dezavantajlar:**
- API ile iletişim gerekir
- Daha karmaşık deployment
- Transaction yönetimi zor

---

## 📚 İLGİLİ DOSYALAR

- `config/database.php` - Connection tanımı
- `app/Models/MarketListing.php` - Model (connection belirtilmiş)
- `database/migrations/2025_11_27_011644_create_market_listings_table.php` - Migration

---

## 🎯 ÖZET

1. ✅ Yeni veritabanı oluştur: `yalihan_market`
2. ✅ `.env` dosyasına `MARKET_DB_*` ayarlarını ekle
3. ✅ `php artisan config:clear` çalıştır
4. ✅ `php artisan migrate --database=market_intelligence` çalıştır
5. ✅ Model otomatik olarak doğru connection'ı kullanır

**Sonuç:** Market Intelligence verileri artık ayrı veritabanında, ama aynı proje içinde kolayca erişilebilir! 🎉

---

**Son Güncelleme:** 2025-11-27







