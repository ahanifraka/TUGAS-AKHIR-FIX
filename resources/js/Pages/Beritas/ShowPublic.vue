<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import useTranslations from '@/Composables/useTranslations.js';

import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    berita: { type: Object, required: true },
    berita_categories: { type: Object, required: true },
});

const page = usePage();
const locale = page.props?.i18n?.locale || 'id';

// Map app locale to appropriate date locale tag
const dateLocale = locale === 'en' ? 'en-US' : 'id-ID';

function formatDate(ts) {
    if (!ts) return '';
    try {
        const d = new Date(ts);
        if (isNaN(d.getTime())) return ts; // fallback raw if invalid
        return d.toLocaleDateString(dateLocale, {
            year: 'numeric', month: 'long', day: 'numeric'
        });
    } catch (e) {
        return ts;
    }
}

function localizeField(translations, fallbackValue = '') {
    if (!translations || typeof translations !== 'object') return fallbackValue;
    return translations[locale] || translations.id || fallbackValue || '';
}

const { t } = useTranslations();
const pageTitle = localizeField(props.berita.title_translations, props.berita.title) || t('berita.public.other_news', 'Berita');
const metaDescription = props.berita.meta_content || props.berita.teaser || '';
const metaKeywords = props.berita.meta_keyword || '';

const expandedImage = ref(null);

const openImage = (imageSrc) => {
    expandedImage.value = imageSrc;
};

const closeImage = () => {
    expandedImage.value = null;
};

const share = (platform) => {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(pageTitle);

    let shareLink = '';

    switch (platform) {
        case 'facebook':
            shareLink = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            break;
        case 'twitter':
            shareLink = `https://twitter.com/intent/tweet?url=${url}&text=${text}`;
            break;
        case 'whatsapp':
            shareLink = `https://wa.me/?text=${text}%20${url}`;
            break;
    }

    if (shareLink) {
        window.open(shareLink, '_blank', 'width=600,height=400');
    }
};

</script>

<style>
#BeritaContent {
    margin-bottom: 15px;
}

#berita-content a {
    color: #007bff !important;
}

#berita-content a:hover {
    color: #0056b3 !important;
}

.image-expandable {
    cursor: pointer;
    transition: opacity 0.2s;
}

.image-expandable:hover {
    opacity: 0.9;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

<template>

    <Head :title="pageTitle">
        <meta v-if="metaDescription" name="description" :content="metaDescription" />
        <meta v-if="metaKeywords" name="keywords" :content="metaKeywords" />
    </Head>

    <GuestLayout>
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-12 gap-4 max-w-4xl mx-auto">
                <div class="col-span-12 space-y-3">

                    <div v-if="props.berita.image" class="overflow-hidden rounded relative group cursor-pointer"
                        @click="openImage(props.berita.image)">
                        <img :src="props.berita.image" :alt="props.berita.title"
                            class="w-full object-cover object-center max-h-[400px]" />
                        <div
                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center pointer-events-none">
                            <span
                                class="text-white text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-black bg-opacity-70 px-4 py-2 rounded-full">
                                {{ t('berita.public.overlay_zoom', 'Klik untuk memperbesar') }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-gray-600 -mt-6 -mb-3"><i class="pi pi-info-circle ml-1"
                                style="font-size:0.65rem"></i> {{ t('berita.public.click_to_enlarge', 'Klik gambar untuk memperbesar') }}</span>
                    </div>
                    <div class="flex w-full justify-between items-center">
                        <div class="flex gap-2">
                            <button @click="share('twitter')"
                                class="w-8 h-8 flex items-center justify-center bg-black text-white rounded-full hover:opacity-80 transition-opacity"
                                title="Share to X">
                                <i class="pi pi-twitter"></i>
                            </button>
                            <button @click="share('facebook')"
                                class="w-8 h-8 flex items-center justify-center bg-blue-600 text-white rounded-full hover:opacity-80 transition-opacity"
                                title="Share to Facebook">
                                <i class="pi pi-facebook"></i>
                            </button>
                            <button @click="share('whatsapp')"
                                class="w-8 h-8 flex items-center justify-center bg-green-500 text-white rounded-full hover:opacity-80 transition-opacity"
                                title="Share to WhatsApp">
                                <i class="pi pi-whatsapp"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4">
                        <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight dark:text-white">{{ pageTitle }}
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-white">{{ formatDate(props.berita.created_at) }}
                        </p>
                    </div>

                    <div v-if="localizeField(props.berita.teaser_translations, props.berita.teaser)"
                        class="text-gray-700 text-sm leading-normal dark:text-gray-300">{{
                            localizeField(props.berita.teaser_translations, props.berita.teaser) }}</div>

                    <article id="BeritaContent" class="prose max-w-none leading-normal dark:text-white">
                        <div id="berita-content"
                            v-html="localizeField(props.berita.content_translations, props.berita.content)"></div>
                    </article>
                </div>
                <div class="col-span-12">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-6 dark:text-white">{{
                            t('berita.public.other_news', 'Berita Lainnya') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                            <div v-for="item in props.berita.beritas" :key="item.id" class="grid grid-cols-6 w-full">
                                <figure class="col-span-2 px-1 relative group cursor-pointer"
                                    @click="openImage(item.image)">
                                    <img :src="item.image" :alt="localizeField(item.title_translations, item.title)"
                                        class="w-full object-cover aspect-square rounded" />
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center rounded pointer-events-none">
                                        <span
                                            class="text-white text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            🔍
                                        </span>
                                    </div>
                                </figure>
                                <div class="col-span-4 px-2">
                                    <Link :href="route('beritas.showPublic', item.slug)"
                                        class="text-normal font-bold text-gray-900 hover:text-primary dark:text-white">
                                    {{
                                        (localizeField(item.title_translations, item.title) || '').length > 60
                                            ? localizeField(item.title_translations, item.title).substring(0, 60) + '...'
                                            : localizeField(item.title_translations, item.title)
                                    }}</Link>
                                    <p class="text-sm text-gray-500 mt-1 dark:text-gray-300">{{
                                        formatDate(item.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-12">
                            <div class="flex flex-wrap gap-2">
                                <div v-for="item in props.berita_categories" :key="item.id">

                                    <Link :href="route('beritas.indexPublic', { category: item.category_name })"
                                        class="text-sm font-bold text-gray-700 hover:text-primary">
                                    <div class="bg-gray-100 px-4 py-2 rounded-full text-sm">
                                        {{ item.category_name }}
                                    </div>
                                    </Link>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Lightbox Modal -->
        <Transition name="fade">
            <div v-if="expandedImage"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90 p-4"
                @click="closeImage" @keydown.esc="closeImage">
                <button class="absolute top-4 right-4 text-white text-4xl font-bold hover:text-gray-300 z-10"
                    @click="closeImage" aria-label="Close">
                    &times;
                </button>
                <img :src="expandedImage" class="max-w-full max-h-full object-contain" @click.stop
                    alt="Expanded view" />
            </div>
        </Transition>
    </GuestLayout>
</template>