<script setup>
import HeaderAuthenticated from '@/Components/HeaderAuthenticated.vue';
import Sidebar from '@/Components/Sidebar.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar (full height) -->
        <Sidebar />

        <!-- Content column (header beside sidebar) -->
        <div class="flex-1 min-h-screen flex flex-col">
            <HeaderAuthenticated />

            <!-- Page Heading inside content column -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto px-4 py-6 sm:px-6 lg:px-16">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
