<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  beritas: {
    type: Array,
    default: () => [],
  },
  albums: {
    type: Array,
    default: () => [],
  },
  error: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: 'Link',
  },
});

const emit = defineEmits(['update:modelValue']);

const linkMode = ref('custom'); // 'custom' | 'berita' | 'album'
const linkCustom = ref('');
const selectedBeritaSlug = ref('');
const selectedAlbumId = ref('');

// Initialize from modelValue
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    linkCustom.value = newValue;
  }
}, { immediate: true });

const setMode = (mode) => {
  linkMode.value = mode;
  updateLink();
};

const updateLink = () => {
  let link = '';
  
  if (linkMode.value === 'custom') {
    link = (linkCustom.value || '').trim();
  } else if (linkMode.value === 'berita') {
    link = selectedBeritaSlug.value
      ? route('beritas.showPublic', selectedBeritaSlug.value)
      : '';
  } else if (linkMode.value === 'album') {
    link = selectedAlbumId.value
      ? route('albums.showPublic', Number(selectedAlbumId.value))
      : '';
  }
  
  emit('update:modelValue', link);
};

const handleCustomInput = () => {
  updateLink();
};

const handleBeritaSelect = () => {
  updateLink();
};

const handleAlbumSelect = () => {
  updateLink();
};

const buttonClass = (mode) => [
  'rounded px-3 py-1 text-sm font-medium transition-all duration-200',
  linkMode.value === mode 
    ? 'bg-secondary text-white shadow-sm' 
    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
];

const inputClass = [
  'mt-1 w-full rounded border px-3 py-2 transition-colors duration-200',
  props.error 
    ? 'border-red-500 focus:border-red-500 focus:ring-red-500' 
    : 'border-gray-400 focus:border-primary focus:ring-primary',
  'focus:outline-none focus:ring-2 focus:ring-opacity-50'
];
</script>

<template>
  <div class="form-group">
    <label class="block text-sm font-bold text-gray-700">{{ label }}:</label>
    
    <div class="mt-2 flex gap-2">
      <button 
        type="button" 
        @click="setMode('custom')"
        :class="buttonClass('custom')"
      >
        Custom
      </button>
      <button 
        type="button" 
        @click="setMode('berita')"
        :class="buttonClass('berita')"
      >
        Berita
      </button>
      <button 
        type="button" 
        @click="setMode('album')"
        :class="buttonClass('album')"
      >
        Galeri/Album
      </button>
    </div>

    <div v-if="linkMode === 'custom'" class="mt-2">
      <input 
        v-model="linkCustom"
        @input="handleCustomInput"
        type="text" 
        placeholder="https://... atau /path"
        :class="inputClass"
      />
    </div>

    <div v-else-if="linkMode === 'berita'" class="mt-2">
      <input 
        v-model="selectedBeritaSlug"
        @input="handleBeritaSelect"
        list="berita-options" 
        placeholder="Cari atau pilih berita..."
        :class="inputClass"
      />
      <datalist id="berita-options">
        <option v-for="b in beritas" :key="b.id" :value="b.slug">{{ b.title }}</option>
      </datalist>
    </div>

    <div v-else class="mt-2">
      <input 
        v-model="selectedAlbumId"
        @input="handleAlbumSelect"
        list="album-options" 
        placeholder="Cari atau pilih album..."
        :class="inputClass"
      />
      <datalist id="album-options">
        <option v-for="a in albums" :key="a.id" :value="a.id">{{ a.title }}</option>
      </datalist>
    </div>

    <div v-if="error" class="mt-1 text-sm text-red-600 flex items-start gap-1">
      <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      <span>{{ error }}</span>
    </div>
  </div>
</template>
