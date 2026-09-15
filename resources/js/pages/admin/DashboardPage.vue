<template>
  <div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-ink tracking-tight">Dashboard Admin</h2>
        <p class="text-sm text-ink/50 mt-1">Ringkasan performa dan antrean aktivitas platform GoUmrah.</p>
      </div>
      <button 
        @click="fetchDashboardData" 
        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-limestone-100 text-xs font-semibold text-ink/70 hover:text-ink hover:bg-limestone-50 transition-all shadow-sm self-start sm:self-auto"
      >
        <i :class="['fi fi-rr-refresh text-xs', { 'animate-spin': loading }]" />
        <span>Muat Ulang Data</span>
      </button>
    </div>

    <!-- Alert Banner (Barisan Prioritas Tindakan) -->
    <div v-if="hasPendingAlerts" class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- Alert Paket -->
      <div 
        v-if="stats?.packages_pending > 0"
        class="relative overflow-hidden bg-gradient-to-r from-rust-50 to-amber-50/30 border border-rust-100/80 rounded-2xl p-4 flex items-center justify-between gap-4 shadow-sm"
      >
        <div class="flex items-center gap-3.5 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-rust/10 text-rust flex items-center justify-center shrink-0">
            <i class="fi fi-rr-mosque text-lg" />
          </div>
          <div class="min-w-0">
            <p class="font-bold text-rust text-sm truncate">{{ stats.packages_pending }} Paket Menunggu</p>
            <p class="text-xs text-ink/60 truncate">Memerlukan verifikasi admin</p>
          </div>
        </div>
        <RouterLink 
          to="/admin/packages" 
          class="shrink-0 p-2 rounded-lg bg-rust/10 text-rust hover:bg-rust hover:text-white transition-colors text-xs font-medium"
        >
          <i class="fi fi-rr-arrow-right" />
        </RouterLink>
      </div>

      <!-- Alert Merchant -->
      <div 
        v-if="stats?.merchants_pending > 0"
        class="relative overflow-hidden bg-gradient-to-r from-rust-50 to-amber-50/30 border border-rust-100/80 rounded-2xl p-4 flex items-center justify-between gap-4 shadow-sm"
      >
        <div class="flex items-center gap-3.5 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-rust/10 text-rust flex items-center justify-center shrink-0">
            <i class="fi fi-rr-building text-lg" />
          </div>
          <div class="min-w-0">
            <p class="font-bold text-rust text-sm truncate">{{ stats.merchants_pending }} Travel Partner</p>
            <p class="text-xs text-ink/60 truncate">Pendaftaran baru belum ditinjau</p>
          </div>
        </div>
        <RouterLink 
          to="/admin/users" 
          class="shrink-0 p-2 rounded-lg bg-rust/10 text-rust hover:bg-rust hover:text-white transition-colors text-xs font-medium"
        >
          <i class="fi fi-rr-arrow-right" />
        </RouterLink>
      </div>

      <!-- Alert Pembayaran -->
      <div 
        v-if="stats?.orders_waiting_confirmation > 0"
        class="relative overflow-hidden bg-gradient-to-r from-rust-50 to-amber-50/30 border border-rust-100/80 rounded-2xl p-4 flex items-center justify-between gap-4 shadow-sm"
      >
        <div class="flex items-center gap-3.5 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-rust/10 text-rust flex items-center justify-center shrink-0">
            <i class="fi fi-rr-credit-card text-lg" />
          </div>
          <div class="min-w-0">
            <p class="font-bold text-rust text-sm truncate">{{ stats.orders_waiting_confirmation }} Pembayaran</p>
            <p class="text-xs text-ink/60 truncate">Bukti bayar butuh konfirmasi</p>
          </div>
        </div>
        <RouterLink 
          to="/admin/payments" 
          class="shrink-0 p-2 rounded-lg bg-rust/10 text-rust hover:bg-rust hover:text-white transition-colors text-xs font-medium"
        >
          <i class="fi fi-rr-arrow-right" />
        </RouterLink>
      </div>
    </div>

    <!-- Bento Grid Skeleton Loading -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div class="md:col-span-2 lg:col-span-2 bg-white rounded-3xl border border-limestone-100 p-6 animate-pulse h-48" />
      <div class="bg-white rounded-3xl border border-limestone-100 p-6 animate-pulse h-48" />
      <div class="bg-white rounded-3xl border border-limestone-100 p-6 animate-pulse h-48" />
      <div v-for="i in 4" :key="i" class="bg-white rounded-3xl border border-limestone-100 p-6 animate-pulse h-36" />
    </div>

    <!-- Bento Grid Content -->
    <div v-else class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
      
      <!-- HERO BENTO 1: Total Jamaah (Featured Card / High Emphasis) -->
      <div class="md:col-span-2 lg:col-span-2 bg-gradient-to-br from-indigo-dusk to-slate-900 text-white rounded-3xl p-6 shadow-md relative overflow-hidden flex flex-col justify-between group">
        <div class="absolute -right-6 -bottom-6 text-white/5 group-hover:scale-105 transition-transform duration-500 pointer-events-none">
          <i class="fi fi-rr-users text-[180px]" />
        </div>
        
        <div class="flex items-center justify-between relative z-10">
          <span class="text-xs font-semibold uppercase tracking-wider text-brass bg-brass/10 px-3 py-1 rounded-full border border-brass/20">
            Pertumbuhan
          </span>
          <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-brass">
            <i class="fi fi-rr-users text-lg" />
          </div>
        </div>

        <div class="my-6 relative z-10">
          <p class="text-4xl lg:text-5xl font-black tracking-tight text-white mb-1">
            {{ formatNumber(stats?.total_jamaah) }}
          </p>
          <p class="text-sm text-white/70">Total Jamaah Terdaftar</p>
        </div>

        <div class="pt-4 border-t border-white/10 flex items-center justify-between text-xs text-white/60 relative z-10">
          <span>Pelanggan aktif: {{ formatNumber(stats?.total_customers) }}</span>
          <RouterLink to="/admin/participants" class="hover:text-brass transition-colors inline-flex items-center gap-1">
            Lihat Detail <i class="fi fi-rr-arrow-small-right" />
          </RouterLink>
        </div>
      </div>

      <!-- BENTO 2: Ringkasan Order & Konversi -->
      <div class="bg-white rounded-3xl border border-limestone-100 p-6 shadow-sm flex flex-col justify-between hover:border-brass/40 transition-colors group">
        <div class="flex items-center justify-between">
          <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-dusk flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fi fi-rr-receipt text-lg" />
          </div>
          <span class="text-xs text-ink/40 font-medium">Transaksi</span>
        </div>
        <div class="my-4">
          <p class="text-3xl font-bold text-ink tracking-tight">{{ formatNumber(stats?.total_orders) }}</p>
          <p class="text-xs text-ink/50 mt-0.5">Total Pesanan</p>
        </div>
        <div class="flex items-center gap-2 pt-3 border-t border-limestone-100 text-xs text-sage font-medium">
          <i class="fi fi-rr-check-circle" />
          <span>{{ formatNumber(stats?.orders_complete) }} Selesai</span>
        </div>
      </div>

      <!-- BENTO 3: Status Paket Umrah -->
      <div class="bg-white rounded-3xl border border-limestone-100 p-6 shadow-sm flex flex-col justify-between hover:border-brass/40 transition-colors group">
        <div class="flex items-center justify-between">
          <div class="w-10 h-10 rounded-2xl bg-amber-50 text-brass flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="fi fi-rr-mosque text-lg" />
          </div>
          <span class="text-xs text-ink/40 font-medium">Katalog</span>
        </div>
        <div class="my-4">
          <p class="text-3xl font-bold text-ink tracking-tight">{{ formatNumber(stats?.total_packages) }}</p>
          <p class="text-xs text-ink/50 mt-0.5">Total Paket Dibuat</p>
        </div>
        <div class="flex items-center justify-between pt-3 border-t border-limestone-100 text-xs">
          <span class="text-sage font-medium inline-flex items-center gap-1">
            <i class="fi fi-rr-check" /> {{ stats?.packages_approved }} Aktif
          </span>
          <span class="text-rust font-medium inline-flex items-center gap-1">
            <i class="fi fi-rr-time-fast" /> {{ stats?.packages_pending }} Pending
          </span>
        </div>
      </div>

      <!-- BENTO 4: Travel Partner / Merchant -->
      <div class="bg-white rounded-3xl border border-limestone-100 p-5 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <i class="fi fi-rr-building text-xl" />
        </div>
        <div class="min-w-0">
          <p class="text-2xl font-bold text-ink tracking-tight">{{ formatNumber(stats?.total_merchants) }}</p>
          <p class="text-xs text-ink/50 truncate">Travel Partner</p>
        </div>
      </div>

      <!-- BENTO 5: Total Customer -->
      <div class="bg-white rounded-3xl border border-limestone-100 p-5 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <i class="fi fi-rr-user text-xl" />
        </div>
        <div class="min-w-0">
          <p class="text-2xl font-bold text-ink tracking-tight">{{ formatNumber(stats?.total_customers) }}</p>
          <p class="text-xs text-ink/50 truncate">Pengguna Terdaftar</p>
        </div>
      </div>

      <!-- BENTO 6: Paket Pending Quick-Card -->
      <div class="bg-white rounded-3xl border border-limestone-100 p-5 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-rust-50 text-rust flex items-center justify-center shrink-0">
          <i class="fi fi-rr-time-fast text-xl" />
        </div>
        <div class="min-w-0">
          <p class="text-2xl font-bold text-rust tracking-tight">{{ formatNumber(stats?.packages_pending) }}</p>
          <p class="text-xs text-ink/50 truncate">Paket Pending Approval</p>
        </div>
      </div>

      <!-- BENTO 7: Order Selesai Quick-Card -->
      <div class="bg-white rounded-3xl border border-limestone-100 p-5 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
          <i class="fi fi-rr-box-check text-xl" />
        </div>
        <div class="min-w-0">
          <p class="text-2xl font-bold text-ink tracking-tight">{{ formatNumber(stats?.orders_complete) }}</p>
          <p class="text-xs text-ink/50 truncate">Order Berhasil Selesai</p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import axios from 'axios';

const stats = ref(null);
const loading = ref(true);

// Cek apakah ada antrean alert yang butuh tindakan cepat
const hasPendingAlerts = computed(() => {
  if (!stats.value) return false;
  return (
    stats.value.packages_pending > 0 ||
    stats.value.merchants_pending > 0 ||
    stats.value.orders_waiting_confirmation > 0
  );
});

// Helper format angka standar Indonesia
function formatNumber(num) {
  if (num === undefined || num === null) return '0';
  return new Intl.NumberFormat('id-ID').format(num);
}

async function fetchDashboardData() {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/admin/dashboard');
    stats.value = data.data;
  } catch (error) {
    console.error('Gagal mengambil data dashboard:', error);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchDashboardData();
});
</script>