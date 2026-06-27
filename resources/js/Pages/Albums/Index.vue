<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import SplitButton from 'primevue/splitbutton';
import { computed, ref } from 'vue';
import useTranslations from '@/Composables/useTranslations.js';
const { t } = useTranslations();
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const props = defineProps({
    albums: { type: Object, required: true },
    filters: { type: Object, default: () => ({ search: '' }) },
});

// Helper to choose locale-specific value with fallback to Indonesian then raw
const localizeField = (translations, fallback) => {
    try {
        const appLocale = (window?.__app?.i18n?.locale) || 'id';
        if (translations && typeof translations === 'object') {
            return translations[appLocale] || translations.id || fallback || '';
        }
        return fallback || '';
    } catch (e) {
        return fallback || '';
    }
};

const search = ref(props.filters?.search ?? '');
const rows = ref(props.albums.meta?.per_page ?? props.filters?.per_page ?? 10);
const totalRecords = computed(() => props.albums.meta?.total ?? 0);
const first = computed(() => ((props.albums.meta?.current_page ?? 1) - 1) * (props.albums.meta?.per_page ?? rows.value));

const page = usePage();
const dateLocale = computed(() => (page.props?.i18n?.locale === 'en' ? 'en-US' : 'id-ID'));

function goToCreate() {
    router.visit(route('albums.create'));
}

function onSearch() {
    router.visit(route('albums.index'), {
        method: 'get',
        data: {
            search: search.value || undefined,
            per_page: rows.value,
        },
        preserveScroll: true,
        preserveState: true,
    });
}

function onPage(event) {
    // PrimeVue sends zero-based `page`
    rows.value = event.rows;
    const targetPage = (event.page ?? Math.floor(event.first / event.rows)) + 1;
    router.visit(route('albums.index'), {
        method: 'get',
        data: {
            page: targetPage,
            search: search.value || undefined,
            per_page: rows.value,
        },
        preserveScroll: true,
        preserveState: true,
    });
}

function getActionItems(row) {
    return [
        {
            label: t('albums.admin.edit'),
            icon: 'pi pi-pen-to-square',
            command: () => router.visit(route('albums.edit', row.id)),
        },
        {
            label: t('albums.admin.delete'),
            icon: 'pi pi-trash',
            command: async () => {
                const result = await Swal.fire({
                    title: t('albums.admin.confirm_delete_title'),
                    text: t('albums.admin.confirm_delete_text'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: t('albums.admin.confirm_delete_confirm'),
                    cancelButtonText: t('albums.admin.confirm_delete_cancel'),
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                });

                if (result.isConfirmed) {
                    router.delete(route('albums.destroy', row.id), {
                        preserveScroll: true,
                        onSuccess: () => Swal.fire({
                            icon: 'success',
                            title: t('albums.admin.deleted_success_title'),
                            text: t('albums.admin.deleted_success_text'),
                            timer: 1200,
                            showConfirmButton: false,
                        }),
                        onError: () => Swal.fire({
                            icon: 'error',
                            title: t('albums.admin.deleted_error_title'),
                            text: t('albums.admin.deleted_error_text'),
                            showConfirmButton: true,
                        }),
                    });
                }
            },
        },
    ];
}

function formatDateLocalized(dateInput) {
    const d = new Date(dateInput);
    if (isNaN(d.getTime())) return '-';
    return d.toLocaleDateString(dateLocale.value, { day: 'numeric', month: 'long', year: 'numeric' });
}
</script>

<template>

    <Head :title="t('menu.gallery')" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ t('menu.gallery') }}</h2>
                <div class="flex gap-2">
                    <input v-model="search" @keyup.enter="onSearch" type="text" :placeholder="t('albums.admin.search_placeholder')"
                        class="rounded border px-3 py-2 border-gray-300" />

                    <button @click="onSearch"
                        class="inline-flex items-center rounded bg-gray-800 px-3 py-2 text-white hover:bg-gray-900"><span><i
                                class="pi pi-search"></i></span></button>

                    <button @click="goToCreate"
                        class="inline-flex gap-2 items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">
                        <span><i class="pi pi-plus-circle"></i></span>
                        <span>{{ t('albums.admin.add_button') }}</span></button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <DataTable
                                :value="props.albums.data"
                                paginator
                                lazy
                                :rows="rows"
                                :rowsPerPageOptions="[5, 10, 20, 50]"
                                :totalRecords="totalRecords"
                                :first="first"
                                size="small"
                                removableSort
                                tableStyle="min-width: 50rem"
                                stripedRows
                                sortField="updated_at"
                                :sortOrder="-1"
                                @page="onPage"
                                @sort="onSort"
                                rowHover
                            >
                                <Column header="#">
                                    <template #body="slotProps">
                                        {{ (props.albums.meta.per_page * (props.albums.meta.current_page - 1)) +
                                            slotProps.index + 1 }}
                                    </template>
                                </Column>
                                <Column :header="t('albums.admin.columns.image')">
                                    <template #body="slotProps">
                                        <img :src="slotProps.data.image || '/images/default-cover.png'"
                                             alt="Gambar"
                                             class="h-12 w-20 object-cover rounded-md border border-gray-200" />
                                    </template>
                                </Column>
                                <Column :header="t('albums.admin.columns.title')">
                                    <template #body="slotProps">
                                        {{ localizeField(slotProps.data.title_translations, slotProps.data.title) }}
                                    </template>
                                </Column>
                                <Column field="images_count" :header="t('albums.admin.columns.total_images')" class="text-right"></Column>
                                <Column :header="t('albums.admin.columns.published')">
                                    <template #body="slotProps">
                                        <div class="flex items-center justify-center">
                                            <i v-if="slotProps.data.published"
                                                class="pi pi-check-circle text-green-600"></i>
                                            <i v-else class="pi pi-times-circle text-red-600"></i>
                                        </div>
                                    </template>
                                </Column>
                                <Column field="updated_at" :header="t('albums.admin.columns.updated_at')" style="width: 200px;" sortable>
                                    <template #body="slotProps">
                                        <div class="flex items-center justify-start text-sm">
                                            {{ formatDateLocalized(slotProps.data.updated_at) }}
                                        </div>
                                    </template>
                                </Column>
                                <Column :header="t('albums.admin.actions')">
                                    <template #body="slotProps">
                                        <SplitButton :label="t('albums.admin.view')" size="small" severity="info" icon="pi pi-eye"
                                            @click="() => router.visit(route('albums.show', slotProps.data.id))"
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