<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [Number, String],
        required: true
    },
    description: {
        type: String,
        required: true
    },
    icon: {
        type: String,
        default: null
    },
    titleWeight: {
        type: String,
        default: 'font-bold'
    },
    valueSize: {
        type: String,
        default: 'text-4xl'
    }
});
</script>

<template>
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="flex flex-row justify-between items-start gap-8 p-6">
            <div>
                <div class="text-sm text-gray-900" :class="titleWeight">{{ title }}</div>
                <div class="mt-2 font-bold text-gray-900 mb-1" :class="valueSize">{{ value }}</div>
                <p class="text-sm font-medium text-gray-500">{{ description }}</p>
            </div>
            <div v-if="icon">
                <span :class="`pi ${icon} text-gray-600`" style="font-size: 1.5rem;"></span>
            </div>
        </div>
    </div>
</template>