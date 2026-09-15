<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink">Manajemen Maskapai Penerbangan</h2>
                <p class="text-xs text-ink/60 mt-0.5">Kelola daftar maskapai resmi yang digunakan dalam paket umrah.</p>
            </div>
            <button 
                @click="openModal()" 
                class="inline-flex items-center gap-2 bg-brass hover:bg-brass-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Maskapai
            </button>
        </div>

        <!-- DataTable -->
        <DataTable 
            :columns="columns" 
            :data="airlines" 
            :loading="loading" 
            :searchable="true" 
            @search="onSearch"
        >
            <template #cell-nama="{ row }">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-dusk text-white flex items-center justify-center font-bold text-xs shrink-0">
                        ✈️
                    </div>
                    <div>
                        <p class="font-bold text-ink text-sm">{{ row.nama }}</p>
                        <p class="text-[11px] text-ink/50">Slug: {{ row.slug }}</p>
                    </div>
                </div>
            </template>

            <template #cell-keterangan="{ value }">
                <span class="text-xs text-ink/70">{{ value || 'Penerbangan standar' }}</span>
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
                        @click="deleteAirline(row)" 
                        class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" 
                        title="Hapus Maskapai"
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
                        {{ editingId ? 'Edit Maskapai' : 'Tambah Maskapai Baru' }}
                    </h3>
                    <button @click="closeModal" class="text-ink/40 hover:text-ink text-lg leading-none">&times;</button>
                </div>

                <form @submit.prevent="saveAirline" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Nama Maskapai <span class="text-rust">*</span></label>
                        <input 
                            v-model="form.nama" 
                            type="text" 
                            required 
                            placeholder="Contoh: Garuda Indonesia / Saudia Airlines"
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Keterangan / Tipe Penerbangan</label>
                        <textarea 
                            v-model="form.keterangan" 
                            rows="3" 
                            placeholder="Contoh: Direct Flight Jakarta - Jeddah, Free Bagasi 30kg"
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all resize-none"
                        />
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
                            {{ saving ? 'Menyimpan...' : 'Simpan Maskapai' }}
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
const airlines = ref([]);
const rawAirlines = ref([]);
const loading = ref(true);
const showModal = ref(false);
const saving = ref(false);
const editingId = ref(null);

const form = ref({
    nama: '',
    keterangan: ''
});

const columns = [
    { key: 'nama', label: 'Maskapai Penerbangan' },
    { key: 'keterangan', label: 'Keterangan Layanan' }
];

async function fetchAirlines() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/airlines');
        rawAirlines.value = data.data;
        airlines.value = data.data;
    } catch {
        showError('Gagal memuat data maskapai.');
    } finally {
        loading.value = false;
    }
}

function onSearch(query) {
    if (!query) {
        airlines.value = rawAirlines.value;
        return;
    }
    const q = query.toLowerCase();
    airlines.value = rawAirlines.value.filter(a => 
        a.nama.toLowerCase().includes(q) || 
        (a.keterangan && a.keterangan.toLowerCase().includes(q))
    );
}

function openModal(row = null) {
    if (row) {
        editingId.value = row.id;
        form.value = { nama: row.nama, keterangan: row.keterangan || '' };
    } else {
        editingId.value = null;
        form.value = { nama: '', keterangan: '' };
    }
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    editingId.value = null;
    form.value = { nama: '', keterangan: '' };
}

async function saveAirline() {
    saving.value = true;
    try {
        if (editingId.value) {
            await axios.put(`/api/v1/admin/airlines/${editingId.value}`, form.value);
            success('Maskapai berhasil diperbarui!');
        } else {
            await axios.post('/api/v1/admin/airlines', form.value);
            success('Maskapai baru berhasil ditambahkan!');
        }
        closeModal();
        fetchAirlines();
    } catch (e) {
        showError(e.response?.data?.message || 'Gagal menyimpan maskapai.');
    } finally {
        saving.value = false;
    }
}

async function deleteAirline(row) {
    if (!confirm(`Hapus maskapai "${row.nama}"?`)) return;
    try {
        await axios.delete(`/api/v1/admin/airlines/${row.id}`);
        success('Maskapai berhasil dihapus.');
        fetchAirlines();
    } catch {
        showError('Gagal menghapus maskapai.');
    }
}

onMounted(() => fetchAirlines());
</script>