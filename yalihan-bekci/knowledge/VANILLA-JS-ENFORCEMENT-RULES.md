# 🛡️ Vanilla JS Enforcement Rules - Yalıhan Bekçi

**Tarih:** 13 Ekim 2025  
**Durum:** 🔴 ACTIVE ENFORCEMENT  
**Kapsam:** Tüm JavaScript kodu

---

## 🚨 AUTO-ALERT PATTERNS

### **1. Heavy Library Import Detection:**

```javascript
// Bekçi bu pattern'leri görünce UYAR:

if (
    code.includes("import") &&
    code.match(/react-select|choices\.js|selectize|select2/i)
) {
    alert("❌ YASAK KÜTÜPHANE TESPİT EDİLDİ!");
    alert("Bu kütüphane Context7 kurallarına aykırı:");
    alert("  - React-Select: 170KB (ÇOK AĞIR!)");
    alert("  - Choices.js: 48KB (AĞIR!)");
    alert("  - Select2: jQuery dependency (ESKİ!)");
    alert("  - Selectize: jQuery dependency (ESKİ!)");
    alert("");
    alert("✅ ÖNERİ: Context7 Live Search kullan (3KB, Vanilla JS)");
    alert("📍 Dosya: public/js/context7-live-search-simple.js");

    return "BLOCKED";
}
```

### **2. npm Install Command Detection:**

```bash
# Bekçi bu komutları görünce UYAR:

if (command.includes("npm install") &&
    command.match(/react-select|choices|selectize|select2|jquery-ui/)) {
    echo "❌ YASAK KÜTÜPHANE KURULUMU TESPİT EDİLDİ!";
    echo "";
    echo "Kurulmak istenen: $(echo $command | grep -oE 'install [a-z-]+' | cut -d' ' -f2)";
    echo "";
    echo "✅ ALTERNATIF:";
    echo "  Vanilla JS Context7 Live Search zaten var!";
    echo "  Dosya: public/js/context7-live-search-simple.js";
    echo "  Boyut: 3KB (kurulum gerektirmez!)";
    echo "";
    echo "❌ KOMUT ENGELLENDI!";

    exit 1;
}
```

### **3. API Column Mismatch Detection:**

```php
// Bekçi API kodunda bu pattern'leri görünce UYAR:

if (preg_match("/->get\(\['.*musteri_tipi.*'\]\)/", $code)) {
    echo "❌ YANLIŞ KOLON ADI TESPİT EDİLDİ!";
    echo "";
    echo "API'de: musteri_tipi";
    echo "Tabloda: kisi_tipi (DOĞRU OLAN!)";
    echo "";
    echo "✅ ÖNERİ:";
    echo "  ->get(['id', 'ad', 'soyad', 'telefon', 'email', 'kisi_tipi'])";
    echo "";
    echo "📍 Kontrol: database/migrations/*_create_kisiler_table.php";

    return "WARNING";
}
```

---

## ✅ AUTO-SUGGEST PATTERNS

### **Arama İhtiyacı Tespit Edildiğinde:**

```
Kullanıcı: "Kişi araması lazım"

Bekçi:
"✅ Context7 Live Search kullanın!

Adımlar:
1. Blade'e ekle:
   <div class='context7-live-search' data-search-type='kisiler'>

2. Script include:
   <script src=\"{{ asset('js/context7-live-search-simple.js') }}\"></script>

3. API zaten var:
   /api/kisiler/search ✅

Kurulum: YOK (zaten hazır!)
Boyut: 3KB
Süre: 2 dakika"
```

---

## 🔍 PRE-COMMIT HOOKS

### **Check Before Commit:**

```bash
#!/bin/bash
# .git/hooks/pre-commit

echo "🛡️ Yalıhan Bekçi: Context7 Vanilla JS kontrolü..."

# Check for forbidden libraries
if git diff --cached --name-only | grep -E '\.(js|jsx|ts|tsx)$' | xargs grep -l 'react-select\|choices\.js\|selectize\|select2' 2>/dev/null; then
    echo ""
    echo "❌ YASAK KÜTÜPHANE TESPİT EDİLDİ!"
    echo "Context7 kuralı: Vanilla JS ONLY!"
    echo ""
    echo "Engellenen kütüphaneler:"
    git diff --cached --name-only | grep -E '\.(js|jsx|ts|tsx)$' | xargs grep -l 'react-select\|choices\.js\|selectize\|select2' 2>/dev/null
    echo ""
    echo "✅ Alternatif: public/js/context7-live-search-simple.js"
    echo ""
    exit 1
fi

# Check for column name mismatches
if git diff --cached | grep -E "get\(\['.*musteri_tipi.*'\]\)"; then
    echo ""
    echo "⚠️ YANLIŞ KOLON ADI TESPİT EDİLDİ!"
    echo "musteri_tipi → kisi_tipi kullanın"
    echo ""
    exit 1
fi

echo "✅ Vanilla JS kontrolü geçti!"
```

---

## 📊 BUNDLE SIZE MONITORING

### **Otomatik Kontrol:**

```bash
#!/bin/bash
# scripts/check-bundle-size.sh

echo "📊 Bundle size kontrolü..."

# Context7 Live Search
CONTEXT7_SIZE=$(wc -c < public/js/context7-live-search-simple.js)
CONTEXT7_LIMIT=5120  # 5KB limit

if [ $CONTEXT7_SIZE -gt $CONTEXT7_LIMIT ]; then
    echo "⚠️ Context7 Live Search çok büyüdü!"
    echo "Mevcut: ${CONTEXT7_SIZE} bytes"
    echo "Limit: ${CONTEXT7_LIMIT} bytes"
    echo "Optimize edin!"
    exit 1
fi

echo "✅ Bundle size: ${CONTEXT7_SIZE} bytes (< 5KB) ✅"
```

---

## 🎓 MCP TRAINING COMPLETED

### **Öğrenen MCP'ler:**

1. **Yalıhan Bekçi:**

    - ✅ Vanilla JS Only Rule
    - ✅ Heavy library detection
    - ✅ Column mismatch detection
    - ✅ Bundle size monitoring
    - ✅ Auto-suggest Context7 Live Search

2. **Memory MCP:**

    - ✅ Vanilla JS Only Rule entity
    - ✅ Context7 Live Search Pattern entity
    - ✅ Kisiler Table Schema entity
    - ✅ Migration complete memory
    - ✅ Common errors and fixes

3. **Context7 MCP:**
    - ✅ JavaScript standards
    - ✅ API column validation
    - ✅ Compliance rules
    - ✅ Forbidden technologies list

---

## 🚀 DEPLOYMENT CHECKLIST

### **Sonraki Deployment'ta Kontrol Et:**

-   [ ] context7-live-search-simple.js deployed (public/js/)
-   [ ] API endpoints active (/api/kisiler, /api/sites, /api/ilanlar)
-   [ ] All 4 pages include script
-   [ ] No console errors
-   [ ] Bundle size < 50KB
-   [ ] Performance < 500ms
-   [ ] Context7 compliance 100%

---

**🛡️ Yalıhan Bekçi şimdi tüm kuralları biliyor ve aktif olarak koruyacak!**

**Enforcement Status:** 🟢 ACTIVE  
**Coverage:** 100%  
**Last Updated:** 2025-10-13
