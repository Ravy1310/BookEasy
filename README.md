# BookEasy — Sistem Reservasi Jadwal untuk UMKM

Aplikasi web self-service untuk UMKM jasa (barbershop, salon, klinik kecantikan, studio foto, dll) agar pelanggan bisa booking jadwal secara online tanpa bolak-balik chat WhatsApp.

## Tentang BookEasy

Mayoritas UMKM jasa di Indonesia masih mengelola reservasi secara manual — buku catatan atau chat WhatsApp langsung. Model ini rentan terhadap **double-booking**, keterbatasan waktu respons admin, dan tidak ada jejak data terpusat.

BookEasy hadir sebagai solusi: pelanggan bisa melihat ketersediaan slot secara real-time, memilih jam yang tersedia, mengisi nama dan nomor WhatsApp, lalu konfirmasi booking — **tanpa perlu login atau registrasi**. Di sisi backend, sistem mencegah double-booking menggunakan database transaction dan locking.

Aplikasi ini juga terintegrasi dengan **WhatsApp** untuk mengirim notifikasi konfirmasi booking dan reminder otomatis ke pelanggan.

## Fitur Utama

### Sudah Diimplementasi

- **Slot Availability Grid** — Grid jadwal real-time dari database, menampilkan status "Tersedia" dan "Penuh" dengan visual yang jelas
- **Guest Checkout** — Pelanggan bisa booking tanpa login/registrasi, cukup isi nama dan nomor WhatsApp
- **Server-Side Validation** — Semua validasi dilakukan di backend menggunakan Laravel Form Request, termasuk pengecekan slot yang sudah terisi
- **Race Condition Protection** — Menggunakan `DB::transaction()` + `lockForUpdate()` untuk mencegah dua request booking slot yang sama secara bersamaan
- **Auto-Refresh UI** — Setelah booking berhasil, grid jadwal ter-update otomatis tanpa perlu reload browser
- **Admin Authentication** — Login admin dengan Laravel Sanctum (token-based auth)
- **Admin Dashboard** — Melihat daftar booking hari ini

### Akan Datang

- **Manajemen Jadwal & Hari Libur** — Admin bisa mengatur jam operasional per hari dan menandai hari libur
- **WhatsApp Notifikasi & Reminder** — Integrasi Fonnte WhatsApp Business API untuk:
  - Mengirim notifikasi konfirmasi booking ke nomor pelanggan setelah booking berhasil
  - Mengirim reminder WhatsApp X jam sebelum jadwal terjadwal (configurable per admin)

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13.x (REST API, PHP 8.3) |
| Frontend | Vue 3.5 + TypeScript + Vite 8 + Tailwind CSS 4 |
| Database | MySQL 8.x |
| Auth | Laravel Sanctum (token-based, untuk admin) |
| WhatsApp API | Fonnte (akan diintegrasikan) |
| Testing Backend | Pest PHP 4.x |
| Testing Frontend | Vitest + Vue Testing Library |

## Struktur Project

```
bookeasy/
├── backend/          # Laravel REST API
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/
│   ├── routes/
│   └── tests/
├── frontend/         # Vue.js SPA
│   ├── src/
│   │   ├── components/
│   │   └── services/
│   └── ...
├── Ravy/             # Dokumentasi perencanaan (PRD, Architecture, Design System)
└── README.md
```

## Quick Start

### Prasyarat

- PHP 8.3+ & Composer
- MySQL 8.x
- Node.js 18+ & npm

### 1. Setup Backend

```bash
cd backend
cp .env.example .env
```

Edit `.env`, isi koneksi MySQL:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookeasy
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

Backend jalan di `http://localhost:8000`.

### 2. Setup Frontend

```bash
cd frontend
npm install
npm run dev
```

Frontend jalan di `http://localhost:5173` dan otomatis terhubung ke backend.

### 3. Menjalankan Test

```bash
# Backend (dari folder backend/)
php artisan test

# Frontend (dari folder frontend/)
npm run test
```

## API Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/bookings?date=YYYY-MM-DD` | No | Mengambil daftar slot + status untuk tanggal tertentu |
| POST | `/api/bookings` | No | Membuat booking baru (validasi via Form Request) |
| POST | `/api/auth/login` | No | Admin login, mengembalikan Sanctum token |
| POST | `/api/auth/logout` | Yes | Admin logout, invalidate token |
| GET | `/api/dashboard?date=YYYY-MM-DD` | Yes | Dashboard admin: daftar booking hari ini |

### Response Format

```json
// Sukses
{ "success": true, "data": { ... } }

// Error
{ "success": false, "message": "...", "errors": { ... } }
```

## Testing

### Backend (Pest PHP)

```
tests/
├── Feature/
│   ├── BookingApiTest.php      # Happy path, validation, conflict, concurrency
│   ├── AuthApiTest.php         # Login/logout, token validation
│   └── DashboardApiTest.php    # Auth, date filter, empty data
└── Unit/
    └── BookingServiceTest.php  # Double-booking prevention
```

Jalankan: `php artisan test`

### Frontend (Vitest)

```
src/components/__test__/
├── ScheduleGrid.test.ts    # Render slot available/booked
└── BookingForm.test.ts     # Submit button behavior
```

Jalankan: `npm run test`

## Dokumentasi Lengkap

- `Ravy/prd.md` — Product Requirements Document
- `Ravy/Architecture.md` — Rencana implementasi teknis
- `Ravy/DESIGN.md` — Design system & UX spec

---

Dibuat oleh Rafi Isnanto Syahlefi — Proyek portofolio full-stack development.
