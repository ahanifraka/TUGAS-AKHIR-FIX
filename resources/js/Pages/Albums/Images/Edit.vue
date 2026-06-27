<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import FormInput from '@/Components/FormInput.vue';

const props = defineProps({
  album: { type: Object, required: true },
  image: { type: Object, required: true },
});

// Mode upload untuk mengganti gambar: 'file' atau 'url'
const uploadMode = ref('url');
const previewUrl = ref(props.image?.image || null);
const imageUrl = ref(props.image?.image || '');

const form = useForm({
  image: null, // hanya dikirim jika diganti (file atau url)
  title: props.image?.title_translations || { id: props.image?.title || '', en: '' },
  description: props.image?.description_translations || { id: props.image?.description || '', en: '' },
  published: !!props.image?.published,
});

function onFileChange(e) {
  const file = e.target.files?.[0];
  if (file) {
    uploadMode.value = 'file';
    form.image = file;
    const reader = new FileReader();
    reader.onload = (ev) => (previewUrl.value = ev.target.result);
    reader.readAsDataURL(file);
  }
}

function onImageUrlInput(val) {
  uploadMode.value = 'url';
  imageUrl.value = val;
  form.image = val && val.length > 0 ? val : null;
  previewUrl.value = val && val.length > 0 ? val : null;
}

function clearPreview() {
  form.image = null;
  imageUrl.value = '';
  previewUrl.value = null;
}

function submit() {
  const isFile = form.image instanceof File;
  const isUrl = typeof form.image === 'string' && form.image.length > 0;
  const needsFormData = isFile;

  form
    .transform((data) => {
      const payload = { ...data };
      // Jangan kirim field image jika tidak diganti
      if (!isFile && !isUrl) {
        delete payload.image;
      }
      return payload;
    })
    .put(route('albums.images.update', [props.album.id, props.image.id]), {
      forceFormData: needsFormData,
      onSuccess: () => {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Gambar diperbarui.', timer: 1200, showConfirmButton: false });
        router.visit(route('albums.edit', props.album.id));
      },
      onError: (errors) => {
        const errorMessages = Object.values(errors).flat().join('\n');
        Swal.fire({ icon: 'error', title: 'Gagal', text: errorMessages || 'Terjadi kesalahan saat menyimpan gambar.', showConfirmButton: true });
      },
    });
}

function destroyImage() {
  router.delete(route('albums.images.destroy', [props.album.id, props.image.id]), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Gambar dihapus.', timer: 1200, showConfirmButton: false });
      router.visit(route('albums.edit', props.album.id));
    },
  });
}
</script>

<template>
  <Head :title="`Edit Gambar - ${props.album.title}`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Gambar - {{ props.album.title }}</h2>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Gambar</label>
              <div class="mt-2 flex gap-2">
                <button type="button" @click="uploadMode = 'file'" :class="['rounded px-3 py-1 text-sm', uploadMode === 'file' ? 'bg-indigo-600 text-white' : 'bg-gray-200']">Upload File</button>
                <button type="button" @click="uploadMode = 'url'" :class="['rounded px-3 py-1 text-sm', uploadMode === 'url' ? 'bg-indigo-600 text-white' : 'bg-gray-200']">URL</button>
              </div>

              <div v-if="uploadMode === 'file'" class="mt-2">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                  <div v-if="!previewUrl">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                      <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="mt-4">
                      <p class="text-sm text-gray-600"><span class="font-medium text-indigo-600 hover:text-indigo-500">Klik untuk pilih file</span></p>
                      <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 10MB</p>
                    </div>
                  </div>
                  <div v-else class="relative">
                    <img :src="previewUrl" alt="Preview" class="mx-auto max-h-48 rounded-lg" />
                    <button @click.stop="clearPreview" type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                  </div>
                </div>
                <input type="file" accept="image/*" @change="onFileChange" class="mt-2" />
              </div>

              <div v-else class="mt-2">
                <input :value="imageUrl" @input="onImageUrlInput($event.target.value)" type="text" placeholder="https://... atau /path" class="mt-1 w-full rounded border px-3 py-2" />
                <div v-if="previewUrl" class="mt-2"><img :src="previewUrl" alt="Preview" class="mx-auto max-h-48 rounded-lg" /></div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormInput v-model="form.title.id" label="Judul (ID)" :error="form.errors['title.id']" />
              <FormInput v-model="form.title.en" label="Title (EN)" :error="form.errors['title.en']" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormInput v-model="form.description.id" label="Deskripsi (ID)" type="textarea" :rows="3" :error="form.errors['description.id']" />
              <FormInput v-model="form.description.en" label="Description (EN)" type="textarea" :rows="3" :error="form.errors['description.en']" />
            </div>

            <div class="flex items-center gap-2">
              <input id="published" type="checkbox" v-model="form.published" />
              <label for="published" class="text-sm text-gray-700">Published</label>
            </div>

            <div class="flex gap-2">
              <button @click="submit" :disabled="form.processing" class="inline-flex items-center rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 disabled:opacity-50">Simpan</button>
              <button @click="destroyImage" :disabled="form.processing" class="inline-flex items-center rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700 disabled:opacity-50">Hapus</button>
              <button @click="() => router.visit(route('albums.edit', props.album.id))" type="button" class="inline-flex items-center rounded bg-gray-200 px-4 py-2 hover:bg-gray-300">Batal</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>