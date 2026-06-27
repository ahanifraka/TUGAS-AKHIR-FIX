<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { watch, onMounted, computed } from 'vue';
import { useSweetAlert } from '@/Composables/useSweetAlert.js';
import FormInput from '@/Components/FormInput.vue';
import FormImageUpload from '@/Components/FormImageUpload.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

const props = defineProps({
    formTitle: { type: String, default: 'Form BUMD' },
    submitUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
    bumd: { type: Object, default: () => ({}) },
});

function tryParseVisi(input) {
    if (!input) return [];
    try {
        const parsed = JSON.parse(input);
        if (Array.isArray(parsed)) return parsed;
    } catch (e) { }
    if (typeof input === 'string') {
        return input
            .split(/\r?\n|\r|;|\|/)
            .map(s => s.trim())
            .filter(Boolean);
    }
    return [];
}

// Daftar Akun Keuangan Tetap
const DAFTAR_AKUN_KEUANGAN = [
    { no_akun: 1, nama_akun: 'Aset' },
    { no_akun: 2, nama_akun: 'Liabilitas' },
    { no_akun: 3, nama_akun: 'Ekuitas' },
    { no_akun: 4, nama_akun: 'Pendapatan Usaha' },
    { no_akun: 5, nama_akun: 'Beban Pokok Pendapatan' },
    { no_akun: 6, nama_akun: 'Laba Kotor' },
    { no_akun: 7, nama_akun: 'Beban Usaha' },
    { no_akun: 8, nama_akun: 'Laba Usaha' },
    { no_akun: 9, nama_akun: 'Laba Tahun Berjalan' },
    { no_akun: 10, nama_akun: 'Dividen Bagian Pemprov DKI Jakarta' },
];

const yearKeys = [
    'tahun_2021',
    'tahun_2022',
    'tahun_2023',
    'tahun_2024'
];

const yearHeaders = [2021, 2022, 2023, 2024];

const form = useForm({
    kode: props.bumd.kode ?? '',
    nama_pendek: props.bumd.nama_pendek ?? '',
    nama: props.bumd.nama ?? '',
    kategory: props.bumd.kategory ?? '',
    sektor: props.bumd.sektor ?? '',
    bidang_usaha: props.bumd.bidang_usaha ?? '',
    hasil_usaha: props.bumd.hasil_usaha ?? '',
    alamat: props.bumd.alamat ?? '',
    hotline: props.bumd.hotline ?? '',
    telp: props.bumd.telp ?? '',
    fax: props.bumd.fax ?? '',
    email: props.bumd.email ?? '',
    website: props.bumd.website ?? '',
    visi: Array.isArray(props.bumd.visi) ? props.bumd.visi : tryParseVisi(props.bumd.visi),
    misi: Array.isArray(props.bumd.misi) ? props.bumd.misi : tryParseVisi(props.bumd.misi),
    tujuan: Array.isArray(props.bumd.tujuan) ? props.bumd.tujuan : tryParseVisi(props.bumd.tujuan),
    logo: null, // Logo di-handle terpisah, null berarti tidak ada perubahan jika edit
    pengurus: props.bumd.pengurus || props.bumd.pengurus_bumds || [],
    kondisi_keuangan: props.bumd.kondisi_keuangan || props.bumd.kondisi_keuangans || [],
});

// Isi data keuangan default JIKA ini form 'create' (data masih kosong)
onMounted(() => {
    if (!form.kondisi_keuangan || form.kondisi_keuangan.length === 0) {
        form.kondisi_keuangan = DAFTAR_AKUN_KEUANGAN.map(akun => ({
            ...akun,
            id: null,
            tahun_2021: 0,
            tahun_2022: 0,
            tahun_2023: 0,
            tahun_2024: 0,
        }));
    }
});

const { success, error } = useSweetAlert();

watch(() => props.bumd, (val) => {
    Object.assign(form, {
        kode: val?.kode ?? '',
        nama_pendek: val?.nama_pendek ?? '',
        nama: val?.nama ?? '',
        kategory: val?.kategory ?? '',
        sektor: val?.sektor ?? '',
        bidang_usaha: val?.bidang_usaha ?? '',
        hasil_usaha: val?.hasil_usaha ?? '',
        alamat: val?.alamat ?? '',
        hotline: val?.hotline ?? '',
        telp: val?.telp ?? '',
        fax: val?.fax ?? '',
        email: val?.email ?? '',
        website: val?.website ?? '',
        visi: Array.isArray(val?.visi) ? val.visi : tryParseVisi(val?.visi),
        misi: Array.isArray(val?.misi) ? val.misi : tryParseVisi(val?.misi),
        tujuan: Array.isArray(val?.tujuan) ? val.tujuan : tryParseVisi(val?.tujuan),
        logo: null,
        pengurus: val?.pengurus || val?.pengurus_bumds || [],
        kondisi_keuangan: val?.kondisi_keuangan || val?.kondisi_keuangans || [],
    });

    // Repopulasi data keuangan jika di-load dan ternyata kosong
    if (val && (!val.kondisi_keuangan || val.kondisi_keuangan.length === 0)) {
        form.kondisi_keuangan = DAFTAR_AKUN_KEUANGAN.map(akun => ({
            ...akun,
            id: null,
            tahun_2021: 0,
            tahun_2022: 0,
            tahun_2023: 0,
            tahun_2024: 0,
        }));
    }
});

// ... (Fungsi add/remove Visi, Misi, Tujuan) ...
function addVisiItem() {
    form.visi.push('');
}
function removeVisiItem(index) {
    form.visi.splice(index, 1);
}
function addMisiItem() {
    form.misi.push('');
}
function removeMisiItem(index) {
    form.misi.splice(index, 1);
}
function addTujuanItem() {
    form.tujuan.push('');
}
function removeTujuanItem(index) {
    form.tujuan.splice(index, 1);
}

// ===========================================
// == FUNGSI BARU UNTUK PENGURUS ==
// ===========================================
function addPengurus(grup) {
    form.pengurus.push({
        id: null, // id null menandakan ini item baru
        jabatan: '',
        nama: '',
        grup: grup // 'Badan Pengawas' or 'Direksi'
    });
}

function removePengurus(pengurusItem) {
    const index = form.pengurus.indexOf(pengurusItem);
    if (index > -1) {
        form.pengurus.splice(index, 1);
    }
}
// ===========================================

function submit() {
    form.transform((data) => {
        return {
            ...data,
            visi: Array.isArray(data.visi) ? data.visi.map(s => (typeof s === 'string' ? s.trim() : '')).filter(Boolean).join('\n') : data.visi,
            misi: Array.isArray(data.misi) ? data.misi.map(s => (typeof s === 'string' ? s.trim() : '')).filter(Boolean).join('\n') : data.misi,
            tujuan: Array.isArray(data.tujuan) ? data.tujuan.map(s => (typeof s === 'string' ? s.trim() : '')).filter(Boolean).join('\n') : data.tujuan,
            pengurus: JSON.stringify(data.pengurus),
            kondisi_keuangan: JSON.stringify(data.kondisi_keuangan),
            _method: props.method.toLowerCase() !== 'post' ? props.method.toUpperCase() : undefined,
        }
    }).post(props.submitUrl, {
        onSuccess: () => {
            const isUpdate = props.method.toLowerCase() !== 'post';
            success(isUpdate ? 'BUMD berhasil diubah.' : 'BUMD berhasil dibuat.');
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join('\n');
            error(errorMessages || 'Terjadi kesalahan saat menyimpan data');
        },
    });
}
</script>

<template>

    <Head :title="formTitle" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ formTitle }}</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                    <Tabs value="0">
                        <TabList>
                            <Tab value="0">Umum</Tab>
                            <Tab value="1">Kontak</Tab>
                            <Tab value="2">Visi & Misi</Tab>
                            <Tab value="3">Susunan Pengurus</Tab>
                            <Tab value="4">Kondisi Keuangan</Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel value="0">
                                <div class="p-4 grid grid-cols-1 gap-4">
                                    <FormImageUpload v-model="form.logo" label="Logo" :preview-url="props.bumd.logo" :error="form.errors.logo" />
                                    <FormInput v-model="form.kode" label="Kode" required :error="form.errors.kode" />
                                    <FormInput v-model="form.nama_pendek" label="Nama Pendek" required :error="form.errors.nama_pendek" />
                                    <FormInput v-model="form.nama" label="Nama" required :error="form.errors.nama" />
                                    <FormInput v-model="form.kategory" label="Kategori" required :error="form.errors.kategory" />
                                    <FormInput v-model="form.sektor" label="Sektor" required :error="form.errors.sektor" />
                                    <FormInput v-model="form.hasil_usaha" label="Hasil Usaha" :error="form.errors.hasil_usaha" />
                                    <FormInput v-model="form.bidang_usaha" label="Bidang Usaha" required :error="form.errors.bidang_usaha" />
                                </div>
                            </TabPanel>

                            <TabPanel value="1">
                                <div class="p-4 grid grid-cols-1 gap-4">
                                    <FormInput v-model="form.alamat" label="Alamat" type="textarea" rows="4" required :error="form.errors.alamat" />
                                    <FormInput v-model="form.hotline" label="Hotline" :error="form.errors.hotline" />
                                    <FormInput v-model="form.telp" label="Telp" :error="form.errors.telp" />
                                    <FormInput v-model="form.fax" label="Fax" :error="form.errors.fax" />
                                    <FormInput v-model="form.email" label="Email" type="email" :error="form.errors.email" />
                                    <FormInput v-model="form.website" label="Website" type="url" :error="form.errors.website" />
                                </div>
                            </TabPanel>

                            <TabPanel value="2">
                                <div class="p-4 grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Visi</label>
                                        <div class="mt-1 space-y-2">
                                            <div v-for="(item, index) in form.visi" :key="index" class="flex gap-2 items-start">
                                                <div class="flex-1">
                                                    <FormInput v-model="form.visi[index]" placeholder="Contoh: 1) Memelihara dan meningkatkan etos kerja..." />
                                                </div>
                                                <button type="button" @click="removeVisiItem(index)"
                                                    class="mt-1 inline-flex items-center rounded bg-red-600 px-3 py-2 text-white hover:bg-red-700"><i
                                                        class="pi pi-trash"></i></button>
                                            </div>
                                            <button type="button" @click="addVisiItem"
                                                class="inline-flex items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">+
                                                Tambah Visi</button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mt-6">Misi</label>
                                        <div class="mt-1 space-y-2">
                                            <div v-for="(item, index) in form.misi" :key="index" class="flex gap-2 items-start">
                                                <div class="flex-1">
                                                    <FormInput v-model="form.misi[index]" placeholder="Contoh: 1) Mengoptimalkan pelayanan ..." />
                                                </div>
                                                <button type="button" @click="removeMisiItem(index)"
                                                    class="mt-1 inline-flex items-center rounded bg-red-600 px-3 py-2 text-white hover:bg-red-700"><i
                                                        class="pi pi-trash"></i></button>
                                            </div>
                                            <button type="button" @click="addMisiItem"
                                                class="inline-flex items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">+
                                                Tambah Misi</button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mt-6">Tujuan</label>
                                        <div class="mt-1 space-y-2">
                                            <div v-for="(item, index) in form.tujuan" :key="index" class="flex gap-2 items-start">
                                                <div class="flex-1">
                                                    <FormInput v-model="form.tujuan[index]" placeholder="Contoh: 1) Mencapai target pertumbuhan ..." />
                                                </div>
                                                <button type="button" @click="removeTujuanItem(index)"
                                                    class="mt-1 inline-flex items-center rounded bg-red-600 px-3 py-2 text-white hover:bg-red-700"><i
                                                        class="pi pi-trash"></i></button>
                                            </div>
                                            <button type="button" @click="addTujuanItem"
                                                class="inline-flex items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">+
                                                Tambah Tujuan</button>
                                        </div>
                                    </div>
                                </div>
                            </TabPanel>

                            <TabPanel value="3">
                                <div class="p-4 grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">A. Badan Pengawas</label>
                                        <div class="mt-1 space-y-2">
                                            <div v-for="(pengurus, index) in form.pengurus.filter(p => p.grup === 'Badan Pengawas')"
                                                :key="`bp-${index}`" class="flex gap-2 items-start">
                                                <div class="flex-1">
                                                    <FormInput v-model="pengurus.jabatan" placeholder="Jabatan (Cth: Ketua Badan Pengawas)" />
                                                </div>
                                                <div class="flex-1">
                                                    <FormInput v-model="pengurus.nama" placeholder="Nama Pengurus" />
                                                </div>
                                                <button type="button" @click="removePengurus(pengurus)"
                                                    class="mt-1 inline-flex items-center rounded bg-red-600 px-3 py-2 text-white hover:bg-red-700"><i
                                                        class="pi pi-trash"></i></button>
                                            </div>
                                            <button type="button" @click="addPengurus('Badan Pengawas')"
                                                class="inline-flex items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">+
                                                Tambah Badan Pengawas</button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mt-6">B. Direksi</label>
                                        <div class="mt-1 space-y-2">
                                            <div v-for="(pengurus, index) in form.pengurus.filter(p => p.grup === 'Direksi')"
                                                :key="`dir-${index}`" class="flex gap-2 items-start">
                                                <div class="flex-1">
                                                    <FormInput v-model="pengurus.jabatan" placeholder="Jabatan (Cth: Direktur Utama)" />
                                                </div>
                                                <div class="flex-1">
                                                    <FormInput v-model="pengurus.nama" placeholder="Nama Pengurus" />
                                                </div>
                                                <button type="button" @click="removePengurus(pengurus)"
                                                    class="mt-1 inline-flex items-center rounded bg-red-600 px-3 py-2 text-white hover:bg-red-700"><i
                                                        class="pi pi-trash"></i></button>
                                            </div>
                                            <button type="button" @click="addPengurus('Direksi')"
                                                class="inline-flex items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">+
                                                Tambah Direksi</button>
                                        </div>
                                    </div>
                                </div>
                            </TabPanel>

                            <TabPanel value="4">
                                <div class="p-4">
                                    <p class="text-sm text-gray-500 mb-4">(dalam ribu rupiah)</p>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full border border-gray-300 text-left text-sm">
                                            <thead class="bg-gray-50">
                                                <tr class="border-b border-gray-300">
                                                    <th class="p-2 border-r">No.</th>
                                                    <th class="p-2 border-r">Nama Akun</th>
                                                    <th v-for="year in yearHeaders" :key="year"
                                                        class="p-2 text-right border-r">
                                                        {{ year }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                <tr v-for="(akun, index) in form.kondisi_keuangan" :key="index">
                                                    <td class="p-1 border-r w-12 text-center">{{ akun.no_akun }}</td>
                                                    <td class="p-1 border-r font-medium w-1/4">{{ akun.nama_akun }}</td>

                                                    <td v-for="key in yearKeys" :key="key" class="p-1 border-r">
                                                        <input v-model="akun[key]" type="number"
                                                            class="w-full rounded border border-gray-300 px-2 py-1 text-right" />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>

                    <div class="p-6 space-y-4 border-t border-gray-200">
                        <div class="flex justify-end">
                            <PrimaryButton @click="submit" :disabled="form.processing">
                                Simpan
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>