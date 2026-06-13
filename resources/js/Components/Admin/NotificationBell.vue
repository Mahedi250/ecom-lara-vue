<template>
    <div class="relative" ref="bellRef">
        <!-- Bell button -->
        <button type="button" @click="toggleDropdown"
            class="relative w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1 leading-none">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <Transition name="dropdown">
            <div v-if="open"
                class="absolute right-0 top-11 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 flex flex-col overflow-hidden">

                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <span class="font-semibold text-gray-900 text-sm">Notifications</span>
                    <button v-if="unreadCount > 0" type="button" @click="markAllRead"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                        Mark all read
                    </button>
                </div>

                <!-- List -->
                <div class="overflow-y-auto max-h-[360px]">
                    <div v-if="!notifications.length" class="py-10 text-center text-gray-400 text-sm">
                        No notifications yet
                    </div>

                    <a v-for="n in notifications" :key="n.id"
                        :href="n.url ?? '#'"
                        @click.prevent="handleClick(n)"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 cursor-pointer"
                        :class="!n.read ? 'bg-indigo-50/50' : ''">

                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm"
                                :class="iconClass(n.event)">
                                {{ iconEmoji(n.event) }}
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800 font-medium leading-snug">{{ n.message }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span v-if="n.amount" class="text-xs text-gray-500">৳{{ n.amount.toLocaleString() }}</span>
                                <span class="text-xs text-gray-400">{{ n.time_ago }}</span>
                            </div>
                        </div>

                        <div v-if="!n.read" class="flex-shrink-0 mt-1.5">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                        </div>
                    </a>
                </div>

                <!-- Footer -->
                <div class="px-4 py-2.5 border-t border-gray-100 bg-gray-50">
                    <a :href="route('admin.orders.index')"
                        class="block text-center text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                        View all orders →
                    </a>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const emit = defineEmits(['new-notification']);

const open = ref(false);
const unreadCount = ref(0);
const notifications = ref([]);
const bellRef = ref(null);
let pollTimer = null;
let prevUnreadCount = -1;

function toggleDropdown() {
    open.value = !open.value;
}

function iconEmoji(event) {
    return { 'order.placed': '🛒', 'order.processing': '✅', 'order.delivered': '📦' }[event] ?? '🔔';
}

function iconClass(event) {
    return {
        'order.placed':     'bg-yellow-100',
        'order.processing': 'bg-green-100',
        'order.delivered':  'bg-blue-100',
    }[event] ?? 'bg-gray-100';
}

async function fetchNotifications(isInitial = false) {
    try {
        const { data } = await window.axios.get(route('admin.notifications.index'));
        const newCount = data.unread_count;

        if (!isInitial && prevUnreadCount !== -1 && newCount > prevUnreadCount) {
            const newOnes = data.notifications.filter(n => !n.read).slice(0, newCount - prevUnreadCount);
            newOnes.forEach(n => emit('new-notification', n));
        }

        prevUnreadCount = newCount;
        unreadCount.value = newCount;
        notifications.value = data.notifications;
    } catch {
        // silent
    }
}

async function markAllRead() {
    await window.axios.post(route('admin.notifications.readAll'));
    notifications.value = notifications.value.map(n => ({ ...n, read: true }));
    unreadCount.value = 0;
    prevUnreadCount = 0;
}

async function handleClick(n) {
    if (!n.read) {
        await window.axios.post(route('admin.notifications.read', n.id));
        n.read = true;
        unreadCount.value = Math.max(0, unreadCount.value - 1);
        prevUnreadCount = unreadCount.value;
    }
    open.value = false;
    if (n.url) window.location.href = n.url;
}

function onClickOutside(e) {
    if (bellRef.value && !bellRef.value.contains(e.target)) {
        open.value = false;
    }
}

onMounted(() => {
    fetchNotifications(true);
    pollTimer = setInterval(() => fetchNotifications(false), 30_000);
    document.addEventListener('click', onClickOutside);
});

onUnmounted(() => {
    clearInterval(pollTimer);
    document.removeEventListener('click', onClickOutside);
});
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active { transition: opacity 0.15s, transform 0.15s; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-6px) scale(0.97); }
</style>
