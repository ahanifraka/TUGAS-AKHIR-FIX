<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import SplitButton from 'primevue/splitbutton'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Swal from 'sweetalert2'

const props = defineProps({
  bumds: { type: Object, required: true },
  filters: { type: Object, default: () => ({ search: '' }) },
})

const search = ref(props.filters?.search ?? '')
const sortField = ref(props.filters?.sort_by ?? 'updated_at')
const sortOrder = ref(props.filters?.sort_dir === 'asc' ? 1 : -1)
const rows = ref(props.bumds?.meta?.per_page ?? props.filters?.per_page ?? 10)

const first = computed(() => {
  const currentPage = props.bumds?.meta?.current_page ?? 1
  const perPage = props.bumds?.meta?.per_page ?? rows.value
  return (currentPage - 1) * perPage
})

const totalRecords = computed(() => props.bumds?.meta?.total ?? 0)

function goToCreate() {
  router.visit(route('bumds.create'))
}

function onSearch() {
  router.visit(route('bumds.index'), {
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
  router.visit(route('bumds.index'), {
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

function onPage(event) {
  rows.value = event.rows
  const page = event.page + 1
  router.visit(route('bumds.index'), {
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

function getActionItems(row) {
  return [
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => router.visit(route('bumds.edit', row.kode)),
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => {
        Swal.fire({
          title: 'Hapus data?',
          text: 'Tindakan ini tidak dapat dibatalkan.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, hapus',
          cancelButtonText: 'Batal',
        }).then((result) => {
          if (result.isConfirmed) {
            router.delete(route('bumds.destroy', row.kode), {
              onSuccess: () => {
                Swal.fire({
                  title: 'Berhasil',
                  text: 'Data berhasil dihapus.',
                  icon: 'success',
                  timer: 1500,
                  showConfirmButton: false,
                })
              },
              onError: (e) => {
                const firstMsg = Object.values(e || {}).find((v) => typeof v === 'string')
                Swal.fire({
                  title: 'Gagal menghapus',
                  text: firstMsg || 'Terjadi kesalahan. Coba lagi.',
                  icon: 'error',
                })
              },
            })
          }
        })
      },
    },
  ]
}

function formatDateID(dateInput) {
  const d = new Date(dateInput)
  if (isNaN(d.getTime())) return '-'
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}
</script>

<template>

  <Head title="BUMD" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">BUMD</h2>
        <div class="flex gap-2">
          <input v-model="search" @keyup.enter="onSearch" type="text" placeholder="Cari kode/nama..."
            class="rounded border px-3 py-2 border-gray-300" />

          <button @click="onSearch"
            class="inline-flex items-center rounded bg-gray-800 px-3 py-2 text-white hover:bg-gray-900"><span><i
                class="pi pi-search"></i></span></button>

          <button v-if="$page.props.auth.user.roles.includes('admin') || $page.props.auth.user.roles.includes('editor')"
            @click="goToCreate"
            class="inline-flex gap-2 items-center rounded bg-primary px-3 py-2 text-white hover:bg-primary-hover">
            <span><i class="pi pi-plus-circle"></i></span>
            <span>Tambah</span>
        </button>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div v-if="$page.props.errors && Object.keys($page.props.errors).length" class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-700">
              <ul>
                <li v-for="(msg, field) in $page.props.errors" :key="field">{{ msg }}</li>
              </ul>
            </div>
            <div v-if="$page.props.error" class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-700">{{ $page.props.error }}</div>
            <div class="overflow-x-auto">
              <DataTable :value="props.bumds?.data || []" lazy paginator :rows="rows"
                :rowsPerPageOptions="[5, 10, 20, 50]" :totalRecords="totalRecords" :first="first" :sortField="sortField"
                :sortOrder="sortOrder" @page="onPage" @sort="onSort" size="small" tableStyle="min-width: 50rem"
                stripedRows removableSort dataKey="kode" rowHover>
                <Column header="#">
                  <template #body="slotProps">
                    {{ (props.bumds.meta.per_page * (props.bumds.meta.current_page - 1)) + slotProps.index + 1 }}
                  </template>
                </Column>

                <Column field="kode" header="Kode"></Column>
                <Column field="nama" header="Nama" sortable>
                  <template #body="slotProps">
                    <div class="flex items-center justify-start gap-x-2 text-sm">
                      <img :src="slotProps.data.logo || '/images/default-cover.png'" alt="Logo" title="Logo"
                        class="h-10 w-10 object-contain rounded bg-gray-50" />
                      {{ slotProps.data.nama }}
                    </div>
                  </template>
                </Column>
                <Column field="sektor" header="Sektor"></Column>
                <Column header="Update Terakhir">
                  <template #body="slotProps">
                    <div class="flex items-center justify-start text-sm">
                      {{ formatDateID(slotProps.data.updated_at) }}
                    </div>
                  </template>
                </Column>
                <Column header="Actions">
                  <template #body="slotProps">
                    <SplitButton label="View" size="small" severity="info" icon="pi pi-eye"
                      @click="() => router.visit(route('bumds.show', slotProps.data.kode))"
                      :model="getActionItems(slotProps.data)" />
                  </template>
                </Column>
              </DataTable>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>