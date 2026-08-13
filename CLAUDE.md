# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Meditech2** is a comprehensive multi-tenant healthcare management system built with Laravel 12 and Livewire 3. It follows FHIR standards for healthcare interoperability and supports medical practices, hospitals, and clinics with features including patient management, appointment scheduling, electronic medical records, and clinical documentation.

## Development Commands

## Codebase Memory (codebase-memory-mcp)
When this MCP server is available, **prefer graph tools over grep/Explore for structural code questions**.
Graph queries return precise results in a single tool call (~500 tokens) vs file-by-file exploration (~80K tokens).

### Essential Development Commands
```bash
# Start development environment (includes server, queue, logs, and vite)
composer dev

# Individual services
php artisan serve                    # Laravel development server
php artisan queue:listen --tries=1   # Queue worker
php artisan pail --timeout=0        # Real-time log viewer
npm run dev                          # Vite asset compilation
npm run build                        # Production asset build

# Database operations
php artisan migrate                  # Run database migrations
php artisan db:seed                  # Seed database with sample data
php artisan migrate:fresh --seed    # Fresh migration with seeding

# Testing
php artisan test                     # Run PHPUnit tests
./vendor/bin/phpunit                 # Direct PHPUnit execution

# Code quality
./vendor/bin/pint                    # Laravel Pint code formatting

# Data Management
php artisan patients:register-relationships  # Register patient relationships from identifiers
php artisan practitioners:create-users       # Create inactive users for migrated practitioners
```

### Livewire Development
```bash
php artisan make:livewire ComponentName    # Create new Livewire component
php artisan livewire:publish --config     # Publish Livewire config
php artisan livewire:publish --assets     # Publish Livewire assets
```

## Architecture Overview

### Multi-Tenant Healthcare System
- **Client → Branch → ConsultingRoom** hierarchy for organizational structure
- **Global Scopes** enforce tenant data isolation (PatientScope, PractitionerScope, AppointmentScope)
- **Role-based access control**: admin, doctor, paciente, asistente
- **User-Client relationships** for multi-organization access

### Core Domain Models
- **Patient**: Central entity with comprehensive medical records
- **Practitioner**: Medical professionals with qualifications and specialties
- **Appointment**: Scheduling system with status workflow management
- **Encounter**: Clinical consultations linked to appointments
- **MedicalHistory**: Patient medical background and conditions
- **MedicationRequest**: Prescription management with FHIR compliance

### Key Livewire Components
- **Patient/**: Patient management, medical history, conditions, medications
- **Appointment/**: Calendar scheduling, status management, modal forms
- **Consultation/**: Clinical documentation, vital signs, diagnostics, prescriptions
- **Dashboard/**: Role-based dashboards with real-time statistics

### FHIR Compliance
- Models include FHIR resource mapping for healthcare interoperability
- Standardized medical coding support (ICD-10, CPT codes)
- FHIR export capabilities for data exchange

## Development Patterns

### Model Relationships
- All models extend `BaseModel` for comprehensive audit logging
- Polymorphic relationships for flexible file attachments
- Global scopes automatically filter data by tenant (Client)
- FHIR resource identifiers follow healthcare standards

### Livewire Component Structure
- **Data Tables**: Use `DataTable.php` pattern for listing with search/filter
- **Modals**: Follow `ModalSave.php` pattern for create/edit operations
- **Real-time Updates**: Calendar and dashboard components auto-refresh
- **Form Validation**: Comprehensive client-side and server-side validation

### Security & Auditing
- **BaseModel**: Tracks all CRUD operations with user attribution
- **StatusHistoryLog**: Specialized logging for workflow status changes
- **UserLog**: Comprehensive user activity tracking
- **Role-based permissions** using Spatie Laravel Permission package

### File Management
- **FileService**: Centralized file handling for avatars and documents
- **Polymorphic file relationships**: Files can attach to any model
- **Storage**: Uses Laravel filesystem with configurable drivers

## Testing

### Test Structure
- **Feature Tests**: End-to-end functionality testing in `tests/Feature/`
- **Unit Tests**: Individual component testing in `tests/Unit/`
- **Database**: SQLite in-memory database for testing
- **Factories**: Comprehensive model factories for test data generation

### Running Tests
```bash
php artisan test --parallel          # Run tests in parallel
php artisan test --coverage         # Generate coverage reports
php artisan test --filter=TestName  # Run specific test
```

## Database

### Multi-Tenancy
- **Global Scopes**: Automatic tenant filtering on all queries
- **Client-based isolation**: Data automatically filtered by user's client access
- **Foreign key constraints**: Maintain referential integrity across tenants

### Seeding
- **Comprehensive seeders**: Medical specialities, CPT codes, ICD-10 codes
- **Development data**: Sample patients, practitioners, appointments
- **Configuration data**: Roles, permissions, default settings

## Frontend Assets

### Styling
- **TailwindCSS 4.0**: Utility-first CSS framework
- **Custom CSS**: Medical-specific styling in `resources/css/`
- **Bootstrap**: Legacy components for compatibility

### JavaScript
- **Livewire 3**: Primary interactive framework
- **jQuery**: Legacy component support
- **International Telephone Input**: Phone number formatting
- **FullCalendar**: Advanced appointment calendar

## Deployment Notes

### Environment Configuration
- **Multi-tenant setup**: Configure client-specific settings
- **Email notifications**: Configure SMTP for appointment notifications
- **File storage**: Configure filesystem driver for production
- **Queue configuration**: Set up reliable queue driver for notifications

### Performance Considerations
- **Database indexing**: Tenant-aware indexes for optimal query performance
- **Livewire optimization**: Use polling and real-time updates efficiently
- **Asset compilation**: Use `npm run build` for production assets
- **Queue workers**: Ensure queue workers run for email notifications

## Important File Locations

```
app/Models/Scopes/          # Multi-tenant global scopes
app/Livewire/Patient/       # Patient management components
app/Livewire/Appointment/   # Appointment scheduling components
app/Livewire/Consultation/  # Clinical documentation components
database/seeders/           # Database seeding files
resources/views/livewire/   # Blade templates for Livewire components
public/assets/              # Static assets and medical imagery
```

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.3. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel-octane/core rules ===

# Laravel Octane

This application uses Laravel Octane, a long-running PHP server. The application bootstraps once and handles many requests within the same process.

- Never store request-specific state in singletons or static properties, because it can leak across requests.
- Use `config('octane.server')` to detect the active driver (`swoole`, `roadrunner`, or `frankenphp`).
- Prefer scoped bindings (`$this->app->scoped()`) over singletons for per-request services.

When working on Octane-specific features (concurrency, shared tables, memory, driver configuration, testing), invoke `octane-development` for detailed rules.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
