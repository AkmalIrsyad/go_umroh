<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-ink">Manajemen Bank</h2>
            <button @click="showForm = true" class="bg-indigo-dusk text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-dusk-900 transition-colors">+ Tambah Bank</button>
        </div>

        <!-- Modal Form -->
        <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
                <h3 class="font-bold text-ink mb-4">{{ editing ? 'Edit' : 'Tambah' }} Bank</h3>
                <form @submit.prevent="saveBank" class="space-y-4">
                    <div><label class="block text-sm font-medium mb-1">Nama Bank</label><input v-model="form.nama" required class="w-full border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30" /></div>
                    <div><label class="block text-sm font-medium mb-1">Nama Akun</label><input v-model="form.nama_akun" required class="w-full border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30" /></div>
                    <div><label class="block text-sm font-medium mb-1">Nomor Rekening</label><input v-model="form.nomor_rekening" required class="w-full border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30" /></div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="closeForm" class="flex-1 border border-limestone-100 rounded-xl py-2 text-sm">Batal</button>
                        <button type="submit" :disabled="saving" class="flex-1 bg-brass text-white rounded-xl py-2 text-sm font-medium disabled:opacity-50">{{ saving ? '...' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <DataTable :columns="columns" :data="banks" :loading="loading" :searchable="false">
            <template #actions="{ row }">
                <div class="flex gap-2 justify-end">
                    <button @click="editBank(row)" class="px-3 py-1 bg-limestone text-ink text-xs rounded-lg hover:bg-limestone-100 transition-colors">Edit</button>
                    <button @click="deleteBank(row)" class="px-3 py-1 bg-red-100 text-red-600 text-xs rounded-lg hover:bg-red-200 transition-colors">Hapus</button>
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
const banks = ref([]); const loading = ref(true); const showForm = ref(false); const saving = ref(false); const editing = ref(null);
const form = ref({ nama: '', nama_akun: '', nomor_rekening: '' });
const columns = [{ key: 'nama', label: 'Bank' }, { key: 'nama_akun', label: 'Nama Akun' }, { key: 'nomor_rekening', label: 'No. Rekening' }];
async function fetchBanks() { loading.value = true; try { const { data } = await axios.get('/api/v1/banks'); banks.value = data.data; } finally { loading.value = false; } }
async function saveBank() { saving.value = true; try { if (editing.value) { await axios.put(`/api/v1/admin/banks/${editing.value}`, form.value); } else { await axios.post('/api/v1/admin/banks', form.value); } success('Disimpan!'); closeForm(); fetchBanks(); } catch { showError('Gagal.'); } finally { saving.value = false; } }
function editBank(row) { editing.value = row.id; form.value = { nama: row.nama, nama_akun: row.nama_akun, nomor_rekening: row.nomor_rekening }; showForm.value = true; }
async function deleteBank(row) { if (!confirm('Hapus?')) return; try { await axios.delete(`/api/v1/admin/banks/${row.id}`); success('Dihapus.'); fetchBanks(); } catch { showError('Gagal.'); } }
function closeForm() { showForm.value = false; editing.value = null; form.value = { nama: '', nama_akun: '', nomor_rekening: '' }; }
onMounted(() => fetchBanks());
</script>