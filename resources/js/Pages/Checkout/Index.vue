<template>
    <StorefrontLayout title="Checkout">
        <div class="bg-gray-50 min-h-screen py-8">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
                    <Link href="/" class="hover:text-indigo-600">Home</Link>
                    <span>/</span>
                    <Link :href="route('cart.index')" class="hover:text-indigo-600">Cart</Link>
                    <span>/</span>
                    <span class="text-gray-800 font-medium">Checkout</span>
                </nav>

                <!-- Steps indicator -->
                <div class="flex items-center gap-0 mb-8">
                    <div class="flex items-center gap-2 text-indigo-600 font-semibold text-sm">
                        <div class="w-7 h-7 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</div>
                        Shipping
                    </div>
                    <div class="flex-1 h-px bg-gray-300 mx-3"></div>
                    <div class="flex items-center gap-2 text-gray-400 text-sm">
                        <div class="w-7 h-7 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center text-xs font-bold">2</div>
                        Payment
                    </div>
                    <div class="flex-1 h-px bg-gray-300 mx-3"></div>
                    <div class="flex items-center gap-2 text-gray-400 text-sm">
                        <div class="w-7 h-7 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center text-xs font-bold">3</div>
                        Confirmation
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- LEFT: Form -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Guest notice -->
                        <div v-if="!auth" class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-700">
                                Ordering as guest.
                                <Link :href="route('login')" class="font-semibold underline">Sign in</Link>
                                to track all your orders easily.
                            </div>
                        </div>

                        <!-- Shipping address -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h2 class="font-bold text-gray-900 text-lg mb-5 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Shipping Address
                            </h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Full name -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.shipping_address.name"
                                        type="text"
                                        placeholder="Maruf Hasan"
                                        class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                        :class="errors['shipping_address.name'] ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                                    />
                                    <p v-if="errors['shipping_address.name']" class="text-red-500 text-xs mt-1">{{ errors['shipping_address.name'] }}</p>
                                </div>

                                <!-- Email (required for guest) -->
                                <div :class="!auth ? '' : 'sm:col-span-1'">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Email <span v-if="!auth" class="text-red-500">*</span>
                                        <span v-else class="text-gray-400 font-normal">(optional)</span>
                                    </label>
                                    <input
                                        v-model="form.shipping_address.email"
                                        type="email"
                                        placeholder="you@example.com"
                                        class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                        :class="errors['shipping_address.email'] ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                                    />
                                    <p v-if="errors['shipping_address.email']" class="text-red-500 text-xs mt-1">{{ errors['shipping_address.email'] }}</p>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.shipping_address.phone"
                                        type="tel"
                                        placeholder="01XXXXXXXXX"
                                        class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                        :class="errors['shipping_address.phone'] ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                                    />
                                    <p v-if="errors['shipping_address.phone']" class="text-red-500 text-xs mt-1">{{ errors['shipping_address.phone'] }}</p>
                                </div>

                                <!-- Address Line 1 -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Street Address <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.shipping_address.address_line1"
                                        type="text"
                                        placeholder="House #, Road #, Area"
                                        class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                        :class="errors['shipping_address.address_line1'] ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                                    />
                                    <p v-if="errors['shipping_address.address_line1']" class="text-red-500 text-xs mt-1">{{ errors['shipping_address.address_line1'] }}</p>
                                </div>

                                <!-- Address Line 2 -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Apartment / Floor <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <input
                                        v-model="form.shipping_address.address_line2"
                                        type="text"
                                        placeholder="Apartment, floor, etc."
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                    />
                                </div>

                                <!-- City -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.shipping_address.city"
                                        type="text"
                                        placeholder="Dhaka"
                                        class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                        :class="errors['shipping_address.city'] ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                                    />
                                    <p v-if="errors['shipping_address.city']" class="text-red-500 text-xs mt-1">{{ errors['shipping_address.city'] }}</p>
                                </div>

                                <!-- State / Division -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Division <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <select
                                        v-model="form.shipping_address.state"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent bg-white"
                                    >
                                        <option value="">Select Division</option>
                                        <option v-for="div in divisions" :key="div" :value="div">{{ div }}</option>
                                    </select>
                                </div>

                                <!-- Postal Code -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <input
                                        v-model="form.shipping_address.postal_code"
                                        type="text"
                                        placeholder="1207"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                                    />
                                </div>

                                <!-- Country -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                    <select
                                        v-model="form.shipping_address.country"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent bg-white"
                                    >
                                        <option value="BD">Bangladesh</option>
                                        <option value="IN">India</option>
                                        <option value="US">United States</option>
                                        <option value="GB">United Kingdom</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h2 class="font-bold text-gray-900 text-lg mb-5 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Payment Method
                            </h2>

                            <div class="space-y-3">
                                <!-- COD -->
                                <label class="flex items-center gap-4 border rounded-xl px-4 py-3.5 cursor-pointer transition"
                                    :class="form.payment_method === 'cod' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" v-model="form.payment_method" value="cod" class="text-indigo-600 w-4 h-4" />
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">Cash on Delivery</p>
                                            <p class="text-xs text-gray-500">Pay when your order arrives</p>
                                        </div>
                                    </div>
                                    <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">Available</span>
                                </label>

                                <!-- bKash (disabled) -->
                                <label class="flex items-center gap-4 border border-gray-100 rounded-xl px-4 py-3.5 opacity-50 cursor-not-allowed bg-gray-50">
                                    <input type="radio" disabled class="w-4 h-4" />
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                            <span class="text-pink-600 font-bold text-xs">bK</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">bKash</p>
                                            <p class="text-xs text-gray-500">Mobile banking</p>
                                        </div>
                                    </div>
                                    <span class="text-xs bg-gray-200 text-gray-500 font-semibold px-2 py-0.5 rounded-full">Coming Soon</span>
                                </label>

                                <!-- Card (disabled) -->
                                <label class="flex items-center gap-4 border border-gray-100 rounded-xl px-4 py-3.5 opacity-50 cursor-not-allowed bg-gray-50">
                                    <input type="radio" disabled class="w-4 h-4" />
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">Debit / Credit Card</p>
                                            <p class="text-xs text-gray-500">Visa, Mastercard, Amex</p>
                                        </div>
                                    </div>
                                    <span class="text-xs bg-gray-200 text-gray-500 font-semibold px-2 py-0.5 rounded-full">Coming Soon</span>
                                </label>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h2 class="font-bold text-gray-900 text-lg mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Order Notes <span class="text-gray-400 font-normal text-sm">(optional)</span>
                            </h2>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                placeholder="Special instructions for delivery, preferred time, etc."
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none"
                            ></textarea>
                        </div>

                    </div>

                    <!-- RIGHT: Order Summary -->
                    <div class="space-y-4">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-24">
                            <h3 class="font-bold text-gray-900 text-base mb-4">Order Summary</h3>

                            <!-- Items -->
                            <div class="space-y-3 mb-4 max-h-64 overflow-y-auto pr-1">
                                <div
                                    v-for="item in cartItems"
                                    :key="item.id"
                                    class="flex gap-3"
                                >
                                    <div class="w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 border border-gray-100">
                                        <img
                                            v-if="item.product?.thumbnail"
                                            :src="item.product.thumbnail"
                                            :alt="item.product?.name"
                                            class="w-full h-full object-cover"
                                        />
                                        <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 line-clamp-2 leading-snug">{{ item.product?.name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Qty: {{ item.quantity }}</p>
                                    </div>
                                    <p class="text-sm font-bold text-gray-800 flex-shrink-0">৳{{ formatPrice(item.subtotal) }}</p>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                                <!-- Coupon -->
                                <div class="flex gap-2 mb-3">
                                    <input
                                        v-model="couponCode"
                                        type="text"
                                        placeholder="Coupon code"
                                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300 uppercase"
                                    />
                                    <button
                                        @click="applyCoupon"
                                        class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-3 py-2 rounded-lg transition"
                                    >Apply</button>
                                </div>

                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
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
                                <div class="border-t border-gray-100 pt-2 flex justify-between font-bold text-gray-900">
                                    <span class="text-base">Total</span>
                                    <span class="text-indigo-700 text-xl">৳{{ formatPrice(total) }}</span>
                                </div>
                            </div>

                            <!-- Place Order -->
                            <button
                                @click="placeOrder"
                                :disabled="submitting || cartItems.length === 0"
                                class="mt-5 w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold py-3.5 rounded-xl flex items-center justify-center gap-2 transition text-base"
                            >
                                <svg v-if="submitting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ submitting ? 'Placing Order...' : 'Place Order' }}
                            </button>

                            <p class="text-xs text-gray-400 text-center mt-3">
                                By placing your order you agree to our
                                <span class="text-indigo-500 cursor-pointer">Terms & Conditions</span>
                            </p>

                            <!-- Trust badges -->
                            <div class="mt-4 flex items-center justify-center gap-4 text-xs text-gray-400">
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
                                    7-Day Return
                                </div>
                            </div>
                        </div>

                        <!-- Back to cart -->
                        <Link :href="route('cart.index')" class="flex items-center justify-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition">
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
const errors = computed(() => page.props.errors ?? {});

const divisions = [
    'Dhaka', 'Chittagong', 'Rajshahi', 'Khulna',
    'Barisal', 'Sylhet', 'Rangpur', 'Mymensingh',
];

const couponCode = ref('');
const discount = ref(0);
const submitting = ref(false);

const form = useForm({
    shipping_address: {
        name: auth?.name ?? '',
        email: auth?.email ?? '',
        phone: '',
        address_line1: '',
        address_line2: '',
        city: '',
        state: '',
        postal_code: '',
        country: 'BD',
    },
    payment_method: 'cod',
    coupon_code: '',
    notes: '',
    shipping_cost: 0,
    tax: 0,
});

const cartItems = computed(() => props.cart?.items ?? []);

const subtotal = computed(() =>
    cartItems.value.reduce((sum, item) => sum + item.subtotal, 0)
);

const shippingSettings = page.props.shipping_settings ?? { default_cost: 120, free_threshold: 2000 };
const freeShippingThreshold = shippingSettings.free_threshold;
const shippingRate = shippingSettings.default_cost;

const shipping = computed(() => {
    form.shipping_cost = subtotal.value >= freeShippingThreshold ? 0 : shippingRate;
    return form.shipping_cost;
});

const total = computed(() => subtotal.value - discount.value + shipping.value);

function formatPrice(val) {
    return Number(val).toLocaleString('en-BD');
}

function applyCoupon() {
    // placeholder – wire to real coupon endpoint when ready
}

function placeOrder() {
    form.coupon_code = couponCode.value;
    form.shipping_cost = shipping.value;
    form.post(route('orders.store'), {
        onStart: () => { submitting.value = true; },
        onFinish: () => { submitting.value = false; },
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
