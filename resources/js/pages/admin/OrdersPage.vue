<template>
    <div>
        <h2 class="text-xl font-bold text-ink mb-6">Manajemen Order</h2>
        <DataTable :columns="columns" :data="orders" :meta="meta" :loading="loading" @search="onSearch" @page-change="onPageChange">
            <template #cell-status="{ value }">
                <span :class="['px-2 py-0.5 rounded-full text-xs font-semibold',
                    value === 'complete' ? 'bg-sage-100 text-sage' :
                    value === 'waiting_confirmation' ? 'bg-rust-100 text-rust' : 'bg-limestone text-ink/60']">
                    {{ value?.replace(/_/g, ' ') }}
                </span>
            </template>
            <template #actions="{ row }">
                <RouterLink :to="{ name: 'admin.orders.detail', params: { id: row.id } }" class="px-3 py-1 bg-indigo-dusk text-white text-xs rounded-lg hover:bg-indigo-dusk-900 transition-colors">Detail</RouterLink>
            </template>
        </DataTable>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import DataTable from '../../components/DataTable.vue';
import axios from 'axios';
const orders = ref([]); const meta = ref(null); const loading = ref(true); const search = ref('');
const columns = [
    { key: 'order_code', label: 'Kode Order' }, { key: 'total', label: 'Total' }, { key: 'status', label: 'Status' }, { key: 'created_at', label: 'Tanggal' },
];
async function fetchOrders(p = {}) { loading.value = true; try { const { data } = await axios.get('/api/v1/admin/orders', { params: p }); orders.value = data.data; meta.value = data.meta; } finally { loading.value = false; } }
function onSearch(q) { search.value = q; fetchOrders({ search: q }); }
function onPageChange(p) { fetchOrders({ page: p, search: search.value }); }
onMounted(() => fetchOrders());
</script>