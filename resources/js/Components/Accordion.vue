<script setup>
import { ref } from 'vue';

const props = defineProps({
    title: String,
    content: String,
});

const isOpen = ref(false);
</script>

<template>
    <div class="border-b border-gray-200 last:border-0">
        <button
            @click="isOpen = !isOpen"
            class="flex justify-between items-center w-full py-4 px-2 text-left focus:outline-none hover:bg-gray-50 transition-colors duration-200 rounded-md"
            :aria-expanded="isOpen"
        >
            <span class="text-lg font-medium text-gray-800">{{ title }}</span>
            <span class="ml-6 flex-shrink-0 text-gray-500">
                <svg
                    class="w-6 h-6 transform transition-transform duration-200"
                    :class="{ 'rotate-180': isOpen }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </span>
        </button>
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-y-95 opacity-0"
            enter-to-class="transform scale-y-100 opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="transform scale-y-100 opacity-100"
            leave-to-class="transform scale-y-95 opacity-0"
        >
            <div
                v-show="isOpen"
                class="overflow-hidden"
            >
                <div class="pb-4 px-2 text-gray-600 leading-relaxed pt-2">
                    {{ content }}
                </div>
            </div>
        </transition>
    </div>
</template>
