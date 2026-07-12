<template>
    <StorefrontLayout>
        <Head :title="page.meta_title || page.title">
            <meta v-if="page.meta_description" name="description" :content="page.meta_description" />
        </Head>

        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <h1 class="text-3xl font-extrabold text-gray-900 text-center mb-8">{{ page.title }}</h1>

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-10">
                <div class="page-content font-serif text-[15px] text-gray-700 leading-relaxed" v-html="page.content"></div>
            </div>
        </div>
    </StorefrontLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

defineProps({
    page: { type: Object, required: true },
});
</script>

<style scoped>
.page-content :deep(p) { margin-bottom: 1.25rem; }
.page-content :deep(h2) { font-size: 1.5rem; font-weight: 700; color: #333; margin-bottom: 0.9rem; }
.page-content :deep(h3) { font-size: 1.15rem; font-weight: 700; color: #111827; margin: 1.25rem 0 0.5rem; }
.page-content :deep(strong) { font-weight: 700; color: #111827; }
.page-content :deep(u) { text-decoration: underline; }
.page-content :deep(s) { text-decoration: line-through; }
.page-content :deep(blockquote) { border-left: 3px solid #d1d5db; padding-left: 1rem; color: #6b7280; margin: 1rem 0; font-style: italic; }
.page-content :deep(ul), .page-content :deep(ol) { padding-left: 1.25rem; margin-bottom: 1.875rem; }
.page-content :deep(ul) { list-style: disc; }
.page-content :deep(ol) { list-style: decimal; }
.page-content :deep(li) { margin-bottom: 1.25rem; }
.page-content :deep(a) { color: #0073e6; text-decoration: none; }
.page-content :deep(a:hover) { text-decoration: underline; }
</style>
