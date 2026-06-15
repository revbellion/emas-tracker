## Goal
- Customize the gold tracking application with gold-themed UI, real-time gold price sync, Excel exports, search/sort, Chart.js dashboard, and Indonesian Rupiah input formatting.

## Constraints & Preferences
- Root `/` must redirect: authenticated → dashboard, unauthenticated → login.
- Authenticated users must not be able to access login page.
- Gold accent (`yellow-600`) used throughout UI, consistent dark mode support.
- Seeders removed per user request – only test user exists in database.
- Number inputs must accept Indonesian format (`.` thousands separator, `,` decimal separator).
- Transactions must support both "By Gram" and "By Rupiah" input modes.

## Progress
### Done
- Root route redirects based on auth state; Guest/Login/Register gold-themed with dark mode.
- Excel exports for Transactions, Portfolio, Profit reports.
- Chart.js line chart on Dashboard with date range presets (1W, 1M, 3M, 6M, 1Y, YTD).
- Antam & Pegadaian providers scraped from `harga-emas.org` – sync working; case-sensitivity bug fixed via `strtolower()` keys in `GoldPriceSyncService`.
- All `type="number"` inputs replaced with `type="text" inputmode="decimal"`, `onInput` filter + `parseIdr` normalization on all number fields.
- "By Gram / By Rupiah" toggle for Buy and Sell in Create transaction form.
- Family accounts (HENDRIK, BAPAK, IBUK, ZASKIA) populated with full historical transactions.
- `transaction_date` changed from `date` to `datetime` via migration; Create.vue uses `type="datetime-local"`.
- Dashboard layout reordered: Harga Emas Hari Ini card above overview cards.
- Favicon changed to gold bar SVG ("AU"); ApplicationLogo replaced with same gold bar SVG.
- Scheduler configured in `routes/console.php`: Antam 08:00 & 12:00, Pegadaian 16:00.
- Manual price input added to Settings page.
- 7 performance fixes: Provider memoization, redundant load removed, gold price caching, accessor 5→1 query, Vite manualChunks, missing indexes migration, font non-blocking.
- All tables responsive: `overflow-x-auto` wrapper + `hidden sm:table-cell` on less important columns.
- Left sidebar toggle button hidden on mobile (`hidden sm:inline-flex`).
- Dockerfile created for Render deployment (PHP 8.3-cli + Composer + Node).

### In Progress
- Deploy to Render: pending Aiven MySQL setup + Docker settings in Render dashboard.

### Blocked
- `pegadaian.co.id/harga-emas` is Nuxt.js (Vue SSR) – prices loaded via internal API call, not in static HTML. Not scrapable with `DOMDocument` + XPath.
- `logamulia.com` uses fingerprint.js anti-bot protection – not scrapable with simple HTTP.
- `antam.com` returns 403 Forbidden.

## Key Decisions
- Used Inertia `router.get` for date range filter on dashboard chart – preserves state, avoids full reload.
- `parseIdr()` strips dots, replaces comma with dot before `parseFloat` – works for Indonesian and standard formats.
- Providers scraped from `harga-emas.org` instead of external APIs – simpler, zero API keys.
- Manual price input chosen over additional scrapers/APIs.
- Dockerfile used for Render deploy (no native PHP runtime on Render).

## Next Steps
- Deploy to Render: Create Aiven MySQL → Create Render Web Service (Docker) → Set env vars → Deploy.
- Consider adding a badge/indicator on Dashboard showing whether current price source is "manual" or "sync".
- Schedule gold price sync on Render via external cron service (cron-job.org free).

## Critical Context
- **Production URL:** `https://emas-tracker.onrender.com`
- **Database:** Aiven MySQL (free tier, SSL required)
- **Auth:** Register new account via app (no seeded test user in production)
- **APP_KEY:** `base64:SMbI5m/WyhirAPkArOGM7ertPcp1XTuq688vY4647Ls=`
- Build and all 25 tests pass after every change.
- `config/database.php` reads env vars (`DB_HOST`, `DB_PORT`, etc.) – no code change needed for Aiven.
- SSL: set `MYSQL_ATTR_SSL_CA` env var (Render system CA at `/etc/ssl/certs/ca-certificates.crt`).
- Left sidebar button hidden on mobile (`hidden sm:inline-flex` in `AuthenticatedLayout.vue`).
- All tables responsive via `overflow-x-auto` + `hidden sm:table-cell` on secondary columns.

## Relevant Files
- `Dockerfile`: PHP 8.3-cli + Composer + Node, `php artisan serve` for Render deploy.
- `.dockerignore`: excludes .git, .env, node_modules, vendor, storage cache.
- `config/database.php`: MySQL config supports `MYSQL_ATTR_SSL_CA` for SSL.
- `routes/web.php`: root redirect, all auth/CRUD/report/export routes.
- `routes/console.php`: scheduled gold price sync tasks.
- `resources/js/Layouts/AuthenticatedLayout.vue`: sidebar toggle hidden on mobile.
- `resources/js/Pages/Transactions/Index.vue`, `resources/js/Pages/Reports/*.vue`: responsive tables.
