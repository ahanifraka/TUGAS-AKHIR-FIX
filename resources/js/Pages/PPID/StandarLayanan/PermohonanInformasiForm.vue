<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';
import Swal from 'sweetalert2';
import axios from 'axios';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const page = usePage();

const breadcrumbItems = [
    { label: 'Layanan Informasi Publik', url: '#' },
    { label: 'Prosedur Permohonan Informasi', url: '/ppid/tata-cara-permohonan-informasi' },
    { label: 'Form Permohonan Informasi', url: '/permohonan-informasi' }
];

const heroTitle = 'Permohonan Informasi';
const heroDescription = 'Form Permohonan Informasi';

// Tracking state
const trackingCode = ref('');
const trackingPin = ref('');
const trackingResult = ref(null);
const isCheckingTracking = ref(false);

const form = useForm({
    nama: '',
    alamat: '',
    no_telepon_email: '',
    rincian_informasi: '',
    tujuan_penggunaan: '',
    cara_memperoleh: [],
    cara_mendapatkan_salinan: [],
    lampiran: null
});

const caraMemperolehOptions = [
    { value: 'melihat_membaca_mendengarkan_mencatat', label: 'Melihat/Membaca/Mendengarkan/Mencatat' },
    { value: 'mendapatkan_salinan_informasi_dokumen', label: 'Mendapatkan Salinan Informasi (Dokumen)' }
];

const caraMendapatkanSalinanOptions = [
    { value: 'soft_copy', label: 'Soft Copy' },
    { value: 'hard_copy', label: 'Hard Copy' },
    { value: 'fax', label: 'Fax' },
    { value: 'email', label: 'Email' },
    { value: 'kurir', label: 'Kurir' },
    { value: 'langsung', label: 'Langsung' },
    { value: 'pos', label: 'Pos' },
    { value: 'faksimili', label: 'Faksimili' }
];

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type (PDF, JPG, PNG)
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'File tidak valid',
                text: 'Hanya file PDF, JPG, dan PNG yang diperbolehkan.',
            });
            event.target.value = '';
            return;
        }
        
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File terlalu besar',
                text: 'Ukuran file maksimal 5MB.',
            });
            event.target.value = '';
            return;
        }
        
        form.lampiran = file;
    }
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Pending',
        'processed': 'Diproses',
        'completed': 'Selesai',
        'rejected': 'Ditolak'
    };
    return labels[status] || status;
};

const buildTimelineHtml = (data) => {
    let timelineHtml = '<div class="text-left mt-6 flow-root max-h-60 overflow-y-auto pr-2"><ul role="list" class="-mb-8">';
    
    // Add logs
    if (data.logs && data.logs.length > 0) {
        data.logs.forEach((log) => {
            const isStatusChanged = log.action === 'status_changed';
            const iconBg = isStatusChanged ? 'bg-blue-500' : 'bg-gray-500';
            const icon = isStatusChanged 
                ? '<svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>'
                : '<svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>';
            
            timelineHtml += `
            <li>
                <div class="relative pb-8">
                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="${iconBg} h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                                ${icon}
                            </span>
                        </div>
                        <div class="min-w-0 flex-1 pt-1.5 justify-between space-y-1">
                            <div>
                                <p class="text-sm text-gray-500">
                                    <span class="font-medium text-gray-900">${log.user_name}</span>
                                    ${isStatusChanged ? 'mengubah status' : 'menambahkan catatan'}
                                </p>
                            </div>
                            ${isStatusChanged ? `
                            <div class="text-sm text-gray-700">
                                <span class="font-medium">${getStatusLabel(log.old_status)}</span>
                                <span class="mx-1">&rarr;</span>
                                <span class="font-medium">${getStatusLabel(log.new_status)}</span>
                            </div>
                            ` : ''}
                            ${log.notes ? `
                            <div class="text-sm text-gray-700 bg-gray-50 p-2 rounded mt-1">
                                ${log.notes}
                            </div>
                            ` : ''}
                            <div class="text-right text-xs text-gray-500 whitespace-nowrap">
                                ${log.created_at}
                            </div>
                        </div>
                    </div>
                </div>
            </li>`;
        });
    }

    // Add creation log (always at the bottom)
    timelineHtml += `
    <li>
        <div class="relative pb-8">
            <div class="relative flex space-x-3">
                <div>
                    <span class="bg-green-500 h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </span>
                </div>
                <div class="min-w-0 flex-1 pt-1.5 justify-between space-y-1">
                    <div>
                        <p class="text-sm text-gray-500">
                            <span class="font-medium text-gray-900">System</span>
                            menerima permohonan
                        </p>
                    </div>
                    <div class="text-right text-xs text-gray-500 whitespace-nowrap">
                        ${data.created_at}
                    </div>
                </div>
            </div>
        </div>
    </li>
    </ul></div>`;

    return timelineHtml;
};

const checkTracking = async () => {
    if (!trackingCode.value.trim() || !trackingPin.value.trim()) {
        Swal.fire({
            title: 'Data Tidak Lengkap',
            text: 'Silakan masukkan kode tracking dan PIN Anda.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    isCheckingTracking.value = true;
    trackingResult.value = null;

    try {
        const response = await axios.post(route('ppid.track-pengajuan'), {
            kode_unik: trackingCode.value.trim(),
            pin: trackingPin.value.trim()
        });

        if (response.data.success) {
            trackingResult.value = response.data.data;
            const timeline = buildTimelineHtml(response.data.data);
            
            Swal.fire({
                title: 'Status Pengajuan',
                html: `
                    <div class="text-left">
                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <p class="text-sm text-gray-600">Kode Tracking:</p>
                            <p class="text-xl font-bold text-blue-600 font-mono">${response.data.data.kode_unik}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600">Nama:</p>
                                <p class="font-medium">${response.data.data.nama}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status:</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                    response.data.data.status === 'completed' ? 'bg-green-100 text-green-800' :
                                    response.data.data.status === 'rejected' ? 'bg-red-100 text-red-800' :
                                    response.data.data.status === 'processed' ? 'bg-blue-100 text-blue-800' :
                                    'bg-yellow-100 text-yellow-800'
                                }">
                                    ${response.data.data.status_label}
                                </span>
                            </div>
                        </div>
                        <hr class="my-4 border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-2">Riwayat Status</h4>
                        ${timeline}
                    </div>
                `,
                width: '600px',
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#3B82F6'
            });
        }
    } catch (error) {
        const message = error.response?.data?.message || 'Kode tracking tidak ditemukan.';
        Swal.fire({
            title: 'Tidak Ditemukan',
            text: message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    } finally {
        isCheckingTracking.value = false;
    }
};

const submit = () => {
    form.post(route('ppid.permohonan-informasi.store'), {
        onSuccess: () => {
            // Use nextTick to ensure reactive data is updated
            nextTick(() => {
                const kodeUnik = page.props.flash?.kode_unik;
                const pin = page.props.flash?.pin;
                
                console.log('Flash data:', page.props.flash); // Debug
                console.log('Kode Unik:', kodeUnik); // Debug
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: `
                        <div class="text-center">
                            <p class="mb-4 text-gray-700">Permohonan informasi Anda telah berhasil dikirim.</p>
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-500 rounded-lg p-6 my-4">
                                <p class="text-sm text-gray-600 mb-2">Kode Tracking Anda:</p>
                                <div class="bg-white rounded-md p-3 mb-3 relative">
                                    <p class="text-3xl font-bold text-blue-600 font-mono tracking-wider" id="tracking-code">${kodeUnik || 'Kode tidak tersedia'}</p>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2 mt-4">PIN Keamanan:</p>
                                <div class="bg-white rounded-md p-3 mb-3 relative w-32 mx-auto">
                                    <p class="text-2xl font-bold text-gray-800 font-mono tracking-widest">${pin || '----'}</p>
                                </div>

                                ${kodeUnik ? `
                                    <button 
                                        onclick="navigator.clipboard.writeText('Kode: ${kodeUnik}\\nPIN: ${pin}').then(() => {
                                            const btn = event.target;
                                            const originalText = btn.innerHTML;
                                            btn.innerHTML = '✓ Tersalin!';
                                            btn.classList.add('bg-green-500');
                                            btn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                                            setTimeout(() => {
                                                btn.innerHTML = originalText;
                                                btn.classList.remove('bg-green-500');
                                                btn.classList.add('bg-blue-500', 'hover:bg-blue-600');
                                            }, 2000);
                                        })"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 mb-2 mt-2"
                                    >
                                        Salin Kode & PIN
                                    </button>
                                ` : ''}
                                <p class="text-xs text-gray-600 mt-2">
                                    <strong>Penting:</strong> Simpan Kode Tracking dan PIN ini untuk melacak status permohonan Anda.
                                </p>
                            </div>
                        </div>
                    `,
                    confirmButtonColor: '#3B82F6',
                    width: '600px',
                    customClass: {
                        popup: 'tracking-success-modal'
                    }
                });
                form.reset();
            });
        },
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat mengirim permohonan. Silakan periksa kembali data Anda.',
                confirmButtonColor: '#EF4444'
            });
        }
    });
};
</script>

<template>
    <Head title="Form Permohonan Informasi Publik" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        
        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
                <!-- Tracking Section -->
                <section class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-lg shadow max-w-4xl mx-auto mb-8">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            Lacak Status Permohonan Anda
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            Masukkan kode tracking untuk melihat status permohonan informasi Anda
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-center align-center gap-3">
                        <div class="grid grid-cols-1 md:grid-cols-4 justify-center align-center gap-4">
                            <input
                                type="text"
                                v-model="trackingCode"
                                placeholder="Kode Tracking (contoh: PI-251126-1234)"
                                class="col-span-3 px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                @keyup.enter="checkTracking"
                            />
                            <input
                                type="password"
                                v-model="trackingPin"
                                placeholder="PIN (4 digit)"
                                maxlength="4"
                                class="col-span-1 w-32 px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                @keyup.enter="checkTracking"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
                            />
                        </div>
                        <button
                            @click="checkTracking"
                            :disabled="isCheckingTracking"
                            class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
                        >
                            <span v-if="isCheckingTracking">Memeriksa...</span>
                            <span v-else>Cek Status</span>
                        </button>
                    </div>
                </section>

                <section class="bg-white p-6 sm:p-8 md:p-10 rounded-lg shadow-md max-w-4xl mx-auto dark:bg-gray-800">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 dark:text-white">
                            FORMULIR PERMOHONAN INFORMASI PUBLIK
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">
                            Setiap Pemohon Informasi <strong>WAJIB</strong> melengkapi data diri berupa:
                        </p>
                        <ul class="list-disc list-outside pl-6 text-gray-700 dark:text-gray-300 mb-6 space-y-1">
                            <li>Copy KTP bagi individu</li>
                            <li>Surat kuasa bagi perwakilan kelompok</li>
                            <li>Copy Anggaran Dasar bila pemohon informasi adalah sebuah Organisasi</li>
                        </ul>
                        <p class="text-gray-600 dark:text-gray-300">
                            Beserta permohonan tertulis dengan alasan dan tujuan penggunaan informasi sebenar-benarnya
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Nama -->
                        <div>
                            <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nama <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="nama"
                                v-model="form.nama"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.nama }"
                                required
                            />
                            <div v-if="form.errors.nama" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nama }}
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label for="alamat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="alamat"
                                v-model="form.alamat"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.alamat }"
                                required
                            ></textarea>
                            <div v-if="form.errors.alamat" class="mt-1 text-sm text-red-600">
                                {{ form.errors.alamat }}
                            </div>
                        </div>

                        <!-- No Telepon/Email -->
                        <div>
                            <label for="no_telepon_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                No Telepon/Email <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="no_telepon_email"
                                v-model="form.no_telepon_email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.no_telepon_email }"
                                required
                            />
                            <div v-if="form.errors.no_telepon_email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.no_telepon_email }}
                            </div>
                        </div>

                        <!-- Rincian Informasi yang Dibutuhkan -->
                        <div>
                            <label for="rincian_informasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rincian Informasi yang Dibutuhkan <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="rincian_informasi"
                                v-model="form.rincian_informasi"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.rincian_informasi }"
                                required
                            ></textarea>
                            <div v-if="form.errors.rincian_informasi" class="mt-1 text-sm text-red-600">
                                {{ form.errors.rincian_informasi }}
                            </div>
                        </div>

                        <!-- Tujuan Penggunaan Informasi -->
                        <div>
                            <label for="tujuan_penggunaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tujuan Penggunaan Informasi <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="tujuan_penggunaan"
                                v-model="form.tujuan_penggunaan"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.tujuan_penggunaan }"
                                required
                            ></textarea>
                            <div v-if="form.errors.tujuan_penggunaan" class="mt-1 text-sm text-red-600">
                                {{ form.errors.tujuan_penggunaan }}
                            </div>
                        </div>

                        <!-- Cara Memperoleh Informasi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Cara Memperoleh Informasi <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                <div v-for="option in caraMemperolehOptions" :key="option.value" class="flex items-center">
                                    <input
                                        :id="`cara_memperoleh_${option.value}`"
                                        type="checkbox"
                                        :value="option.value"
                                        v-model="form.cara_memperoleh"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                    <label :for="`cara_memperoleh_${option.value}`" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ option.label }}
                                    </label>
                                </div>
                            </div>
                            <div v-if="form.errors.cara_memperoleh" class="mt-1 text-sm text-red-600">
                                {{ form.errors.cara_memperoleh }}
                            </div>
                        </div>

                        <!-- Cara Mendapatkan Salinan Informasi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Cara Mendapatkan Salinan Informasi
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                <div v-for="option in caraMendapatkanSalinanOptions" :key="option.value" class="flex items-center">
                                    <input
                                        :id="`cara_salinan_${option.value}`"
                                        type="checkbox"
                                        :value="option.value"
                                        v-model="form.cara_mendapatkan_salinan"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                    <label :for="`cara_salinan_${option.value}`" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ option.label }}
                                    </label>
                                </div>
                            </div>
                            <div v-if="form.errors.cara_mendapatkan_salinan" class="mt-1 text-sm text-red-600">
                                {{ form.errors.cara_mendapatkan_salinan }}
                            </div>
                        </div>

                        <!-- Lampiran KTP/Surat Kuasa/Anggaran Dasar -->
                        <div>
                            <label for="lampiran" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Lampiran KTP/Surat Kuasa/Anggaran Dasar <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="file"
                                id="lampiran"
                                @change="handleFileUpload"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.lampiran }"
                                required
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                File yang diperbolehkan: PDF, JPG, PNG (Maksimal 5MB)
                            </p>
                            <div v-if="form.errors.lampiran" class="mt-1 text-sm text-red-600">
                                {{ form.errors.lampiran }}
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-6">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-primary text-white font-semibold py-3 px-6 rounded-md hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Mengirim...</span>
                                <span v-else>Kirim Permohonan</span>
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </GuestLayout>
</template>