<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Register" />

        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 md:p-10">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Sign up</h2>
                <p class="mt-2 text-sm text-gray-600">Create your account to get started</p>
            </div>

            <form @submit.prevent="submit">
                <div>
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.name"
                        required
                        autofocus
                        placeholder="Name"
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.email"
                        required
                        placeholder="Email"
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.password"
                        required
                        placeholder="Password"
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.password_confirmation"
                        required
                        placeholder="Confirm Password"
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="mt-6">
                    <button
                        class="w-full bg-[#FCD397] hover:bg-[#fcc570] text-gray-900 font-bold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FCD397]"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Register
                    </button>
                </div>

                <div class="mt-6 text-center text-sm">
                    <span class="text-gray-600">Already registered? </span>
                    <Link :href="route('login')" class="font-bold text-gray-900 hover:underline">
                        Log in
                    </Link>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>
