<template>
    <AdminLayout :title="`Edit: ${product.name}`" subtitle="Update product details">
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
                            <input v-model="form.name" type="text" class="input" :class="{'border-red-400': errors.name}" />
                            <p v-if="errors.name" class="error">{{ errors.name }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">SKU <span class="text-red-500">*</span></label>
                                <input v-model="form.sku" type="text" class="input" />
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
                            <textarea v-model="form.short_description" rows="2" class="input resize-none"></textarea>
                        </div>

                        <div>
                            <label class="label">Full Description</label>
                            <textarea v-model="form.description" rows="5" class="input resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Pricing & Stock -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900 text-base">Pricing & Stock</h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Price (৳) <span class="text-red-500">*</span></label>
                                <input v-model="form.price" type="number" step="0.01" min="0" class="input" />
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
                                <input v-model="form.stock" type="number" min="0" class="input" />
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

                    <!-- Variants -->
                    <ProductVariantsEditor
                        v-model="form.variants"
                        :attributes="attributes"
                        @variant-image-change="onVariantImageChange"
                    />

                    <!-- Gallery Images -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900 text-base">Gallery Images</h2>

                        <!-- Existing images -->
                        <div v-if="product.images?.length">
                            <p class="text-xs text-gray-400 mb-2">Current images</p>
                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                <div v-for="img in product.images" :key="img.id"
                                    class="relative group aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                                    <img :src="img.url ?? img.image_url" class="w-full h-full object-cover" />
                                    <span v-if="img.is_primary" class="absolute top-1 left-1 bg-indigo-600 text-white text-[10px] px-1.5 py-0.5 rounded font-semibold">Main</span>
                                </div>
                            </div>
                        </div>

                        <!-- Add more images -->
                        <div>
                            <p class="text-xs text-gray-400 mb-2">Add more images</p>
                            <div
                                class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center cursor-pointer hover:border-indigo-400 transition"
                                @click="$refs.galleryInput.click()">
                                <svg class="w-7 h-7 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <p class="text-sm text-gray-400">Click to upload more images</p>
                            </div>
                            <input ref="galleryInput" type="file" accept="image/*" multiple class="hidden" @change="onGallery" />
                        </div>

                        <!-- New image previews -->
                        <div v-if="galleryPreviews.length" class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                            <div v-for="(preview, i) in galleryPreviews" :key="i"
                                class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 group border-2 border-indigo-200">
                                <img :src="preview" class="w-full h-full object-cover" />
                                <button type="button" @click="removeGallery(i)"
                                    class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition">×</button>
                                <span class="absolute bottom-1 left-1 text-[9px] bg-indigo-600 text-white px-1 rounded">New</span>
                            </div>
                        </div>
                    </div>
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
                        <MediaPicker v-model="thumbFile" :current-url="product.thumbnail" @select="onThumbMediaSelect" />
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
                        {{ submitting ? 'Saving...' : 'Save Changes' }}
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

const props = defineProps({ product: Object, categories: Array, brands: Array, productTypes: Array, attributes: Array });
const page = usePage();
const errors = computed(() => page.props.errors ?? {});
const thumbFile = ref(null);
const existingThumbnail = ref(null);
const galleryPreviews = ref([]);
const galleryFiles = ref([]);
const variantImageFiles = ref({});
const submitting = ref(false);

const form = useForm({
    name: props.product.name ?? '',
    sku: props.product.sku ?? '',
    status: props.product.status ?? 'active',
    short_description: props.product.short_description ?? '',
    description: props.product.description ?? '',
    price: props.product.price ?? '',
    sale_price: props.product.sale_price ?? '',
    stock: props.product.stock ?? 0,
    manage_stock: props.product.manage_stock ?? true,
    is_featured: props.product.is_featured ?? false,
    category_id: props.product.category_id ?? '',
    brand_id: props.product.brand_id ?? '',
    meta_title: props.product.meta_title ?? '',
    meta_description: props.product.meta_description ?? '',
    _method: 'PUT',
    variants: (props.product.variants ?? []).map(v => ({
        id: v.id ?? null,
        sku: v.sku ?? '',
        price: v.price ?? '',
        sale_price: v.sale_price ?? '',
        stock: v.stock ?? 0,
        is_active: v.is_active ?? true,
        attributes_map: v.attributes_map ?? {},
    })),
});

function onThumbMediaSelect(media) {
    if (media) { existingThumbnail.value = media.path; thumbFile.value = null; }
    else { existingThumbnail.value = null; }
}

function onGallery(e) {
    Array.from(e.target.files).forEach(file => {
        galleryPreviews.value.push(URL.createObjectURL(file));
        galleryFiles.value.push(file);
    });
    e.target.value = '';
}

function removeGallery(i) {
    galleryPreviews.value.splice(i, 1);
    galleryFiles.value.splice(i, 1);
}

function onVariantImageChange({ index, file }) {
    variantImageFiles.value[index] = file;
}

function submit() {
    const data = form.data();
    const formData = new FormData();

    formData.append('_method', 'PUT');

    Object.entries(data).forEach(([key, value]) => {
        if (key === 'variants' || key === '_method') return;
        if (value === null || value === undefined) return;
        // Boolean → "1"/"0" (Laravel boolean rule requires this)
        formData.append(key, typeof value === 'boolean' ? (value ? '1' : '0') : value);
    });

    if (existingThumbnail.value) formData.append('existing_thumbnail', existingThumbnail.value);
    galleryFiles.value.forEach(file => formData.append('images[]', file));

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
    router.post(route('admin.products.update', props.product.id), formData, {
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
