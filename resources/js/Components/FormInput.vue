<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
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
  autofocus: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  rows: {
    type: Number,
    default: 4,
  },
});

const emit = defineEmits(['update:modelValue']);

const isTextarea = computed(() => props.type === 'textarea');

const inputClasses = computed(() => [
  'mt-1 w-full rounded px-3 py-2 border',
  props.error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-400 focus:border-primary focus:ring-primary',
  props.disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white',
  'focus:outline-none focus:ring-2 focus:ring-opacity-50 transition-colors duration-200',
]);

const handleInput = (event) => {
  emit('update:modelValue', event.target.value);
};
</script>

<template>
  <div class="form-group">
    <label v-if="label" class="block text-sm font-bold text-gray-700">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <textarea
      v-if="isTextarea"
      :value="modelValue"
      @input="handleInput"
      :placeholder="placeholder"
      :disabled="disabled"
      :rows="rows"
      :class="inputClasses"
    />
    
    <input
      v-else
      :type="type"
      :value="modelValue"
      @input="handleInput"
      :placeholder="placeholder"
      :disabled="disabled"
      :autofocus="autofocus"
      :class="inputClasses"
    />
    
    <div v-if="error" class="mt-1 text-sm text-red-600 flex items-start gap-1">
      <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      <span>{{ error }}</span>
    </div>
  </div>
</template>
