# BookEasy — Frontend

Vue.js SPA untuk sistem reservasi jadwal BookEasy.

## Tech Stack

- **Vue 3.5** — Composition API (`<script setup>`)
- **TypeScript** — Type safety
- **Vite 8** — Dev server & build tool
- **Tailwind CSS 4** — Utility-first CSS
- **Axios** — HTTP client
- **Vitest** — Unit testing
- **Vue Testing Library** — Component testing

## Prasyarat

- Node.js 18+
- Backend Laravel sudah jalan di `http://localhost:8000`

## Setup

```bash
npm install
npm run dev
```

Frontend jalan di `http://localhost:5173`.

## Testing

```bash
npx vitest run
```

36 test cases — semua harus lolos sebelum push.

### Struktur Test

```
src/components/__test__/
├── AdminLogin.test.ts
├── AdminDashboard.test.ts
├── ScheduleManager.test.ts
├── HolidayManager.test.ts
├── ScheduleGrid.test.ts
└── BookingForm.test.ts
```

## Build

```bash
npm run build
```

Output ada di folder `build/`. Bisa di-deploy ke Vercel atau hosting statis lainnya.

## Struktur Folder

```
frontend/
├── src/
│   ├── components/
│   │   ├── AdminLogin.vue
│   │   ├── AdminLayout.vue
│   │   ├── AdminDashboard.vue
│   │   ├── ScheduleManager.vue
│   │   ├── HolidayManager.vue
│   │   ├── ScheduleGrid.vue
│   │   ├── SlotButton.vue
│   │   ├── BookingForm.vue
│   │   ├── ConfirmModal.vue
│   │   └── __test__/
│   ├── views/
│   │   ├── HomeView.vue
│   │   ├── AdminView.vue
│   │   ├── AdminLoginView.vue
│   │   ├── ScheduleView.vue
│   │   └── HolidayView.vue
│   ├── services/
│   │   ├── authApi.ts
│   │   └── adminApi.ts
│   ├── router/index.ts
│   ├── style.css
│   └── main.ts
├── vercel.json
├── vite.config.ts
└── package.json
```

## Deploy (Vercel)

1. Push ke GitHub
2. Import repo di Vercel
3. Set `VITE_API_BASE_URL` ke URL backend production

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `VITE_API_BASE_URL` | Backend API URL | `http://127.0.0.1:8000/api` |
| `VITE_APP_NAME` | App name | `BookEasy` |
| `VITE_APP_ENV` | Environment | `development` |
