<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, nextTick } from 'vue';
import Swal from 'sweetalert2';
import axios from 'axios';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const page = usePage();

const breadcrumbItems = [
    { label: 'Layanan Informasi Publik', url: '#' },
    { label: 'Pengajuan Keberatan', url: '/ppid/prosedur-pengajuan-keberatan' },
    { label: 'Form Pengajuan Keberatan', url: '/pengajuan-keberatan' }
];

const heroTitle = 'Pengajuan Keberatan';
const heroDescription = 'Form Pengajuan Keberatan Informasi';

// Tracking state
const trackingCode = ref('');
const trackingPin = ref('');
const trackingResult = ref(null);
const isCheckingTracking = ref(false);

const form = useForm({
    // A. Informasi Pengaju Keberatan
    nama: '',
    alamat: '',
    pekerjaan: '',
    no_telepon: '',
    nomor_induk_kependudukan: '',
    nomor_pokok_wajib_pajak: '',
    
    // B. Alasan Pengajuan Keberatan
    alasan_keberatan: [],
    
    // Kasus Posisi
    kasus_posisi: ''
});

const alasanKeberatanOptions = [
    { 
        value: 'permohonan_informasi_ditolak', 
        label: 'a. Permohonan Informasi ditolak' 
    },
    { 
        value: 'informasi_berkala_tidak_disediakan', 
        label: 'b. Informasi berkala tidak disediakan' 
    },
    { 
        value: 'permintaan_informasi_tidak_ditanggapi', 
        label: 'c. Permintaan Informasi tidak ditanggapi' 
    },
    { 
        value: 'permintaan_informasi_ditanggapi_tidak_sebagaimana_diminta', 
        label: 'd. Permintaan Informasi ditanggapi tidak sebagaimana yang diminta' 
    },
    { 
        value: 'permintaan_informasi_tidak_dipenuhi', 
        label: 'e. Permintaan Informasi tidak dipenuhi' 
    },
    { 
        value: 'biaya_yang_dikenakan_tidak_wajar', 
        label: 'f. Biaya yang dikenakan tidak wajar' 
    },
    { 
        value: 'informasi_disampaikan_melebihi_jangka_waktu_ditentukan', 
        label: 'g. Informasi disampaikan melebihi jangka waktu yang ditentukan' 
    }
];

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
    form.post(route('ppid.pengajuan-keberatan.store'), {
        onSuccess: () => {
            // Use nextTick to ensure reactive data is updated
            nextTick(() => {
                const kodeUnik = page.props.flash?.kode_unik;
                const pin = page.props.flash?.pin;
                
                console.log('Flash data:', page.props.flash); // Debug
                console.log('Kode Unik:', kodeUnik); // Debug
                
                Swal.fire({
                    title: 'Berhasil!',
                    html: `
                        <div class="text-center">
                            <p class="mb-4 text-gray-700">Pengajuan keberatan Anda telah berhasil dikirim.</p>
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
                                    <strong>Penting:</strong> Simpan Kode Tracking dan PIN ini untuk melacak status pengajuan Anda.
                                </p>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    width: '600px',
                    customClass: {
                        popup: 'tracking-success-modal'
                    }
                });
                form.reset();
            });
        },
        onError: (errors) => {
            const firstError = Object.values(errors || {})[0];
            Swal.fire({
                title: 'Validasi gagal',
                text: firstError || 'Terjadi kesalahan saat mengirim pengajuan keberatan.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
};
</script>

<template>
    <Head title="Form Pengajuan Keberatan" />
    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
                <!-- Tracking Section -->
                <section class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-lg shadow max-w-4xl mx-auto mb-8">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            Lacak Status Pengajuan Anda
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                            Masukkan kode tracking untuk melihat status pengajuan keberatan Anda
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-center align-center gap-3">
                        <div class="grid grid-cols-1 md:grid-cols-4 justify-center align-center gap-4">
                            <input
                                type="text"
                                v-model="trackingCode"
                                placeholder="Kode Tracking (contoh: PK-251126-1234)"
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
                    
                    <!-- Header Form -->
                    <div class="mb-8 text-center">
                        <h2 class="text-xl font-bold text-gray-800 mb-2 dark:text-white">
                            FORMULIR PERNYATAAN KEBERATAN ATAS PERMOHONAN INFORMASI
                        </h2>
                    </div>

                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <!-- A. INFORMASI PENGAJU KEBERATAN -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-200 pb-2">
                                A. INFORMASI PENGAJU KEBERATAN
                            </h3>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                                Tulisan Penggunaan Informasi
                            </p>

                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-700 dark:text-gray-300">Identitas Pemohon</h4>
                                
                                <!-- Nama -->
                                <div>
                                    <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nama<span class="text-red-500">*</span>
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
                                        Alamat<span class="text-red-500">*</span>
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

                                <!-- Pekerjaan -->
                                <div>
                                    <label for="pekerjaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Pekerjaan<span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="pekerjaan"
                                        v-model="form.pekerjaan"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.pekerjaan }"
                                        required
                                    />
                                    <div v-if="form.errors.pekerjaan" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.pekerjaan }}
                                    </div>
                                </div>

                                <!-- No Telepon -->
                                <div>
                                    <label for="no_telepon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        No Telepon<span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="tel"
                                        id="no_telepon"
                                        v-model="form.no_telepon"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.no_telepon }"
                                        required
                                    />
                                    <div v-if="form.errors.no_telepon" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.no_telepon }}
                                    </div>
                                </div>

                                <!-- Nomor Induk Kependudukan -->
                                <div>
                                    <label for="nomor_induk_kependudukan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nomor Induk Kependudukan<span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="nomor_induk_kependudukan"
                                        v-model="form.nomor_induk_kependudukan"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.nomor_induk_kependudukan }"
                                        required
                                    />
                                    <div v-if="form.errors.nomor_induk_kependudukan" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.nomor_induk_kependudukan }}
                                    </div>
                                </div>

                                <!-- Nomor Pokok Wajib Pajak -->
                                <div>
                                    <label for="nomor_pokok_wajib_pajak" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nomor Pokok Wajib Pajak
                                    </label>
                                    <input
                                        type="number"
                                        id="nomor_pokok_wajib_pajak"
                                        v-model="form.nomor_pokok_wajib_pajak"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.nomor_pokok_wajib_pajak }"
                                    />
                                    <div v-if="form.errors.nomor_pokok_wajib_pajak" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.nomor_pokok_wajib_pajak }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- B. ALASAN PENGAJUAN KEBERATAN -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-200 pb-2">
                                B. ALASAN PENGAJUAN KEBERATAN<span class="text-red-500">*</span>
                            </h3>
                            
                            <div class="space-y-3">
                                <div v-for="option in alasanKeberatanOptions" :key="option.value" class="flex items-start">
                                    <input
                                        :id="option.value"
                                        type="checkbox"
                                        :value="option.value"
                                        v-model="form.alasan_keberatan"
                                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                    <label :for="option.value" class="ml-3 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                        {{ option.label }}
                                    </label>
                                </div>
                            </div>
                            <div v-if="form.errors.alasan_keberatan" class="mt-1 text-sm text-red-600">
                                {{ form.errors.alasan_keberatan }}
                            </div>
                        </div>

                        <!-- KASUS POSISI -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-200 pb-2">
                                KASUS POSISI (tambahkan kertas bila perlu)<span class="text-red-500">*</span>
                            </h3>
                            
                            <div>
                                <textarea
                                    id="kasus_posisi"
                                    v-model="form.kasus_posisi"
                                    rows="6"
                                    placeholder="Jelaskan kasus posisi Anda secara detail..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.kasus_posisi }"
                                    required
                                ></textarea>
                                <div v-if="form.errors.kasus_posisi" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.kasus_posisi }}
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-6">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-blue-600 text-white font-semibold py-3 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Mengirim Pengajuan Keberatan...</span>
                                <span v-else>Kirim Pengajuan Keberatan</span>
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </GuestLayout>
</template>