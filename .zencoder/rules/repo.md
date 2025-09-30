# MCC-IPES Repository Quick Facts

## Project Overview
- MCC-IPES is a Laravel-based web application for managing questionnaires, staff evaluations, and academic year scheduling.
- The stack includes Laravel 10 (PHP), Blade templates, Bootstrap 5, Font Awesome, and assorted JavaScript libraries such as SweetAlert and DataTables.
- Core modules focus on Questionnaire management, Academic Year configuration, User roles/permissions, and schedule automation.

## Key Directories
- `app/`: Laravel application logic (controllers, models, policies, etc.).
- `resources/views/`: Blade templates for UI.
- `public/`: Public assets including compiled CSS/JS.
- `routes/web.php`: Main route definitions.
- `database/`: Migrations, seeders, factories.

## Frontend Conventions
- Rely on Bootstrap 5 utility classes for layout and responsive design.
- Font Awesome icons used for status indicators and actions.
- SweetAlert handles toast and modal notifications.
- Preference for `table-responsive` wrappers to maintain mobile compatibility.

## Backend Conventions
- Controllers reside in `app/Http/Controllers` and follow REST naming conventions.
- Form requests are in `app/Http/Requests` for validation.
- Eloquent models stored in `app/Models` with snake_case table names.
- Policies and gates leverage Laravel's authorization system.

## Styling & Assets
- Vite (`vite.config.js`) builds asset pipeline; run `npm run dev` for live reload.
- Laravel Mix references are legacy and not used.
- Custom SCSS/CSS resides under `resources/css` and is imported via Vite entrypoints.

## Testing & Tooling
- PHPUnit configuration in `phpunit.xml`.
- Pest or Dusk not currently configured.
- Coding standards follow PSR-12; use `vendor/bin/pint` for linting.

## Environment & Setup
- Copy `.env.example` to `.env`; configure DB, mail, and queue settings.
- Artisan commands handled via `php artisan ...`.
- Docker not supplied; run locally with PHP 8.2+, Composer, Node 18+.

## Deployment Notes
- Ensure `php artisan optimize` before deployment.
- Run database migrations and seeders relevant to academic year and questionnaire data.

## Miscellaneous
- SweetAlert scripts are embedded directly in Blade templates.
- DataTables integration may require `resources/js/app.js` tweaks for column behavior.
- Keep question-related Blade partials organized for reusable modals/forms.