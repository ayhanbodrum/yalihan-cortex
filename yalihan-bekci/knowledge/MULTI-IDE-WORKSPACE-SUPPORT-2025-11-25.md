# Multi-IDE Workspace Support Guide

**Tarih:** 25 Kasım 2025  
**Durum:** Knowledge Base for Yalıhan Bekçi  
**Scope:** Warp + Cursor + VS Code shared workspace configuration  
**Version:** 1.0

---

## 🌍 Overview: IDE Landscape

This project is developed in a **shared workspace** using **multiple IDEs**:

```
Workspace: /Users/macbookpro/Projects/yalihanai/
├── 🔵 VS Code (Primary IDE)
│   └── Config: .vscode/, .vscode/tasks.json
├── 🔴 Cursor (AI-enhanced alternative)
│   └── Config: .cursor/, .cursor/mcp.json, .cursor/settings.json, .cursor/rules/
├── 🟡 Warp (Terminal IDE + prompt history)
│   └── Config: .warp/ (if configured)
└── Shared Files: .cursorrules, .context7/*, docs/*, etc.
```

**Critical:** Context7 compliance rules must work across **ALL IDEs** without modification.

---

## 📋 IDE Configurations

### IDE 1: VS Code (Primary)

**Location:** `.vscode/`

**Key Files:**

- **tasks.json** - Build, test, and deploy tasks
- **launch.json** - Debugger configurations (if present)
- **extensions.json** - Recommended extensions list
- **settings.json** - Workspace-specific settings

**Context7 Awareness:**

- `tasks.json` includes Context7 validation tasks:
    - "Context7: Validate All"
    - "Context7: Auto Fix"
    - "Context7: Health Check"

**Shared Configurations:**

- Uses `.context7/` standards
- Respects `.cursorrules` for AI guidance
- Syncs with `.env.example` for secrets

**How to Extend:** Add tasks to `.vscode/tasks.json` that ALL IDEs can use

---

### IDE 2: Cursor (AI IDE)

**Location:** `.cursor/`

**Key Files:**

- **mcp.json** - MCP server configuration (Yalıhan Bekçi integration)
- **settings.json** - Cursor-specific settings
- **extensions/** - Downloaded extensions
- **memory/** - AI context memory
- **rules/** - Custom rules directory

**Context7 Awareness:**

- `mcp.json` points to Yalıhan Bekçi server (port 4001)
- Cursor can read Context7 standards from `.context7/` directory
- AI models in Cursor are aware of project rules

**Root Files:**

- **.cursorrules** - AI prompt/rules for Cursor (top-level)
- **.cursorignore** - Files Cursor should ignore

**Important Note:** Cursor has its own MCP integration with Yalıhan Bekçi. VS Code uses Copilot's built-in MCP support.

---

### IDE 3: Warp (Terminal IDE)

**Location:** Potential `.warp/` directory (if configured)

**Features:**

- Command history with AI suggestions
- Prompt templates
- Reusable commands from project

**Status:** ⏳ Currently unclear if Warp is configured

- Check if `.warp/` directory exists
- Document any Warp-specific configurations

**Recommendation:** Create `.warp/` with:

```
.warp/
├── README.md - Warp-specific commands
├── commands/ - Project-specific CLI shortcuts
└── workflows/ - Terminal automation
```

---

## ✅ Cross-IDE Shared Standards

### Standard 1: Context7 Compliance Rules

**Source:** `.context7/` (14-15 active standard files)

**How Each IDE Uses It:**

1. **VS Code:** Via tasks.json (`context7-full-scan.sh`)
2. **Cursor:** Via MCP server + local file read
3. **Warp:** Via shell commands with same scripts
4. **GitHub Actions:** Via workflows (CI/CD validation)

**Location:** `/Users/macbookpro/Projects/yalihanai/.context7/`

**Key Standards Enforced:**

- ✅ STATUS_COLUMN_GLOBAL_STANDARD (boolean, no 'Aktif' string)
- ✅ ORDER_DISPLAY_ORDER_STANDARD (no 'order' field)
- ✅ LOCATION_MAHALLE_ID_STANDARD (no 'semt_id')
- ✅ ENABLED_FIELD_FORBIDDEN (no 'is_active'/'enabled')
- ✅ ROUTE_NAMING_STANDARD (consistent naming)

**IDE Independence:** ✅ YES

- All IDEs read same `.context7/` files
- All IDEs run same validation scripts
- All IDEs use same Yalıhan Bekçi knowledge base

---

### Standard 2: Workspace Configuration

**Shared Files (All IDEs use these):**

- `yalihanai.code-workspace` - VS Code workspace definition
- `.env.example` - Environment template
- `.envrc` (if direnv) - Environment loader
- `phpunit.xml` - PHP test configuration
- `tailwind.config.js` - Tailwind CSS settings
- `vite.config.js` - Vite build configuration
- `.gitignore` - Git ignore patterns
- `.prettierrc` - Code formatter config
- `eslint.config.js` - JavaScript linter config
- `.pre-commit-config.yaml` - Pre-commit hooks

**IDE Usage:**

- VS Code: Reads `yalihanai.code-workspace`
- Cursor: Uses folder root as context
- Warp: Uses `.env` and `.envrc` for shell environment
- All: Respect `.gitignore`

---

### Standard 3: AI Guidance

**VS Code + Copilot:**

- Uses GitHub Copilot (built-in)
- Follows `.github/copilot-instructions.md` (copilot-instructions.md in root)
- Context: Repository structure + conventions

**Cursor + Built-in AI:**

- Uses `.cursorrules` (project-level AI rules)
- Also respects `.github/copilot-instructions.md` (same content)
- Has Yalıhan Bekçi MCP integration via `.cursor/mcp.json`

**Warp + AI Commands:**

- Uses prompt history
- Can reference previous commands
- AI model suggests next commands

**Alignment:** ✅ Both `.cursorrules` and `.github/copilot-instructions.md` should have identical Content7 guidelines.

---

## 🔧 IDE-Specific Configuration Details

### VS Code Configuration

**File:** `.vscode/tasks.json`

**Relevant Context7 Tasks:**

```json
{
    "label": "Context7: Validate All",
    "command": "php",
    "args": ["artisan", "context7:validate-migration", "--all"]
}
```

**How to Use:** `Ctrl+Shift+B` or Run Task

**Additional VS Code Files:**

- `.vscode/launch.json` - Not found (consider creating)
- `.vscode/extensions.json` - Contains:
    - GitHub Copilot
    - Tailwind CSS Intellisense
    - Laravel extensions
    - PHP extensions

---

### Cursor Configuration

**File:** `.cursor/mcp.json`

**Current Configuration:**

```json
{
    "servers": {
        "yalihan-bekci": {
            "command": "./scripts/services/start-bekci-server.sh",
            "port": 4001
        }
    }
}
```

**Status:** ✅ Yalıhan Bekçi MCP server configured

**Knowledge Base:**

- Location: `yalihan-bekci/knowledge/`
- Cursor MCP reads from this directory
- AI has context about project standards

**Cursor Rules:**

- File: `.cursor/rules/` (directory)
- These rules are read by Cursor's AI model
- Should align with Context7 standards

---

### Warp Configuration

**Status:** ⏳ NEEDS DOCUMENTATION

**If Configured:**

- File: `.warp/workflows.json` or similar
- Stores command templates
- Can include Django/Laravel commands

**Recommended Setup:**

```bash
# .warp/workflows/laravel-commands.json
{
  "workflows": [
    {
      "name": "context7-validate",
      "command": "php artisan context7:validate-migration --all"
    },
    {
      "name": "laravel-serve",
      "command": "php artisan serve --port=8002"
    },
    {
      "name": "migration-auto-fix",
      "command": "./scripts/fix-migrations.sh"
    }
  ]
}
```

---

## 📱 IDE Capabilities Comparison

| Capability          | VS Code      | Cursor       | Warp       | Notes                   |
| ------------------- | ------------ | ------------ | ---------- | ----------------------- |
| Code Editing        | ✅ Full      | ✅ Full      | 🟡 Limited | Warp is terminal-first  |
| Context7 Validation | ✅ Tasks     | ✅ MCP       | ✅ Shell   | All can run validation  |
| AI Assistance       | ✅ Copilot   | ✅ Built-in  | 🟡 Limited | Cursor has stronger AI  |
| Debugging           | ✅ Yes       | ✅ Yes       | ❌ No      | Terminal IDE limitation |
| Extensions          | ✅ Extensive | ✅ Extensive | 🟡 Limited | Terminal tools only     |
| Keyboard Shortcuts  | ✅ VS Code   | ✅ VS Code   | 🟡 Shell   | Different paradigm      |
| Pre-commit Hooks    | ✅ Yes       | ✅ Yes       | ✅ Yes     | All work with git       |
| MCP Integration     | 🟡 Limited   | ✅ Full      | ❌ No      | Cursor optimized        |

---

## 🔌 How Yalıhan Bekçi Works Across IDEs

### Yalıhan Bekçi MCP Server

**Running on Port:** 4001

**Start Command:** `./scripts/services/start-bekci-server.sh`

**Served Files:**

- `.context7/` standard files
- `yalihan-bekci/knowledge/` knowledge base
- Context7 validation logic

**IDE Integration:**

**VS Code:**

- ✅ Can call Yalıhan Bekçi via terminal commands
- Via Copilot MCP bridge (if configured)
- Primary access: Task execution

**Cursor:**

- ✅ Direct MCP integration via `.cursor/mcp.json`
- AI model has full knowledge base access
- Can suggest fixes based on Context7 rules

**Warp:**

- ✅ Can run shell commands that call Yalıhan Bekçi
- Via `curl` or direct script execution
- Command history integration

**GitHub Actions:**

- ✅ Runs context7 scripts directly
- No MCP needed (CI/CD context)
- Uses `./scripts/context7-full-scan.sh`

---

## 📚 Multi-IDE Development Workflow

### Scenario 1: Developer Using VS Code

```
1. Developer edits file in VS Code
2. Pre-commit hook runs (git hook)
3. Context7 validation triggered
4. If error: Developer runs "Context7: Auto Fix" task
5. Auto fix modifies file
6. Developer commits
7. GitHub Actions validates again
```

**IDE Advantage:** Full debugging support, extensive extensions

---

### Scenario 2: Developer Using Cursor

```
1. Developer edits file in Cursor
2. Cursor AI (via MCP) reads Context7 rules
3. AI suggests violations before save
4. Developer accepts suggestion
5. File is corrected automatically
6. Pre-commit hook validates
7. Developer commits
```

**IDE Advantage:** AI-first workflow, real-time suggestions

---

### Scenario 3: DevOps/CLI User Using Warp

```
1. DevOps executes: context7-validate
2. Warp suggests next command: migration auto-fix
3. DevOps accepts suggestion
4. Script executes
5. Results displayed
6. Command added to history
```

**IDE Advantage:** Fast terminal workflow, command history

---

## 🛠️ Setup Instructions for Each IDE

### For VS Code Users

1. **Install Extensions:**
    - GitHub Copilot (or copilot-instructions.md)
    - Tailwind CSS Intellisense
    - PHP Intelephense
    - Laravel Blade Snippets

2. **Configure Tasks:**
    - Tasks already in `.vscode/tasks.json`
    - Use `Ctrl+Shift+B` to run build tasks
    - Use Shift+Ctrl+P → "Run Task" to find Context7 tasks

3. **Enable AI Assistance:**
    - Install Copilot extension
    - Accept workspace context from `.github/copilot-instructions.md`

4. **Configure Debugger:**
    - Create `.vscode/launch.json` for PHP debugging
    - Configure Xdebug port 9003

---

### For Cursor Users

1. **Install Extensions:**
    - Same as VS Code (Cursor supports VS Code extensions)
    - Additional: Context7 Compliance Helper (custom)

2. **Setup MCP:**
    - Check `.cursor/mcp.json` is configured
    - Start MCP server: `./scripts/services/start-bekci-server.sh`
    - Cursor will connect on startup

3. **Enable AI Rules:**
    - Rules in `.cursor/rules/` are auto-loaded
    - AI model reads these before responding
    - Will suggest Context7 compliance automatically

4. **Configure Memory:**
    - `.cursor/memory/` stores AI context
    - Persists learning across sessions
    - Automatically updated

---

### For Warp Users

1. **Create Workflow File:**
    - Create `.warp/workflows.json`
    - Add Laravel commands (see suggested setup above)
    - Add Context7 commands

2. **Set Environment:**
    - Copy `.env.example` to `.env`
    - Warp reads via `.envrc` (if direnv installed)
    - Run `direnv allow` to authorize

3. **Enable Command History:**
    - Warp saves all commands automatically
    - Search with `^R` (reverse history search)
    - AI suggests next commands based on pattern

4. **Configure Shell:**
    - Shell: zsh (as per environment info)
    - Add alias: `alias context7-validate="php artisan context7:validate-migration --all"`
    - Add alias: `alias mcp-start="./scripts/services/start-bekci-server.sh"`

---

## 📋 Troubleshooting Multi-IDE Issues

### Issue 1: Context7 Rules Not Loaded in Cursor

**Symptoms:** Cursor doesn't suggest Context7 fixes

**Solution:**

1. Check `.cursor/mcp.json` is configured
2. Verify Yalıhan Bekçi server is running (port 4001)
3. Restart Cursor

**Command:**

```bash
cd /Users/macbookpro/Projects/yalihanai
./scripts/services/start-bekci-server.sh
```

---

### Issue 2: Tasks Not Appearing in VS Code

**Symptoms:** Context7 tasks not visible in command palette

**Solution:**

1. Verify `.vscode/tasks.json` exists
2. Reload VS Code window: `Ctrl+Shift+P` → Reload Window
3. Run task: `Ctrl+Shift+B` or `Tasks: Run Task`

---

### Issue 3: Git Hooks Not Running Pre-commit

**Symptoms:** Files with violations commit anyway

**Solution:**

1. Check pre-commit framework: `pre-commit --version`
2. Install if needed: `pip install pre-commit`
3. Run manually: `pre-commit run --all-files`
4. Configure hooks: `.pre-commit-config.yaml`

---

### Issue 4: Warp Commands Not Finding PHP

**Symptoms:** PHP command not found in Warp

**Solution:**

1. Check PHP path: `which php`
2. Add to shell profile: `export PATH="/usr/local/bin:$PATH"`
3. Source `.envrc`: `direnv allow`

---

## 🔒 Security Considerations

### Secret Management Across IDEs

**Do NOT include in IDE configs:**

- Database passwords
- API keys
- OAuth tokens
- Personal access tokens

**Proper Secret Storage:**

- `.env` file (gitignored)
- `.env.example` (template only)
- GitHub Secrets (for CI/CD)
- `.env.local` (IDE-specific overrides)

**IDE Behavior:**

- VS Code: Reads `.env` via PHP
- Cursor: Respects `.env` (AI won't suggest secrets)
- Warp: Reads `.env` in shell context

---

## 📊 IDE Usage Statistics (Recommended)

```
Workspace Distribution:
├── 60% VS Code (Primary development)
├── 30% Cursor (AI-assisted coding sessions)
└── 10% Warp (DevOps/CLI operations)
```

**Rationale:**

- VS Code: Most mature, extensive ecosystem
- Cursor: Best AI assistance for learning/prototyping
- Warp: Fastest for terminal operations

---

## 🚀 Future IDE Support

### Potential IDEs to Support

1. **JetBrains IDE (PhpStorm)**
    - Full PHP support
    - Advanced debugging
    - Professional license required

2. **Neovim**
    - Terminal-based editing
    - Highly customizable
    - Steep learning curve

3. **Emacs**
    - Ultimate customization
    - Legacy support

4. **Vim**
    - Minimal footprint
    - Keyboard-optimized

**Recommendation:** Support if requested, but VS Code/Cursor/Warp are optimal for current team.

---

## ✅ Checklist: IDE Configuration

- [ ] VS Code has Context7 tasks in `.vscode/tasks.json`
- [ ] Cursor has MCP configured in `.cursor/mcp.json`
- [ ] Cursor rules align with Context7 standards in `.cursor/rules/`
- [ ] `.cursorrules` matches `.github/copilot-instructions.md`
- [ ] All IDEs can run `php artisan context7:validate-migration --all`
- [ ] Pre-commit hooks work across all IDEs
- [ ] Secrets are NOT in IDE config files
- [ ] `.warp/` directory created (if Warp is used)
- [ ] Yalıhan Bekçi MCP server can be started from any IDE
- [ ] GitHub Actions CI/CD mirrors Context7 validation

---

## 📝 Knowledge Base Updates

**This document should be indexed in:**

- Yalıhan Bekçi: `yalihan-bekci/knowledge/MULTI-IDE-WORKSPACE-SUPPORT.md` ← **THIS FILE**
- GitHub Copilot: `.github/copilot-instructions.md` (reference section)
- Cursor Rules: `.cursor/rules/multi-ide-rules.md` (digest)

**Update Schedule:**

- When new IDE is added to team
- When Context7 standards change
- When workspace structure changes
- Quarterly review

---

## 🎯 Conclusion

**Multi-IDE Workspace Status:** ✅ SUPPORTED

**Key Insight:** Context7 compliance works seamlessly across **VS Code, Cursor, and Warp** because:

1. All use same `.context7/` standard files
2. All can run same validation scripts
3. All access same Yalıhan Bekçi MCP server
4. All respect git pre-commit hooks

**For New Team Members:** Read this guide to understand IDE choices and setup.

**For IDEs Upgrades:** Keep these configurations in sync when updating.

---

**Version:** 1.0  
**Created:** 25 November 2025  
**Last Updated:** 25 November 2025  
**Maintainer:** Yalıhan Bekçi Knowledge Base
