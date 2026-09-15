<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink">Manajemen Panduan Manasik Umrah</h2>
                <p class="text-xs text-ink/60 mt-0.5">Kelola kategori panduan, rukun umrah, doa-doa, dan materi manasik untuk jamaah.</p>
            </div>
            <button 
                @click="openGroupModal()" 
                class="inline-flex items-center gap-2 bg-brass hover:bg-brass-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Tambah Kategori Manasik
            </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-brass border-t-transparent"></div>
            <p class="text-xs text-ink/50 mt-2">Memuat panduan manasik...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="groups.length === 0" class="bg-white rounded-2xl border border-limestone-100 p-12 text-center shadow-sm">
            <div class="w-16 h-16 bg-brass/10 text-brass rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl font-bold">
                📖
            </div>
            <h3 class="text-base font-bold text-ink">Belum Ada Panduan Manasik</h3>
            <p class="text-xs text-ink/50 mt-1 mb-4">Buat kategori manasik pertama untuk mengelompokkan materi panduan ibadah.</p>
            <button @click="openGroupModal()" class="px-4 py-2 bg-brass text-white rounded-xl text-xs font-bold hover:bg-brass-600 transition-colors">
                + Tambah Kategori
            </button>
        </div>

        <!-- Group List with Items Accordion / Cards -->
        <div v-else class="space-y-4">
            <div 
                v-for="grp in groups" 
                :key="grp.id" 
                class="bg-white rounded-2xl border border-limestone-100 overflow-hidden shadow-sm transition-all"
            >
                <!-- Group Header -->
                <div class="bg-limestone-50/70 p-4 md:p-5 border-b border-limestone-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-indigo-dusk text-white flex items-center justify-center font-bold text-xs">
                            🕌
                        </span>
                        <div>
                            <h3 class="font-bold text-ink text-sm md:text-base">{{ grp.title }}</h3>
                            <p class="text-xs text-ink/50">{{ grp.manasiks?.length || 0 }} materi panduan</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button 
                            @click="openItemModal(grp)" 
                            class="px-3 py-1.5 bg-brass/10 hover:bg-brass text-brass hover:text-white rounded-lg text-xs font-bold transition-all inline-flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Materi
                        </button>
                        <button 
                            @click="openGroupModal(grp)" 
                            class="px-2.5 py-1.5 bg-white border border-limestone-100 hover:bg-limestone-50 text-ink text-xs font-semibold rounded-lg transition-colors"
                        >
                            Edit
                        </button>
                        <button 
                            @click="deleteGroup(grp)" 
                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                            title="Hapus Kategori"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Group Manasik Items List -->
                <div class="p-4 md:p-5">
                    <div v-if="!grp.manasiks || grp.manasiks.length === 0" class="text-center py-6 text-xs text-ink/40">
                        Belum ada materi pada kategori ini. Klik "Tambah Materi" di atas.
                    </div>
                    <div v-else class="space-y-3">
                        <div 
                            v-for="(item, idx) in grp.manasiks" 
                            :key="item.id" 
                            class="p-4 rounded-xl border border-limestone-100 bg-white hover:border-brass/30 transition-all space-y-2"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 rounded-md bg-limestone text-ink/70 flex items-center justify-center text-[11px] font-bold shrink-0">
                                        {{ idx + 1 }}
                                    </span>
                                    <h4 class="font-bold text-xs md:text-sm text-ink truncate">{{ item.title }}</h4>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button 
                                        @click="openItemModal(grp, item)" 
                                        class="px-2.5 py-1 bg-limestone hover:bg-limestone-100 text-ink text-[11px] font-bold rounded-lg transition-colors"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        @click="deleteItem(item)" 
                                        class="p-1 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus Materi"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="text-xs text-ink/70 leading-relaxed whitespace-pre-line pl-7">
                                {{ item.content }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form Category / Group -->
        <div v-if="showGroupModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-limestone-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between pb-4 border-b border-limestone-100 mb-5">
                    <h3 class="text-base font-bold text-ink">
                        {{ editingGroupId ? 'Edit Kategori Manasik' : 'Tambah Kategori Manasik' }}
                    </h3>
                    <button @click="showGroupModal = false" class="text-ink/40 hover:text-ink text-lg leading-none">&times;</button>
                </div>

                <form @submit.prevent="saveGroup" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Nama Kategori <span class="text-rust">*</span></label>
                        <input 
                            v-model="groupForm.title" 
                            type="text" 
                            required 
                            placeholder="Contoh: Rukun Umrah & Tata Cara Ihram"
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        />
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-limestone-100">
                        <button type="button" @click="showGroupModal = false" class="px-4 py-2 border border-limestone-100 rounded-xl text-xs font-bold text-ink/70 hover:bg-limestone transition-colors">Batal</button>
                        <button type="submit" :disabled="saving" class="px-5 py-2 bg-brass hover:bg-brass-600 text-white rounded-xl text-xs font-bold transition-all disabled:opacity-50">
                            {{ saving ? 'Menyimpan...' : 'Simpan Kategori' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Form Manasik Item / Materi -->
        <div v-if="showItemModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-limestone-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between pb-4 border-b border-limestone-100 mb-5">
                    <div>
                        <h3 class="text-base font-bold text-ink">
                            {{ editingItemId ? 'Edit Materi Panduan' : 'Tambah Materi Panduan' }}
                        </h3>
                        <p class="text-xs text-brass font-medium mt-0.5">Kategori: {{ targetGroup?.title }}</p>
                    </div>
                    <button @click="showItemModal = false" class="text-ink/40 hover:text-ink text-lg leading-none">&times;</button>
                </div>

                <form @submit.prevent="saveItem" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Judul Materi / Langkah <span class="text-rust">*</span></label>
                        <input 
                            v-model="itemForm.title" 
                            type="text" 
                            required 
                            placeholder="Contoh: Niat Ihram di Miqat dan Larangan Ihram"
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Konten Penjelasan & Bacaan Doa <span class="text-rust">*</span></label>
                        <textarea 
                            v-model="itemForm.content" 
                            rows="6" 
                            required 
                            placeholder="Tuliskan panduan tata cara secara urut, lafadz niat / doa dalam tulisan arab / latin beserta artinya..."
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl p-4 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all resize-y"
                        />
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-limestone-100">
                        <button type="button" @click="showItemModal = false" class="px-4 py-2 border border-limestone-100 rounded-xl text-xs font-bold text-ink/70 hover:bg-limestone transition-colors">Batal</button>
                        <button type="submit" :disabled="saving" class="px-5 py-2 bg-brass hover:bg-brass-600 text-white rounded-xl text-xs font-bold transition-all disabled:opacity-50">
                            {{ saving ? 'Menyimpan...' : 'Simpan Materi' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from '../../composables/useToast';
import axios from 'axios';

const { success, error: showError } = useToast();
const groups = ref([]);
const loading = ref(true);
const saving = ref(false);

const showGroupModal = ref(false);
const editingGroupId = ref(null);
const groupForm = ref({ title: '' });

const showItemModal = ref(false);
const editingItemId = ref(null);
const targetGroup = ref(null);
const itemForm = ref({ title: '', content: '' });

async function fetchGroups() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/admin/manasik-groups');
        groups.value = data.data;
    } catch {
        showError('Gagal memuat grup manasik.');
    } finally {
        loading.value = false;
    }
}

function openGroupModal(grp = null) {
    if (grp) {
        editingGroupId.value = grp.id;
        groupForm.value = { title: grp.title };
    } else {
        editingGroupId.value = null;
        groupForm.value = { title: '' };
    }
    showGroupModal.value = true;
}

async function saveGroup() {
    saving.value = true;
    try {
        if (editingGroupId.value) {
            await axios.put(`/api/v1/admin/manasik-groups/${editingGroupId.value}`, groupForm.value);
            success('Kategori manasik berhasil diperbarui!');
        } else {
            await axios.post('/api/v1/admin/manasik-groups', groupForm.value);
            success('Kategori manasik baru berhasil ditambahkan!');
        }
        showGroupModal.value = false;
        fetchGroups();
    } catch (e) {
        showError(e.response?.data?.message || 'Gagal menyimpan kategori.');
    } finally {
        saving.value = false;
    }
}

async function deleteGroup(grp) {
    if (!confirm(`Hapus kategori manasik "${grp.title}" beserta seluruh materinya?`)) return;
    try {
        await axios.delete(`/api/v1/admin/manasik-groups/${grp.id}`);
        success('Kategori manasik berhasil dihapus.');
        fetchGroups();
    } catch {
        showError('Gagal menghapus kategori.');
    }
}

function openItemModal(grp, item = null) {
    targetGroup.value = grp;
    if (item) {
        editingItemId.value = item.id;
        itemForm.value = { title: item.title, content: item.content };
    } else {
        editingItemId.value = null;
        itemForm.value = { title: '', content: '' };
    }
    showItemModal.value = true;
}

async function saveItem() {
    saving.value = true;
    try {
        if (editingItemId.value) {
            await axios.put(`/api/v1/admin/manasiks/${editingItemId.value}`, itemForm.value);
            success('Materi manasik berhasil diperbarui!');
        } else {
            await axios.post(`/api/v1/admin/manasik-groups/${targetGroup.value.id}/manasiks`, itemForm.value);
            success('Materi manasik baru berhasil ditambahkan!');
        }
        showItemModal.value = false;
        fetchGroups();
    } catch (e) {
        showError(e.response?.data?.message || 'Gagal menyimpan materi.');
    } finally {
        saving.value = false;
    }
}

async function deleteItem(item) {
    if (!confirm(`Hapus materi "${item.title}"?`)) return;
    try {
        await axios.delete(`/api/v1/admin/manasiks/${item.id}`);
        success('Materi manasik berhasil dihapus.');
        fetchGroups();
    } catch {
        showError('Gagal menghapus materi.');
    }
}

onMounted(() => fetchGroups());
</script>