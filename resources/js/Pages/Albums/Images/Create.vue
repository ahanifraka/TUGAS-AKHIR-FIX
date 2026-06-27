<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { useSweetAlert } from '@/Composables/useSweetAlert.js';
import FormInput from '@/Components/FormInput.vue';
import FormImageUpload from '@/Components/FormImageUpload.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
  album: { type: Object, required: true },
});

const swal = useSweetAlert();

// New image entry state
const newImage = ref({
  image: null,
  title: { id: '', en: '' },
  description: { id: '', en: '' },
  published: true,
});
const newImagePreview = ref(null);

// Form state for submission
const form = useForm({
  images: [],
});

function addImageToQueue() {
  if (!newImage.value.image) {
    swal.error('Silakan pilih gambar terlebih dahulu.');
    return;
  }
  
  form.images.push({
    image: newImage.value.image,
    title: { ...newImage.value.title },
    description: { ...newImage.value.description },
    published: !!newImage.value.published,
    imagePreview: newImagePreview.value,
  });
  
  // Reset entry form
  newImage.value = { image: null, title: { id: '', en: '' }, description: { id: '', en: '' }, published: true };
  newImagePreview.value = null;
}

function removeImageFromQueue(index) {
  form.images.splice(index, 1);
}

function submit() {
  if (form.images.length === 0) {
    swal.error('Belum ada gambar yang ditambahkan ke antrean.');
    return;
  }

  form.post(route('albums.images.store', props.album.id), {
    forceFormData: true,
    onSuccess: () => {
      swal.success('Gambar berhasil ditambahkan.');
      form.reset();
    },
    onError: (errors) => {
      const errorMessages = Object.values(errors).flat().join('\n');
      swal.error(errorMessages || 'Terjadi kesalahan saat menyimpan gambar.');
    },
  });
}
</script>

<template>
  <Head :title="`Tambah Gambar - ${props.album.title}`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Tambah Gambar ke Album: {{ props.album.title }}
        </h2>
        <button @click="() => router.visit(route('albums.show', props.album.id))"
            class="inline-flex items-center rounded bg-gray-200 px-3 py-2 hover:bg-gray-300 transition-colors">
            <i class="pi pi-arrow-left mr-2" style="font-size: 0.75rem"></i>
            Kembali
        </button>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <!-- Album Info (Read Only) -->
            <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-100">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Informasi Album</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Judul</p>
                        <p class="font-medium text-gray-900">{{ props.album.title }}</p>
                    </div>
                    <div>
                         <p class="text-sm text-gray-500">Status</p>
                         <span :class="props.album.published ? 'text-green-600' : 'text-red-600'" class="font-medium">
                            {{ props.album.published ? 'Published' : 'Draft' }}
                         </span>
                    </div>
                </div>
            </div>

            <!-- Add Image Form -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Input Gambar Baru</h3>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="space-y-4">
                        <FormImageUpload
                            v-model="newImage.image"
                            v-model:preview-url="newImagePreview"
                            label="Upload Image"
                        />
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <FormInput v-model="newImage.title.id" label="Judul Gambar (ID)" placeholder="Judul gambar" />
                            <FormInput v-model="newImage.title.en" label="Image Title (EN)" placeholder="Image title" />
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <FormInput v-model="newImage.description.id" label="Deskripsi Gambar (ID)" type="textarea" :rows="2" placeholder="Deskripsi gambar" />
                            <FormInput v-model="newImage.description.en" label="Image Description (EN)" type="textarea" :rows="2" placeholder="Image description" />
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <Checkbox id="newImagePublished" v-model:checked="newImage.published" />
                            <label for="newImagePublished" class="text-sm text-gray-700">Published</label>
                        </div>
                        
                        <div class="flex justify-end">
                            <SecondaryButton @click="addImageToQueue" class="bg-white border-gray-300 text-gray-700 hover:bg-gray-50">
                                <i class="pi pi-plus mr-2"></i> Tambahkan ke Antrean
                            </SecondaryButton>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Queue List -->
            <div v-if="form.images.length > 0" class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Antrean Upload ({{ form.images.length }})</h3>
                <div class="space-y-3">
                    <div v-for="(image, index) in form.images" :key="index" class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex items-start gap-4">
                                <img :src="image.imagePreview" class="w-24 h-24 object-cover rounded bg-gray-100" />
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ image.title.id || '(Tanpa Judul)' }}</h4>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ image.description.id || '(Tanpa Deskripsi)' }}</p>
                                    <div class="mt-2 text-xs">
                                        <span :class="image.published ? 'text-green-600 bg-green-50 px-2 py-1 rounded' : 'text-red-600 bg-red-50 px-2 py-1 rounded'">
                                            {{ image.published ? 'Published' : 'Draft' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button @click="removeImageFromQueue(index)" class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-colors">
                                <i class="pi pi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Actions -->
            <div class="flex items-center gap-4 border-t pt-6">
                <PrimaryButton @click="submit" :disabled="form.processing || form.images.length === 0" class="w-full sm:w-auto justify-center">
                    <i class="pi pi-upload mr-2"></i>
                    {{ form.processing ? 'Mengunggah...' : 'Upload Semua Gambar' }}
                </PrimaryButton>
                
                <div v-if="form.progress" class="flex-1">
                    <div class="h-2 w-full rounded bg-gray-200 overflow-hidden">
                        <div class="h-full bg-indigo-600 transition-all duration-300" :style="{ width: `${form.progress.percentage}%` }"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 text-right">{{ form.progress.percentage }}%</p>
                </div>
            </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>