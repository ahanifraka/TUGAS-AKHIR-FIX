<script setup>
import { reactive, watch, ref, onMounted, computed } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import { Head, router, usePage } from '@inertiajs/vue3'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import useTranslations from '@/Composables/useTranslations.js'
import { useSweetAlert } from '@/Composables/useSweetAlert.js'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import FormImageUpload from '@/Components/FormImageUpload.vue'

import '@vueup/vue-quill/dist/vue-quill.snow.css'

const isSubmitting = ref(false)
const { t } = useTranslations()
const { success, error: showError } = useSweetAlert()
const page = usePage()

// Get errors from Inertia
const errors = computed(() => page.props.errors || {})

const props = defineProps({
  formTitle: { type: String, default: 'Form Berita' },
  submitUrl: { type: String, required: true },
  method: { type: String, default: 'post' },
  categories: { type: Array, default: () => [] },
  berita: { type: Object, default: () => ({}) },
})

// Reactive form state
const form = reactive({
  title: {
    id: props.berita.title_translations?.id ?? props.berita.title ?? '',
    en: props.berita.title_translations?.en ?? '',
  },
  slug: props.berita.slug ?? '',
  teaser: {
    id: props.berita.teaser_translations?.id ?? props.berita.teaser ?? '',
    en: props.berita.teaser_translations?.en ?? '',
  },
  content: {
    id: props.berita.content_translations?.id ?? props.berita.content ?? '',
    en: props.berita.content_translations?.en ?? '',
  },
  image: props.berita.image ?? '',
  published: props.berita.published ?? 0,
  published_at: props.berita.published_at ?? '',
  category_id: props.berita.category_id ?? null,
  meta_title: {
    id: props.berita.meta_title_translations?.id ?? props.berita.meta_title ?? '',
    en: props.berita.meta_title_translations?.en ?? '',
  },
  meta_keyword: {
    id: props.berita.meta_keyword_translations?.id ?? props.berita.meta_keyword ?? '',
    en: props.berita.meta_keyword_translations?.en ?? '',
  },
  meta_content: {
    id: props.berita.meta_content_translations?.id ?? props.berita.meta_content ?? '',
    en: props.berita.meta_content_translations?.en ?? '',
  },
  popular: props.berita.popular ?? 0,
})

const slugManual = ref(false)
const isPreviewMode = ref(false)
const quillEditor = ref(null)
const isSourceView = ref(false)
const activeContentLocale = ref('id')
const sourceContent = reactive({ id: '', en: '' })

// Custom image handler for QuillEditor
const imageHandler = function() {
  const input = document.createElement('input')
  input.setAttribute('type', 'file')
  input.setAttribute('accept', 'image/*')
  input.click()

  input.onchange = () => {
    const file = input.files[0]
    if (file) {
      const reader = new FileReader()
      reader.onload = (e) => {
        const quill = this.quill
        const range = quill.getSelection(true)
        quill.insertEmbed(range.index, 'image', e.target.result)
        quill.setSelection(range.index + 1)
      }
      reader.readAsDataURL(file)
    }
  }
}

// Custom video handler for QuillEditor
const videoHandler = function() {
  const url = prompt('Masukkan URL video (YouTube, Vimeo, dll):')
  if (url) {
    const quill = this.quill
    const range = quill.getSelection(true)
    
    // Convert YouTube/Vimeo URLs to embed format
    let embedUrl = url
    
    // YouTube URL conversion
    if (url.includes('youtube.com/watch?v=')) {
      const videoId = url.split('v=')[1].split('&')[0]
      embedUrl = `https://www.youtube.com/embed/${videoId}`
    } else if (url.includes('youtu.be/')) {
      const videoId = url.split('youtu.be/')[1].split('?')[0]
      embedUrl = `https://www.youtube.com/embed/${videoId}`
    }
    // Vimeo URL conversion
    else if (url.includes('vimeo.com/')) {
      const videoId = url.split('vimeo.com/')[1].split('?')[0]
      embedUrl = `https://player.vimeo.com/video/${videoId}`
    }
    
    quill.insertEmbed(range.index, 'video', embedUrl)
    quill.setSelection(range.index + 1)
  }
}

// Enhanced QuillEditor options with comprehensive toolbar
const quillOptions = {
  theme: 'snow',
  modules: {
    toolbar: {
      container: [
        // Font and size options
        [{ 'font': [] }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        
        // Text formatting
        ['bold', 'italic', 'underline', 'strike'],
        
        // Colors
        [{ 'color': [] }, { 'background': [] }],
        
        // Headers
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        
        // Lists and indentation
        [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        
        // Text alignment and direction
        [{ 'align': [] }],
        [{ 'direction': 'rtl' }],
        
        // Scripts
        [{ 'script': 'sub'}, { 'script': 'super' }],
        
        // Blocks
        ['blockquote', 'code-block'],
        
        // Media and links
        ['link', 'image', 'video', 'formula'],
        
        // Clean formatting
        ['clean']
      ],
       handlers: {
         image: imageHandler,
         video: videoHandler
       }
    }
  },
  placeholder: 'Tulis konten berita di sini...',
  formats: [
    'header', 'font', 'size',
    'bold', 'italic', 'underline', 'strike',
    'color', 'background',
    'script', 'super', 'sub',
    'blockquote', 'code-block',
    'list', 'bullet', 'check', 'indent',
    'align', 'direction',
    'link', 'image', 'video', 'formula',
    'clean'
  ]
}

// Computed property for formatted date
const formattedDate = computed(() => {
  return new Date().toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long', 
    day: 'numeric'
  })
})

// Watch for berita prop changes to update form
watch(() => props.berita, (val) => {
  Object.assign(form, {
    title: {
      id: val?.title_translations?.id ?? val?.title ?? '',
      en: val?.title_translations?.en ?? '',
    },
    slug: val?.slug ?? '',
    teaser: {
      id: val?.teaser_translations?.id ?? val?.teaser ?? '',
      en: val?.teaser_translations?.en ?? '',
    },
    content: {
      id: val?.content_translations?.id ?? val?.content ?? '',
      en: val?.content_translations?.en ?? '',
    },
    image: val?.image ?? '',
    published: val?.published ?? 0,
    published_at: val?.published_at ?? '',
    category_id: val?.category_id ?? null,
    meta_title: {
      id: val?.meta_title_translations?.id ?? val?.meta_title ?? '',
      en: val?.meta_title_translations?.en ?? '',
    },
    meta_keyword: {
      id: val?.meta_keyword_translations?.id ?? val?.meta_keyword ?? '',
      en: val?.meta_keyword_translations?.en ?? '',
    },
    meta_content: {
      id: val?.meta_content_translations?.id ?? val?.meta_content ?? '',
      en: val?.meta_content_translations?.en ?? '',
    },
    popular: val?.popular ?? 0,
  })
})

watch(() => form.title.id, (newTitle) => {
  if (!slugManual.value || !form.slug) {
    form.slug = slugify(newTitle)
  }
})

const previewUrl = ref('')

// Image picker state


onMounted(() => {
  if (!form.slug) {
    form.slug = slugify(form.title.id)
  }
})


function submit() {
  if (isSubmitting.value) return
  isSubmitting.value = true
  const isFileUpload = form.image instanceof File
  let data
  if (isFileUpload) {
    data = new FormData()
    for (const [key, value] of Object.entries(form)) {
      if (key === 'image') {
        if (value) data.append('image', value)
      } else if (typeof value === 'object' && value !== null && !(value instanceof File)) {
        for (const [subKey, subVal] of Object.entries(value)) {
          data.append(`${key}[${subKey}]`, subVal ?? '')
        }
      } else if (value !== undefined && value !== null) {
        data.append(key, value)
      }
    }
    if (props.method.toLowerCase() !== 'post') {
      data.append('_method', props.method.toUpperCase())
    }
  } else {
    data = { ...form }
    if (props.method.toLowerCase() !== 'post') {
      data._method = props.method.toUpperCase()
    }
  }

  router.post(props.submitUrl, data, {
    onSuccess: () => {
      const successTitle = props.method.toLowerCase() === 'post' ? 'Berita berhasil dibuat' : 'Berita berhasil diperbarui'
      success('', successTitle, 1600)
      isSubmitting.value = false
    },
    onError: (e) => {
      console.error('Validation errors:', e)
      const firstMsg = Object.values(e || {}).find((v) => typeof v === 'string')
      
      // Show error notification
      showError(firstMsg || 'Mohon periksa input dan coba lagi.', 'Gagal menyimpan')
      
      // Scroll to first error
      if (e.slug) {
        // Scroll to slug input if slug error exists
        setTimeout(() => {
          const slugInput = document.querySelector('input[name="slug"]')
          if (slugInput) {
            slugInput.scrollIntoView({ behavior: 'smooth', block: 'center' })
            slugInput.focus()
          }
        }, 100)
      }
      
      isSubmitting.value = false
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

function slugify(str) {
  return (str || '')
    .toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

function togglePreview() {
  isPreviewMode.value = !isPreviewMode.value
}

function toggleSourceView() {
  const locale = activeContentLocale.value
  if (isSourceView.value) {
    // Save back edited source to current locale content
    form.content[locale] = sourceContent[locale]
    isSourceView.value = false
  } else {
    // Load current locale content into source buffer
    sourceContent[locale] = form.content[locale]
    isSourceView.value = true
  }
}

function switchContentLocale(locale) {
  if (activeContentLocale.value !== locale) {
    if (isSourceView.value) {
      const current = activeContentLocale.value
      form.content[current] = sourceContent[current]
      sourceContent[locale] = form.content[locale]
    }
    activeContentLocale.value = locale
  }
}
</script>

<style>
p {
    margin-bottom: 15px;
}

#berita-content a {
    color: #007bff !important;
}

#berita-content a:hover {
    color: #0056b3 !important;
}

.preview-mode {
    background-color: #f8f9fa;
    min-height: 100vh;
}
</style>

<template>
  <Head :title="formTitle" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ formTitle }}</h2>
        <div class="flex gap-2">
          <button 
            @click="togglePreview" 
            :class="['px-4 py-2 rounded text-sm font-medium transition-colors', 
                     isPreviewMode ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white hover:bg-blue-700']"
          >
            {{ isPreviewMode ? 'Edit Mode' : 'Preview Mode' }}
          </button>
        </div>
      </div>
    </template>

    <!-- Edit Mode -->
    <div v-if="!isPreviewMode" class="grid grid-cols-12 gap-6 py-12 px-12">
      <!-- Main Editor -->
      <div class="col-span-12 md:col-span-8">
        <div>
          <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 space-y-4">
              <div>
                <InputLabel value="Judul" />
                <span class="text-red-500">*</span>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-1">
                  <div>
                    <label class="block text-xs font-medium text-gray-600">Judul (ID)</label>
                    <TextInput v-model="form.title.id" type="text" class="mt-1 block w-full" />
                    <p v-if="errors['title.id']" class="mt-1 text-sm text-red-600">{{ errors['title.id'] }}</p>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-gray-600">Title (EN)</label>
                    <TextInput v-model="form.title.en" type="text" class="mt-1 block w-full" />
                    <p v-if="errors['title.en']" class="mt-1 text-sm text-red-600">{{ errors['title.en'] }}</p>
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">{{ t('berita.content.label','Konten') }}<span class="text-red-500">*</span></label>
                <p v-if="errors['content.id']" class="mt-1 text-sm text-red-600">{{ errors['content.id'] }}</p>
                <div class="flex gap-2 mb-2 mt-4 items-center flex-wrap">
                  <div class="flex gap-1">
                    <button
                      type="button"
                      @click="switchContentLocale('id')"
                      :class="['px-3 py-1 text-sm rounded border', activeContentLocale === 'id' ? 'bg-secondary text-white border-secondary' : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200']"
                    >ID</button>
                    <button
                      type="button"
                      @click="switchContentLocale('en')"
                      :class="['px-3 py-1 text-sm rounded border', activeContentLocale === 'en' ? 'bg-secondary text-white border-secondary' : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200']"
                    >EN</button>
                  </div>
                  <button
                    type="button"
                    @click="toggleSourceView"
                    class="px-3 py-1 text-sm bg-gray-500 text-white rounded hover:bg-gray-600"
                  >
                    {{ isSourceView ? 'WYSIWYG' : 'Source Code' }} ({{ activeContentLocale.toUpperCase() }})
                  </button>
                </div>
                <!-- Source Code View -->
                <div v-if="isSourceView" class="border rounded-lg">
                  <textarea
                    v-model="sourceContent[activeContentLocale]"
                    class="w-full h-[32rem] p-4 font-mono text-sm border-0 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :placeholder="'Enter HTML content (' + activeContentLocale.toUpperCase() + ')...'"
                  ></textarea>
                </div>
                <!-- WYSIWYG Editor -->
                <QuillEditor
                  v-else
                  ref="quillEditor"
                  v-model:content="form.content[activeContentLocale]"
                  :options="quillOptions"
                  content-type="html"
                  style="height: 600px;"
                />
              </div>

              <div class="flex justify-end gap-2 pt-4">
                <PrimaryButton @click="submit" :disabled="isSubmitting" :class="[isSubmitting ? 'opacity-50 cursor-not-allowed' : '']">Simpan</PrimaryButton>
                <button @click="() => router.visit(route('beritas.index'))" type="button" class="inline-flex items-center rounded bg-gray-200 px-4 py-2 hover:bg-gray-300">Batal</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar Content -->
      <div class="col-span-12 md:col-span-4 flex flex-col gap-6">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col gap-6">
          <div>
            <div class="flex items-center justify-between">
              <InputLabel value="Slug (opsional)" />
              <span v-if="errors.slug" class="text-xs text-red-600 font-medium">⚠ Error</span>
            </div>
            <TextInput 
              v-model="form.slug" 
              @input="slugManual = true" 
              type="text" 
              name="slug"
              :class="[
                'mt-1 block w-full transition-colors', 
                errors.slug ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50' : ''
              ]" 
            />
            <div v-if="errors.slug" class="mt-2 p-3 bg-red-50 border-l-4 border-red-500 rounded">
              <p class="text-sm text-red-700 font-medium flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span>{{ errors.slug }}</span>
              </p>
            </div>
            <p v-else class="text-xs text-gray-500 mt-1">Slug akan otomatis dibuat dari judul jika dibiarkan kosong</p>
          </div>
          <div>
            <InputLabel value="Teaser (opsional)" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600">Teaser (ID)</label>
                <textarea v-model="form.teaser.id" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600">Teaser (EN)</label>
                <textarea v-model="form.teaser.en" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
              </div>
            </div>
          </div>
          <div>
            <InputLabel value="Kategori" />
            <select v-model="form.category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option :value="null">-- Pilih Kategori --</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.category_name }}</option>
            </select>
          </div>
          <div class="inline-flex gap-2 items-start justify-start">
            <div>
              <div class="mt-1">
                <button
                  type="button"
                  role="switch"
                  :aria-checked="Boolean(form.published)"
                  @click="form.published = form.published ? 0 : 1"
                  :class="['inline-flex items-center gap-2 px-3 py-1 rounded transition', form.published ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700']"
                >
                  <i :class="form.published ? 'pi pi-check' : 'pi pi-times'"></i>
                  <span>{{ form.published ? 'Published' : 'Draft' }}</span>
                </button>
              </div>
            </div>
            <div>
              <div class="mt-1">
                <button
                  type="button"
                  @click="form.popular = form.popular ? 0 : 1"
                  :class="['inline-flex items-center gap-2 px-3 py-1 rounded transition', form.popular ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700']"
                >
                  <i :class="form.popular ? 'pi pi-star' : 'pi pi-star'"></i>
                  <span>{{ form.popular ? 'Popular' : 'Normal' }}</span>
                </button>
              </div>
            </div>
          </div>
          <div v-if="form.published" class="mt-2">
            <InputLabel value="Jadwal Publikasi (opsional)" class="mb-1" />
            <input 
              v-model="form.published_at" 
              type="datetime-local" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              :min="new Date().toISOString().slice(0, 16)"
            />
            <p class="text-xs text-gray-500 mt-1">Kosongkan untuk publikasi langsung</p>
          </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col gap-6">
          <div>
            <FormImageUpload
              v-model="form.image"
              v-model:previewUrl="previewUrl"
              :error="errors.image"
              label="Gambar"
            />
          </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col gap-6">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <InputLabel value="Meta Title" />
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-1">
                <div>
                  <label class="block text-xs font-medium text-gray-600">Meta Title (ID)</label>
                  <TextInput v-model="form.meta_title.id" type="text" class="mt-1 block w-full" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600">Meta Title (EN)</label>
                  <TextInput v-model="form.meta_title.en" type="text" class="mt-1 block w-full" />
                </div>
              </div>
            </div>
            <div>
              <InputLabel value="Meta Keyword" />
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-1">
                <div>
                  <label class="block text-xs font-medium text-gray-600">Meta Keyword (ID)</label>
                  <TextInput v-model="form.meta_keyword.id" type="text" class="mt-1 block w-full" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600">Meta Keyword (EN)</label>
                  <TextInput v-model="form.meta_keyword.en" type="text" class="mt-1 block w-full" />
                </div>
              </div>
            </div>
            <div>
              <InputLabel value="Meta Content" />
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-1">
                <div>
                  <label class="block text-xs font-medium text-gray-600">Meta Content (ID)</label>
                  <TextInput v-model="form.meta_content.id" type="text" class="mt-1 block w-full" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600">Meta Content (EN)</label>
                  <TextInput v-model="form.meta_content.en" type="text" class="mt-1 block w-full" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Mode -->
    <div v-else class="preview-mode">
      <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-12 gap-4">
          <div class="order-2 lg:order-1 col-span-12 lg:col-span-3">
            <div>
              <h2 class="text-xl font-bold text-gray-900 mb-6">Preview Mode</h2>
              <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <p class="text-sm">Ini adalah mode preview. Berita belum dipublikasikan.</p>
              </div>
              <div v-if="categories.length > 0" class="mt-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Kategori</h3>
                <div class="flex flex-wrap gap-2">
                  <div v-for="item in categories" :key="item.id">
                    <div class="bg-gray-100 px-4 py-2 rounded-full text-sm">
                      {{ item.category_name }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="order-1 lg:order-2 col-span-12 lg:col-span-9 space-y-6">
            
            <div v-if="previewUrl || form.image" class="overflow-hidden rounded">
              <img :src="previewUrl || form.image" :alt="form.title || 'Preview'" class="w-full object-cover object-center max-h-[400px]" />
            </div>

            <div class="flex flex-col items-start justify-between">
              <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                {{ form.title.id || 'Judul Berita' }}
              </h1>
              <p class="text-sm text-gray-500">{{ formattedDate }}</p>
            </div>

            <div v-if="form.teaser.id" class="text-gray-700 text-sm leading-normal">
              {{ form.teaser.id }}
            </div>

            <article class="prose max-w-none leading-normal">
              <div id="berita-content" v-html="form.content.id || '<p>Konten berita akan ditampilkan di sini...</p>'"></div>
            </article>

            <!-- Action buttons in preview mode -->
            <div class="flex justify-start gap-2 pt-8 border-t">
              <PrimaryButton @click="submit" :disabled="isSubmitting" :class="[isSubmitting ? 'opacity-50 cursor-not-allowed' : '']">Simpan</PrimaryButton>
              <button @click="togglePreview" class="inline-flex items-center rounded bg-gray-200 px-4 py-2 hover:bg-gray-300">Kembali ke Edit</button>
              <button @click="() => router.visit(route('beritas.index'))" type="button" class="inline-flex items-center rounded bg-gray-200 px-4 py-2 hover:bg-gray-300">Batal</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>