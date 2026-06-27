<template>
  <header class="bg-white dark:bg-gray-900 shadow-md py-4 sticky top-0 z-[1000] w-full">
    <div class="max-w-[1400px] mx-auto px-4 flex justify-between items-center">

      <Link href="/" class="flex items-center gap-2 md:gap-4 no-underline" :title="t('menu.home', 'Beranda')">
      <img :src="theme === 'dark' ? '/images/logo-bpbumd-dark.png' : '/images/logo-bpbumd.png'"
        :alt="t('header.logo_alt', 'Logo Badan Pembinaan BPBUMD')" class="h-12 md:h-16" :title="t('header.logo_alt', 'Logo Badan Pembinaan BPBUMD')" />
      <div class="flex flex-col leading-tight"></div>
      </Link>

      <!-- Mobile menu button -->
      <button @click="toggleMobileMenu" :aria-label="t('header.menu_toggle', 'Menu')"
        class="lg:hidden flex flex-col justify-center items-center w-8 h-8 space-y-1 focus:outline-none"
        :class="{ 'space-y-0': isMobileMenuOpen }">
        <span class="block w-6 h-0.5 bg-gray-800 dark:bg-gray-200 transition-all duration-300"
          :class="{ 'rotate-45 translate-y-1': isMobileMenuOpen }"></span>
        <span class="block w-6 h-0.5 bg-gray-800 dark:bg-gray-200 transition-all duration-300"
          :class="{ 'opacity-0': isMobileMenuOpen }"></span>
        <span class="block w-6 h-0.5 bg-gray-800 dark:bg-gray-200 transition-all duration-300"
          :class="{ '-rotate-45 -translate-y-2': isMobileMenuOpen }"></span>
      </button>

      <!-- Desktop Navigation -->
      <nav class="hidden lg:flex items-center gap-3">
        <ul class="list-none flex justify-center items-center m-0 p-0 gap-6 lg:gap-8">

          <li>
            <Link href="/" :title="t('menu.home', 'Beranda')"
              class="no-underline text-gray-800 dark:text-gray-100 font-semibold text-sm py-1 relative hover:text-primary uppercase">
            {{ t('menu.home', 'Beranda') }}</Link>
          </li>

          <li class="relative" @mouseover="activeDropdown = 'profil'" @mouseleave="activeDropdown = false">

            <Link href="#"
              class="inline-flex items-center justify-center no-underline text-gray-800 dark:text-gray-100 font-semibold text-sm py-1 hover:text-primary uppercase">
            <span>{{ t('menu.profile', 'Profil') }}</span><span class="ml-1 pt-1"><i class="pi pi-angle-down"></i></span>
            </Link>

            <transition enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-2">

              <ul v-if="activeDropdown === 'profil'"
                class="absolute top-full left-1/2 -translate-x-1/2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-xl p-2 mt-2 min-w-[250px] z-[1010]">
                <li v-for="item in profilMenu" :key="item.text">
                  <Link :href="item.link"
                    class="flex items-center px-4 py-3 text-gray-800 dark:text-gray-100 no-underline rounded-md text-sm font-medium hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-primary transition">
                  <i :class="[item.icon, 'text-base mr-4 w-5 text-center']"></i>
                  <span>{{ item.text }}</span>
                  </Link>
                </li>
              </ul>

            </transition>
          </li>

          <li class="relative" @mouseenter="activeDropdown = 'informasi'"
            @mouseleave="activeDropdown = null; activeSubmenu = null">
            <Link href="#"
              class="inline-flex items-center justify-center no-underline text-gray-800 dark:text-gray-100 font-semibold text-sm py-1 hover:text-primary uppercase">
            <span>{{ t('menu.bumd_info', 'Informasi BUMD') }}</span><span class="ml-1 pt-1"><i class="pi pi-angle-down"></i></span>
            </Link>

            <transition enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-2">

              <ul v-if="activeDropdown === 'informasi'"
                class="absolute top-full left-1/2 -translate-x-1/2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-xl p-2 min-w-[300px] lg:min-w-[300px] z-[1010] mt-2">

                <li v-for="item in informasiMenu" :key="item.text"
                  @mouseenter="activeSubmenu = item.children ? item.text : null">
                  <Link :href="item.link || '#'"
                    class="flex items-center px-4 py-3 text-gray-800 dark:text-gray-100 no-underline rounded-md text-sm font-medium hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-primary transition">
                  <i :class="[item.icon, 'text-base mr-4 w-5 text-center']"></i>
                  <span>{{ item.text }}</span>
                  <i v-if="item.children"
                    :class="['pi', 'text-xs ml-auto pl-4 opacity-60 transition-transform', activeSubmenu === item.text ? 'pi-chevron-up' : 'pi-chevron-down']"></i>
                  </Link>

                  <ul v-if="item.children && activeSubmenu === item.text"
                    class="list-none pl-6 my-1 dark:bg-gray-700 rounded-md py-1">
                    <li v-for="child in item.children" :key="child.text">
                      <a :href="child.link" target="_blank" rel="noopener noreferrer"
                        class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-100 no-underline rounded-md text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <i :class="[child.icon, 'mr-2']"></i>
                        <span>{{ child.text }}</span>
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </transition>
          </li>

          <li>
            <Link href="/berita" :title="t('menu.news', 'Berita')"
              class="no-underline text-gray-800 dark:text-gray-100 font-semibold text-sm py-1 hover:text-primary uppercase">
            {{ t('menu.news', 'Berita') }}</Link>
          </li>
          <li>
            <Link href="/pengumuman" :title="t('menu.pengumuman', 'Pengumuman')"
              class="no-underline text-gray-800 dark:text-gray-100 font-semibold text-sm py-1 hover:text-primary uppercase">
            {{ t('menu.pengumuman', 'Pengumuman') }}</Link>
          </li>
          <li>
            <Link href="/galeri" :title="t('menu.gallery', 'Galeri')"
              class="no-underline text-gray-800 dark:text-gray-100 font-semibold text-sm py-1 hover:text-primary uppercase">
            {{ t('menu.gallery', 'Galeri') }}</Link>
          </li>
          <li>
            <Link href="/regulasi" :title="t('menu.regulations', 'Regulasi')"
              class="no-underline text-gray-800 dark:text-gray-100 font-semibold text-sm py-1 hover:text-primary uppercase">
            {{ t('menu.regulations', 'Regulasi') }}</Link>
          </li>

          <li class="relative" @mouseover="activeDropdown = 'ppid'" @mouseleave="activeDropdown = null">
            <Link href="#"
              class="inline-flex items-center justify-center no-underline text-gray-800 dark:text-gray-100 font-semibold text-sm py-1 hover:text-primary uppercase">
            <span>{{ t('menu.ppid', 'PPID') }}</span><span class="ml-1 pt-1"><i class="pi pi-angle-down"></i></span>
            </Link>

            <transition enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-2">
              <ul v-if="activeDropdown === 'ppid'"
                class="absolute top-full right-0 bg-white dark:bg-gray-800 rounded-lg shadow-xl p-5 z-[1010] mt-6 flex flex-col lg:flex-row gap-4">
                <div class="flex flex-col w-full lg:w-60">
                  <li class="list-none text-[0.9rem] font-bold text-gray-900 dark:text-gray-100 uppercase mb-2 px-4">
                    {{ t('ppid.categories.info_ppid', 'INFORMASI PPID') }}</li>
                  <li v-for="item in ppidMenu" :key="item.text" class="list-none">
                    <Link :href="item.link"
                      class="block no-underline text-gray-800 dark:text-gray-100 text-sm font-medium px-4 py-2 rounded-md mb-0.5 hover:bg-blue-100 dark:hover:bg-gray-700 hover:text-blue-700 transition">
                    <span>{{ item.text }}</span>
                    </Link>
                  </li>
                </div>

                <div class="flex flex-col w-full lg:w-60">
                  <li class="list-none text-[0.9rem] font-bold text-gray-900 dark:text-gray-100 uppercase mb-2 px-4">
                    {{ t('ppid.categories.public_info', 'INFORMASI PUBLIK') }}</li>
                  <li v-for="item in informasiPublikMenu" :key="item.text" class="list-none">
                    <Link :href="item.link"
                      class="block no-underline text-gray-800 dark:text-gray-100 text-sm font-medium px-4 py-2 rounded-md mb-0.5 hover:bg-blue-100 dark:hover:bg-gray-700 hover:text-blue-700 transition">
                    <span>{{ item.text }}</span>
                    </Link>
                  </li>
                </div>

                <div class="flex flex-col w-full lg:w-60">
                  <li class="list-none text-[0.9rem] font-bold text-gray-900 dark:text-gray-100 uppercase mb-2 px-4">
                    {{ t('ppid.categories.service_standards', 'STANDAR LAYANAN') }}</li>
                  <li v-for="item in standarLayananMenu" :key="item.text" class="list-none">
                    <Link :href="item.link"
                      class="block no-underline text-gray-800 dark:text-gray-100 text-sm font-medium px-4 py-2 rounded-md mb-0.5 hover:bg-blue-100 dark:hover:bg-gray-700 hover:text-blue-700 transition">
                    <span>{{ item.text }}</span>
                    </Link>
                  </li>
                </div>
              </ul>
            </transition>
          </li>

          <!-- <li>
            <Link href="/faq" :title="t('menu.faq', 'FAQ')"
              class="no-underline text-gray-800 dark:text-gray-100 font-semibold text-sm py-1 hover:text-primary uppercase">
            {{ t('menu.faq', 'FAQ') }}</Link>
          </li> -->

        </ul>
        <!-- Language Switcher -->
        <div class="ml-4">
          <LanguageSwitcher />
        </div>
      </nav>

      <!-- Mobile Navigation -->
      <transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0" leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-4">
        <nav v-if="isMobileMenuOpen"
          class="lg:hidden absolute top-full h-[100dvh] pb-24 left-0 right-0 overflow-y-auto overscroll-contain bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg z-[999]">
          <div class="max-w-[1400px] mx-auto px-4 py-4">
            <ul class="list-none space-y-4">

              <li>
                <Link href="/" @click="isMobileMenuOpen = false" :title="t('menu.home', 'Beranda')"
                  class="block no-underline text-gray-800 dark:text-gray-100 font-semibold text-base py-2 hover:text-primary uppercase">
                {{ t('menu.home', 'Beranda') }}
                </Link>
              </li>

              <li>
                <button @click="toggleMobileDropdown('profil')"
                  class="w-full flex items-center justify-between text-gray-800 dark:text-gray-100 font-semibold text-base py-2 hover:text-primary">
                  <span>{{ t('menu.profile', 'Profil').toUpperCase() }}</span>
                  <i
                    :class="['pi', 'text-sm transition-transform', activeMobileDropdown === 'profil' ? 'pi-chevron-up' : 'pi-chevron-down']"></i>
                </button>

                <transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0 max-h-0"
                  enter-to-class="opacity-100 max-h-96" leave-active-class="transition-all duration-200"
                  leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
                  <ul v-if="activeMobileDropdown === 'profil'"
                    class="overflow-hidden dark:bg-gray-800 rounded-lg mt-2 pl-4">
                    <li v-for="item in profilMenu" :key="item.text" class="border-b border-gray-200 last:border-b-0">
                      <Link :href="item.link" @click="isMobileMenuOpen = false"
                        class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 no-underline text-sm hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-primary transition">
                      <i :class="[item.icon, 'text-base mr-3 w-5 text-center']"></i>
                      <span>{{ item.text }}</span>
                      </Link>
                    </li>
                  </ul>
                </transition>
              </li>

              <li>
                <button @click="toggleMobileDropdown('informasi')"
                  class="w-full flex items-center justify-between text-gray-800 dark:text-gray-100 font-semibold text-base py-2 hover:text-primary">
                  <span>{{ t('menu.bumd_info', 'Informasi BUMD').toUpperCase() }}</span>
                  <i
                    :class="['pi', 'text-sm transition-transform', activeMobileDropdown === 'informasi' ? 'pi-chevron-up' : 'pi-chevron-down']"></i>
                </button>

                <transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0 max-h-0"
                  enter-to-class="opacity-100 max-h-96" leave-active-class="transition-all duration-200"
                  leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
                  <ul v-if="activeMobileDropdown === 'informasi'"
                    class="overflow-hidden dark:bg-gray-800 rounded-lg mt-2 pl-4">
                    <li v-for="item in informasiMenu" :key="item.text" class="border-b border-gray-200 last:border-b-0">
                      <div v-if="item.children">
                        <button @click="toggleMobileSubmenu(item.text)"
                          class="w-full flex items-center justify-between px-4 py-3 text-gray-700 dark:text-gray-200 text-sm hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-primary transition">
                          <div class="flex items-center">
                            <i :class="[item.icon, 'text-base mr-3 w-5 text-center']"></i>
                            <span>{{ item.text }}</span>
                          </div>
                          <i
                            :class="['pi', 'text-xs transition-transform', activeMobileSubmenu === item.text ? 'pi-chevron-up' : 'pi-chevron-down']"></i>
                        </button>

                        <ul v-if="activeMobileSubmenu === item.text" class="dark:bg-gray-700 pl-8">
                          <li v-for="child in item.children" :key="child.text">
                            <a :href="child.link" target="_blank" rel="noopener noreferrer"
                              class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-200 no-underline text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                              <i :class="[child.icon, 'mr-2']"></i>
                              <span>{{ child.text }}</span>
                            </a>
                          </li>
                        </ul>
                      </div>

                      <Link v-else :href="item.link || '#'" @click="isMobileMenuOpen = false"
                        class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 no-underline text-sm hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-primary transition">
                      <i :class="[item.icon, 'text-base mr-3 w-5 text-center']"></i>
                      <span>{{ item.text }}</span>
                      </Link>
                    </li>
                  </ul>
                </transition>
              </li>

              <li>
                <Link href="/berita" @click="isMobileMenuOpen = false"
                  class="block no-underline text-gray-800 dark:text-gray-100 font-semibold text-base py-2 hover:text-primary uppercase">
                {{ t('menu.news', 'Berita') }}
                </Link>
              </li>

              <li>
                <Link href="/galeri" @click="isMobileMenuOpen = false"
                  class="block no-underline text-gray-800 dark:text-gray-100 font-semibold text-base py-2 hover:text-blue-600 uppercase">
                {{ t('menu.gallery', 'Galeri') }}
                </Link>
              </li>

              <li>
                <a href="/regulasi" @click="isMobileMenuOpen = false"
                  class="block no-underline text-gray-800 dark:text-gray-100 font-semibold text-base py-2 hover:text-blue-600">
                  {{ t('menu.regulations', 'Regulasi').toUpperCase() }}
                </a>
              </li>
              <li>
                <button @click="toggleMobileDropdown('ppid')"
                  class="w-full flex items-center justify-between text-gray-800 dark:text-gray-100 font-semibold text-base py-2 hover:text-blue-600">
                  <span>{{ t('menu.ppid', 'PPID') }}</span>
                  <i
                    :class="['pi', 'text-sm transition-transform', activeMobileDropdown === 'ppid' ? 'pi-chevron-up' : 'pi-chevron-down']"></i>
                </button>

                <transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0 max-h-0"
                  enter-to-class="opacity-100 max-h-96" leave-active-class="transition-all duration-200"
                  leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
                  <div v-if="activeMobileDropdown === 'ppid'"
                    class="overflow-hidden dark:bg-gray-800 rounded-lg mt-2 pl-4 space-y-4 py-4">

                    <div>
                      <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase mb-2 px-4 pt-2">{{ t('ppid.categories.info_ppid', 'INFORMASI PPID') }}</h4>
                      <ul class="space-y-1">
                        <li v-for="item in ppidMenu" :key="item.text">
                          <Link :href="item.link" @click="isMobileMenuOpen = false"
                            class="block no-underline text-gray-700 dark:text-gray-200 text-sm px-4 py-2 hover:bg-blue-100 dark:hover:bg-gray-700 hover:text-blue-700 transition">
                          {{ item.text }}
                          </Link>
                        </li>
                      </ul>
                    </div>

                    <div>
                      <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase mb-2 px-4">{{ t('ppid.categories.public_info', 'INFORMASI PUBLIK') }}</h4>
                      <ul class="space-y-1">
                        <li v-for="item in informasiPublikMenu" :key="item.text">
                          <Link :href="item.link" @click="isMobileMenuOpen = false"
                            class="block no-underline text-gray-700 dark:text-gray-200 text-sm px-4 py-2 hover:bg-blue-100 dark:hover:bg-gray-700 hover:text-blue-700 transition">
                          {{ item.text }}
                          </Link>
                        </li>
                      </ul>
                    </div>

                    <div>
                      <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase mb-2 px-4">{{ t('ppid.categories.service_standards', 'STANDAR LAYANAN') }}</h4>
                      <ul class="space-y-1 pb-2">
                        <li v-for="item in standarLayananMenu" :key="item.text">
                          <Link :href="item.link" @click="isMobileMenuOpen = false"
                            class="block no-underline text-gray-700 dark:text-gray-200 text-sm px-4 py-2 hover:bg-blue-100 dark:hover:bg-gray-700 hover:text-blue-700 transition">
                          {{ item.text }}
                          </Link>
                        </li>
                      </ul>
                    </div>

                  </div>
                </transition>
              </li>

              <!-- <li>
                <a href="/faq" @click="isMobileMenuOpen = false"
                  class="block no-underline text-gray-800 dark:text-gray-100 font-semibold text-base py-2 hover:text-blue-600">
                  {{ t('menu.faq', 'FAQ').toUpperCase() }}
                </a>
              </li> -->

              
            </ul>
            <!-- Mobile Language Switcher -->
            <div class="mt-6">
              <LanguageSwitcher />
            </div>
          </div>
        </nav>
      </transition>

    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Link } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import useTranslations from '@/Composables/useTranslations.js';

// --- STATE MANAGEMENT ---
const isMobileMenuOpen = ref(false);
const activeMobileDropdown = ref(null);
const activeMobileSubmenu = ref(null);

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
  // Close all dropdowns when closing mobile menu
  if (!isMobileMenuOpen.value) {
    activeMobileDropdown.value = null;
    activeMobileSubmenu.value = null;
  }
};

const toggleMobileDropdown = (dropdown) => {
  if (activeMobileDropdown.value === dropdown) {
    activeMobileDropdown.value = null;
    activeMobileSubmenu.value = null;
  } else {
    activeMobileDropdown.value = dropdown;
    activeMobileSubmenu.value = null;
  }
};

const toggleMobileSubmenu = (submenu) => {
  if (activeMobileSubmenu.value === submenu) {
    activeMobileSubmenu.value = null;
  } else {
    activeMobileSubmenu.value = submenu;
  }
};

const activeDropdown = ref(null)

const activeSubmenu = ref(null);

// --- THEME TOGGLE ---
const theme = ref(document.documentElement.classList.contains('dark') ? 'dark' : 'light');
let rootClassObserver = null;

const updateThemeFromDocument = () => {
  theme.value = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
};

onMounted(() => {
  // Sync with current <html> class and observe for changes (e.g., toggled elsewhere)
  updateThemeFromDocument();
  rootClassObserver = new MutationObserver(updateThemeFromDocument);
  rootClassObserver.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class'],
  });
});

onBeforeUnmount(() => {
  if (rootClassObserver) rootClassObserver.disconnect();
});
const toggleTheme = () => {
  if (theme.value === 'dark') {
    theme.value = 'light';
    try { localStorage.setItem('theme', 'light'); } catch (_) { }
    document.documentElement.classList.remove('dark');
  } else {
    theme.value = 'dark';
    try { localStorage.setItem('theme', 'dark'); } catch (_) { }
    document.documentElement.classList.add('dark');
  }
};

const { t } = useTranslations();

const profilMenu = [
  { text: t('profile.tentangkami', 'Tentang Kami'), link: '/tentang-kami', icon: 'pi pi-user' },
  { text: t('profile.visimisi', 'Visi dan Misi'), link: '/visi-misi', icon: 'pi pi-warehouse' },
  { text: t('profile.tupoksi', 'Tugas Pokok dan Fungsi'), link: '/tugas-pokok-dan-fungsi', icon: 'pi pi-briefcase' },
  { text: t('profile.org_structure', 'Struktur Organisasi'), link: '/struktur-organisasi', icon: 'pi pi-sitemap' },
  { text: t('profile.officials', 'Pejabat Struktural'), link: '/pejabat', icon: 'pi pi-users' },
  { text: t('profile.renstra', 'Rencana Strategis'), link: '/rencana-strategis', icon: 'pi pi-file-pdf' },
];

const informasiMenu = [
  { text: t('informasi.list_bumd', 'Daftar BUMD dan Perusahaan Patungan'), link: '/daftar-bumd-perusahaan-patungan', icon: 'pi pi-th-large' },
  { text: t('informasi.profiles', 'Profil BUMD dan Perusahaan Patungan'), link: '/bumd', icon: 'pi pi-building-columns' },
  {
    text: t('informasi.financial_performance', 'Kinerja Keuangan BUMD dan Perusahaan Patungan'),
    icon: 'pi pi-chart-line',
    children: [
      { text: t('informasi.child.financial_conditions_bumd', 'Kondisi Keuangan BUMD'), link: '/uploads/kondisi-keuangan-bumd.pdf' },
      { text: t('informasi.child.financial_ratios_bumd', 'Rasio Keuangan BUMD'), link: '/uploads/rasio-keuangan-bumd.pdf' },
      { text: t('informasi.child.financial_conditions_bumd_joint', 'Kondisi Keuangan BUMD dan Perusahaan Patungan'), link: '/uploads/kondisi-keuangan-bumd-perusahaan-patungan.pdf' },
      { text: t('informasi.child.financial_ratios_bumd_joint', 'Rasio Keuangan BUMD dan Perusahaan Patungan'), link: '/uploads/rasio-keuangan-bumd-perusahaan-patungan.pdf' },
    ]
  },
];

const ppidMenu = [
  { text: t('ppid.profile', 'Profil PPID'), link: '/ppid/profil' },
  { text: t('ppid.vision_mission', 'Visi dan Misi PPID'), link: '/ppid/visi-misi' },
  { text: t('ppid.org_structure', 'Struktur Organisasi PPID'), link: '/ppid/struktur-organisasi' },
  { text: t('ppid.duties_functions', 'Tugas dan Fungsi PPID'), link: '/ppid/tugas-fungsi' },
  { text: t('ppid.legal_basis', 'Dasar dan Hukum PPID'), link: '/ppid/dasar-hukum' },
];

const informasiPublikMenu = [
  { text: t('informasi_publik.periodic', 'Informasi yang Wajib Disediakan dan Diumumkan Secara Berkala'), link: '/ppid/informasi-yang-wajib-disediakan-dan-diumumkan-secara-berkala' },
  { text: t('informasi_publik.anytime', 'Informasi yang Wajib Tersedia Setiap Saat'), link: '/ppid/informasi-yang-wajib-tersedia-setiap-saat' },
  { text: t('informasi_publik.immediate', 'Informasi yang Wajib Diumumkan Secara Serta Merta'), link: '/ppid/informasi-yang-wajib-diumumkan-secara-serta-merta' },
];

const standarLayananMenu = [
  { text: t('standar_layanan.service_charter', 'Maklumat Layanan'), link: '/ppid/maklumat-layanan' },
  { text: t('standar_layanan.info_request_procedure', 'Prosedur Permohonan Informasi'), link: '/ppid/tata-cara-permohonan-informasi' },
  { text: t('standar_layanan.objection_procedure', 'Prosedur Pengajuan Keberatan'), link: '/ppid/prosedur-pengajuan-keberatan' },
  { text: t('standar_layanan.dispute_procedure', 'Prosedur Permohonan Penyelesaian Sengketa Informasi'), link: '/ppid/tata-cara-pengajuan-permohonan' },
  { text: t('standar_layanan.service_fees', 'Biaya Layanan'), link: '/ppid/biaya-layanan' },
];


</script>