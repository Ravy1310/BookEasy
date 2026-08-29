<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { adminApi } from '../services/adminApi';
import ConfirmModal from './ConfirmModal.vue';

interface Booking {
    id: number;
    customer_name: string;
    customer_phone: string;
    time_slot: string;
    booking_date: string;
    created_at: string;
}

const bookings = ref<Booking[]>([]);
const totalBookings = ref(0);
const currentDate = ref('');
const isLoading = ref(true);
const isReady = ref(false);
const errorMessage = ref('');
const cancellingId = ref<number | null>(null);

// Modal state
const showCancelModal = ref(false);
const cancelTarget = ref<Booking | null>(null);

let refreshTimer: ReturnType<typeof setInterval> | null = null;

const formattedDate = computed(() => {
    if (!currentDate.value) return '';
    const date = new Date(currentDate.value + 'T00:00:00');
    return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
});

const fetchDashboard = async () => {
    try {
        errorMessage.value = '';
        const data = await adminApi.getDashboard();
        bookings.value = data.data.today_bookings;
        totalBookings.value = data.data.total_bookings;
        currentDate.value = data.data.date;
    } catch {
        errorMessage.value = 'Gagal memuat data. Coba refresh manual.';
    } finally {
        isLoading.value = false;
        requestAnimationFrame(() => { isReady.value = true; });
    }
};

const handleRefresh = async () => {
    isLoading.value = true;
    isReady.value = false;
    await fetchDashboard();
};

const promptCancel = (booking: Booking) => {
    cancelTarget.value = booking;
    showCancelModal.value = true;
};

const handleCancel = async () => {
    if (!cancelTarget.value) return;
    showCancelModal.value = false;
    cancellingId.value = cancelTarget.value.id;
    errorMessage.value = '';
    try {
        await adminApi.cancelBooking(cancelTarget.value.id);
        bookings.value = bookings.value.filter((b) => b.id !== cancelTarget.value!.id);
        totalBookings.value = bookings.value.length;
    } catch (e: any) {
        const msg = e.response?.data?.message || e.message;
        errorMessage.value = `Gagal membatalkan booking: ${msg}`;
    } finally {
        cancellingId.value = null;
        cancelTarget.value = null;
    }
};

onMounted(() => {
    fetchDashboard();
    refreshTimer = setInterval(fetchDashboard, 30_000);
});

onUnmounted(() => {
    if (refreshTimer) clearInterval(refreshTimer);
});
</script>

<template>
    <div>
        <!-- Confirm Modal -->
        <ConfirmModal
            :show="showCancelModal"
            title="Batalkan Booking?"
            :message="`Yakin ingin membatalkan booking ${cancelTarget?.customer_name} jam ${cancelTarget?.time_slot}? Tindakan ini tidak dapat dibatalkan.`"
            confirm-label="Ya, Batalkan"
            variant="danger"
            @confirm="handleCancel"
            @cancel="showCancelModal = false"
        />

        <!-- Page heading + refresh -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="font-headline-lg text-gray-900">Dashboard</h1>
                <p class="text-gray-400 text-sm mt-1">Ringkasan booking hari ini.</p>
            </div>
            <button
                @click="handleRefresh"
                :disabled="isLoading"
                class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-bke-primary hover:bg-indigo-50 transition-all duration-200 font-label-md disabled:opacity-50"
                title="Refresh data"
            >
                <span
                    class="material-symbols-outlined text-[18px]"
                    :class="{ 'animate-spin': isLoading }"
                    aria-hidden="true"
                >refresh</span>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>

        <!-- Error state -->
        <Transition name="toast">
            <div
                v-if="errorMessage"
                class="mb-6 bg-red-50 text-red-600 p-4 rounded-xl flex items-center gap-2 border border-red-100"
            >
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">error</span>
                <span class="font-caption">{{ errorMessage }}</span>
            </div>
        </Transition>

        <!-- Main card -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">

            <!-- Card header -->
            <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap justify-between items-center gap-3">
                <h2 class="font-headline-sm text-gray-900">
                    Booking Hari Ini
                    <span v-if="formattedDate" class="text-gray-400 font-body-md">
                        — {{ formattedDate }}
                    </span>
                </h2>
            </div>

            <!-- Loading skeleton -->
            <div v-if="isLoading" class="p-6 space-y-3">
                <div v-for="i in 4" :key="i" class="h-14 bg-gray-50 rounded-xl animate-pulse" />
            </div>

            <!-- Empty state -->
            <div v-else-if="bookings.length === 0" class="py-16 text-center text-gray-300">
                <span class="material-symbols-outlined text-[48px] block mb-3 opacity-30">
                    event_available
                </span>
                <p class="font-body-md">Belum ada booking hari ini. Santai dulu!</p>
            </div>

            <!-- Tabel booking -->
            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse" aria-label="Daftar booking hari ini">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 text-gray-400 font-label-md">
                            <th class="py-3 px-4 w-16 text-center" scope="col">No</th>
                            <th class="py-3 px-4 w-32" scope="col">Jam Sesi</th>
                            <th class="py-3 px-4" scope="col">Nama Pelanggan</th>
                            <th class="py-3 px-4 w-48" scope="col">No WhatsApp</th>
                            <th class="py-3 px-4 w-40 text-center" scope="col">Status</th>
                            <th class="py-3 px-4 w-16 text-center" scope="col">
                                <span class="sr-only">Batalkan</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="font-body-md text-gray-700 divide-y divide-gray-50">
                        <tr
                            v-for="(booking, index) in bookings"
                            :key="booking.id"
                            class="hover:bg-indigo-50/50 transition-colors duration-150 group"
                            :class="isReady ? 'anim-fade-in-up' : 'opacity-0'"
                            :style="{ animationDelay: `${index * 50}ms` }"
                        >
                            <td class="py-3 px-4 text-center font-label-md text-gray-300">{{ index + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-medium text-gray-800">{{ booking.time_slot }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-900">{{ booking.customer_name }}</td>
                            <td class="py-3 px-4 text-gray-400">{{ booking.customer_phone }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-600 text-xs font-semibold border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500" aria-hidden="true"></span>
                                    Terkonfirmasi
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button
                                    @click="promptCancel(booking)"
                                    :disabled="cancellingId === booking.id"
                                    class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all duration-200 md:opacity-0 md:group-hover:opacity-100 focus:opacity-100 rounded-lg disabled:opacity-30"
                                    :aria-label="`Batalkan booking ${booking.customer_name} jam ${booking.time_slot}`"
                                >
                                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">
                                        {{ cancellingId === booking.id ? 'progress_activity' : 'cancel' }}
                                    </span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer tabel -->
            <div
                v-if="!isLoading && bookings.length > 0"
                class="px-4 py-3 border-t border-gray-100 flex justify-between items-center text-gray-300 font-caption bg-gray-50/50"
            >
                <span>Menampilkan {{ totalBookings }} booking</span>
            </div>

        </div>
    </div>
</template>

<style scoped>
.font-mono {
    font-family: var(--font-mono);
}

.toast-enter-active {
    animation: slide-down 0.3s ease-out;
}
.toast-leave-active {
    animation: slide-down 0.2s ease-in reverse;
}
</style>
