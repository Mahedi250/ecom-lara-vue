<template>
    <AdminLayout title="Customers" subtitle="Manage your customer accounts">
        <template #header-actions>
            <button @click="showCreate = true"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Customer
            </button>
        </template>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 font-semibold mb-1">Total</p>
                <p class="text-2xl font-extrabold text-gray-900">{{ stats.total }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-green-500 font-semibold mb-1">Active</p>
                <p class="text-2xl font-extrabold text-green-600">{{ stats.active }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-red-400 font-semibold mb-1">Banned</p>
                <p class="text-2xl font-extrabold text-red-500">{{ stats.banned }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-indigo-400 font-semibold mb-1">New This Month</p>
                <p class="text-2xl font-extrabold text-indigo-600">{{ stats.new_this_month }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-center">
            <div class="relative flex-1 min-w-[180px]">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input v-model="filters.search" @input="applyFilters" type="text" placeholder="Search name, email, phone..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300" />
            </div>
            <div class="flex gap-2">
                <button v-for="opt in statusOpts" :key="opt.value"
                    @click="setStatus(opt.value)"
                    class="px-3 py-2 text-xs font-semibold rounded-xl border transition"
                    :class="filters.status === opt.value
                        ? 'bg-indigo-600 text-white border-indigo-600'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'">
                    {{ opt.label }}
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                            <th class="text-left px-5 py-3 font-semibold">Customer</th>
                            <th class="text-left px-4 py-3 font-semibold hidden sm:table-cell">Phone</th>
                            <th class="text-left px-4 py-3 font-semibold hidden md:table-cell">Joined</th>
                            <th class="text-right px-4 py-3 font-semibold hidden sm:table-cell">Orders</th>
                            <th class="text-right px-4 py-3 font-semibold hidden md:table-cell">Spent</th>
                            <th class="text-center px-4 py-3 font-semibold">Status</th>
                            <th class="text-right px-5 py-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="!customers.data?.length">
                            <td colspan="7" class="text-center py-16 text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                No customers found
                            </td>
                        </tr>
                        <tr v-for="c in customers.data" :key="c.id"
                            class="hover:bg-gray-50/50 transition">

                            <!-- Name + Email -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center text-sm font-bold text-white"
                                        :style="{ background: avatarColor(c.name) }">
                                        {{ initials(c.name) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 truncate">{{ c.name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ c.email }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3.5 text-gray-500 hidden sm:table-cell">{{ c.phone ?? '—' }}</td>

                            <td class="px-4 py-3.5 text-gray-500 hidden md:table-cell">{{ fmtDate(c.created_at) }}</td>

                            <td class="px-4 py-3.5 text-right hidden sm:table-cell">
                                <span class="font-semibold text-gray-700">{{ c.orders_count }}</span>
                            </td>

                            <td class="px-4 py-3.5 text-right hidden md:table-cell">
                                <span class="font-semibold text-gray-700">৳{{ fmt(c.orders_sum_total) }}</span>
                            </td>

                            <!-- Status badge -->
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold"
                                    :class="c.status === 'banned'
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-green-100 text-green-700'">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                        :class="c.status === 'banned' ? 'bg-red-500' : 'bg-green-500'"></span>
                                    {{ c.status === 'banned' ? 'Banned' : 'Active' }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Ban / Unban -->
                                    <button v-if="c.status !== 'banned'" @click="ban(c)"
                                        class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        Ban
                                    </button>
                                    <button v-else @click="unban(c)"
                                        class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Unban
                                    </button>
                                    <!-- Delete -->
                                    <button @click="destroy(c)"
                                        class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-gray-100 text-gray-500 hover:bg-red-50 hover:text-red-600 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="customers.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400">Showing {{ customers.from }}–{{ customers.to }} of {{ customers.total }}</p>
                <div class="flex gap-1.5">
                    <button v-for="link in customers.links" :key="link.label"
                        @click="link.url && goToPage(link.url)"
                        :disabled="!link.url"
                        v-html="link.label"
                        class="px-3 py-1.5 text-xs rounded-lg border transition disabled:opacity-40 disabled:cursor-not-allowed"
                        :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 text-gray-600 hover:border-indigo-400'">
                    </button>
                </div>
            </div>
        </div>

        <!-- Create Customer Modal -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="showCreate = false">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                            <h3 class="font-bold text-gray-900 text-base">Create New Customer</h3>
                            <button @click="showCreate = false" class="text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <form @submit.prevent="submitCreate" class="p-6 space-y-4">
                            <div>
                                <label class="label">Full Name <span class="text-red-500">*</span></label>
                                <input v-model="createForm.name" type="text" class="input" placeholder="John Doe" />
                                <p v-if="errors.name" class="error">{{ errors.name }}</p>
                            </div>
                            <div>
                                <label class="label">Email <span class="text-red-500">*</span></label>
                                <input v-model="createForm.email" type="email" class="input" placeholder="john@example.com" />
                                <p v-if="errors.email" class="error">{{ errors.email }}</p>
                            </div>
                            <div>
                                <label class="label">Phone</label>
                                <input v-model="createForm.phone" type="tel" class="input" placeholder="+880..." />
                            </div>
                            <div>
                                <label class="label">Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input v-model="createForm.password" :type="showPwd ? 'text' : 'password'" class="input pr-10" placeholder="Min 8 characters" />
                                    <button type="button" @click="showPwd = !showPwd"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <svg v-if="!showPwd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                                <p v-if="errors.password" class="error">{{ errors.password }}</p>
                            </div>
                            <div class="flex gap-3 pt-2">
                                <button type="button" @click="showCreate = false"
                                    class="flex-1 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                                    Cancel
                                </button>
                                <button type="submit" :disabled="creating"
                                    class="flex-1 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 rounded-xl transition">
                                    {{ creating ? 'Creating...' : 'Create Customer' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ customers: Object, filters: Object, stats: Object });
const page = usePage();
const errors = computed(() => page.props.errors ?? {});

const showCreate = ref(false);
const showPwd = ref(false);
const creating = ref(false);

const filters = reactive({
    search: props.filters?.search ?? '',
    status: props.filters?.status ?? '',
});

const statusOpts = [
    { value: '', label: 'All' },
    { value: 'active', label: 'Active' },
    { value: 'banned', label: 'Banned' },
];

const createForm = reactive({ name: '', email: '', phone: '', password: '' });

let searchTimer = null;
function applyFilters() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(route('admin.customers.index'), filters, { preserveState: true, replace: true });
    }, 350);
}

function setStatus(s) {
    filters.status = s;
    router.get(route('admin.customers.index'), filters, { preserveState: true, replace: true });
}

function goToPage(url) {
    router.get(url, filters, { preserveState: true });
}

function submitCreate() {
    creating.value = true;
    router.post(route('admin.customers.store'), createForm, {
        onSuccess: () => {
            showCreate.value = false;
            Object.assign(createForm, { name: '', email: '', phone: '', password: '' });
        },
        onFinish: () => { creating.value = false; },
        preserveScroll: true,
    });
}

function ban(c) {
    if (!confirm(`Ban ${c.name}? They will no longer be able to log in.`)) return;
    router.patch(route('admin.customers.ban', c.id), {}, { preserveScroll: true });
}

function unban(c) {
    router.patch(route('admin.customers.unban', c.id), {}, { preserveScroll: true });
}

function destroy(c) {
    if (!confirm(`Delete ${c.name}? This cannot be undone.`)) return;
    router.delete(route('admin.customers.destroy', c.id), { preserveScroll: true });
}

function fmt(val) { return Number(val).toLocaleString('en-BD'); }

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

function initials(name) {
    return name?.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase() ?? '?';
}

const colors = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#3b82f6','#ef4444','#14b8a6'];
function avatarColor(name) {
    let hash = 0;
    for (const c of (name ?? '')) hash = c.charCodeAt(0) + ((hash << 5) - hash);
    return colors[Math.abs(hash) % colors.length];
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent; }
.error { @apply text-red-500 text-xs mt-1; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
