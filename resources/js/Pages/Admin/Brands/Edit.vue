<template>
    <AdminLayout :title="`Edit: ${brand.name}`" subtitle="Update brand details">
        <template #header-actions>
            <Link :href="route('admin.brands.index')" class="text-sm text-gray-500 hover:text-gray-800 border border-gray-200 px-4 py-2 rounded-xl transition">← Back</Link>
        </template>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900">Brand Information</h2>

                        <div>
                            <label class="label">Brand Name <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" class="input" :class="{'border-red-400': errors.name}" />
                            <p v-if="errors.name" class="error">{{ errors.name }}</p>
                        </div>

                        <div>
                            <label class="label">Slug</label>
                            <input v-model="form.slug" type="text" class="input" />
                            <p v-if="errors.slug" class="error">{{ errors.slug }}</p>
                        </div>

                        <div>
                            <label class="label">Website</label>
                            <input v-model="form.website" type="url" placeholder="https://..." class="input" />
                        </div>

                        <div>
                            <label class="label">Description</label>
                            <textarea v-model="form.description" rows="3" class="input resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-900">Settings</h2>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="form.is_active" type="checkbox" class="w-4 h-4 text-indigo-600 rounded" />
                            <span class="text-sm text-gray-700 font-medium">Active</span>
                        </label>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                        <h2 class="font-semibold text-gray-900">Logo</h2>
                        <MediaPicker v-model="form.logo" :current-url="brand.logo" @select="onMediaSelect" />
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

const props = defineProps({ brand: Object });
const page = usePage();
const errors = computed(() => page.props.errors ?? {});

const form = useForm({
    name: props.brand.name ?? '',
    slug: props.brand.slug ?? '',
    website: props.brand.website ?? '',
    description: props.brand.description ?? '',
    is_active: props.brand.is_active ?? true,
    logo: null,
    existing_logo: null,
    _method: 'PUT',
});

function onMediaSelect(media) {
    if (media) { form.existing_logo = media.path; form.logo = null; }
    else { form.existing_logo = null; }
}

function submit() {
    form.post(route('admin.brands.update', props.brand.id));
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent; }
.error { @apply text-red-500 text-xs mt-1; }
</style>
