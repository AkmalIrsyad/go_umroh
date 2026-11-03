<template>
  <div class="min-h-screen bg-limestone flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-limestone-100 shadow-xl p-8 max-w-md w-full text-center">
      <div class="w-14 h-14 bg-indigo-dusk/5 text-brass rounded-2xl flex items-center justify-center mx-auto mb-4">
        <div class="w-7 h-7 border-3 border-brass border-t-transparent rounded-full animate-spin"></div>
      </div>
      <h2 class="text-xl font-bold text-ink tracking-tight mb-2">Menghubungkan Akun Google</h2>
      <p class="text-sm text-ink/60 leading-relaxed">
        Mohon tunggu sebentar, kami sedang memproses autentikasi akun Google Anda...
      </p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useToast } from '../../composables/useToast';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const { success, error: showError } = useToast();

onMounted(async () => {
  const token = route.query.token;
  const error = route.query.error;

  if (error) {
    showError(decodeURIComponent(error));
    router.replace({ name: 'login' });
    return;
  }

  if (token) {
    try {
      localStorage.setItem('go_umrah_token', token);
      authStore.token = token;
      await authStore.fetchMe();

      success(`Selamat datang, ${authStore.user?.name || 'Pengguna'}!`);

      if (authStore.user?.roles?.includes('admin')) {
        router.replace({ name: 'admin.dashboard' });
      } else if (authStore.user?.roles?.includes('merchants')) {
        router.replace({ name: 'merchant.dashboard' });
      } else {
        router.replace({ name: 'home' });
      }
    } catch (e) {
      localStorage.removeItem('go_umrah_token');
      authStore.token = null;
      showError('Gagal memproses login akun Google. Silakan coba lagi.');
      router.replace({ name: 'login' });
    }
  } else {
    showError('Token otentikasi Google tidak ditemukan.');
    router.replace({ name: 'login' });
  }
});
</script>

