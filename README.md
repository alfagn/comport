# Empetix Solution — Laravel 13 + CMS Dashboard (`laravel-13-dashboard` branch)

The Empetix company website rebuilt as a Laravel 13 application: every public
page is a Blade template rendered from the database, and a dedicated admin
dashboard controls the site's content.

> The `main` branch still holds the original static site and is untouched.

## Stack

- **Laravel 13** (PHP 8.3–8.5, per the official Laravel 13 release notes), Blade templates, SQLite by default (any Laravel-supported DB works)
- Architecture: Controllers → FormRequests → DTOs → Services → Repository contracts + Eloquent implementations → Models. Comments explain every layer.
- Dashboard listings use **Yajra DataTables v13** (server-side): `app/DataTables` classes on a shared `app/Core/DataTables/AdminDataTable.php` base; search, sort and pagination run in SQL through each entity's repository. jQuery + DataTables.net (both MIT) are vendored in `public/assets/admin/vendor`.
- Dashboard design: bespoke Empetix design system (`public/assets/admin/admin.css`), inspired by leading dark dashboard products (eyebrow page headings, uppercase stat labels, icon-chip cards, quiet bordered surfaces). No third-party template code or assets are used.
- Frontend assets are the original site's static files under `public/assets` (no build step required); the dashboard has its own design system at `public/assets/admin/admin.css`.

## What the dashboard controls

- Hero slides (incl. slider tabs), Services, Industries, Projects (full case-study builder: facts, outcome stats, narrative sections, quote, video), Team & founders (incl. bio and expertise), Job posts (numbered spec sections, tech tags), Journal posts (article body, author card, table of contents), Partners
- **Pages & Content**: editable headings/paragraphs/buttons per page plus the footer banner and brand summary (`sec()` blocks)
- **Settings**: emails, phone, address, social links, site name/tagline
- **Inbox**: contact form messages and newsletter subscribers
- Entity ordering (`sort_order`), publish/active toggles everywhere, image uploads

## Setup

Fresh install (empty database):

```bash
composer install
cp .env.example .env          # then set APP_KEY via `php artisan key:generate`
touch database/database.sqlite
php artisan migrate --seed    # seeds the real site content (fresh installs only — see below)
php artisan empetix:admin     # create your dashboard login (hidden password prompt)
php artisan serve             # public site: http://127.0.0.1:8000  |  dashboard: /admin
```

Upgrading an EXISTING install (database already has your content):

Back up first (non-destructive habit, one minute): copy `database/database.sqlite`
and your `storage/app/public` uploads somewhere safe. If you edited project
files locally, commit or `git stash` them before pulling.

```bash
git pull origin laravel-13-dashboard
composer install                           # picks up new packages (Livewire, permissions)
php artisan migrate                          # adds new tables/columns, never touches your rows
php artisan db:seed --class=PageSectionSeeder  # registers newly editable CMS blocks (additive only)
php artisan empetix:owner you@example.com    # grant owner to YOUR account (use your real login email)
```

`empetix:owner` creates the roles/permissions tables content (additively)
and grants the `owner` role to exactly the one account you name. Nothing
grants roles in bulk or implicitly: until an account is granted a role it
can reach the dashboard home only, and every section is enforced
server-side. New admins created from the Admins screen get exactly the
roles you pick. `empetix:admin` also asks, per account, whether the new
login should be an owner.

Do NOT run a bare `php artisan db:seed` (or `migrate --seed`) on an install where
you have edited content in the dashboard: ContentSeeder, NavItemSeeder and
ContentListSeeder use updateOrCreate and would replace your customized content
with the seed defaults. PageSectionSeeder is the only seeder that is purely
additive, so it is always safe. Never use `migrate:fresh` outside a throwaway
database — it wipes every table.

No default or generated password ever exists: `empetix:admin` asks for one
interactively (typed hidden, min 12 chars), and `--reset` changes it later.

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
- `app/DataTables` — one server-side Yajra table per dashboard listing
- `app/Repositories/{Contracts,Eloquent}` — persistence contracts + implementations
- `app/Services` — business logic (uploads, JSON line normalization, tag sync, cache busting)
- `app/DTOs`, `app/Http/Requests` — typed input carriers + validation
- `database/seeders/data/content.json` — the seeded site content (reviewable without reading PHP)
- `app/Support/helpers.php` — `sec()` (editable page block) and `setting()` helpers

## What the dashboard controls

Everything below is editable from `/admin` — no code changes needed:

- **Navigation** — utility-bar and mobile-drawer links (label, URL, order, visibility) plus the full mega menu: columns, links with descriptions, and feature cards for both panels.
- **Hero slides** — the homepage slider (eyebrow, title, subtitle, CTA, image, tabs).
- **Services, Industries, Projects, Team & Founders, Careers, Blog & Media, Partners** — full CRUD with image uploads, slugs, ordering and publish toggles. Projects carry the whole case-study page (facts, stats, results, quotes, gallery); jobs carry sections, requirements and perks; members carry bios and social links.
- **Pages & Content** — 34 editable text blocks: page heroes, section headings and mega-menu intro copy across home, about, team, careers, work, partners, blog, contact and the footer CTA.
- **Content Lists** — the repeating card lists as structured records (no delimiter text areas): about pillars, principles and timeline milestones; team culture cards; careers practice tracks, benefits and hiring steps. Each card has title, body, labels, link, order and visibility.
- **Settings** — public email/phone/address, social links, footer copy, SEO defaults.
- **Inbox** — contact-form briefs and newsletter subscribers, with read/unread state.

Layout-level markup (grids, section wrappers, decorative shells) stays in
Blade — that is presentation structure, not content. Every piece of copy,
link, card, image path and ordering listed above is editable from the
dashboard.

