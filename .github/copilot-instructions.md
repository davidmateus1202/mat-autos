# Copilot Instructions - Venta de Carros App

## Project Overview
Full-stack car sales management system built with **Laravel 11 API + Vue 3 SPA** (Vite + Tailwind + Sanctum). Handles car inventory, expenses per vehicle, sales transactions, and financial management (accounts, loans, consolidated statements).

## Architecture

### Backend (Laravel 11)
- **API-only** at `/api/v1` with Sanctum authentication
- **Models**: `Brand`, `Car`, `CarExpense`, `CarSale`, `FinancialAccount`, `BankLoan`, `BankLoanPayment`, `FinancialMovement`
- **Financial engine**: All monetary operations create `FinancialMovement` records. Balances computed from movements, never stored directly
- **Validation**: Use FormRequest classes for all write operations
- **Storage**: Car and expense images stored via `storage:link` (public disk)

### Frontend (Vue 3 in `/resources/js`)
- **SPA router** (`/resources/js/router/index.js`) with auth guards
- **State**: Pinia stores (`useAuth`, `useCars`, `useFinance`, `useDashboards`)
- **Axios** with CSRF/Sanctum interceptors (401 → auto-redirect to login)
- **Compiled via Vite**: Entry is `resources/js/app.js`, loaded with `@vite('resources/js/app.js')`

### Key Integration Points
- **Sanctum CSRF**: All POST/PUT/DELETE requests require CSRF token from `/sanctum/csrf-cookie`
- **Image uploads**: Use `Content-Type: multipart/form-data` for `CarExpense` images
- **API responses**: Always `{ data: {...}, meta: {...} }` for success, `{ errors: {...} }` for 422

## Financial Rules (Critical)

### Car Purchase Flow
1. Create `Car` record (status: `available`)
2. Creates `FinancialMovement`: `car_purchase`, **debits** account (reduces balance)
3. Total cost = purchase_price + accumulated `car_expenses.amount`

### Car Expense Flow
1. Create `CarExpense` linked to `Car`
2. Creates `FinancialMovement`: `car_expense`, **debits** account
3. Recalculates car's `total_cost`

### Car Sale Flow
1. Update `Car` with `sale_price`, `sale_date`, `sold_at` timestamp
2. Creates `FinancialMovement`: `car_sale`, **credits** account (increases balance)
3. Compute: `gross_profit = sale_price - purchase_price`, `net_profit = sale_price - total_cost`

### Bank Loan Flow
- **Approved** (`status: approved`): No financial impact yet
- **Disbursed** (`status: disbursed`): Creates `FinancialMovement` **crediting** account with `disbursed_amount`
- **Payments**: Split into principal + interest. Principal reduces `remaining_balance`, interest is expense

### Balance Calculation
```
available_balance = SUM(movements.amount) for account
  where movements.amount > 0 (credits) - movements.amount < 0 (debits)
```

## UI/UX Conventions

### Mobile-First Responsive (Tailwind)
- **Breakpoints**: Design for ≤375px first, then `md:` (768px), `lg:` (1024px), `xl:` (1280px)
- **Tables → Cards on mobile**: 
  - Cars list: `<DataTable>` on md+, `<CardList>` on sm
  - Use `hidden md:table` for tables, `block md:hidden` for cards
- **Filters**: Off-canvas drawer on mobile, visible sidebar on desktop
- **Sticky headers**: `sticky top-0 z-10` for table headers and page toolbars

### Component Library (`/resources/js/components/ui`)
- `<AppContainer>`: Wrapper with `mx-auto max-w-7xl px-4 sm:px-6 lg:px-8`
- `<PageHeader>`: Title + breadcrumbs + action buttons
- `<DataToolbar>`: Search input (debounced 300ms) + filters + status chips + "New" button
- `<DataTable>`: Sticky headers, hover rows, action columns
- `<CardList>`: Mobile-optimized card grid for data lists
- `<StatTile>`: KPI display (available cash, invested, monthly sales/expenses/profits)
- `<EmptyState>`, `<ErrorState>`, `<SkeletonRows>`: For loading/empty/error states

### Tailwind Patterns
```css
/* Containers */
.container-responsive { @apply mx-auto max-w-7xl px-4 sm:px-6 lg:px-8; }

/* Cards */
.card { @apply bg-white dark:bg-zinc-900 rounded-xl shadow-sm ring-1 ring-zinc-200/70 dark:ring-zinc-800; }

/* Inputs */
.input { @apply w-full rounded-lg border-zinc-300 dark:border-zinc-700 focus:border-primary focus:ring-primary; }

/* Buttons */
.btn-primary { @apply inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-white hover:opacity-90 focus-visible:ring-2; }

/* Status chips */
.chip-available { @apply bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300; }
.chip-sold { @apply bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300; }
```

### Dark Mode
- Strategy: `class` with `.dark` on `<html>`
- All components must support `dark:*` variants
- Color palette defined in `tailwind.config.js` (primary, surface, muted, success, warning, danger)

### Accessibility (AA)
- **Contrast**: All text meets WCAG AA (4.5:1 minimum)
- **Focus states**: Always `focus-visible:ring-2 focus-visible:ring-primary`
- **Touch targets**: Minimum `min-h-[44px]` for interactive elements
- **ARIA**: Use `aria-label`, `aria-describedby`, proper roles on custom components

## Key Pages & Workflows

### Cars/Index.vue
- **Mobile**: Card list with brand+model+year, status chip, prices, "Add Expense" / "Record Sale" / "View Detail" actions
- **Desktop**: Table with columns: Brand | Model | Year | Status | Purchase Price | Expenses | Sale Price | Net Profit | Actions
- **Filters**: Status (available/sold), brand, year range, purchase date range
- **Search**: Debounced 300ms on model, VIN, plate

### Cars/Show.vue
Tabs: **Summary** (purchase info + calculated KPIs in stat tiles), **Expenses** (list + add form with optional image), **Sale** (sale form if available, details if sold), **History** (timeline of movements)

### Finance/Index.vue
Sections: **Accounts** (list with balances, "New Account" button), **Loans** (list with status, "New Loan" / "Disburse" / "Pay" actions), **Consolidated** (stat tiles for total available, invested in cars, monthly stats)

### Dashboards/Index.vue
- KPI tiles (available, invested, sold this month, expenses this month, gross/net profit this month)
- Chart: "Sales by Brand" (bar chart)
- Chart: "Monthly Stats" (line chart for sales count, expenses, profits)

## Development Workflows

### Setup (Fresh Install)
```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev
```

### Running
```bash
# Terminal 1: Laravel API
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

### API Endpoints (Reference)
- `GET /api/v1/cars?status=available&brand_id=1&search=corolla&page=1`
- `POST /api/v1/cars` - Body: `{ brand_id, model, year, vin, purchase_price, purchase_date, account_id }`
- `POST /api/v1/cars/{id}/expenses` - Body: `{ concept, amount, expense_date, account_id, image? }` (multipart)
- `POST /api/v1/cars/{id}/sale` - Body: `{ sale_price, sale_date, account_id }`
- `GET /api/v1/dashboards/summary` - Returns all KPIs
- `GET /api/v1/dashboards/sales-by-brand` - Chart data
- `GET /api/v1/dashboards/monthly-stats?months=12` - Time series

### Testing
```bash
# Backend
php artisan test --filter=CarTest
php artisan test --filter=FinancialServiceTest

# Frontend
npm run test:unit
npm run test:e2e
```

## Common Patterns

### Creating a New API Resource
1. Model + migration (`php artisan make:model X -m`)
2. FormRequest for validation (`php artisan make:request StoreXRequest`)
3. Resource transformer (`php artisan make:resource XResource`)
4. Controller method returning `XResource::collection($items)` or `new XResource($item)`

### Creating a New Vue Page
1. Create `resources/js/pages/X/Index.vue`
2. Add route in `resources/js/router/index.js` with `meta: { requiresAuth: true }`
3. Create Pinia store `resources/js/stores/useX.js` with actions calling API
4. Use `<AppContainer>` + `<PageHeader>` + `<DataToolbar>` layout pattern

### Adding a Financial Operation
1. Create service method in `app/Services/FinancialService.php`
2. Always create `FinancialMovement` record with: `account_id`, `type`, `amount` (+/-), `description`, `movementable_*` (polymorphic)
3. **Never** update account balance directly - always recompute from movements

## Gotchas & Anti-Patterns

❌ **Don't**: Store computed values (balances, totals) in database  
✅ **Do**: Compute from movements/expenses in real-time or cache with explicit invalidation

❌ **Don't**: Use `v-if` for responsive visibility (breaks SSR-style hydration)  
✅ **Do**: Use Tailwind `hidden md:block` classes

❌ **Don't**: Hardcode color hex values in Vue templates  
✅ **Do**: Use Tailwind color utilities from `tailwind.config.js`

❌ **Don't**: Create bank loan movements on status `approved`  
✅ **Do**: Only create movement when status changes to `disbursed`

❌ **Don't**: Use generic validation errors  
✅ **Do**: Return Laravel 422 with field-specific errors; Vue displays inline with `v-for` over `errors.fieldName`

## Questions for Clarification
1. Should car reversal (unselling) be allowed for non-admin roles, or admin-only?
2. Do we need multi-currency support, or single currency (COP/USD) is sufficient?
3. Should loan interest accrue automatically via scheduled job, or only on manual payment?
4. Are there specific performance requirements for dashboard queries (caching strategy)?
