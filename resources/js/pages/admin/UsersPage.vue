<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink">Manajemen Pengguna (User Management)</h2>
                <p class="text-xs text-ink/60 mt-0.5">Kelola akun Admin, Travel Agent (Merchant), dan Jamaah (Customer).</p>
            </div>
            <button 
                @click="openModal()" 
                class="inline-flex items-center gap-2 bg-brass hover:bg-brass-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                + Tambah Pengguna
            </button>
        </div>

        <!-- Filter Role & Status Tabs -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-limestone-100 pb-3">
            <div class="flex items-center gap-2">
                <button 
                    v-for="r in [
                        { label: 'Semua Role', val: '' },
                        { label: 'Admin', val: 'admin' },
                        { label: 'Merchant / Travel', val: 'merchants' },
                        { label: 'Jamaah / User', val: 'user' }
                    ]" 
                    :key="r.val"
                    @click="filterRole(r.val)"
                    :class="[
                        'px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all',
                        activeRole === r.val 
                            ? 'bg-indigo-dusk text-white shadow-sm' 
                            : 'bg-white text-ink/70 hover:bg-limestone-50 border border-limestone-100'
                    ]"
                >
                    {{ r.label }}
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button 
                    v-for="st in [
                        { label: 'Semua Status', val: '' },
                        { label: 'Approved', val: 'approved' },
                        { label: 'Pending', val: 'pending' }
                    ]"
                    :key="st.val"
                    @click="filterStatus(st.val)"
                    :class="[
                        'px-3 py-1 rounded-lg text-xs font-semibold transition-all',
                        activeStatus === st.val 
                            ? 'bg-brass text-white shadow-sm' 
                            : 'bg-white text-ink/70 hover:bg-limestone-50 border border-limestone-100'
                    ]"
                >
                    {{ st.label }}
                </button>
            </div>
        </div>

        <!-- DataTable -->
        <DataTable 
            :columns="columns" 
            :data="users" 
            :meta="meta" 
            :loading="loading" 
            @search="onSearch" 
            @page-change="onPageChange"
        >
            <!-- Custom Cell: Name & Info -->
            <template #cell-name="{ row }">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-dusk/10 text-indigo-dusk flex items-center justify-center font-bold text-sm shrink-0">
                        {{ row.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <p class="font-bold text-ink text-sm leading-tight">{{ row.name }}</p>
                        <p v-if="row.business_name" class="text-[11px] text-brass font-medium">🏢 {{ row.business_name }}</p>
                        <p class="text-[11px] text-ink/50">{{ row.email }}</p>
                    </div>
                </div>
            </template>

            <!-- Custom Cell: Roles -->
            <template #cell-roles="{ value }">
                <div class="flex items-center gap-1">
                    <span 
                        v-for="role in (Array.isArray(value) ? value : [value])" 
                        :key="role"
                        :class="[
                            'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                            role === 'admin' ? 'bg-indigo-dusk text-white' :
                            role === 'merchants' ? 'bg-brass/15 text-brass' : 'bg-limestone text-ink/80'
                        ]"
                    >
                        {{ role === 'merchants' ? 'Merchant' : (role === 'admin' ? 'Admin' : 'Jamaah') }}
                    </span>
                </div>
            </template>

            <!-- Custom Cell: Status -->
            <template #cell-status="{ value }">
                <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                    value === 'approved' ? 'bg-sage-100 text-sage' : 'bg-rust-100 text-rust']">
                    {{ value === 'approved' ? '✓ Approved' : '⏳ Pending' }}
                </span>
            </template>

            <!-- Custom Cell: Phone -->
            <template #cell-phone_number="{ value }">
                <span class="text-xs font-medium text-ink">{{ value || '-' }}</span>
            </template>

            <!-- Actions -->
            <template #actions="{ row }">
                <div class="flex items-center gap-1.5 justify-end">
                    <!-- Quick Approve / Suspend -->
                    <button 
                        v-if="row.status === 'pending'"
                        @click="updateUserStatus(row, 'approved')"
                        class="px-2.5 py-1 bg-sage text-white text-xs font-bold rounded-lg hover:bg-sage-900 transition-colors"
                        title="Setujui Akun"
                    >
                        Approve
                    </button>
                    <button 
                        v-else-if="row.roles && !row.roles.includes('admin')"
                        @click="updateUserStatus(row, 'pending')"
                        class="px-2.5 py-1 bg-rust/10 text-rust text-xs font-bold rounded-lg hover:bg-rust hover:text-white transition-colors"
                        title="Tangguhkan Akun"
                    >
                        Suspend
                    </button>

                    <button 
                        @click="openModal(row)" 
                        class="px-2.5 py-1 bg-limestone text-ink text-xs font-bold rounded-lg hover:bg-limestone-100 transition-colors"
                    >
                        Edit
                    </button>

                    <button 
                        v-if="!row.roles || !row.roles.includes('admin')"
                        @click="deleteUser(row)" 
                        class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" 
                        title="Hapus Pengguna"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal Form Add/Edit User -->
        <div v-if="showModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-limestone-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between pb-4 border-b border-limestone-100 mb-5">
                    <h3 class="text-base font-bold text-ink">
                        {{ editingId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                    </h3>
                    <button @click="closeModal" class="text-ink/40 hover:text-ink text-lg leading-none">&times;</button>
                </div>

                <form @submit.prevent="saveUser" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-rust">*</span></label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                required 
                                placeholder="Nama Pengguna"
                                class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Email <span class="text-rust">*</span></label>
                            <input 
                                v-model="form.email" 
                                type="email" 
                                required 
                                placeholder="email@contoh.com"
                                class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">
                                {{ editingId ? 'Password Baru (Opsional)' : 'Password' }} <span v-if="!editingId" class="text-rust">*</span>
                            </label>
                            <input 
                                v-model="form.password" 
                                type="password" 
                                :required="!editingId" 
                                placeholder="Min. 6 karakter"
                                class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Nomor HP / WhatsApp</label>
                            <input 
                                v-model="form.phone_number" 
                                type="text" 
                                placeholder="08123456789"
                                class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Role Pengguna <span class="text-rust">*</span></label>
                            <select 
                                v-model="form.role" 
                                required 
                                class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                            >
                                <option value="user">Jamaah (Customer)</option>
                                <option value="merchants">Travel Agent (Merchant)</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Status Akun <span class="text-rust">*</span></label>
                            <select 
                                v-model="form.status" 
                                required 
                                class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                            >
                                <option value="approved">Approved (Aktif)</option>
                                <option value="pending">Pending (Menunggu)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Additional Info for Merchant -->
                    <div v-if="form.role === 'merchants'" class="p-4 bg-brass/5 border border-brass/20 rounded-xl space-y-3">
                        <p class="text-xs font-bold text-brass uppercase tracking-wider">Informasi Travel Agent (Merchant)</p>
                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1">Nama Perusahaan / Travel</label>
                            <input 
                                v-model="form.business_name" 
                                type="text" 
                                placeholder="Contoh: PT Al Barokah Wisata Mandiri"
                                class="w-full bg-white border border-limestone-100 rounded-xl px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1">Keterangan / Izin Kemenag</label>
                            <textarea 
                                v-model="form.business_information" 
                                rows="2" 
                                placeholder="No. Izin PPIU Kemenag No. 123/2024..."
                                class="w-full bg-white border border-limestone-100 rounded-xl px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 resize-none"
                            />
                        </div>
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
                            {{ saving ? 'Menyimpan...' : 'Simpan Pengguna' }}
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
const users = ref([]);
const meta = ref(null);
const loading = ref(true);
const saving = ref(false);

const activeRole = ref('');
const activeStatus = ref('');
const currentSearch = ref('');

const showModal = ref(false);
const editingId = ref(null);

const form = ref({
    name: '',
    email: '',
    password: '',
    phone_number: '',
    role: 'user',
    status: 'approved',
    business_name: '',
    business_information: ''
});

const columns = [
    { key: 'name', label: 'Pengguna' },
    { key: 'phone_number', label: 'No. Telepon' },
    { key: 'roles', label: 'Role' },
    { key: 'status', label: 'Status' }
];

async function fetchUsers(params = {}) {
    loading.value = true;
    try {
        const query = { ...params };
        if (activeRole.value) query.role = activeRole.value;
        if (activeStatus.value) query.status = activeStatus.value;
        if (currentSearch.value) query.search = currentSearch.value;

        const { data } = await axios.get('/api/v1/admin/users', { params: query });
        users.value = data.data;
        meta.value = data.meta;
    } catch {
        showError('Gagal memuat daftar pengguna.');
    } finally {
        loading.value = false;
    }
}

function filterRole(r) {
    activeRole.value = r;
    fetchUsers({ page: 1 });
}

function filterStatus(st) {
    activeStatus.value = st;
    fetchUsers({ page: 1 });
}

function onSearch(q) {
    currentSearch.value = q;
    fetchUsers({ search: q, page: 1 });
}

function onPageChange(p) {
    fetchUsers({ page: p });
}

function openModal(row = null) {
    if (row) {
        editingId.value = row.id;
        form.value = {
            name: row.name,
            email: row.email,
            password: '',
            phone_number: row.phone_number || '',
            role: (row.roles && row.roles[0]) || 'user',
            status: row.status || 'approved',
            business_name: row.business_name || '',
            business_information: row.business_information || ''
        };
    } else {
        editingId.value = null;
        form.value = {
            name: '',
            email: '',
            password: '',
            phone_number: '',
            role: 'user',
            status: 'approved',
            business_name: '',
            business_information: ''
        };
    }
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    editingId.value = null;
    form.value = {
        name: '',
        email: '',
        password: '',
        phone_number: '',
        role: 'user',
        status: 'approved',
        business_name: '',
        business_information: ''
    };
}

async function saveUser() {
    saving.value = true;
    try {
        if (editingId.value) {
            await axios.put(`/api/v1/admin/users/${editingId.value}`, form.value);
            success('Data pengguna berhasil diperbarui!');
        } else {
            await axios.post('/api/v1/admin/users', form.value);
            success('Pengguna baru berhasil ditambahkan!');
        }
        closeModal();
        fetchUsers();
    } catch (e) {
        showError(e.response?.data?.message || 'Gagal menyimpan data pengguna.');
    } finally {
        saving.value = false;
    }
}

async function updateUserStatus(row, newStatus) {
    try {
        await axios.put(`/api/v1/admin/users/${row.id}/status`, { status: newStatus });
        success(`Status akun ${row.name} diubah menjadi ${newStatus}!`);
        fetchUsers();
    } catch {
        showError('Gagal mengubah status akun.');
    }
}

async function deleteUser(row) {
    if (!confirm(`Hapus pengguna "${row.name}"? Tindakan ini tidak dapat dibatalkan.`)) return;
    try {
        await axios.delete(`/api/v1/admin/users/${row.id}`);
        success('Pengguna berhasil dihapus.');
        fetchUsers();
    } catch {
        showError('Gagal menghapus pengguna.');
    }
}

onMounted(() => fetchUsers());
</script>