# HandyChef — Project Context for Claude Code

This file is read automatically by Claude Code at the start of every session in this
directory. It captures full context from a prior chat-based session (using Claude +
Desktop Commander) so you don't need to be re-briefed.

## What this project is

**HandyChef** — a Final Year Project (Software Engineering degree), Laravel meal
subscription platform for ghost kitchens. **Not a delivery app** — customers pick up
meals themselves, no delivery logic anywhere.

Flow: Ghost Kitchen creates meal plans → Customer browses & subscribes → Kitchen
prepares meals daily → Customer picks up.

**Deadline pressure:** this was built under a 2-day FYP deadline. Priority is a working
MVP, not architectural purity. Don't redesign what's working — extend it.

## Tech stack & environment (Windows)

- **PHP 8.4.24** via Laravel Herd at `C:\Users\DELL\.config\herd\bin\php84\php.exe`
- **Laravel 13.23.0**, SQLite (not MySQL — deliberately switched early on for zero-config
  speed; `database/database.sqlite`)
- **Tailwind CSS v4** via `@tailwindcss/vite` (not the old `tailwind.config.js` v3 style —
  don't reintroduce those files, Breeze's Blade installer adds v3-style files by default
  and they were deliberately removed/reconciled to v4)
- **Laravel Breeze (Blade stack)** for auth — no Livewire, no Inertia, no React/Vue

### Known environment gotchas (Windows-specific)

1. **`npm`/`node` are not on PATH by default** in a fresh shell — they live at
   `C:\Program Files\nodejs`. Prepend it if `npm` isn't found.
2. **`NODE_ENV=production` is set as a persistent Windows environment variable** on this
   machine. This makes `npm install` silently skip all `devDependencies` (Vite, Tailwind,
   etc. — everything actually needed). **Always run**
   `$env:NODE_ENV='development'` before `npm install` or `npm run build`, or the build
   will silently produce nothing/stale output.
3. There was previously a conflicting old WAMP PHP 8.0.30 on System PATH shadowing
   Herd's PHP — this was removed. If `php --version` ever shows 8.0.x again, check
   System PATH for a stray `wamp64` entry.
4. Storage symlink (`php artisan storage:link`) is already created — `public/storage`
   exists, needed for meal plan/item image uploads to render via `asset('storage/...')`.
5. **`php artisan serve` silently breaks all file uploads on Windows** unless started
   with `--no-reload`. Root cause: `ServeCommand::startProcess()` strips almost every
   env var (including `TMP`/`TEMP`) from the spawned PHP subprocess unless `--no-reload`
   is passed — without a resolvable temp dir, PHP fails multipart uploads at the SAPI
   level with `PHP Request Startup: File upload error - unable to create a temporary
   file`, before Laravel/the app code ever runs. Symptom: image upload forms redirect
   as if they succeeded, but `image_path` stays null and nothing lands in
   `storage/app/public/...`. **Always run `php artisan serve --no-reload`** on this
   machine (already set in `.claude/launch.json`).

## Data model (as actually built — not the original spec verbatim)

Tables: `users` (+ `role` column: customer/ghost_kitchen/admin), `ghost_kitchens`,
`meal_plans`, `meal_items`, `subscriptions`, `subscription_items`, `pickup_schedules`.

**Important deviation from the original weekly-flat-price plan:** meal plan pricing is
now **auto-calculated** from the sum of its meal items' individual prices, not a
manually-entered flat price. `MealPlan::refreshPrice()` recalculates and saves the
plan's `price` column; it's called after any meal item create/update/delete in
`GhostKitchen\MealItemController`. The kitchen-facing plan form no longer has a price
field — it shows "Calculated automatically from the prices of its meal items."

**Multi-subscription support:** a customer can hold multiple *different* active
subscriptions simultaneously — the one-active-subscription-at-a-time rule was narrowed
to "one active subscription per meal plan" (see `Customer\SubscriptionController`).
`customer.subscription.show` now lists all active subscriptions, and
`customer.subscription.destroy` takes a `{subscription}` route param (not implicit).

**Image uploads:** `meal_plans.image_path` and `meal_items.image_path` (nullable
string), stored via `Storage::disk('public')`, uploaded through standard file inputs
(`enctype="multipart/form-data"` — already correctly set on all relevant forms).

**Pickup status lifecycle:** `pending` → `ready` → `collected` (not just
pending/ready — `PickupController@markCollected` was added, only allowed from `ready`).

## Role system

Three roles on the `users` table: `customer`, `ghost_kitchen`, `admin`. Enforced by
`App\Http\Middleware\EnsureUserHasRole` (alias `role:xyz`, supports comma-separated
multiple roles). Registration only lets the public choose Customer or Ghost Kitchen —
Admin is never self-registerable, only exists via `DatabaseSeeder`. Every
kitchen-scoped controller action also checks **resource ownership** (not just role) —
e.g. a kitchen can't edit another kitchen's meal plan even though both have the
`ghost_kitchen` role.

`/dashboard` is a smart redirector that sends the user to `customer.dashboard`,
`kitchen.dashboard`, or `admin.dashboard` based on `$user->role`.

## What's built and verified (via live HTTP requests, not just code review)

- ✅ **Auth + roles**: registration with role picker, role-based dashboard redirect,
  role middleware blocking cross-role access (tested: customer gets 403 on kitchen
  routes)
- ✅ **Ghost Kitchen meal plan CRUD**: create/edit/delete plans, add/edit/delete meal
  items (with image + price), ownership checks
- ✅ **Customer flow**: browse active plans, view plan detail, subscribe (select meal
  items), view active subscription(s) + pickup schedule, cancel a specific subscription
- ✅ **Ghost Kitchen subscribers + pickups**: view subscriber list, view pickup list,
  mark ready, mark collected
- ✅ **Admin dashboard**: platform stats (customer/kitchen/plan/subscription counts),
  manage customers (list + remove), manage kitchens (list + remove, cascades to their
  plans/items/subscriptions via FK `cascadeOnDelete`), manage meal plans (list, toggle
  active/inactive, delete). **Verified cross-cutting**: deactivating a plan as admin
  correctly hides it from customer browse.
- ✅ Since the last review pass, the person manually added the pricing/image/
  multi-subscription/mark-collected changes described above directly to the files
  (not through this chat) — I (prior session) reviewed every changed file and found
  them internally consistent and well-implemented. **These changes have NOT been
  re-tested via live HTTP requests** the way earlier steps were — worth a quick manual
  smoke test (upload an image on a meal item, confirm price recalculates, confirm it
  displays) before considering them done.

## One known minor gap (not a bug, just incomplete)

On the customer plan-detail page (`customer/meal-plans/show.blade.php`), the meal item
checkboxes show name/description/image but **not each item's individual price** — only
the plan's total weekly price is shown up top. Minor UX gap, not urgent.

## Not yet done

- **Deployment** (target: Railway, per original spec) — nothing done here yet. Will need:
  switching SQLite → whatever Railway provides (or keep SQLite if Railway supports a
  persistent volume — worth checking), environment variables, build step for Vite
  assets, `php artisan migrate --force` on deploy.
- No automated test suite (Pest/PHPUnit) was written — everything was verified via
  manual live HTTP request scripts during the chat session, which are not saved
  anywhere (they were temporary `.ps1`/`.php` scripts, deleted after each use).
  Consider writing real feature tests if time allows.

## Test accounts (seeded via `database/seeders/DatabaseSeeder.php`)

All passwords: `password`

- `admin@handychef.test` — admin
- `customer@handychef.test` — customer, has an active subscription to the seeded plan
- `kitchen@handychef.test` — ghost kitchen, owns "Test Ghost Kitchen" with "Weekly
  Balanced Plan" (3 meal items)

Reset anytime with `php artisan migrate:fresh --seed`.

## Running it locally

```powershell
cd C:\Users\DELL\Desktop\Handychef\handychef
$env:Path = 'C:\Users\DELL\.config\herd\bin\php84;C:\Program Files\nodejs;' + $env:Path
php artisan serve
# separate terminal:
$env:NODE_ENV = 'development'
npm run dev   # or npm run build for production assets
```

## Route naming conventions used throughout

- `customer.*` — customer-facing routes (prefix `/customer`)
- `kitchen.*` — ghost kitchen routes (prefix `/kitchen`)
- `admin.*` — admin routes (prefix `/admin`)

Controller class names collide across namespaces (e.g. three different
`MealPlanController`s) — `routes/web.php` imports them with aliases
(`CustomerMealPlanController`, `AdminMealPlanController`, etc). Follow that pattern if
adding more.
