<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    upload: { type: Object, required: true },
});
</script>

<template>
    <Head title="Detail Upload" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail Upload</h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-4">
                            <img :src="props.upload.cover_thumbnail" alt="cover" class="h-24 w-24 rounded object-cover" />
                            <div>
                                <div class="text-lg font-semibold">{{ props.upload.title }}</div>
                                <div class="text-sm text-gray-600">Periode: {{ props.upload.periode || '-' }}</div>
                                <div class="text-sm text-gray-600">Akses: {{ props.upload.akses }}</div>
                                <div class="text-sm text-gray-600">BUMD: {{ props.upload.bumd_nama }} ({{ props.upload.kode_bumd }})</div>
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-medium">Keterangan</div>
                            <div class="text-gray-800 whitespace-pre-line">{{ props.upload.keterangan || '-' }}</div>
                        </div>

                        <div class="text-sm text-gray-600">Dibuat: {{ props.upload.created_at }}</div>
                        <div class="text-sm text-gray-600">Diupdate: {{ props.upload.updated_at }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>