<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Breadcrumb from 'primevue/breadcrumb';
import useTranslations from '@/Composables/useTranslations.js';
const { t } = useTranslations();

const props = defineProps({
    album: { type: Object, required: true },
    images: { type: Array, required: true },
});

// Reactive locale
const page = usePage();
const appLocale = computed(() => page.props?.i18n?.locale || 'id');
// Helper to localize fields using translation maps with fallback (reactive)
const localizeField = (translations, fallback) => {
    if (!translations || typeof translations !== 'object') return fallback || '';
    return translations[appLocale.value] || translations.id || fallback || '';
};

// Breadcrumb items
const breadcrumbHome = { icon: 'pi pi-home', url: '/' };
const breadcrumbItems = [
    { label: t('menu.gallery'), url: route('albums.indexPublic') },
{ label: (() => { const t = localizeField(props.album.title_translations, props.album.title); return t?.length > 50 ? t.slice(0, 50) + '…' : t; })() },
];

// Lightbox functionality
const showLightbox = ref(false);
const currentImageIndex = ref(0);

const openLightbox = (index) => {
    currentImageIndex.value = index;
    showLightbox.value = true;
    document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
    showLightbox.value = false;
    document.body.style.overflow = 'auto';
};

const nextImage = () => {
    currentImageIndex.value = (currentImageIndex.value + 1) % props.images.length;
};

const prevImage = () => {
    currentImageIndex.value = currentImageIndex.value === 0
        ? props.images.length - 1
        : currentImageIndex.value - 1;
};

// Keyboard navigation
const handleKeydown = (e) => {
    if (!showLightbox.value) return;

    if (e.key === 'Escape') {
        closeLightbox();
    } else if (e.key === 'ArrowRight') {
        nextImage();
    } else if (e.key === 'ArrowLeft') {
        prevImage();
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = 'auto';
});
</script>

<template>

    <Head :title="localizeField(props.album.title_translations, props.album.title)">
        <meta name="description" :content="localizeField(props.album.description_translations, props.album.description) || `Album ${localizeField(props.album.title_translations, props.album.title)} - Galeri BPBUMD`" />
        <meta name="keywords" :content="`${localizeField(props.album.title_translations, props.album.title)}, galeri, foto, dokumentasi, BPBUMD, album`" />
    </Head>

    <GuestLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <Breadcrumb 
                :home="breadcrumbHome" 
                :model="breadcrumbItems" 
                class="mb-8 w-full overflow-x-auto px-2 sm:px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700"
                :pt="{
                    list: { class: 'flex items-center whitespace-nowrap gap-1 sm:gap-2' },
                    item: { class: 'flex items-center' },
                    action: { class: 'no-underline flex items-center text-gray-700 dark:text-gray-200 hover:text-primary transition-colors' },
                    label: { class: 'truncate max-w-[160px] sm:max-w-none' },
                    separator: { class: 'mx-1 sm:mx-2 text-gray-400 dark:text-gray-500' }
                }"
            />

            <!-- Album Header -->
            <div class="mb-12">
                <div>
                    <div class="flex flex-col lg:flex-row gap-8">

                        <!-- Album Cover -->
                        <div class="lg:w-1/3">
                            <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden dark:bg-gray-900">
                                <img v-if="props.album.image" :src="props.album.image" :alt="localizeField(props.album.title_translations, props.album.title)"
                                    class="w-full h-full object-cover" />
                                <div v-else
                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Album Info -->
                        <div class="lg:w-2/3">
                            <h1 class="text-3xl md:text-3xl font-bold text-gray-900 mb-4 dark:text-white">{{ localizeField(props.album.title_translations, props.album.title) }}</h1>

                            <p v-if="props.album.description || props.album.description_translations" class="text-normal text-justify text-gray-600 mb-6 dark:text-gray-200">
                                {{ localizeField(props.album.description_translations, props.album.description) }}
                            </p>

                            <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-200">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-calendar"></i>
                                    <span>{{ props.album.created_at }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-images"></i>
                                    <span>{{ props.images.length }} {{ t('albums.public.photos_suffix') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images Gallery -->
            <div v-if="props.images.length > 0" class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">{{ t('albums.public.images_heading') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <div v-for="(image, index) in props.images" :key="image.id" @click="openLightbox(index)"
                        class="group cursor-pointer bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 dark:bg-gray-800 dark:border-gray-700">

                        <!-- Image -->
                        <div class="aspect-square bg-gray-100 overflow-hidden">
                            <img :src="image.image" :alt="image.title || `Foto ${index + 1}`"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                        </div>

                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('albums.public.no_photos_title') }}</h3>
                    <p class="text-gray-500">{{ t('albums.public.no_photos_desc') }}</p>
            </div>

            <!-- Back to Gallery -->
            <div class="text-center">
                <Link :href="route('albums.indexPublic')"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-hover transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ t('albums.public.back_to_gallery') }}
                </Link>
            </div>
        </div>

        <!-- Lightbox Modal -->
        <div v-if="showLightbox" class="fixed inset-0 z-[2000] flex items-center justify-center bg-black bg-opacity-90"
            @click="closeLightbox">

            <!-- Close Button -->
            <button @click="closeLightbox"
                class="absolute top-4 right-4 z-60 p-2 text-white hover:text-gray-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <!-- Navigation Buttons -->
            <button v-if="props.images.length > 1" @click.stop="prevImage"
                class="absolute left-4 z-60 p-2 text-white hover:text-gray-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <button v-if="props.images.length > 1" @click.stop="nextImage"
                class="absolute right-4 z-60 p-2 text-white hover:text-gray-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Image Container -->
            <div class="max-w-4xl max-h-full mx-4" @click.stop>
                <img :src="props.images[currentImageIndex]?.image"
                    :alt="props.images[currentImageIndex]?.title || `Foto ${currentImageIndex + 1}`"
                    class="max-w-full max-h-full object-contain" />

                <!-- Image Info -->
                <div v-if="props.images[currentImageIndex]?.title || props.images[currentImageIndex]?.description"
                    class="bg-black bg-opacity-50 text-white p-4 mt-2 rounded">
                    <h3 v-if="props.images[currentImageIndex]?.title" class="font-medium mb-1">
                        {{ props.images[currentImageIndex].title }}
                    </h3>
                    <p v-if="props.images[currentImageIndex]?.description" class="text-sm text-gray-200">
                        {{ props.images[currentImageIndex].description }}
                    </p>
                </div>
            </div>

            <!-- Image Counter -->
            <div v-if="props.images.length > 1"
                class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-sm">
                {{ currentImageIndex + 1 }} / {{ props.images.length }}
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.z-60 {
    z-index: 60;
}
</style>