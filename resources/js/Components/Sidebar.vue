<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

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
    <aside
        class="hidden lg:flex sticky top-0 flex-col h-screen w-64 bg-white border-r"
    >
        <!-- Logo -->
        <div class="px-4 py-6 border-b">
            <Link
                :href="route('dashboard')"
                class="flex items-center w-full justify-center"
            >
                <img
                    src="/images/logo-bpbumd.png"
                    alt="Logo BPBUMD"
                    class="w-40"
                />
            </Link>
        </div>

        <!-- Navigation -->
        <nav class="space-y-1 px-2 pt-4 flex-1 overflow-y-auto">
            <ResponsiveNavLink
                :href="route('dashboard')"
                :active="route().current('dashboard')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-th-large"></i> Dashboard</span
                >
            </ResponsiveNavLink>
            <ResponsiveNavLink
                v-if="hasRole('editor') || hasRole('admin')"
                :href="route('content-sliders.index')"
                :active="route().current('content-sliders.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-image"></i> Sliders</span
                >
            </ResponsiveNavLink>
            <ResponsiveNavLink
                :href="route('beritas.index')"
                :active="route().current('beritas.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-book"></i> Berita</span
                >
            </ResponsiveNavLink>

            <!-- INI PERUBAHANNYA -->
            <ResponsiveNavLink
                :href="route('admin.pengumuman.index')"
                :active="route().current('pengumuman.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-megaphone"></i> Pengumuman</span
                >
            </ResponsiveNavLink>

            <ResponsiveNavLink
                :href="route('albums.index')"
                :active="route().current('albums.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-images"></i> Albums</span
                >
            </ResponsiveNavLink>
            <ResponsiveNavLink
                v-if="hasRole('editor') || hasRole('admin')"
                :href="route('bumds.index')"
                :active="route().current('bumds.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-building-columns"></i> BUMD</span
                >
            </ResponsiveNavLink>
            <ResponsiveNavLink
                v-if="hasRole('editor') || hasRole('admin')"
                :href="route('regulasis.index')"
                :active="route().current('regulasis.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-file"></i> Regulasi</span
                >
            </ResponsiveNavLink>
            <ResponsiveNavLink
                v-if="hasRole('editor') || hasRole('admin')"
                :href="route('content-pages.index')"
                :active="route().current('content-pages.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-file-edit"></i> Pages</span
                >
            </ResponsiveNavLink>
            <ResponsiveNavLink
                v-if="hasRole('editor') || hasRole('admin')"
                :href="route('pejabats.index')"
                :active="route().current('pejabats.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-id-card"></i> Pejabat</span
                >
            </ResponsiveNavLink>
            <ResponsiveNavLink
                v-if="hasRole('editor') || hasRole('admin')"
                :href="route('admin.ppid.dashboard')"
                :active="route().current('admin.ppid.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-info-circle"></i> PPID</span
                >
            </ResponsiveNavLink>
            <ResponsiveNavLink
                :href="route('media.index')"
                :active="route().current('media.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-folder"></i> Media Library</span
                >
            </ResponsiveNavLink>
            <ResponsiveNavLink
                v-if="hasRole('admin')"
                :href="route('users.index')"
                :active="route().current('users.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-users"></i> Users</span
                >
            </ResponsiveNavLink>
            <ResponsiveNavLink
                v-if="hasRole('admin')"
                :href="route('logs.index')"
                :active="route().current('logs.*')"
            >
                <span class="inline-flex items-center gap-2"
                    ><i class="pi pi-history"></i> Logs</span
                >
            </ResponsiveNavLink>
        </nav>

        <!-- Account actions -->
        <div class="px-2 py-6 border-t">
            <div class="space-y-1">
                <ResponsiveNavLink
                    :href="route('profile.edit')"
                    :active="route().current('profile.edit')"
                >
                    <span class="inline-flex items-center gap-2"
                        ><i class="pi pi-user-edit"></i> Profile</span
                    >
                </ResponsiveNavLink>
                <ResponsiveNavLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                >
                    <span class="inline-flex items-center gap-2"
                        ><i class="pi pi-sign-out"></i> Log Out</span
                    >
                </ResponsiveNavLink>
            </div>
        </div>
    </aside>
</template>
