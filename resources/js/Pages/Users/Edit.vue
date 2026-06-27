<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  user: { type: Object, required: true },
});

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  full_name: props.user.full_name,
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.put(route('users.update', props.user.id), {
    onSuccess: () => {
      Swal.fire({
        title: 'Berhasil',
        text: 'User berhasil diubah.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
      });
    },
  });
};
</script>

<template>
  <Head title="Edit User" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit User</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit">
              <div class="mb-4">
                <InputLabel for="name" value="Nama" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>

              <div class="mb-4">
                <InputLabel for="full_name" value="Nama Lengkap" />
                <TextInput id="full_name" type="text" class="mt-1 block w-full" v-model="form.full_name" />
                <InputError class="mt-2" :message="form.errors.full_name" />
              </div>

              <div class="mb-4">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                <InputError class="mt-2" :message="form.errors.email" />
              </div>

              <div class="mb-4">
                <InputLabel for="password" value="Password (opsional)" />
                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" />
                <InputError class="mt-2" :message="form.errors.password" />
              </div>

              <div class="mb-6">
                <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" />
              </div>

              <div class="flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan</PrimaryButton>
                <Link :href="route('users.index')" class="text-gray-600 hover:text-gray-900">Kembali</Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>