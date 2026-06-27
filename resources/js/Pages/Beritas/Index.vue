<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import SplitButton from 'primevue/splitbutton';
import { computed, ref } from 'vue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';


const props = defineProps({
    beritas: { type: Object, required: true },
    filters: { type: Object, default: () => ({ search: '' }) },
});

const search = ref(props.filters?.search ?? '');
// Initial sort and rows-per-page from backend
const sortField = ref(props.filters?.sort_by ?? 'updated_at');
const sortOrder = ref(props.filters?.sort_dir === 'asc' ? 1 : -1);
const rows = ref(props.filters?.per_page ?? props.beritas?.meta?.per_page ?? 10);

function goToCreate() {
    router.visit(route('beritas.create'));
}

function onSearch() {
    router.visit(route('beritas.index', {
        search: search.value || undefined,
        sort_by: sortField.value || undefined,
        sort_dir: sortOrder.value === 1 ? 'asc' : 'desc',
        per_page: rows.value,
    }));
}

// Handle remote sort
function onSort(event) {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;
    router.visit(route('beritas.index', {
        search: search.value || undefined,
        sort_by: event.sortField,
        sort_dir: event.sortOrder === 1 ? 'asc' : 'desc',
        per_page: rows.value,
    }), { preserveState: true, preserveScroll: true });
}

// Handle server-side pagination and rows-per-page
function onPage(event) {
    const page = event.page !== undefined ? event.page + 1 : Math.floor(event.first / event.rows) + 1;
    rows.value = event.rows;
    router.visit(route('beritas.index', {
        search: search.value || undefined,
        sort_by: sortField.value || undefined,
        sort_dir: sortOrder.value === 1 ? 'asc' : 'desc',
        page,
        per_page: event.rows,
    }), { preserveState: true, preserveScroll: true });
}

// Add action dropdown items per row
function getActionItems(row) {
    return [
        {
            label: 'Edit',
            icon: 'pi pi-pen-to-square',
            command: () => router.visit(route('beritas.edit', row.id)),
        },
        {
            label: 'Delete',
            icon: 'pi pi-trash',
            command: () => {
                Swal.fire({
                    title: 'Hapus berita ini?',
                    text: 'Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        router.delete(route('beritas.destroy', row.id), {
                            onSuccess: () => {
                                Swal.fire({
                                    title: 'Berita berhasil dihapus',
                                    icon: 'success',
                                    timer: 1600,
                                    showConfirmButton: false,
                                });
                            },
                            onError: (e) => {
                                const firstMsg = Object.values(e || {}).find((v) => typeof v === 'string');
                                Swal.fire({
                                    title: 'Gagal menghapus',
                                    text: firstMsg || 'Terjadi kesalahan. Coba lagi.',
                                    icon: 'error',
                                });
                            },
                        });
                    }
                });
            },
        },
    ];
}

function formatDateID(dateInput) {
    const d = new Date(dateInput);
    if (isNaN(d.getTime())) return '-';
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

</script>

<template>

    <Head title="Berita" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row items-center justify-between gap-y-8">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Berita</h2>

                <div class="flex flex-col md:flex-row gap-2">
                    <div class="flex gap-2">
                        <input v-model="search" @keyup.enter="onSearch" type="text" placeholder="Cari judul/konten..."
                            class="rounded border border-gray-300 px-3 py-2" />

                        <button @click="onSearch"
                            class="inline-flex items-center rounded bg-gray-800 px-3 py-2 text-white hover:bg-gray-900"><span><i
                                    class="pi pi-search"></i></span></button>
                    </div>

                    <div class="flex gap-2">
                        <button @click="goToCreate"
                            class="inline-flex gap-2 items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">
                            <span><i class="pi pi-plus-circle"></i></span>
                            <span>Tambah</span></button>

                        <Link :href="route('berita-categories.index')"
                            class="inline-flex gap-2 items-center rounded bg-gray-200 px-3 py-2 text-gray-900 hover:bg-gray-300">
                        <span><i class="pi pi-th-large"></i></span>
                        <span>Berita Kategori</span></Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6">

            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">


                        <div class="overflow-x-auto">

                            <DataTable :value="props.beritas.data" paginator :rows="rows" size="small"
                                :rowsPerPageOptions="[5, 10, 20, 50]" :totalRecords="props.beritas.meta.total"
                                responsiveLayout="scroll"
                                :first="props.beritas.meta.per_page * (props.beritas.meta.current_page - 1)"
                                tableStyle="min-width: 50rem" stripedRows removableSort :sortField="sortField"
                                :sortOrder="sortOrder" lazy @page="onPage" @sort="onSort" rowHover>
                                <Column header="#">
                                    <template #body="slotProps">
                                        {{ (props.beritas.meta.per_page * (props.beritas.meta.current_page - 1))
                                            +
                                            slotProps.index + 1 }}
                                    </template>
                                </Column>
                                <Column>
                                    <template #body="slotProps">
                                        <img :src="slotProps.data.image || '/images/default-cover.png'" alt="Gambar"
                                            class="h-12 w-32 object-cover rounded-md border border-gray-200" />
                                    </template>
                                </Column>
                                <Column field="title" header="Judul"></Column>
                                <Column header="Kategori">
                                    <template #body="slotProps">
                                        {{ slotProps.data.category?.name ?? '-' }}
                                    </template>
                                </Column>
                                <Column header="Published">
                                    <template #body="slotProps">
                                        <div class="flex items-center justify-center">
                                            <i v-if="slotProps.data.published"
                                                class="pi pi-check-circle text-green-600"></i>
                                            <i v-else class="pi pi-times-circle text-red-600"></i>
                                        </div>
                                    </template>
                                </Column>
                                <Column field="popular" header="Popular" style="width: 140px;" sortable>
                                    <template #body="slotProps">
                                        <div class="flex items-center justify-center">
                                            <i v-if="slotProps.data.popular"
                                               class="pi pi-star-fill text-yellow-500"></i>
                                            <i v-else class="pi pi-star text-gray-400"></i>
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
                                            @click="() => router.visit(route('beritas.show', slotProps.data.id))"
                                            :model="getActionItems(slotProps.data)" />
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>