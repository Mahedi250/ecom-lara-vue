<template>
    <AdminLayout title="Add Coupon" subtitle="Create a new discount coupon">
        <template #header-actions>
            <Link :href="route('admin.coupons.index')" class="text-sm text-gray-500 hover:text-gray-800 border border-gray-200 px-4 py-2 rounded-xl transition">← Back</Link>
        </template>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h2 class="font-semibold text-gray-900">Coupon Details</h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Coupon Code <span class="text-red-500">*</span></label>
                                <div class="flex gap-2">
                                    <input v-model="form.code" type="text" placeholder="SUMMER20" class="input font-mono uppercase" :class="{'border-red-400': errors.code}" />
                                    <button type="button" @click="generateCode" class="px-3 py-2.5 text-xs bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition flex-shrink-0">Gen</button>
                                </div>
                                <p v-if="errors.code" class="error">{{ errors.code }}</p>
                            </div>
                            <div>
                                <label class="label">Name / Label</label>
                                <input v-model="form.name" type="text" placeholder="Summer Sale 20%" class="input" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Discount Type <span class="text-red-500">*</span></label>
                                <select v-model="form.type" class="input">
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount (৳)</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Value <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">{{ form.type === 'percentage' ? '%' : '৳' }}</span>
                                    <input v-model="form.value" type="number" step="0.01" min="0" :max="form.type === 'percentage' ? 100 : undefined" placeholder="20" class="input pl-8" :class="{'border-red-400': errors.value}" />
                                </div>
                                <p v-if="errors.value" class="error">{{ errors.value }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Minimum Order (৳)</label>
                                <input v-model="form.minimum_order_amount" type="number" step="0.01" min="0" placeholder="0" class="input" />
                            </div>
                            <div>
                                <label class="label">Max Discount (৳)</label>
                                <input v-model="form.maximum_discount" type="number" step="0.01" min="0" placeholder="Optional cap" class="input" :disabled="form.type === 'fixed'" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Total Usage Limit</label>
                                <input v-model="form.usage_limit" type="number" min="1" placeholder="Unlimited" class="input" />
                            </div>
                            <div>
                                <label class="label">Per User Limit</label>
                                <input v-model="form.usage_limit_per_user" type="number" min="1" placeholder="Unlimited" class="input" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Start Date</label>
                                <input v-model="form.starts_at" type="datetime-local" class="input" />
                            </div>
                            <div>
                                <label class="label">Expiry Date</label>
                                <input v-model="form.expires_at" type="datetime-local" class="input" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-900">Settings</h2>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="form.is_active" type="checkbox" class="w-4 h-4 text-indigo-600 rounded" />
                            <span class="text-sm text-gray-700 font-medium">Active</span>
                        </label>
                    </div>

                    <!-- Preview Card -->
                    <div v-if="form.code" class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl p-5 text-white">
                        <p class="text-xs font-semibold opacity-70 uppercase tracking-wide mb-2">Coupon Preview</p>
                        <p class="font-mono font-bold text-2xl tracking-widest mb-1">{{ form.code.toUpperCase() }}</p>
                        <p class="text-sm opacity-80">{{ form.type === 'percentage' ? form.value + '% off' : '৳' + (form.value || '0') + ' off' }}</p>
                        <p v-if="form.minimum_order_amount" class="text-xs opacity-60 mt-1">Min order: ৳{{ form.minimum_order_amount }}</p>
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-bold py-3.5 rounded-xl transition">
                        {{ form.processing ? 'Creating...' : 'Create Coupon' }}
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const page = usePage();
const errors = computed(() => page.props.errors ?? {});

const form = useForm({
    code: '', name: '', type: 'percentage', value: '',
    minimum_order_amount: '', maximum_discount: '',
    usage_limit: '', usage_limit_per_user: '',
    starts_at: '', expires_at: '', is_active: true,
});

function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    form.code = Array.from({ length: 8 }, () => chars[Math.floor(Math.random() * chars.length)]).join('');
}

function submit() {
    form.post(route('admin.coupons.store'));
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent; }
.error { @apply text-red-500 text-xs mt-1; }
</style>
