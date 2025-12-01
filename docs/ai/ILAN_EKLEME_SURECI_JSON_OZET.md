# 📝 İLAN EKLEME SÜRECİ - JSON ÖZET

**Hızlı Referans:** İlan ekleme sürecinin JSON formatında özeti

```json
{
  "process_name": "İlan Ekleme Süreci",
  "form_sections": 10,
  "workflow": {
    "step_1": {
      "name": "Kategori Seçimi",
      "sub_steps": [
        {
          "order": 1,
          "field": "ana_kategori_id",
          "type": "select",
          "required": true,
          "api": "GET /api/categories (ana kategoriler)",
          "on_change": "loadAltKategoriler(anaKategoriId)",
          "options": ["Konut", "İşyeri", "Arsa", "Yazlık Kiralama", "Turistik Tesisler"]
        },
        {
          "order": 2,
          "field": "alt_kategori_id",
          "type": "select",
          "required": true,
          "api": "GET /api/categories/sub/${anaKategoriId}",
          "on_change": "loadYayinTipleri(altKategoriId)",
          "depends_on": "ana_kategori_id",
          "example": {
            "konut": ["Daire", "Villa", "Müstakil Ev", "Dubleks"],
            "arsa": ["İmar Arsaları", "Tarım Arazileri", "Orman Arazileri"],
            "yazlik": ["Günlük Kiralama", "Haftalık Kiralama", "Aylık Kiralama"]
          }
        },
        {
          "order": 3,
          "field": "yayin_tipi_id",
          "type": "select",
          "required": true,
          "api": "GET /api/categories/sub/${altKategoriId}",
          "on_change": "dispatchCategoryChanged()",
          "depends_on": "alt_kategori_id",
          "event": "category-changed",
          "example": {
            "daire": ["Satılık", "Kiralık"],
            "imar-arsalari": ["Satılık"],
            "gunluk-kiralama": ["Günlük"]
          }
        }
      ]
    },
    
    "step_2": {
      "name": "Lokasyon Seçimi",
      "sub_steps": [
        {
          "order": 1,
          "field": "il_id",
          "type": "select",
          "required": false,
          "api": "GET /api/location/iller",
          "on_change": "loadIlceler(ilId)"
        },
        {
          "order": 2,
          "field": "ilce_id",
          "type": "select",
          "required": false,
          "api": "GET /api/location/ilce/${ilId}",
          "on_change": "loadMahalleler(ilceId)",
          "depends_on": "il_id"
        },
        {
          "order": 3,
          "field": "mahalle_id",
          "type": "select",
          "required": false,
          "api": "GET /api/location/mahalle/${ilceId}",
          "depends_on": "ilce_id"
        }
      ],
      "map_integration": {
        "library": "Leaflet.js",
        "features": [
          "Marker placement",
          "Reverse geocoding (Nominatim)",
          "Two-way sync (dropdown ↔ map)"
        ],
        "fields": {
          "latitude": "decimal",
          "longitude": "decimal"
        }
      }
    },
    
    "step_3": {
      "name": "Fiyat Yönetimi",
      "fields": [
        {
          "field": "fiyat",
          "type": "decimal",
          "required": true,
          "validation": "numeric|min:0"
        },
        {
          "field": "para_birimi",
          "type": "select",
          "required": true,
          "options": ["TRY", "USD", "EUR", "GBP"],
          "default": "TRY"
        },
        {
          "field": "fiyat_try_cached",
          "type": "decimal",
          "auto_calculated": true,
          "description": "USD/EUR/GBP ise TRY'ye çevrilmiş değer"
        }
      ]
    },
    
    "step_4": {
      "name": "Temel Bilgiler + AI Yardımcısı",
      "fields": [
        {
          "field": "baslik",
          "type": "text",
          "required": true,
          "max_length": 255,
          "ai_generation": true
        },
        {
          "field": "aciklama",
          "type": "textarea",
          "required": false,
          "ai_generation": true
        }
      ],
      "ai_features": [
        {
          "name": "Başlık Öner",
          "button_id": "ai-generate-title",
          "requires": ["kategori", "lokasyon", "fiyat"],
          "context_percentage": 100
        },
        {
          "name": "Açıklama Öner",
          "button_id": "ai-generate-description",
          "requires": ["kategori", "lokasyon", "fiyat", "ozellikler"],
          "context_percentage": 100
        },
        {
          "name": "Fiyat Öner",
          "button_id": "ai-price-suggestion",
          "requires": ["lokasyon", "kategori"],
          "context_percentage": 75
        },
        {
          "name": "Alan Önerileri",
          "button_id": "ai-field-suggestion",
          "requires": ["kategori", "yayin_tipi"],
          "context_percentage": 50
        }
      ]
    },
    
    "step_5": {
      "name": "Fotoğraflar",
      "type": "file_upload",
      "multiple": true,
      "features": [
        "Drag & Drop",
        "Multiple selection",
        "Cover photo selection",
        "Reorder (drag to sort)",
        "Preview before upload"
      ],
      "api": "POST /api/admin/ilanlar/${ilanId}/fotograflar"
    },
    
    "step_6": {
      "name": "İlan Özellikleri (Field Dependencies)",
      "dynamic": true,
      "trigger": "category-changed event",
      "api": "GET /api/admin/field-dependencies?kategori_slug=${slug}&yayin_tipi=${tip}",
      "structure": {
        "table": "kategori_yayin_tipi_field_dependencies",
        "filter": {
          "kategori_slug": "konut|arsa|yazlik|isyeri",
          "yayin_tipi": "Satılık|Kiralık|Günlük",
          "status": true
        },
        "grouping": "field_category",
        "sorting": "display_order ASC"
      },
      "field_types": [
        {
          "type": "text",
          "examples": ["ada_no", "parsel_no"],
          "storage": "direct (ilanlar table) or pivot (ilan_feature)"
        },
        {
          "type": "number",
          "examples": ["oda_sayisi", "banyo_sayisi", "alan_m2", "kaks", "taks"],
          "storage": "direct or pivot"
        },
        {
          "type": "boolean",
          "examples": ["havuz", "esyali", "yola_cephe"],
          "storage": "pivot (value: '1' or '0')"
        },
        {
          "type": "select",
          "examples": ["imar_statusu", "isitma_tipi"],
          "options_source": "field_options (JSON)",
          "storage": "pivot"
        },
        {
          "type": "textarea",
          "examples": ["ozel_notlar"],
          "storage": "direct or pivot"
        },
        {
          "type": "date",
          "examples": ["sezon_baslangic", "sezon_bitis"],
          "storage": "direct"
        },
        {
          "type": "price",
          "examples": ["gunluk_fiyat", "haftalik_fiyat"],
          "storage": "direct"
        }
      ],
      "field_categories": [
        {
          "slug": "fiyatlandirma",
          "name": "💰 Fiyatlandırma",
          "default_open": true
        },
        {
          "slug": "fiziksel_ozellikler",
          "name": "📐 Fiziksel Özellikler",
          "default_open": true
        },
        {
          "slug": "donanim_tesisat",
          "name": "🔌 Donanım & Tesisat",
          "default_open": false
        },
        {
          "slug": "arsa",
          "name": "🗺️ Arsa Özellikleri",
          "default_open": false,
          "special_fields": [
            "ada_no", "parsel_no", "imar_statusu",
            "kaks", "taks", "gabari",
            "yola_cephe", "altyapi_*"
          ]
        },
        {
          "slug": "yazlik",
          "name": "🏖️ Yazlık Özellikleri",
          "default_open": false,
          "special_fields": [
            "gunluk_fiyat", "haftalik_fiyat", "aylik_fiyat",
            "havuz", "min_konaklama", "max_misafir",
            "sezon_baslangic", "sezon_bitis"
          ]
        }
      ],
      "progress_tracking": {
        "per_category": true,
        "overall": true,
        "visual": "progress bar (0-100%)"
      }
    },
    
    "step_7": {
      "name": "Kişi (İlan Sahibi)",
      "fields": [
        {
          "field": "ilan_sahibi_id",
          "type": "person_selector",
          "required": true,
          "features": ["Live search", "Quick add modal"],
          "api": "GET /api/admin/kisiler/search"
        },
        {
          "field": "ilgili_kisi_id",
          "type": "person_selector",
          "required": false
        }
      ]
    },
    
    "step_8": {
      "name": "Site/Apartman",
      "field": "proje_id",
      "type": "site_selector",
      "required": false,
      "features": ["Live search", "Project details auto-fill"]
    },
    
    "step_9": {
      "name": "Anahtar",
      "fields": [
        {
          "field": "anahtar_kimde",
          "type": "text",
          "required": false
        },
        {
          "field": "anahtar_turu",
          "type": "select",
          "options": ["mal_sahibi", "danisman", "kapici", "emlakci", "yonetici", "diger"]
        },
        {
          "field": "anahtar_notlari",
          "type": "textarea"
        }
      ]
    },
    
    "step_10": {
      "name": "Yayın Durumu",
      "fields": [
        {
          "field": "status",
          "type": "select",
          "required": true,
          "options": ["Taslak", "Aktif", "Pasif", "Beklemede", "Yayında", "Satıldı", "Kiralandı"]
        },
        {
          "field": "crm_only",
          "type": "checkbox",
          "description": "Sadece CRM'de görünsün (public'e çıkmayacak)"
        }
      ]
    }
  },
  
  "category_specific_fields": {
    "arsa": {
      "required_fields": [
        "ada_no",
        "parsel_no",
        "imar_statusu"
      ],
      "important_fields": [
        "kaks",
        "taks",
        "gabari",
        "yola_cephe",
        "altyapi_elektrik",
        "altyapi_su",
        "altyapi_dogalgaz"
      ],
      "field_count": 16
    },
    "yazlik": {
      "required_fields": [
        "gunluk_fiyat",
        "min_konaklama"
      ],
      "important_fields": [
        "havuz",
        "haftalik_fiyat",
        "aylik_fiyat",
        "sezon_baslangic",
        "sezon_bitis",
        "max_misafir"
      ],
      "field_count": 14,
      "related_tables": [
        "yazlik_fiyatlandirma",
        "yazlik_rezervasyonlar"
      ]
    },
    "konut": {
      "required_fields": [
        "oda_sayisi",
        "net_m2"
      ],
      "important_fields": [
        "banyo_sayisi",
        "brut_m2",
        "kat",
        "toplam_kat",
        "bina_yasi",
        "isitma",
        "esyali"
      ]
    }
  },
  
  "validation_system": {
    "base_rules": {
      "baslik": "required|string|max:255",
      "fiyat": "required|numeric|min:0",
      "para_birimi": "required|in:TRY,USD,EUR,GBP",
      "ana_kategori_id": "required|exists:ilan_kategorileri,id",
      "alt_kategori_id": "required|exists:ilan_kategorileri,id",
      "yayin_tipi_id": "required|exists:ilan_kategori_yayin_tipleri,id",
      "ilan_sahibi_id": "required|exists:kisiler,id",
      "status": "required|string|in:Taslak,Aktif,Pasif,Beklemede"
    },
    "category_specific": {
      "service": "CategoryFieldValidator",
      "method": "getRules(kategoriSlug, yayinTipiSlug)",
      "examples": {
        "arsa": {
          "ada_no": "required|string|max:50",
          "parsel_no": "required|string|max:50",
          "imar_statusu": "required|string"
        },
        "yazlik": {
          "gunluk_fiyat": "required|numeric|min:0",
          "min_konaklama": "required|integer|min:1"
        }
      }
    }
  },
  
  "save_process": {
    "steps": [
      {
        "order": 1,
        "action": "Validation",
        "type": "backend",
        "services": ["Validator", "CategoryFieldValidator"]
      },
      {
        "order": 2,
        "action": "Database Transaction Start",
        "type": "backend",
        "method": "DB::beginTransaction()"
      },
      {
        "order": 3,
        "action": "Create Ilan",
        "type": "backend",
        "model": "Ilan::create([...])",
        "fields": [
          "baslik", "aciklama", "fiyat", "para_birimi",
          "ana_kategori_id", "alt_kategori_id", "yayin_tipi_id",
          "il_id", "ilce_id", "mahalle_id",
          "ilan_sahibi_id", "danisman_id", "status"
        ]
      },
      {
        "order": 4,
        "action": "Create Price History",
        "type": "backend",
        "model": "IlanPriceHistory::create([...])",
        "reason": "İlk ilan oluşturma"
      },
      {
        "order": 5,
        "action": "Generate Reference Number",
        "type": "backend",
        "service": "IlanReferansService::generateReferansNo()",
        "format": "REF-YYYY-NNNNNN"
      },
      {
        "order": 6,
        "action": "Attach Features",
        "type": "backend",
        "method": "$ilan->features()->attach([...])",
        "pivot_table": "ilan_feature",
        "fields": ["feature_id", "value"]
      },
      {
        "order": 7,
        "action": "Save Yazlık Details (if applicable)",
        "type": "backend",
        "model": "YazlikDetail::create([...])",
        "condition": "kategori_slug === 'yazlik-kiralama'"
      },
      {
        "order": 8,
        "action": "Upload Photos",
        "type": "backend",
        "method": "File upload + IlanFotografi::create([...])",
        "condition": "if photos exist"
      },
      {
        "order": 9,
        "action": "Commit Transaction",
        "type": "backend",
        "method": "DB::commit()"
      },
      {
        "order": 10,
        "action": "Fire Events",
        "type": "backend",
        "events": [
          "IlanCreated",
          "IlanPriceChanged (if price changed)"
        ],
        "listeners": [
          "FindMatchingDemands (Smart Property Matching)",
          "NotifyN8nAboutNewIlan (n8n webhook)"
        ]
      },
      {
        "order": 11,
        "action": "Redirect",
        "type": "backend",
        "url": "/admin/ilanlar/${ilan->id}",
        "message": "İlan başarıyla oluşturuldu."
      }
    ]
  },
  
  "auto_save": {
    "enabled": true,
    "interval": 30,
    "unit": "seconds",
    "type": "draft",
    "storage": "localStorage + Database",
    "indicator": "Save indicator in form progress bar"
  },
  
  "progress_tracking": {
    "overall_progress": {
      "field": "form-progress-bar",
      "calculation": "Filled fields / Total fields",
      "range": "0-100%"
    },
    "section_progress": {
      "sections": [
        "category", "location", "price", "basic-info",
        "photos", "fields", "person", "site", "key", "status"
      ],
      "indicator": "Section nav links highlight"
    },
    "feature_progress": {
      "per_category": true,
      "visual": "Progress bar per category",
      "example": "💰 Fiyatlandırma: 40% (2/5)"
    }
  },
  
  "api_endpoints": {
    "categories": {
      "main": "GET /api/categories",
      "subcategories": "GET /api/categories/sub/{id}",
      "yayin_tipleri": "GET /api/categories/sub/{altKategoriId}"
    },
    "location": {
      "iller": "GET /api/location/iller",
      "ilceler": "GET /api/location/ilce/{ilId}",
      "mahalleler": "GET /api/location/mahalle/{ilceId}"
    },
    "field_dependencies": {
      "list": "GET /api/admin/field-dependencies?kategori_slug={slug}&yayin_tipi={tip}",
      "by_category": "GET /api/admin/field-dependencies/by-category/{kategoriId}"
    },
    "features": {
      "by_category": "GET /api/admin/features/category/{slug}?yayin_tipi={tip}"
    },
    "ai": {
      "generate_title": "POST /api/admin/ai/generate-title",
      "generate_description": "POST /api/admin/ai/generate-description",
      "price_suggestion": "POST /api/admin/ai/price-suggestion",
      "field_suggestion": "POST /api/admin/ai/field-suggestion"
    },
    "ilan": {
      "store": "POST /admin/ilanlar",
      "photos": "POST /api/admin/ilanlar/{id}/fotograflar"
    }
  },
  
  "database_tables": {
    "main": "ilanlar",
    "related": [
      "ilan_kategorileri",
      "ilan_kategori_yayin_tipleri",
      "kategori_yayin_tipi_field_dependencies",
      "ilan_feature (pivot)",
      "features",
      "yazlik_fiyatlandirma",
      "yazlik_rezervasyonlar",
      "ilan_fotografileri",
      "ilan_price_history"
    ]
  },
  
  "event_system": {
    "events": [
      {
        "name": "category-changed",
        "trigger": "Yayın tipi seçildiğinde",
        "detail": {
          "category": {
            "id": "number",
            "slug": "string",
            "parent_slug": "string"
          },
          "yayinTipi": "string",
          "yayinTipiId": "number"
        },
        "listeners": [
          "FieldDependenciesManager.loadFields()",
          "CategoryFieldsManager.showFields()"
        ]
      },
      {
        "name": "IlanCreated",
        "trigger": "İlan kaydedildikten sonra",
        "listeners": [
          "FindMatchingDemands (Smart Property Matching)",
          "NotifyN8nAboutNewIlan (n8n webhook)"
        ]
      }
    ]
  }
}
```

---

## 📋 KULLANIM

Bu JSON'u Google Gemini'ye vererek:

1. **Süreç Analizi:** İlan ekleme sürecinin tüm adımlarını anlayabilir
2. **Fikir Üretimi:** Süreci iyileştirme önerileri sunabilir
3. **Otomasyon:** Hangi adımların otomatikleştirilebileceğini önerebilir
4. **Validasyon:** Validasyon kurallarını analiz edebilir
5. **UX İyileştirme:** Kullanıcı deneyimini iyileştirme önerileri sunabilir

**Örnek Prompt:**
```
Bu JSON'a göre, "Arsa" kategorisi için ilan ekleme sürecini nasıl iyileştirebiliriz?
Ada-Parsel numarası otomatik doğrulama eklenebilir mi?
```



