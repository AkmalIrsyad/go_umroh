<template>
    <div class="bg-white rounded-2xl border border-limestone-100 overflow-hidden">
        <!-- Header with search -->
        <div class="px-4 py-3 border-b border-limestone-100 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
            <slot name="header-actions" />
            <input v-if="searchable" v-model="searchQuery" @input="onSearch"
                type="text" placeholder="Cari..."
                class="border border-limestone-100 rounded-lg px-3 py-2 text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-brass/30 bg-limestone-50" />
        </div>

        <!-- Table wrapper with overflow scroll indicator for mobile -->
        <div class="overflow-x-auto relative">
            <!-- Desktop Table -->
            <table class="w-full text-sm hidden md:table">
                <thead class="bg-limestone-50 text-ink/60 uppercase text-xs">
                    <tr>
                        <th v-for="col in columns" :key="col.key"
                            class="px-4 py-3 text-left font-semibold whitespace-nowrap">{{ col.label }}</th>
                        <th v-if="$slots.actions" class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-limestone-100">
                    <tr v-if="loading">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-8 text-center text-ink/40">
                            <div class="inline-block w-6 h-6 border-2 border-brass border-t-transparent rounded-full animate-spin" />
                        </td>
                    </tr>
                    <tr v-else-if="!data.length">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-8 text-center text-ink/40">
                            Tidak ada data.
                        </td>
                    </tr>
                    <tr v-else v-for="row in data" :key="row.id" class="hover:bg-limestone-50 transition-colors">
                        <td v-for="col in columns" :key="col.key" class="px-4 py-3">
                            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                {{ row[col.key] ?? '-' }}
                            </slot>
                        </td>
                        <td v-if="$slots.actions" class="px-4 py-3 text-right">
                            <slot name="actions" :row="row" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-limestone-100">
                <div v-if="loading" class="py-8 text-center text-ink/40">
                    <div class="inline-block w-6 h-6 border-2 border-brass border-t-transparent rounded-full animate-spin" />
                </div>
                <div v-else-if="!data.length" class="py-8 text-center text-ink/40 text-sm">Tidak ada data.</div>
                <div v-else v-for="row in data" :key="row.id" class="p-4 hover:bg-limestone-50 transition-colors">
                    <div v-for="col in columns" :key="col.key" class="flex justify-between py-1 text-sm">
                        <span class="text-ink/50 font-medium">{{ col.label }}</span>
                        <span class="text-ink text-right">
                            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                {{ row[col.key] ?? '-' }}
                            </slot>
                        </span>
                    </div>
                    <div v-if="$slots.actions" class="mt-3 flex justify-end gap-2">
                        <slot name="actions" :row="row" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="meta && meta.last_page > 1"
            class="px-4 py-3 border-t border-limestone-100 flex flex-col sm:flex-row gap-2 items-center justify-between text-sm text-ink/60">
            <span>Menampilkan {{ meta.from }}-{{ meta.to }} dari {{ meta.total }} data</span>
            <div class="flex gap-1">
                <button v-for="page in pageRange" :key="page"
                    @click="$emit('page-change', page)"
                    :disabled="page === meta.current_page"
                    :class="['px-3 py-1 rounded-lg transition-colors', page === meta.current_page
                        ? 'bg-indigo-dusk text-white' : 'hover:bg-limestone']">
                    {{ page }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    columns: { type: Array, required: true },
    data: { type: Array, default: () => [] },
    meta: { type: Object, default: null },
    loading: { type: Boolean, default: false },
    searchable: { type: Boolean, default: true },
});

const emit = defineEmits(['search', 'page-change']);
const searchQuery = ref('');
let searchTimer = null;

function onSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => emit('search', searchQuery.value), 400);
}

const pageRange = computed(() => {
    if (!props.meta) return [];
    const total = props.meta.last_page;
    const current = props.meta.current_page;
    const range = [];
    for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) {
        range.push(i);
    }
    return range;
});
</script>