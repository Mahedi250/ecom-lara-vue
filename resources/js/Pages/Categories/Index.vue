<template>
    <StorefrontLayout>
        <Head title="Shop by Category" />

        <!-- Page header -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-2xl font-bold text-gray-900">Shop by Category</h1>
                <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                    <Link :href="route('home')" class="hover:text-indigo-600">Home</Link>
                    <span>/</span>
                    <span class="text-gray-800">Categories</span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div v-if="total" class="bg-white rounded-2xl p-3 sm:p-4">
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2 sm:gap-3">
                    <Link
                        v-for="(cat, i) in categories.data"
                        :key="cat.id"
                        :href="route('products.index', { category_id: cat.id })"
                        class="group flex-col items-center rounded-xl overflow-hidden transition-all duration-200 hover:scale-[1.03] hover:shadow-md bg-[#eeeeee]"
                        :class="visibilityClass(i)"
                    >
                        <!-- Icon area -->
                        <div class="w-full aspect-[5/3] flex items-center justify-center">
                            <img
                                v-if="cat.image"
                                :src="cat.image"
                                :alt="cat.name"
                                class="w-1/2 h-1/2 object-contain group-hover:scale-110 transition-transform duration-300"
                            />
                            <span v-else class="text-4xl select-none group-hover:scale-110 transition-transform duration-300">{{ emoji(cat.name) }}</span>
                        </div>
                        <!-- Title at bottom -->
                        <div class="w-full bg-[#eeeeee] pt-1.5 pb-2.5 px-2 text-center">
                            <span class="text-[10px] sm:text-[11px] font-bold text-gray-700 leading-tight line-clamp-1">{{ cat.name }}</span>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- See all / show less -->
            <div v-if="total > 6" class="text-center mt-5">
                <button @click="expanded = !expanded" class="text-sm text-indigo-600 font-medium hover:underline transition">
                    {{ expanded ? 'Show less ↑' : 'See all →' }}
                </button>
            </div>

            <div v-if="!total" class="text-center py-20">
                <p class="text-5xl mb-3">🗂️</p>
                <h2 class="text-lg font-semibold text-gray-800">No categories available</h2>
                <p class="text-sm text-gray-500 mt-1">Check back later — we're stocking up.</p>
            </div>
        </div>
    </StorefrontLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { categoryEmojis } from '@/Constants/categoryEmojis';

const props = defineProps({
    categories: { type: Object, default: () => ({ data: [] }) },
});

const expanded = ref(false);
const total = computed(() => props.categories?.data?.length ?? 0);

// Same on mobile and desktop: first 6, "See all" reveals everything
function visibilityClass(index) {
    if (expanded.value) return 'flex';
    return index >= 6 ? 'hidden' : 'flex';
}

function emoji(name) {
    return categoryEmojis[name] ?? '🛍️';
}
</script>
