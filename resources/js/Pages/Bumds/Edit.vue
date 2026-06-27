<script setup>
import Form from './Form.vue';

const props = defineProps({
    bumd: { type: Object, required: true },
});
</script>

<template>
    <Form
        form-title="Edit BUMD"
        :submit-url="route('bumds.update', props.bumd.kode)"
        method="put"
        :bumd="props.bumd"
    />
</template>