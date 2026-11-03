<template>
  <div class="min-h-screen bg-limestone flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <!-- Main Outer Container -->
    <div class="w-full max-w-5xl bg-white rounded-3xl border border-limestone-100 shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-2 p-3 sm:p-4 lg:p-5 gap-6">
      
      <!-- Left Column: Form Section -->
      <div class="flex flex-col justify-between p-4 sm:p-6 lg:p-8">
        <div>
          <!-- Logo & Brand Header -->
          <div class="flex items-center justify-between mb-6">
            <RouterLink to="/" class="inline-flex items-center hover:opacity-95 transition-opacity">
              <img src="/go_umroh.svg" alt="Go Umroh" class="h-9 w-auto object-contain" />
            </RouterLink>
          </div>

          <!-- Mobile Banner Image (Hanya tampil di Layar HP & Tablet) -->
          <div class="lg:hidden w-full h-44 sm:h-56 rounded-2xl overflow-hidden mb-6 relative">
            <img 
              src="/images/madinah.jpg" 
              alt="Umrah Visual" 
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Title & Subtitle -->
          <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight flex items-center gap-2">
              Selamat Datang 👋
            </h1>
            <p class="text-ink/50 text-sm mt-1.5 leading-relaxed">
              Hari baru untuk melangkah. Masuk ke akun Anda untuk melanjutkan perjalanan ibadah.
            </p>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleLogin" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-ink/70 uppercase tracking-wider mb-1.5">Email</label>
              <input 
                v-model="form.email" 
                type="email" 
                required
                class="w-full border border-limestone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass bg-limestone-50/40 transition-all placeholder:text-ink/30"
                placeholder="email@contoh.com" 
              />
              <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-ink/70 uppercase tracking-wider mb-1.5">Password</label>
              <input 
                v-model="form.password" 
                type="password" 
                required
                class="w-full border border-limestone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass bg-limestone-50/40 transition-all placeholder:text-ink/30"
                placeholder="••••••••" 
              />
              <div class="flex justify-end mt-1.5">
                <RouterLink to="/forgot-password" class="text-xs text-brass font-medium hover:underline">
                  Lupa Password?
                </RouterLink>
              </div>
            </div>

            <button 
              type="submit" 
              :disabled="loading"
              class="w-full bg-indigo-dusk hover:bg-indigo-dusk-900 text-white py-3.5 rounded-xl font-semibold text-sm transition-all shadow-md shadow-indigo-dusk/10 disabled:opacity-50 flex items-center justify-center gap-2 mt-2 cursor-pointer"
            >
              <div v-if="loading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
              {{ loading ? 'Memproses...' : 'Masuk' }}
            </button>
          </form>

          <!-- Divider -->
          <div class="relative my-5">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-limestone-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
              <span class="bg-white px-3 text-ink/40 font-medium tracking-wider">atau</span>
            </div>
          </div>

          <!-- Google Login Button -->
          <button 
            type="button" 
            @click="loginWithGoogle"
            class="w-full border border-limestone-200 hover:border-limestone-300 hover:bg-limestone-50/70 text-ink py-3 rounded-xl font-medium text-sm transition-all flex items-center justify-center gap-3 shadow-xs cursor-pointer"
          >
            <svg class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Masuk dengan Google</span>
          </button>

          <!-- Register Link -->
          <p class="text-center text-xs text-ink/50 mt-6">
            Belum punya akun?
            <RouterLink to="/register" class="text-brass font-semibold hover:underline">Daftar sekarang</RouterLink>
          </p>
        </div>

        <!-- Footer Copyright -->
        <div class="mt-8 text-center lg:text-left">
          <p class="text-[11px] text-ink/30 uppercase tracking-wider font-medium">
            &copy; {{ new Date().getFullYear() }} GoUmrah. All rights reserved.
          </p>
        </div>
      </div>

      <!-- Right Column: Visual Image (Desktop Only) -->
      <div class="hidden lg:block relative min-h-[560px] rounded-2xl overflow-hidden">
        <img 
  src="/images/madinah.jpg" 
  alt="Umrah Scenic Visual" 
  class="w-full h-full object-cover"
/>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink, useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useToast } from '../../composables/useToast';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const { success, error: showError } = useToast();
const loading = ref(false);
const errors = ref({});
const form = ref({ email: '', password: '' });

onMounted(() => {
  if (route.query.error) {
    showError(decodeURIComponent(route.query.error));
  }
});

function loginWithGoogle() {
  window.location.href = '/api/v1/auth/google/redirect';
}

async function handleLogin() {
  loading.value = true;
  errors.value = {};
  try {
    const user = await authStore.login(form.value);
    success('Login berhasil!');

    const redirect = route.query.redirect;
    if (redirect) {
      router.push(redirect);
    } else if (user.roles?.includes('admin')) {
      router.push({ name: 'admin.dashboard' });
    } else if (user.roles?.includes('merchants')) {
      router.push({ name: 'merchant.dashboard' });
    } else {
      router.push({ name: 'home' });
    }
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors || {};
    } else {
      showError(e.response?.data?.message || 'Login gagal, coba lagi.');
    }
  } finally {
    loading.value = false;
  }
}
</script>