<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';

// PrimeVue
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import SplitButton from 'primevue/splitbutton';
import Avatar from 'primevue/avatar';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// Props
const props = defineProps({
    pejabats: { type: Object, required: true },
    filters: { type: Object, default: () => ({ search: '' }) },
});

const search = ref(props.filters?.search ?? '');
// Set initial sort state from backend filters
const sortField = ref(props.filters?.sort_by ?? 'updated_at');
const sortOrder = ref(props.filters?.sort_dir === 'asc' ? 1 : -1);
// Track rows per page from server/meta or filters
const rows = ref(props.filters?.per_page ?? props.pejabats?.meta?.per_page ?? 10);

function goToCreate() {
    router.visit(route('pejabats.create'));
}

function goToReorder() {
    router.visit(route('pejabats.reorder'));
}

function onSearch() {
    router.visit(route('pejabats.index', {
        search: search.value || undefined,
        sort_by: sortField.value || undefined,
        sort_dir: sortOrder.value === 1 ? 'asc' : 'desc',
        per_page: rows.value,
    }));
}

// Add clear search handler
function clearSearch() {
    search.value = '';
    onSearch();
}

// Handle remote sort: send sort params to backend
function onSort(event) {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;
    router.visit(route('pejabats.index', {
        search: search.value || undefined,
        sort_by: event.sortField,
        sort_dir: event.sortOrder === 1 ? 'asc' : 'desc',
        per_page: rows.value,
    }), { preserveState: true, preserveScroll: true });
}

// Handle server-side pagination and rows-per-page changes
function onPage(event) {
    // PrimeVue event provides page (0-based) OR first/rows
    const page = event.page !== undefined ? event.page + 1 : Math.floor(event.first / event.rows) + 1;
    rows.value = event.rows;
    router.visit(route('pejabats.index', {
        search: search.value || undefined,
        sort_by: sortField.value || undefined,
        sort_dir: sortOrder.value === 1 ? 'asc' : 'desc',
        page,
        per_page: event.rows,
    }), { preserveState: true, preserveScroll: true });
}

function getActionItems(row) {
    return [
        {
            label: 'Edit',
            icon: 'pi pi-pen-to-square',
            command: () => router.visit(route('pejabats.edit', row.id)),
        },
        {
            label: 'Delete',
            icon: 'pi pi-trash',
            command: () => {
                Swal.fire({
                    title: `Yakin hapus pejabat "${row.nama}"?`,
                    text: 'Tindakan ini tidak bisa dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        router.delete(route('pejabats.destroy', row.id), {
                            preserveScroll: true,
                            onSuccess: () => {
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: 'Data pejabat berhasil dihapus.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                });
                            },
                            onError: (errors) => {
                                const errorMessages = Object.values(errors).flat().join('\n');
                                Swal.fire({
                                    title: 'Gagal',
                                    text: errorMessages || 'Terjadi kesalahan saat menghapus data.',
                                    icon: 'error',
                                    showConfirmButton: true,
                                });
                            },
                        });
                    }
                });
            },
        },
    ];
}

// Function to format date to Indonesian format
function formatDateID(dateInput) {
    const d = new Date(dateInput);
    if (isNaN(d.getTime())) return '-';
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}
</script>

<template>

    <Head title="Pejabat" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold leading-tight text-gray-800">Pejabat</h1>
                <div class="flex gap-2">
                    <input v-model="search" @keyup.enter="onSearch" type="text" placeholder="Cari nama..."
                        class="rounded border border-gray-300 px-3 py-2" autofocus />
                    <button @click="onSearch"
                        class="inline-flex items-center rounded bg-gray-800 px-3 py-2 text-white hover:bg-gray-900">
                        <span><i class="pi pi-search"></i></span>
                    </button>
                    <button v-if="search" @click="clearSearch"
                        class="inline-flex items-center rounded bg-gray-200 px-3 py-2 text-gray-700 hover:bg-gray-300">
                        <span><i class="pi pi-times"></i></span>
                    </button>
                    <button @click="goToCreate"
                        class="inline-flex gap-2 items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">
                        <span><i class="pi pi-plus-circle"></i></span>
                        <span>Tambah</span>
                    </button>
                    <button @click="goToReorder"
                        class="inline-flex gap-2 items-center rounded bg-blue-600 px-3 py-2 text-white hover:bg-blue-700">
                        <span><i class="pi pi-sync"></i></span>
                        <span>Atur Urutan</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <DataTable :value="props.pejabats.data" paginator :rows="rows"
                                :rowsPerPageOptions="[5, 10, 20, 50]" :totalRecords="props.pejabats.meta.total"
                                :first="props.pejabats.meta.per_page * (props.pejabats.meta.current_page - 1)"
                                tableStyle="min-width: 40rem" size="small" stripedRows removableSort rowHover
                                :sortField="sortField" :sortOrder="sortOrder" @sort="onSort" lazy @page="onPage">
                                <Column header="#">
                                    <template #body="slotProps">
                                        {{ (props.pejabats.meta.per_page * (props.pejabats.meta.current_page - 1)) +
                                            slotProps.index + 1 }}
                                    </template>
                                </Column>
                                <Column field="nama" header="Nama" style="width: 30%">
                                    <template #body="slotProps">
                                        <div class="flex items-center justify-start gap-x-2">
                                            <img
                                                :src="slotProps.data.image_url || '/images/default-cover.png'"
                                                class="w-12 h-12 rounded-full object-cover object-top"
                                                loading="lazy"
                                                @error="event => event.target.src = '/images/default-cover.png'"
                                            />
                                            <span class="text-gray-900">{{ slotProps.data.nama }}</span>

                                        </div>
                                    </template>
                                </Column>
                                <Column field="jabatan" header="Jabatan"></Column>
                                <Column header="Published">
                                    <template #body="slotProps">
                                        <div class="flex items-center justify-center">
                                            <i v-if="slotProps.data.published"
                                                class="pi pi-check-circle text-green-600"></i>
                                            <i v-else class="pi pi-times-circle text-red-600"></i>
                                        </div>
                                    </template>
                                </Column>
                                <Column field="updated_at" header="Update Terakhir" style="width: 200px;" sortable>
                                    <template #body="slotProps">
                                        <div class="flex items-center justify-start text-sm">
                                            {{ formatDateID(slotProps.data.updated_at) }}
                                        </div>
                                    </template>
                                </Column>
                                <Column header="Actions">
                                    <template #body="slotProps">
                                        <SplitButton label="View" size="small" severity="info" icon="pi pi-eye"
                                            @click="() => router.visit(route('pejabats.show', slotProps.data.id))"
                                            :model="getActionItems(slotProps.data)" />
                                    </template>
                                </Column>
                                <template #empty>
                                    <div class="text-center p-6 text-gray-500">
                                        <i class="pi pi-inbox text-4xl text-gray-400 block mb-3"></i>
                                        <b class="block text-lg mb-1">Data tidak ditemukan</b>
                                        <p>Belum ada data pejabat yang tersimpan.</p>
                                    </div>
                                </template>
                            </DataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>