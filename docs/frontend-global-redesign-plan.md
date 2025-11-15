## Frontend Global Redesign Blueprint

### Goals
- Build a fresh, Context7-compliant frontend focused on international listings.
- Preserve existing Laravel data flows while replacing legacy markup with modern Tailwind components.
- Provide consistent UX across desktop and mobile with dark mode and smooth transitions.

### Layout Structure
- `resources/views/layouts/frontend.blade.php`
  - Extend to support global navigation slots (language + currency switchers).
  - Extract navigation and footer into Blade components for reuse.
- New shared components (Blade):
  - `components/frontend/header-switcher.blade.php`
  - `components/frontend/category-tabs.blade.php`
  - `components/frontend/property-card-global.blade.php`
  - `components/frontend/ai-guide-card.blade.php`
  - `components/frontend/currency-badge.blade.php`

### Page Inventory
- `resources/views/frontend/ilanlar/international.blade.php`
  - Hero + AI guide CTA section.
  - Tabbed quick filters (Satılık, Kiralık, Yazlık, Vatandaşlık).
  - Advanced filter sidebar with country/district, citizenship eligibility, price, property type.
  - Responsive property grid using `property-card-global`.
  - “Vatandaşlık Programları” accordion & FAQ section.
- Existing pages (`ilanlar/index`, `frontend/portfolio`, etc.) will adopt the new layout and components incrementally.

### Data & Filters
- Controller (`IlanPublicController@index`) enhancements:
  - Accept `country`, `city`, `citizenship_eligible`, and `type` parameters.
  - Provide category tree and currency metadata to the view.
- Introduce `App\Services\CurrencyConversionService` (check for existing helper first).
- Cache category/alt-category lookups for performance.

### Interaction Patterns
- Tailwind-only styling with `transition-all duration-300 ease-out` defaults.
- Dark mode variants for every section (`dark:bg-slate-900`, `dark:text-slate-100`).
- Mobile-first: sticky bottom action bar with quick access to filters, AI guide, currency, language.
- AI integration uses `AIService::analyze` with `/api/admin/ai/analyze` endpoint.

### Tailwind Config Updates
- Enable plugins: `@tailwindcss/forms`, `@tailwindcss/line-clamp`, `@tailwindcss/typography`.
- Define component presets with `@layer components` for buttons, badges, cards (Context7-safe names only).
- Document gradient backgrounds and spacing scale for consistency.

### Compliance Checklist
- No forbidden field or class names (e.g., `neo-*`, `btn-*`).
- Routes follow `ilanlar.*` patterns; no deprecated namespaces.
- Components documented in `docs/frontend-global-components.md` (to be created after implementation).
- Run `php artisan context7:check` and `php context7_final_compliance_checker.php` before delivery.

### Next Steps
1. Refactor layout into components and add language / currency switcher skeletons.
2. Create the international listings page scaffolding with hero, tabs, and filter sections.
3. Build reusable property card and AI guide components.
4. Hook up controller data, currency conversion helper, and AI endpoint wiring.
5. Perform responsive and dark mode QA, then run compliance checks.

