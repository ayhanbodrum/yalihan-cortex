import AIService from '../../admin/services/AIService.js'
import { FormValidator } from '../../admin/form-validator.js'

function ensureStatus(el) {
  let status = el.querySelector('[data-ai-status]')
  if (!status) {
    status = document.createElement('div')
    status.setAttribute('data-ai-status', 'true')
    status.setAttribute('role', 'status')
    status.setAttribute('aria-live', 'polite')
    status.className = 'mt-2 text-sm text-gray-600'
    el.appendChild(status)
  }
  return status
}

function collectContext(form) {
  const data = {}
  const inputs = form.querySelectorAll('input, select, textarea')
  inputs.forEach(function (i) {
    const k = i.name || i.id
    if (!k) return
    const v = i.type === 'checkbox' ? i.checked : i.value
    data[k] = v
  })
  return data
}

async function handleSuggest(form) {
  const status = ensureStatus(form)
  status.textContent = 'Öneriler yükleniyor…'
  status.setAttribute('aria-busy', 'true')
  const validator = new FormValidator('#' + (form.id || ''))
  const required = form.querySelectorAll('[data-ai-required]')
  let ok = true
  required.forEach(function (el) {
    if (!el.value || String(el.value).trim() === '') { validator.showFieldError(el, 'Bu alan zorunludur'); ok = false }
  })
  if (!ok) { status.textContent = 'Eksik alanlar var'; status.setAttribute('aria-busy', 'false'); return }
  try {
    const context = collectContext(form)
    const res = await AIService.suggestFeatures(context, { rateMs: 300 })
    status.setAttribute('aria-busy', 'false')
    if (!res || res.status === false) { status.textContent = 'Öneri alınamadı'; return }
    const box = form.querySelector('[data-ai-suggestions]') || ensureStatus(form)
    box.textContent = ''
    if (Array.isArray(res.data)) {
      const ul = document.createElement('ul')
      ul.className = 'mt-2 space-y-1'
      res.data.forEach(function (s) {
        const li = document.createElement('li')
        li.textContent = String(s && s.label ? s.label : s)
        ul.appendChild(li)
      })
      box.appendChild(ul)
    } else if (res.data && res.data.html) {
      box.innerHTML = res.data.html
    } else {
      box.textContent = JSON.stringify(res.data)
    }
  } catch (e) {
    status.setAttribute('aria-busy', 'false')
    status.textContent = 'Hata: ' + (e && e.message ? e.message : 'Bilinmeyen hata')
  }
}

function init(selector = '[data-ai-suggest="true"]') {
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll(selector).forEach(function (form) {
      const btn = form.querySelector('[data-ai-suggest-button]')
      const status = ensureStatus(form)
      status.textContent = ''
      if (btn) {
        btn.addEventListener('click', function (e) { e.preventDefault(); handleSuggest(form) })
      }
    })
  })
}

try { if (typeof window !== 'undefined') window.FeatureSuggest = { init } } catch (e) {}

export default { init }