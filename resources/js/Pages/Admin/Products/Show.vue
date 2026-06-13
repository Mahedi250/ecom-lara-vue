<template>
    <AdminLayout :title="product.name" subtitle="Product details">
        <template #header-actions>
            <div class="flex items-center gap-2">
                <Link :href="route('admin.products.index')"
                    class="text-sm text-gray-500 hover:text-gray-800 border border-gray-200 px-4 py-2 rounded-xl transition">
                    ← Back
                </Link>
                <Link :href="route('admin.products.edit', product.id)"
                    class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-xl transition">
                    Edit Product
                </Link>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left: main info -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Basic info -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ product.name }}</h2>
                            <p class="text-sm text-gray-400 font-mono mt-1">SKU: {{ product.sku }}</p>
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                            :class="statusClass">
                            <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass"></span>
                            {{ product.status_label ?? product.status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
                        <div class="bg-indigo-50 rounded-xl p-3">
                            <p class="text-xs text-indigo-400 font-semibold mb-1">Price</p>
                            <p class="text-lg font-extrabold text-indigo-700">৳{{ fmt(product.effective_price) }}</p>
                            <p v-if="product.sale_price" class="text-xs text-gray-400 line-through">৳{{ fmt(product.price) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 font-semibold mb-1">Stock</p>
                            <p class="text-lg font-extrabold" :class="product.in_stock ? 'text-green-600' : 'text-red-500'">
                                {{ product.stock }}
                            </p>
                            <p class="text-xs" :class="product.in_stock ? 'text-green-500' : 'text-red-400'">
                                {{ product.in_stock ? 'In stock' : 'Out of stock' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 font-semibold mb-1">Category</p>
                            <p class="text-sm font-bold text-gray-700 truncate">{{ product.category?.name ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 font-semibold mb-1">Brand</p>
                            <p class="text-sm font-bold text-gray-700 truncate">{{ product.brand?.name ?? '—' }}</p>
                        </div>
                    </div>

                    <div v-if="product.short_description" class="text-sm text-gray-600 leading-relaxed border-t border-gray-50 pt-4">
                        {{ product.short_description }}
                    </div>
                </div>

                <!-- Variants -->
                <div v-if="product.variants?.length" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900 text-base">Variants</h3>
                        <span class="text-xs bg-indigo-50 text-indigo-700 font-bold px-2.5 py-1 rounded-full">
                            {{ product.variants.length }} total
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div v-for="variant in product.variants" :key="variant.id"
                            class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50">
                            <!-- Variant image -->
                            <div class="w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-white border border-gray-200">
                                <img v-if="variant.image" :src="variant.image" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <!-- Attribute pills -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap gap-1 mb-1">
                                    <span v-for="attr in (variant.attributes ?? [])" :key="attr.attribute"
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                        <span v-if="attr.color_code" class="w-2.5 h-2.5 rounded-full border border-white shadow-sm" :style="{ background: attr.color_code }"></span>
                                        {{ attr.attribute }}: {{ attr.value }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">{{ variant.sku }}</p>
                            </div>
                            <!-- Price + stock -->
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-extrabold text-gray-900">৳{{ fmt(variant.effective_price) }}</p>
                                <p class="text-xs mt-0.5" :class="variant.stock > 0 ? 'text-green-500' : 'text-red-400'">
                                    {{ variant.stock }} in stock
                                </p>
                            </div>
                            <!-- Active badge -->
                            <span class="flex-shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full"
                                :class="variant.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'">
                                {{ variant.is_active ? 'Active' : 'Off' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div v-if="product.description" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 text-base mb-3">Description</h3>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ product.description }}</p>
                </div>

                <!-- Gallery images -->
                <div v-if="product.images?.length" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 text-base mb-4">Gallery Images</h3>
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                        <div v-for="img in product.images" :key="img.id"
                            class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                            <img :src="img.url" class="w-full h-full object-cover" />
                            <span v-if="img.is_primary" class="absolute top-1 left-1 bg-indigo-600 text-white text-[10px] px-1.5 py-0.5 rounded font-semibold">Main</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right sidebar -->
            <div class="space-y-5">

                <!-- Thumbnail -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-semibold text-gray-900 text-base mb-3">Thumbnail</h3>
                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                        <img v-if="product.thumbnail" :src="product.thumbnail" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-300 gap-2">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm">No thumbnail</p>
                        </div>
                    </div>
                </div>

                <!-- Flags -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                    <h3 class="font-semibold text-gray-900 text-base">Settings</h3>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Featured</span>
                        <span class="font-semibold" :class="product.is_featured ? 'text-indigo-600' : 'text-gray-400'">
                            {{ product.is_featured ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Manage Stock</span>
                        <span class="font-semibold text-gray-700">{{ product.manage_stock ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Slug</span>
                        <span class="font-mono text-xs text-gray-500 truncate max-w-[120px]">{{ product.slug }}</span>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                    <h3 class="font-semibold text-gray-900 text-base">History</h3>
                    <div class="text-sm space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Created</span>
                            <span class="text-gray-600 font-medium">{{ fmtDate(product.created_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Updated</span>
                            <span class="text-gray-600 font-medium">{{ fmtDate(product.updated_at) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Danger zone -->
                <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-5">
                    <h3 class="font-semibold text-red-700 text-base mb-3">Danger Zone</h3>
                    <Link :href="route('admin.products.destroy', product.id)" method="delete" as="button"
                        class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-sm py-2.5 rounded-xl transition border border-red-100"
                        :onBefore="() => confirm('Delete this product? This cannot be undone.')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Product
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ product: Object });

const statusClass = computed(() => ({
    'bg-green-50 text-green-700 border-green-200':  props.product.status === 'active',
    'bg-gray-50 text-gray-500 border-gray-200':     props.product.status === 'inactive',
    'bg-amber-50 text-amber-700 border-amber-200':  props.product.status === 'draft',
    'bg-red-50 text-red-600 border-red-200':        props.product.status === 'archived',
}));

const statusDotClass = computed(() => ({
    'bg-green-500': props.product.status === 'active',
    'bg-gray-400':  props.product.status === 'inactive',
    'bg-amber-500': props.product.status === 'draft',
    'bg-red-500':   props.product.status === 'archived',
}));

function fmt(val) { return Number(val).toLocaleString('en-BD'); }
function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>
