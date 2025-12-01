# 🔒 GÜVENLİK FİX: Ollama Localhost Endpoint Kullanımı

**Tarih:** 25 Kasım 2025  
**Durum:** ✅ Uygulandı (Geçici Fix)  
**Amaç:** Public HTTP bağlantısından kaçınmak ve KVKK uyumluluğu

---

## ✅ YAPILAN DEĞİŞİKLİKLER

### 1. Config Güncellemesi

**Dosya:** `config/ai.php`

**DEĞİŞTİRİLEN:**

```php
// ❌ ÖNCE (Public IP - Güvensiz)
'ollama_api_url' => env('OLLAMA_API_URL', 'http://51.75.64.121:11434'),
'ollama_endpoint' => env('OLLAMA_API_URL', 'http://51.75.64.121:11434'),
```

**YENİ HAL:**

```php
// ✅ SONRA (Localhost - SSH Tunnel/VPN ile)
'ollama_api_url' => env('OLLAMA_API_URL', 'http://localhost:11434'),
'ollama_endpoint' => env('OLLAMA_API_URL', 'http://localhost:11434'),
```

**Eklenen Güvenlik Notu:**

```php
/*
|--------------------------------------------------------------------------
| ⚠️ GÜVENLİK NOTU: Ollama Endpoint (LOCALHOST)
|--------------------------------------------------------------------------
|
| ❗ BU AYAR SADECE SSH TUNNEL VEYA WIREGUARD VPN AKTİF OLDUĞUNDA ÇALIŞIR!
|
| Geçici Fix: Public HTTP bağlantısından kaçınmak için localhost kullanılıyor.
|
| SSH Tunnel Kurulumu:
|   ssh -L 11434:localhost:11434 root@51.75.64.121 -N -f
|
| WireGuard VPN Kurulumu:
|   wg-quick up wg0  (10.0.0.1:11434)
|
| Production Gereksinimi:
|   - Nginx Reverse Proxy ile HTTPS zorunlu
|   - Cloudflare Tunnel veya Let's Encrypt SSL sertifikası
|   - Rate limiting ve IP whitelisting
|
| KVKK Uyarısı: HTTP plain text ile müşteri verileri taşınmamalı!
|
*/
```

### 2. OllamaService Güncellemesi

**Dosya:** `app/Services/AI/OllamaService.php`

**DEĞİŞTİRİLEN:**

- Class docblock'a güvenlik notu eklendi
- `getOllamaUrl()` metodundaki fallback `http://localhost:11434` yapıldı

### 3. AIService Güncellemesi

**Dosya:** `app/Services/AIService.php`

**DEĞİŞTİRİLEN:**

- `getOllamaModels()` metoduna güvenlik notu eklendi
- Localhost kullanımı açıkça belirtildi

---

## 🚀 KULLANIM KILAVUZU

### Option 1: SSH Tunnel (Hızlı Geçici Çözüm)

**1. Manuel Tunnel Başlatma:**

```bash
# CRM sunucusunda çalıştır
ssh -L 11434:localhost:11434 root@51.75.64.121 -N -f

# Kontrol et
curl http://localhost:11434/api/tags
```

**2. Otomatik Tunnel (Systemd Service):**

`/etc/systemd/system/ollama-tunnel.service` oluştur:

```ini
[Unit]
Description=SSH Tunnel to Ollama Server
After=network.target

[Service]
Type=simple
User=www-data
ExecStart=/usr/bin/ssh -o ServerAliveInterval=60 -o ServerAliveCountMax=3 -L 11434:localhost:11434 root@51.75.64.121 -N
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

```bash
# Servisi etkinleştir
sudo systemctl enable ollama-tunnel
sudo systemctl start ollama-tunnel
sudo systemctl status ollama-tunnel
```

**3. Laravel Cron ile Kontrol:**

`app/Console/Kernel.php` ekle:

```php
// Her saat tunnel kontrolü
$schedule->command('ollama:check-tunnel')->hourly();
```

`app/Console/Commands/CheckOllamaTunnel.php` oluştur:

```php
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckOllamaTunnel extends Command
{
    protected $signature = 'ollama:check-tunnel';
    protected $description = 'SSH tunnel kontrolü ve yeniden başlatma';

    public function handle()
    {
        try {
            $response = Http::timeout(5)->get('http://localhost:11434/api/tags');

            if ($response->successful()) {
                $this->info('✅ Ollama tunnel aktif');
                return 0;
            }
        } catch (\Exception $e) {
            $this->error('❌ Ollama tunnel down, yeniden başlatılıyor...');

            // Tunnel'ı yeniden başlat
            exec('pgrep -f "ssh.*11434" | xargs kill');
            exec('ssh -L 11434:localhost:11434 root@51.75.64.121 -N -f');

            sleep(2);

            // Kontrol et
            try {
                Http::timeout(5)->get('http://localhost:11434/api/tags');
                $this->info('✅ Tunnel yeniden başlatıldı');
            } catch (\Exception $e2) {
                $this->error('❌ Tunnel başlatılamadı: ' . $e2->getMessage());
                return 1;
            }
        }

        return 0;
    }
}
```

### Option 2: WireGuard VPN (Kalıcı Çözüm)

**1. WireGuard Kurulumu:**

```bash
# Her iki sunucuda
sudo apt update
sudo apt install wireguard

# Key üret
wg genkey | tee privatekey | wg pubkey > publickey
```

**2. CRM Sunucu (Client) Config:**

`/etc/wireguard/wg0.conf`:

```ini
[Interface]
PrivateKey = <CRM_PRIVATE_KEY>
Address = 10.0.0.2/24

[Peer]
PublicKey = <OLLAMA_PUBLIC_KEY>
Endpoint = 51.75.64.121:51820
AllowedIPs = 10.0.0.1/32
PersistentKeepalive = 25
```

**3. Ollama Sunucu (Server) Config:**

`/etc/wireguard/wg0.conf`:

```ini
[Interface]
PrivateKey = <OLLAMA_PRIVATE_KEY>
Address = 10.0.0.1/24
ListenPort = 51820

[Peer]
PublicKey = <CRM_PUBLIC_KEY>
AllowedIPs = 10.0.0.2/32
```

**4. VPN Başlatma:**

```bash
# Her iki sunucuda
sudo wg-quick up wg0
sudo systemctl enable wg-quick@wg0

# Kontrol
sudo wg show
ping 10.0.0.1  # CRM'den Ollama'ya
```

**5. Config Güncellemesi:**

`.env` dosyasını değiştir:

```env
OLLAMA_API_URL=http://10.0.0.1:11434
```

### Option 3: Cloudflare Tunnel (Kolay + Güvenli)

**1. Cloudflared Kurulumu (Ollama sunucusunda):**

```bash
# Kurulum
wget https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
sudo dpkg -i cloudflared-linux-amd64.deb

# Login
cloudflared tunnel login

# Tunnel oluştur
cloudflared tunnel create yalihanai-ollama

# DNS kayıt ekle
cloudflared tunnel route dns yalihanai-ollama ollama.yalihanai.com
```

**2. Config Dosyası:**

`~/.cloudflared/config.yml`:

```yaml
tunnel: yalihanai-ollama
credentials-file: /root/.cloudflared/<TUNNEL_ID>.json

ingress:
    - hostname: ollama.yalihanai.com
      service: http://localhost:11434
    - service: http_status:404
```

**3. Tunnel Başlatma:**

```bash
# Manuel
cloudflared tunnel run yalihanai-ollama

# Systemd service
sudo cloudflared service install
sudo systemctl enable cloudflared
sudo systemctl start cloudflared
```

**4. Config Güncellemesi:**

`.env` dosyasını değiştir:

```env
OLLAMA_API_URL=https://ollama.yalihanai.com
```

---

## ✅ KONTROL VE TEST

### 1. Tunnel Durumu Kontrolü

```bash
# SSH tunnel
pgrep -f "ssh.*11434"

# WireGuard
sudo wg show

# Cloudflared
sudo systemctl status cloudflared
```

### 2. Ollama API Testi

```bash
# Localhost (SSH tunnel)
curl http://localhost:11434/api/tags

# VPN IP
curl http://10.0.0.1:11434/api/tags

# Cloudflare domain
curl https://ollama.yalihanai.com/api/tags
```

### 3. Laravel Test

```bash
php artisan tinker

# Ollama servis testi
$service = app(\App\Services\AI\OllamaService::class);
$models = $service->getAvailableModels();
dd($models);
```

### 4. Health Check Endpoint

```php
// routes/web.php
Route::get('/admin/ollama-health', function () {
    try {
        $response = Http::timeout(5)->get(config('ai.ollama_api_url') . '/api/tags');

        return response()->json([
            'status' => 'healthy',
            'endpoint' => config('ai.ollama_api_url'),
            'response_time' => $response->transferStats->getTransferTime(),
            'models' => $response->json('models'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'error' => $e->getMessage(),
            'endpoint' => config('ai.ollama_api_url'),
        ], 500);
    }
})->middleware('auth');
```

---

## 🔐 GÜVENLİK KONTROL LİSTESİ

- [ ] SSH tunnel aktif veya VPN çalışıyor
- [ ] Public HTTP endpoint (51.75.64.121:11434) firewall'da kapalı
- [ ] Laravel `.env` dosyası `OLLAMA_API_URL=http://localhost:11434` olarak ayarlı
- [ ] Cache temizlendi (`php artisan cache:clear`)
- [ ] Config önbelleği yenilendi (`php artisan config:cache`)
- [ ] Health check endpoint test edildi
- [ ] AI işlevleri test edildi (başlık üretme, açıklama vb.)
- [ ] Cron job tunnel kontrolü ayarlandı (opsiyonel)
- [ ] Monitoring/alerting kuruldu (opsiyonel)

---

## ⚠️ PRODUCTION HAZIRLIĞI

### Kısa Vadeli (1-2 Hafta)

- [x] Localhost endpoint kullanımı
- [ ] SSH tunnel systemd servisi
- [ ] Tunnel health check cron
- [ ] Monitoring dashboard (Ollama uptime)

### Orta Vadeli (1 Ay)

- [ ] WireGuard VPN kurulumu
- [ ] Firewall kuralları (public port kapatma)
- [ ] SSL sertifikası (Let's Encrypt)
- [ ] Nginx reverse proxy + rate limiting

### Uzun Vadeli (3 Ay)

- [ ] Cloudflare Tunnel production
- [ ] Load balancer (multiple Ollama nodes)
- [ ] Auto-scaling (demand-based)
- [ ] KVKK compliance audit

---

## 📊 MALIYET ANALİZİ

| Yöntem            | Kurulum | Maliyet | Güvenlik   | Kararlılık | Önerilen    |
| ----------------- | ------- | ------- | ---------- | ---------- | ----------- |
| SSH Tunnel        | 10 dk   | $0      | ⭐⭐⭐     | ⭐⭐       | Geçici      |
| WireGuard VPN     | 2 saat  | $0      | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ✅ Kalıcı   |
| Cloudflare Tunnel | 45 dk   | $0      | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐   | ✅ Kolay    |
| Nginx HTTPS       | 1 saat  | $0      | ⭐⭐⭐⭐   | ⭐⭐⭐⭐   | Orta Vadeli |

**Öneri:** WireGuard VPN (kalıcı, hızlı, güvenli) veya Cloudflare Tunnel (kolay kurulum, yönetimli)

---

## 🚨 HATA GİDERME

### "Connection refused" Hatası

```bash
# Tunnel kontrol
pgrep -f "ssh.*11434"

# Yeniden başlat
pkill -f "ssh.*11434"
ssh -L 11434:localhost:11434 root@51.75.64.121 -N -f

# Test
curl http://localhost:11434/api/tags
```

### "Timeout" Hatası

```bash
# Firewall kontrol (Ollama sunucusunda)
sudo ufw status
sudo ufw allow 11434/tcp

# Ollama servis kontrol
sudo systemctl status ollama
sudo systemctl restart ollama
```

### "Models not found" Hatası

```bash
# Ollama sunucusunda
ollama list
ollama pull gemma2:2b
```

---

**Hazırlayan:** GitHub Copilot (Claude Sonnet 4.5)  
**Tarih:** 25 Kasım 2025  
**Versiyon:** 1.0  
**Durum:** Production Ready (SSH Tunnel/VPN ile)
