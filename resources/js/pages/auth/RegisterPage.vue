<template>
    <div class="min-h-screen bg-limestone flex items-center justify-center p-4 py-12">
        <div class="w-full max-w-lg">
            <div class="text-center mb-8">
                <RouterLink to="/" class="inline-flex items-center gap-2">
                    <div class="w-10 h-10 bg-indigo-dusk rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold">GU</span>
                    </div>
                    <span class="font-bold text-2xl text-ink">Go<span class="text-brass">Umrah</span></span>
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
                        class="w-full bg-brass hover:bg-brass-600 text-white py-3.5 rounded-xl font-semibold text-sm transition-colors disabled:opacity-50 flex items-center justify-center gap-2 mt-2">
                        <div v-if="loading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                        {{ loading ? 'Mendaftar...' : 'Daftar Sekarang' }}
                    </button>
                </form>

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
</script>