# 📊 EmlakPro Page Analyzer

Bu tool, Laravel admin sayfalarını otomatik olarak analiz eder ve raporlar sunar.

## 🎯 Özellikler

### 1. **Static Code Analysis**
- Controller boşluk analizi
- Route coverage analizi  
- View consistency kontrolü
- Context7 compliance check

### 2. **UI/UX Analysis**
- Design system tutarlılığı
- Component kullanım analizi
- Accessibility audit
- Performance metrics

### 3. **Database Schema Analysis**
- Migration consistency
- Relationship validation
- Index optimization önerileri
- N+1 query detection

### 4. **Real-time Monitoring**
- Page load times
- User interaction tracking
- Error rate monitoring
- Feature usage analytics

## 🚀 Kullanım

```bash
# Tüm admin sayfalarını analiz et
php artisan analyze:pages

# Belirli sayfayı analiz et
php artisan analyze:pages --page=my-listings

# Rapor oluştur
php artisan analyze:report --format=html
```

## 📈 Çıktı Örnekleri

### Analiz Raporu
```
📊 EmlakPro Page Analysis Report
===============================

🔴 CRITICAL ISSUES (3)
- MyListingsController: Controller completely empty
- AnalyticsController: No implementation found
- NotificationController: Missing CRUD methods

⚠️ WARNING ISSUES (5)
- AdresYonetimiController: Schema mismatch in iller table
- TelegramBotController: Missing analytics features

✅ HEALTHY PAGES (2)
- TelegramBot: 8/10 score
- Dashboard: 9/10 score

💡 RECOMMENDATIONS
1. Implement missing controllers (Priority: Critical)
2. Add schema migrations (Priority: High)
3. Enhance monitoring (Priority: Medium)
```

## 🛠️ Teknolojiler

- **Backend**: PHP 8.2 + Laravel 10
- **Frontend**: Vue.js 3 + Chart.js
- **Analysis**: PHPStan + Custom Rules
- **Reporting**: PDF/HTML export
- **Monitoring**: Real-time WebSocket
