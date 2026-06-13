<template>
    <AdminLayout :title="`Order ${order.order_number}`" subtitle="Order details">
        <template #header-actions>
            <Link :href="route('admin.orders.index')" class="text-sm text-gray-500 hover:text-gray-800 border border-gray-200 px-4 py-2 rounded-xl transition">← Back</Link>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left: Items + Timeline -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Items -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-900">Order Items</h2>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-for="item in order.items" :key="item.id" class="flex gap-4 px-5 py-4">
                            <div class="w-12 h-12 flex-shrink-0 rounded-xl overflow-hidden bg-gray-100">
                                <img v-if="item.product_thumbnail" :src="item.product_thumbnail" :alt="item.product_name" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm">{{ item.product_name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">SKU: {{ item.product_sku }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ item.quantity }} × ৳{{ fmt(item.unit_price) }}</p>
                            </div>
                            <p class="font-bold text-gray-900 text-sm flex-shrink-0">৳{{ fmt(item.total_price) }}</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 px-5 py-4 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-500"><span>Subtotal</span><span>৳{{ fmt(order.subtotal) }}</span></div>
                        <div v-if="order.discount > 0" class="flex justify-between text-green-600"><span>Discount</span><span>-৳{{ fmt(order.discount) }}</span></div>
                        <div class="flex justify-between text-gray-500"><span>Shipping</span><span>{{ order.shipping_cost == 0 ? 'FREE' : '৳' + fmt(order.shipping_cost) }}</span></div>
                        <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-100 text-base"><span>Total</span><span class="text-indigo-700">৳{{ fmt(order.total) }}</span></div>
                    </div>
                </div>

                <!-- Notes -->
                <div v-if="order.notes" class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4">
                    <p class="text-sm font-semibold text-amber-800 mb-1">Order Notes</p>
                    <p class="text-sm text-amber-700">{{ order.notes }}</p>
                </div>
            </div>

            <!-- Right: Status + Customer + Address -->
            <div class="space-y-5">

                <!-- Update Status -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h2 class="font-semibold text-gray-900 mb-4">Update Status</h2>
                    <div class="mb-4">
                        <span class="text-sm font-semibold px-3 py-1.5 rounded-full" :class="statusClass(order.status)">
                            Current: {{ order.status_label }}
                        </span>
                    </div>
                    <select v-model="newStatus" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white mb-3">
                        <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                    <button @click="updateStatus" :disabled="updating || newStatus === order.status" class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                        {{ updating ? 'Updating...' : 'Update Status' }}
                    </button>
                </div>

                <!-- Order Info -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                    <h2 class="font-semibold text-gray-900 mb-3">Order Info</h2>
                    <div class="text-sm space-y-2">
                        <div class="flex justify-between"><span class="text-gray-500">Order #</span><span class="font-medium text-gray-800">{{ order.order_number }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Payment</span><span class="font-medium text-gray-800">{{ order.payment_status_label }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Date</span><span class="font-medium text-gray-800 text-xs">{{ fmtDate(order.created_at) }}</span></div>
                        <div v-if="order.coupon_code" class="flex justify-between"><span class="text-gray-500">Coupon</span><span class="font-medium text-indigo-600">{{ order.coupon_code }}</span></div>
                    </div>
                </div>

                <!-- Customer -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h2 class="font-semibold text-gray-900 mb-3">Customer</h2>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold text-sm">
                            {{ (order.user?.name ?? 'G')[0].toUpperCase() }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ order.user?.name ?? 'Guest' }}</p>
                            <p class="text-xs text-gray-400">{{ order.user?.email ?? 'No account' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div v-if="order.shipping_address" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h2 class="font-semibold text-gray-900 mb-3">Shipping Address</h2>
                    <div class="text-sm text-gray-600 space-y-0.5">
                        <p class="font-semibold text-gray-800">{{ order.shipping_address.name }}</p>
                        <p v-if="order.shipping_address.phone">{{ order.shipping_address.phone }}</p>
                        <p v-if="order.shipping_address.email" class="text-xs text-gray-400">{{ order.shipping_address.email }}</p>
                        <p>{{ order.shipping_address.address_line1 }}</p>
                        <p v-if="order.shipping_address.address_line2">{{ order.shipping_address.address_line2 }}</p>
                        <p>{{ order.shipping_address.city }}<span v-if="order.shipping_address.state">, {{ order.shipping_address.state }}</span></p>
                        <p v-if="order.shipping_address.postal_code">{{ order.shipping_address.postal_code }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ order: Object, statuses: Array });
const newStatus = ref(props.order.status);
const updating = ref(false);

function fmt(v) { return Number(v).toLocaleString('en-BD'); }
function fmtDate(d) { return d ? new Date(d).toLocaleString('en-BD', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : ''; }
function statusClass(s) {
    const m = { pending: 'bg-amber-100 text-amber-700', processing: 'bg-blue-100 text-blue-700', shipped: 'bg-violet-100 text-violet-700', delivered: 'bg-green-100 text-green-700', cancelled: 'bg-red-100 text-red-700', refunded: 'bg-gray-100 text-gray-600' };
    return m[s] ?? 'bg-gray-100 text-gray-600';
}
function updateStatus() {
    updating.value = true;
    router.patch(route('admin.orders.status', props.order.id), { status: newStatus.value }, {
        onFinish: () => { updating.value = false; },
    });
}
</script>
