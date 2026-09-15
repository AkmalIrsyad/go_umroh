<template>
    <CustomerLayout>
        <div class="max-w-7xl mx-auto px-4 py-10">
            <h1 class="text-2xl md:text-3xl font-bold text-ink mb-2">Daftar Paket Umrah</h1>
            <p class="text-ink/50 mb-8">Temukan paket umrah terbaik sesuai kebutuhan Anda</p>

            <!-- Filter panel -->
            <div class="bg-white rounded-2xl border border-limestone-100 p-4 mb-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <input v-model="filters.search" @input="debouncedFetch" type="text" placeholder="Cari paket..."
                        class="border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30 lg:col-span-2" />
                    <select v-model="filters.type" @change="fetchPackages" class="border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none">
                        <option value="">Semua Tipe</option>
                        <option value="regular">Regular</option>
                        <option value="vip">VIP</option>
                        <option value="promo">Promo</option>
                    </select>
                    <input v-model="filters.min_price" @input="debouncedFetch" type="number" placeholder="Harga min"
                        class="border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none" />
                    <input v-model="filters.max_price" @input="debouncedFetch" type="number" placeholder="Harga maks"
                        class="border border-limestone-100 rounded-xl px-3 py-2 text-sm focus:outline-none" />
                </div>
            </div>

            <!-- Results -->
            <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="i in 9" :key="i" class="bg-white rounded-2xl border border-limestone-100 animate-pulse">
                    <div class="aspect-[16/9] bg-limestone rounded-t-2xl" />
                    <div class="p-4 space-y-3">
                        <div class="h-4 bg-limestone rounded w-3/4" />
                        <div class="h-3 bg-limestone rounded w-1/2" />
                        <div class="h-6 bg-limestone rounded w-1/3" />
                    </div>
                </div>
            </div>
            <div v-else-if="!packages.length" class="text-center py-20 text-ink/40">
                <p class="text-4xl mb-3">🔍</p>
                <p>Tidak ada paket ditemukan.</p>
            </div>
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <PackageCard v-for="pkg in packages" :key="pkg.id" :pkg="pkg" />
            </div>

            <!-- Pagination -->
            <div v-if="meta && meta.last_page > 1" class="mt-8 flex justify-center gap-2">
                <button v-for="p in meta.last_page" :key="p" @click="changePage(p)"
                    :class="['px-4 py-2 rounded-xl text-sm font-medium transition-colors',
                        p === meta.current_page ? 'bg-indigo-dusk text-white' : 'bg-white hover:bg-limestone border border-limestone-100']">
                    {{ p }}
                </button>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import CustomerLayout from '../../layouts/CustomerLayout.vue';
import PackageCard from '../../components/PackageCard.vue';
import axios from 'axios';

const route = useRoute();
const packages = ref([]);
const meta = ref(null);
const loading = ref(true);
const filters = ref({ search: '', type: '', min_price: '', max_price: '', page: 1 });
let searchTimer = null;

async function fetchPackages() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/v1/packages', { params: filters.value });
        packages.value = data.data;
        meta.value = data.meta;
    } finally {
        loading.value = false;
    }
}

function debouncedFetch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(fetchPackages, 400);
}

function changePage(page) {
    filters.value.page = page;
    fetchPackages();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

onMounted(() => {
    if (route.query.search) filters.value.search = route.query.search;
    if (route.query.type) filters.value.type = route.query.type;
    fetchPackages();
});
</script>