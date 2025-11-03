# 🔧 TODO: Auth Login View Eksik

## Hata:
```
InvalidArgumentException
View [auth::login] not found.
```

## Konum:
- `app/Modules/Auth/Controllers/AuthController.php:21`
- `return view('auth::login');`

## Çözüm:
- `app/Modules/Auth/Views/login.blade.php` oluştur
- VEYA
- `resources/views/auth/login.blade.php` oluştur ve controller'ı değiştir

## Öncelik:
- **DÜŞÜK** (admin login zaten çalışıyor)
- Frontend için gerekli değil

## Tarih:
2025-11-03 13:53
