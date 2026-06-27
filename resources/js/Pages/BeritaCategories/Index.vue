<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import SplitButton from 'primevue/splitbutton';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';

const props = defineProps({
    categories: { type: Object, required: true },
    filters: { type: Object, default: () => ({ search: '' }) },
});

const search = ref(props.filters?.search ?? '');

function goToCreate() {
    router.visit(route('berita-categories.create'));
}

function onSearch() {
    router.visit(route('berita-categories.index', { search: search.value || undefined }));
}

function getActionItems(row) {
    return [
        {
            label: 'Edit',
            icon: 'pi pi-pen-to-square',
            command: () => router.visit(route('berita-categories.edit', row.id)),
        },
        {
            label: 'Delete',
            icon: 'pi pi-trash',
            command: () => {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Apakah Anda yakin ingin menghapus kategori "${row.category_name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Menghapus...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        router.delete(route('berita-categories.destroy', row.id), {
                            onSuccess: () => {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Kategori berita berhasil dihapus.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                            },
                            onError: (errors) => {
                                let errorMessage = 'Terjadi kesalahan saat menghapus kategori.';
                                if (errors && Object.keys(errors).length > 0) {
                                    const firstError = Object.values(errors)[0];
                                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                                }
                                
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
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

    <Head title="Kategori Berita" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Kategori Berita</h2>
                <div class="flex gap-2">
                    <input v-model="search" @keyup.enter="onSearch" type="text" placeholder="Cari nama kategori..."
                        class="rounded border border-gray-200 px-3 py-2" />
                    <button @click="onSearch"
                        class="inline-flex items-center rounded bg-gray-800 px-3 py-2 text-white hover:bg-gray-900"><span><i
                                class="pi pi-search"></i></span></button>
                    <button @click="goToCreate"
                        class="inline-flex gap-2 items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover"><span><i
                                class="pi pi-plus-circle"></i></span>
                        <span>Tambah</span></button>

                    <Link :href="route('beritas.index')"
                        class="inline-flex gap-2 items-center rounded bg-gray-200 px-3 py-2 text-gray-900 hover:bg-gray-300">
                    <span><i class="pi pi-file"></i></span>
                    <span>Berita</span></Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <DataTable :value="props.categories.data" tableStyle="min-width: 40rem" stripedRows rowHover>
                                <Column header="No.">
                                    <template #body="slotProps">
                                        {{ (props.categories.meta.per_page * (props.categories.meta.current_page - 1)) +
                                            slotProps.index + 1 }}
                                    </template>
                                </Column>
                                <Column field="category_name" header="Nama Kategori"></Column>
                                <Column header="Update Terakhir">
                                    <template #body="slotProps">
                                        {{ formatDateID(slotProps.data.updated_at) }}
                                    </template>
                                </Column>
                                <Column header="Aktif">
                                    <template #body="slotProps">
                                        <i v-if="slotProps.data.is_active" class="pi pi-check-circle text-green-600"></i>
                                        <i v-else class="pi pi-times-circle text-red-600"></i>
                                    </template>
                                </Column>
                                <Column header="Actions">
                                    <template #body="slotProps">
                                        <SplitButton label="View" size="small" severity="info" icon="pi pi-eye"
                                            @click="() => router.visit(route('berita-categories.show', slotProps.data.id))"
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