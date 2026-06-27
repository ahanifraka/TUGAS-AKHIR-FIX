<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const isOpen = ref(false);
const dropdownRef = ref(null);

const i18n = computed(() => page.props?.i18n ?? {});
const supported = computed(() => Array.isArray(i18n.value?.supportedLocales) ? i18n.value.supportedLocales : ['id', 'en']);
const current = computed(() => i18n.value?.locale || i18n.value?.defaultLocale || 'id');

const languages = {
  en: { name: 'English', flag: '/images/country/us.svg' },
  id: { name: 'Indonesia', flag: '/images/country/indonesia.svg' }
};

const currentLanguage = computed(() => languages[current.value] || languages.en);

function toggleDropdown() {
  if (closeTimeout) clearTimeout(closeTimeout);
  isOpen.value = !isOpen.value;
}

let closeTimeout = null;

function openDropdown() {
  if (closeTimeout) clearTimeout(closeTimeout);
  isOpen.value = true;
}

function closeDropdown() {
  closeTimeout = setTimeout(() => {
    isOpen.value = false;
  }, 200);
}

function switchTo(locale) {
  if (locale === current.value) {
    isOpen.value = false;
    return;
  }
  const redirect = encodeURIComponent(`${window.location.pathname}${window.location.search}${window.location.hash}`);
  window.location.assign(`/set-locale/${locale}?redirect=${redirect}`);
}

function handleClickOutside(event) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false;
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div class="relative inline-block" ref="dropdownRef" @mouseenter="openDropdown" @mouseleave="closeDropdown">
    <button 
      type="button" 
      class="flex items-center gap-2 px-3 py-2 min-w-[140px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-200 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
      @click="toggleDropdown"
      aria-label="Language Switcher"
      :aria-expanded="isOpen"
    >
      <img :src="currentLanguage.flag" alt="Bahasa" class="w-5 h-5 object-contain" />
      <span class="flex-1 text-left">{{ currentLanguage.name }}</span>
      <svg 
        class="text-gray-500 dark:text-gray-400 transition-transform duration-200" 
        :class="{ 'rotate-180': isOpen }"
        width="12" 
        height="12" 
        viewBox="0 0 12 12" 
        fill="none" 
        xmlns="http://www.w3.org/2000/svg"
      >
        <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>

    <transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div 
        v-if="isOpen" 
        class="absolute top-[calc(100%+4px)] left-0 right-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden z-50"
      >
        <button
          v-for="loc in supported"
          :key="loc"
          type="button"
          class="flex items-center gap-2 w-full px-3 py-2.5 bg-white dark:bg-gray-800 border-none cursor-pointer text-sm text-gray-700 dark:text-gray-200 transition-colors duration-150 text-left hover:bg-gray-100 dark:hover:bg-gray-700"
          :class="{ 
            'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-medium': current === loc,
            'border-b border-gray-100 dark:border-gray-700': loc !== supported[supported.length - 1]
          }"
          @click="switchTo(loc)"
        >
          <img :src="languages[loc].flag" :alt="languages[loc].name" class="w-5 h-5 object-contain" />
          <span>{{ languages[loc].name }}</span>
        </button>
      </div>
    </transition>
  </div>
</template>
