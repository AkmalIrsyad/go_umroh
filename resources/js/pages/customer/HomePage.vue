<template>
  <CustomerLayout>
    <!-- Hero / Slider Section -->
    <section class="relative bg-indigo-dusk overflow-hidden min-h-[580px] flex items-center">
      <!-- Background Image Carousel -->
      <div class="absolute inset-0 z-0">
        <TransitionGroup name="fade">
          <div 
            v-if="sliders.length" 
            :key="activeSlide"
            class="absolute inset-0"
          >
            <img 
              :src="sliders[activeSlide]?.image" 
              class="w-full h-full object-cover opacity-25 scale-105 transition-transform duration-1000" 
              alt="Promo Umrah" 
            />
          </div>
        </TransitionGroup>
        <!-- Gradients -->
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-dusk via-indigo-dusk/90 to-transparent" />
        <div class="absolute inset-0 bg-gradient-to-t from-indigo-dusk via-transparent to-transparent" />
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
        <div class="max-w-2xl">
          <div class="inline-flex items-center gap-2 bg-brass/15 border border-brass/30 text-brass text-xs font-semibold px-3.5 py-1.5 rounded-full mb-6 backdrop-blur-md">
            <i class="fi fi-rr-badge-check text-sm" />
            <span class="uppercase tracking-wider">Marketplace Umrah Terpercaya #1</span>
          </div>

          <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white leading-tight mb-6 tracking-tight">
            Wujudkan <br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-brass via-amber-300 to-brass">
              Ibadah Umrah
            </span> <br />
            Impian Anda
          </h1>

          <p class="text-white/70 text-base sm:text-lg mb-8 leading-relaxed font-normal">
            Temukan ratusan paket umrah resmi dengan jaminan fasilitas terbaik, kepastian keberangkatan, dan transparansi harga.
          </p>

          <!-- Floating Search Widget -->
          <div class="bg-white/95 backdrop-blur-md p-2.5 sm:p-3 rounded-2xl shadow-2xl border border-white/20 flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1 flex items-center">
              <i class="fi fi-rr-search absolute left-4 text-ink/40 text-base" />
              <input 
                v-model="searchQuery" 
                @keyup.enter="goSearch" 
                type="text"
                placeholder="Cari nama paket atau travel..."
                class="w-full pl-11 pr-4 py-3 bg-transparent text-ink placeholder-ink/40 focus:outline-none text-sm font-medium" 
              />
            </div>

            <div class="relative sm:w-48 flex items-center border-t sm:border-t-0 sm:border-l border-limestone-200 pt-2 sm:pt-0 sm:pl-2">
              <i class="fi fi-rr-filter absolute left-4 sm:left-6 text-ink/40 text-base pointer-events-none" />
              <select 
                v-model="selectedType"
                class="w-full pl-10 pr-8 py-3 bg-transparent text-ink text-sm font-medium focus:outline-none appearance-none cursor-pointer"
              >
                <option value="">Semua Tipe</option>
                <option value="regular">Regular</option>
                <option value="vip">VIP</option>
                <option value="promo">Promo</option>
              </select>
              <i class="fi fi-rr-angle-small-down absolute right-3 text-ink/40 pointer-events-none" />
            </div>

            <button 
              @click="goSearch"
              class="bg-brass hover:bg-brass-600 text-white px-7 py-3.5 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-brass/20 active:scale-95"
            >
              <span>Cari Paket</span>
              <i class="fi fi-rr-arrow-right text-xs" />
            </button>
          </div>
        </div>
      </div>

      <!-- Carousel Pagination Indicator -->
      <div v-if="sliders.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
        <button 
          v-for="(_, i) in sliders" 
          :key="i" 
          @click="activeSlide = i"
          :class="['h-2 rounded-full transition-all duration-300', i === activeSlide ? 'bg-brass w-8' : 'bg-white/30 hover:bg-white/50 w-2']" 
        />
      </div>
    </section>

    <!-- Key Statistics Bar -->
    <!-- <section class="bg-white border-b border-limestone-100 relative z-20 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">
          <div 
            v-for="stat in stats" 
            :key="stat.label" 
            class="flex items-center gap-4 p-2"
          >
            <div class="w-12 h-12 rounded-2xl bg-brass/10 text-brass flex items-center justify-center text-xl shrink-0">
              <i :class="stat.icon" />
            </div>
            <div>
              <p class="text-2xl font-bold text-ink tracking-tight">{{ stat.value }}</p>
              <p class="text-xs font-medium text-ink/50 mt-0.5">{{ stat.label }}</p>
            </div>
          </div>
        </div>
      </div>
    </section> -->
<!-- Partner Maskapai Marquee Section -->
<section class="bg-white border-b border-limestone-100 py-6 relative overflow-hidden">
  
  <!-- Gradient Fade Effect di Kiri & Kanan -->
  <div class="pointer-events-none absolute inset-y-0 left-0 w-16 md:w-32 bg-gradient-to-r from-white to-transparent z-10"></div>
  <div class="pointer-events-none absolute inset-y-0 right-0 w-16 md:w-32 bg-gradient-to-l from-white to-transparent z-10"></div>

  <!-- Label Judul Kecil -->
  <div class="max-w-7xl mx-auto px-4 text-center mb-4">
    <p class="text-xs font-bold tracking-widest text-ink/40 uppercase">Maskapai Penerbangan Partner</p>
  </div>

  <!-- Running Marquee Track -->
  <div class="flex overflow-hidden select-none group">
    <!-- Set 1 -->
    <div class="flex shrink-0 items-center justify-around gap-12 min-w-full animate-marquee group-hover:[animation-play-state:paused]">
      <div 
        v-for="(airline, index) in airlines" 
        :key="`a1-${index}`" 
        class="flex items-center px-2 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300 cursor-pointer"
      >
        <img :src="airline.logo" :alt="airline.name" class="h-7 md:h-9 w-auto object-contain" />
      </div>
    </div>

    <!-- Set 2 (Duplikasi untuk efek seamless loop tanpa jeda) -->
    <div class="flex shrink-0 items-center justify-around gap-12 min-w-full animate-marquee group-hover:[animation-play-state:paused]" aria-hidden="true">
      <div 
        v-for="(airline, index) in airlines" 
        :key="`a2-${index}`" 
        class="flex items-center px-2 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300 cursor-pointer"
      >
        <img :src="airline.logo" :alt="airline.name" class="h-7 md:h-9 w-auto object-contain" />
      </div>
    </div>
  </div>
</section>

    <!-- Featured Packages Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
        <div>
          <div class="flex items-center gap-2 text-brass text-xs font-bold uppercase tracking-wider mb-2">
            <i class="fi fi-rr-star text-sm" />
            <span>Pilihan Terfavorit</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">Paket Unggulan</h2>
          <p class="text-ink/60 text-sm mt-1">Pilihan paket umrah terbaik yang direkomendasikan untuk Anda</p>
        </div>
        <RouterLink 
          to="/packages"
          class="inline-flex items-center gap-2 text-brass font-semibold hover:text-brass-600 text-sm group transition-colors self-start sm:self-auto"
        >
          <span>Lihat Semua Paket</span>
          <i class="fi fi-rr-arrow-right text-xs group-hover:translate-x-1 transition-transform" />
        </RouterLink>
      </div>

      <!-- Skeleton Loading -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="bg-white rounded-3xl border border-limestone-100 overflow-hidden animate-pulse shadow-sm">
          <div class="aspect-[16/10] bg-limestone-100" />
          <div class="p-5 space-y-3">
            <div class="h-4 bg-limestone-100 rounded-lg w-3/4" />
            <div class="h-3 bg-limestone-100 rounded-lg w-1/2" />
            <div class="pt-4 flex justify-between items-center">
              <div class="h-6 bg-limestone-100 rounded-lg w-1/3" />
              <div class="h-8 bg-limestone-100 rounded-xl w-1/4" />
            </div>
          </div>
        </div>
      </div>

      <!-- Cards Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <PackageCard v-for="pkg in featuredPackages" :key="pkg.id" :pkg="pkg" />
      </div>
    </section>

    <!-- ════════════════════ CALENDAR SCHEDULE SECTION ════════════════════ -->
    <section class="bg-white border-y border-limestone-100 py-16 md:py-20 relative overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
          <div>
            <div class="flex items-center gap-2 text-brass text-xs font-bold uppercase tracking-wider mb-2">
              <i class="fi fi-rr-calendar text-sm" />
              <span>Jadwal Lengkap Keberangkatan</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">Kalender Keberangkatan Umrah</h2>
            <p class="text-ink/60 text-sm mt-1">Pilih tanggal dan bulan yang sesuai untuk melihat jadwal keberangkatan serta paket yang tersedia.</p>
          </div>

          <!-- Color Legends -->
          <div class="flex items-center gap-3 flex-wrap text-xs font-medium">
            <div class="flex items-center gap-1.5">
              <span class="w-3 h-3 rounded-full bg-indigo-dusk inline-block"></span>
              <span class="text-ink/70">Regular</span>
            </div>
            <div class="flex items-center gap-1.5">
              <span class="w-3 h-3 rounded-full bg-brass inline-block"></span>
              <span class="text-ink/70">VIP</span>
            </div>
            <div class="flex items-center gap-1.5">
              <span class="w-3 h-3 rounded-full bg-rust inline-block"></span>
              <span class="text-ink/70">Promo</span>
            </div>
          </div>
        </div>

        <!-- FullCalendar Container Card -->
        <div class="bg-limestone-50/50 rounded-3xl border border-limestone-100 p-4 sm:p-6 lg:p-8 shadow-sm calendar-wrapper">
          <FullCalendar :options="calendarOptions" />
        </div>
      </div>
    </section>

    <!-- ════════════════════ MODAL DETAIL JADWAL / PAKET ════════════════════ -->
    <div 
      v-if="selectedEventPackage || selectedDatePackages.length > 0" 
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-limestone-100 max-h-[90vh] overflow-y-auto animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-4 border-b border-limestone-100 mb-6">
          <div>
            <div class="flex items-center gap-2 text-brass text-xs font-bold uppercase tracking-wider mb-1">
              <span>🗓️ {{ activeModalTitle }}</span>
            </div>
            <h3 class="text-xl font-bold text-ink">
              {{ selectedEventPackage ? selectedEventPackage.title : `Daftar Paket Keberangkatan (${selectedDatePackages.length})` }}
            </h3>
          </div>
          <button 
            @click="closePackageModal" 
            class="w-8 h-8 rounded-full bg-limestone-50 hover:bg-limestone text-ink/60 hover:text-ink flex items-center justify-center transition-colors"
          >
            ✕
          </button>
        </div>

        <!-- Single Package Detail View -->
        <div v-if="selectedEventPackage" class="space-y-6">
          <div class="flex flex-wrap items-center gap-2">
            <span :class="['px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider',
              selectedEventPackage.type === 'vip' ? 'bg-brass text-white' :
              selectedEventPackage.type === 'promo' ? 'bg-rust text-white' : 'bg-indigo-dusk text-white']">
              Tipe {{ selectedEventPackage.type }}
            </span>
            <span class="px-3 py-1 bg-sage-100 text-sage rounded-full text-xs font-bold">
              ✓ Kuota Tersedia ({{ selectedEventPackage.available_seats || (selectedEventPackage.capacity - (selectedEventPackage.booked_count || 0)) }} Seat)
            </span>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-limestone-50 rounded-2xl border border-limestone-100 text-xs">
            <div>
              <p class="text-ink/50 font-medium">Tanggal Berangkat</p>
              <p class="font-bold text-ink mt-0.5">{{ formatDateIndo(selectedEventPackage.departure_date) }}</p>
            </div>
            <div>
              <p class="text-ink/50 font-medium">Durasi Program</p>
              <p class="font-bold text-ink mt-0.5">{{ selectedEventPackage.duration }} Hari</p>
            </div>
            <div>
              <p class="text-ink/50 font-medium">Harga per Orang</p>
              <p class="font-bold text-brass text-sm mt-0.5">{{ formatRupiah(selectedEventPackage.price) }}</p>
            </div>
            <div v-if="selectedEventPackage.airline">
              <p class="text-ink/50 font-medium">Maskapai</p>
              <p class="font-bold text-ink mt-0.5">{{ selectedEventPackage.airline.nama }}</p>
            </div>
            <div v-if="selectedEventPackage.route" class="sm:col-span-2">
              <p class="text-ink/50 font-medium">Rute</p>
              <p class="font-bold text-ink mt-0.5">{{ selectedEventPackage.route.name }}</p>
            </div>
          </div>

          <div v-if="selectedEventPackage.hotels && selectedEventPackage.hotels.length > 0">
            <p class="text-xs font-bold text-ink uppercase tracking-wider mb-2">🏨 Akomodasi Hotel</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <div v-for="h in selectedEventPackage.hotels" :key="h.id" class="p-3 bg-limestone-50 rounded-xl border border-limestone-100 text-xs">
                <p class="font-bold text-ink">{{ h.nama }}</p>
                <p class="text-[11px] text-ink/60">📍 {{ h.lokasi }} • {{ h.keterangan || 'Fasilitas Bintang 5' }}</p>
              </div>
            </div>
          </div>

          <div v-if="selectedEventPackage.details">
            <p class="text-xs font-bold text-ink uppercase tracking-wider mb-1.5">📝 Ringkasan Fasilitas & Detail</p>
            <p class="text-xs text-ink/70 leading-relaxed line-clamp-4">{{ selectedEventPackage.details }}</p>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-limestone-100">
            <button 
              type="button" 
              @click="closePackageModal" 
              class="px-5 py-2.5 border border-limestone-100 rounded-xl text-xs font-bold text-ink/70 hover:bg-limestone transition-colors"
            >
              Tutup
            </button>
            <RouterLink 
              :to="{ name: 'package.detail', params: { slug: selectedEventPackage.slug } }"
              class="px-6 py-2.5 bg-brass hover:bg-brass-600 text-white rounded-xl text-xs font-bold shadow-md shadow-brass/20 transition-all inline-flex items-center gap-2"
            >
              <span>Lihat Detail & Booking</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </RouterLink>
          </div>
        </div>

        <!-- Multiple Packages List for Selected Date -->
        <div v-else-if="selectedDatePackages.length > 0" class="space-y-4">
          <div 
            v-for="pkg in selectedDatePackages" 
            :key="pkg.id" 
            class="p-4 rounded-2xl border border-limestone-100 bg-limestone-50/50 hover:bg-white hover:border-brass/40 hover:shadow-md transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4"
          >
            <div class="space-y-1">
              <div class="flex items-center gap-2">
                <span :class="['px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                  pkg.type === 'vip' ? 'bg-brass text-white' :
                  pkg.type === 'promo' ? 'bg-rust text-white' : 'bg-indigo-dusk text-white']">
                  {{ pkg.type }}
                </span>
                <span class="text-xs text-ink/50">{{ pkg.duration }} Hari</span>
              </div>
              <h4 class="font-bold text-ink text-sm">{{ pkg.title }}</h4>
              <p class="text-xs text-ink/60">
                Maskapai: <span class="font-semibold text-ink">{{ pkg.airline?.nama || 'Direct Flight' }}</span>
              </p>
            </div>

            <div class="flex sm:flex-col items-end justify-between sm:justify-center gap-2 shrink-0">
              <p class="text-sm font-extrabold text-brass">{{ formatRupiah(pkg.price) }}</p>
              <RouterLink 
                :to="{ name: 'package.detail', params: { slug: pkg.slug } }"
                class="px-4 py-2 bg-indigo-dusk hover:bg-indigo-dusk-900 text-white rounded-xl text-xs font-bold transition-colors"
              >
                Pilih Paket
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Why Choose Us Section -->
    <section class="bg-gradient-to-b from-indigo-dusk to-slate-900 text-white py-20 relative overflow-hidden">
      <!-- Decorative Background Ornaments -->
      <div class="absolute top-0 right-0 translate-x-1/3 -translate-y-1/3 w-[500px] h-[500px] bg-brass/10 rounded-full blur-3xl pointer-events-none" />
      <div class="absolute bottom-0 left-0 -translate-x-1/3 translate-y-1/3 w-[500px] h-[500px] bg-indigo-500/10 rounded-full blur-3xl pointer-events-none" />

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <span class="text-brass text-xs font-bold uppercase tracking-widest bg-brass/10 border border-brass/20 px-3 py-1 rounded-full inline-block mb-3">
            Keunggulan Layanan
          </span>
          <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 tracking-tight">Mengapa Pilih GoUmrah?</h2>
          <p class="text-white/60 text-base leading-relaxed">
            Komitmen kami adalah memberikan ketenangan dan kenyamanan ibadah Anda dari persiapan hingga kembali ke tanah air.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div 
            v-for="reason in reasons" 
            :key="reason.title"
            class="bg-white/5 border border-white/10 rounded-3xl p-6 hover:bg-white/10 hover:border-brass/30 transition-all duration-300 group"
          >
            <div class="w-14 h-14 rounded-2xl bg-brass/15 text-brass flex items-center justify-center text-2xl mb-6 group-hover:scale-110 group-hover:bg-brass group-hover:text-white transition-all duration-300 shadow-lg">
              <i :class="reason.icon" />
            </div>
            <h3 class="font-bold text-lg mb-2 text-white group-hover:text-brass transition-colors">{{ reason.title }}</h3>
            <p class="text-white/60 text-sm leading-relaxed">{{ reason.desc }}</p>
          </div>
        </div>
      </div>
    </section>
  </CustomerLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import CustomerLayout from '../../layouts/CustomerLayout.vue';
import PackageCard from '../../components/PackageCard.vue';
import { useFormatCurrency } from '../../composables/useFormatCurrency';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import axios from 'axios';

const router = useRouter();
const { formatRupiah } = useFormatCurrency();

const loading = ref(true);
const featuredPackages = ref([]);
const allPackages = ref([]);
const sliders = ref([]);
const activeSlide = ref(0);
const searchQuery = ref('');
const selectedType = ref('');
let slideInterval = null;

const selectedEventPackage = ref(null);
const selectedDatePackages = ref([]);
const activeModalTitle = ref('');

// Stats dengan Flaticon Icons
// const stats = [
//   { value: '500+', label: 'Paket Tersedia', icon: 'fi fi-rr-mosque' },
//   { value: '50+', label: 'Travel Partner', icon: 'fi fi-rr-building' },
//   { value: '10.000+', label: 'Jamaah Diberangkatkan', icon: 'fi fi-rr-users' },
//   { value: '4.9/5', label: 'Rating Kepuasan', icon: 'fi fi-rr-star' },
// ];
const airlines = [
  { name: 'Saudia', logo: '/airlines/Saudia_Logo.svg' },
  { name: 'Garuda Indonesia', logo: '/airlines/Garuda_Indonesia_Logo.svg' },
  { name: 'Qatar Airways', logo: '/airlines/Qatar_Airways_logo.svg' },
  { name: 'Emirates', logo: '/airlines/Emirates_Logo.svg' },
  { name: 'Etihad Airways', logo: '/airlines/Etihad_Airways_Logo.svg' },
  { name: 'Oman Air', logo: '/airlines/Oman_Air_Logo.svg' },
  { name: 'Flynas', logo: '/airlines/Flynas_Logo.svg' },
];

// Reasons dengan Flaticon Icons
const reasons = [
  { 
    icon: 'fi fi-rr-shield-check', 
    title: 'Terpercaya', 
    desc: 'Semua travel partner terverifikasi resmi dan memiliki izin Penyelenggara Perjalanan Ibadah Umrah (PPIU).' 
  },
  { 
    icon: 'fi fi-rr-receipt', 
    title: 'Harga Transparan', 
    desc: 'Tanpa biaya tersembunyi. Seluruh rincian biaya, fasilitator, dan skema pembayaran tertera jelas.' 
  },
  { 
    icon: 'fi fi-rr-mobile-button', 
    title: 'Mudah & Cepat', 
    desc: 'Kemudahan pendaftaran, verifikasi berkas dokumen, dan pembayaran online dalam satu genggaman.' 
  },
  { 
    icon: 'fi fi-rr-apps', 
    title: 'Pilihan Lengkap', 
    desc: 'Bebas pilih ratusan paket Regular, VIP, maupun Promo yang disesuaikan dengan kebutuhan Anda.' 
  },
];

function formatDateIndo(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

// Map Packages to FullCalendar Events
const calendarEvents = computed(() => {
  return allPackages.value.map(pkg => {
    const isVip = pkg.type === 'vip';
    const isPromo = pkg.type === 'promo';
    const bg = isVip ? '#A97C3F' : (isPromo ? '#A34B36' : '#1E2A3A');
    const border = isVip ? '#8D6734' : (isPromo ? '#843C2A' : '#111823');

    return {
      id: String(pkg.id),
      title: `${pkg.type.toUpperCase()}: ${pkg.title}`,
      start: pkg.departure_date ? pkg.departure_date.split('T')[0] : '',
      allDay: true,
      backgroundColor: bg,
      borderColor: border,
      textColor: '#FFFFFF',
      extendedProps: {
        raw: pkg
      }
    };
  });
});

const calendarOptions = computed(() => ({
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  locale: 'id',
  height: 'auto',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: ''
  },
  buttonText: {
    today: 'Bulan Ini'
  },
  events: calendarEvents.value,
  eventClick: handleEventClick,
  dateClick: handleDateClick,
  dayMaxEvents: 3,
  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    meridiem: false
  }
}));

function handleEventClick(info) {
  const pkg = info.event.extendedProps.raw;
  selectedEventPackage.value = pkg;
  selectedDatePackages.value = [];
  activeModalTitle.value = `Keberangkatan: ${formatDateIndo(pkg.departure_date)}`;
}

function handleDateClick(info) {
  const dateStr = info.dateStr;
  const matched = allPackages.value.filter(p => {
    const pDate = p.departure_date ? p.departure_date.split('T')[0] : '';
    return pDate === dateStr;
  });

  if (matched.length === 1) {
    selectedEventPackage.value = matched[0];
    selectedDatePackages.value = [];
    activeModalTitle.value = `Keberangkatan: ${formatDateIndo(dateStr)}`;
  } else if (matched.length > 1) {
    selectedEventPackage.value = null;
    selectedDatePackages.value = matched;
    activeModalTitle.value = `Keberangkatan: ${formatDateIndo(dateStr)}`;
  }
}

function closePackageModal() {
  selectedEventPackage.value = null;
  selectedDatePackages.value = [];
}

function goSearch() {
  router.push({ name: 'packages', query: { search: searchQuery.value, type: selectedType.value } });
}

onMounted(async () => {
  try {
    const [featRes, allRes, sliderRes] = await Promise.all([
      axios.get('/api/v1/packages', { params: { per_page: 6 } }),
      axios.get('/api/v1/packages', { params: { per_page: 100 } }),
      axios.get('/api/v1/sliders'),
    ]);
    featuredPackages.value = featRes.data.data;
    allPackages.value = allRes.data.data;
    sliders.value = sliderRes.data.data;

    if (sliders.value.length > 1) {
      slideInterval = setInterval(() => {
        activeSlide.value = (activeSlide.value + 1) % sliders.value.length;
      }, 5000);
    }
  } catch (error) {
    console.error('Gagal mengambil data landing page:', error);
  } finally {
    loading.value = false;
  }
});

onUnmounted(() => {
  if (slideInterval) clearInterval(slideInterval);
});
</script>

<style>
/* FullCalendar Custom Theme Styles matching Brand Palette */
.calendar-wrapper .fc {
  font-family: inherit;
  --fc-border-color: #EDE7DB;
  --fc-button-bg-color: #1E2A3A;
  --fc-button-border-color: #1E2A3A;
  --fc-button-hover-bg-color: #0C121B;
  --fc-button-hover-border-color: #0C121B;
  --fc-button-active-bg-color: #A97C3F;
  --fc-button-active-border-color: #A97C3F;
  --fc-today-bg-color: rgba(169, 124, 63, 0.08);
}

.calendar-wrapper .fc-toolbar-title {
  font-size: 1.15rem !important;
  font-weight: 800 !important;
  color: #2B241C !important;
  text-transform: capitalize;
}

.calendar-wrapper .fc-button {
  border-radius: 0.75rem !important;
  font-size: 0.75rem !important;
  font-weight: 700 !important;
  padding: 0.5rem 0.85rem !important;
  box-shadow: none !important;
}

.calendar-wrapper .fc-col-header-cell {
  background-color: #EDE7DB !important;
  padding: 8px 0 !important;
  font-size: 0.75rem !important;
  font-weight: 700 !important;
  color: #1E2A3A !important;
  text-transform: uppercase;
}

.calendar-wrapper .fc-daygrid-day-number {
  font-size: 0.75rem !important;
  font-weight: 700 !important;
  color: #2B241C !important;
  padding: 4px 6px !important;
}

.calendar-wrapper .fc-event {
  border-radius: 6px !important;
  font-size: 0.7rem !important;
  font-weight: 600 !important;
  padding: 2px 5px !important;
  cursor: pointer !important;
  margin-bottom: 2px !important;
  transition: transform 0.15s ease;
}

.calendar-wrapper .fc-event:hover {
  transform: scale(1.02);
  filter: brightness(1.1);
}

/* Transition Fade untuk Carousel Gambar */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.8s ease-in-out;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes marquee {
  0% {
    transform: translateX(0%);
  }
  100% {
    transform: translateX(-100%);
  }
}

.animate-marquee {
  animation: marquee 25s linear infinite;
}
</style>