<template>
    <Head title="Struktur Organisasi">
        <meta name="description" content="Struktur Organisasi Pemerintah BPBUMD DKI Jakarta">
        <meta name="keywords" content="Struktur Organisasi, Pemerintah BPBUMD DKI Jakarta">
    </Head>

    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="container content-container py-12 mx-auto">
            <img 
                src="/images/struktur-organisasi.png" 
                alt="Struktur Organisasi BPBUMD DKI Jakarta" 
                class="w-full max-w-6xl h-auto mx-auto cursor-pointer hover:opacity-90 transition-opacity duration-200"
                @click="openZoomModal"
            >
        </main>
    </GuestLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const breadcrumbItems = [
    { label: 'Profil', url: '#' },
    { label: 'Struktur Organisasi', url: '/struktur-organisasi' },
];

const heroTitle = 'Struktur Organisasi';
const heroDescription = 'Informasi mengenai Struktur Organisasi BPBUMD DKI';

const openZoomModal = () => {
    Swal.fire({
        imageUrl: '/images/struktur-organisasi.png',
        imageAlt: 'Struktur Organisasi BPBUMD DKI Jakarta',
        showCloseButton: true,
        showConfirmButton: false,
        width: '90%',
        padding: '1rem',
        background: '#fff',
        backdrop: `
            rgba(0,0,0,0.8)
        `,
        customClass: {
            image: 'swal-image-zoom',
            popup: 'swal-popup-zoom'
        }
    });
};
</script>

<style>
.swal-image-zoom {
    max-width: 100% !important;
    max-height: 80vh !important;
    object-fit: contain !important;
}

.swal-popup-zoom {
    border-radius: 8px !important;
}
</style>