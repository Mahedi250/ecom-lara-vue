<template>
    <Head title="My Dashboard" />

    <CustomerLayout>
        <!-- Stats grid -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-4 text-white shadow-lg shadow-indigo-200">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-3xl font-extrabold leading-none">{{ stats.total_orders ?? 0 }}</p>
                <p class="text-indigo-100 text-xs mt-1.5">Total Orders</p>
            </div>

            <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl p-4 text-white shadow-lg shadow-orange-200">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-3xl font-extrabold leading-none">{{ stats.pending_orders ?? 0 }}</p>
                <p class="text-orange-100 text-xs mt-1.5">In Progress</p>
            </div>

            <div class="bg-gradient-to-br from-emerald-400 to-green-600 rounded-2xl p-4 text-white shadow-lg shadow-green-200">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-3xl font-extrabold leading-none">{{ stats.delivered_orders ?? 0 }}</p>
                <p class="text-green-100 text-xs mt-1.5">Delivered</p>
            </div>

            <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-4 text-white shadow-lg shadow-purple-200">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-lg font-extrabold leading-none">৳{{ fmtShort(stats.total_spent ?? 0) }}</p>
                <p class="text-purple-100 text-xs mt-1.5">Total Spent</p>
            </div>
        </div>

        <!-- Shop CTA banner -->
        <Link :href="route('products.index')"
            class="block mb-4 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl px-5 py-4 relative overflow-hidden group">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/5 rounded-full"></div>
                <div class="absolute -right-2 bottom-0 w-16 h-16 bg-purple-400/10 rounded-full"></div>
            </div>
            <div class="relative flex items-center justify-between">
                <div>
                    <p class="text-white font-bold text-sm">Discover New Arrivals</p>
                    <p class="text-indigo-200 text-xs mt-0.5">Shop the latest deals →</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-white/30 transition">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </Link>

        <!-- Recent Orders -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
            <div class="flex items-center justify-between px-4 py-4 border-b border-gray-50">
                <h2 class="font-bold text-gray-900 text-sm">Recent Orders</h2>
                <Link :href="route('orders.index')"
                    class="text-xs text-indigo-600 font-semibold bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-full transition">
                    See all
                </Link>
            </div>

            <!-- Empty -->
            <div v-if="!recentOrders.length" class="py-12 px-4 text-center">
                <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-700 text-sm mb-1">No orders yet</p>
                <p class="text-xs text-gray-400 mb-4">Start shopping and your orders appear here</p>
                <Link :href="route('products.index')"
                    class="inline-flex items-center gap-2 bg-indigo-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl">
                    Browse Products
                </Link>
            </div>

            <div v-else class="divide-y divide-gray-50">
                <Link
                    v-for="order in recentOrders" :key="order.id"
                    :href="route('orders.show', order.id)"
                    class="flex items-center gap-3 px-4 py-3.5 active:bg-gray-50 transition group"
                >
                    <!-- Status dot + icon -->
                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white"
                            :style="{ background: order.status_color }"></div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 text-sm">{{ order.order_number }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ order.items_count }} item{{ order.items_count !== 1 ? 's' : '' }} · {{ fmtDate(order.created_at) }}
                        </p>
                    </div>

                    <div class="text-right flex-shrink-0">
                        <p class="font-bold text-gray-900 text-sm">৳{{ fmt(order.total) }}</p>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                            :style="{ background: order.status_color + '20', color: order.status_color }">
                            {{ order.status_label }}
                        </span>
                    </div>

                    <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-400 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </Link>
            </div>
        </div>

        <!-- Quick actions -->
        <div class="grid grid-cols-3 gap-3">
            <Link :href="route('orders.index')"
                class="flex flex-col items-center gap-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-4 active:scale-95 transition hover:border-indigo-200">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-gray-700 text-center leading-tight">My Orders</p>
            </Link>

            <Link :href="route('profile.edit')"
                class="flex flex-col items-center gap-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-4 active:scale-95 transition hover:border-indigo-200">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-gray-700 text-center leading-tight">Profile</p>
            </Link>

            <Link :href="route('products.index')"
                class="flex flex-col items-center gap-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-4 active:scale-95 transition hover:border-indigo-200">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-gray-700 text-center leading-tight">Shop</p>
            </Link>
        </div>
    </CustomerLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    recentOrders: { type: Array, default: () => [] },
});

const page = usePage();
const firstName = computed(() => page.props.auth.user?.name?.split(' ')[0] ?? 'there');

function fmt(val) { return Number(val).toLocaleString('en-BD'); }
function fmtShort(val) {
    const n = Number(val);
    if (n >= 100000) return (n / 100000).toFixed(1) + 'L';
    if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
    return n.toLocaleString('en-BD');
}
function fmtDate(d) {
    if (!d) return '';
    return new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
}
</script>
