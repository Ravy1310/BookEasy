# BookEasy — Frontend (Vue.js SPA)

Frontend untuk sistem reservasi jadwal BookEasy. Dibangun dengan Vue 3.5, TypeScript, Vite 8, dan Tailwind CSS 4.

## Prasyarat

- Node.js 18 atau lebih baru (cek: `node -v`)
- npm (cek: `npm -v`)
- Backend Laravel sudah jalan di `http://localhost:8000`

## Setup

### 1. Install Dependencies

```bash
npm install
```

### 2. Konfigurasi Environment

Tidak ada `.env` yang perlu dikonfigurasi secara manual. Frontend otomatis terhubung ke backend di `http://localhost:8000` melalui service API.

### 3. Jalankan Development Server

```bash
npm run dev
```

Frontend jalan di `http://localhost:5173`.

> **Catatan:** Backend harus sudah jalan terlebih dahulu. CORS sudah dikonfigurasi di backend untuk mengizinkan origin `http://localhost:5173`.

## Testing

```bash
npm run test
```

Menggunakan Vitest dengan jsdom environment.

### Struktur Test

```
src/components/__test__/
├── ScheduleGrid.test.ts    # Test grid ketersediaan slot
└── BookingForm.test.ts     # Test form booking (validasi, submit)
```

### Skenario Test

1. Grid merender slot "Tersedia" dan "Penuh" dengan benar
2. Slot "Penuh" tidak bisa diklik
3. Tombol submit disabled saat nama kosong
4. Setelah submit sukses, grid ter-update tanpa reload

## Build untuk Production

```bash
npm run build
```

Output build ada di folder `dist/`, bisa di-deploy ke Vercel/Netlify/hosting statis lainnya.

## Struktur Folder

```
frontend/
├── src/
│   ├── components/
│   │   ├── ScheduleGrid.vue       # Grid ketersediaan slot
│   │   ├── SlotButton.vue         # Tombol slot (available/booked)
│   │   ├── BookingForm.vue        # Form pemesanan
│   │   └── __test__/              # Vitest test files
│   ├── services/
│   │   └── bookingApi.js          # Axios calls ke backend API
│   ├── App.vue                    # Komponen utama (routing sederhana)
│   └── main.ts                    # Entry point
├── index.html
├── package.json
├── vite.config.ts                 # Vite + Vitest config
├── tailwind.config.js
└── tsconfig.json
```

## Tech Stack

| Komponen | Versi | Keterangan |
|---|---|---|
| Vue.js | ^3.5 | Composition API (`<script setup>`) |
| TypeScript | ~6.0 | Type safety |
| Vite | ^8.2 | Dev server & build tool |
| Tailwind CSS | ^4.3 | Utility-first CSS |
| Axios | ^1.20 | HTTP client |
| Vitest | ^4.1 | Unit testing |
| Vue Testing Library | ^8.1 | Component testing |
