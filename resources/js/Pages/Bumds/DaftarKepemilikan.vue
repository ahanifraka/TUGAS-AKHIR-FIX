<template>
    <Head title="Daftar Kepemilikan BUMD Dan Perusahaan Patungan">
        <meta name="description" content="Daftar Kepemilikan BUMD Dan Perusahaan Patungan Pemerintah BPBUMD DKI Jakarta">
        <meta name="keywords" content="Daftar Kepemilikan, BUMD, Perusahaan Patungan, Pemerintah BPBUMD DKI Jakarta">
    </Head>

    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="container mx-auto py-24 px-4 md:px-8">
            <div class="flex flex-col gap-8">
                <div class="mb-12">
                    <h1 class="text-3xl text-center">DAFTAR BADAN USAHA MILIK DAERAH PROVINSI DKI JAKARTA</h1>
                    <div class="mt-8 overflow-x-auto max-w-5xl mx-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
                            <thead>
                                <tr class="bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-white">
                                    <th class="px-4 py-2 border">No</th>
                                    <th class="px-4 py-2 border">BUMD</th>
                                    <th class="px-4 py-2 border">Kepemilikan Pemprov DKI Jakarta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in bumdList" :key="index" class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 border text-center">{{ index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ item.bumd }}</td>
                                    <td class="px-4 py-2 border text-center">{{ item.kepemilikan }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl text-center">DAFTAR PERUSAHAAN PATUNGAN PROVINSI DKI JAKARTA</h1>
                    <div class="mt-8 overflow-x-auto max-w-5xl mx-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
                            <thead>
                                <tr class="bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-white">
                                    <th class="px-4 py-2 border">No</th>
                                    <th class="px-4 py-2 border">BUMD</th>
                                    <th class="px-4 py-2 border">Kepemilikan Pemprov DKI Jakarta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in patunganlist" :key="index" class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 border text-center">{{ index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ item.bumd }}</td>
                                    <td class="px-4 py-2 border text-center">{{ item.kepemilikan }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </GuestLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head } from '@inertiajs/vue3';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const breadcrumbItems = [
    { label: 'Informasi BUMD', url: '#' },
    { label: 'Daftar BUMD Dan Perusahaan Patungan', url: '/daftar-bumd-perusahaan-patungan' },
];

const heroTitle = 'Daftar BUMD Dan Perusahaan Patungan';
const heroDescription = 'Informasi mengenai Daftar BUMD Dan Perusahaan Patungan.';



// Local JSON reference for BUMD list displayed in the table
const bumdList = ref([
    { bumd: 'Perumda Air Minum Jaya', kepemilikan: '100.00%' },
    { bumd: 'Perumda Dharma Jaya', kepemilikan: '100.00%' },
    { bumd: 'Perumda Pasar Jaya', kepemilikan: '100.00%' },
    { bumd: 'Perumda Pembangunan Sarana Jaya', kepemilikan: '100.00%' },
    { bumd: 'Perumda Paljaya', kepemilikan: '100.00%' },
    { bumd: 'PT Food Station Tjipinang Jaya (Perseroda)', kepemilikan: '99.985%' },
    { bumd: 'PT Pembangunan Jaya Ancol Tbk', kepemilikan: '72.00%' },
    { bumd: 'PT Jakarta Propertindo (Perseroda)', kepemilikan: '99.998%' },
    { bumd: 'PT Bank DKI', kepemilikan: '99.98%' },
    { bumd: 'PT Jakarta Tourisindo (Perseroda)', kepemilikan: '99.998%' },
    { bumd: 'PT MRT Jakarta (Perseroda)', kepemilikan: '99.998%' },
    { bumd: 'PT Transportasi Jakarta', kepemilikan: '99.70%' },
    { bumd: 'PT Jamkrida Jakarta (Perseroda)', kepemilikan: '98.75%' },
    { bumd: 'PT Jakarta Industrial Estate Pulogadung (Perseroda)', kepemilikan: '53.06%' },
]);

// Local JSON reference for Patungan list displayed in the table
const patunganlist = ref([
    { bumd: 'PT Pembangunan Jaya', kepemilikan: '38.80%' },
    { bumd: 'PT Delta Djakarta, Tbk', kepemilikan: '26.25%' },
    { bumd: 'PT Kawasan Berikat Nusantara', kepemilikan: '26.85%' },
    { bumd: 'PT Cemani Toka', kepemilikan: '27.42%' },
    { bumd: 'PT Asuransi Bangun Askrida', kepemilikan: '4.10%' },
    { bumd: 'PT Pakuan, Tbk', kepemilikan: '1.11%' },
    { bumd: 'PT Asuransi Bangun Askrida', kepemilikan: '4.10%' },
    { bumd: 'PT Grahasari Suryajaya', kepemilikan: '8.08%' },
]);
</script>