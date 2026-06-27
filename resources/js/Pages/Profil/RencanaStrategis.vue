<template>
        <Head title="Rencana Strategis">
        <meta name="description" content="Rencana Strategis Pemerintah BPBUMD DKI Jakarta">
        <meta name="keywords" content="Rencana Strategis, Pemerintah BPBUMD DKI Jakarta">
    </Head>

    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="py-12 mx-auto">
            <embed style="width: 90vw; height: 100vh" :src="pdfUrl" type="application/pdf" class="mx-auto" />
        </main>
    </GuestLayout>
</template>

<script setup>  
import { Head } from '@inertiajs/vue3';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const breadcrumbItems = [
    { label: 'Profil', url: '#' },
    { label: 'Rencana Strategis', url: '/rencana-strategis' },
];

const heroTitle = 'Rencana Strategis';
const heroDescription = 'Informasi mengenai Rencana Strategis BPBUMD DKI';


// Use the public asset path so Vite/Laravel serves the PDF correctly
const pdfUrl = '/dok/RenstraBPBUMDTahun2023-2026.pdf';
</script>