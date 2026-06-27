<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';
import useTranslations from '@/Composables/useTranslations.js';
const { t } = useTranslations();

const props = defineProps({
    albums: { type: Object, required: true },
    filters: { type: Object, required: true },
});

// Reactive locale from Inertia page props
const page = usePage();
const appLocale = computed(() => page.props?.i18n?.locale || 'id');
// Helper to pick localized value from translation maps with fallback; reactive to locale changes
const localizeField = (translations, fallback) => {
    if (!translations || typeof translations !== 'object') return fallback || '';
    return translations[appLocale.value] || translations.id || fallback || '';
};

const search = ref(props.filters.search || '');
const imageLoadedStates = ref({});
const imageErrorStates = ref({});

// Watch for changes and update URL
watch([search], () => {
    router.get(route('albums.indexPublic'), {
        search: search.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
}, { debounce: 300 });

// Image loading handlers
const handleImageLoad = (albumId) => {
    imageLoadedStates.value[albumId] = true;
};

const handleImageError = (albumId) => {
    imageErrorStates.value[albumId] = true;
};

const clearFilters = () => {
    search.value = '';
};

// Build numeric pagination items with ellipses
const buildPageItems = (current, last) => {
    const items = [];

    // If small number of pages, show all
    if (last <= 9) {
        for (let i = 1; i <= last; i++) items.push(i);
        return items;
    }

    // Always include first 3 and last 3 pages
    const base = new Set([1, 2, 3, last - 2, last - 1, last]);
    // Include neighbors around current page
    for (let i = Math.max(1, current - 1); i <= Math.min(last, current + 1); i++) {
        base.add(i);
    }

    const sorted = Array.from(base).sort((a, b) => a - b);
    for (let i = 0; i < sorted.length; i++) {
        items.push(sorted[i]);
        const next = sorted[i + 1];
        if (next !== undefined && next - sorted[i] > 1) {
            items.push('ellipsis');
        }
    }
    return items;
};

const pageItems = computed(() => buildPageItems(props.albums.meta.current_page, props.albums.meta.last_page));
const pageHref = (page) => route('albums.indexPublic', { page, search: search.value || undefined });

const breadcrumbItems = [
    { label: t('menu.gallery'), url: '/galeri' },
];

const heroTitle = t('albums.public.heading');
const heroDescription = 'informasi galeri BPBUMD DKI Jakarta';

</script>

<template>

    <Head :title="t('albums.public.heading')">
        <meta name="description" content="Galeri foto dan dokumentasi kegiatan BPBUMD" />
        <meta name="keywords" content="galeri, foto, dokumentasi, BPBUMD, album" />
    </Head>

    <GuestLayout>

        <!-- Hero Section -->
        <Hero :title="heroTitle" :description="heroDescription" :breadcrumbItems="breadcrumbItems" />

        <main class="container mx-auto px-4 py-12">

            <!-- Search Filter -->
            <div class="max-w-md mx-auto mb-12">
                <div class="relative">
                    <input v-model="search" type="text" :placeholder="t('albums.public.search_placeholder')"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700 dark:text-white" />

                </div>
                
                <!-- Clear Filters -->
                <div v-if="search" class="mt-4">
                    <button @click="clearFilters"
                        class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm dark:bg-gray-600 dark:text-white">
                        {{ t('albums.public.clear_filters') }}
                    </button>
                </div>
            </div>

            <!-- Albums Grid -->
            <div v-if="albums.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
                
                <Link v-for="album in albums.data" :key="album.id" :href="route('albums.showPublic', album.id)"
                    class="group bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 dark:bg-gray-900 dark:border-gray-600">
                    
                    <!-- Album Image -->
                    <div class="aspect-video bg-gray-100 overflow-hidden dark:bg-gray-900 relative">
                        <!-- Loading Skeleton -->
                        <div v-if="album.image && !imageLoadedStates[album.id] && !imageErrorStates[album.id]" 
                            class="absolute inset-0 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 animate-shimmer dark:from-gray-700 dark:via-gray-600 dark:to-gray-700">
                        </div>
                        
                        <!-- Actual Image -->
                        <img v-if="album.image && !imageErrorStates[album.id]" 
                            :src="album.image" 
                            :alt="localizeField(album.title_translations, album.title)"
                            loading="lazy"
                            decoding="async"
                            @load="handleImageLoad(album.id)"
                            @error="handleImageError(album.id)"
                            :class="[
                                'w-full h-full object-cover group-hover:scale-105 transition-all duration-300',
                                imageLoadedStates[album.id] ? 'opacity-100' : 'opacity-0'
                            ]" />
                        
                        <!-- Fallback Placeholder -->
                        <div v-if="!album.image || imageErrorStates[album.id]" 
                            class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Album Info -->
                    <div class="p-4">

                        <h3 class="font-semibold text-gray-900 text-lg mb-2 line-clamp-3 group-hover:text-primary transition-colors dark:text-white">
                            {{ localizeField(album.title_translations, album.title) }}
                        </h3>
                        
                        <!-- <p v-if="album.description || album.description_translations" class="text-sm text-gray-600 mb-3 line-clamp-2 dark:text-gray-300">
                            {{ localizeField(album.description_translations, album.description) }}
                        </p> -->
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ album.created_at }}</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ album.images_count }}
                            </span>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('albums.public.no_albums_title') }}</h3>
                <p class="text-gray-500">
                    {{ search ? t('albums.public.no_albums_desc_search') : t('albums.public.no_albums_desc_none') }}
                </p>
            </div>

            <!-- Pagination -->
            <div v-if="albums.data.length > 0 && albums.meta.last_page > 1" class="flex flex-col sm:flex-row justify-center items-center gap-3 sm:gap-2">
                
                <!-- Mobile: Page Info (visible only on mobile) -->
                <div class="sm:hidden text-sm font-medium text-gray-600 dark:text-gray-400 order-first">
                    {{ t('common.page') }} {{ albums.meta.current_page }} {{ t('common.of') }} {{ albums.meta.last_page }}
                </div>

                <!-- Pagination Controls -->
                <div class="flex justify-center items-center gap-2">
                    <!-- Previous Button -->
                    <Link v-if="albums.links.prev" :href="albums.links.prev"
                        class="inline-flex items-center justify-center gap-2 min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0 px-4 sm:px-3 py-3 sm:py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors dark:bg-slate-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-slate-700"
                        :aria-label="t('common.previous_page')">
                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="hidden sm:inline">{{ t('common.previous') }}</span>
                    </Link>
                    <div v-else class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0 px-4 sm:px-3 py-3 sm:py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed dark:bg-slate-900 dark:border-gray-800"
                        :aria-label="t('common.previous_page')" aria-disabled="true">
                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="hidden sm:inline">{{ t('common.previous') }}</span>
                    </div>

                    <!-- Numeric Pages (hidden on mobile, visible on tablet+) -->
                    <template v-for="(item, idx) in pageItems" :key="idx">
                        <span v-if="item === 'ellipsis'" class="hidden sm:inline-block px-3 py-2 text-gray-500 select-none dark:text-gray-400">...</span>
                        <Link v-else :href="pageHref(item)"
                            :aria-current="item === albums.meta.current_page ? 'page' : undefined"
                            class="hidden sm:inline-flex items-center justify-center min-w-10 text-center px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                            :class="item === albums.meta.current_page 
                                ? 'bg-primary text-white border-primary dark:bg-primary dark:text-white dark:border-primary' 
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 active:bg-gray-100 dark:bg-slate-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-slate-700'">
                            {{ item }}
                        </Link>
                    </template>

                    <!-- Next Button -->
                    <Link v-if="albums.links.next" :href="albums.links.next"
                        class="inline-flex items-center justify-center gap-2 min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0 px-4 sm:px-3 py-3 sm:py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors dark:bg-slate-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-slate-700"
                        :aria-label="t('common.next_page')">
                        <span class="hidden sm:inline">{{ t('common.next') }}</span>
                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </Link>
                    <div v-else class="inline-flex items-center justify-center gap-2 min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0 px-4 sm:px-3 py-3 sm:py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed dark:bg-slate-900 dark:border-gray-800"
                        :aria-label="t('common.next_page')" aria-disabled="true">
                        <span class="hidden sm:inline">{{ t('common.next') }}</span>
                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </div>

        </main>
    </GuestLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Shimmer loading animation */
@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

.animate-shimmer {
    background-size: 200% 100%;
    animation: shimmer 1.5s ease-in-out infinite;
}
</style>