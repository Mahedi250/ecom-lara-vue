<template>
    <AdminLayout :title="`Edit: ${category.name}`" subtitle="Update category details">
        <template #header-actions>
            <Link :href="route('admin.categories.index')" class="text-sm text-gray-500 hover:text-gray-800 border border-gray-200 px-4 py-2 rounded-xl transition">← Back</Link>
        </template>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900">Basic Information</h2>

                        <div>
                            <label class="label">Category Name <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" class="input" :class="{'border-red-400': errors.name}" />
                            <p v-if="errors.name" class="error">{{ errors.name }}</p>
                        </div>

                        <div>
                            <label class="label">Slug</label>
                            <input v-model="form.slug" type="text" class="input" />
                            <p v-if="errors.slug" class="error">{{ errors.slug }}</p>
                        </div>

                        <div>
                            <label class="label">Description</label>
                            <textarea v-model="form.description" rows="3" class="input resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Meta Title</label>
                                <input v-model="form.meta_title" type="text" class="input" />
                            </div>
                            <div>
                                <label class="label">Sort Order</label>
                                <input v-model="form.sort_order" type="number" min="0" class="input" />
                            </div>
                        </div>

                        <div>
                            <label class="label">Meta Description</label>
                            <textarea v-model="form.meta_description" rows="2" class="input resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-900">Settings</h2>

                        <div>
                            <label class="label">Parent Category</label>
                            <select v-model="form.parent_id" class="input">
                                <option value="">None (Root)</option>
                                <option v-for="p in parents" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="form.is_active" type="checkbox" class="w-4 h-4 text-indigo-600 rounded" />
                            <span class="text-sm text-gray-700 font-medium">Active</span>
                        </label>
                    </div>

                    <!-- Image -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                        <h2 class="font-semibold text-gray-900">Image</h2>
                        <MediaPicker v-model="form.image" :current-url="category.image" @select="onMediaSelect" />
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-bold py-3.5 rounded-xl transition">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MediaPicker from '@/Components/Admin/MediaPicker.vue';

const props = defineProps({ category: Object, parents: Array });
const page = usePage();
const errors = computed(() => page.props.errors ?? {});

const form = useForm({
    name: props.category.name ?? '',
    slug: props.category.slug ?? '',
    description: props.category.description ?? '',
    parent_id: props.category.parent_id ?? '',
    is_active: props.category.is_active ?? true,
    sort_order: props.category.sort_order ?? 0,
    meta_title: props.category.meta_title ?? '',
    meta_description: props.category.meta_description ?? '',
    image: null,
    existing_image: null,
    _method: 'PUT',
});

function onMediaSelect(media) {
    if (media) { form.existing_image = media.path; form.image = null; }
    else { form.existing_image = null; }
}

function submit() {
    form.post(route('admin.categories.update', props.category.id));
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent; }
.error { @apply text-red-500 text-xs mt-1; }
</style>
