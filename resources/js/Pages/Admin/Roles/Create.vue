<template>
    <AdminLayout title="New Role" subtitle="Create a role and pick what it can access">
        <template #header-actions>
            <Link :href="route('admin.roles.index')" class="text-sm text-gray-500 hover:text-gray-800 border border-gray-200 px-4 py-2 rounded-xl transition">← Back</Link>
        </template>

        <form @submit.prevent="submit" class="max-w-3xl space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Role Name <span class="text-red-500">*</span></label>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="e.g. Accountant, Manager, Support"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                />
                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <RolePermissionEditor v-model="form.permissions" :menu-items="menuItems" />
                <p v-if="form.errors.permissions" class="text-red-500 text-xs mt-2">{{ form.errors.permissions }}</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold py-2.5 px-6 rounded-xl transition text-sm">
                    {{ form.processing ? 'Creating...' : 'Create Role' }}
                </button>
                <Link :href="route('admin.roles.index')" class="border border-gray-200 text-gray-600 font-semibold py-2.5 px-6 rounded-xl hover:bg-gray-50 transition text-sm">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RolePermissionEditor from '@/Components/Admin/RolePermissionEditor.vue';

defineProps({ menuItems: { type: Array, default: () => [] } });

const form = useForm({
    name: '',
    permissions: [],
});

function submit() {
    form.post(route('admin.roles.store'));
}
</script>
