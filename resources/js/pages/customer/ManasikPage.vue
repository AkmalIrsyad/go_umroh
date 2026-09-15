<template>
    <CustomerLayout>
        <div class="max-w-3xl mx-auto px-4 py-10">
            <h1 class="text-2xl md:text-3xl font-bold text-ink mb-2">Panduan Manasik Umrah</h1>
            <p class="text-ink/50 mb-8">Pelajari tata cara dan tuntunan ibadah umrah yang benar.</p>
            <div v-if="loading" class="space-y-4"><div v-for="i in 3" :key="i" class="bg-white rounded-2xl border border-limestone-100 p-5 animate-pulse h-16" /></div>
            <div v-else class="space-y-6">
                <div v-for="group in manasikGroups" :key="group.id" class="bg-white rounded-2xl border border-limestone-100 overflow-hidden">
                    <div class="bg-indigo-dusk px-5 py-4">
                        <h2 class="font-semibold text-white">{{ group.title }}</h2>
                    </div>
                    <div class="divide-y divide-limestone-100">
                        <div v-for="item in group.manasiks" :key="item.id" class="px-5 py-4">
                            <p class="font-medium text-ink mb-2">{{ item.title }}</p>
                            <p class="text-sm text-ink/60 leading-relaxed">{{ item.content }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue'; import CustomerLayout from '../../layouts/CustomerLayout.vue'; import axios from 'axios';
const manasikGroups = ref([]); const loading = ref(true);
onMounted(async () => { try { const { data } = await axios.get('/api/v1/manasiks'); manasikGroups.value = data.data; } finally { loading.value = false; } });
</script>