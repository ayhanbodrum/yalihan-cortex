import fs from 'fs'
import path from 'path'

const checks = [
  { name: 'turkiye_disi_konut_satisi', patterns: [/yurtd[ıi]ş[ıi].*konut.*sat[ıi]ş/i, /uluslararas[ıi]/i] },
  { name: 'golden_vize', patterns: [/golden\s+vize/i, /yatirimc[ıi]\s+vize/i] },
  { name: 'mcp_yapilari', patterns: [/\bMCP\b|Context7 MCP/i] },
  { name: 'dis_baglanti_api', patterns: [/external\s+api/i, /harici.*api/i] },
  { name: 'referans_numarasi_sistemleri', patterns: [/referans\s+numar/i, /referans_no/i] },
]

const roots = ['docs', 'yalihan-bekci', 'reports']
const exts = new Set(['.md'])
const ignoreDirs = new Set(['node_modules','vendor','storage','public','.git'])

function walk(dir, out){
  let entries
  try { entries = fs.readdirSync(dir,{ withFileTypes:true }) } catch { return }
  for (const e of entries){
    const full = path.join(dir, e.name)
    if (e.isDirectory()) { if (ignoreDirs.has(e.name)) continue; walk(full, out) }
    else { if (!exts.has(path.extname(e.name))) continue; out.push(full) }
  }
}

function scan(){
  const files = []
  for (const r of roots) walk(r, files)
  const result = {}
  for (const c of checks) result[c.name] = []
  for (const fp of files){
    let txt
    try { txt = fs.readFileSync(fp,'utf8') } catch { continue }
    const lines = txt.split(/\r?\n/)
    for (const c of checks){
      for (const p of c.patterns){
        for (let i=0;i<lines.length;i++){
          if (p.test(lines[i])) { result[c.name].push(fp+':'+(i+1)); break }
        }
      }
    }
  }
  return result
}

function main(){
  const res = scan()
  const now = new Date()
  const ym = String(now.getFullYear()) + '-' + String(now.getMonth()+1).padStart(2,'0')
  const dir = path.join(process.cwd(),'yalihan-bekci','reports',ym)
  try { fs.mkdirSync(dir,{ recursive:true }) } catch {}
  const file = path.join(dir,'INCOMPLETE_PROCESSES_AUDIT.txt')
  const lines = []
  lines.push('# Incomplete Processes Audit')
  for (const k of Object.keys(res)){
    lines.push('\n## '+k)
    const arr = res[k]
    if (!arr || !arr.length) { lines.push('- none'); continue }
    for (const item of arr.slice(0,500)) lines.push('- '+item)
  }
  try { fs.writeFileSync(file, lines.join('\n'),'utf8') } catch {}
}

main()