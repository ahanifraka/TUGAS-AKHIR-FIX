<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// props: roles array from backend
const props = defineProps({
  roles: { type: Array, default: () => [] },
});

// Form data
const form = useForm({
  name: '',
  full_name: '',
  email: '',
  role: props.roles?.[0] ?? '',
  password: '',
  password_confirmation: '',
});

// Submit form
const submit = () => {
  form.post(route('users.store'), {
    onSuccess: () => {
      Swal.fire({
        title: 'Berhasil',
        text: 'User berhasil ditambahkan.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
      });
    },
  });
};

</script>

<template>

  <Head title="Tambah User" />

  <AuthenticatedLayout>

    <template #header>
      <h1 class="text-xl font-semibold leading-tight text-gray-800">Tambah User</h1>
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
                <TextInput id="full_name" type="text" class="mt-1 block w-full" v-model="form.full_name" required />
                <InputError class="mt-2" :message="form.errors.full_name" />
              </div>

              <div class="mb-4">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                <InputError class="mt-2" :message="form.errors.email" />
              </div>

              <div class="mb-4">
                <InputLabel for="role" value="Role" />
                <select id="role" v-model="form.role" required class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
                  <option v-for="r in props.roles" :key="r" :value="r">{{ r }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.role" />
              </div>

              <div class="mb-4">
                <InputLabel for="password" value="Password" />
                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
                <InputError class="mt-2" :message="form.errors.password" />
              </div>

              <div class="mb-6">
                <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                  v-model="form.password_confirmation" required />
              </div>

              <div class="flex items-center justify-between">
                <Link :href="route('users.index')" class="text-gray-600 hover:text-gray-900">Kembali</Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan
                </PrimaryButton>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>