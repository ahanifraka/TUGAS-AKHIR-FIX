<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import ContentSliderForm from '@/Pages/ContentSliders/Form.vue';

const props = defineProps({
    slider: { type: Object, required: true },
    beritas: { type: Array, default: () => [] },
    albums: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Edit Slider" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Slider</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <ContentSliderForm
                        mode="edit"
                        :slider="props.slider"
                        submit-label="Update"
                        :beritas="props.beritas"
                        :albums="props.albums"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>