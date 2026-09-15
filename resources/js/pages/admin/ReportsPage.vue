<template><div><h2 class="text-xl font-bold text-ink mb-6">Laporan Penjualan</h2><DataTable :columns="columns" :data="orders" :meta="meta" :loading="loading" @page-change="onPageChange">
<template #cell-total="{ value }">{{ formatRupiah(value) }}</template>
<template #cell-status="{ value }"><span :class="['px-2 py-0.5 rounded-full text-xs font-semibold', value === 'complete' ? 'bg-sage-100 text-sage' : 'bg-rust-100 text-rust']">{{ value }}</span></template>
</DataTable></div></template>
<script setup>
import { ref, onMounted } from 'vue'; import DataTable from '../../components/DataTable.vue'; import { useFormatCurrency } from '../../composables/useFormatCurrency'; import axios from 'axios';
const { formatRupiah } = useFormatCurrency(); const orders = ref([]); const meta = ref(null); const loading = ref(true);
const columns = [{ key: 'order_code', label: 'Kode' }, { key: 'total', label: 'Total' }, { key: 'status', label: 'Status' }, { key: 'created_at', label: 'Tanggal' }];
async function fetch(p = {}) { loading.value = true; try { const { data } = await axios.get('/api/v1/admin/reports/sales', { params: p }); orders.value = data.data.data || []; meta.value = data.data.meta; } finally { loading.value = false; } }
function onPageChange(p) { fetch({ page: p }); }
onMounted(() => fetch());
</script>