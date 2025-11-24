import fs from 'fs'
import path from 'path'

const roots = ['docs', 'yalihan-bekci', 'reports', 'ai', 'scripts']
const ignoreDirs = new Set(['node_modules', 'vendor', 'storage', 'public', '.git'])
const exts = new Set(['.md'])
const topics = [
  { key: 'golden vize', re: /golden\s+vize/i },
  { key: 'yurtdışı konut satışı', re: /(yurtd[ıi]ş[ıi]|yurt d[ıi]ş[ıi]).*konut.*sat[ıi]ş/i },
  { key: 'MCP', re: /\bMCP\b|Context7 MCP/i },
  { key: 'dış API', re: /d[ıi]ş.*API|external\s+api/i },
  { key: 'referans numarası', re: /referans\s+numar/i },
  { key: 'arsa hesaplamaları', re: /arsa.*hesap/i },
  { key: 'CRM', re: /\bCRM\b/i },
  { key: 'apartman', re: /apartman/i },
  { key: 'portal ID', re: /portal\s+id|sahibinden_id|emlakjet_id/i },
  { key: 'sahibinden', re: /sahibinden\.com|\bsahibinden\b/i },
]

function walk(dir, out) {
  let entries
  try { entries = fs.readdirSync(dir, { withFileTypes: true }) } catch { return }
  for (const e of entries) {
    const full = path.join(dir, e.name)
    if (e.isDirectory()) {
      if (ignoreDirs.has(e.name)) continue
      walk(full, out)
    } else {
      if (!exts.has(path.extname(e.name))) continue
      out.push(full)
    }
  }
}

function analyzeFile(fp) {
  let content
  try { content = fs.readFileSync(fp, 'utf8') } catch { return null }
  const lines = content.split(/\r?\n/)
  const matches = {}
  for (const t of topics) {
    const ms = []
    for (let i = 0; i < lines.length; i++) {
      if (t.re.test(lines[i])) ms.push({ line: i + 1, text: lines[i].trim().slice(0, 200) })
    }
    if (ms.length) matches[t.key] = ms
  }
  const outdated = /2024|2023|deprecated|eski|kald[ıi]r[ıi]l(d[ıi])?/i.test(content)
  const duplicateHint = /final|complete|tamamland[ıi]|summary/i.test(content)
  return { matches, outdated, duplicateHint }
}

function main() {
  const files = []
  for (const r of roots) walk(r, files)
  const now = new Date()
  const ym = String(now.getFullYear()) + '-' + String(now.getMonth() + 1).padStart(2, '0')
  const dir = path.join(process.cwd(), 'yalihan-bekci', 'reports', ym)
  try { fs.mkdirSync(dir, { recursive: true }) } catch {}
  const mdReport = path.join(dir, 'MD_AUDIT_SUMMARY.txt')
  const lines = []
  lines.push('# MD Audit Summary')
  lines.push('files: ' + files.length)
  for (const fp of files) {
    const a = analyzeFile(fp)
    if (!a) continue
    const rel = fp
    const flags = []
    if (a.outdated) flags.push('outdated')
    if (a.duplicateHint) flags.push('duplicate_hint')
    lines.push('\n- ' + rel + (flags.length ? ' [' + flags.join(',') + ']' : ''))
    for (const k of Object.keys(a.matches)) {
      const ms = a.matches[k]
      lines.push('  * ' + k + ': ' + ms.map(m => (rel + ':' + m.line)).slice(0, 3).join(', '))
    }
  }
  try { fs.writeFileSync(mdReport, lines.join('\n'), 'utf8') } catch {}
}

main()