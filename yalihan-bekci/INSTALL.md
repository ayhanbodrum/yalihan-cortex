# TestSprite MCP Kurulum Talimatları

Bu belge, TestSprite MCP'nin Laravel projenize nasıl kurulacağını adım adım açıklar.

## Ön Gereksinimler

- PHP 7.4 veya üzeri
- Laravel 8.0 veya üzeri
- Node.js 14.0 veya üzeri
- NPM 6.0 veya üzeri

## Kurulum Adımları

### 1. TestSprite MCP Dosyalarını Kopyalama

TestSprite MCP dosyalarını projenize kopyalayın:

```bash
# Projenizin kök dizininde
mkdir -p testsprite
cp -r /path/to/testsprite/* testsprite/
```

### 2. Node.js Bağımlılıklarını Kurma

TestSprite MCP sunucusu için gerekli Node.js bağımlılıklarını kurun:

```bash
cd testsprite/server
npm install
```

### 3. Laravel Entegrasyonunu Yapılandırma

#### 3.1. Service Provider'ı Kaydetme

`config/app.php` dosyasındaki `providers` dizisine TestSprite ServiceProvider'ı ekleyin:

```php
'providers' => [
    // Diğer servis sağlayıcılar...
    App\Providers\TestSpriteServiceProvider::class,
],
```

#### 3.2. Yapılandırma Dosyasını Yayınlama

Aşağıdaki komutu çalıştırarak TestSprite yapılandırma dosyasını yayınlayın:

```bash
php artisan vendor:publish --provider="App\Providers\TestSpriteServiceProvider" --tag="config"
```

### 4. Çevre Değişkenlerini Ayarlama

`.env` dosyanıza aşağıdaki değişkenleri ekleyin:

```
TESTSPRITE_SERVER_URL=http://localhost:3333
TESTSPRITE_NODE_PATH=/usr/local/bin/node
TESTSPRITE_AUTO_CORRECT=false
TESTSPRITE_NOTIFICATIONS_ENABLED=true
```

### 5. Komut Dosyasını Kaydetme

`RunTestSpriteCommand.php` dosyasını `app/Console/Commands/` dizinine kopyalayın:

```bash
cp testsprite/integration/RunTestSpriteCommand.php app/Console/Commands/
```

### 6. Servis Sınıfını Kaydetme

`TestSpriteService.php` dosyasını `app/Services/MCP/` dizinine kopyalayın:

```bash
mkdir -p app/Services/MCP
cp testsprite/integration/TestSpriteService.php app/Services/MCP/
```

### 7. Zamanlayıcıyı Ayarlama (İsteğe Bağlı)

TestSprite MCP'yi düzenli olarak çalıştırmak için `app/Console/Kernel.php` dosyasındaki `schedule` metodunu düzenleyin:

```php
protected function schedule(Schedule $schedule)
{
    // Diğer zamanlanmış görevler...
    $schedule->command('testsprite:run')->dailyAt('03:00');
}
```

### 8. Pre-commit Hook Kurulumu (İsteğe Bağlı)

Git pre-commit hook'u ekleyerek, her commit öncesi TestSprite MCP testlerinin çalışmasını sağlayabilirsiniz:

```bash
cat > .git/hooks/pre-commit << 'EOL'
#!/bin/sh

echo "TestSprite MCP testleri çalıştırılıyor..."
php artisan testsprite:run --type=migrations
if [ $? -ne 0 ]; then
    echo "TestSprite MCP testleri başarısız oldu!"
    exit 1
fi
EOL

chmod +x .git/hooks/pre-commit
```

## Kurulumu Doğrulama

Kurulumu doğrulamak için aşağıdaki komutu çalıştırın:

```bash
php artisan testsprite:run
```

Eğer her şey doğru yapılandırıldıysa, TestSprite MCP sunucusu başlayacak ve testler çalışacaktır.

## Sorun Giderme

### TestSprite MCP Sunucusu Başlatılamıyor

Eğer TestSprite MCP sunucusu başlatılamazsa:

1. Node.js'in doğru şekilde kurulu olduğunu kontrol edin:

    ```bash
    node --version
    ```

2. `.env` dosyasındaki `TESTSPRITE_NODE_PATH` değişkeninin doğru olduğunu kontrol edin:

    ```bash
    which node
    ```

3. TestSprite sunucu dizininde bağımlılıkların kurulu olduğunu kontrol edin:
    ```bash
    cd testsprite/server
    npm list
    ```

### Testler Çalışmıyor

Eğer testler çalışmıyorsa:

1. Laravel log dosyalarını kontrol edin:

    ```bash
    tail -f storage/logs/laravel.log
    ```

2. TestSprite MCP sunucusunun log dosyalarını kontrol edin:

    ```bash
    cat testsprite/server/logs/server.log
    ```

3. Yapılandırma dosyasının doğru olduğunu kontrol edin:
    ```bash
    php artisan config:show testsprite
    ```

## Güncelleme

TestSprite MCP'yi güncellemek için:

1. Eski dosyaları yedekleyin:

    ```bash
    mv testsprite testsprite.bak
    ```

2. Yeni dosyaları kopyalayın:

    ```bash
    cp -r /path/to/new/testsprite testsprite
    ```

3. Node.js bağımlılıklarını güncelleyin:

    ```bash
    cd testsprite/server
    npm install
    ```

4. Yapılandırma dosyasını yeniden yayınlayın:
    ```bash
    php artisan vendor:publish --provider="App\Providers\TestSpriteServiceProvider" --tag="config" --force
    ```
