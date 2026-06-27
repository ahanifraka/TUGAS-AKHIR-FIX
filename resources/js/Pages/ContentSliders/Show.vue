<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    slider: { type: Object, required: true },
});
</script>

<template>


    <Head :title="`Slider: ${props.slider.title}`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold leading-tight text-gray-800">Detail Slider</h1>

                <div class="flex gap-2">
                    <button @click="() => router.visit(route('content-sliders.edit', props.slider.id))"
                        class="inline-flex items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">
                        <i class="pi pi-pen-to-square mr-2"></i>
                        Edit
                    </button>
                    <button @click="() => router.visit(route('content-sliders.index'))"
                        class="inline-flex items-center rounded bg-gray-200 px-3 py-2 hover:bg-gray-300">
                        <i class="pi pi-arrow-left mr-2" style="font-size: 0.75rem"></i>
                        Kembali</button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="flex flex-col items-start gap-4">

                            <img :src="props.slider.image_url || props.slider.image" alt="Slider Image"
                                class="w-full aspect-video rounded object-cover" />

                            <div>
                                <h3 class="text-2xl font-semibold mb-8">{{ props.slider.title }}</h3>
                                <p class="text-gray-600 font-bold">Published: </p>
                                <p :class="props.slider.published ? 'text-green-600' : 'text-red-600'">{{
                                    props.slider.published ? 'Ya' : 'Tidak' }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2 font-bold">Deskripsi:</label>
                            <p class="mt-1 whitespace-pre-line rounded">{{
                                props.slider.description }}</p>
                        </div>
                        <div>
                            <label class="text-gray-700 mb-2 font-bold">Tanggal Dibuat:</label>
                            <p class="mt-1 whitespace-pre-line rounded">{{
                                props.slider.created_at }}</p>
                        </div>
                        <div>
                            <label class="text-gray-700 mb-2 font-bold">Dibuat Oleh:</label>
                            <p class="mt-1 whitespace-pre-line rounded">{{
                                props.slider.created_by?.name || '-' }}</p>
                        </div>
                        <div>
                            <label class="text-gray-700 mb-2 font-bold">Tanggal Diupdate:</label>
                            <p class="mt-1 whitespace-pre-line rounded">{{
                                props.slider.updated_at }}</p>
                        </div>
                        <div>
                            <label class="text-gray-700 mb-2 font-bold">Diupdate Oleh:</label>
                            <p class="mt-1 whitespace-pre-line rounded">{{
                                props.slider.updated_by?.name || '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>