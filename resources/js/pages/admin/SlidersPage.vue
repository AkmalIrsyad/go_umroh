<template><div><h2 class="text-xl font-bold text-ink mb-6">Manajemen Slider</h2>
<div v-if="loading" class="text-center py-10">Loading...</div>
<div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div v-for="slider in sliders" :key="slider.id" class="bg-white rounded-2xl border border-limestone-100 overflow-hidden group relative">
        <img :src="slider.image" class="w-full aspect-video object-cover" />
        <button @click="del(slider)" class="absolute top-2 right-2 bg-red-500 text-white w-7 h-7 rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">✕</button>
    </div>
    <label class="bg-limestone rounded-2xl border-2 border-dashed border-limestone-100 aspect-video flex items-center justify-center cursor-pointer hover:border-brass/30 transition-colors">
        <input type="file" class="hidden" @change="upload" accept="image/*" />
        <span class="text-ink/30 text-4xl">+</span>
    </label>
</div></div></template>
<script setup>
import { ref, onMounted } from 'vue'; import { useToast } from '../../composables/useToast'; import axios from 'axios';
const { success } = useToast(); const sliders = ref([]); const loading = ref(true);
async function fetch() { loading.value = true; try { const { data } = await axios.get('/api/v1/sliders'); sliders.value = data.data; } finally { loading.value = false; } }
async function del(s) { if (!confirm('Hapus?')) return; await axios.delete(`/api/v1/admin/sliders/${s.id}`); success('Dihapus.'); fetch(); }
async function upload(e) { const f = e.target.files[0]; if (!f) return; const fd = new FormData(); fd.append('image', f); await axios.post('/api/v1/admin/sliders', fd); success('Slider ditambahkan!'); fetch(); }
onMounted(() => fetch());
</script>