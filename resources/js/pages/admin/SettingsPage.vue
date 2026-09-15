<template>
    <div><h2 class="text-xl font-bold text-ink mb-6">Pengaturan Aplikasi</h2>
    <div class="bg-white rounded-2xl border border-limestone-100 p-6 max-w-lg">
        <div v-if="loading" class="text-center py-8">Loading...</div>
        <form v-else @submit.prevent="save" class="space-y-4">
            <div v-for="setting in settings" :key="setting.key">
                <label class="block text-sm font-medium text-ink mb-1.5">{{ setting.key }}</label>
                <input v-model="setting.value" class="w-full border border-limestone-100 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30" />
            </div>
            <button type="submit" :disabled="saving" class="w-full bg-indigo-dusk text-white py-3 rounded-xl font-medium text-sm disabled:opacity-50">{{ saving ? 'Menyimpan...' : 'Simpan Pengaturan' }}</button>
        </form>
    </div></div>
</template>
<script setup>
import { ref, onMounted } from 'vue'; import { useToast } from '../../composables/useToast'; import axios from 'axios';
const { success } = useToast(); const settings = ref([]); const loading = ref(true); const saving = ref(false);
async function fetch() { loading.value = true; try { const { data } = await axios.get('/api/v1/admin/settings'); settings.value = data.data; } finally { loading.value = false; } }
async function save() { saving.value = true; try { await axios.put('/api/v1/admin/settings', { settings: settings.value.map(s => ({ key: s.key, value: s.value })) }); success('Pengaturan disimpan!'); } finally { saving.value = false; } }
onMounted(() => fetch());
</script>