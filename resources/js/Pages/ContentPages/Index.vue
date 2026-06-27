<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import SplitButton from 'primevue/splitbutton';
import Paginator from 'primevue/paginator';
import { computed, ref } from 'vue';

import { useSweetAlert } from '@/Composables/useSweetAlert';

const props = defineProps({
    pages: { type: Object, required: true },
    filters: { type: Object, default: () => ({ search: '' }) },
});

const search = ref(props.filters?.search ?? '');
const { confirmDelete } = useSweetAlert();

function goToCreate() {
    router.visit(route('content-pages.create'));
}

function onSearch() {
    router.visit(route('content-pages.index', { search: search.value || undefined }));
}

function getActionItems(row) {
    return [
        {
            label: 'Edit',
            icon: 'pi pi-pencil',
            command: () => router.visit(route('content-pages.edit', row.id)),
        },
        {
            label: 'Delete',
            icon: 'pi pi-trash',
            command: async () => {
                if (await confirmDelete()) {
                    router.delete(route('content-pages.destroy', row.id));
                }
            },
        },
    ];
}

const rows = computed(() => props.pages.meta?.per_page ?? 10);
const totalRecords = computed(() => props.pages.meta?.total ?? props.pages.data?.length ?? 0);
const first = computed(() => ((props.pages.meta?.current_page ?? 1) - 1) * rows.value);

function onPage(event) {
    const targetPage = (event.page ?? Math.floor(event.first / event.rows)) + 1;
    router.visit(route('content-pages.index', { page: targetPage, search: search.value || undefined }), { preserveScroll: true });
}

function openPublicPage(slug) {
    window.open(route('contentpages.showPublicBySlug', slug), '_blank');
}
</script>

<template>

    <Head title="Content Pages" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Pages</h2>
                <div class="flex gap-2">

                    <input v-model="search" @keyup.enter="onSearch" type="text" placeholder="Cari kode/nama..."
                        class="rounded border border-gray-300 px-3 py-2" />

                    <button @click="onSearch"
                        class="inline-flex items-center rounded bg-gray-800 px-3 py-2 text-white hover:bg-gray-900"><span><i
                                class="pi pi-search"></i></span></button>

                    <button @click="goToCreate"
                        class="inline-flex gap-2 items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">
                        <span><i class="pi pi-plus-circle"></i></span>
                        <span>Tambah</span></button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <DataTable :value="props.pages.data" tableStyle="min-width: 40rem" stripedRows>
                                <Column header="No.">
                                    <template #body="slotProps">
                                        {{ (props.pages.meta.per_page * (props.pages.meta.current_page - 1)) +
                                        slotProps.index + 1 }}
                                    </template>
                                </Column>
                                <Column field="title" header="Judul"></Column>
                                <Column field="slug" header="Slug"></Column>
                                <Column header="Published">
                                    <template #body="slotProps">
                                        <i v-if="slotProps.data.published" class="pi pi-check-circle text-green-600"></i>
                                        <i v-else class="pi pi-times-circle text-red-600"></i>
                                    </template>
                                </Column>
                                <Column field="updated_at" header="Update Terakhir"></Column>
                                <Column header="Actions">
                                    <template #body="slotProps">
                                        <SplitButton label="View" severity="info" icon="pi pi-eye"
                                            @click="() => openPublicPage(slotProps.data.slug)"
                                            :model="getActionItems(slotProps.data)" />
                                    </template>
                                </Column>
                            </DataTable>
                        </div>

                        <div class="mt-4">
                            <Paginator :rows="rows" :totalRecords="totalRecords" :first="first" @page="onPage" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>