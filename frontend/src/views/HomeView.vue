<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import ScheduleGrid from '../components/ScheduleGrid.vue';
import BookingForm from '../components/BookingForm.vue';
import { bookingApi } from '../services/bookingApi';

const globalSelectedSlot = ref<string | null>(null);
const isSubmitting = ref(false);
const errorMessage = ref<string | null>(null);
const successMessage = ref<string | null>(null);
const gridKey = ref(0);
const isReady = ref(false);

// Live clock
const now = ref(new Date());
let clockTimer: ReturnType<typeof setInterval> | null = null;

const formattedTime = ref('');
const formattedGreeting = ref('');

const updateClock = () => {
    now.value = new Date();
    formattedTime.value = now.value.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
    const hour = now.value.getHours();
    if (hour < 11) formattedGreeting.value = 'Selamat pagi';
    else if (hour < 15) formattedGreeting.value = 'Selamat siang';
    else if (hour < 18) formattedGreeting.value = 'Selamat sore';
    else formattedGreeting.value = 'Selamat malam';
};

onMounted(() => {
    updateClock();
    clockTimer = setInterval(updateClock, 1000);
    // trigger entrance animations setelah mount
    requestAnimationFrame(() => { isReady.value = true; });
});

onUnmounted(() => {
    if (clockTimer) clearInterval(clockTimer);
});

const handleBookingSubmit = async (formData: { customer_name: string; customer_phone: string }) => {
    if (!globalSelectedSlot.value) return;

    isSubmitting.value = true;
    errorMessage.value = null;
    successMessage.value = null;

    try {
        const payload = {
            ...formData,
            time_slot: globalSelectedSlot.value,
            booking_date: new Date().toISOString().split('T')[0],
        };

        await bookingApi.createBooking(payload);

        successMessage.value = `Booking berhasil! Sampai jumpa jam ${globalSelectedSlot.value} ya`;
        gridKey.value += 1;
        globalSelectedSlot.value = null;

        setTimeout(() => {
            successMessage.value = null;
        }, 4000);
    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            const backendMessage = error.response.data.errors;
            errorMessage.value =
                backendMessage.time_slot?.[0] ||
                error.response.data.message ||
                'Validasi gagal. Silakan periksa data Anda kembali.';
        } else {
            errorMessage.value = 'Gagal terhubung ke server. Silakan coba beberapa saat lagi.';
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-bke-surface font-sans">
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-bke-primary via-indigo-500 to-violet-600 text-white">
            <!-- Decorative dots pattern -->
            <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 24px 24px;"></div>

            <div class="relative max-w-3xl mx-auto px-4 sm:px-8 pt-10 pb-12 sm:pt-14 sm:pb-16">
                <!-- Greeting + clock -->
                <div
                    class="flex items-center gap-3 mb-4"
                    :class="isReady ? 'anim-fade-in-up' : 'opacity-0'"
                >
                    <span class="font-mono text-3xl sm:text-4xl font-medium tracking-tight tabular-nums">
                        {{ formattedTime }}
                    </span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                </div>

                <h1
                    class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight mb-3"
                    :class="isReady ? 'anim-fade-in-up stagger-1' : 'opacity-0'"
                >
                    {{ formattedGreeting }}, mau potong rambut?
                </h1>

                <p
                    class="text-indigo-100 text-base sm:text-lg max-w-md"
                    :class="isReady ? 'anim-fade-in-up stagger-2' : 'opacity-0'"
                >
                    Pilih jam yang pas, isi data singkat, selesai. Tanpa antri, tanpa chat.
                </p>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-4 sm:px-8 -mt-6 relative z-10">
            <!-- Toast Error -->
            <Transition name="toast">
                <div
                    v-if="errorMessage"
                    class="mb-5 bg-white text-red-600 p-4 rounded-xl flex items-center gap-3 shadow-lg border border-red-100"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium">{{ errorMessage }}</span>
                </div>
            </Transition>

            <!-- Toast Success -->
            <Transition name="toast">
                <div
                    v-if="successMessage"
                    class="mb-5 bg-white text-green-700 p-4 rounded-xl flex items-center gap-3 shadow-lg border border-green-100"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium">{{ successMessage }}</span>
                </div>
            </Transition>

            <!-- Schedule Grid -->
            <div :class="isReady ? 'anim-fade-in-up stagger-3' : 'opacity-0'">
                <ScheduleGrid :key="gridKey" @slot-selected="(slot) => (globalSelectedSlot = slot)" />
            </div>

            <!-- Booking Form -->
            <Transition name="form-slide">
                <BookingForm
                    v-if="!successMessage"
                    :selected-slot="globalSelectedSlot"
                    :is-submitting="isSubmitting"
                    @submit="handleBookingSubmit"
                />
            </Transition>
        </div>
    </div>
</template>

<style scoped>
.font-display {
    font-family: var(--font-display);
}
.font-mono {
    font-family: var(--font-mono);
}

/* Toast transition */
.toast-enter-active {
    animation: slide-down 0.3s ease-out;
}
.toast-leave-active {
    animation: slide-down 0.2s ease-in reverse;
}

/* Form slide transition */
.form-slide-enter-active {
    animation: slide-up 0.35s ease-out;
}
.form-slide-leave-active {
    animation: slide-up 0.2s ease-in reverse;
}
</style>
