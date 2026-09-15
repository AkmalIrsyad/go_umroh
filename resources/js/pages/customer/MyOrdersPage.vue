<template>
    <CustomerLayout>
        <div class="max-w-3xl mx-auto px-4 py-10">
            <h1 class="text-2xl font-bold text-ink mb-6">Riwayat Pesanan Saya</h1>
            <div v-if="loading" class="space-y-4"><div v-for="i in 3" :key="i" class="bg-white rounded-2xl border border-limestone-100 p-5 animate-pulse h-28" /></div>
            <div v-else-if="!orders.length" class="text-center py-20 text-ink/40"><p class="text-4xl mb-3">📋</p><p>Belum ada pesanan.</p><RouterLink to="/packages" class="mt-3 inline-block text-brass hover:underline">Cari paket sekarang</RouterLink></div>
            <div v-else class="space-y-4">
                <RouterLink v-for="order in orders" :key="order.id" :to="{ name: 'order.detail', params: { id: order.id } }"
                    class="block bg-white rounded-2xl border border-limestone-100 p-5 hover:shadow-md hover:border-brass/20 transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-ink text-sm">{{ order.package?.title }}</p>
                            <p class="text-xs text-ink/50 mt-1">{{ order.order_code }}</p>
                        </div>
                        <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold shrink-0',
                            order.status === 'complete' ? 'bg-sage-100 text-sage' :
                            order.status === 'waiting_confirmation' ? 'bg-rust-100 text-rust' : 'bg-limestone text-ink/60']">
                            {{ order.status?.replace(/_/g, ' ') }}
                        </span>
                    </div>
                    <p class="text-brass font-bold mt-3">{{ formatRupiah(order.total) }}</p>
                </RouterLink>
            </div>
        </div>
    </CustomerLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue'; import { RouterLink } from 'vue-router'; import CustomerLayout from '../../layouts/CustomerLayout.vue'; import { useFormatCurrency } from '../../composables/useFormatCurrency'; import axios from 'axios';
const { formatRupiah } = useFormatCurrency(); const orders = ref([]); const loading = ref(true);
onMounted(async () => { try { const { data } = await axios.get('/api/v1/orders'); orders.value = data.data; } finally { loading.value = false; } });
</script>