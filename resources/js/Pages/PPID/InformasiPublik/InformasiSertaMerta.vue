<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue'; // Ganti dengan nama layout Anda
import Hero from '@/Components/Hero.vue';

// Data untuk Hero Section
const breadcrumbItems = [
    { label: 'Layanan Informasi Publik', url: '#' },
    { label: 'Informasi Yang Wajib Diumumkan Secara Serta-merta', url: '/informasi-3' }

];
const heroTitle = 'Informasi Yang Wajib Diumumkan Secara Serta-merta';
const heroDescription = '-';

</script>

<template>
    <Head title="Informasi Yang Wajib Tersedia Setiap Saat" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
            </div>
        </main>
    </GuestLayout>
</template>

