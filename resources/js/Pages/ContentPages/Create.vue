<script setup>
import Form from './Form.vue';
</script>

<template>
    <Form
        form-title="Tambah Content Page"
        :submit-url="route('content-pages.store')"
        method="post"
    />
</template>