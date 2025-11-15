# Admin Kullanıcı Bilgileri - 2025-11-11

**Tarih:** 2025-11-11 18:10:00  
**Durum:** ⚠️ Seeder henüz çalıştırılmamış

---

## 🔐 Admin Kullanıcı Bilgileri

### Süper Admin

```yaml
Ad: Yalıhan Emlak
Email: yalihanemlak@gmail.com
Şifre: admin123
Telefon: +905332090302
Rol: Süper Admin (role_id: 1)
Durum: Aktif
Ünvan: Süper Admin
Bio: Yalıhan Emlak sistem yöneticisi
Adres: Kadıköy, İstanbul
```

### Admin

```yaml
Ad: Ayhan Küçük
Email: ayhankucuk@gmail.com
Şifre: admin123
Telefon: +905332090302
Rol: Admin (role_id: 1)
Durum: Aktif
Ünvan: Admin
Bio: Yalıhan Emlak yöneticisi
Adres: Kadıköy, İstanbul
```

---

## 📋 Roller

Seeder'da tanımlı roller:

1. **Süper Admin** (ID: 1)
   - Tüm yetkilere sahip
   - Sistem yönetimi

2. **Admin** (ID: 2)
   - Kullanıcı yönetimi
   - İlan yönetimi
   - Danışman yönetimi

3. **Danışman** (ID: 3)
   - İlan görüntüleme/düzenleme
   - Müşteri yönetimi

---

## ⚠️ Durum

**Veritabanı Durumu:**
- ✅ Tablo yapısı hazır
- ❌ Kullanıcılar henüz oluşturulmamış
- ❌ Roller henüz oluşturulmamış

---

## 🚀 Admin Kullanıcıları Oluşturma

Admin kullanıcılarını oluşturmak için seeder'ı çalıştırın:

```bash
# Roller ve admin kullanıcıları oluştur
php artisan db:seed --class=Context7SystemSeeder

# Veya tüm seeder'ları çalıştır
php artisan db:seed
```

---

## 📝 Seeder Detayları

**Dosya:** `database/seeders/Context7SystemSeeder.php`

**Oluşturulacaklar:**
1. ✅ Roller (Süper Admin, Admin, Danışman)
2. ✅ Admin kullanıcıları (2 adet)
3. ✅ Danışman kullanıcıları (3 adet)

---

## 🔑 Giriş Bilgileri (Seeder çalıştırıldıktan sonra)

### Admin Paneli Girişi

**URL:** `http://127.0.0.1:8000/login`

**Süper Admin:**
- Email: `yalihanemlak@gmail.com`
- Şifre: `admin123`

**Admin:**
- Email: `ayhankucuk@gmail.com`
- Şifre: `admin123`

---

**Not:** Seeder çalıştırılmadan önce admin kullanıcıları veritabanında bulunmaz. Seeder'ı çalıştırdıktan sonra yukarıdaki bilgilerle giriş yapabilirsiniz.

