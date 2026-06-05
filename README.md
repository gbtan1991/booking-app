# SwissBook — TALL Stack Booking MVP

A production-grade appointment booking system built with **Laravel 13**, **Livewire v3**, **Alpine.js**, and **Tailwind CSS v4**.

## Requirements

| Tool | Version |
|---|---|
| PHP | **8.3+** (tested on 8.3.31) |
| Composer | 2.x |
| Node.js | 18+ |
| MySQL | 8.0+ (via XAMPP, Laragon, or Herd) |
| Apache or php artisan serve | any |

---

## Local Setup (Windows — XAMPP / Laragon / Herd)

### 1. Clone & install dependencies

```bash
git clone <repo-url> swissbook
cd swissbook
composer install
npm install
```

### 2. Configure environment

```bash
copy .env.example .env
php artisan key:generate
```

Open `.env` and set your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=swissbook
DB_USERNAME=root
DB_PASSWORD=          # leave blank for XAMPP default
```

Create the database first in phpMyAdmin or MySQL CLI:

```sql
CREATE DATABASE swissbook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Build frontend assets

```bash
npm run build
```

### 5. Start the dev server

```bash
php artisan serve
```

Visit **http://localhost:8000** for the public booking page.
Visit **http://localhost:8000/admin** for the admin dashboard (HTTP Basic Auth — uses the `users` table).

---

## Development (hot reload)

Run these two commands in separate terminals:

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

---

## Key Files

| Path | Description |
|---|---|
| `app/Models/Booking.php` | Eloquent model with soft deletes, scopes |
| `database/migrations/…create_bookings_table.php` | Schema with composite MySQL indexes |
| `app/Livewire/BookingWizard.php` | 3-step public booking wizard |
| `app/Livewire/Admin/BookingDashboard.php` | Admin panel with live filters |
| `resources/views/pages/home.blade.php` | Public landing page |
| `resources/views/layouts/app.blade.php` | Public layout |
| `resources/views/layouts/admin.blade.php` | Admin layout |

---

## Security Features

- CSRF protection on all Livewire requests
- `DB::transaction()` + `lockForUpdate()` prevents double-booking race conditions
- `RateLimiter` — 5 booking attempts per IP per 10 minutes
- Strict `$fillable` on `Booking` model (Mass Assignment protected)
- `strip_tags()` on all free-text inputs
- `auth.basic` middleware on `/admin` routes
</content>
</invoke>