<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink">Manajemen Hotel & Akomodasi</h2>
                <p class="text-xs text-ink/60 mt-0.5">Kelola daftar hotel di Makkah, Madinah, Jeddah, dan kota transit lainnya.</p>
            </div>
            <button 
                @click="openModal()" 
                class="inline-flex items-center gap-2 bg-brass hover:bg-brass-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Hotel
            </button>
        </div>

        <!-- Location Filter Tabs -->
        <div class="flex items-center gap-2 border-b border-limestone-100 pb-3">
            <button 
                v-for="loc in ['Semua Lokasi', 'Makkah', 'Madinah', 'Jeddah', 'Turki']" 
                :key="loc"
                @click="filterLocation(loc)"
                :class="[
                    'px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all',
                    activeLocation === loc 
                        ? 'bg-indigo-dusk text-white shadow-sm' 
                        : 'bg-white text-ink/70 hover:bg-limestone-50 border border-limestone-100'
                ]"
            >
                {{ loc }}
            </button>
        </div>

        <!-- DataTable -->
        <DataTable 
            :columns="columns" 
            :data="filteredHotels" 
            :loading="loading" 
            :searchable="true" 
            @search="onSearch"
        >
            <template #cell-nama="{ row }">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-brass/10 text-brass flex items-center justify-center font-bold text-sm shrink-0">
                        🏨
                    </div>
                    <div>
                        <p class="font-bold text-ink text-sm">{{ row.nama }}</p>
                        <p class="text-[11px] text-ink/50">Slug: {{ row.slug }}</p>
                    </div>
                </div>
            </template>

            <template #cell-lokasi="{ value }">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-dusk-100 text-indigo-dusk">
                    📍 {{ value }}
                </span>
            </template>

            <template #cell-keterangan="{ value }">
                <span class="text-xs text-ink/70">{{ value || 'Bintang 5, Fasilitas Lengkap' }}</span>
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
                        @click="deleteHotel(row)" 
                        class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" 
                        title="Hapus Hotel"
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
                        {{ editingId ? 'Edit Hotel' : 'Tambah Hotel Baru' }}
                    </h3>
                    <button @click="closeModal" class="text-ink/40 hover:text-ink text-lg leading-none">&times;</button>
                </div>

                <form @submit.prevent="saveHotel" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Nama Hotel <span class="text-rust">*</span></label>
                        <input 
                            v-model="form.nama" 
                            type="text" 
                            required 
                            placeholder="Contoh: Pullman Zamzam Makkah / Movenpick Hotel"
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Lokasi Kota <span class="text-rust">*</span></label>
                        <select 
                            v-model="form.lokasi" 
                            required 
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        >
                            <option value="">-- Pilih Lokasi --</option>
                            <option value="Makkah">Makkah</option>
                            <option value="Madinah">Madinah</option>
                            <option value="Jeddah">Jeddah</option>
                            <option value="Turki">Turki / Istanbul</option>
                            <option value="Dubai">Dubai</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Keterangan & Fasilitas</label>
                        <textarea 
                            v-model="form.keterangan" 
                            rows="3" 
                            placeholder="Contoh: Hotel Bintang 5, Jarak ±50m ke Pelataran Masjidil Haram, Sarapan Buffet Internasional"
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
                            {{ saving ? 'Menyimpan...' : 'Simpan Hotel' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import DataTable from '../../components/DataTable.vue';
import { useToast } from '../../composables/useToast';
import axios from 'axios';

const { success, error: showError } = useToast();
const hotels = ref([]);
const rawHotels = ref([]);
const loading = ref(true);
const showModal = ref(false);
const saving = ref(false);
const editingId = ref(null);
const activeLocation = ref('Semua Lokasi');
const searchQuery = ref('');

const form = ref({
    nama: '',
    lokasi: 'Makkah',
    keterangan: ''
});

const columns = [
    { key: 'nama', label: 'Nama Hotel' },
    { key: 'lokasi', label: 'Lokasi Kota' },
    { key: 'keterangan', label: 'Keterangan & Fasilitas' }
];

const filteredHotels = computed(() => {
    let list = rawHotels.value;
    if (activeLocation.value !== 'Semua Lokasi') {
        list = list.filter(h => h.lokasi === activeLocation.value);
    }
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        list = list.filter(h => 
            h.nama.toLowerCase().includes(q) || 
            (h.lokasi && h.lokasi.toLowerCase().includes(q)) ||
            (h.keterangan && h.keterangan.toLowerCase().includes(q))
        );
    }
    return list;
});

async function fetchHotels() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/hotels');
        rawHotels.value = data.data;
        hotels.value = data.data;
    } catch {
        showError('Gagal memuat data hotel.');
    } finally {
        loading.value = false;
    }
}

function filterLocation(loc) {
    activeLocation.value = loc;
}

function onSearch(query) {
    searchQuery.value = query;
}

function openModal(row = null) {
    if (row) {
        editingId.value = row.id;
        form.value = { nama: row.nama, lokasi: row.lokasi || 'Makkah', keterangan: row.keterangan || '' };
    } else {
        editingId.value = null;
        form.value = { nama: '', lokasi: 'Makkah', keterangan: '' };
    }
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    editingId.value = null;
    form.value = { nama: '', lokasi: 'Makkah', keterangan: '' };
}

async function saveHotel() {
    saving.value = true;
    try {
        if (editingId.value) {
            await axios.put(`/api/v1/admin/hotels/${editingId.value}`, form.value);
            success('Hotel berhasil diperbarui!');
        } else {
            await axios.post('/api/v1/admin/hotels', form.value);
            success('Hotel baru berhasil ditambahkan!');
        }
        closeModal();
        fetchHotels();
    } catch (e) {
        showError(e.response?.data?.message || 'Gagal menyimpan hotel.');
    } finally {
        saving.value = false;
    }
}

async function deleteHotel(row) {
    if (!confirm(`Hapus hotel "${row.nama}"?`)) return;
    try {
        await axios.delete(`/api/v1/admin/hotels/${row.id}`);
        success('Hotel berhasil dihapus.');
        fetchHotels();
    } catch {
        showError('Gagal menghapus hotel.');
    }
}

onMounted(() => fetchHotels());
</script>