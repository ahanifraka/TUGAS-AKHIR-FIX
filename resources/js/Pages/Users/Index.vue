<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import SplitButton from 'primevue/splitbutton';
import Swal from 'sweetalert2';

import 'sweetalert2/dist/sweetalert2.min.css';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Props
const props = defineProps({
  users: { type: Object, required: true },
  filters: { type: Object, default: () => ({ search: '' }) },
});

// Search
const search = ref(props.filters?.search || '');
const sortField = ref(props.filters?.sortField || null);
const sortOrder = ref(props.filters?.sortOrder ?? null);

watch(search, (val) => {
  router.get(route('users.index'), { search: val }, { preserveState: true, replace: true });
});

// Delete User with SweetAlert2 confirmation
const destroyUser = async (id) => {
  const result = await Swal.fire({
    title: 'Hapus user ini?',
    text: 'Tindakan ini tidak dapat dibatalkan.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
  });

  if (result.isConfirmed) {
    router.delete(route('users.destroy', id), {
      preserveState: true,
      replace: true,
      onSuccess: () => {
        Swal.fire({
          title: 'Terhapus',
          text: 'User berhasil dihapus.',
          icon: 'success',
          timer: 1500,
          showConfirmButton: false,
        });
      },
      onError: () => {
        Swal.fire({
          title: 'Gagal',
          text: 'Terjadi kesalahan saat menghapus.',
          icon: 'error',
        });
      },
    });
  }
};

// PrimeVue DataTable server-side pagination bindings
const rows = computed(() => props.users?.meta?.per_page ?? 10);
const totalRecords = computed(() => props.users?.meta?.total ?? (props.users?.data?.length ?? 0));
const first = computed(() => {
  const current = props.users?.meta?.current_page ?? 1;
  const perPage = rows.value;
  return (current - 1) * perPage;
});

const onPage = (event) => {
  const nextPage = (event.page ?? Math.floor(event.first / event.rows)) + 1;
  router.get(route('users.index'), { search: search.value, page: nextPage, sortField: sortField.value, sortOrder: sortOrder.value }, { preserveState: true, replace: true });
};

const onSort = (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder;
  router.get(route('users.index'), { search: search.value, sortField: sortField.value, sortOrder: sortOrder.value, page: 1 }, { preserveState: true, replace: true });
};

</script>

<style scoped>
.p-datatable.p-datatable-sm .p-datatable-tbody>tr>td {
  padding: 0 !important;
}
</style>

<template>

  <Head title="Users" />

  <AuthenticatedLayout>
    <template #header>
      <h1 class="text-xl font-semibold leading-tight text-gray-800">Manage Users</h1>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

          <div class="px-8 py-12">

            <!-- Header -->
            <div class="flex items-center justify-between pb-12">

              <!-- Search -->
              <input v-model="search" type="text" placeholder="Cari nama atau email..."
                class="border border-gray-300 rounded px-3 py-2 w-64" />

              <!-- Add User Button -->
              <Link :href="route('users.create')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded hover:bg-primary-hover">
                <span><i class="pi pi-user-plus"></i></span>
                <span>Tambah User</span>
              </Link>

            </div>
            <hr>
            <div class="overflow-x-auto">

              <DataTable :value="users.data" :paginator="true" :rows="rows" :rowsPerPageOptions="[10, 20, 50]"
                :totalRecords="totalRecords" :first="first" :lazy="true" @page="onPage" @sort="onSort" removableSort
                :sortField="sortField" :sortOrder="sortOrder" size="small" stripedRows responsiveLayout="scroll"
                tableStyle="min-width: 50rem" rowHover>

                <Column header="#" style="width: 60px">
                  <template #body="{ index }">
                    {{ first + index + 1 }}
                  </template>
                </Column>
                <Column field="name" header="Nama" sortable />
                <Column field="role" header="Role" />
                <Column field="email" header="Email" />
                <Column header="Aksi" style="width: 140px">
                  <template #body="{ data }">
                    <div class="flex justify-end">
                      <SplitButton label="Edit" size="small" severity="info" icon="pi pi-pencil" :model="[
                        { label: 'Hapus', icon: 'pi pi-trash', command: () => destroyUser(data.id) }
                      ]" @click="router.get(route('users.edit', data.id))" class="p-button-sm" />
                    </div>
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