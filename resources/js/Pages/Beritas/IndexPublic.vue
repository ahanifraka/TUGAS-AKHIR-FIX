<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const props = defineProps({
    beritas: { type: Object, required: true },
    berita_categories: { type: Array, required: true },
    random_beritas: { type: Array, required: true },
    filters: { type: Object, required: true },
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');

// Loading states
const isLoading = ref(true);
const loadedImages = ref(new Set());

// Initialize loading state
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 300);
});

// Track image loading
const handleImageLoad = (id) => {
    loadedImages.value.add(id);
};

const isImageLoaded = (id) => {
    return loadedImages.value.has(id);
};

// Watch for changes and update URL
watch([search, category], () => {
    isLoading.value = true;
    router.get(route('beritas.indexPublic'), {
        search: search.value || undefined,
        category: category.value || undefined,
    }, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            setTimeout(() => {
                isLoading.value = false;
                loadedImages.value.clear(); // Clear loaded images on new search
            }, 200);
        }
    });
}, { debounce: 300 });

const clearFilters = () => {
    search.value = '';
    category.value = '';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Generate pagination tokens: 3 around current, ellipsis, last 3
const displayPages = computed(() => {
    const total = props.beritas?.meta?.last_page ?? 1;
    const current = props.beritas?.meta?.current_page ?? 1;

    // Small totals: show all pages
    if (total <= 6) {
        return Array.from({ length: total }, (_, i) => i + 1);
    }

    // Left window: 3 pages ending at current (start clamped to 1)
    const leftStart = Math.max(1, current - 2);
    const left = [leftStart, leftStart + 1, leftStart + 2].filter((p) => p <= total);

    // Right window: last 3 pages
    const right = [total - 2, total - 1, total];

    const lastLeft = left[left.length - 1];
    const firstRight = right[0];

    // If overlapping or adjacent, return union without ellipsis
    if (lastLeft >= firstRight - 1) {
        const union = Array.from(new Set([...left, ...right]));
        return union;
    }

    // Otherwise: left, ellipsis, right
    return [...left, 'ellipsis', ...right];
});

const breadcrumbItems = [
    { label: 'Berita', url: '/berita' },
];

const heroTitle = 'Berita';
const heroDescription = 'Informasi Terkini dari BPBUMD Jakarta.';

</script>

<template>

    <Head title="Berita">
        <meta name="description" content="Kumpulan berita terbaru dari BPBUMD" />
        <meta name="keywords" content="berita, BPBUMD, informasi, terbaru" />
    </Head>

    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-12 gap-8">

                <!-- Sidebar -->
                <div class="col-span-12 md:col-span-3">

                    <!-- Search -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Cari Berita</h3>
                        <div class="relative">
                            <input v-model="search" type="text" placeholder="Masukkan kata kunci..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white" />
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Kategori</h3>
                        <div class="space-y-2">
                            <button @click="category = ''" :class="[
                                'block w-full text-left px-3 py-2 rounded-lg text-sm transition-colors',
                                !category ? 'bg-blue-100 text-blue-700 font-medium dark:bg-primary dark:text-gray-900' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white'
                            ]">
                                Semua Kategori
                            </button>
                            <button v-for="cat in berita_categories" :key="cat.id" @click="category = cat.category_name"
                                :class="[
                                    'block w-full text-left px-3 py-2 rounded-lg text-sm transition-colors',
                                    category === cat.category_name ? 'bg-blue-100 text-blue-700 font-medium dark:bg-primary dark:text-gray-900' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white'
                                ]">
                                {{ cat.category_name }}
                            </button>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <div v-if="search || category" class="mb-8">
                        <button @click="clearFilters"
                            class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm dark:bg-gray-700 dark:text-white">
                            Hapus Filter
                        </button>
                    </div>

                    <!-- Random News -->
                    <div v-if="random_beritas.length > 0" class="mb-8 hidden md:block">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Berita Lainnya</h3>
                        <div class="space-y-4">
                            <div v-for="item in random_beritas" :key="item.id" class="flex gap-3">
                                <div class="flex-1 min-w-0">
                                    <Link :href="route('beritas.showPublic', item.slug)"
                                        class="text-sm font-medium text-gray-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400 line-clamp-2">
                                    {{ item.title }}
                                    </Link>
                                    <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">   
                                        {{ formatDate(item.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Main Content -->
                <div class="col-span-12 md:col-span-9">

                    <!-- News Grid -->
                    <div v-if="isLoading" 
                        class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                        <!-- Skeleton Loading -->
                        <div v-for="n in 8" :key="`skeleton-${n}`"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden animate-pulse">
                            <div class="aspect-video bg-gray-300 dark:bg-gray-700"></div>
                            <div class="p-4">
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-20 mb-4"></div>
                                <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded w-full mb-2"></div>
                                <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mb-4"></div>
                                <div class="flex flex-col gap-2">
                                    <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-24"></div>
                                    <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-32"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="beritas.data.length > 0"
                        class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

                        <Link v-for="berita in beritas.data" :key="berita.id"
                            :href="route('beritas.showPublic', berita.slug)"
                            class="group block bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-all duration-300 animate-fade-in">

                            <div class="aspect-video overflow-hidden relative bg-gray-200 dark:bg-gray-700">
                                <!-- Image Skeleton -->
                                <div v-if="!isImageLoaded(berita.id)" 
                                    class="absolute inset-0 animate-pulse bg-gray-300 dark:bg-gray-700"></div>
                                
                                <!-- Actual Image -->
                                <img 
                                    :src="berita.image" 
                                    :alt="berita.title"
                                    @load="handleImageLoad(berita.id)"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300"
                                    :class="isImageLoaded(berita.id) ? 'opacity-100' : 'opacity-0'"
                                    loading="lazy"
                                    decoding="async" />
                            </div>

                            <div class="p-4">
                                <div v-if="berita.category" class="mb-4">
                                    <span
                                        class="inline-block px-2 py-1 bg-blue-100 text-primary text-xs font-medium rounded dark:bg-primary dark:text-gray-900">
                                        {{ berita.category.name }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 line-clamp-2 group-hover:text-primary transition-colors">
                                    {{ berita.title && berita.title.length > 50
                                        ? berita.title.substring(0, 47) + '...'
                                    : berita.title }}
                                </h3>

                                <div class="flex flex-col items-start justify-between text-xs text-gray-500 dark:text-gray-400 gap-2">
                                    <span>{{ formatDate(berita.created_at) }}</span>
                                    <span
                                        class="group-hover:text-primary font-medium dark:text-white dark:group-hover:text-blue-400 transition-colors">
                                    Baca Selengkapnya →
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="!isLoading && beritas.data.length === 0" class="text-center py-12 animate-fade-in">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada berita</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <span v-if="search || category">
                                Tidak ditemukan berita yang sesuai dengan filter Anda.
                            </span>
                            <span v-else>
                                Belum ada berita yang dipublikasikan.
                            </span>
                        </p>
                        <div v-if="search || category" class="mt-6">
                            <button @click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-white dark:bg-blue-600 dark:hover:bg-blue-700">
                                Hapus Filter
                            </button>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="beritas.data.length > 0 && beritas.meta.last_page > 1"
                        class="flex items-center justify-between">
                        <div class="flex-1 flex justify-center sm:hidden">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <Link v-if="beritas.links.prev" :href="beritas.links.prev"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Sebelumnya</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </Link>
                                <span v-else
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>

                                <template v-for="t in displayPages" :key="t">
                                    <span v-if="t === 'ellipsis'"
                                        class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                        ...
                                    </span>
                                    <Link v-else :href="route('beritas.indexPublic', { search: search || undefined, category: category || undefined, page: t })"
                                        :class="[
                                            'relative inline-flex items-center px-3 py-2 border text-sm font-medium',
                                            t === beritas.meta.current_page
                                                ? 'bg-blue-100 text-blue-700 border-blue-300'
                                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                        ]">
                                        {{ t }}
                                    </Link>
                                </template>

                                <Link v-if="beritas.links.next" :href="beritas.links.next"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Selanjutnya</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </Link>
                                <span v-else
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </nav>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-white">
                                    Menampilkan
                                    <span class="font-medium">{{ ((beritas.meta.current_page - 1) *
                                        beritas.meta.per_page) + 1 }}</span>
                                    sampai
                                    <span class="font-medium">{{ Math.min(beritas.meta.current_page *
                                        beritas.meta.per_page, beritas.meta.total) }}</span>
                                    dari
                                    <span class="font-medium">{{ beritas.meta.total }}</span>
                                    hasil
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                    aria-label="Pagination">
                                    <Link v-if="beritas.links.prev" :href="beritas.links.prev"
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                    <span class="sr-only">Sebelumnya</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    </Link>
                                    <span v-else
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-500">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>

                                    <template v-for="t in displayPages" :key="t">
                                        <span v-if="t === 'ellipsis'"
                                            class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                            ...
                                        </span>
                                        <Link v-else :href="route('beritas.indexPublic', { search: search || undefined, category: category || undefined, page: t })"
                                            :class="[
                                                'relative inline-flex items-center px-3 py-2 border text-sm font-medium',
                                                t === beritas.meta.current_page
                                                    ? 'bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-900 dark:text-blue-200 dark:border-blue-800'
                                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700'
                                            ]">
                                            {{ t }}
                                        </Link>
                                    </template>

                                    <Link v-if="beritas.links.next" :href="beritas.links.next"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                    <span class="sr-only">Selanjutnya</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    </Link>
                                    <span v-else
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-500">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

    
    </GuestLayout>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

/* Stagger animation for news cards */
.animate-fade-in:nth-child(1) { animation-delay: 0.05s; }
.animate-fade-in:nth-child(2) { animation-delay: 0.1s; }
.animate-fade-in:nth-child(3) { animation-delay: 0.15s; }
.animate-fade-in:nth-child(4) { animation-delay: 0.2s; }
.animate-fade-in:nth-child(5) { animation-delay: 0.25s; }
.animate-fade-in:nth-child(6) { animation-delay: 0.3s; }
.animate-fade-in:nth-child(7) { animation-delay: 0.35s; }
.animate-fade-in:nth-child(8) { animation-delay: 0.4s; }
</style>
