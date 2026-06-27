<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    imageSrc: {
        type: String,
        required: true
    },
    imageAlt: {
        type: String,
        default: 'Isometric view'
    },
    infoDots: {
        type: Array,
        default: () => []
    }
});

const activeTooltip = ref(null);
const activeThumbTooltip = ref(null);

const showTooltip = (index) => {
    activeTooltip.value = index;
};

const hideTooltip = () => {
    activeTooltip.value = null;
};

const toggleThumbTooltip = (event, thumbIndex) => {
    event.preventDefault();
    if (activeThumbTooltip.value === thumbIndex) {
        activeThumbTooltip.value = null;
    } else {
        activeThumbTooltip.value = thumbIndex;
    }
};

const hideThumbTooltip = () => {
    activeThumbTooltip.value = null;
};

const handleThumbClick = (event, thumbIndex, link) => {
    // If tooltip is not active, show it and prevent navigation
    if (activeThumbTooltip.value !== thumbIndex) {
        event.preventDefault();
        activeThumbTooltip.value = thumbIndex;
    }
    // If tooltip is already active, allow navigation (Link component will handle it)
};
</script>

<template>
    <div class="isometric-container">
        <div class="image-wrapper">
            <img 
                :src="imageSrc" 
                alt="ecosystem"
                class="isometric-image"
                title="ecosystem"
            />
            
            <!-- Info dots -->
            <div 
                v-for="(dot, index) in infoDots" 
                :key="index"
                class="info-dot"
                :class="{ active: activeTooltip === index }"
                :style="{ left: dot.x + '%', top: dot.y + '%' }"
                @mouseenter="showTooltip(index)"
                @mouseleave="hideTooltip"
            >
                <div class="dot-wrapper">
                    <div class="dot"></div>
                    <div class="ripple"></div>
                    <div class="ripple ripple-delay-1"></div>
                    <div class="ripple ripple-delay-2"></div>
                </div>
                <div 
                    v-if="activeTooltip === index"
                    class="tooltip"
                >
                    <div class="tooltip-content">
                        <img 
                            v-if="dot.icon" 
                            :src="dot.icon" 
                            :alt="dot.title"
                            class="tooltip-icon"
                        />
                        <h4>{{ dot.title }}</h4>
                        <p class="pb-2">{{ dot.description }}</p>

                        <!-- Optional clickable thumbnails (multiple) -->
                        <div v-if="dot.thumbnails && dot.thumbnails.length" class="tooltip-footer">
                            <div class="tooltip-thumbs">
                                <Link
                                    v-for="(thumb, tIndex) in dot.thumbnails"
                                    :key="tIndex"
                                    class="tooltip-thumb-link"
                                    :href="thumb.link || thumb.src"
                                    :aria-label="`Buka gambar terkait ${dot.title} ${tIndex + 1}`"
                                    :title="thumb.alt"
                                    @click="handleThumbClick($event, `${index}-${tIndex}`, thumb.link)"
                                    @mouseenter="activeThumbTooltip = `${index}-${tIndex}`"
                                    @mouseleave="hideThumbTooltip"
                                >
                                    <img
                                        :src="thumb.src"
                                        :alt="thumb.alt || (dot.title + ' thumbnail ' + (tIndex + 1))"
                                        class="tooltip-thumb"
                                    />
                                    <span 
                                        class="tooltip-thumb-name"
                                        :class="{ 'active': activeThumbTooltip === `${index}-${tIndex}` }"
                                    >{{ thumb.alt }}</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Backward-compatible single thumbnail -->
                        <div v-else-if="dot.thumbnail" class="tooltip-footer">
                            <Link
                                class="tooltip-thumb-link"
                                :href="dot.thumbLink || dot.link || dot.thumbnail"
                                :aria-label="`Buka gambar terkait ${dot.title}`"
                            >
                                <img
                                    :src="dot.thumbnail"
                                    :alt="dot.title + ' thumbnail'"
                                    class="tooltip-thumb"
                                />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.isometric-container {
    position: relative;
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
}

.image-wrapper {
    position: relative;
    display: inline-block;
    width: 100%;
}

.isometric-image {
    width: 100%;
    height: auto;
    display: block;
}

.info-dot {
    position: absolute;
    cursor: pointer;
    z-index: 1;
}

.info-dot.active {
    z-index: 1000; /* Ensure active tooltip stacks above other dots */
}

.dot-wrapper {
    position: relative;
    width: 20px;
    height: 20px;
}

.dot {
    position: relative;
    width: 20px;
    height: 20px;
    background: rgba(0, 0, 0, 1);
    border: 3px solid #fff;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    animation: pulse 2s infinite;
    z-index: 2;
}

.dot:hover {
    background: #0056b3;
    transform: scale(1.1);
}

.ripple {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    border: 2px solid rgba(0, 0, 0, 0.6);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    animation: ripple 2s ease-out infinite;
    z-index: 1;
}

.ripple-delay-1 {
    animation-delay: 0.6s;
}

.ripple-delay-2 {
    animation-delay: 1.2s;
}

.tooltip {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    padding: 16px;
    min-width: 300px;
    max-width: 400px;
    z-index: 2000;
    animation: fadeIn 0.3s ease-in-out;
}

/* Responsive adjustments for mobile */
@media (max-width: 640px) {
    .tooltip {
        min-width: 280px;
        max-width: 90vw;
        left: 50%;
        right: auto;
        transform: translateX(-50%);
    }
    
    /* Adjust for dots near edges */
    .info-dot[style*="left: 0%"] .tooltip,
    .info-dot[style*="left: 1"] .tooltip,
    .info-dot[style*="left: 2"] .tooltip {
        left: 0;
        transform: translateX(0);
    }
    
    .info-dot[style*="left: 8"] .tooltip,
    .info-dot[style*="left: 9"] .tooltip {
        left: auto;
        right: 0;
        transform: translateX(0);
    }
}

.tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 8px solid transparent;
    border-top-color: #fff;
}

/* Adjust arrow for edge tooltips on mobile */
@media (max-width: 640px) {
    .info-dot[style*="left: 0%"] .tooltip::after,
    .info-dot[style*="left: 1"] .tooltip::after,
    .info-dot[style*="left: 2"] .tooltip::after {
        left: 20px;
        transform: translateX(0);
    }
    
    .info-dot[style*="left: 8"] .tooltip::after,
    .info-dot[style*="left: 9"] .tooltip::after {
        left: auto;
        right: 20px;
        transform: translateX(0);
    }
}

.tooltip-content {
    text-align: center;
}

.tooltip-footer {
    margin-top: 12px;
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
}

.tooltip-thumbs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
}

.tooltip-thumb-link {
    display: inline-block;
    position: relative;
}

.tooltip-thumb {
    width: 72px;
    height: 72px;
    padding: 10px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.tooltip-thumb-name {
    position: absolute;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.85);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    white-space: nowrap;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s ease;
    z-index: 3000;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Prevent tooltip from going off-screen on the right */
.tooltip-thumbs > a:nth-last-child(-n+2) .tooltip-thumb-name {
    left: auto;
    right: 0;
    transform: translateX(0);
}

/* Prevent tooltip from going off-screen on the left */
.tooltip-thumbs > a:nth-child(-n+2) .tooltip-thumb-name {
    left: 0;
    transform: translateX(0);
}

/* For middle items, keep centered */
.tooltip-thumbs > a:not(:nth-child(-n+2)):not(:nth-last-child(-n+2)) .tooltip-thumb-name {
    left: 50%;
    transform: translateX(-50%);
}

/* Show on hover for desktop */
.tooltip-thumb-link:hover .tooltip-thumb-name {
    opacity: 1;
}

/* Show when active (for mobile touch) */
.tooltip-thumb-name.active {
    opacity: 1;
}

/* Touch device support */
@media (hover: none) and (pointer: coarse) {
    .tooltip-thumb-link {
        -webkit-tap-highlight-color: transparent;
    }
}

.tooltip-thumb-link:hover .tooltip-thumb {
    transform: scale(1.05);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.tooltip-icon {
    width: 48px;
    height: 48px;
    object-fit: contain;
    margin-bottom: 8px;
}

.tooltip h4 {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 16px;
    font-weight: 600;
}

.tooltip p {
    margin: 0;
    color: #333;
    font-size: 14px;
    line-height: 1.4;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

@keyframes ripple {
    0% {
        width: 20px;
        height: 20px;
        opacity: 1;
    }
    100% {
        width: 60px;
        height: 60px;
        opacity: 0;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}
</style>