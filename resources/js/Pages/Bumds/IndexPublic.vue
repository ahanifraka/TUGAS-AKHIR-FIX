<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, nextTick, onMounted } from 'vue';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const props = defineProps({
    bumds: { type: Object, required: true },
    sektors: { type: Array, required: true },
    filters: { type: Object, required: true },
});

const search = ref(props.filters.search || '');
const sektor = ref(props.filters.sektor || '');

// Watch for changes and update URL
watch([search, sektor], () => {
    router.get(route('bumds.indexPublic'), {
        search: search.value || undefined,
        sektor: sektor.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, { debounce: 300 });

const clearFilters = () => {
    search.value = '';
    sektor.value = '';
};

const breadcrumbItems = [
    { label: 'Informasi BUMD', url: '#' },
    { label: 'Daftar BUMD', url: '/bumd' },
];

const heroTitle = 'Profil BUMD dan Perusahaan Patungan';
const heroDescription = 'Daftar Badan Usaha Milik Daerah (BUMD) Provinsi DKI Jakarta';

// Helper: get initial letter from kode or name
const getInitial = (name, kode) => {
    const base = (kode || name || '').toString().trim();
    return base ? base.charAt(0).toUpperCase() : '?';
};

// Fallback image for BUMD logos
const fallbackLogo = '/images/jaya-raya.png';
const setFallbackImage = (e) => { e.target.src = fallbackLogo; };

// Staggered animation setup
const setStaggeredDelay = () => {
    nextTick(() => {
        const items = document.querySelectorAll('.list-item');
        items.forEach((item, index) => {
            item.style.setProperty('--enter-delay', index);
            item.style.setProperty('--leave-delay', items.length - index - 1);
        });
    });
};

// Apply staggered delays on mount and when data changes
onMounted(() => {
    setStaggeredDelay();
});

watch(() => props.bumds.data, () => {
    setStaggeredDelay();
}, { deep: true });
</script>

<template>

    <Head title="Profil BUMD dan Perusahaan Patungan">
        <meta name="description" content="Daftar lengkap Badan Usaha Milik Daerah (BUMD): informasi kontak, bidang usaha, alamat, telepon, email, dan website perusahaan daerah." />
        <meta name="keywords" content="BUMD, badan usaha milik daerah, perusahaan daerah" />
    </Head>

    <GuestLayout>

        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />

        <main class="px-4 py-12 bg-gray-50 dark:bg-gray-900">
            <div class="flex flex-col gap-8">

                <!-- Filter -->
                <div class="max-w-5xl mx-auto">

                    <!-- Search -->
                    <div class="mb-8">
                        <div class="relative">
                            <input v-model="search" type="text" placeholder="Masukkan nama BUMD..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                        </div>
                    </div>

                    <!-- Sektor Filter -->
                    <div class="mb-8">
                        <div class="flex flex-row flex-wrap items-center justify-center gap-4">
                            <button @click="sektor = ''" :class="[
                                'block text-center px-3 py-2 rounded-full text-sm transition-colors',
                                !sektor ? 'bg-blue-100 text-primary font-medium dark:bg-primary dark:text-gray-900' : 'text-primary dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
                            ]">
                                Semua Sektor
                            </button>
                            <button v-for="sektorItem in sektors" :key="sektorItem" @click="sektor = sektorItem"
                                :class="[
                                    'block text-center px-3 py-2 rounded-full text-sm transition-colors',
                                    sektor === sektorItem ? 'bg-blue-100 text-primary font-medium dark:bg-primary dark:text-gray-900' : 'text-primary hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white'
                                ]">
                                {{ sektorItem }}
                            </button>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <div v-if="search || sektor" class="mb-8">
                        <button @click="clearFilters"
                            class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            Hapus Filter
                        </button>
                    </div>

                </div>

                <!-- Main Content -->
                <div class="col-span-6 md:col-span-4">

                    <!-- BUMD Grid -->
                    <TransitionGroup v-if="bumds.data.length > 0"
                        name="list" tag="div"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 w-full max-w-6xl mx-auto">

                        <Link v-for="bumd in bumds.data" :key="bumd.id" :href="'/bumd/' + bumd.kode"
                            class="list-item group rounded-lg overflow-hidden transition-all hover:scale-105 transform bg-white">

                            <div class="p-6">
                                <!-- Logo and Header -->
                                <div class="flex flex-col items-center gap-4 mb-4">

                                    <div v-if="bumd.logo && bumd.logo.trim() !== ''" class="logo-container flex-shrink-0 transition-transform duration-500 group-hover:scale-105">
                                        <img :src="bumd.logo" :alt="bumd.nama" :title="bumd.nama" @error="setFallbackImage"
                                            class="w-48 h-48 object-contain transition-all duration-300" />
                                    </div>

                                    <div v-else class="logo-container flex-shrink-0 transition-transform duration-500 group-hover:scale-105">
                                        <img :src="fallbackLogo" :alt="bumd.nama" :title="bumd.nama"
                                            class="w-48 h-48 object-contain transition-all duration-300" />
                                    </div>
                                    
                                    <div class="flex flex-col min-w-0 justify-center items-center w-full">
                                        <h3 class="text-normal font-bold text-gray-900 mb-1 text-center">
                                            {{ bumd.nama }}
                                        </h3>
                                        <p v-if="bumd.nama_pendek" class="text-sm text-gray-600 mb-4">
                                            {{ bumd.nama_pendek }}
                                        </p>
                                        <div class="flex flex-wrap gap-2">
                                            <span v-if="bumd.sektor" class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">
                                                {{ bumd.sektor }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </TransitionGroup>

                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h4m6 0h2M7 15h10">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada BUMD</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            <span v-if="search || sektor">
                                Tidak ditemukan BUMD yang sesuai dengan filter Anda.
                            </span>
                            <span v-else>
                                Belum ada BUMD yang terdaftar.
                            </span>
                        </p>
                        <div v-if="search || sektor" class="mt-6">
                            <button @click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                                Hapus Filter
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </main>

    </GuestLayout>
</template>

<style scoped>
/* Enhanced list transitions with staggered animations */
.list-enter-active {
  transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  transition-delay: calc(var(--enter-delay, 0) * 50ms);
}

.list-leave-active {
  transition: all 0.4s cubic-bezier(0.55, 0.06, 0.68, 0.19);
  transition-delay: calc(var(--leave-delay, 0) * 30ms);
}

.list-enter-from {
  opacity: 0;
  transform: scale(0.8) translateY(30px) rotateX(15deg);
  filter: blur(4px);
}

.list-leave-to {
  opacity: 0;
  transform: scale(0.9) translateY(-20px) rotateX(-10deg);
  filter: blur(2px);
}

.list-move {
  transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

/* Enhanced card hover animations */
.group {
  transform-style: preserve-3d;
  perspective: 1000px;
  will-change: transform;
}

.group:hover {
  transform: translateY(-12px) scale(1.02) rotateX(2deg);
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
}

.group:hover .logo-container,
.group:hover .logo-placeholder {
  transform: scale(1.08) rotateY(8deg) translateZ(20px);
}

.group:hover .logo-container img {
  transform: scale(1.025);
  filter: brightness(1.15) contrast(1.1);
}

.group:hover .logo-placeholder span {
  transform: scale(1.1) translateZ(10px);
  text-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

/* Smooth loading animation for images */
.group img {
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.group:hover img {
  transform: scale(1.1);
  filter: brightness(1.1);
}

/* Enhanced fade transition for empty state */
.fade-enter-active,
.fade-leave-active {
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Smooth filter button transitions */
button {
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

button:hover {
  transform: translateY(-2px);
}

/* Input field smooth focus transitions */
input {
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

input:focus {
  transform: translateY(-1px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
}
</style>