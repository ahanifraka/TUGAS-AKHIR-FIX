<!-- resources/js/Pages/Pengumuman/Index.vue -->
<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import SplitButton from "primevue/splitbutton";
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

const page = usePage();
const props = defineProps({
    pengumuman: { type: Object, required: true },
    tipeOptions: { type: Array, default: () => [] },
    filters: {
        type: Object,
        default: () => ({
            search: "",
            tipe: "",
            status: "",
            sort_by: "updated_at",
            sort_dir: "desc",
            per_page: 10,
        }),
    },
});

const search = ref(props.filters?.search ?? "");
const tipeFilter = ref(props.filters?.tipe ?? "");
const statusFilter = ref(props.filters?.status ?? "");
const sortField = ref(props.filters?.sort_by ?? "updated_at");
const sortOrder = ref(props.filters?.sort_dir === "asc" ? 1 : -1);
const rows = ref(
    props.filters?.per_page ?? props.pengumuman?.meta?.per_page ?? 10
);

function goToCreate() {
    router.visit(route("admin.pengumuman.create"));
}

function onSearch() {
    router.visit(
        route("admin.pengumuman.index", {
            search: search.value || undefined,
            tipe: tipeFilter.value || undefined,
            status: statusFilter.value || undefined,
            sort_by: sortField.value || undefined,
            sort_dir: sortOrder.value === 1 ? "asc" : "desc",
            per_page: rows.value,
        }),
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}

function resetFilters() {
    search.value = "";
    tipeFilter.value = "";
    statusFilter.value = "";
    sortField.value = "updated_at";
    sortOrder.value = -1;
    rows.value = 10;
    router.visit(route("admin.pengumuman.index"), {
        preserveState: true,
        preserveScroll: true,
    });
}

// Handle remote sort
function onSort(event) {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;
    router.visit(
        route("admin.pengumuman.index", {
            search: search.value || undefined,
            tipe: tipeFilter.value || undefined,
            status: statusFilter.value || undefined,
            sort_by: event.sortField,
            sort_dir: event.sortOrder === 1 ? "asc" : "desc",
            per_page: rows.value,
        }),
        { preserveState: true, preserveScroll: true }
    );
}

// Handle server-side pagination and rows-per-page
function onPage(event) {
    const page =
        event.page !== undefined
            ? event.page + 1
            : Math.floor(event.first / event.rows) + 1;
    rows.value = event.rows;
    router.visit(
        route("admin.pengumuman.index", {
            search: search.value || undefined,
            tipe: tipeFilter.value || undefined,
            status: statusFilter.value || undefined,
            sort_by: sortField.value || undefined,
            sort_dir: sortOrder.value === 1 ? "asc" : "desc",
            page,
            per_page: event.rows,
        }),
        { preserveState: true, preserveScroll: true }
    );
}

// Add action dropdown items per row
function getActionItems(row) {
    return [
        {
            label: "View",
            icon: "pi pi-eye",
            command: () => router.visit(route("admin.pengumuman.show", row.id)),
        },
        {
            label: "Edit",
            icon: "pi pi-pen-to-square",
            command: () => router.visit(route("admin.pengumuman.edit", row.id)),
        },
        {
            label: "Hapus",
            icon: "pi pi-trash",
            command: () => {
                Swal.fire({
                    title: "Hapus pengumuman ini?",
                    text: "Tindakan ini tidak dapat dibatalkan.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus",
                    cancelButtonText: "Batal",
                }).then((result) => {
                    if (result.isConfirmed) {
                        router.delete(
                            route("admin.pengumuman.destroy", row.id),
                            {
                                onSuccess: () => {
                                    Swal.fire({
                                        title: "Pengumuman berhasil dihapus",
                                        icon: "success",
                                        timer: 1600,
                                        showConfirmButton: false,
                                    });
                                },
                                onError: (e) => {
                                    const firstMsg = Object.values(
                                        e || {}
                                    ).find((v) => typeof v === "string");
                                    Swal.fire({
                                        title: "Gagal menghapus",
                                        text:
                                            firstMsg ||
                                            "Terjadi kesalahan. Coba lagi.",
                                        icon: "error",
                                    });
                                },
                            }
                        );
                    }
                });
            },
        },
    ];
}

function formatDateID(dateInput) {
    if (!dateInput) return "-";
    const d = new Date(dateInput);
    if (isNaN(d.getTime())) return "-";
    return d.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
}

function getTipeBadgeClass(tipe) {
    const classes = {
        pengumuman: "bg-blue-100 text-blue-800",
        pemberitahuan: "bg-yellow-100 text-yellow-800",
        undangan: "bg-purple-100 text-purple-800",
        lowongan: "bg-green-100 text-green-800",
        laporan: "bg-gray-100 text-gray-800",
        lainnya: "bg-indigo-100 text-indigo-800",
    };
    return classes[tipe] || "bg-gray-100 text-gray-800";
}

function getStatusBadge(isAktif) {
    return {
        class: isAktif
            ? "bg-green-100 text-green-800"
            : "bg-red-100 text-red-800",
        text: isAktif ? "Aktif" : "Nonaktif",
        icon: isAktif ? "pi pi-check-circle" : "pi pi-times-circle",
    };
}

function getPentingBadge(isPenting) {
    return {
        class: "bg-yellow-100 text-yellow-800",
        text: "Penting",
        icon: "pi pi-star",
    };
}
</script>

<template>
    <Head title="Pengumuman" />
    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col md:flex-row items-center justify-between gap-y-8"
            >
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Pengumuman
                </h2>

                <div class="flex flex-col md:flex-row gap-2">
                    <div class="flex gap-2">
                        <input
                            v-model="search"
                            @keyup.enter="onSearch"
                            type="text"
                            placeholder="Cari judul/konten..."
                            class="rounded border border-gray-300 px-3 py-2"
                        />

                        <select
                            v-model="tipeFilter"
                            @change="onSearch"
                            class="rounded border border-gray-300 px-3 py-2"
                        >
                            <option value="">Semua Tipe</option>
                            <option
                                v-for="tipe in tipeOptions"
                                :key="tipe"
                                :value="tipe"
                            >
                                {{
                                    tipe.charAt(0).toUpperCase() + tipe.slice(1)
                                }}
                            </option>
                        </select>

                        <select
                            v-model="statusFilter"
                            @change="onSearch"
                            class="rounded border border-gray-300 px-3 py-2"
                        >
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>

                        <button
                            @click="onSearch"
                            class="inline-flex items-center rounded bg-gray-800 px-3 py-2 text-white hover:bg-gray-900"
                        >
                            <i class="pi pi-search"></i>
                        </button>

                        <button
                            @click="resetFilters"
                            class="inline-flex items-center rounded bg-gray-200 px-3 py-2 text-gray-700 hover:bg-gray-300"
                        >
                            <i class="pi pi-filter-slash"></i>
                        </button>
                    </div>

                    <div class="flex gap-2">
                        <button
                            @click="goToCreate"
                            class="inline-flex gap-2 items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover"
                        >
                            <i class="pi pi-plus-circle"></i>
                            <span>Tambah</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <DataTable
                                :value="props.pengumuman.data"
                                paginator
                                :rows="rows"
                                size="small"
                                :rowsPerPageOptions="[5, 10, 20, 50]"
                                :totalRecords="props.pengumuman.meta.total"
                                responsiveLayout="scroll"
                                :first="
                                    props.pengumuman.meta.per_page *
                                    (props.pengumuman.meta.current_page - 1)
                                "
                                tableStyle="min-width: 50rem"
                                stripedRows
                                removableSort
                                :sortField="sortField"
                                :sortOrder="sortOrder"
                                lazy
                                @page="onPage"
                                @sort="onSort"
                                rowHover
                            >
                                <Column header="#" style="width: 60px">
                                    <template #body="slotProps">
                                        {{
                                            props.pengumuman.meta.per_page *
                                                (props.pengumuman.meta
                                                    .current_page -
                                                    1) +
                                            slotProps.index +
                                            1
                                        }}
                                    </template>
                                </Column>

                                <Column header="Gambar" style="width: 120px">
                                    <template #body="slotProps">
                                        <img
                                            :src="
                                                slotProps.data.gambar ||
                                                '/images/default-cover.png'
                                            "
                                            alt="Gambar"
                                            class="h-12 w-20 object-cover rounded-md border border-gray-200"
                                        />
                                    </template>
                                </Column>

                                <Column field="judul" header="Judul" sortable>
                                    <template #body="slotProps">
                                        <div class="font-medium text-gray-900">
                                            {{ slotProps.data.judul }}
                                        </div>
                                        <div
                                            v-if="
                                                slotProps.data.nomor_pengumuman
                                            "
                                            class="text-xs text-gray-500"
                                        >
                                            No:
                                            {{
                                                slotProps.data.nomor_pengumuman
                                            }}
                                        </div>
                                    </template>
                                </Column>

                                <Column
                                    field="tipe"
                                    header="Tipe"
                                    style="width: 120px"
                                    sortable
                                >
                                    <template #body="slotProps">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-xs font-medium rounded-full',
                                                getTipeBadgeClass(
                                                    slotProps.data.tipe
                                                ),
                                            ]"
                                        >
                                            {{
                                                slotProps.data.tipe
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                slotProps.data.tipe.slice(1)
                                            }}
                                        </span>
                                    </template>
                                </Column>

                                <Column
                                    field="tanggal_terbit"
                                    header="Tanggal Terbit"
                                    style="width: 140px"
                                    sortable
                                >
                                    <template #body="slotProps">
                                        <div class="text-sm">
                                            {{
                                                formatDateID(
                                                    slotProps.data
                                                        .tanggal_terbit
                                                )
                                            }}
                                        </div>
                                    </template>
                                </Column>

                                <Column header="Status" style="width: 100px">
                                    <template #body="slotProps">
                                        <div class="flex items-center gap-1">
                                            <i
                                                :class="{
                                                    'icon-class': true,
                                                    'text-green-600':
                                                        slotProps.data.is_aktif,
                                                    'text-red-600':
                                                        !slotProps.data
                                                            .is_aktif,
                                                }"
                                            ></i>
                                            <span
                                                :class="[
                                                    'px-2 py-1 text-xs rounded-full',
                                                    getStatusBadge(
                                                        slotProps.data.is_aktif
                                                    ).class,
                                                ]"
                                            >
                                                {{
                                                    getStatusBadge(
                                                        slotProps.data.is_aktif
                                                    ).text
                                                }}
                                            </span>
                                        </div>
                                    </template>
                                </Column>

                                <Column header="Penting" style="width: 100px">
                                    <template #body="slotProps">
                                        <div
                                            v-if="slotProps.data.is_penting"
                                            class="flex items-center gap-1"
                                        >
                                            <i
                                                class="pi pi-star text-yellow-500"
                                            ></i>
                                            <span
                                                :class="[
                                                    'px-2 py-1 text-xs rounded-full',
                                                    getPentingBadge(
                                                        slotProps.data
                                                            .is_penting
                                                    ).class,
                                                ]"
                                            >
                                                {{
                                                    getPentingBadge(
                                                        slotProps.data
                                                            .is_penting
                                                    ).text
                                                }}
                                            </span>
                                        </div>
                                        <div
                                            v-else
                                            class="text-gray-400 text-xs"
                                        >
                                            -
                                        </div>
                                    </template>
                                </Column>

                                <Column
                                    field="updated_at"
                                    header="Update Terakhir"
                                    style="width: 140px"
                                    sortable
                                >
                                    <template #body="slotProps">
                                        <div class="text-sm">
                                            {{
                                                formatDateID(
                                                    slotProps.data.updated_at
                                                )
                                            }}
                                        </div>
                                    </template>
                                </Column>

                                <Column header="Aksi" style="width: 150px">
                                    <template #body="slotProps">
                                        <SplitButton
                                            label="View"
                                            size="small"
                                            severity="info"
                                            icon="pi pi-eye"
                                            @click="
                                                () =>
                                                    router.visit(
                                                        route(
                                                            'admin.pengumuman.show',
                                                            slotProps.data.id
                                                        )
                                                    )
                                            "
                                            :model="
                                                getActionItems(slotProps.data)
                                            "
                                        />
                                    </template>
                                </Column>
                            </DataTable>
                        </div>

                        <!-- Summary Info -->
                        <div
                            class="mt-4 text-sm text-gray-600 flex items-center gap-4"
                        >
                            <div>
                                Menampilkan
                                {{ props.pengumuman.data.length }} dari
                                {{ props.pengumuman.meta.total }} pengumuman
                            </div>
                            <div
                                v-if="props.filters.search"
                                class="flex items-center gap-1"
                            >
                                <i class="pi pi-search"></i>
                                <span
                                    >Pencarian: "{{
                                        props.filters.search
                                    }}"</span
                                >
                            </div>
                            <div
                                v-if="props.filters.tipe"
                                class="flex items-center gap-1"
                            >
                                <i class="pi pi-tag"></i>
                                <span>Tipe: {{ props.filters.tipe }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
