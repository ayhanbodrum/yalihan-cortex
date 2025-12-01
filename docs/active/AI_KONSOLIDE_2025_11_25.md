# 🤖 EmlakPro AI - Konsolide Dokümantasyon

**Son Güncelleme:** 25 Kasım 2025  
**Context7 Standardı:** C7-AI-KONSOLIDE-2025-11-25  
**AI Dokümantasyon:** 10 Ana Bileşen

---

## 📋 İÇİNDEKİLER

1. [Kullanım Örnekleri](#examples)
2. [Copilot Guide](#copilot)
3. [Context7 Review](#context7)
4. [Prompt Library](#prompts)
5. [Training System](#training)
6. [API Integration](#api)
7. [MCP Servers](#mcp)
8. [Knowledge Base](#knowledge)
9. [Workflows](#workflows)
10. [Development Ideas](#ideas)

---

## 💡 KULLANIM ÖRNEKLERİ {#examples}

**Dosya:** `docs/ai/AI_KULLANIM_ORNEKLERI.md`

### Property Description Generation

```php
class PropertyDescriptionService
{
    public function generateDescription($propertyData, $images)
    {
        $prompt = "Bu emlak için profesyonel satış açıklaması yaz";

        if (!empty($images)) {
            return $this->aiService->analyzeImagesAndDescribe(
                $prompt,
                $images,
                'gpt-4-vision'
            );
        }
        return $this->aiService->generate($prompt);
    }
}
```

### Lead Analysis & Scoring

```php
class LeadAnalysisService
{
    public function scoreAndCategorize($leadMessage)
    {
        $analysis = $this->aiService->analyze([
            'message' => $leadMessage,
            'analysis_points' => [
                'budget_range',
                'urgency_level',
                'property_type_preference',
                'location_preference'
            ]
        ]);

        return $analysis;
    }
}
```

### Price Suggestion Engine

```php
class PriceSuggestionService
{
    public function suggestPrice($propertyId)
    {
        $property = Property::find($propertyId);
        $similarProperties = $this->findSimilarProperties($property);

        $analysis = $this->aiService->analyzeMarket([
            'property' => $property->toArray(),
            'comparable_properties' => $similarProperties,
            'analysis_type' => 'price_suggestion'
        ]);

        return $analysis;
    }
}
```

---

## 🎯 COPILOT PROMPTS {#copilot}

**Dosya:** `docs/ai/COPILOT_PROMPTS_GUIDE.md`

### Code Generation Prompts

```markdown
# Property Model Generation

"Laravel 10 Eloquent model:

- Fields: title, description, price, currency, il_id
- Relationships: belongsTo(User), hasMany(Images)
- Use status enum (not legacy patterns)
- Tailwind CSS with dark mode"
```

### Debug & Troubleshooting

```markdown
# API Error Debugging

"Debug N+1 query issue:

- Property::with('category')->get() loop
- Solution: Eager loading optimization"
```

### Documentation Generation

```markdown
# API Endpoint Documentation

"Generate OpenAPI 3.0 docs:

- Routes: properties endpoints
- Include Context7 compliance annotations"
```

---

## 🔍 CONTEXT7 AI REVIEW {#context7}

**Dosya:** `docs/ai/AI_PROMPTS_CONTEXT7_REVIEW.md`

### Compliance Checking

```php
class Context7AutoFixService
{
    public function suggestFixes($violationReport)
    {
        $violations = $violationReport['violations'];

        $fixes = $this->aiService->generateFixes([
            'violations' => $violations,
            'context' => 'context7_compliance',
            'target_language' => 'php',
            'framework' => 'laravel10'
        ]);

        return $fixes;
    }
}
```

### Validation Rules

```text
1. Status field check: enum('active', 'passive', 'archived')
2. CSS framework: Tailwind CSS only
3. Database: Indexes on (il_id, ilce_id, mahalle_id)
4. Relationships: Eager loading with()
5. Rate limiting: API throttling configured
```

---

## 📚 PROMPT LIBRARY {#prompts}

**Klasör:** `docs/prompts/` - 4 temel prompt

### İlan Açıklaması

`docs/prompts/ilan-aciklama.prompt.md`

- Profesyonel satış metni
- JSON format çıktı

### İlan Başlığı

`docs/prompts/ilan-baslik.prompt.md`

- SEO-optimized (80 karakter)
- Lokasyon + Tip + Özellik

### Danışman Raporu

`docs/prompts/danisman-raporu.prompt.md`

- KPI özeti
- Başarı hikayeleri
- İyileştirme önerileri

---

## 🎓 TRAINING SYSTEM {#training}

**Klasör:** `docs/ai-training/` - 24 eğitim modülü

### Levels

```text
Level 1 - Temel:
  - 00-BASLA-BURADAN.md
  - 01-AI-FEATURES-GUIDE.md

Level 2 - Orta:
  - 03-ADVANCED-PROMPTING.md
  - 04-CONTEXT7-AI-INTEGRATION.md

Level 3 - İleri:
  - 06-CUSTOM-AI-MODELS.md
  - 07-FINE-TUNING.md

AnythingLLM:
  - 00-ANYTHINGLLM-MASTER-TRAINING.md
  - Setup & Advanced features
```

### Learning Workflow

```php
class AILearningWorkflow
{
    public function recordAction($action)
    {
        $this->recordToBecci([
            'action_type' => $action['type'],
            'context' => $action['context'],
            'outcome' => $action['result'],
            'timestamp' => now()
        ]);
    }

    public function generateInsights()
    {
        return $this->aiService->analyzePatterns([
            'recent_actions' => $this->getRecentActions(100),
            'success_rate' => $this->calculateSuccessRate(),
            'improvements' => $this->identifyImprovementAreas()
        ]);
    }
}
```

---

## 🔌 API INTEGRATION {#api}

**Dosya:** `docs/api/context7-api-documentation.md`

### API Endpoints

#### Property Analysis

```php
POST /api/ai/analyze-property
{
    "property_id": 123,
    "analysis_type": "price_suggestion"
}

Response: {
    "analysis": {...},
    "confidence": 0.85,
    "suggestions": [...]
}
```

#### Lead Scoring

```php
POST /api/ai/score-lead
{
    "lead_message": "İstanbul'da 2+1 daire arıyorum",
    "contact_info": {...}
}

Response: {
    "score": 85,
    "priority": "high",
    "suggested_agent": 5
}
```

#### Content Generation

```php
POST /api/ai/generate-content
{
    "property_id": 123,
    "content_type": "description",
    "language": "tr"
}

Response: {
    "content": "...",
    "seo_keywords": [...],
    "quality_score": 0.92
}
```

---

## 🤖 MCP SERVERS {#mcp}

### Yalıhan Bekçi MCP

```bash
# Başlatma
./scripts/services/start-bekci-server.sh

# Server: Port 4000
# Endpoints:
#   GET /health
#   POST /analyze
#   POST /learn-from-action
#   POST /generate-ideas
```

### MCP Tools

```json
{
    "tools": [
        {
            "name": "analyze_code",
            "description": "AI kod analizi"
        },
        {
            "name": "generate_documentation",
            "description": "Otomatik dokümantasyon"
        },
        {
            "name": "learn_from_action",
            "description": "Bekçi'ye aksiyon öğret"
        }
    ]
}
```

---

## 💾 KNOWLEDGE BASE {#knowledge}

### Yalıhan Bekçi Knowledge

```yaml
Project Patterns:
    - Module architecture
    - Service layer design
    - API patterns

Code Standards:
    - Context7 compliance
    - Tailwind CSS patterns
    - Laravel best practices

AI Best Practices:
    - Prompt engineering
    - Context management
    - Error handling

Optimization:
    - N+1 query prevention
    - Caching strategies
    - Performance tuning
```

---

## 🔄 WORKFLOWS {#workflows}

### Lead Processing Workflow

```text
New Lead → AI Analysis → Scoring → Assignment
   ↓
CRM Update → Notification → Follow-up
```

### Property Listing Workflow

```text
New Property → AI Description → Price Suggestion
   ↓
Publishing → Social Media → Email Alert
```

### Content Generation Workflow

```text
Property Data → Analysis → Title
   ├→ Description
   ├→ Keywords
   └→ Social Copy
```

---

## 💡 DEVELOPMENT IDEAS {#ideas}

### AI-Generated Suggestions

```php
class DevelopmentIdeasService
{
    public function generateIdeas($category = 'performance')
    {
        $analysis = $this->analyzeCurrentSystem();

        return $this->aiService->generateSuggestions([
            'category' => $category,
            'metrics' => $analysis,
            'count' => 5
        ]);
    }
}
```

### Categories

- `performance`: Performans iyileştirmeleri
- `features`: Yeni özellikler
- `ux`: UX iyileştirmeleri
- `security`: Güvenlik
- `scalability`: Ölçeklenebilirlik

---

## 📊 DOSYA HARITASI

```
docs/ai/
├── AI_KULLANIM_ORNEKLERI.md
├── COPILOT_PROMPTS_GUIDE.md
└── AI_PROMPTS_CONTEXT7_REVIEW.md

docs/ai-training/
├── 00-BASLA-BURADAN.md
├── 01-AI-FEATURES-GUIDE.md
└── (22 more training modules)

docs/prompts/
├── ilan-aciklama.prompt.md
├── ilan-baslik.prompt.md
└── danisman-raporu.prompt.md

docs/api/
└── context7-api-documentation.md

docs/yalihan-bekci/
└── (Bekçi AI system)
```

---

## ✅ CONTEXT7 COMPLIANCE

- ✅ No legacy field patterns
- ✅ Tailwind CSS only (no Neo Design)
- ✅ Dark mode support
- ✅ Eager loading documented
- ✅ Rate limiting examples
- ✅ Proper database indexing
- ✅ API standardization

---

## 📚 BIRLEŞTIRILEN DOSYALAR

**AI Kullanım:** 3 dosya

- AI_KULLANIM_ORNEKLERI.md
- COPILOT_PROMPTS_GUIDE.md
- AI_PROMPTS_CONTEXT7_REVIEW.md

**Prompt Library:** 4 dosya

- ilan-aciklama.prompt.md
- ilan-baslik.prompt.md
- danisman-raporu.prompt.md
- (prompts directory)

**Training:** 24 modül

- AnythingLLM master training
- Beginner to Advanced levels

**API & Integration:** 2 dosya

- context7-api-documentation.md
- AI integration examples

**Context7 Compliance:** ✅ C7-AI-KONSOLIDE-2025-11-25  
**Tarih:** 25 Kasım 2025
