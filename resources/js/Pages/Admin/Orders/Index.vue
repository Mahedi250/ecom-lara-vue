<template>
    <AdminLayout title="Orders" subtitle="Manage customer orders">

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5 flex flex-col sm:flex-row gap-3">
            <input v-model="filters.search" type="text" placeholder="Search order number..." class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" @keyup.enter="applyFilters" />
            <select v-model="filters.status" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                <option value="">All Status</option>
                <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
            <button @click="applyFilters" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition">Filter</button>
            <button v-if="hasFilters" @click="clearFilters" class="text-gray-500 border border-gray-200 px-3 py-2 rounded-xl text-sm transition">Clear</button>
        </div>

        <!-- Status tabs -->
        <div class="flex gap-2 mb-5 flex-wrap">
            <button
                v-for="s in [{ value: '', label: 'All' }, ...statuses]" :key="s.value"
                @click="setStatus(s.value)"
                class="text-xs font-semibold px-3 py-1.5 rounded-full transition border"
                :class="filters.status === s.value ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'"
            >{{ s.label }}</button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Order</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Customer</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Payment</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Date</th>
                            <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="orders.data.length === 0">
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400">No orders found</td>
                        </tr>
                        <tr v-for="o in orders.data" :key="o.id" class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-gray-800 text-xs">{{ o.order_number }}</p>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <p class="text-gray-700">{{ o.user?.name ?? 'Guest' }}</p>
                                <p class="text-xs text-gray-400">{{ o.user?.email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3.5 font-bold text-gray-900">৳{{ fmt(o.total) }}</td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full" :class="statusClass(o.status)">{{ o.status_label }}</span>
                            </td>
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full" :class="o.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                                    {{ o.payment_status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-400 text-xs hidden lg:table-cell">{{ fmtDate(o.created_at) }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <Link :href="route('admin.orders.show', o.id)" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium border border-indigo-200 px-2.5 py-1 rounded-lg transition">View</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="orders.meta?.last_page > 1" class="border-t border-gray-100 px-5 py-3.5 flex items-center justify-between text-sm">
                <p class="text-gray-500 text-xs">Showing {{ orders.meta.from }}–{{ orders.meta.to }} of {{ orders.meta.total }}</p>
                <div class="flex gap-1">
                    <Link v-for="link in orders.meta.links" :key="link.label" :href="link.url ?? '#'"
                        :class="['px-3 py-1.5 rounded-lg text-xs transition', link.active ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-gray-100', !link.url ? 'opacity-40 pointer-events-none' : '']"
                        v-html="link.label" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ orders: Object, statuses: Array, filters: Object });
const filters = reactive({ search: props.filters?.search ?? '', status: props.filters?.status ?? '' });
const hasFilters = computed(() => filters.search || filters.status);

function fmt(v) { return Number(v).toLocaleString('en-BD'); }
function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-BD', { day: '2-digit', month: 'short', year: 'numeric' }) : ''; }
function applyFilters() { router.get(route('admin.orders.index'), filters, { preserveState: true, replace: true }); }
function clearFilters() { Object.assign(filters, { search: '', status: '' }); applyFilters(); }
function setStatus(s) { filters.status = s; applyFilters(); }
function statusClass(s) {
    const m = { pending: 'bg-amber-100 text-amber-700', processing: 'bg-blue-100 text-blue-700', shipped: 'bg-violet-100 text-violet-700', delivered: 'bg-green-100 text-green-700', cancelled: 'bg-red-100 text-red-700', refunded: 'bg-gray-100 text-gray-600' };
    return m[s] ?? 'bg-gray-100 text-gray-600';
}
</script>
