<template>
    <RouterLink :to="{ name: 'package.detail', params: { slug: pkg.slug } }"
        class="group bg-white rounded-2xl border border-limestone-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
        <!-- Image -->
        <div class="relative aspect-[16/9] overflow-hidden bg-limestone-100">
            <img v-if="primaryImage" :src="primaryImage" :alt="pkg.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
            <div v-else class="w-full h-full flex items-center justify-center text-ink/20">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>

            <!-- Type badge -->
            <div :class="['absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide',
                pkg.type === 'vip' ? 'bg-brass text-white' :
                pkg.type === 'promo' ? 'bg-rust text-white' :
                'bg-indigo-dusk text-white']">
                {{ pkg.type }}
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 flex flex-col flex-1">
            <h3 class="font-semibold text-ink leading-snug mb-2 group-hover:text-brass transition-colors line-clamp-2">
                {{ pkg.title }}
            </h3>
            <div class="text-xs text-ink/50 space-y-1 mb-3">
                <div class="flex items-center gap-1.5">
                    <span>✈️</span> {{ pkg.airline?.nama ?? '-' }}
                </div>
                <div class="flex items-center gap-1.5">
                    <span>📅</span> {{ formatDate(pkg.departure_date) }} &bull; {{ pkg.duration }} hari
                </div>
                <div class="flex items-center gap-1.5">
                    <span>💺</span> {{ pkg.available_seats }} kursi tersisa
                </div>
            </div>
            <div class="mt-auto pt-3 border-t border-limestone-100 flex items-end justify-between">
                <div>
                    <p class="text-xs text-ink/40">Mulai dari</p>
                    <p class="text-lg font-bold text-brass">{{ formatRupiah(pkg.price) }}</p>
                </div>
                <span class="text-xs bg-limestone text-ink/60 px-2 py-1 rounded-lg">/ orang</span>
            </div>
        </div>
    </RouterLink>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import { useFormatCurrency } from '../composables/useFormatCurrency';

const props = defineProps({ pkg: { type: Object, required: true } });
const { formatRupiah } = useFormatCurrency();
const primaryImage = computed(() => props.pkg.images?.find(img => img.is_primary)?.url || props.pkg.images?.[0]?.url);

function formatDate(dateStr) {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>