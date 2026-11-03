<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-limestone-50 px-4 text-center">
        <!-- Lock GIF -->
        <img
            src="/images/lock.gif"
            alt="Akun Ditangguhkan"
            class="w-48 h-48 object-contain mb-6"
        />

        <h1 class="text-3xl font-bold text-rust-600 mb-3">Akun Ditangguhkan</h1>
        <p class="text-limestone-700 text-lg max-w-md mb-2">
            Akun Anda saat ini sedang <strong>ditangguhkan</strong> oleh administrator.
        </p>
        <p class="text-limestone-500 text-sm max-w-sm mb-8">
            Jika Anda merasa ini adalah kesalahan, silakan hubungi tim kami untuk mendapatkan bantuan lebih lanjut.
        </p>

        <button
            @click="handleLogout"
            class="bg-rust-600 hover:bg-rust-700 text-white font-semibold px-8 py-3 rounded-xl transition"
        >
            Keluar dari Akun
        </button>
    </div>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

async function handleLogout() {
    await authStore.logout();
    router.push({ name: 'login' });
}
</script>

