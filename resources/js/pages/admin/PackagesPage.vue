<template>
    <div class="space-y-6">
        <!-- Header with Add Button -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink">Manajemen Paket Umrah</h2>
                <p class="text-xs text-ink/60 mt-0.5">Kelola seluruh paket umrah dari seluruh travel agent / merchant.</p>
            </div>
            <RouterLink 
                to="/admin/packages/create" 
                class="inline-flex items-center gap-2 bg-brass hover:bg-brass-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Paket Baru
            </RouterLink>
        </div>

        <!-- Filter Status Tabs -->
        <div class="flex items-center gap-2 border-b border-limestone-100 pb-3">
            <button 
                v-for="st in [
                    { label: 'Semua Status', val: '' },
                    { label: 'Approved', val: 'approved' },
                    { label: 'Pending Review', val: 'pending' }
                ]" 
                :key="st.val"
                @click="filterStatus(st.val)"
                :class="[
                    'px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all',
                    activeStatus === st.val 
                        ? 'bg-indigo-dusk text-white shadow-sm' 
                        : 'bg-white text-ink/70 hover:bg-limestone-50 border border-limestone-100'
                ]"
            >
                {{ st.label }}
            </button>
        </div>

        <!-- DataTable -->
        <DataTable 
            :columns="columns" 
            :data="packages" 
            :meta="meta" 
            :loading="loading" 
            @search="onSearch" 
            @page-change="onPageChange"
        >
            <!-- Custom Cell: Title & Merchant -->
            <template #cell-title="{ row }">
                <div>
                    <p class="font-bold text-ink leading-tight">{{ row.title }}</p>
                    <p class="text-[11px] text-ink/50 mt-0.5">
                        By: <span class="text-brass font-medium">{{ row.merchant?.business_name || row.merchant?.name || 'Admin' }}</span>
                    </p>
                </div>
            </template>

            <!-- Custom Cell: Type -->
            <template #cell-type="{ value }">
                <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                    value === 'vip' ? 'bg-brass text-white' :
                    value === 'promo' ? 'bg-rust text-white' : 'bg-indigo-dusk-100 text-indigo-dusk']">
                    {{ value }}
                </span>
            </template>

            <!-- Custom Cell: Status -->
            <template #cell-status="{ value }">
                <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                    value === 'approved' ? 'bg-sage-100 text-sage' : 'bg-rust-100 text-rust']">
                    {{ value === 'approved' ? '✓ Approved' : '⏳ Pending' }}
                </span>
            </template>

            <!-- Custom Cell: Price -->
            <template #cell-price="{ value }">
                <span class="font-bold text-ink">{{ formatRupiah(value) }}</span>
            </template>

            <!-- Custom Cell: Quota / Seats -->
            <template #cell-capacity="{ row }">
                <div class="text-xs">
                    <span class="font-semibold text-ink">{{ row.booked_count || 0 }}</span> / {{ row.capacity }} Pax
                    <div class="w-16 bg-limestone-100 h-1.5 rounded-full overflow-hidden mt-1">
                        <div 
                            class="bg-brass h-full rounded-full" 
                            :style="{ width: `${Math.min(100, ((row.booked_count || 0) / row.capacity) * 100)}%` }"
                        />
                    </div>
                </div>
            </template>

            <!-- Actions -->
            <template #actions="{ row }">
                <div class="flex items-center gap-1.5 justify-end">
                    <button 
                        v-if="row.status === 'pending'"
                        @click="updateStatus(row, 'approved')"
                        class="px-2.5 py-1 bg-sage text-white text-xs font-bold rounded-lg hover:bg-sage-900 transition-colors"
                        title="Setujui Paket"
                    >
                        Approve
                    </button>
                    <button 
                        v-else
                        @click="updateStatus(row, 'pending')"
                        class="px-2.5 py-1 bg-rust/10 text-rust text-xs font-bold rounded-lg hover:bg-rust hover:text-white transition-colors"
                        title="Tangguhkan Paket"
                    >
                        Suspend
                    </button>

                    <RouterLink 
                        :to="{ name: 'admin.packages.edit', params: { id: row.id } }"
                        class="px-2.5 py-1 bg-limestone text-ink text-xs font-bold rounded-lg hover:bg-limestone-100 transition-colors"
                    >
                        Edit
                    </RouterLink>

                    <button 
                        @click="deletePackage(row)"
                        class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                        title="Hapus Paket"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </template>
        </DataTable>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import DataTable from '../../components/DataTable.vue';
import { useFormatCurrency } from '../../composables/useFormatCurrency';
import { useToast } from '../../composables/useToast';
import axios from 'axios';

const { formatRupiah } = useFormatCurrency();
const { success, error: showError } = useToast();
const packages = ref([]);
const meta = ref(null);
const loading = ref(true);
const currentSearch = ref('');
const activeStatus = ref('');

const columns = [
    { key: 'title', label: 'Judul Paket' },
    { key: 'type', label: 'Tipe' },
    { key: 'departure_date', label: 'Tgl Berangkat' },
    { key: 'capacity', label: 'Kuota Terisi' },
    { key: 'price', label: 'Harga / Pax' },
    { key: 'status', label: 'Status' },
];

async function fetchPackages(params = {}) {
    loading.value = true;
    try {
        const query = { ...params };
        if (activeStatus.value) query.status = activeStatus.value;
        const { data } = await axios.get('/api/v1/admin/packages', { params: query });
        packages.value = data.data;
        meta.value = data.meta;
    } catch {
        showError('Gagal memuat paket.');
    } finally {
        loading.value = false;
    }
}

function filterStatus(st) {
    activeStatus.value = st;
    fetchPackages({ search: currentSearch.value });
}

async function updateStatus(row, newStatus) {
    try {
        await axios.put(`/api/v1/admin/packages/${row.id}/status`, { status: newStatus });
        success(`Status paket berhasil diubah ke ${newStatus}!`);
        fetchPackages({ search: currentSearch.value });
    } catch {
        showError('Gagal mengubah status paket.');
    }
}

async function deletePackage(row) {
    if (!confirm(`Hapus paket "${row.title}"?`)) return;
    try {
        await axios.delete(`/api/v1/admin/packages/${row.id}`);
        success('Paket berhasil dihapus.');
        fetchPackages({ search: currentSearch.value });
    } catch {
        showError('Gagal menghapus paket.');
    }
}

function onSearch(q) {
    currentSearch.value = q;
    fetchPackages({ search: q });
}

function onPageChange(p) {
    fetchPackages({ search: currentSearch.value, page: p });
}

onMounted(() => fetchPackages());
</script>