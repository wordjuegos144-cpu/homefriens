# Copilot instructions — Homefriends (Homestay-main)

Concise, focused notes to help an AI contributor be productive immediately.

What this repo is
- Laravel backend (Filament admin + stancl/tenancy multitenancy) in the project root (`Homestay-main`).
- A React + Vite frontend lives in `frontend/` and is a separate dev surface for the public UI.

Where to look first (high-value files)
- Business logic: `app/Services/ReservaService.php` — all monetary & date calculations (e.g. calcularCantidadNoches, calcularComisionCanal).
- Reservation model behavior: `app/Models/Reserva.php` — important `booted()` hooks compute monetary fields and create related `Limpieza` and `Pago` entries.
- Admin resources: `app/Filament/Resources/*` — Filament forms/tables implementations.
- Public routes: `routes/web.php` (note: `/` redirects to `/admin`; calendar endpoints in `App\Http\Controllers\Frontend`).
- Frontend entry: `frontend/src/main.jsx` and `frontend/package.json` for dev/build scripts.

Key project patterns and constraints (do not change lightly)
- Monetary computations live in `ReservaService` and are also invoked from `Reserva::creating` in `booted()`; keep computations centralized to avoid inconsistencies.
- `Reserva` uses Spanish-ish DB column names (e.g. `idDepartamento`, `montoLimpieza`) and fills them via `$fillable`. Respect column naming when adding queries or migrations.
- The model explicitly avoids setting a non-existent DB column `totalAPagar` (see inline comment in `Reserva.php`) — do not add attributes that lack migrations without updating schema first.
- Multitenancy: several models use `Stancl\Tenancy\Database\Concerns\BelongsToTenant`. When touching tenant-scoped models, ensure tenant context is present in tests/dev flows.

Developer workflows (discoverable commands)
- Backend dependencies: `composer install` (PHP). Migrations: `php artisan migrate` (the README mentions defaulting to SQLite).
- Backend dev server: typical Laravel command is `php artisan serve` (verify env/mode). Tests: `php artisan test`.
- Frontend: from `frontend/` run `npm install` then `npm run dev` (Vite). `npm run build` creates production assets.
- The backend also has a `package.json` for Vite/asset dev (`Homestay-main/package.json`) — run `npm run dev` there when building assets integrated with Laravel's vite plugin.

Concrete examples to reference when changing behavior
- If you need to change how reservation totals are computed, update `app/Services/ReservaService.php` and then ensure `app/Models/Reserva.php` still calls the same helpers in its `creating` hook.
- When adding or changing a Filament form field, check the corresponding Resource in `app/Filament/Resources/` and follow the existing `Forms\Components` usage.

Quick checks before PRs
- Run `php artisan migrate:fresh --seed` (or `migrate`) and `php artisan test` locally (use SQLite if easier). Ensure tenant context if tests target tenant-scoped models.
- Search for Spanish column names (e.g. `montoPropietario`, `idCanalReserva`) to find related code paths — they are widely used and sensitive to renames.

Notes & assumptions
- The repo uses Spanish identifiers and comments; prefer concise bilingual commit messages and PR descriptions.
- This file was merged from the existing project README/copilot notes; if anything here is stale, point to the exact file path and line and we will update.

If something is unclear or you need more detail (example requests: list of tenant setup steps, where seeds are kept, or a failing test trace), tell me which area to expand and I will update this file.