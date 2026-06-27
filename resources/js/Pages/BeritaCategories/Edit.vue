<script setup>
import Form from './Form.vue';

const props = defineProps({
    category: { type: Object, required: true },
});
</script>

<template>
    <Form
        form-title="Edit Kategori"
        :submit-url="route('berita-categories.update', props.category.id)"
        method="put"
        :category="props.category"
    />
</template>