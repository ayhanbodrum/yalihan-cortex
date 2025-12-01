# Universal IDE Configuration - Context7 Standards

Bu dokümantasyon tüm modern IDE'ler için Context7 standartlarının nasıl entegre edileceğini açıklar.

## 📋 Desteklenen IDE'ler

### ✅ Tam Destek
- 🖱️ **Cursor IDE** - AI-first IDE with Claude integration
- 🌊 **Windsurf IDE** - AI-powered development environment  
- ⚡ **Warp Terminal** - Modern terminal with AI features
- 📝 **VS Code** - Microsoft Visual Studio Code
- 🐙 **GitHub Copilot** - AI pair programmer

### 🔧 Konfigürasyon Dosyaları

```
.cursor/
├── settings.json          # Cursor IDE ayarları + MCP entegrasyonu
├── mcp.json              # MCP server tanımları
└── rules/                # Context7 kuralları

.windsurf/
├── settings.json         # Windsurf IDE ayarları + AI konfigürasyonu
└── workflows/            # Geliştirme iş akışları

.warp/
├── settings.json         # Warp terminal ayarları
├── aliases/              # Terminal kısayolları
└── workflows/            # Terminal iş akışları

.vscode/
├── settings.json         # VS Code ayarları
├── tasks.json           # Görevler (MCP server başlatma vs.)
├── launch.json          # Debug konfigürasyonları
└── extensions.json      # Önerilen uzantılar

.context7/
├── authority.json       # Tek kaynak hakikat (Single Source of Truth)
├── mcp-config.json     # Universal MCP konfigürasyonu
└── cache/              # Cache dosyaları
```

## 🤖 MCP (Model Context Protocol) Servers

### Context7 Ekosistemi

1. **Upstash Context7 MCP** (Port: Auto)
   - Library dokümantasyonları
   - API referansları
   - Framework bilgileri

2. **Yalıhan Bekçi MCP** (Port: 4001)
   - AI öğrenme sistemi
   - Geliştirme önerileri
   - Pattern analizi

3. **Context7 Validator MCP** (Port: 4002)
   - Real-time validation
   - Auto-fix özellikleri
   - Compliance kontrolü

### 🚀 MCP Server Yönetimi

```bash
# Tüm serverları başlat
./scripts/services/start-all-mcp-servers.sh

# Tüm serverları durdur  
./scripts/services/stop-all-mcp-servers.sh

# Sadece Bekçi'yi başlat
./scripts/services/start-bekci-server.sh
```

## 📖 IDE-Specific Kullanım

### 🖱️ Cursor IDE

```jsonc
{
  "cursor.ai.provider": "claude",
  "cursor.ai.model": "claude-3.5-sonnet",
  "cursor.context7.enabled": true,
  "cursor.context7.authority": ".context7/authority.json"
}
```

**Özellikler:**
- Otomatik Context7 validation
- Real-time AI önerileri
- MCP server entegrasyonu
- Auto-learning system

### 🌊 Windsurf IDE

```jsonc
{
  "windsurf.ai.provider": "claude",
  "windsurf.context7": {
    "enabled": true,
    "realTimeValidation": true,
    "autoFix": true
  }
}
```

**Özellikler:**
- Inline AI chat
- Context-aware kod önerileri
- Workflow automation
- Real-time collaboration

### ⚡ Warp Terminal

```jsonc
{
  "aliases": {
    "c7v": "php artisan context7:validate-migration --all",
    "c7f": "php artisan context7:validate-migration --auto-fix",
    "bekci-start": "./scripts/services/start-bekci-server.sh"
  }
}
```

**Workflow'lar:**
- Context7 Full Check
- Laravel Dev Start
- MCP Servers Start

### 📝 VS Code

**Görevler (Tasks):**
- Context7: Validate All
- Context7: Auto Fix
- MCP: Start All Servers
- Laravel: Start Server

**Uzantılar:**
- PHP Intelephense
- Laravel Blade Spacers
- Tailwind CSS IntelliSense
- GitKraken Glo Boards

## 🎯 Quick Actions

### Tüm IDE'lerde Ortak Kısayollar

| Eylem | Kısayol | Açıklama |
|-------|---------|----------|
| `c7v` | Context7 Validation | Tüm proje validation |
| `c7f` | Context7 Auto Fix | Otomatik düzeltmeler |
| `c7r` | Context7 Report | Compliance raporu |
| `bekci-start` | Bekçi Start | AI öğrenme sistemi |

### MCP Tool Calls

```javascript
// Context7 validation
await mcp.call('context7-validator', 'validate_file', {
  file_path: 'app/Models/User.php',
  auto_fix: true
});

// AI öğrenme
await mcp.call('yalihan-bekci', 'learn_from_action', {
  action_type: 'code_change',
  context: 'User model updated',
  files_changed: ['app/Models/User.php']
});

// Geliştirme önerileri
await mcp.call('yalihan-bekci', 'generate_development_ideas', {
  category: 'performance',
  priority: 'high'
});
```

## 🔄 Auto Triggers

### Dosya Kaydetme (onFileSave)

```javascript
// PHP dosyaları için
"**/*.php" -> {
  servers: ["context7-validator", "yalihan-bekci"],
  tools: ["validate_file", "learn_from_action"]
}

// Blade dosyaları için
"**/*.blade.php" -> {
  servers: ["context7-validator"],
  tools: ["validate_file"]
}
```

### Proje Açma (onProjectOpen)

```javascript
{
  servers: ["yalihan-bekci"],
  tools: ["get_project_health"]
}
```

## 📊 Monitoring & Analytics

### Real-time Metrics

- **Compliance Score**: Context7 uyumluluk yüzdesi
- **Code Quality**: Kod kalite metrikleri  
- **Performance**: Bundle size, load times
- **Security**: Güvenlik açığı taraması

### Reports

```bash
# Günlük rapport
php artisan context7:daily-report

# Haftalık analytics
php artisan context7:weekly-analytics

# AI öğrenme raporu
php artisan bekci:learning-report
```

## 🛠️ Troubleshooting

### Yaygın Sorunlar

1. **MCP Server bağlanmıyor**
   ```bash
   # Port kontrolü
   lsof -i :4001
   
   # Server restart
   ./scripts/services/stop-all-mcp-servers.sh
   ./scripts/services/start-all-mcp-servers.sh
   ```

2. **Context7 validation çalışmıyor**
   ```bash
   # Authority.json kontrolü
   cat .context7/authority.json | jq .version
   
   # Cache temizliği
   php artisan context7:cache:clear
   ```

3. **AI önerileri gelmiyor**
   ```bash
   # Bekçi server log kontrolü
   tail -f logs/mcp/yalihan-bekci.log
   ```

### Log Dosyaları

```
logs/
├── mcp/
│   ├── context7-upstash.log
│   ├── yalihan-bekci.log
│   └── context7-validator.log
└── pids/
    ├── context7-upstash.pid
    ├── yalihan-bekci.pid
    └── context7-validator.pid
```

## 🚀 Next Steps

1. **IDE Extension Development**
   - Context7 VS Code extension
   - Cursor plugin enhancement
   - Windsurf workflow library

2. **CI/CD Integration**
   - GitHub Actions workflow
   - Context7 compliance checks
   - Automated reporting

3. **Advanced AI Features**
   - Code generation with Context7 rules
   - Intelligent refactoring
   - Performance optimization suggestions

---

💡 **Bu konfigürasyon, Context7 standartlarının tüm modern IDE'lerde tek bir authority.json dosyası üzerinden yönetilmesini sağlar.**
