# 🎯 EMLAK SİSTEMİ - JSON ÖZET (Gemini İçin)

**Hızlı Referans:** Sistem yapısının JSON formatında özeti

```json
{
  "system_name": "Yalıhan Emlak Management System",
  "version": "4.4.0",
  "technology_stack": {
    "backend": "Laravel 10.x",
    "php_version": "8.1+",
    "database": "MySQL 8.0+",
    "cache": "Redis",
    "frontend": {
      "css": "Tailwind CSS (ONLY)",
      "js": "Vanilla JavaScript + Alpine.js",
      "maps": "Leaflet.js + OpenStreetMap"
    },
    "ai_providers": [
      "OpenAI (GPT-3.5, GPT-4)",
      "Claude (claude-3-sonnet)",
      "Gemini (gemini-pro)",
      "DeepSeek (deepseek-chat)",
      "Ollama (Local models)"
    ]
  },
  
  "category_structure": {
    "hierarchy_levels": 3,
    "levels": {
      "level_0": "Ana Kategori (Ana Kategori)",
      "level_1": "Alt Kategori (Alt Kategori)",
      "level_2": "Yayın Tipi (Satılık/Kiralık/Günlük)"
    },
    "database_tables": {
      "ilan_kategorileri": "Ana ve alt kategoriler",
      "ilan_kategori_yayin_tipleri": "Yayın tipleri (yeni sistem)"
    }
  },

  "main_categories": [
    {
      "id": 1,
      "name": "Konut",
      "slug": "konut",
      "icon": "home",
      "subcategories": [
        {
          "name": "Daire",
          "slug": "daire",
          "yayin_tipleri": ["Satılık", "Kiralık"],
          "special_fields": [
            "oda_sayisi", "banyo_sayisi", "net_m2", "brut_m2",
            "kat", "toplam_kat", "bina_yasi", "isitma", "esyali", "aidat"
          ]
        },
        {
          "name": "Villa",
          "slug": "villa",
          "yayin_tipleri": ["Satılık", "Kiralık"],
          "special_fields": ["daire_fields", "bahce_m2", "site_ozellikleri"]
        },
        {
          "name": "Müstakil Ev",
          "slug": "mustakil-ev",
          "yayin_tipleri": ["Satılık", "Kiralık"]
        },
        {
          "name": "Dubleks",
          "slug": "dubleks",
          "yayin_tipleri": ["Satılık", "Kiralık"]
        }
      ]
    },
    {
      "id": 2,
      "name": "İşyeri",
      "slug": "isyeri",
      "icon": "building",
      "subcategories": [
        {
          "name": "Ofis",
          "slug": "ofis",
          "yayin_tipleri": ["Satılık", "Kiralık"],
          "special_fields": [
            "isyeri_tipi", "kira_bilgisi", "ciro_bilgisi",
            "ruhsat_durumu", "personel_kapasitesi", "isyeri_cephesi"
          ]
        },
        {
          "name": "Dükkan",
          "slug": "dukkan",
          "yayin_tipleri": ["Satılık", "Kiralık"]
        },
        {
          "name": "Fabrika",
          "slug": "fabrika",
          "yayin_tipleri": ["Satılık", "Kiralık"]
        },
        {
          "name": "Depo",
          "slug": "depo",
          "yayin_tipleri": ["Satılık", "Kiralık"]
        }
      ]
    },
    {
      "id": 3,
      "name": "Arsa",
      "slug": "arsa",
      "icon": "map",
      "special_fields_count": 16,
      "subcategories": [
        {
          "name": "İmar Arsaları",
          "slug": "imar-arsalari",
          "yayin_tipleri": ["Satılık"],
          "special_fields": [
            {
              "field": "ada_no",
              "type": "varchar",
              "required": true,
              "ai_auto_fill": true,
              "description": "Ada numarası (TKGM'den çekilebilir)"
            },
            {
              "field": "parsel_no",
              "type": "varchar",
              "required": true,
              "ai_auto_fill": true,
              "description": "Parsel numarası"
            },
            {
              "field": "imar_statusu",
              "type": "select",
              "options": [
                "imarli",
                "imarsiz",
                "villa_imarli",
                "konut_imarli",
                "ticari_imarli"
              ],
              "required": true
            },
            {
              "field": "alan_m2",
              "type": "decimal",
              "required": true,
              "description": "Arsa alanı (m²)"
            },
            {
              "field": "yola_cephe",
              "type": "boolean",
              "description": "Yola cepheli mi?"
            },
            {
              "field": "yola_cephesi",
              "type": "decimal",
              "unit": "metre",
              "description": "Yola cephe mesafesi"
            },
            {
              "field": "altyapi_elektrik",
              "type": "boolean",
              "description": "Elektrik altyapısı var mı?"
            },
            {
              "field": "altyapi_su",
              "type": "boolean",
              "description": "Su altyapısı var mı?"
            },
            {
              "field": "altyapi_dogalgaz",
              "type": "boolean",
              "description": "Doğalgaz altyapısı var mı?"
            },
            {
              "field": "kaks",
              "type": "decimal",
              "ranges": {
                "0.00-0.50": "Çok düşük yoğunluk (Villa)",
                "0.51-1.00": "Düşük yoğunluk",
                "1.01-2.00": "Orta yoğunluk (4-6 katlı)",
                "2.01-4.00": "Yüksek yoğunluk (8-12 katlı)",
                "4.01+": "Çok yüksek yoğunluk (Gökdelen)"
              },
              "ai_suggestion": true,
              "description": "Kat Alanı Katsayısı"
            },
            {
              "field": "taks",
              "type": "decimal",
              "ranges": {
                "0.00-0.20": "Minimum taban alanı (Geniş bahçe)",
                "0.21-0.35": "Düşük taban alanı (Villa)",
                "0.36-0.50": "Orta taban alanı (Standart konut)",
                "0.51-0.70": "Yüksek taban alanı (Apartman)",
                "0.71+": "Maksimum taban alanı (Ticari)"
              },
              "ai_suggestion": true,
              "description": "Taban Alanı Katsayısı"
            },
            {
              "field": "gabari",
              "type": "decimal",
              "unit": "metre",
              "ranges": {
                "0-6.5": "1-2 kat",
                "6.51-9.5": "2-3 kat",
                "9.51-12.5": "3-4 kat",
                "12.51-15.5": "4-5 kat",
                "15.51+": "5+ kat"
              },
              "description": "Yükseklik sınırı"
            },
            {
              "field": "taban_alani",
              "type": "decimal",
              "unit": "m²",
              "description": "Taban alanı"
            },
            {
              "field": "konum_avantajlari",
              "type": "json",
              "options": [
                "denize_yakin",
                "deniz_manzarali",
                "marina_yakin",
                "golf_sahasi_yakin",
                "havaalani_yakin"
              ],
              "description": "Konum avantajları (çoklu seçim)"
            }
          ]
        },
        {
          "name": "Tarım Arazileri",
          "slug": "tarim-arazileri",
          "yayin_tipleri": ["Satılık"]
        },
        {
          "name": "Orman Arazileri",
          "slug": "orman-arazileri",
          "yayin_tipleri": ["Satılık"]
        }
      ]
    },
    {
      "id": 4,
      "name": "Yazlık Kiralama",
      "slug": "yazlik-kiralama",
      "icon": "sun",
      "special_fields_count": 14,
      "subcategories": [
        {
          "name": "Günlük Kiralama",
          "slug": "gunluk-kiralama",
          "yayin_tipleri": ["Günlük"],
          "special_fields": [
            {
              "field": "gunluk_fiyat",
              "type": "decimal",
              "required": true,
              "ai_auto_fill": true,
              "description": "Günlük fiyat (AI ile piyasa analizi)"
            },
            {
              "field": "haftalik_fiyat",
              "type": "decimal",
              "ai_calculation": true,
              "description": "Haftalık fiyat (AI ile hesaplanabilir)"
            },
            {
              "field": "aylik_fiyat",
              "type": "decimal",
              "ai_calculation": true,
              "description": "Aylık fiyat (AI ile hesaplanabilir)"
            },
            {
              "field": "sezonluk_fiyat",
              "type": "decimal",
              "description": "Sezonluk fiyat"
            },
            {
              "field": "min_konaklama",
              "type": "integer",
              "description": "Minimum konaklama günü"
            },
            {
              "field": "max_misafir",
              "type": "integer",
              "description": "Maksimum misafir sayısı"
            },
            {
              "field": "temizlik_ucreti",
              "type": "decimal",
              "description": "Temizlik ücreti"
            },
            {
              "field": "havuz",
              "type": "boolean",
              "description": "Havuz var mı?"
            },
            {
              "field": "havuz_turu",
              "type": "select",
              "options": ["ozel", "genel", "infinity"],
              "conditional": "havuz == true"
            },
            {
              "field": "sezon_baslangic",
              "type": "date",
              "description": "Sezon başlangıç tarihi"
            },
            {
              "field": "sezon_bitis",
              "type": "date",
              "description": "Sezon bitiş tarihi"
            },
            {
              "field": "elektrik_dahil",
              "type": "boolean",
              "description": "Elektrik fiyata dahil mi?"
            },
            {
              "field": "su_dahil",
              "type": "boolean",
              "description": "Su fiyata dahil mı?"
            }
          ],
          "related_tables": {
            "yazlik_fiyatlandirma": {
              "purpose": "Sezonluk fiyatlandırma",
              "fields": [
                "sezon_tipi (enum: yaz, ara_sezon, kis)",
                "baslangic_tarihi",
                "bitis_tarihi",
                "gunluk_fiyat",
                "haftalik_fiyat",
                "aylik_fiyat",
                "minimum_konaklama",
                "maksimum_konaklama",
                "ozel_gunler (JSON)"
              ]
            },
            "yazlik_rezervasyonlar": {
              "purpose": "Rezervasyon yönetimi",
              "fields": [
                "check_in",
                "check_out",
                "misafir_sayisi",
                "cocuk_sayisi",
                "pet_sayisi",
                "toplam_fiyat",
                "kapora_tutari",
                "status (enum: beklemede, onaylandi, iptal, tamamlandi)"
              ]
            }
          }
        },
        {
          "name": "Haftalık Kiralama",
          "slug": "haftalik-kiralama",
          "yayin_tipleri": ["Haftalık"]
        },
        {
          "name": "Aylık Kiralama",
          "slug": "aylik-kiralama",
          "yayin_tipleri": ["Aylık"]
        }
      ]
    },
    {
      "id": 5,
      "name": "Turistik Tesisler",
      "slug": "turistik-tesisler",
      "icon": "hotel",
      "subcategories": [
        {
          "name": "Otel",
          "slug": "otel",
          "yayin_tipleri": ["Satılık", "Kiralık"]
        },
        {
          "name": "Pansiyon",
          "slug": "pansiyon",
          "yayin_tipleri": ["Satılık", "Kiralık"]
        },
        {
          "name": "Tatil Köyü",
          "slug": "tatil-koyu",
          "yayin_tipleri": ["Satılık", "Kiralık"]
        }
      ]
    }
  ],

  "ilan_model": {
    "table": "ilanlar",
    "required_fields": [
      "baslik",
      "aciklama",
      "fiyat",
      "para_birimi",
      "status",
      "il_id",
      "ilce_id",
      "mahalle_id",
      "ana_kategori_id",
      "alt_kategori_id",
      "yayin_tipi_id"
    ],
    "common_fields": [
      "metrekare",
      "oda_sayisi",
      "banyo_sayisi",
      "bina_yasi",
      "isitma",
      "esyali",
      "aidat",
      "krediye_uygun",
      "takasa_uygun",
      "latitude",
      "longitude"
    ],
    "conditional_fields": {
      "arsa": ["ada_no", "parsel_no", "imar_statusu", "kaks", "taks", "gabari"],
      "yazlik": ["gunluk_fiyat", "havuz", "sezon_baslangic", "min_konaklama"],
      "konut": ["net_m2", "brut_m2", "kat", "toplam_kat"],
      "isyeri": ["isyeri_tipi", "kira_bilgisi", "ruhsat_durumu"]
    }
  },

  "dynamic_field_system": {
    "table": "kategori_yayin_tipi_field_dependencies",
    "purpose": "Her kategori ve yayın tipi için dinamik form alanları",
    "features": [
      "Kategoriye özel alan tanımlama",
      "Yayın tipine özel alan tanımlama",
      "AI otomatik doldurma (ai_auto_fill)",
      "AI öneri sistemi (ai_suggestion)",
      "Conditional fields (bir alan başka alanı aktif eder)",
      "Smart validation (kategoriye özel kurallar)"
    ],
    "field_types": [
      "text",
      "number",
      "boolean",
      "select",
      "textarea",
      "date",
      "price"
    ]
  },

  "modules": [
    {
      "name": "Emlak",
      "purpose": "İlan yönetimi, kategori yönetimi",
      "models": ["Ilan", "IlanKategori", "IlanFotografi", "Ozellik"]
    },
    {
      "name": "CRM",
      "purpose": "Müşteri yönetimi, talep yönetimi",
      "models": ["Kisi", "Talep", "IlanTalepEslesme"],
      "ai_features": [
        "Smart Property Matching",
        "Customer Churn Analysis",
        "Opportunity Synthesis"
      ]
    },
    {
      "name": "Arsa",
      "purpose": "Arsa özel işlemleri, TKGM entegrasyonu",
      "models": ["ArsaCalculation"],
      "services": ["PropertyValuationService", "TKGMService"],
      "features": [
        "Ada-Parsel doğrulama",
        "İmar durumu analizi",
        "KAKS/TAKS hesaplamaları",
        "Değerleme tahmini"
      ]
    },
    {
      "name": "Yazlık Kiralama",
      "purpose": "Sezonluk fiyatlandırma, rezervasyon",
      "models": ["YazlikFiyatlandirma", "YazlikRezervasyon", "Season"],
      "features": [
        "3 sezon fiyatlandırması (Yaz, Ara Sezon, Kış)",
        "Rezervasyon çakışma kontrolü",
        "Otomatik fiyat hesaplama"
      ]
    },
    {
      "name": "Takım Yönetimi",
      "purpose": "Görev yönetimi, proje yönetimi",
      "models": ["Gorev", "Proje", "TakimUyesi"],
      "integrations": ["Telegram Bot", "n8n Workflow"]
    },
    {
      "name": "Finans",
      "purpose": "Finansal işlemler, komisyon yönetimi",
      "models": ["FinansalIslem", "Komisyon"],
      "ai_features": [
        "Finansal trend analizi",
        "Gelir/gider tahminleri",
        "Komisyon optimizasyonu"
      ]
    }
  ],

  "ai_system": {
    "orchestrator": "YalihanCortex",
    "location": "app/Services/AI/YalihanCortex.php",
    "features": [
      {
        "name": "Smart Property Matching",
        "description": "Talep ile ilan eşleştirme, match skoru hesaplama"
      },
      {
        "name": "Price Valuation",
        "description": "Arsa ve konut değerleme, piyasa analizi"
      },
      {
        "name": "Customer Churn Analysis",
        "description": "Müşteri risk analizi, churn skoru hesaplama"
      },
      {
        "name": "Voice-to-CRM",
        "description": "Sesli komut → JSON dönüşümü, NLP ile doğal dil işleme"
      },
      {
        "name": "Content Generation",
        "description": "İlan açıklaması üretme, SEO optimizasyonu, çok dilli içerik"
      }
    ],
    "providers": [
      {
        "name": "OpenAI",
        "models": ["gpt-3.5-turbo", "gpt-4"]
      },
      {
        "name": "Claude",
        "models": ["claude-3-sonnet"]
      },
      {
        "name": "Gemini",
        "models": ["gemini-pro"]
      },
      {
        "name": "DeepSeek",
        "models": ["deepseek-chat"]
      },
      {
        "name": "Ollama",
        "models": ["mistral", "llama2"]
      }
    ]
  },

  "idea_suggestions": {
    "category_based_ai": [
      {
        "category": "Arsa",
        "suggestions": [
          "Ada-Parsel numarasından otomatik TKGM veri çekme",
          "İmar durumuna göre yatırım potansiyeli analizi",
          "KAKS/TAKS değerlerine göre proje önerileri",
          "Konum avantajlarına göre fiyat tahmini"
        ]
      },
      {
        "category": "Yazlık",
        "suggestions": [
          "Sezon bazlı dinamik fiyat önerileri",
          "Rezervasyon yoğunluğuna göre fiyat optimizasyonu",
          "Müşteri tercihlerine göre özellik önerileri",
          "Talep tahminleme (hangi tarihlerde yoğunluk)"
        ]
      },
      {
        "category": "Konut",
        "suggestions": [
          "Özelliklere göre fiyat tahmini",
          "Benzer ilan karşılaştırması",
          "İyileştirme önerileri (değer artışı için)"
        ]
      }
    ],
    "integration_ideas": [
      "Arsa + Konut: Arsa üzerine konut projesi önerileri",
      "Yazlık + Konut: Yazlık olarak kullanılabilen konutlar",
      "İşyeri + Konut: Ticari + konut karışık projeler"
    ],
    "market_analysis": [
      "Kategori bazlı pazar trendleri",
      "Lokasyon bazlı fiyat haritası",
      "Sezon bazlı talep analizi (yazlık için)",
      "Karşılaştırmalı analiz (benzer ilanlar)"
    ]
  }
}
```

---

## 📝 KULLANIM ÖNERİLERİ

Bu JSON'u Google Gemini'ye vererek:

1. **Sistem Analizi:** Sistem mimarisini anlayabilir
2. **Fikir Üretimi:** Kategori bazlı AI önerileri üretebilir
3. **İyileştirme:** Dinamik form sistemi için öneriler sunabilir
4. **Entegrasyon:** Kategori entegrasyonları önerebilir
5. **Market Analysis:** Pazar analizi özellikleri geliştirebilir

**Örnek Prompt:**
```
Bu JSON verilerine göre, "Arsa" kategorisi için AI destekli özellik önerileri geliştir.
KAKS/TAKS değerlerine göre otomatik proje önerileri nasıl yapılabilir?
```



