# Context7 API Documentation

## 📋 **Context7 Standardı:** C7-API-DOCUMENTATION-2025-01-29

**Versiyon:** 1.0.0 (API Documentation)
**Son Güncelleme:** 29 Ocak 2025
**Durum:** ✅ API Documentation Tamamlandı
**Context7 Hafıza:** ✅ Senkronize

---

## 🎯 **CONTEXT7 API ENDPOINTS**

### **1. Property Type AI Description API**

#### **Endpoint:** `POST /admin/ilanlar/ai-property-type-description`

**Açıklama:** Emlak tipine göre AI açıklama üretir

**Request Headers:**

```http
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
Authorization: Bearer {token}
```

**Request Body:**

```json
{
    "property_type": "land|yazlik|apartment|house|villa|office|shop",
    "title": "string (required)",
    "metrekare": "number (optional)",
    "lokasyon": "string (optional)",
    "fiyat": "number (optional)",
    "imar_durumu": "string (optional)",
    "kaks": "number (optional)",
    "taks": "number (optional)",
    "denize_uzaklik": "number (optional)",
    "max_kisi_sayisi": "number (optional)",
    "min_konaklama": "number (optional)",
    "havuz": "boolean (optional)",
    "bahce": "boolean (optional)",
    "barbeku": "boolean (optional)",
    "dahil_hizmetler": "string (optional)",
    "ucretsiz_hizmetler": "string (optional)"
}
```

**Response Success (200):**

```json
{
    "success": true,
    "description": "string",
    "metadata": {
        "word_count": "number",
        "processing_time": "number",
        "confidence_score": "number"
    },
    "property_type": "string"
}
```

**Response Error (500):**

```json
{
    "success": false,
    "error": "string",
    "fallback_description": "string"
}
```

**Validation Rules:**

- `property_type`: Required, must be one of: land, yazlik, apartment, house, villa, office, shop
- `title`: Required, max 255 characters
- `metrekare`: Optional, numeric, min 1
- `fiyat`: Optional, numeric, min 0

**Example Request:**

```bash
curl -X POST /admin/ilanlar/ai-property-type-description \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {csrf_token}" \
  -d '{
    "property_type": "land",
    "title": "İmarlı Arsa",
    "metrekare": 1000,
    "lokasyon": "Çeşme, İzmir",
    "fiyat": 500000,
    "imar_durumu": "İmarlı",
    "kaks": 1.5,
    "taks": 0.3
  }'
```

### **2. Dynamic Form Fields API**

#### **Endpoint:** `GET /admin/ilanlar/dynamic-fields/{propertyType}`

**Açıklama:** Emlak tipine göre dinamik form alanları döndürür

**URL Parameters:**

- `propertyType`: string (required) - apartment, house, villa, land, yazlik, office, shop

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "name": "string",
        "fields": [
            {
                "name": "string",
                "label": "string",
                "type": "select|number|checkbox|textarea|text",
                "required": "boolean",
                "options": ["array"],
                "min": "number",
                "max": "number"
            }
        ]
    }
}
```

**Response Error (500):**

```json
{
    "success": false,
    "message": "string"
}
```

### **3. AI Property Suggestions API**

#### **Endpoint:** `POST /admin/ilanlar/ai-property-suggestions`

**Açıklama:** AI özellik önerileri döndürür

**Request Body:**

```json
{
    "property_type": "string",
    "location": "string (optional)"
}
```

**Response Success (200):**

```json
{
    "success": true,
    "data": [
        {
            "name": "string",
            "category": "string",
            "priority": "high|medium|low",
            "description": "string"
        }
    ]
}
```

---

## 🔧 **API TESTING**

### **Test Endpoints**

#### **1. API Health Check**

```bash
curl -X GET /admin/ilanlar/api/health
```

#### **2. API Statistics**

```bash
curl -X GET /admin/ilanlar/api/stats
```

#### **3. API Performance**

```bash
curl -X GET /admin/ilanlar/api/performance
```

### **Rate Limiting**

- **Default Limit:** 100 requests per minute per IP
- **AI Endpoints:** 10 requests per minute per user
- **Cache Endpoints:** 500 requests per minute per IP

### **Error Codes**

- **200:** Success
- **400:** Bad Request (validation error)
- **401:** Unauthorized
- **403:** Forbidden
- **404:** Not Found
- **429:** Too Many Requests (rate limit)
- **500:** Internal Server Error

---

## 📊 **API MONITORING**

### **Performance Metrics**

- **Response Time:** < 2 seconds
- **Success Rate:** > 95%
- **Error Rate:** < 5%
- **Availability:** > 99.9%

### **Monitoring Endpoints**

#### **API Metrics**

```bash
curl -X GET /admin/ilanlar/api/metrics
```

#### **Cache Statistics**

```bash
curl -X GET /admin/ilanlar/api/cache-stats
```

#### **Database Performance**

```bash
curl -X GET /admin/ilanlar/api/db-performance
```

---

## 🚀 **DEPLOYMENT**

### **Production Configuration**

```php
// config/api.php
'context7' => [
    'rate_limiting' => [
        'default' => 100,
        'ai_endpoints' => 10,
        'cache_endpoints' => 500
    ],
    'timeout' => 30,
    'retry_attempts' => 3,
    'cache_ttl' => 3600
]
```

### **Environment Variables**

```env
CONTEXT7_API_ENABLED=true
CONTEXT7_CACHE_ENABLED=true
CONTEXT7_AI_ENABLED=true
CONTEXT7_RATE_LIMITING=true
CONTEXT7_MONITORING=true
```

---

## 📋 **API VERSIONING**

### **Current Version:** v1.0.0

### **Versioning Strategy:**

- **URL Versioning:** `/api/v1/`
- **Header Versioning:** `API-Version: v1`
- **Backward Compatibility:** 2 versions supported

---

## ✅ **TESTING CHECKLIST**

### **API Testing**

- [ ] Property Type AI Description API
- [ ] Dynamic Form Fields API
- [ ] AI Property Suggestions API
- [ ] Error handling
- [ ] Validation
- [ ] Rate limiting
- [ ] Authentication
- [ ] Performance

### **Integration Testing**

- [ ] Frontend integration
- [ ] Cache integration
- [ ] Database integration
- [ ] AI service integration

### **Performance Testing**

- [ ] Load testing
- [ ] Stress testing
- [ ] Endurance testing
- [ ] Spike testing

---

## 🎯 **NEXT STEPS**

1. **API Testing Implementation**
2. **Performance Monitoring Setup**
3. **Advanced AI Features**
4. **Advanced Cache Features**
5. **Final Testing & Deployment**
