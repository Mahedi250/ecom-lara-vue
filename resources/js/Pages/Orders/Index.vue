<template>
    <Head title="My Orders" />

    <CustomerLayout>
        <!-- Header -->
        <div class="flex items-center gap-3 mb-4">
            <div class="flex-1">
                <h1 class="text-lg font-bold text-gray-900">My Orders</h1>
                <p class="text-xs text-gray-500">Track and manage your purchases</p>
            </div>
            <div class="bg-indigo-600 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                {{ allOrders.length }} total
            </div>
        </div>

        <!-- Filter chips -->
        <div class="flex gap-2 overflow-x-auto scrollbar-hide mb-4 pb-1">
            <button
                v-for="tab in tabs" :key="tab.value"
                @click="activeTab = tab.value"
                class="flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition flex-shrink-0 border"
                :class="activeTab === tab.value
                    ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-200'
                    : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'"
            >
                {{ tab.label }}
                <span v-if="tab.count > 0"
                    class="w-4 h-4 rounded-full text-[10px] flex items-center justify-center font-bold"
                    :class="activeTab === tab.value ? 'bg-white/30 text-white' : 'bg-gray-100 text-gray-500'">
                    {{ tab.count }}
                </span>
            </button>
        </div>

        <!-- Empty state -->
        <div v-if="!filteredOrders.length"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 py-16 px-4 text-center">
            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="font-bold text-gray-800 mb-1">No orders found</p>
            <p class="text-sm text-gray-400 mb-5">
                {{ activeTab === 'all' ? "You haven't placed any orders yet." : 'No orders with this status.' }}
            </p>
            <Link :href="route('products.index')"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-6 py-3 rounded-xl transition">
                Start Shopping
            </Link>
        </div>

        <!-- Orders -->
        <div v-else class="space-y-3">
            <Link
                v-for="order in filteredOrders" :key="order.id"
                :href="route('orders.show', order.id)"
                class="block bg-white rounded-2xl border border-gray-100 shadow-sm active:scale-[0.99] hover:shadow-md hover:border-indigo-100 transition-all group"
            >
                <!-- Colored top strip based on status -->
                <div class="h-1 rounded-t-2xl" :style="{ background: order.status_color }"></div>

                <div class="p-4">
                    <!-- Row 1: order# + status + arrow -->
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ order.order_number }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ fmtDate(order.created_at) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full"
                                :style="{ background: order.status_color + '18', color: order.status_color }">
                                <span class="w-1.5 h-1.5 rounded-full inline-block" :style="{ background: order.status_color }"></span>
                                {{ order.status_label }}
                            </span>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Progress stepper -->
                    <div v-if="order.status !== 'cancelled'" class="mb-3">
                        <div class="flex items-center">
                            <template v-for="(step, i) in trackingSteps" :key="step.key">
                                <!-- Circle -->
                                <div class="flex flex-col items-center flex-shrink-0">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center border-2 transition-all"
                                        :class="stepReached(order.status, i)
                                            ? 'border-0'
                                            : 'border-gray-200 bg-white'"
                                        :style="stepReached(order.status, i) ? { background: order.status_color } : {}">
                                        <svg v-if="stepReached(order.status, i)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <div v-else class="w-2 h-2 rounded-full bg-gray-200"></div>
                                    </div>
                                    <span class="text-[9px] font-semibold mt-1 leading-none"
                                        :style="stepReached(order.status, i) ? { color: order.status_color } : {}"
                                        :class="stepReached(order.status, i) ? '' : 'text-gray-300'">
                                        {{ step.label }}
                                    </span>
                                </div>
                                <!-- Line -->
                                <div v-if="i < trackingSteps.length - 1"
                                    class="flex-1 h-0.5 mx-1 rounded-full transition-all"
                                    :style="stepReached(order.status, i + 1) ? { background: order.status_color } : {}"
                                    :class="stepReached(order.status, i + 1) ? '' : 'bg-gray-100'">
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Cancelled message -->
                    <div v-else class="mb-3 flex items-center gap-2 bg-red-50 rounded-xl px-3 py-2">
                        <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <p class="text-xs text-red-600 font-medium">This order was cancelled</p>
                    </div>

                    <!-- Row 3: items count + total -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                        <p class="text-xs text-gray-500">
                            <span class="font-bold text-gray-700">{{ order.items_count }}</span>
                            item{{ order.items_count !== 1 ? 's' : '' }}
                        </p>
                        <div class="text-right">
                            <p class="font-extrabold text-gray-900">৳{{ fmt(order.total) }}</p>
                        </div>
                    </div>
                </div>
            </Link>
        </div>

        <!-- Pagination -->
        <div v-if="orders.links && orders.links.length > 3" class="flex justify-center gap-1.5 mt-6">
            <template v-for="link in orders.links" :key="link.label">
                <Link v-if="link.url" :href="link.url"
                    class="w-9 h-9 flex items-center justify-center text-xs rounded-xl font-semibold transition"
                    :class="link.active ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border border-gray-200 text-gray-600 hover:border-indigo-300'"
                    v-html="link.label" />
                <span v-else class="w-9 h-9 flex items-center justify-center text-xs text-gray-300 bg-white border border-gray-100 rounded-xl" v-html="link.label"/>
            </template>
        </div>
    </CustomerLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';

const props = defineProps({
    orders: { type: Object, required: true },
});

const activeTab = ref('all');
const allOrders = computed(() => props.orders.data ?? []);

const tabs = computed(() => [
    { label: 'All', value: 'all', count: allOrders.value.length },
    { label: 'Pending', value: 'pending', count: allOrders.value.filter(o => o.status === 'pending').length },
    { label: 'Processing', value: 'processing', count: allOrders.value.filter(o => o.status === 'processing').length },
    { label: 'Shipped', value: 'shipped', count: allOrders.value.filter(o => o.status === 'shipped').length },
    { label: 'Delivered', value: 'delivered', count: allOrders.value.filter(o => o.status === 'delivered').length },
    { label: 'Cancelled', value: 'cancelled', count: allOrders.value.filter(o => o.status === 'cancelled').length },
]);

const filteredOrders = computed(() => {
    if (activeTab.value === 'all') return allOrders.value;
    return allOrders.value.filter(o => o.status === activeTab.value);
});

const trackingSteps = [
    { key: 'pending',    label: 'Placed' },
    { key: 'processing', label: 'Confirmed' },
    { key: 'shipped',    label: 'Shipped' },
    { key: 'delivered',  label: 'Delivered' },
];

const statusOrder = ['pending', 'processing', 'shipped', 'delivered'];

function stepReached(status, stepIndex) {
    if (status === 'cancelled') return false;
    return statusOrder.indexOf(status) >= stepIndex;
}

function fmt(val) { return Number(val).toLocaleString('en-BD'); }
function fmtDate(d) {
    if (!d) return '';
    return new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
