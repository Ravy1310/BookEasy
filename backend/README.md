# BookEasy — Backend (Laravel REST API)

REST API untuk sistem reservasi jadwal BookEasy. Dibangun dengan Laravel 13.x, PHP 8.3, dan MySQL 8.x.

## Prasyarat

- PHP 8.3 atau lebih baru (cek: `php -v`)
- Composer (cek: `composer -V`)
- MySQL 8.x (cek: `mysql --version`)
- Node.js (untuk build asset, opsional saat development API)

## Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Buka `.env`, sesuaikan koneksi MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookeasy
DB_USERNAME=root
DB_PASSWORD=
```

Pastikan database `bookeasy` sudah ada di MySQL. Kalau belum, buat dulu:

```sql
CREATE DATABASE bookeasy;
```

### 3. Jalankan Migrations

```bash
php artisan migrate
```

### 4. (Opsional) Seed Data Contoh

```bash
php artisan db:seed
```

## Menjalankan Server

```bash
php artisan serve
```

API tersedia di `http://localhost:8000`.

## Testing

```bash
# Jalankan semua test
php artisan test

# Atau pakai composer script
composer test
```

Test menggunakan SQLite in-memory (otomatis dikonfigurasi di `phpunit.xml`), jadi tidak perlu setup database terpisah untuk testing.

### Struktur Test

```
tests/
├── Feature/
│   ├── BookingApiTest.php       # Test endpoint booking (CRUD, validasi, conflict)
│   └── ExampleTest.php
└── Unit/
    ├── BookingServiceTest.php   # Test logic service (race condition, transaction)
    └── ExampleTest.php
```

## Endpoint API

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | `/api/bookings?date=YYYY-MM-DD` | Ambil daftar slot + status | Tidak |
| POST | `/api/bookings` | Booking baru | Tidak |

## Struktur Folder

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/     # BookingController
│   │   ├── Requests/            # StoreBookingRequest (validasi)
│   │   └── Resources/           # BookingResource (response format)
│   ├── Models/                  # Booking
│   └── Services/                # BookingService (logic bisnis)
├── database/
│   ├── migrations/              # Schema database
│   └── factories/               # BookingFactory (data dummy testing)
├── routes/
│   └── api.php                  # Route definition
├── tests/                       # Pest test files
└── phpunit.xml                  # Test configuration
```

## Format Response API

Semua response mengikuti format konsisten:

```json
// Sukses
{ "success": true, "data": { ... } }

// Gagal
{ "success": false, "message": "...", "errors": { ... } }
```
