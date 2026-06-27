<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { onClickOutside } from '@vueuse/core';

// import Dropdown from '@/Components/Dropdown.vue';
// import DropdownLink from '@/Components/DropdownLink.vue';
// import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

// State for mobile navigation toggle
const showingNavigationDropdown = ref(false);
const showNotifications = ref(false);
const notificationDropdown = ref(null);

// Close notification dropdown when clicking outside
onClickOutside(notificationDropdown, () => {
    showNotifications.value = false;
});

// Roles from shared props using usePage
const page = usePage();
const roles = computed(() => {
    const user = page.props?.auth?.user;
    return Array.isArray(user?.roles) ? user.roles : [];
});

const ppidNotifications = computed(() => page.props?.ppid_notifications);
const notificationCount = computed(() => ppidNotifications.value?.total || 0);

function hasRole(role) {
    return roles.value.includes(role);
}
</script>

<template>
    <nav class="block lg:hidden border-b border-gray-100 bg-white">
        <!-- Primary Navigation Menu -->
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 justify-between w-full">
                <div class="flex flex-row justify-between lg:justify-start items-center w-full">
                    <!-- Logo moved to Sidebar -->
                    <!-- Navigation moved to Sidebar -->
                    
                    <!-- Notifications Bell -->
                    <div v-if="hasRole('editor') || hasRole('admin')" class="relative mr-4">
                        <button
                            @click="showNotifications = !showNotifications"
                            class="relative inline-flex items-center justify-center p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition duration-150 ease-in-out"
                            :aria-expanded="showNotifications ? 'true' : 'false'"
                        >
                            <i class="pi pi-bell text-xl"></i>
                            <span
                                v-if="notificationCount > 0"
                                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
                            >
                                {{ notificationCount }}
                            </span>
                        </button>

                        <!-- Notifications Dropdown -->
                        <div
                            ref="notificationDropdown"
                            v-show="showNotifications"
                            class="absolute right-0 z-50 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden border border-gray-200"
                        >
                            <div class="py-2 px-4 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-700">PPID Notifications</h3>
                            </div>
                            
                            <div class="max-h-96 overflow-y-auto">
                                <!-- Permohonan Informasi -->
                                <div v-if="ppidNotifications?.permohonan_informasi > 0" class="px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Permohonan Informasi</span>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded-full">
                                            {{ ppidNotifications.permohonan_informasi }}
                                        </span>
                                    </div>
                                    <div v-for="item in ppidNotifications.recent?.permohonan" :key="item.id" class="mt-2">
                                        <a
                                            :href="route('admin.ppid.permohonan-informasi.show', item.id)"
                                            class="block text-xs text-gray-600 hover:text-blue-600 hover:bg-gray-50 p-2 rounded"
                                        >
                                            <div class="font-medium">{{ item.nama }}</div>
                                            <div class="text-gray-500">{{ item.kode_unik }}</div>
                                        </a>
                                    </div>
                                </div>

                                <!-- Pengajuan Keberatan -->
                                <div v-if="ppidNotifications?.pengajuan_keberatan > 0" class="px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Pengajuan Keberatan</span>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-orange-600 rounded-full">
                                            {{ ppidNotifications.pengajuan_keberatan }}
                                        </span>
                                    </div>
                                    <div v-for="item in ppidNotifications.recent?.keberatan" :key="item.id" class="mt-2">
                                        <a
                                            :href="route('admin.ppid.pengajuan-keberatan.show', item.id)"
                                            class="block text-xs text-gray-600 hover:text-orange-600 hover:bg-gray-50 p-2 rounded"
                                        >
                                            <div class="font-medium">{{ item.nama }}</div>
                                            <div class="text-gray-500">{{ item.kode_unik }}</div>
                                        </a>
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div v-if="notificationCount === 0" class="px-4 py-8 text-center text-gray-500 text-sm">
                                    <i class="pi pi-check-circle text-3xl mb-2 text-green-500"></i>
                                    <p>No pending requests</p>
                                </div>
                            </div>

                            <div class="py-2 px-4 bg-gray-50 border-t border-gray-200">
                                <a
                                    :href="route('admin.ppid.dashboard')"
                                    class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium"
                                >
                                    View All PPID Requests
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center lg:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                            :aria-expanded="showingNavigationDropdown ? 'true' : 'false'" aria-controls="mobile-menu"
                            class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path
                                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Account actions moved to Sidebar -->
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div id="mobile-menu" :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
            class="lg:hidden bg-white border-t border-gray-200 shadow-md">
            <div class="space-y-1 pb-3 pt-2">
                <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-home"></i> Dashboard</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink v-if="hasRole('editor') || hasRole('admin')" :href="route('content-sliders.index')"
                    :active="route().current('content-sliders.*')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-images"></i> Sliders</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('beritas.index')" :active="route().current('beritas.*')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-book"></i> Berita</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink v-if="hasRole('editor') || hasRole('admin')" :href="route('bumds.index')"
                    :active="route().current('bumds.*')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-building-columns"></i> BUMD</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('albums.index')" :active="route().current('albums.*')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-images"></i> Albums</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink v-if="hasRole('editor') || hasRole('admin')" :href="route('regulasis.index')"
                    :active="route().current('regulasis.*')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-file"></i> Regulasi</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink v-if="hasRole('editor') || hasRole('admin')" :href="route('content-pages.index')"
                    :active="route().current('content-pages.*')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-file-edit"></i> Pages</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink v-if="hasRole('editor') || hasRole('admin')" :href="route('pejabats.index')"
                    :active="route().current('pejabats.*')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-id-card"></i> Pejabat</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('media.index')" :active="route().current('media.*')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-folder"></i> Media Library</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink v-if="hasRole('admin')" :href="route('users.index')"
                    :active="route().current('users.*')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-users"></i> Users</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink v-if="hasRole('admin')" :href="route('logs.index')"
                    :active="route().current('logs.*')">
                    <span class="inline-flex items-center gap-2"><i class="pi pi-history"></i> Logs</span>
                </ResponsiveNavLink>
                <ResponsiveNavLink v-if="hasRole('editor') || hasRole('admin')" :href="route('admin.ppid.dashboard')"
                    :active="route().current('admin.ppid.*')">
                    <span class="inline-flex items-center gap-2 justify-between w-full">
                        <span class="inline-flex items-center gap-2">
                            <i class="pi pi-info-circle"></i> PPID
                        </span>
                        <span 
                            v-if="notificationCount > 0" 
                            class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full"
                        >
                            {{ notificationCount }}
                        </span>
                    </span>
                </ResponsiveNavLink>
            </div>

            <!-- Responsive Settings Options -->
            <div class="border-t border-gray-200 pb-1 pt-4">
                <div class="px-4">
                    <div class="text-base font-medium text-gray-800">
                        <div class="text-base font-medium text-gray-800">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-sm font-medium text-gray-500">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">
                            Profile
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                            Log Out
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>