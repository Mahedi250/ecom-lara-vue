<template>
    <AdminLayout title="Add Product" subtitle="Create a new product">
        <template #header-actions>
            <Link :href="route('admin.products.index')" class="text-sm text-gray-500 hover:text-gray-800 border border-gray-200 px-4 py-2 rounded-xl transition">← Back</Link>
        </template>

        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left: Main info -->
                <div class="lg:col-span-2 space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900 text-base">Basic Information</h2>

                        <div>
                            <label class="label">Product Name <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" placeholder="Samsung Galaxy S25..." class="input" :class="{'border-red-400': errors.name}" />
                            <p v-if="errors.name" class="error">{{ errors.name }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">SKU <span class="text-red-500">*</span></label>
                                <input v-model="form.sku" type="text" placeholder="PROD-001" class="input" />
                                <p v-if="errors.sku" class="error">{{ errors.sku }}</p>
                            </div>
                            <div>
                                <label class="label">Status</label>
                                <select v-model="form.status" class="input">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="label">Short Description</label>
                            <textarea v-model="form.short_description" rows="2" placeholder="Brief summary..." class="input resize-none"></textarea>
                        </div>

                        <div>
                            <label class="label">Full Description</label>
                            <textarea v-model="form.description" rows="5" placeholder="Detailed product description..." class="input resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Pricing & Stock -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900 text-base">Pricing & Stock</h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Price (৳) <span class="text-red-500">*</span></label>
                                <input v-model="form.price" type="number" step="0.01" min="0" placeholder="0.00" class="input" />
                                <p v-if="errors.price" class="error">{{ errors.price }}</p>
                            </div>
                            <div>
                                <label class="label">Sale Price (৳)</label>
                                <input v-model="form.sale_price" type="number" step="0.01" min="0" placeholder="Optional" class="input" />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="label">Stock</label>
                                <input v-model="form.stock" type="number" min="0" placeholder="0" class="input" />
                            </div>
                            <div class="flex items-end pb-px">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="form.manage_stock" type="checkbox" class="w-4 h-4 text-indigo-600 rounded" />
                                    <span class="text-sm text-gray-700">Manage Stock</span>
                                </label>
                            </div>
                            <div class="flex items-end pb-px">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="form.is_featured" type="checkbox" class="w-4 h-4 text-indigo-600 rounded" />
                                    <span class="text-sm text-gray-700">Featured</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900 text-base">SEO</h2>
                        <div>
                            <label class="label">Meta Title</label>
                            <input v-model="form.meta_title" type="text" class="input" />
                        </div>
                        <div>
                            <label class="label">Meta Description</label>
                            <textarea v-model="form.meta_description" rows="2" class="input resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Gallery Images -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900 text-base">Gallery Images</h2>
                        <MultiMediaPicker @change="onGalleryChange" />
                    </div>

                    <!-- Variants -->
                    <ProductVariantsEditor
                        v-model="form.variants"
                        :attributes="attributes"
                        @variant-image-change="onVariantImageChange"
                    />
                </div>

                <!-- Right: Sidebar -->
                <div class="space-y-5">

                    <!-- Category & Brand -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-900 text-base">Organisation</h2>
                        <div>
                            <label class="label">Category</label>
                            <select v-model="form.category_id" class="input">
                                <option value="">Select category</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Brand</label>
                            <select v-model="form.brand_id" class="input">
                                <option value="">Select brand</option>
                                <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                        <h2 class="font-semibold text-gray-900 text-base">Thumbnail</h2>
                        <MediaPicker v-model="thumbFile" @select="onThumbMediaSelect" />
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2"
                    >
                        <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ submitting ? 'Creating...' : 'Create Product' }}
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ProductVariantsEditor from '@/Components/Admin/ProductVariantsEditor.vue';
import MediaPicker from '@/Components/Admin/MediaPicker.vue';
import MultiMediaPicker from '@/Components/Admin/MultiMediaPicker.vue';

const props = defineProps({ categories: Array, brands: Array, productTypes: Array, attributes: { type: Array, default: () => [] } });
const page = usePage();
const errors = computed(() => page.props.errors ?? {});
const thumbFile = ref(null);
const existingThumbnail = ref(null);
const galleryPaths = ref([]);
const variantImageFiles = ref({});
const submitting = ref(false);

const form = useForm({
    name: '', sku: '', status: 'active',
    short_description: '', description: '',
    price: '', sale_price: '', stock: 0,
    manage_stock: true, is_featured: false,
    category_id: '', brand_id: '',
    meta_title: '', meta_description: '',
    variants: [],
});

function onThumbMediaSelect(media) {
    if (media) { existingThumbnail.value = media.path; thumbFile.value = null; }
    else { existingThumbnail.value = null; }
}

function onGalleryChange(paths) {
    galleryPaths.value = paths;
}

function onVariantImageChange({ index, file }) {
    variantImageFiles.value[index] = file;
}

function submit() {
    // Build FormData manually to support gallery + variant image arrays
    const data = form.data();
    const formData = new FormData();

    // Scalar fields
    Object.entries(data).forEach(([key, value]) => {
        if (key === 'variants') return;
        if (value === null || value === undefined) return;
        // Boolean → "1"/"0" (Laravel boolean rule requires this)
        formData.append(key, typeof value === 'boolean' ? (value ? '1' : '0') : value);
    });

    // Thumbnail
    if (existingThumbnail.value) formData.append('existing_thumbnail', existingThumbnail.value);

    // Gallery images (existing media paths)
    galleryPaths.value.forEach(path => formData.append('existing_images[]', path));

    // Variants (nested)
    form.variants.forEach((variant, i) => {
        Object.entries(variant).forEach(([k, v]) => {
            if (k === 'existingImage') return;
            if (k === 'attributes') {
                Object.entries(v ?? {}).forEach(([attrId, valueId]) => {
                    formData.append(`variants[${i}][attributes][${attrId}]`, valueId);
                });
            } else if (v !== null && v !== undefined) {
                formData.append(`variants[${i}][${k}]`, typeof v === 'boolean' ? (v ? '1' : '0') : v);
            }
        });
        if (variantImageFiles.value[i]) {
            formData.append(`variants[${i}][imageFile]`, variantImageFiles.value[i]);
        }
    });

    submitting.value = true;
    router.post(route('admin.products.store'), formData, {
        forceFormData: true,
        onFinish: () => { submitting.value = false; },
    });
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent; }
.error { @apply text-red-500 text-xs mt-1; }
</style>
