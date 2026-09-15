<template>
    <CustomerLayout>
        <div class="max-w-4xl mx-auto px-4 py-10">
            <h1 class="text-2xl font-bold text-ink mb-6">Form Pemesanan</h1>

            <div v-if="loading" class="text-center py-20"><div class="inline-block w-10 h-10 border-4 border-brass border-t-transparent rounded-full animate-spin" /></div>
            <div v-else-if="!pkg" class="text-center py-20 text-ink/40">Paket tidak ditemukan.</div>
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl border border-limestone-100 p-6">
                        <h2 class="font-semibold text-ink mb-4">Data Peserta</h2>
                        <div v-for="(p, i) in participants" :key="i" class="border border-limestone-100 rounded-xl p-4 mb-4">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-sm font-semibold">Peserta {{ i + 1 }}</h3>
                                <button v-if="i > 0" @click="removeParticipant(i)" class="text-red-400 hover:text-red-600 text-xs">Hapus</button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium mb-1">Nama Lengkap</label>
                                    <input v-model="p.nama" required class="w-full border border-limestone-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1">No. HP</label>
                                    <input v-model="p.nomor_ponsel" type="tel" class="w-full border border-limestone-100 rounded-lg px-3 py-2 text-sm focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1">Tipe</label>
                                    <select v-model="p.tipe" class="w-full border border-limestone-100 rounded-lg px-3 py-2 text-sm focus:outline-none">
                                        <option value="dewasa">Dewasa</option>
                                        <option value="anak">Anak</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button @click="addParticipant" :disabled="participants.length >= pkg.available_seats"
                            class="text-brass text-sm hover:underline disabled:opacity-50 disabled:no-underline">+ Tambah Peserta</button>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-white rounded-2xl border border-limestone-100 shadow-xl p-6 h-fit lg:sticky lg:top-24">
                    <h3 class="font-semibold mb-4">Ringkasan Pesanan</h3>
                    <p class="text-sm font-medium text-ink mb-3 line-clamp-2">{{ pkg.title }}</p>
                    <div class="space-y-2 text-sm text-ink/70 border-t border-limestone-100 pt-3">
                        <div class="flex justify-between"><span>{{ participants.length }}x Peserta</span><span>{{ formatRupiah(pkg.price * participants.length) }}</span></div>
                        <div class="flex justify-between"><span>Booking Fee</span><span>{{ formatRupiah(pkg.booking_fee) }}</span></div>
                        <div class="flex justify-between"><span>Kode Unik</span><span>~Rp 100-999</span></div>
                        <div class="flex justify-between font-bold text-ink border-t border-limestone-100 pt-2 mt-2">
                            <span>Estimasi Total</span><span class="text-brass">{{ formatRupiah(pkg.price * participants.length + Number(pkg.booking_fee)) }}</span>
                        </div>
                    </div>
                    <button @click="submitOrder" :disabled="submitting"
                        class="mt-5 w-full bg-brass hover:bg-brass-600 text-white py-3.5 rounded-xl font-semibold text-sm transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                        <div v-if="submitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                        {{ submitting ? 'Memproses...' : '🕌 Konfirmasi Pesanan' }}
                    </button>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue'; import { useRoute, useRouter } from 'vue-router'; import CustomerLayout from '../../layouts/CustomerLayout.vue'; import { useFormatCurrency } from '../../composables/useFormatCurrency'; import { useToast } from '../../composables/useToast'; import axios from 'axios';
const route = useRoute(); const router = useRouter(); const { formatRupiah } = useFormatCurrency(); const { success, error: showError } = useToast();
const pkg = ref(null); const loading = ref(true); const submitting = ref(false);
const participants = ref([{ nama: '', nomor_ponsel: '', tipe: 'dewasa' }]);
function addParticipant() { participants.value.push({ nama: '', nomor_ponsel: '', tipe: 'dewasa' }); }
function removeParticipant(i) { participants.value.splice(i, 1); }
onMounted(async () => { try { const { data } = await axios.get(`/api/v1/packages/${route.params.id}`); pkg.value = data.data; } finally { loading.value = false; } });
async function submitOrder() {
    submitting.value = true;
    try {
        const { data } = await axios.post('/api/v1/orders', { umrah_package_id: route.params.id, participants: participants.value });
        success('Pesanan berhasil dibuat!');
        router.push({ name: 'order.detail', params: { id: data.data.id } });
    } catch (e) { showError(e.response?.data?.message || 'Gagal membuat pesanan.'); }
    finally { submitting.value = false; }
}
</script>