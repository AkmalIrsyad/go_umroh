<template>
    <CustomerLayout>
        <div class="max-w-3xl mx-auto px-4 py-10">
            <RouterLink to="/orders" class="text-sm text-ink/50 hover:text-brass mb-6 inline-flex items-center gap-1">← Riwayat Pesanan</RouterLink>
            <div v-if="loading" class="text-center py-20"><div class="inline-block w-10 h-10 border-4 border-brass border-t-transparent rounded-full animate-spin" /></div>
            <div v-else-if="order" class="space-y-6">
                <div class="bg-white rounded-2xl border border-limestone-100 p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold text-ink">{{ order.order_code }}</p>
                            <p class="text-sm text-ink/50 mt-0.5">{{ order.package?.title }}</p>
                        </div>
                        <span :class="['px-3 py-1 rounded-full text-sm font-semibold',
                            order.status === 'complete' ? 'bg-sage-100 text-sage' :
                            order.status === 'waiting_confirmation' ? 'bg-rust-100 text-rust' : 'bg-limestone text-ink/60']">
                            {{ order.status?.replace(/_/g, ' ') }}
                        </span>
                    </div>
                    <div class="mt-4 pt-4 border-t border-limestone-100 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-ink/50">Total</span><span class="font-bold text-brass text-base">{{ formatRupiah(order.total) }}</span></div>
                        <div class="flex justify-between"><span class="text-ink/50">Kode Unik</span><span>{{ order.kode_unik }}</span></div>
                    </div>
                </div>

                <!-- Participants -->
                <div class="bg-white rounded-2xl border border-limestone-100 p-6">
                    <h3 class="font-semibold mb-4">Data Jamaah</h3>
                    <div v-for="p in order.participants" :key="p.id" class="flex justify-between py-2 text-sm border-b border-limestone-100 last:border-0">
                        <span>{{ p.nama }}</span><span class="text-ink/50 capitalize">{{ p.tipe }}</span>
                    </div>
                </div>

                <!-- Upload Payment -->
                <div v-if="order.status === 'waiting_payment'" class="bg-white rounded-2xl border border-limestone-100 p-6">
                    <h3 class="font-semibold mb-2">Upload Bukti Pembayaran</h3>
                    <p class="text-sm text-ink/60 mb-4">Transfer sejumlah <span class="font-bold text-brass">{{ formatRupiah(order.total) }}</span> ke rekening yang telah ditentukan, lalu upload bukti transfer.</p>
                    <form @submit.prevent="uploadPayment" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1.5">Bank Tujuan</label>
                                <select v-model.number="payForm.bank_id" required class="w-full border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none">
                                    <option value="">Pilih Bank</option>
                                    <option v-for="b in banks" :key="b.id" :value="b.id">{{ b.nama }} - {{ b.nomor_rekening }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1.5">Nominal Transfer</label>
                                <input v-model.number="payForm.nominal" type="number" required class="w-full border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1.5">Bank Pengirim</label>
                                <input v-model="payForm.bank_pengirim" required class="w-full border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1.5">No. Rekening Pengirim</label>
                                <input v-model="payForm.nomor_rekening_pengirim" required class="w-full border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Bukti Transfer</label>
                            <input ref="fileInput" type="file" accept="image/*" required @change="onFileChange" class="w-full text-sm text-ink/60 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-limestone file:text-ink file:text-sm" />
                        </div>
                        <button type="submit" :disabled="uploading" class="w-full bg-brass hover:bg-brass-600 text-white py-3 rounded-xl font-semibold text-sm disabled:opacity-50">
                            {{ uploading ? 'Mengupload...' : 'Kirim Bukti Pembayaran' }}
                        </button>
                    </form>
                </div>

                <!-- Payment Status -->
                <div v-if="order.payment" class="bg-limestone rounded-2xl border border-limestone-100 p-5">
                    <h3 class="font-semibold mb-2">Status Pembayaran</h3>
                    <span :class="['px-3 py-1 rounded-full text-sm font-semibold', order.payment.status === 'approved' ? 'bg-sage-100 text-sage' : 'bg-rust-100 text-rust']">
                        {{ order.payment.status === 'approved' ? 'Pembayaran Dikonfirmasi ✅' : 'Menunggu Konfirmasi' }}
                    </span>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue'; import { RouterLink, useRoute } from 'vue-router'; import CustomerLayout from '../../layouts/CustomerLayout.vue'; import { useFormatCurrency } from '../../composables/useFormatCurrency'; import { useToast } from '../../composables/useToast'; import axios from 'axios';
const route = useRoute(); const { formatRupiah } = useFormatCurrency(); const { success, error: showError } = useToast();
const order = ref(null); const loading = ref(true); const uploading = ref(false); const banks = ref([]);
const fileInput = ref(null); const payFile = ref(null);
const payForm = ref({ bank_id: '', nominal: 0, bank_pengirim: '', nomor_rekening_pengirim: '' });
function onFileChange(e) { payFile.value = e.target.files[0]; }
onMounted(async () => {
    try {
        const [oRes, bRes] = await Promise.all([axios.get(`/api/v1/orders/${route.params.id}`), axios.get('/api/v1/banks')]);
        order.value = oRes.data.data; banks.value = bRes.data.data;
    } finally { loading.value = false; }
});
async function uploadPayment() {
    uploading.value = true;
    try {
        const fd = new FormData();
        fd.append('bank_id', payForm.value.bank_id); fd.append('nominal', payForm.value.nominal);
        fd.append('bank_pengirim', payForm.value.bank_pengirim); fd.append('nomor_rekening_pengirim', payForm.value.nomor_rekening_pengirim);
        if (payFile.value) fd.append('bukti_transfer', payFile.value);
        await axios.post(`/api/v1/orders/${order.value.id}/payment`, fd);
        success('Bukti pembayaran berhasil dikirim!');
        const { data } = await axios.get(`/api/v1/orders/${route.params.id}`); order.value = data.data;
    } catch (e) { showError(e.response?.data?.message || 'Gagal upload.'); }
    finally { uploading.value = false; }
}
</script>