<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout>

        <Head title="Reset Password" />

        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 md:p-10">
            <div class="text-center mb-8">
                <Link href="/" class="flex items-center gap-2 md:gap-4 mb-4 no-underline justify-center">
                <img :src="theme === 'dark' ? '/images/logo-bpbumd-dark.png' : '/images/logo-bpbumd.png'"
                    alt="Logo Badan Pembinaan BPBUMD" class="h-12 md:h-16" title="Logo Badan Pembinaan BPBUMD" />
                <div class="flex flex-col leading-tight"></div>
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Reset Password</h2>
                <p class="mt-2 text-sm text-gray-600">Enter your new password below</p>
            </div>

            <form @submit.prevent="submit">
                <div>
                    <TextInput id="email" type="email"
                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.email" required autofocus placeholder="Email" autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <TextInput id="password" type="password"
                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.password" required placeholder="New Password" autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <TextInput id="password_confirmation" type="password"
                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.password_confirmation" required placeholder="Confirm New Password"
                        autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="mt-6">
                    <button
                        class="w-full bg-primary hover:bg-primary-hover text-gray-100 font-bold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FCD397]"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }" :disabled="form.processing">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>
