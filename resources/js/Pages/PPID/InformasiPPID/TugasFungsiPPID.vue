<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue'; // Ganti dengan nama layout Anda
import Hero from '@/Components/Hero.vue';

// Data untuk Hero Section
const breadcrumbItems = [
    { label: 'PPID', url: '#' },
    { label: 'Tugas Fungsi PPID', url: '/tugasfungsi-ppid' }

];
const heroTitle = 'Tugas Fungsi PPID'; // Dibuat reaktif
const heroDescription = 'Layanan Informasi Publik Pejabat Pengelola Informasi dan Dokumentasi';

</script>

<template>

    <Head title="Tugas Fungsi PPID" />

    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />

        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
                <section class="p-6 sm:p-8 md:p-10 rounded-lg max-w-4xl mx-auto">
                    <div class="text-center mb-10">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Keputusan Kepala Badan Pembinaan Badan Usaha Milik Daerah Provinsi Daerah Khusus Ibukota
                            Jakarta Nomor e-0006 Tahun 2024
                        </h4>
                        <p class="text-base text-gray-600 mt-1 dark:text-gray-200">
                            Tentang Pejabat Pengelola Informasi dan Dokumentasi Tahun 2024
                        </p>
                    </div>

                    <div class="mb-12">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 dark:text-white">
                            PPID Bertugas:
                        </h3>
                        <ol
                            class="list-decimal list-outside pl-10 text-gray-800 text-base md:text-lg leading-relaxed space-y-3 dark:text-gray-200">
                            <li class="pl-3 text-justify">Menyusun dan melaksanakan kebijakan layanan Informasi Publik;</li>
                            <li class="pl-3 text-justify">Menyusun laporan pelaksanaan kebijakan layanan Informasi Publik;</li>
                            <li class="pl-3 text-justify">Mengoordinasikan dan mengonsolidasikan proses penyimpanan, pendokumentasian, penyediaan,
                                dan pelayanan Informasi Publik;</li>
                            <li class="pl-3 text-justify">Mengoordinasikan dan mengonsolidasikan pengumpulan dokumen Informasi Publik dari PPID
                                Pelaksana dan/atau Petugas Pelayanan Informasi di Badan Publik;</li>
                            <li class="pl-3 text-justify">Melakukan verifikasi dokumen Informasi Publik;</li>
                            <li class="pl-3 text-justify">Menentukan Informasi Publik yang dapat diakses publik dan layak untuk dipublikasikan;
                            </li>
                            <li class="pl-3 text-justify">Melakukan pengujian tentang konsekuens atas Informasi Publik yang akan dikecualikan;
                            </li>
                            <li class="pl-3 text-justify">Melakukan pengelolaan, pemeliharaan, dan pemutakhiran Daftar Informasi Publik;</li>
                            <li class="pl-3 text-justify">Menyediakan Informasi Publik secara efektif dan efisien agar mudah diakses oleh publik;
                                dan</li>
                            <li class="pl-3 text-justify">Melakukan pembinaan, pengawasan, evaluasi, dan monitoring atas pelaksanaan kebijakan
                                teknis Informasi Publik yang dilakukan oleh PPID Pelaksana dan/atau Petugas Pelayanan
                                Informasi.</li>
                        </ol>
                    </div>

                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 dark:text-white">
                            PPID Berwenang:
                        </h3>
                        <ol
                             class="list-decimal list-outside pl-10 text-gray-800 text-base md:text-lg leading-relaxed space-y-3 dark:text-gray-200">
                            <li class="pl-3 text-justify">Menetapkan kebijakan layanan Informasi Publik;</li>
                            <li class="pl-3 text-justify">Menetapkan laporan pelaksanaan kebijakan layanan Informasi Publik;</li>
                            <li class="pl-3 text-justify">Melaksanakan rapat koordinasi dan rapat kerja secara berkala dan/atau sesuai dengan
                                kebutuhan dalam melaksanakan pelayanan Informasi Publik;</li>
                            <li class="pl-3 text-justify">Meminta klarifikasi kepada PPID Pelaksana dan/atau Petugas Pelayanan Informasi dalam
                                melaksanakan pelayanan Informasi Publik;</li>
                            <li class="pl-3 text-justify">Menetapkan dan memutuskan suatu Informasi Publik dapat diakses publik atau tidak
                                berdasarkan pengujian tentang konsekuensi atas Informasi Publik yang akan dikecualikan,
                                dengan persetujuan Atasan PPID;</li>
                            <li class="pl-3 text-justify">Menolak Permintaan Informasi Publik dengan menyampaikan pertimbangan secara tertulis
                                apabila Informasi Publik yang dimohon termasuk informasi yang dikecualikan atau rahasia,
                                dengan persetujuan Atasan PPID;</li>
                            <li class="pl-3 text-justify">Menugaskan PPID Pelaksana dan/atau Petugas Pelayanan Informasi untuk membuat, mengelola,
                                memelihara, dan/atau memutakhirkan Daftar Informasi Publik; dan</li>
                            <li class="pl-3 text-justify">Menetapkan strategi dan metode pembinaan, pengawasan, evaluasi, dan monitoring atas
                                pelaksanaan kebijakan teknis Informasi Publik yang dilakukan oleh PPID Pelaksana
                                dan/atau Petugas Pelayanan Informasi.</li>
                        </ol>
                    </div>
                </section>
            </div>
        </main>
    </GuestLayout>
</template>
