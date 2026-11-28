# AGENTS.md

Guidance for AI agents working on this repo. General project rules live in `CLAUDE.md` — read it first.

## Commands
- Tests: `php artisan test`
- Lint (PHP): `./vendor/bin/pint --test <changed files>`
- Frontend build: `npm run build` (dev: `npm run dev`)
- Local run: `composer dev` (or `php artisan serve` + `npm run dev`)

## Architecture: Admin menu, roles & permissions

The admin sidebar, permissions, and route enforcement all derive from ONE registry:
`config/admin-menu.php`. Roles/permissions are managed at runtime in the admin UI
(`/admin/roles`, admin-only) using Spatie laravel-permission. `admin` role is the
super admin (bypasses everything via `Gate::before` in `AppServiceProvider`).

### RULE: every feature must be permission-guarded (mandatory)

Every admin feature — new or existing — MUST be authenticatable through the
permission system. For each feature, EVERY action (view, create, update/edit,
delete, or any custom action like ban/upload/reorder/reset-password) must:

1. Have a permission name in the registry (`config/admin-menu.php`) — the item's
   `permission` for viewing + one entry per action in `actions` (e.g.
   `reports.view`, `reports.create`, `reports.edit`, `reports.delete`,
   `reports.export` => 'Export CSV'). Never invent permissions outside the
   registry — the Roles access editor and the seeder only know registry names.
2. Have its route(s) wrapped with `role_or_permission:admin|{permission}`
   in `routes/admin.php` — never rely on the panel-entry middleware alone.
3. Have its UI buttons gated with `can('{permission}')` via the `useCan()`
   composable.
4. Have a feature test proving: authorized role → 2xx, unauthorized role → 403.

An admin route or admin action WITHOUT a registry permission + route middleware
is a bug — treat it as incomplete work. This includes "hidden" actions (custom
PATCH/POST endpoints), not just CRUD pages.

### Checklist: adding a new admin sidebar menu (full functionality)

Do ALL of these, in order (see the RULE above — it applies to every action this
menu exposes):

1. **Controller** — create `app/Http/Controllers/Admin/{Name}Controller.php`
   (no query logic in controllers; use services/repositories).
2. **Routes** — in `routes/admin.php`, wrap each CRUD group with permission
   middleware using the registry naming convention:
   ```php
   Route::middleware('role_or_permission:admin|{key}.view')->group(function () {
       Route::resource('{key}', ...)->only(['index', 'show']);
   });
   // same pattern for create/store -> {key}.create, edit/update -> {key}.edit,
   // destroy -> {key}.delete
   ```
   Route names MUST be `admin.{key}.*` — the sidebar highlights/links use them.
3. **Vue page(s)** — `resources/js/Pages/Admin/{Name}/Index.vue` (+ Create/Edit).
   Gate action buttons with the `useCan()` composable:
   `const { can } = useCan();` → `v-if="can('{key}.create')"` on Add/Delete buttons.
4. **Registry entry** — add to `config/admin-menu.php`:
   ```php
   ['key' => '{key}', 'label' => 'Label', 'group' => 'Catalog|Sales|People|System',
    'route' => 'admin.{key}.index', 'permission' => '{key}.view',
    'actions' => ['{key}.create' => 'Create', '{key}.edit' => 'Edit', '{key}.delete' => 'Delete']],
   ```
   This is what makes the item appear dynamically in the sidebar AND in the
   Roles access editor. Never hardcode menu links in `AdminLayout.vue`.
5. **Icon** (optional) — add an SVG path to the `navIcons` map in
   `resources/js/Layouts/AdminLayout.vue` keyed by `{key}` (fallback icon exists).
6. **Create the permissions** — either save any role in the admin UI
   (auto-creates missing permissions) or run:
   `php artisan db:seed --class=RolePermissionSeeder`
   The seeder also re-syncs ALL permissions to the `admin` role.
7. **Tests** — feature test: admin can access the new page (200), a role without
   `{key}.view` gets 403. Use `RefreshDatabase` + seed
   `RolePermissionSeeder` in `setUp()`.
8. **Verify** — `php artisan test`, `./vendor/bin/pint --test <changed files>`,
   `npm run build` before done.

### Adding a new permission to an existing section
Add it to that section's `actions` in `config/admin-menu.php`, wire the route with
`role_or_permission:admin|{permission}`, gate the UI button with `can('{permission}')`,
then re-run the seeder or save any role. No other changes needed.

### Managing roles/users at runtime
- Roles & permissions: `/admin/roles` (create roles like "Accountant", tick menus
  + actions; system roles `admin`/`staff` can't be renamed/deleted).
- Storefront roles (`customer`, `vendor` — see `storefront_roles` in
  `config/admin-menu.php`) are auto-assigned on registration, hidden from the
  access editor, and can't be edited/deleted there — they must never receive
  admin-panel permissions.
- Assign roles & update passwords: `/admin/users/{id}` (password reset needs the
  `users.credentials` permission).

## Production deploy notes
- VPS runs **aaPanel** (no Docker): sites under `/www/wwwroot`, PHP managed via
  `/etc/init.d/php-fpm-{version}` (or the aaPanel UI), MySQL runs locally.
- Deploys are FTP-only (`.github/workflows/deploy.yml`) — they do NOT run
  composer/npm/artisan. After every deployment, SSH in (or aaPanel terminal,
  in the site root) and run:
  ```
  php artisan app:post-deploy
  ```
  (runs migrate + RolePermissionSeeder + optimize:clear + queue:restart +
  best-effort PHP-FPM reload; add `--optimize` to re-cache config/routes/views).
  If not root, reload PHP via the aaPanel UI (App Store → PHP → Reload).
- `public/build` is committed: run `npm run build` and commit before pushing.

## Known pre-existing issues (do not "fix" drive-by)
- 3 failing tests: `ExampleTest` (no RefreshDatabase), `RegistrationTest`,
  `ProfileTest` account deletion.
