# Project Rules

## Architecture & Code Structure
- ALWAYS use the Repository pattern. Controllers MUST NOT contain query logic.
  Inject a repository (or service) instead of using Eloquent directly in controllers.
- Business logic goes in Service classes, not in controllers or models.
- Use Form Request classes for validation, never validate inside controllers.
- Use API Resources for JSON responses, do not return raw models.
- Follow PSR-12. Keep methods small and single-purpose.

## Database
- NEVER change a database table directly. ANY schema change (new table,
  new column, index, type change) MUST be done in a new migration file.
- NEVER edit an already-migrated migration file. Create a NEW migration instead.
- Update the relevant model ($fillable, casts, relationships) when a migration
  changes a table.
- Add/adjust factory and seeder when adding a new table or important columns.
- Add DB indexes for columns used in where/join/orderBy (in the migration).
- Use DB::transaction for multi-step writes.

## Security (non-negotiable)
- NEVER mass-assign without protection. Use $fillable, never $guarded = [].
- ALWAYS authorize actions with Policies/Gates, not inline if-checks.
- NEVER trust request input for IDs/ownership — scope queries to the
  authenticated user (e.g. auth()->user()->orders(), not Order::find($id)).
- Use Eloquent/query bindings only. NEVER concatenate user input into raw SQL.
- Escape output in Blade with {{ }}; only use {!! !!} for trusted HTML.

## Performance / N+1
- ALWAYS eager-load relationships used in loops (with(), load()).
- NEVER run queries inside a foreach. Use whereIn / eager load instead.
- Use chunk() or cursor() for large datasets, never ->all() on big tables.
- Cache expensive reads; invalidate the cache on the related write.

## Eloquent / Data
- Use PHP enums or casts instead of magic strings/ints.
- Money as integers (cents) or decimal casts, never floats.

## Error Handling
- NEVER swallow exceptions with empty catch blocks.
- Throw domain-specific exceptions; let the handler format the response.
- Return consistent JSON error shapes for the API.
- Log with context: logger->error($msg, ['order_id' => $id]), not bare strings.

## Queues & External Work
- Slow or external work (email, SMS, payment, GPS/device, tower monitoring)
  MUST be a queued Job with defined tries/backoff. Never block the request.
- Webhook/callback handlers MUST be idempotent (dedupe on a unique key).

## Inertia + Vue 3 (Frontend)
- ALWAYS use <script setup> with the Composition API. No Options API.
- Components MUST be PascalCase files (UserCard.vue). One component per file.
- Pages live in resources/js/Pages, reusable UI in resources/js/Components.
- NEVER fetch with axios/fetch for page data — pass it as props from the
  controller via Inertia::render(). Use router.reload({ only: [...] }) for
  partial refreshes. Direct API calls are only for non-page data
  (autocomplete, polling live GPS/NOC status).
- Use Inertia <Link> for navigation, never raw <a> for internal routes.
- Forms MUST use Inertia's useForm() — it handles errors, processing, and
  dirty state. NEVER hand-roll form submission.
- Validation errors come from the server via useForm().errors. Do NOT
  duplicate validation rules on the frontend as the source of truth.
- Share global data (auth user, flash messages) via HandleInertiaRequests
  middleware, not by passing it into every page.
- Define props with defineProps() using explicit types/defaults.
  Emit events with defineEmits(). NEVER mutate props directly.
- Keep components small. Extract reused logic into composables (useXxx)
  in resources/js/Composables.
- Use computed for derived state, not methods. ref for primitives,
  reactive for objects. Clean up listeners/intervals in onUnmounted.
- Keep UI text out of components — pass translation strings via shared
  props / i18n so components never hardcode Bangla or English.

## Testing
- Write a feature test for every new endpoint/action (happy path + 1 failure).
- Use factories + RefreshDatabase. Mock external services (HTTP, payment, SMS).

## What NOT to do (Backend)
- DO NOT use raw DB::statement or DB::table for schema changes — use migrations.
- DO NOT write queries inside Blade views or controllers.
- DO NOT call env() outside config files — use config() everywhere else.
- DO NOT introduce new dependencies without flagging it first.
- DO NOT refactor or reformat files unrelated to the current task.
- DO NOT touch .env, config/*, or generated files unless explicitly asked.
- DO NOT commit .env, /vendor, /node_modules, or storage logs.

## What NOT to do (Frontend)
- DO NOT call the API directly for data already available as page props.
- DO NOT put business logic in components — keep it server-side.
- DO NOT use v-html with user input (XSS).
- DO NOT mutate a prop; emit an event or use a local copy.
- DO NOT skip :key on v-for, and never use index as key for dynamic lists.

## Conventions
- Naming: Models singular (User), tables plural snake_case (users),
  controllers PascalCase + "Controller", routes kebab-case,
  Vue components PascalCase.
- Keep .env.example updated when adding new env keys.

## Commands
- Migrate: php artisan migrate
- Tests: php artisan test
- Frontend build: npm run build (dev: npm run dev)
- After any code change, run: php artisan test (and the linter) before done.
