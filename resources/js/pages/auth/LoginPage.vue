<template>
  <div class="min-h-screen bg-limestone flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <!-- Main Outer Container -->
    <div class="w-full max-w-5xl bg-white rounded-3xl border border-limestone-100 shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-2 p-3 sm:p-4 lg:p-5 gap-6">
      
      <!-- Left Column: Form Section -->
      <div class="flex flex-col justify-between p-4 sm:p-6 lg:p-8">
        <div>
          <!-- Logo & Brand Header -->
          <div class="flex items-center justify-between mb-6">
            <RouterLink to="/" class="inline-flex items-center gap-2">
              <div class="w-9 h-9 bg-indigo-dusk rounded-xl flex items-center justify-center shadow-sm">
                <span class="text-white font-bold text-sm">GU</span>
              </div>
              <span class="font-bold text-xl text-ink">Go<span class="text-brass">Umrah</span></span>
            </RouterLink>
          </div>

          <!-- Mobile Banner Image (Hanya tampil di Layar HP & Tablet) -->
          <div class="lg:hidden w-full h-44 sm:h-56 rounded-2xl overflow-hidden mb-6 relative">
            <img 
              src="https://images.unsplash.com/photo-1591604466107-ec97de577aff?q=80&w=1000&auto=format&fit=crop" 
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
              class="w-full bg-indigo-dusk hover:bg-indigo-dusk-900 text-white py-3.5 rounded-xl font-semibold text-sm transition-all shadow-md shadow-indigo-dusk/10 disabled:opacity-50 flex items-center justify-center gap-2 mt-2"
            >
              <div v-if="loading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
              {{ loading ? 'Memproses...' : 'Masuk' }}
            </button>
          </form>

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
import { ref } from 'vue';
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