<template>
    <div>
        <RouterLink to="/admin/orders" class="text-sm text-ink/50 hover:text-brass mb-4 inline-flex items-center gap-1">← Kembali</RouterLink>
        <h2 class="text-xl font-bold text-ink mb-6">Detail Order</h2>
        <div v-if="loading" class="text-center py-10"><div class="inline-block w-8 h-8 border-2 border-brass border-t-transparent rounded-full animate-spin" /></div>
        <div v-else-if="order" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-limestone-100 p-6">
                <h3 class="font-semibold mb-4">Informasi Order</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-ink/50">Kode</dt><dd class="font-medium">{{ order.order_code }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink/50">Total</dt><dd class="font-bold text-brass">{{ formatRupiah(order.total) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink/50">Kode Unik</dt><dd>{{ order.kode_unik }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink/50">Status</dt>
                        <dd><span :class="['px-2 py-0.5 rounded-full text-xs font-semibold', order.status === 'complete' ? 'bg-sage-100 text-sage' : 'bg-rust-100 text-rust']">{{ order.status?.replace(/_/g, ' ') }}</span></dd>
                    </div>
                </dl>
            </div>
            <div class="bg-white rounded-2xl border border-limestone-100 p-6">
                <h3 class="font-semibold mb-4">Data Jamaah</h3>
                <div class="space-y-2">
                    <div v-for="p in order.participants" :key="p.id" class="flex justify-between text-sm py-2 border-b border-limestone-100 last:border-0">
                        <span>{{ p.nama }}</span>
                        <span class="text-ink/50 capitalize">{{ p.tipe }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useFormatCurrency } from '../../composables/useFormatCurrency';
import axios from 'axios';
const route = useRoute(); const { formatRupiah } = useFormatCurrency();
const order = ref(null); const loading = ref(true);
onMounted(async () => { try { const { data } = await axios.get(`/api/v1/admin/orders/${route.params.id}`); order.value = data.data; } finally { loading.value = false; } });
</script>