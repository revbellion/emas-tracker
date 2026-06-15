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
- Root route changed to redirect based on auth state.
- GuestLayout.vue restyled with gold accent, dark mode, card border.
- Login/Register pages gold-themed; PrimaryButton, TextInput, InputLabel updated to gold theme.
- `maatwebsite/excel` installed; export classes created for Transactions, Portfolio, Profit.
- Export buttons added to all report pages.
- Chart.js + vue-chartjs installed; Dashboard now shows interactive gold-themed line chart.
- AntamProvider & PegadaianProvider rewritten to scrape `harga-emas.org` (Next.js site, 7‑table layout) – sync now works.
- Sync saves to `gold_prices` table; both Antam (Buy 2.863.000, Sell 2.576.700) and Pegadaian (Buy 2.739.000, Sell 2.420.000) working.
- Family accounts inserted: HENDRIK, BAPAK, IBUK, ZASKIA with total capital and gram balance.
- Transaction create form now has toggle **By Gram / By Rupiah** for Buy and Sell.
- `parseIdr()` utility normalizes `.` and `,` in number inputs; inputs use `type="text"` with `inputmode="decimal"`.

### In Progress
- (none)

### Blocked
- (none)

## Key Decisions
- Used Inertia `router.visit` for filter/sort on report pages – preserves state, avoids full reload.
- `parseIdr()` strips dots, replaces comma with dot before `parseFloat` – works for Indonesian and standard formats.
- Providers scraped from `harga-emas.org` instead of external APIs – simpler, zero API keys.
- Provider names simplified (`Antam` not `Antam (via harga-emas.org)`) for clean URL parameters.

## Next Steps
- Test manual price setting: Settings → Atur Harga Manual → verify updates dashboard & portfolio.
- Consider adding a badge/indicator in Dashboard showing whether current price is from "manual" or "sync".

## Critical Context
- App at `http://127.0.0.1:8000/`, login with `test@example.com` / `password`.
- Database clean except test user and 4 family accounts; gold prices from sync exist.
- Build and all 25 tests pass after every change.
- `harga-emas.org` is a Next.js site; scraping relies on `DOMDocument` + XPath on static HTML tables.
- `Welcome.vue` orphaned but still exists.

## Relevant Files
- `routes/web.php`: root redirect, all auth/CRUD/report/export routes.
- `resources/js/Pages/Auth/Login.vue`, `Register.vue`: gold-themed styling, dark mode.
- `resources/js/Layouts/GuestLayout.vue`: gold logo, card, dark mode background.
- `resources/js/Components/PrimaryButton.vue`, `TextInput.vue`, `InputLabel.vue`: gold accent, dark mode.
- `resources/js/Pages/Dashboard.vue`: Chart.js line chart (gold) for portfolio growth.
- `resources/js/Pages/Transactions/Index.vue`, `resources/js/Pages/Reports/*.vue`: filter/search/sort UI + export buttons.
- `resources/js/Pages/Transactions/Create.vue`: By Gram / By Rupiah toggle, `parseIdr` normalization.
- `app/Services/PriceProviders/AntamProvider.php`, `PegadaianProvider.php`: rewritten scrapers for `harga-emas.org`.
- `app/Services/GoldPriceSyncService.php`, `app/Http/Controllers/SettingController.php`: sync logic, route, and manual price setting.
- `app/Http/Requests/StoreManualPriceRequest.php`: validation for manual price input (buy_price, sell_price, optional date).
- `resources/js/Pages/Settings/Index.vue`: manual price form with `parseIdr`/`onInput` filters + flash success/error banners.
- `app/Exports/TransactionsExport.php`, `PortfolioExport.php`, `ProfitExport.php`: Excel exports.
- `app/Services/ReportService.php`: sort support added to `transactionHistory` and `goldPriceHistory`.
- `database/seeders/DatabaseSeeder.php`: only test user (demo seeders removed).
