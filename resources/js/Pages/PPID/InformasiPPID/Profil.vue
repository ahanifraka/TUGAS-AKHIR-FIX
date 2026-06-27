<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue'
import Hero from '@/Components/Hero.vue';

const breadcrumbItems = [
  { label: 'PPID', url: '#' },
  { label: 'Profil PPID', url: '/profil-ppid' }
];

const heroTitle = 'Profil PPID';
const heroDescription = 'Profil PPID';

</script>

<template>

  <Head title="Informasi PPID" />

  <GuestLayout>
    <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />

    <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
      <div class="container mx-auto px-4">

        <section class="p-6 sm:p-8 md:p-10 rounded-lg max-w-4xl mx-auto">

          <div class="text-left">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 dark:text-white">
              Informasi PPID
            </h2>
            <div class="mt-4 mb-8 w-24 h-0.5 bg-primary rounded"></div>
          </div>
          <p class="text-justify text-base md:text-lg text-gray-700 leading-relaxed dark:text-gray-200">
            PPID adalah kepanjangan dari Pejabat Pengelola Informasi dan Dokumentasi, PPID bertanggungjawab melaksanakan
            layanan Informasi Publik yang meliputi proses penyimpanan, pendokumentasian, penyediaan dan pelayanan
            Informasi Publik di Badan Publik.
          </p>
          <a href="https://drive.google.com/file/d/15IL8CaQKHzBIGEaMSA33pmbSDvE2dteg/view?usp=drive_link"
            class="inline-block text-primary hover:text-primary-hover transition duration-300 mt-3"
            target="_blank" rel="noopener noreferrer" >
            [SK PPID BPBUMD Tahun 2025]
          </a>
        </section>

      </div>
    </main>
  </GuestLayout>
</template>