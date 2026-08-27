# AGENTS.md — KSP KOPINKA

## Stack

- **Backend**: Laravel 13 + PHP 8.2+ + Eloquent ORM
- **Frontend**: Inertia.js v3 + React 19 + TypeScript + Tailwind CSS 4
- **UI Kit**: shadcn/ui (new-york style) + Radix primitives + Lucide icons
- **Database**: PostgreSQL (tests run against real pgsql dev DB; `pgsql usp_kopinka`)
- **Testing**: Pest PHP v4
- **Bundler**: Vite 7 with `laravel-vite-plugin`
- **Routing (JS)**: Ziggy — `route()` is a global function available in every React component

## Commands

| Task | Command |
|---|---|
| Start full dev stack (server + queue + vite) | `composer dev` |
| Start only Vite (frontend hot-reload) | `npm run dev` |
| Build for production | `npm run build` |
| Run all tests | `composer test` (runs `php artisan config:clear && php artisan test`) |
| Run a single Pest test file | `vendor/bin/pest tests/Feature/TransaksiSimpananTest.php` |
| Run tests filtered by name | `vendor/bin/pest --filter="store setoran"` |

No lint, format, or typecheck commands are configured. There is no CI pipeline.

## Architecture

This is a **cooperative savings & loan app** (Koperasi Simpan Pinjam). All routes live under `superadmin.` prefix with `auth` + `role:superadmin` middleware.

### Backend

- **Entry point**: `bootstrap/app.php` — registers `HandleInertiaRequests` middleware and `web.php` routes.
- **Controllers**: `app/Http/Controllers/Superadmin/` — 29 controllers. Each follows the same CRUD pattern: `index` returns paginated data via `inertia('Superadmin/<Module>/Index', ...)`, `store` validates and redirects.
- **Models**: `app/Models/` — 46 Eloquent models. No service classes; business logic lives in controllers and models.
- **PDF views**: `resources/views/pdf/` — Blade templates for PDF export (DomPDF).
- **Route registration**: `routes/web.php` — many routes are generated via `foreach` loops for variant-based modules (kolektif, berjangka, laporan). Do not manually add routes that match these patterns.
- **Wilayah data**: Uses `laravolt/indonesia` package for province/city/district/village dropdowns.

### Frontend

- **Inertia entry**: `resources/js/app.tsx` — uses `import.meta.glob` for lazy-loaded page chunks + idle prefetch.
- **Page resolution**: Inertia resolves `Superadmin/Kantor/Index` → `resources/js/Pages/Superadmin/Kantor/Index.tsx`.
- **Path alias**: `@/` maps to `resources/js/` (configured in both `tsconfig.json` and `vite.config.js`).
- **Shared layout**: All superadmin pages wrap in `AuthenticatedLayout.tsx` which provides sidebar, navbar, flash toasts.
- **Sidebar menu**: Defined as a `MENU` array in `AuthenticatedLayout.tsx`. When adding a new page, add its route here.
- **Type definitions**: `resources/js/types/models.ts` — shared TypeScript interfaces for all domain entities. Add new types here.
- **Global types**: `resources/js/types/global.d.ts` — declares `route()` (Ziggy) and `SharedProps`.
- **shadcn/ui components**: `resources/js/Components/ui/` — 16 components. Use these, not raw HTML.
- **Custom components**: `resources/js/Components/` — `ConfirmDelete`, `DenominationCalculator`, `PageHeader`, `Pagination`, `SignaturePanel`, `WilayahSelect`.
- **CSS entry**: `resources/css/react.css` — Tailwind 4 with custom KOPINKA brand colors (`brand-50` to `brand-900`) and dark sidebar palette (`night-700` to `night-900`).
- **Utility**: `resources/js/lib/utils.ts` — exports `cn()` for class merging (clsx + tailwind-merge).

### Adding a new CRUD page (the pattern)

1. Create controller in `app/Http/Controllers/Superadmin/` returning `inertia('Superadmin/<Name>/Index', [...])`.
2. Create React page at `resources/js/Pages/Superadmin/<Name>/Index.tsx` (plus Create/Edit/Show as needed).
3. Page wraps content in `<AuthenticatedLayout>` and uses `Head` from `@inertiajs/react`.
4. Add routes in `routes/web.php`.
5. Add TypeScript types in `resources/js/types/models.ts`.
6. Add sidebar entry in `AuthenticatedLayout.tsx` MENU array.

## Testing

- Tests are **self-contained**: each test creates its own data (prefixed `TEST-`, `KT-`, `REK-`, etc.) and cleans up after itself.
- **No `RefreshDatabase`** — tests run against the real database. Do not assume a clean DB.
- Test base class: `tests/TestCase.php` (no customizations). Pest config: `tests/Pest.php` binds `Tests\TestCase` and only applies to `Feature/`.
- Run with `vendor/bin/pest`, not `phpunit`.

## Conventions

- **Language**: UI is in Indonesian. Validation messages, flash messages, labels — all Bahasa Indonesia.
- **Route names**: Always prefixed `superadmin.` (e.g. `superadmin.kantor`, `superadmin.transaksi-simpanan.setoran-simpanan`).
- **Route model binding**: Controllers use Laravel's implicit route model binding (e.g. `Kantor $kantor`).
- **Flash messages**: After store/update/destroy, controllers use `->with('flash.status', '...')`. The layout renders these as toast notifications.
- **Validation errors**: Custom Indonesian messages passed as second arg to `$request->validate([...], [...])`.
- **Pagination**: Controllers use `->paginate()` which returns a `Paginated<T>` structure on the frontend.
- **No ESLint/Prettier/Pint config**: Code style follows Laravel/React defaults. 4-space indent (`.editorconfig`).
- **Ziggy**: `route()` is globally available in React via `@routes` Blade directive. Use it for all route generation — never hardcode URLs.

## Gotchas

- The `route()` Ziggy helper uses **Laravel route names**, not file paths. Example: `route('superadmin.kantor.show', item.id)`.
- The `resources/css/react.css` file has duplicate scrollbar CSS blocks (copy-paste artifact). Don't add more duplicates.
- Vite build uses `manualChunks` to split vendor bundles — if adding a new large dependency, check if it needs a new chunk entry.
- Tests use `->assertInertia()` to verify the correct Inertia component name is rendered. The component path matches the file path under `Pages/`.
- The `scratch/` directory contains temporary utility scripts — not part of the app.
