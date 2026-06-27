<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    album: { type: Object, required: true },
    images: { type: Array, default: () => [] },
});

function deleteImage(imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
        router.delete(route('albums.images.destroy', [props.album.id, imageId]));
    }
}
</script>

<template>

    <Head title="Detail Album" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail Album</h2>
                <div class="flex gap-2">
                    <button @click="() => router.visit(route('albums.edit', album.id))"
                        class="inline-flex items-center rounded bg-gray-600 px-3 py-2 text-sm text-white hover:bg-gray-700">
                        Edit Album
                    </button>
                    <button @click="() => router.visit(route('albums.index'))"
                        class="inline-flex items-center rounded bg-gray-200 px-3 py-2 hover:bg-gray-300">
                        <i class="pi pi-arrow-left mr-2" style="font-size: 0.75rem"></i>
                        Kembali</button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="flex flex-col items-start gap-4">
                            <img v-if="album.image" :src="album.image" alt="cover"
                                class="w-full aspect-video rounded object-cover" />
                            <div>
                                <h3 class="text-2xl font-semibold mb-4">{{ album.title }}</h3>
                                <p class="text-gray-800">{{ album.description }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-900">Published:</span>
                            <i v-if="album.published" class="pi pi-check-circle text-green-600"></i>
                            <i v-else class="pi pi-times-circle text-red-600"></i>
                        </div>

                        <!-- Metadata Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                            <div>
                                <p class="text-sm text-gray-500">Created at</p>
                                <p class="text-sm font-medium text-gray-900">{{ album.created_at }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Updated at</p>
                                <p class="text-sm font-medium text-gray-900">{{ album.updated_at }}</p>
                            </div>
                            <div v-if="album.creator">
                                <p class="text-sm text-gray-500">Created by</p>
                                <p class="text-sm font-medium text-gray-900">{{ album.creator.name }}</p>
                            </div>
                            <div v-if="album.updater">
                                <p class="text-sm text-gray-500">Updated by</p>
                                <p class="text-sm font-medium text-gray-900">{{ album.updater.name }}</p>
                            </div>
                        </div>

                        <!-- Images Section -->
                        <div class="border-t pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Album Images ({{ images.length }})</h3>
                                <button @click="() => router.visit(route('albums.images.create', album.id))"
                                    class="inline-flex items-center rounded bg-primary px-3 py-2 text-sm text-white hover:bg-primary-hover">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Images
                                </button>
                            </div>

                            <div v-if="images.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="img in images" :key="img.id"
                                    class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <div class="aspect-w-16 aspect-h-9">
                                        <img :src="img.image" :alt="img.title" class="w-full h-48 object-cover" />
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="font-medium text-gray-900 truncate">{{ img.title || 'Untitled' }}
                                            </h4>
                                            <div class="flex gap-1 ml-2">
                                                <button
                                                    @click="() => router.visit(route('albums.images.edit', [album.id, img.id]))"
                                                    class="text-indigo-600 hover:text-indigo-800 p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button @click="deleteImage(img.id)"
                                                    class="text-red-600 hover:text-red-800 p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ img.description || 'No description' }}</p>
                                        <div class="flex items-center justify-between">
                                            <span
                                                :class="img.published ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                                {{ img.published ? 'Published' : 'Draft' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No images</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by adding some images to this album.
                                </p>
                                <div class="mt-6">
                                    <button @click="() => router.visit(route('albums.images.create', album.id))"
                                        class="inline-flex items-center rounded bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Images
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>