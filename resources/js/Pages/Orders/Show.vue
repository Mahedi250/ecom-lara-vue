<template>
    <StorefrontLayout :title="`Order ${order.order_number}`">
        <div class="bg-gray-50 min-h-screen py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Success banner -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-10 text-center">
                        <!-- Animated checkmark -->
                        <div class="mx-auto w-20 h-20 bg-white/20 backdrop-blur rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-white mb-1">Order Confirmed!</h1>
                        <p class="text-indigo-200 text-sm">Thank you for shopping with us</p>
                    </div>

                    <div class="px-6 py-5 grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-100 text-center">
                        <div class="px-4 py-2">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Order #</p>
                            <p class="font-bold text-gray-900 text-sm">{{ order.order_number }}</p>
                        </div>
                        <div class="px-4 py-2">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Status</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                :class="statusClass">
                                {{ order.status_label }}
                            </span>
                        </div>
                        <div class="px-4 py-2">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Payment</p>
                            <p class="text-sm font-semibold text-gray-800">Cash on Delivery</p>
                        </div>
                        <div class="px-4 py-2">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total</p>
                            <p class="font-bold text-indigo-700 text-sm">৳{{ formatPrice(order.total) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Email notice -->
                <div v-if="order.shipping_address?.email" class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-blue-700">
                        Confirmation details sent to <strong>{{ order.shipping_address.email }}</strong>
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    <!-- Order Items -->
                    <div class="sm:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h2 class="font-bold text-gray-900">Items Ordered</h2>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="flex gap-4 px-5 py-4"
                            >
                                <!-- Image -->
                                <div class="w-14 h-14 flex-shrink-0 rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                                    <img
                                        v-if="item.product_thumbnail"
                                        :src="item.product_thumbnail"
                                        :alt="item.product_name"
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
                                    <p class="font-semibold text-gray-800 text-sm leading-snug">{{ item.product_name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">SKU: {{ item.product_sku }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ item.quantity }} × ৳{{ formatPrice(item.unit_price) }}
                                    </p>
                                </div>

                                <!-- Subtotal -->
                                <div class="text-right flex-shrink-0">
                                    <p class="font-bold text-gray-900 text-sm">৳{{ formatPrice(item.total_price) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Totals -->
                        <div class="border-t border-gray-100 px-5 py-4 space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium">৳{{ formatPrice(order.subtotal) }}</span>
                            </div>
                            <div v-if="order.discount > 0" class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span class="font-medium">-৳{{ formatPrice(order.discount) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span :class="order.shipping_cost == 0 ? 'text-green-600 font-medium' : 'font-medium'">
                                    {{ order.shipping_cost == 0 ? 'FREE' : '৳' + formatPrice(order.shipping_cost) }}
                                </span>
                            </div>
                            <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-100">
                                <span class="text-base">Total Paid</span>
                                <span class="text-indigo-700 text-lg">৳{{ formatPrice(order.total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Shipping To
                        </h2>
                        <div v-if="order.shipping_address" class="space-y-1 text-sm text-gray-600">
                            <p class="font-semibold text-gray-800">{{ order.shipping_address.name }}</p>
                            <p v-if="order.shipping_address.phone">{{ order.shipping_address.phone }}</p>
                            <p>{{ order.shipping_address.address_line1 }}</p>
                            <p v-if="order.shipping_address.address_line2">{{ order.shipping_address.address_line2 }}</p>
                            <p>{{ order.shipping_address.city }}<span v-if="order.shipping_address.state">, {{ order.shipping_address.state }}</span></p>
                            <p v-if="order.shipping_address.postal_code">{{ order.shipping_address.postal_code }}</p>
                        </div>
                        <div v-else class="text-sm text-gray-400">No address on record</div>
                    </div>

                    <!-- Delivery Timeline -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            What's Next?
                        </h2>
                        <div class="space-y-3">
                            <div class="flex gap-3">
                                <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Order Placed</p>
                                    <p class="text-xs text-gray-400">{{ formatDate(order.created_at) }}</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="w-7 h-7 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Processing</p>
                                    <p class="text-xs text-gray-400">We'll confirm your order soon</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-400">Out for Delivery</p>
                                    <p class="text-xs text-gray-400">1–3 business days</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-400">Delivered</p>
                                    <p class="text-xs text-gray-400">Pay on delivery (COD)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <Link
                        href="/products"
                        class="flex-1 text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Continue Shopping
                    </Link>
                    <Link
                        v-if="isAuth"
                        :href="route('orders.index')"
                        class="flex-1 text-center border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        View All Orders
                    </Link>
                </div>

            </div>
        </div>
    </StorefrontLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const props = defineProps({
    order: { type: Object, required: true },
});

const page = usePage();
const isAuth = computed(() => !!page.props.auth?.user);

const statusClass = computed(() => {
    const map = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        shipped: 'bg-indigo-100 text-indigo-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
        refunded: 'bg-gray-100 text-gray-800',
    };
    return map[props.order.status] ?? 'bg-gray-100 text-gray-800';
});

function formatPrice(val) {
    return Number(val).toLocaleString('en-BD');
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-BD', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}
</script>
