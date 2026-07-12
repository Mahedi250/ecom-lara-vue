<template>
    <AdminLayout title="Settings" subtitle="Configure your store">

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-5">

                    <!-- General -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-semibold text-gray-900 mb-4">General</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="label">Store Name</label>
                                <input v-model="values['general.store_name']" type="text" placeholder="My Ecom Store" class="input" />
                            </div>
                            <div>
                                <label class="label">Store Email</label>
                                <input v-model="values['general.store_email']" type="email" placeholder="store@example.com" class="input" />
                            </div>
                            <div>
                                <label class="label">Store Phone</label>
                                <input v-model="values['general.store_phone']" type="text" placeholder="+880..." class="input" />
                            </div>
                            <div>
                                <label class="label">Store Address</label>
                                <textarea v-model="values['general.store_address']" rows="2" class="input resize-none" placeholder="Dhaka, Bangladesh"></textarea>
                            </div>
                            <div>
                                <label class="label">Currency Symbol</label>
                                <input v-model="values['general.currency_symbol']" type="text" placeholder="৳" class="input" />
                            </div>
                        </div>
                    </div>

                    <!-- Shipping -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-semibold text-gray-900 mb-4">Shipping</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Default Shipping Cost (৳)</label>
                                    <input v-model="values['shipping.default_cost']" type="number" step="0.01" min="0" placeholder="60" class="input" />
                                </div>
                                <div>
                                    <label class="label">Free Shipping Minimum (৳)</label>
                                    <input v-model="values['shipping.free_threshold']" type="number" step="0.01" min="0" placeholder="1000" class="input" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-semibold text-gray-900 mb-4">Orders</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="label">Order Number Prefix</label>
                                <input v-model="values['order.prefix']" type="text" placeholder="ORD-" class="input" />
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input v-model="values['order.guest_checkout']" type="checkbox" class="w-4 h-4 text-indigo-600 rounded" />
                                <span class="text-sm text-gray-700 font-medium">Allow Guest Checkout</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input v-model="values['order.email_notifications']" type="checkbox" class="w-4 h-4 text-indigo-600 rounded" />
                                <span class="text-sm text-gray-700 font-medium">Send Order Email Notifications</span>
                            </label>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-semibold text-gray-900 mb-4">Admin Notifications</h2>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input v-model="values['notification.sound_enabled']" type="checkbox" class="w-4 h-4 text-indigo-600 rounded" />
                                <div>
                                    <span class="text-sm text-gray-700 font-medium">Notification Sound</span>
                                    <p class="text-xs text-gray-400 mt-0.5">Play a sound when a new order arrives in admin panel</p>
                                </div>
                            </label>
                            <div class="rounded-xl bg-indigo-50 border border-indigo-100 p-3 text-xs text-indigo-700">
                                <p class="font-semibold mb-1">Notify admin on:</p>
                                <p>🛒 New order placed by customer</p>
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-semibold text-gray-900 mb-4">SEO & Meta</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="label">Meta Title</label>
                                <input v-model="values['seo.meta_title']" type="text" placeholder="My Store – Best Products Online" class="input" />
                            </div>
                            <div>
                                <label class="label">Meta Description</label>
                                <textarea v-model="values['seo.meta_description']" rows="2" class="input resize-none" placeholder="Shop the best..."></textarea>
                            </div>
                            <div>
                                <label class="label">Google Analytics ID</label>
                                <input v-model="values['seo.ga_id']" type="text" placeholder="G-XXXXXXXXXX" class="input" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right column -->
                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                        <h2 class="font-semibold text-gray-900">Social Links</h2>
                        <div>
                            <label class="label">Facebook</label>
                            <input v-model="values['social.facebook']" type="url" placeholder="https://facebook.com/..." class="input" />
                        </div>
                        <div>
                            <label class="label">Instagram</label>
                            <input v-model="values['social.instagram']" type="url" placeholder="https://instagram.com/..." class="input" />
                        </div>
                        <div>
                            <label class="label">Twitter / X</label>
                            <input v-model="values['social.twitter']" type="url" placeholder="https://x.com/..." class="input" />
                        </div>
                        <div>
                            <label class="label">YouTube</label>
                            <input v-model="values['social.youtube']" type="url" placeholder="https://youtube.com/..." class="input" />
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                        <h2 class="font-semibold text-gray-900">Maintenance</h2>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="values['general.maintenance_mode']" type="checkbox" class="w-4 h-4 text-indigo-600 rounded" />
                            <span class="text-sm text-gray-700 font-medium">Maintenance Mode</span>
                        </label>
                        <p class="text-xs text-gray-400">When enabled, the storefront shows a maintenance page to visitors.</p>
                    </div>

                    <button type="submit" :disabled="saving" class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2">
                        <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ saving ? 'Saving...' : 'Save Settings' }}
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ settings: Object });
const saving = ref(false);

const defaultValues = {
    'general.store_name': '', 'general.store_email': '', 'general.store_phone': '',
    'general.store_address': '', 'general.currency_symbol': '৳', 'general.maintenance_mode': false,
    'shipping.default_cost': '', 'shipping.free_threshold': '',
    'order.prefix': 'ORD-', 'order.guest_checkout': true, 'order.email_notifications': true,
    'notification.sound_enabled': true,
    'seo.meta_title': '', 'seo.meta_description': '', 'seo.ga_id': '',
    'social.facebook': '', 'social.instagram': '', 'social.twitter': '', 'social.youtube': '',
};

const values = reactive({ ...defaultValues });

// Keys that must be boolean for checkbox v-model
const booleanKeys = new Set(
    Object.keys(defaultValues).filter(k => typeof defaultValues[k] === 'boolean')
);

if (props.settings) {
    Object.entries(props.settings).forEach(([group, items]) => {
        (Array.isArray(items) ? items : Object.values(items)).forEach(s => {
            const key = `${s.group ?? group}.${s.key}`;
            if (key in values) {
                values[key] = booleanKeys.has(key)
                    ? (s.value === '1' || s.value === 1 || s.value === true)
                    : s.value;
            }
        });
    });
}

function submit() {
    saving.value = true;
    const settings = Object.entries(values).map(([dotKey, value]) => {
        const [group, ...rest] = dotKey.split('.');
        return { group, key: rest.join('.'), value };
    });
    router.post(route('admin.settings.update'), { settings }, {
        onFinish: () => { saving.value = false; },
    });
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent; }
</style>
