# TestSprite MCP

TestSprite MCP, Laravel projelerinde migration ve seeder dosyalarının senkronizasyonunu ve kod standartlarına uygunluğunu test eden bir MCP (Memory, Context, Processing) sistemidir.

## Özellikler

- Migration ve seeder dosyalarının modül yapısına uygunluğunu kontrol eder
- Semantic versioning kurallarına uygunluğu denetler
- Kod standartlarını (PSR-12, Vue Composition API, Blade strict mode) kontrol eder
- Güvenlik politikalarını (env dosyaları, API keyler, şifreleme) denetler
- CSS çakışmalarını tespit eder
- Değişiklikleri changelog ile kaydeder
- Otomatik düzeltme önerileri sunar

## Kullanım

### Komut Satırı Arayüzü

TestSprite MCP'yi komut satırından çalıştırmak için:

```bash
php artisan testsprite:run
```

#### Parametreler

- `--type`: Test türü (all, migrations, seeders)
- `--report`: Rapor türü (summary, detailed, changelog)

Örnek:

```bash
# Sadece migration testlerini çalıştır
php artisan testsprite:run --type=migrations

# Detaylı rapor oluştur
php artisan testsprite:run --report=detailed

# Sadece seeder testlerini çalıştır ve changelog raporu oluştur
php artisan testsprite:run --type=seeders --report=changelog
```

### Raporlar

TestSprite MCP üç farklı rapor türü sunar:

1. **Summary**: Genel test sonuçlarını içeren özet rapor
2. **Detailed**: Tüm test sonuçlarını ve hata detaylarını içeren kapsamlı rapor
3. **Changelog**: Son çalıştırmadan bu yana yapılan değişiklikleri içeren rapor

Raporlar `storage/app/testsprite/reports` dizininde saklanır.

### Otomatik Düzeltme

TestSprite MCP, tespit ettiği basit hataları otomatik olarak düzeltebilir. Bu özelliği etkinleştirmek için:

```php
// config/testsprite.php
'auto_correct' => true,
```

veya `.env` dosyasında:

```
TESTSPRITE_AUTO_CORRECT=true
```

### Zamanlama

TestSprite MCP testlerini otomatik olarak çalıştırmak için Laravel'in zamanlayıcısını kullanabilirsiniz:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('testsprite:run')->dailyAt('03:00');
}
```

## Örnek Kullanım Senaryoları

### Senaryo 1: Migration Dosyalarının Kontrolü

Yeni bir migration dosyası oluşturduğunuzda, TestSprite MCP şunları kontrol eder:

- Dosya doğru modül dizininde mi?
- Semantic versioning kurallarına uygun mu?
- PSR-12 kod standartlarına uygun mu?

```bash
php artisan testsprite:run --type=migrations
```

### Senaryo 2: Seeder Senkronizasyonu

Seeder dosyalarının migration'larla senkronize olup olmadığını kontrol etmek için:

```bash
php artisan testsprite:run --type=seeders
```

### Senaryo 3: Kod Kalitesi Kontrolü

Tüm testleri çalıştırarak kod kalitesini kontrol etmek için:

```bash
php artisan testsprite:run --report=detailed
```

### Senaryo 4: Pre-commit Hook Entegrasyonu

Git pre-commit hook'una TestSprite MCP testlerini ekleyerek, commit öncesi kontrol yapabilirsiniz:

```bash
#!/bin/sh
# .git/hooks/pre-commit

echo "TestSprite MCP testleri çalıştırılıyor..."
php artisan testsprite:run
if [ $? -ne 0 ]; then
    echo "TestSprite MCP testleri başarısız oldu!"
    exit 1
fi
```

## Hata Kodları ve Çözümleri

TestSprite MCP, tespit ettiği hataları aşağıdaki kodlarla raporlar:

- `M001`: Migration dosyası yanlış dizinde
- `M002`: Semantic versioning hatası
- `M003`: PSR-12 kod stili hatası
- `S001`: Seeder dosyası yanlış dizinde
- `S002`: afterLastBatch metodu eksik
- `S003`: Seeder bağımlılık hatası
- `C001`: CSS çakışması tespit edildi
- `SEC001`: Güvenlik politikası ihlali

Her hata kodu için önerilen çözümler raporda detaylı olarak açıklanır.