# BookEasy — Backend

REST API untuk sistem reservasi jadwal BookEasy. Dibangun dengan Laravel 13.x dan PHP 8.3.

## Tech Stack

- **Laravel 13.x** — REST API
- **PHP 8.3** — Backend language
- **PostgreSQL** — Database (Supabase)
- **Sanctum** — Token-based auth
- **Pest PHP 4.x** — Testing

## Prasyarat

- PHP 8.3+
- Composer
- PostgreSQL (atau Supabase)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan serve
```

API tersedia di `http://localhost:8000`.

## Testing

```bash
php artisan test
```

Atau pakai composer script:

```bash
composer test
```

### Struktur Test

```
tests/
├── Feature/
│   ├── BookingApiTest.php       # Endpoint booking
│   ├── AuthApiTest.php          # Login/logout
│   └── DashboardApiTest.php     # Dashboard admin
└── Unit/
    └── BookingServiceTest.php   # Logic service
```

## Endpoint API

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/health` | No | Health check |
| GET | `/api/bookings?date=YYYY-MM-DD` | No | Ambil daftar slot + status |
| POST | `/api/bookings` | No | Booking baru |
| POST | `/api/auth/login` | No | Admin login |
| POST | `/api/auth/logout` | Yes | Admin logout |
| GET | `/api/admin/dashboard` | Yes | Dashboard admin |
| GET | `/api/admin/schedules` | Yes | Jadwal operasional |
| PUT | `/api/admin/schedules` | Yes | Update jadwal |
| GET | `/api/admin/holidays` | Yes | Daftar hari libur |
| POST | `/api/admin/holidays` | Yes | Tambah hari libur |
| DELETE | `/api/admin/holidays/{date}` | Yes | Hapus hari libur |
| DELETE | `/api/admin/bookings/{id}` | Yes | Batalkan booking |

## Struktur Folder

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── BookingController.php
│   │   │   ├── DashboardController.php
│   │   │   └── ScheduleController.php
│   │   └── Middleware/
│   │       └── AdminAuth.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Booking.php
│   │   ├── Schedule.php
│   │   └── Holiday.php
│   └── Services/
│       ├── BookingService.php
│       └── WhatsAppService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/api.php
├── tests/
├── Dockerfile
└── composer.json
```

## Deploy (Render)

1. Push ke GitHub
2. Import repo di Render (Docker)
3. Set environment variables

### Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_ENV` | Environment | `production` |
| `APP_DEBUG` | Debug mode | `false` |
| `APP_KEY` | Encryption key | *(generate with `php artisan key:generate --show`)* |
| `APP_URL` | App URL | `https://bookeasy-api.onrender.com` |
| `FRONTEND_URL` | Frontend URL | `https://your-app.vercel.app` |
| `DB_CONNECTION` | Database driver | `pgsql` |
| `DB_HOST` | Database host | `aws-1-ap-southeast-1.pooler.supabase.com` |
| `DB_PORT` | Database port | `5432` |
| `DB_DATABASE` | Database name | `postgres` |
| `DB_USERNAME` | Database user | `postgres.xxxxx` |
| `DB_PASSWORD` | Database password | *(from Supabase)* |
| `SANCTUM_STATEFUL_DOMAINS` | SPA domains | `your-app.vercel.app` |
| `SESSION_DRIVER` | Session driver | `cookie` |
| `CACHE_STORE` | Cache driver | `file` |
| `LOG_CHANNEL` | Log channel | `stderr` |
