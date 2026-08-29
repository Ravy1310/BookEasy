<script setup lang="ts">
defineProps<{
    show: boolean;
    title: string;
    message: string;
    confirmLabel?: string;
    cancelLabel?: string;
    variant?: 'danger' | 'warning' | 'primary';
}>();

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="show"
                class="fixed inset-0 z-[999] flex items-center justify-center p-4"
                @click.self="emit('cancel')"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" />

                <!-- Modal -->
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 anim-scale-in">
                    <!-- Icon -->
                    <div class="flex justify-center mb-4">
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center"
                            :class="{
                                'bg-red-50 text-red-500': variant === 'danger',
                                'bg-amber-50 text-amber-500': variant === 'warning',
                                'bg-indigo-50 text-indigo-500': variant === 'primary',
                            }"
                        >
                            <span class="material-symbols-outlined text-[28px]" aria-hidden="true">
                                {{ variant === 'danger' ? 'delete' : variant === 'warning' ? 'warning' : 'info' }}
                            </span>
                        </div>
                    </div>

                    <!-- Title -->
                    <h3 class="text-center font-headline-sm text-gray-900 mb-2">{{ title }}</h3>

                    <!-- Message -->
                    <p class="text-center text-sm text-gray-400 mb-6 leading-relaxed">{{ message }}</p>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button
                            @click="emit('cancel')"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 font-label-md transition-all duration-200"
                        >
                            {{ cancelLabel || 'Batal' }}
                        </button>
                        <button
                            @click="emit('confirm')"
                            class="flex-1 px-4 py-2.5 rounded-xl text-white font-label-md transition-all duration-200 shadow-sm hover:shadow-md"
                            :class="{
                                'bg-red-500 hover:bg-red-600': variant === 'danger',
                                'bg-amber-500 hover:bg-amber-600': variant === 'warning',
                                'bg-bke-primary hover:bg-bke-primary-hover': variant === 'primary',
                            }"
                        >
                            {{ confirmLabel || 'Ya, Lanjutkan' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-enter-active {
    animation: fade-in 0.2s ease-out;
}
.modal-leave-active {
    animation: fade-in 0.15s ease-in reverse;
}
.modal-enter-active .relative {
    animation: scale-in 0.2s ease-out;
}
.modal-leave-active .relative {
    animation: scale-in 0.15s ease-in reverse;
}
</style>
