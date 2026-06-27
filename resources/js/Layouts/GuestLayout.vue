<script setup>
import ScrollTop from 'primevue/scrolltop';

import Hearder from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';

</script>

<style scoped>
.p-button {
    background-color: #1f2937;
    border: none !important;
}

.p-button:hover,
.p-button:focus,
.p-button:not(:disabled):hover
.p-button:not(:disabled):focus {
    background-color: #1C5EBD !important;
    border: none !important;
}
</style>

<template>
    <Hearder />
    <main class="flex-1">
        <slot />
    </main>
    <Footer />
    <ScrollTop  />
</template>
