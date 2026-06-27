<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// Props
const props = defineProps({
    pejabats: { type: Array, required: true },
});

// Create a reactive copy of pejabats for reordering
const orderedPejabats = ref([...props.pejabats]);

// Drag and drop state
const draggedItem = ref(null);
const draggedOverIndex = ref(null);

// Form for submitting the new order
const form = useForm({
    pejabats: []
});

// Handle drag start
function onDragStart(event, index) {
    draggedItem.value = index;
    event.dataTransfer.effectAllowed = 'move';
}

// Handle drag over
function onDragOver(event, index) {
    event.preventDefault();
    draggedOverIndex.value = index;
}

// Handle drag leave
function onDragLeave() {
    draggedOverIndex.value = null;
}

// Handle drop
function onDrop(event, dropIndex) {
    event.preventDefault();
    
    if (draggedItem.value === null || draggedItem.value === dropIndex) {
        draggedItem.value = null;
        draggedOverIndex.value = null;
        return;
    }

    // Reorder the items
    const draggedItemData = orderedPejabats.value[draggedItem.value];
    orderedPejabats.value.splice(draggedItem.value, 1);
    orderedPejabats.value.splice(dropIndex, 0, draggedItemData);

    // Update order values
    orderedPejabats.value.forEach((item, index) => {
        item.order = index + 1;
    });

    // Reset drag state
    draggedItem.value = null;
    draggedOverIndex.value = null;
}

// Handle drag end
function onDragEnd() {
    draggedItem.value = null;
    draggedOverIndex.value = null;
}

// Move item up
function moveUp(index) {
    if (index > 0) {
        const item = orderedPejabats.value[index];
        orderedPejabats.value.splice(index, 1);
        orderedPejabats.value.splice(index - 1, 0, item);
        
        // Update order values
        orderedPejabats.value.forEach((item, index) => {
            item.order = index + 1;
        });
    }
}

// Move item down
function moveDown(index) {
    if (index < orderedPejabats.value.length - 1) {
        const item = orderedPejabats.value[index];
        orderedPejabats.value.splice(index, 1);
        orderedPejabats.value.splice(index + 1, 0, item);
        
        // Update order values
        orderedPejabats.value.forEach((item, index) => {
            item.order = index + 1;
        });
    }
}

// Save the new order
function saveOrder() {
    form.pejabats = orderedPejabats.value.map((item, index) => ({
        id: item.id,
        order: index + 1
    }));

    form.post(route('pejabats.update-order'), {
        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil',
                text: 'Urutan pejabat berhasil diperbarui.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
            });
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join('\n');
            Swal.fire({
                title: 'Gagal',
                text: errorMessages || 'Terjadi kesalahan saat memperbarui urutan.',
                icon: 'error',
                showConfirmButton: true,
            });
        },
    });
}

// Cancel and go back
function cancel() {
    router.visit(route('pejabats.index'));
}

// Check if order has changed
const hasChanges = computed(() => {
    const result = orderedPejabats.value.some((item, index) => {
        const originalIndex = props.pejabats.findIndex(p => p.id === item.id);
        const hasChanged = originalIndex !== index;
        console.log(`Item ${item.id}: original index = ${originalIndex}, current index = ${index}, changed = ${hasChanged}`);
        return hasChanged;
    });
    console.log('hasChanges result:', result);
    return result;
});
</script>

<template>
    <Head title="Atur Urutan Pejabat" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold leading-tight text-gray-800">Atur Urutan Pejabat</h1>
                <div class="flex gap-2">
                    <button @click="cancel"
                        class="inline-flex items-center rounded bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">
                        <i class="pi pi-times mr-2"></i>
                        Batal
                    </button>
                    <button @click="saveOrder" :disabled="!hasChanges || form.processing"
                        class="inline-flex items-center rounded bg-primary px-4 py-2 text-white hover:bg-primary-hover disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="pi pi-save mr-2"></i>
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Simpan Urutan</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-4 text-sm text-gray-600">
                            <p><i class="pi pi-info-circle mr-1"></i> Seret dan lepas item untuk mengatur urutan, atau gunakan tombol panah. Hanya pejabat yang dipublikasikan yang dapat diurutkan.</p>
                        </div>
                        
                        <div class="space-y-2">
                            <div
                                v-for="(pejabat, index) in orderedPejabats"
                                :key="pejabat.id"
                                :class="[
                                    'flex items-center justify-between p-4 border rounded-lg cursor-move transition-all duration-200',
                                    draggedOverIndex === index ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:border-gray-300',
                                    draggedItem === index ? 'opacity-50' : ''
                                ]"
                                draggable="true"
                                @dragstart="onDragStart($event, index)"
                                @dragover="onDragOver($event, index)"
                                @dragleave="onDragLeave"
                                @drop="onDrop($event, index)"
                                @dragend="onDragEnd"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full text-sm font-medium">
                                        {{ index + 1 }}
                                    </div>
                                    <img :src="pejabat.image_url || '/images/default-cover.png'"
                                        class="w-12 h-12 rounded-full object-cover object-top" />
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ pejabat.nama }}</h3>
                                        <p class="text-sm text-gray-500">{{ pejabat.jabatan }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-grip-vertical text-gray-400 mr-2"></i>
                                    <button
                                        @click="moveUp(index)"
                                        :disabled="index === 0"
                                        class="p-2 text-gray-500 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed"
                                        title="Pindah ke atas"
                                    >
                                        <i class="pi pi-chevron-up"></i>
                                    </button>
                                    <button
                                        @click="moveDown(index)"
                                        :disabled="index === orderedPejabats.length - 1"
                                        class="p-2 text-gray-500 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed"
                                        title="Pindah ke bawah"
                                    >
                                        <i class="pi pi-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="orderedPejabats.length === 0" class="text-center p-8 text-gray-500">
                            <i class="pi pi-inbox text-4xl text-gray-400 block mb-3"></i>
                            <p>Tidak ada pejabat yang dipublikasikan untuk diurutkan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.cursor-move {
    cursor: move;
}

.cursor-move:active {
    cursor: grabbing;
}
</style>