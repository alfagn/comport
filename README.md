# Empetix Solution — Laravel 13 + CMS Dashboard (`laravel-13-dashboard` branch)

The Empetix company website rebuilt as a Laravel 13 application: every public
page is a Blade template rendered from the database, and a dedicated admin
dashboard controls the site's content.

> The `main` branch still holds the original static site and is untouched.

## Stack

- **Laravel 13** (PHP 8.2+), Blade templates, SQLite by default (any Laravel-supported DB works)
- Architecture: Controllers → FormRequests → DTOs → Services → Repository contracts + Eloquent implementations → Models. Comments explain every layer.
- Frontend assets are the original site's static files under `public/assets` (no build step required); the dashboard has its own design system at `public/assets/admin/admin.css`.

## What the dashboard controls

- Hero slides (incl. slider tabs), Services, Industries, Projects (full case-study builder: facts, outcome stats, narrative sections, quote, video), Team & founders (incl. bio and expertise), Job posts (numbered spec sections, tech tags), Journal posts (article body, author card, table of contents), Partners
- **Pages & Content**: editable headings/paragraphs/buttons per page plus the footer banner and brand summary (`sec()` blocks)
- **Settings**: emails, phone, address, social links, site name/tagline
- **Inbox**: contact form messages and newsletter subscribers
- Entity ordering (`sort_order`), publish/active toggles everywhere, image uploads

## Setup

```bash
composer install
cp .env.example .env          # then set APP_KEY via `php artisan key:generate`
touch database/database.sqlite
php artisan migrate --seed    # seeds the real site content + an admin login
php artisan serve             # public site: http://127.0.0.1:8000  |  dashboard: /admin
```

The seeder prints the generated admin password once (or set your own first):

```env
ADMIN_EMAIL=admin@empetix.com
ADMIN_INITIAL_PASSWORD=choose-a-strong-password
```

For uploaded images to be served: `php artisan storage:link`.

## Tests

```bash
php artisan test
```

Feature tests cover the public routes (listings, detail pages, 404s, contact +
newsletter persistence, settings/section rendering) and the dashboard (guest
redirects, login, every index screen, project CRUD cycle, service update,
settings and page-section saves).

## Repo layout worth knowing

- `app/Core` — base repository/service/DTO abstractions
- `app/Repositories/{Contracts,Eloquent}` — persistence contracts + implementations
- `app/Services` — business logic (uploads, JSON line normalization, tag sync, cache busting)
- `app/DTOs`, `app/Http/Requests` — typed input carriers + validation
- `database/seeders/data/content.json` — the seeded site content (reviewable without reading PHP)
- `app/Support/helpers.php` — `sec()` (editable page block) and `setting()` helpers
