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
        class="slot-btn min-h-[48px] min-w-[48px] py-3 px-4 rounded-xl text-sm font-semibold border-2 text-center flex items-center justify-center gap-1.5 font-mono tracking-wide"
        :class="[
            status === 'booked'
                ? 'bg-gray-50 text-gray-300 border-gray-100 line-through cursor-not-allowed'
                : isSelected
                    ? 'bg-bke-accent text-white border-bke-accent shadow-md'
                    : 'bg-white border-gray-200 text-gray-800 cursor-pointer'
        ]"
    >
        <span>{{ time }}</span>

        <!-- Check icon saat terpilih -->
        <svg
            v-if="isSelected"
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 anim-scale-in"
            viewBox="0 0 20 20"
            fill="currentColor"
        >
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
    </button>
</template>

<style scoped>
.font-mono {
    font-family: var(--font-mono);
}
</style>
