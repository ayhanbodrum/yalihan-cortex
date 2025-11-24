import fs from 'fs'
import path from 'path'

async function main() {
  const base = process.env.BASE_URL || 'http://localhost'
  const url = base.replace(/\/$/, '') + '/api/admin/ai/analytics'
  let data = null
  try {
    const res = await fetch(url, { headers: { Accept: 'application/json' } })
    const json = await res.json()
    data = json && (json.data || json) || null
  } catch (e) {
    data = null
  }
  const now = new Date()
  const ym = String(now.getFullYear()) + '-' + String(now.getMonth() + 1).padStart(2, '0')
  const dir = path.join(process.cwd(), 'yalihan-bekci', 'reports', ym)
  const file = path.join(dir, 'ai-analytics-summary.txt')
  try { fs.mkdirSync(dir, { recursive: true }) } catch {}
  const lines = []
  if (data && typeof data === 'object') {
    lines.push('response: ' + (data.average_response_time != null ? Math.round(Number(data.average_response_time)) + ' ms' : '-'))
    lines.push('cancelled: ' + (data.cancelled_requests != null ? String(data.cancelled_requests) : '-'))
    lines.push('errors: ' + (data.failed_requests != null ? String(data.failed_requests) : '-'))
    lines.push('total: ' + (data.total_requests != null ? String(data.total_requests) : '-'))
    lines.push('success: ' + (data.successful_requests != null ? String(data.successful_requests) : '-'))
  } else {
    lines.push('response: -')
    lines.push('cancelled: -')
    lines.push('errors: -')
    lines.push('total: -')
    lines.push('success: -')
  }
  try { fs.writeFileSync(file, lines.join('\n'), 'utf8') } catch {}
}

main()