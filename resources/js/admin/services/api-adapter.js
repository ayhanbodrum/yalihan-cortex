const ApiAdapter = (function () {
  const BASE = '/api/admin/api/v1'
  const tokenEl = document.querySelector('meta[name="csrf-token"]')
  const CSRF = tokenEl ? tokenEl.getAttribute('content') : null

  function qs(params = {}) {
    const sp = new URLSearchParams()
    Object.entries(params).forEach(([k, v]) => {
      if (v !== undefined && v !== null) sp.append(k, String(v))
    })
    const s = sp.toString()
    return s ? `?${s}` : ''
  }

  async function request(method, path, { params, body, headers } = {}) {
    const url = `${BASE}${path}${qs(params)}`
    const h = Object.assign({}, headers || {}, {
      'Accept': 'application/json',
    })
    if (CSRF) h['X-CSRF-TOKEN'] = CSRF
    if (method !== 'GET' && body && !(body instanceof FormData)) {
      h['Content-Type'] = 'application/json'
      body = JSON.stringify(body)
    }
    const res = await fetch(url, { method, headers: h, body })
    const json = await res.json().catch(() => ({ success: false, message: 'Geçersiz JSON' }))
    const out = {
      status: json.success === true,
      message: json.message || '',
      data: json.data ?? null,
      errors: json.errors ?? null,
      meta: json.meta ?? null,
      timestamp: json.timestamp ?? null,
      httpStatus: res.status,
    }
    if (!out.status || res.status >= 400) {
      const err = new Error(out.message || `HTTP ${res.status}`)
      err.response = out
      throw err
    }
    return out
  }

  return {
    get: (path, params = {}) => request('GET', path, { params }),
    post: (path, body = {}, headers = {}) => request('POST', path, { body, headers }),
    put: (path, body = {}, headers = {}) => request('PUT', path, { body, headers }),
    delete: (path, body = {}, headers = {}) => request('DELETE', path, { body, headers }),
  }
})()

try { if (typeof window !== 'undefined') window.ApiAdapter = ApiAdapter } catch (e) {}

export default ApiAdapter