<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const breadcrumbItems = [
    { label: 'Layanan Informasi Publik', url: '#' },
    { label: 'Prosedur Permohonan Informasi', url: '/prosedur-permohonan-informasi' }

];
const heroTitle = 'Prosedur Permohonan Informasi';
const isLangsungOpen = ref(true);
const isTidakLangsungOpen = ref(false);
const isPersyaratanOpen = ref(false);
const heroDescription = 'Informasi Mengenai Tata Cara Permohonan Informasi';

const openZoomModal = () => {
    Swal.fire({
        imageUrl: '/images/ppid/prosedur-permohonan-informasi.png',
        imageAlt: 'Prosedur Permohonan Informasi',
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

    <Head title="Tata Cara Permohonan Informasi" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-0 md:px-4">
                <section class="bg-white p-6 sm:p-8 md:p-10 rounded-lg shadow-md max-w-6xl mx-auto dark:bg-gray-800">
                    <div class="lg:flex">
                        <div class="lg:w-2/3 lg:pr-10">
                            <div class="space-y-4">
                                <div class="border rounded-lg overflow-hidden">
                                    <button @click="isLangsungOpen = !isLangsungOpen"
                                        class="flex justify-between items-center w-full p-5 font-semibold text-base text-left text-gray-800 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:text-white dark:bg-gray-700">
                                        <span>Permohonan Informasi Secara Langsung</span>
                                        <svg class="w-5 h-5 transition-transform duration-200"
                                            :class="{ 'rotate-180': isLangsungOpen }" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div v-if="isLangsungOpen"
                                        class="p-5 border-t border-gray-200 text-gray-700 dark:text-white text-base leading-relaxed space-y-3">
                                        <ul class="list-disc list-outside pl-6 text-gray-800 text-justify dark:text-white">
                                            <li>
                                                Pemohon Informasi Publik Mendatangi secara langsung Gedung BPBUMD di
                                                Kompleks
                                                Balaikota Blok H Lantai 17, Jl. Medan Merdeka Selatan No.8-9, Jakarta
                                                Pusat
                                                10110
                                            </li>
                                            <li>
                                                Dalam memberikan layanan informasi publik, PPID menetapkan waktu
                                                pemberian layanan publik. Penyelenggaraan pelayanan informasi publik di
                                                lingkungan BPBUMD dilaksanakan pada:
                                                <div class="mt-2">
                                                    <p><strong>Senin s/d Kamis : 09.00 -15.00 WIB (Istirahat
                                                            12.00-13.00 WIB)</strong></p>
                                                    <p><strong>Jumat : 09.00 -15.00 WIB (Istirahat 11.00-13.00
                                                            WIB)</strong></p>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="border rounded-lg overflow-hidden">
                                    <button @click="isTidakLangsungOpen = !isTidakLangsungOpen"
                                        class="flex justify-between items-center w-full p-5 font-semibold text-base text-left text-gray-800 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:text-white dark:bg-gray-700">
                                        <span>Permohonan Informasi Publis Secara Tidak Langsung</span>
                                        <svg class="w-5 h-5 transition-transform duration-200"
                                            :class="{ 'rotate-180': isTidakLangsungOpen }" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div v-if="isTidakLangsungOpen"
                                        class="p-5 border-t border-gray-200 text-gray-700 dark:text-white text-base leading-relaxed space-y-3">
                                        <ul class="list-disc list-inside space-y-3 pl-2 text-justify dark:text-white">
                                            <li>
                                                <strong>Portal Publik (Website)</strong>
                                                <p class="pl-5">Pemohon dapat mengunduh formulir yang tersedia pada
                                                    website BPBUMD pada alamat link berikut: <a href="/docs/form-pip.pdf" target="_blank" rel="noreferrer noopener"
                                                        class="inline-block font-bold text-primary hover:text-primary-hover">UNDUH
                                                        FORMULIR</a></p>
                                            </li>
                                            <li>
                                                <strong>Surat Electronic (Email)</strong>
                                                <p class="pl-5">Pemohon dapat menyampaikan permohonan melalui email yang
                                                    dikirim melalui <strong>bpbumbd@jakarta.go.id</strong> dengan
                                                    melampirkan kelengkapan
                                                    identitas pemohon informasi publik yang telah ditentukan.</p>
                                            </li>
                                            <li>
                                                <strong>Telepon</strong>
                                                <p class="pl-5">Pemohon informasi dapat mengajukan permohonan melalui
                                                    nomor telepon <strong>(021)3822076</strong></p>
                                            </li>
                                            <li>
                                                <strong>Surat</strong>
                                                <p class="pl-5">Pemohon dapat mengajukan permohonan melalui surat yang
                                                    ditujukan kepada Pejabat Pengelola Informasi dan Dokumentasi (PPID)
                                                    BPBUMD, <strong>
                                                        Kompleks Balaikota Blok H Lantai 17, Jl. Medan Merdeka
                                                        Selatan No.8-9, Jakarta Pusat 10110.
                                                    </strong></p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="border rounded-lg overflow-hidden">
                                    <button @click="isPersyaratanOpen = !isPersyaratanOpen"
                                        class="flex justify-between items-center w-full p-5 font-semibold text-base text-left text-gray-800 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:text-white dark:bg-gray-700">
                                        <span>Persyaratan Permohonan Informasi Publik</span>
                                        <svg class="w-5 h-5 transition-transform duration-200"
                                            :class="{ 'rotate-180': isPersyaratanOpen }" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div v-if="isPersyaratanOpen"
                                        class="p-5 border-t border-gray-200 text-gray-700 dark:text-white text-base leading-relaxed space-y-3 dark:bg-gray-700">
                                        <ul class="list-disc list-inside space-y-3 pl-2 dark:text-white">
                                            <li>
                                                <strong>Perseorangan</strong>
                                                <ol class="list-[lower-alpha] list-inside pl-5 mt-1 space-y-1">
                                                    <li>Fotocopy Identitas Diri (KTP/SIM/NPWP/Paspor)</li>
                                                    <li>Lampiran Formulir Permohonan Informasi Publik</li>
                                                </ol>
                                            </li>
                                            <li>
                                                <strong>Organisasi / Badan Publik / Lembaga / Perusahaan</strong>
                                                <ol class="list-[lower-alpha] list-inside pl-5 mt-1 space-y-1">
                                                    <li>Fotocopy Identitas Diri (KTP/SIM/NPWP/Akta Perusahaan)</li>
                                                    <li>Surat/Lampiran Formulir Permohonan Informasi Publik</li>
                                                </ol>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-1/3 mt-10 lg:mt-0 lg:pl-10 lg:border-l border-gray-200 text-justify dark:text-white">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 dark:text-white">
                                Form Permohonan Informasi
                            </h3>
                            <p class="text-gray-800 text-base mb-4 dark:text-white">
                                Setiap Pemohon Informasi WAJIB melengkapi data diri berupa:
                            </p>
                            <ul class="list-disc list-outside text-gray-800 pl-6 text-base leading-relaxed mb-3 dark:text-white">
                                <li>Copy KTP bagi individu</li>
                                <li>Surat Kuasa bagi perwakilan kelompok</li>
                                <li>Copy Anggaran Dasar bila pemohon informasi adalah sebuah Organisasi</li>
                            </ul>
                            <p class="text-gray-700 text-base mb-6 dark:text-white">
                                Beserta permohonan tertulis dengan alasan dan tujuan penggunaan informasi
                                sebenar-benarnya
                                Input formulir :
                            </p>
                            <Link href="/permohonan-informasi"
                                class="inline-block bg-blue-600 text-white font-semibold text-sm py-2 px-4 rounded hover:bg-blue-700 transition-colors duration-200 text-center">
                                FORMULIR PERMOHONAN INFORMASI PUBLIK
                            </Link>
                        </div>
                    </div>
                    <hr class="my-10 md:my-12 border-gray-200">

                    <div>
                        <img src="/images/ppid/prosedur-permohonan-informasi.png" alt="Prosedur Permohonan Informasi" title="Prosedur Permohonan Informasi"
                            class="w-full h-auto rounded-lg cursor-zoom-in hover:opacity-90 transition-opacity duration-200" @click="openZoomModal">
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
