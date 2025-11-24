import fs from 'fs'
import path from 'path'

function read(p){ try { return fs.readFileSync(p,'utf8') } catch { return '' } }

function main(){
  const now = new Date()
  const ym = String(now.getFullYear()) + '-' + String(now.getMonth()+1).padStart(2,'0')
  const dir = path.join(process.cwd(),'yalihan-bekci','reports',ym)
  const mdAudit = path.join(dir,'MD_AUDIT_SUMMARY.txt')
  const incAudit = path.join(dir,'INCOMPLETE_PROCESSES_AUDIT.txt')
  const out = path.join(dir,'NEXT_HYBRID_STEPS.txt')
  const a = read(mdAudit)
  const b = read(incAudit)
  const lines = []
  lines.push('# Next Hybrid Steps')
  lines.push('\n## Cleanup Targets')
  lines.push(a ? a.split('\n').slice(0,50).join('\n') : '-')
  lines.push('\n## Incomplete Topics')
  lines.push(b ? b.split('\n').slice(0,100).join('\n') : '-')
  lines.push('\n## Actions')
  lines.push('- Standardize portal IDs to unified portal_ids field (adapter level)')
  lines.push('- Centralize CRM listing/person summaries via CRMDataAggregator')
  lines.push('- Apply Context7 compliance checks in CI and pre-commit')
  lines.push('- Remove outdated/duplicate markdown after review')
  try { fs.writeFileSync(out, lines.join('\n'),'utf8') } catch {}
}

main()