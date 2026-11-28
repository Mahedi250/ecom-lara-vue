<template>
    <AdminLayout :title="user.name" subtitle="User details & role access">
        <template #header-actions>
            <Link :href="route('admin.users.index')" class="text-sm text-gray-500 hover:text-gray-800 border border-gray-200 px-4 py-2 rounded-xl transition">← Back</Link>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl">

            <!-- Left: profile form -->
            <div class="lg:col-span-2 space-y-5">
                <form @submit.prevent="submit" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h2 class="font-semibold text-gray-900">Profile</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input v-model="form.name" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input v-model="form.email" type="email" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input v-model="form.phone" type="tel" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                            <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select v-model="form.status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                                <option value="active">Active</option>
                                <option value="banned">Banned</option>
                            </select>
                            <p v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</p>
                        </div>
                    </div>

                    <div v-if="can('users.edit')" class="pt-2">
                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold py-2.5 px-6 rounded-xl transition text-sm">
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: roles + credentials -->
            <div class="space-y-5">
                <div v-if="can('users.credentials')" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-semibold text-gray-900 mb-1">Update Password</h2>
                    <p class="text-xs text-gray-400 mb-4">Set a new password for this user. They can still change it themselves from their profile.</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input v-model="passwordForm.password" type="password" autocomplete="new-password" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                            <p v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input v-model="passwordForm.password_confirmation" type="password" autocomplete="new-password" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                        </div>
                        <button @click="passwordForm.patch(route('admin.users.password', user.id))" :disabled="passwordForm.processing" class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                            {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-semibold text-gray-900 mb-4">Roles & Access</h2>
                    <div class="space-y-2.5">
                        <label
                            v-for="r in roles" :key="r.id"
                            class="flex items-center gap-3 cursor-pointer"
                        >
                            <input
                                type="checkbox"
                                :value="r.name"
                                v-model="form.roles"
                                :disabled="!can('users.edit')"
                                class="w-4 h-4 text-indigo-600 rounded disabled:opacity-50"
                            />
                            <span class="text-sm text-gray-700 font-medium capitalize">{{ r.name }}</span>
                            <span v-if="r.name === 'admin'" class="text-[10px] bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded-full">Super Admin</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">
                        Roles control which menus this user sees in the admin panel. Manage permissions per role under
                        <Link v-if="canManageRoles" :href="route('admin.roles.index')" class="text-indigo-600 hover:underline">Roles &amp; Access</Link>
                        <span v-else>Roles &amp; Access</span>.
                    </p>
                    <button
                        v-if="can('users.edit')"
                        @click="submit"
                        :disabled="form.processing"
                        class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold py-2.5 rounded-xl transition text-sm"
                    >{{ form.processing ? 'Saving...' : 'Save Roles' }}</button>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-sm space-y-2">
                    <div class="flex justify-between"><span class="text-gray-500">Joined</span><span class="text-gray-800">{{ fmtDate(user.created_at) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">User ID</span><span class="text-gray-800 font-mono text-xs">#{{ user.id }}</span></div>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useCan } from '@/Composables/useCan';

const props = defineProps({
    user: { type: Object, required: true },
    roles: { type: Array, default: () => [] },
});

const { can } = useCan();
const canManageRoles = computed(() => usePage().props.can_manage_roles ?? false);

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone ?? '',
    status: props.user.status ?? 'active',
    roles: [...(props.user.roles ?? [])],
});

const passwordForm = useForm({
    password: '',
    password_confirmation: '',
});

function fmtDate(d) {
    return d ? new Date(d).toLocaleDateString('en-BD', { day: '2-digit', month: 'short', year: 'numeric' }) : '';
}

function submit() {
    form.patch(route('admin.users.update', props.user.id));
}
</script>
