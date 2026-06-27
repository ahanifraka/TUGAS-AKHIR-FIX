<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Form from './Form.vue';

const props = defineProps({
    pejabat: { type: Object, required: true },
});
</script>

<template>
    <Head title="Edit Pejabat" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Pejabat</h2>
                 <Link :href="route('pejabats.index')" class="p-button p-button-secondary p-button-sm">
                    <i class="pi pi-arrow-left mr-2"></i> Kembali
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <Form 
                    :pejabat="pejabat"
                    :submit-route="route('pejabats.update', pejabat.id)"
                    submit-method="put"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>