<template>
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <RouterLink :to="backUrl" class="inline-flex items-center gap-1.5 text-sm font-medium text-ink/60 hover:text-brass transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Paket
                </RouterLink>
                <h1 class="text-2xl font-bold text-ink">{{ isEdit ? 'Edit Paket Umrah' : 'Tambah Paket Umrah Baru' }}</h1>
                <p class="text-xs text-ink/60 mt-1">Lengkapi seluruh informasi formulir multi-seksi di bawah ini secara detail.</p>
            </div>

            <!-- Status Badge if Edit -->
            <div v-if="isEdit && packageData" class="flex items-center gap-2">
                <span :class="['px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider',
                    packageData.status === 'approved' ? 'bg-sage-100 text-sage' : 'bg-rust-100 text-rust']">
                    {{ packageData.status === 'approved' ? '✓ Disetujui' : '⏳ Pending Review' }}
                </span>
            </div>
        </div>

        <!-- Section Navigation Steps / Tabs -->
        <div class="bg-white rounded-2xl border border-limestone-100 p-2 shadow-sm">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-1.5">
                <button 
                    v-for="(step, idx) in steps" 
                    :key="step.id"
                    type="button"
                    @click="activeStep = idx"
                    :class="[
                        'flex items-center gap-2.5 px-3.5 py-3 rounded-xl text-left transition-all duration-200',
                        activeStep === idx 
                            ? 'bg-indigo-dusk text-white shadow-md shadow-indigo-dusk/20 font-semibold' 
                            : 'text-ink/70 hover:bg-limestone-50 hover:text-ink font-medium'
                    ]"
                >
                    <div :class="[
                        'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0',
                        activeStep === idx ? 'bg-brass text-white' : 'bg-limestone text-ink/70'
                    ]">
                        {{ idx + 1 }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs leading-tight truncate">{{ step.title }}</p>
                        <p class="text-[10px] opacity-60 truncate hidden sm:block">{{ step.subtitle }}</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Form Card Container -->
        <form @submit.prevent="handleSubmit" class="space-y-6">
            
            <!-- ════════════════════ SECTION 1: INFO DASAR ════════════════════ -->
            <div v-show="activeStep === 0" class="bg-white rounded-2xl border border-limestone-100 p-6 md:p-8 space-y-6 shadow-sm">
                <div class="border-b border-limestone-100 pb-4">
                    <h2 class="text-lg font-bold text-ink flex items-center gap-2">
                        <span class="w-7 h-7 bg-brass/10 text-brass rounded-lg flex items-center justify-center text-sm">📋</span>
                        Seksi 1: Informasi Dasar Paket
                    </h2>
                    <p class="text-xs text-ink/60 mt-1">Tentukan judul, tipe paket, jadwal keberangkatan, dan kuota jamaah.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Judul Paket <span class="text-rust">*</span></label>
                        <input 
                            v-model="form.title" 
                            type="text" 
                            required 
                            placeholder="Contoh: Paket Umrah Plus Turki 12 Hari Musim Semi"
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-3 text-sm text-ink placeholder:text-ink/30 focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Tipe Paket <span class="text-rust">*</span></label>
                        <div class="grid grid-cols-3 gap-2.5">
                            <label 
                                v-for="t in ['regular', 'vip', 'promo']" 
                                :key="t" 
                                :class="[
                                    'flex flex-col items-center justify-center p-3 rounded-xl border text-center cursor-pointer transition-all',
                                    form.type === t 
                                        ? 'border-brass bg-brass/5 text-brass font-bold shadow-sm' 
                                        : 'border-limestone-100 bg-limestone-50 text-ink/70 hover:border-brass/40'
                                ]"
                            >
                                <input type="radio" v-model="form.type" :value="t" class="sr-only" />
                                <span class="text-xs uppercase font-bold tracking-wider">{{ t }}</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Tanggal Keberangkatan <span class="text-rust">*</span></label>
                        <input 
                            v-model="form.departure_date" 
                            type="date" 
                            required 
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-3 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Durasi Perjalanan (Hari) <span class="text-rust">*</span></label>
                        <div class="relative">
                            <input 
                                v-model.number="form.duration" 
                                type="number" 
                                min="1" 
                                required 
                                placeholder="9"
                                class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-3 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                            />
                            <span class="absolute right-4 top-3 text-xs text-ink/40 font-semibold">Hari</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Kuota / Kapasitas Jamaah <span class="text-rust">*</span></label>
                        <div class="relative">
                            <input 
                                v-model.number="form.capacity" 
                                type="number" 
                                min="1" 
                                required 
                                placeholder="45"
                                class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-3 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                            />
                            <span class="absolute right-4 top-3 text-xs text-ink/40 font-semibold">Pax</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button 
                        type="button" 
                        @click="activeStep = 1" 
                        class="px-6 py-2.5 bg-indigo-dusk text-white rounded-xl text-xs font-bold hover:bg-indigo-dusk-900 transition-all inline-flex items-center gap-2"
                    >
                        Lanjut ke Harga
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>

            <!-- ════════════════════ SECTION 2: HARGA & PEMBAYARAN ════════════════════ -->
            <div v-show="activeStep === 1" class="bg-white rounded-2xl border border-limestone-100 p-6 md:p-8 space-y-6 shadow-sm">
                <div class="border-b border-limestone-100 pb-4">
                    <h2 class="text-lg font-bold text-ink flex items-center gap-2">
                        <span class="w-7 h-7 bg-brass/10 text-brass rounded-lg flex items-center justify-center text-sm">💰</span>
                        Seksi 2: Rincian Harga & DP
                    </h2>
                    <p class="text-xs text-ink/60 mt-1">Tentukan nominal harga paket per jamaah dan batas minimum uang muka (booking fee).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Harga Paket per Orang (Rp) <span class="text-rust">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-xs text-ink/50 font-bold">Rp</span>
                            <input 
                                v-model.number="form.price" 
                                type="number" 
                                min="0" 
                                required 
                                placeholder="35000000"
                                class="w-full bg-limestone-50 border border-limestone-100 rounded-xl pl-11 pr-4 py-3 text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                            />
                        </div>
                        <p class="text-xs text-brass font-semibold mt-2">
                            Preview: {{ formatRupiah(form.price || 0) }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Uang Muka / Booking Fee (Rp) <span class="text-rust">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-xs text-ink/50 font-bold">Rp</span>
                            <input 
                                v-model.number="form.booking_fee" 
                                type="number" 
                                min="0" 
                                placeholder="5000000"
                                class="w-full bg-limestone-50 border border-limestone-100 rounded-xl pl-11 pr-4 py-3 text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                            />
                        </div>
                        <p class="text-xs text-brass font-semibold mt-2">
                            Preview: {{ formatRupiah(form.booking_fee || 0) }}
                        </p>
                    </div>
                </div>

                <div class="bg-limestone-50 border border-limestone-100 rounded-xl p-4 text-xs text-ink/70 flex items-start gap-3">
                    <span class="text-base">💡</span>
                    <div>
                        <p class="font-bold text-ink mb-0.5">Informasi Booking Fee</p>
                        <p>Booking Fee adalah nominal uang muka yang wajib dibayarkan jamaah saat pertama kali melakukan pemesanan untuk mengamankan seat.</p>
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <button type="button" @click="activeStep = 0" class="px-5 py-2.5 border border-limestone-100 rounded-xl text-xs font-bold text-ink/70 hover:bg-limestone transition-all">← Kembali</button>
                    <button type="button" @click="activeStep = 2" class="px-6 py-2.5 bg-indigo-dusk text-white rounded-xl text-xs font-bold hover:bg-indigo-dusk-900 transition-all inline-flex items-center gap-2">
                        Lanjut ke Transportasi & Hotel
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>

            <!-- ════════════════════ SECTION 3: MASKAPAI & HOTEL ════════════════════ -->
            <div v-show="activeStep === 2" class="bg-white rounded-2xl border border-limestone-100 p-6 md:p-8 space-y-6 shadow-sm">
                <div class="border-b border-limestone-100 pb-4">
                    <h2 class="text-lg font-bold text-ink flex items-center gap-2">
                        <span class="w-7 h-7 bg-brass/10 text-brass rounded-lg flex items-center justify-center text-sm">✈️</span>
                        Seksi 3: Maskapai, Rute & Akomodasi Hotel
                    </h2>
                    <p class="text-xs text-ink/60 mt-1">Pilih maskapai penerbangan, rute perjalanan, dan hotel yang digunakan selama di Makkah & Madinah.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Maskapai Penerbangan <span class="text-rust">*</span></label>
                        <select 
                            v-model.number="form.air_line_id" 
                            required 
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-3 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        >
                            <option value="">-- Pilih Maskapai --</option>
                            <option v-for="a in airlines" :key="a.id" :value="a.id">{{ a.nama }} ({{ a.keterangan || 'Standard' }})</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Rute Perjalanan <span class="text-rust">*</span></label>
                        <select 
                            v-model.number="form.route_id" 
                            required 
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl px-4 py-3 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all"
                        >
                            <option value="">-- Pilih Rute Perjalanan --</option>
                            <option v-for="r in routes" :key="r.id" :value="r.id">{{ r.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Hotel Selection with Cards -->
                <div>
                    <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">
                        Pilihan Hotel (Pilih minimal 1 hotel) <span class="text-rust">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        <label 
                            v-for="h in hotels" 
                            :key="h.id" 
                            :class="[
                                'flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all duration-200 select-none',
                                form.hotel_ids.includes(h.id) 
                                    ? 'border-brass bg-brass/5 shadow-sm ring-1 ring-brass' 
                                    : 'border-limestone-100 bg-limestone-50/50 hover:bg-limestone-50'
                            ]"
                        >
                            <input 
                                type="checkbox" 
                                :value="h.id" 
                                v-model="form.hotel_ids" 
                                class="mt-0.5 w-4 h-4 rounded text-brass focus:ring-brass/30 accent-brass"
                            />
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <p class="text-xs font-bold text-ink">{{ h.nama }}</p>
                                    <span class="text-[10px] bg-indigo-dusk-100 text-indigo-dusk px-1.5 py-0.5 rounded font-semibold">{{ h.lokasi }}</span>
                                </div>
                                <p class="text-[11px] text-ink/60 mt-1 line-clamp-2">{{ h.keterangan || 'Fasilitas lengkap bintang 5' }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <button type="button" @click="activeStep = 1" class="px-5 py-2.5 border border-limestone-100 rounded-xl text-xs font-bold text-ink/70 hover:bg-limestone transition-all">← Kembali</button>
                    <button type="button" @click="activeStep = 3" class="px-6 py-2.5 bg-indigo-dusk text-white rounded-xl text-xs font-bold hover:bg-indigo-dusk-900 transition-all inline-flex items-center gap-2">
                        Lanjut ke Detail & Syarat
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>

            <!-- ════════════════════ SECTION 4: DETAIL & PERSYARATAN ════════════════════ -->
            <div v-show="activeStep === 3" class="bg-white rounded-2xl border border-limestone-100 p-6 md:p-8 space-y-6 shadow-sm">
                <div class="border-b border-limestone-100 pb-4">
                    <h2 class="text-lg font-bold text-ink flex items-center gap-2">
                        <span class="w-7 h-7 bg-brass/10 text-brass rounded-lg flex items-center justify-center text-sm">📝</span>
                        Seksi 4: Detail, Fasilitas & Persyaratan
                    </h2>
                    <p class="text-xs text-ink/60 mt-1">Uraikan deskripsi lengkap jadwal kegiatan, fasilitas termasuk / tidak termasuk, dan syarat dokumen jamaah.</p>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Detail & Rangkaian Perjalanan <span class="text-rust">*</span></label>
                        <textarea 
                            v-model="form.details" 
                            rows="6" 
                            required
                            placeholder="Deskripsikan program umrah, itinerary harian, ziarah Makkah & Madinah, fasilitas konsumsi, pembimbing ibadah / muthawwif..."
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl p-4 text-sm text-ink placeholder:text-ink/30 focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all resize-y"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">Persyaratan & Dokumen Jamaah <span class="text-rust">*</span></label>
                        <textarea 
                            v-model="form.requirement" 
                            rows="5" 
                            required
                            placeholder="Contoh:&#10;1. Paspor asli berlaku minimal 7 bulan dengan nama minimal 2 kata&#10;2. Pas foto background putih fokus wajah 80% (4x6 = 4 lembar)&#10;3. Buku Kuning Vaksin Meningitis&#10;4. KTP & Kartu Keluarga (fotokopi)"
                            class="w-full bg-limestone-50 border border-limestone-100 rounded-xl p-4 text-sm text-ink placeholder:text-ink/30 focus:outline-none focus:ring-2 focus:ring-brass/40 focus:border-brass transition-all resize-y"
                        />
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <button type="button" @click="activeStep = 2" class="px-5 py-2.5 border border-limestone-100 rounded-xl text-xs font-bold text-ink/70 hover:bg-limestone transition-all">← Kembali</button>
                    <button type="button" @click="activeStep = 4" class="px-6 py-2.5 bg-indigo-dusk text-white rounded-xl text-xs font-bold hover:bg-indigo-dusk-900 transition-all inline-flex items-center gap-2">
                        Lanjut ke Galeri Foto
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>

            <!-- ════════════════════ SECTION 5: GALERI FOTO ════════════════════ -->
            <div v-show="activeStep === 4" class="bg-white rounded-2xl border border-limestone-100 p-6 md:p-8 space-y-6 shadow-sm">
                <div class="border-b border-limestone-100 pb-4">
                    <h2 class="text-lg font-bold text-ink flex items-center gap-2">
                        <span class="w-7 h-7 bg-brass/10 text-brass rounded-lg flex items-center justify-center text-sm">📸</span>
                        Seksi 5: Galeri Foto Paket
                    </h2>
                    <p class="text-xs text-ink/60 mt-1">Unggah foto banner dan foto fasilitas paket umrah untuk menarik minat calon jamaah.</p>
                </div>

                <!-- Existing Images Grid -->
                <div v-if="existingImages.length > 0" class="space-y-3">
                    <p class="text-xs font-bold text-ink uppercase tracking-wider">Foto Paket Saat Ini ({{ existingImages.length }})</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        <div 
                            v-for="img in existingImages" 
                            :key="img.id" 
                            class="group relative aspect-4/3 rounded-xl overflow-hidden border border-limestone-100 bg-limestone-50 shadow-sm"
                        >
                            <img :src="img.url" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            
                            <!-- Primary Badge -->
                            <div v-if="img.is_primary" class="absolute top-2 left-2 bg-brass text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-md">
                                ★ Foto Utama
                            </div>

                            <!-- Image Action Overlay -->
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 p-2">
                                <button 
                                    v-if="!img.is_primary" 
                                    type="button" 
                                    @click="setPrimary(img)"
                                    class="p-1.5 bg-brass hover:bg-brass-600 text-white rounded-lg text-xs font-semibold transition-colors"
                                    title="Jadikan Foto Utama"
                                >
                                    ★ Jadikan Utama
                                </button>
                                <button 
                                    type="button" 
                                    @click="deleteImage(img)"
                                    class="p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs transition-colors"
                                    title="Hapus Foto"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload New Image Input -->
                <div class="border-2 border-dashed border-limestone-100 hover:border-brass/50 rounded-2xl p-6 md:p-8 text-center bg-limestone-50/50 transition-colors">
                    <input 
                        type="file" 
                        ref="fileInput" 
                        multiple 
                        accept="image/*" 
                        @change="handleFileSelect" 
                        class="hidden" 
                        id="package-images-input"
                    />
                    <label for="package-images-input" class="cursor-pointer block">
                        <div class="w-12 h-12 rounded-2xl bg-brass/10 text-brass flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-ink">Klik untuk memilih foto paket</p>
                        <p class="text-xs text-ink/50 mt-1">Mendukung format JPG, PNG, WEBP (Maksimal 5MB per file)</p>
                    </label>
                </div>

                <!-- Selected Pending Upload Previews -->
                <div v-if="pendingImages.length > 0" class="space-y-3">
                    <p class="text-xs font-bold text-ink uppercase tracking-wider">Foto Baru yang Akan Diunggah ({{ pendingImages.length }})</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div v-for="(pImg, pIdx) in pendingImages" :key="pIdx" class="relative aspect-4/3 rounded-xl overflow-hidden border border-limestone-100 bg-limestone-50">
                            <img :src="pImg.preview" class="w-full h-full object-cover" />
                            <button 
                                type="button" 
                                @click="removePendingImage(pIdx)"
                                class="absolute top-1.5 right-1.5 p-1 bg-black/60 hover:bg-black text-white rounded-full text-xs"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between pt-4 border-t border-limestone-100">
                    <button type="button" @click="activeStep = 3" class="px-5 py-2.5 border border-limestone-100 rounded-xl text-xs font-bold text-ink/70 hover:bg-limestone transition-all">← Kembali</button>
                    <button 
                        type="submit" 
                        :disabled="saving" 
                        class="px-8 py-3 bg-brass hover:bg-brass-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-brass/20 disabled:opacity-50 transition-all inline-flex items-center gap-2"
                    >
                        <svg v-if="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>{{ saving ? 'Menyimpan Paket...' : (isEdit ? 'Simpan Perubahan Paket' : 'Simpan & Publikasikan Paket') }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';
import { useFormatCurrency } from '../composables/useFormatCurrency';
import { useToast } from '../composables/useToast';
import axios from 'axios';

const props = defineProps({
    portal: {
        type: String,
        default: 'admin' // 'admin' | 'merchant'
    }
});

const route = useRoute();
const router = useRouter();
const { formatRupiah } = useFormatCurrency();
const { success, error: showError } = useToast();

const isEdit = computed(() => !!route.params.slug);
const backUrl = computed(() => props.portal === 'admin' ? '/admin/packages' : '/merchant/packages');

const activeStep = ref(0);
const saving = ref(false);
const packageData = ref(null);
const airlines = ref([]);
const routes = ref([]);
const hotels = ref([]);
const existingImages = ref([]);
const pendingImages = ref([]);

const steps = [
    { id: 1, title: 'Info Dasar', subtitle: 'Judul, Tipe & Kuota' },
    { id: 2, title: 'Harga & DP', subtitle: 'Biaya & Uang Muka' },
    { id: 3, title: 'Transport & Hotel', subtitle: 'Maskapai & Penginapan' },
    { id: 4, title: 'Detail & Syarat', subtitle: 'Itinerary & Dokumen' },
    { id: 5, title: 'Galeri Foto', subtitle: 'Upload Gambar Paket' },
];

const form = ref({
    title: '',
    type: 'regular',
    departure_date: '',
    duration: 9,
    capacity: 40,
    price: 0,
    booking_fee: 0,
    air_line_id: '',
    route_id: '',
    hotel_ids: [],
    details: '',
    requirement: ''
});

onMounted(async () => {
    try {
        const [aRes, rRes, hRes] = await Promise.all([
            axios.get('/api/v1/airlines'),
            axios.get('/api/v1/routes'),
            axios.get('/api/v1/hotels')
        ]);
        airlines.value = aRes.data.data;
        routes.value = rRes.data.data;
        hotels.value = hRes.data.data;

        if (isEdit.value) {
            await fetchPackageData(route.params.slug);
        }
    } catch {
        showError('Gagal memuat data master.');
    }
});

async function fetchPackageData(id) {
    try {
        const url = props.portal === 'admin' 
            ? `/api/v1/admin/packages/${id}` 
            : `/api/v1/merchant/packages/${id}`;
        const { data } = await axios.get(url);
        const pkg = data.data;
        packageData.value = pkg;

        form.value = {
            title: pkg.title,
            type: pkg.type,
            departure_date: pkg.departure_date ? pkg.departure_date.split('T')[0] : '',
            duration: pkg.duration,
            capacity: pkg.capacity,
            price: Number(pkg.price),
            booking_fee: Number(pkg.booking_fee || 0),
            air_line_id: pkg.airline?.id || '',
            route_id: pkg.route?.id || '',
            hotel_ids: (pkg.hotels || []).map(h => h.id),
            details: pkg.details || '',
            requirement: pkg.requirement || ''
        };

        existingImages.value = pkg.images || [];
    } catch (e) {
        showError('Gagal memuat detail paket.');
    }
}

function handleFileSelect(e) {
    const files = Array.from(e.target.files);
    for (const file of files) {
        if (file.size > 5 * 1024 * 1024) {
            showError(`File ${file.name} melebihi 5MB.`);
            continue;
        }
        const preview = URL.createObjectURL(file);
        pendingImages.value.push({ file, preview });
    }
    e.target.value = '';
}

function removePendingImage(idx) {
    pendingImages.value.splice(idx, 1);
}

async function deleteImage(img) {
    if (!confirm('Hapus foto ini?')) return;
    try {
        await axios.delete(`/api/v1/packages/images/${img.id}`);
        existingImages.value = existingImages.value.filter(i => i.id !== img.id);
        success('Foto berhasil dihapus.');
    } catch {
        showError('Gagal menghapus foto.');
    }
}

async function setPrimary(img) {
    try {
        await axios.put(`/api/v1/packages/images/${img.id}/primary`);
        existingImages.value.forEach(i => i.is_primary = (i.id === img.id));
        success('Foto utama diperbarui.');
    } catch {
        showError('Gagal mengubah foto utama.');
    }
}

async function handleSubmit() {
    if (form.value.hotel_ids.length === 0) {
        showError('Pilih minimal 1 hotel di Seksi 3.');
        activeStep.value = 2;
        return;
    }

    saving.value = true;
    try {
        let savedPackageSlug = route.params.slug;

        if (isEdit.value) {
            const url = props.portal === 'admin' 
                ? `/api/v1/admin/packages/${savedPackageSlug}` 
                : `/api/v1/merchant/packages/${savedPackageSlug}`;
            await axios.put(url, form.value);
        } else {
            const url = props.portal === 'admin' 
                ? '/api/v1/admin/packages' 
                : '/api/v1/merchant/packages';
            const { data } = await axios.post(url, form.value);
            savedPackageSlug = data.data.slug;
        }

        // Upload any pending new images
        if (pendingImages.value.length > 0 && savedPackageSlug) {
            for (let i = 0; i < pendingImages.value.length; i++) {
                const item = pendingImages.value[i];
                const fd = new FormData();
                fd.append('image', item.file);
                if (existingImages.value.length === 0 && i === 0) {
                    fd.append('is_primary', '1');
                }
                await axios.post(`/api/v1/packages/${savedPackageSlug}/images`, fd);
            }
        }

        success(isEdit.value ? 'Paket berhasil diperbarui!' : 'Paket baru berhasil disimpan!');
        router.push(backUrl.value);
    } catch (err) {
        showError(err.response?.data?.message || 'Gagal menyimpan paket.');
    } finally {
        saving.value = false;
    }
}
</script>
