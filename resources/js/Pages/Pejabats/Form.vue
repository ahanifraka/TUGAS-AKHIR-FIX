<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormInput from '@/Components/FormInput.vue';
import FormImageUpload from '@/Components/FormImageUpload.vue';
import FormRichTextEditor from '@/Components/FormRichTextEditor.vue';
import ToggleSwitch from 'primevue/toggleswitch';
import Button from 'primevue/button';
import { useSweetAlert } from '@/Composables/useSweetAlert.js';

const props = defineProps({
    pejabat: { type: Object, default: null },
    submitRoute: { type: String, required: true },
    submitMethod: { type: String, default: 'post' },
});

const emit = defineEmits(['submitted']);

const { success, validationError } = useSweetAlert();
const imagePreviewUrl = ref(props.pejabat?.image_url || '');

const form = useForm({
    nama: props.pejabat?.nama || '',
    ttl: props.pejabat?.ttl || '',
    image: props.pejabat?.image || '',
    pendidikan: props.pejabat?.pendidikan || '',
    jabatan: props.pejabat?.jabatan || '',
    description: props.pejabat?.description || '',
    order: props.pejabat?.order || null,
    published: props.pejabat?.published ?? true,
});

function submit() {
    const options = {
        forceFormData: true,
        onSuccess: () => {
            success('Data pejabat berhasil disimpan!');
            emit('submitted');
        },
        onError: () => {
            validationError();
        }
    };

    if (props.submitMethod === 'post') {
        form.post(props.submitRoute, options);
    } else if (props.submitMethod === 'put') {
        form.post(props.submitRoute, {
            ...options,
            _method: 'put'
        });
    }
}
</script>

<template>
    <form @submit.prevent="submit">
        <div class="overflow-hidden bg-white shadow-md sm:rounded-lg">
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                <!-- Left Column: Main Fields -->
                <div class="md:col-span-2 space-y-6">
                    <FormInput
                        v-model="form.nama"
                        label="Nama"
                        placeholder="Masukkan nama lengkap"
                        :error="form.errors.nama"
                        required
                    />

                    <FormInput
                        v-model="form.jabatan"
                        label="Jabatan"
                        placeholder="Masukkan jabatan"
                        :error="form.errors.jabatan"
                        required
                    />

                    <FormInput
                        v-model="form.pendidikan"
                        label="Pendidikan Terakhir"
                        placeholder="Contoh: S2 Magister Manajemen"
                        :error="form.errors.pendidikan"
                    />

                    <FormRichTextEditor
                        v-model="form.description"
                        label="Deskripsi"
                        :error="form.errors.description"
                        height="180px"
                    />
                </div>

                <!-- Right Column: Additional Fields -->
                <div class="md:col-span-1 space-y-6">
                    <!-- Image Upload -->
                    <FormImageUpload
                        v-model="form.image"
                        v-model:preview-url="imagePreviewUrl"
                        :label="pejabat ? 'Upload Gambar Baru' : 'Gambar'"
                        :error="form.errors.image"
                    />

                    <FormInput
                        v-model="form.ttl"
                        label="Tempat, Tanggal Lahir"
                        placeholder="Contoh: Jakarta, 1 Januari 1970"
                        :error="form.errors.ttl"
                    />


                    <FormInput
                        v-model="form.order"
                        label="Order"
                        type="number"
                        :error="form.errors.order"
                    />

                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-gray-700 text-sm">Status</label>
                        <div class="flex items-center gap-3 mt-1">
                            <ToggleSwitch v-model="form.published" inputId="published" />
                            <span
                                :class="form.published ? 'text-green-600 font-semibold' : 'text-yellow-600 font-semibold'">
                                {{ form.published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="p-4 bg-gray-50 text-right border-t">
                <Button type="submit" :label="pejabat ? 'Update Pejabat' : 'Simpan Pejabat'"
                    :loading="form.processing" severity="contrast" />
            </div>
        </div>
    </form>
</template>

<style scoped>
.p-error {
    color: #e24c4c;
}

/* Toggle Switch Styles */
.switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #fbbf24;
    transition: .4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
}

input:checked+.slider {
    background-color: #22c55e;
}

input:checked+.slider:before {
    transform: translateX(20px);
}

.slider.round {
    border-radius: 24px;
}
</style>
