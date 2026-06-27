<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref } from 'vue';
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Hero from "@/Components/Hero.vue";

const breadcrumbItems = [
    { label: "Layanan Informasi Publik", url: "#" },
    { label: "Informasi yang Wajib Disediakan dan Diumumkan Secara Berkala", url: "/informasi-1" },
];

const heroTitle = "Informasi yang Wajib Disediakan dan Diumumkan Secara Berkala";
const heroDescription = "Informasi yang Wajib Disediakan dan Diumumkan Secara Berkala";
const openSection = ref(null);
const toggle = (section) => {
    openSection.value = openSection.value === section ? null : section;
};
const pejabat = [
    { id: 1, nama: 'Syaefuloh Hidayat', jabatan: 'Kepala Badan', url: 'https://bpbumd.jakarta.go.id/dok/Pengumuman_Harta_Kekayaan_LHKPN_Syaefuloh%20Hidayat.pdf' },
    { id: 2, nama: 'Fitria Rahadiani', jabatan: 'Sekretaris Badan', url: 'https://bpbumd.jakarta.go.id/dok/Pengumuman_Harta_Kekayaan_LHKPN_483849%20Ibu%20Fitria.pdf' },
    { id: 3, nama: 'Memi Mundari Pernithasari', jabatan: 'Kepala Bidang Usaha Transportasi, Properti, dan Keuangan', url: 'https://bpbumd.jakarta.go.id/dok/Pengumuman_Harta_Kekayaan_LHKPN_Memi%20Mundari.pdf' },
    { id: 4, nama: 'Budi Purnama', jabatan: 'Kepala Bidang Usaha Infrastruktur, Pariwisata, dan Kawasan', url: 'https://bpbumd.jakarta.go.id/dok/PPengumuman_Harta_Kekayaan_LHKPN_Budi%20Purnama.pdf' },
    { id: 5, nama: 'Thomas', jabatan: 'Kepala Bidang Usaha Pangan, Utilitas, Perpasaran dan Industri', url: 'https://bpbumd.jakarta.go.id/dok/Pengumuman_Harta_Kekayaan_LHKPN_Thomas.pdf' },
    { id: 6, nama: 'Wahyudi', jabatan: 'Kepala Pusat Kebijakan Strategis dan Pelayanan BUMD', url: 'https://bpbumd.jakarta.go.id/dok/Pengumuman_Harta_Kekayaan_LHKPN_Wahyudi.pdf' },
    { id: 4, nama: 'Budi Purnama', jabatan: 'Kepala Bidang Usaha Infrastruktur, Pariwisata, dan Kawasan', url: 'https://bpbumd.jakarta.go.id/dok/PPengumuman_Harta_Kekayaan_LHKPN_Budi%20Purnama.pdf' },
    { id: 5, nama: 'Thomas', jabatan: 'Kepala Bidang Usaha Pangan, Utilitas, Perpasaran dan Industri', url: 'https://bpbumd.jakarta.go.id/dok/Pengumuman_Harta_Kekayaan_LHKPN_Thomas.pdf' },
    { id: 6, nama: 'Wahyudi', jabatan: 'Kepala Pusat Kebijakan Strategis dan Pelayanan BUMD', url: 'https://bpbumd.jakarta.go.id/dok/Pengumuman_Harta_Kekayaan_LHKPN_Wahyudi.pdf' },
    { id: 7, nama: 'Rio Apriyanto', jabatan: 'Kepala Subbagian Umum dan Kepegawaian', url: 'https://bpbumd.jakarta.go.id/dok/Pengumuman_Harta_Kekayaan_LHKPN_Rio%20Apriyanto.pdf' },
    { id: 8, nama: 'Eka Permatahati', jabatan: 'Kepala Subbagian Tata Usaha Pusat Kebijakan Strategis dan Pelayanan BUMD', url: 'https://bpbumd.jakarta.go.id/dok/Pengumuman_Harta_Kekayaan_LHKPN_Eka%20Permatahati.pdf' },
    { id: 9, nama: 'Saloko Salasanto', jabatan: 'Bendahara', url: 'https://bpbumd.jakarta.go.id/dok/Pengumuman_Harta_Kekayaan_LHKPN_Saloko%20Salasanto.pdf' },
];
</script>

<template>

    <Head title="Informasi yang Wajib Disediakan dan Diumumkan Secara Berkala" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />

        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-2">
                <section class="py-6 sm:py-8 md:py-10 rounded-lg max-w-5xl mx-auto">
                    <div class="text-center">
                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 dark:text-gray-100">
                            Pilih Dokumen
                        </h2>
                        <div class="mt-4 mb-8 w-24 h-0.5 bg-primary mx-auto"></div>
                    </div>

                    <div class="space-y-3">

                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <button @click="toggle('perkin')"
                                class="flex justify-between items-center w-full text-left text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 focus:outline-none">
                                <span>Perkin Kepala Perangkat Daerah</span>

                                <svg class="w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': openSection === 'perkin' }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-if="openSection === 'perkin'" class="flex flex-col space-y-2 pl-5 mt-3">
                                <a href="https://bpbumd.jakarta.go.id/uploads/perkin_kepala_perangkat_daerah_tahun_2022.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="flex items-center text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Perkin Kepala Perangkat Daerah Tahun 2022 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/PerkinKaban.pdf" target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Perkin Kepala Perangkat Daerah Tahun 2023 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/perkin_kepala_perangkat_daerah_tahun_2025.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="flex items-center text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Perkin Kepala Perangkat Daerah Tahun 2024 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                            </div>
                        </div>

                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <Link href="/tugas-pokok-dan-fungsi"
                                class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                            Tugas Pokok dan Fungsi BPBUMD <i class="pi pi-arrow-right ml-1"
                                style="font-size: 0.75rem"></i>
                            </Link>
                        </div>

                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <Link href="/struktur-organisasi"
                                class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                            Struktur Organisasi BPBUMD <i class="pi pi-arrow-right ml-1" style="font-size: 0.75rem"></i>
                            </Link>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <Link href="/pejabat"
                                class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                            Daftar Pejabat Struktural dan Fungsional BPBUMD <i class="pi pi-arrow-right ml-1"
                                style="font-size: 0.75rem"></i>
                            </Link>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <button @click="toggle('renstra')"
                                class="flex justify-between items-center w-full text-left text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 focus:outline-none">
                                <span>Rencana Strategis BPBUMD</span>

                                <svg class="w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': openSection === 'renstra' }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-if="openSection === 'renstra'" class="flex flex-col space-y-2 pl-5 mt-3">
                                <Link href="/rencana-strategis"
                                    class="flex items-center text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                Rencana Strategis BPBUMD Tahun 2017 - 2022 <i class="pi pi-arrow-right ml-1"
                                    style="font-size: 0.75rem"></i>
                                </Link>
                                <a href="https://bpbumd.jakarta.go.id/dok/RenstraBPBUMDTahun2023-2026.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="flex items-center text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Rencana Strategis BPBUMD Tahun 2023 - 2026 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                            </div>
                        </div>

                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <button @click="toggle('lakip')"
                                class="flex justify-between items-center w-full text-left text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 focus:outline-none">
                                <span>LAKIP BPBUMD</span>

                                <svg class="w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': openSection === 'lakip' }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-if="openSection === 'lakip'" class="flex flex-col space-y-2 pl-5 mt-3">
                                <a href="https://bpbumd.jakarta.go.id/dok/LAPORAN_LAKIP_BPBUMD_2021_Final.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="flex items-center text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    LAKIP BPBUMD Tahun 2021 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/LKIP_BPBUMD_Tahun_2022[1].pdf" target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    LAKIP BPBUMD Tahun 2022 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/LAPORAN%20LAKIP%20BPBUMD%20Tahun%202023%20Fix.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="flex items-center text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    LAKIP BPBUMD Tahun 2023 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/LAKIP%20BPBUMD%20Tahun%202024.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="flex items-center text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    LAKIP BPBUMD Tahun 2024 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                            </div>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <button @click="toggle('lapkeu')"
                                class="flex justify-between items-center w-full text-left text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 focus:outline-none">
                                <span>Laporan Keuangan BPBUMD</span>

                                <svg class="w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': openSection === 'lapkeu' }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-if="openSection === 'lapkeu'" class="flex flex-col space-y-2 pl-5 mt-3">
                                <a href="https://bpbumd.jakarta.go.id/dok/Laporan-Keuangan-BPBUMD-2020.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Laporan Keuangan BPBUMD 2020 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Laporan-Keuangan-BPBUMD-2021.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Laporan Keuangan BPBUMD 2021 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Laporan-Keuangan-BPBUMD-2022.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Laporan Keuangan BPBUMD 2022 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Laporan%20Keuangan%20BPBUMD%202023.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Laporan Keuangan BPBUMD 2023 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Laporan%20Keuangan%20BPBUMD%202024.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Laporan Keuangan BPBUMD 2024 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                            </div>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <button @click="toggle('rka')"
                                class="flex justify-between items-center w-full text-left text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 focus:outline-none">
                                <span>Rencana Kerja dan Anggaran</span>

                                <svg class="w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': openSection === 'rka' }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-if="openSection === 'rka'" class="flex flex-col space-y-2 pl-5 mt-3">
                                <a href="https://bpbumd.jakarta.go.id/dok/PerubahanRKABPBUMDTahun2023.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Perubahan RKA BPBUMD Tahun 2023 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Perubahan%20RKA%20BPBUMD%20Tahun%202024.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Perubahan RKA BPBUMD Tahun 2024 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Perubahan%20RKA%20BPBUMD%20Tahun%202025.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Perubahan RKA BPBUMD Tahun 2025 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                            </div>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <button @click="toggle('dpa')"
                                class="flex justify-between items-center w-full text-left text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 focus:outline-none">
                                <span>Dokumen Pelaksanaan Anggaran</span>

                                <svg class="w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': openSection === 'dpa' }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-if="openSection === 'dpa'" class="flex flex-col space-y-2 pl-5 mt-3">
                                <a href="https://bpbumd.jakarta.go.id/dok/P%20DPA%20BPBUMD%202022.pdf" target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Perubahan DPA BPBUMD Tahun 2022 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Perubahan%20DPA%20BPBUMD%20Tahun%202023.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Perubahan DPA BPBUMD Tahun 2023 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Perubahan%20DPA%20BPBUMD%20Tahun%202024.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Perubahan DPA BPBUMD Tahun 2024 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Perubahan%20DPA%20BPBUMD%20Tahun%202025.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                    Perubahan DPA BPBUMD Tahun 2025 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                            </div>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <button @click="toggle('program')"
                                class="flex justify-between items-center w-full text-left text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 focus:outline-none">
                                <span>Program dan Kegiatan BPBUMD</span>

                                <svg class="w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': openSection === 'program' }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-if="openSection === 'program'" class="flex flex-col space-y-2 pl-5 mt-3">
                                <a href="https://bpbumd.jakarta.go.id/dok/DPPA%20BPBUMD%20Tahun%202021%20Final.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Program dan Kegiatan BPBUMD Tahun 2021 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Perubahan_DPA_BPBUMD_Tahun_2023[1].pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Program dan Kegiatan BPBUMD Tahun 2023 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Program_dan_Kegiatan_BPBUMD_Tahun_2024.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Program dan Kegiatan BPBUMD Tahun 2024 <i class="pi pi-external-link ml-1"
                                        style="font-size: 0.75rem"></i>
                                </a>
                            </div>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <a href="https://publik.bapedadki.net/ringkasan_skpd/1040/1040/belanja" target="_blank"
                                rel="noopener noreferrer"
                                class="text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                Data Program dan Kegiatan <i class="pi pi-external-link ml-1"
                                    style="font-size: 0.75rem"></i>
                            </a>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <button @click="toggle('ikhtisar')"
                                class="flex justify-between items-center w-full text-left text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 focus:outline-none">
                                <span>Ikhtisar Realisasi Anggaran BPBUMD</span>

                                <svg class="w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': openSection === 'ikhtisar' }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-if="openSection === 'ikhtisar'" class="flex flex-col space-y-2 pl-5 mt-3">
                                <a href="https://ppid.jakarta.go.id/detail/257/6415" target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Laporan Pelayanan Permintaan Informasi Publik 2020 <i
                                        class="pi pi-external-link ml-1" style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Surat%20Penyampaian%20Laporan%20Pelayanan%20Permintaan%20Informasi%20Publik%202023%20PPID_sign.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Laporan Pelayanan Permintaan Informasi Publik 2023 <i
                                        class="pi pi-external-link ml-1" style="font-size: 0.75rem"></i>
                                </a>
                                <a href="https://bpbumd.jakarta.go.id/dok/Surat%20Penyampaian%20Laporan%20Pelayanan%20Permintaan%20Informasi%20Publik%202024%20PPID.pdf"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors duration-200">
                                    Laporan Pelayanan Permintaan Informasi Publik 2024 <i
                                        class="pi pi-external-link ml-1" style="font-size: 0.75rem"></i>
                                </a>
                            </div>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <Link href="/ppid/tata-cara-permohonan-informasi"
                                class="text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                            Prosedur Permohonan Informasi Publik <i class="pi pi-arrow-right ml-1"
                                style="font-size: 0.75rem"></i>
                            </Link>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <a href="https://bpbumd.jakarta.go.id/dok/Rekap%20SIRUP%202025.pdf" target="_blank"
                                rel="noopener noreferrer"
                                class="text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                Rencana Umum Pengadaan Barang dan Jasa <i class="pi pi-external-link ml-1"
                                    style="font-size: 0.75rem"></i>
                            </a>
                        </div>
                        <div class="transition-colors duration-200 py-2 px-4 rounded">
                            <button @click="toggle('lhkpn')"
                                class="flex justify-between items-center w-full text-left text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 focus:outline-none">
                                <span>Laporan Harta Kekayaan Penyelenggara Negara</span>
                                <svg class="w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': openSection === 'lhkpn' }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-if="openSection === 'lhkpn'" class="w-full mt-4 pl-5">
                                <div class="flex items-center pb-3 mb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div
                                        class="w-1/12 text-left font-bold text-gray-600 dark:text-gray-300 uppercase text-sm">
                                        No.</div>
                                    <div
                                        class="w-4/12 text-left font-bold text-gray-600 dark:text-gray-300 uppercase text-sm">
                                        Nama</div>
                                    <div
                                        class="w-7/12 text-left font-bold text-gray-600 dark:text-gray-300 uppercase text-sm">
                                        Jabatan
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div v-for="item in pejabat" :key="item.id"
                                        class="flex items-center text-base md:text-lg">
                                        <div class="w-1/12 text-left text-gray-700 dark:text-gray-300">{{ item.id }}
                                        </div>
                                        <div class="w-4/12 text-left">
                                            <Link :href="item.url"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline font-medium">
                                            {{
                                                item.nama }}</Link>
                                        </div>
                                        <div class="w-7/12 text-left">
                                            <Link href="#"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline font-medium">
                                            {{
                                                item.jabatan }}</Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </GuestLayout>
</template>