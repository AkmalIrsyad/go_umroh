<template>
  <div class="flex h-screen bg-limestone overflow-hidden">
    <!-- Mobile Backdrop -->
    <Transition name="fade">
      <div 
        v-if="sidebarOpen" 
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-20 md:hidden" 
      />
    </Transition>

    <!-- Sidebar -->
    <aside 
      :class="[
        'fixed inset-y-0 left-0 z-30 w-64 bg-indigo-dusk text-white flex flex-col transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        'md:relative md:translate-x-0'
      ]"
    >
      <!-- Logo Header -->
      <div class="flex items-center justify-between px-6 py-5 border-b border-white/10 shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-brass rounded-xl flex items-center justify-center font-bold text-white shadow-lg shadow-brass/20">
            GU
          </div>
          <span class="font-bold text-xl tracking-tight">Go<span class="text-brass">Umrah</span></span>
        </div>
        <button 
          @click="sidebarOpen = false" 
          class="md:hidden p-1.5 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6 custom-scrollbar">
        <!-- Render Grouped Menu -->
        <div v-for="(group, gIdx) in menuGroups" :key="gIdx" class="space-y-1">
          <!-- Group Title -->
          <div 
            v-if="group.title" 
            class="px-3 text-[11px] font-semibold text-white/40 uppercase tracking-wider mb-2"
          >
            {{ group.title }}
          </div>

          <!-- Group Items -->
          <RouterLink 
            v-for="item in group.items" 
            :key="item.path"
            :to="item.path"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200 group"
            active-class="bg-brass/20 text-brass font-semibold shadow-sm"
            @click="sidebarOpen = false"
          >
            <i :class="[item.icon, 'text-lg group-hover:scale-110 transition-transform duration-200']" />
            <span>{{ item.label }}</span>
          </RouterLink>
        </div>
      </nav>

      <!-- User Profile & Logout -->
      <div class="p-4 border-t border-white/10 shrink-0 bg-black/10">
        <div class="flex items-center gap-3 mb-3 p-1">
          <div class="w-10 h-10 rounded-xl bg-brass/20 border border-brass/30 flex items-center justify-center text-brass font-bold text-base shrink-0">
            {{ userInitial }}
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold truncate leading-tight">{{ authStore.user?.name || 'User' }}</p>
            <p class="text-xs text-white/50 capitalize truncate mt-0.5">{{ roles }}</p>
          </div>
        </div>
        <button 
          @click="handleLogout"
          class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium bg-red-500/10 hover:bg-red-500/20 text-red-300 hover:text-red-200 transition-all duration-200"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Keluar
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Topbar Header -->
      <header class="bg-white border-b border-limestone-100 px-4 md:px-6 py-3.5 flex items-center gap-4 shrink-0 shadow-sm">
        <button 
          @click="sidebarOpen = true" 
          class="md:hidden p-2 rounded-xl text-ink/70 hover:bg-limestone transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        
        <h1 class="text-lg font-bold text-ink flex-1 truncate">{{ pageTitle }}</h1>

        <RouterLink 
          to="/" 
          class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-ink/70 hover:text-brass hover:bg-limestone transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          Lihat Situs
        </RouterLink>
      </header>

      <!-- Main Router View -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-limestone">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { RouterLink, RouterView, useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const sidebarOpen = ref(false);

const isAdmin = computed(() => authStore.hasRole('admin'));
const roles = computed(() => (authStore.user?.roles || []).join(', '));
const userInitial = computed(() => authStore.user?.name?.charAt(0)?.toUpperCase() || 'U');

// Pengelompokan Menu Admin agar lebih rapi & terstruktur
const adminMenuGroups = [
  {
    title: 'Utama',
    items: [
      { path: '/admin/dashboard', label: 'Dashboard', icon: 'fi fi-rr-chart-pie-alt' },
      { path: '/admin/packages', label: 'Paket Umrah', icon: 'fi fi-rr-mosque' },
    ]
  },
  {
    title: 'Transaksi & Jamaah',
    items: [
      { path: '/admin/orders', label: 'Manajemen Order', icon: 'fi fi-rr-receipt' },
      { path: '/admin/participants', label: 'Data Jamaah', icon: 'fi fi-rr-users' },
      { path: '/admin/payments', label: 'Konfirmasi Bayar', icon: 'fi fi-rr-credit-card' },
      { path: '/admin/reports', label: 'Laporan', icon: 'fi fi-rr-chart-line-up' },
    ]
  },
  {
    title: 'Master Data',
    items: [
      { path: '/admin/banks', label: 'Bank', icon: 'fi fi-rr-bank' },
      { path: '/admin/airlines', label: 'Maskapai', icon: 'fi fi-rr-plane' },
      { path: '/admin/hotels', label: 'Hotel', icon: 'fi fi-rr-building' },
      { path: '/admin/routes', label: 'Rute', icon: 'fi fi-rr-map' },
      { path: '/admin/manasiks', label: 'Manasik', icon: 'fi fi-rr-book-open-reader' },
    ]
  },
  {
    title: 'Pengaturan System',
    items: [
      { path: '/admin/users', label: 'Pengguna', icon: 'fi fi-rr-user' },
      { path: '/admin/faqs', label: 'FAQ', icon: 'fi fi-rr-interrogation' },
      { path: '/admin/sliders', label: 'Slider', icon: 'fi fi-rr-picture' },
      { path: '/admin/settings', label: 'Pengaturan', icon: 'fi fi-rr-settings' },
    ]
  }
];

const merchantMenuGroups = [
  {
    title: 'Menu Merchant',
    items: [
      { path: '/merchant/dashboard', label: 'Dashboard', icon: 'fi fi-rr-chart-pie-alt' },
      { path: '/merchant/packages', label: 'Paket Saya', icon: 'fi fi-rr-mosque' },
      { path: '/merchant/orders', label: 'Order Masuk', icon: 'fi fi-rr-receipt' },
      { path: '/merchant/reports', label: 'Laporan', icon: 'fi fi-rr-chart-line-up' },
    ]
  }
];

const menuGroups = computed(() => isAdmin.value ? adminMenuGroups : merchantMenuGroups);
const pageTitle = computed(() => route.meta?.title || 'Panel Admin');

async function handleLogout() {
  await authStore.logout();
  router.push({ name: 'login' });
}
</script>

<style scoped>
/* Transition Fade untuk Backdrop Mobile */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Custom Scrollbar halus untuk sidebar */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}
</style>