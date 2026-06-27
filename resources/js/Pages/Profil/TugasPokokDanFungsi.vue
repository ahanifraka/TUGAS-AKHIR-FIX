<script setup>
import { Head } from '@inertiajs/vue3';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';
import useTranslations from '@/Composables/useTranslations.js';

const { t } = useTranslations();

const breadcrumbItems = [
    { label: t('pages.tupoksi.breadcrumb.profile', 'Profil'), url: '#' },
    { label: t('pages.tupoksi.breadcrumb.page', 'Tugas Pokok dan Fungsi'), url: '/tupoksi' },
];

const heroTitle = t('pages.tupoksi.hero.title', 'Tugas Pokok dan Fungsi');
const heroDescription = t('pages.tupoksi.hero.description', 'Informasi mengenai tugas dan fungsi dari BPBUMD DKI');

</script>

<template>

    <Head :title="t('pages.tupoksi.head.title', 'Tugas Pokok dan Fungsi')">
        <meta name="description" :content="t('pages.tupoksi.head.description', 'Tugas Pokok dan Fungsi Pemerintah BPBUMD DKI Jakarta')">
        <meta name="keywords" :content="t('pages.tupoksi.head.keywords', 'Tugas Pokok dan Fungsi, Pemerintah BPBUMD DKI Jakarta')">
    </Head>

    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="container py-12 mx-auto px-12 md:px-4">
            <div>
                <div class="mb-8 max-w-2xl mx-auto">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-4">{{ t('pages.tupoksi.headings.duties', 'TUGAS') }}</h2>
                    <p class="text-lg text-justify">{{ t('pages.tupoksi.duties_description', 'BP BUMD mempunyai tugas menyelenggarakan fungsi penunjang urusan pemerintahan di bidang keuangan pada sub bidang pembinaan BUMD.') }}</p>

                    <hr class="mt-8">
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-4">{{ t('pages.tupoksi.headings.functions', 'FUNGSI') }}</h2>
                    <p class="text-lg text-justify max-w-2xl mx-auto mb-12">{{ t('pages.tupoksi.functions_description', 'BP BUMD mempunyai tugas menyelenggarakan fungsi penunjang urusan pemerintahan di bidang keuangan pada sub bidang pembinaan BUMD.') }}</p>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-4 lg:gap-12 items-start max-w-5xl mx-auto">
                        <ul
                            class="list-disc list-outside pl-6 marker:text-blue-600 marker:font-semibold text-gray-800 dark:text-gray-300 text-base md:text-lg leading-relaxed">
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.1', 'Penyusunan rencana strategis dan rencana kerja dan anggaran BP BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.2', 'Pelaksanaan rencana strategis dan dokumen pelaksanaan anggaran BP BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.3', 'Perumusan dan penetapan kebijakan di bidang penguatan inovasi bisnis BUMD, penguatan daya saing, percepatan penguatan kinerja, penciptaan pertumbuhan berkelanjutan, reduksi emisi, dan lain-lain yang relevan, serta peningkatan kapasitas infrastruktur bisnis BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.4', 'Koordinasi dan sinkronisasi pelaksanaan kebijakan di bidang penguatan inovasi bisnis BUMD, penguatan daya saing, percepatan penguatan kinerja, penciptaan pertumbuhan berkelanjutan, reduksi emisi, dan lain-lain yang relevan, serta peningkatan kapasitas infrastruktur bisnis BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.5', 'Pengkajian dan pengajuan pembentukan BUMD baru.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.6', 'Pelaksanaan monitoring dan evaluasi pengelolaan BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.7', 'Pelaksanaan monitoring dan evaluasi kinerja pengurus BUMD dan Perseroan serta kinerja bisnis BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.8', 'Pelaksanaan monitoring dan evaluasi proyek penugasan strategis Gubernur pada BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.9', 'Pelaksanaan pengangkatan dan pemberhentian Direksi, Badan Pengawas, dan Komisaris pada BUMD dan Perseroan Wakil Pemerintah Daerah.') }}</li>
                        </ul>

                        <ul
                            class="fungsi-list list-disc list-outside pl-6 marker:text-blue-600 marker:font-semibold text-gray-800 dark:text-gray-300 text-base md:text-lg leading-relaxed">
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.10', 'Penyelenggaraan seleksi dan pengajuan calon pengurus BUMD dan Perseroan Wakil Pemerintah Daerah.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.11', 'Pengkajian dan pengajuan rencana akuisisi, merger, spinoff, privatisasi, divestasi, perubahan bentuk badan hukum, dan lain-lain BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.12', 'Pengkajian dan proses pengesahan penghasilan/remunerasi pengurus BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.13', 'Pengkajian dan proses pengesahan rencana jangka panjang (RJP), rencana kerja dan anggaran perusahaan (RKAP), dan lain-lain rencana strategis BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.14', 'Pengajuan rencana investasi langsung penyertaan modal daerah pada BUMD dan Perseroan berkoordinasi dengan SKPD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.15', 'Pengelolaan investasi daerah yang bersifat langsung berupa penyertaan modal daerah.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.16', 'Pelaksanaan evaluasi nilai ekonomi dan kontribusi keuangan BUMD dan Perseroan terhadap Pemerintah Daerah.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.17', 'Pengelolaan kepegawaian, keuangan, dan barang BP BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.18', 'Pengelolaan ketatausahaan dan kerumahtanggaan BP BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.19', 'Pengelolaan kearsipan, data, dan informasi BP BUMD.') }}</li>
                            <li class="mb-3 text-justify">{{ t('pages.tupoksi.functions_list.20', 'Pelaporan dan pertanggungjawaban pelalcsanaan tugas dan fungsi BP BUMD.') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </GuestLayout>
</template>
