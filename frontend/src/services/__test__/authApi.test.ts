// src/services/__test__/authApi.test.ts
// ===========================
// TEST: AUTH SERVICE
// ===========================
// Test semua fungsi di authApi.ts
//
// Kenapa mock axios?
// Kita tidak mau test ini benar-benar hit backend.
// Kita hanya mau pastikan logic di authApi.ts benar:
// - Token disimpan saat login sukses
// - Token dihapus saat logout
// - isAuthenticated() return value yang benar

import { describe, test, expect, vi, beforeEach, afterEach } from 'vitest';
import { setToken, getToken, removeToken, isAuthenticated, authApi } from '../authApi';

// ===========================
// MOCK AXIOS
// ===========================
// vi.hoisted() memastikan mock fn tersedia sebelum vi.mock() factory dijalankan.
// vi.mock() di-hoist oleh Vitest ke paling atas — jadi variable biasa belum
// terinisialisasi saat factory-nya berjalan. vi.hoisted() adalah cara yang benar.
const { mockPostFn } = vi.hoisted(() => ({
    mockPostFn: vi.fn(),
}));

vi.mock('axios', () => ({
    default: {
        // create() dipanggil saat authApi.ts di-import untuk membuat authClient.
        // Kita return mock instance yang post-nya bisa kita kendalikan per-test.
        create: vi.fn(() => ({
            post: mockPostFn,
            interceptors: {
                request: { use: vi.fn() },
                response: { use: vi.fn() },
            },
        })),
    },
}));

// ===========================
// SETUP & TEARDOWN
// ===========================
beforeEach(() => {
    localStorage.clear();
});

afterEach(() => {
    vi.clearAllMocks();
});

// ===========================
// TEST: TOKEN MANAGEMENT
// ===========================

describe('Token Management', () => {
    test('setToken menyimpan token ke localStorage', () => {
        const token = 'abc123-token';
        setToken(token);
        expect(localStorage.getItem('admin_token')).toBe(token);
    });

    test('getToken mengambil token dari localStorage', () => {
        const token = 'xyz789-token';
        localStorage.setItem('admin_token', token);
        expect(getToken()).toBe(token);
    });

    test('getToken return null jika tidak ada token', () => {
        expect(getToken()).toBeNull();
    });

    test('removeToken menghapus token dari localStorage', () => {
        localStorage.setItem('admin_token', 'token-to-delete');
        removeToken();
        expect(localStorage.getItem('admin_token')).toBeNull();
    });

    test('isAuthenticated return true jika ada token', () => {
        localStorage.setItem('admin_token', 'some-token');
        expect(isAuthenticated()).toBe(true);
    });

    test('isAuthenticated return false jika tidak ada token', () => {
        expect(isAuthenticated()).toBe(false);
    });
});

// ===========================
// TEST: LOGIN
// ===========================

describe('authApi.login', () => {
    test('login sukses menyimpan token ke localStorage', async () => {
        // Arrange
        const mockToken = 'success-token-123';
        const mockUser = { id: 1, name: 'Admin', email: 'admin@test.com' };

        mockPostFn.mockResolvedValueOnce({
            data: {
                success: true,
                data: { token: mockToken, user: mockUser },
            },
        });

        // Act
        const result = await authApi.login('admin@test.com', 'password123');

        // Assert
        expect(result.success).toBe(true);
        expect(localStorage.getItem('admin_token')).toBe(mockToken);
        expect(mockPostFn).toHaveBeenCalledWith('/auth/login', {
            email: 'admin@test.com',
            password: 'password123',
        });
    });

    test('login gagal (401) tidak menyimpan token', async () => {
        // Arrange
        mockPostFn.mockResolvedValueOnce({
            data: {
                success: false,
                message: 'Invalid credentials',
            },
        });

        // Act
        const result = await authApi.login('wrong@email.com', 'wrongpass');

        // Assert
        expect(result.success).toBe(false);
        expect(localStorage.getItem('admin_token')).toBeNull();
    });
});

// ===========================
// TEST: LOGOUT
// ===========================

describe('authApi.logout', () => {
    test('logout menghapus token dari localStorage', async () => {
        // Arrange
        localStorage.setItem('admin_token', 'token-to-logout');
        mockPostFn.mockResolvedValueOnce({ data: { success: true } });

        // Act
        await authApi.logout();

        // Assert
        expect(localStorage.getItem('admin_token')).toBeNull();
    });

    test('logout tetap hapus token meskipun backend gagal', async () => {
        // Arrange
        localStorage.setItem('admin_token', 'token-to-logout');
        mockPostFn.mockRejectedValueOnce(new Error('Network Error'));

        // Act
        await authApi.logout();

        // Assert: token tetap terhapus
        expect(localStorage.getItem('admin_token')).toBeNull();
    });
});
