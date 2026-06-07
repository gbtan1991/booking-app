# SwissBook

A production-grade business booking system built with the **TALL stack** — Tailwind CSS, Alpine.js, Laravel, and Livewire. Customers land directly on a multi-step booking wizard; admins manage bookings, services, and users through a protected backend dashboard.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 |
| UI Components | Livewire v3 |
| Interactivity | Alpine.js v3 (bundled via Livewire) |
| Styling | Tailwind CSS v4 |
| Build Tool | Vite 8 |
| PHP | 8.3+ |
| Database | MySQL (SQLite supported for local dev) |

---

## Features

### Public
- **Multi-step booking wizard** — Service → Date → Time → Customer details → Confirmation
- Services loaded dynamically from the database (no hardcoded options)
- Date picker rendered as a proper 7-column calendar grid (Mon–Sun)
- Time slots displayed in a responsive 3-column grid, filtered against existing bookings
- Atomic slot reservation using `DB::transaction()` + `lockForUpdate()` to prevent double-booking
- Rate limiting (5 submissions / IP / 10 minutes) on booking submission
- Server-side validation of service name, date, and time on every submission

### Admin
- **Booking Dashboard** — tabbed view of Current / Completed / Cancelled bookings with inline status toggles (mark done, cancel, restore), search, date filter, and paginated results
- **Service Manager** — full CRUD for bookable services (name, description, CHF price) via a slide-over panel
- **User Manager** — full CRUD for admin users (name, position, system role, email, password) with self-deletion guard
- Stats cards showing Current, Today, Completed, and Cancelled counts
- All admin routes protected by `EnsureAdmin` middleware

### Auth
- Admin-only login with rate limiting (5 attempts / 5 minutes)
- No public registration — admin accounts are created through the admin panel
- Remember me support and session invalidation on logout

---

## Project Structure

```
app/
├── Http/Middleware/
│   └── EnsureAdmin.php          # Redirects non-admins to login
├── Livewire/
│   ├── Admin/
│   │   ├── Dashboard.php        # Booking management + stats
│   │   ├── ServiceManager.php   # Services CRUD
│   │   └── UserManager.php      # Admin user CRUD
│   ├── Auth/
│   │   └── Login.php            # Rate-limited login
│   └── BookingWizard.php        # Public 5-step booking flow

├── Models/
│   ├── Book.php                 # Bookings (table: book)
│   ├── Service.php              # Bookable services
│   └── User.php                 # Admin users

resources/views/
├── components/layouts/
│   ├── app.blade.php            # Public layout (minimal nav)
│   ├── admin.blade.php          # Admin layout (sidebar + topbar)
│   └── guest.blade.php          # Auth pages layout
├── livewire/
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── service-manager.blade.php
│   │   └── user-manager.blade.php
│   ├── auth/
│   │   └── login.blade.php
│   └── booking-wizard.blade.php
```

---

## Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | varchar | |
| `position` | varchar nullable | e.g. Office Manager |
| `system_role` | varchar | Default: `Administrator` |
| `email` | varchar unique | |
| `password` | varchar | Hashed via `Hash::make()` |

### `book`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `service` | varchar | Service name at time of booking |
| `date` | date | Indexed |
| `time` | varchar(10) | e.g. `09:00` |
| `customer_name` | varchar | |
| `customer_email` | varchar | |
| `customer_telephone` | varchar(30) | |
| `customer_notes` | text nullable | |
| `status` | varchar(20) | `current` / `completed` / `cancelled` |

Composite indexes on `(date, time, status)` and `(status, date)` for fast dashboard queries.

### `services`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | varchar | |
| `description` | text nullable | |
| `price` | decimal(8,2) | Displayed as `CHF X` |

---

## Routes

| Method | URI | Name | Access |
|---|---|---|---|
| GET | `/` | `home` | Public |
| GET | `/login` | `login` | Guest only |
| POST | `/logout` | `logout` | Auth |
| GET | `/admin` | `admin.dashboard` | Auth + Admin |
| GET | `/admin/services` | `admin.services` | Auth + Admin |
| GET | `/admin/users` | `admin.users` | Auth + Admin |

---

## Local Setup

### Requirements
- PHP 8.3+
- Composer
- Node.js 18+
- MySQL — or change `DB_CONNECTION=sqlite` in `.env` for zero-config local dev

### Steps

```bash
# 1. Clone and install dependencies
git clone https://github.com/gbtan1991/booking-app.git
cd booking-app
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Edit .env — set your database credentials
#    DB_CONNECTION=mysql
#    DB_DATABASE=swissbook
#    DB_USERNAME=root
#    DB_PASSWORD=

# 4. Run migrations and seed the default admin account + sample services
php artisan migrate --seed

# 5. Build frontend assets
npm run build

# 6. Start the development server
php artisan serve
```

Visit **http://localhost:8000** — the booking wizard loads immediately.

Admin panel: **http://localhost:8000/admin**

### Default Admin Credentials (seeded)

| Field | Value |
|---|---|
| Email | `admin@swissbook.test` |
| Password | `password` |

> Change these immediately before any public deployment.

---

## Development Workflow

```bash
# Terminal 1 — backend
php artisan serve

# Terminal 2 — frontend watch
npm run dev
```

---

## Key Design Decisions

**Alpine.js is not imported in `app.js`**
Livewire v3 bundles its own Alpine instance. Importing Alpine separately causes two concurrent instances, which silently breaks `wire:click` event dispatching on form submission — bookings never reach the server.

**Booking wizard uses server-side `@if` for step switching**
Livewire v3's DOM morphing resets Alpine `x-show` inline styles on every server round-trip, causing all steps to render visible simultaneously. Server-side `@if ($step === N)` avoids this entirely.

**`public const` for the time-slot array**
Livewire serialises component state across HTTP requests. `protected` arrays are not accessible inside `DB::transaction()` closures because PHP closures bind `$this` but cannot reach protected members across the serialisation boundary. `public const` sidesteps this cleanly.

**`DB::transaction()` with `lockForUpdate()`**
Prevents the race condition where two customers simultaneously select and submit the same slot. The conflict check and the `Book::create()` happen atomically — one wins, the other receives a validation error and is returned to the time selection step.

---

## Deployment Checklist

```bash
# .env
APP_ENV=production
APP_DEBUG=false

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build minified assets
npm run build
```

Set `SESSION_DRIVER=database` (already the default) — do not use `file` on multi-server deployments.

---

## License

MIT
