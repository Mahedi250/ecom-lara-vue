<template>
    <AdminLayout title="Products" subtitle="Manage your product catalog">
        <template #header-actions>
            <Link :href="route('admin.products.create')" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-3 sm:px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">Add Product</span>
            </Link>
        </template>

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5 flex flex-col sm:flex-row gap-3">
            <input
                v-model="filters.search" type="text" placeholder="Search products..."
                class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                @keyup.enter="applyFilters"
            />
            <select v-model="filters.category_id" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                <option value="">All Categories</option>
            </select>
            <select v-model="filters.status" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
            </select>
            <button @click="applyFilters" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition">Filter</button>
            <button v-if="hasFilters" @click="clearFilters" class="text-gray-500 hover:text-gray-800 text-sm px-3 py-2 rounded-xl border border-gray-200 transition">Clear</button>
        </div>

        <!-- Mobile cards (< md) -->
        <div class="md:hidden space-y-3">
            <div v-if="products.data.length === 0" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center text-gray-400">
                No products found
            </div>
            <div v-for="p in products.data" :key="p.id" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-start gap-3">
                    <!-- Thumbnail -->
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0">
                        <img v-if="p.thumbnail" :src="p.thumbnail" :alt="p.name" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">{{ p.name }}</p>
                        <p class="text-xs text-gray-400 mb-1.5">{{ p.sku }}</p>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-bold text-gray-900">৳{{ fmt(p.effective_price) }}</span>
                            <span v-if="p.sale_price" class="text-xs text-gray-400 line-through">৳{{ fmt(p.price) }}</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full" :class="p.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                {{ p.status_label }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Stock: <span :class="p.stock <= 5 ? 'text-red-600 font-semibold' : ''">{{ p.stock }}</span></p>
                    </div>
                </div>

                <!-- Footer: Featured toggle + actions -->
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Featured</span>
                        <button
                            @click="toggleFeatured(p)"
                            :disabled="togglingId === p.id"
                            :title="p.is_featured ? 'Remove from featured' : 'Mark as featured'"
                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none disabled:opacity-50"
                            :class="p.is_featured ? 'bg-amber-400' : 'bg-gray-200'"
                        >
                            <span
                                class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200"
                                :class="p.is_featured ? 'translate-x-4' : 'translate-x-0'"
                            />
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link :href="route('admin.products.edit', p.id)" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium border border-indigo-200 px-3 py-1.5 rounded-lg transition">Edit</Link>
                        <button @click="confirmDelete(p)" class="text-xs text-red-500 hover:text-red-700 font-medium border border-red-200 px-3 py-1.5 rounded-lg transition">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop table (md+) -->
        <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Product</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Price</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Stock</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Featured</th>
                            <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="products.data.length === 0">
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400">No products found</td>
                        </tr>
                        <tr v-for="p in products.data" :key="p.id" class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0">
                                        <img v-if="p.thumbnail" :src="p.thumbnail" :alt="p.name" class="w-full h-full object-cover" />
                                        <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 truncate max-w-[180px]">{{ p.name }}</p>
                                        <p class="text-xs text-gray-400">{{ p.sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-gray-600 text-xs">{{ p.category?.name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div>
                                    <p class="font-semibold text-gray-800">৳{{ fmt(p.effective_price) }}</p>
                                    <p v-if="p.sale_price" class="text-xs text-gray-400 line-through">৳{{ fmt(p.price) }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span :class="p.stock <= 5 ? 'text-red-600 font-semibold' : 'text-gray-600'">{{ p.stock }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full" :class="p.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                    {{ p.status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <button
                                    @click="toggleFeatured(p)"
                                    :disabled="togglingId === p.id"
                                    :title="p.is_featured ? 'Remove from featured' : 'Mark as featured'"
                                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none disabled:opacity-50"
                                    :class="p.is_featured ? 'bg-amber-400' : 'bg-gray-200'"
                                >
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200"
                                        :class="p.is_featured ? 'translate-x-4' : 'translate-x-0'"
                                    />
                                </button>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center gap-2 justify-end">
                                    <Link :href="route('admin.products.edit', p.id)" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium border border-indigo-200 px-2.5 py-1 rounded-lg transition">Edit</Link>
                                    <button @click="confirmDelete(p)" class="text-xs text-red-500 hover:text-red-700 font-medium border border-red-200 px-2.5 py-1 rounded-lg transition">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="products.meta?.last_page > 1" class="border-t border-gray-100 px-5 py-3.5 flex items-center justify-between text-sm">
                <p class="text-gray-500 text-xs">Showing {{ products.meta.from }}–{{ products.meta.to }} of {{ products.meta.total }}</p>
                <div class="flex gap-1">
                    <Link
                        v-for="link in products.meta.links" :key="link.label"
                        :href="link.url ?? '#'"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs transition',
                            link.active ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-500 hover:bg-gray-100',
                            !link.url ? 'opacity-40 pointer-events-none' : '',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>

        <!-- Mobile pagination -->
        <div v-if="products.meta?.last_page > 1" class="md:hidden mt-3 flex items-center justify-between text-sm">
            <p class="text-gray-500 text-xs">{{ products.meta.from }}–{{ products.meta.to }} of {{ products.meta.total }}</p>
            <div class="flex gap-1">
                <Link
                    v-for="link in products.meta.links" :key="link.label"
                    :href="link.url ?? '#'"
                    :class="[
                        'px-3 py-1.5 rounded-lg text-xs transition',
                        link.active ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-500 hover:bg-gray-100 border border-gray-200',
                        !link.url ? 'opacity-40 pointer-events-none' : '',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>

        <!-- Delete confirm modal -->
        <Transition name="fade">
            <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Delete Product?</h3>
                    <p class="text-gray-500 text-sm mb-6">This will permanently delete <strong>{{ deleteTarget.name }}</strong>. This action cannot be undone.</p>
                    <div class="flex gap-3">
                        <button @click="deleteTarget = null" class="flex-1 border border-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition text-sm">Cancel</button>
                        <button @click="doDelete" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl transition text-sm">Delete</button>
                    </div>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    products: Object,
    filters: Object,
});

const page = usePage();
const filters = reactive({ search: props.filters?.search ?? '', category_id: props.filters?.category_id ?? '', status: props.filters?.status ?? '' });
const deleteTarget = ref(null);
const togglingId = ref(null);
const hasFilters = computed(() => filters.search || filters.category_id || filters.status);

function fmt(v) { return Number(v).toLocaleString('en-BD'); }
function applyFilters() { router.get(route('admin.products.index'), filters, { preserveState: true, replace: true }); }
function clearFilters() { Object.assign(filters, { search: '', category_id: '', status: '' }); applyFilters(); }
function confirmDelete(p) { deleteTarget.value = p; }
function doDelete() {
    router.delete(route('admin.products.destroy', deleteTarget.value.id), {
        onSuccess: () => { deleteTarget.value = null; },
    });
}
function toggleFeatured(p) {
    togglingId.value = p.id;
    router.patch(route('admin.products.toggle-featured', p.id), {}, {
        preserveScroll: true,
        onFinish: () => { togglingId.value = null; },
    });
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
