<template>
    <AdminLayout title="Categories" subtitle="Manage product categories">
        <template #header-actions>
            <Link :href="route('admin.categories.create')"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-3 sm:px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Add Category</span>
            </Link>
        </template>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Parent</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Products</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="cat in categories" :key="cat.id" class="hover:bg-gray-50/50 transition">
                        <!-- Name + image -->
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 flex-shrink-0">
                                    <img v-if="cat.image" :src="cat.image" class="w-full h-full object-contain p-1" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v1H3V7zm0 4h18v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ cat.name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ cat.slug }}</p>
                                </div>
                            </div>
                        </td>
                        <!-- Parent -->
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span v-if="cat.parent_id" class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-full font-medium">
                                {{ categories.find(c => c.id === cat.parent_id)?.name ?? '—' }}
                            </span>
                            <span v-else class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full font-medium">Root</span>
                        </td>
                        <!-- Products count -->
                        <td class="px-4 py-3 hidden md:table-cell text-gray-500 text-xs">{{ cat.products_count ?? 0 }}</td>
                        <!-- Status -->
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold px-2 py-1 rounded-full"
                                :class="cat.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'">
                                {{ cat.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <!-- Actions -->
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 justify-end">
                                <Link :href="route('admin.categories.edit', cat.id)"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold border border-indigo-100 px-2.5 py-1 rounded-lg transition hover:bg-indigo-50">
                                    Edit
                                </Link>
                                <button @click="confirmDelete(cat)"
                                    class="text-xs text-red-500 hover:text-red-700 font-semibold border border-red-100 px-2.5 py-1 rounded-lg transition hover:bg-red-50">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="!categories.length" class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v1H3V7zm0 4h18v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6z"/>
                </svg>
                <p>No categories yet.</p>
                <Link :href="route('admin.categories.create')" class="mt-3 inline-block text-sm text-indigo-600 font-semibold">Create your first category →</Link>
            </div>
        </div>

        <!-- Delete confirm modal -->
        <Transition name="fade">
            <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="deleteTarget = null">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Delete Category?</h3>
                    <p class="text-gray-500 text-sm mb-4">
                        Delete <strong>{{ deleteTarget.name }}</strong>? Products won't be deleted.
                    </p>
                    <div class="flex gap-3">
                        <button @click="deleteTarget = null"
                            class="flex-1 border border-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl hover:bg-gray-50 text-sm">
                            Cancel
                        </button>
                        <button @click="doDelete"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl text-sm">
                            Delete
                        </button>
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

const props = defineProps({ categories: Array });

const categories = props.categories;

const deleteTarget = ref(null);
function confirmDelete(c) { deleteTarget.value = c; }
function doDelete() {
    router.delete(route('admin.categories.destroy', deleteTarget.value.id), {
        onSuccess: () => { deleteTarget.value = null; },
    });
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
