<template>
    <CustomerLayout>
        <div v-if="loading" class="max-w-6xl mx-auto px-4 py-16 text-center text-ink/40">
            <div class="inline-block w-10 h-10 border-4 border-brass border-t-transparent rounded-full animate-spin mb-4" />
            <p>Memuat paket...</p>
        </div>

        <div v-else-if="!pkg" class="max-w-6xl mx-auto px-4 py-20 text-center text-ink/40">
            <p class="text-4xl mb-4">🔍</p>
            <p>Paket tidak ditemukan.</p>
            <RouterLink to="/packages" class="mt-4 inline-block text-brass hover:underline">Kembali ke daftar</RouterLink>
        </div>

        <div v-else>
            <!-- Gallery -->
            <div class="bg-indigo-dusk-900">
                <div class="max-w-6xl mx-auto px-4 py-6">
                    <div class="relative rounded-2xl overflow-hidden aspect-video bg-limestone-100">
                        <img v-if="activeImage" :src="activeImage" :alt="pkg.title"
                            class="w-full h-full object-cover" />
                    </div>
                    <div v-if="pkg.images?.length > 1" class="flex gap-2 mt-3 overflow-x-auto pb-2">
                        <button v-for="img in pkg.images" :key="img.id" @click="activeImage = img.url"
                            :class="['h-16 w-24 flex-shrink-0 rounded-lg overflow-hidden border-2 transition-all',
                                activeImage === img.url ? 'border-brass' : 'border-transparent opacity-60 hover:opacity-100']">
                            <img :src="img.url" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="max-w-6xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span :class="['px-3 py-1 rounded-full text-xs font-bold uppercase',
                                pkg.type === 'vip' ? 'bg-brass text-white' :
                                pkg.type === 'promo' ? 'bg-rust text-white' : 'bg-indigo-dusk text-white']">
                                {{ pkg.type }}
                            </span>
                            <span :class="['px-3 py-1 rounded-full text-xs font-bold',
                                pkg.status === 'approved' ? 'bg-sage-50 text-sage' : 'bg-rust-50 text-rust']">
                                {{ pkg.status === 'approved' ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-ink mb-2">{{ pkg.title }}</h1>
                        <div class="flex flex-wrap gap-4 text-sm text-ink/60">
                            <span>✈️ {{ pkg.airline?.nama }}</span>
                            <span>📅 {{ formatDate(pkg.departure_date) }}</span>
                            <span>⏳ {{ pkg.duration }} hari</span>
                            <span>💺 {{ pkg.available_seats }} tersisa</span>
                        </div>
                    </div>

                    <!-- Hotels -->
                    <div v-if="pkg.hotels?.length" class="bg-limestone rounded-2xl p-4">
                        <h3 class="font-semibold text-ink mb-3">🏨 Hotel</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div v-for="hotel in pkg.hotels" :key="hotel.id"
                                class="bg-white rounded-xl px-4 py-3">
                                <p class="font-medium text-ink text-sm">{{ hotel.nama }}</p>
                                <p class="text-xs text-ink/50">📍 {{ hotel.lokasi }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div v-if="pkg.details" class="bg-white rounded-2xl border border-limestone-100 p-4">
                        <h3 class="font-semibold text-ink mb-3">📋 Detail Paket</h3>
                        <div class="text-sm text-ink/70 leading-relaxed prose max-w-none" v-html="pkg.details" />
                    </div>

                    <!-- Requirement -->
                    <div v-if="pkg.requirement" class="bg-white rounded-2xl border border-limestone-100 p-4">
                        <h3 class="font-semibold text-ink mb-3">📄 Persyaratan</h3>
                        <div class="text-sm text-ink/70 leading-relaxed prose max-w-none" v-html="pkg.requirement" />
                    </div>
                </div>

                <!-- Booking Card (sticky) -->
                <div class="lg:sticky lg:top-24 h-fit">
                    <div class="bg-white rounded-2xl border border-limestone-100 shadow-xl p-6">
                        <p class="text-xs text-ink/40 mb-1">Harga per orang</p>
                        <p class="text-3xl font-bold text-brass mb-1">{{ formatRupiah(pkg.price) }}</p>
                        <p class="text-sm text-ink/50 mb-4">+ Booking fee: {{ formatRupiah(pkg.booking_fee) }}</p>

                        <div class="space-y-2 text-sm text-ink/70 mb-6 border-t border-limestone-100 pt-4">
                            <div class="flex justify-between"><span>Maskapai</span><span class="font-medium">{{ pkg.airline?.nama }}</span></div>
                            <div class="flex justify-between"><span>Rute</span><span class="font-medium text-right max-w-[60%]">{{ pkg.route?.name }}</span></div>
                            <div class="flex justify-between"><span>Kapasitas</span><span class="font-medium">{{ pkg.capacity }} orang</span></div>
                            <div class="flex justify-between"><span>Sisa Kursi</span>
                                <span :class="['font-bold', pkg.available_seats < 5 ? 'text-rust' : 'text-sage']">
                                    {{ pkg.available_seats }} kursi
                                </span>
                            </div>
                        </div>

                        <RouterLink v-if="pkg.available_seats > 0 && pkg.status === 'approved'"
                            :to="{ name: 'booking', params: { slug: pkg.slug } }"
                            class="block w-full bg-brass hover:bg-brass-600 text-white text-center py-3.5 rounded-xl font-semibold transition-colors">
                            🕌 Pesan Sekarang
                        </RouterLink>
                        <div v-else class="w-full bg-limestone text-ink/40 text-center py-3.5 rounded-xl font-medium">
                            Tidak Tersedia
                        </div>

                        <p class="text-xs text-center text-ink/40 mt-3">
                            Oleh: {{ pkg.merchant?.business_name || pkg.merchant?.name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import CustomerLayout from '../../layouts/CustomerLayout.vue';
import { useFormatCurrency } from '../../composables/useFormatCurrency';
import axios from 'axios';

const route = useRoute();
const { formatRupiah } = useFormatCurrency();
const pkg = ref(null);
const loading = ref(true);
const activeImage = ref(null);

/** Dynamically set Open Graph & title meta tags for SEO */
function setOpenGraphTags(data) {
    const primaryImage = data.images?.find(i => i.is_primary)?.url || data.images?.[0]?.url || '';
    const description = `Paket Umroh ${data.type?.toUpperCase()} - ${data.duration} Hari. Keberangkatan: ${formatDate(data.departure_date)}. Harga mulai ${formatRupiah(data.price)}. Sisa ${data.available_seats} kursi.`;
    const url = window.location.href;

    document.title = `${data.title} | Go Umroh`;

    const metas = [
        { property: 'og:title', content: data.title },
        { property: 'og:description', content: description },
        { property: 'og:image', content: primaryImage },
        { property: 'og:url', content: url },
        { property: 'og:type', content: 'product' },
        { property: 'og:site_name', content: 'Go Umroh' },
        { name: 'description', content: description },
        { name: 'twitter:card', content: 'summary_large_image' },
        { name: 'twitter:title', content: data.title },
        { name: 'twitter:description', content: description },
        { name: 'twitter:image', content: primaryImage },
    ];

    metas.forEach(({ property, name, content }) => {
        const selector = property
            ? `meta[property="${property}"]`
            : `meta[name="${name}"]`;
        let el = document.querySelector(selector);
        if (!el) {
            el = document.createElement('meta');
            if (property) el.setAttribute('property', property);
            if (name) el.setAttribute('name', name);
            document.head.appendChild(el);
        }
        el.setAttribute('content', content);
    });
}

/** Remove dynamic OG tags when navigating away */
function removeOpenGraphTags() {
    document.title = 'Go Umroh — Marketplace Umroh Terpercaya';
    ['og:title', 'og:description', 'og:image', 'og:url', 'og:type'].forEach(prop => {
        document.querySelector(`meta[property="${prop}"]`)?.remove();
    });
}

onMounted(async () => {
    try {
        const { data } = await axios.get(`/api/v1/packages/${route.params.slug}`);
        pkg.value = data.data;
        activeImage.value = pkg.value.images?.find(i => i.is_primary)?.url || pkg.value.images?.[0]?.url;
        setOpenGraphTags(pkg.value);
    } finally {
        loading.value = false;
    }
});

onUnmounted(() => {
    removeOpenGraphTags();
});

function formatDate(d) {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}
</script>