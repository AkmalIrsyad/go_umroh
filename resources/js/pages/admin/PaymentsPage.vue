<template>
    <div>
        <h2 class="text-xl font-bold text-ink mb-6">Konfirmasi Pembayaran</h2>
        <DataTable :columns="columns" :data="payments" :meta="meta" :loading="loading" @search="onSearch" @page-change="onPageChange">
            <template #cell-status="{ value }">
                <span :class="['px-2 py-0.5 rounded-full text-xs font-semibold', value === 'approved' ? 'bg-sage-100 text-sage' : 'bg-rust-100 text-rust']">{{ value }}</span>
            </template>
            <template #actions="{ row }">
                <div class="flex gap-2 justify-end">
                    <a v-if="row.bukti_transfer" :href="row.bukti_transfer" target="_blank" class="px-3 py-1 bg-limestone text-ink text-xs rounded-lg hover:bg-limestone-100 transition-colors">Lihat Bukti</a>
                    <button v-if="row.status !== 'approved'" @click="approve(row)" class="px-3 py-1 bg-sage text-white text-xs rounded-lg hover:bg-sage-900 transition-colors">Approve</button>
                </div>
            </template>
        </DataTable>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import DataTable from '../../components/DataTable.vue';
import { useToast } from '../../composables/useToast';
import axios from 'axios';
const { success, error: showError } = useToast();
const payments = ref([]); const meta = ref(null); const loading = ref(true);
const columns = [
    { key: 'id', label: 'ID' }, { key: 'nominal', label: 'Nominal' }, { key: 'bank_pengirim', label: 'Bank Pengirim' }, { key: 'status', label: 'Status' },
];
async function fetchPayments(p = {}) { loading.value = true; try { const { data } = await axios.get('/api/v1/admin/payments', { params: p }); payments.value = data.data; meta.value = data.meta; } finally { loading.value = false; } }
async function approve(row) { try { await axios.put(`/api/v1/admin/payments/${row.id}/approve`); success('Pembayaran disetujui!'); fetchPayments(); } catch { showError('Gagal.'); } }
function onSearch(q) { fetchPayments({ search: q }); }
function onPageChange(p) { fetchPayments({ page: p }); }
onMounted(() => fetchPayments());
</script>