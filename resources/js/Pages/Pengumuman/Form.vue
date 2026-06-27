<script setup>
import { reactive, watch, ref, onMounted, computed } from "vue";
import { QuillEditor } from "@vueup/vue-quill";
import { Head, router, usePage } from "@inertiajs/vue3";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import useTranslations from "@/Composables/useTranslations.js";
import { useSweetAlert } from "@/Composables/useSweetAlert.js";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import FormImageUpload from "@/Components/FormImageUpload.vue";

import "@vueup/vue-quill/dist/vue-quill.snow.css";

const isSubmitting = ref(false);
const { t } = useTranslations();
const { success, error: showError } = useSweetAlert();
const page = usePage();

// Get errors from Inertia
const errors = computed(() => page.props.errors || {});

const props = defineProps({
    formTitle: { type: String, default: "Form Pengumuman" },
    submitUrl: { type: String, required: true },
    method: { type: String, default: "post" },
    tipeOptions: { type: Array, default: () => [] },
    pengumuman: { type: Object, default: null },
});

// Reactive form state sesuai model Pengumuman
const form = reactive({
    judul: props.pengumuman?.judul ?? "",
    slug: props.pengumuman?.slug ?? "",
    konten: props.pengumuman?.konten ?? "",
    excerpt: props.pengumuman?.excerpt ?? "",
    gambar: null,
    _method: "PUT",
    dokumen: props.pengumuman?.dokumen ?? "",
    nomor_pengumuman: props.pengumuman?.nomor_pengumuman ?? "",
    tanggal_terbit: props.pengumuman?.tanggal_terbit
        ? new Date(props.pengumuman.tanggal_terbit).toISOString().split("T")[0]
        : new Date().toISOString().split("T")[0],
    tanggal_berakhir: props.pengumuman?.tanggal_berakhir
        ? new Date(props.pengumuman.tanggal_berakhir)
              .toISOString()
              .split("T")[0]
        : "",
    is_penting: props.pengumuman?.is_penting ? 1 : 0,
    is_aktif:
        props.pengumuman?.is_aktif !== undefined
            ? props.pengumuman.is_aktif
                ? 1
                : 0
            : 1,
    tipe: props.pengumuman?.tipe ?? "pengumuman",
});

const slugManual = ref(false);
const isPreviewMode = ref(false);
const quillEditor = ref(null);
const isSourceView = ref(false);
const previewUrl = ref("");
const documentFile = ref(null);
const documentUrl = ref(
    props.pengumuman.dokumen ? props.pengumuman.dokumen : ""
);

// Custom image handler for QuillEditor
const imageHandler = function () {
    const input = document.createElement("input");
    input.setAttribute("type", "file");
    input.setAttribute("accept", "image/*");
    input.click();

    input.onchange = () => {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const quill = this.quill;
                const range = quill.getSelection(true);
                quill.insertEmbed(range.index, "image", e.target.result);
                quill.setSelection(range.index + 1);
            };
            reader.readAsDataURL(file);
        }
    };
};

// Custom video handler for QuillEditor
const videoHandler = function () {
    const url = prompt("Masukkan URL video (YouTube, Vimeo, dll):");
    if (url) {
        const quill = this.quill;
        const range = quill.getSelection(true);

        // Convert YouTube/Vimeo URLs to embed format
        let embedUrl = url;

        // YouTube URL conversion
        if (url.includes("youtube.com/watch?v=")) {
            const videoId = url.split("v=")[1].split("&")[0];
            embedUrl = `https://www.youtube.com/embed/${videoId}`;
        } else if (url.includes("youtu.be/")) {
            const videoId = url.split("youtu.be/")[1].split("?")[0];
            embedUrl = `https://www.youtube.com/embed/${videoId}`;
        }
        // Vimeo URL conversion
        else if (url.includes("vimeo.com/")) {
            const videoId = url.split("vimeo.com/")[1].split("?")[0];
            embedUrl = `https://player.vimeo.com/video/${videoId}`;
        }

        quill.insertEmbed(range.index, "video", embedUrl);
        quill.setSelection(range.index + 1);
    }
};

// Enhanced QuillEditor options with comprehensive toolbar
const quillOptions = {
    theme: "snow",
    modules: {
        toolbar: {
            container: [
                // Font and size options
                [{ font: [] }],
                [{ size: ["small", false, "large", "huge"] }],

                // Text formatting
                ["bold", "italic", "underline", "strike"],

                // Colors
                [{ color: [] }, { background: [] }],

                // Headers
                [{ header: 1 }, { header: 2 }],
                [{ header: [1, 2, 3, 4, 5, 6, false] }],

                // Lists and indentation
                [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
                [{ indent: "-1" }, { indent: "+1" }],

                // Text alignment and direction
                [{ align: [] }],
                [{ direction: "rtl" }],

                // Scripts
                [{ script: "sub" }, { script: "super" }],

                // Blocks
                ["blockquote", "code-block"],

                // Media and links
                ["link", "image", "video", "formula"],

                // Clean formatting
                ["clean"],
            ],
            handlers: {
                image: imageHandler,
                video: videoHandler,
            },
        },
    },
    placeholder: "Tulis konten pengumuman di sini...",
    formats: [
        "header",
        "font",
        "size",
        "bold",
        "italic",
        "underline",
        "strike",
        "color",
        "background",
        "script",
        "super",
        "sub",
        "blockquote",
        "code-block",
        "list",
        "bullet",
        "check",
        "indent",
        "align",
        "direction",
        "link",
        "image",
        "video",
        "formula",
        "clean",
    ],
};

// Computed property for formatted date
const formattedDate = computed(() => {
    if (!form.tanggal_terbit) return "";
    return new Date(form.tanggal_terbit).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

// Watch for pengumuman prop changes to update form
watch(
    () => props.pengumuman,
    (val) => {
        Object.assign(form, {
            judul: val?.judul ?? "",
            slug: val?.slug ?? "",
            konten: val?.konten ?? "",
            excerpt: val?.excerpt ?? "",
            dokumen: val?.dokumen ?? "",
            nomor_pengumuman: val?.nomor_pengumuman ?? "",
            tanggal_terbit: val?.tanggal_terbit
                ? new Date(val.tanggal_terbit).toISOString().split("T")[0]
                : new Date().toISOString().split("T")[0],
            tanggal_berakhir: val?.tanggal_berakhir
                ? new Date(val.tanggal_berakhir).toISOString().split("T")[0]
                : "",
            is_penting: val?.is_penting ? 1 : 0,
            is_aktif: val?.is_aktif !== undefined ? (val.is_aktif ? 1 : 0) : 1,
            tipe: val?.tipe ?? "pengumuman",
        });

        // Set document URL untuk preview
        if (val?.dokumen) {
            documentUrl.value = val.dokumen.startsWith("http")
                ? val.dokumen
                : `/storage/${val.dokumen}`;
        } else {
            documentUrl.value = "";
        }

        // Set preview URL untuk gambar
        if (val?.gambar) {
            const img = val.gambar.replace(/\\/g, "/");
            previewUrl.value = img.startsWith("http")
                ? img
                : "/storage/" + img.replace(/^storage\//, "");
        }
    }
);

watch(
    () => form.judul,
    (newJudul) => {
        if (!slugManual.value || !form.slug) {
            form.slug = slugify(newJudul);
        }
    }
);

onMounted(() => {
    if (!form.slug) {
        form.slug = slugify(form.judul);
    }
});

// Handle document upload
const handleDocumentUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        documentFile.value = file;
        documentUrl.value = file.name;
        form.dokumen = file;
    }
};

// Remove document
const removeDocument = () => {
    documentFile.value = null;
    documentUrl.value = "";
    form.dokumen = null;
    form.remove_dokumen = true;
};

function submit() {
    if (isSubmitting.value) return;
    isSubmitting.value = true;

    const data = new FormData();

    // Append form data sesuai dengan model Pengumuman
    const formFields = [
        "judul",
        "slug",
        "konten",
        "excerpt",
        "gambar",
        "dokumen",
        "nomor_pengumuman",
        "tanggal_terbit",
        "tanggal_berakhir",
        "is_penting",
        "is_aktif",
        "tipe",
    ];

    // Validasi required fields
    if (!form.judul || !form.konten || !form.tanggal_terbit) {
        showError(
            "Judul, konten, dan tanggal terbit wajib diisi",
            "Validasi Gagal"
        );
        isSubmitting.value = false;
        return;
    }

    formFields.forEach((key) => {
        const value = form[key];

        if (key === "gambar") {
            if (value instanceof File) {
                data.append("gambar", value);
            } else if (value === null && props.pengumuman?.gambar) {
                data.append("remove_gambar", "true");
            }
        } else if (key === "dokumen") {
            if (value instanceof File) {
                data.append("dokumen", value);
            } else if (value === null && props.pengumuman.dokumen) {
                // Jika menghapus dokumen yang sudah ada
                data.append("remove_dokumen", "true");
            } else if (value !== undefined && value !== null && value !== "") {
                data.append("dokumen", value);
            }
        } else if (key === "is_penting" || key === "is_aktif") {
            data.append(key, value); // kirim langsung "0" atau "1"
        } else if (key === "tanggal_terbit" || key === "tanggal_berakhir") {
            // Date values
            if (value) {
                data.append(key, value);
            }
        } else if (value !== undefined && value !== null) {
            data.append(key, value);
        }
    });

    if (props.method.toLowerCase() !== "post") {
        data.append("_method", props.method.toUpperCase());
    }

    router.post(props.submitUrl, data, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            const successTitle =
                props.method.toLowerCase() === "post"
                    ? "Pengumuman berhasil dibuat"
                    : "Pengumuman berhasil diperbarui";
            success("", successTitle, 1600);
            isSubmitting.value = false;

            // Redirect ke index setelah sukses
            setTimeout(() => {
                router.visit(route("admin.pengumuman.index"));
            }, 1700);
        },
        onError: (e) => {
            console.error("Validation errors:", e);

            // Tampilkan semua error
            const errorMessages = Object.values(e || {})
                .filter((v) => typeof v === "string")
                .join(", ");

            showError(
                errorMessages || "Mohon periksa input dan coba lagi.",
                "Gagal menyimpan"
            );

            // Scroll ke error pertama
            setTimeout(() => {
                const firstError = Object.keys(e || {})[0];
                if (firstError) {
                    const errorElement = document.querySelector(
                        `[name="${firstError}"]`
                    );
                    if (errorElement) {
                        errorElement.scrollIntoView({
                            behavior: "smooth",
                            block: "center",
                        });
                        errorElement.focus();
                    }
                }
            }, 100);

            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
}

function slugify(str) {
    return (str || "")
        .toString()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "");
}

function togglePreview() {
    isPreviewMode.value = !isPreviewMode.value;
}

function toggleSourceView() {
    if (isSourceView.value) {
        // Save back edited source to content
        form.konten = sourceContent.value;
        isSourceView.value = false;
    } else {
        // Load current content into source buffer
        sourceContent.value = form.konten;
        isSourceView.value = true;
    }
}

const sourceContent = ref("");
</script>

<style>
p {
    margin-bottom: 15px;
}

#pengumuman-content a {
    color: #007bff !important;
}

#pengumuman-content a:hover {
    color: #0056b3 !important;
}

.preview-mode {
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Prose styling untuk preview */
.prose {
    max-width: none;
    color: #374151;
}

.prose p {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
}

.prose h1,
.prose h2,
.prose h3,
.prose h4 {
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    font-weight: 600;
}
</style>

<template>
    <Head :title="formTitle" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ formTitle }}
                </h2>
                <div class="flex gap-2">
                    <button
                        @click="togglePreview"
                        :class="[
                            'px-4 py-2 rounded text-sm font-medium transition-colors',
                            isPreviewMode
                                ? 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                : 'bg-blue-600 text-white hover:bg-blue-700',
                        ]"
                    >
                        {{ isPreviewMode ? "Edit Mode" : "Preview Mode" }}
                    </button>
                </div>
            </div>
        </template>

        <!-- Edit Mode -->
        <div v-if="!isPreviewMode" class="grid grid-cols-12 gap-6 py-12 px-12">
            <!-- Main Editor -->
            <div class="col-span-8">
                <div>
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 space-y-4">
                            <div>
                                <InputLabel value="Judul Pengumuman" />
                                <span class="text-red-500">*</span>
                                <TextInput
                                    v-model="form.judul"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :class="
                                        errors.judul ? 'border-red-500' : ''
                                    "
                                />
                                <p
                                    v-if="errors.judul"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.judul }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Konten<span class="text-red-500"
                                        >*</span
                                    ></label
                                >
                                <p
                                    v-if="errors.konten"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.konten }}
                                </p>

                                <div class="flex gap-2 mb-2 mt-4 items-center">
                                    <button
                                        type="button"
                                        @click="toggleSourceView"
                                        class="px-3 py-1 text-sm bg-gray-500 text-white rounded hover:bg-gray-600"
                                    >
                                        {{
                                            isSourceView
                                                ? "WYSIWYG"
                                                : "Source Code"
                                        }}
                                    </button>
                                </div>

                                <!-- Source Code View -->
                                <div
                                    v-if="isSourceView"
                                    class="border rounded-lg"
                                >
                                    <textarea
                                        v-model="sourceContent"
                                        class="w-full h-[32rem] p-4 font-mono text-sm border-0 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Enter HTML content..."
                                    ></textarea>
                                </div>

                                <!-- WYSIWYG Editor -->
                                <QuillEditor
                                    v-else
                                    ref="quillEditor"
                                    v-model:content="form.konten"
                                    :options="quillOptions"
                                    content-type="html"
                                    style="height: 600px"
                                />
                            </div>

                            <div class="flex justify-end gap-2 pt-4">
                                <PrimaryButton
                                    @click="submit"
                                    :disabled="isSubmitting"
                                    :class="[
                                        isSubmitting
                                            ? 'opacity-50 cursor-not-allowed'
                                            : '',
                                    ]"
                                >
                                    Simpan
                                </PrimaryButton>
                                <button
                                    @click="
                                        () =>
                                            router.visit(
                                                route('admin.pengumuman.index')
                                            )
                                    "
                                    type="button"
                                    class="inline-flex items-center rounded bg-gray-200 px-4 py-2 hover:bg-gray-300"
                                >
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Content -->
            <div class="col-span-4 flex flex-col gap-6">
                <div
                    class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col gap-6"
                >
                    <div>
                        <div class="flex items-center justify-between">
                            <InputLabel value="Slug (opsional)" />
                            <span
                                v-if="errors.slug"
                                class="text-xs text-red-600 font-medium"
                                >⚠ Error</span
                            >
                        </div>
                        <TextInput
                            v-model="form.slug"
                            @input="slugManual = true"
                            type="text"
                            name="slug"
                            :class="[
                                'mt-1 block w-full transition-colors',
                                errors.slug
                                    ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50'
                                    : '',
                            ]"
                        />
                        <div
                            v-if="errors.slug"
                            class="mt-2 p-3 bg-red-50 border-l-4 border-red-500 rounded"
                        >
                            <p
                                class="text-sm text-red-700 font-medium flex items-start gap-2"
                            >
                                <svg
                                    class="w-5 h-5 flex-shrink-0 mt-0.5"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span>{{ errors.slug }}</span>
                            </p>
                        </div>
                        <p v-else class="text-xs text-gray-500 mt-1">
                            Slug akan otomatis dibuat dari judul jika dibiarkan
                            kosong
                        </p>
                    </div>

                    <div>
                        <InputLabel value="Ringkasan (opsional)" />
                        <textarea
                            v-model="form.excerpt"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ringkasan singkat pengumuman..."
                            maxlength="500"
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            Maksimal 500 karakter ({{
                                form.excerpt?.length || 0
                            }}/500)
                        </p>
                    </div>

                    <div>
                        <InputLabel value="Nomor Pengumuman (opsional)" />
                        <TextInput
                            v-model="form.nomor_pengumuman"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Contoh: 001/PPID/2024"
                        />
                    </div>

                    <div>
                        <InputLabel value="Tipe Pengumuman" />
                        <select
                            v-model="form.tipe"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option
                                v-for="tipe in tipeOptions"
                                :key="tipe"
                                :value="tipe"
                            >
                                {{
                                    tipe.charAt(0).toUpperCase() + tipe.slice(1)
                                }}
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel value="Tanggal Terbit" />
                            <span class="text-red-500">*</span>
                            <input
                                v-model="form.tanggal_terbit"
                                type="date"
                                :class="[
                                    'mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                                    errors.tanggal_terbit
                                        ? 'border-red-500'
                                        : 'border-gray-300',
                                ]"
                            />
                            <p
                                v-if="errors.tanggal_terbit"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.tanggal_terbit }}
                            </p>
                        </div>

                        <div>
                            <InputLabel value="Tanggal Berakhir (opsional)" />
                            <input
                                v-model="form.tanggal_berakhir"
                                type="date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :min="form.tanggal_terbit"
                            />
                            <p
                                v-if="errors.tanggal_berakhir"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.tanggal_berakhir }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center space-x-2">
                                <input
                                    v-model="form.is_penting"
                                    type="checkbox"
                                    true-value="1"
                                    false-value="0"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <span class="text-sm font-medium text-gray-700"
                                    >Penting</span
                                >
                            </label>
                            <p class="text-xs text-gray-500 mt-1">
                                Tandai sebagai pengumuman penting
                            </p>
                        </div>

                        <div>
                            <label class="flex items-center space-x-2">
                                <input
                                    v-model="form.is_aktif"
                                    type="checkbox"
                                    true-value="1"
                                    false-value="0"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <span class="text-sm font-medium text-gray-700"
                                    >Aktif</span
                                >
                            </label>
                            <p class="text-xs text-gray-500 mt-1">
                                Nonaktifkan untuk menyembunyikan
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col gap-6"
                >
                    <div>
                        <FormImageUpload
                            v-model="form.gambar"
                            v-model:previewUrl="previewUrl"
                            :error="errors.gambar"
                            label="Gambar Thumbnail"
                        />
                        <p class="text-xs text-gray-500 mt-2">
                            Gambar akan dikonversi ke format WebP otomatis
                        </p>
                    </div>
                </div>

                <div
                    class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col gap-6"
                >
                    <div>
                        <InputLabel value="Lampiran Dokumen" />
                        <div class="mt-2">
                            <div
                                v-if="documentUrl && !documentFile"
                                class="mb-3 p-3 bg-blue-50 rounded-lg"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg
                                            class="w-5 h-5 text-blue-500 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        <a
                                            :href="documentUrl"
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-800 text-sm truncate"
                                        >
                                            {{ documentUrl.split("/").pop() }}
                                        </a>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeDocument"
                                        class="text-red-500 hover:text-red-700"
                                        title="Hapus dokumen"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div
                                v-if="documentFile"
                                class="mb-3 p-3 bg-green-50 rounded-lg"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg
                                            class="w-5 h-5 text-green-500 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        <span
                                            class="text-green-700 text-sm truncate"
                                            >{{ documentFile.name }}</span
                                        >
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeDocument"
                                        class="text-red-500 hover:text-red-700"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <label class="block">
                                <span class="sr-only">Upload dokumen</span>
                                <input
                                    type="file"
                                    @change="handleDocumentUpload"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                            </label>
                            <p class="text-xs text-gray-500 mt-2">
                                Format: PDF, DOC, DOCX, XLS, XLSX (maks. 10MB)
                            </p>
                            <p
                                v-if="errors.dokumen"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.dokumen }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Mode -->
        <div v-else class="preview-mode">
            <div class="container mx-auto px-4 py-12">
                <div class="grid grid-cols-12 gap-4">
                    <div class="order-2 lg:order-1 col-span-12 lg:col-span-3">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 mb-6">
                                Preview Mode
                            </h2>
                            <div
                                class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4"
                            >
                                <p class="text-sm">
                                    Ini adalah mode preview. Pengumuman belum
                                    dipublikasikan.
                                </p>
                            </div>

                            <div class="mt-6 space-y-4">
                                <div>
                                    <h3
                                        class="text-sm font-bold text-gray-700 mb-2"
                                    >
                                        Informasi
                                    </h3>
                                    <div class="space-y-2">
                                        <div>
                                            <span class="text-xs text-gray-500"
                                                >Tipe:</span
                                            >
                                            <p class="font-medium">
                                                {{
                                                    form.tipe
                                                        .charAt(0)
                                                        .toUpperCase() +
                                                    form.tipe.slice(1)
                                                }}
                                            </p>
                                        </div>
                                        <div v-if="form.nomor_pengumuman">
                                            <span class="text-xs text-gray-500"
                                                >Nomor:</span
                                            >
                                            <p class="font-medium">
                                                {{ form.nomor_pengumuman }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500"
                                                >Status:</span
                                            >
                                            <span
                                                :class="[
                                                    'px-2 py-1 text-xs rounded-full',
                                                    form.is_aktif
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800',
                                                ]"
                                            >
                                                {{
                                                    form.is_aktif
                                                        ? "Aktif"
                                                        : "Nonaktif"
                                                }}
                                            </span>
                                        </div>
                                        <div v-if="form.is_penting">
                                            <span class="text-xs text-gray-500"
                                                >Status:</span
                                            >
                                            <span
                                                class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full"
                                            >
                                                ⭐ Penting
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="form.tanggal_berakhir"
                                    class="pt-4 border-t"
                                >
                                    <span class="text-xs text-gray-500"
                                        >Berlaku hingga:</span
                                    >
                                    <p class="font-medium">
                                        {{
                                            new Date(
                                                form.tanggal_berakhir
                                            ).toLocaleDateString("id-ID", {
                                                year: "numeric",
                                                month: "long",
                                                day: "numeric",
                                            })
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="order-1 lg:order-2 col-span-12 lg:col-span-9 space-y-6"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span
                                :class="[
                                    'px-3 py-1 text-xs font-medium rounded-full',
                                    form.tipe === 'pengumuman'
                                        ? 'bg-blue-100 text-blue-800'
                                        : form.tipe === 'pemberitahuan'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : form.tipe === 'undangan'
                                        ? 'bg-purple-100 text-purple-800'
                                        : form.tipe === 'lowongan'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-gray-100 text-gray-800',
                                ]"
                            >
                                {{
                                    form.tipe.charAt(0).toUpperCase() +
                                    form.tipe.slice(1)
                                }}
                            </span>
                            <span
                                v-if="form.is_penting"
                                class="text-yellow-500 text-lg"
                                >⭐</span
                            >
                        </div>

                        <div
                            v-if="previewUrl || form.gambar"
                            class="overflow-hidden rounded"
                        >
                            <img
                                :src="previewUrl"
                                :alt="form.judul || 'Preview'"
                                class="w-full object-cover object-center max-h-[400px]"
                            />
                        </div>

                        <div class="flex flex-col items-start justify-between">
                            <h1
                                class="text-4xl font-bold text-gray-900 mb-4 leading-tight"
                            >
                                {{ form.judul || "Judul Pengumuman" }}
                            </h1>
                            <p class="text-sm text-gray-500">
                                {{ formattedDate }}
                            </p>
                            <div
                                v-if="form.nomor_pengumuman"
                                class="text-sm text-gray-600 mt-1"
                            >
                                Nomor: {{ form.nomor_pengumuman }}
                            </div>
                        </div>

                        <div
                            v-if="form.excerpt"
                            class="text-gray-700 text-sm leading-normal p-4 bg-gray-50 rounded-lg"
                        >
                            {{ form.excerpt }}
                        </div>

                        <article class="prose max-w-none leading-normal">
                            <div
                                id="pengumuman-content"
                                v-html="
                                    form.konten ||
                                    '<p>Konten pengumuman akan ditampilkan di sini...</p>'
                                "
                            ></div>
                        </article>

                        <div
                            v-if="documentUrl"
                            class="p-4 border rounded-lg bg-blue-50"
                        >
                            <div class="flex items-center">
                                <svg
                                    class="w-6 h-6 text-blue-500 mr-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        Lampiran Dokumen
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Tersedia untuk diunduh
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons in preview mode -->
                        <div class="flex justify-start gap-2 pt-8 border-t">
                            <PrimaryButton
                                @click="submit"
                                :disabled="isSubmitting"
                                :class="[
                                    isSubmitting
                                        ? 'opacity-50 cursor-not-allowed'
                                        : '',
                                ]"
                            >
                                Simpan
                            </PrimaryButton>
                            <button
                                @click="togglePreview"
                                class="inline-flex items-center rounded bg-gray-200 px-4 py-2 hover:bg-gray-300"
                            >
                                Kembali ke Edit
                            </button>
                            <button
                                @click="
                                    () =>
                                        router.visit(
                                            route('admin.pengumuman.index')
                                        )
                                "
                                type="button"
                                class="inline-flex items-center rounded bg-gray-200 px-4 py-2 hover:bg-gray-300"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
