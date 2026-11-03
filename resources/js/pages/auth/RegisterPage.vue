<template>
    <div class="min-h-screen bg-limestone flex items-center justify-center p-4 py-12">
        <div class="w-full max-w-lg">
            <div class="text-center mb-8">
                <RouterLink to="/" class="inline-flex items-center hover:opacity-95 transition-opacity">
                    <img src="/go_umroh.svg" alt="Go Umroh" class="h-10 w-auto object-contain mx-auto" />
                </RouterLink>
                <h1 class="mt-4 text-xl font-semibold text-ink">Buat Akun Baru</h1>
                <p class="text-ink/50 text-sm mt-1">Daftar sebagai customer atau travel partner</p>
            </div>

            <div class="bg-white rounded-2xl border border-limestone-100 shadow-xl p-8">
                <!-- Role Selector -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <button @click="form.role = 'user'" :class="['border-2 rounded-xl p-3 text-sm font-medium transition-all',
                        form.role === 'user' ? 'border-brass bg-brass-50 text-brass' : 'border-limestone-100 hover:border-brass/30']">
                        👤 Jamaah / Customer
                    </button>
                    <button @click="form.role = 'merchants'" :class="['border-2 rounded-xl p-3 text-sm font-medium transition-all',
                        form.role === 'merchants' ? 'border-brass bg-brass-50 text-brass' : 'border-limestone-100 hover:border-brass/30']">
                        🏢 Travel Partner
                    </button>
                </div>

                <form @submit.prevent="handleRegister" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-ink mb-1.5">Nama Lengkap</label>
                            <input v-model="form.name" type="text" required
                                class="w-full border border-limestone-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30 bg-limestone-50" />
                            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink mb-1.5">Email</label>
                            <input v-model="form.email" type="email" required
                                class="w-full border border-limestone-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30 bg-limestone-50" />
                            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink mb-1.5">No. Telepon</label>
                            <input v-model="form.phone_number" type="tel"
                                class="w-full border border-limestone-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30 bg-limestone-50" />
                        </div>
                        <template v-if="form.role === 'merchants'">
                            <div>
                                <label class="block text-sm font-medium text-ink mb-1.5">Nama Perusahaan</label>
                                <input v-model="form.business_name" type="text"
                                    class="w-full border border-limestone-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30 bg-limestone-50" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink mb-1.5">Informasi Usaha</label>
                                <textarea v-model="form.business_information" rows="3"
                                    class="w-full border border-limestone-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30 bg-limestone-50 resize-none" />
                            </div>
                        </template>
                        <div>
                            <label class="block text-sm font-medium text-ink mb-1.5">Password</label>
                            <input v-model="form.password" type="password" required
                                class="w-full border border-limestone-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30 bg-limestone-50" />
                            <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password[0] }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink mb-1.5">Konfirmasi Password</label>
                            <input v-model="form.password_confirmation" type="password" required
                                class="w-full border border-limestone-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brass/30 bg-limestone-50" />
                        </div>
                    </div>

                    <div v-if="form.role === 'merchants'" class="bg-rust-50 border border-rust-100 rounded-xl p-3 text-sm text-rust">
                        ⚠️ Akun travel partner perlu diverifikasi oleh admin sebelum dapat digunakan.
                    </div>

                    <button type="submit" :disabled="loading"
                        class="w-full bg-brass hover:bg-brass-600 text-white py-3.5 rounded-xl font-semibold text-sm transition-colors disabled:opacity-50 flex items-center justify-center gap-2 mt-2 cursor-pointer">
                        <div v-if="loading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                        {{ loading ? 'Mendaftar...' : 'Daftar Sekarang' }}
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-5">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-limestone-100"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white px-3 text-ink/40 font-medium tracking-wider">atau</span>
                    </div>
                </div>

                <!-- Google Register Button -->
                <button 
                    type="button" 
                    @click="registerWithGoogle"
                    class="w-full border border-limestone-200 hover:border-limestone-300 hover:bg-limestone-50/70 text-ink py-3 rounded-xl font-medium text-sm transition-all flex items-center justify-center gap-3 shadow-xs cursor-pointer"
                >
                    <svg class="w-4.5 h-4.5 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Daftar dengan Google</span>
                </button>

                <p class="text-center text-sm text-ink/50 mt-6">
                    Sudah punya akun?
                    <RouterLink to="/login" class="text-brass font-semibold hover:underline">Masuk</RouterLink>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useToast } from '../../composables/useToast';

const router = useRouter();
const authStore = useAuthStore();
const { success, error: showError } = useToast();
const loading = ref(false);
const errors = ref({});
const form = ref({
    name: '', email: '', phone_number: '', business_name: '',
    business_information: '', password: '', password_confirmation: '', role: 'user',
});

async function handleRegister() {
    loading.value = true;
    errors.value = {};
    try {
        await authStore.register(form.value);
        success('Registrasi berhasil! Silakan login.');
        router.push({ name: 'login' });
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {};
        } else {
            showError(e.response?.data?.message || 'Registrasi gagal.');
        }
    } finally {
        loading.value = false;
    }
}

function registerWithGoogle() {
    window.location.href = `/api/v1/auth/google/redirect?role=${form.value.role}`;
}
</script>