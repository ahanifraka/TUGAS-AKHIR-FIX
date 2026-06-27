<script setup>
import { computed, ref, onMounted } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

// Terima props 'pejabats' dari ProfilController
const props = defineProps({
    pejabats: { type: Array, default: () => [] },
});

// Pastikan selalu berupa array agar aman di template
const pejabats = computed(() => props.pejabats ?? []);

// Loading states
const isLoading = ref(true);
const loadedImages = ref(new Set());
const page = usePage();

// Simulate initial loading for smooth transition
onMounted(() => {
    // Check if navigating from another page
    const isInertiaVisit = page.props.value !== null;

    if (isInertiaVisit && pejabats.value.length > 0) {
        // Short delay for smooth transition
        setTimeout(() => {
            isLoading.value = false;
        }, 300);
    } else {
        isLoading.value = false;
    }
});

// Track image loading
const handleImageLoad = (id) => {
    loadedImages.value.add(id);
};

const isImageLoaded = (id) => {
    return loadedImages.value.has(id);
};

// Data untuk Hero dan Breadcrumb (ini sudah benar)
const breadcrumbItems = [
    { label: 'Profil', url: '#' },
    { label: 'Pejabat Struktural', url: '/pejabat' },
];
const heroTitle = 'Pejabat Struktural';
const heroDescription = 'Informasi mengenai Pejabat Struktural BPBUMD DKI';

// Definisi struktur organisasi - Hanya 2 level teratas
const organizationStructure = [
    { 
        title: 'Kepala Badan', 
        keywords: ['kepala badan'],
        excludeKeywords: []
    },
    { 
        title: 'Sekretariat', 
        keywords: ['sekretaris badan', 'sekretaris'],
        excludeKeywords: [],
        includeSubordinates: [
            'kepala subbagian umum dan kepegawaian',
            'ketua subkelompok program dan keuangan',
            'ketua subkelompok data dan informasi'
        ]
    },
    { 
        title: 'Bidang Usaha Transportasi, Properti, dan Keuangan', 
        keywords: ['kepala bidang usaha transportasi'],
        excludeKeywords: [],
        includeSubordinates: [
            'ketua subkelompok usaha transportasi',
            'ketua subkelompok usaha properti',
            'ketua subkelompok usaha keuangan'
        ]
    },
    { 
        title: 'Bidang Usaha Infrastruktur, Pariwisata, dan Kawasan', 
        keywords: ['kepala bidang usaha infrastruktur'],
        excludeKeywords: [],
        includeSubordinates: [
            'ketua subkelompok usaha infrastruktur',
            'ketua subkelompok usaha pariwisata',
            'ketua subkelompok usaha kawasan'
        ]
    },
    { 
        title: 'Bidang Usaha Pangan, Utilitas, serta Perpasaran dan Industri', 
        keywords: ['kepala bidang usaha pangan'],
        excludeKeywords: [],
        includeSubordinates: [
            'ketua subkelompok usaha pangan',
            'ketua subkelompok usaha utilitas',
            'ketua subkelompok usaha perpasaran'
        ]
    },
    { 
        title: 'Pusat Kebijakan Strategis dan Pelayanan BUMD', 
        keywords: ['kepala pusat kebijakan strategis'],
        excludeKeywords: [],
        includeSubordinates: [
            'kepala subbagian tata usaha',
            'ketua satuan pelaksana strategis',
            'kepala satuan pelaksana tanggung jawab sosial'
        ]
    },
    { 
        title: 'Kelompok Jabatan Fungsional', 
        keywords: ['kelompok jabatan fungsional'],
        excludeKeywords: [],
        includeSubordinates: []
    },
];

// Group pejabat by their position (including subordinates)
const groupedPejabats = computed(() => {
    const groups = [];
    
    organizationStructure.forEach(structure => {
        const filteredPejabats = pejabats.value.filter(pejabat => {
            const jabatanLower = pejabat.jabatan?.toLowerCase() || '';
            
            // Check main keywords
            const matchesMain = structure.keywords.some(keyword => 
                jabatanLower.includes(keyword.toLowerCase())
            );
            
            // Check subordinates keywords
            const matchesSubordinate = structure.includeSubordinates?.some(keyword => 
                jabatanLower.includes(keyword.toLowerCase())
            ) || false;
            
            return matchesMain || matchesSubordinate;
        });
        
        if (filteredPejabats.length > 0) {
            groups.push({
                title: structure.title,
                pejabats: filteredPejabats
            });
        }
    });
    
    return groups;
});
</script>

<template>

    <Head title="Pejabat Struktural">
        <meta name="description" content="Pejabat Struktural Pemerintah BPBUMD DKI Jakarta">
        <meta name="keywords" content="Pejabat Struktural, Pemerintah BPBUMD DKI Jakarta">
    </Head>

    <GuestLayout>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />

        <!-- Skeleton Loading State -->
        <div v-if="isLoading" class="py-12 mx-auto bg-gray-100 dark:bg-gray-900 px-8 md:px-12">
            <div class="container mx-auto max-w-6xl">
                <div v-for="n in 3" :key="`section-skeleton-${n}`" class="mb-12">
                    <div class="h-8 bg-gray-300 dark:bg-gray-700 rounded w-64 mb-6 animate-pulse"></div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="m in 4" :key="`card-skeleton-${n}-${m}`" class="bg-white p-4 rounded-lg shadow animate-pulse">
                            <div class="rounded-md bg-gray-300 dark:bg-gray-700 mx-auto mb-4 w-full h-[300px]"></div>
                            <div class="flex flex-col items-center space-y-3">
                                <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actual Content with Grouped Sections -->
        <div v-else class="py-12 mx-auto bg-gray-100 dark:bg-gray-900 px-8 md:px-12">
            <div class="container mx-auto max-w-6xl">
                <!-- Display message if no pejabats -->
                <div v-if="groupedPejabats.length === 0"
                    class="text-center text-gray-500 dark:text-gray-400 py-10 animate-fade-in">
                    <i class="pi pi-users text-4xl mb-4 block"></i>
                    <p>Data pejabat belum tersedia.</p>
                </div>

                <!-- Loop through grouped sections -->
                <div v-else v-for="(group, groupIndex) in groupedPejabats" :key="`group-${groupIndex}`" class="mb-12">
                    <!-- Section Title -->
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        {{ group.title }}
                    </h2>

                    <!-- Grid of Pejabat Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        <Link v-for="(pejabat, index) in group.pejabats" 
                            :key="pejabat.id" 
                            :href="route('pejabat.show.public', pejabat.id)"
                            class="rounded-lg transition-all duration-300 hover:-translate-y-1 animate-fade-in">

                            <!-- Image Container with Loading State -->
                            <div class="relative text-left rounded-md overflow-hidden mx-auto mb-4 w-full max-h-[300px]">
                                <!-- Image Skeleton -->
                                <div v-if="!isImageLoaded(pejabat.id)"
                                    class="absolute inset-0 animate-pulse bg-gray-300 dark:bg-gray-700"></div>

                                <!-- Actual Image -->
                                <img :src="pejabat.image ? pejabat.image : '/images/default.jpg'" 
                                    :alt="pejabat.nama"
                                    @load="handleImageLoad(pejabat.id)"
                                    class="border border-gray-300 dark:border-gray-700 rounded-xl object-cover object-top mx-auto w-full max-h-[300px] transition-opacity duration-300 aspect-square"
                                    :class="isImageLoaded(pejabat.id) ? 'opacity-100' : 'opacity-0'" 
                                    loading="lazy"
                                    decoding="async">
                            </div>

                            <div class="flex flex-col items-start">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ pejabat.nama }}</h3>
                                <p class="text-sm text-gray-800 dark:text-gray-400">{{ pejabat.jabatan }}</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>

</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

/* Stagger animation for cards */
.animate-fade-in:nth-child(1) {
    animation-delay: 0.05s;
}

.animate-fade-in:nth-child(2) {
    animation-delay: 0.1s;
}

.animate-fade-in:nth-child(3) {
    animation-delay: 0.15s;
}

.animate-fade-in:nth-child(4) {
    animation-delay: 0.2s;
}

.animate-fade-in:nth-child(5) {
    animation-delay: 0.25s;
}

.animate-fade-in:nth-child(6) {
    animation-delay: 0.3s;
}

.animate-fade-in:nth-child(7) {
    animation-delay: 0.35s;
}

.animate-fade-in:nth-child(8) {
    animation-delay: 0.4s;
}
</style>
