<script setup>
import { Head, Link } from '@inertiajs/vue3';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';

const props = defineProps({
    page: { type: Object, required: true },
});

const pageTitle = props.page.metadata_title || props.page.title;
const pageDescription = props.page.metadata_description || (props.page.subtitle || '');
const pageKeywords = props.page.metadata_keywords || '';
</script>

<template>

    <Head :title="pageTitle">
        <meta name="description" :content="pageDescription" />
        <meta name="keywords" :content="pageKeywords" />
    </Head>

    <GuestLayout>
        <!-- Header -->
        <header class="mb-8">
            <Hero :title="props.page.title" :description="props.page.subtitle" />
        </header>

        <div class="max-w-5xl mx-auto">
            <!-- Image -->
            <div v-if="props.page.image" class="mb-8 overflow-hidden rounded">
                <img :src="props.page.image.startsWith('http') ? props.page.image : '/' + props.page.image"
                    :alt="props.page.title" class="w-full object-cover object-center max-h-[420px]" />
            </div>

            <!-- Content -->
            <article class="prose max-w-none leading-normal">
                <div v-if="props.page.description" v-html="props.page.description"></div>
                <div v-if="props.page.description2" v-html="props.page.description2" class="mt-6"></div>
            </article>
        </div>

    </GuestLayout>
</template>