<script setup>
const props = defineProps({
  title: { type: String, required: true },
  icon: { type: String, default: null },
  statuses: { type: Object, default: () => ({}) },
  titleWeight: { type: String, default: 'font-bold' },
});

const STATUS_ITEMS = [
  { key: 'pending', label: 'Menunggu', classes: 'bg-yellow-100 text-yellow-800' },
  { key: 'processed', label: 'Diproses', classes: 'bg-blue-100 text-blue-800' },
  { key: 'completed', label: 'Selesai', classes: 'bg-green-100 text-green-800' },
  { key: 'rejected', label: 'Ditolak', classes: 'bg-red-100 text-red-800' },
];
</script>

<template>
  <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg h-full">
    <div class="p-6 h-full flex flex-col">
      <div class="flex flex-row justify-between items-center mb-6">
        <div class="text-lg text-gray-800" :class="titleWeight">{{ title }}</div>
        <div v-if="icon" class="p-2 bg-gray-50 rounded-full">
          <span :class="`pi ${icon} text-gray-500`" style="font-size: 1.2rem;"></span>
        </div>
      </div>

      <div class="grid grid-cols-4 gap-4 flex-1">
        <div v-for="item in STATUS_ITEMS" :key="item.key" class="flex flex-col">
          <div class="text-xs font-semibold text-gray-500 mb-2 text-center uppercase tracking-wider">{{ item.label }}</div>
          <div class="flex-1 rounded-lg flex items-center justify-center p-4 transition-transform hover:scale-105"
            :class="item.classes">
            <span class="text-2xl font-bold">{{ statuses?.[item.key] ?? 0 }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>