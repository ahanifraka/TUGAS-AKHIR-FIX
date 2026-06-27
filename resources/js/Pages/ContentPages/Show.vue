<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    page: { type: Object, required: true },
});
</script>

<template>
    <Head :title="props.page.title" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ props.page.title }}</h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="font-medium">Slug:</span>
                                <span>{{ props.page.slug }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Published:</span>
                                <span>
                                    <i v-if="props.page.published" class="pi pi-check text-green-600"></i>
                                    <i v-else class="pi pi-times text-red-600"></i>
                                </span>
                            </div>
                        </div>

                        <div v-if="props.page.subtitle">
                            <span class="font-medium">Subtitle:</span>
                            <div class="mt-1" v-html="props.page.subtitle"></div>
                        </div>

                        <div>
                            <span class="font-medium">Deskripsi:</span>
                            <div class="mt-1" v-html="props.page.description"></div>
                        </div>

                        <div v-if="props.page.description2">
                            <span class="font-medium">Deskripsi 2:</span>
                            <div class="mt-1" v-html="props.page.description2"></div>
                        </div>

                        <div v-if="props.page.image">
                            <span class="font-medium">Image:</span>
                            <div class="mt-1">
                                <img :src="props.page.image.startsWith('http') ? props.page.image : '/' + props.page.image" alt="image" class="max-h-64" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="font-medium">Meta Title:</span>
                                <div class="mt-1">{{ props.page.metadata_title }}</div>
                            </div>
                            <div>
                                <span class="font-medium">Meta Keywords:</span>
                                <div class="mt-1">{{ props.page.metadata_keywords }}</div>
                            </div>
                            <div>
                                <span class="font-medium">Meta Description:</span>
                                <div class="mt-1">{{ props.page.metadata_description }}</div>
                            </div>
                        </div>

                        <div v-if="props.page.upload_files">
                            <span class="font-medium">Upload Files:</span>
                            <pre class="mt-1 whitespace-pre-wrap">{{ props.page.upload_files }}</pre>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="font-medium">Dibuat:</span>
                                <span>{{ props.page.created_at }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Diupdate:</span>
                                <span>{{ props.page.updated_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>