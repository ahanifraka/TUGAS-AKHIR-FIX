<script setup>
import Form from './Form.vue';
</script>

<template>
    <Form
        form-title="Tambah Kategori Berita"
        :submit-url="route('berita-categories.store')"
        method="post"
    />
</template>