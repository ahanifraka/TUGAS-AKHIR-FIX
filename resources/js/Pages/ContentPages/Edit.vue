<script setup>
import Form from './Form.vue';

const props = defineProps({
    page: { type: Object, required: true },
});
</script>

<template>
    <Form
        form-title="Edit Content Page"
        :submit-url="route('content-pages.update', props.page.id)"
        method="put"
        :page="props.page"
    />
</template>