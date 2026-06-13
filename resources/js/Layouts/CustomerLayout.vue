<template>
    <StorefrontLayout>
        <div class="min-h-screen bg-gray-50">

            <!-- Mobile hero header -->
            <div class="lg:hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 pt-6 pb-6 px-4 relative overflow-hidden">
                <!-- Background shapes -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full"></div>
                    <div class="absolute bottom-0 -left-10 w-32 h-32 bg-purple-500/20 rounded-full"></div>
                </div>
                <div class="relative flex items-center gap-4">
                    <!-- Avatar -->
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0 border border-white/30">
                        <span class="text-white font-bold text-xl">{{ initials }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-white font-bold text-lg leading-tight truncate">{{ user.name }}</p>
                        <p class="text-indigo-200 text-xs truncate mt-0.5">{{ user.email }}</p>
                    </div>
                </div>
            </div>

            <!-- Mobile nav tabs (separate from hero, no overlap) -->
            <div class="lg:hidden bg-white border-b border-gray-100 shadow-sm">
                <div class="flex overflow-x-auto scrollbar-hide px-3 py-2 gap-2">
                    <Link
                        v-for="item in navItems" :key="item.route"
                        :href="route(item.route)"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition flex-shrink-0 border"
                        :class="isActive(item.route)
                            ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm'
                            : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-indigo-300 hover:text-indigo-600'"
                    >
                        <component :is="item.icon" class="w-3.5 h-3.5" />
                        {{ item.label }}
                    </Link>
                    <Link
                        :href="route('logout')" method="post" as="button"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap border border-gray-200 bg-gray-50 text-red-500 hover:bg-red-50 hover:border-red-200 transition flex-shrink-0"
                    >
                        <LogoutIcon class="w-3.5 h-3.5" />
                        Sign out
                    </Link>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-28 md:pb-10">
                <!-- Mobile content (no pull-up, clean separation) -->
                <div class="lg:hidden pt-4">
                    <slot />
                </div>

                <!-- Desktop layout -->
                <div class="hidden lg:flex gap-6 items-start pt-8">
                    <!-- Sidebar -->
                    <aside class="w-60 flex-shrink-0 sticky top-24">
                        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-5 mb-3 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-white/5 rounded-full -translate-y-8 translate-x-8"></div>
                            <div class="relative flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 border border-white/30">
                                    <span class="text-white font-bold text-lg">{{ initials }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-white font-bold text-sm truncate">{{ user.name }}</p>
                                    <p class="text-indigo-200 text-xs truncate">{{ user.email }}</p>
                                </div>
                            </div>
                        </div>

                        <nav class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <Link
                                v-for="item in navItems" :key="item.route"
                                :href="route(item.route)"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm transition border-b border-gray-50 last:border-0 group"
                                :class="isActive(item.route)
                                    ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                            >
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition"
                                    :class="isActive(item.route) ? 'bg-indigo-600' : 'bg-gray-100 group-hover:bg-indigo-100'">
                                    <component :is="item.icon" class="w-3.5 h-3.5 transition"
                                        :class="isActive(item.route) ? 'text-white' : 'text-gray-500 group-hover:text-indigo-600'" />
                                </div>
                                {{ item.label }}
                                <svg v-if="isActive(item.route)" class="w-3 h-3 ml-auto text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </Link>
                            <Link
                                :href="route('logout')" method="post" as="button"
                                class="w-full flex items-center gap-3 px-4 py-3.5 text-sm text-red-500 hover:bg-red-50 transition group"
                            >
                                <div class="w-7 h-7 rounded-lg bg-red-50 group-hover:bg-red-100 flex items-center justify-center flex-shrink-0 transition">
                                    <LogoutIcon class="w-3.5 h-3.5 text-red-400" />
                                </div>
                                Sign out
                            </Link>
                        </nav>
                    </aside>

                    <div class="flex-1 min-w-0">
                        <slot />
                    </div>
                </div>
            </div>

        </div>
    </StorefrontLayout>
</template>

<script setup>
import { computed, h } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const initials = computed(() => {
    if (!user.value?.name) return '?';
    return user.value.name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
});

function isActive(routeName) {
    try {
        const url = page.url;
        const target = route(routeName);
        return url === target || url.startsWith(target + '/');
    } catch { return false; }
}

const DashboardIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' }),
]) };
const OrdersIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' }),
]) };
const ProfileIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' }),
]) };
const LogoutIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1' }),
]) };

const navItems = [
    { label: 'Dashboard', route: 'dashboard', icon: DashboardIcon },
    { label: 'My Orders', route: 'orders.index', icon: OrdersIcon },
    { label: 'Profile', route: 'profile.edit', icon: ProfileIcon },
];
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
