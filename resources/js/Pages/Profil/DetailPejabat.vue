<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    pejabat: Object 
});

const heroTitle = computed(() => props.pejabat.nama);
const heroDescription = computed(() => props.pejabat.jabatan);

const breadcrumbItems = computed(() => [
    { label: 'Profil', url: '#' }, 
    { label: 'Pejabat Struktural', url: '/profil/pejabat' },
    { label: props.pejabat.nama, url: '#' },
]);
// -------------------------------------------

</script>

<template>
    <Head :title="pejabat.nama" />

    <GuestLayout>

        <Hero 
            :breadcrumb="breadcrumbItems" 
            :title="heroTitle" 
            :description="heroDescription" 
        />

        <div class="container mx-auto px-4 py-12 md:py-16">
            <div class="flex flex-col md:flex-row gap-8 lg:gap-12 items-start"> 
                
                <div class="md:w-4/12 lg:w-3/12 md:sticky md:top-24 self-start">
                    <img 
                        :src="pejabat.image ? pejabat.image : '/images/default.jpg'" 
                        :alt="'Foto ' + pejabat.nama"
                        class="w-full h-auto max-h-[450px] rounded-lg shadow-lg object-cover object-top"
                    > 
                </div>

                <div class="md:w-8/12 lg:w-9/12">
                    <h1 class="text-4xl font-bold mb-2">{{ pejabat.nama }}</h1>
                    <p class="text-2xl text-gray-600 dark:text-white mb-8">{{ pejabat.jabatan }}</p>
                    <hr class="border-gray-200 mb-8">
                    <div 
                        class="prose prose-xl max-w-none text-gray-800 dark:text-white leading-relaxed" 
                        v-html="pejabat.description"
                    >
                        </div>
                </div>

            </div>
        </div>
    </GuestLayout>
</template>

<style>
.prose p {
    margin-bottom: 1em;
}
</style>