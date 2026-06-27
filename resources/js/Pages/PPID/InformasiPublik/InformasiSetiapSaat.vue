
<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue'; // Ganti dengan nama layout Anda
import Hero from '@/Components/Hero.vue';

// Data untuk Hero Section
const breadcrumbItems = [
    { label: 'Layanan Informasi Publik', url: '#' },
    { label: 'Informasi yang Wajib Disediakan dan Diumumkan Secara Berkala', url: '/informasi-2' }

];
const heroTitle = 'Informasi Yang Wajib Tersedia Setiap Saat';
const heroDescription = 'Informasi Yang Wajib Tersedia Setiap Saat';

</script>

<template>

    <Head title="Informasi Yang Wajib Tersedia Setiap Saat" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />

        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
                <section class="p-6 sm:p-8 md:p-10 rounded-lg max-w-4xl mx-auto">
                    <div class="text-center">
                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 dark:text-white">
                            Informasi Yang Wajib Tersedia Setiap Saat
                        </h2>
                        <div class="mt-6 mb-12 w-24 h-0.5 bg-primary mx-auto"></div>
                    </div>
                    <ul class="list-disc list-inside text-base md:text-lg text-gray-700 leading-relaxed space-y-3 dark:text-white">
                        <li>Data Umum Kepegawaian BPBUMD</li>
                        <li>Data Aset Tetap KIB B BPBUMD</li>
                        <li>Data Aset Tidak Berwujud BPBUMD</li>
                        <li>Data Aset Rusak Berat BPBUMD</li>
                        <li>Rencana Kerja BPBUMD</li>
                        <li>Surat Perizinan dengan Pihak Ketiga</li>
                        <li>Surat-menyurat dalam Rangka Pelaksanaan Tugas Pokok dan Fungsi</li>
                        <li>Data Realisasi Pencairan Penyertaan Modal Daerah pada BUMD dan Perusahaan Patungan</li>
                        <li>Dokumen Proses Pengadaan Barang dan Jasa</li>
                        <li>Data Realisasi Penyetoran Dividen dari BUMD dan Perusahaan Patungan</li>
                    </ul>
                </section>
            </div>
        </main>
    </GuestLayout>
</template>
