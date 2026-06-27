<template>
  <Head :title="props.item.judul_peraturan || props.item.title">
    <meta name="description" :content="truncateHtml(props.item.content, 160)">
  </Head>

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail Regulasi</h2>
    </template>

    <div class="py-12 mx-auto bg-gray-50 dark:bg-gray-900">
      <div class="container mx-auto px-4 md:px-8 max-w-6xl">

        <!-- Main Content Card -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

          <!-- Header Section -->
          <div
            class="flex flex-col md:flex-row justify-between w-full bg-gradient-to-r from-primary to-primary-hover p-6 text-white">
            <div class="flex flex-wrap gap-3">
              <span v-if="props.item.status_peraturan"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm">
                <i class="pi pi-check-circle mr-2"></i>
                {{ props.item.status_peraturan }}
              </span>
              <span v-if="props.item.tipe_dokumen"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm">
                <i class="pi pi-file mr-2"></i>
                {{ props.item.tipe_dokumen }}
              </span>
            </div>

            <!-- Quick Stats -->
            <div class="flex gap-4 text-sm text-gray-200">
              <div v-if="props.item.updated_at" class="flex items-center gap-1"
                title="Terakhir diperbarui">
                <span>Last Update: {{ props.item.updated_at }}</span>
              </div>
            </div>

          </div>

          <!-- Action Buttons -->
          <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <div class="flex flex-wrap gap-3 items-center justify-between">

              <div>
                <div>
                  <div v-if="props.item.subjek" class="flex flex-wrap gap-2">
                    <span
                      v-for="(tag, index) in props.item.subjek.split('-').map(s => s.trim()).filter(Boolean)"
                      :key="index"
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                      <i class="pi pi-tag mr-1 text-xs"></i>
                      {{ tag }}
                    </span>
                  </div>
                  <p v-else class="text-gray-400">-</p>
                </div>
              </div>

              <div class="flex flex-wrap gap-3">
                <a v-if="props.item.file_url" :href="props.item.file_url" target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition-colors">
                  <i class="pi pi-download"></i>
                  <span>Unduh Dokumen</span>
                </a>
                <button @click="handleShare"
                  class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-700 transition-colors">
                  <i class="pi pi-share-alt"></i>
                  <span>Bagikan</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Content Section -->
          <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
              Abstraksi / Deskripsi
            </h2>
            <div class="prose prose-lg prose-blue max-w-none dark:prose-invert bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700"
              v-html="props.item.content"></div>
          </div>

          <!-- Document Information -->
          <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
              Informasi Dokumen
            </h2>

            <div class="p-5 mb-6">
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">

                <!-- Tipe Dokumen -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tipe Dokumen
                  </dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    {{ props.item.tipe_dokumen || '-' }}
                  </dd>
                </div>

                <!-- Judul Peraturan -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Judul
                    Peraturan</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    {{ props.item.judul_peraturan || '-' }}
                  </dd>
                </div>

                <!-- T.E.U Badan -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">T.E.U Badan
                  </dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.teu_badan">
                      {{ props.item.teu_badan }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Nomor Peraturan -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nomor
                    Peraturan</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    {{ props.item.nomor_peraturan || '-' }}
                  </dd>
                </div>

                <!-- Tahun Peraturan -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tahun
                    Peraturan</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    {{ props.item.tahun_peraturan || '-' }}
                  </dd>
                </div>

                <!-- Jenis Peraturan -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Jenis
                    Peraturan</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    {{ props.item.jenis_peraturan || '-' }}
                  </dd>
                </div>

                <!-- Singkatan Jenis -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Singkatan
                    Jenis</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <span v-if="props.item.singkatan_jenis"
                      class="text-base text-gray-900 dark:text-gray-100">
                      {{ props.item.singkatan_jenis }}
                    </span>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Tempat Penetapan -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tempat
                    Penetapan</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.tempat_penetapan">
                      {{ props.item.tempat_penetapan }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Tanggal Penetapan -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tanggal
                    Penetapan</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.tanggal_penetapan">
                      {{ props.item.tanggal_penetapan }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Tanggal Pengundangan -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tanggal
                    Pengundangan</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.tanggal_pengundangan">
                      {{ props.item.tanggal_pengundangan }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Sumber -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Sumber</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.sumber">
                      {{ props.item.sumber }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Subjek -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Subjek</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.subjek">
                      {{ props.item.subjek }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Bidang Hukum -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Bidang Hukum
                  </dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.bidang_hukum">
                      {{ props.item.bidang_hukum }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Bahasa -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Bahasa</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.bahasa">
                      {{ props.item.bahasa }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Lokasi -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Lokasi</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.lokasi">
                      {{ props.item.lokasi }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Status -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.status_peraturan">
                      {{ props.item.status_peraturan }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Keterangan Status -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Keterangan
                    Status</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.keterangan_status">
                      {{ props.item.keterangan_status }}
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Tag -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tag</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <template v-if="props.item.tag">
                      <div class="flex flex-wrap gap-2">
                        <span
                          v-for="(tag, index) in props.item.tag.split(',').map(s => s.trim()).filter(Boolean)"
                          :key="index"
                          class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                          {{ tag }}
                        </span>
                      </div>
                    </template>
                    <span v-else class="text-gray-400">-</span>
                  </dd>
                </div>

                <!-- Is Active -->
                <div class="flex flex-col">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status Aktif</dt>
                  <dd class="text-base text-gray-900 dark:text-gray-100">
                    <span :class="[
                      'inline-flex items-center px-2 py-1 text-xs rounded-full font-medium',
                      props.item.is_active
                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                    ]">
                      <i :class="['pi mr-1 text-xs', props.item.is_active ? 'pi-check-circle' : 'pi-times-circle']"></i>
                      {{ props.item.is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                  </dd>
                </div>
              </dl>
            </div>

            <!-- Additional Notes -->
            <div
              class="mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-50 dark:from-gray-900/20 dark:to-gray-900/20 rounded-lg border border-gray-400 dark:border-gray-600">
              <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center">
                <i class="pi pi-exclamation-triangle mr-2"></i>
                Keterangan Dokumen
              </h3>
              <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ props.item.keterangan_dokumen || '-' }}
              </p>
            </div>

            <!-- File Section -->
            <div
              class="mt-6 p-5 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 rounded-lg border border-red-200 dark:border-red-800">
              <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center">
                <i class="pi pi-file-pdf mr-2 text-red-600 text-lg"></i>
                File Peraturan
              </h3>
              <div class="text-sm text-gray-500 dark:text-gray-400 pb-4">
                <i class="pi pi-info-circle mr-1"></i>
                Format: PDF
              </div>
              <div v-if="props.item.file_url" class="flex flex-col items-start gap-3">
                <a :href="props.item.file_url" target="_blank" rel="noopener noreferrer"
                  class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors shadow-md hover:shadow-lg">
                  <i class="pi pi-file-pdf text-lg"></i>
                  <span class="font-medium">Lihat / Unduh Dokumen PDF</span>
                  <i class="pi pi-external-link text-sm"></i>
                </a>
              </div>
              <p v-else class="text-gray-400">
                <i class="pi pi-times-circle mr-1"></i>
                Tidak ada file tersedia
              </p>
            </div>
          </div>

          <!-- Back Button -->
          <div
            class="flex flex-row justify-end px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
            <button @click="goBack"
              class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
              <i class="pi pi-arrow-left"></i>
              <span>Kembali ke Daftar Regulasi</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  item: { type: Object, required: true },
})

function truncateHtml(html, maxLength) {
  if (!html) return ''
  const div = document.createElement('div')
  div.innerHTML = html
  const text = div.textContent || div.innerText || ''
  return text.length > maxLength ? text.slice(0, maxLength) + '…' : text
}

function goBack() {
  router.visit(route('regulasis.index'))
}

function handleShare() {
  if (navigator.share) {
    navigator.share({
      title: props.item.judul_peraturan || props.item.title,
      text: truncateHtml(props.item.content, 200),
      url: window.location.href
    }).catch(() => {
      // User cancelled or error occurred
    })
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(window.location.href).then(() => {
      alert('Link berhasil disalin ke clipboard!')
    })
  }
}
</script>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }
}

.prose {
  color: inherit;
}

.prose h1,
.prose h2,
.prose h3,
.prose h4,
.prose h5,
.prose h6 {
  color: inherit;
  margin-top: 1.5em;
  margin-bottom: 0.75em;
}

.prose p {
  margin-top: 1em;
  margin-bottom: 1em;
}

.prose ul,
.prose ol {
  margin-top: 1em;
  margin-bottom: 1em;
  padding-left: 1.5em;
}

.prose li {
  margin-top: 0.5em;
  margin-bottom: 0.5em;
}

.prose a {
  color: #3b82f6;
  text-decoration: underline;
}

.prose strong {
  font-weight: 600;
}

.prose em {
  font-style: italic;
}
</style>