<template>
    <Head title="Maklumat Layanan" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="py-4 md:py-8 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
                <section class="p-6 sm:p-8 md:p-10 rounded-lg max-w-7xl mx-auto">
                    <div>
                        <a href="/images/ppid/maklumat-pelayanan-informasi-publik.jpeg" target="_blank" rel="noopener noreferrer" @click.prevent="openZoomModal">
                            <img src="/images/ppid/maklumat-pelayanan-informasi-publik.jpeg" alt="Maklumat Layanan" title="Maklumat Layanan"
                            class="w-full h-auto rounded-lg shadow-sm hover:opacity-90 transition-opacity duration-200 cursor-zoom-in">
                        </a>
                    </div>
                </section>
            </div>
        </main>
    </GuestLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

// Data untuk Hero Section
const breadcrumbItems = [
    { label: 'Layanan Informasi Publik', url: '#' },
    { label: 'Maklumat Layanan', url: '/maklumat-layanan' }

];
const heroTitle = 'Maklumat Layanan';
const heroDescription = '-';

const openZoomModal = () => {
    Swal.fire({
        imageUrl: '/images/ppid/maklumat-pelayanan-informasi-publik.jpeg',
        imageAlt: 'Maklumat Layanan',
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