<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    bumd: { type: Object, required: true },
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const pengurus = computed(() => props.bumd.pengurus || []);

// grup-filter helpers
const allPengurus = computed(() => props.bumd.pengurus || props.bumd.pengurus_bumds || []);

const dewanKomisaris = computed(() =>
    allPengurus.value
        .filter(p => {
            const grp = (p.grup || '').toString().toLowerCase();
            const jab = (p.jabatan || '').toString().toLowerCase();
            return grp.includes('komisaris') || grp.includes('badan pengawas') || jab.includes('komisaris') || jab.includes('pengawas');
        })
        .sort((a, b) => (a.urutan || 0) - (b.urutan || 0))
);

const direksi = computed(() =>
    allPengurus.value
        .filter(p => {
            const grp = (p.grup || '').toString().toLowerCase();
            const jab = (p.jabatan || '').toString().toLowerCase();
            return grp.includes('direksi') || jab.includes('direksi') || jab.includes('direktur');
        })
        .sort((a, b) => (a.urutan || 0) - (b.urutan || 0))
);

const yearKeys = [
    'tahun_2021',
    'tahun_2022',
    'tahun_2023',
    'tahun_2024'
];

const yearHeaders = [2021, 2022, 2023, 2024];

// Ambil juga data keuangan
const kondisiKeuangan = computed(() => {
    const data = (props.bumd.kondisi_keuangan || props.bumd.kondisi_keuangans || [])
        .sort((a, b) => a.no_akun - b.no_akun);

    return data.filter(item => {
        return yearKeys.some(key => {
            const val = Number(item[key]);
            return !isNaN(val) && val !== 0;
        });
    });
});

// Helper format angka (jika belum ada)
const formatCurrency = (value) => {
    if (value === null || value === undefined) return 'Rp 0';
    const num = Number(value);
    if (isNaN(num)) return 'Rp 0';
    const isNegative = num < 0;
    const formatted = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.abs(num));
    return isNegative ? `Rp(${formatted})` : `Rp${formatted}`;
};

// Parse JSON string arrays for visi, misi, and tujuan
const parseArrayString = (arrayString) => {
    if (!arrayString) return [];
    try {
        const parsed = JSON.parse(arrayString);
        return Array.isArray(parsed) ? parsed : [arrayString];
    } catch (error) {
        // If parsing fails, treat as single string
        return [arrayString];
    }
};

const visiList = computed(() => parseArrayString(props.bumd.visi));
const misiList = computed(() => parseArrayString(props.bumd.misi));
const tujuanList = computed(() => parseArrayString(props.bumd.tujuan));

// Helper: get initial letter from kode or name
const getInitial = (name, kode) => {
    const base = (kode || name || '').toString().trim();
    return base ? base.charAt(0).toUpperCase() : '?';
};

</script>

<template>

    <Head :title="props.bumd.nama">
        <meta name="description"
            :content="`Detail informasi ${props.bumd.nama} - ${props.bumd.bidang_usaha || 'BUMD'}`" />
        <meta name="keywords"
            :content="`${props.bumd.nama}, ${props.bumd.nama_pendek}, BUMD, ${props.bumd.sektor || ''}, ${props.bumd.bidang_usaha || ''}`" />
    </Head>

    <GuestLayout>
        <div class="container mx-auto px-4 py-12">
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-300">
                    <li>
                        <Link href="/" class="hover:text-gray-700">Beranda</Link>
                    </li>
                    <li>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li>
                        <Link :href="route('bumds.indexPublic')" class="hover:text-gray-700">BUMD</Link>
                    </li>
                    <li>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li class="text-gray-900 font-medium dark:text-white">{{ props.bumd.nama_pendek || props.bumd.nama
                        }}</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2">

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8 dark:bg-gray-800">
                        <div class="flex flex-col items-start gap-6 mb-6">

                            <div v-if="props.bumd.logo" class="flex-shrink-0">
                                <img :src="props.bumd.logo" :alt="props.bumd.nama"
                                    class="w-32 h-32 object-contain rounded-lg" />
                            </div>

                            <div v-else class="flex-shrink-0">
                                <img src="/images/jaya-raya.png" :alt="props.bumd.nama"
                                    class="w-32 h-32 object-contain rounded-lg border" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <h1 class="text-3xl font-bold text-gray-900 mb-2 dark:text-white">{{ props.bumd.nama }}
                                </h1>

                                <p v-if="props.bumd.nama_pendek" class="text-lg text-gray-600 mb-3 dark:text-gray-300">
                                    {{ props.bumd.nama_pendek }}
                                </p>

                                <div class="flex flex-wrap gap-2">
                                    <span v-if="props.bumd.kategory"
                                        class="inline-block px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">
                                        {{ props.bumd.kategory }}
                                    </span>
                                    <span v-if="props.bumd.sektor"
                                        class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                                        {{ props.bumd.sektor }}
                                    </span>

                                </div>
                            </div>
                        </div>

                        <div v-if="props.bumd.bidang_usaha" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 dark:text-white">Bidang Usaha</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ props.bumd.bidang_usaha }}</p>
                        </div>

                        <div v-if="props.bumd.hasil_usaha" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 dark:text-white">Hasil Usaha</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ props.bumd.hasil_usaha }}</p>
                        </div>

                        <div v-if="props.bumd.akta_pendirian" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 dark:text-white">Akta Pendirian</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ props.bumd.akta_pendirian }}</p>
                        </div>

                        <div v-if="props.bumd.akta_perubahan" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 dark:text-white">Akta Perubahan</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ props.bumd.akta_perubahan }}</p>
                        </div>

                        <div v-if="props.bumd.dasar_hukum" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 dark:text-white">Dasar Hukum</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ props.bumd.dasar_hukum }}</p>
                        </div>

                        <div v-if="props.bumd.nilai_saham" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 dark:text-white">Nilai Saham</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ props.bumd.nilai_saham }}</p>
                        </div>

                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8 dark:bg-gray-800">
                        <div class="grid grid-cols-1 gap-8">

                            <div v-if="visiList.length > 0">
                                <h3
                                    class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2 dark:text-white">
                                    Visi
                                </h3>
                                <ul v-if="visiList.length > 1" class="text-gray-700 space-y-2 dark:text-gray-300">
                                    <li v-for="(item, index) in visiList" :key="index" class="flex items-start">
                                        <span>{{ item }}</span>
                                    </li>
                                </ul>
                                <p v-else class="text-gray-700 dark:text-gray-300">{{ visiList[0] }}</p>
                            </div>

                            <div v-if="misiList.length > 0">
                                <h3
                                    class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2 dark:text-white">
                                    Misi
                                </h3>
                                <ul v-if="misiList.length > 1" class="text-gray-700 space-y-2 dark:text-gray-300">
                                    <li v-for="(item, index) in misiList" :key="index" class="flex items-start">
                                        <div v-html="item" class="whitespace-pre-line"></div>
                                    </li>
                                </ul>
                                <div v-else class="text-gray-700 dark:text-gray-300 whitespace-pre-line" v-html="misiList[0]"></div>
                            </div>
                        </div>
                    </div>

                    <div v-if="dewanKomisaris.length > 0 || direksi.length > 0"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8 dark:bg-gray-800">

                        <h2 class="text-xl font-bold mb-4 dark:text-white">SUSUNAN PENGURUS</h2>

                        <div v-if="dewanKomisaris.length > 0">
                            <h3 class="text-l font-semibold mb-4 dark:text-white">Dewan Komisaris</h3>
                            <div class="space-y-3 mb-6 text-gray-700 dark:text-gray-300">
                                <div v-for="p in dewanKomisaris" :key="p.id"
                                    class="grid grid-cols-[200px_auto_1fr] gap-x-4">
                                    <span>{{ p.jabatan }}</span>
                                    <span>:</span>
                                    <span class="font-small text-gray-900 dark:text-white">{{ p.nama }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="direksi.length > 0">
                            <h3 class="text-l font-semibold mb-4 dark:text-white">Direksi</h3>
                            <div class="space-y-3 text-gray-700 dark:text-gray-300">
                                <div v-for="p in direksi" :key="p.id" class="grid grid-cols-[200px_auto_1fr] gap-x-4">
                                    <span>{{ p.jabatan }}</span>
                                    <span>:</span>
                                    <span class="font-small text-gray-900 dark:text-white">{{ p.nama }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="kondisiKeuangan.length > 0"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8 dark:bg-gray-800">

                        <h2 class="text-2xl font-bold mb-2 dark:text-white">Kondisi Keuangan</h2>
                        <p class="text-sm text-gray-500 mb-4 dark:text-gray-400">(dalam ribu rupiah)</p>

                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full border border-gray-300 dark:border-gray-600 text-left text-sm text-gray-900 dark:text-gray-200">

                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr class="border-b border-gray-300 dark:border-gray-600">
                                        <th rowspan="2" class="p-3 border-r border-gray-300 dark:border-gray-600">No.
                                        </th>
                                        <th rowspan="2" class="p-3 border-r border-gray-300 dark:border-gray-600">Nama
                                            Akun</th>
                                        <th :colspan="yearHeaders.length"
                                            class="p-3 text-center border-r border-gray-300 dark:border-gray-600">Tahun
                                        </th>
                                    </tr>
                                    <tr class="border-b border-gray-300 dark:border-gray-600">
                                        <th v-for="year in yearHeaders" :key="year"
                                            class="p-3 text-right border-r border-gray-300 dark:border-gray-600">
                                            {{ year }}
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    <tr v-for="akun in kondisiKeuangan" :key="akun.id">
                                        <td class="p-3 border-r border-gray-300 dark:border-gray-600">{{ akun.no_akun }}
                                        </td>
                                        <td class="p-3 border-r border-gray-300 dark:border-gray-600">{{ akun.nama_akun
                                            }}</td>

                                        <td v-for="key in yearKeys" :key="key"
                                            class="p-3 text-right border-r border-gray-300 dark:border-gray-600">
                                            {{ formatCurrency(akun[key]) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8 dark:bg-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 dark:text-white">Informasi Kontak</h3>
                        <div class="space-y-4">
                            <div v-if="props.bumd.alamat" class="flex items-center gap-3">
                                <i class="pi pi-map-marker"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Alamat</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ props.bumd.alamat }}</p>
                                </div>
                            </div>

                            <div v-if="props.bumd.telp" class="flex items-center gap-3">
                                <i class="pi pi-phone"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Telepon</p>
                                    <a :href="`tel:${props.bumd.telp}`"
                                        class="text-sm text-blue-600 hover:text-blue-800">
                                        {{ props.bumd.telp }}
                                    </a>
                                </div>
                            </div>

                            <div v-if="props.bumd.hotline" class="flex items-center gap-3">
                                <i class="pi pi-headphones"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Hotline</p>
                                    <a :href="`tel:${props.bumd.hotline}`"
                                        class="text-sm text-blue-600 hover:text-blue-800">
                                        {{ props.bumd.hotline }}
                                    </a>
                                </div>
                            </div>

                            <div v-if="props.bumd.fax" class="flex items-center gap-3">
                                <i class="pi pi-print"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Fax</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ props.bumd.fax }}</p>
                                </div>
                            </div>

                            <div v-if="props.bumd.email" class="flex items-center gap-3">
                                <i class="pi pi-envelope"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Email</p>
                                    <a :href="`mailto:${props.bumd.email}`"
                                        class="text-sm text-blue-600 hover:text-blue-800">
                                        {{ props.bumd.email }}
                                    </a>
                                </div>
                            </div>

                            <div v-if="props.bumd.website" class="flex items-center gap-3">
                                <i class="pi pi-globe"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Website</p>
                                    <a :href="`https://${props.bumd.website}`" target="_blank" rel="noopener noreferrer"
                                        class="text-sm text-blue-600 hover:text-blue-800">
                                        {{ props.bumd.website }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 dark:bg-gray-800">
                        <Link :href="route('bumds.indexPublic')"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-hover transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar BUMD
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>