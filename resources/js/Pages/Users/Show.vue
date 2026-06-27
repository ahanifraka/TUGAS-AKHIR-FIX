<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  user: { type: Object, required: true },
});
</script>

<template>
  <Head title="Detail User" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail User</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 space-y-2">
            <div><span class="font-semibold">ID:</span> {{ user.id }}</div>
            <div><span class="font-semibold">Nama:</span> {{ user.name }}</div>
            <div><span class="font-semibold">Email:</span> {{ user.email }}</div>
            <div class="text-sm text-gray-500">Dibuat: {{ user.created_at }}</div>
            <div class="text-sm text-gray-500">Diubah: {{ user.updated_at }}</div>
            <div class="pt-4">
              <Link :href="route('users.index')" class="text-gray-600 hover:text-gray-900">Kembali</Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>