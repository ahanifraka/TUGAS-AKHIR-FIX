<template>
    <Head title="Dashboard PPID Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard PPID Admin
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Permohonan Informasi Stats -->
                    <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="text-blue-500 pi pi-info-circle" style="font-size: 2rem"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-800 truncate">
                                            Total Permohonan Informasi
                                        </dt>
                                        <dd class="text-lg font-bold text-gray-900">
                                            {{ permohonanStats.total }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pengajuan Keberatan Stats -->
                    <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="text-red-500 pi pi-exclamation-triangle" style="font-size: 2rem"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-800 truncate">
                                            Total Pengajuan Keberatan
                                        </dt>
                                        <dd class="text-lg font-bold text-gray-900">
                                            {{ keberatanStats.total }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Items -->
                    <div class="bg-amber-50 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="text-amber-500 pi pi-clock" style="font-size: 2rem"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-800 truncate">
                                            Menunggu Proses
                                        </dt>
                                        <dd class="text-lg font-bold text-gray-900">
                                            {{ permohonanStats.pending + keberatanStats.pending }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Items -->
                    <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="text-green-500 pi pi-check-circle" style="font-size: 2rem"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-800 truncate">
                                            Selesai
                                        </dt>
                                        <dd class="text-lg font-bold text-gray-900">
                                            {{ permohonanStats.completed + keberatanStats.completed }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Permohonan Informasi</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Pending</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ permohonanStats.pending }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Diproses</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ permohonanStats.processed }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Selesai</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ permohonanStats.completed }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Ditolak</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ permohonanStats.rejected }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <Link :href="route('admin.ppid.permohonan-informasi')" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-hover focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Kelola Permohonan
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Pengajuan Keberatan</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Pending</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ keberatanStats.pending }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Diproses</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ keberatanStats.processed }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Selesai</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ keberatanStats.completed }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Ditolak</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ keberatanStats.rejected }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <Link :href="route('admin.ppid.pengajuan-keberatan')" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-hover focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Kelola Keberatan
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Submissions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Permohonan Informasi -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Permohonan Informasi Terbaru</h3>
                            <div class="space-y-3">
                                <div v-for="item in recentPermohonan" :key="item.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ item.nama }}</p>
                                        <p class="text-xs text-gray-500">{{ formatDate(item.created_at) }}</p>
                                    </div>
                                    <span :class="getStatusBadgeClass(item.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ getStatusLabel(item.status) }}
                                    </span>
                                </div>
                                <div v-if="recentPermohonan.length === 0" class="text-center py-4 text-gray-500">
                                    Belum ada permohonan informasi
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Pengajuan Keberatan -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Pengajuan Keberatan Terbaru</h3>
                            <div class="space-y-3">
                                <div v-for="item in recentKeberatan" :key="item.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ item.nama }}</p>
                                        <p class="text-xs text-gray-500">{{ formatDate(item.created_at) }}</p>
                                    </div>
                                    <span :class="getStatusBadgeClass(item.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ getStatusLabel(item.status) }}
                                    </span>
                                </div>
                                <div v-if="recentKeberatan.length === 0" class="text-center py-4 text-gray-500">
                                    Belum ada pengajuan keberatan
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
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    permohonanStats: Object,
    keberatanStats: Object,
    recentPermohonan: Array,
    recentKeberatan: Array,
});

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
</script>