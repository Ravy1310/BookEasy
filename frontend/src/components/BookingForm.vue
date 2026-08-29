<script setup lang="ts">
import { computed, ref } from 'vue'

const props = defineProps<{
    selectedSlot: string | null
    isSubmitting: boolean
}>()

const emit = defineEmits(['submit'])

const customerName = ref('')
const customerPhone = ref('')

const isFormValid = computed(() => {
    return customerName.value.trim() !== '' &&
        customerPhone.value.trim() !== '' &&
        !!props.selectedSlot
})

const handleSubmit = () => {
    if (!isFormValid.value || props.isSubmitting) return

    emit('submit', {
        customer_name: customerName.value,
        customer_phone: customerPhone.value,
    })
}
</script>

<template>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <h2 class="font-headline-sm text-gray-900 mb-5">Lengkapi Data Anda</h2>

        <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Slot terpilih badge -->
            <div
                class="rounded-xl text-sm flex items-center gap-2.5 px-4 py-3 transition-all duration-300"
                :class="selectedSlot
                    ? 'bg-amber-50 text-amber-700 border border-amber-200'
                    : 'bg-gray-50 text-gray-400 border border-gray-100'"
            >
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">
                    {{ selectedSlot ? 'schedule' : 'touch_app' }}
                </span>
                <span v-if="selectedSlot" class="font-semibold font-mono">{{ selectedSlot }}</span>
                <span v-else>Klik jam di atas untuk memilih</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pelanggan</label>
                    <input
                        id="name"
                        v-model="customerName"
                        type="text"
                        placeholder="Masukkan nama Anda"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-bke-primary focus:border-bke-primary text-sm outline-none transition-all duration-200"
                    >
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp</label>
                    <input
                        id="phone"
                        v-model="customerPhone"
                        type="tel"
                        placeholder="Contoh: 08123456789"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-bke-primary focus:border-bke-primary text-sm outline-none transition-all duration-200"
                    >
                </div>
            </div>

            <button
                type="submit"
                :disabled="!isFormValid || isSubmitting"
                class="mt-2 w-full font-semibold py-3 rounded-xl transition-all duration-200 text-sm tracking-wide"
                :class="isFormValid && !isSubmitting
                    ? 'bg-bke-primary text-white hover:bg-bke-primary-hover shadow-md hover:shadow-lg'
                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
            >
                <span v-if="isSubmitting" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Memproses...
                </span>
                <span v-else>Pesan Sekarang</span>
            </button>
        </form>
    </div>
</template>

<style scoped>
.font-headline-sm {
    font-family: var(--font-display);
}
.font-mono {
    font-family: var(--font-mono);
}
</style>
