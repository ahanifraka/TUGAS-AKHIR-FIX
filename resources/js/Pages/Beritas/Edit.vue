<script setup>
import Form from './Form.vue';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    berita: { type: Object, required: true },
});
</script>

<template>
    <Form
        form-title="Edit Berita"
        :submit-url="route('beritas.update', props.berita.id)"
        method="put"
        :categories="props.categories"
        :berita="props.berita"
    />
</template>