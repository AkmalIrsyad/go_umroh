<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink">Manajemen Rute Perjalanan</h2>
                <p class="text-xs text-ink/60 mt-0.5">Kelola rute keberangkatan, kota transit, dan kepulangan jamaah umrah.</p>
            </div>
            <button 
                @click="openModal()" 
                class="inline-flex items-center gap-2 bg-brass hover:bg-brass-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Rute Baru
            </button>
        </div>

        <!-- DataTable -->
        <DataTable 
            :columns="columns" 
            :data="routes" 
            :loading="loading" 
            :searchable="true" 
            @search="onSearch"
        >
            <template #cell-name="{ row }">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-dusk-100 text-indigo-dusk flex items-center justify-center font-bold text-xs shrink-0">
                        🗺️
                    </div>
                    <div>
                        <p class="font-bold text-ink text-sm">{{ row.name }}</p>
                        <p class="text-[11px] text-ink/50">Slug: {{ row.slug }}</p>
                    </div>
                </div>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-2 justify-end">
                    <button 
                        @click="openModal(row)" 
                        class="px-3 py-1.5 bg-limestone text-ink text-xs font-bold rounded-lg hover:bg-limestone-100 transition-colors"
                    >
                        Edit
                    </button>
                    <button 
                        @click="deleteRoute(row)" 
                        class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" 
                        title="Hapus Rute"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal Form Add/Edit -->
        <div v-if="showModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-limestone-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between pb-4 border-b border-limestone-100 mb-5">
                    <h3 class="text-base font-bold text-ink">
                        {{ editingId ? 'Edit Rute Perjalanan' : 'Tambah Rute Perjalanan Baru' }}
                    </h3>
                    <button @click="closeModal" class="text-ink/40 hover:text-ink text-lg leading-none">&times;</button>
                </div>

                <form @submit.prevent="saveRoute" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Nama Rute Lengkap <span class="text-rust">*</span></label>
                        <input 
                            v-model="form.name" 
                            type="text" 
                            required 
                            placeholder="Contoh: Jakarta - Jeddah - Makkah - Madinah - Jakarta"
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        />
                        <p class="text-[11px] text-ink/50 mt-1">Gunakan tanda strip (-) sebagai pemisah antar kota tujuan.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-limestone-100">
                        <button 
                            type="button" 
                            @click="closeModal" 
                            class="px-4 py-2 border border-limestone-100 rounded-xl text-xs font-bold text-ink/70 hover:bg-limestone transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="saving" 
                            class="px-5 py-2 bg-brass hover:bg-brass-600 text-white rounded-xl text-xs font-bold transition-all disabled:opacity-50"
                        >
                            {{ saving ? 'Menyimpan...' : 'Simpan Rute' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DataTable from '../../components/DataTable.vue';
import { useToast } from '../../composables/useToast';
import axios from 'axios';

const { success, error: showError } = useToast();
const routes = ref([]);
const rawRoutes = ref([]);
const loading = ref(true);
const showModal = ref(false);
const saving = ref(false);
const editingId = ref(null);

const form = ref({
    name: ''
});

const columns = [
    { key: 'name', label: 'Rute Perjalanan Umrah' }
];

async function fetchRoutes() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/routes');
        rawRoutes.value = data.data;
        routes.value = data.data;
    } catch {
        showError('Gagal memuat data rute.');
    } finally {
        loading.value = false;
    }
}

function onSearch(query) {
    if (!query) {
        routes.value = rawRoutes.value;
        return;
    }
    const q = query.toLowerCase();
    routes.value = rawRoutes.value.filter(r => r.name.toLowerCase().includes(q) || r.slug.toLowerCase().includes(q));
}

function openModal(row = null) {
    if (row) {
        editingId.value = row.id;
        form.value = { name: row.name };
    } else {
        editingId.value = null;
        form.value = { name: '' };
    }
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    editingId.value = null;
    form.value = { name: '' };
}

async function saveRoute() {
    saving.value = true;
    try {
        if (editingId.value) {
            await axios.put(`/api/v1/admin/routes/${editingId.value}`, form.value);
            success('Rute berhasil diperbarui!');
        } else {
            await axios.post('/api/v1/admin/routes', form.value);
            success('Rute baru berhasil ditambahkan!');
        }
        closeModal();
        fetchRoutes();
    } catch (e) {
        showError(e.response?.data?.message || 'Gagal menyimpan rute.');
    } finally {
        saving.value = false;
    }
}

async function deleteRoute(row) {
    if (!confirm(`Hapus rute "${row.name}"?`)) return;
    try {
        await axios.delete(`/api/v1/admin/routes/${row.id}`);
        success('Rute berhasil dihapus.');
        fetchRoutes();
    } catch {
        showError('Gagal menghapus rute.');
    }
}

onMounted(() => fetchRoutes());
</script>