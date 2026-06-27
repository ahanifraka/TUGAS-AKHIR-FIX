<!-- resources/js/Pages/Pengumuman/IndexPublic.vue -->
<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch, computed, onMounted } from "vue";

import GuestLayout from "@/Layouts/GuestLayout.vue";
import Hero from "@/Components/Hero.vue";

// Props dengan default untuk pengumuman
const props = defineProps({
    pengumuman: {
        type: Object,
        default: () => ({
            data: [],
            meta: {
                current_page: 1,
                last_page: 1,
                per_page: 12,
                total: 0,
            },
            links: {},
        }),
    },
    tipeOptions: { type: Array, required: true },
    random_pengumuman: { type: Array, required: true },
    filters: { type: Object, required: true },
});

const search = ref(props.filters.search || "");
const tipe = ref(props.filters.tipe || "");

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
watch(
    [search, tipe],
    () => {
        isLoading.value = true;
        router.get(
            route("pengumuman.indexPublic"),
            {
                search: search.value || undefined,
                tipe: tipe.value || undefined,
            },
            {
                preserveState: true,
                replace: true,
                onFinish: () => {
                    setTimeout(() => {
                        isLoading.value = false;
                        loadedImages.value.clear(); // Clear loaded images on new search
                    }, 200);
                },
            }
        );
    },
    { debounce: 300 }
);

const clearFilters = () => {
    search.value = "";
    tipe.value = "";
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

// Generate pagination tokens
const displayPages = computed(() => {
    const total = props.pengumuman?.meta?.last_page ?? 1;
    const current = props.pengumuman?.meta?.current_page ?? 1;

    if (total <= 6) {
        return Array.from({ length: total }, (_, i) => i + 1);
    }

    const leftStart = Math.max(1, current - 2);
    const left = [leftStart, leftStart + 1, leftStart + 2].filter(
        (p) => p <= total
    );

    const right = [total - 2, total - 1, total];

    const lastLeft = left[left.length - 1];
    const firstRight = right[0];

    if (lastLeft >= firstRight - 1) {
        const union = Array.from(new Set([...left, ...right]));
        return union;
    }

    return [...left, "ellipsis", ...right];
});

const breadcrumbItems = [{ label: "Pengumuman", url: "/pengumuman" }];

const heroTitle = "Pengumuman";
const heroDescription =
    "Informasi resmi dan pemberitahuan dari BPBUMD Jakarta.";
</script>

<template>
    <Head title="Pengumuman">
        <meta
            name="description"
            content="Kumpulan pengumuman resmi dari BPBUMD"
        />
        <meta
            name="keywords"
            content="pengumuman, BPBUMD, informasi resmi, pemberitahuan"
        />
    </Head>

    <GuestLayout>
        <Hero
            :breadcrumb="breadcrumbItems"
            :title="heroTitle"
            :description="heroDescription"
        />

        <main class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-12 gap-8">
                <!-- Sidebar -->
                <div class="col-span-12 md:col-span-3">
                    <!-- Search & Filter Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            Cari Pengumuman
                        </h3>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Masukkan kata kunci..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                        />
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            Kategori
                        </h3>
                        <div class="space-y-2">
                            <button
                                @click="tipe = ''"
                                :class="[
                                    'block w-full text-left px-3 py-2 rounded-lg text-sm transition-colors',
                                    !tipe
                                        ? 'bg-blue-100 text-blue-700 font-medium dark:bg-primary dark:text-gray-900'
                                        : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white',
                                ]"
                            >
                                Semua Kategori
                            </button>
                            <button
                                v-for="option in tipeOptions"
                                :key="option"
                                @click="tipe = option"
                                :class="[
                                    'block w-full text-left px-3 py-2 rounded-lg text-sm transition-colors',
                                    tipe === option
                                        ? 'bg-blue-100 text-blue-700 font-medium dark:bg-primary dark:text-gray-900'
                                        : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white',
                                ]"
                            >
                                {{
                                    option.charAt(0).toUpperCase() +
                                    option.slice(1)
                                }}
                            </button>
                        </div>
                    </div>

                    <div v-if="search || tipe" class="mb-8">
                        <button
                            @click="clearFilters"
                            class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm dark:bg-gray-700 dark:text-white"
                        >
                            Hapus Filter
                        </button>
                    </div>

                    <!-- Random Pengumuman -->
                    <div
                        v-if="random_pengumuman.length > 0"
                        class="mb-8 hidden md:block"
                    >
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white mb-4"
                        >
                            Pengumuman Lainnya
                        </h3>
                        <div class="space-y-4">
                            <div
                                v-for="item in random_pengumuman"
                                :key="item.id"
                                class="flex gap-3"
                            >
                                <div class="flex-1 min-w-0">
                                    <Link
                                        :href="
                                            route(
                                                'pengumuman.showPublic',
                                                item.slug
                                            )
                                        "
                                        class="text-sm font-medium text-gray-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400 line-clamp-2"
                                    >
                                        {{ item.judul }}
                                    </Link>
                                    <p
                                        class="text-xs text-gray-500 mt-1 dark:text-gray-400"
                                    >
                                        {{ formatDate(item.tanggal_terbit) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-span-12 md:col-span-9">
                    <!-- Pengumuman Grid -->
                    <div
                        v-if="isLoading"
                        class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-8"
                    >
                        <!-- Skeleton Loading -->
                        <div
                            v-for="n in 8"
                            :key="`skeleton-${n}`"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden animate-pulse"
                        >
                            <div
                                class="aspect-video bg-gray-300 dark:bg-gray-700"
                            ></div>
                            <div class="p-4">
                                <div
                                    class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-20 mb-4"
                                ></div>
                                <div
                                    class="h-5 bg-gray-300 dark:bg-gray-700 rounded w-full mb-2"
                                ></div>
                                <div
                                    class="h-5 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mb-4"
                                ></div>
                                <div class="flex flex-col gap-2">
                                    <div
                                        class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-24"
                                    ></div>
                                    <div
                                        class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-32"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else-if="pengumuman?.data?.length > 0"
                        class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-8"
                    >
                        <Link
                            v-for="item in pengumuman.data"
                            :key="item.id"
                            :href="route('pengumuman.showPublic', item.slug)"
                            class="group block bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-all duration-300 animate-fade-in"
                        >
                            <div
                                class="aspect-video overflow-hidden relative bg-gray-200 dark:bg-gray-700"
                            >
                                <div
                                    v-if="!isImageLoaded(item.id)"
                                    class="absolute inset-0 animate-pulse bg-gray-300 dark:bg-gray-700"
                                ></div>
                                <img
                                    :src="
                                        item.gambar
                                            ? item.gambar.startsWith('http')
                                                ? item.gambar
                                                : `/storage/${item.gambar}`
                                            : '/images/default-cover.png'
                                    "
                                    :alt="item.judul"
                                    @load="handleImageLoad(item.id)"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300"
                                    :class="
                                        isImageLoaded(item.id)
                                            ? 'opacity-100'
                                            : 'opacity-0'
                                    "
                                    loading="lazy"
                                    decoding="async"
                                />

                                <div
                                    v-if="item.is_penting"
                                    class="absolute top-2 right-2"
                                >
                                    <span
                                        class="px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full shadow-md"
                                    >
                                        ⭐ PENTING
                                    </span>
                                </div>
                            </div>

                            <div class="p-4">
                                <div
                                    class="mb-4 flex justify-between items-center"
                                >
                                    <span
                                        class="inline-block px-2 py-1 bg-blue-100 text-primary text-xs font-medium rounded dark:bg-primary dark:text-gray-900"
                                    >
                                        {{
                                            item.tipe.charAt(0).toUpperCase() +
                                            item.tipe.slice(1)
                                        }}
                                    </span>
                                    <span
                                        v-if="!item.is_aktif"
                                        class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded dark:bg-red-900 dark:text-red-200"
                                    >
                                        NONAKTIF
                                    </span>
                                </div>

                                <h3
                                    class="text-lg font-bold text-gray-900 dark:text-white mb-4 line-clamp-2 group-hover:text-primary transition-colors"
                                >
                                    {{
                                        item.judul && item.judul.length > 50
                                            ? item.judul.substring(0, 47) +
                                              "..."
                                            : item.judul
                                    }}
                                </h3>

                                <div v-if="item.nomor_pengumuman" class="mb-3">
                                    <span
                                        class="text-xs text-gray-600 dark:text-gray-400 font-medium"
                                    >
                                        No: {{ item.nomor_pengumuman }}
                                    </span>
                                </div>

                                <div
                                    class="flex flex-col items-start justify-between text-xs text-gray-500 dark:text-gray-400 gap-2"
                                >
                                    <span>{{
                                        formatDate(item.tanggal_terbit)
                                    }}</span>
                                    <span
                                        v-if="item.tanggal_berakhir"
                                        class="text-xs text-red-600 dark:text-red-400"
                                    >
                                        Berlaku hingga:
                                        {{ formatDate(item.tanggal_berakhir) }}
                                    </span>
                                    <span
                                        class="group-hover:text-primary font-medium dark:text-white dark:group-hover:text-blue-400 transition-colors"
                                    >
                                        Baca Selengkapnya →
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else-if="!isLoading && pengumuman?.data?.length === 0"
                        class="text-center py-12 animate-fade-in"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                            />
                        </svg>
                        <h3
                            class="mt-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Tidak ada pengumuman
                        </h3>
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            <span v-if="search || tipe"
                                >Tidak ditemukan pengumuman yang sesuai dengan
                                filter Anda.</span
                            >
                            <span v-else
                                >Belum ada pengumuman yang dipublikasikan.</span
                            >
                        </p>
                        <div v-if="search || tipe" class="mt-6">
                            <button
                                @click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-white dark:bg-blue-600 dark:hover:bg-blue-700"
                            >
                                Hapus Filter
                            </button>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="
                            pengumuman?.data?.length > 0 &&
                            (pengumuman?.meta?.last_page ?? 0) > 1
                        "
                        class="flex items-center justify-between"
                    >
                        <!-- Mobile & Desktop pagination tetap sama, gunakan optional chaining di semua akses pengumuman.meta dan pengumuman.links -->
                        <!-- Bisa disesuaikan sama kode lama, tapi jangan lupa tambah tanda ? atau ?? -->
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
</style>
