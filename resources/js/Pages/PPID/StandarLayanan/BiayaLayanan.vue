<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue'; // Ganti dengan nama layout Anda
import Hero from '@/Components/Hero.vue';

// Data untuk Hero Section
const breadcrumbItems = [
    { label: 'Layanan Informasi Publik', url: '#' },
    { label: 'Biaya Layanan', url: '/biaya-layanan' }

];
const heroTitle = 'Biaya Layanan';
const heroDescription = 'Informasi dan Ketentuan Biaya Layanan';

</script>

<template>

    <Head title="Biaya Layanan" />

    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />

        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
                <section class="bg-white p-6 sm:p-8 md:p-10 rounded-lg shadow-md max-w-4xl mx-auto dark:bg-gray-800">
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 dark:text-white">
                            Ketentuan Biaya
                        </h2>
                        <ol
                            class="text-base md:text-lg text-gray-700 leading-relaxed space-y-3 dark:text-gray-300">
                            <li class="text-justify">Pelayangan Informasi Publik tidak dipungut biaya. Namun biaya pengadaan atau perekaman yang timbul ditanggung oleh Pemohon Informasi Publik, kecuali untuk informasi yang telah ditentukan biaya Penerimaan Negara Bukan Pajak (PNBP).</li>
                        </ol>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 dark:text-white">
                            Waktu Pelayanan Informasi Publik
                        </h2>
                        <ol
                            class="list-disc list-outside pl-8 text-base md:text-lg text-gray-700 leading-relaxed space-y-3 dark:text-gray-300">
                            <li>Senin s/d Kamis : 09.00 -15.00 WIB (Istirahat 12.00-13.00 WIB)</li>
                            <li>Jumat : 09.00 -15.00 WIB (Istirahat 11.00-13.00 WIB)</li>
                        </ol>
                    </div>
                </section>
            </div>
        </main>
    </GuestLayout>
</template>

