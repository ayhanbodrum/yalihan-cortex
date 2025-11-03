# Standard Tools Guide - Yalıhan Emlak

**Date:** 2025-10-30  
**Status:** ACTIVE  
**Priority:** HIGH  
**Context7 Compliant:** ✅

---

## 🎯 STANDART SİSTEMLERE GEÇİŞ

### **Amaç:**
Dizin işlemleri ve dosya güncellemeleri için **standart Cursor toolları** kullanmak.

---

## 📁 DOSYA İŞLEMLERİ

### **Standart Cursor Tools:**

#### **1. Dosya Okuma** ✅
```yaml
Tool: read_file
Usage:
  - Dosya içeriğini okumak için
  - Offset ve limit ile kısmi okuma
  - Image dosyalarını okuma

Example:
  read_file(target_file="/path/to/file.php")
  read_file(target_file="app/Models/Talep.php", offset=1, limit=50)
```

#### **2. Dosya Yazma / Düzenleme** ✅
```yaml
Tool: search_replace (PREFERRED for existing files)
Usage:
  - Mevcut dosyalarda değişiklik yapmak için
  - Exact string replacement
  - Context7 uyumlu

Example:
  search_replace(
    file_path="app/Models/Talep.php",
    old_string="public function show($talep)",
    new_string="public function show(Talep $talep)"
  )

Tool: write (for new files only)
Usage:
  - Yeni dosya oluşturmak için
  - Mevcut dosyaları OVERWRITE eder (dikkat!)
```

#### **3. Dosya Silme** ✅
```yaml
Tool: delete_file
Usage:
  - Dosya silmek için
  - Güvenli (gracefully fails)

Example:
  delete_file(target_file="public/css/neo-unified.css")
```

#### **4. Dosya/Dizin Listeleme** ✅
```yaml
Tool: list_dir
Usage:
  - Dizin içeriğini listelemek için
  - Glob patterns ile filtreleme

Example:
  list_dir(
    target_directory="resources/views/admin",
    ignore_globs=["*.md", "**/vendor/**"]
  )

Tool: glob_file_search
Usage:
  - Dosya pattern ile arama
  - Recursive search

Example:
  glob_file_search(
    glob_pattern="*Controller.php",
    target_directory="app/Http/Controllers"
  )
```

#### **5. Kod Arama** ✅
```yaml
Tool: grep
Usage:
  - Kod içeriğinde arama
  - Regex support
  - Context lines (-A, -B, -C)

Example:
  grep(
    pattern="public function show",
    path="app/Http/Controllers",
    type="php"
  )

Tool: codebase_search (semantic)
Usage:
  - Semantic code search
  - "How/Where/What" questions

Example:
  codebase_search(
    query="Where is the Talep model relationship defined?",
    target_directories=["app/Models"]
  )
```

---

## 🚫 KULLANILMAYACAK TOOLS

### **Desktop Commander (MCP_DOCKER) - KULLANMA!**
```yaml
❌ mcp_MCP_DOCKER_read_file
❌ mcp_MCP_DOCKER_write_file
❌ mcp_MCP_DOCKER_edit_block
❌ mcp_MCP_DOCKER_list_directory
❌ mcp_MCP_DOCKER_move_file
❌ mcp_MCP_DOCKER_start_search

Neden Kullanma:
  - Docker container içinde çalışıyor
  - Mounted directories gerekiyor
  - Yavaş ve kompleks
  - Sandbox restrictions var
  - Standard tools daha hızlı ve güvenilir
```

---

## ✅ BEST PRACTICES

### **1. Dosya Okuma Stratejisi:**
```yaml
Small files (<500 lines):
  → read_file (full content)

Large files (>500 lines):
  → read_file with offset/limit
  → grep for specific sections
  → codebase_search for semantic search

Binary files (images, PDFs):
  → read_file (auto-detects)
```

### **2. Dosya Düzenleme Stratejisi:**
```yaml
Single change:
  → search_replace (PREFERRED)

Multiple changes in same file:
  → Multiple search_replace calls
  → Each change should be atomic

New file:
  → write

Global rename:
  → search_replace with replace_all=true
```

### **3. Arama Stratejisi:**
```yaml
Exact text match:
  → grep (fast, efficient)

Pattern matching:
  → grep with regex

Semantic search:
  → codebase_search (AI-powered)

File name search:
  → glob_file_search
```

---

## 📋 WORKFLOW EXAMPLES

### **Example 1: Controller Fix**
```yaml
1. Find file:
   glob_file_search(glob_pattern="TalepController.php")

2. Read file:
   read_file(target_file="app/Http/Controllers/Admin/TalepController.php")

3. Make change:
   search_replace(
     file_path="app/Http/Controllers/Admin/TalepController.php",
     old_string="public function show($talep)",
     new_string="public function show(Talep $talep)"
   )

4. Verify:
   grep(pattern="public function show", path="app/Http/Controllers/Admin/TalepController.php")
```

### **Example 2: CSS Migration**
```yaml
1. Find usage:
   grep(pattern="neo-btn", path="resources/views/admin", type="php")

2. Read affected files:
   read_file(target_file="resources/views/admin/talepler/index.blade.php")

3. Convert Neo → Tailwind:
   search_replace(
     file_path="resources/views/admin/talepler/index.blade.php",
     old_string='class="neo-btn neo-btn-primary"',
     new_string='class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg shadow-md transition-all"'
   )
```

### **Example 3: Cleanup Duplicates**
```yaml
1. Find duplicates:
   glob_file_search(glob_pattern="*duplicate*.css")

2. Review:
   read_file(target_file="public/css/duplicate.css")

3. Delete:
   delete_file(target_file="public/css/duplicate.css")
```

---

## 🎯 CONTEXT7 COMPLIANCE

### **Forbidden Operations:**
```yaml
❌ Using Desktop Commander tools
❌ Manual file system operations via terminal
❌ Creating files without Context7 validation
❌ Modifying files outside workspace
```

### **Required Operations:**
```yaml
✅ Always use standard Cursor tools
✅ Validate Context7 compliance before changes
✅ Document major file operations
✅ Test after modifications
```

---

## 📊 TOOL COMPARISON

### **Performance:**
```yaml
File Operations:
  Standard Tools: ⚡⚡⚡ (Fast)
  Desktop Commander: ⚡ (Slow)

Reliability:
  Standard Tools: ✅✅✅ (High)
  Desktop Commander: ✅✅ (Medium)

Ease of Use:
  Standard Tools: ⭐⭐⭐ (Simple)
  Desktop Commander: ⭐⭐ (Complex)

Context7 Compliance:
  Standard Tools: ✅ (Native)
  Desktop Commander: ⚠️ (Manual validation)
```

### **Use Cases:**
```yaml
Standard Tools - Use for:
  ✅ All file read/write operations
  ✅ Code search and navigation
  ✅ File management (create, delete, move)
  ✅ Project-wide changes
  ✅ 95% of all operations

Desktop Commander - Use for:
  ⚠️ Terminal commands (php artisan, npm)
  ⚠️ Process management
  ⚠️ Data analysis (CSV, JSON)
  ⚠️ Only when standard tools can't do it
```

---

## 🚀 MIGRATION GUIDE

### **From Desktop Commander to Standard Tools:**

```yaml
OLD (Desktop Commander):
  mcp_MCP_DOCKER_read_file(path="/path/to/file.php")
NEW (Standard):
  read_file(target_file="/path/to/file.php")

OLD (Desktop Commander):
  mcp_MCP_DOCKER_edit_block(file_path, old_string, new_string)
NEW (Standard):
  search_replace(file_path, old_string, new_string)

OLD (Desktop Commander):
  mcp_MCP_DOCKER_list_directory(path="/path/to/dir")
NEW (Standard):
  list_dir(target_directory="/path/to/dir")

OLD (Desktop Commander):
  mcp_MCP_DOCKER_start_search(pattern="text")
NEW (Standard):
  grep(pattern="text", path="/path")
```

---

## 📚 DOCUMENTATION

### **References:**
- Cursor Standard Tools: Built-in documentation
- Context7 Standards: `.context7/authority.json`
- Migration Strategy: `css-migration-strategy.md`
- Phase 1 Completion: `PHASE1-COMPLETED.md`

---

## ✅ CHECKLIST

### **Before Any File Operation:**
- [ ] Use standard Cursor tools (not Desktop Commander)
- [ ] Validate Context7 compliance
- [ ] Test after changes
- [ ] Document major changes
- [ ] Update Yalıhan Bekçi knowledge

### **Common Operations:**
- [ ] Read file → `read_file`
- [ ] Edit file → `search_replace`
- [ ] New file → `write`
- [ ] Delete file → `delete_file`
- [ ] Search code → `grep` or `codebase_search`
- [ ] Find files → `glob_file_search`
- [ ] List directory → `list_dir`

---

**Last Updated:** 2025-10-30  
**Status:** ACTIVE ✅  
**Standard Tools:** ENFORCED ✅

