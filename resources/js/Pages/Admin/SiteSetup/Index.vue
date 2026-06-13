<template>
    <AdminLayout title="Site Setup" subtitle="Hero slider, logo, favicon">

        <!-- Tabs -->
        <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit mb-6">
            <button type="button" @click="tab = 'slides'"
                class="px-4 py-1.5 text-sm font-semibold rounded-lg transition"
                :class="tab === 'slides' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                Hero Slides
            </button>
            <button type="button" @click="tab = 'assets'"
                class="px-4 py-1.5 text-sm font-semibold rounded-lg transition"
                :class="tab === 'assets' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                Site Assets
            </button>
        </div>

        <!-- ── Hero Slides ── -->
        <template v-if="tab === 'slides'">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4 sticky top-6">
                        <h2 class="font-semibold text-gray-900">{{ editingSlide ? 'Edit Slide' : 'Add Slide' }}</h2>

                        <!-- Image (required) -->
                        <div>
                            <label class="label">Slide Image <span class="text-red-500">*</span></label>
                            <MediaPicker v-model="slideImageFile" :current-url="imagePreview" @select="onSlideImageSelect" />
                            <p v-if="imageError" class="error mt-1">Image is required.</p>
                            <div class="mt-2 bg-indigo-50 border border-indigo-100 rounded-xl px-3 py-2 space-y-1">
                                <p class="text-[11px] font-semibold text-indigo-600 uppercase tracking-wide">Image Guidelines</p>
                                <ul class="text-[11px] text-indigo-500 space-y-0.5">
                                    <li>&#8226; Size: <strong>1920 × 600 px</strong> (recommended)</li>
                                    <li>&#8226; Format: <strong>JPG</strong> at 80% quality</li>
                                    <li>&#8226; Max file size: <strong>200 KB</strong></li>
                                    <li>&#8226; Keep subject in <strong>center 60%</strong> — edges crop on mobile</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Optional button -->
                        <div class="border border-gray-100 rounded-xl p-3 space-y-3 bg-gray-50">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Button (optional)</p>
                            <div>
                                <label class="label">Button Text</label>
                                <input v-model="slideForm.button_text" type="text" class="input" placeholder="Shop Now" />
                            </div>
                            <div>
                                <label class="label">Link URL</label>
                                <input v-model="slideForm.href" type="text" class="input" placeholder="/products?brand_id=1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Sort Order</label>
                                <input v-model.number="slideForm.sort_order" type="number" min="0" class="input" />
                            </div>
                            <div class="flex items-end pb-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="slideForm.is_active" type="checkbox" class="w-4 h-4 text-indigo-600 rounded" />
                                    <span class="text-sm text-gray-700 font-medium">Active</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-1">
                            <button v-if="editingSlide" type="button" @click="cancelEdit"
                                class="flex-1 border border-gray-200 text-gray-600 text-sm py-2.5 rounded-xl hover:bg-gray-50 transition font-medium">
                                Cancel
                            </button>
                            <button type="button" @click="submitSlide" :disabled="slideForm.processing"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-bold py-2.5 rounded-xl transition">
                                {{ slideForm.processing ? 'Saving...' : (editingSlide ? 'Update' : 'Add Slide') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Slides list -->
                <div class="lg:col-span-2 space-y-3">
                    <div v-if="!slides.length" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm">No slides yet. Upload your first banner image.</p>
                    </div>

                    <div v-for="slide in slides" :key="slide.id"
                        class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex items-stretch">

                        <!-- Thumbnail -->
                        <div class="w-40 h-24 flex-shrink-0 bg-gray-100 overflow-hidden">
                            <img v-if="slide.image_url" :src="slide.image_url" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 px-4 py-3 flex items-center justify-between min-w-0">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span :class="slide.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                        class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full">
                                        {{ slide.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span v-if="slide.button_text" class="text-[10px] bg-indigo-50 text-indigo-600 font-semibold px-2 py-0.5 rounded-full">
                                        Button: {{ slide.button_text }}
                                    </span>
                                </div>
                                <p v-if="slide.href" class="text-xs text-gray-400 truncate max-w-[200px]">{{ slide.href }}</p>
                                <p v-else class="text-xs text-gray-300">No button link</p>
                                <p class="text-xs text-gray-400 mt-0.5">Order: {{ slide.sort_order }}</p>
                            </div>

                            <div class="flex items-center gap-1.5 flex-shrink-0 ml-3">
                                <button type="button" @click="startEdit(slide)"
                                    class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:text-indigo-600 hover:border-indigo-300 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button type="button" @click="deleteSlide(slide)"
                                    class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:text-red-600 hover:border-red-300 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ── Site Assets ── -->
        <template v-if="tab === 'assets'">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                    <div>
                        <h3 class="font-semibold text-gray-900">Site Logo</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Navbar logo. Transparent PNG recommended, 200×60px.</p>
                    </div>
                    <MediaPicker v-model="logoFile" :current-url="assetPreviews.logo" @select="m => assetForm.site_logo = m?.path ?? null" />
                    <button type="button" @click="saveAsset('logo')" :disabled="!assetForm.site_logo"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 text-white text-sm font-bold py-2.5 rounded-xl transition">
                        Save Logo
                    </button>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                    <div>
                        <h3 class="font-semibold text-gray-900">Site Icon</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Square navbar icon. 64×64px recommended.</p>
                    </div>
                    <MediaPicker v-model="iconFile" :current-url="assetPreviews.icon" @select="m => assetForm.site_icon = m?.path ?? null" />
                    <button type="button" @click="saveAsset('icon')" :disabled="!assetForm.site_icon"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 text-white text-sm font-bold py-2.5 rounded-xl transition">
                        Save Icon
                    </button>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                    <div>
                        <h3 class="font-semibold text-gray-900">Favicon</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Browser tab icon. ICO or PNG, 32×32px.</p>
                    </div>
                    <MediaPicker v-model="faviconFile" :current-url="assetPreviews.favicon" @select="m => assetForm.favicon = m?.path ?? null" />
                    <button type="button" @click="saveAsset('favicon')" :disabled="!assetForm.favicon"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 text-white text-sm font-bold py-2.5 rounded-xl transition">
                        Save Favicon
                    </button>
                </div>
            </div>
        </template>

    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MediaPicker from '@/Components/Admin/MediaPicker.vue';

const props = defineProps({
    slides: { type: Array, default: () => [] },
    assets: { type: Object, default: () => ({}) },
});

const tab = ref('slides');

// ── Slide form ──────────────────────────────────────────────
const editingSlide = ref(null);
const slideImageFile = ref(null);
const imagePreview = ref(null);
const imageError = ref(false);

const slideForm = useForm({
    image: null,
    href: '',
    button_text: '',
    sort_order: 0,
    is_active: true,
});

function onSlideImageSelect(media) {
    if (media) {
        slideForm.image = media.path;
        imagePreview.value = media.url;
        imageError.value = false;
    } else {
        slideForm.image = null;
        imagePreview.value = null;
    }
}

function startEdit(slide) {
    editingSlide.value = slide;
    slideForm.image = slide.image ?? null;
    slideForm.href = slide.href ?? '';
    slideForm.button_text = slide.button_text ?? '';
    slideForm.sort_order = slide.sort_order ?? 0;
    slideForm.is_active = slide.is_active ?? true;
    imagePreview.value = slide.image_url ?? null;
    imageError.value = false;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelEdit() {
    editingSlide.value = null;
    slideForm.reset();
    imagePreview.value = null;
    imageError.value = false;
}

function submitSlide() {
    if (!slideForm.image) { imageError.value = true; return; }

    const data = {
        image: slideForm.image,
        href: slideForm.href || null,
        button_text: slideForm.button_text || null,
        sort_order: slideForm.sort_order,
        is_active: slideForm.is_active ? 1 : 0,
    };

    if (editingSlide.value) {
        slideForm.transform(() => data).post(route('admin.site-setup.slides.update', editingSlide.value.id), {
            onSuccess: () => cancelEdit(),
        });
    } else {
        slideForm.transform(() => data).post(route('admin.site-setup.slides.store'), {
            onSuccess: () => {
                slideForm.reset();
                imagePreview.value = null;
                slideImageFile.value = null;
            },
        });
    }
}

function deleteSlide(slide) {
    if (!confirm('Delete this slide?')) return;
    router.delete(route('admin.site-setup.slides.destroy', slide.id));
}

// ── Asset form ──────────────────────────────────────────────
const logoFile = ref(null);
const iconFile = ref(null);
const faviconFile = ref(null);

const assetPreviews = reactive({
    logo:    props.assets.logo    ?? null,
    icon:    props.assets.icon    ?? null,
    favicon: props.assets.favicon ?? null,
});

const assetForm = reactive({
    site_logo: null,
    site_icon: null,
    favicon:   null,
});

function saveAsset(type) {
    const payload = {};
    if (type === 'logo'    && assetForm.site_logo) payload.site_logo = assetForm.site_logo;
    if (type === 'icon'    && assetForm.site_icon) payload.site_icon = assetForm.site_icon;
    if (type === 'favicon' && assetForm.favicon)   payload.favicon   = assetForm.favicon;
    router.post(route('admin.site-setup.assets.update'), payload, { preserveScroll: true });
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent; }
.error { @apply text-red-500 text-xs; }
</style>
