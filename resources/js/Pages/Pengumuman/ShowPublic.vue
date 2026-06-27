<script setup>
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    pengumuman: Object,
});
</script>

<template>
    <Head :title="pengumuman.judul" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-5xl mx-auto px-4 py-6">
                <Link
                    :href="route('admin.pengumuman.index')"
                    class="text-blue-600 hover:underline text-sm"
                >
                    ← Kembali ke Pengumuman
                </Link>

                <h1 class="text-3xl font-bold mt-2">
                    {{ pengumuman.judul }}
                </h1>

                <div class="text-sm text-gray-500 mt-2 flex flex-wrap gap-3">
                    <span>{{ pengumuman.tanggal_terbit }}</span>
                    <span>• {{ pengumuman.tipe }}</span>
                    <span v-if="pengumuman.is_penting" class="text-yellow-600">
                        ⭐ Penting
                    </span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-5xl mx-auto px-4 py-8 bg-white shadow rounded mt-6">
            <!-- Gambar -->
            <div v-if="pengumuman.image_url" class="mb-6">
                <img
                    :src="pengumuman.image_url"
                    class="w-full max-h-[420px] object-cover rounded"
                />
            </div>

            <!-- Nomor -->
            <div
                v-if="pengumuman.nomor_pengumuman"
                class="text-sm text-gray-500 mb-4"
            >
                Nomor Pengumuman: {{ pengumuman.nomor_pengumuman }}
            </div>

            <!-- Konten -->
            <div class="prose max-w-none" v-html="pengumuman.konten" />

            <!-- Dokumen -->
            <div v-if="pengumuman.dokumen" class="mt-8 border-t pt-4">
                <a
                    :href="pengumuman.dokumen"
                    target="_blank"
                    class="inline-flex items-center text-blue-600 hover:underline"
                >
                    📄 Unduh Lampiran
                </a>
            </div>
        </div>
    </div>
</template>
