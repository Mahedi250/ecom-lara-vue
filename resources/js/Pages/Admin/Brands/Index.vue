<template>
    <AdminLayout title="Brands" subtitle="Manage product brands">
        <template #header-actions>
            <Link :href="route('admin.brands.create')" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-3 sm:px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">Add Brand</span>
            </Link>
        </template>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Brand</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Website</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Products</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="brands.data.length === 0">
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400">No brands yet</td>
                        </tr>
                        <tr v-for="b in brands.data" :key="b.id" class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 flex items-center justify-center">
                                        <img v-if="b.logo" :src="b.logo" :alt="b.name" class="w-full h-full object-contain p-1" />
                                        <span v-else class="text-xs font-bold text-gray-400">{{ b.name?.[0]?.toUpperCase() }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ b.name }}</p>
                                        <p class="text-xs text-gray-400">{{ b.slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-gray-500 text-xs hidden md:table-cell">
                                <a v-if="b.website" :href="b.website" target="_blank" rel="noopener" class="text-indigo-600 hover:underline truncate max-w-[140px] inline-block">{{ b.website }}</a>
                                <span v-else class="text-gray-300">—</span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 hidden sm:table-cell">{{ b.products_count ?? 0 }}</td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full" :class="b.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                    {{ b.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center gap-2 justify-end">
                                    <Link :href="route('admin.brands.edit', b.id)" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium border border-indigo-200 px-2.5 py-1 rounded-lg transition">Edit</Link>
                                    <button @click="confirmDelete(b)" class="text-xs text-red-500 hover:text-red-700 font-medium border border-red-200 px-2.5 py-1 rounded-lg transition">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="brands.meta?.last_page > 1" class="border-t border-gray-100 px-5 py-3.5 flex items-center justify-between">
                <p class="text-gray-500 text-xs">{{ brands.meta.total }} brands</p>
                <div class="flex gap-1">
                    <Link v-for="link in brands.meta.links" :key="link.label" :href="link.url ?? '#'"
                        :class="['px-3 py-1.5 rounded-lg text-xs transition', link.active ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-gray-100', !link.url ? 'opacity-40 pointer-events-none' : '']"
                        v-html="link.label" />
                </div>
            </div>
        </div>

        <Transition name="fade">
            <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Delete Brand?</h3>
                    <p class="text-gray-500 text-sm mb-6">Delete <strong>{{ deleteTarget.name }}</strong>? This cannot be undone.</p>
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

const props = defineProps({ brands: Object });
const deleteTarget = ref(null);
function confirmDelete(b) { deleteTarget.value = b; }
function doDelete() { router.delete(route('admin.brands.destroy', deleteTarget.value.id), { onSuccess: () => { deleteTarget.value = null; } }); }
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
