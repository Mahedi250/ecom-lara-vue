<template>
    <StorefrontLayout title="Your Cart">
        <div class="bg-gray-50 min-h-screen py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
                    <Link href="/" class="hover:text-indigo-600">Home</Link>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-800 font-medium">Shopping Cart</span>
                </nav>

                <!-- Empty Cart -->
                <div v-if="!cart.items || cart.items.length === 0" class="text-center py-24">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Your cart is empty</h2>
                    <p class="text-gray-500 mb-8">Looks like you haven't added anything yet.</p>
                    <Link
                        href="/products"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-xl transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Continue Shopping
                    </Link>
                </div>

                <!-- Cart Content -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Left: Items -->
                    <div class="lg:col-span-2 space-y-4">

                        <!-- Header row -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                                <h1 class="text-xl font-bold text-gray-900">
                                    Shopping Cart
                                    <span class="text-sm font-normal text-gray-500 ml-2">({{ cart.item_count }} {{ cart.item_count === 1 ? 'item' : 'items' }})</span>
                                </h1>
                                <button
                                    @click="clearCart"
                                    :disabled="clearing"
                                    class="text-sm text-red-500 hover:text-red-700 font-medium transition disabled:opacity-50"
                                >
                                    {{ clearing ? 'Clearing...' : 'Clear all' }}
                                </button>
                            </div>

                            <!-- Cart Items -->
                            <div class="divide-y divide-gray-50">
                                <div
                                    v-for="item in cart.items"
                                    :key="item.id"
                                    class="flex gap-4 p-6 hover:bg-gray-50/50 transition"
                                >
                                    <!-- Product Image -->
                                    <Link
                                        :href="item.product ? route('products.show', item.product.slug) : '#'"
                                        class="flex-shrink-0"
                                    >
                                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                                            <img
                                                v-if="item.product?.thumbnail"
                                                :src="item.product.thumbnail"
                                                :alt="item.product?.name"
                                                class="w-full h-full object-cover"
                                            />
                                            <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </Link>

                                    <!-- Product Info -->
                                    <div class="flex-1 min-w-0">
                                        <Link
                                            :href="item.product ? route('products.show', item.product.slug) : '#'"
                                            class="font-semibold text-gray-800 hover:text-indigo-600 transition line-clamp-2 block"
                                        >
                                            {{ item.product?.name }}
                                        </Link>

                                        <!-- Variant -->
                                        <p v-if="item.variant" class="text-sm text-gray-500 mt-0.5">
                                            {{ variantLabel(item.variant) }}
                                        </p>

                                        <!-- Category -->
                                        <p v-if="item.product?.category" class="text-xs text-indigo-500 uppercase tracking-wide mt-1">
                                            {{ item.product.category.name }}
                                        </p>

                                        <!-- Mobile price -->
                                        <p class="text-indigo-700 font-bold mt-2 sm:hidden">
                                            ৳{{ formatPrice(item.subtotal) }}
                                        </p>

                                        <!-- Quantity + Remove row -->
                                        <div class="flex items-center gap-4 mt-3">
                                            <!-- Quantity stepper -->
                                            <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                                <button
                                                    @click="updateQuantity(item, item.quantity - 1)"
                                                    :disabled="item.quantity <= 1 || updatingId === item.id"
                                                    class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 transition disabled:opacity-40 disabled:cursor-not-allowed"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                <span class="px-4 py-1.5 text-sm font-semibold text-gray-800 min-w-[2.5rem] text-center bg-white">
                                                    <svg v-if="updatingId === item.id" class="w-3.5 h-3.5 animate-spin mx-auto text-indigo-600" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                    </svg>
                                                    <span v-else>{{ item.quantity }}</span>
                                                </span>
                                                <button
                                                    @click="updateQuantity(item, item.quantity + 1)"
                                                    :disabled="updatingId === item.id"
                                                    class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 transition disabled:opacity-40"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Remove -->
                                            <button
                                                @click="removeItem(item)"
                                                :disabled="removingId === item.id"
                                                class="text-sm text-red-400 hover:text-red-600 transition flex items-center gap-1 disabled:opacity-50"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Desktop subtotal -->
                                    <div class="hidden sm:flex flex-col items-end justify-between flex-shrink-0">
                                        <div class="text-right">
                                            <p class="font-bold text-gray-900 text-lg">৳{{ formatPrice(item.subtotal) }}</p>
                                            <p v-if="item.quantity > 1" class="text-xs text-gray-400">৳{{ formatPrice(item.price) }} each</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Continue Shopping -->
                        <Link
                            href="/products"
                            class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium text-sm transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Continue Shopping
                        </Link>
                    </div>

                    <!-- Right: Summary -->
                    <div class="space-y-4">

                        <!-- Coupon -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Coupon Code
                            </h3>
                            <div class="flex gap-2">
                                <input
                                    v-model="couponCode"
                                    type="text"
                                    placeholder="Enter coupon code"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 uppercase"
                                    @keyup.enter="applyCoupon"
                                />
                                <button
                                    @click="applyCoupon"
                                    :disabled="applyingCoupon || !couponCode.trim()"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition disabled:opacity-50"
                                >
                                    {{ applyingCoupon ? '...' : 'Apply' }}
                                </button>
                            </div>
                            <p v-if="couponError" class="text-red-500 text-xs mt-2">{{ couponError }}</p>
                            <p v-if="couponSuccess" class="text-green-600 text-xs mt-2 font-medium">{{ couponSuccess }}</p>
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                            <h3 class="font-bold text-gray-900 text-lg mb-5">Order Summary</h3>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal ({{ cart.item_count }} {{ cart.item_count === 1 ? 'item' : 'items' }})</span>
                                    <span class="font-medium text-gray-800">৳{{ formatPrice(subtotal) }}</span>
                                </div>

                                <div class="flex justify-between text-gray-600">
                                    <span>Shipping</span>
                                    <span :class="shipping === 0 ? 'text-green-600 font-medium' : 'font-medium text-gray-800'">
                                        {{ shipping === 0 ? 'FREE' : '৳' + formatPrice(shipping) }}
                                    </span>
                                </div>

                                <div v-if="discount > 0" class="flex justify-between text-green-600">
                                    <span>Discount</span>
                                    <span class="font-medium">-৳{{ formatPrice(discount) }}</span>
                                </div>

                                <div class="border-t border-gray-100 pt-3 flex justify-between">
                                    <span class="font-bold text-gray-900 text-base">Total</span>
                                    <span class="font-bold text-indigo-700 text-xl">৳{{ formatPrice(total) }}</span>
                                </div>
                            </div>

                            <!-- Free shipping notice -->
                            <div v-if="subtotal < freeShippingThreshold" class="mt-4 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2.5">
                                <p class="text-xs text-amber-700">
                                    Add <strong>৳{{ formatPrice(freeShippingThreshold - subtotal) }}</strong> more for FREE shipping!
                                </p>
                                <div class="mt-1.5 bg-amber-200 rounded-full h-1.5">
                                    <div
                                        class="bg-amber-500 h-1.5 rounded-full transition-all"
                                        :style="{ width: Math.min((subtotal / freeShippingThreshold) * 100, 100) + '%' }"
                                    ></div>
                                </div>
                            </div>
                            <div v-else class="mt-4 bg-green-50 border border-green-200 rounded-lg px-3 py-2.5 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <p class="text-xs text-green-700 font-medium">You qualify for FREE shipping!</p>
                            </div>

                            <!-- Checkout Button -->
                            <Link
                                :href="checkoutUrl"
                                class="mt-5 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl flex items-center justify-center gap-2 transition text-base"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Proceed to Checkout
                            </Link>

                            <!-- Trust badges -->
                            <div class="mt-5 flex items-center justify-center gap-4 text-xs text-gray-400">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Secure
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    Easy Returns
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    All Cards
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Flash Toast -->
        <Transition name="slide-up">
            <div
                v-if="toast"
                class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 max-w-xs"
            >
                <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm">{{ toast }}</span>
            </div>
        </Transition>
    </StorefrontLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const props = defineProps({
    cart: {
        type: Object,
        required: true,
    },
});

const page = usePage();

const updatingId = ref(null);
const removingId = ref(null);
const clearing = ref(false);
const couponCode = ref('');
const applyingCoupon = ref(false);
const couponError = ref('');
const couponSuccess = ref('');
const toast = ref('');

const shippingSettings = page.props.shipping_settings ?? { default_cost: 120, free_threshold: 2000 };
const freeShippingThreshold = shippingSettings.free_threshold;
const shippingRate = shippingSettings.default_cost;

const subtotal = computed(() => {
    if (!props.cart.items) return 0;
    return props.cart.items.reduce((sum, item) => sum + item.subtotal, 0);
});

const discount = ref(0);

const shipping = computed(() => {
    return subtotal.value >= freeShippingThreshold ? 0 : shippingRate;
});

const total = computed(() => subtotal.value - discount.value + shipping.value);

const auth = computed(() => page.props.auth?.user ?? null);
const guestCheckoutEnabled = computed(() => page.props.guest_checkout_enabled !== false);

const checkoutUrl = computed(() => {
    if (!auth.value && !guestCheckoutEnabled.value) {
        return route('login');
    }
    return route('checkout');
});

function formatPrice(val) {
    return Number(val).toLocaleString('en-BD');
}

function variantLabel(variant) {
    if (!variant) return '';
    const parts = [];
    if (variant.color) parts.push(variant.color);
    if (variant.size) parts.push(variant.size);
    if (variant.storage) parts.push(variant.storage);
    return parts.length ? parts.join(' / ') : (variant.name || '');
}

function showToast(msg) {
    toast.value = msg;
    setTimeout(() => { toast.value = ''; }, 3000);
}

function updateQuantity(item, qty) {
    if (qty < 1 || updatingId.value === item.id) return;
    updatingId.value = item.id;
    router.patch(route('cart.update', item.id), { quantity: qty }, {
        preserveScroll: true,
        onSuccess: () => showToast('Cart updated'),
        onFinish: () => { updatingId.value = null; },
    });
}

function removeItem(item) {
    removingId.value = item.id;
    router.delete(route('cart.remove', item.id), {
        preserveScroll: true,
        onSuccess: () => showToast('Item removed'),
        onFinish: () => { removingId.value = null; },
    });
}

function clearCart() {
    clearing.value = true;
    router.delete(route('cart.clear'), {
        preserveScroll: true,
        onSuccess: () => showToast('Cart cleared'),
        onFinish: () => { clearing.value = false; },
    });
}

function applyCoupon() {
    if (!couponCode.value.trim() || applyingCoupon.value) return;
    couponError.value = '';
    couponSuccess.value = '';
    applyingCoupon.value = true;
    setTimeout(() => {
        couponError.value = 'Invalid or expired coupon code.';
        applyingCoupon.value = false;
    }, 600);
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.3s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
    opacity: 0;
    transform: translateY(1rem);
}
</style>
