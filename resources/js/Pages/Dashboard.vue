<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Calendar from "primevue/calendar";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DashboardCard from "@/Components/DashboardCard.vue";
import DashboardStatusTile from "@/Components/DashboardStatusTile.vue";

// Get user data
const user = usePage().props.auth.user;

// Time-based greeting function
const getTimeBasedGreeting = () => {
    const hour = new Date().getHours();

    if (hour >= 5 && hour < 11) {
        return "Selamat Pagi";
    } else if (hour >= 11 && hour < 15) {
        return "Selamat Siang";
    } else if (hour >= 15 && hour < 18) {
        return "Selamat Sore";
    } else {
        return "Selamat Malam";
    }
};

const greeting = computed(() => getTimeBasedGreeting());

// Props
const props = defineProps({
    totalBerita: { type: Number, default: 0 },
    totalDraftBerita: { type: Number, default: 0 },
    totalSliders: { type: Number, default: 0 },
    totalDraftSliders: { type: Number, default: 0 },
    totalBumd: { type: Number, default: 0 },
    totalAlbum: { type: Number, default: 0 },
    totalPengumuman: { type: Number, default: 0 },
    totalRegulasi: { type: Number, default: 0 },
    totalPages: { type: Number, default: 0 },
    totalPejabat: { type: Number, default: 0 },
    totalUsers: { type: Number, default: 0 },
    permohonanStatus: { type: Object, default: () => ({}) },
    keberatanStatus: { type: Object, default: () => ({}) },
    beritaEvents: { type: Array, default: () => [] },
});

// Calendar state
const selectedDate = ref(new Date());

const selectedDateEvents = computed(() => {
    if (!selectedDate.value) return [];
    const year = selectedDate.value.getFullYear();
    const month = String(selectedDate.value.getMonth() + 1).padStart(2, "0");
    const day = String(selectedDate.value.getDate()).padStart(2, "0");
    const dateString = `${year}-${month}-${day}`;
    return props.beritaEvents.filter((event) => event.date === dateString);
});

const hasEvent = (date) => {
    const month = String(date.month + 1).padStart(2, "0");
    const day = String(date.day).padStart(2, "0");
    const dateString = `${date.year}-${month}-${day}`;
    return props.beritaEvents.some((event) => event.date === dateString);
};

// Roles from shared props using usePage
const page = usePage();
const roles = computed(() => {
    const user = page.props?.auth?.user;
    return Array.isArray(user?.roles) ? user.roles : [];
});

function hasRole(role) {
    return roles.value.includes(role);
}
</script>

<style>
.p-datepicker-today > .p-datepicker-day-selected {
    background-color: #dbeafe !important;
    color: #000 !important;
}

.p-datepicker-day-selected {
    background-color: #99f6e4 !important;
    color: #000 !important;
}
</style>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h1>
        </template>

        <div class="py-12 px-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ greeting }} {{ user.name }}!
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4"
                >
                    <DashboardCard
                        title="Sliders"
                        :value="props.totalSliders"
                        description="Total Sliders Terpublikasi"
                        icon="pi-image"
                        title-weight="font-bold"
                        value-size="text-4xl"
                        v-if="hasRole('editor') || hasRole('admin')"
                    />
                    <DashboardCard
                        title="Berita"
                        :value="props.totalBerita"
                        description="Total Berita Terpublikasi"
                        icon="pi-book"
                        title-weight="font-medium"
                        value-size="text-4xl"
                    />
                    <DashboardCard
                        title="Album"
                        :value="props.totalAlbum"
                        description="Total Album Terpublikasi"
                        icon="pi-images"
                        title-weight="font-bold"
                        value-size="text-3xl"
                    />
                    <DashboardCard
                        title="Pengumuman"
                        :value="props.totalPengumuman"
                        description="Total Pengumuman Terpublikasi"
                        icon="pi-images"
                        title-weight="font-bold"
                        value-size="text-3xl"
                    />
                    <DashboardCard
                        title="BUMD & Patungan"
                        :value="props.totalBumd"
                        description="Total BUMD dan Perusahaan Patungan Aktif"
                        icon="pi-building-columns"
                        title-weight="font-bold"
                        value-size="text-3xl"
                        v-if="hasRole('editor') || hasRole('admin')"
                    />
                    <DashboardCard
                        title="Regulasi"
                        :value="props.totalRegulasi"
                        description="Total Regulasi Aktif"
                        v-if="hasRole('editor') || hasRole('admin')"
                        icon="pi-file"
                        title-weight="font-bold"
                        value-size="text-3xl"
                    />
                    <DashboardCard
                        title="Pages"
                        :value="props.totalPages"
                        description="Total Pages Terpublikasi"
                        icon="pi-file-edit"
                        title-weight="font-bold"
                        value-size="text-3xl"
                        v-if="hasRole('editor') || hasRole('admin')"
                    />
                    <DashboardCard
                        title="Pejabat"
                        :value="props.totalPejabat"
                        description="Total Pejabat Terpublikasi"
                        icon="pi-id-card"
                        title-weight="font-bold"
                        value-size="text-3xl"
                        v-if="hasRole('editor') || hasRole('admin')"
                    />
                    <DashboardCard
                        title="User"
                        :value="props.totalUsers"
                        description="Total Pengguna"
                        icon="pi-users"
                        title-weight="font-bold"
                        value-size="text-3xl"
                        v-if="hasRole('admin')"
                    />
                </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <DashboardStatusTile
                        title="Permohonan Informasi"
                        :statuses="props.permohonanStatus"
                        icon="pi-inbox"
                        v-if="hasRole('editor') || hasRole('admin')"
                    />
                    <DashboardStatusTile
                        title="Pengajuan Keberatan"
                        :statuses="props.keberatanStatus"
                        icon="pi-exclamation-triangle"
                        v-if="hasRole('editor') || hasRole('admin')"
                    />
                </div>

                <!-- Calendar Widget -->
                <div class="grid grid-cols-1 gap-6">
                    <div
                        class="overflow-hidden col-span-1 md:col-span-2 bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="mb-4 flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-800">
                                    Kalender
                                </h2>
                                <div class="text-sm text-gray-500">
                                    {{
                                        new Intl.DateTimeFormat("id-ID", {
                                            dateStyle: "full",
                                        }).format(selectedDate || new Date())
                                    }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-1">
                                    <Calendar
                                        v-model="selectedDate"
                                        inline
                                        showIcon
                                        class="w-full"
                                    >
                                        <template #date="slotProps">
                                            <div
                                                class="w-full h-full flex flex-col items-center justify-center relative p-2"
                                            >
                                                <span
                                                    :class="{
                                                        'flex items-center justify-center':
                                                            hasEvent(
                                                                slotProps.date
                                                            ),
                                                    }"
                                                >
                                                    {{ slotProps.date.day }}
                                                </span>
                                                <span
                                                    v-if="
                                                        hasEvent(slotProps.date)
                                                    "
                                                    class="absolute bottom-[0.09rem] h-1.5 w-1.5 bg-blue-400 rounded-full"
                                                ></span>
                                            </div>
                                        </template>
                                    </Calendar>
                                </div>

                                <div
                                    v-if="selectedDateEvents.length > 0"
                                    class="md:col-span-1"
                                >
                                    <h3
                                        class="font-semibold text-gray-800 mb-3"
                                    >
                                        Jadwal Berita ({{
                                            selectedDateEvents.length
                                        }})
                                    </h3>
                                    <div class="space-y-3">
                                        <div
                                            v-for="event in selectedDateEvents"
                                            :key="event.id"
                                            class="p-3 rounded-lg border border-gray-100 bg-gray-50 hover:bg-gray-100 transition-colors"
                                        >
                                            <div
                                                class="flex items-start justify-between"
                                            >
                                                <span
                                                    class="text-sm font-medium text-gray-700"
                                                    >{{ event.title }}</span
                                                >
                                                <span
                                                    :class="
                                                        event.published
                                                            ? 'bg-green-100 text-green-800'
                                                            : 'bg-yellow-100 text-yellow-800'
                                                    "
                                                    class="text-xs px-2 py-1 rounded-full ml-2 whitespace-nowrap"
                                                >
                                                    {{
                                                        event.published
                                                            ? "Published"
                                                            : "Scheduled"
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="md:col-span-1 text-center text-gray-500 text-sm"
                                >
                                    Tidak ada jadwal berita untuk tanggal ini.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
