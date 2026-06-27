<template>
    <Head :title="`Detail Pengajuan Keberatan - ${pengajuan.nama}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Pengajuan Keberatan
                </h2>
                <Link
                    :href="route('admin.ppid.pengajuan-keberatan')"
                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Informasi Pengaju -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pengaju</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ pengajuan.nama }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">PIN Keamanan</label>
                                        <p class="mt-1 text-sm font-mono font-bold text-blue-600 bg-blue-50 inline-block px-2 py-0.5 rounded">{{ pengajuan.pin || '----' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ pengajuan.pekerjaan }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ pengajuan.alamat }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ pengajuan.no_telepon }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">NIK</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ pengajuan.nomor_induk_kependudukan }}</p>
                                    </div>
                                    <div v-if="pengajuan.nomor_pokok_wajib_pajak" class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">NPWP</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ pengajuan.nomor_pokok_wajib_pajak }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Keberatan -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Keberatan</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Alasan Keberatan</label>
                                        <div class="mt-2">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li v-for="alasan in pengajuan.alasan_keberatan" :key="alasan" class="text-sm text-gray-900">
                                                    {{ getAlasanLabel(alasan) }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Kasus Posisi</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ pengajuan.kasus_posisi }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Admin -->
                        <div v-if="pengajuan.admin_notes" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan Admin</h3>
                                <div class="p-3 bg-blue-50 rounded-md">
                                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ pengajuan.admin_notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Status -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span :class="getStatusBadgeClass(pengajuan.status)" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                                            {{ getStatusLabel(pengajuan.status) }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <p>Diajukan: {{ formatDate(pengajuan.created_at) }}</p>
                                        <p v-if="pengajuan.processed_at">Diproses: {{ formatDate(pengajuan.processed_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Status -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Update Status</h3>
                                <form @submit.prevent="updateStatus">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                            <select
                                                id="status"
                                                v-model="form.status"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required
                                            >
                                                <option value="pending">Pending</option>
                                                <option value="processed">Diproses</option>
                                                <option value="completed">Selesai</option>
                                                <option value="rejected">Ditolak</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="admin_notes" class="block text-sm font-medium text-gray-700">Catatan Admin</label>
                                            <textarea
                                                id="admin_notes"
                                                v-model="form.admin_notes"
                                                rows="4"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="Tambahkan catatan untuk pengaju..."
                                            ></textarea>
                                        </div>
                                        <button
                                            type="submit"
                                            :disabled="form.processing"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                        >
                                            <span v-if="form.processing">Memproses...</span>
                                            <span v-else>Update Status</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline</h3>
                                <div class="flow-root">
                                    <ul role="list" class="-mb-8">
                                        <li v-for="(log, index) in pengajuan.logs" :key="log.id">
                                            <div class="relative pb-8">
                                                <span v-if="index !== pengajuan.logs.length - 1 || true" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span :class="[
                                                            log.action === 'status_changed' ? 'bg-blue-500' : 'bg-gray-500',
                                                            'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white'
                                                        ]">
                                                            <svg v-if="log.action === 'status_changed'" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                            </svg>
                                                            <svg v-else class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 justify-between space-y-1">
                                                        <div>
                                                            <p class="text-sm text-gray-500">
                                                                <span class="font-medium text-gray-900">{{ log.user_name || 'System' }}</span>
                                                                {{ log.action === 'status_changed' ? 'mengubah status' : 'menambahkan catatan' }}
                                                            </p>
                                                        </div>
                                                        <div v-if="log.action === 'status_changed'" class="text-sm text-gray-700">
                                                            <span class="font-medium">{{ getStatusLabel(log.old_status) }}</span>
                                                            <span class="mx-1">&rarr;</span>
                                                            <span class="font-medium">{{ getStatusLabel(log.new_status) }}</span>
                                                        </div>
                                                        <div v-if="log.notes" class="text-sm text-gray-700 bg-gray-50 p-2 rounded mt-1">
                                                            {{ log.notes }}
                                                        </div>
                                                        <div class="text-right text-xs text-gray-500 whitespace-nowrap">
                                                            {{ formatDate(log.created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        
                                        <!-- Original Creation Log -->
                                        <li>
                                            <div class="relative pb-8">
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="bg-green-500 h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 justify-between space-y-1">
                                                        <div>
                                                            <p class="text-sm text-gray-500">
                                                                <span class="font-medium text-gray-900">System</span>
                                                                menerima pengajuan
                                                            </p>
                                                        </div>
                                                        <div class="text-right text-xs text-gray-500 whitespace-nowrap">
                                                            {{ formatDate(pengajuan.created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    pengajuan: Object,
});

const form = useForm({
    status: props.pengajuan.status,
    admin_notes: props.pengajuan.admin_notes || '',
});

const updateStatus = () => {
    form.patch(route('admin.ppid.pengajuan-keberatan.update', props.pengajuan.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Form will be reset automatically by Inertia
        },
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
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

const getStatusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processed': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getTimelineIconClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-500',
        'processed': 'bg-blue-500',
        'completed': 'bg-green-500',
        'rejected': 'bg-red-500'
    };
    return classes[status] || 'bg-gray-500';
};

const getAlasanLabel = (alasan) => {
    const labels = {
        'permohonan_informasi_ditolak': 'Permohonan Informasi Ditolak',
        'informasi_berkala_tidak_disediakan': 'Informasi Berkala Tidak Disediakan',
        'permintaan_informasi_tidak_ditanggapi': 'Permintaan Informasi Tidak Ditanggapi',
        'permintaan_informasi_ditanggapi_tidak_sebagaimana_mestinya': 'Permintaan Informasi Ditanggapi Tidak Sebagaimana Mestinya',
        'permintaan_informasi_tidak_dipenuhi': 'Permintaan Informasi Tidak Dipenuhi',
        'biaya_yang_dikenakan_tidak_wajar': 'Biaya yang Dikenakan Tidak Wajar',
        'informasi_disampaikan_melebihi_jangka_waktu': 'Informasi Disampaikan Melebihi Jangka Waktu'
    };
    return labels[alasan] || alasan;
};
</script>