<template>
    <AdminLayout title="Add Page" subtitle="Create a new content page">
        <template #header-actions>
            <Link :href="route('admin.pages.index')" class="text-sm text-gray-500 hover:text-gray-800 border border-gray-200 px-4 py-2 rounded-xl transition">← Back</Link>
        </template>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900">Page Information</h2>

                        <div>
                            <label class="label">Title <span class="text-red-500">*</span></label>
                            <input v-model="form.title" type="text" placeholder="Terms & Conditions" class="input" :class="{'border-red-400': form.errors.title}" @input="autoSlug" />
                            <p v-if="form.errors.title" class="error">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label class="label">URL slug</label>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400 text-sm">/page/</span>
                                <input v-model="form.slug" type="text" placeholder="terms-and-conditions" class="input" />
                            </div>
                            <p v-if="form.errors.slug" class="error">{{ form.errors.slug }}</p>
                        </div>

                        <div>
                            <label class="label">Content <span class="text-red-500">*</span></label>
                            <RichTextEditor v-model="form.content" />
                            <p v-if="form.errors.content" class="error">{{ form.errors.content }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900">SEO (optional)</h2>
                        <div>
                            <label class="label">Meta Title</label>
                            <input v-model="form.meta_title" type="text" class="input" />
                        </div>
                        <div>
                            <label class="label">Meta Description</label>
                            <textarea v-model="form.meta_description" rows="2" class="input resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-900">Status</h2>
                        <select v-model="form.status" class="input">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                        <p class="text-xs text-gray-400">Only published pages are visible publicly and shown in the footer.</p>
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-bold py-3.5 rounded-xl transition">
                        {{ form.processing ? 'Creating...' : 'Create Page' }}
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RichTextEditor from '@/Components/Admin/RichTextEditor.vue';

const form = useForm({
    title: '', slug: '', content: '',
    meta_title: '', meta_description: '', status: 'draft',
});

function autoSlug() {
    form.slug = form.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
}

function submit() {
    form.post(route('admin.pages.store'));
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent; }
.error { @apply text-red-500 text-xs mt-1; }
</style>
