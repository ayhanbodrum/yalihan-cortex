# 🎯 YALIHAN EMLAK - HIZLI REFERANS KILAVUZU
## AI Asistanları için Özet Rehber

**Versiyon:** 1.0.0  
**Tarih:** 29 Kasım 2025  
**Kullanım:** Hızlı referans ve hatırlatıcı

---

## ⚡ HIZLI KURALLAR

### 1. YASAK PATTERN'LER (Asla Kullanma!)

```php
// ❌ Database
'order' => 1              // → 'display_order' kullan
'enabled' => true         // → 'status' kullan
'is_active' => 1          // → 'status' kullan
'aktif' => 1              // → 'status' kullan
'sehir_id' => 1           // → 'il_id' kullan
'musteri_id' => 1         // → 'kisi_id' kullan

// ❌ CSS
class="neo-btn"           // → Tailwind utilities kullan
class="neo-card"          // → Tailwind utilities kullan
class="btn-primary"       // → Tailwind utilities kullan

// ❌ Routes
->name('admin.admin.x')   // → ->name('admin.x') kullan
```

### 2. ZORUNLU PATTERN'LER (Her Zaman Kullan!)

```html
<!-- Tailwind Transitions (ZORUNLU) -->
class="transition-all duration-200"

<!-- Dark Mode (ZORUNLU) -->
class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white"

<!-- Focus States (ZORUNLU) -->
class="focus:ring-2 focus:ring-blue-500 focus:outline-none"

<!-- Hover Effects (ZORUNLU) -->
class="hover:bg-blue-700 hover:shadow-lg"

<!-- Active States (ZORUNLU) -->
class="active:scale-95"
```

### 3. AI ÇIKTILARI İÇİN ZORUNLU ALANLAR

```php
Schema::create('ai_feature_name', function (Blueprint $table) {
    $table->id();
    $table->string('status')->default('draft'); // ZORUNLU
    $table->text('ai_response')->nullable();
    $table->string('ai_model_used')->nullable();
    $table->string('ai_prompt_version')->nullable();
    $table->timestamp('ai_generated_at')->nullable();
    $table->foreignId('approved_by')->nullable(); // ZORUNLU
    $table->timestamp('approved_at')->nullable(); // ZORUNLU
    $table->timestamps();
});
```

---

## 🎭 PROJE DAVRANIŞI

### AI'nın Rolü
```
AI = Yardımcı (Taslak üretir)
İnsan = Karar Verici (Onaylar/Reddeder)
```

### İş Akışı
```
1. Danışman İsteği
2. AI Taslak Üretir
3. DB'ye Kaydedilir (status: draft)
4. Danışman İnceler/Düzenler
5. Danışman Onaylar
6. İşlem Gerçekleşir
```

### Veri Ayrımı
```
yalihan_market  → Harici ilanlar (Sahibinden, Emlakjet)
yalihan_ai      → AI analizleri, raporlar
Ana DB          → CRM, portföy, müşteriler
```

---

## 💻 KOD ŞABLONLARİ

### Controller

```php
class IlanController extends Controller
{
    public function __construct(
        private IlanService $ilanService
    ) {}
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            'status' => 'required|in:draft,active,sold',
        ]);
        
        $ilan = $this->ilanService->create($validated);
        
        return ResponseService::success([
            'data' => $ilan,
            'message' => 'İlan başarıyla oluşturuldu'
        ]);
    }
}
```

### Model

```php
class Ilan extends Model
{
    protected $fillable = [
        'baslik',
        'status',           // ✅ DOĞRU
        'display_order',    // ✅ DOĞRU
        'il_id',            // ✅ DOĞRU
    ];
    
    protected $casts = [
        'status' => 'string',
        'display_order' => 'integer',
    ];
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
```

### Migration

```php
Schema::create('ilanlar', function (Blueprint $table) {
    $table->id();
    $table->string('baslik');
    $table->string('status')->default('draft'); // ✅ DOĞRU
    $table->integer('display_order')->default(0); // ✅ DOĞRU
    $table->foreignId('il_id')->constrained('iller'); // ✅ DOĞRU
    $table->timestamps();
    
    $table->index('status');
    $table->index('display_order');
});
```

### Button (Tailwind)

```html
<button 
    class="px-4 py-2 bg-blue-600 text-white rounded-lg 
           hover:bg-blue-700 active:scale-95
           transition-all duration-200 
           dark:bg-blue-700 dark:hover:bg-blue-800
           focus:ring-2 focus:ring-blue-500 focus:outline-none"
>
    Kaydet
</button>
```

### Input (Tailwind)

```html
<input 
    type="text"
    class="w-full px-4 py-2.5 
           border border-gray-300 rounded-lg 
           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
           transition-all duration-200
           dark:bg-gray-800 dark:border-gray-700 dark:text-white"
    placeholder="Ad Soyad"
/>
```

### Select (Tailwind)

```html
<select 
    class="w-full px-4 py-2.5 
           border border-gray-300 rounded-lg 
           focus:ring-2 focus:ring-blue-500
           transition-all duration-200
           dark:bg-gray-900 dark:border-gray-700 dark:text-white"
    style="color-scheme: light dark;"
>
    <option value="">Seçiniz</option>
    <option value="1">Seçenek 1</option>
</select>
```

---

## 🔍 KONTROL LİSTESİ

Kod yazmadan önce kontrol et:

- [ ] Context7 kurallarına uygun mu?
- [ ] Yasaklı pattern kullanılmış mı?
- [ ] `status` field kullanıldı mı? (`enabled` değil)
- [ ] `display_order` kullanıldı mı? (`order` değil)
- [ ] `il_id` kullanıldı mı? (`sehir_id` değil)
- [ ] Pure Tailwind kullanıldı mı? (Neo Design değil)
- [ ] Dark mode variant'ları var mı?
- [ ] Transition'lar eklenmiş mi?
- [ ] Focus state'ler var mı?
- [ ] AI çıktıları için onay mekanizması var mı?
- [ ] Service layer kullanılmış mı?
- [ ] Response format standartlara uygun mu?

---

## 🚀 HIZLI KOMUTLAR

```bash
# Sunucular
php artisan serve
node mcp-servers/yalihan-bekci-mcp.js
node mcp-servers/context7-validator-mcp.js

# Database
php artisan migrate
php artisan db:seed

# Cache
php artisan cache:clear
php artisan config:clear

# Validation
grep -r "order\|enabled\|aktif" --include="*.php" app/
grep -r "neo-btn\|neo-card" resources/views/

# Code Quality
./vendor/bin/phpstan analyse
./vendor/bin/pint --test
```

---

## 📚 DOKÜMANTASYON

### Öncelikli Okuma
1. `AI_EGITIM_GEMINI_CHATGPT.md` - Ana eğitim dokümanı
2. `.context7/authority.json` - Context7 kuralları
3. `MASTER_PROMPT_YALIHAN_EMLAK_AI.md` - AI master prompt

### Detaylı Referans
- `docs/FORM_STANDARDS.md` - Form standartları
- `YALIHAN_BEKCI_EGITIM_DOKUMANI.md` - Yalıhan Bekçi eğitimi
- `docs/modules/` - Modül detayları

---

## 🎯 MANTRA

> **"Context7'ye uy, AI sadece yardımcı, son söz insanda!"**

---

**Son Güncelleme:** 29 Kasım 2025  
**Versiyon:** 1.0.0
