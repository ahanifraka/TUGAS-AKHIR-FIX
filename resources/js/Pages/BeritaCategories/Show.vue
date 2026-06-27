<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    category: { type: Object, required: true },
});
</script>

<template>

    <Head title="Detail Kategori" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail Berita Kategori</h2>
                <button @click="() => router.visit(route('berita-categories.index'))"
                    class="inline-flex items-center rounded bg-gray-200 px-3 py-2 hover:bg-gray-300">
                    <i class="pi pi-arrow-left mr-2" style="font-size: 0.75rem"></i>
                    Kembali</button>
            </div>
        </template>
        <div class="py-6">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Nama Kategori:</span>
                            <span>{{ props.category.category_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Slug:</span>
                            <span>{{ props.category.category_slug }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Aktif:</span>
                            <span>
                                <i v-if="props.category.is_active" class="pi pi-check-circle text-green-600"></i>
                                <i v-else class="pi pi-times-circle text-red-600"></i>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Dibuat:</span>
                            <span>{{ props.category.created_at }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Dibuat oleh:</span>
                            <span>{{ props.category.created_by?.name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Diupdate:</span>
                            <span>{{ props.category.updated_at }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Diupdate oleh:</span>
                            <span>{{ props.category.updated_by?.name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>