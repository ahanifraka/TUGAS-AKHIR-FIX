<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import Select from 'primevue/select'
import DatePicker from 'primevue/datepicker'
import Checkbox from '@/Components/Checkbox.vue'
import FormInput from '@/Components/FormInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { useSweetAlert } from '@/Composables/useSweetAlert.js'

const props = defineProps({
  mode: { type: String, default: 'create' }, // 'create' | 'edit'
  item: { type: Object, default: () => ({}) },
  tipeDokumenOptions: { type: Array, default: () => [] },
  statusPeraturanOptions: { type: Array, default: () => [] },
})

const { success, error } = useSweetAlert()

const form = useForm({
  title: props.item?.title ?? '',
  content: props.item?.content ?? '',
  file: null,
  is_active: props.item?.is_active ?? true,
  tipe_dokumen: props.item?.tipe_dokumen ?? null,
  judul_peraturan: props.item?.judul_peraturan ?? '',
  nomor_peraturan: props.item?.nomor_peraturan ?? '',
  tahun_peraturan: props.item?.tahun_peraturan ?? '',
  jenis_peraturan: props.item?.jenis_peraturan ?? '',
  singkatan_jenis: props.item?.singkatan_jenis ?? '',
  tempat_penetapan: props.item?.tempat_penetapan ?? '',
  tanggal_penetapan: props.item?.tanggal_penetapan ?? null,
  tanggal_pengundangan: props.item?.tanggal_pengundangan ?? null,
  sumber: props.item?.sumber ?? '',
  subjek: props.item?.subjek ?? '',
  status_peraturan: props.item?.status_peraturan ?? null,
  keterangan_dokumen: props.item?.keterangan_dokumen ?? '',
  teu_badan: props.item?.teu_badan ?? '',
  bidang_hukum: props.item?.bidang_hukum ?? '',
  bahasa: props.item?.bahasa ?? '',
  lokasi: props.item?.lokasi ?? '',
  keterangan_status: props.item?.keterangan_status ?? '',
  tag: props.item?.tag ?? '',
})

const quillOptions = {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ 'header': [1, 2, 3, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'align': [] }],
      ['link', 'image'],
      ['clean']
    ]
  },
  placeholder: 'Tulis konten regulasi di sini...'
}

const fileInput = ref(null)
const fileError = ref('')

function onFileChange(e) {
  const f = e.target.files?.[0] ?? null
  fileError.value = ''

  if (f) {
    // Validate file type
    const allowedTypes = ['application/pdf']
    const allowedExtensions = ['.pdf']
    const fileName = f.name.toLowerCase()
    const fileExtension = fileName.substring(fileName.lastIndexOf('.'))

    if (!allowedTypes.includes(f.type) && !allowedExtensions.includes(fileExtension)) {
      fileError.value = 'File harus berformat PDF'
      if (fileInput.value) {
        fileInput.value.value = ''
      }
      form.file = null
      return
    }

    // Validate file size (max 10MB)
    const maxSize = 10 * 1024 * 1024 // 10MB in bytes
    if (f.size > maxSize) {
      fileError.value = 'Ukuran file maksimal 10MB'
      if (fileInput.value) {
        fileInput.value.value = ''
      }
      form.file = null
      return
    }
  }

  form.file = f
}

function submit() {
  const isEdit = props.mode === 'edit'
  const url = isEdit ? route('regulasis.update', props.item.id) : route('regulasis.store')
  const options = {
    forceFormData: true,
    onSuccess: () => {
      success(isEdit ? 'Regulasi berhasil diubah.' : 'Regulasi berhasil dibuat.')
      router.visit(route('regulasis.index'))
    },
    onError: (errors) => {
      const errorMessages = Object.values(errors).flat().join('\n')
      error(errorMessages || 'Terjadi kesalahan saat menyimpan data')
    }
  }

  if (isEdit) {
    form.transform((data) => ({ ...data, _method: 'put' })).post(url, options)
    form.transform((data) => data) // reset transform
  } else {
    form.post(url, options)
  }
}

function cancel() {
  router.visit(route('regulasis.index'))
}
</script>

<template>

  <Head :title="props.mode === 'edit' ? 'Edit Regulasi' : 'Tambah Regulasi'" />
  <div class="p-6 flex flex-col gap-6">
    <FormInput v-model="form.title" label="Judul" :error="form.errors.title" required />

    <div>
      <label class="block text-sm font-bold text-gray-700">Tipe Dokumen</label>
      <Select v-model="form.tipe_dokumen" :options="props.tipeDokumenOptions" placeholder="Pilih Tipe Dokumen"
        class="mt-1 w-full" />
      <div v-if="form.errors.tipe_dokumen" class="mt-1 text-sm text-red-600">{{ form.errors.tipe_dokumen }}</div>
    </div>

    <FormInput v-model="form.judul_peraturan" label="Judul Peraturan" :error="form.errors.judul_peraturan" />

    <FormInput v-model="form.teu_badan" label="T.E.U Badan" :error="form.errors.teu_badan" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <FormInput v-model="form.nomor_peraturan" label="Nomor Peraturan" :error="form.errors.nomor_peraturan" required />

      <FormInput v-model="form.tahun_peraturan" label="Tahun Peraturan" :error="form.errors.tahun_peraturan" required
        maxlength="4" placeholder="2025" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <FormInput v-model="form.jenis_peraturan" label="Jenis Peraturan" :error="form.errors.jenis_peraturan" required />

      <FormInput v-model="form.singkatan_jenis" label="Singkatan Jenis" :error="form.errors.singkatan_jenis" />
    </div>

    <FormInput v-model="form.tempat_penetapan" label="Tempat Penetapan" :error="form.errors.tempat_penetapan" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-bold text-gray-700">Tanggal Penetapan</label>
        <DatePicker v-model="form.tanggal_penetapan" dateFormat="yy-mm-dd" showIcon class="mt-1 w-full" />
        <div v-if="form.errors.tanggal_penetapan" class="mt-1 text-sm text-red-600">{{ form.errors.tanggal_penetapan }}
        </div>
      </div>

      <div>
        <label class="block text-sm font-bold text-gray-700">Tanggal Pengundangan</label>
        <DatePicker v-model="form.tanggal_pengundangan" dateFormat="yy-mm-dd" showIcon class="mt-1 w-full" />
        <div v-if="form.errors.tanggal_pengundangan" class="mt-1 text-sm text-red-600">{{
          form.errors.tanggal_pengundangan }}</div>
      </div>
    </div>

    <FormInput v-model="form.sumber" label="Sumber" :error="form.errors.sumber" />

    <FormInput v-model="form.subjek" label="Subjek" :error="form.errors.subjek" type="textarea" :rows="3" />

    <div>
      <label class="block text-sm font-bold text-gray-700">Status Peraturan</label>
      <Select v-model="form.status_peraturan" :options="props.statusPeraturanOptions"
        placeholder="Pilih Status Peraturan" class="mt-1 w-full" />
      <div v-if="form.errors.status_peraturan" class="mt-1 text-sm text-red-600">{{ form.errors.status_peraturan }}
      </div>
    </div>

    <div>
      <label class="block text-sm font-bold text-gray-700">Deskripsi<span class="text-red-500">*</span></label>
      <QuillEditor v-model:content="form.content" contentType="html" :options="quillOptions" class="mt-2 bg-white"
        style="min-height: 300px;" />
      <div v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</div>
    </div>

    <FormInput v-model="form.keterangan_dokumen" label="Keterangan Dokumen" :error="form.errors.keterangan_dokumen"
      type="textarea" :rows="3" />

    <FormInput v-model="form.bidang_hukum" label="Bidang Hukum" :error="form.errors.bidang_hukum" />

    <FormInput v-model="form.bahasa" label="Bahasa" :error="form.errors.bahasa" />

    <FormInput v-model="form.lokasi" label="Lokasi" :error="form.errors.lokasi" />

    <FormInput v-model="form.keterangan_status" label="Keterangan Status" :error="form.errors.keterangan_status"
      type="textarea" :rows="3" />

    <FormInput v-model="form.tag" label="Tag" :error="form.errors.tag" type="textarea" :rows="2"
      placeholder="Pisahkan dengan koma" />

    <div>
      <label class="block text-sm font-bold text-gray-700">File (PDF)</label>
      <input ref="fileInput" type="file" accept=".pdf,application/pdf" @change="onFileChange" class="mt-2" />
      <div v-if="fileError" class="mt-1 text-sm text-red-600">{{ fileError }}</div>
      <div v-if="form.errors.file" class="mt-1 text-sm text-red-600">{{ form.errors.file }}</div>
      <div class="mt-1 text-xs text-gray-500">Format: PDF, Maksimal 10MB</div>
      <div v-if="props.item?.file_url" class="mt-2 text-sm">
        File saat ini: <a :href="props.item.file_url" target="_blank" rel="noopener"
          class="text-primary hover:underline">Lihat</a>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <Checkbox v-model:checked="form.is_active" />
      <span>Aktif</span>
    </div>

    <div class="flex gap-2">
      <PrimaryButton @click="submit">Simpan</PrimaryButton>
      <SecondaryButton @click="cancel">Batal</SecondaryButton>
    </div>
  </div>
</template>