<script setup>
import { ref, computed } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Controller, Mousewheel } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import useTranslations from '@/Composables/useTranslations.js';

const { t } = useTranslations();

// Define eras for the sidebar navigation
const eras = computed(() => [
    { id: '1960s', label: t('timeline.eras.humble_beginning', 'A Humble Beginning'), sub: '(1960s - 1990s)', startYear: 1974 },
    { id: '2000s', label: t('timeline.eras.stepping_out', 'Stepping Out'), sub: '(2000s)', startYear: 2002 },
    { id: '2010s', label: t('timeline.eras.global_investment', 'Global Investment'), sub: '(2010s)', startYear: 2014 },
    { id: '2020s', label: t('timeline.eras.looking_ahead', 'Looking Ahead'), sub: '(2020s)', startYear: 2024 },
]);

const timelineData = computed(() => [
    {
        year: '1527',
        title: t('timeline.items.1527.title', 'Berdirinya Kota Jakarta'),
        description: t('timeline.items.1527.description', 'Berdirinya Jayakarta pada tahun 1527, yang merupakan ibu kota Provinsi DKI Jakarta.'),
        image: '/images/timeline/kota-tua.jpg',
        link: null,
        era: '1520s'
    },
    {
        year: '1945',
        title: t('timeline.items.1945.title', 'Indonesia Merdeka'),
        description: t('timeline.items.1945.description', 'Indonesia merdeka pada 17 Agustus 1945.'),
        image: '/images/hero/bendera.jpg',
        link: null,
        era: '1940s'
    },
    {
        year: '1961',
        title: t('timeline.items.1961.title', 'Bank Jakarta'),
        description: t('timeline.items.1961.description', 'Bank Jakarta berdiri dan beroperasi sejak tanggal 11 April 1961 merupakan bank pembangunan daerah pertama yang lahir di Indonesia seiring dengan terbentuknya kota Jakarta sebagai ibukota Indonesia.'),
        image: '/images/timeline/bank-jakarta.jpg',
        link: null,
        era: '1960s'
    },
    {
        year: '1966',
        title: t('timeline.items.1966_pasar_jaya.title', 'Perumda Pasar Jaya'),
        description: t('timeline.items.1966_pasar_jaya.description', 'Perusahaan Daerah Pasar Jaya didirikan berdasarkan Keputusan Gubernur Kepala Daerah Khusus Ibukota Jakarta No. lb.3/2/15/66 pada tanggal 24 Desember 1966.'),
        image: '/images/timeline/pasar-jaya.jpg',
        link: null,
        era: '1960s'
    },
    {
        year: '1966',
        title: t('timeline.items.1966_dharma_jaya.title', 'Perumda Dharma Jaya'),
        description: t('timeline.items.1966_dharma_jaya.description', 'Transformasi menjadi sentra protein hewani di Indonesia adalah capaian penting bagi Perumda Dharma Jaya yang disadari harus melalui proses panjang.'),
        image: '/images/timeline/dharma.jpg',
        link: null,
        era: '1960s'
    },
    {
        year: '1973',
        title: t('timeline.items.1973.title', 'PT JIEP'),
        description: t('timeline.items.1973.description', 'Pendirian PT Jakarta Industrial Estate Pulogadung (PT JIEP), 26 Juni 1973, pengganti Proyek Industrial Estate Pulogadung. Komposisi kepemilikan saham: 50% Pemerintah Pusat, 50% Pemerintah Provinsi DKI Jakarta.'),
        image: '/images/timeline/jiep.jpg',
        link: null,
        era: '1970s'
    },
    {
        year: '1977',
        title: t('timeline.items.1977.title', 'PAM Jaya'),
        description: t('timeline.items.1977.description', 'Berawal dari tanggal 23 Desember 1922, untuk pertama kalinya air yang berasal dari Desa Ciburial, Bogor, dialirkan ke kota Batavia (Jakarta), dan pada tanggal tersebut dijadikan sebagai hari jadi PAM Jaya.'),
        image: '/images/timeline/pam.jpg',
        link: null,
        era: '1970s'
    },
    {
        year: '1982',
        title: t('timeline.items.1982.title', 'Perumda Sarana Jaya'),
        description: t('timeline.items.1982.description', 'Perusahaan Umum Daerah (Perumda) Pembangunan Sarana Jaya berdiri sejak tahun 1982, yang pada awal berdirinya Sarana Jaya berorientasi sebagai penunjang kebijaksanaan umum Pemprov DKI Jakarta dalam rangka meningkatkan kesejahteraan rakyat.'),
        image: '/images/timeline/sarana-jaya.jpg',
        link: null,
        era: '1980s'
    },
    {
        year: '1991',
        title: t('timeline.items.1991.title', 'Perumda Paljaya'),
        description: t('timeline.items.1991.description', 'PPD PAL JAYA Berdasarkan Perda Nomor 10 Tahun 1991 tanggal 26 September 1991 tentang PD PAL JAYA.'),
        image: '/images/timeline/pal.jpg',
        link: null,
        era: '1990s'
    },
    {
        year: '1992',
        title: t('timeline.items.1992.title', 'PT Pembangunan Jaya Ancol'),
        description: t('timeline.items.1992.description', 'BPP Proyek Ancol mulai melakukan pembenahan secara internal menyusul perubahan status badan hukumnya menjadi PT Pembangunan Jaya Ancol (“Ancol”) melalui Akta Perubahan No. 33 tanggal 10 Juli 1992.'),
        image: '/images/timeline/ancol.jpg',
        link: null,
        era: '1990s'
    },
        {
        year: '2000',
        title: t('timeline.items.1960.title', 'PT Jakarta Propertindo'),
        description: t('timeline.items.1960.description', 'Jakpro dibangun dengan misi mengembangkan komunitas dan karya yang mengedepankan prinsip keberlanjutan (sustainability).'),
        image: '/images/timeline/jakpro.jpg',
        link: null,
        era: '2000s'
    },
    {
        year: '2003',
        title: t('timeline.items.2003.title', 'PT Jakarta Tourisindo (Perseroda)'),
        description: t('timeline.items.2003.description', 'Berdasarkan SK Gubernur No.2161/2003 tanggal 2 Juli 2003 PT Jakarta Tourisindo dibentuk dalam rangka pendirian sub-holding company wisata dan perhotelan di lingkungan pemerintah Provinsi DKI Jakarta.'),
        image: '/images/timeline/jkktour.jpg',
        link: null,
        era: '2000s'
    },
    {
        year: '2004',
        title: t('timeline.items.2004.title', 'PT Transportasi Jakarta'),
        description: t('timeline.items.2004.description', 'TransJakarta adalah sebuah sistem transportasi Bus Rapid Transit (BRT) pertama di Asia Tenggara dan Selatan dengan jalur lintasan terpanjang di dunia (208 km).'),
        image: '/images/timeline/tj.jpg',
        link: null,
        era: '2000s'
    },
    {
        year: '2008',
        title: t('timeline.items.2008.title', 'PT MRT Jakarta'),
        description: t('timeline.items.2008.description', 'PT MRT Jakarta memiliki ruang lingkup kegiatan di antaranya untuk pengusahaan dan pembangunan prasarana dan sarana MRT.'),
        image: '/images/timeline/mrt.jpg',
        link: null,
        era: '2000s'
    },
    {
        year: '2013',
        title: t('timeline.items.2013.title', 'PT Jamkrida Jakarta'),
        description: t('timeline.items.2013.description', 'PT Jamkrida Jakarta adalah Badan Usaha Perseroan Terbatas Milik Pemprov DKI Jakarta yang melaksanakan kegiatan usaha di bidang penjaminan kredit bagi Usaha Mikro, Kecil, Menengah, dan Koperasi (UMKMK).'),
        image: '/images/timeline/jamkrida.jpg',
        link: null,
        era: '2010s'
    },
]);

const swiperRef = ref(null);
const activeIndex = ref(0);

const onSwiper = (swiper) => {
    swiperRef.value = swiper;
};

const onSlideChange = (swiper) => {
    activeIndex.value = swiper.activeIndex;
};

const jumpToEra = (eraId) => {
    const index = timelineData.value.findIndex(item => item.era === eraId);
    if (index !== -1 && swiperRef.value) {
        swiperRef.value.slideTo(index);
    }
};

const jumpToYear = (index) => {
    if (swiperRef.value) {
        swiperRef.value.slideTo(index);
    }
};

const activeEra = computed(() => {
    return timelineData.value[activeIndex.value]?.era;
});

const uniqueTimelineMarkers = computed(() => {
    return timelineData.value.reduce((acc, item, index) => {
        if (!acc.some(marker => marker.year === item.year)) {
            acc.push({ year: item.year, index });
        }
        return acc;
    }, []);
});

</script>

<template>
    <section class="relative bg-gray-100 dark:bg-gray-900 flex flex-col h-[800px] overflow-hidden">

        <!-- Background Elements -->
        <div class="absolute inset-0 z-0 opacity-10 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                preserveAspectRatio="none" viewBox="0 0 1440 560">
                <g fill="none">
                    <rect x="0" y="0" fill="rgba(245, 245, 245, 0.5)"></rect>
                    <path
                        d="M -512.854422184578,84 C -440.85,128.4 -296.85,302.2 -152.854422184578,306 C -8.85,309.8 63.15,90.4 207.145577815422,103 C 351.15,115.6 423.15,367.2 567.145577815422,369 C 711.15,370.8 783.15,116.2 927.145577815422,112 C 1071.15,107.8 1184.57,344 1287.145577815422,348 C 1389.72,352 1409.43,175.2 1440,132"
                        stroke="rgba(97, 97, 97, 0.5)" stroke-width="2"></path>
                    <path
                        d="M -305.42187094114973,377 C -233.42,318.6 -89.42,95 54.57812905885029,85 C 198.58,75 270.58,312 414.57812905885027,327 C 558.58,342 630.58,150.8 774.5781290588503,160 C 918.58,169.2 990.58,355 1134.5781290588502,373 C 1278.58,391 1433.49,251.8 1494.5781290588502,250 C 1555.66,248.2 1450.92,341.2 1440,364"
                        stroke="rgba(97, 97, 97, 0.5)" stroke-width="2"></path>
                    <path
                        d="M -661.8449407817884,352 C -589.84,301.2 -445.84,111 -301.84494078178835,98 C -157.84,85 -85.84,260 58.15505921821165,287 C 202.16,314 274.16,202 418.15505921821165,233 C 562.16,264 634.16,467.8 778.1550592182116,442 C 922.16,416.2 1005.79,104.2 1138.1550592182116,104 C 1270.52,103.8 1379.63,373.6 1440,441"
                        stroke="rgba(97, 97, 97, 0.5)" stroke-width="2"></path>
                    <path
                        d="M -244.27881245886414,377 C -172.28,342 -28.28,184.2 115.72118754113586,202 C 259.72,219.8 331.72,461 475.72118754113586,466 C 619.72,471 691.72,239.6 835.7211875411358,227 C 979.72,214.4 1051.72,404 1195.7211875411358,403 C 1339.72,402 1506.87,223.2 1555.7211875411358,222 C 1604.58,220.8 1463.14,362 1440,397"
                        stroke="rgba(97, 97, 97, 0.5)" stroke-width="2"></path>
                    <path
                        d="M -343.87133580832875,369 C -271.87,350.6 -127.87,276.6 16.128664191671245,277 C 160.13,277.4 232.13,372.8 376.12866419167125,371 C 520.13,369.2 592.13,281 736.1286641916712,268 C 880.13,255 952.13,325 1096.1286641916713,306 C 1240.13,287 1387.35,163.2 1456.1286641916713,173 C 1524.9,182.8 1443.23,318.6 1440,355"
                        stroke="rgba(97, 97, 97, 0.5)" stroke-width="2"></path>
                </g>
                <defs>
                    <mask id="SvgjsMask1004">
                        <rect fill="#ffffff"></rect>
                    </mask>
                </defs>
            </svg>
        </div>

        <div class="flex flex-col lg:flex-row flex-grow h-full relative z-10">


            <!-- Main Slider Content -->
            <div class="w-full lg:w-full relative flex flex-col h-auto">
                <Swiper :modules="[Navigation, Pagination, Controller, Mousewheel]" :slides-per-view="'auto'"
                    :centered-slides="true" :space-between="40" :mousewheel="true" :grab-cursor="true" :speed="600"
                    class="w-full h-full !pb-24 !pt-12" @swiper="onSwiper" @slideChange="onSlideChange">
                    <SwiperSlide v-for="(item, index) in timelineData" :key="index"
                        class="!w-[85vw] md:!w-[650px] !h-auto flex items-center justify-center transition-opacity duration-300 pb-12"
                        :class="{ 'opacity-40 scale-95': activeIndex !== index, 'opacity-100 scale-100': activeIndex === index }">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border h-full flex flex-col md:flex-row transition-all duration-300"
                            :class="[
                                item.title === 'BPBUMD'
                                    ? 'border-blue-500 dark:border-blue-400 bg-blue-100 border-2'
                                    : 'border-gray-100 dark:border-gray-700'
                            ]">
                            <div class="p-8 md:p-10 flex-1 flex flex-col justify-center">
                                <div class="text-sm font-bold text-gray-400 mb-2 tracking-widest uppercase">{{ item.year
                                    }}</div>
                                <h2
                                    class="text-3xl md:text-4xl font-bold text-primary dark:text-blue-300 mb-6 leading-tight">
                                    {{ item.title }}
                                </h2>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-8 text-lg">
                                    {{ item.description }}
                                </p>
                                <div v-if="item.link" class="mt-auto">
                                    <a :href="item.link"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <span class="ml-3 text-sm font-bold text-gray-900 dark:text-white">{{ t('timeline.read_more', 'Read more') }}</span>
                                </div>
                            </div>
                            <div v-if="item.image" class="md:w-1/2 h-64 md:h-auto relative">
                                <img :src="item.image" :alt="item.title"
                                    class="absolute inset-0 w-full h-full object-cover" />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent md:bg-gradient-to-l">
                                </div>
                            </div>
                        </div>
                    </SwiperSlide>
                </Swiper>

                <!-- Bottom Timeline Ruler -->
                <div
                    class="absolute bottom-0 left-0 w-full bg-white/90 dark:bg-gray-900/90 backdrop-blur h-20 z-30 pt-4 flex items-center overflow-x-auto hide-scrollbar">
                    <div
                        class="flex items-center px-4 w-full min-w-max mx-auto justify-center md:justify-start md:px-10">
                        <div v-for="(marker, index) in uniqueTimelineMarkers" :key="index"
                            @click="jumpToYear(marker.index)"
                            class="relative flex flex-col items-center cursor-pointer group px-6 transition-all duration-300"
                            :class="{ 'flex-grow': true }">
                            <div class="w-3 h-3 rounded-full mb-3 transition-all duration-300 z-10"
                                :class="timelineData[activeIndex]?.year === marker.year ? 'bg-blue-600 scale-150 ring-4 ring-blue-100 dark:ring-blue-900' : 'bg-gray-300 dark:bg-gray-600 group-hover:bg-blue-400'">
                            </div>
                            <span class="text-sm font-bold transition-colors duration-300"
                                :class="timelineData[activeIndex]?.year === marker.year ? 'text-blue-700 dark:text-blue-300' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300'">
                                {{ marker.year }}
                            </span>
                            <!-- Connecting Line -->
                            <div v-if="index < uniqueTimelineMarkers.length - 1"
                                class="absolute top-[5px] left-[50%] w-full h-[2px] bg-gray-200 dark:bg-gray-700 -z-0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Custom Swiper Styles */
:deep(.swiper-slide) {
    height: auto;
    align-self: stretch;
}
</style>
