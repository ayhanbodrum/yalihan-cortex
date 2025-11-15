# 🤖 AI Prompts - Context7 Compliance Review

**Date:** 2025-11-06  
**Purpose:** Ensure AI prompts follow Context7 standards  
**Status:** REVIEW NEEDED ⚠️

---

## 📋 SUMMARY

**Total Prompts:** 12 files  
**Matches Found:** 10 occurrences of `enabled|status|aktif|durum`  
**Context7 Compliance:** UNKNOWN - Needs manual review

---

## 🔍 ANALYSIS BY FILE

### 1. ilan-ekleme-fiyat-onerisi.prompt.md
**Matches:** 5  
**Context:** Fiyat öneri prompt'u  
**Status:** ⚠️ REVIEW NEEDED

**Potential Issues:**
- May use Turkish field names
- Need to check if mentions `aktif`, `durum`, `enabled`

---

### 2. emlak-segment-workflow-aciklama-olustur.prompt.md
**Matches:** 1  
**Context:** Açıklama oluşturma workflow  
**Status:** ⚠️ REVIEW NEEDED

---

### 3. daire-baslik-olustur.prompt.md
**Matches:** 1  
**Context:** Daire başlığı oluşturma  
**Status:** ⚠️ REVIEW NEEDED

---

### 4. arsa-aciklama-olustur.prompt.md
**Matches:** 3  
**Context:** Arsa açıklaması oluşturma  
**Status:** ⚠️ REVIEW NEEDED

---

## 🎯 CONTEXT7 COMPLIANCE REQUIREMENTS

### Database Fields
✅ **REQUIRED:**
- `status` (for active/inactive state)
- `il_id` (not `sehir_id`)
- `mahalle_id` (not `semt_id`)
- `para_birimi` (not `currency`)

❌ **FORBIDDEN:**
- `enabled` → Use `status`
- `is_active` → Use `status`
- `aktif` → Use `status`
- `durum` → Use `status`
- `sehir_id` → Use `il_id`
- `semt_id` → Use `mahalle_id`

---

## 📝 RECOMMENDED PROMPT ADDITIONS

Add to each AI prompt:

```markdown
## 🛡️ Context7 Compliance Rules

When generating code or database references:

### Database Fields (MANDATORY)
- ✅ Use `status` (NEVER `enabled`, `is_active`, `aktif`, `durum`)
- ✅ Use `il_id` (NEVER `sehir_id`)
- ✅ Use `mahalle_id` (NEVER `semt_id`)
- ✅ Use `para_birimi` (NEVER `currency`)

### Code Standards
- Models: $fillable must use `status` not `enabled`
- Queries: where('status', true) not where('enabled', true)
- Scopes: scopeActive() should check `status` field

### Reference
See: `.context7/ENABLED_FIELD_FORBIDDEN.md`
```

---

## ✅ ACTION ITEMS

### Immediate (Priority 1)
- [ ] Review all 12 prompt files manually
- [ ] Check for forbidden field names
- [ ] Add Context7 compliance section to each prompt
- [ ] Update field naming in examples

### Short-term (Priority 2)
- [ ] Create `prompt.context7.template.md`
- [ ] Add automated prompt validation
- [ ] Integrate with Yalıhan Bekçi checks

### Long-term (Priority 3)
- [ ] AI prompt versioning system
- [ ] Automated Context7 compliance testing for prompts
- [ ] Prompt effectiveness metrics

---

## 🔄 REVIEW CHECKLIST

For each prompt file:

```markdown
- [ ] No `enabled` field references
- [ ] No `is_active` field references
- [ ] No `aktif` field references (Turkish)
- [ ] No `durum` field references (Turkish)
- [ ] Uses `status` for active/inactive
- [ ] Uses `il_id` not `sehir_id`
- [ ] Uses `mahalle_id` not `semt_id`
- [ ] Has Context7 compliance section
- [ ] Examples use correct field names
- [ ] JSON output format is Context7 compliant
```

---

## 📚 REFERENCE DOCUMENTS

1. `.context7/authority.json` - Master rules
2. `.context7/ENABLED_FIELD_FORBIDDEN.md` - enabled prohibition details
3. `yalihan-bekci/rules/status-field-standard.json` - Status field standard
4. `.context7/LOCATION_MAHALLE_ID_STANDARD.md` - Location field standards

---

## 🎯 EXPECTED OUTCOME

After review and updates:

✅ **All 12 AI prompts:**
- Context7 compliant field naming
- Proper documentation of standards
- Examples using correct fields
- Validation rules included
- Reference links to authority docs

---

## 📊 CURRENT STATS

| Metric | Value |
|--------|-------|
| Total Prompts | 12 |
| Reviewed | 0 ⚠️ |
| Compliant | Unknown |
| Need Updates | Unknown |
| Match Count | 10 occurrences |

---

## 🚀 NEXT STEPS

1. **Manual Review** - Go through each prompt file
2. **Update Content** - Fix any Context7 violations
3. **Add Standards** - Include compliance section
4. **Test Prompts** - Verify AI output is compliant
5. **Document** - Update this review file with results

---

**Status:** ⚠️ PENDING REVIEW  
**Deadline:** Before next AI prompt usage  
**Owner:** Development Team

