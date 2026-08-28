# BookEasy — Sistem Reservasi Jadwal

Aplikasi web self-service untuk UMKM jasa (barbershop, salon, klinik kecantikan, dll) agar pelanggan bisa booking jadwal secara online tanpa bolak-balik chat WhatsApp. Dirancang sebagai portofolio project yang production-grade.

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13.x (REST API, PHP 8.3) |
| Frontend | Vue 3.5 + TypeScript + Vite 8 + Tailwind CSS 4 |
| Database | MySQL 8.x |
| Auth | Laravel Sanctum (token-based, untuk admin) |
| Testing Backend | Pest PHP 4.x |
| Testing Frontend | Vitest + Vue Testing Library |

## Struktur Project

```
bookeasy/
├── backend/          # Laravel REST API
├── frontend/         # Vue.js SPA
├── README.md         # File ini
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

## Fitur Utama

- **F1** — Grid ketersediaan slot (real-time dari database)
- **F2** — Formulir pemesanan sederhana (guest checkout, tanpa login)
- **F3** — Server-side validation + race condition protection
- **F4** — Auto-refresh UI setelah booking berhasil

## Dokumentasi Lengkap

- [`backend/README.md`](backend/README.md) — Setup detail backend, MySQL, testing
- [`frontend/README.md`](frontend/README.md) — Setup detail frontend, development
