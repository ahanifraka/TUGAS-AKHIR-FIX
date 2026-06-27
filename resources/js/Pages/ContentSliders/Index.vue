<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';

// PrimeVue
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import SplitButton from 'primevue/splitbutton';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useSweetAlert } from '@/Composables/useSweetAlert.js';

const swal = useSweetAlert();

// Props
const props = defineProps({
    sliders: { type: Object, required: true },
    filters: { type: Object, default: () => ({ search: '' }) },
});

const search = ref(props.filters?.search ?? '');
// Set initial sort state from backend filters
const sortField = ref(props.filters?.sort_by ?? 'updated_at');
const sortOrder = ref(props.filters?.sort_dir === 'asc' ? 1 : -1);
// Track rows per page from server/meta or filters
const rows = ref(props.filters?.per_page ?? props.sliders?.meta?.per_page ?? 10);

// Track image loading states
const imageLoadingStates = ref({});
const imageErrorStates = ref({});

// Handle image load
function onImageLoad(sliderId) {
    imageLoadingStates.value[sliderId] = false;
}

// Handle image error
function onImageError(sliderId) {
    imageLoadingStates.value[sliderId] = false;
    imageErrorStates.value[sliderId] = true;
}

// Initialize loading state
function initImageLoading(sliderId) {
    if (imageLoadingStates.value[sliderId] === undefined) {
        imageLoadingStates.value[sliderId] = true;
    }
}

// Zoom image with SweetAlert2
function zoomImage(row) {
    const imageUrl = row.image_url || row.image;
    if (!imageUrl) return;
    
    // Show loading modal first
    swal.loading();
    
    // Preload image
    const img = new Image();
    img.onload = function() {
        // Calculate available viewport space (accounting for modal padding and buttons)
        const maxWidth = window.innerWidth * 0.85; // 85% of viewport width
        const maxHeight = window.innerHeight * 0.75; // 75% of viewport height
        
        // Calculate image dimensions while maintaining aspect ratio
        let displayWidth = img.naturalWidth;
        let displayHeight = img.naturalHeight;
        
        // Scale down if image is larger than max dimensions
        if (displayWidth > maxWidth) {
            const ratio = maxWidth / displayWidth;
            displayWidth = maxWidth;
            displayHeight = displayHeight * ratio;
        }
        
        if (displayHeight > maxHeight) {
            const ratio = maxHeight / displayHeight;
            displayHeight = maxHeight;
            displayWidth = displayWidth * ratio;
        }
        
        swal.custom({
            imageUrl,
            imageAlt: row.title || 'Slider Image',
            showConfirmButton: true,
            confirmButtonText: 'Close',
            showCloseButton: true,
            width: 'auto',
            padding: '1rem',
            backdrop: true,
            imageWidth: Math.floor(displayWidth),
            imageHeight: Math.floor(displayHeight),
            customClass: {
                popup: 'swal-zoom-popup',
                image: 'swal-zoom-image'
            },
            didOpen: (popup) => {
                const imgElement = popup.querySelector('.swal2-image');
                if (imgElement) {
                    imgElement.style.objectFit = 'contain';
                    imgElement.style.cursor = 'zoom-out';
                    imgElement.style.maxWidth = '85vw';
                    imgElement.style.maxHeight = '75vh';
                }
                
                // Add custom CSS to ensure modal doesn't overflow
                const style = document.createElement('style');
                style.textContent = `
                    .swal-zoom-popup {
                        max-width: 90vw !important;
                        max-height: 90vh !important;
                        overflow: auto;
                    }
                    .swal-zoom-image {
                        display: block;
                        margin: 0 auto;
                    }
                `;
                document.head.appendChild(style);
            },
            willClose: () => {
                // Clean up custom styles
                const styles = document.querySelectorAll('style');
                styles.forEach(style => {
                    if (style.textContent.includes('.swal-zoom-popup')) {
                        style.remove();
                    }
                });
            }
        });
    };
    
    img.onerror = function() {
        swal.error('Failed to load image', 'Error');
    };
    
    img.src = imageUrl;
}

function goToCreate() {
    router.visit(route('content-sliders.create'));
}

function onSearch() {
    router.visit(route('content-sliders.index', {
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
    router.visit(route('content-sliders.index', {
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
    router.visit(route('content-sliders.index', {
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
            command: () => router.visit(route('content-sliders.edit', row.id)),
        },
        {
            label: 'Delete',
            icon: 'pi pi-trash',
            command: async () => {
                const confirmed = await swal.confirmDelete('Tindakan ini tidak bisa dibatalkan.');
                if (confirmed) {
                    router.delete(route('content-sliders.destroy', row.id), {
                        preserveScroll: true,
                        onSuccess: () => {
                            swal.success('Slider berhasil dihapus.');
                        },
                        onError: (errors) => {
                            const errorMessages = Object.values(errors).flat().join('\n');
                            swal.error(errorMessages || 'Terjadi kesalahan saat menghapus slider.');
                        },
                    });
                }
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

    <Head title="Sliders" />


    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold leading-tight text-gray-800">Sliders</h1>
                <div class="flex gap-2">
                    <input v-model="search" @keyup.enter="onSearch" type="text" placeholder="Cari judul..."
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
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <DataTable :value="props.sliders.data" paginator :rows="rows"
                                :rowsPerPageOptions="[5, 10, 20, 50]" :totalRecords="props.sliders.meta.total"
                                :first="props.sliders.meta.per_page * (props.sliders.meta.current_page - 1)"
                                tableStyle="min-width: 40rem" size="small" stripedRows removableSort rowHover
                                :sortField="sortField" :sortOrder="sortOrder" @sort="onSort" lazy @page="onPage"
                                :rowClass="(data) => data.is_active_highlight ? 'bg-yellow-50 border-l-4 border-yellow-400' : ''">
                                <Column header="#">
                                    <template #body="slotProps">
                                        <div class="flex items-center gap-2">
                                            <span>{{ (props.sliders.meta.per_page * (props.sliders.meta.current_page - 1)) +
                                                slotProps.index + 1 }}</span>
                                            <i v-if="slotProps.data.is_active_highlight" 
                                               class="pi pi-star-fill text-yellow-500 text-sm" 
                                               title="Activea Slider"></i>
                                        </div>
                                    </template>
                                </Column>
                                <Column header="Preview">
                                    <template #body="slotProps">
                                        <div class="relative h-12 w-20">
                                            <!-- Loading skeleton -->
                                            <div v-if="imageLoadingStates[slotProps.data.id] !== false"
                                                class="absolute inset-0 bg-gray-200 animate-pulse rounded"></div>
                                            
                                            <!-- Error state -->
                                            <div v-if="imageErrorStates[slotProps.data.id]"
                                                class="absolute inset-0 flex items-center justify-center bg-gray-100 rounded border border-gray-200">
                                                <i class="pi pi-image text-gray-400 text-xl"></i>
                                            </div>
                                            
                                            <!-- Image -->
                                            <img :src="slotProps.data.image_url || slotProps.data.image"
                                                :alt="slotProps.data.title"
                                                loading="lazy"
                                                :class="[
                                                    'h-12 w-20 object-cover rounded cursor-zoom-in border border-gray-200 transition-opacity duration-200',
                                                    imageLoadingStates[slotProps.data.id] === false && !imageErrorStates[slotProps.data.id] ? 'opacity-100' : 'opacity-0'
                                                ]"
                                                @load="onImageLoad(slotProps.data.id)"
                                                @error="onImageError(slotProps.data.id)"
                                                @click="!imageErrorStates[slotProps.data.id] && zoomImage(slotProps.data)"
                                                v-show="!imageErrorStates[slotProps.data.id]" />
                                        </div>
                                    </template>
                                </Column>
                                <Column field="title" header="Judul"></Column>
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
                                            @click="() => router.visit(route('content-sliders.show', slotProps.data.id))"
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