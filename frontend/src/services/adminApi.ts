import authClient from "./authApi";

// DASHBOARD

export const adminApi = {
    async getDashboard(date?: string) {
        const params = date ? {date} : {};
        const response = await authClient.get('/admin/dashboard', { params });
        return response.data;
    },

    // batalkan booking
    async cancelBooking(id: number) {
        const response = await authClient.delete(`/admin/bookings/${id}`);
        return response.data;
    },

    //SCHEDULES

    // ambil jam operasional
    async getSchedules() {
        const response = await authClient.get('/admin/schedules');
        return response.data;
    },

    // update jam operasional
    async updateSchedules(schedules: any[]) {
        const response = await authClient.put('/admin/schedules', { schedules });
        return response.data;
    },

    //HOLIDAY

    // ambil hari libur
    async getHolidays(year?: number) {
        const params= year ? { year } : {};
        const response = await authClient.get('/admin/holidays', { params });
        return response.data;
    },

    // Tambah hari libur
    async addHoliday(date: string, reason: string) {
        const response = await authClient.post('/admin/holidays', { date, reason });
        return response.data;
    },

    // hapus hari libur
    async removeHoliday(date: string) {
        const response = await authClient.delete(`/admin/holidays/${date}`);
        return response.data;
    },
};