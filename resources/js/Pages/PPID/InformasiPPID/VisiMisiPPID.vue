<script setup>
import { Head } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Hero from "@/Components/Hero.vue";

const breadcrumbItems = [
    { label: "PPID", url: "#" },
    { label: "Visi & Misi PPID", url: "/visimisi-ppid" },
];

const heroTitle = "Visi dan Misi PPID";
const heroDescription =
    "Visi dan Misi Pejabat Pengelola Informasi dan Dokumentasi BPBUMD Provinsi DKI Jakarta";
</script>

<template>
    <GuestLayout>
        <Head title="Visi dan Misi PPID">
            <meta
                name="description"
                content="Visi dan Misi PPID BPBUMD Provinsi DKI Jakarta"
            />
            <meta
                name="keywords"
                content="Visi Misi PPID, informasi publik, BPBUMD, DKI Jakarta"
            />
        </Head>

        <Hero
            :breadcrumb="breadcrumbItems"
            :title="heroTitle"
            :description="heroDescription"
        />

        <main class="py-12 max-w-4xl mx-auto px-4 md:px-4">
            <section
                class="bg-white p-6 sm:p-8 md:p-10 rounded-lg max-w-4xl mx-auto dark:bg-gray-800"
            >
                <!-- Visi Section -->
                <div class="mb-8">
                    <h2
                        class="text-center text-2xl font-bold text-gray-900 mb-4 dark:text-white"
                    >
                        Visi PPID
                    </h2>
                    <div class="mt-4 mb-8 w-24 h-0.5 bg-primary mx-auto"></div>
                    <p class="text-lg text-gray-700 leading-relaxed text-justify dark:text-gray-200">
                        Terlaksananya keterbukaan informasi publik yang
                        berkualitas dalam rangka mendukung terwujudnya tata
                        kelola pemerintahan yang baik (<i>good governance</i>) dalam
                        pembinaan BUMD dan tata kelola perusahaan yang baik
                        (<i>good corporate governance</i>) dalam pengelolaan BUMD.
                    </p>
                </div>

                <!-- Misi Section -->
                <div class="mb-8">
                    <h2
                        class="text-center text-2xl font-bold text-gray-900 mb-4 dark:text-white"
                    >
                        Misi PPID
                    </h2>
                    <div class="mt-4 mb-8 w-24 h-0.5 bg-primary mx-auto"></div>
                    <ol
                        class="list-decimal list-outside pl-6 text-gray-800 text-base md:text-lg leading-relaxed space-y-3 dark:text-gray-200"
                    >
                        <li class="pl-2 text-justify">
                            Melaksanakan layanan informasi publik yang
                            berkualitas dan sesuai ketentuan peraturan
                            perundang-undangan.
                        </li>
                        <li class="pl-2 text-justify">
                            Mengoptimalkan penggunaan sarana dan prasarana
                            teknologi informasi dalam rangka efisiensi dan
                            efektivitas layanan informasi publik.
                        </li>
                        <li class="pl-2 text-justify">
                            Meningkatkan profesionalisme SDM layanan informasi
                            publik.
                        </li>
                        <li class="pl-2 text-justify">
                            Membangun sinergi dengan Komisi Informasi dan BUMD
                            Provinsi DKI Jakarta dalam meningkatkan kualitas
                            pelaksanaan keterbukaan informasi publik terkait
                            BUMD.
                        </li>
                    </ol>
                </div>
            </section>
        </main>
    </GuestLayout>
</template>