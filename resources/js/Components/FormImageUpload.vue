<script setup>
import { ref, watch, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: [String, File, null],
        default: null,
    },
    previewUrl: {
        type: String,
        default: "",
    },
    error: {
        type: String,
        default: "",
    },
    label: {
        type: String,
        default: "Gambar",
    },
    maxSize: {
        type: Number,
        default: 5, // MB
    },
    acceptedFormats: {
        type: String,
        default: "PNG, JPG, JPEG",
    },
});

const emit = defineEmits([
    "update:modelValue",
    "update:previewUrl",
    "update:uploadMode",
]);

const uploadMode = ref("file"); // 'file' | 'url'
const imageUrl = ref("");
const localPreviewUrl = ref("");
const isDragging = ref(false);
const fileInput = ref(null);

// Initialize from props
watch(
    () => props.modelValue,
    (newValue) => {
        if (
            newValue &&
            typeof newValue === "string" &&
            !localPreviewUrl.value
        ) {
            // hanya untuk load awal
            uploadMode.value = "url";
            imageUrl.value = newValue;
            localPreviewUrl.value = newValue;
        }
    }
);

watch(
    () => props.previewUrl,
    (newValue) => {
        localPreviewUrl.value = newValue;
    },
    { immediate: true }
);

const setUploadMode = (mode) => {
    uploadMode.value = mode;
    emit("update:uploadMode", mode);

    // Clear values when switching modes
    if (mode === "file") {
        imageUrl.value = "";
        emit("update:modelValue", null);
    } else {
        emit("update:modelValue", imageUrl.value);
    }
};

const handleFileChange = (e) => {
    const file = e.target.files?.[0] ?? null;
    if (!file) return;

    uploadMode.value = "file";
    emit("update:uploadMode", "file");

    emit("update:modelValue", file);

    const preview = URL.createObjectURL(file);
    localPreviewUrl.value = preview;
    emit("update:previewUrl", preview);
};

const handleDrop = (e) => {
    e.preventDefault();
    isDragging.value = false;

    const file = e.dataTransfer?.files?.[0] ?? null;
    if (!file) return;

    const dt = new DataTransfer();
    dt.items.add(file);
    fileInput.value.files = dt.files;

    emit("update:modelValue", file);

    const preview = URL.createObjectURL(file);
    localPreviewUrl.value = preview;
    emit("update:previewUrl", preview);

    uploadMode.value = "file";
    emit("update:uploadMode", "file");
};

const handleDragOver = (e) => {
    e.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = (e) => {
    e.preventDefault();
    isDragging.value = false;
};

const handleUrlInput = (val) => {
    imageUrl.value = val;
    emit("update:modelValue", val);
    localPreviewUrl.value = val || "";
    emit("update:previewUrl", val || "");
};

const handlePaste = (e) => {
    const items = e.clipboardData?.items || [];
    for (const item of items) {
        if (item.kind === "file") {
            const file = item.getAsFile();
            if (!file || !file.type.startsWith("image/")) continue;

            // 🔥 Sinkronkan ke input HTML
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.value.files = dt.files;

            emit("update:modelValue", file);

            const preview = URL.createObjectURL(file);
            localPreviewUrl.value = preview;
            emit("update:previewUrl", preview);

            uploadMode.value = "file";
            emit("update:uploadMode", "file");

            e.preventDefault();
            break;
        }
    }
};

const clearImage = () => {
    imageUrl.value = "";
    emit("update:modelValue", uploadMode.value === "file" ? null : "");
    localPreviewUrl.value = "";
    emit("update:previewUrl", "");
    if (fileInput.value) {
        fileInput.value.value = "";
    }
};

const openFileDialog = () => {
    fileInput.value?.click();
};

const modeButtonClass = (mode) => [
    "rounded px-3 py-1 text-sm font-medium transition-all duration-200",
    uploadMode.value === mode
        ? "bg-secondary text-white shadow-sm"
        : "bg-gray-200 text-gray-700 hover:bg-gray-300",
];

const dropZoneClass = computed(() => [
    "flex flex-col items-center justify-center rounded-lg border-2 border-dashed px-6 py-12 text-center cursor-pointer transition-all duration-300 ease-in-out",
    isDragging.value
        ? "border-primary bg-primary/5 scale-105 shadow-lg"
        : "border-gray-300 hover:border-primary hover:bg-gray-50 hover:shadow-md",
]);

const iconClass = computed(() => [
    "mx-auto mb-4 flex h-32 w-32 items-center justify-center rounded-full transition-all duration-300",
    isDragging.value
        ? "bg-primary text-white scale-110"
        : "bg-gray-100 text-gray-400 hover:bg-primary hover:text-white",
]);

const titleClass = computed(() => [
    "text-lg font-semibold transition-colors duration-300",
    isDragging.value ? "text-primary" : "text-gray-700",
]);
</script>

<template>
    <div class="form-group">
        <label class="block text-sm font-bold text-gray-700"
            >{{ label }}:</label
        >

        <div class="mt-2 flex gap-2">
            <button
                type="button"
                @click="setUploadMode('file')"
                :class="modeButtonClass('file')"
            >
                Upload File
            </button>
            <button
                type="button"
                @click="setUploadMode('url')"
                :class="modeButtonClass('url')"
            >
                URL
            </button>
        </div>

        <!-- File Upload Mode -->
        <div v-if="uploadMode === 'file'" class="mt-2">
            <div
                @dragover="handleDragOver"
                @dragleave="handleDragLeave"
                @drop="handleDrop"
                @click="openFileDialog"
                :class="dropZoneClass"
            >
                <!-- Upload Icon -->
                <div :class="iconClass">
                    <i class="pi pi-cloud-upload" style="font-size: 3rem"></i>
                </div>

                <!-- Main Text -->
                <div class="space-y-2">
                    <p :class="titleClass">
                        {{
                            isDragging
                                ? "Lepaskan gambar di sini"
                                : "Upload Gambar"
                        }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Seret & letakkan gambar di sini atau
                        <span
                            class="font-medium text-primary hover:text-primary-hover"
                            >klik untuk memilih</span
                        >
                    </p>
                    <p
                        class="text-xs text-gray-400 flex items-center justify-center gap-2"
                    >
                        <i class="pi pi-info-circle text-2xl"></i>
                        Maks {{ maxSize }}MB • {{ acceptedFormats }} • Akan
                        dikonversi ke WebP
                    </p>
                </div>

                <input
                    ref="fileInput"
                    type="file"
                    name="gambar"
                    accept="image/*"
                    @change="handleFileChange"
                    class="hidden"
                />
            </div>

            <!-- Preview -->
            <div v-if="localPreviewUrl" class="mt-4">
                <div class="relative group">
                    <img
                        :src="localPreviewUrl"
                        alt="Preview"
                        class="w-full aspect-video object-cover rounded-lg border-2 border-gray-200 shadow-sm transition-all duration-300 group-hover:shadow-md"
                    />
                    <div
                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded-lg transition-all duration-300 flex items-center justify-center"
                    >
                        <div
                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        >
                            <svg
                                class="h-8 w-8 text-white drop-shadow-lg"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clear Button -->
            <div class="mt-6 flex gap-3">
                <button
                    type="button"
                    @click="clearImage"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-red-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        ></path>
                    </svg>
                    Hapus Gambar
                </button>
            </div>
        </div>

        <!-- URL Mode -->
        <div v-else class="mt-2">
            <input
                :value="imageUrl"
                @input="handleUrlInput($event.target.value)"
                @paste="handlePaste"
                type="text"
                placeholder="https://... atau /path/ke/gambar.jpg"
                class="mt-1 w-full rounded border border-gray-400 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition-colors duration-200"
            />
            <div v-if="localPreviewUrl" class="mt-2">
                <img
                    :src="localPreviewUrl"
                    alt="Preview"
                    class="w-full aspect-video object-cover rounded border"
                />
            </div>
        </div>

        <!-- Error Message -->
        <div
            v-if="error"
            class="mt-1 text-sm text-red-600 flex items-start gap-1"
        >
            <svg
                class="w-4 h-4 mt-0.5 flex-shrink-0"
                fill="currentColor"
                viewBox="0 0 20 20"
            >
                <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                />
            </svg>
            <span>{{ error }}</span>
        </div>
    </div>
</template>
