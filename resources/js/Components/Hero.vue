<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
    breadcrumb: {
        type: Array,
        default: () => [],
    },
    breadcrumbHome: {
        type: Object,
        default: () => ({ icon: 'pi pi-home', url: '/' }),
    },
    title: {
        type: String,
        default: 'Hello',
    },
    description: {
        type: String,
        default:
            '-',
    },
    backgroundImage: {
        type: String,
        default: '',
    },
});

// Array of available hero images
const heroImages = [
    '/images/hero/monas.jpg',
    '/images/hero/mrt.jpg',
    '/images/hero/jis.jpg',
    '/images/hero/bundaran-hi.jpg'
];

// Select a random hero image
const getRandomHeroImage = () => {
    const randomIndex = Math.floor(Math.random() * heroImages.length);
    return heroImages[randomIndex];
};

// Use provided background image or random one
const selectedBackgroundImage = props.backgroundImage || getRandomHeroImage();

const scrollY = ref(0);

const handleScroll = () => {
    scrollY.value = window.scrollY;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

// Calculate parallax transform
const parallaxTransform = computed(() => {
    const parallaxSpeed = 0.5; // Adjust this value to control parallax intensity
    const yPos = scrollY.value * parallaxSpeed;
    return `translateY(${yPos}px)`;
});

// Background style with parallax effect
const backgroundStyle = computed(() => ({
    backgroundImage: `url('${selectedBackgroundImage}')`,
    transform: parallaxTransform.value,
    transformOrigin: 'center center',
}));
</script>

<style scoped>
.parallax-bg {
    position: absolute;
    top: -5%;
    left: 0;
    width: 100%;
    height: 120%;
    background-size: cover;
    background-position: 50% 40%;
    background-repeat: no-repeat;
    will-change: transform;
}

/* Custom scrollbar for breadcrumb */
nav::-webkit-scrollbar {
    height: 4px;
}

nav::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
}

nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
}

nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>

<template>
    <section>
        <div class="w-full">
            <div class="relative overflow-hidden min-h-[320px] flex items-center">
                <!-- Parallax Background -->
                <div class="parallax-bg" :style="backgroundStyle">
                </div>
                <!-- Overlay -->
                 <div class="absolute inset-0 bg-black/60 z-20"></div>
                <!-- <div class="absolute inset-0 bg-gradient-to-br from-primary/90 to-secondary/40 z-10"></div> -->
                <!-- Content -->
                <div
                    class="flex flex-col items-center justify-center relative z-20 w-full text-center px-6 lg:px-10 max-w-5xl mx-auto -mt-4">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-white pt-4">{{ props.title }}</h1>
                    <p class="mt-4 text-lg leading-8 text-gray-200 tracking-wide">{{ props.description }}</p>

                    <!-- Custom Breadcrumb -->
                    <nav v-if="props.breadcrumb?.length" class="w-full mt-6">
                        <div
                            class="flex items-center flex-wrap gap-2 text-sm md:text-base text-gray-300 dark:text-gray-400 justify-center">
                            <!-- Home Icon -->
                            <a v-if="props.breadcrumbHome" :href="props.breadcrumbHome.url"
                                class="hover:text-gray-300 transition-colors flex items-center gap-1">
                                <span class="inline">Beranda</span>
                            </a>

                            <!-- Breadcrumb Items -->
                            <template v-for="(item, idx) in props.breadcrumb" :key="idx">
                                <i class="pi pi-angle-right text-gray-400 text-xs"></i>
                                <a v-if="item.url" :href="item.url" class="hover:text-gray-300 transition-colors">
                                    {{ item.label }}
                                </a>
                                <span v-else class="text-gray-300">{{ item.label }}</span>
                            </template>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </section>
</template>