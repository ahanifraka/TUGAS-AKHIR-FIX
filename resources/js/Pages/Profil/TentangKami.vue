<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';
import useTranslations from '@/Composables/useTranslations.js';

import TimelineSejarah from '@/Sections/TimelineSejarah.vue';
import Timeline from '@/Sections/Timeline.vue';

const { t } = useTranslations();

const breadcrumbItems = [
    { label: t('pages.tentang_kami.breadcrumb.profile', 'Profil'), url: '#' },
    { label: t('pages.tentang_kami.breadcrumb.page', 'Tentang Kami'), url: '/tentang-kami' },
];

const heroTitle = t('pages.tentang_kami.hero.title', 'Tentang Kami');
const heroDescription = t('pages.tentang_kami.hero.description', 'Informasi Tentang BPBUMD Provinsi DKI Jakarta');

// Stats data
const stats = ref([
    {
        value: 14,
        label: 'pages.tentang_kami.stats.bumd',
        defaultLabel: 'Badan Usaha Milik Daerah',
        currentValue: 0,
        color: 'teal',
        icon: 'pi-building-columns'
    },
    {
        value: 9,
        label: 'pages.tentang_kami.stats.joint_venture',
        defaultLabel: 'Perusahaan Patungan',
        currentValue: 0,
        color: 'blue',
        icon: 'pi-building'
    }
]);

const statsContainer = ref(null);
const hasAnimated = ref(false);

// Counter animation function
const animateCounter = (stat, duration = 2000) => {
    const startTime = performance.now();
    const startValue = 0;
    const endValue = stat.value;

    const animate = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function for smooth animation
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        
        stat.currentValue = Math.floor(easeOutQuart * endValue);
        
        if (progress < 1) {
            requestAnimationFrame(animate);
        } else {
            stat.currentValue = endValue;
        }
    };
    
    requestAnimationFrame(animate);
};

// Setup Intersection Observer
onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !hasAnimated.value) {
                hasAnimated.value = true;
                stats.value.forEach(stat => {
                    animateCounter(stat);
                });
            }
        });
    }, {
        threshold: 0.3
    });

    if (statsContainer.value) {
        observer.observe(statsContainer.value);
    }
});
</script>

<template>
    <GuestLayout>

        <Head :title="t('pages.tentang_kami.head.title', 'Tentang Kami')">
            <meta name="description"
                :content="t('pages.tentang_kami.head.description', 'Informasi Tentang BPBUMD Provinsi DKI Jakarta')">
            <meta name="keywords"
                :content="t('pages.tentang_kami.head.keywords', 'Tentang Kami BPBUMD Provinsi DKI Jakarta')">
        </Head>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="py-12">
            <div class="max-w-3xl mx-auto mb-12 px-12 md:px-0">
                <p class="text-xl mb-8 text-justify">{{ t('pages.tentang_kami.description_1', 'Badan Pembinaan BUMD Provinsi Jakarta adalah badan yang bertanggung jawab dalam pengelolaan dan pengembangan BUMD di Provinsi Jakarta.') }}</p>
                <p class="text-xl text-justify">
                    {{ t('pages.tentang_kami.description_2', 'Pembinaan BUMD di Provinsi DKI Jakarta dilakukan oleh SKPD dilingkungan Provinsi DKI Jakarta. Diawal pembentukannya fungsi pembinaan BUMD tidak berdiri sendiri namun disertakan bersama urusan penanaman modal yang terkait dengan investasi Pemerintah Daerah. Bagi Pemerintah DKI Jakarta penanganan penanaman modal merupakan suatu hal yang khusus karena berbeda dengan daerah lainnya di Indonesia.') }}
                </p>
            </div>

            <!-- Stats Counter Section -->
            <div ref="statsContainer" class="max-w-3xl mx-auto mb-16 px-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div 
                        v-for="(stat, index) in stats" 
                        :key="index"
                        :class="[
                            'rounded-lg p-12 text-white text-center transform transition-all duration-500',
                            stat.color === 'teal' ? 'bg-gradient-to-br from-teal-700 to-teal-800' : 'bg-gradient-to-br from-blue-900 to-indigo-900'
                        ]"
                    >
                        <!-- Icon -->
                        <div class="absolute top-4 right-4 flex justify-center mb-8 text-gray-200">
                            <i :class="['pi', stat.icon]" style="font-size: 1.75rem; opacity: 0.9"></i>
                        </div>
                        
                        <div class="text-5xl font-bold mb-2 tracking-tight">
                            {{ stat.currentValue }}
                        </div>
                        <div class="text-lg font-medium tracking-wide opacity-90">
                            {{ t(stat.label, stat.defaultLabel) }}
                        </div>
                    </div>
                </div>
            </div>

            <TimelineSejarah />
            <div class="pb-24">
                <Timeline />
            </div>
        </main>
    </GuestLayout>

</template>

<style scoped>
/* Add subtle entrance animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>