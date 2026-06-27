<script setup>
// Helper for avatar fallback
function getAvatarUrl(url) {
    return url || '/images/default-cover.png';
}
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Avatar from 'primevue/avatar';
import Badge from 'primevue/badge';
import Button from 'primevue/button';

const props = defineProps({
    pejabat: { type: Object, required: true },
});

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'full', timeStyle: 'short' }).format(date);
}
</script>

<template>
    <Head :title="`Detail: ${props.pejabat.nama}`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Pejabat: {{ props.pejabat.nama }}
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('pejabats.index')" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 text-gray-900 rounded">
                        <i class="pi pi-arrow-left mr-2"></i> Kembali
                    </Link>
                    <Link :href="route('pejabats.edit', props.pejabat.id)" class="bg-primary hover:bg-primary-hover px-4 py-2 text-white rounded">
                        <i class="pi pi-pencil mr-2"></i> Edit
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl w-full px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                    <div class="p-8 flex flex-col sm:flex-row items-center gap-8 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                        <div class="relative">
                            <img
                                :src="getAvatarUrl(props.pejabat.image_url)"
                                alt="Foto Pejabat"
                                class="h-36 w-36 rounded-full object-cover object-top border-4 border-white shadow-lg bg-gray-200"
                                loading="lazy"
                                @error="event => event.target.src = '/images/default-cover.png'"
                            />
                        </div>
                        <div class="flex-1 text-center sm:text-left mt-4 sm:mt-0">
                            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ props.pejabat.nama }}</h3>
                            <p class="text-lg text-indigo-700 font-medium">{{ props.pejabat.jabatan }}</p>
                            <div class="mt-3 flex justify-center sm:justify-start">
                                <Badge :severity="props.pejabat.published ? 'success' : 'warning'"
                                       :value="props.pejabat.published ? 'Published' : 'Draft'" />
                            </div>
                        </div>
                    </div>
                    <div class="p-8 space-y-8">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">TTL</dt>
                                <dd class="mt-1 text-base text-gray-900">{{ props.pejabat.ttl || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pendidikan</dt>
                                <dd class="mt-1 text-base text-gray-900">{{ props.pejabat.pendidikan || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Urutan (Order)</dt>
                                <dd class="mt-1 text-base text-gray-900">{{ props.pejabat.order || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Update Terakhir</dt>
                                <dd class="mt-1 text-base text-gray-900">{{ formatDate(props.pejabat.updated_at) }}</dd>
                            </div>
                        </dl>
                        <div class="pt-4 border-t">
                            <dt class="text-base font-semibold text-gray-700 mb-2">Deskripsi</dt>
                            <dd class="mt-1 prose prose-sm max-w-none text-gray-800"
                                 v-html="props.pejabat.description || '<p class=\'italic text-gray-500\'>Tidak ada deskripsi.</p>'">
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>