<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const breadcrumbItems = [
    { label: 'PPID', url: '#' },
    { label: 'Struktur Organisasi PPID', url: '/struktur-ppid' }

];
const heroTitle = 'Struktur Organisasi PPID';
const heroDescription = 'Layanan Informasi Publik Pejabat Pengelola Informasi dan Dokumentasi';

</script>

<template>

    <Head title="Struktur Organisasi PPID" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />

        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-8 sm:px-4">
                <section class="p-2 sm:p-3 md:p-10 rounded-lg max-w-4xl mx-auto">
                    <div class="text-center">
                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 dark:text-white">
                            Struktur Organisasi PPID
                        </h2>
                        <div class="mt-4 mb-8 w-24 h-0.5 bg-primary mx-auto"></div>
                    </div>

                    <div class="text-left text-base md:text-lg text-gray-700 leading-relaxed dark:text-gray-200">

                        <p class="font-semibold mb-4">SUSUNAN ORGANISASI:</p>

                        <ol class="list-decimal list-inside space-y-4">

                            <li>
                                <strong>Atasan Pejabat Pengelola Informasi dan Dokumentasi:</strong>
                                <ul class="list-disc list-inside ml-5 mt-2">
                                    <li class="text-justify">Kepala Badan</li>
                                </ul>
                            </li>

                            <li class="text-justify">
                                <strong>Pejabat Pengelola Informasi dan Dokumentasi (PPID):</strong>
                                <ul class="list-disc list-inside ml-5 mt-2">
                                    <li class="text-justify">Sekretaris</li>
                                </ul>
                            </li>

                            <li class="text-justify">
                                <strong>Pejabat Pengelola Informasi dan Dokumentasi (PPID) Pelaksana:</strong>
                                <ol class="list-[lower-alpha] list-inside ml-5 mt-2 space-y-3">
                                    <li class="text-justify">
                                        PPID Pelaksana Sekretariat:
                                        <ul class="list-disc list-inside ml-5 mt-2">
                                            <li class="text-justify">Sekretaris</li>
                                        </ul>
                                    </li>
                                    <li class="text-justify">
                                        PPID Pelaksana Bidang Usaha Transportasi, Properti dan Keuangan:
                                        <ul class="list-disc list-inside ml-5 mt-2">
                                            <li class="text-justify">Kepala Bidang Usaha Transportasi, Properti dan Keuangan</li>
                                        </ul>
                                    </li>
                                    <li class="text-justify">
                                        PPID Pelaksana Bidang Usaha Pangan, Utilitas, Perpasaran dan Industri:
                                        <ul class="list-disc list-inside ml-5 mt-2">
                                            <li class="text-justify">Kepala Bidang Usaha Pangan, Utilitas, Perpasaran dan Industri</li>
                                        </ul>
                                    </li>
                                    <li class="text-justify">
                                        PPID Pelaksana Bidang Usaha Infrastruktur, Pariwisata dan Kawasan:
                                        <ul class="list-disc list-inside ml-5 mt-2">
                                            <li class="text-justify">Kepala Bidang Usaha Infrastruktur, Pariwisata dan Kawasan</li>
                                        </ul>
                                    </li>
                                    <li class="text-justify">
                                        PPID Pelaksana Pusat Kebijakan Strategis dan Pelayanan BUMD:
                                        <ul class="list-disc list-inside ml-5 mt-2">
                                            <li class="text-justify">Kepala Pusat Kebijakan Strategis dan Pelayanan BUMD</li>
                                        </ul>
                                    </li>
                                </ol>
                            </li>

                            <li class="text-justify">
                                <strong>Tim Pertimbangan:</strong>
                                <div class="ml-5 mt-2 space-y-2">
                                    <p><strong class="font-medium">Ketua:</strong> Sekretaris</p>
                                    <p><strong class="font-medium">Sekretaris:</strong> Subkoordinator Urusan/Ketua
                                        Subkelompok Data dan Informasi</p>
                                    <div>
                                        <p><strong class="font-medium">Anggota:</strong></p>
                                        <ol class="list-[lower-alpha] list-inside ml-5 mt-2 space-y-2">
                                            <li class="text-justify">Kepala Bidang Usaha Transportasi, Properti dan Keuangan</li>
                                            <li class="text-justify">Kepala Bidang Usaha Pangan, Utilitas, Perpasaran dan Industri</li>
                                            <li class="text-justify">Kepala Bidang Usaha Infrastruktur, Pariwisata dan Kawasan</li>
                                            <li class="text-justify">Kepala Pusat Kebijakan Strategis dan Pelayanan BUMD</li>
                                        </ol>
                                    </div>
                                </div>
                            </li>

                            <li class="text-justify">
                                <strong>Petugas Pelayanan Informasi Publik:</strong>
                                <ul class="list-disc list-inside ml-5 mt-2">
                                    <li class="text-justify">Para pegawai pada Urusan/Subkelompok Data dan Informasi</li>
                                </ul>
                            </li>

                        </ol>

                    </div>
                </section>
            </div>
        </main>
    </GuestLayout>
</template>
