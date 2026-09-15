<template><div><div class="flex justify-between items-center mb-6"><h2 class="text-xl font-bold text-ink">Data Jamaah</h2>
<a href="/api/v1/admin/participants/export" target="_blank" class="bg-sage text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-sage-900 transition-colors">📥 Export CSV</a>
</div><DataTable :columns="columns" :data="participants" :meta="meta" :loading="loading" @search="onSearch" @page-change="onPageChange" /></div></template>
<script setup>
import { ref, onMounted } from 'vue'; import DataTable from '../../components/DataTable.vue'; import axios from 'axios';
const participants = ref([]); const meta = ref(null); const loading = ref(true); const search = ref('');
const columns = [{ key: 'nama', label: 'Nama Jamaah' }, { key: 'nomor_ponsel', label: 'No HP' }, { key: 'tipe', label: 'Tipe' }];
async function fetch(p = {}) { loading.value = true; try { const { data } = await axios.get('/api/v1/admin/participants', { params: p }); participants.value = data.data; meta.value = data.meta; } finally { loading.value = false; } }
function onSearch(q) { search.value = q; fetch({ search: q }); }
function onPageChange(p) { fetch({ page: p, search: search.value }); }
onMounted(() => fetch());
</script>