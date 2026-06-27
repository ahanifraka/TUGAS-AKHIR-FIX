<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import SplitButton from 'primevue/splitbutton'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Swal from 'sweetalert2'

const props = defineProps({
  items: { type: Object, required: true },
  filters: { type: Object, default: () => ({ search: '', sort_by: 'updated_at', sort_dir: 'desc', per_page: 10 }) },
})

const search = ref(props.filters?.search ?? '')
const sortField = ref(props.filters?.sort_by ?? 'updated_at')
const sortOrder = ref(props.filters?.sort_dir === 'asc' ? 1 : -1)
const rows = ref(props.items?.meta?.per_page ?? props.filters?.per_page ?? 10)

const first = computed(() => {
  const currentPage = props.items?.meta?.current_page ?? 1
  const perPage = props.items?.meta?.per_page ?? rows.value
  return (currentPage - 1) * perPage
})

const totalRecords = computed(() => props.items?.meta?.total ?? 0)

function goToCreate() {
  router.visit(route('regulasis.create'))
}

function onSearch() {
  router.visit(route('regulasis.index'), {
    method: 'get',
    data: {
      search: search.value || undefined,
      sort_by: sortField.value,
      sort_dir: sortOrder.value === 1 ? 'asc' : 'desc',
      per_page: rows.value,
    },
    preserveState: true,
    preserveScroll: true,
  })
}

function onSort(event) {
  sortField.value = event.sortField
  sortOrder.value = event.sortOrder
  onSearch()
}

function onPageChange(event) {
  rows.value = event.rows
  const page = Math.floor(event.first / event.rows) + 1
  router.visit(route('regulasis.index'), {
    method: 'get',
    data: {
      search: search.value || undefined,
      sort_by: sortField.value,
      sort_dir: sortOrder.value === 1 ? 'asc' : 'desc',
      per_page: rows.value,
      page,
    },
    preserveState: true,
    preserveScroll: true,
  })
}

function formatDateID(dateStr) {
  if (!dateStr) return '-'
  try {
    return new Date(dateStr).toLocaleString('id-ID', {
      year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
    })
  } catch (e) { return dateStr }
}

function getActionItems(item) {
  return [
    {
      label: 'Edit', icon: 'pi pi-pencil', command: () => router.visit(route('regulasis.edit', item.id))
    },
    {
      label: 'Delete', icon: 'pi pi-trash', command: () => onDelete(item)
    },
  ]
}

function onDelete(item) {
  Swal.fire({
    title: 'Hapus Regulasi?',
    text: `Anda akan menghapus: ${item.title}`,
    icon: 'warning', showCancelButton: true,
    confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('regulasis.destroy', item.id), {
        onSuccess: () => Swal.fire({ title: 'Terhapus', text: 'Regulasi dihapus.', icon: 'success', timer: 1200, showConfirmButton: false })
      })
    }
  })
}
</script>

<template>

  <Head title="Regulasi" />
  <AuthenticatedLayout>
    <template #header>
        <div class="flex flex-col gap-2">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Regulasi</h2>
            <div class="inline-flex gap-2 items-center justify-center">
              <div class="flex items-center gap-2">
                <InputText v-model="search" placeholder="Cari judul/konten..." class="w-72" @keyup.enter="onSearch" />
                <Button icon="pi pi-search" severity="contrast" @click="onSearch" />
              </div>
              <div>
                <button @click="goToCreate"
                  class="inline-flex gap-2 items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">
                  <span><i class="pi pi-plus-circle"></i></span>
                  <span>Tambah</span></button>
              </div>
            </div>
          </div>
        </div>
    </template>

    <div class="py-6">
      <div class="mx-auto container sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="flex gap-2 mt-2 p-4 w-full items-end justify-end">
            <Button label="Manage Tipe Dokumen" icon="pi pi-cog" severity="secondary" size="small" @click="() => router.visit('/tipe-dokumen')" />
            <Button label="Manage Status Peraturan" icon="pi pi-cog" severity="secondary" size="small" @click="() => router.visit('/status-peraturan')" />
          </div>
          <div class="p-6">
            <DataTable :value="props.items.data" paginator :rows="rows" :totalRecords="totalRecords" :first="first"
              :lazy="true" :sortField="sortField" :sortOrder="sortOrder" @sort="onSort" @page="onPageChange"
              dataKey="id" tableStyle="min-width: 50rem">
              <Column header="#" style="width:80px">
                <template #body="slotProps">
                  {{ (props.items.meta.per_page * (props.items.meta.current_page - 1)) + slotProps.index + 1 }}
                </template>
              </Column>
              <Column field="title" header="Judul" sortable></Column>
              <Column field="nomor_peraturan" header="Nomor" sortable></Column>
              <Column field="tahun_peraturan" header="Tahun" sortable></Column>
              <Column field="tipe_dokumen" header="Tipe">
                <template #body="slotProps">
                  {{ slotProps.data.tipe_dokumen || '-' }}
                </template>
              </Column>
              <Column field="status_peraturan" header="Status">
                <template #body="slotProps">
                  <Tag v-if="slotProps.data.status_peraturan" :value="slotProps.data.status_peraturan"
                    :severity="slotProps.data.status_peraturan === 'Berlaku' ? 'success' : 'info'" />
                  <span v-else class="text-gray-400">-</span>
                </template>
              </Column>
              <Column header="Aktif">
                <template #body="slotProps">
                  <Tag :value="slotProps.data.is_active ? 'Ya' : 'Tidak'"
                    :severity="slotProps.data.is_active ? 'success' : 'danger'" />
                </template>
              </Column>
              <Column header="File">
                <template #body="slotProps">
                  <a v-if="slotProps.data.file_url" :href="slotProps.data.file_url" target="_blank"
                    rel="noopener noreferrer" class="text-primary hover:underline">Lihat</a>
                  <span v-else class="text-gray-400">-</span>
                </template>
              </Column>
              <Column header="Update Terakhir">
                <template #body="slotProps">
                  {{ formatDateID(slotProps.data.updated_at) }}
                </template>
              </Column>
              <Column header="Actions">
                <template #body="slotProps">
                  <SplitButton label="View" size="small" severity="info" icon="pi pi-eye"
                    @click="() => router.visit(route('regulasis.show', slotProps.data.id))"
                    :model="getActionItems(slotProps.data)" />
                </template>
              </Column>
            </DataTable>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>