<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

// Data untuk Hero Section
const breadcrumbItems = [
    { label: 'PPID', url: '#' },
    { label: 'Dasar Hukum PPID', url: '/dasarhukum-ppid' }

];
const heroTitle = 'Dasar Hukum PPID';
const heroDescription = 'Layanan Informasi Publik Pejabat Pengelola Informasi dan Dokumentasi';

</script>

<template>

    <Head title="Dasar Hukum PPID" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />

        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
                <section class="p-6 sm:p-8 md:p-10 rounded-lg max-w-4xl mx-auto">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-6 dark:text-white">
                        Dasar Hukum PPID
                    </h2>

                    <ol
                        class="list-decimal list-outside text-base md:text-lg text-gray-700 leading-relaxed space-y-5 ml-5 dark:text-white">

                        <li>
                            Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik
                            <a href="https://kependudukancapil.jakarta.go.id/wp-content/uploads/2022/10/uu-no-14-thn-2008-tentang-informasi-publik.pdf" 
                                target="_blank" rel="noopener noreferrer"
                                class="inline-block bg-blue-100 text-blue-700 font-medium py-1 px-3 rounded-full text-sm hover:bg-blue-200 transition-colors duration-200 ml-2">
                                Download
                            </a>
                        </li>

                        <li>
                            Undang-Undang Nomor 25 Tahun 2009 tentang Pelayanan Publik
                            <a href="https://kependudukancapil.jakarta.go.id/wp-content/uploads/2022/10/Undang-Undang-Nomor-25-Tahun-2009.pdf" 
                                target="_blank" rel="noopener noreferrer"
                                class="inline-block bg-blue-100 text-blue-700 font-medium py-1 px-3 rounded-full text-sm hover:bg-blue-200 transition-colors duration-200 ml-2">
                                Download
                            </a>
                        </li>

                        <li>
                            Peraturan Komisi Informasi Pusat Republik Indonesia Nomor 1 tahun 2021 tentang Standar
                            Layanan Informasi Publik
                            <a href="https://kip.jakarta.go.id/wp-content/uploads/fluentform/ff-ee3d9573c8d0c3a87b3d4c0ee687773d-ff-Peraturan_Komisi_Informasi_No_1_Tahun_2021.pdf" 
                                target="_blank" rel="noopener noreferrer"
                                class="inline-block bg-blue-100 text-blue-700 font-medium py-1 px-3 rounded-full text-sm hover:bg-blue-200 transition-colors duration-200 ml-2">
                                Download
                            </a>
                        </li>

                        <li>
                            Peraturan Gubernur Nomor 175 Tahun 2016 tentang Layanan Informasi Publik
                            <a href="https://kip.jakarta.go.id/wp-content/uploads/fluentform/ff-ff4942834ef4e9ed0bd1d8d520f23db9-ff-Peraturan_Gubernur_No_175_Tahun_2016.pdf" 
                                target="_blank" rel="noopener noreferrer"
                                class="inline-block bg-blue-100 text-blue-700 font-medium py-1 px-3 rounded-full text-sm hover:bg-blue-200 transition-colors duration-200 ml-2">
                                Download
                            </a>
                        </li>

                        <li>
                            Keputusan Sekretaris Daerah Provinsi DKI Jakarta Nomor 86 Tahun 2025 tentang Standar
                            Operasional Prosedur Pengelolaan dan Pelayanan Informasi melalui Pejabat Pengelola Informasi
                            dan Dokumentasi Utama dan Pelaksana
                            <a href="https://drive.google.com/file/d/1xJ_CI8PwCGSH-N9CyPVgI2_vVn4M06YK/view?usp=drive_link" 
                                target="_blank" rel="noopener noreferrer"
                                class="inline-block bg-blue-100 text-blue-700 font-medium py-1 px-3 rounded-full text-sm hover:bg-blue-200 transition-colors duration-200 ml-2">
                                Download
                            </a>
                        </li>
                    </ol>
                </section>
            </div>
        </main>
    </GuestLayout>
</template>

