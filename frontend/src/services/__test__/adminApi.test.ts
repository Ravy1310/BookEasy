import { describe, test, expect, vi, beforeEach } from 'vitest';
import { adminApi } from '../adminApi';

// ===========================
// MOCK AUTHCLIENT
// ===========================
// Mock authClient module
// Semua import authClient di adminApi.ts akan pakai mock ini


const { mockGet, mockPost, mockPut, mockDelete } = vi.hoisted(() => ({
    mockGet: vi.fn(),
    mockPost: vi.fn(),
    mockPut: vi.fn(),
    mockDelete: vi.fn(),
}));


vi.mock('../authApi', () => ({
    default: {
        get: mockGet,
        post: mockPost,
        put: mockPut,
        delete: mockDelete,
    },
}));

// ===========================
// SETUP & TEARDOWN
// ===========================

beforeEach(() => {
    vi.clearAllMocks();
});

// ===========================
// TEST: DASHBOARD
// ===========================

describe('adminApi.getDashboard', () => {
    test('getDashboard memanggil GET /admin/dashboard', async () => {
        // Arrange
        const mockData = {
            success: true,
            data: {
                today_bookings: [],
                total_bookings: 0,
                popular_slots: [],
            },
        };
        mockGet.mockResolvedValueOnce({ data: mockData });

        // Act
        const result = await adminApi.getDashboard();

        // Assert
        expect(mockGet).toHaveBeenCalledWith('/admin/dashboard', { params: {} });
        expect(result).toEqual(mockData);
    });

    test('getDashboard dengan date parameter', async () => {
        // Arrange
        mockGet.mockResolvedValueOnce({ data: { success: true } });

        // Act
        await adminApi.getDashboard('2026-08-29');

        // Assert
        expect(mockGet).toHaveBeenCalledWith('/admin/dashboard', {
            params: { date: '2026-08-29' },
        });
    });
});

// ===========================
// TEST: SCHEDULES
// ===========================

describe('adminApi.getSchedules', () => {
    test('getSchedules memanggil GET /admin/schedules', async () => {
        // Arrange
        const mockSchedules = [
            { day_of_week: 0, start_time: '09:00', end_time: '17:00', is_closed: false },
        ];
        mockGet.mockResolvedValueOnce({ data: { success: true, data: mockSchedules } });

        // Act
        const result = await adminApi.getSchedules();

        // Assert
        expect(mockGet).toHaveBeenCalledWith('/admin/schedules');
        expect(result.data).toEqual(mockSchedules);
    });
});

describe('adminApi.updateSchedules', () => {
    test('updateSchedules memanggil PUT /admin/schedules', async () => {
        // Arrange
        const schedules = [
            { day_of_week: 0, start_time: '09:00', end_time: '17:00', is_closed: false },
        ];
        mockPut.mockResolvedValueOnce({ data: { success: true } });

        // Act
        const result = await adminApi.updateSchedules(schedules);

        // Assert
        expect(mockPut).toHaveBeenCalledWith('/admin/schedules', { schedules });
        expect(result.success).toBe(true);
    });
});

// ===========================
// TEST: HOLIDAYS
// ===========================

describe('adminApi.getHolidays', () => {
    test('getHolidays memanggil GET /admin/holidays', async () => {
        // Arrange
        mockGet.mockResolvedValueOnce({ data: { success: true, data: [] } });

        // Act
        await adminApi.getHolidays();

        // Assert
        expect(mockGet).toHaveBeenCalledWith('/admin/holidays', { params: {} });
    });

    test('getHolidays dengan year parameter', async () => {
        // Arrange
        mockGet.mockResolvedValueOnce({ data: { success: true } });

        // Act
        await adminApi.getHolidays(2026);

        // Assert
        expect(mockGet).toHaveBeenCalledWith('/admin/holidays', {
            params: { year: 2026 },
        });
    });
});

describe('adminApi.addHoliday', () => {
    test('addHoliday memanggil POST /admin/holidays', async () => {
        // Arrange
        const date = '2026-12-25';
        const reason = 'Christmas';
        mockPost.mockResolvedValueOnce({
            data: { success: true, data: { date, reason } },
        });

        // Act
        const result = await adminApi.addHoliday(date, reason);

        // Assert
        expect(mockPost).toHaveBeenCalledWith('/admin/holidays', { date, reason });
        expect(result.data.date).toBe(date);
    });
});

describe('adminApi.removeHoliday', () => {
    test('removeHoliday memanggil DELETE /admin/holidays/{date}', async () => {
        // Arrange
        const date = '2026-12-25';
        mockDelete.mockResolvedValueOnce({ data: { success: true } });

        // Act
        const result = await adminApi.removeHoliday(date);

        // Assert
        expect(mockDelete).toHaveBeenCalledWith(`/admin/holidays/${date}`);
        expect(result.success).toBe(true);
    });
});
