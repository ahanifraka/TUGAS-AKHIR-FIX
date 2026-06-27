<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import SplitButton from 'primevue/splitbutton'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Dropdown from 'primevue/dropdown'
import Tag from 'primevue/tag'
import Card from 'primevue/card'
import Paginator from 'primevue/paginator'

const props = defineProps({
  files: { type: Object, required: true },
  filters: { type: Object, default: () => ({ search: '', source: '' }) },
})

const search = ref(props.filters?.search ?? '')
const source = ref(props.filters?.source ?? '')
const rows = ref(props.files?.meta?.per_page ?? props.filters?.per_page ?? 10)
const viewMode = ref('table') // 'table' or 'grid'

const sourceOptions = [
  { label: 'All Sources', value: '' },
  { label: 'File Uploads', value: 'files' },
  { label: 'Berita Images', value: 'berita' },
  { label: 'Content Sliders', value: 'slider' },
  { label: 'Album Covers', value: 'album' },
  { label: 'Album Images', value: 'album_image' },
]

const first = computed(() => {
  const currentPage = props.files?.meta?.current_page ?? 1
  const perPage = props.files?.meta?.per_page ?? rows.value
  return (currentPage - 1) * perPage
})

const totalRecords = computed(() => props.files?.meta?.total ?? 0)

function onSearch() {
  router.visit(route('media.index'), {
    method: 'get',
    data: {
      search: search.value || undefined,
      source: source.value || undefined,
      per_page: rows.value,
    },
    preserveState: true,
    preserveScroll: true,
  })
}

function clearSearch() {
  search.value = ''
  source.value = ''
  onSearch()
}

function onPage(event) {
  rows.value = event.rows
  const targetPage = (event.page ?? Math.floor(event.first / event.rows)) + 1
  router.visit(route('media.index'), {
    method: 'get',
    data: {
      page: targetPage,
      search: search.value || undefined,
      source: source.value || undefined,
      per_page: rows.value,
    },
    preserveState: true,
    preserveScroll: true,
  })
}

function formatBytes(bytes) {
  if (bytes === null || bytes === undefined) return '-'
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  if (bytes === 0) return '0 B'
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  const value = (bytes / Math.pow(1024, i)).toFixed(i === 0 ? 0 : 1)
  return `${value} ${sizes[i]}`
}

function getSourceSeverity(source) {
  const severityMap = {
    'files': 'info',
    'berita': 'success',
    'slider': 'warning',
    'album': 'secondary',
    'album_image': 'contrast'
  }
  return severityMap[source] || 'info'
}

function actionItems(row) {
  const items = [
    {
      label: 'Open',
      icon: 'pi pi-external-link',
      command: () => {
        if (row.url) window.open(row.url, '_blank')
      },
    },
    {
      label: 'Copy URL',
      icon: 'pi pi-copy',
      command: () => {
        if (row.url) navigator.clipboard?.writeText(row.url)
      },
    },
  ]

  // Add source-specific actions
  if (row.source === 'berita') {
    items.push({
      label: 'View Berita',
      icon: 'pi pi-eye',
      command: () => {
        router.visit(route('beritas.show', row.original_id))
      },
    })
  } else if (row.source === 'slider') {
    items.push({
      label: 'View Slider',
      icon: 'pi pi-eye',
      command: () => {
        router.visit(route('content-sliders.show', row.original_id))
      },
    })
  } else if (row.source === 'album') {
    items.push({
      label: 'View Album',
      icon: 'pi pi-eye',
      command: () => {
        router.visit(route('albums.show', row.original_id))
      },
    })
  } else if (row.source === 'album_image') {
    items.push({
      label: 'View Album Image',
      icon: 'pi pi-eye',
      command: () => {
        // Note: You might need to adjust this route based on your routing structure
        router.visit(route('albums.images.show', [row.album_id, row.original_id]))
      },
    })
  }

  return items
}

function isImageFile(mimeType) {
  return mimeType && mimeType.startsWith('image/')
}

function openFile(file) {
  if (file.url) {
    window.open(file.url, '_blank')
  }
}

function copyUrl(file) {
  if (file.url && navigator.clipboard) {
    navigator.clipboard.writeText(file.url).then(() => {
      // You could add a toast notification here if needed
      console.log('URL copied to clipboard')
    }).catch(err => {
      console.error('Failed to copy URL: ', err)
    })
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Media Library" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-semibold">Media Library</h2>
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <Button 
                    :class="['p-2', viewMode === 'table' ? 'p-button-info' : 'p-button-outlined']"
                    @click="viewMode = 'table'"
                    v-tooltip="'Table View'"
                  >
                    <i class="pi pi-list text-sm"></i>
                  </Button>
                  <Button 
                    :class="['p-2', viewMode === 'grid' ? 'p-button-info' : 'p-button-outlined']"
                    @click="viewMode = 'grid'"
                    v-tooltip="'Grid View'"
                  >
                    <i class="pi pi-th-large text-sm"></i>
                  </Button>
                </div>
                <div class="text-sm text-gray-600">
                  Total: {{ totalRecords }} items
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
              <div class="col-span-4">
                <div class="flex gap-2">
                  <InputText v-model="search" placeholder="Search filename, title, or type" class="w-full" @keyup.enter="onSearch" />
                  <Button icon="pi pi-search" @click="onSearch" severity="contrast" class="w-24 px-32" />
                </div>
              </div>
              <div>
                <Dropdown 
                  v-model="source" 
                  :options="sourceOptions" 
                  optionLabel="label" 
                  optionValue="value" 
                  placeholder="Filter by source"
                  class="w-full"
                  @change="onSearch"
                />
              </div>
              <div>
                <Button label="Clear Filters" icon="pi pi-times" severity="secondary" @click="clearSearch" class="w-48" />
              </div>
            </div>

            <!-- Table View -->
            <DataTable
              v-if="viewMode === 'table'"
              :value="files?.data ?? []"
              :paginator="true"
              :rows="rows"
              :totalRecords="totalRecords"
              :first="first"
              lazy
              @page="onPage"
              dataKey="id"
              responsiveLayout="scroll"
            >
              <Column header="Preview" class="w-20">
                <template #body="slotProps">
                  <div class="flex items-center justify-center">
                    <img 
                      v-if="isImageFile(slotProps.data.mime_type) && slotProps.data.url" 
                      :src="slotProps.data.url" 
                      :alt="slotProps.data.title"
                      class="w-12 h-12 object-cover rounded border"
                      @error="$event.target.style.display='none'"
                    />
                    <i v-else class="pi pi-file text-2xl text-gray-400" />
                  </div>
                </template>
              </Column>

              <Column field="title" header="Title" sortable>
                <template #body="slotProps">
                  <div class="flex flex-col gap-1">
                    <span class="font-medium truncate max-w-[320px]" :title="slotProps.data.title">
                      {{ slotProps.data.title }}
                    </span>
                    <span class="text-sm text-gray-500 truncate max-w-[320px]" :title="slotProps.data.filename">
                      {{ slotProps.data.filename }}
                    </span>
                  </div>
                </template>
              </Column>

              <Column field="source" header="Source" sortable>
                <template #body="slotProps">
                  <Tag 
                    :value="slotProps.data.source_label" 
                    :severity="getSourceSeverity(slotProps.data.source)"
                  />
                </template>
              </Column>

              <Column field="mime_type" header="Type" sortable>
                <template #body="slotProps">
                  <span class="text-sm">{{ slotProps.data.mime_type }}</span>
                </template>
              </Column>

              <Column field="size" header="Size">
                <template #body="slotProps">
                  {{ formatBytes(slotProps.data.size) }}
                </template>
              </Column>

              <Column field="path" header="Path">
                <template #body="slotProps">
                  <a v-if="slotProps.data.url" :href="slotProps.data.url" target="_blank" class="text-primary underline text-sm truncate block max-w-[200px]" :title="slotProps.data.path">
                    {{ slotProps.data.path }}
                  </a>
                  <span v-else class="text-sm truncate block max-w-[200px]" :title="slotProps.data.path">{{ slotProps.data.path }}</span>
                </template>
              </Column>

              <Column header="Actions" class="w-32">
                <template #body="slotProps">
                  <SplitButton label="Open" icon="pi pi-external-link" size="small" severity="info"
                    @click="openFile(slotProps.data)"
                    :model="actionItems(slotProps.data)" />
                </template>
              </Column>
            </DataTable>

            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4">
              <Card 
                v-for="file in files?.data ?? []" 
                :key="file.id"
                class="media-card hover:shadow-lg transition-shadow duration-200 cursor-pointer"
                @click="openFile(file)"
              >
                <template #content>
                  <div class="p-0">
                    <!-- Media Preview -->
                    <div class="aspect-square bg-gray-50 rounded-t-lg overflow-hidden relative group">
                      <img 
                        v-if="isImageFile(file.mime_type) && file.url" 
                        :src="file.url" 
                        :alt="file.title"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                        loading="lazy"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                        <i class="pi pi-file text-4xl text-gray-400"></i>
                      </div>
                      
                      <!-- Source Badge -->
                      <div class="absolute top-2 left-2">
                        <Tag 
                          :value="file.source_label" 
                          :severity="getSourceSeverity(file.source)"
                          class="text-xs"
                        />
                      </div>
                      
                      <!-- Actions Overlay -->
                      <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="flex gap-2">
                          <Button 
                            icon="pi pi-eye" 
                            class="p-button-rounded p-button-info p-button-sm"
                            @click.stop="openFile(file)"
                            v-tooltip="'View'"
                          />
                          <Button 
                            icon="pi pi-copy" 
                            class="p-button-rounded p-button-secondary p-button-sm"
                            @click.stop="copyUrl(file)"
                            v-tooltip="'Copy URL'"
                          />
                        </div>
                      </div>
                    </div>
                    
                    <!-- File Info -->
                    <div class="p-3">
                      <div class="font-medium text-sm truncate mb-1" :title="file.filename">
                        {{ file.filename }}
                      </div>
                      <div class="text-xs text-gray-500 truncate mb-2" :title="file.title">
                        {{ file.title || 'No title' }}
                      </div>
                      <div class="flex items-center justify-between text-xs text-gray-400">
                        <span class="truncate">{{ file.mime_type || 'Unknown' }}</span>
                        <span>{{ formatBytes(file.size) }}</span>
                      </div>
                    </div>
                  </div>
                </template>
              </Card>
            </div>

            <!-- Pagination for Grid View -->
            <div v-if="viewMode === 'grid' && totalRecords > rows" class="mt-6">
              <Paginator
                :first="first"
                :rows="rows"
                :totalRecords="totalRecords"
                @page="onPage"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.media-card {
  transition: all 0.2s ease-in-out;
  border: 1px solid #e5e7eb;
}

.media-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.media-card .p-card-content {
  padding: 0;
}

/* Responsive grid adjustments */
@media (max-width: 640px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
  }
}

@media (max-width: 480px) {
  .grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}

/* Enhanced hover effects */
.group:hover .group-hover\:scale-105 {
  transform: scale(1.05);
}

.group:hover .group-hover\:bg-opacity-30 {
  background-opacity: 0.3;
}

.group:hover .group-hover\:opacity-100 {
  opacity: 1;
}

/* View toggle buttons */
.p-button.p-button-info {
  background-color: #3b82f6;
  border-color: #3b82f6;
}

.p-button.p-button-outlined {
  background-color: transparent;
  color: #6b7280;
  border-color: #d1d5db;
}

.p-button.p-button-outlined:hover {
  background-color: #f3f4f6;
  color: #374151;
}

/* Improved card spacing */
.media-card .p-card-body {
  padding: 0;
}

/* Better text truncation */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Loading states */
.media-card img {
  transition: opacity 0.2s ease-in-out;
}

.media-card img[src=""] {
  opacity: 0;
}
</style>