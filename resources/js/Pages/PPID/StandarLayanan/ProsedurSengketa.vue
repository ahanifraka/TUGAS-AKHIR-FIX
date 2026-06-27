<script setup>
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import GuestLayout from '@/Layouts/GuestLayout.vue'; // Ganti dengan nama layout Anda
import Hero from '@/Components/Hero.vue';

// Data untuk Hero Section
const breadcrumbItems = [
    { label: 'Layanan Informasi Publik', url: '#' },
    { label: 'Prosedur Permohonan Penyelesaian Sengketa Informasi', url: '/prosedur-permohonan-penyelesaian-sengketa-informasi' }

];
const heroTitle = 'Prosedur Permohonan Penyelesaian Sengketa Informasi';
const heroDescription = 'Informasi mengenai Tata Cara Pengajuan Permohonan BPBUMD DKI Jakarta';

const openZoomModal = () => {
    Swal.fire({
        imageUrl: '/images/ppid/mekanisme-permohonan-penyelesaian-sengketa-informasi.jpg',
        imageAlt: 'Prosedur Sengketa Informasi',
        showCloseButton: true,
        showConfirmButton: false,
        width: '90%',
        padding: '1rem',
        background: '#fff',
        backdrop: `rgba(0,0,0,0.8)`,
        customClass: {
            image: 'swal-image-zoom',
            popup: 'swal-popup-zoom'
        }
    });
};

</script>

<template>
    <Head title="Prosedur Permohonan Penyelesaian Sengketa Informasi" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
                <section class="bg-white p-6 sm:p-8 md:p-10 rounded-lg shadow-md max-w-4xl mx-auto dark:bg-gray-800">
                    <div>
                        <a href="/images/ppid/mekanisme-permohonan-penyelesaian-sengketa-informasi.jpg" target="_blank" rel="noopener noreferrer" @click.prevent="openZoomModal">
                            <img src="/images/ppid/mekanisme-permohonan-penyelesaian-sengketa-informasi.jpg" alt="Prosedur Sengketa Informasi" title="Prosedur Sengketa Informasi"
                            class="w-full h-auto rounded-lg shadow-sm hover:opacity-90 transition-opacity duration-200 cursor-zoom-in">
                        </a>
                    </div>
                </section>
            </div>
        </main>
    </GuestLayout>
</template>

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

