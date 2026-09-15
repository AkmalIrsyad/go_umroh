<template>
    <CustomerLayout>
        <div class="max-w-xl mx-auto px-4 py-10">
            <h1 class="text-2xl font-bold text-ink mb-6">Profil Saya</h1>
            <div class="bg-white rounded-2xl border border-limestone-100 p-6">
                <form @submit.prevent="save" class="space-y-4">
                    <div><label class="block text-sm font-medium mb-1.5">Nama</label><input v-model="form.name" class="w-full border border-limestone-100 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30" /></div>
                    <div><label class="block text-sm font-medium mb-1.5">No. Telepon</label><input v-model="form.phone_number" class="w-full border border-limestone-100 rounded-xl px-3 py-2.5 text-sm focus:outline-none" /></div>
                    <div><label class="block text-sm font-medium mb-1.5">Alamat</label><textarea v-model="form.address" rows="3" class="w-full border border-limestone-100 rounded-xl px-3 py-2.5 text-sm focus:outline-none resize-none" /></div>
                    <button type="submit" :disabled="saving" class="w-full bg-indigo-dusk text-white py-3 rounded-xl font-medium text-sm disabled:opacity-50">{{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}</button>
                </form>
            </div>
        </div>
    </CustomerLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue'; import CustomerLayout from '../../layouts/CustomerLayout.vue'; import { useAuthStore } from '../../stores/auth'; import { useToast } from '../../composables/useToast'; import axios from 'axios';
const authStore = useAuthStore(); const { success } = useToast(); const saving = ref(false);
const form = ref({ name: authStore.user?.name || '', phone_number: authStore.user?.phone_number || '', address: authStore.user?.address || '' });
async function save() { saving.value = true; try { await axios.put('/api/v1/me', form.value); await authStore.fetchMe(); success('Profil disimpan!'); } finally { saving.value = false; } }
</script>