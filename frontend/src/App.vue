<script setup lang="ts">
import { ref } from 'vue';
import ScheduleGrid from './components/ScheduleGrid.vue';
import BookingForm from './components/BookingForm.vue';
import { bookingApi } from './services/bookingApi';

// meyimpan jadwal yang akan dilempar dari grid ke from
const globalSelectedSlot = ref<string | null>(null)
const isSubmitting = ref(false)

const handleBookingSubmit = async (formData: { customer_name: string, customer_phone: string}) => {
  if (!globalSelectedSlot.value) return
  
  isSubmitting.value = true
  try {
    const payload = {
      ...formData,
      time_slot: globalSelectedSlot.value,
    // gunakan tanggal hari ini
    booking_date: new Date().toISOString().split('T')[0]
    }

    // tembak data ke backend
    await bookingApi.createBooking(payload)

    // sementara pakai alert untuk tes sebelum buat toast notifikasi
    alert('Booking berhasil dikirim ke database!')
  } catch (error) {
    console.error('terjadi kesalahan:', error)
    alert('Gagal menyimpan booking, SIlakan coba lagi.')
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

    <!-- Tangkap event klik dari grid -->
     <ScheduleGrid @slot-selected="(slot) => globalSelectedSlot= slot"/>

      <!-- Lempar jadwal terpilih ke dalam form -->
       <BookingForm 
       :selected-slot="globalSelectedSlot"
       :is-submitting="isSubmitting"
       @submit="handleBookingSubmit"/>
  </div>
</template>