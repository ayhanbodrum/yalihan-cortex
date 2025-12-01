# 🚀 GitHub'a Push Rehberi

**Tarih:** 01 Aralık 2025  
**Repository:** https://github.com/ayhanbodrum/yalihan-cortex

---

## 📋 DURUM

✅ Git repository hazır  
✅ Commit oluşturuldu (58873e7)  
✅ Branch 'main' olarak ayarlandı  
✅ Remote repository eklendi  
✅ .env dosyası güvenli (commit'te yok)  

⚠️ **GEREKLİ:** GitHub Authentication

---

## 🔐 YÖNTEM 1: GitHub CLI (Önerilen)

### Adım 1: Authentication

Terminal'de şu komutu çalıştırın:

```bash
gh auth login
```

### Adım 2: Sorulara Cevap Verin

1. **What account do you want to log into?**
   - `GitHub.com` seçin (Enter)

2. **What is your preferred protocol for Git operations?**
   - `HTTPS` seçin (Enter)

3. **Authenticate Git with your GitHub credentials?**
   - `Yes` seçin (Y)

4. **How would you like to authenticate GitHub CLI?**
   - `Login with a web browser` seçin (Enter)

5. **Press Enter to open github.com in your browser...**
   - Enter'a basın
   - Browser'da GitHub login sayfası açılacak
   - GitHub'da authorize edin
   - Terminal'de "✓ Authentication complete" mesajını göreceksiniz

### Adım 3: Push İşlemi

Authentication tamamlandıktan sonra:

```bash
git push -u origin main
```

---

## 🔐 YÖNTEM 2: Personal Access Token

### Adım 1: Token Oluştur

1. GitHub'a giriş yapın: https://github.com
2. Sağ üst köşeden profil resminize tıklayın
3. **Settings** seçin
4. Sol menüden **Developer settings** seçin
5. **Personal access tokens** > **Tokens (classic)** seçin
6. **Generate new token** > **Generate new token (classic)** tıklayın
7. **Note:** "Yalihan Cortex Push" yazın
8. **Expiration:** İstediğiniz süreyi seçin
9. **Select scopes:** `repo` seçeneğini işaretleyin
10. **Generate token** tıklayın
11. **Token'ı kopyalayın** (bir daha gösterilmeyecek!)

### Adım 2: Remote URL'i Güncelle

Terminal'de şu komutu çalıştırın (TOKEN yerine kopyaladığınız token'ı yapıştırın):

```bash
git remote set-url origin https://TOKEN@github.com/ayhanbodrum/yalihan-cortex.git
```

### Adım 3: Push İşlemi

```bash
git push -u origin main
```

---

## 🔐 YÖNTEM 3: SSH Key

### Adım 1: SSH Key Kontrolü

```bash
ls -la ~/.ssh
```

Eğer `id_rsa.pub` veya `id_ed25519.pub` dosyası varsa, içeriğini kopyalayın:

```bash
cat ~/.ssh/id_rsa.pub
# veya
cat ~/.ssh/id_ed25519.pub
```

### Adım 2: GitHub'a SSH Key Ekle

1. GitHub'a giriş yapın: https://github.com
2. Sağ üst köşeden profil resminize tıklayın
3. **Settings** seçin
4. Sol menüden **SSH and GPG keys** seçin
5. **New SSH key** tıklayın
6. **Title:** "MacBook Pro" yazın
7. **Key:** Kopyaladığınız SSH key'i yapıştırın
8. **Add SSH key** tıklayın

### Adım 3: Remote URL'i Güncelle

```bash
git remote set-url origin git@github.com:ayhanbodrum/yalihan-cortex.git
```

### Adım 4: Push İşlemi

```bash
git push -u origin main
```

---

## ✅ BAŞARILI PUSH SONRASI

Push işlemi başarılı olduğunda şu mesajı göreceksiniz:

```
Enumerating objects: X, done.
Counting objects: 100% (X/X), done.
Delta compression using up to X threads
Compressing objects: 100% (X/X), done.
Writing objects: 100% (X/X), X.XX MiB | X.XX MiB/s, done.
Total X (delta X), reused X (delta X), pack-reused X
To https://github.com/ayhanbodrum/yalihan-cortex.git
 * [new branch]      main -> main
Branch 'main' set up to track remote branch 'main' from 'origin'.
```

---

## 🔍 SORUN GİDERME

### "Permission denied" Hatası

- SSH key'iniz GitHub'da tanımlı mı kontrol edin
- Personal Access Token'ın `repo` scope'u var mı kontrol edin

### "Repository not found" Hatası

- Repository'nin GitHub'da oluşturulmuş olduğundan emin olun
- Repository adının doğru olduğundan emin olun: `ayhanbodrum/yalihan-cortex`

### "Authentication failed" Hatası

- Token'ın süresi dolmuş olabilir, yeni token oluşturun
- GitHub CLI authentication'ı yeniden yapın: `gh auth login`

---

## 📊 MEVCUT DURUM

- **Repository:** git@github.com:ayhanbodrum/yalihan-cortex.git
- **Branch:** main
- **Commit:** 58873e7
- **Mesaj:** "Yalihan Cortex v2.1 Stable Release - Production Ready"
- **Değişiklikler:** 1781 dosya (103,220 ekleme, 199,058 silme)

---

**Son Güncelleme:** 01 Aralık 2025

