<script setup>

import { ref, onMounted, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { z } from 'zod';

import ToggleSwitch from 'primevue/toggleswitch';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

import { useSweetAlert } from '@/Composables/useSweetAlert.js';
import FormInput from '@/Components/FormInput.vue';
import FormLinkSelector from '@/Components/FormLinkSelector.vue';
import FormImageUpload from '@/Components/FormImageUpload.vue';

const swal = useSweetAlert();

// Props
const props = defineProps({
  mode: { type: String, default: 'create' },
  slider: { type: Object, default: null },
  submitLabel: { type: String, default: '' },
  beritas: { type: Array, default: () => [] },
  albums: { type: Array, default: () => [] },
});

const uploadMode = ref('file');
const previewUrl = ref('');

const form = useForm({
  title: {
    id: props.slider?.title_translations?.id ?? props.slider?.title ?? '',
    en: props.slider?.title_translations?.en ?? '',
  },
  description: {
    id: props.slider?.description_translations?.id ?? (props.slider?.description || ''),
    en: props.slider?.description_translations?.en ?? '',
  },
  image: props.slider?.image ?? null,
  link: props.slider?.link ?? '',
  published: props.slider?.published ?? true,
});

// Validation schema
const imageSchema = z.union([
  z
    .string(),
  z
    .instanceof(File)
    .refine((file) => file.type?.startsWith('image/'), 'File harus berupa gambar')
    .refine((file) => file.size <= 5 * 1024 * 1024, 'Ukuran gambar maksimal 5MB'),
]);

const schema = z.object({
  title: z.object({
    id: z.string().min(1, 'Judul (ID) wajib diisi').max(255, 'Judul maksimal 255 karakter'),
    en: z.string().optional(),
  }),
  description: z.object({
    id: z.string().optional(),
    en: z.string().optional(),
  }).optional(),
  image: imageSchema,
  link: z.string().optional(),
  published: z.boolean(),
});

// Form setup
onMounted(() => {
  if (props.slider?.image && typeof props.slider.image === 'string') {
    uploadMode.value = 'url';
    previewUrl.value = props.slider?.image_url || props.slider.image;
    form.image = props.slider.image;
  } else {
    uploadMode.value = 'file';
  }
});

// Form submission
const resolvedSubmitLabel = computed(() => {
  if (props.submitLabel) return props.submitLabel;
  return props.mode === 'edit' ? 'Update' : 'Simpan';
});

function submit() {
  form.clearErrors();
  const result = schema.safeParse({
    title: form.title,
    description: form.description,
    image: form.image,
    link: form.link ?? '',
    published: !!form.published,
  });

  if (!result.success) {
    for (const issue of result.error.issues) {
      const path = issue.path?.join('.') || 'form';
      form.setError(path, issue.message);
    }

    swal.validationError();
    return;
  }

  const isFileUpload = uploadMode.value === 'file';

  // Edit mode: update existing slider
  if (props.mode === 'edit' && (props.slider?.id ?? null) !== null) {

    if (isFileUpload) {
      form
        .transform((data) => ({ ...data, _method: 'put' }))
        .post(route('content-sliders.update', props.slider.id), {
          forceFormData: true,
          onFinish: () => {
            form.transform((data) => data);
          },
          onSuccess: () => {
            form.link = '';
            form.image = null;
            previewUrl.value = '';
            swal.success('Slider berhasil diubah.');
          },
          onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join('\n');
            swal.error(errorMessages || 'Terjadi kesalahan saat menyimpan data');
          },
        });
    } else {
      form.put(route('content-sliders.update', props.slider.id), {
        onSuccess: () => {
          form.link = '';
          form.image = null;
          previewUrl.value = '';
          swal.success('Slider berhasil diubah.');
        },
        onError: (errors) => {
          const errorMessages = Object.values(errors).flat().join('\n');
          swal.error(errorMessages || 'Terjadi kesalahan saat menyimpan data');
        },
      });
    }
  } else {
    form.post(route('content-sliders.store'), {
      forceFormData: isFileUpload,
      onSuccess: () => {
        form.link = '';
        form.image = null;
        previewUrl.value = '';
        swal.success('Slider berhasil dibuat.');
      },

      onError: (errors) => {
        const errorMessages = Object.values(errors).flat().join('\n');
        swal.error(errorMessages || 'Terjadi kesalahan saat menyimpan data');
      },
    });
  }
}
</script>

<template>
  <div class="p-6 space-y-4">
    <Tabs value="0">
      <TabList>
        <Tab value="0">Indonesia</Tab>
        <Tab value="1">English</Tab>
      </TabList>
      <TabPanels>
        <TabPanel value="0">
          <div class="flex flex-col space-y-4">
            <FormInput v-model="form.title.id" label="Judul (ID)" type="text" :error="form.errors['title.id']"
              :required="true" :autofocus="true" />
            <FormInput v-model="form.description.id" label="Deskripsi (ID)" type="textarea"
              :error="form.errors['description.id']" :rows="4" />
          </div>
        </TabPanel>
        <TabPanel value="1">
          <div class="flex flex-col space-y-4">
            <FormInput v-model="form.title.en" label="Title (EN)" type="text" :error="form.errors['title.en']" />
            <FormInput v-model="form.description.en" label="Description (EN)" type="textarea"
              :error="form.errors['description.en']" :rows="4" />
          </div>
        </TabPanel>
      </TabPanels>
    </Tabs>

    <!-- Link -->
    <div class="px-5">
      <FormLinkSelector
        v-model="form.link"
        :beritas="props.beritas"
        :albums="props.albums"
        :error="form.errors.link"
      />
    </div>

    <!-- Image Upload -->
    <div class="px-5">
      <FormImageUpload
        v-model="form.image"
        v-model:preview-url="previewUrl"
        v-model:upload-mode="uploadMode"
        :error="form.errors.image"
      />
    </div>

    <!-- Upload progress -->
    <div v-if="form.progress" class="mt-2">
      <div class="h-2 w-full rounded bg-gray-200">
        <div class="h-2 rounded bg-indigo-600" :style="{ width: `${form.progress.percentage}%` }"></div>
      </div>
      <div class="mt-1 text-xs text-gray-500">Mengunggah: {{ form.progress.percentage }}%</div>
    </div>

    <div class="flex flex-row justify-between items-center gap-2 w-full px-5">

      <div class="flex items-center gap-3 py-4">
        <span class="text-sm text-gray-700 font-bold">Published</span>
        <ToggleSwitch v-model="form.published" />
      </div>

      <div class="inline-flex gap-4">
        <button @click="submit" :disabled="form.processing"
          class="inline-flex items-center rounded bg-primary px-4 py-2 text-white hover:bg-primary-hover disabled:opacity-50">{{
          resolvedSubmitLabel }}</button>
        <button @click="() => router.visit(route('content-sliders.index'))" type="button"
          class="inline-flex items-center rounded bg-gray-200 px-4 py-2 hover:bg-gray-300">Batal</button>
      </div>
    </div>

  </div>
</template>