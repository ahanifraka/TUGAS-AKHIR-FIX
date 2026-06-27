<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Confirm Password" />

        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 md:p-10">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Confirm Password</h2>
                <p class="mt-2 text-sm text-gray-600">
                    This is a secure area of the application. Please confirm your password before continuing.
                </p>
            </div>

            <form @submit.prevent="submit">
                <div>
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.password"
                        required
                        placeholder="Password"
                        autocomplete="current-password"
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-6">
                    <button
                        class="w-full bg-[#FCD397] hover:bg-[#fcc570] text-gray-900 font-bold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FCD397]"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>
