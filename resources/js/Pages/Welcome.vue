<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";

import useTranslations from "@/Composables/useTranslations.js";

import GuestLayout from "@/Layouts/GuestLayout.vue";
import SkeletonCard from "@/Components/SkeletonCard.vue";
import SkeletonBanner from "@/Components/SkeletonBanner.vue";
import SkeletonLogo from "@/Components/SkeletonLogo.vue";
import Isometric from "@/Components/Isometric.vue";
import StatCard from "@/Components/StatCard.vue";

// Swiper Carousel
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/autoplay";

const swiperModules = [Navigation, Pagination, Autoplay];
const newsCarouselModules = [Navigation]; // Manual navigation only, no pagination

// Stats data
const stats = ref([
    {
        value: 14,
        label: 'pages.tentang_kami.stats.bumd',
        defaultLabel: 'Badan Usaha Milik Daerah',
        currentValue: 0,
        color: 'teal',
        icon: 'pi-building-columns'
    },
    {
        value: 9,
        label: 'pages.tentang_kami.stats.joint_venture',
        defaultLabel: 'Perusahaan Patungan',
        currentValue: 0,
        color: 'blue',
        icon: 'pi-building'
    }
]);

const statsContainer = ref(null);
const hasAnimated = ref(false);

// Props
const props = defineProps({
    slider: { type: Object, required: true },
    berita_populer: { type: Object, required: true },
    berita: { type: Object, required: true },
    pengumuman: { type: Array, default: () => [] },
    bumd: { type: Object, required: true },
    album: { type: Object, required: true },
    bumdBySektor: { type: Object, required: true },
    berita_categories: { type: Array, required: true },
});

// Loading states
const isSliderLoaded = ref(false);
const isBeritaPopulerLoaded = ref(false);
const isBeritaLoaded = ref(false);
const isBumdLoaded = ref(false);
const isAlbumLoaded = ref(false);

// Image loading states
const loadedImages = ref(new Set());

// News carousel state
const newsCarouselInstance = ref(null);

// Initialize selectedCategory from URL query parameter
const initialCategory =
    typeof window !== "undefined"
        ? new URLSearchParams(window.location.search).get("category_id")
        : null;
const selectedCategory = ref(
    initialCategory ? parseInt(initialCategory) : null
);

// Use props.berita.data directly as it is filtered by backend
const filteredBerita = computed(() => {
    return props.berita?.data || [];
});

// Watch for changes in selectedCategory to trigger backend filter
watch(selectedCategory, (newVal) => {
    router.get(
        window.location.pathname,
        { category_id: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            only: ["berita"],
        }
    );
});

const onNewsSwiper = (swiper) => {
    newsCarouselInstance.value = swiper;
};

// Swiper controls state
const activeIndex = ref(0);
const swiperInstance = ref(null);
const onSwiper = (swiper) => {
    swiperInstance.value = swiper;
};
const onSlideChange = (swiper) => {
    activeIndex.value = swiper.realIndex;
};

// Dynamic font size based on title length
const getTitleClass = (title) => {
    const length = title.length;

    if (length <= 50) {
        return "text-4xl md:text-5xl xl:text-6xl";
    } else if (length <= 80) {
        return "text-3xl md:text-4xl xl:text-5xl";
    } else if (length <= 120) {
        return "text-2xl md:text-3xl xl:text-3xl";
    } else {
        return "text-xl md:text-xl xl:text-2xl";
    }
};

// Fallback image for BUMD logos
const fallbackLogo = "/images/jaya-raya.png";
const setFallbackImage = (e) => {
    e.target.src = fallbackLogo;
};

// Handle image load
const handleImageLoad = (imageId) => {
    loadedImages.value.add(imageId);
};

// i18n helpers
const { t } = useTranslations();
const page = usePage();
const dateLocale = computed(() =>
    page.props?.i18n?.locale === "en" ? "en-US" : "id-ID"
);

// Helper to pick localized field from translation maps provided by controller
function localizeField(translations, fallback = "") {
    if (!translations || typeof translations !== "object") return fallback;
    const current = page.props?.i18n?.locale || "id";
    return translations[current] || translations.id || fallback || "";
}

// Isometric component data
const isometricData = ref({
    imageSrc: "/images/isometric.png",
    imageAlt: "BPBUMD Isometric View",
    infoDots: [
        {
            x: 47,
            y: 16,
            title: t("welcome.sectors.tourism.title", "Pariwisata"),
            description: t(
                "welcome.sectors.tourism.description",
                "Mengelola dan mengembangkan potensi wisata daerah melalui pengelolaan destinasi, promosi, dan pengembangan industri pariwisata."
            ),
            thumbnails: (props.bumdBySektor["Pariwisata"] || []).map(
                (bumd) => ({
                    src: bumd.logo || "/images/jaya-raya.png",
                    link: `/bumd/${bumd.kode}`,
                    alt: bumd.nama,
                })
            ),
        },
        {
            x: 50,
            y: 46,
            title: t("welcome.sectors.food.title", "Pangan"),
            description: t(
                "welcome.sectors.food.description",
                "Menjamin ketersediaan dan distribusi bahan pangan pokok yang berkualitas dengan harga terjangkau."
            ),
            thumbnails: (props.bumdBySektor["Pangan"] || []).map((bumd) => ({
                src: bumd.logo || "/images/jaya-raya.png",
                link: `/bumd/${bumd.kode}`,
                alt: bumd.nama,
            })),
        },
        {
            x: 67,
            y: 48,
            title: t("welcome.sectors.property.title", "Properti"),
            description: t(
                "welcome.sectors.property.description",
                "Mengelola dan mengembangkan aset properti serta kawasan strategis untuk optimalisasi nilai ekonomi daerah."
            ),
            thumbnails: (props.bumdBySektor["Properti"] || []).map((bumd) => ({
                src: bumd.logo || "/images/jaya-raya.png",
                link: `/bumd/${bumd.kode}`,
                alt: bumd.nama,
            })),
        },
        {
            x: 27,
            y: 83,
            title: t("welcome.sectors.transport.title", "Transportasi"),
            description: t(
                "welcome.sectors.transport.description",
                "MMengelola sistem transportasi publik yang aman, nyaman, dan terjangkau untuk melayani mobilitas masyarakat. "
            ),
            thumbnails: (props.bumdBySektor["Transportasi"] || []).map(
                (bumd) => ({
                    src: bumd.logo || "/images/jaya-raya.png",
                    link: `/bumd/${bumd.kode}`,
                    alt: bumd.nama,
                })
            ),
        },
        {
            x: 28,
            y: 27,
            title: t("welcome.sectors.finance.title", "Keuangan"),
            description: t(
                "welcome.sectors.finance.description",
                "Menyediakan layanan perbankan dan pembiayaan yang mendukung pertumbuhan ekonomi daerah."
            ),
            thumbnails: (props.bumdBySektor["Keuangan"] || []).map((bumd) => ({
                src: bumd.logo || "/images/jaya-raya.png",
                link: `/bumd/${bumd.kode}`,
                alt: bumd.nama,
            })),
        },
        {
            x: 74,
            y: 30,
            title: t("welcome.sectors.utilities.title", "Utilitas dan lainnya"),
            description: t(
                "welcome.sectors.utilities.description",
                "Menyediakan layanan utilitas publik seperti air bersih, pengelolaan limbah, dan lainnya untuk meningkatkan kualitas hidup masyarakat."
            ),
            thumbnails: (
                props.bumdBySektor["Utilitas & Lainya"] ||
                props.bumdBySektor["Utilitas dan lainnya"] ||
                []
            ).map((bumd) => ({
                src: bumd.logo || "/images/jaya-raya.png",
                link: `/bumd/${bumd.kode}`,
                alt: bumd.nama,
            })),
        },
    ],
});

// Animate counter function
const animateCounter = (stat) => {
    const duration = 2000; // 2 seconds
    const start = 0;
    const end = stat.value;
    const startTime = performance.now();

    const update = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function (easeOutQuart)
        const ease = 1 - Math.pow(1 - progress, 4);
        
        stat.currentValue = Math.floor(start + (end - start) * ease);

        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            stat.currentValue = end;
        }
    };

    requestAnimationFrame(update);
};

// Initialize loading states
onMounted(() => {
    setTimeout(() => {
        isSliderLoaded.value = true;
    }, 100);

    setTimeout(() => {
        isBeritaPopulerLoaded.value = true;
    }, 200);

    setTimeout(() => {
        isBeritaLoaded.value = true;
    }, 300);

    setTimeout(() => {
        isBumdLoaded.value = true;
    }, 400);

    setTimeout(() => {
        isAlbumLoaded.value = true;
    }, 500);
});
</script>

<style>
.swiper-pagination-bullet-active {
    background: var(--color-primary);
}

#Berita-Populer {
    padding: 0 0 10px 0 !important;
}

.berita-title {
    line-height: 1.25 !important;
}

/* Image fade-in animation */
@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.image-fade-in {
    animation: fadeIn 0.5s ease-in;
}

/* Banner slider zoom animation */
@keyframes bannerZoom {
    0% {
        transform: scale(1);
    }

    100% {
        transform: scale(1.04);
    }
}

.banner-image {
    will-change: transform;
    transform-origin: center center;
}

/* Play zoom animation only on the active slide */
.swiper-slide-active .banner-image {
    animation: bannerZoom 5s linear forwards;
}

/* Banner pagination rectangle styling */
.banner-pagination {
    padding-top: 8rem !important;
}

.banner-pagination .swiper-pagination-bullet {
    background: rgba(255, 255, 255, 0.5);
    opacity: 1;
    width: 8px;
    height: 8px;
    margin: 0 6px !important;
    border-radius: 50%;
    transition: all 300ms ease;
    position: relative;
    display: inline-block;
}

.swiper-pagination-fraction,
.swiper-pagination-custom,
.swiper-horizontal > .swiper-pagination-bullets,
.swiper-pagination-bullets.swiper-pagination-horizontal {
    top: 32px !important;
}

.banner-pagination .swiper-pagination-bullet-active {
    background: #eeeeee;
    width: 25px;
    border-radius: 6px;
}

.banner-pagination .swiper-pagination-bullet:hover {
    background: #eeeeee;
    opacity: 0.8;
}

/* News carousel pagination styling */
.news-carousel-pagination .swiper-pagination-bullet {
    background: rgba(0, 0, 0, 0.3);
    opacity: 1;
    width: 12px;
    height: 12px;
    margin: 0 4px !important;
    border-radius: 50%;
    transition: all 300ms ease;
}

.news-carousel-pagination .swiper-pagination-bullet-active {
    background: var(--color-primary);
    width: 12px;
    height: 12px;
}

.news-carousel-pagination .swiper-pagination-bullet:hover {
    background: var(--color-primary);
    opacity: 0.8;
}

/* Scroll down indicator animation */
@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
        opacity: 1;
    }
    50% {
        transform: translateY(-10px);
        opacity: 0.8;
    }
}

.animate-bounce-slow {
    animation: bounce-slow 2s ease-in-out infinite;
}

.scroll-arrow {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}
</style>

<template>
    <Head :title="t('welcome.title', 'Beranda')">
        <meta
            name="description"
            :content="
                t(
                    'welcome.meta.description',
                    'Badan Pembinaan Badan Usaha Milik Daerah Pemprov Jakarta - Menyelenggarakan fungsi penunjang urusan pemerintah di bidang keuangan pada sub bidang pembinaan BUMD'
                )
            "
        />
        <meta
            name="keywords"
            :content="
                t(
                    'welcome.meta.keywords',
                    'BPBUMD, badan pembinaan, bumd, pemprov jakarta'
                )
            "
        />
    </Head>

    <GuestLayout>
        <!-- Slider Carousel Banner -->
        <section class="relative w-full -mt-20">
            <SkeletonBanner v-if="!isSliderLoaded" />
            <Swiper
                v-else
                :modules="swiperModules"
                :slides-per-view="1"
                :space-between="0"
                :loop="true"
                :autoplay="{ delay: 5000, disableOnInteraction: false }"
                :pagination="{ clickable: true, el: '.banner-pagination' }"
                :navigation="{
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                }"
                class="w-full h-[400px] md:h-[500px] lg:h-[600px] p-0 m-0"
                @swiper="onSwiper"
                @slideChange="onSlideChange"
            >
                <SwiperSlide
                    v-for="item in slider.data"
                    :key="item.id"
                    class="relative"
                >
                    <div
                        class="absolute inset-0 bg-black opacity-50 sm:opacity-60 z-10 w-full h-full"
                    ></div>
                    <img
                        :src="item.image"
                        :alt="item.title"
                        :title="item.title"
                        loading="lazy"
                        @load="handleImageLoad(`slider-${item.id}`)"
                        :class="['w-full h-full object-cover banner-image', loadedImages.has(`slider-${item.id}`) ? 'image-fade-in' : '']" />
                    <div
                        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20 w-full max-w-[95%] xs:max-w-[90%] sm:max-w-2xl md:max-w-2xl lg:max-w-3xl xl:max-w-5xl px-3 xs:px-4 sm:px-6 md:px-8 py-4 xs:py-6 sm:py-8">
                        <p
                            class="text-white text-xs xs:text-sm sm:text-base lg:text-lg xl:text-xl mb-1 xs:mb-2 font-bold uppercase">
                            {{ t('welcome.banner.label', 'Berita') }}</p>
                        <h2 :class="getTitleClass(item.title)"
                            class="text-white font-bold leading-tight mb-4 sm:mb-8 berita-title">
                            {{ item.title.length > 140 ? item.title.substring(0, 137) + '...' : item.title }}
                        </h2>
                        <Link :href="item.link || '#'"
                            class="inline-block text-white border border-white hover:bg-white hover:text-gray-900 transition-colors duration-300 px-4 sm:px-4 py-1.5 text-xs xs:text-sm sm:text-base font-medium rounded-md">
                        {{
                            t(
                                "welcome.banner.read_more",
                                "Lihat Selengkapnya"
                            )
                        }}</Link>
                    </div>
                </SwiperSlide>

                <!-- Navigation Arrows -->
                <template #container-end>
                    <div
                        class="absolute inset-y-0 left-2 xs:left-4 sm:left-6 md:left-8 lg:left-10 hidden lg:flex items-center justify-center z-30">
                        <button type="button" aria-label="Prev"
                            class="swiper-button-prev !text-white !w-4 !h-4 xs:!w-8 xs:!h-6 sm:!w-6 sm:!h-8 !mt-0 hover:!text-gray-300 transition-colors duration-300 hidden xs:flex"></button>
                    </div>
                    <div
                        class="absolute inset-y-0 right-2 xs:right-4 sm:right-6 md:right-8 lg:right-10 hidden lg:flex items-center justify-center z-30">
                        <button type="button" aria-label="Next"
                            class="swiper-button-next !text-white !w-4 !h-4 xs:!w-8 xs:!h-6 sm:!w-6 sm:!h-8 !mt-0 hover:!text-gray-300 transition-colors duration-300 hidden xs:flex"></button>
                    </div>
                </template>
            </Swiper>

            <!-- Pagination dots -->
            <div
                class="banner-pagination absolute -mt-8 bottom-8 xs:bottom-6 sm:bottom-8 md:bottom-10 left-0 right-0 z-30 flex justify-center items-center">
            </div>
        </section>

        <!-- Tentang BPBUMD dan Berita Terpopuler -->
        <section
            id="Tentang"
            class="container mx-auto px-3 md:px-0 max-w-6xl bg-white dark:bg-gray-900"
        >
            <div
                v-animate-on-scroll
                class="grid grid-cols-1 lg:grid-cols-2 gap-4 py-24"
            >
                <div class="p-4">
                    <h2
                        class="text-xl text-gray-900 dark:text-white font-extrabold mb-4"
                    >
                        {{ t("welcome.about.heading", "Tentang BPBUMD") }}
                    </h2>
                    <h1
                        class="text-4xl text-primary dark:text-white font-extrabold mb-6 leading-tight"
                    >
                        {{
                            t(
                                "welcome.about.title",
                                "BADAN PEMBINAAN BADAN USAHA MILIK DAERAH (BPBUMD)"
                            )
                        }}
                    </h1>
                    <p class="text-xl text-gray-900 dark:text-white mb-4">
                        {{
                            t(
                                "welcome.about.description",
                                "Badan Pembinaan BUMD Provinsi Jakarta adalah badan yang bertanggung jawab dalam pengelolaan dan pengembangan BUMD di Provinsi Jakarta."
                            )
                        }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-white mb-4">
                        {{
                            t(
                                "welcome.about.address",
                                "Kompleks Balaikota Blok H Lantai 17, Jl. Medan Merdeka Selatan No. 8 - 9, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10110"
                            )
                        }}
                    </p>
                    <ul class="text-sm text-gray-600 dark:text-white mb-12">
                        <li>(021) 3822076</li>
                        <li>bpbumd@jakarta.go.id</li>
                    </ul>
                    <Link
                        :href="route('tupoksi.index')"
                        :title="
                            t('welcome.about.view_more', 'Lihat Selengkapnya')
                        "
                        class="border border-gray-800 dark:border-white hover:bg-gray-800 dark:hover:bg-white hover:text-white dark:hover:text-gray-900 text-gray-800 dark:text-white px-8 py-2 rounded-md transition-all duration-300"
                    >
                        {{
                            t("welcome.about.view_more", "Lihat Selengkapnya")
                        }}</Link
                    >
                </div>
                <div class="p-4">
                    <h2 class="text-xl text-gray-900 font-extrabold mb-4">
                        {{
                            t(
                                "welcome.popular_news.heading",
                                "Berita Terpopuler"
                            )
                        }}
                    </h2>

                    <template v-if="!isBeritaPopulerLoaded">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <SkeletonCard v-for="n in 2" :key="n" />
                        </div>
                    </template>
                    <Swiper v-else :modules="swiperModules" :slides-per-view="2" :space-between="16" :loop="true"
                        :autoplay="{ delay: 5000, disableOnInteraction: false }" :breakpoints="{
                            640: { slidesPerView: 1, spaceBetween: 16 },
                            768: { slidesPerView: 2, spaceBetween: 16 },
                            1024: { slidesPerView: 2, spaceBetween: 16 },
                        }" :pagination="{
                            clickable: true,
                            el: '.berita-populer-pagination',
                        }"
                        :navigation="{
                            nextEl: '.berita-populer-next',
                            prevEl: '.berita-populer-prev',
                        }" id="Berita-Populer" class="w-full relative group">
                        <SwiperSlide v-for="item in berita_populer.data" :key="item.id">
                            <div
                                class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 shadow-sm w-full max-w-xl h-full"
                            >
                                <figure class="w-full mb-2">
                                    <img
                                        :src="item.image"
                                        :alt="
                                            localizeField(
                                                item.title_translations,
                                                item.title
                                            )
                                        "
                                        :title="
                                            localizeField(
                                                item.title_translations,
                                                item.title
                                            )
                                        "
                                        loading="lazy"
                                        @load="
                                            handleImageLoad(
                                                `berita-populer-${item.id}`
                                            )
                                        "
                                        :class="[
                                            'w-full h-full object-cover aspect-square',
                                            loadedImages.has(
                                                `berita-populer-${item.id}`
                                            )
                                                ? 'image-fade-in'
                                                : '',
                                        ]"
                                    />
                                </figure>
                                <div class="p-4">
                                    <p
                                        class="text-gray-500 dark:text-white text-sm mb-2"
                                    >
                                        {{
                                            new Date(
                                                item.created_at
                                            ).toLocaleDateString(dateLocale, {
                                                day: "numeric",
                                                month: "long",
                                                year: "numeric",
                                            })
                                        }}
                                    </p>
                                    <Link :href="route(
                                        'beritas.showPublic',
                                        item.slug
                                    )
                                        " :title="item.title">
                                    <h3 class="text-normal font-bold mb-2 hover:text-primary">
                                        {{
                                            (
                                                localizeField(
                                                    item.title_translations,
                                                    item.title
                                                ) || ""
                                            ).length > 35
                                                ? localizeField(
                                                    item.title_translations,
                                                    item.title
                                                ).substring(0, 32) + "..."
                                                : localizeField(
                                                    item.title_translations,
                                                    item.title
                                                )
                                        }}
                                    </h3>
                                    </Link>
                                    <Link :href="route(
                                        'beritas.showPublic',
                                        item.slug
                                    )
                                        " :title="item.title"
                                        class="text-gray-900 dark:text-white text-sm inline-flex justify-center items-center hover:text-primary">
                                    <span>{{
                                        t(
                                            "welcome.news.read_more",
                                            "Baca Selengkapnya"
                                        )
                                    }}</span>
                                    <i class="pi pi-arrow-right mx-2" style="
                                                font-size: 10px;
                                                margin-top: 3px;
                                            "
                                        ></i>
                                    </Link>
                                </div>
                            </div>
                        </SwiperSlide>
                        <!-- Small arrow navigation -->
                        <template #container-end>
                            <div
                                class="absolute inset-y-0 left-4 flex items-center z-20"
                            >
                                <button
                                    type="button"
                                    aria-label="Prev"
                                    class="berita-populer-prev inline-flex items-center justify-center w-7 h-7 rounded-full border border-primary dark:border-white bg-primary text-gray-200 dark:text-white hover:bg-primary-hover shadow-sm transition-all opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto"
                                >
                                    <i class="pi pi-chevron-left text-xs"></i>
                                </button>
                            </div>
                            <div
                                class="absolute inset-y-0 right-4 flex items-center z-20"
                            >
                                <button
                                    type="button"
                                    aria-label="Next"
                                    class="berita-populer-next inline-flex items-center justify-center w-7 h-7 rounded-full border border-primary dark:border-white bg-primary text-gray-200 dark:text-white hover:bg-primary-hover shadow-sm transition-all opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto"
                                >
                                    <i class="pi pi-chevron-right text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </Swiper>
                    <div
                        class="berita-populer-pagination mt-3 flex justify-center"
                    ></div>
                </div>
            </div>

            <!-- <div class="grid grid-cols-1 sm:grid-cols-2 justify-center gap-6 pb-12">
                <StatCard 
                    title="Asset" 
                    value="Rp216.617"
                    valueText=" Miliar" 
                    change="+5%" 
                    changeType="positive" 
                    changeText="sejak 2020" 
                    icon="pi-briefcase" 
                    iconColor="green" 
                />
                <StatCard 
                    title="Laba Bersih" 
                    value="Rp3.213"
                    valueText=" Miliar" 
                    change="+5%" 
                    changeType="positive" 
                    changeText="sejak 2020" 
                    icon="pi-briefcase" 
                    iconColor="green" 
                />
                <StatCard 
                    title="Pendapatan" 
                    value="Rp37.233"
                    valueText=" Miliar" 
                    change="+5%" 
                    changeType="positive" 
                    changeText="sejak 2020" 
                    icon="pi-briefcase" 
                    iconColor="green" 
                />
                <StatCard 
                    title="Dividen" 
                    value="Rp631.391"
                    valueText=" Miliar" 
                    change="+5%" 
                    changeType="positive" 
                    changeText="sejak 2020" 
                    icon="pi-briefcase" 
                    iconColor="green" 
                />
            </div> -->
        </section>

        <!-- Layanan Informasi Publik -->
        <section id="Layanan" class="relative bg-white dark:bg-white">
            <div
                class="relative w-full py-32 px-12 md:px-0 text-center bg-cover bg-no-repeat bg-fixed"
                style="
                    background-image: url('/images/bg/parallax.jpg');
                    background-position: center 15%;
                "
            >
                <div class="absolute inset-0 bg-black/60"></div>
                <div v-animate-on-scroll class="relative">
                    <h2
                        class="font-bold text-white text-5xl mb-4 leading-tight"
                    >
                        {{
                            t(
                                "welcome.services.heading",
                                "Layanan Informasi Publik"
                            )
                        }}
                    </h2>
                    <p class="text-white text-2xl mb-12">
                        {{
                            t(
                                "welcome.services.description",
                                "Nyok, ajuin permohonan informasi untuk mendapatkan info yang kamu mau."
                            )
                        }}
                    </p>
                    <a href="/docs/form-pip.pdf" title="Download Formulir" target="_blank" rel="noopener noreferrer"
                        class="bg-white text-primary border border-white hover:bg-primary hover:text-white hover:border-primary px-8 py-4 rounded-md font-bold transition-all duration-300">
                        {{
                            t("welcome.services.download", "Download Formulir")
                        }}</a>
                </div>
            </div>
        </section>

        <!-- Berita -->
        <section id="Berita" class="px-4 py-24 bg-gray-50 dark:bg-gray-900">
            <div class="px-4 md:px-12">
                <div>
                    <div v-animate-on-scroll class="text-center mb-12">
                        <h2
                            class="text-5xl font-extrabold mb-4 text-gray-900 dark:text-white"
                        >
                            {{ t("welcome.news.heading", "Berita") }}
                        </h2>
                        <h3
                            class="text-xl max-w-2xl mx-auto font-normal mb-4 text-gray-900 dark:text-white"
                        >
                            {{
                                t(
                                    "welcome.news.description",
                                    "Informasi terbaru tentang pengumuman dan kegiatan dalam mendukung pelayanan publik dan pertumbuhan ekonomi Jakarta."
                                )
                            }}
                        </h3>
                    </div>
                    <div
                        class="flex flex-col md:flex-row justify-center items-center mb-12 gap-4"
                    >
                        <select
                            v-model="selectedCategory"
                            aria-label="Kategori Berita"
                            class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                        >
                            <option :value="null">
                                {{
                                    t(
                                        "welcome.news.all_categories",
                                        "Semua Kategori"
                                    )
                                }}
                            </option>
                            <option
                                v-for="category in berita_categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.category_name }}
                            </option>
                        </select>

                        <!-- Navigation Arrows and View All Button -->
                        <div
                            class="w-full flex flex-col md:flex-row justify-end items-center gap-x-4 gap-y-0 md:gap-y-8"
                        >
                            <div
                                class="flex flex-row gap-4 mr-0 md:mr-8 mb-8 md:mb-0 mt-4 md:mt-0"
                            >
                                <button
                                    type="button"
                                    aria-label="Previous"
                                    class="news-carousel-prev inline-flex items-center justify-center w-10 h-10 rounded-full border-2 border-primary dark:border-white bg-white dark:bg-gray-800 text-primary dark:text-white hover:bg-primary hover:text-white dark:hover:bg-white dark:hover:text-gray-900 shadow-lg transition-all"
                                >
                                    <i class="pi pi-chevron-left text-sm"></i>
                                </button>

                                <button
                                    type="button"
                                    aria-label="Next"
                                    class="news-carousel-next inline-flex items-center justify-center w-10 h-10 rounded-full border-2 border-primary dark:border-white bg-white dark:bg-gray-800 text-primary dark:text-white hover:bg-primary hover:text-white dark:hover:bg-white dark:hover:text-gray-900 shadow-lg transition-all"
                                >
                                    <i class="pi pi-chevron-right text-sm"></i>
                                </button>
                            </div>

                            <div>
                                <Link
                                    href="/berita"
                                    :title="
                                        t(
                                            'welcome.news.view_all',
                                            'Lihat Semua Berita'
                                        )
                                    "
                                    class="text-center text-sm font-bold text-primary dark:text-gray-300 hover:text-white mt-12 px-8 py-3 rounded-md border-2 border-primary dark:border-gray-300 hover:bg-primary transition-all duration-300"
                                >
                                    {{
                                        t(
                                            "welcome.news.view_all",
                                            "Lihat Semua Berita"
                                        )
                                    }}</Link
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <template v-if="!isBeritaLoaded">
                    <div
                        class="grid grid-cols-2 lg:grid-cols-3 gap-y-8 gap-6 w-full max-w-5xl mx-auto"
                    >
                        <SkeletonCard v-for="n in 6" :key="n" />
                    </div>
                </template>

                <div
                    v-else
                    v-animate-on-scroll
                    class="grid grid-cols-1 lg:grid-cols-12 gap-6 w-full"
                >
                    <!-- Right: News Carousel -->
                    <div class="lg:col-span-12">
                        <div
                            v-if="filteredBerita.length === 0"
                            class="text-center py-12"
                        >
                            <p class="text-gray-500 dark:text-gray-400 text-lg">
                                {{
                                    t(
                                        "welcome.news.no_news",
                                        "Tidak ada berita tersedia"
                                    )
                                }}
                            </p>
                        </div>

                        <Swiper
                            v-else
                            :modules="newsCarouselModules"
                            :slides-per-view="1.5"
                            :space-between="24"
                            :loop="false"
                            :breakpoints="{
                                640: { slidesPerView: 1.5, spaceBetween: 24 },
                                768: { slidesPerView: 3.5, spaceBetween: 24 },
                                1024: { slidesPerView: 4.5, spaceBetween: 24 },
                                1600: { slidesPerView: 5.5, spaceBetween: 24 },
                            }"
                            :navigation="{
                                nextEl: '.news-carousel-next',
                                prevEl: '.news-carousel-prev',
                            }"
                            class="w-full relative group"
                            @swiper="onNewsSwiper"
                        >
                            <SwiperSlide
                                v-for="item in filteredBerita"
                                :key="item.id"
                                class="!h-auto pb-4"
                            >
                                <Link
                                    :href="
                                        route('beritas.showPublic', item.slug)
                                    "
                                    :title="item.title"
                                    class="block w-full h-full group/card"
                                >
                                    <div
                                        class="bg-white dark:bg-gray-900 border md:border-1 border-gray-200 dark:border-gray-700 shadow-sm w-full h-full flex flex-col transition-shadow duration-300 hover:shadow"
                                    >
                                        <figure
                                            class="w-full mb-2 overflow-hidden"
                                        >
                                            <img
                                                :src="item.image"
                                                :alt="
                                                    localizeField(
                                                        item.title_translations,
                                                        item.title
                                                    )
                                                "
                                                :title="
                                                    localizeField(
                                                        item.title_translations,
                                                        item.title
                                                    )
                                                "
                                                loading="lazy"
                                                @load="
                                                    handleImageLoad(
                                                        `berita-${item.id}`
                                                    )
                                                "
                                                :class="[
                                                    'w-full h-full object-cover aspect-square transition-transform duration-300 group-hover/card:scale-105',
                                                    loadedImages.has(
                                                        `berita-${item.id}`
                                                    )
                                                        ? 'image-fade-in'
                                                        : '',
                                                ]"
                                            />
                                        </figure>
                                        <div
                                            class="p-4 flex flex-col flex-grow"
                                        >
                                            <p
                                                class="text-gray-500 text-xs md:text-sm mb-2 dark:text-white"
                                            >
                                                {{
                                                    new Date(
                                                        item.created_at
                                                    ).toLocaleDateString(
                                                        dateLocale,
                                                        {
                                                            day: "numeric",
                                                            month: "long",
                                                            year: "numeric",
                                                        }
                                                    )
                                                }}
                                            </p>
                                            <h3
                                                class="text-base md:text-lg font-bold mb-2 group-hover/card:text-primary transition-colors duration-300 flex-grow"
                                            >
                                                {{
                                                    (
                                                        localizeField(
                                                            item.title_translations,
                                                            item.title
                                                        ) || ""
                                                    ).length > 50
                                                        ? localizeField(
                                                              item.title_translations,
                                                              item.title
                                                          ).substring(0, 47) +
                                                          "..."
                                                        : localizeField(
                                                              item.title_translations,
                                                              item.title
                                                          )
                                                }}
                                            </h3>
                                            <div
                                                class="text-gray-900 dark:text-white text-sm inline-flex justify-start items-center group-hover/card:text-primary transition-colors duration-300 mt-auto"
                                            >
                                                <span>{{
                                                    t(
                                                        "welcome.news.read_more",
                                                        "Baca Selengkapnya"
                                                    )
                                                }}</span>
                                                <i
                                                    class="pi pi-arrow-right mx-2"
                                                    style="
                                                        font-size: 10px;
                                                        margin-top: 3px;
                                                    "
                                                ></i>
                                            </div>
                                        </div>
                                    </div>
                                </Link>
                            </SwiperSlide>
                        </Swiper>
                    </div>
                </div>
            </div>
        </section>

        <!-- PENGUMUMAN -->
        <section id="Pengumuman" class="px-4 py-24 bg-white dark:bg-gray-800">
            <div class="px-4 md:px-12">
                <div>
                    <div v-animate-on-scroll class="text-center mb-12">
                        <h2
                            class="text-5xl font-extrabold mb-4 text-gray-900 dark:text-white"
                        >
                            {{
                                t("welcome.announcements.heading", "Pengumuman")
                            }}
                        </h2>
                        <h3
                            class="text-xl max-w-2xl mx-auto font-normal mb-4 text-gray-900 dark:text-white"
                        >
                            {{
                                t(
                                    "welcome.announcements.description",
                                    "Informasi resmi dan pengumuman penting dari BPBUMD Provinsi DKI Jakarta"
                                )
                            }}
                        </h3>
                    </div>
                </div>

                <!-- Tampilkan jika belum ada data pengumuman -->
                <template v-if="!pengumuman || pengumuman.length === 0">
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400 text-lg">
                            {{
                                t(
                                    "welcome.announcements.no_data",
                                    "Tidak ada pengumuman saat ini"
                                )
                            }}
                        </p>
                    </div>
                </template>

                <!-- Grid Pengumuman -->
                <div
                    v-else
                    v-animate-on-scroll
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="item in pengumuman"
                        :key="item.id"
                        class="bg-white dark:bg-gray-900 border border-yellow-100 dark:border-yellow-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300"
                    >
                        <!-- Badge Pengumuman -->
                        <div class="px-4 pt-4">
                            <span
                                class="inline-block bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs font-semibold px-3 py-1 rounded-full"
                            >
                                {{
                                    t(
                                        "welcome.announcements.badge",
                                        "PENGUMUMAN"
                                    )
                                }}
                            </span>
                        </div>

                        <!-- Konten -->
                        <div class="p-4">
                            <!-- Tanggal -->
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400 mb-2"
                            >
                                {{
                                    new Date(
                                        item.created_at
                                    ).toLocaleDateString(dateLocale, {
                                        day: "numeric",
                                        month: "long",
                                        year: "numeric",
                                    })
                                }}
                            </p>

                            <!-- Judul -->
                            <Link
                                :href="
                                    route('pengumuman.showPublic', item.slug)
                                "
                                :title="
                                    localizeField(
                                        item.title_translations,
                                        item.title
                                    )
                                "
                                class="block mb-3"
                            >
                                <h3
                                    class="text-lg font-bold text-gray-900 dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors line-clamp-2"
                                >
                                    {{
                                        localizeField(
                                            item.title_translations,
                                            item.title
                                        )
                                    }}
                                </h3>
                            </Link>

                            <!-- Excerpt/Deskripsi Singkat -->
                            <p
                                class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3"
                            >
                                {{
                                    localizeField(
                                        item.excerpt_translations,
                                        item.excerpt
                                    ) ||
                                    item.excerpt ||
                                    ""
                                }}
                            </p>

                            <!-- Link Baca Selengkapnya -->
                            <div
                                class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700"
                            >
                                <Link
                                    :href="
                                        route(
                                            'pengumuman.showPublic',
                                            item.slug
                                        )
                                    "
                                    :title="
                                        localizeField(
                                            item.title_translations,
                                            item.title
                                        )
                                    "
                                    class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 font-semibold text-sm inline-flex items-center"
                                >
                                    <span>{{
                                        t(
                                            "welcome.announcements.read_more",
                                            "Lihat Pengumuman"
                                        )
                                    }}</span>
                                    <i
                                        class="pi pi-arrow-right ml-2"
                                        style="font-size: 12px"
                                    ></i>
                                </Link>

                                <!-- Tipe/Kategori (opsional) -->
                                <span
                                    v-if="item.category"
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ item.category.category_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Lihat Semua Pengumuman -->
                <div class="text-center mt-12">
                    <Link
                        :href="route('pengumuman.indexPublic')"
                        :title="
                            t(
                                'welcome.announcements.view_all',
                                'Lihat Semua Pengumuman'
                            )
                        "
                    >
                        <span>{{
                            t(
                                "welcome.announcements.view_all",
                                "Lihat Semua Pengumuman"
                            )
                        }}</span>
                        <i class="pi pi-arrow-right ml-2"></i>
                    </Link>
                </div>
            </div>
        </section>

        <!-- BUMD -->
        <section id="BUMD" class="px-4 py-24">
            <div>
                <h2
                    v-animate-on-scroll
                    class="text-5xl font-extrabold mb-8 text-center"
                >
                    {{ t("welcome.bumd.heading", "BUMD") }}
                </h2>
                <p class="text-2xl text-center mb-3">
                    {{ t("welcome.bumd.subheading", "Provinsi DKI Jakarta") }}
                </p>
                <section id="Isometric" class="px-4 pt-6 pb-24">
                    <div class="mb-8">
                        <p class="text-xs text-center">
                            {{
                                t(
                                    "welcome.bumd.instruction",
                                    "(Arahkan kursor ke titik atau klik titik untuk melihat informasi)"
                                )
                            }}
                        </p>
                    </div>
                    <div class="mx-auto">
                        <Isometric
                            v-animate-on-scroll
                            :imageSrc="isometricData.imageSrc"
                            :imageAlt="isometricData.imageAlt"
                            :infoDots="isometricData.infoDots"
                        />
                    </div>
                </section>
                <template v-if="!isBumdLoaded">
                    <div
                        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 w-full max-w-4xl mx-auto"
                    >
                        <SkeletonLogo v-for="n in 10" :key="n" />
                    </div>
                </template>
                <div
                    v-else
                    v-animate-on-scroll
                    class="w-full max-w-7xl mx-auto overflow-hidden px-12 md:px-0"
                >
                    <div
                        class="flex flex-wrap justify-center [&>a:nth-child(2n+1)]:border-l-0 [&>a:nth-child(2n)]:border-r-0 md:[&>a:nth-child(2n+1)]:border-l md:[&>a:nth-child(2n)]:border-r md:[&>a:nth-child(3n+1)]:border-l-0 md:[&>a:nth-child(3n)]:border-r-0 lg:[&>a:nth-child(3n+1)]:border-l lg:[&>a:nth-child(3n)]:border-r lg:[&>a:nth-child(8n+1)]:border-l-0 lg:[&>a:nth-child(8n)]:border-r-0 [&>a:last-child]:border-r-0"
                    >
                        <Link
                            :href="route('bumds.showPublic', item.kode)"
                            :title="item.nama"
                            class="group p-4 w-1/2 md:w-1/3 lg:w-[12.5%] dark:rounded-md aspect-square border border-gray-200 dark:border-transparent -ml-px -mt-px flex flex-col justify-center items-center"
                            v-for="item in bumd.data" :key="item.id">
                        <figure class="w-full h-full mx-auto rounded overflow-hidden dark:bg-white">
                            <template v-if="item.logo && item.logo.trim() !== ''">
                                <img :src="item.logo" :alt="item.nama" :title="item.nama" loading="lazy"
                                    @error="setFallbackImage" @load="handleImageLoad(`bumd-${item.id}`)" :class="[
                                        'w-full h-full object-contain rounded p-6 transition-transform duration-300 ease-out group-hover:scale-125',
                                        loadedImages.has(`bumd-${item.id}`)
                                            ? 'image-fade-in'
                                            : '',
                                    ]" />
                            </template>
                            <template v-else>
                                <img :src="fallbackLogo" :alt="item.nama" :title="item.nama" loading="lazy"
                                    class="w-full h-full object-contain rounded p-6 transition-transform duration-300 ease-out group-hover:scale-125" />
                            </template>
                        </figure>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Galeri -->
        <section id="Galeri" class="px-4 py-24 bg-gray-50 dark:bg-gray-900">
            <div class="w-full">
                <h2
                    v-animate-on-scroll
                    class="text-5xl font-extrabold mb-4 text-center text-gray-900 dark:text-white"
                >
                    {{ t("welcome.gallery.heading", "Galeri") }}
                </h2>
                <p
                    class="text-2xl text-center mb-12 text-gray-800 dark:text-white"
                >
                    {{
                        t(
                            "welcome.gallery.description",
                            "Foto-foto kegiatan BPBUMD Jakarta"
                        )
                    }}
                </p>
                <template v-if="!isAlbumLoaded">
                    <div
                        class="grid grid-cols-4 md:grid-cols-8 gap-2 w-full max-w-7xl mx-auto"
                    >
                        <SkeletonCard v-for="n in 6" :key="n" />
                    </div>
                </template>
                <div
                    v-else
                    v-animate-on-scroll
                    class="grid grid-cols-2 md:grid-cols-8 gap-2 w-full mx-auto"
                >
                    <!-- First large image - spans 2 columns and 2 rows -->
                    <div v-if="album.data[0]"
                        class="col-span-2 md:col-span-4 row-span-2 overflow-hidden group cursor-pointer relative">
                        <Link :href="route('albums.showPublic', album.data[0].id)" :title="localizeField(
                            album.data[0].title_translations,
                            album.data[0].title
                        )
                            " class="block h-full relative">
                        <figure class="w-full h-full overflow-hidden">
                            <img :src="album.data[0].image" :alt="localizeField(
                                album.data[0].title_translations,
                                album.data[0].title
                            )
                                " :title="localizeField(
                                    album.data[0].title_translations,
                                    album.data[0].title
                                )
                                    " loading="lazy" @load="
                                        handleImageLoad(
                                            `album-${album.data[0].id}`
                                        )
                                        " :class="[
                                                'w-full h-full object-cover transition-transform duration-300 group-hover:scale-110',
                                                loadedImages.has(
                                                    `album-${album.data[0].id}`
                                                )
                                                    ? 'image-fade-in'
                                                    : '',
                                            ]" />
                        </figure>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 pb-6 px-4 md:px-4 text-white">
                            <p class="text-xs md:text-sm text-gray-200 mb-1 md:mb-2">
                                {{
                                    new Date(
                                        album.data[0].created_at
                                    ).toLocaleDateString(dateLocale, {
                                        day: "numeric",
                                        month: "long",
                                        year: "numeric",
                                    })
                                }}
                            </p>
                            <h3 class="font-bold text-sm md:text-lg lg:text-xl line-clamp-2 drop-shadow-lg">
                                {{
                                    localizeField(
                                        album.data[0].title_translations,
                                        album.data[0].title
                                    )
                                }}
                            </h3>
                        </div>
                        </Link>
                    </div>

                    <!-- Top right images (2-3) -->
                    <div
                        v-if="album.data[1]"
                        class="col-span-1 md:col-span-2 row-span-1 overflow-hidden group cursor-pointer relative"
                    >
                        <Link
                            :href="route('albums.showPublic', album.data[1].id)"
                            :title="
                                localizeField(
                                    album.data[1].title_translations,
                                    album.data[1].title
                                )
                            "
                            class="block h-full relative"
                        >
                            <figure class="w-full h-full overflow-hidden">
                                <img
                                    :src="album.data[1].image"
                                    :alt="
                                        localizeField(
                                            album.data[1].title_translations,
                                            album.data[1].title
                                        )
                                    "
                                    :title="
                                        localizeField(
                                            album.data[1].title_translations,
                                            album.data[1].title
                                        )
                                    "
                                    loading="lazy"
                                    @load="
                                        handleImageLoad(
                                            `album-${album.data[1].id}`
                                        )
                                        " :class="[
                                                'w-full h-full object-cover transition-transform duration-300 group-hover:scale-110',
                                                loadedImages.has(
                                                    `album-${album.data[1].id}`
                                                )
                                                    ? 'image-fade-in'
                                                    : '',
                                            ]" />
                        </figure>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 pb-6 px-4 md:px-3 text-white">
                            <p class="text-xs text-gray-200 mb-1 hidden md:block">
                                {{
                                    new Date(
                                        album.data[1].created_at
                                    ).toLocaleDateString(dateLocale, {
                                        day: "numeric",
                                        month: "short",
                                        year: "numeric",
                                    })
                                }}
                            </p>
                            <h3 class="font-bold text-xs md:text-sm lg:text-base line-clamp-2 drop-shadow-lg">
                                {{
                                    localizeField(
                                        album.data[1].title_translations,
                                        album.data[1].title
                                    )
                                }}
                            </h3>
                        </div>
                        </Link>
                    </div>

                    <div
                        v-if="album.data[2]"
                        class="col-span-1 md:col-span-2 row-span-1 overflow-hidden group cursor-pointer relative"
                    >
                        <Link
                            :href="route('albums.showPublic', album.data[2].id)"
                            :title="
                                localizeField(
                                    album.data[2].title_translations,
                                    album.data[2].title
                                )
                            "
                            class="block h-full relative"
                        >
                            <figure class="w-full h-full overflow-hidden">
                                <img
                                    :src="album.data[2].image"
                                    :alt="
                                        localizeField(
                                            album.data[2].title_translations,
                                            album.data[2].title
                                        )
                                    "
                                    :title="
                                        localizeField(
                                            album.data[2].title_translations,
                                            album.data[2].title
                                        )
                                    "
                                    loading="lazy"
                                    @load="
                                        handleImageLoad(
                                            `album-${album.data[2].id}`
                                        )
                                        " :class="[
                                                'w-full h-full object-cover transition-transform duration-300 group-hover:scale-110',
                                                loadedImages.has(
                                                    `album-${album.data[2].id}`
                                                )
                                                    ? 'image-fade-in'
                                                    : '',
                                            ]" />
                        </figure>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 pb-6 px-4 md:px-3 text-white">
                            <p class="text-xs text-gray-200 mb-1 hidden md:block">
                                {{
                                    new Date(
                                        album.data[2].created_at
                                    ).toLocaleDateString(dateLocale, {
                                        day: "numeric",
                                        month: "short",
                                        year: "numeric",
                                    })
                                }}
                            </p>
                            <h3 class="font-bold text-xs md:text-sm lg:text-base line-clamp-2 drop-shadow-lg">
                                {{
                                    localizeField(
                                        album.data[2].title_translations,
                                        album.data[2].title
                                    )
                                }}
                            </h3>
                        </div>
                        </Link>
                    </div>

                    <!-- Bottom right images (4-5) -->
                    <div
                        v-if="album.data[3]"
                        class="col-span-1 md:col-span-2 row-span-1 overflow-hidden group cursor-pointer relative"
                    >
                        <Link
                            :href="route('albums.showPublic', album.data[3].id)"
                            :title="
                                localizeField(
                                    album.data[3].title_translations,
                                    album.data[3].title
                                )
                            "
                            class="block h-full relative"
                        >
                            <figure class="w-full h-full overflow-hidden">
                                <img
                                    :src="album.data[3].image"
                                    :alt="
                                        localizeField(
                                            album.data[3].title_translations,
                                            album.data[3].title
                                        )
                                    "
                                    :title="
                                        localizeField(
                                            album.data[3].title_translations,
                                            album.data[3].title
                                        )
                                    "
                                    loading="lazy"
                                    @load="
                                        handleImageLoad(
                                            `album-${album.data[3].id}`
                                        )
                                        " :class="[
                                                'w-full h-full object-cover transition-transform duration-300 group-hover:scale-110',
                                                loadedImages.has(
                                                    `album-${album.data[3].id}`
                                                )
                                                    ? 'image-fade-in'
                                                    : '',
                                            ]" />
                        </figure>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 pb-6 px-4 md:px-3 text-white">
                            <p class="text-xs text-gray-200 mb-1 hidden md:block">
                                {{
                                    new Date(
                                        album.data[3].created_at
                                    ).toLocaleDateString(dateLocale, {
                                        day: "numeric",
                                        month: "short",
                                        year: "numeric",
                                    })
                                }}
                            </p>
                            <h3 class="font-bold text-xs md:text-sm lg:text-base line-clamp-2 drop-shadow-lg">
                                {{
                                    localizeField(
                                        album.data[3].title_translations,
                                        album.data[3].title
                                    )
                                }}
                            </h3>
                        </div>
                        </Link>
                    </div>

                    <div
                        v-if="album.data[4]"
                        class="col-span-1 md:col-span-2 row-span-1 overflow-hidden group cursor-pointer relative"
                    >
                        <Link
                            :href="route('albums.showPublic', album.data[4].id)"
                            :title="
                                localizeField(
                                    album.data[4].title_translations,
                                    album.data[4].title
                                )
                            "
                            class="block h-full relative"
                        >
                            <figure class="w-full h-full overflow-hidden">
                                <img
                                    :src="album.data[4].image"
                                    :alt="
                                        localizeField(
                                            album.data[4].title_translations,
                                            album.data[4].title
                                        )
                                    "
                                    :title="
                                        localizeField(
                                            album.data[4].title_translations,
                                            album.data[4].title
                                        )
                                    "
                                    loading="lazy"
                                    @load="
                                        handleImageLoad(
                                            `album-${album.data[4].id}`
                                        )
                                        " :class="[
                                                'w-full h-full object-cover transition-transform duration-300 group-hover:scale-110',
                                                loadedImages.has(
                                                    `album-${album.data[4].id}`
                                                )
                                                    ? 'image-fade-in'
                                                    : '',
                                            ]" />
                        </figure>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 pb-6 px-4 md:px-3 text-white">
                            <p class="text-xs text-gray-200 mb-1 hidden md:block">
                                {{
                                    new Date(
                                        album.data[4].created_at
                                    ).toLocaleDateString(dateLocale, {
                                        day: "numeric",
                                        month: "short",
                                        year: "numeric",
                                    })
                                }}
                            </p>
                            <h3 class="font-bold text-xs md:text-sm lg:text-base line-clamp-2 drop-shadow-lg">
                                {{
                                    localizeField(
                                        album.data[4].title_translations,
                                        album.data[4].title
                                    )
                                }}
                            </h3>
                        </div>
                        </Link>
                    </div>
                </div>
            </div>
            <div class="w-full flex justify-center">
                <Link
                    href="/galeri"
                    title="Lihat Semua Galeri"
                    class="bg-primary text-center text-sm font-bold text-white mt-12 px-8 py-2 rounded-md hover:bg-primary-hover transition-all duration-300"
                >
                    {{ t("welcome.gallery.view_all", "Lihat Semua Galeri") }}
                </Link>
            </div>
        </section>
    </GuestLayout>
</template>
