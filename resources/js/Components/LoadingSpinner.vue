<template>
  <div v-if="show" class="loading-overlay">
    <div class="loading-container">
      <div class="loading-spinner">
        <img src="/logo.svg" alt="Loading..." class="spinning-logo" />
      </div>
      <p class="loading-text">Loading… {{ Math.round(progress) }}%</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

defineProps({
  show: {
    type: Boolean,
    default: true
  }
});

// Simulated progress that smoothly increments until navigation completes
const progress = ref(0);
let timer = null;

onMounted(() => {
  progress.value = 0;
  // Increment towards 95% with easing; final jump to 100% happens when overlay is removed
  timer = setInterval(() => {
    if (progress.value < 95) {
      const remaining = 95 - progress.value;
      const step = Math.max(1, Math.round(remaining * 0.08));
      progress.value = Math.min(95, progress.value + step);
    }
  }, 120);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
  timer = null;
});
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.9);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.loading-spinner {
  width: 80px;
  height: 80px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.spinning-logo {
  width: 100%;
  height: 100%;
  animation: spin 2s linear infinite;
}

.loading-text {
  font-size: 1.125rem;
  font-weight: 500;
  color: #374151;
  margin: 0;
}

.progress-wrapper {
  position: relative;
  width: 220px;
  height: 8px;
  border-radius: 9999px;
  background-color: #e5e7eb; /* gray-200 */
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background-color: #1C5EBD; /* primary color used in app */
  transition: width 0.15s ease;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>