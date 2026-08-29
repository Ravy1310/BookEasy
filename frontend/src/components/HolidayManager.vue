<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { adminApi } from '../services/adminApi';
import ConfirmModal from './ConfirmModal.vue';

interface Holiday {
    id?: number;
    date: string;
    reason: string | null;
}

const holidays = ref<Holiday[]>([]);
const isLoading = ref(true);
const isReady = ref(false);
const isAdding = ref(false);
const deletingDate = ref<string | null>(null);
const successMessage = ref('');
const errorMessage = ref('');

const newDate = ref('');
const newReason = ref('');

// Modal state
const showDeleteModal = ref(false);
const deleteTarget = ref<Holiday | null>(null);

const formatDate = (dateStr: string) => {
    const datePart = dateStr.split(' ')[0].split('T')[0];
    const [year, month, day] = datePart.split('-').map(Number);
    const date = new Date(year, month - 1, day);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const fetchHolidays = async () => {
    isLoading.value = true;
    try {
        const res = await adminApi.getHolidays();
        holidays.value = res.data ?? [];
    } catch {
        errorMessage.value = 'Gagal memuat daftar hari libur.';
    } finally {
        isLoading.value = false;
        requestAnimationFrame(() => { isReady.value = true; });
    }
};

const handleAdd = async () => {
    if (!newDate.value) {
        errorMessage.value = 'Tanggal libur wajib diisi.';
        return;
    }
    isAdding.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    try {
        await adminApi.addHoliday(newDate.value, newReason.value);
        successMessage.value = `Hari libur ${formatDate(newDate.value)} berhasil ditambahkan.`;
        newDate.value = '';
        newReason.value = '';
        await fetchHolidays();
        setTimeout(() => (successMessage.value = ''), 4000);
    } catch (err: any) {
        const msg = err.response?.data?.message || err.message;
        errorMessage.value = `Gagal menambahkan hari libur: ${msg}`;
    } finally {
        isAdding.value = false;
    }
};

const promptDelete = (holiday: Holiday) => {
    deleteTarget.value = holiday;
    showDeleteModal.value = true;
};

const handleDelete = async () => {
    if (!deleteTarget.value) return;
    showDeleteModal.value = false;

    const date = deleteTarget.value.date;
    deletingDate.value = date;
    successMessage.value = '';
    errorMessage.value = '';

    try {
        await adminApi.removeHoliday(date);
        successMessage.value = `Hari libur ${formatDate(date)} berhasil dihapus.`;
        holidays.value = holidays.value.filter((h) => h.date !== date);
        setTimeout(() => (successMessage.value = ''), 4000);
    } catch (e: any) {
        const msg = e.response?.data?.message || e.message;
        errorMessage.value = `Gagal menghapus hari libur: ${msg}`;
    } finally {
        deletingDate.value = null;
        deleteTarget.value = null;
    }
};

onMounted(fetchHolidays);
</script>

<template>
    <div>
        <!-- Confirm Modal -->
        <ConfirmModal
            :show="showDeleteModal"
            title="Hapus Hari Libur?"
            :message="`Yakin ingin menghapus hari libur tanggal ${deleteTarget ? formatDate(deleteTarget.date) : ''}? Tindakan ini tidak dapat dibatalkan.`"
            confirm-label="Ya, Hapus"
            variant="danger"
            @confirm="handleDelete"
            @cancel="showDeleteModal = false"
        />

        <!-- Page heading -->
        <div class="mb-6">
            <h1 class="font-headline-lg text-gray-900">Hari Libur & Cuti</h1>
            <p class="text-gray-400 text-sm mt-1">Tandai tanggal yang tidak beroperasi.</p>
        </div>

        <!-- Toast sukses -->
        <Transition name="toast">
            <div
                v-if="successMessage"
                class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl flex items-center gap-2 border border-green-100"
            >
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">check_circle</span>
                <span class="font-caption">{{ successMessage }}</span>
            </div>
        </Transition>

        <!-- Toast error -->
        <Transition name="toast">
            <div
                v-if="errorMessage"
                class="mb-6 bg-red-50 text-red-600 p-4 rounded-xl flex items-center gap-2 border border-red-100"
            >
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">error</span>
                <span class="font-caption">{{ errorMessage }}</span>
            </div>
        </Transition>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- Card header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-400">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">event_busy</span>
                </div>
                <h2 class="font-headline-sm text-gray-900">Manajemen Hari Libur & Cuti</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">

                <!-- KIRI: Form tambah libur -->
                <div class="p-6">
                    <h3 class="font-label-md text-gray-600 mb-4">Tambah Hari Libur</h3>
                    <form @submit.prevent="handleAdd" class="space-y-4">

                        <div>
                            <label for="holiday-date" class="block text-sm font-medium text-gray-600 mb-1.5">
                                Tanggal Libur <span class="text-red-400">*</span>
                            </label>
                            <input
                                id="holiday-date"
                                v-model="newDate"
                                type="date"
                                required
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-bke-primary focus:border-bke-primary transition-all duration-200"
                            />
                        </div>

                        <div>
                            <label for="holiday-reason" class="block text-sm font-medium text-gray-600 mb-1.5">
                                Alasan Libur <span class="text-gray-300 font-normal">(opsional)</span>
                            </label>
                            <input
                                id="holiday-reason"
                                v-model="newReason"
                                type="text"
                                placeholder="Misal: Libur Nasional, Renovasi"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-bke-primary focus:border-bke-primary transition-all duration-200"
                            />
                        </div>

                        <button
                            type="submit"
                            :disabled="isAdding"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-bke-primary hover:bg-bke-primary-hover disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl font-label-md transition-all duration-200 shadow-sm hover:shadow-md"
                        >
                            <span
                                class="material-symbols-outlined text-[18px]"
                                :class="{ 'animate-spin': isAdding }"
                                aria-hidden="true"
                            >{{ isAdding ? 'progress_activity' : 'add' }}</span>
                            {{ isAdding ? 'Menambahkan...' : 'Tambah Libur' }}
                        </button>

                    </form>
                </div>

                <!-- KANAN: Daftar libur -->
                <div class="p-6">
                    <h3 class="font-label-md text-gray-600 mb-4">Daftar Libur Aktif</h3>

                    <!-- Loading skeleton -->
                    <div v-if="isLoading" class="space-y-3">
                        <div v-for="i in 3" :key="i" class="h-12 bg-gray-50 rounded-xl animate-pulse" />
                    </div>

                    <!-- Empty state -->
                    <div
                        v-else-if="holidays.length === 0"
                        class="py-12 text-center text-gray-300"
                    >
                        <span class="material-symbols-outlined text-[40px] block mb-2 opacity-40">
                            event_available
                        </span>
                        <p class="text-sm">Tidak ada hari libur yang ditandai.</p>
                    </div>

                    <!-- Tabel daftar libur -->
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" aria-label="Daftar hari libur">
                            <thead>
                                <tr class="border-b border-gray-100 text-gray-400 text-sm">
                                    <th class="py-2 px-2 font-label-md" scope="col">Tanggal</th>
                                    <th class="py-2 px-2 font-label-md" scope="col">Alasan</th>
                                    <th class="py-2 px-2 font-label-md text-center" scope="col">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr
                                    v-for="(holiday, index) in holidays"
                                    :key="holiday.date"
                                    class="hover:bg-gray-50 transition-colors duration-150"
                                    :class="isReady ? 'anim-fade-in-up' : 'opacity-0'"
                                    :style="{ animationDelay: `${index * 50}ms` }"
                                >
                                    <td class="py-3 px-2 text-sm font-medium text-gray-800">
                                        {{ formatDate(holiday.date) }}
                                    </td>
                                    <td class="py-3 px-2 text-sm text-gray-400">
                                        {{ holiday.reason || '—' }}
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <button
                                            @click="promptDelete(holiday)"
                                            :disabled="deletingDate === holiday.date"
                                            class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200 disabled:opacity-30"
                                            :aria-label="`Hapus libur tanggal ${formatDate(holiday.date)}`"
                                        >
                                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">
                                                {{ deletingDate === holiday.date ? 'progress_activity' : 'delete' }}
                                            </span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.toast-enter-active {
    animation: slide-down 0.3s ease-out;
}
.toast-leave-active {
    animation: slide-down 0.2s ease-in reverse;
}
</style>
