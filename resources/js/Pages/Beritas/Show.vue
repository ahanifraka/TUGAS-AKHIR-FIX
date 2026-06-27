<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Divider } from 'primevue';

const props = defineProps({
    berita: { type: Object, required: true },
});
</script>

<template>

    <Head :title="props.berita.title" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold leading-tight text-gray-800">{{ props.berita.title }}</h1>

                <div class="flex gap-2">
                    <button @click="() => router.visit(route('beritas.edit', props.berita.id))"
                        class="inline-flex items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">
                        <i class="pi pi-pen-to-square mr-2"></i>
                        Edit
                    </button>
                    <button @click="() => router.visit(route('beritas.index'))"
                        class="inline-flex items-center rounded bg-gray-200 px-3 py-2 hover:bg-gray-300">
                        <i class="pi pi-arrow-left mr-2" style="font-size: 0.75rem"></i>
                        Kembali</button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="grid grid-cols-1 xl:grid-cols-6 gap-4 px-24 py-6">
                <div class="bg-white col-span-1 xl:col-span-4 shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div>
                            <img :src="props.berita.image" alt="cover" class="mt-2 max-h-96 w-full rounded mb-8 object-cover" />
                            <div class="prose max-w-none" v-html="props.berita.content"></div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 col-span-1 xl:col-span-2 bg-white shadow-sm sm:rounded-lg p-6 h-fit">
                    <div>
                        <span class="block text-sm text-gray-500">Slug:</span>
                        <span>{{ props.berita.slug }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Teaser:</span>
                        <p class="mt-1">{{ props.berita.teaser }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Published:</span>
                        <span class="ml-2">{{ props.berita.published ? 'Ya' : 'Tidak' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Popular:</span>
                        <span class="ml-2">{{ props.berita.popular ? 'Ya' : 'Tidak' }}</span>
                    </div>
                    <Divider />
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Created By:</span>
                            <span class="ml-2" v-if="props.berita.created_by">{{ props.berita.created_by.name }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Updated By:</span>
                            <span class="ml-2" v-if="props.berita.updated_by">{{ props.berita.updated_by.name }}</span>
                        </div>
                    </div>
                    <Divider />
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Meta Title:</span>
                            <span class="ml-2">{{ props.berita.meta_title }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Meta Keyword:</span>
                            <span class="ml-2">{{ props.berita.meta_keyword }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Meta Content:</span>
                            <span class="ml-2">{{ props.berita.meta_content }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>