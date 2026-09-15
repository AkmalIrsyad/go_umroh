<template>
    <RouterView />
    <AppToast />
</template>

<script setup>
import { RouterView } from 'vue-router';
import { useAuthStore } from './stores/auth';
import AppToast from './components/AppToast.vue';
import { onMounted } from 'vue';

const authStore = useAuthStore();

onMounted(() => {
    authStore.initFromStorage();
});
</script>