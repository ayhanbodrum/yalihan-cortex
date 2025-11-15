# 🤖 EmlakPro Copilot Prompt Rehberi

## 📋 Context7 Uyumlu Kod İsteme

### Model Oluşturma

```
"Context7 kurallarına uygun bir Property model oluştur.
status field kullan, il_id relationship ekle, backward compatibility accessor'ları dahil et"
```

### Migration Oluşturma

```
"Context7 uyumlu properties tablosu migration'ı oluştur.
status yerine status, il_id yerine il_id kullan"
```

### Controller Oluşturma

```
"Context7 uyumlu PropertyController oluştur.
Neo Design System view'ları kullan, Context7 field mapping ekle"
```

### Blade Template

```
"Neo Design System kullanarak property list blade template oluştur.
neo-card, neo-btn sınıfları kullan, Context7 field'ları göster"
```

## 🔧 Kod Düzeltme İsteme

### Legacy Code Fix

```
"Bu kodu Context7 standartlarına uygun hale getir:
- status → status
- btn-primary → neo-btn neo-btn--primary
- il() → il()
Backward compatibility accessor'ları ekle"
```

### CSS Class Fix

```
"Bu Blade template'teki tüm Bootstrap sınıflarını
Neo Design System sınıflarına çevir"
```

### Database Query Fix

```
"Bu sorguları Context7 uyumlu hale getir:
User::where('status', 'aktif') → User::where('status', 'active')"
```

## 🚀 Feature Development

### Live Search Component

```
"Context7 Live Search API kullanan bir kişi arama component'i oluştur.
300ms debounce, /api/hybrid-search/kisiler endpoint'i kullan"
```

### Modal System

```
"Neo Design System ile 'Yoksa Ekle' modal sistemi oluştur.
Context7 uyumlu form validation dahil et"
```

## 📊 System Integration

### AI Service Integration

```
"Context7 uyumlu AI service oluştur.
config/ai.php ayarlarını kullan, 5 provider desteği ekle"
```

### API Endpoint

```
"Context7 uyumlu RESTful API endpoint oluştur.
Sanctum auth, rate limiting, proper JSON response format"
```

## 🎯 Best Practices

### Error Handling

```
"Context7 standardına uygun error handling sistemi oluştur.
Neo Design System alert component'leri kullan"
```

### Performance Optimization

```
"Bu kod bloğunu Context7 kurallarına uygun şekilde optimize et.
Eager loading, caching, indexing dahil et"
```

## 🛡️ Security & Validation

### Form Validation

```
"Context7 uyumlu form validation rules oluştur.
Laravel validation + Neo Design System error display"
```

### CSRF Protection

```
"Bu AJAX isteğine Context7 uyumlu CSRF koruması ekle"
```

## 📱 UI/UX Components

### Responsive Card

```
"Neo Design System ile responsive property card component oluştur.
Mobile-first, accessibility uyumlu"
```

### Navigation Menu

```
"Context7 uyumlu admin navigation menu oluştur.
Role-based access control dahil et"
```

---

## 💡 Pro Tips

1. **Spesifik Ol**: "Context7 uyumlu" kelimesini her zaman kullan
2. **Backward Compatibility**: Eski kod uyumluluğunu her zaman iste
3. **Neo Design System**: UI için mutlaka belirt
4. **Performance**: Cache, eager loading gibi optimizasyonları dahil et
5. **Security**: Sanctum, CSRF, validation'ı unutma

## 🚨 Yasaklı Patterns

Copilot'a ASLA bunları söyleme:

- "Bootstrap kullan"
- "jQuery ekle"
- "status field oluştur"
- "is_active boolean ekle"
- "il_id relationship oluştur"

Bu patterns Context7 kurallarına aykırıdır!
