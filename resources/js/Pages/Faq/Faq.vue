<script setup>
import { Head } from '@inertiajs/vue3';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import Hero from '@/Components/Hero.vue';
import Accordion from '@/Components/Accordion.vue';
import { faqData } from './faqData.js';
import useTranslations from '@/Composables/useTranslations.js';

const { t } = useTranslations();

const breadcrumbItems = [
    { label: t('pages.faq.breadcrumb.profile', 'FAQ'), url: '#' },
    { label: t('pages.faq.breadcrumb.page', 'Frequently Asked Questions'), url: '/faq' },
];

const heroTitle = t('pages.faq.hero.title', 'Frequently Asked Questions');
const heroDescription = t('pages.faq.hero.description', 'Informasi Frequently Asked Questions Pemerintah Provinsi DKI Jakarta');
</script>

<template>
    <GuestLayout>

        <Head :title="t('pages.faq.head.title', 'FAQ')">
            <meta name="description" :content="t('pages.faq.head.description', 'FAQ Pemerintah Provinsi DKI Jakarta')">
            <meta name="keywords" :content="t('pages.faq.head.keywords', 'FAQ, Pemerintah Provinsi DKI Jakarta')">
        </Head>
        <Hero :breadcrumb="breadcrumbItems" :title="heroTitle" :description="heroDescription" />
        <main class="container mx-auto px-12 md:px-4 py-12">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="space-y-1">
                    <Accordion
                        v-for="(item, index) in faqData"
                        :key="index"
                        :title="item.question"
                        :content="item.answer"
                    />
                </div>
            </div>
        </main>
    </GuestLayout>

</template>