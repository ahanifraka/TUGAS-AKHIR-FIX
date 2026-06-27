<script setup>
import Form from './Form.vue';
</script>

<template>
    <Form
        form-title="Tambah BUMD"
        :submit-url="route('bumds.store')"
        method="post"
    />
</template>