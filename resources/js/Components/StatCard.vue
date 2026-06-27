<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    valueText: {
        type: [String, String],
    },
    change: {
        type: String,
        default: ''
    },
    changeType: {
        type: String,
        default: 'neutral', // 'positive', 'negative', 'neutral'
    },
    changeText: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: ''
    },
    iconColor: {
        type: String,
        default: 'green' // 'green', 'blue', 'red', etc.
    }
});

const getIconBgColor = (color) => {
    const colors = {
        green: 'bg-green-100 text-green-600',
        blue: 'bg-blue-100 text-blue-600',
        red: 'bg-red-100 text-red-600',
        yellow: 'bg-yellow-100 text-yellow-600',
        purple: 'bg-purple-100 text-purple-600',
        teal: 'bg-teal-100 text-teal-600',
    };
    return colors[color] || colors.green;
};

const getChangeColor = (type) => {
    if (type === 'positive') return 'text-green-500';
    if (type === 'negative') return 'text-red-500';
    return 'text-gray-500';
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 flex justify-between items-start w-full">
        <div>
            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">
                {{ title }}
            </h3>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                {{ value }}<span class="text-lg font-medium text-gray-600">{{ valueText }}</span>
            </div>
            <div v-if="change || changeText" class="flex items-center mt-2 text-sm font-medium">
                <span :class="getChangeColor(changeType)" class="flex items-center">
                    <i v-if="changeType === 'positive'" class="pi pi-arrow-up-right text-xs mr-1"></i>
                    <i v-if="changeType === 'negative'" class="pi pi-arrow-down text-right-xs mr-1"></i>
                    {{ change }}
                </span>
                <span class="text-gray-400 ml-2 text-xs">
                    {{ changeText }}
                </span>
            </div>
        </div>
        <div v-if="icon" :class="['p-3 h-12 w-12 rounded-full flex items-center justify-center', getIconBgColor(iconColor)]">
            <i :class="['pi', icon, 'text-xl']"></i>
        </div>
    </div>
</template>
