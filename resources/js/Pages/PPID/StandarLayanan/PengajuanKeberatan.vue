<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue'; // Ganti dengan nama layout Anda
import Hero from '@/Components/Hero.vue';

// Data untuk Hero Section
const breadcrumbItems = [
    { label: 'Layanan Informasi Publik', url: '#' },
    { label: 'Prosedur Pengajuan Keberatan', url: '/prosedur-pengajuan-keberatan' }

];
const heroTitle = 'Prosedur Pengajuan Keberatan';
const heroDescription = '-';

</script>

<style>
li>* {
    vertical-align: text-top;
}
</style>

<template>

    <Head title="Prosedur Pengajuan Keberatan" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <!-- Main Content Container -->
            <div class="container mx-auto px-4 py-8 max-w-4xl">
                <!-- Main Title -->
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 border-b-2 border-blue-600 pb-3 dark:text-white">
                    PENGAJUAN KEBERATAN INFORMASI PUBLIK
                </h2>

                <!-- Subtitle -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4 dark:text-white">Mekanisme Pengajuan Keberatan</h3>
                </div>

                <!-- Content Sections -->
                <div class="space-y-8">

                    <!-- Section 1: Pengajuan Keberatan -->
                    <div class="bg-white p-6 border-l-4 border-blue-500 dark:bg-gray-800">
                        <ul class="list-disc list-outside pl-4 space-y-4 text-justify">
                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                <div class="inline-block">
                                    Pemohon informasi publik dapat mengajukan keberatan secara tertulis atau mengisi
                                    formulir
                                    <Link href="/form-pengajuan-keberatan"
                                        class="text-blue-600 hover:text-blue-800 underline font-medium transition-colors duration-200">
                                    Formulir Pengajuan Keberatan
                                    </Link>
                                    kepada Atasan PPID paling lambat 30 (tiga puluh) hari kerja setelah ditemukannya
                                    alasan
                                    sebagai berikut:

                                    <!-- Nested List -->
                                    <ul class="list-disc mt-4 pl-12 space-y-2">
                                        <li>
                                            <span class="text-gray-600 dark:text-white">penolakan atas permohonan informasi
                                                publik;</span>
                                        </li>
                                        <li>
                                            <span class="text-gray-600 dark:text-white">tidak disediakannya informasi berkala;</span>
                                        </li>
                                        <li>
                                            <span class="text-gray-600 dark:text-white">tidak ditanggapinya permohonan informasi
                                                publik;</span>
                                        </li>
                                        <li>
                                            <span class="text-gray-600 dark:text-white">permohonan informasi publik ditanggapi tidak
                                                sebagaimana yang diminta;</span>
                                        </li>
                                        <li>
                                            <span class="text-gray-600 dark:text-white">tidak dipenuhinya permohonan informasi
                                                publik;</span>
                                        </li>
                                        <li>
                                            <span class="text-gray-600 dark:text-white">pengenaan biaya yang tidak wajar;
                                                dan/atau</span>
                                        </li>
                                        <li>
                                            <span class="text-gray-600 dark:text-white">penyampaian informasi publik yang melebihi waktu
                                                yang diatur dalam peraturan perundang-undangan.</span>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                <span>Dalam hal pengajuan keberatan disampaikan secara tidak tertulis, Tim Sekretariat
                                    PPID
                                    mengarahkan Pemohon Informasi Publik yang mengajukan keberatan atau pihak penerima
                                    kuasa
                                    untuk mengisi formulir keberatan sesuai format.</span>
                            </li>
                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                <span>Dalam mengajukan keberatan, pemohon wajib menyertakan identitas pemohon yang sah
                                    sebagaimana syarat dalam permohonan informasi</span>
                            </li>
                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                <span>Dalam mengajukan keberatan, pemohon wajib menyertakan identitas pemohon yang sah
                                    sebagaimana syarat dalam permohonan informasi</span>
                            </li>
                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                <div>Pemohon Keberatan harus menyertakan dokumen sebagai berikut:</div>
                                <div class="pl-6 space-y-2 text-gray-700 dark:text-white">
                                    <div class="flex items-start">
                                        <span class="mr-2">a)</span>
                                        <span>Surat tanggapan/jawaban permohonan informasi dari PPID</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="mr-2">b)</span>
                                        <span>Formulir tanda terima permohonan informasi (dalam hal tidak ditanggapinya
                                            permohonan informasi)</span>
                                    </div>
                                </div>
                            </li>
                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                <span>Tim Sekretariat PPID wajib memberikan salinan formulir keberatan disertai nomor
                                    registrasi keberatan kepada Pemohon Informasi Publik yang mengajukan keberatan atau
                                    kuasanya sebagai tanda terima pengajuan keberatan.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white p-6 border-l-4 border-teal-500 text-justify dark:bg-gray-800 dark:border-teal-500">
                        <p class="text-gray-700 leading-relaxed mb-6 dark:text-white">
                            Pengajuan sengketa informasi publik ke Komisi
                            Informasi diajukan Pemohon Informasi publik selambat-lambatnya.
                            14 HARI KERJA sejak diterimanya tanggapan tertulis
                            atas surat keberatan pemohon informasi publik kepada atasan PPID badan publik atau
                            berakhirnya
                            masa 30 HARI KERJA bagi atasan PPID badan publik untuk
                            memberikan tanggapan secara tertulis atas surat keberatan dari pemohon informasi publik.
                        </p>

                        <ul class="list-disc list-outside pl-12 space-y-4">
                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                <span>Pengajuan sengketa informasi publik baik oleh perorangan, badan hukum ataupun
                                    kelompok
                                    orang bisa diajukan dengan cara mendatangi langsung kantor komisi informasi dan
                                    menemui
                                    petugas administrasi-pengaduan dan penyelesaian sengketa informasi ataupun
                                    mengajukan
                                    permohonan sengketa informasi publik secara online seperti email;</span>
                            </li>
                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                <div class="flex items-start">
                                    <div>
                                        <span>Pemohon sengketa informasi publik wajib melengkapi berkas permohonan
                                            pengajuan
                                            sengketa informasi publik sebelum mendapatkan nomor registrasi/akta
                                            registrasi
                                            sengketa informasi publik dari petugas kepaniteraan komisi informasi:</span>

                                        <!-- Detailed Requirements -->
                                        <ul class="list-disc list-outside mt-4 ml-12 space-y-3">
                                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                                <span class="text-gray-600 dark:text-white">Pemohon mengisi Form Permohonan Penyelesaian
                                                    Sengketa Informasi yang telah disediakan petugas;</span>
                                            </li>
                                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                                <span class="text-gray-600 dark:text-white">Membawa bukti surat permohonan informasi
                                                    kepada
                                                    Badan Publik dan tanda terimanya;</span>
                                            </li>
                                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                                <span class="text-gray-600 dark:text-white">Membawa bukti jawaban permohonan informasi
                                                    dari
                                                    Badan Publik beserta tanda terimanya (jika ada);</span>
                                            </li>
                                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                                <span class="text-gray-600 dark:text-white">Membawa bukti pengajuan keberatan kepada
                                                    Badan
                                                    Publik dan tanda terimanya;</span>
                                            </li>
                                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                                <span class="text-gray-600 dark:text-white">Membawa bukti jawaban keberatan dari Badan
                                                    Publik beserta tanda terimanya (jika ada);</span>
                                            </li>
                                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                                <div class="text-gray-600 dark:text-white">
                                                    <span>Membawa bukti identitas (Identitas yang sah yaitu: Fotokopi
                                                        Kartu
                                                        Tanda Penduduk, Paspor atau
                                                        identitas lain yang sah yang dapat membuktikan Pemohon adalah
                                                        warga negara Indonesia; atau Anggaran dasar yang telah disahkan
                                                        oleh
                                                        Menteri Hukum dan Hak Asasi Manusia dan telah tercatat di Berita
                                                        Negara Republik Indonesia dalam hal Pemohon adalah Badan Hukum;
                                                        Surat kuasa dan fotokopi Kartu Tanda Penduduk
                                                        pemberi kuasa dalam hal Pemohon mewakili kelompok orang).</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li class="text-gray-700 leading-relaxed dark:text-white">
                                <span>Setelah permohonan sengketa informasi publik mendapatkan nomor registrasi atau
                                    akta
                                    registrasi maka 14 hari kerja setelahnya komisi informasi mulai melakukan proses
                                    penyelesaian sengketa informasi
                                    publik dengan diawali melakukan pemanggilan secara patut kepada pemohon dan termohon
                                    untuk menghadiri sidang ajudikasi non litigasi tahap pemeriksaan awal</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </GuestLayout>
</template>
