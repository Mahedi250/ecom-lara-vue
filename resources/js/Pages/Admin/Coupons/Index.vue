<template>
    <AdminLayout title="Coupons" subtitle="Manage discount coupons">
        <template #header-actions>
            <Link :href="route('admin.coupons.create')" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Coupon
            </Link>
        </template>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Code</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Type / Value</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Usage</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Expires</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="coupons.data.length === 0">
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400">No coupons yet</td>
                        </tr>
                        <tr v-for="c in coupons.data" :key="c.id" class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3.5">
                                <p class="font-mono font-bold text-gray-800 tracking-wider text-xs bg-gray-100 px-2 py-1 rounded inline-block">{{ c.code }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ c.name }}</p>
                            </td>
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <span class="text-gray-700 font-semibold">
                                    {{ c.type === 'percentage' ? c.value + '%' : '৳' + fmt(c.value) }}
                                </span>
                                <span class="text-xs text-gray-400 ml-1">{{ c.type }}</span>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="text-gray-600">{{ c.used_count }}</span>
                                <span v-if="c.usage_limit" class="text-gray-400"> / {{ c.usage_limit }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-500 hidden lg:table-cell">
                                <span v-if="c.expires_at">{{ fmtDate(c.expires_at) }}</span>
                                <span v-else class="text-gray-300">Never</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full" :class="c.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                    {{ c.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center gap-2 justify-end">
                                    <Link :href="route('admin.coupons.edit', c.id)" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium border border-indigo-200 px-2.5 py-1 rounded-lg transition">Edit</Link>
                                    <button @click="confirmDelete(c)" class="text-xs text-red-500 hover:text-red-700 font-medium border border-red-200 px-2.5 py-1 rounded-lg transition">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="coupons.last_page > 1" class="border-t border-gray-100 px-5 py-3.5 flex items-center justify-between">
                <p class="text-gray-500 text-xs">{{ coupons.total }} coupons</p>
                <div class="flex gap-1">
                    <Link v-for="link in coupons.links" :key="link.label" :href="link.url ?? '#'"
                        :class="['px-3 py-1.5 rounded-lg text-xs transition', link.active ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-gray-100', !link.url ? 'opacity-40 pointer-events-none' : '']"
                        v-html="link.label" />
                </div>
            </div>
        </div>

        <Transition name="fade">
            <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Delete Coupon?</h3>
                    <p class="text-gray-500 text-sm mb-6">Delete coupon <strong>{{ deleteTarget.code }}</strong>? This cannot be undone.</p>
                    <div class="flex gap-3">
                        <button @click="deleteTarget = null" class="flex-1 border border-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl hover:bg-gray-50 text-sm">Cancel</button>
                        <button @click="doDelete" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl text-sm">Delete</button>
                    </div>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ coupons: Object });
const deleteTarget = ref(null);

function fmt(v) { return Number(v).toLocaleString('en-BD'); }
function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-BD', { day: '2-digit', month: 'short', year: 'numeric' }) : ''; }
function confirmDelete(c) { deleteTarget.value = c; }
function doDelete() { router.delete(route('admin.coupons.destroy', deleteTarget.value.id), { onSuccess: () => { deleteTarget.value = null; } }); }
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
