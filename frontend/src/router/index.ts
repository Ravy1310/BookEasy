import { createRouter, createWebHistory } from 'vue-router';
import { isAuthenticated } from '../services/authApi';

const HomeView       = () => import('../views/HomeView.vue');
const AdminLoginView = () => import('../views/AdminLoginView.vue');
const AdminView      = () => import('../views/AdminView.vue');
const ScheduleView   = () => import('../views/ScheduleView.vue');
const HolidayView    = () => import('../views/HolidayView.vue');

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView,
    },
    {
        path: '/admin/login',
        name: 'admin-login',
        component: AdminLoginView,
        beforeEnter: () => {
            if (isAuthenticated()) return { name: 'admin' };
        },
    },
    {
        path: '/admin',
        name: 'admin',
        component: AdminView,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/jadwal',
        name: 'admin-jadwal',
        component: ScheduleView,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/libur',
        name: 'admin-libur',
        component: HolidayView,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    if (to.meta.requiresAuth && !isAuthenticated()) {
        return { name: 'admin-login' };
    }
});

export default router;
