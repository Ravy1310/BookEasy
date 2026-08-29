<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { adminApi } from '../services/adminApi';

interface Schedule {
    id?: number;
    day_of_week: number;
    start_time: string;
    end_time: string;
    is_closed: boolean;
}

const DAY_NAMES = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

const schedules = ref<Schedule[]>([]);
const isLoading = ref(true);
const isReady = ref(false);
const isSaving = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const defaultSchedules = (): Schedule[] =>
    DAY_NAMES.map((_, i) => ({
        day_of_week: i,
        start_time: '09:00',
        end_time: '17:00',
        is_closed: i >= 5,
    }));

// Pastikan time selalu format HH:MM (zero-padded 24-hour)
const normalizeTime = (t: string): string => {
    if (!t) return '00:00';
    const parts = t.split(':');
    const h = parts[0].padStart(2, '0');
    const m = (parts[1] || '00').padStart(2, '0');
    return `${h}:${m}`;
};

const fetchSchedules = async () => {
    isLoading.value = true;
    try {
        const res = await adminApi.getSchedules();
        if (res.data && res.data.length > 0) {
            const map = Object.fromEntries(res.data.map((s: Schedule) => [s.day_of_week, s]));
            schedules.value = defaultSchedules().map((def) => map[def.day_of_week] ?? def);
        } else {
            schedules.value = defaultSchedules();
        }
    } catch {
        schedules.value = defaultSchedules();
        errorMessage.value = 'Gagal memuat jadwal. Menampilkan data default.';
    } finally {
        isLoading.value = false;
        requestAnimationFrame(() => { isReady.value = true; });
    }
};

const handleSave = async () => {
    isSaving.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    for (const s of schedules.value) {
        if (!s.is_closed && s.start_time >= s.end_time) {
            errorMessage.value = `${DAY_NAMES[s.day_of_week]}: jam tutup harus lebih dari jam buka.`;
            isSaving.value = false;
            return;
        }
    }

    try {
        const payload = schedules.value.map((s) => ({
            ...s,
            start_time: s.is_closed ? null : normalizeTime(s.start_time),
            end_time: s.is_closed ? null : normalizeTime(s.end_time),
        }));
        await adminApi.updateSchedules(payload);
        successMessage.value = 'Jadwal berhasil diperbarui!';
        setTimeout(() => (successMessage.value = ''), 4000);
    } catch (e: any) {
        const msg = e.response?.data?.message || e.response?.data?.errors?.schedules?.[0] || e.message;
        errorMessage.value = `Gagal menyimpan jadwal: ${msg}`;
    } finally {
        isSaving.value = false;
    }
};

const toggleClosed = (index: number) => {
    schedules.value[index].is_closed = !schedules.value[index].is_closed;
};

onMounted(fetchSchedules);
</script>

<template>
    <div>
        <!-- Page heading -->
        <div class="mb-6">
            <h1 class="font-headline-lg text-gray-900">Jadwal Operasional</h1>
            <p class="text-gray-400 text-sm mt-1">Atur jam buka dan tutup untuk setiap hari.</p>
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

        <!-- Card jadwal -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- Card header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-bke-primary">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">calendar_month</span>
                </div>
                <h2 class="font-headline-sm text-gray-900">Jadwal Operasional Mingguan</h2>
            </div>

            <!-- Loading skeleton -->
            <div v-if="isLoading" class="p-6 space-y-3">
                <div v-for="i in 7" :key="i" class="h-14 bg-gray-50 rounded-xl animate-pulse" />
            </div>

            <!-- Tabel jadwal -->
            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 text-sm">
                            <th class="py-3 px-4 font-label-md" scope="col">Hari</th>
                            <th class="py-3 px-4 font-label-md" scope="col">Jam Buka</th>
                            <th class="py-3 px-4 font-label-md" scope="col">Jam Tutup</th>
                            <th class="py-3 px-4 font-label-md text-right" scope="col">Tutup Hari Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr
                            v-for="(schedule, index) in schedules"
                            :key="schedule.day_of_week"
                            class="hover:bg-gray-50 transition-colors duration-150"
                            :class="[
                                { 'opacity-50': schedule.is_closed },
                                isReady ? 'anim-fade-in-up' : 'opacity-0'
                            ]"
                            :style="{ animationDelay: `${index * 50}ms` }"
                        >
                            <td class="py-3 px-4 font-label-md text-gray-800">
                                {{ DAY_NAMES[schedule.day_of_week] }}
                            </td>

                            <td class="py-3 px-4">
                                <input
                                    v-model="schedule.start_time"
                                    type="time"
                                    :disabled="schedule.is_closed"
                                    class="w-32 rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-bke-primary focus:border-bke-primary disabled:bg-gray-50 disabled:text-gray-300 disabled:cursor-not-allowed transition-all duration-200"
                                    :aria-label="`Jam buka ${DAY_NAMES[schedule.day_of_week]}`"
                                />
                            </td>

                            <td class="py-3 px-4">
                                <input
                                    v-model="schedule.end_time"
                                    type="time"
                                    :disabled="schedule.is_closed"
                                    class="w-32 rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-bke-primary focus:border-bke-primary disabled:bg-gray-50 disabled:text-gray-300 disabled:cursor-not-allowed transition-all duration-200"
                                    :aria-label="`Jam tutup ${DAY_NAMES[schedule.day_of_week]}`"
                                />
                            </td>

                            <td class="py-3 px-4 text-right">
                                <button
                                    type="button"
                                    role="switch"
                                    :aria-checked="schedule.is_closed"
                                    :aria-label="`Tutup ${DAY_NAMES[schedule.day_of_week]}`"
                                    @click="toggleClosed(index)"
                                    class="toggle-track relative inline-flex h-6 w-11 items-center rounded-full focus:outline-none focus:ring-2 focus:ring-bke-primary focus:ring-offset-2"
                                    :class="schedule.is_closed ? 'bg-bke-primary' : 'bg-gray-200'"
                                >
                                    <span
                                        class="toggle-ball inline-block h-4 w-4 transform rounded-full bg-white shadow-sm"
                                        :class="schedule.is_closed ? 'translate-x-6' : 'translate-x-1'"
                                    />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer — tombol simpan -->
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end bg-gray-50/50">
                <button
                    @click="handleSave"
                    :disabled="isSaving || isLoading"
                    class="flex items-center gap-2 px-5 py-2.5 bg-bke-primary hover:bg-bke-primary-hover disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl font-label-md transition-all duration-200 shadow-sm hover:shadow-md"
                >
                    <span
                        class="material-symbols-outlined text-[18px]"
                        :class="{ 'animate-spin': isSaving }"
                        aria-hidden="true"
                    >{{ isSaving ? 'progress_activity' : 'save' }}</span>
                    {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
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
