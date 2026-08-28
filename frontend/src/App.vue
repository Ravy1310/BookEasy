<script setup lang="ts">
import { ref } from 'vue';
import ScheduleGrid from './components/ScheduleGrid.vue';
import BookingForm from './components/BookingForm.vue';
import { bookingApi } from './services/bookingApi';

// meyimpan jadwal yang akan dilempar dari grid ke from
const globalSelectedSlot = ref<string | null>(null)
const isSubmitting = ref(false)
const errorMessage = ref<string | null>(null)
const successMessage = ref<string | null>(null)
const gridKey = ref(0)

const handleBookingSubmit = async (formData: { customer_name: string, customer_phone: string}) => {
  if (!globalSelectedSlot.value) return
  
  isSubmitting.value = true
  errorMessage.value = null
  successMessage.value = null

  try {
    const payload = {
      ...formData,
      time_slot: globalSelectedSlot.value,
    // gunakan tanggal hari ini
    booking_date: new Date().toISOString().split('T')[0]
    }

    // tembak data ke backend
   await bookingApi.createBooking(payload)

    successMessage.value = `Booking berhasil! Sampai jumpa jam ${globalSelectedSlot.value} ya`
    gridKey.value += 1

    globalSelectedSlot.value = null

    setTimeout(() => {
      successMessage.value = null
    }, 4000);
    
  } catch (error: any) {
    // Tangkap kode 422 dari backend (validasi gagal/slot sudah dipesan)
    if (error.response && error.response.status === 422) {
      const backendMessage = error.response.data.errors 
      // tarik pesan spesifik dari field time_slot
      errorMessage.value = backendMessage.time_slot?.[0]
      || error.response.data.message
      || 'Validasi gagal. Silakan periksa data Anda kembali.'
    } else {
      errorMessage.value = 'Gagal terhubung ke server. Silakan coba beberapa saat lagi.'
    }
  } finally {
    isSubmitting.value = false
  }
}

</script>

<template>
  <div class="min-h-screen bg-gray-50 p-4 sm:-8 font-sans">
    <div class="max-w-3xl mx-auto mb-6">
      <h1 class="text-2xl font-bold text-indigo-600">BookEasy</h1>
      <p class="text-gray-500 text-sm mt-1">Pilih waktu luang untuk layanan Anda.</p>
    </div>

    <!-- Kotak Notifikasi Error -->
     <div
     v-if="errorMessage"
     class="max-w-3xl mx-auto mb-6 bg-red-50 text-red-600 p-4 rounded-lg flex items-center gap-3 shadow-sm border border-red-100 transition-all"
     >
     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <span class="text-sm font-medium">{{ errorMessage }}</span>
     </div>

    <!-- kotak notifikasi sukses -->
     <div 
     v-if="successMessage"
     class="max-w-3xl mx-auto mb-6 bg-green-50 text-green-700 p-4 rounded-lg-flex items-center gap-3 shadow-sm border border-green-100 transition-all"
     >
     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
      <span class="text-sm font-medium">{{ successMessage }}</span>
    </div>

    <!-- Tangkap event klik dari grid -->
     <ScheduleGrid 
     :key="gridKey"
     @slot-selected="(slot) => globalSelectedSlot= slot"/>

      <!-- Lempar jadwal terpilih ke dalam form -->
       <BookingForm 
       v-if="!successMessage"
       :selected-slot="globalSelectedSlot"
       :is-submitting="isSubmitting"
       @submit="handleBookingSubmit"/>
  </div>
</template>
