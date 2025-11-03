# 🔎 Deep Research – UI/UX and Context7 Findings (2025-10-20)

## Priority Order (Do First → Later)
1) Stable-Create skeleton + retry + disable flow for cascades
2) TalepController: provide datasets + eager loading + filters
3) Forbidden patterns cleanup (status/is_active/aktif/sehir)
4) Link Health in CI (block on non-200)
5) AI suggest timeout/retry + user-friendly toasts
6) Neo Design System standardization (neo-input/select/switch/btn/card)
7) Currency inputs: mask/format + write-as-text backend and show
8) CSP connect-src allowlist for AI hosts + rate limit & error logs
9) Dynamic features: loading states, reset on selection change
10) A11y: labels/aria, focus rings, aria-busy for loaders

## Concrete Issues Observed
- Stable-Create
  - Console-only error reporting on fetch failures (alt_kategori, yayin_tipi, features, ilce, mahalle)
  - Limited skeleton/empty states; missing retry CTA
  - Mixed use of generic classes; Neo classes present but not pervasive
- Talep (Demands) Index
  - Controller does not pass required datasets (`talepler`, `statuslar`, `$kategoriler`, `$ulkeler`, `$talepTipleri`)
  - Potential N+1 risk without eager loading
- Compliance
  - Legacy field traces exist (status family, location variants) in reports; must keep DB/Model/View aligned
- AI UX
  - No unified timeout/retry; technical error messages leak to console
- CI/Ops
  - Link health checks exist locally; not enforced in CI

## Prescriptive Fix Patterns
- Fetch Helper (single source)
  - Timeout 10–12s, single retry (exponential backoff), CSRF header, JSON guard
  - On error → toast.error("Yüklenemedi, tekrar dener misiniz?") + Retry button
- Cascaded Selects (Category → Sub → Publication → Features)
  - On parent change: disable child selects, clear values, show skeleton
  - On success: populate options, enable, remove skeleton
  - On failure: show empty state + retry CTA
- Neo Standardization
  - Inputs: neo-input; Selects: neo-select; Switches: neo-switch; Buttons: neo-btn; Cards: neo-card
  - Grouping: neo-form-group; Tables: neo-table within neo-table-responsive
- TalepController (index)
  - with(['kisi','kategori','il','ilce']) + paginate(20)
  - Filters: search, status, alt_kategori_id, ulke_id, date range
  - Provide datasets: $statuslar, $kategoriler, $ulkeler, $talepTipleri
- Currency UX
  - TR mask (1.234.567,89) + para_birimi select (TRY/USD/EUR/GBP)
  - Backend: amount-to-words; Detail page: textual display under numeric
- Security
  - CSP connect-src: include AI host from Settings/env
  - Rate limit AI endpoints; log latency, provider, cost

## Acceptance Criteria (Critical)
- Stable-Create: all cascades render skeleton within 100ms; resolve < 1s over fast link
- Form submit blocks when required missing; first error scrolled & focused
- AI suggest: visible spinner, cancel, friendly error on failure; 0 console errors
- Link Health CI: any non-200 critical page fails the pipeline
- No legacy forbidden patterns in queries/models/migrations (Context7)

## Notes for Yalıhan Bekçi (Learning)
- Prefer Vanilla JS + Alpine; no heavy selects; debounce 300ms for live search
- Keep per-page JS gzipped < 50KB; current stable-create ≈ 11.57KB gzipped
- Always use Context7-compliant field names in server code; UI strings can be Turkish
