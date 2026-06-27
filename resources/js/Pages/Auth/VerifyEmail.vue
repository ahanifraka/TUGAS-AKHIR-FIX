<script setup>
import { computed } from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <AuthLayout>
        <Head title="Email Verification" />

        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 md:p-10">
            <div class="text-center mb-8">
                <Link href="/" class="flex items-center gap-2 md:gap-4 mb-4 no-underline justify-center">
                    <img :src="theme === 'dark' ? '/images/logo-bpbumd-dark.png' : '/images/logo-bpbumd.png'"
                        alt="Logo Badan Pembinaan BPBUMD" class="h-12 md:h-16" title="Logo Badan Pembinaan BPBUMD" />
                    <div class="flex flex-col leading-tight"></div>
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Verify Email</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
                </p>
            </div>

            <div
                class="mb-4 text-sm font-medium text-green-600 text-center"
                v-if="verificationLinkSent"
            >
                A new verification link has been sent to the email address you provided during registration.
            </div>

            <form @submit.prevent="submit">
                <div class="mt-6 flex flex-col gap-4">
                    <button
                        class="w-full bg-primary hover:bg-primary-hover text-gray-100 font-bold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FCD397]"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Resend Verification Email
                    </button>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-sm text-gray-600 underline hover:text-gray-900 text-center"
                    >
                        Log Out
                    </Link>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>
