<template>
    <div class="min-h-screen bg-gray-50">

        <!-- Mobile backdrop -->
        <Transition name="fade">
            <div v-if="sidebarOpen" class="fixed inset-0 bg-black/50 z-20 lg:hidden" @click="sidebarOpen = false" />
        </Transition>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 w-64 bg-slate-900 z-30 flex flex-col transition-transform duration-300"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <!-- Logo -->
            <div class="flex items-center gap-3 px-5 h-16 border-b border-white/10 flex-shrink-0">
                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                    <img v-if="siteSettings?.logo_url" :src="siteSettings.logo_url" alt="" class="w-full h-full object-cover" />
                    <svg v-else class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-bold text-sm leading-none">{{ siteSettings?.store_name || 'নিত্য গ্যাজেট' }}</p>
                    <p class="text-slate-400 text-xs mt-0.5">Admin Panel</p>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-4">

                <!-- Main -->
                <div>
                    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider px-3 mb-1">Main</p>
                    <Link :href="route('admin.dashboard')" class="nav-item" :class="isActive('admin.dashboard') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="flex-1">Dashboard</span>
                    </Link>
                </div>

                <!-- Catalog -->
                <div>
                    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider px-3 mb-1">Catalog</p>
                    <Link :href="route('admin.products.index')" class="nav-item" :class="isActive('admin.products') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span class="flex-1">Products</span>
                        <span v-if="stats?.total_products" class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-indigo-400/30 text-indigo-300">{{ stats.total_products }}</span>
                    </Link>
                    <Link :href="route('admin.categories.index')" class="nav-item" :class="isActive('admin.categories') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v1H3V7zm0 4h18v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6z"/></svg>
                        <span class="flex-1">Categories</span>
                    </Link>
                    <Link :href="route('admin.brands.index')" class="nav-item" :class="isActive('admin.brands') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span class="flex-1">Brands</span>
                    </Link>
                    <Link :href="route('admin.media.index')" class="nav-item" :class="isActive('admin.media') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="flex-1">Media</span>
                    </Link>
                    <Link :href="route('admin.attributes.index')" class="nav-item" :class="isActive('admin.attributes') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                        <span class="flex-1">Attributes</span>
                    </Link>
                    <Link :href="route('admin.customers.index')" class="nav-item" :class="isActive('admin.customers') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="flex-1">Customers</span>
                    </Link>
                </div>

                <!-- Sales -->
                <div>
                    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider px-3 mb-1">Sales</p>
                    <Link :href="route('admin.orders.index')" class="nav-item" :class="isActive('admin.orders') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span class="flex-1">Orders</span>
                        <span v-if="stats?.pending_orders" class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-yellow-400 text-yellow-900">{{ stats.pending_orders }}</span>
                    </Link>
                    <Link :href="route('admin.coupons.index')" class="nav-item" :class="isActive('admin.coupons') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        <span class="flex-1">Coupons</span>
                    </Link>
                </div>

                <!-- People -->
                <div>
                    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider px-3 mb-1">People</p>
                    <Link :href="route('admin.users.index')" class="nav-item" :class="isActive('admin.users') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="flex-1">Users</span>
                    </Link>
                </div>

                <!-- System -->
                <div>
                    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider px-3 mb-1">System</p>
                    <Link :href="route('admin.site-setup.index')" class="nav-item" :class="isActive('admin.site-setup') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="flex-1">Site Setup</span>
                    </Link>
                    <Link :href="route('admin.pages.index')" class="nav-item" :class="isActive('admin.pages') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="flex-1">Pages</span>
                    </Link>
                    <Link :href="route('admin.settings.index')" class="nav-item" :class="isActive('admin.settings') ? 'nav-active' : 'nav-default'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="flex-1">Settings</span>
                    </Link>
                </div>

            </nav>

            <!-- User footer -->
            <div class="border-t border-white/10 p-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ auth?.name?.[0]?.toUpperCase() ?? 'A' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ auth?.name }}</p>
                        <p class="text-slate-400 text-xs truncate">{{ auth?.email }}</p>
                    </div>
                    <Link :href="route('admin.logout')" method="post" as="button" class="text-slate-400 hover:text-red-400 transition p-1 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main content area — offset by sidebar width on desktop -->
        <div class="lg:pl-64 flex flex-col min-h-screen">

            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center px-4 sm:px-6 gap-4 sticky top-0 z-10">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-800 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex-1">
                    <h1 class="text-lg font-semibold text-gray-900">{{ title }}</h1>
                    <p v-if="subtitle" class="text-xs text-gray-500 leading-none mt-0.5">{{ subtitle }}</p>
                </div>

                <slot name="header-actions" />

                <NotificationBell @new-notification="onNewNotification" />

                <a href="/" target="_blank" class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 hover:text-indigo-600 border border-gray-200 rounded-lg px-3 py-1.5 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Storefront
                </a>
            </header>

            <!-- Flash -->
            <Transition name="slide-down">
                <div v-if="flash?.success" class="mx-4 sm:mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 flex items-center gap-3 text-sm">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ flash.success }}
                    <button @click="dismissFlash" class="ml-auto text-green-500 hover:text-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </Transition>
            <Transition name="slide-down">
                <div v-if="flash?.error" class="mx-4 sm:mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 flex items-center gap-3 text-sm">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{ flash.error }}
                </div>
            </Transition>

            <!-- Page content -->
            <main class="flex-1 p-4 sm:p-6">
                <slot />
            </main>
        </div>

        <!-- Toast notifications -->
        <div class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 items-end pointer-events-none">
            <TransitionGroup name="toast">
                <div v-for="toast in toasts" :key="toast.id"
                    class="pointer-events-auto flex items-start gap-3 bg-white border border-gray-200 shadow-xl rounded-2xl px-4 py-3 max-w-xs w-full cursor-pointer hover:shadow-2xl hover:border-indigo-200 transition-shadow"
                    @click="toast.url ? (window.location.href = toast.url) : null">
                    <div class="text-xl flex-shrink-0 mt-0.5">{{ toast.icon }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900">{{ toast.title }}</p>
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ toast.message }}</p>
                    </div>
                    <button type="button" @click.stop="dismissToast(toast.id)"
                        class="text-gray-300 hover:text-gray-500 flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NotificationBell from '@/Components/Admin/NotificationBell.vue';

defineProps({
    title: { type: String, default: 'Dashboard' },
    subtitle: { type: String, default: '' },
});

const page = usePage();
const auth = computed(() => page.props.auth?.user);
const flashDismissed = ref(false);
const flash = computed(() => flashDismissed.value ? null : page.props.flash);
const stats = computed(() => page.props.admin_stats ?? null);
const siteSettings = computed(() => page.props.site_settings ?? null);
const sidebarOpen = ref(false);
const toasts = ref([]);
let toastSeq = 0;

watch(() => page.props.flash, (newFlash) => {
    if (newFlash?.success || newFlash?.error) {
        flashDismissed.value = false;
    }
});

function dismissFlash() { flashDismissed.value = true; }

function isActive(routeName) {
    const url = page.url;
    if (routeName === 'admin.dashboard') return url === '/admin' || url === '/admin/';
    const segment = routeName.replace('admin.', '').replace('.index', '').replace('.', '/');
    return url.startsWith('/admin/' + segment);
}

function onNewNotification(n) {
    const id = ++toastSeq;
    const icons = { 'order.placed': '🛒', 'order.processing': '✅', 'order.delivered': '📦' };
    const titles = { 'order.placed': 'New Order!', 'order.processing': 'Order Confirmed', 'order.delivered': 'Order Delivered' };

    toasts.value.push({
        id,
        icon: icons[n.event] ?? '🔔',
        title: titles[n.event] ?? 'Notification',
        message: n.message,
        url: n.url ?? null,
    });

    if (page.props.notification_sound !== false) playSound();

    setTimeout(() => dismissToast(id), 6000);
}

function dismissToast(id) {
    toasts.value = toasts.value.filter(t => t.id !== id);
}

function playSound() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        [[880, 0, 0.15], [1100, 0.18, 0.15]].forEach(([freq, start, dur]) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.value = freq;
            gain.gain.setValueAtTime(0.25, ctx.currentTime + start);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + start + dur);
            osc.start(ctx.currentTime + start);
            osc.stop(ctx.currentTime + start + dur + 0.05);
        });
    } catch {
        // browser blocked audio
    }
}
</script>

<style scoped>
.nav-item { @apply flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all w-full; }
.nav-active { @apply bg-indigo-600 text-white shadow-sm; }
.nav-default { @apply text-slate-400 hover:text-white hover:bg-slate-800; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-0.5rem); }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateX(100%); }
.toast-move { transition: transform 0.3s ease; }
</style>
