<template>
    <div>
        <h2 class="text-xl font-bold text-ink mb-6">Dashboard Travel Partner</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-limestone-100 p-5">
                <p class="text-2xl font-bold text-sage">{{ stats.approved }}</p>
                <p class="text-xs text-ink/50 mt-1">Paket Disetujui</p>
            </div>
            <div class="bg-white rounded-2xl border border-limestone-100 p-5">
                <p class="text-2xl font-bold text-rust">{{ stats.pending }}</p>
                <p class="text-xs text-ink/50 mt-1">Paket Pending</p>
            </div>
            <div class="bg-white rounded-2xl border border-limestone-100 p-5">
                <p class="text-2xl font-bold text-brass">{{ stats.orders }}</p>
                <p class="text-xs text-ink/50 mt-1">Total Order</p>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue';
const stats = ref({ approved: 0, pending: 0, orders: 0 });
</script>