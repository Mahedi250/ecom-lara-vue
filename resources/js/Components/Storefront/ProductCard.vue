<template>
    <div class="group bg-white rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200 flex flex-col overflow-hidden">

        <!-- Image -->
        <Link :href="route('products.show', product.slug)" class="block relative bg-white p-3 pb-0">
            <div class="aspect-square relative overflow-hidden rounded-lg bg-gray-50 flex items-center justify-center">
                <img
                    v-if="product.thumbnail"
                    :src="product.thumbnail"
                    :alt="product.name"
                    class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-400"
                    loading="lazy"
                />
                <div v-else class="flex items-center justify-center w-full h-full text-gray-300">
                    <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>

                <!-- Badges -->
                <div class="absolute top-2 left-2 flex flex-col gap-1">
                    <span v-if="discountPercent" class="bg-red-500 text-white text-[11px] font-bold px-2 py-0.5 rounded">
                        Save {{ discountPercent }}%
                    </span>
                    <span v-if="product.is_featured" class="bg-orange-500 text-white text-[11px] font-bold px-2 py-0.5 rounded">
                        Featured
                    </span>
                </div>

                <!-- Out of stock -->
                <div v-if="!product.in_stock" class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-lg">
                    <span class="bg-gray-800 text-white text-xs font-semibold px-3 py-1 rounded-full">Out of Stock</span>
                </div>
            </div>
        </Link>

        <!-- Info -->
        <div class="p-3 flex flex-col flex-1">
            <!-- Category -->
            <p v-if="product.category" class="text-[11px] text-indigo-600 font-semibold uppercase tracking-wide mb-1 truncate">
                {{ product.category.name }}
            </p>

            <!-- Name: clamped, never flex-grows -->
            <Link
                :href="route('products.show', product.slug)"
                class="text-sm font-medium text-gray-800 hover:text-indigo-600 transition leading-snug line-clamp-2 mb-2"
            >
                {{ product.name }}
            </Link>

            <!-- Spacer pushes price + button to bottom -->
            <div class="flex-1"></div>

            <!-- Price -->
            <div class="flex items-baseline gap-2 mb-2">
                <span class="text-sm font-bold text-red-600">৳{{ fmt(product.effective_price) }}</span>
                <span v-if="product.sale_price" class="text-xs text-gray-400 line-through">৳{{ fmt(product.price) }}</span>
            </div>

            <!-- Add to Cart -->
            <button
                v-if="product.in_stock"
                @click="addToCart"
                :disabled="adding"
                class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-xs font-semibold py-2 rounded-lg transition flex items-center justify-center gap-1.5 flex-shrink-0"
            >
                <svg v-if="!adding" class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <svg v-else class="w-3.5 h-3.5 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ adding ? 'Adding...' : 'Add to Cart' }}
            </button>
            <div v-else class="w-full bg-gray-100 text-gray-400 text-xs font-semibold py-2 rounded-lg text-center flex-shrink-0">
                Out of Stock
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useCartDrawer } from '@/Composables/useCartDrawer';

const props = defineProps({ product: { type: Object, required: true } });
const adding = ref(false);
const { open: openCart } = useCartDrawer();

const discountPercent = computed(() => {
    if (!props.product.sale_price) return 0;
    return Math.round(((props.product.price - props.product.sale_price) / props.product.price) * 100);
});

function fmt(val) { return Number(val).toLocaleString('en-BD'); }

function addToCart() {
    adding.value = true;
    router.post(route('cart.add'), { product_id: props.product.id, quantity: 1 }, {
        preserveScroll: true,
        onSuccess: () => {},
        onFinish: () => { adding.value = false; },
    });
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
