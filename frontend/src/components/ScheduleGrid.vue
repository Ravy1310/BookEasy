<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { bookingApi } from '../services/bookingApi';
import SlotButton from './SlotButton.vue';

const slots = ref<any[]>([]);
const isLoading = ref(true);
const isReady = ref(false);
const errorMsg = ref('');
const selectedSlot = ref<string | null>(null);
const emit = defineEmits(['slotSelected']);

onMounted(async () => {
    try {
        const response = await bookingApi.getBookings();
        const now = new Date();
        const currentHour = now.getHours().toString().padStart(2, '0');
        const currentMinute = now.getMinutes().toString().padStart(2, '0');
        const currentTime = `${currentHour}:${currentMinute}`;
        slots.value = response.data;

        slots.value = response.data.map((slot: any) => {
            if (slot.time_slot < currentTime) {
                return { ...slot, status: 'booked' };
            }
            return slot;
        });
    } catch (err) {
        errorMsg.value = 'Gagal memuat jadwal. Silahkan refresh browser';
    } finally {
        isLoading.value = false;
        requestAnimationFrame(() => { isReady.value = true; });
    }
});
</script>

<template>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h2 class="font-headline-sm text-gray-900 mb-5">Jadwal Hari Ini</h2>

        <!-- Loading skeleton -->
        <div v-if="isLoading" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <div v-for="n in 8" :key="n" class="h-12 bg-gray-100 rounded-xl animate-pulse"></div>
        </div>

        <!-- Error state -->
        <div v-else-if="errorMsg" class="p-4 bg-red-50 text-red-600 rounded-xl text-sm text-center">
            {{ errorMsg }}
        </div>

        <!-- Empty state -->
        <div v-else-if="slots.length === 0" class="text-center py-12 bg-gray-50 rounded-xl text-gray-500">
            <span class="material-symbols-outlined text-[40px] block mb-2 opacity-30">event_busy</span>
            <p class="text-sm">Semua jadwal hari ini penuh. Cek kembali besok ya.</p>
        </div>

        <!-- Slot grid with staggered reveal -->
        <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <div
                v-for="(slot, index) in slots"
                :key="slot.time_slot"
                :class="isReady ? 'anim-fade-in-up' : 'opacity-0'"
                :style="{ animationDelay: `${index * 60}ms` }"
            >
                <SlotButton
                    :time="slot.time_slot"
                    :status="slot.status"
                    :is-selected="selectedSlot === slot.time_slot"
                    @select="selectedSlot = $event; emit('slotSelected', $event)"
                />
            </div>
        </div>
    </div>
</template>
