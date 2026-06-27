<template>
    <Head title="Detail Permohonan Informasi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Permohonan Informasi
                </h2>
                <Link
                    :href="route('admin.ppid.permohonan-informasi')"
                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Informasi Pemohon -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pemohon</h3>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Nama</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ permohonan.nama }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">No. Telepon/Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ permohonan.no_telepon_email }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">PIN Keamanan</dt>
                                        <dd class="mt-1 text-sm font-mono font-bold text-blue-600 bg-blue-50 inline-block px-2 py-0.5 rounded">{{ permohonan.pin || '----' }}</dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ permohonan.alamat }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Detail Permohonan -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Permohonan</h3>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Rincian Informasi yang Dibutuhkan</dt>
                                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ permohonan.rincian_informasi }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Tujuan Penggunaan Informasi</dt>
                                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ permohonan.tujuan_penggunaan }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Cara Memperoleh Informasi</dt>
                                        <dd class="mt-1">
                                            <ul class="list-disc list-inside text-sm text-gray-900">
                                                <li v-for="cara in permohonan.cara_memperoleh" :key="cara">{{ cara }}</li>
                                            </ul>
                                        </dd>
                                    </div>
                                    <div v-if="permohonan.cara_mendapatkan_salinan && permohonan.cara_mendapatkan_salinan.length > 0">
                                        <dt class="text-sm font-medium text-gray-500">Cara Mendapatkan Salinan Informasi</dt>
                                        <dd class="mt-1">
                                            <ul class="list-disc list-inside text-sm text-gray-900">
                                                <li v-for="cara in permohonan.cara_mendapatkan_salinan" :key="cara">{{ cara }}</li>
                                            </ul>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Lampiran -->
                        <div v-if="permohonan.lampiran_path" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Lampiran</h3>
                                <div class="flex flex-col gap-3">
                                    <template v-if="isImageFile(permohonan.lampiran_path)">
                                        <img
                                            :src="route('admin.ppid.permohonan-informasi.download', permohonan.id)"
                                            alt="Lampiran"
                                            class="max-w-xs max-h-64 rounded border object-contain mb-2"
                                            loading="lazy"
                                            @error="event => event.target.style.display = 'none'"
                                        />
                                    </template>
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">KTP/Surat Kuasa/Anggaran Dasar</p>
                                            <p class="text-sm text-gray-500">{{ getFileName(permohonan.lampiran_path) }}</p>
                                        </div>
                                        <a
                                            :href="route('admin.ppid.permohonan-informasi.download', permohonan.id)"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            target="_blank"
                                        >
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Notes -->
                        <div v-if="permohonan.admin_notes" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan Admin</h3>
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ permohonan.admin_notes }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Status & Actions -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Status & Aksi</h3>
                                
                                <!-- Current Status -->
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-500">Status Saat Ini</label>
                                    <div class="mt-1">
                                        <span :class="getStatusBadgeClass(permohonan.status)" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                                            {{ getStatusLabel(permohonan.status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Update Status Form -->
                                <form @submit.prevent="updateStatus" class="space-y-4">
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700">Update Status</label>
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        >
                                            <option value="pending">Pending</option>
                                            <option value="processed">Diproses</option>
                                            <option value="completed">Selesai</option>
                                            <option value="rejected">Ditolak</option>
                                        </select>
                                        <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</div>
                                    </div>

                                    <div>
                                        <label for="admin_notes" class="block text-sm font-medium text-gray-700">Catatan Admin</label>
                                        <textarea
                                            id="admin_notes"
                                            v-model="form.admin_notes"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Tambahkan catatan untuk pemohon..."
                                        ></textarea>
                                        <div v-if="form.errors.admin_notes" class="mt-1 text-sm text-red-600">{{ form.errors.admin_notes }}</div>
                                    </div>

                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                    >
                                        <span v-if="form.processing">Menyimpan...</span>
                                        <span v-else>Update Status</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline</h3>
                                <div class="flow-root">
                                    <ul role="list" class="-mb-8">
                                        <li v-for="(log, index) in permohonan.logs" :key="log.id">
                                            <div class="relative pb-8">
                                                <span v-if="index !== permohonan.logs.length - 1 || true" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
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
                                                                menerima permohonan
                                                            </p>
                                                        </div>
                                                        <div class="text-right text-xs text-gray-500 whitespace-nowrap">
                                                            {{ formatDate(permohonan.created_at) }}
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
    permohonan: Object,
});

const form = useForm({
    status: props.permohonan.status,
    admin_notes: props.permohonan.admin_notes || '',
});

const updateStatus = () => {
    form.patch(route('admin.ppid.permohonan-informasi.update', props.permohonan.id), {
        preserveScroll: true,
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

const getFileName = (path) => {
    return path ? path.split('/').pop() : '';
};

// Helper to check if file is image
const isImageFile = (path) => {
    if (!path) return false;
    return /\.(jpg|jpeg|png|webp)$/i.test(getFileName(path));
};
</script>