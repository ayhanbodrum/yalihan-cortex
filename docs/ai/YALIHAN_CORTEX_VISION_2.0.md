# 🧠 YALIHAN CORTEX VISION 2.0 - YENİ ZEKÂ GÖREVLERİ

**Tarih:** 26 Kasım 2025  
**Versiyon:** 2.0  
**Context7 Standardı:** C7-YALIHAN-CORTEX-V2-2025-11-26

---

## 📋 ÖNERİLEN 6 YENİ GÖREV

### 1. 🎯 FIRSAT SENTEZİ (Opportunity Synthesis)

**Mevcut Durum:**
- Churn riski yüksekse uyarıyoruz
- Eşleşme varsa öneriyoruz
- **Sorun:** İki bilgi birleştirilmiyor

**Cortex'in Yapması Gereken:**
Bir ilan girildiğinde, Cortex o ilana uygun ve aynı zamanda Churn Riski Yüksek (Hemen Aranması Gereken) müşterileri filtreleyip **"Acil Satış Fırsatı"** raporu üretmeli.

**Formül:**
```
Harekete Geçme Puanı = (Match Skoru × 0.6) + (Churn Skoru × 0.4)
```

**Implementasyon:**
```php
public function findUrgentOpportunities(Ilan $ilan): array
{
    // 1. İlana uygun talepleri bul (SmartPropertyMatcherAI)
    $matches = $this->propertyMatcher->match($ilan);
    
    // 2. Her eşleşme için churn riski hesapla
    $opportunities = [];
    foreach ($matches as $match) {
        $talep = Talep::find($match['talep_id']);
        $churnRisk = $this->churnService->calculateChurnRisk($talep->kisi);
        
        // 3. Acil fırsat skoru hesapla
        $urgencyScore = ($match['score'] * 0.6) + ($churnRisk['score'] * 0.4);
        
        if ($urgencyScore >= 70) { // Eşik değer
            $opportunities[] = [
                'kisi_id' => $talep->kisi_id,
                'talep_id' => $talep->id,
                'match_score' => $match['score'],
                'churn_score' => $churnRisk['score'],
                'urgency_score' => $urgencyScore,
                'recommendation' => 'Acil arama yapılmalı - Yüksek churn riski + Mükemmel eşleşme',
                'action_items' => [
                    'Hemen telefon et',
                    'Özel teklif hazırla',
                    'VIP muamele göster',
                ],
            ];
        }
    }
    
    // 4. Skora göre sırala
    usort($opportunities, fn($a, $b) => $b['urgency_score'] <=> $a['urgency_score']);
    
    return $opportunities;
}
```

**Dashboard Widget:**
- "🔥 Acil Fırsatlar" kartı
- Top 5 acil fırsat listesi
- Her fırsat için "Hemen Ara" butonu

---

### 2. 💰 AKILLI BÜTÇE DÜZELTMESİ (Budget Correction)

**Mevcut Durum:**
- Müşteri 5-7 Milyon TL talep ettiyse, bu aralığa bakıyoruz
- **Sorun:** Gerçek satın alma gücü analiz edilmiyor

**Cortex'in Yapması Gereken:**
Müşterinin gerçek satın alma gücünü analiz edip, bütçeyi revize etmeyi danışmana önermek.

**Veri Kaynakları:**
- `Kisi.gelir_duzeyi`
- `Kisi.meslek`
- `Kisi.pipeline_stage`
- `Kisi.segment`
- `Talep.min_fiyat`, `Talep.max_fiyat`

**Implementasyon:**
```php
public function analyzeBudgetReality(Talep $talep): array
{
    $kisi = $talep->kisi;
    
    // 1. Gelir düzeyi analizi
    $gelirDuzeyi = $kisi->gelir_duzeyi;
    $meslek = $kisi->meslek;
    
    // 2. AI ile finansal gerçeklik analizi
    $prompt = "Müşteri profili:
- Gelir Düzeyi: {$gelirDuzeyi}
- Meslek: {$meslek}
- Talep Bütçe: {$talep->min_fiyat} - {$talep->max_fiyat} TL
- Segment: {$kisi->segment}

Bu müşterinin gerçek satın alma gücü nedir? Talep bütçesi gerçekçi mi? 
Eğer gerçek gücü daha yüksekse, önerilen bütçe aralığını belirt.";
    
    $aiAnalysis = $this->aiService->analyze($prompt, [
        'context' => 'budget_reality_check',
        'model' => 'gpt-4',
    ]);
    
    // 3. Bütçe revizyon önerisi
    $suggestedMin = $aiAnalysis['suggested_min_budget'] ?? $talep->min_fiyat;
    $suggestedMax = $aiAnalysis['suggested_max_budget'] ?? $talep->max_fiyat;
    
    return [
        'current_budget' => [
            'min' => $talep->min_fiyat,
            'max' => $talep->max_fiyat,
        ],
        'suggested_budget' => [
            'min' => $suggestedMin,
            'max' => $suggestedMax,
        ],
        'reality_score' => $aiAnalysis['reality_score'] ?? 0,
        'recommendation' => $aiAnalysis['recommendation'] ?? '',
        'confidence' => $aiAnalysis['confidence'] ?? 0,
    ];
}
```

**Kullanım Senaryosu:**
- Talep oluşturulduğunda otomatik analiz
- Dashboard'da "💰 Bütçe Revizyon Önerisi" badge'i
- Danışmana bildirim: "Müşterinin gerçek gücü 10 Milyon TL, 7 Milyon'a odaklanmayın"

---

### 3. 🗺️ PAZAR HAKİMİYETİ ANALİZİ (Competitor Mapping)

**Mevcut Durum:**
- Kendi ilanlarımızın fiyat geçmişini biliyoruz
- **Sorun:** Rakip analizi yok

**Cortex'in Yapması Gereken:**
Rakip analizi yaparak danışmana ne kadar indirim yapması gerektiğini söylemek.

**Veri Kaynakları:**
- n8n ile dış veri çekimi (Sahibinden, Emlakjet, vb.)
- Kendi ilan veritabanı
- Fiyat geçmişi

**Implementasyon:**
```php
public function analyzeMarketCompetition(Ilan $ilan): array
{
    // 1. n8n'den rakip ilanları çek
    $competitors = $this->fetchCompetitorsFromN8n($ilan);
    
    // 2. En yakın 3 rakibi bul (lokasyon + özellik benzerliği)
    $topCompetitors = $this->findTopCompetitors($ilan, $competitors, 3);
    
    // 3. Fiyat karşılaştırması
    $ourPrice = $ilan->fiyat;
    $avgCompetitorPrice = array_sum(array_column($topCompetitors, 'fiyat')) / count($topCompetitors);
    $priceDifference = (($ourPrice - $avgCompetitorPrice) / $avgCompetitorPrice) * 100;
    
    // 4. Öneri hesapla
    $recommendation = $this->calculatePriceRecommendation($priceDifference, $topCompetitors);
    
    return [
        'our_price' => $ourPrice,
        'market_avg' => $avgCompetitorPrice,
        'price_difference_percent' => round($priceDifference, 2),
        'top_competitors' => $topCompetitors,
        'recommendation' => $recommendation,
        'map_data' => $this->generateMapData($ilan, $topCompetitors),
    ];
}

private function calculatePriceRecommendation(float $priceDiff, array $competitors): array
{
    if ($priceDiff > 15) {
        return [
            'action' => 'reduce',
            'percentage' => 5,
            'message' => 'Piyasaya göre %' . round($priceDiff) . ' pahalısınız. %5 indirimle satılabilir.',
            'urgency' => 'high',
        ];
    } elseif ($priceDiff > 10) {
        return [
            'action' => 'consider',
            'percentage' => 3,
            'message' => 'Piyasaya göre %' . round($priceDiff) . ' pahalısınız. %3 indirim düşünülebilir.',
            'urgency' => 'medium',
        ];
    } else {
        return [
            'action' => 'maintain',
            'percentage' => 0,
            'message' => 'Fiyatınız piyasa ortalamasına yakın. Mevcut fiyatı koruyabilirsiniz.',
            'urgency' => 'low',
        ];
    }
}
```

**Dashboard Widget:**
- Harita üzerinde rakip ilanlar (3 adet)
- Fiyat karşılaştırma grafiği
- "💡 Öneri: %5 indirim yapın" bildirimi

---

### 4. ⚖️ OTOMATİK HUKUKİ KONTROL (Contract Guard)

**Mevcut Durum:**
- Sözleşme taslakları hazır
- **Sorun:** Risk kontrolü manuel

**Cortex'in Yapması Gereken:**
Sözleşme oluşturulurken riskleri anında tespit etmek.

**Kontrol Noktaları:**
1. İmar durumu (TKGM)
2. Tapu değeri vs. sözleşme fiyatı (Vergi riski)
3. Mülkiyet durumu
4. Yasal kısıtlamalar

**Implementasyon:**
```php
public function checkContractRisks(Ilan $ilan, array $contractData): array
{
    $risks = [];
    
    // 1. İmar durumu kontrolü
    $imarStatus = $this->tkgmService->getImarStatus($ilan->ada_no, $ilan->parsel_no);
    if ($imarStatus['risk_level'] === 'high') {
        $risks[] = [
            'type' => 'imar_risk',
            'severity' => 'high',
            'message' => 'İmar durumu riskli: ' . $imarStatus['status'],
            'recommendation' => 'Hukuk ekibine danışın',
        ];
    }
    
    // 2. Fiyat farkı kontrolü
    $tapuDegeri = $this->tkgmService->getTapuDegeri($ilan->ada_no, $ilan->parsel_no);
    $contractPrice = $contractData['fiyat'];
    $priceDifference = abs($contractPrice - $tapuDegeri) / $tapuDegeri * 100;
    
    if ($priceDifference > 30) {
        $risks[] = [
            'type' => 'price_discrepancy',
            'severity' => 'high',
            'message' => "Sözleşme fiyatı tapu değerinden %{$priceDifference} farklı. Vergi riski var.",
            'recommendation' => 'Fiyat farkını gerekçelendirin veya düzeltin',
        ];
    }
    
    // 3. AnythingLLM'e gönder (Yalihan Hukuk workspace)
    if (!empty($risks)) {
        $legalAnalysis = $this->sendToLegalAI($ilan, $contractData, $risks);
        $risks[] = [
            'type' => 'legal_ai_analysis',
            'severity' => $legalAnalysis['risk_level'],
            'message' => $legalAnalysis['summary'],
            'recommendation' => $legalAnalysis['recommendation'],
        ];
    }
    
    return [
        'has_risks' => !empty($risks),
        'risk_count' => count($risks),
        'risks' => $risks,
        'overall_risk_level' => $this->calculateOverallRisk($risks),
    ];
}

private function sendToLegalAI(Ilan $ilan, array $contractData, array $risks): array
{
    $prompt = "Emlak sözleşmesi risk analizi:
    
İlan: {$ilan->baslik}
Fiyat: {$contractData['fiyat']} TL
Lokasyon: {$ilan->il->il_adi} / {$ilan->ilce->ilce_adi}

Tespit Edilen Riskler:
" . json_encode($risks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "

Bu sözleşme için yasal risk var mı? Önerileriniz nelerdir?";

    return $this->aiService->analyze($prompt, [
        'context' => 'legal_contract_review',
        'workspace' => 'yalihan-hukuk',
        'model' => 'claude-3-sonnet', // Hukuki analiz için Claude daha iyi
    ]);
}
```

**Kullanım Senaryosu:**
- Pipeline "Sözleşme Hazırlanıyor" aşamasına geçtiğinde otomatik tetiklenir
- Risk varsa danışmana bildirim
- AnythingLLM'e gönderilir, hukuk ekibi onaylar

---

### 5. 😊 MÜŞTERİ HİS ANALİZİ (Sentiment Analysis)

**Mevcut Durum:**
- `KisiEtkilesim` tablosuna görüşme notları kaydediliyor
- **Sorun:** Duygusal durum analiz edilmiyor

**Cortex'in Yapması Gereken:**
Görüşme notlarını analiz edip müşterinin duygusal durumunu çıkarmak.

**Implementasyon:**
```php
public function analyzeCustomerSentiment(int $kisiId): array
{
    // 1. Son 10 etkileşimi al
    $etkilesimler = KisiEtkilesim::where('kisi_id', $kisiId)
        ->aktif()
        ->sonEtkilesimler(10)
        ->get();
    
    // 2. Notları birleştir
    $allNotes = $etkilesimler->pluck('notlar')->filter()->implode(' ');
    
    if (empty($allNotes)) {
        return [
            'sentiment_score' => 0,
            'sentiment_label' => 'neutral',
            'confidence' => 0,
            'message' => 'Yeterli veri yok',
        ];
    }
    
    // 3. AI ile sentiment analizi
    $prompt = "Müşteri görüşme notları:
    
{$allNotes}

Bu notlara göre müşterinin duygusal durumu nedir?
- Pozitif mi, Negatif mi, Tereddütlü mü?
- Skor: 0-100 (0=çok negatif, 100=çok pozitif)
- Güven skoru: 0-100";

    $analysis = $this->aiService->analyze($prompt, [
        'context' => 'sentiment_analysis',
        'model' => 'gpt-4',
    ]);
    
    $sentimentScore = $analysis['sentiment_score'] ?? 50;
    $sentimentLabel = $this->classifySentiment($sentimentScore);
    
    // 4. Eğer negatifse uyarı
    $alert = null;
    if ($sentimentScore < 40) {
        $alert = [
            'type' => 'negative_sentiment',
            'message' => '⚠️ Müşteri duygusal durumu negatif! Hemen müdahale edin.',
            'urgency' => 'high',
            'recommendation' => $analysis['recommendation'] ?? 'Müşteriyle özel görüşme yapın',
        ];
    }
    
    return [
        'kisi_id' => $kisiId,
        'sentiment_score' => $sentimentScore,
        'sentiment_label' => $sentimentLabel,
        'confidence' => $analysis['confidence'] ?? 0,
        'trend' => $this->calculateSentimentTrend($etkilesimler),
        'alert' => $alert,
        'key_phrases' => $analysis['key_phrases'] ?? [],
    ];
}

private function classifySentiment(float $score): string
{
    if ($score >= 70) return 'positive';
    if ($score >= 40) return 'neutral';
    return 'negative';
}

private function calculateSentimentTrend($etkilesimler): string
{
    // Son 5 vs önceki 5 etkileşim karşılaştırması
    // Basitleştirilmiş versiyon
    return 'stable'; // 'improving', 'declining', 'stable'
}
```

**Kullanım Senaryosu:**
- Her etkileşim kaydedildiğinde otomatik analiz (Job ile)
- Dashboard'da sentiment badge'i (😊 Pozitif / 😐 Nötr / 😞 Negatif)
- Negatif sentiment anında danışmana bildirim

---

### 6. 🌍 ÇOK DİLLİ İÇERİK MÜKEMMELLİĞİ (Multi-Language Content Excellence)

**Mevcut Durum:**
- AIService ile içerik üretimi yapılabiliyor
- **Sorun:** Basit çeviri, kültürel lokalizasyon yok

**Cortex'in Yapması Gereken:**
Yabancı dil ilan açıklaması üretirken kültürel lokalizasyon yapmak.

**Desteklenen Diller:**
- İngilizce (English)
- Rusça (Russian)
- Arapça (Arabic)
- Almanca (German) - Gelecek

**Implementasyon:**
```php
public function generateLocalizedContent(Ilan $ilan, string $targetLanguage, string $targetCountry = null): array
{
    // 1. Temel ilan verilerini al
    $baseData = [
        'baslik' => $ilan->baslik,
        'aciklama' => $ilan->aciklama,
        'fiyat' => $ilan->fiyat,
        'lokasyon' => "{$ilan->il->il_adi} / {$ilan->ilce->ilce_adi}",
        'ozellikler' => $ilan->getOzelliklerArray(),
    ];
    
    // 2. Kültürel lokalizasyon prompt'u oluştur
    $prompt = $this->buildLocalizationPrompt($baseData, $targetLanguage, $targetCountry);
    
    // 3. AI ile lokalize içerik üret
    $localizedContent = $this->aiService->generate($prompt, [
        'context' => 'localized_listing',
        'language' => $targetLanguage,
        'model' => 'gpt-4', // Çok dilli için GPT-4 en iyi
        'temperature' => 0.7, // Yaratıcılık için
    ]);
    
    // 4. SEO kelimeleri ekle
    $seoKeywords = $this->getSEOKeywords($targetLanguage, $targetCountry, $ilan);
    $localizedContent['seo_keywords'] = $seoKeywords;
    
    // 5. Kültürel vurgular ekle
    $culturalHighlights = $this->addCulturalHighlights($localizedContent, $targetLanguage, $targetCountry);
    
    return [
        'language' => $targetLanguage,
        'country' => $targetCountry,
        'title' => $localizedContent['title'],
        'description' => $localizedContent['description'],
        'seo_keywords' => $seoKeywords,
        'cultural_highlights' => $culturalHighlights,
        'confidence' => $localizedContent['confidence'] ?? 0,
    ];
}

private function buildLocalizationPrompt(array $data, string $language, ?string $country): string
{
    $culturalContext = $this->getCulturalContext($language, $country);
    
    return "Emlak ilanı içeriği oluştur:
    
Dil: {$language}
Hedef Ülke: {$country ?? 'Genel'}
Kültürel Bağlam: {$culturalContext}

Orijinal İçerik:
" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "

Görev:
1. Sadece çeviri yapma, kültürel lokalizasyon yap
2. Hedef ülkenin kültürüne uygun vurgular ekle
3. SEO için popüler kelimeler kullan
4. O ülkenin emlak tercihlerine göre özellikleri öne çıkar

Örnek:
- Arapça için: 'Bereketli', 'Huzurlu', 'Aile dostu' vurguları
- Rusça için: 'Lüks', 'Güvenli', 'Yatırım değeri' vurguları
- İngilizce için: 'Investment opportunity', 'Prime location' vurguları";
}

private function getCulturalContext(string $language, ?string $country): string
{
    $contexts = [
        'ar' => [
            'general' => 'Arap kültüründe aile, huzur ve bereket önemlidir. Dini mekanlara yakınlık vurgulanmalı.',
            'saudi' => 'Suudi Arabistan pazarı için lüks ve konfor ön planda.',
            'uae' => 'BİAE pazarı için yatırım potansiyeli ve modern yaşam vurgulanmalı.',
        ],
        'ru' => [
            'general' => 'Rus pazarı için güvenlik, lüks ve yatırım değeri önemlidir.',
            'moscow' => 'Moskova pazarı için prestij ve konum kritik.',
        ],
        'en' => [
            'general' => 'İngiliz pazarı için yatırım fırsatı, lokasyon ve kira getirisi önemlidir.',
            'uk' => 'İngiltere pazarı için Brexit sonrası yatırım fırsatları vurgulanmalı.',
        ],
    ];
    
    $key = $country ? strtolower($country) : 'general';
    return $contexts[$language][$key] ?? $contexts[$language]['general'] ?? '';
}

private function getSEOKeywords(string $language, ?string $country, Ilan $ilan): array
{
    // Dil ve ülkeye özel SEO kelimeleri
    $keywords = [
        'ar' => ['عقار', 'استثمار', 'شقة', 'فيلا', 'تركيا'],
        'ru' => ['недвижимость', 'инвестиции', 'квартира', 'вилла', 'Турция'],
        'en' => ['property', 'investment', 'apartment', 'villa', 'Turkey'],
    ];
    
    $baseKeywords = $keywords[$language] ?? [];
    
    // Kategoriye özel kelimeler ekle
    $categoryKeywords = $this->getCategoryKeywords($ilan->kategori, $language);
    
    return array_merge($baseKeywords, $categoryKeywords);
}
```

**Kullanım Senaryosu:**
- İlan oluşturulurken "Çok Dilli İçerik Üret" butonu
- Seçilen diller için lokalize içerik üretilir
- Her dil için ayrı SEO optimizasyonu

---

## 🚀 EK ÖNERİLER

### 7. 📊 PREDİKTİF SATIŞ TAHMİNİ (Predictive Sales Forecast)

**Amaç:** Bir ilanın ne kadar sürede satılacağını tahmin etmek.

**Veri Kaynakları:**
- Benzer ilanların satış süreleri
- Lokasyon faktörü
- Fiyat faktörü
- Mevsimsel trendler

**Implementasyon:**
```php
public function predictSaleTimeframe(Ilan $ilan): array
{
    // 1. Benzer ilanları bul
    $similarIlans = $this->findSimilarIlans($ilan);
    
    // 2. Ortalama satış süresini hesapla
    $avgSaleTime = $similarIlans->avg('satilma_suresi_gun');
    
    // 3. Fiyat ve lokasyon faktörlerini uygula
    $priceFactor = $this->calculatePriceFactor($ilan);
    $locationFactor = $this->calculateLocationFactor($ilan);
    
    // 4. Tahmin hesapla
    $predictedDays = $avgSaleTime * $priceFactor * $locationFactor;
    
    return [
        'predicted_days' => round($predictedDays),
        'predicted_date' => now()->addDays(round($predictedDays)),
        'confidence' => $this->calculateConfidence($similarIlans),
        'factors' => [
            'price_factor' => $priceFactor,
            'location_factor' => $locationFactor,
            'seasonal_factor' => $this->getSeasonalFactor(),
        ],
    ];
}
```

---

### 8. 🎯 OTOMATİK FİYAT ÖNERİSİ (Auto-Pricing Intelligence)

**Amaç:** İlan fiyatını otomatik optimize etmek.

**Mantık:**
- İlan 30 günden fazla satılmadıysa
- Cortex fiyat analizi yapar
- %5-10 indirim önerir
- Danışmana bildirim gönderir

**Implementasyon:**
```php
public function suggestPriceOptimization(Ilan $ilan): ?array
{
    $daysOnMarket = $ilan->created_at->diffInDays(now());
    
    if ($daysOnMarket < 30) {
        return null; // Henüz erken
    }
    
    // 1. Pazar analizi
    $marketAnalysis = $this->analyzeMarketCompetition($ilan);
    
    // 2. Görüntülenme ve ilgi analizi
    $engagementData = $this->getEngagementData($ilan);
    
    // 3. Fiyat önerisi hesapla
    if ($marketAnalysis['price_difference_percent'] > 10 && $engagementData['view_count'] < 50) {
        $suggestedPrice = $ilan->fiyat * 0.95; // %5 indirim
        
        return [
            'current_price' => $ilan->fiyat,
            'suggested_price' => $suggestedPrice,
            'discount_percent' => 5,
            'reason' => 'Piyasaya göre pahalı ve düşük ilgi',
            'urgency' => 'medium',
        ];
    }
    
    return null;
}
```

---

### 9. 🤝 OTOMATİK MÜŞTERİ EŞLEŞTİRME (Auto-Matching)

**Amaç:** Yeni ilan eklendiğinde otomatik olarak uygun müşterilere bildirim göndermek.

**Mantık:**
- İlan oluşturulduğunda
- Cortex uygun talepleri bulur
- Yüksek skorlu eşleşmelere otomatik bildirim
- WhatsApp/SMS/Email entegrasyonu

**Implementasyon:**
```php
public function autoMatchAndNotify(Ilan $ilan): array
{
    // 1. Uygun talepleri bul
    $matches = $this->propertyMatcher->match($ilan);
    
    // 2. Yüksek skorlu eşleşmeleri filtrele (skor >= 80)
    $highScoreMatches = array_filter($matches, fn($m) => $m['score'] >= 80);
    
    // 3. Her eşleşme için bildirim gönder
    $notifications = [];
    foreach ($highScoreMatches as $match) {
        $talep = Talep::find($match['talep_id']);
        $kisi = $talep->kisi;
        
        // WhatsApp bildirimi
        $this->sendWhatsAppNotification($kisi, $ilan, $match);
        
        // Email bildirimi
        $this->sendEmailNotification($kisi, $ilan, $match);
        
        $notifications[] = [
            'kisi_id' => $kisi->id,
            'talep_id' => $talep->id,
            'match_score' => $match['score'],
            'notification_sent' => true,
        ];
    }
    
    return [
        'ilan_id' => $ilan->id,
        'total_matches' => count($highScoreMatches),
        'notifications_sent' => count($notifications),
        'details' => $notifications,
    ];
}
```

---

### 10. 📈 TREND ANALİZİ VE ÖNGÖRÜ (Trend Analysis & Forecasting)

**Amaç:** Piyasa trendlerini analiz edip gelecek tahminleri yapmak.

**Veri Kaynakları:**
- Geçmiş satış verileri
- Fiyat değişimleri
- Lokasyon bazlı trendler
- Mevsimsel pattern'ler

**Implementasyon:**
```php
public function analyzeMarketTrends(int $ilId, int $ilceId = null): array
{
    // 1. Son 12 ay verilerini al
    $historicalData = $this->getHistoricalData($ilId, $ilceId, 12);
    
    // 2. Trend analizi
    $trends = [
        'price_trend' => $this->calculatePriceTrend($historicalData),
        'demand_trend' => $this->calculateDemandTrend($historicalData),
        'supply_trend' => $this->calculateSupplyTrend($historicalData),
    ];
    
    // 3. Gelecek tahmini (AI ile)
    $forecast = $this->generateForecast($trends, $historicalData);
    
    return [
        'current_trends' => $trends,
        'forecast' => $forecast,
        'recommendations' => $this->generateTrendRecommendations($trends, $forecast),
    ];
}
```

---

## 📋 ÖNCELİK SIRASI

### **PHASE 1 (Hemen Başla - 2 Hafta):**
1. ✅ Fırsat Sentezi (Opportunity Synthesis)
2. ✅ Müşteri His Analizi (Sentiment Analysis)
3. ✅ Otomatik Müşteri Eşleşme (Auto-Matching)

### **PHASE 2 (1 Ay İçinde):**
4. ✅ Akıllı Bütçe Düzeltmesi (Budget Correction)
5. ✅ Otomatik Fiyat Önerisi (Auto-Pricing Intelligence)
6. ✅ Prediktif Satış Tahmini (Predictive Sales Forecast)

### **PHASE 3 (2-3 Ay İçinde):**
7. ✅ Pazar Hakimiyeti Analizi (Competitor Mapping) - n8n entegrasyonu gerekli
8. ✅ Otomatik Hukuki Kontrol (Contract Guard) - AnythingLLM entegrasyonu gerekli
9. ✅ Çok Dilli İçerik Mükemmelliği (Multi-Language Content)
10. ✅ Trend Analizi ve Öngörü (Trend Analysis)

---

## 🎯 SONUÇ

**Toplam Öneri:** 10 yeni görev (6 önerilen + 4 ek)

**Beklenen Etki:**
- 📈 Satış oranı: +%25-30
- ⚡ İşlem süresi: -%40
- 😊 Müşteri memnuniyeti: +%35
- 💰 Gelir optimizasyonu: +%15-20

**Teknik Gereksinimler:**
- n8n entegrasyonu (rakip verisi için)
- AnythingLLM entegrasyonu (hukuki analiz için)
- WhatsApp/SMS API (bildirimler için)
- AI Model: GPT-4 (çok dilli içerik için)

---

**Context7 Compliance:** ✅ Tüm öneriler Context7 standartlarına uygun  
**Yalıhan Bekçi:** ✅ Bu dokümantasyon Yalıhan Bekçi'ye öğretilecek













