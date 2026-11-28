<template>
    <StorefrontLayout title="Checkout">
        <div class="bg-gray-50 min-h-screen">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-10">

                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-400 mb-4">
                    <Link href="/" class="hover:text-indigo-600">Home</Link>
                    <span>/</span>
                    <Link :href="route('cart.index')" class="hover:text-indigo-600">Cart</Link>
                    <span>/</span>
                    <span class="text-gray-700 font-medium">Checkout</span>
                </nav>

                <div class="mb-6">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Checkout</h1>
                    <p class="text-sm text-gray-500 mt-1">Just a few details and your order is on its way.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                    <!-- LEFT: Form -->
                    <div class="lg:col-span-3 space-y-5">

                        <!-- Contact & Delivery -->
                        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
                            <h2 class="font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </span>
                                Delivery Details
                            </h2>

                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        autocomplete="name"
                                        placeholder="Your full name"
                                        class="w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent"
                                        :class="err('name') ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                                    />
                                    <p v-if="err('name')" class="text-red-500 text-xs mt-1">{{ err('name') }}</p>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        inputmode="tel"
                                        autocomplete="tel"
                                        placeholder="01XXXXXXXXX"
                                        class="w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent"
                                        :class="err('phone') ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                                    />
                                    <p v-if="err('phone')" class="text-red-500 text-xs mt-1">{{ err('phone') }}</p>
                                </div>

                                <!-- Address -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Delivery Address <span class="text-red-500">*</span></label>
                                    <textarea
                                        v-model="form.address"
                                        rows="3"
                                        autocomplete="street-address"
                                        placeholder="House / Road / Area, city"
                                        class="w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent resize-none"
                                        :class="err('address_line1') ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                                    ></textarea>
                                    <p v-if="err('address_line1')" class="text-red-500 text-xs mt-1">{{ err('address_line1') }}</p>
                                </div>

                                <p class="flex items-center gap-1.5 text-xs text-gray-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    We'll call you to confirm your order.
                                </p>
                            </div>
                        </section>

                        <!-- Payment Method -->
                        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
                            <h2 class="font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </span>
                                Payment Method
                            </h2>

                            <div class="space-y-3">
                                <!-- Cash on Delivery -->
                                <label
                                    class="flex items-center gap-3 rounded-xl border-2 p-4 cursor-pointer transition"
                                    :class="form.payment_method === 'cod' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'"
                                >
                                    <input type="radio" v-model="form.payment_method" value="cod" class="sr-only" />
                                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm text-gray-800">Cash on Delivery</p>
                                        <p class="text-xs text-gray-500">Pay when your order arrives</p>
                                    </div>
                                    <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                        :class="form.payment_method === 'cod' ? 'border-indigo-500' : 'border-gray-300'">
                                        <span v-if="form.payment_method === 'cod'" class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                    </span>
                                </label>

                                <!-- Collect from Shop -->
                                <label
                                    class="flex items-center gap-3 rounded-xl border-2 p-4 cursor-pointer transition"
                                    :class="form.payment_method === 'pickup' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'"
                                >
                                    <input type="radio" v-model="form.payment_method" value="pickup" class="sr-only" />
                                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l1-3h16l1 3M4 7h16v2a3 3 0 01-3 3H7a3 3 0 01-3-3V7zm1 5v6a2 2 0 002 2h10a2 2 0 002-2v-6M9 22V12h6v10"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-sm text-gray-800">Collect from Shop</p>
                                            <span class="text-[10px] bg-green-100 text-green-700 font-bold px-1.5 py-0.5 rounded-full">FREE</span>
                                        </div>
                                        <p class="text-xs text-gray-500">Pick up from our store — no delivery fee</p>
                                    </div>
                                    <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                        :class="form.payment_method === 'pickup' ? 'border-indigo-500' : 'border-gray-300'">
                                        <span v-if="form.payment_method === 'pickup'" class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                    </span>
                                </label>

                                <!-- Pickup info -->
                                <div v-if="form.payment_method === 'pickup'" class="rounded-xl bg-purple-50 border border-purple-100 px-4 py-3 text-xs text-purple-700">
                                    <p class="font-semibold mb-0.5">Collect from:</p>
                                    <p>{{ storeAddress || 'Our store — we will share the pickup address after confirming your order.' }}</p>
                                </div>

                                <!-- Online Payment (coming soon) -->
                                <label class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 p-4 opacity-60 cursor-not-allowed">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm text-gray-700">Online Payment</p>
                                        <p class="text-xs text-gray-400">bKash, Nagad, Card</p>
                                    </div>
                                    <span class="text-[10px] bg-gray-200 text-gray-500 font-semibold px-2 py-0.5 rounded-full">Coming Soon</span>
                                </label>

                                <p v-if="err('payment_method')" class="text-red-500 text-xs">{{ err('payment_method') }}</p>
                            </div>
                        </section>

                        <!-- Notes -->
                        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Order Note <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="2"
                                placeholder="Any special instruction for delivery?"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent resize-none"
                            ></textarea>
                        </section>

                        <!-- Guest hint -->
                        <p v-if="!auth" class="text-center text-xs text-gray-400">
                            Ordering as guest.
                            <Link :href="route('login')" class="text-indigo-600 font-semibold hover:underline">Sign in</Link>
                            to track your orders.
                        </p>
                    </div>

                    <!-- RIGHT: Order Summary -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 lg:sticky lg:top-24">
                            <h2 class="font-bold text-gray-900 text-base mb-4">Order Summary</h2>

                            <!-- Items -->
                            <div class="space-y-3 mb-4 max-h-64 overflow-y-auto pr-1">
                                <div v-for="item in cartItems" :key="item.id" class="flex items-center gap-3">
                                    <div class="w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 border border-gray-100">
                                        <img v-if="item.product?.thumbnail" :src="item.product.thumbnail" :alt="item.product?.name" class="w-full h-full object-cover" />
                                        <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 leading-snug line-clamp-2">{{ item.product?.name }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Qty: {{ item.quantity }}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800 flex-shrink-0">৳{{ formatPrice(item.subtotal) }}</p>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                                <!-- Coupon -->
                                <div class="pb-1">
                                    <div class="flex gap-2">
                                        <input
                                            v-model="couponCode"
                                            type="text"
                                            placeholder="Coupon code"
                                            class="flex-1 min-w-0 border rounded-lg px-3 py-2 text-xs uppercase tracking-wide focus:outline-none focus:ring-2 focus:ring-indigo-300"
                                            :class="couponError ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                                            @input="couponError = ''"
                                        />
                                        <button
                                            @click="applyCoupon"
                                            :disabled="applying || !couponCode.trim()"
                                            class="text-xs bg-gray-100 hover:bg-gray-200 disabled:opacity-50 text-gray-700 font-semibold px-3.5 py-2 rounded-lg transition"
                                        >{{ applying ? '...' : 'Apply' }}</button>
                                    </div>
                                    <p v-if="couponMessage" class="text-[11px] text-green-600 font-medium mt-1.5">
                                        ✓ {{ couponMessage }} — you save ৳{{ formatPrice(discount) }}
                                    </p>
                                    <p v-else-if="couponError" class="text-[11px] text-red-500 mt-1.5">{{ couponError }}</p>
                                    <p v-if="err('coupon_code')" class="text-[11px] text-red-500 mt-1.5">{{ err('coupon_code') }}</p>
                                </div>

                                <div class="flex justify-between text-gray-500">
                                    <span>Subtotal</span>
                                    <span class="font-medium text-gray-800">৳{{ formatPrice(subtotal) }}</span>
                                </div>
                                <div v-if="discount > 0" class="flex justify-between text-green-600">
                                    <span>Coupon ({{ couponApplied }})</span>
                                    <span class="font-medium">-৳{{ formatPrice(discount) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Shipping</span>
                                    <span :class="shipping === 0 ? 'text-green-600 font-semibold' : 'font-medium text-gray-800'">
                                        {{ shipping === 0 ? 'FREE' : '৳' + formatPrice(shipping) }}
                                    </span>
                                </div>
                                <div class="border-t border-gray-100 pt-3 flex justify-between items-baseline font-bold text-gray-900">
                                    <span class="text-base">Total</span>
                                    <span class="text-indigo-700 text-2xl">৳{{ formatPrice(total) }}</span>
                                </div>
                            </div>

                            <button
                                @click="placeOrder"
                                :disabled="form.processing || cartItems.length === 0"
                                class="mt-5 w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold py-3.5 rounded-xl flex items-center justify-center gap-2 transition text-base"
                            >
                                <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ form.processing ? 'Placing Order...' : 'Place Order' }}
                            </button>

                            <p class="text-[11px] text-gray-400 text-center mt-3">
                                By placing your order you agree to our
                                <span class="text-indigo-500">Terms &amp; Conditions</span>.
                            </p>

                            <div class="mt-4 flex items-center justify-center gap-4 text-xs text-gray-400">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Secure
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    7-Day Return
                                </span>
                            </div>
                        </div>

                        <Link :href="route('cart.index')" class="flex items-center justify-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition mt-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Back to Cart
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </StorefrontLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const props = defineProps({
    cart: { type: Object, required: true },
});

const page = usePage();
const auth = page.props.auth?.user ?? null;

const storeAddress = computed(() => page.props.site_settings?.store_address || '');

const cartItems = computed(() => props.cart?.items ?? []);

const subtotal = computed(() =>
    cartItems.value.reduce((sum, item) => sum + Number(item.subtotal), 0)
);

const shippingSettings = page.props.shipping_settings ?? { default_cost: 120, free_threshold: 2000 };
const freeShippingThreshold = Number(shippingSettings.free_threshold);
const shippingRate = Number(shippingSettings.default_cost);

const form = useForm({
    name: auth?.name ?? '',
    phone: '',
    address: '',
    notes: '',
    payment_method: 'cod',
});

const shipping = computed(() => {
    if (form.payment_method === 'pickup') return 0;
    return subtotal.value >= freeShippingThreshold ? 0 : shippingRate;
});

const total = computed(() => Math.max(0, subtotal.value - discount.value + shipping.value));

// Coupon
const couponCode = ref('');
const couponApplied = ref(null);
const discount = ref(0);
const couponMessage = ref('');
const couponError = ref('');
const applying = ref(false);

async function applyCoupon() {
    const code = couponCode.value.trim();
    if (!code) return;
    applying.value = true;
    couponError.value = '';
    couponMessage.value = '';
    try {
        const { data } = await window.axios.post(route('api.coupons.validate'), {
            code,
            order_total: subtotal.value,
        });
        discount.value = Number(data.discount);
        couponApplied.value = data.coupon.code;
        couponMessage.value = data.coupon.name ? `"${data.coupon.name}" applied` : 'Coupon applied';
    } catch (e) {
        discount.value = 0;
        couponApplied.value = null;
        couponError.value = e.response?.data?.message ?? 'Coupon could not be applied.';
    } finally {
        applying.value = false;
    }
}

function err(key) {
    return form.errors[key] || form.errors[`shipping_address.${key}`] || '';
}

function formatPrice(val) {
    return Number(val).toLocaleString('en-BD');
}

async function placeOrder() {
    // Auto-apply a typed-but-not-applied coupon so the shown total matches the server.
    const code = couponCode.value.trim();
    if (code && couponApplied.value !== code) {
        await applyCoupon();
    }
    form.transform((data) => ({
        shipping_address: {
            name: data.name,
            phone: data.phone,
            address_line1: data.address,
            city: 'N/A',
            country: 'BD',
        },
        payment_method: data.payment_method,
        coupon_code: couponApplied.value,
        notes: data.notes || null,
        shipping_cost: shipping.value,
        tax: 0,
    })).post(route('orders.store'), {
        preserveScroll: true,
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
