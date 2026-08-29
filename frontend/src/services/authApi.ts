import axios from "axios";
import router from "../router";

const API_BASE = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api';

const authClient = axios.create({
    baseURL: API_BASE,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    timeout: 10000,
});

// TOKEN MANAGEMENT

export const setToken = (token: string): void => {
    localStorage.setItem('admin_token', token);
};

export const getToken = (): string | null => {
    return localStorage.getItem('admin_token');
};

export const removeToken = (): void => {
    localStorage.removeItem('admin_token');
};

export const isAuthenticated = (): boolean => {
    return getToken() !== null;
};

// API CALLS

export const authApi = {
    async login(email: string, password: string) {
        const response = await authClient.post('/auth/login', { email, password });
        const result = response.data;

        if (result.success && result.data?.token) {
            setToken(result.data.token);
        }

        return result;
    },

    async logout() {
        const token = getToken();
        if (token) {
            try {
                await authClient.post('/auth/logout', {}, {
                    headers: { Authorization: `Bearer ${token}` },
                });
            } catch (_) { /* ignore */ }
        }
        removeToken();
    },
};

// REQUEST interceptor — attach token
authClient.interceptors.request.use((config) => {
    const token = getToken();
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// RESPONSE interceptor — auto-redirect on 401
authClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401 && router.currentRoute.value.name !== 'admin-login') {
            removeToken();
            router.push({ name: 'admin-login' });
        }
        return Promise.reject(error);
    }
);

export default authClient;
