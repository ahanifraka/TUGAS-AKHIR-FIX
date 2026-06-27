<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Checkbox from 'primevue/checkbox'
import Dialog from 'primevue/dialog'
import Tag from 'primevue/tag'
import Swal from 'sweetalert2'

const props = defineProps({
  items: { type: Array, required: true },
})

const showDialog = ref(false)
const editMode = ref(false)
const editingId = ref(null)

const form = useForm({
  name: '',
  order: 0,
  is_active: true,
})

function openCreateDialog() {
  editMode.value = false
  form.reset()
  form.name = ''
  form.order = 0
  form.is_active = true
  showDialog.value = true
}

function openEditDialog(item) {
  editMode.value = true
  editingId.value = item.id
  form.name = item.name
  form.order = item.order
  form.is_active = item.is_active
  showDialog.value = true
}

function submit() {
  if (editMode.value) {
    form.put(route('status-peraturan.update', editingId.value), {
      onSuccess: () => {
        showDialog.value = false
        Swal.fire({ title: 'Berhasil', text: 'Status peraturan berhasil diperbarui', icon: 'success', timer: 1500, showConfirmButton: false })
      }
    })
  } else {
    form.post(route('status-peraturan.store'), {
      onSuccess: () => {
        showDialog.value = false
        Swal.fire({ title: 'Berhasil', text: 'Status peraturan berhasil ditambahkan', icon: 'success', timer: 1500, showConfirmButton: false })
      }
    })
  }
}

function onDelete(item) {
  Swal.fire({
    title: 'Hapus Status Peraturan?',
    text: `Anda akan menghapus: ${item.name}`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('status-peraturan.destroy', item.id), {
        onSuccess: () => Swal.fire({ title: 'Terhapus', text: 'Status peraturan dihapus.', icon: 'success', timer: 1200, showConfirmButton: false })
      })
    }
  })
}
</script>

<template>

  <Head title="Manajemen Status Peraturan" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Manajemen Status Peraturan</h2>
        <div class="flex gap-2">
          <Button label="Kembali" severity="secondary" icon="pi pi-arrow-left"
            @click="() => router.visit(route('regulasis.index'))" />
          <Button label="Tambah Status" severity="info" icon="pi pi-plus" @click="openCreateDialog" />
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <DataTable :value="props.items" dataKey="id">
              <Column field="name" header="Nama Status" sortable></Column>
              <Column field="order" header="Urutan" sortable></Column>
              <Column header="Status">
                <template #body="slotProps">
                  <Tag :value="slotProps.data.is_active ? 'Aktif' : 'Nonaktif'"
                    :severity="slotProps.data.is_active ? 'success' : 'danger'" />
                </template>
              </Column>
              <Column header="Aksi">
                <template #body="slotProps">
                  <div class="flex gap-2">
                    <Button icon="pi pi-pencil" severity="info" size="small" @click="openEditDialog(slotProps.data)" />
                    <Button icon="pi pi-trash" severity="danger" size="small" @click="onDelete(slotProps.data)" />
                  </div>
                </template>
              </Column>
            </DataTable>
          </div>
        </div>
      </div>
    </div>

    <Dialog v-model:visible="showDialog" :header="editMode ? 'Edit Status Peraturan' : 'Tambah Status Peraturan'"
      :modal="true" :style="{ width: '450px' }">
      <div class="flex flex-col gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Status<span
              class="text-red-500">*</span></label>
          <InputText v-model="form.name" class="w-full" />
          <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
          <InputNumber v-model="form.order" class="w-full" />
          <div v-if="form.errors.order" class="mt-1 text-sm text-red-600">{{ form.errors.order }}</div>
        </div>

        <div class="flex items-center gap-2">
          <Checkbox v-model="form.is_active" :binary="true" />
          <span>Aktif</span>
        </div>
      </div>

      <template #footer>
        <Button label="Batal" severity="secondary" @click="showDialog = false" />
        <Button label="Simpan" severity="info" @click="submit" :loading="form.processing" />
      </template>
    </Dialog>
  </AuthenticatedLayout>
</template>
