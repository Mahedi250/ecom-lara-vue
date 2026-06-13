<template>
    <AdminLayout title="Users" subtitle="Manage registered users">

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5 flex flex-col sm:flex-row gap-3">
            <input v-model="filters.search" type="text" placeholder="Search name or email..." class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" @keyup.enter="applyFilters" />
            <select v-model="filters.role" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                <option value="">All Roles</option>
                <option v-for="r in roles" :key="r.id" :value="r.name">{{ r.name }}</option>
            </select>
            <button @click="applyFilters" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition">Filter</button>
            <button v-if="hasFilters" @click="clearFilters" class="text-gray-500 border border-gray-200 px-3 py-2 rounded-xl text-sm transition">Clear</button>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Role</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Status</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Joined</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400">No users found</td>
                        </tr>
                        <tr v-for="u in users.data" :key="u.id" class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full overflow-hidden bg-indigo-100 flex-shrink-0 flex items-center justify-center">
                                        <img v-if="u.avatar" :src="u.avatar" :alt="u.name" class="w-full h-full object-cover" />
                                        <span v-else class="text-sm font-bold text-indigo-600">{{ u.name?.[0]?.toUpperCase() }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 text-sm">{{ u.name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ u.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="role in (u.roles ?? [])" :key="role"
                                        class="text-xs px-2 py-0.5 rounded-full font-medium"
                                        :class="role === 'admin' ? 'bg-red-100 text-red-700' : role === 'staff' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'">
                                        {{ role }}
                                    </span>
                                    <span v-if="!u.roles?.length" class="text-xs text-gray-300">customer</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                    :class="u.status === 'active' ? 'bg-green-100 text-green-700' : u.status === 'banned' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500'">
                                    {{ u.status ?? 'active' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-400 hidden lg:table-cell">{{ fmtDate(u.created_at) }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center gap-2 justify-end">
                                    <Link :href="route('admin.users.show', u.id)" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium border border-indigo-200 px-2.5 py-1 rounded-lg transition">View</Link>
                                    <button @click="confirmDelete(u)" class="text-xs text-red-500 hover:text-red-700 font-medium border border-red-200 px-2.5 py-1 rounded-lg transition">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="users.meta?.last_page > 1" class="border-t border-gray-100 px-5 py-3.5 flex items-center justify-between text-sm">
                <p class="text-gray-500 text-xs">Showing {{ users.meta.from }}–{{ users.meta.to }} of {{ users.meta.total }}</p>
                <div class="flex gap-1">
                    <Link v-for="link in users.meta.links" :key="link.label" :href="link.url ?? '#'"
                        :class="['px-3 py-1.5 rounded-lg text-xs transition', link.active ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-gray-100', !link.url ? 'opacity-40 pointer-events-none' : '']"
                        v-html="link.label" />
                </div>
            </div>
        </div>

        <Transition name="fade">
            <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Delete User?</h3>
                    <p class="text-gray-500 text-sm mb-6">Permanently delete <strong>{{ deleteTarget.name }}</strong>? All their data will be removed.</p>
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
import { ref, reactive, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ users: Object, roles: Array });
const deleteTarget = ref(null);
const filters = reactive({ search: '', role: '' });
const hasFilters = computed(() => filters.search || filters.role);

function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-BD', { day: '2-digit', month: 'short', year: 'numeric' }) : ''; }
function applyFilters() { router.get(route('admin.users.index'), filters, { preserveState: true, replace: true }); }
function clearFilters() { Object.assign(filters, { search: '', role: '' }); applyFilters(); }
function confirmDelete(u) { deleteTarget.value = u; }
function doDelete() { router.delete(route('admin.users.destroy', deleteTarget.value.id), { onSuccess: () => { deleteTarget.value = null; } }); }
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
