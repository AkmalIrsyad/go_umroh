<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink">Paket Umrah Saya</h2>
                <p class="text-xs text-ink/60 mt-0.5">Kelola paket perjalanan umrah yang Anda tawarkan kepada calon jamaah.</p>
            </div>
            <RouterLink 
                to="/merchant/packages/create" 
                class="inline-flex items-center gap-2 bg-brass hover:bg-brass-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Tambah Paket Baru
            </RouterLink>
        </div>

        <DataTable 
            :columns="columns" 
            :data="packages" 
            :meta="meta" 
            :loading="loading" 
            @search="onSearch" 
            @page-change="onPageChange"
        >
            <template #cell-type="{ value }">
                <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                    value === 'vip' ? 'bg-brass text-white' :
                    value === 'promo' ? 'bg-rust text-white' : 'bg-indigo-dusk-100 text-indigo-dusk']">
                    {{ value }}
                </span>
            </template>

            <template #cell-price="{ value }">
                <span class="font-bold text-ink">{{ formatRupiah(value) }}</span>
            </template>

            <template #cell-capacity="{ row }">
                <span class="text-xs font-semibold">{{ row.booked_count || 0 }} / {{ row.capacity }} Pax</span>
            </template>

            <template #cell-status="{ value }">
                <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                    value === 'approved' ? 'bg-sage-100 text-sage' : 'bg-rust-100 text-rust']">
                    {{ value === 'approved' ? '✓ Disetujui' : '⏳ Menunggu Review' }}
                </span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-2 justify-end">
                    <RouterLink 
                        :to="{ name: 'merchant.packages.edit', params: { slug: row.slug } }"
                        class="px-3 py-1.5 bg-indigo-dusk text-white text-xs font-bold rounded-lg hover:bg-indigo-dusk-900 transition-colors"
                    >
                        Edit Paket
                    </RouterLink>
                    <button 
                        @click="deletePackage(row)"
                        class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
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
const search = ref('');

const columns = [
    { key: 'title', label: 'Judul Paket' },
    { key: 'type', label: 'Tipe' },
    { key: 'departure_date', label: 'Tgl Berangkat' },
    { key: 'capacity', label: 'Kuota' },
    { key: 'price', label: 'Harga / Pax' },
    { key: 'status', label: 'Status' }
];

async function fetchPackages(p = {}) {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/merchant/packages', { params: p });
        packages.value = data.data;
        meta.value = data.meta;
    } catch {
        showError('Gagal memuat paket merchant.');
    } finally {
        loading.value = false;
    }
}

async function deletePackage(row) {
    if (!confirm(`Hapus paket "${row.title}"?`)) return;
    try {
        await axios.delete(`/api/v1/merchant/packages/${row.id}`);
        success('Paket berhasil dihapus.');
        fetchPackages({ search: search.value });
    } catch {
        showError('Gagal menghapus paket.');
    }
}

function onSearch(q) {
    search.value = q;
    fetchPackages({ search: q });
}

function onPageChange(p) {
    fetchPackages({ page: p, search: search.value });
}

onMounted(() => fetchPackages());
</script>