import fs from 'fs'
import path from 'path'
import { execSync } from 'node:child_process'

const roots = [
  'resources/js',
  'resources/views'
]
const exts = new Set(['.js', '.mjs', '.ts', '.tsx', '.vue', '.blade.php', '.php', '.html'])
const ignoreDirs = new Set(['node_modules', 'vendor', 'storage', 'yalihan-bekci', 'backups', '.git', 'public'])
const patterns = [
  /\$\s*\(/,
  /\bjQuery\b/,
  /\$\.(ajax|get|post|on|off|ready|each)\b/
]

let found = []
const isCI = process.argv.includes('--mode=ci')

function walk(dir) {
  let entries
  try { entries = fs.readdirSync(dir, { withFileTypes: true }) } catch { return }
  for (const e of entries) {
    const full = path.join(dir, e.name)
    if (e.isDirectory()) {
      if (ignoreDirs.has(e.name)) continue
      walk(full)
    } else {
      const ext = exts.has(path.extname(e.name))
      if (!ext) continue
      scanFile(full)
    }
  }
}

function scanFile(fp) {
  let content
  try { content = fs.readFileSync(fp, 'utf8') } catch { return }
  const lines = content.split(/\r?\n/)
  for (let i = 0; i < lines.length; i++) {
    const line = lines[i]
    for (const p of patterns) {
      if (p.test(line)) {
        found.push({ file: fp, line: i + 1, snippet: line.trim() })
        break
      }
    }
  }
}

function getStagedFiles() {
  try {
    const out = execSync('git diff --cached --name-only', { encoding: 'utf8' })
    return out.split(/\r?\n/).filter(Boolean)
  } catch { return [] }
}

if (isCI) {
  for (const r of roots) walk(r)
} else {
  const staged = getStagedFiles()
  for (const fp of staged) {
    const dir = path.dirname(fp).split(path.sep).pop()
    if (ignoreDirs.has(dir)) continue
    const ext = exts.has(path.extname(fp))
    if (!ext) continue
    scanFile(fp)
  }
}

if (found.length) {
  console.error('jQuery kullanımı bulundu:')
  for (const f of found.slice(0, 200)) {
    console.error(`${f.file}:${f.line} -> ${f.snippet}`)
  }
  if (found.length > 200) console.error(`... toplam ${found.length} bulgu, yalnızca ilk 200 gösterildi`)
  process.exit(1)
} else {
  console.log('✅ jQuery kullanımı bulunamadı')
  process.exit(0)
}