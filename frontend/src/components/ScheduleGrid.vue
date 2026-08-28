<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { bookingApi } from '../services/bookingApi';
import SlotButton from './SlotButton.vue';



const slots = ref<any[]>([]);
const isLoading = ref(true);
const errorMsg = ref ('')
const selectedSlot = ref<string | null>(null) //menyimpan jadwal yang sedang diklick user
const emit = defineEmits(['slotSelected'])



// Tarik data ketersediaan pas komponen pertama kali muncul di layar
onMounted(async () => {
    try {
        const response = await bookingApi.getBookings()
        const now = new Date();
        const currentHour = now.getHours().toString().padStart(2, '0');
        const currentMinute = now.getMinutes().toString().padStart(2, '0');
        const currentTime = `${currentHour}:${currentMinute}`
        slots.value = response.data

        slots.value = response.data.map((slot: any) => {
    if(slot.time_slot < currentTime) {
        return {
            ...slot,
            status: 'booked'
        }
    }

    return slot
})

    } catch (err) {
        errorMsg.value = 'Gagal memuat jadwal. Silahkan refresh browser'
    }finally {
        isLoading.value = false
    }
})


</script>

<template>
    <div class="max-w-3xl mx-auto mt-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Hari Ini</h2>

        <!-- State pas lagi loading data dari server, pakai skeleton agar modern -->
         <div v-if="isLoading" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <div v-for="n in 8" :key="n" class="h-11 bg-gray-200 animate-pulse rounded-md"></div>
         </div>

         <!-- Kalau gagal fetch API -->
          <div v-else-if="errorMsg" class="p-4 bg-red-50 text-red-600 rounded-lg text-sm text-center">
            {{ errorMsg }}
          </div>

          <!-- Kalau Jadwal bener benar penuh (empty state) -->
           <div v-else-if="slots.length === 0" class=" text-center p-8 bg-gray-50 rounded-lg text-gray-600 text-sm">
                Semua jadwal hari ini penuh. Silahkan cek kembali besok
           </div>

           <!-- Panggil komponen SlotButton -->
            <div v-else class="grid grid-cols-2 sm-grid-cols-3 md:grid-cols-4 gap-4">
                <SlotButton
                v-for="slot in slots"
                :key="slot.time_slot"
                :time="slot.time_slot"
                :status="slot.status"
                :is-selected="selectedSlot === slot.time_slot"
                @select="selectedSlot = $event; emit('slotSelected', $event)"
                />

            </div>
    </div>
</template>