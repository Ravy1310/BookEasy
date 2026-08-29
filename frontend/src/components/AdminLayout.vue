<script setup lang="ts">
import { useRouter, useRoute } from 'vue-router';
import { authApi } from '../services/authApi';

const router = useRouter();
const route = useRoute();

const handleLogout = async () => {
    await authApi.logout();
    router.push({ name: 'admin-login' });
};

const isActive = (name: string) => route.name === name;
</script>

<template>
    <div class="bg-bke-surface min-h-screen flex flex-col font-sans">

        <!-- TOP NAVBAR -->
        <header class="bg-white flex justify-between items-center px-8 py-4 w-full border-b border-gray-100 sticky top-0 z-50 shadow-sm">
            <span class="font-display text-xl font-extrabold text-bke-primary tracking-tight">BookEasy</span>
            <button
                @click="handleLogout"
                class="flex items-center gap-2 px-4 py-2 rounded-xl hover:bg-red-50 transition-all duration-200 text-red-400 hover:text-red-500 font-label-md"
            >
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">logout</span>
                <span>Logout</span>
            </button>
        </header>

        <div class="flex flex-1 overflow-hidden">

            <!-- SIDEBAR -->
            <aside class="w-64 bg-white border-r border-gray-100 hidden md:flex flex-col py-6 px-4 shrink-0">
                <nav class="flex flex-col gap-1.5 w-full">
                    <RouterLink
                        :to="{ name: 'admin' }"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl font-label-md transition-all duration-200"
                        :class="isActive('admin')
                            ? 'bg-bke-primary text-white shadow-md'
                            : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600'"
                    >
                        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">dashboard</span>
                        Dashboard
                    </RouterLink>

                    <RouterLink
                        :to="{ name: 'admin-jadwal' }"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl font-label-md transition-all duration-200"
                        :class="isActive('admin-jadwal')
                            ? 'bg-bke-primary text-white shadow-md'
                            : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600'"
                    >
                        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">calendar_month</span>
                        Jadwal
                    </RouterLink>

                    <RouterLink
                        :to="{ name: 'admin-libur' }"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl font-label-md transition-all duration-200"
                        :class="isActive('admin-libur')
                            ? 'bg-bke-primary text-white shadow-md'
                            : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600'"
                    >
                        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">event_busy</span>
                        Libur
                    </RouterLink>
                </nav>
            </aside>

            <!-- SLOT konten halaman -->
            <main class="flex-1 p-6 md:p-8 overflow-y-auto bg-bke-surface">
                <div class="max-w-5xl mx-auto">
                    <slot />
                </div>
            </main>

        </div>
    </div>
</template>

<style scoped>
.font-display {
    font-family: var(--font-display);
}
</style>
