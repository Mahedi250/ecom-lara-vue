<template>
    <AdminLayout title="Dashboard" subtitle="Welcome back! Here's what's happening.">

        <!-- Stat cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <StatCard label="Total Revenue" :value="'৳' + fmt(stats.total_revenue)" icon="revenue" color="indigo" :sub="`৳${fmt(stats.today_revenue)} today`" />
            <StatCard label="Total Orders" :value="stats.total_orders" icon="orders" color="blue" :sub="`${stats.pending_orders} pending`" />
            <StatCard label="Products" :value="stats.total_products" icon="products" color="violet" :sub="`${stats.low_stock} low stock`" />
            <StatCard label="Customers" :value="stats.total_users" icon="users" color="emerald" :sub="`${stats.today_orders} orders today`" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Recent Orders -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-[0_2px_12px_0_rgba(0,0,0,0.06)] overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Recent Orders</h2>
                    <Link :href="route('admin.orders.index')" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">View all →</Link>
                </div>
                <div v-if="recent_orders.length === 0" class="px-5 py-10 text-center text-gray-400 text-sm">No orders yet</div>
                <div v-else class="divide-y divide-gray-100">
                    <Link
                        v-for="order in recent_orders"
                        :key="order.id"
                        :href="route('admin.orders.show', order.id)"
                        class="relative flex items-center gap-3 pl-4 pr-4 sm:pr-5 py-3.5 hover:bg-indigo-50/40 transition group shadow-[0_1px_0_0_rgba(0,0,0,0.04)]"
                    >
                        <!-- Status colour bar on left edge -->
                        <div class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full flex-shrink-0" :class="statusBarClass(order.status)"></div>

                        <!-- Avatar -->
                        <div class="w-9 h-9 bg-indigo-100 group-hover:bg-indigo-200 rounded-xl flex items-center justify-center flex-shrink-0 transition ml-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>

                        <!-- Left: order id + customer + status dot on mobile -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="text-sm font-semibold text-gray-800 truncate max-w-[100px] sm:max-w-[170px]">{{ order.order_number }}</p>
                                <!-- Mobile status dot -->
                                <span class="sm:hidden w-2 h-2 rounded-full flex-shrink-0" :class="statusDotClass(order.status)"></span>
                            </div>
                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ order.customer }}</p>
                        </div>

                        <!-- Middle: status badge (sm+) -->
                        <span class="hidden sm:inline-flex flex-shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full" :class="statusClass(order.status)">
                            {{ order.status_label }}
                        </span>

                        <!-- Right: amount + date -->
                        <div class="text-right flex-shrink-0 min-w-[70px]">
                            <p class="text-sm font-bold text-gray-900">৳{{ fmt(order.total) }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ order.created_at }}</p>
                        </div>

                        <!-- Arrow -->
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-500 flex-shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </Link>
                </div>
            </div>

            <!-- Quick Actions + Summary -->
            <div class="space-y-4">

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h2 class="font-semibold text-gray-900 mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-2 gap-2">
                        <Link :href="route('admin.products.create')" class="flex flex-col items-center gap-2 p-3 bg-indigo-50 hover:bg-indigo-100 rounded-xl text-indigo-700 transition text-xs font-medium text-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Product
                        </Link>
                        <Link :href="route('admin.orders.index')" class="flex flex-col items-center gap-2 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl text-blue-700 transition text-xs font-medium text-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            View Orders
                        </Link>
                        <Link :href="route('admin.categories.create')" class="flex flex-col items-center gap-2 p-3 bg-violet-50 hover:bg-violet-100 rounded-xl text-violet-700 transition text-xs font-medium text-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v1H3V7zm0 4h18v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6z"/></svg>
                            Add Category
                        </Link>
                        <Link :href="route('admin.coupons.create')" class="flex flex-col items-center gap-2 p-3 bg-amber-50 hover:bg-amber-100 rounded-xl text-amber-700 transition text-xs font-medium text-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            Add Coupon
                        </Link>
                    </div>
                </div>

                <!-- Status Summary -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h2 class="font-semibold text-gray-900 mb-4">Order Status</h2>
                    <div class="space-y-2.5">
                        <div v-for="s in orderStatusSummary" :key="s.label" class="flex items-center gap-3">
                            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: s.color }"></div>
                            <span class="text-sm text-gray-600 flex-1">{{ s.label }}</span>
                            <span class="text-sm font-semibold text-gray-800">{{ s.count }}</span>
                        </div>
                    </div>
                </div>

                <!-- Storefront link -->
                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl p-5 text-white">
                    <p class="font-semibold mb-1">Visit Storefront</p>
                    <p class="text-indigo-200 text-xs mb-3">Preview your live store</p>
                    <Link href="/" target="_blank" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                        Open Store
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed, defineComponent, h } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: Object,
    recent_orders: { type: Array, default: () => [] },
});

function fmt(val) { return Number(val).toLocaleString('en-BD'); }

function statusClass(status) {
    const map = {
        pending:    'bg-yellow-100 text-yellow-700',
        processing: 'bg-blue-100 text-blue-700',
        shipped:    'bg-indigo-100 text-indigo-700',
        delivered:  'bg-green-100 text-green-700',
        cancelled:  'bg-red-100 text-red-700',
        refunded:   'bg-gray-100 text-gray-600',
    };
    return map[status] ?? 'bg-gray-100 text-gray-600';
}

function statusBarClass(status) {
    const map = {
        pending:    'bg-yellow-400',
        processing: 'bg-blue-400',
        shipped:    'bg-indigo-400',
        delivered:  'bg-green-400',
        cancelled:  'bg-red-400',
        refunded:   'bg-gray-300',
    };
    return map[status] ?? 'bg-gray-300';
}

function statusDotClass(status) {
    const map = {
        pending:    'bg-yellow-400',
        processing: 'bg-blue-400',
        shipped:    'bg-indigo-400',
        delivered:  'bg-green-400',
        cancelled:  'bg-red-400',
        refunded:   'bg-gray-300',
    };
    return map[status] ?? 'bg-gray-300';
}

const orderStatusSummary = computed(() => [
    { label: 'Pending', color: '#f59e0b', count: props.stats?.pending_orders ?? 0 },
    { label: 'Total Orders', color: '#6366f1', count: props.stats?.total_orders ?? 0 },
    { label: 'Low Stock', color: '#ef4444', count: props.stats?.low_stock ?? 0 },
]);

// StatCard inline component
const StatCard = defineComponent({
    props: { label: String, value: [String, Number], icon: String, color: String, sub: String },
    setup(props) {
        const colors = {
            indigo: { bg: 'bg-indigo-50', icon: 'text-indigo-600', iconBg: 'bg-indigo-100' },
            blue: { bg: 'bg-blue-50', icon: 'text-blue-600', iconBg: 'bg-blue-100' },
            violet: { bg: 'bg-violet-50', icon: 'text-violet-600', iconBg: 'bg-violet-100' },
            emerald: { bg: 'bg-emerald-50', icon: 'text-emerald-600', iconBg: 'bg-emerald-100' },
        };
        const icons = {
            revenue: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            orders: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
            products: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            users: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        };
        const c = colors[props.color] ?? colors.indigo;
        return () => h('div', { class: 'bg-white rounded-2xl border border-gray-100 shadow-sm p-5' }, [
            h('div', { class: 'flex items-start justify-between mb-3' }, [
                h('div', { class: `w-10 h-10 ${c.iconBg} rounded-xl flex items-center justify-center` },
                    [h('svg', { class: `w-5 h-5 ${c.icon}`, fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' },
                        [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: icons[props.icon] })]
                    )]
                ),
            ]),
            h('p', { class: 'text-2xl font-bold text-gray-900 mb-0.5' }, props.value),
            h('p', { class: 'text-sm text-gray-500 font-medium' }, props.label),
            props.sub ? h('p', { class: 'text-xs text-gray-400 mt-1' }, props.sub) : null,
        ]);
    },
});
</script>
