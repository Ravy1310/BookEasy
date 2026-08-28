import axios from "axios";

// konfigurasi dasar axios. Mengarahkan ke URL backend laravel lokal
const apiClient = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',

    },
});

export const bookingApi = {
    // Ambil jadwal kosong berdasarkan tanggal 
    async getBookings(date: string | null = null) {
        const params = date ? { date } : {};
        const response = await apiClient.get('/bookings', { params });
        return response.data;
    },

    //Tembak data booking baru ke server
    async createBooking(payload: any) {
        const response = await apiClient.post('/bookings', payload);
        return response.data;
    }
}