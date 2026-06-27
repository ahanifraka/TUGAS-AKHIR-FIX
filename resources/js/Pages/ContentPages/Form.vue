<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FormInput from '@/Components/FormInput.vue';
import FormImageUpload from '@/Components/FormImageUpload.vue';
import FormRichTextEditor from '@/Components/FormRichTextEditor.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ref, watch } from 'vue';
import ToggleSwitch from 'primevue/toggleswitch';
import { useSweetAlert } from '@/Composables/useSweetAlert.js';

const props = defineProps({
    formTitle: { type: String, default: 'Form Content Page' },
    submitUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
    page: { type: Object, default: () => ({}) },
});

const { success, validationError } = useSweetAlert();
const imagePreviewUrl = ref('');

const form = useForm({
    _method: props.method === 'put' ? 'put' : 'post',
    title: props.page.title ?? '',
    slug: props.page.slug ?? '',
    subtitle: props.page.subtitle ?? '',
    description: props.page.description ?? '',
    description2: props.page.description2 ?? '',
    image: props.page.image ?? '',
    metadata_title: props.page.metadata_title ?? '',
    metadata_keywords: props.page.metadata_keywords ?? '',
    metadata_description: props.page.metadata_description ?? '',
    upload_files: props.page.upload_files ?? '',
    published: props.page.published ?? true,
});

// Initialize image preview URL
if (props.page.image) {
    imagePreviewUrl.value = props.page.image.startsWith('http') 
        ? props.page.image 
        : '/' + props.page.image;
}

watch(() => props.page, (val) => {
    form.title = val?.title ?? '';
    form.slug = val?.slug ?? '';
    form.subtitle = val?.subtitle ?? '';
    form.description = val?.description ?? '';
    form.description2 = val?.description2 ?? '';
    form.image = val?.image ?? '';
    form.metadata_title = val?.metadata_title ?? '';
    form.metadata_keywords = val?.metadata_keywords ?? '';
    form.metadata_description = val?.metadata_description ?? '';
    form.upload_files = val?.upload_files ?? '';
    form.published = val?.published ?? true;
    
    // Update image preview
    if (val?.image) {
        imagePreviewUrl.value = val.image.startsWith('http') 
            ? val.image 
            : '/' + val.image;
    }
});

function submit() {
    form.post(props.submitUrl, {
        forceFormData: true,
        onSuccess: () => {
            success('Data berhasil disimpan!');
        },
        onError: () => {
            validationError();
        },
    });
}

function cancel() {
    router.visit(route('content-pages.index'));
}
</script>

<template>
    <Head :title="formTitle" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ formTitle }}</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <!-- Title -->
                        <FormInput
                            v-model="form.title"
                            label="Judul"
                            type="text"
                            :error="form.errors.title"
                            required
                        />

                        <!-- Slug & Subtitle -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <FormInput
                                v-model="form.slug"
                                label="Slug (opsional)"
                                type="text"
                                placeholder="akan dibuat otomatis jika kosong"
                                :error="form.errors.slug"
                            />
                            <FormInput
                                v-model="form.subtitle"
                                label="Subtitle (opsional)"
                                type="text"
                                :error="form.errors.subtitle"
                            />
                        </div>

                        <!-- Description -->
                        <FormRichTextEditor
                            v-model="form.description"
                            label="Deskripsi"
                            :error="form.errors.description"
                            required
                        />

                        <!-- Description 2 -->
                        <FormRichTextEditor
                            v-model="form.description2"
                            label="Deskripsi 2 (opsional)"
                            :error="form.errors.description2"
                        />

                        <!-- Image Upload -->
                        <FormImageUpload
                            v-model="form.image"
                            v-model:preview-url="imagePreviewUrl"
                            label="Gambar (opsional)"
                            :error="form.errors.image"
                        />

                        <!-- Metadata Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <FormInput
                                v-model="form.metadata_title"
                                label="Meta Title"
                                type="text"
                                :error="form.errors.metadata_title"
                            />
                            <FormInput
                                v-model="form.metadata_keywords"
                                label="Meta Keywords"
                                type="text"
                                placeholder="keyword1, keyword2"
                                :error="form.errors.metadata_keywords"
                            />
                            <FormInput
                                v-model="form.metadata_description"
                                label="Meta Description"
                                type="text"
                                :error="form.errors.metadata_description"
                            />
                        </div>

                        <!-- Upload Files -->
                        <FormInput
                            v-model="form.upload_files"
                            label="Upload Files (opsional)"
                            type="textarea"
                            :rows="3"
                            placeholder="JSON atau teks"
                            :error="form.errors.upload_files"
                        />

                        <!-- Published Toggle -->
                        <div class="form-group">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Status Publikasi
                            </label>
                            <div class="flex items-center gap-3">
                                <ToggleSwitch v-model="form.published" inputId="published" />
                                <span class="text-sm text-gray-600">
                                    {{ form.published ? 'Dipublikasikan' : 'Draft' }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t">
                            <PrimaryButton 
                                @click="submit" 
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                            </PrimaryButton>
                            <SecondaryButton 
                                @click="cancel"
                                :disabled="form.processing"
                            >
                                Batal
                            </SecondaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>