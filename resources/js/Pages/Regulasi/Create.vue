<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Form from './Form.vue'

const props = defineProps({
  tipeDokumenOptions: { type: Array, default: () => [] },
  statusPeraturanOptions: { type: Array, default: () => [] },
})
</script>

<template>
  <Head title="Tambah Regulasi" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah Regulasi</h2>
    </template>
    <div class="py-6">
      <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <Form mode="create" :tipeDokumenOptions="props.tipeDokumenOptions" :statusPeraturanOptions="props.statusPeraturanOptions" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>