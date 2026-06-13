<template>
    <Teleport to="body">
        <!-- Backdrop -->
        <Transition name="fade">
            <div
                v-if="isOpen"
                class="fixed inset-0 bg-black/50 z-[60] backdrop-blur-sm"
                @click="close"
            />
        </Transition>

        <!-- Drawer -->
        <Transition name="slide-right">
            <div
                v-if="isOpen"
                class="fixed top-0 right-0 h-full w-full max-w-md bg-white z-[61] shadow-2xl flex flex-col"
            >
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900 text-base">Your Cart</h2>
                            <p class="text-xs text-gray-500">{{ cart ? cart.item_count : 0 }} {{ (cart?.item_count ?? 0) === 1 ? 'item' : 'items' }}</p>
                        </div>
                    </div>
                    <button
                        @click="close"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-800 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-8 h-8 animate-spin text-indigo-400 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <p class="text-sm text-gray-400 mt-2">Loading cart...</p>
                    </div>
                </div>

                <!-- Empty -->
                <div v-else-if="!cart || !cart.items || cart.items.length === 0" class="flex-1 flex flex-col items-center justify-center px-6 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-1">Cart is empty</h3>
                    <p class="text-sm text-gray-400 mb-6">Add some products to get started</p>
                    <button
                        @click="goShop"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl transition text-sm"
                    >
                        Browse Products
                    </button>
                </div>

                <!-- Items -->
                <div v-else class="flex-1 overflow-y-auto">
                    <!-- Free shipping bar -->
                    <div
                        v-if="subtotal < freeShippingThreshold"
                        class="mx-4 mt-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5"
                    >
                        <p class="text-xs text-amber-700">
                            Add <strong>৳{{ formatPrice(freeShippingThreshold - subtotal) }}</strong> more for FREE shipping!
                        </p>
                        <div class="mt-1.5 bg-amber-200 rounded-full h-1">
                            <div
                                class="bg-amber-500 h-1 rounded-full transition-all duration-500"
                                :style="{ width: Math.min((subtotal / freeShippingThreshold) * 100, 100) + '%' }"
                            />
                        </div>
                    </div>
                    <div v-else class="mx-4 mt-4 bg-green-50 border border-green-200 rounded-xl px-4 py-2.5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-xs text-green-700 font-medium">You qualify for FREE shipping!</p>
                    </div>

                    <!-- Item list -->
                    <div class="divide-y divide-gray-50 px-4 mt-3">
                        <div
                            v-for="item in cart.items"
                            :key="item.id"
                            class="py-4 flex gap-3"
                        >
                            <!-- Image -->
                            <div class="w-16 h-16 flex-shrink-0 rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                                <img
                                    v-if="item.product?.thumbnail"
                                    :src="item.product.thumbnail"
                                    :alt="item.product?.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 line-clamp-2 leading-snug">{{ item.product?.name }}</p>
                                <p v-if="item.variant" class="text-xs text-gray-400 mt-0.5">{{ variantLabel(item.variant) }}</p>
                                <p class="text-sm font-bold text-indigo-700 mt-1">৳{{ formatPrice(item.subtotal) }}</p>

                                <!-- Controls -->
                                <div class="flex items-center gap-2 mt-2">
                                    <!-- Qty stepper -->
                                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                        <button
                                            @click="updateQty(item, item.quantity - 1)"
                                            :disabled="item.quantity <= 1 || updatingId === item.id"
                                            class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition disabled:opacity-40"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center text-sm font-semibold text-gray-800">
                                            <svg v-if="updatingId === item.id" class="w-3 h-3 animate-spin mx-auto text-indigo-500" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                            <span v-else>{{ item.quantity }}</span>
                                        </span>
                                        <button
                                            @click="updateQty(item, item.quantity + 1)"
                                            :disabled="updatingId === item.id"
                                            class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition disabled:opacity-40"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Remove -->
                                    <button
                                        @click="removeItem(item)"
                                        :disabled="removingId === item.id"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition disabled:opacity-40"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer summary -->
                <div v-if="cart && cart.items && cart.items.length > 0" class="border-t border-gray-100 bg-white px-5 py-4 space-y-3">
                    <!-- Totals -->
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-800">৳{{ formatPrice(subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Shipping</span>
                            <span :class="shipping === 0 ? 'text-green-600 font-medium' : 'font-medium text-gray-800'">
                                {{ shipping === 0 ? 'FREE' : '৳' + formatPrice(shipping) }}
                            </span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-900 pt-1.5 border-t border-gray-100">
                            <span>Total</span>
                            <span class="text-indigo-700 text-base">৳{{ formatPrice(total) }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="grid grid-cols-2 gap-2">
                        <Link
                            :href="route('cart.index')"
                            @click="close"
                            class="text-center border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-semibold py-2.5 rounded-xl text-sm transition"
                        >
                            View Cart
                        </Link>
                        <Link
                            :href="checkoutUrl"
                            @click="close"
                            class="text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Checkout
                        </Link>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useCartDrawer } from '@/Composables/useCartDrawer';

const { isOpen, close } = useCartDrawer();

const page = usePage();
const cart = ref(null);
const loading = ref(false);
const updatingId = ref(null);
const removingId = ref(null);

const freeShippingThreshold = 2000;
const shippingRate = 120;

const subtotal = computed(() => {
    if (!cart.value?.items) return 0;
    return cart.value.items.reduce((sum, item) => sum + item.subtotal, 0);
});

const shipping = computed(() => subtotal.value >= freeShippingThreshold ? 0 : shippingRate);
const total = computed(() => subtotal.value + shipping.value);

const checkoutUrl = computed(() => {
    return route('checkout');
});

async function fetchCart() {
    loading.value = true;
    try {
        const res = await fetch(route('cart.data'), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });
        cart.value = await res.json();
    } catch (e) {
        cart.value = null;
    } finally {
        loading.value = false;
    }
}

watch(isOpen, (val) => {
    if (val) {
        fetchCart();
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

function formatPrice(val) {
    return Number(val).toLocaleString('en-BD');
}

function variantLabel(variant) {
    if (!variant) return '';
    return [variant.color, variant.size, variant.storage].filter(Boolean).join(' / ') || variant.name || '';
}

function updateQty(item, qty) {
    if (qty < 1 || updatingId.value === item.id) return;
    updatingId.value = item.id;
    router.patch(route('cart.update', item.id), { quantity: qty }, {
        preserveScroll: true,
        onSuccess: () => fetchCart(),
        onFinish: () => { updatingId.value = null; },
    });
}

function removeItem(item) {
    removingId.value = item.id;
    router.delete(route('cart.remove', item.id), {
        preserveScroll: true,
        onSuccess: () => fetchCart(),
        onFinish: () => { removingId.value = null; },
    });
}

function goShop() {
    close();
    router.visit(route('products.index'));
}
</script>

<style scoped>
.slide-right-enter-active,
.slide-right-leave-active {
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-right-enter-from,
.slide-right-leave-to {
    transform: translateX(100%);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
