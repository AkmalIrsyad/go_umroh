<template>
    <CustomerLayout>
        <div class="max-w-3xl mx-auto px-4 py-10">
            <h1 class="text-2xl md:text-3xl font-bold text-ink mb-2">Pertanyaan yang Sering Diajukan</h1>
            <p class="text-ink/50 mb-8">Temukan jawaban atas pertanyaan umum seputar umrah dan layanan kami.</p>
            <div v-if="loading" class="space-y-4"><div v-for="i in 3" :key="i" class="bg-white rounded-2xl border border-limestone-100 p-5 animate-pulse h-16" /></div>
            <div v-else class="space-y-4">
                <div v-for="group in faqGroups" :key="group.id" class="bg-white rounded-2xl border border-limestone-100 overflow-hidden">
                    <button @click="toggle(group.id)" class="w-full flex justify-between items-center px-5 py-4 hover:bg-limestone-50 transition-colors">
                        <h2 class="font-semibold text-ink text-left">{{ group.title }}</h2>
                        <svg :class="['w-5 h-5 text-ink/40 transition-transform', openGroups.includes(group.id) ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div v-if="openGroups.includes(group.id)" class="border-t border-limestone-100 divide-y divide-limestone-100">
                        <div v-for="faq in group.faqs" :key="faq.id" class="px-5 py-4">
                            <p class="font-medium text-ink mb-2">{{ faq.title }}</p>
                            <p class="text-sm text-ink/60 leading-relaxed">{{ faq.content }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue'; import CustomerLayout from '../../layouts/CustomerLayout.vue'; import axios from 'axios';
const faqGroups = ref([]); const loading = ref(true); const openGroups = ref([]);
function toggle(id) { openGroups.value.includes(id) ? openGroups.value = openGroups.value.filter(i => i !== id) : openGroups.value.push(id); }
onMounted(async () => { try { const { data } = await axios.get('/api/v1/faqs'); faqGroups.value = data.data; if (faqGroups.value[0]) openGroups.value.push(faqGroups.value[0].id); } finally { loading.value = false; } });
</script>