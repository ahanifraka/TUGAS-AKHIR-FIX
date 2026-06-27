<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    pengumuman: Object,
});
</script>

<template>
    <Head :title="pengumuman.judul" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">Detail Pengumuman</h2>
                <Link
                    :href="route('admin.pengumuman.index')"
                    class="bg-gray-600 text-white px-4 py-2 rounded"
                >
                    Kembali
                </Link>
            </div>
        </template>

        <div class="p-6 max-w-5xl mx-auto bg-white rounded shadow">
            <!-- Info Status -->
            <div class="mb-4 flex flex-wrap gap-2">
                <span
                    class="inline-block px-3 py-1 text-sm rounded-full"
                    :class="
                        pengumuman.is_aktif
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700'
                    "
                >
                    {{ pengumuman.is_aktif ? "Aktif" : "Nonaktif" }}
                </span>

                <span
                    v-if="pengumuman.is_penting"
                    class="inline-block px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-700"
                >
                    ⭐ Penting
                </span>

                <span
                    v-if="pengumuman.is_popular"
                    class="inline-block px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-700"
                >
                    🔥 Popular
                </span>
            </div>

            <!-- Judul -->
            <h1 class="text-3xl font-bold mb-4">
                {{ pengumuman.judul }}
            </h1>

            <!-- Slug (SEO) -->
            <div class="mb-2">
                <span class="text-sm text-gray-500">Slug: </span>
                <code class="text-sm bg-gray-100 px-2 py-1 rounded">
                    {{ pengumuman.slug || "-" }}
                </code>
            </div>

            <!-- Meta Data -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded"
            >
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">
                        Informasi Publikasi
                    </h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex">
                            <span class="w-40 text-gray-600"
                                >Tanggal Terbit:</span
                            >
                            <span>{{ pengumuman.tanggal_terbit || "-" }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-40 text-gray-600"
                                >Nomor Pengumuman:</span
                            >
                            <span>{{
                                pengumuman.nomor_pengumuman || "-"
                            }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-40 text-gray-600">Tipe:</span>
                            <span>{{ pengumuman.tipe || "-" }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-40 text-gray-600"
                                >Status Publikasi:</span
                            >
                            <span
                                class="px-2 py-1 rounded text-xs"
                                :class="
                                    pengumuman.is_published
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-gray-100 text-gray-700'
                                "
                            >
                                {{
                                    pengumuman.is_published
                                        ? "Published"
                                        : "Draft"
                                }}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">
                        Informasi SEO
                    </h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex">
                            <span class="w-40 text-gray-600">Meta Title:</span>
                            <span class="truncate">{{
                                pengumuman.meta_title || "-"
                            }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-40 text-gray-600"
                                >Meta Keywords:</span
                            >
                            <span class="truncate">{{
                                pengumuman.meta_keywords || "-"
                            }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-600">Meta Description:</span>
                            <span
                                class="text-sm text-gray-700 mt-1 line-clamp-2"
                            >
                                {{ pengumuman.meta_description || "-" }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gambar -->
            <div v-if="pengumuman.image_url" class="mb-6">
                <img
                    :src="pengumuman.image_url"
                    :alt="pengumuman.judul"
                    class="w-full max-h-[400px] object-cover rounded"
                />
                <p class="text-xs text-gray-500 mt-1">
                    Image URL: {{ pengumuman.image_url }}
                </p>
            </div>

            <!-- Teaser -->
            <div v-if="pengumuman.teaser" class="mb-6 p-4 bg-blue-50 rounded">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Teaser</h3>
                <p class="text-gray-700 italic">{{ pengumuman.teaser }}</p>
            </div>

            <!-- Konten -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Konten</h3>
                <div
                    class="prose max-w-none border rounded p-4"
                    v-html="pengumuman.konten"
                />
            </div>

            <!-- Dokumen -->
            <div v-if="pengumuman.dokumen" class="mb-6 p-4 bg-gray-50 rounded">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">
                    Dokumen Terlampir
                </h3>
                <a
                    :href="pengumuman.dokumen"
                    target="_blank"
                    class="inline-flex items-center gap-2 text-blue-600 hover:underline"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                    Unduh Dokumen
                </a>
                <p class="text-xs text-gray-500 mt-1">
                    URL: {{ pengumuman.dokumen }}
                </p>
            </div>

            <!-- Informasi Pembuat/Update -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500"
                >
                    <div>
                        <span class="font-medium">Dibuat oleh:</span>
                        {{ pengumuman.created_by || "System" }}
                        <span
                            v-if="pengumuman.created_at"
                            class="block text-xs"
                        >
                            pada {{ pengumuman.created_at }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium">Diupdate oleh:</span>
                        {{ pengumuman.updated_by || "System" }}
                        <span
                            v-if="pengumuman.updated_at"
                            class="block text-xs"
                        >
                            pada {{ pengumuman.updated_at }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div
                class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3"
            >
                <Link
                    :href="route('admin.pengumuman.edit', pengumuman.id)"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                >
                    Edit Pengumuman
                </Link>
                <Link
                    :href="route('admin.pengumuman.index')"
                    class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"
                >
                    Kembali ke Daftar
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
