<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import useTranslations from '@/Composables/useTranslations.js';

const { t } = useTranslations();

const timelineData = computed(() => [
    {
        year: '1967',
        title: t('timeline_sejarah.items.1967.title', 'Project Officer of Foreign Investment in Djakarta (POFID)'),
        description: t('timeline_sejarah.items.1967.description', 'Project Officer of Foreign Investment in Djakarta (POFID) dibentuk pada tahun 1967 sebagai pelaksana operasional Undang-Undang Nomor 1 Tahun 1967 tentang Penanaman Modal Asing di DKI Jakarta.'),
        image: null,
        link: null,
        era: '1960s'
    },
    {
        year: '1968',
        title: t('timeline_sejarah.items.1968.title', 'Djakarta Investment Office (DIO)'),
        description: t('timeline_sejarah.items.1968.description', 'Djakarta Investment Office (DIO) dibentuk pada tahun 1968 sejalan dengan diundangkannya Undang-Undang Nomor 6 Tahun 1968 tentang Penanaman Modal Dalam Negeri. FDIO merupakan pengganti POFID dengan bidang tugas yang bersifat lebih luas.'),
        image: null,
        link: null,
        era: '1960s'
    },
    {
        year: '1973',
        title: t('timeline_sejarah.items.1973.title', 'Badan Koordinasi Penanaman Modal Daerah (BKPMD)'),
        description: t('timeline_sejarah.items.1973.description', 'Pada tahun 1973 dibentuk Badan Koordinasi Penanaman Modal Daerah (BKPMD) DKI Jakarta berdasarkan Keputusan Menteri Dalam Negeri Nomor 16 Tahun 1973 tentang Badan Koordinasi Penanaman Modal Daerah, sebagai pelaksanaan lebih lanjut Keputusan Presiden Nomor 20 Tahun 1973.'),
        image: null,
        link: null,
        era: '1960s'
    },
    {
        year: '2001',
        title: t('timeline_sejarah.items.2001.title', 'Penanaman Modal dan Pendayagunaan Kekayaan dan Usaha Daerah (BPM & PKUD)'),
        description: t('timeline_sejarah.items.2001.description', 'Dalam rangka meningkatkan pelaksanaan fungsi Pemerintah Daerah yang berorientasi pada pelayanan publik dan pengembangan perekonomian serta memperhatikan kewenangan Pemerintah dan Kewenangan Propinsi sebagai Daerah Otonomi berdasarkan UU No. 22/1999, telah diterbitkan Peraturan Daerah Provinsi DKI Jakarta Nomor 3 Tahun 2001 tentang Bentuk Susunan Organisasi dan Tata Kerja Perangkat Daerah dan Sekretariat Dewan Perwakilan Rakyat Daerah Propinsi DKI Jakarta, dibentuklah Badan Penanaman Modal dan Pendayagunaan Kekayaan dan Usaha Daerah (BPM & PKUD).'),
        image: null,
        link: null,
        era: '2000s'
    },
    {
        year: '2008',
        title: t('timeline_sejarah.items.2008.title', 'Badan Penanaman Modal dan Pendayagunaan Kekayaan dan Usaha Daerah (BPM & PKUD)'),
        description: t('timeline_sejarah.items.2008.description', 'Badan Pengelola Keuangan Daerah (BPKD) berubah nama dan bentuk menjadi Badan Penanaman Modal dan Promosi (BPMP) Provinsi DKI Jakarta berdasarkan Peraturan daerah Nomor 10 Tahun 2008 tentang Organisasi Perangkat Daerah yang kemudian ditindaklanjuti dengan Peraturan Gubernur Provinsi DKI Jakarta Nomor 115 Tahun 2009 Tentang Organisasi dan Tata Kerja Badan Penanaman Modal dan Promosi.'),
        image: null,
        link: null,
        era: '2000s'
    },
    {
        year: '2010',
        title: t('timeline_sejarah.items.2010.title', 'Organisasi dan Tata Kerja Unit Pelayanan Terpadu Satu Pintu Bidang Penanaman Modal'),
        description: t('timeline_sejarah.items.2010.description', 'Pada tahun 2010, dikeluarkan Peraturan Gubernur nomor 223 Tahun 2010 mengenai pembentukan Organisasi dan Tata Kerja Unit Pelayanan Terpadu Satu Pintu Bidang Penanaman Modal. Unit PTSP Bidang Penanaman Modal ini merupakan unit pelaksana teknis Badan Penanaman Modal dan Promosi di bidang pelayanan perizinan/non perizinan dan fasilitas penanaman modal.'),
        image: null,
        link: null,
        era: '2010s'
    },
    {
        year: '2014',
        title: t('timeline_sejarah.items.2014.title', 'Badan Pembinaan BUMD dan Penanaman Modal (BP BUMD & PM)'),
        description: t('timeline_sejarah.items.2014.description', 'Pada tahun 2014, sebagaimana tercantum dalam Peraturan Daerah Provinsi DKI Jakarta Nomor 12 Tahun 2014 tentang Organisasi Perangkat Daerah, nama Badan Penanaman Modal dan Promosi (BPMP) diubah menjadi Badan Pembinaan BUMD dan Penanaman Modal (BP BUMD & PM). Struktur Organisasi BP BUMD & PM dikukuhkan melalui Peraturan Gubernur Nomor 225 Tahun 2014 tentang Organisasi dan Tata Kerja Badan Pembinaan Badan Usaha Milik Daerah dan Penanaman Modal.'),
        image: null,
        link: null,
        era: '2010s'
    },
    {
        year: '2016',
        title: t('timeline_sejarah.items.2016.title', 'Badan Pembinaan Badan Usaha Milik Daerah'),
        description: t('timeline_sejarah.items.2016.description', 'Pada tahun 2016, diterbitkan Peraturan Gubernur Nomor 259 tahun 2016 tentang organisasi dan tata kerja Badan Pembinaan Badan Usaha Milik Daerah. Berdasarkan Peraturan Gubernur ini BP BUMD merupakan pelaksana fungsi penunjang urusan pemerintahan bidang keuangan pada subbidang pembinaan BUMD. Fungsi penanaman modal yang sebelumnya dijalankan telah disatukan kepada SKPD Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu.'),
        image: null,
        link: null,
        era: '2010s'
    },
    {
        year: '2021',
        title: t('timeline_sejarah.items.2021.title', 'Pusat Kebijakan Strategis dan Pelayanan BUMD'),
        description: t('timeline_sejarah.items.2021.description', 'Pada tahun 2021, diterbitkan Peraturan Gubernur Nomor 62 Tahun 2021 tentang organisasi dan tata kerja Badan Pembinaan Badan Usaha Milik Daerah. Berdasarkan Peraturan Gubernur ini terdapat penambahan Unit Pelaksana Teknis (UPT) yaitu Pusat Kebijakan Strategis dan Pelayanan BUMD.'),
        image: null,
        link: null,
        era: '2010s'
    },
]);

const containerRef = ref(null);
const itemRefs = ref([]);
const progressHeight = ref(0);
const activeIndex = ref(-1);

const handleScroll = () => {
    if (!containerRef.value) return;

    const container = containerRef.value;
    const rect = container.getBoundingClientRect();
    const windowHeight = window.innerHeight;
    const centerOffset = windowHeight / 2;

    // Calculate progress for the line
    // We want the line to fill up to the center of the screen relative to the container
    const relativeY = centerOffset - rect.top;
    let percentage = (relativeY / rect.height) * 100;
    progressHeight.value = Math.max(0, Math.min(100, percentage));

    // Calculate active items
    let newActiveIndex = -1;
    itemRefs.value.forEach((el, index) => {
        if (el) {
            const itemRect = el.getBoundingClientRect();
            // If the item's top is above the center of the screen, it's active
            if (itemRect.top < centerOffset + 50) { 
                newActiveIndex = index;
            }
        }
    });
    activeIndex.value = newActiveIndex;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <section class="relative py-20 overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="relative" ref="containerRef">
                <!-- Center Line (Desktop) Background -->
                <div class="hidden md:block absolute left-1/2 transform -translate-x-1/2 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                <!-- Center Line (Desktop) Progress -->
                <div class="hidden md:block absolute left-1/2 transform -translate-x-1/2 top-0 w-0.5 bg-blue-600 transition-all duration-100 ease-out" :style="{ height: progressHeight + '%' }"></div>

                <!-- Left Line (Mobile) Background -->
                <div class="md:hidden absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                <!-- Left Line (Mobile) Progress -->
                <div class="md:hidden absolute left-6 top-0 w-0.5 bg-blue-600 transition-all duration-100 ease-out" :style="{ height: progressHeight + '%' }"></div>

                <div v-for="(item, index) in timelineData" :key="index" class="mb-12 relative" :ref="el => itemRefs[index] = el">
                    
                    <!-- Desktop View (Zigzag) -->
                    <div class="hidden md:flex items-center justify-between w-full group">
                        <!-- Left Side -->
                        <div class="w-5/12 flex justify-end pr-12">
                             <!-- If Even: Content -->
                             <div v-if="index % 2 === 0" class="w-full">
                                <div :class="[
                                    'bg-white dark:bg-gray-800 p-6 shadow-lg border-t-4 relative hover:-translate-y-1 transition-all duration-300',
                                    index <= activeIndex ? 'border-blue-500' : 'border-black dark:border-gray-600'
                                ]">
                                    <h3 :class="[
                                        'text-xl font-bold mb-2 transition-colors duration-300',
                                        index <= activeIndex ? 'text-blue-800 dark:text-blue-300' : 'text-black dark:text-white'
                                    ]">{{ item.title }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ item.description }}</p>
                                    <a v-if="item.link" :href="item.link" class="inline-block mt-4 text-blue-600 font-semibold text-sm hover:underline">{{ t('timeline_sejarah.visit', 'Visit') }} &rsaquo;</a>
                                </div>
                             </div>
                             <!-- If Odd: Year -->
                             <div v-else class="flex items-center justify-end">
                                <div :class="[
                                    'px-6 py-2 rounded-full shadow-md font-bold text-sm tracking-wide transform transition-transform group-hover:scale-105 transition-colors duration-300',
                                    index <= activeIndex ? 'bg-blue-600 text-white' : 'bg-black text-white'
                                ]">
                                    {{ item.year }}
                                </div>
                             </div>
                        </div>

                        <!-- Center Dot -->
                        <div class="absolute left-1/2 transform -translate-x-1/2 flex items-center justify-center z-20">
                             <div :class="[
                                 'w-4 h-4 rounded-full border-4 shadow-md group-hover:scale-125 transition-all duration-300',
                                 index <= activeIndex ? 'bg-blue-500 border-blue-500' : 'bg-black border-white dark:border-gray-900'
                             ]"></div>
                        </div>

                        <!-- Right Side -->
                        <div class="w-5/12 flex justify-start pl-12">
                             <!-- If Even: Year -->
                             <div v-if="index % 2 === 0" class="flex items-center justify-start">
                                <div :class="[
                                    'px-6 py-2 rounded-full shadow-md font-bold text-sm tracking-wide transform transition-transform group-hover:scale-105 transition-colors duration-300',
                                    index <= activeIndex ? 'bg-blue-600 text-white' : 'bg-black text-white'
                                ]">
                                    {{ item.year }}
                                </div>
                             </div>
                             <!-- If Odd: Content -->
                             <div v-else class="w-full">
                                <div :class="[
                                    'bg-white dark:bg-gray-800 p-6 shadow-lg border-t-4 relative hover:-translate-y-1 transition-all duration-300',
                                    index <= activeIndex ? 'border-blue-500' : 'border-black dark:border-gray-600'
                                ]">
                                    <h3 :class="[
                                        'text-xl font-bold mb-2 transition-colors duration-300',
                                        index <= activeIndex ? 'text-blue-800 dark:text-blue-300' : 'text-black dark:text-white'
                                    ]">{{ item.title }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ item.description }}</p>
                                    <a v-if="item.link" :href="item.link" class="inline-block mt-4 text-blue-600 font-semibold text-sm hover:underline">{{ t('timeline_sejarah.visit', 'Visit') }} &rsaquo;</a>
                                </div>
                             </div>
                        </div>
                    </div>

                    <!-- Mobile View (Linear) -->
                    <div class="md:hidden flex w-full group">
                        <!-- Dot Column -->
                        <div class="w-12 flex-shrink-0 flex justify-center relative">
                             <div :class="[
                                 'w-4 h-4 rounded-full border-4 shadow-md z-20 mt-6 group-hover:scale-125 transition-all duration-300',
                                 index <= activeIndex ? 'bg-blue-500 border-blue-500' : 'bg-black border-white dark:border-gray-900'
                             ]"></div>
                        </div>
                        <!-- Content Column -->
                        <div class="flex-grow pl-4 pb-8">
                             <div class="mb-2">
                                 <span :class="[
                                     'text-xs font-bold px-3 py-1 rounded-full shadow-sm transition-colors duration-300',
                                     index <= activeIndex ? 'bg-blue-600 text-white' : 'bg-black text-white'
                                 ]">
                                    {{ item.year }}
                                 </span>
                             </div>
                             <div :class="[
                                 'bg-white dark:bg-gray-800 p-6 shadow-lg border-t-4 relative hover:-translate-y-1 transition-all duration-300',
                                 index <= activeIndex ? 'border-blue-500' : 'border-black dark:border-gray-600'
                             ]">
                                <h3 :class="[
                                    'text-xl font-bold mb-2 transition-colors duration-300',
                                    index <= activeIndex ? 'text-blue-800 dark:text-blue-300' : 'text-black dark:text-white'
                                ]">{{ item.title }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ item.description }}</p>
                                <a v-if="item.link" :href="item.link" class="inline-block mt-4 text-blue-600 font-semibold text-sm hover:underline">Visit &rsaquo;</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Add any custom animations or overrides here if needed */
</style>
