<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Form from './Form.vue';

const props = defineProps({
    album: { type: Object, required: true },
    images: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Edit Album" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Album</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <Form mode="edit" :album="props.album" :images="props.images" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>