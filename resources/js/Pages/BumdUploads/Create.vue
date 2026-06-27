<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    bumds: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
});

const form = useForm({
    kode_bumd: '',
    kategori_id: null,
    periode: '',
    title: '',
    keterangan: '',
    cover: '',
    file_id: null,
    akses: 'public',
});

function submit() {
    form.post(route('bumd-uploads.store'));
}
</script>

<template>
    <Head title="Tambah Upload" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah Upload</h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium">BUMD</label>
                            <select v-model="form.kode_bumd" class="mt-1 w-full rounded border px-3 py-2">
                                <option value="">-- Pilih BUMD --</option>
                                <option v-for="b in bumds" :key="b.id" :value="b.kode">{{ b.nama }} ({{ b.kode }})</option>
                            </select>
                            <div v-if="form.errors.kode_bumd" class="text-red-600 text-sm">{{ form.errors.kode_bumd }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Kategori</label>
                            <select v-model="form.kategori_id" class="mt-1 w-full rounded border px-3 py-2">
                                <option :value="null">-- Tanpa Kategori --</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Periode</label>
                            <input v-model="form.periode" type="text" class="mt-1 w-full rounded border px-3 py-2" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Judul</label>
                            <input v-model="form.title" type="text" class="mt-1 w-full rounded border px-3 py-2" />
                            <div v-if="form.errors.title" class="text-red-600 text-sm">{{ form.errors.title }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Keterangan</label>
                            <textarea v-model="form.keterangan" class="mt-1 w-full rounded border px-3 py-2"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Cover (URL atau path)</label>
                            <input v-model="form.cover" type="text" class="mt-1 w-full rounded border px-3 py-2" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Akses</label>
                            <select v-model="form.akses" class="mt-1 w-full rounded border px-3 py-2">
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button @click="submit" class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Simpan</button>
                            <button @click="() => router.visit(route('bumd-uploads.index'))" class="rounded bg-gray-300 px-4 py-2">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>