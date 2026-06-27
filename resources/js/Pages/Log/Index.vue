<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  logs: Object,
  filters: Object,
  roles: Array,
});

const search = ref(props.filters?.search || '');
const role = ref(props.filters?.role || '');
const startDate = ref(props.filters?.start_date || '');
const endDate = ref(props.filters?.end_date || '');

function submitSearch() {
  const params = new URLSearchParams();
  if (search.value) params.set('search', search.value);
  if (role.value) params.set('role', role.value);
  if (startDate.value) params.set('start_date', startDate.value);
  if (endDate.value) params.set('end_date', endDate.value);
  params.set('per_page', props.filters?.per_page || 20);
  window.location.href = route('logs.index') + (params.toString() ? `?${params.toString()}` : '');
}
</script>

<template>
  <Head title="Activity Logs" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Activity Logs</h2>
    </template>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-16 py-6">
      <div class="mb-4 flex flex-wrap items-center gap-2">
        <input v-model="search" type="text" placeholder="Search activity..."
               class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
        
        <select v-model="role" class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
          <option value="">All Roles</option>
          <option v-for="r in props.roles" :key="r" :value="r">{{ r }}</option>
        </select>

        <div class="flex items-center gap-2">
            <input v-model="startDate" type="date" class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
            <span class="text-gray-500">-</span>
            <input v-model="endDate" type="date" class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
        </div>

        <button @click="submitSearch" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 text-sm">Filter</button>
      </div>

      <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="log in props.logs.data" :key="log.id">
              <td class="px-4 py-3 text-sm text-gray-700">{{ log.created_at }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ log.user_name || 'System/Unknown' }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">
                <span v-for="role in log.user_roles" :key="role" class="mr-1 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ role }}</span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ log.activity }}</td>
            </tr>
            <tr v-if="!props.logs.data || props.logs.data.length === 0">
              <td class="px-4 py-6 text-center text-sm text-gray-500" colspan="4">No logs found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-4 flex items-center gap-2">
        <Link v-if="props.logs.links.prev" :href="props.logs.links.prev" class="rounded-md border px-3 py-1 text-sm text-gray-700 hover:bg-gray-50">Previous</Link>
        <Link v-if="props.logs.links.next" :href="props.logs.links.next" class="rounded-md border px-3 py-1 text-sm text-gray-700 hover:bg-gray-50">Next</Link>
        <span class="text-sm text-gray-500">Page {{ props.logs.meta.current_page }} of {{ props.logs.meta.last_page }}</span>
      </div>
    </div>
  </AuthenticatedLayout>
  
  
</template>