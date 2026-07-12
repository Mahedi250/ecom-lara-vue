<template>
    <StorefrontLayout>
        <Head title="All Brands" />

        <!-- Page header -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-2xl font-bold text-gray-900">All Brands</h1>
                <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                    <Link :href="route('home')" class="hover:text-indigo-600">Home</Link>
                    <span>/</span>
                    <span class="text-gray-800">Brands</span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div v-if="brands.data.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4">
                <Link v-for="brand in brands.data" :key="brand.id"
                    :href="route('products.index', { brand_id: brand.id })"
                    :title="brand.name"
                    class="bg-white rounded-xl border border-gray-100 hover:border-indigo-200 hover:shadow-md transition p-4 flex items-center justify-center h-24">
                    <img v-if="brand.logo" :src="brand.logo" :alt="brand.name"
                        class="h-16 w-auto max-w-full object-contain"/>
                    <div v-else class="h-16 w-16 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 text-xl font-bold">
                        {{ brand.name.charAt(0) }}
                    </div>
                </Link>
            </div>

            <div v-else class="text-center py-20">
                <p class="text-5xl mb-3">🏷️</p>
                <h2 class="text-lg font-semibold text-gray-800">No brands available</h2>
                <p class="text-sm text-gray-500 mt-1">Check back later for our brand lineup.</p>
            </div>
        </div>
    </StorefrontLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

defineProps({
    brands: { type: Object, default: () => ({ data: [] }) },
});
</script>
