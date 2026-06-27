<script setup>
import Form from './Form.vue';

const props = defineProps({
    categories: { type: Array, default: () => [] },
});
</script>

<template>
    <Form
        form-title="Tambah Berita"
        :submit-url="route('beritas.store')"
        method="post"
        :categories="props.categories"
    />
</template>