import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const usePackagesStore = defineStore('packages', () => {
    const packages = ref([]);
    const currentPackage = ref(null);
    const meta = ref(null);
    const loading = ref(false);

    async function fetchPackages(params = {}) {
        loading.value = true;
        try {
            const { data } = await axios.get('/api/v1/packages', { params });
            packages.value = data.data;
            meta.value = data.meta;
        } finally {
            loading.value = false;
        }
    }

    async function fetchPackage(id) {
        loading.value = true;
        try {
            const { data } = await axios.get(`/api/v1/packages/${id}`);
            currentPackage.value = data.data;
            return data.data;
        } finally {
            loading.value = false;
        }
    }

    return { packages, currentPackage, meta, loading, fetchPackages, fetchPackage };
});