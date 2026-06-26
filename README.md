# Family Quran Tracker

A modern, responsive **Laravel 12** web app where a **Family Leader (Admin)** manages family members and tracks everyone's daily Quran recitation — with dashboards, charts, streaks, badges, leaderboards, reports and a clean Apple-minimal green-Islamic UI (light & dark mode).

Built with **PHP 8.3+ · Laravel 12 · MySQL · Laravel Breeze (Blade) · Tailwind CSS · Alpine.js · Chart.js**.

---

## Features

- **Two roles** — Family Leader (Admin) & Family Member. No public registration; only the Admin creates accounts.
- **Authentication** — login, logout, forgot/reset password, change password, remember-me. Disabled members are blocked at the login form.
- **Admin dashboard** — stat cards (members, today/week/month/total pages & time, sessions, most/least active, family streak, badges) + Chart.js charts (daily pages, weekly activity, monthly time, top readers) + a calendar heatmap.
- **Member management** — create, edit, enable/disable, reset password, soft-delete; photo upload; per-member daily goal.
- **Reading module** — two methods:
  - *Manual entry* — date, surah, start/end page (pages auto-computed), juz, minutes, notes.
  - *Reading timer* — Start/Stop captures duration & timestamps automatically; save surah/pages on stop.
- **History** — daily/weekly/monthly filters, search, edit today's record.
- **Streaks** — current & longest consecutive-day streaks.
- **Gamification** — badges (First Reading, 7/30-day streaks, 100/500/1000 pages), congrats popup, weekly/monthly/all-time leaderboard.
- **Reports** — daily/weekly/monthly/yearly/per-member, exportable to **CSV, Excel (xlsx) and PDF**.
- **Settings** — family name & logo, reading goals, toggle leaderboard/badges.
- **Activity log** — logins, reading, member changes, password changes, exports, etc.
- **PWA** — installable, offline shell, dark mode, mobile bottom navigation.

## Architecture

- **Services** (`app/Services`) — `StatsService`, `StreakService`, `BadgeService`, `ReadingSessionService`, `ReportService`, `ExportService`, `ActivityLogger`.
- **Repositories** (`app/Repositories`) — `UserRepository`, `ReadingSessionRepository`.
- **Form Requests** for validation, **Policies** (`UserPolicy`, `ReadingSessionPolicy`) for authorization, custom **middleware** (`admin`, `active`).
- **Eloquent models** with relationships + soft deletes; factories & seeders for realistic demo data.

## Local Setup

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate
# set DB_DATABASE / DB_USERNAME / DB_PASSWORD in .env (MySQL)

# 3. Database + demo data
php artisan migrate:fresh --seed
php artisan storage:link

# 4. Build assets & run
npm run build      # or: npm run dev
php artisan serve
```

### Demo accounts (password: `password`)

| Role          | Email                 |
|---------------|-----------------------|
| Family Leader | `admin@example.com`   |
| Member        | `fatima@example.com`  |
| Member        | `maryam@example.com`  |
| Member        | `yusuf@example.com`   |
| Disabled      | `zayd@example.com`    |

## Deployment (Hostinger shared hosting)

This host has **no SSH/Composer/npm** — deploy via Git + File Manager. Therefore:

1. Run `npm run build` locally and **commit** the `public/build` assets.
2. The `vendor/` directory is **committed** (see `.gitignore`).
3. Point the domain's document root at `/public`.
4. Set the production `.env` (DB credentials, `APP_ENV=production`, `APP_DEBUG=false`).
5. Run migrations via a one-off route/script or Hostinger's DB tools.

The app version lives in `config/quran.php` (`version`) and is shown in the footer/settings — **bump it when shipping a feature** so deploys are verifiable.

## Tests

```bash
php artisan test
```

Feature tests cover auth gating, role isolation, member CRUD, reading logging, streak calc, badge awarding and report exports (CSV/XLSX/PDF).
