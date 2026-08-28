import { createApp } from 'vue'
import './style.css' // Pastikan ini mengarah ke file Tailwind Anda
import App from './App.vue'
import { bookingApi } from './services/bookingApi'

createApp(App).mount('#app')

// Test hit API saat aplikasi dimuat 
bookingApi.getBookings().then(res => console.log('Data dari Laravel:', res));