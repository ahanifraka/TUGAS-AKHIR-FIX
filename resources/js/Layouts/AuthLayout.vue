<script setup>
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <div class="min-h-screen relative overflow-hidden font-sans text-gray-900 bg-[image:url('/images/hero/jakarta.jpg')] bg-cover bg-center
    selection:bg-[#FCD397] selection:text-gray-900">
        <div class="absolute inset-0 bg-gray-900/60"></div>

        <!-- Main Content -->
        <main class="relative z-10 flex flex-col items-center justify-center min-h-[calc(100vh-160px)] px-4 py-8 pt-24">
             
            <slot />
        </main>

        <!-- Footer -->
        <footer class="relative z-10 py-6 text-center text-xs text-gray-100">
            <p>© {{ new Date().getFullYear() }} - BPBUMD PROVINSI DKI JAKARTA</p>
        </footer>
    </div>
</template>
