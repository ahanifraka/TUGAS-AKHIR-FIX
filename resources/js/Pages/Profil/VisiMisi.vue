<script setup>
import { Head } from '@inertiajs/vue3';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';
import useTranslations from '@/Composables/useTranslations.js';

const { t } = useTranslations();

const breadcrumbItems = [
    { label: t('pages.visi_misi.breadcrumb.profile', 'Profil'), url: '#' },
    { label: t('pages.visi_misi.breadcrumb.page', 'Visi & Misi'), url: '/visi-misi' },
];

const heroTitle = t('pages.visi_misi.hero.title', 'Visi & Misi Pemerintah Provinsi DKI Jakarta');
const heroDescription = t('pages.visi_misi.hero.description', 'Informasi Visi & Misi Pemerintah Provinsi DKI Jakarta');
</script>

<style scoped>
    .ol {
        list-style: decimal;
    }
</style>

<template>
    <GuestLayout>

        <Head :title="t('pages.visi_misi.head.title', 'Visi dan Misi')">
            <meta name="description" :content="t('pages.visi_misi.head.description', 'Visi & Misi Pemerintah Provinsi DKI Jakarta')">
            <meta name="keywords" :content="t('pages.visi_misi.head.keywords', 'Visi & Misi, Pemerintah Provinsi DKI Jakarta')">
        </Head>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="container mx-auto px-12 md:px-4 py-12 max-w-3xl">
            <div class="mb-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ t('pages.visi_misi.headings.vision', 'Visi Pemerintah Provinsi DKI Jakarta') }}</h2>
                <p class="text-lg text-gray-700 dark:text-white mb-8 text-justify">{{ t('pages.visi_misi.vision_paragraph', 'Jakarta kota maju, lestari dan berbudaya yang warganya terlibat dalam mewujudkan keberadaban, keadilan dan kesejahteraan bagi semua.') }}</p>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ t('pages.visi_misi.headings.mission', 'Misi Pemerintah Provinsi DKI Jakarta') }}</h2>
                <ol class="list-decimal list-outside pl-6 text-gray-800 dark:text-white text-base md:text-lg leading-relaxed mb-6">
                    <li class="mb-4 text-justify">{{ t('pages.visi_misi.mission_1', 'Menjadikan Jakarta kota yang aman, sehat, cerdas, berbudaya, dengan memperkuat nilai-nilai keluarga dan memberikan ruang kreativitas melalui kepemimpinan yang melibatkan, menggerakkan dan memanusiakan.') }}</li>
                    <li class="mb-4 text-justify">{{ t('pages.visi_misi.mission_2', 'Menjadikan Jakarta kota yang memajukan kesejahteraan umum melalui terciptanya lapangan kerja, kestabilan dan keterjangkauan kebutuhan pokok, meningkatnya keadilan sosial, percepatan pembangunan infrastruktur, kemudahan investasi dan berbisnis, serta perbaikan pengelolaan tata ruang.') }}</li>
                    <li class="mb-4 text-justify">{{ t('pages.visi_misi.mission_3', 'Menjadikan Jakarta tempat wahana aparatur negara yang berkarya, mengabdi, melayani, serta menyelesaikan berbagai permasalahan kota dan warga, secara efektif, meritokratis dan berintegritas.') }}</li>
                    <li class="mb-4 text-justify">{{ t('pages.visi_misi.mission_4', 'Menjadikan Jakarta kota yang lestari, dengan pembangunan dan tata kehidupan yang memperkuat daya dukung lingkungan dan sosial.') }}</li>
                    <li class="mb-4 text-justify">{{ t('pages.visi_misi.mission_5', 'Menjadikan Jakarta Ibu kota yang dinamis sebagai simpul kemajuan Indonesia yang bercirikan keadilan, kebangsaan dan kebhinekaan.') }}</li>
                </ol>
            </div>

        </main>
    </GuestLayout>

</template>