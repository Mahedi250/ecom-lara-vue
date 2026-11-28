<template>
    <AdminLayout title="New User" subtitle="Create an admin or customer account">
        <template #header-actions>
            <Link :href="route('admin.users.index')" class="text-sm text-gray-500 hover:text-gray-800 border border-gray-200 px-4 py-2 rounded-xl transition">← Back</Link>
        </template>

        <form @submit.prevent="submit" class="max-w-3xl space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Account</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input v-model="form.name" type="text" autocomplete="name" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input v-model="form.email" type="email" autocomplete="email" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                        <p class="text-xs text-gray-400 mt-1">Optional.</p>
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                        <input v-model="form.phone" type="tel" placeholder="01XXXXXXXXX" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                        <p class="text-xs text-gray-400 mt-1">Must be unique. Used to log in.</p>
                        <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select v-model="form.status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="banned">Banned</option>
                        </select>
                        <p v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                        <input v-model="form.password" type="password" autocomplete="new-password" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                        <input v-model="form.password_confirmation" type="password" autocomplete="new-password" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-1">Roles &amp; Access</h2>
                <p class="text-xs text-gray-400 mb-3">Controls which menus this user sees. Configure permissions per role under Roles &amp; Access.</p>
                <div class="flex flex-wrap gap-2">
                    <label
                        v-for="r in roles" :key="r.id"
                        class="flex items-center gap-1.5 cursor-pointer border rounded-lg px-3 py-2 text-sm transition capitalize"
                        :class="form.roles.includes(r.name)
                            ? 'border-indigo-300 bg-indigo-50 text-indigo-700'
                            : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                    >
                        <input type="checkbox" :value="r.name" v-model="form.roles" class="w-3.5 h-3.5 text-indigo-600 rounded" />
                        {{ r.name }}
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold py-2.5 px-6 rounded-xl transition text-sm">
                    {{ form.processing ? 'Creating...' : 'Create User' }}
                </button>
                <Link :href="route('admin.users.index')" class="border border-gray-200 text-gray-600 font-semibold py-2.5 px-6 rounded-xl hover:bg-gray-50 transition text-sm">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ roles: { type: Array, default: () => [] } });

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    status: 'active',
    roles: [],
});

function submit() {
    form.post(route('admin.users.store'));
}
</script>
