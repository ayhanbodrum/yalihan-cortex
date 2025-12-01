# 🌐 Market Intelligence - Uzak Sunucu Kurulumu

**Tarih:** 2025-11-29  
**Versiyon:** 1.0.0  
**Durum:** ✅ Uzak Sunucu Desteği

---

## 🎯 Uzak Sunucu Yapılandırması

Pazar İstihbaratı verileri **uzak bir MySQL sunucusunda** tutulabilir. Bu sayede:
- ✅ Ana sunucudan ayrı yönetim
- ✅ Ölçeklenebilirlik
- ✅ Yük dağılımı
- ✅ Güvenlik (ayrı sunucu)

---

## 📋 KURULUM ADIMLARI

### 1. Uzak Sunucuda Veritabanı Oluşturma

Uzak MySQL sunucusunda veritabanını oluşturun:

```sql
CREATE DATABASE yalihan_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Kullanıcı oluştur (güvenlik için)
CREATE USER 'yalihan_market_user'@'%' IDENTIFIED BY 'güçlü_şifre_buraya';
GRANT ALL PRIVILEGES ON yalihan_market.* TO 'yalihan_market_user'@'%';
FLUSH PRIVILEGES;
```

**Önemli:** Uzak sunucuya erişim için:
- Firewall'da 3306 portunu açın
- MySQL'de remote access'i aktif edin
- SSL bağlantısı önerilir (production için)

---

### 2. .env Dosyasına Uzak Sunucu Ayarları

`.env` dosyanıza aşağıdaki satırları ekleyin:

```env
# Market Intelligence Database (Uzak Sunucu)
MARKET_DB_HOST=uzak-sunucu-ip-veya-domain.com
MARKET_DB_PORT=3306
MARKET_DB_DATABASE=yalihan_market
MARKET_DB_USERNAME=yalihan_market_user
MARKET_DB_PASSWORD=güçlü_şifre_buraya
MARKET_DB_CHARSET=utf8mb4
MARKET_DB_COLLATION=utf8mb4_unicode_ci

# SSL Bağlantısı (Opsiyonel - Production için önerilir)
# MARKET_DB_SSL_CA=/path/to/ca-cert.pem
# MYSQL_ATTR_SSL_CA=/path/to/ca-cert.pem
```

**Örnek Uzak Sunucu Ayarları:**

```env
# Örnek 1: IP Adresi ile
MARKET_DB_HOST=192.168.1.100
MARKET_DB_PORT=3306
MARKET_DB_DATABASE=yalihan_market
MARKET_DB_USERNAME=yalihan_market_user
MARKET_DB_PASSWORD=MySecurePassword123!

# Örnek 2: Domain ile
MARKET_DB_HOST=db.yalihanemlak.com.tr
MARKET_DB_PORT=3306
MARKET_DB_DATABASE=yalihan_market
MARKET_DB_USERNAME=yalihan_market_user
MARKET_DB_PASSWORD=MySecurePassword123!

# Örnek 3: Farklı Port
MARKET_DB_HOST=192.168.1.100
MARKET_DB_PORT=3307
MARKET_DB_DATABASE=yalihan_market
MARKET_DB_USERNAME=yalihan_market_user
MARKET_DB_PASSWORD=MySecurePassword123!
```

---

### 3. Config Cache Temizleme

```bash
php artisan config:clear
php artisan config:cache
```

---

### 4. Bağlantıyı Test Etme

```bash
# Tinker ile test
php artisan tinker

# Bağlantıyı kontrol et
DB::connection('market_intelligence')->getPdo();

# Veritabanı adını kontrol et
DB::connection('market_intelligence')->getDatabaseName();
// Çıktı: "yalihan_market"

# Tabloyu kontrol et
Schema::connection('market_intelligence')->hasTable('market_listings');
// Çıktı: true
```

---

### 5. Migration Çalıştırma

**ÖNEMLİ:** Migration'ı **market_intelligence** connection'ına çalıştırmak için:

```bash
php artisan migrate --database=market_intelligence
```

**Veya sadece market_listings migration'ını çalıştırmak için:**

```bash
php artisan migrate --path=database/migrations/2025_11_27_011644_create_market_listings_table.php --database=market_intelligence
```

---

## 🔒 GÜVENLİK AYARLARI

### 1. SSL Bağlantısı (Önerilir)

Production ortamında SSL kullanın:

```env
# .env dosyasına ekleyin
MARKET_DB_SSL_CA=/path/to/ca-cert.pem
MYSQL_ATTR_SSL_CA=/path/to/ca-cert.pem
```

### 2. Firewall Kuralları

Uzak sunucuda sadece gerekli IP'lerden erişime izin verin:

```bash
# MySQL sunucusunda
# Sadece belirli IP'den erişim
GRANT ALL PRIVILEGES ON yalihan_market.* TO 'yalihan_market_user'@'192.168.1.50' IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
```

### 3. Güçlü Şifre

- Minimum 16 karakter
- Büyük/küçük harf, sayı, özel karakter
- Düzenli olarak değiştirin

---

## 🔍 SORUN GİDERME

### Hata: "Connection refused" veya "Connection timed out"

**Çözüm:**
1. Uzak sunucunun IP/domain'ini kontrol edin
2. Firewall'da 3306 portunun açık olduğundan emin olun
3. MySQL'de remote access'in aktif olduğunu kontrol edin:

```sql
-- MySQL'de kontrol
SELECT host, user FROM mysql.user WHERE user='yalihan_market_user';
-- '%' veya IP adresi görünmeli
```

### Hata: "Access denied"

**Çözüm:**
1. Kullanıcı adı ve şifreyi kontrol edin
2. Kullanıcıya yetki verildiğinden emin olun:

```sql
GRANT ALL PRIVILEGES ON yalihan_market.* TO 'yalihan_market_user'@'%';
FLUSH PRIVILEGES;
```

### Hata: "Unknown database 'yalihan_market'"

**Çözüm:**
Uzak sunucuda veritabanını oluşturun:

```sql
CREATE DATABASE yalihan_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Hata: "Table doesn't exist"

**Çözüm:**
Migration'ı çalıştırın:

```bash
php artisan migrate --database=market_intelligence
```

---

## 📊 BAĞLANTI YAPISI

```
┌─────────────────────────────────────────┐
│  Ana Sunucu (Laravel Uygulaması)       │
│  ├─ Ana DB: yalihanemlak_ultra          │
│  │  └─ market_intelligence_settings     │
│  │                                      │
│  └─ Uzak Bağlantı                       │
│     └─ market_intelligence connection   │
└─────────────────────────────────────────┘
                    ↓
         [MySQL Network Connection]
                    ↓
┌─────────────────────────────────────────┐
│  Uzak Sunucu (MySQL)                    │
│  └─ Veritabanı: yalihan_market          │
│     └─ market_listings                  │
└─────────────────────────────────────────┘
```

---

## ✅ DOĞRULAMA KONTROL LİSTESİ

- [ ] Uzak sunucuda veritabanı oluşturuldu
- [ ] Kullanıcı oluşturuldu ve yetki verildi
- [ ] Firewall'da 3306 portu açık
- [ ] `.env` dosyasına `MARKET_DB_*` ayarları eklendi
- [ ] `php artisan config:clear` çalıştırıldı
- [ ] Bağlantı test edildi (`php artisan tinker`)
- [ ] Migration çalıştırıldı (`php artisan migrate --database=market_intelligence`)
- [ ] Tablo oluşturuldu (`market_listings`)

---

## 🎯 ÖZET

1. ✅ Uzak sunucuda `yalihan_market` veritabanını oluştur
2. ✅ Kullanıcı oluştur ve yetki ver
3. ✅ `.env` dosyasına `MARKET_DB_*` ayarlarını ekle (uzak sunucu bilgileri)
4. ✅ `php artisan config:clear` çalıştır
5. ✅ Bağlantıyı test et
6. ✅ `php artisan migrate --database=market_intelligence` çalıştır

**Sonuç:** Market Intelligence verileri artık uzak sunucuda, güvenli ve ölçeklenebilir! 🎉

---

**Son Güncelleme:** 2025-11-29






