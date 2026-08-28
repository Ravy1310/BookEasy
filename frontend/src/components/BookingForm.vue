<script setup lang="ts">
import { computed, ref } from 'vue'

const props =defineProps<{
    selectedSlot: string | null
    isSubmitting: boolean
}>()

const emit = defineEmits(['submit'])

const customerName = ref('')
const customerPhone = ref('')

// form dinyatakan valid JIka nama, nomor HP diisi, dan slot sudah dipilih
const isFormValid = computed(() => {
    return customerName.value.trim() !== ''&&
            customerPhone.value.trim() !== ''&&
            !!props.selectedSlot !== null
})

const handleSubmit = () => {
    // mencegah submit jika entak bagiamana form disubmit paksa
    if (!isFormValid.value || props.isSubmitting) return
    
    emit('submit', {
        customer_name: customerName.value,
        customer_phone: customerPhone.value,
    })
}
</script>

<template>
    <div class="mt-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 max-w-3xl mx-auto">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Lengkapi Data Anda</h2>

        <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Lencana slot terpilih -->
             <div class="bg-indigo-50 text-indigo-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
        </svg>
            <span v-if="selectedSlot">Jam Terpilih: <strong class="font-bold">{{ selectedSlot }}</strong></span>
            <span v-else>Belum ada jadwal yang terpilih, silakan klik di atas.</span>
             </div>

             <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                    <input 
                    id="name"
                    v-model="customerName"
                    type="text"
                    placeholder="Masukkan nama Anda"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none"
                    >
                </div>
                <div>
          <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
          <input 
            id="phone" 
            v-model="customerPhone" 
            type="tel" 
            placeholder="Contoh: 08123456789" 
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none"
          >
        </div>
             </div>

             <button
             type="submit"
             :disabled="!isFormValid || isSubmitting"
             class="mt-4 w-full font-semibold py-2.5 rounded-md trasition-colors"
             :class="isFormValid && !isSubmitting
             ? 'bg-indigo-600 text-white hover:bg-indigo-700'
             : 'bg-gray-300 text-gray-600 cursor-not-allowed'"
             >
             {{ isSubmitting ? 'Memproses...':'Pesan Sekarang' }}
             </button>
        </form>
    </div>
</template>