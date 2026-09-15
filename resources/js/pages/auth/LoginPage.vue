<template>
    <div class="min-h-screen bg-limestone flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <RouterLink to="/" class="inline-flex items-center gap-2">
                    <div class="w-10 h-10 bg-indigo-dusk rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold">GU</span>
                    </div>
                    <span class="font-bold text-2xl text-ink">Go<span class="text-brass">Umrah</span></span>
                </RouterLink>
                <h1 class="mt-4 text-xl font-semibold text-ink">Selamat Datang Kembali</h1>
                <p class="text-ink/50 text-sm mt-1">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-2xl border border-limestone-100 shadow-xl p-8">
                <form @submit.prevent="handleLogin" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Email</label>
                        <input v-model="form.email" type="email" required
                            class="w-full border border-limestone-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30 bg-limestone-50"
                            placeholder="email@contoh.com" />
                        <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Password</label>
                        <input v-model="form.password" type="password" required
                            class="w-full border border-limestone-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30 bg-limestone-50"
                            placeholder="••••••••" />
                    </div>

                    <button type="submit" :disabled="loading"
                        class="w-full bg-indigo-dusk hover:bg-indigo-dusk-900 text-white py-3.5 rounded-xl font-semibold text-sm transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                        <div v-if="loading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                        {{ loading ? 'Memproses...' : 'Masuk' }}
                    </button>
                </form>

                <p class="text-center text-sm text-ink/50 mt-6">
                    Belum punya akun?
                    <RouterLink to="/register" class="text-brass font-semibold hover:underline">Daftar sekarang</RouterLink>
                </p>
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