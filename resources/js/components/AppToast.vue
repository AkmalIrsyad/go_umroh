<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 max-w-xs">
            <TransitionGroup name="toast" tag="div" class="flex flex-col gap-2">
                <div v-for="toast in toasts" :key="toast.id"
                    :class="['px-4 py-3 rounded-xl shadow-lg text-sm font-medium flex items-start gap-2 border',
                        toast.type === 'success' ? 'bg-sage-50 border-sage-100 text-sage' :
                        toast.type === 'error' ? 'bg-red-50 border-red-100 text-red-700' :
                        'bg-limestone border-limestone-100 text-ink']">
                    <span class="shrink-0">{{ toast.type === 'success' ? '✅' : toast.type === 'error' ? '❌' : 'ℹ️' }}</span>
                    <span>{{ toast.message }}</span>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup>
import { useToast } from '../composables/useToast';
const { toasts } = useToast();
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from { opacity: 0; transform: translateX(20px); }
.toast-leave-to { opacity: 0; transform: translateX(20px); }
</style>