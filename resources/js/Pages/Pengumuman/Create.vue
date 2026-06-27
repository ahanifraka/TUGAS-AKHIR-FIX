<script setup>
import Form from "./Form.vue";

const props = defineProps({
    tipeOptions: { type: Array, default: () => [] },
});
</script>

<template>
    <Form
        form-title="Tambah Pengumuman"
        :submit-url="route('admin.pengumuman.store')"
        method="post"
        :tipe-options="props.tipeOptions"
    />
</template>
