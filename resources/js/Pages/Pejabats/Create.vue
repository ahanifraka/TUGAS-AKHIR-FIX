<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Form from './Form.vue';
</script>

<template>
    <Head title="Tambah Pejabat" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah Pejabat Baru</h2>
                <Link :href="route('pejabats.index')" class="p-button p-button-secondary p-button-sm">
                    <i class="pi pi-arrow-left mr-2"></i> Batal
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <Form 
                    :submit-route="route('pejabats.store')"
                    submit-method="post"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>