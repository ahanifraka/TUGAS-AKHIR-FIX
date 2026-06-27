<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
    turnstile_token: '',
});

const captchaError = ref('');
const siteKey = (typeof window !== 'undefined' && window.APP_CONFIG && window.APP_CONFIG.turnstileSiteKey)
    ? window.APP_CONFIG.turnstileSiteKey
    : (import.meta.env.VITE_TURNSTILE_SITE_KEY || '');

function renderTurnstile() {
    const container = document.getElementById('turnstile-container');
    if (!container) return;

    if (!siteKey) {
        captchaError.value = 'Captcha is not configured. Contact administrator.';
        return;
    }

    if (window.turnstile && typeof window.turnstile.render === 'function') {
        window.turnstile.render('#turnstile-container', {
            sitekey: siteKey,
            callback: (token) => {
                form.turnstile_token = token;
                captchaError.value = '';
            },
            'expired-callback': () => {
                form.turnstile_token = '';
            },
            'error-callback': () => {
                captchaError.value = 'Captcha error, please try again.';
            },
            theme: 'auto',
        });
    }
}

function ensureTurnstileScript() {
    if (window.turnstile) {
        renderTurnstile();
        return;
    }

    const existing = document.getElementById('turnstile-script');
    if (existing) {
        existing.addEventListener('load', renderTurnstile, { once: true });
        return;
    }

    const s = document.createElement('script');
    s.id = 'turnstile-script';
    s.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
    s.async = true;
    s.defer = true;
    s.onload = () => renderTurnstile();
    document.head.appendChild(s);
}

onMounted(() => {
    ensureTurnstileScript();
});

const submit = () => {
    if (!form.turnstile_token) {
        captchaError.value = 'Please complete the captcha.';
        return;
    }
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onError: () => {
            // If backend validation fails, reset captcha token to force re-challenge
            form.turnstile_token = '';
            if (window.turnstile && typeof window.turnstile.reset === 'function') {
                try { window.turnstile.reset('#turnstile-container'); } catch (_) { }
            }
        },
    });
};
</script>

<template>
    <AuthLayout>

        <Head title="Log in" />

        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 md:p-10">
            <div class="text-center mb-8">
                <Link href="/" class="flex items-center gap-2 md:gap-4 mb-4 no-underline justify-center">
                    <img :src="theme === 'dark' ? '/images/logo-bpbumd-dark.png' : '/images/logo-bpbumd.png'"
                        alt="Logo Badan Pembinaan BPBUMD" class="h-12 md:h-16" title="Logo Badan Pembinaan BPBUMD" />
                    <div class="flex flex-col leading-tight"></div>
                </Link>
                
                <h2 class="text-2xl font-bold text-gray-900">Login</h2>
                <p class="mt-2 text-sm text-gray-600">Welcome back, please enter your detail</p>
            </div>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <TextInput id="email" type="email"
                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.email" required autofocus placeholder="Enter Email" autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4 relative">
                    <TextInput id="password" type="password"
                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.password" required placeholder="Password" autocomplete="current-password" />
                    <!-- Hide/Show password toggle could go here -->
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4 pb-4 pt-2 flex items-center justify-between">
                    <div class="text-sm text-gray-600">Having trouble in sign in?</div>
                    <Link v-if="canResetPassword" :href="route('password.request')"
                        class="text-sm font-medium text-gray-900 hover:underline">
                    Reset Password
                    </Link>
                </div>

                <div class="mt-4">
                    <div id="turnstile-container" class="flex justify-center"></div>
                    <InputError class="mt-6 text-center" :message="form.errors.turnstile_token || captchaError" />
                </div>

                <div class="mt-6">
                    <button
                        class="w-full bg-primary hover:bg-primary-hover text-gray-100 font-bold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FCD397]"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing || !form.turnstile_token">
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>
