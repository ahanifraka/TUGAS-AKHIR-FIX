<script setup>
import { ref, watch } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    height: {
        type: String,
        default: '400px',
    }
});

const emit = defineEmits(['update:modelValue']);

const showHtmlSource = ref(false);
const content = ref(props.modelValue);

watch(() => props.modelValue, (val) => {
    if (val !== content.value) {
        content.value = val;
    }
});

watch(content, (val) => {
    emit('update:modelValue', val);
});

function toggleHtmlSource() {
    showHtmlSource.value = !showHtmlSource.value;
}
</script>

<template>
    <div class="form-group">
        <div class="flex items-center justify-between mb-2">
            <label v-if="label" class="block text-sm font-bold text-gray-700">
                {{ label }}
                <span v-if="required" class="text-red-500">*</span>
            </label>
            <button
                type="button"
                @click="toggleHtmlSource"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-200"
                :class="showHtmlSource 
                    ? 'bg-gray-700 text-white hover:bg-gray-800' 
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
                {{ showHtmlSource ? 'Visual Editor' : 'HTML Source' }}
            </button>
        </div>
        
        <!-- Visual Editor -->
        <QuillEditor 
            v-if="!showHtmlSource"
            theme="snow" 
            v-model:content="content" 
            content-type="html" 
            :style="{ height: height, marginBottom: '50px' }" 
        />
        
        <!-- HTML Source Editor -->
        <textarea
            v-else
            v-model="content"
            class="w-full rounded border border-gray-400 px-3 py-2 font-mono text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition-colors duration-200"
            :style="{ height: parseInt(height) + 50 + 'px', tabSize: 2 }"
            placeholder="<div>HTML code here...</div>"
        ></textarea>
        
        <div v-if="error" class="mt-1 text-sm text-red-600 flex items-start gap-1">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ error }}</span>
        </div>
    </div>
</template>
