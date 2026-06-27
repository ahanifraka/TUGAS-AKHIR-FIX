<template>

    <Head title="Regulasi">
        <meta name="description" content="Regulasi BUMD DKI Jakarta">
        <meta name="keywords" content="Regulasi, BUMD DKI Jakarta">
    </Head>

    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <div class="py-12 mx-auto bg-gray-100 dark:bg-gray-800 px-4 xl:px-0">
            <div class="container mx-auto px-4 md:px-0 max-w-7xl">
                <!-- Filters -->
                <div class="mb-6 space-y-4">
                    <div class="flex flex-wrap items-center gap-2 text-gray-800">
                        <select v-model="tipeDokumen" @change="applySearch"
                            class="rounded border border-gray-300 px-3 py-2 min-w-80">
                            <option value="">Semua Tipe Dokumen</option>
                            <option v-for="tipe in props.tipeDokumenOptions" :key="tipe" :value="tipe">{{ tipe }}
                            </option>
                        </select>
                        <select v-model="statusPeraturan" @change="applySearch"
                            class="rounded border border-gray-300 px-3 py-2 min-w-60">
                            <option value="">Semua Status</option>
                            <option v-for="status in props.statusPeraturanOptions" :key="status" :value="status">{{
                                status }}
                            </option>
                        </select>
                        <input v-model="search" @keyup.enter="applySearch" type="text"
                            placeholder="Cari judul/konten..."
                            class="flex-1 min-w-[200px] rounded border border-gray-300 px-3 py-2" />
                        <input v-model="nomorPeraturan" @keyup.enter="applySearch" type="text"
                            placeholder="Nomor Peraturan..." class="w-40 rounded border border-gray-300 px-3 py-2" />
                        <input v-model="tahunPeraturan" @keyup.enter="applySearch" type="text" placeholder="Tahun..."
                            maxlength="4" class="w-28 rounded border border-gray-300 px-3 py-2" />
                        <button @click="applySearch"
                            class="px-4 py-2 rounded bg-primary text-white hover:bg-primary-hover">
                            <i class="pi pi-search mr-1"></i> Cari
                        </button>
                        <button @click="resetFilters"
                            class="px-4 py-2 rounded bg-gray-500 text-white hover:bg-gray-600">
                            <i class="pi pi-times mr-1"></i> Reset
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-gray-900 shadow-sm rounded overflow-hidden">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800 text-left">
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Judul</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Nomor</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Tahun</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Tipe</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Status</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Ringkasan
                                </th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">File</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in props.items?.data ?? []" :key="item.id"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-4 py-3 align-top cursor-pointer" @click="goToDetail(item.id)">
                                    <div class="font-semibold text-primary hover:text-primary-hover hover:underline">
                                        {{ item.judul_peraturan || item.title }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ item.nomor_peraturan || '-'
                                        }}</div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ item.tahun_peraturan || '-'
                                        }}</div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        <span v-if="item.tipe_dokumen">
                                            {{ item.tipe_dokumen }}
                                        </span>
                                        <span v-else class=" text-gray-400">-</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div v-if="item.status_peraturan">
                                        <span :class="[
                                            'inline-flex items-center px-2 py-1 text-xs rounded-full font-medium',
                                            item.status_peraturan === 'Berlaku'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                        ]">
                                            <i :class="[
                                                'pi mr-1 text-xs',
                                                item.status_peraturan === 'Berlaku' ? 'pi-check-circle' : 'pi-info-circle'
                                            ]"></i>
                                            {{ item.status_peraturan }}
                                        </span>
                                    </div>
                                    <span v-else class="text-gray-400 text-xs">-</span>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ truncateHtml(item.content,
                                        160) }}</div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div>
                                        <a v-if="item.file_url" :href="item.file_url" target="_blank" rel="noopener"
                                            class="inline-flex items-center gap-1 text-red-600 hover:text-red-700 hover:underline">
                                            <i class="pi pi-file-pdf"></i>
                                            <span class="text-xs">PDF</span>
                                        </a>
                                        <span v-else class="text-gray-400 text-xs">Tidak ada</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <button @click="goToDetail(item.id)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-primary text-white rounded hover:bg-primary-hover transition-colors">
                                        <i class="pi pi-eye"></i>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="(props.items?.data ?? []).length === 0">
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">Tidak ada regulasi
                                    ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="props.items?.meta?.last_page > 1" class="mt-8 flex items-center justify-center gap-2">
                    <button :disabled="!props.items?.links?.prev" @click="goToUrl(props.items.links.prev)"
                        class="px-3 py-1 rounded border border-gray-300 disabled:opacity-50">Prev</button>
                    <span>Halaman {{ props.items?.meta?.current_page }} dari {{ props.items?.meta?.last_page }}</span>
                    <button :disabled="!props.items?.links?.next" @click="goToUrl(props.items.links.next)"
                        class="px-3 py-1 rounded border border-gray-300 disabled:opacity-50">Next</button>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue'

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const props = defineProps({
    items: { type: Object, required: true },
    filters: { type: Object, required: true },
    tipeDokumenOptions: { type: Array, default: () => [] },
    statusPeraturanOptions: { type: Array, default: () => [] },
})

const breadcrumbItems = [
    { label: 'Regulasi', url: '/regulasi' },
];

const heroTitle = 'Regulasi';
const heroDescription = 'Regulasi BUMD DKI Jakarta';

const search = ref(props.filters?.search ?? '')
const tipeDokumen = ref(props.filters?.tipe_dokumen ?? '')
const statusPeraturan = ref(props.filters?.status_peraturan ?? '')
const nomorPeraturan = ref(props.filters?.nomor_peraturan ?? '')
const tahunPeraturan = ref(props.filters?.tahun_peraturan ?? '')

function applySearch() {
    router.get(route('regulasi.indexPublic'), {
        search: search.value || undefined,
        tipe_dokumen: tipeDokumen.value || undefined,
        status_peraturan: statusPeraturan.value || undefined,
        nomor_peraturan: nomorPeraturan.value || undefined,
        tahun_peraturan: tahunPeraturan.value || undefined,
    }, { preserveState: true, replace: true })
}

function resetFilters() {
    search.value = ''
    tipeDokumen.value = ''
    statusPeraturan.value = ''
    nomorPeraturan.value = ''
    tahunPeraturan.value = ''
    applySearch()
}

function goToUrl(url) {
    if (!url) return
    router.visit(url, { preserveState: true, preserveScroll: true })
}

function goToDetail(id) {
    router.visit(route('regulasi.showPublic', id))
}

function formatDate(dateString) {
    if (!dateString) return '-'
    try {
        return new Date(dateString).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
    } catch (e) { return dateString }
}

function truncateHtml(html, maxLength) {
    const div = document.createElement('div')
    div.innerHTML = html || ''
    const text = div.textContent || div.innerText || ''
    return text.length > maxLength ? text.slice(0, maxLength) + '…' : text
}

</script>