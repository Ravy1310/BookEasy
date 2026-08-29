<template>
    <div class="min-h-screen bg-bke-surface flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm rounded-2xl shadow-lg p-8 anim-fade-in-up">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="font-display text-2xl font-extrabold text-bke-primary mb-1 tracking-tight">BookEasy Admin</h1>
                <p class="text-sm text-gray-400">Masuk ke dashboard Anda</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleLogin" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5" for="email">
                        Email
                    </label>
                    <input
                        v-model="form.email"
                        id="email"
                        type="email"
                        placeholder="nama@email.com"
                        required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bke-primary focus:border-bke-primary transition-all duration-200"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5" for="password">
                        Password
                    </label>
                    <input
                        v-model="form.password"
                        id="password"
                        type="password"
                        placeholder="Masukkan password"
                        required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-bke-primary focus:border-bke-primary transition-all duration-200"
                    />
                </div>

                <!-- Pesan error -->
                <Transition name="toast">
                    <div v-if="errorMessage" class="text-sm text-red-500 text-center bg-red-50 p-3 rounded-lg">
                        {{ errorMessage }}
                    </div>
                </Transition>

                <button
                    type="submit"
                    :disabled="isLoading"
                    class="w-full bg-bke-primary hover:bg-bke-primary-hover disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-semibold py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                >
                    <svg
                        v-if="isLoading"
                        class="animate-spin h-4 w-4 text-white"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    {{ isLoading ? 'Memproses...' : 'Masuk' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { authApi } from '../services/authApi';

const router = useRouter();
const isLoading = ref(false);
const errorMessage = ref('');

const form = reactive({
    email: '',
    password: '',
});

const handleLogin = async () => {
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const result = await authApi.login(form.email, form.password);
        if (result.success) {
            router.push({ name: 'admin' });
        } else {
            errorMessage.value = result.message || 'Email atau password salah.';
        }
    } catch (error: any) {
        errorMessage.value = error.response?.data?.message || 'Email atau password salah.';
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.font-display {
    font-family: var(--font-display);
}

.toast-enter-active {
    animation: slide-down 0.3s ease-out;
}
.toast-leave-active {
    animation: slide-down 0.2s ease-in reverse;
}
</style>
