<script setup lang="ts">
defineProps<{
time: string
status: 'available' | 'booked'
isSelected?: boolean
}>()

defineEmits(['select'])
</script>

<template>
    <button
    @click="status === 'available' && $emit('select', time)"
    :disabled="status === 'booked'"
    class="min-h-[44px] min-w-[44px] py-2.5 px-4 rounded-md text-sm font-semibold border text-center transtion-all duration-150 flex items-center justify-center gap-1"
    :class="[
        status === 'booked'
        ? 'bg-gray-100 text-gray-400 border-gray-100 line-through opacity-70 cursor-not-allowed'
        : isSelected
        ? 'bg-indigo-600 text-white border-indigo-600'
        : 'bg-white border-gray-300 text-gray-900 hover:border-indigo-600 hover:indigo-50 active:scale-95'
    ]"
    >
        {{ time }}

        <!-- tampilkan ikon cnetang SVG kalau slot ini sedang dipilih -->
         <svg v-if="isSelected" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
    </svg>
    </button>
</template>