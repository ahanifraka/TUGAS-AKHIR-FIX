<script setup>
import { Head, router } from '@inertiajs/vue3';
import { reactive, watch, ref } from 'vue';
import { z } from 'zod';
import ToggleSwitch from 'primevue/toggleswitch';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FormInput from '@/Components/FormInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useSweetAlert } from '@/Composables/useSweetAlert.js';

// Props
const props = defineProps({
    formTitle: { type: String, default: 'Form Kategori Berita' },
    submitUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
    category: { type: Object, default: () => ({}) },
});

// Composables
const { success, error, confirm, loading, custom } = useSweetAlert();

// Zod validation schema
const categorySchema = z.object({
    category_name: z.string()
        .min(1, 'Nama kategori wajib diisi')
        .max(255, 'Nama kategori maksimal 255 karakter')
        .trim(),
    category_slug: z.string()
        .min(1, 'Slug wajib diisi')
        .max(255, 'Slug maksimal 255 karakter')
        .regex(/^[a-z0-9]+(?:-[a-z0-9]+)*$/, 'Slug hanya boleh mengandung huruf kecil, angka, dan tanda hubung. Tidak boleh diawali atau diakhiri dengan tanda hubung')
        .trim(),
    is_active: z.boolean().default(true),
});

// Forms
const form = reactive({
    category_name: props.category.category_name ?? '',
    category_slug: props.category.category_slug ?? '',
    is_active: props.category.is_active ?? true,
});

// Validation errors
const errors = ref({});
const hasErrors = ref(false);
const isAutoGeneratingSlug = ref(false);

// Function to generate slug from category name
function generateSlug(text) {
    return text
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '') // Remove special characters
        .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
        .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
}

// Watch for changes in category_name to auto-generate slug
watch(() => form.category_name, (newName) => {
    if (newName) {
        isAutoGeneratingSlug.value = true;
        form.category_slug = generateSlug(newName);
        setTimeout(() => { isAutoGeneratingSlug.value = false; }, 0);
    } else {
        isAutoGeneratingSlug.value = true;
        form.category_slug = '';
        setTimeout(() => { isAutoGeneratingSlug.value = false; }, 0);
    }

    // Clear errors when user types
    if (errors.value.category_name) {
        delete errors.value.category_name;
        hasErrors.value = Object.keys(errors.value).length > 0;
    }
}, { immediate: true });

// Watch for slug changes to clear errors
watch(() => form.category_slug, (newSlug) => {
    // Only clear errors if user is manually editing slug
    if (!isAutoGeneratingSlug.value) {
        if (errors.value.category_slug) {
            delete errors.value.category_slug;
            hasErrors.value = Object.keys(errors.value).length > 0;
        }
    }
});

// Validate form function
function validateForm() {
    try {
        categorySchema.parse(form);
        errors.value = {};
        hasErrors.value = false;
        return true;
    } catch (error) {
        if (error instanceof z.ZodError) {
            const newErrors = {};

            // Use Zod's issues array to collect messages
            const issues = error.issues;
            if (Array.isArray(issues)) {
                issues.forEach((issue) => {
                    if (issue.path && issue.path.length > 0) {
                        const fieldName = issue.path[0];
                        // Use the first error message for each field
                        if (!newErrors[fieldName]) {
                            newErrors[fieldName] = issue.message;
                        }
                    }
                });
            }

            errors.value = newErrors;
            hasErrors.value = Object.keys(newErrors).length > 0;

            // Log for debugging purposes but don't reset errors
            if (Object.keys(newErrors).length === 0) {
                console.warn('Zod validation failed but no field errors were extracted:', error);
            }
        } else {
            // Handle non-Zod errors
            console.error('Unexpected validation error:', error);

            // Set a general error message for unexpected errors
            errors.value = {
                general: 'Terjadi kesalahan validasi yang tidak terduga. Silakan periksa kembali data Anda.'
            };
            hasErrors.value = true;
        }
        return false;
    }
}

watch(() => props.category, (val) => {
    Object.assign(form, {
        category_name: val?.category_name ?? '',
        category_slug: val?.category_slug ?? '',
        is_active: val?.is_active ?? true,
    });
});

async function submit() {
    // Validate form before submitting
    if (!validateForm()) {
        // Get all error messages for display
        const errorMessages = Object.values(errors.value);
        const errorText = errorMessages.length > 0
            ? `Mohon perbaiki kesalahan berikut:\n• ${errorMessages.join('\n• ')}`
            : 'Mohon periksa kembali data yang Anda masukkan.';

        custom({
            title: 'Validasi Gagal!',
            html: errorText.replace(/\n/g, '<br>'),
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                htmlContainer: 'text-left'
            }
        });
        return;
    }

    // Show confirmation dialog before submitting
    const isConfirmed = await confirm(
        'Apakah Anda yakin ingin menyimpan data kategori berita ini?',
        'Konfirmasi',
        'Ya, Simpan!',
        'Batal'
    );

    if (isConfirmed) {
        const data = { ...form };
        if (props.method.toLowerCase() !== 'post') {
            data._method = props.method.toUpperCase();
        }

        // Show loading state
        loading('Menyimpan...');

        router.post(props.submitUrl, data, {
            onSuccess: () => {
                success('Data kategori berita berhasil disimpan.', 'Berhasil!');
            },
            onError: (serverErrors) => {
                // Normalize and merge server-side validation errors
                const normalizedErrors = {};
                Object.entries(serverErrors).forEach(([key, val]) => {
                    normalizedErrors[key] = Array.isArray(val) ? val[0] : val;
                });
                errors.value = normalizedErrors;
                hasErrors.value = Object.keys(errors.value).length > 0;

                const errorMessages = Object.values(serverErrors).flatMap((v) => Array.isArray(v) ? v : [v]);
                const errorText = errorMessages.length > 0
                    ? `Mohon perbaiki kesalahan berikut:\n• ${errorMessages.join('\n• ')}`
                    : 'Terjadi kesalahan saat menyimpan data.';

                custom({
                    title: 'Validasi Gagal!',
                    html: errorText.replace(/\n/g, '<br>'),
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        htmlContainer: 'text-left'
                    }
                });
            },
            onFinish: () => {
                // Close loading if still open; new alert will replace it
            },
            preserveScroll: true
        });
    }
}
</script>

<template>
    <Head :title="formTitle" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ formTitle }}</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">

                        <!-- Error Summary Section -->
                        <div v-if="hasErrors" class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="pi pi-exclamation-triangle text-red-400" style="font-size: 1rem"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        Terdapat {{ Object.keys(errors).length }} kesalahan yang perlu diperbaiki:
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li v-for="(error, field) in errors" :key="field">
                                                <strong>{{
                                                    field === 'category_name' ? 'Nama Kategori' :
                                                        field === 'category_slug' ? 'Slug' :
                                                            field === 'general' ? 'Umum' :
                                                                field
                                                }}:</strong> {{ error }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <FormInput
                            v-model="form.category_name"
                            label="Nama Kategori"
                            placeholder="Masukkan nama kategori"
                            :error="errors.category_name"
                            :required="true"
                        />

                        <FormInput
                            v-model="form.category_slug"
                            label="Slug"
                            placeholder="slug-kategori"
                            :error="errors.category_slug"
                            :required="true"
                        />

                        <div class="flex items-center gap-3">
                            <ToggleSwitch v-model="form.is_active" inputId="is_active" />
                            <label for="is_active" class="text-sm text-gray-700">
                                {{ form.is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </label>
                        </div>

                        <div class="pt-4 flex justify-between gap-2 w-full">
                            <div class="pt-4 flex justify-end gap-2 w-full">
                                <PrimaryButton @click="submit">
                                    Simpan
                                </PrimaryButton>
                                <SecondaryButton @click="() => router.visit(route('berita-categories.index'))">
                                    Batal
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>