<template>
    <div class="min-h-screen flex flex-col bg-limestone">
        <!-- Navbar -->
        <nav class="bg-indigo-dusk text-white shadow-sm sticky top-0 z-50 border-b border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Diubah menjadi h-20 agar lebih lega -->
                <div class="flex items-center justify-between h-20">
                    
                    <!-- Left: Logo (Diatur width-nya agar bisa menyeimbangkan flex-center) -->
                    <div class="flex-shrink-0 md:w-1/4 flex items-center">
                        <RouterLink to="/" class="flex items-center gap-3 hover:opacity-90 transition-opacity">
                            <div class="w-10 h-10 bg-brass rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-base">GU</span>
                            </div>
                            <span class="font-bold text-2xl tracking-tight">Go<span class="text-brass">Umrah</span></span>
                        </RouterLink>
                    </div>

                    <!-- Center: Desktop Nav (Menu ditaruh persis di tengah) -->
                    <div class="hidden md:flex flex-1 justify-center items-center gap-10 text-base font-medium">
                        <RouterLink to="/packages" class="hover:text-brass transition-colors relative py-2" active-class="text-brass after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-brass after:rounded-t-md">Paket</RouterLink>
                        <RouterLink to="/faq" class="hover:text-brass transition-colors relative py-2" active-class="text-brass after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-brass after:rounded-t-md">FAQ</RouterLink>
                        <RouterLink to="/manasik" class="hover:text-brass transition-colors relative py-2" active-class="text-brass after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-brass after:rounded-t-md">Manasik</RouterLink>
                    </div>

                    <!-- Right: Auth Buttons -->
                    <div class="hidden md:flex flex-shrink-0 md:w-1/4 justify-end items-center gap-4">
                        <template v-if="authStore.isAuthenticated">
                            <RouterLink to="/orders" class="text-sm font-medium hover:text-brass transition-colors">Pesanan Saya</RouterLink>
                            <!-- Garis Pemisah -->
                            <div class="w-px h-6 bg-white/20 mx-1"></div> 
                            <div class="relative" ref="dropdownRef">
                                <button @click="dropdownOpen = !dropdownOpen"
                                    class="flex items-center gap-2 bg-white/5 hover:bg-white/15 border border-white/10 px-4 py-2 rounded-full transition-all text-sm font-medium">
                                    <span>{{ authStore.user?.name }}</span>
                                    <svg class="w-4 h-4 text-brass" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <!-- Dropdown Menu -->
                                <div v-if="dropdownOpen"
                                    class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-limestone-200 z-50 py-2 overflow-hidden">
                                    <RouterLink to="/profile" @click="dropdownOpen = false"
                                        class="block px-5 py-2.5 text-ink text-sm font-medium hover:bg-limestone transition-colors">Profil Saya</RouterLink>
                                    <template v-if="authStore.hasRole('admin')">
                                        <RouterLink to="/admin" @click="dropdownOpen = false"
                                            class="block px-5 py-2.5 text-ink text-sm font-medium hover:bg-limestone transition-colors">Panel Admin</RouterLink>
                                    </template>
                                    <template v-else-if="authStore.hasRole('merchants')">
                                        <RouterLink to="/merchant" @click="dropdownOpen = false"
                                            class="block px-5 py-2.5 text-ink text-sm font-medium hover:bg-limestone transition-colors">Panel Merchant</RouterLink>
                                    </template>
                                    <div class="h-px bg-limestone my-1"></div>
                                    <button @click="handleLogout"
                                        class="block w-full text-left px-5 py-2.5 text-rust text-sm font-medium hover:bg-rust/5 transition-colors">Keluar</button>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <RouterLink to="/login" class="text-sm font-medium hover:text-brass transition-colors px-2">Masuk</RouterLink>
                            <RouterLink to="/register"
                                class="bg-brass hover:bg-brass-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold shadow-lg shadow-brass/20 transition-all hover:-translate-y-0.5">
                                Daftar Sekarang
                            </RouterLink>
                        </template>
                    </div>

                    <!-- Mobile hamburger -->
                    <div class="md:hidden flex items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-show="mobileMenuOpen" class="md:hidden border-t border-white/10 bg-indigo-dusk pb-6 pt-2 px-4 shadow-xl">
                <div class="flex flex-col space-y-1">
                    <RouterLink to="/packages" @click="mobileMenuOpen = false" class="px-4 py-3 rounded-lg hover:bg-white/5 hover:text-brass transition-colors font-medium">Paket Umrah</RouterLink>
                    <RouterLink to="/faq" @click="mobileMenuOpen = false" class="px-4 py-3 rounded-lg hover:bg-white/5 hover:text-brass transition-colors font-medium">FAQ</RouterLink>
                    <RouterLink to="/manasik" @click="mobileMenuOpen = false" class="px-4 py-3 rounded-lg hover:bg-white/5 hover:text-brass transition-colors font-medium">Manasik</RouterLink>
                </div>
                <div class="border-t border-white/10 mt-4 pt-4 px-4">
                    <template v-if="authStore.isAuthenticated">
                        <RouterLink to="/orders" @click="mobileMenuOpen = false" class="block py-3 font-medium hover:text-brass transition-colors">Pesanan Saya</RouterLink>
                        <button @click="handleLogout" class="block w-full text-left py-3 font-medium text-rust">Keluar Akun</button>
                    </template>
                    <template v-else>
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <RouterLink to="/login" @click="mobileMenuOpen = false" class="flex items-center justify-center py-2.5 rounded-xl border border-white/20 font-medium">Masuk</RouterLink>
                            <RouterLink to="/register" @click="mobileMenuOpen = false" class="flex items-center justify-center py-2.5 rounded-xl bg-brass text-white font-medium">Daftar</RouterLink>
                        </div>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Page content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-indigo-dusk text-white mt-20 pt-16 pb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Pre-Footer CTA -->
                <div class="border-b border-white/10 pb-12 mb-12 flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold mb-2">Siap Memulai Perjalanan Suci Anda?</h2>
                        <p class="text-white/70">Temukan paket umrah terbaik dari puluhan travel terpercaya di Indonesia.</p>
                    </div>
                    <RouterLink to="/packages" class="shrink-0 bg-brass hover:bg-brass-600 text-white px-8 py-3.5 rounded-full font-semibold transition-all hover:-translate-y-1 shadow-lg shadow-brass/20">
                        Cari Paket Umrah
                    </RouterLink>
                </div>

                <!-- Footer Main Content (4 Columns) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                    
                    <!-- Col 1: Brand & About -->
                    <div class="lg:col-span-1">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-brass rounded-xl flex items-center justify-center">
                                <span class="text-white font-bold text-base">GU</span>
                            </div>
                            <span class="font-bold text-2xl tracking-tight">Go<span class="text-brass">Umrah</span></span>
                        </div>
                        <p class="text-white/60 text-sm leading-relaxed mb-6">
                            Marketplace paket umrah terpercaya. Memudahkan jamaah menemukan paket ibadah terbaik, aman, dan nyaman dari travel resmi Kemenag.
                        </p>
                    </div>

                    <!-- Col 2: Jelajahi -->
                    <div>
                        <h4 class="text-lg font-semibold mb-6">Jelajahi</h4>
                        <ul class="space-y-4 text-white/70 text-sm font-medium">
                            <li><RouterLink to="/" class="hover:text-brass hover:translate-x-1 inline-block transition-transform">Beranda</RouterLink></li>
                            <li><RouterLink to="/packages" class="hover:text-brass hover:translate-x-1 inline-block transition-transform">Semua Paket Umrah</RouterLink></li>
                            <li><RouterLink to="/manasik" class="hover:text-brass hover:translate-x-1 inline-block transition-transform">Panduan Manasik</RouterLink></li>
                            <li><RouterLink to="/promo" class="hover:text-brass hover:translate-x-1 inline-block transition-transform">Promo Spesial</RouterLink></li>
                        </ul>
                    </div>

                    <!-- Col 3: Bantuan -->
                    <div>
                        <h4 class="text-lg font-semibold mb-6">Bantuan</h4>
                        <ul class="space-y-4 text-white/70 text-sm font-medium">
                            <li><RouterLink to="/faq" class="hover:text-brass hover:translate-x-1 inline-block transition-transform">Pusat Bantuan (FAQ)</RouterLink></li>
                            <li><RouterLink to="/terms" class="hover:text-brass hover:translate-x-1 inline-block transition-transform">Syarat & Ketentuan</RouterLink></li>
                            <li><RouterLink to="/privacy" class="hover:text-brass hover:translate-x-1 inline-block transition-transform">Kebijakan Privasi</RouterLink></li>
                            <li><RouterLink to="/register-merchant" class="hover:text-brass hover:translate-x-1 inline-block transition-transform">Daftar sebagai Travel</RouterLink></li>
                        </ul>
                    </div>

                    <!-- Col 4: Kontak & Social -->
                    <div>
                        <h4 class="text-lg font-semibold mb-6">Hubungi Kami</h4>
                        <ul class="space-y-4 text-white/70 text-sm">
                            <li class="flex items-start gap-3">
                                <span class="text-brass text-lg mt-0.5">📍</span>
                                <span>Gedung GoUmrah,<br/>Jl. Jend. Sudirman No. 123,<br/>Jakarta Selatan, Indonesia</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-brass text-lg">📞</span>
                                <span>+62 21 1234 5678</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-brass text-lg">📧</span>
                                <span>cs@goumrah.id</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-white/50">
                        &copy; {{ new Date().getFullYear() }} GoUmrah. All rights reserved.
                    </p>
                    <div class="flex gap-4 text-white/50 text-sm">
                        <span>Aman & Terdaftar di Kemenag RI</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const router = useRouter();
const mobileMenuOpen = ref(false);
const dropdownOpen = ref(false);
const dropdownRef = ref(null);

async function handleLogout() {
    await authStore.logout();
    router.push({ name: 'login' });
}

function handleClickOutside(e) {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        dropdownOpen.value = false;
    }
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>