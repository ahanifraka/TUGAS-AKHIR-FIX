<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useSweetAlert } from '@/Composables/useSweetAlert.js';
import FormInput from '@/Components/FormInput.vue';
import FormImageUpload from '@/Components/FormImageUpload.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ToggleSwitch from 'primevue/toggleswitch';

const props = defineProps({
  mode: { type: String, default: 'create' }, // 'create' | 'edit'
  album: { type: Object, default: () => ({}) },
  images: { type: Array, default: () => [] }, // existing images for edit
});

const swal = useSweetAlert();

// Cover image preview & URL
const initialHasImageString = typeof props.album?.image === 'string' && props.album.image?.length > 0;
const previewUrl = ref(initialHasImageString ? props.album.image : null);

// Album image new entry
const newImage = ref({
  image: null,
  title: { id: '', en: '' },
  description: { id: '', en: '' },
  published: true,
});
const newImagePreview = ref(null);

// Form state
const form = useForm({
  title: props.album?.title_translations ?? { id: props.album?.title ?? '', en: '' },
  description: props.album?.description_translations ?? { id: props.album?.description ?? '', en: '' },
  image: props.album?.image ?? null,
  published: !!(props.album?.published ?? true),
  images: [],
});

function addImage() {
  if (!newImage.value.image && typeof newImage.value.image !== 'string') return;
  
  form.images.push({
    image: newImage.value.image,
    title: { ...newImage.value.title },
    description: { ...newImage.value.description },
    published: !!newImage.value.published,
    imagePreview: newImagePreview.value,
  });
  // reset
  newImage.value = { image: null, title: { id: '', en: '' }, description: { id: '', en: '' }, published: true };
  newImagePreview.value = null;
}

function removeImageAt(index) {
  form.images.splice(index, 1);
}

const resolvedSubmitLabel = computed(() => (props.mode === 'edit' ? 'Update' : 'Simpan'));

function submit() {
  // Determine if any file uploads are present (cover or nested)
  const isCoverFile = form.image instanceof File;
  const hasNestedFile = Array.isArray(form.images) && form.images.some((img) => img.image instanceof File);
  const needsFormData = isCoverFile || hasNestedFile;

  if (props.mode === 'edit' && (props.album?.id ?? null) !== null) {
    if (needsFormData) {
      form
        .transform((data) => ({ ...data, _method: 'put' }))
        .post(route('albums.update', props.album.id), {
          forceFormData: true,
          onFinish: () => form.transform((d) => d),
          onSuccess: () => {
            swal.success('Album berhasil diperbarui.');
          },
          onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join('\n');
            swal.error(errorMessages || 'Terjadi kesalahan saat menyimpan data');
          },
        });
    } else {
      form.put(route('albums.update', props.album.id), {
        onSuccess: () => {
          swal.success('Album berhasil diperbarui.');
        },
        onError: (errors) => {
          const errorMessages = Object.values(errors).flat().join('\n');
          swal.error(errorMessages || 'Terjadi kesalahan saat menyimpan data');
        },
      });
    }
  } else {
    form.post(route('albums.store'), {
      forceFormData: needsFormData,
      onSuccess: () => {
        // reset simple states
        form.reset();
        previewUrl.value = null;
        swal.success('Album berhasil disimpan.');
        router.visit(route('albums.index'));
      },
      onError: (errors) => {
        const errorMessages = Object.values(errors).flat().join('\n');
        swal.error(errorMessages || 'Terjadi kesalahan saat menyimpan album.', 'Gagal Menyimpan Album');
      },
    });
  }
}

const deleteImage = (imageId) => {
    swal.confirmDelete().then((confirmed) => {
        if (confirmed) {
            router.delete(route('albums.images.destroy', [props.album.id, imageId]), {
                preserveScroll: true,
                onSuccess: () => swal.success('Gambar dihapus.', 'Berhasil', 1200)
            });
        }
    });
};
</script>

<template>
  <div class="p-6 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <FormInput
        v-model="form.title.id"
        label="Judul (ID)"
        :error="form.errors['title.id'] || form.errors.title"
        required
      />
      <FormInput
        v-model="form.title.en"
        label="Title (EN)"
        :error="form.errors['title.en']"
      />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <FormInput
        v-model="form.description.id"
        label="Deskripsi (ID)"
        type="textarea"
        :rows="4"
        :error="form.errors['description.id'] || form.errors.description"
      />
      <FormInput
        v-model="form.description.en"
        label="Description (EN)"
        type="textarea"
        :rows="4"
        :error="form.errors['description.en']"
      />
    </div>

    <div>
      <FormImageUpload
        v-model="form.image"
        v-model:preview-url="previewUrl"
        label="Gambar Sampul"
        :error="form.errors.image"
      />
    </div>

    <div class="flex items-center gap-2">
      <label for="published" class="text-sm text-gray-700">Published</label>
      <ToggleSwitch id="published" v-model="form.published" />
    </div>

    <!-- Existing Images (Edit Mode) -->
    <div v-if="props.mode === 'edit'" class="border-t pt-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Existing Images</h3>
        <PrimaryButton
            @click="router.visit(route('albums.images.create', props.album.id))"
            class="bg-primary hover:bg-primary-hover focus:ring-green-500 text-xs"
        >
            <i class="pi pi-plus mr-1"></i> Tambah Gambar
        </PrimaryButton>
      </div>
      <div v-if="(props.images?.length ?? 0) > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div v-for="image in props.images" :key="image.id" class="bg-white border rounded-lg p-4 flex flex-col gap-3">
          <img :src="image.image" :alt="image.title || 'Album image'" class="w-full h-40 object-cover rounded" />
          <div>
            <p class="font-medium text-gray-900">{{ image.title || 'Untitled' }}</p>
            <p class="text-sm text-gray-600">{{ image.description || 'No description' }}</p>
            <p class="text-xs mt-1" :class="image.published ? 'text-green-600' : 'text-gray-500'">{{ image.published ? 'Published' : 'Draft' }}</p>
          </div>
          <div class="flex gap-2">
            <PrimaryButton
              @click="router.visit(route('albums.images.edit', [props.album.id, image.id]))"
              class="text-xs px-3 py-2"
            >
              Edit
            </PrimaryButton>
            <DangerButton
              @click="deleteImage(image.id)"
              class="text-xs px-3 py-2"
            >
              Delete
            </DangerButton>
          </div>
        </div>
      </div>
      <div v-else class="bg-gray-50 p-4 rounded">
        <p class="text-sm text-gray-700">Belum ada gambar di album ini.</p>
      </div>
    </div>

    <!-- Multiple Images Section (Create Mode Only) -->
    <div v-if="props.mode === 'create'" class="border-t pt-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Album Images</h3>

      <!-- Add New Image Form -->
      <div class="bg-gray-50 p-4 rounded-lg mb-4">
        <h4 class="text-md font-medium text-gray-700 mb-3">Add New Image</h4>
        <div class="space-y-3">
          <FormImageUpload
            v-model="newImage.image"
            v-model:preview-url="newImagePreview"
            label="Upload Image"
          />
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormInput v-model="newImage.title.id" label="Image Title (ID)" placeholder="Judul gambar" />
            <FormInput v-model="newImage.title.en" label="Image Title (EN)" placeholder="Image title" />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormInput v-model="newImage.description.id" label="Image Description (ID)" type="textarea" :rows="2" placeholder="Deskripsi gambar" />
            <FormInput v-model="newImage.description.en" label="Image Description (EN)" type="textarea" :rows="2" placeholder="Image description" />
          </div>
          <div class="flex items-center gap-2">
            <label for="newImagePublished" class="text-sm text-gray-700">Published</label>
            <ToggleSwitch id="newImagePublished" v-model="newImage.published" />
          </div>
          <PrimaryButton @click="addImage" class="bg-green-600 hover:bg-green-700 focus:ring-green-500">Add Image</PrimaryButton>
        </div>
      </div>

      <!-- Images List -->
      <div v-if="form.images.length > 0" class="space-y-3">
        <h4 class="text-md font-medium text-gray-700">Added Images ({{ form.images.length }})</h4>
        <div v-for="(image, index) in form.images" :key="index" class="bg-white border rounded-lg p-4">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <img :src="image.imagePreview" :alt="image.title?.id || 'Album image'" class="w-16 h-16 object-cover rounded" />
                <div>
                  <p class="font-medium text-gray-900">{{ image.title?.id || 'Untitled' }}</p>
                  <p class="text-sm text-gray-600">{{ image.description?.id || 'No description' }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <label :for="`imgPublished-${index}`" class="text-sm text-gray-700">Published</label>
                <ToggleSwitch :id="`imgPublished-${index}`" v-model="image.published" />
              </div>
            </div>
            <DangerButton @click="removeImageAt(index)">Remove</DangerButton>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload progress -->
    <div v-if="form.progress" class="mt-2">
      <div class="h-2 w-full rounded bg-gray-200">
        <div class="h-2 rounded bg-indigo-600" :style="{ width: `${form.progress.percentage}%` }"></div>
      </div>
      <div class="mt-1 text-xs text-gray-500">Mengunggah: {{ form.progress.percentage }}%</div>
    </div>

    <div class="flex gap-2">
      <PrimaryButton @click="submit" :disabled="form.processing" class="disabled:opacity-50">{{ resolvedSubmitLabel }}</PrimaryButton>
      <SecondaryButton @click="() => router.visit(route('albums.index'))">Batal</SecondaryButton>
    </div>
  </div>
</template>