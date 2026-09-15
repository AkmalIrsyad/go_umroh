<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink">Manajemen Tanya Jawab (FAQ)</h2>
                <p class="text-xs text-ink/60 mt-0.5">Kelola kategori FAQ dan daftar pertanyaan umum seputar pemesanan paket umrah.</p>
            </div>
            <button 
                @click="openGroupModal()" 
                class="inline-flex items-center gap-2 bg-brass hover:bg-brass-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Tambah Kategori FAQ
            </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-brass border-t-transparent"></div>
            <p class="text-xs text-ink/50 mt-2">Memuat daftar FAQ...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="groups.length === 0" class="bg-white rounded-2xl border border-limestone-100 p-12 text-center shadow-sm">
            <div class="w-16 h-16 bg-brass/10 text-brass rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl font-bold">
                ❓
            </div>
            <h3 class="text-base font-bold text-ink">Belum Ada Kategori FAQ</h3>
            <p class="text-xs text-ink/50 mt-1 mb-4">Buat kategori FAQ pertama untuk mengelompokkan pertanyaan umum.</p>
            <button @click="openGroupModal()" class="px-4 py-2 bg-brass text-white rounded-xl text-xs font-bold hover:bg-brass-600 transition-colors">
                + Tambah Kategori
            </button>
        </div>

        <!-- Group List with Items -->
        <div v-else class="space-y-4">
            <div 
                v-for="grp in groups" 
                :key="grp.id" 
                class="bg-white rounded-2xl border border-limestone-100 overflow-hidden shadow-sm transition-all"
            >
                <!-- Group Header -->
                <div class="bg-limestone-50/70 p-4 md:p-5 border-b border-limestone-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-brass text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            ❓
                        </span>
                        <div>
                            <h3 class="font-bold text-ink text-sm md:text-base">{{ grp.title }}</h3>
                            <p class="text-xs text-ink/50">{{ grp.faqs?.length || 0 }} pertanyaan terdaftar</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button 
                            @click="openFaqModal(grp)" 
                            class="px-3 py-1.5 bg-brass/10 hover:bg-brass text-brass hover:text-white rounded-lg text-xs font-bold transition-all inline-flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Pertanyaan
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

                <!-- Group FAQ Items List -->
                <div class="p-4 md:p-5">
                    <div v-if="!grp.faqs || grp.faqs.length === 0" class="text-center py-6 text-xs text-ink/40">
                        Belum ada pertanyaan pada kategori ini. Klik "Tambah Pertanyaan" di atas.
                    </div>
                    <div v-else class="space-y-3">
                        <div 
                            v-for="(faq, idx) in grp.faqs" 
                            :key="faq.id" 
                            class="p-4 rounded-xl border border-limestone-100 bg-white hover:border-brass/30 transition-all space-y-2"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 rounded-md bg-limestone text-ink/70 flex items-center justify-center text-[11px] font-bold shrink-0">
                                        Q{{ idx + 1 }}
                                    </span>
                                    <h4 class="font-bold text-xs md:text-sm text-ink truncate">{{ faq.title }}</h4>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button 
                                        @click="openFaqModal(grp, faq)" 
                                        class="px-2.5 py-1 bg-limestone hover:bg-limestone-100 text-ink text-[11px] font-bold rounded-lg transition-colors"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        @click="deleteFaq(faq)" 
                                        class="p-1 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus FAQ"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="text-xs text-ink/70 leading-relaxed whitespace-pre-line pl-7">
                                {{ faq.content }}
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
                        {{ editingGroupId ? 'Edit Kategori FAQ' : 'Tambah Kategori FAQ' }}
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
                            placeholder="Contoh: Pendaftaran & Syarat Dokumen"
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

        <!-- Modal Form FAQ Item -->
        <div v-if="showFaqModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-limestone-100 animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between pb-4 border-b border-limestone-100 mb-5">
                    <div>
                        <h3 class="text-base font-bold text-ink">
                            {{ editingFaqId ? 'Edit Tanya Jawab' : 'Tambah Tanya Jawab Baru' }}
                        </h3>
                        <p class="text-xs text-brass font-medium mt-0.5">Kategori: {{ targetGroup?.title }}</p>
                    </div>
                    <button @click="showFaqModal = false" class="text-ink/40 hover:text-ink text-lg leading-none">&times;</button>
                </div>

                <form @submit.prevent="saveFaq" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Pertanyaan <span class="text-rust">*</span></label>
                        <input 
                            v-model="faqForm.title" 
                            type="text" 
                            required 
                            placeholder="Contoh: Berapa lama masa berlaku paspor yang dibutuhkan?"
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1.5">Jawaban Lengkap <span class="text-rust">*</span></label>
                        <textarea 
                            v-model="faqForm.content" 
                            rows="5" 
                            required 
                            placeholder="Tuliskan jawaban yang jelas dan informatif bagi calon jamaah..."
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl p-4 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all resize-y"
                        />
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-limestone-100">
                        <button type="button" @click="showFaqModal = false" class="px-4 py-2 border border-limestone-100 rounded-xl text-xs font-bold text-ink/70 hover:bg-limestone transition-colors">Batal</button>
                        <button type="submit" :disabled="saving" class="px-5 py-2 bg-brass hover:bg-brass-600 text-white rounded-xl text-xs font-bold transition-all disabled:opacity-50">
                            {{ saving ? 'Menyimpan...' : 'Simpan FAQ' }}
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

const showFaqModal = ref(false);
const editingFaqId = ref(null);
const targetGroup = ref(null);
const faqForm = ref({ title: '', content: '' });

async function fetchGroups() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/admin/faq-groups');
        groups.value = data.data;
    } catch {
        showError('Gagal memuat kategori FAQ.');
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
            await axios.put(`/api/v1/admin/faq-groups/${editingGroupId.value}`, groupForm.value);
            success('Kategori FAQ berhasil diperbarui!');
        } else {
            await axios.post('/api/v1/admin/faq-groups', groupForm.value);
            success('Kategori FAQ baru berhasil ditambahkan!');
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
    if (!confirm(`Hapus kategori FAQ "${grp.title}" beserta seluruh pertanyaannya?`)) return;
    try {
        await axios.delete(`/api/v1/admin/faq-groups/${grp.id}`);
        success('Kategori FAQ berhasil dihapus.');
        fetchGroups();
    } catch {
        showError('Gagal menghapus kategori.');
    }
}

function openFaqModal(grp, faq = null) {
    targetGroup.value = grp;
    if (faq) {
        editingFaqId.value = faq.id;
        faqForm.value = { title: faq.title, content: faq.content };
    } else {
        editingFaqId.value = null;
        faqForm.value = { title: '', content: '' };
    }
    showFaqModal.value = true;
}

async function saveFaq() {
    saving.value = true;
    try {
        if (editingFaqId.value) {
            await axios.put(`/api/v1/admin/faqs/${editingFaqId.value}`, faqForm.value);
            success('Pertanyaan FAQ berhasil diperbarui!');
        } else {
            await axios.post(`/api/v1/admin/faq-groups/${targetGroup.value.id}/faqs`, faqForm.value);
            success('Pertanyaan FAQ baru berhasil ditambahkan!');
        }
        showFaqModal.value = false;
        fetchGroups();
    } catch (e) {
        showError(e.response?.data?.message || 'Gagal menyimpan FAQ.');
    } finally {
        saving.value = false;
    }
}

async function deleteFaq(faq) {
    if (!confirm(`Hapus pertanyaan "${faq.title}"?`)) return;
    try {
        await axios.delete(`/api/v1/admin/faqs/${faq.id}`);
        success('Pertanyaan FAQ berhasil dihapus.');
        fetchGroups();
    } catch {
        showError('Gagal menghapus FAQ.');
    }
}

onMounted(() => fetchGroups());
</script>