<template>
    <div class="min-h-screen flex flex-col bg-limestone">
        <!-- Navbar -->
        <nav class="bg-indigo-dusk text-white shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <RouterLink to="/" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-brass rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">GU</span>
                        </div>
                        <span class="font-bold text-xl tracking-tight">Go<span class="text-brass">Umrah</span></span>
                    </RouterLink>

                    <!-- Desktop Nav -->
                    <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                        <RouterLink to="/packages" class="hover:text-brass transition-colors" active-class="text-brass">Paket</RouterLink>
                        <RouterLink to="/faq" class="hover:text-brass transition-colors" active-class="text-brass">FAQ</RouterLink>
                        <RouterLink to="/manasik" class="hover:text-brass transition-colors" active-class="text-brass">Manasik</RouterLink>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="hidden md:flex items-center gap-3">
                        <template v-if="authStore.isAuthenticated">
                            <RouterLink to="/orders" class="text-sm hover:text-brass transition-colors">Pesanan Saya</RouterLink>
                            <div class="relative" ref="dropdownRef">
                                <button @click="dropdownOpen = !dropdownOpen"
                                    class="flex items-center gap-2 bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-colors text-sm">
                                    <span>{{ authStore.user?.name }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div v-if="dropdownOpen"
                                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-limestone-100 z-50 py-1">
                                    <RouterLink to="/profile" @click="dropdownOpen = false"
                                        class="block px-4 py-2 text-ink text-sm hover:bg-limestone transition-colors">Profil</RouterLink>
                                    <template v-if="authStore.hasRole('admin')">
                                        <RouterLink to="/admin" @click="dropdownOpen = false"
                                            class="block px-4 py-2 text-ink text-sm hover:bg-limestone transition-colors">Panel Admin</RouterLink>
                                    </template>
                                    <template v-else-if="authStore.hasRole('merchants')">
                                        <RouterLink to="/merchant" @click="dropdownOpen = false"
                                            class="block px-4 py-2 text-ink text-sm hover:bg-limestone transition-colors">Panel Merchant</RouterLink>
                                    </template>
                                    <button @click="handleLogout"
                                        class="block w-full text-left px-4 py-2 text-rust text-sm hover:bg-limestone transition-colors">Keluar</button>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <RouterLink to="/login" class="text-sm hover:text-brass transition-colors">Masuk</RouterLink>
                            <RouterLink to="/register"
                                class="bg-brass hover:bg-brass-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                Daftar
                            </RouterLink>
                        </template>
                    </div>

                    <!-- Mobile hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg hover:bg-white/10 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden border-t border-white/10 bg-indigo-dusk-900 py-4 px-4 space-y-2">
                <RouterLink to="/packages" @click="mobileMenuOpen = false" class="block py-2 hover:text-brass transition-colors">Paket Umrah</RouterLink>
                <RouterLink to="/faq" @click="mobileMenuOpen = false" class="block py-2 hover:text-brass transition-colors">FAQ</RouterLink>
                <RouterLink to="/manasik" @click="mobileMenuOpen = false" class="block py-2 hover:text-brass transition-colors">Manasik</RouterLink>
                <div class="border-t border-white/10 pt-3 mt-2 flex gap-3">
                    <template v-if="authStore.isAuthenticated">
                        <RouterLink to="/orders" @click="mobileMenuOpen = false" class="text-sm hover:text-brass transition-colors">Pesanan Saya</RouterLink>
                        <button @click="handleLogout" class="text-sm text-rust">Keluar</button>
                    </template>
                    <template v-else>
                        <RouterLink to="/login" @click="mobileMenuOpen = false" class="text-sm">Masuk</RouterLink>
                        <RouterLink to="/register" @click="mobileMenuOpen = false" class="bg-brass text-white px-3 py-1 rounded-lg text-sm">Daftar</RouterLink>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Page content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-indigo-dusk text-white/80 mt-16">
            <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-brass rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">GU</span>
                        </div>
                        <span class="font-bold text-xl text-white">Go<span class="text-brass">Umrah</span></span>
                    </div>
                    <p class="text-sm leading-relaxed">Marketplace paket umrah terpercaya. Temukan paket terbaik dari travel partner resmi kami.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">Navigasi</h4>
                    <ul class="space-y-2 text-sm">
                        <li><RouterLink to="/packages" class="hover:text-brass transition-colors">Paket Umrah</RouterLink></li>
                        <li><RouterLink to="/faq" class="hover:text-brass transition-colors">FAQ</RouterLink></li>
                        <li><RouterLink to="/manasik" class="hover:text-brass transition-colors">Panduan Manasik</RouterLink></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm">
                        <li>📧 info@goumrah.id</li>
                        <li>📞 +62 21 1234 5678</li>
                        <li>📍 Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 py-4 text-center text-sm text-white/50">
                &copy; {{ new Date().getFullYear() }} GoUmrah. All rights reserved.
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