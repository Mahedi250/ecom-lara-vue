<template>
    <AdminLayout title="Roles & Access" subtitle="Manage roles and what they can access">

        <div class="flex justify-end mb-5">
            <Link :href="route('admin.roles.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Role
            </Link>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Permissions</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Users</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-800">{{ role.name }}</span>
                                    <span v-if="role.name === 'admin'" class="text-[10px] bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded-full">Super Admin</span>
                                    <span v-else-if="isSystem(role.name)" class="text-[10px] bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full">System</span>
                                </div>
                                <p v-if="role.name === 'admin'" class="text-xs text-gray-400 mt-0.5">Full access to everything</p>
                            </td>
                            <td class="px-4 py-3.5 hidden sm:table-cell text-gray-600">{{ role.permissions_count }} permissions</td>
                            <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">{{ role.users_count }} users</td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center gap-2 justify-end">
                                    <Link :href="route('admin.roles.edit', role.id)" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium border border-indigo-200 px-2.5 py-1 rounded-lg transition">Edit access</Link>
                                    <button v-if="!isSystem(role.name)" @click="confirmDelete(role)" class="text-xs text-red-500 hover:text-red-700 font-medium border border-red-200 px-2.5 py-1 rounded-lg transition">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Transition name="fade">
            <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Delete Role?</h3>
                    <p class="text-gray-500 text-sm mb-6">Delete the role <strong>{{ deleteTarget.name }}</strong>? Users with this role will lose its access.</p>
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

defineProps({ roles: { type: Array, default: () => [] } });

const deleteTarget = ref(null);
const SYSTEM_ROLES = ['admin', 'staff'];

function isSystem(name) { return SYSTEM_ROLES.includes(name); }
function confirmDelete(role) { deleteTarget.value = role; }
function doDelete() {
    router.delete(route('admin.roles.destroy', deleteTarget.value.id), {
        onSuccess: () => { deleteTarget.value = null; },
    });
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
