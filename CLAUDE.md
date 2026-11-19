# EduConnect — Claude Code instructions

Read this file and PLAN.md before doing any work.
PLAN.md is the source of truth for scope, names, tables, routes and phase order.

## Who you are working for

Quit, an IT student. He runs and maintains this code himself and must explain every line to his professor.
So: readable over clever, Laravel defaults over custom patterns, no extra packages, no surprise changes.

## Environment (fixed, do not change)

- Windows, XAMPP installed at `C:\xampp`.
- PHP 8.5.7 (XAMPP's `C:\xampp\php\php.exe` must be the `php` on PATH).
- MySQL/MariaDB from XAMPP: host 127.0.0.1, port 3306, user `root`, empty password, database `educonnect`.
- Project root: `C:\xampp\htdocs\educonnect`
- Laravel 13.x, Laravel Breeze 2.x (Blade stack), Eloquent, Blade.
- Bootstrap 5.3.3 and Bootstrap Icons 1.11.3 by CDN for every EduConnect page. No Tailwind in new pages.
- EduConnect's own CSS and JS live in `public/css/educonnect.css` and `public/js/educonnect.js` (plain files, no Vite build needed).
- Breeze's own pages (login, register, forgot password, profile) keep Breeze's Tailwind + Vite assets. Build them with `npm run build`.
- Sanctum is NOT used. This is a session-auth Blade app with no API.
- Dev server: `php artisan serve` → http://127.0.0.1:8000
- Shell: your commands run in Git Bash on Windows. Use forward slashes in shell paths (`C:/xampp/mysql/bin/mysql.exe`).

## Working mode: one phase at a time

1. Work only on the current phase: the first unchecked phase in the "Progress" list in PLAN.md.
2. Before writing code, tell Quit in 3 to 6 lines which files you will create or change.
3. Build the phase. Write complete files.
4. Run the phase's checks (listed in the phase). Fix anything that fails before reporting.
5. Tick the phase's box in PLAN.md "Progress" and append the phase's entry to `docs/WHY.md`.
6. Send the phase report (format below), then STOP.
7. Do not start the next phase until Quit says "go" or "next".
8. If Quit reports a bug, fix only that bug, re-run the checks, and report again.

### Phase report format

```
## Phase N done: <phase name>
Created: <file paths>
Changed: <file paths>
Test it in the browser:
  1. ... (numbered steps using the seeded accounts)
Automated tests: <last line of `php artisan test`>
Why (for your professor):
  - <one plain sentence per key decision>
Flags: <problems noticed outside this phase, one line each, or "none">
```

### docs/WHY.md entry format

```
## Phase N — <phase name>
- <Decision>: <one or two plain sentences Quit can repeat to a professor>
```

## Code rules

Names and scope
- Use the exact table, column, model, controller, route, view and file names in PLAN.md. Do not rename anything.
- Change only what the current phase needs. If you notice a problem elsewhere, list it under "Flags" and leave it.
- Do not edit Breeze files except the ones PLAN.md names.
- No new Composer or npm packages unless PLAN.md lists them. Ask Quit first otherwise.

Completeness
- Write whole files. Never leave `// ...`, `// existing code`, `TODO` or placeholder text.
- Every view a route returns must exist and render.

Database
- Use `string` columns for role, type, status and audience values, not MySQL `enum`. Tests run on SQLite, and string columns are easier to change later.
- Validate allowed values with `Rule::in(...)` using the constants on the model.
- Foreign keys use `foreignId(...)->constrained(...)` with the cascade rule given in PLAN.md.
- `migrate:fresh` wipes all data. Only run it in phases where PLAN.md says so, and mention it in the report.

Models
- Classic style: `protected $fillable = [...]` and a `casts()` method. No PHP-attribute model configuration (easier to explain).
- Allowed values live as `public const` arrays on the model (for example `Opportunity::TYPES`).

Controllers and validation
- Validate with `$request->validate([...])` inside the controller method.
- Set owner columns (`user_id`, `teacher_id`, `owner_id`) from `$request->user()->id`, never from form input.
- Lists use `->latest()->paginate(10)->withQueryString()` and eager-load shown relations with `with()`.
- Redirect after every POST/PATCH/DELETE with `->with('success', '...')`.

Authorization (two layers)
- "What kind of user may open this page" → route groups with the `role:` middleware.
- "May this user touch this record" → a Policy in `app/Policies`, called with `Gate::authorize('ability', $model)`.
  The base Controller in Laravel 11+ has no `authorize()` helper, so use the `Gate` facade.
- Admin passes every policy check (use a `before()` method in each policy).

Blade
- Every page extends `layouts.main` (except Breeze auth/profile pages and the print view).
- Every form has `@csrf`, a `<label for>` for each input, `old()` values, and `@error` messages with `is-invalid`.
- Escape user content with `{{ }}`. For multi-line text use `{!! nl2br(e($text)) !!}`. Never print raw user input.
- Dates: `->format('M d, Y')`. Times: `->format('g:i A')`.
- Tables are wrapped in `<div class="table-responsive">`.
- Links to routes built in later phases are wrapped in `@if (Route::has('route.name'))` so every phase renders.

Files
- Uploads go to the `public` disk under `modules/`. Save the original file name in the database.
- Downloads go through the controller with `Storage::disk('public')->download($path, $originalName)`.

## Useful commands

```
php artisan serve
php artisan test
php artisan migrate
php artisan migrate:fresh --seed
php artisan route:list --except-vendor
php artisan optimize:clear
npm run build
```

## Seeded accounts (all use password: password)

| Email | Role |
|---|---|
| admin@educonnect.test | admin |
| teacher@educonnect.test | teacher |
| student@educonnect.test | student (Grade 10, child of parent@) |
| student2@educonnect.test | student (Grade 10, not linked to parent@) |
| parent@educonnect.test | parent |
| org@educonnect.test | organization |

## Git

Run `git init` in Phase 0. Commit only when Quit says "commit", with the message `Phase N: <phase name>`.
