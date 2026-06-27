<script setup>
import Form from "./Form.vue";

const props = defineProps({
    tipeOptions: { type: Array, default: () => [] },
    pengumuman: { type: Object, required: true },
});
</script>

<template>
    <Form
        form-title="Edit Pengumuman"
        :submit-url="route('admin.pengumuman.update', props.pengumuman.id)"
        method="put"
        :tipe-options="props.tipeOptions"
        :pengumuman="props.pengumuman"
    />
</template>
