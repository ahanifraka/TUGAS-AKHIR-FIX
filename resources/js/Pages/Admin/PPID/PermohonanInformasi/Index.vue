<template>
    <Head title="Kelola Permohonan Informasi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kelola Permohonan Informasi
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
                            <div class="text-sm text-gray-600">Total</div>
                        </div>
                    </div>
                    <div class="bg-yellow-50 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</div>
                            <div class="text-sm text-gray-600">Pending</div>
                        </div>
                    </div>
                    <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ stats.processed }}</div>
                            <div class="text-sm text-gray-600">Diproses</div>
                        </div>
                    </div>
                    <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ stats.completed }}</div>
                            <div class="text-sm text-gray-600">Selesai</div>
                        </div>
                    </div>
                    <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 text-center">
                            <div class="text-2xl font-bold text-red-600">{{ stats.rejected }}</div>
                            <div class="text-sm text-gray-600">Ditolak</div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form @submit.prevent="search" class="flex flex-wrap gap-4">
                            <div class="flex-1 min-w-64">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="Cari berdasarkan nama, telepon/email, atau rincian informasi..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div class="min-w-48">
                                <select
                                    v-model="form.status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="processed">Diproses</option>
                                    <option value="completed">Selesai</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Cari
                            </button>
                            <Link
                                :href="route('admin.ppid.permohonan-informasi')"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Reset
                            </Link>
                        </form>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kode Tracking
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kontak
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in permohonan.data" :key="item.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-mono font-semibold text-blue-600">{{ item.kode_unik }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ item.nama }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ item.no_telepon_email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getStatusBadgeClass(item.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            {{ getStatusLabel(item.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(item.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link
                                            :href="route('admin.ppid.permohonan-informasi.show', item.id)"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3"
                                        >
                                            Detail
                                        </Link>
                                        <a
                                            v-if="item.lampiran_path"
                                            :href="route('admin.ppid.permohonan-informasi.download', item.id)"
                                            class="text-green-600 hover:text-green-900"
                                            target="_blank"
                                        >
                                            Lampiran
                                        </a>
                                    </td>
                                </tr>
                                <tr v-if="permohonan.data.length === 0">
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data permohonan informasi
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="permohonan.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="permohonan.prev_page_url"
                                    :href="permohonan.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="permohonan.next_page_url"
                                    :href="permohonan.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing {{ permohonan.from }} to {{ permohonan.to }} of {{ permohonan.total }} results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <template v-for="(link, index) in permohonan.links" :key="index">
                                            <Link
                                                v-if="link.url"
                                                :href="link.url"
                                                :class="[
                                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                    link.active
                                                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                    index === 0 ? 'rounded-l-md' : '',
                                                    index === permohonan.links.length - 1 ? 'rounded-r-md' : ''
                                                ]"
                                                v-html="link.label"
                                            />
                                            <span
                                                v-else
                                                :class="[
                                                    'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500',
                                                    index === 0 ? 'rounded-l-md' : '',
                                                    index === permohonan.links.length - 1 ? 'rounded-r-md' : ''
                                                ]"
                                                v-html="link.label"
                                            />
                                        </template>
                                    </nav>
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
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    permohonan: Object,
    filters: Object,
    stats: Object,
});

const form = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
});

const search = () => {
    router.get(route('admin.ppid.permohonan-informasi'), form, {
        preserveState: true,
        replace: true,
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
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