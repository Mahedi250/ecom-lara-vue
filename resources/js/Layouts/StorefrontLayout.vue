<template>
    <div class="min-h-screen flex flex-col bg-gray-50 overflow-x-hidden">
        <Navbar />
        <main class="flex-1 pb-20 md:pb-0">
            <slot />
        </main>
        <Footer />

        <!-- Cart Drawer -->
        <CartDrawer />

        <!-- Floating Cart FAB (desktop only — mobile has bottom nav) -->
        <Transition name="fab">
            <button
                v-if="cartCount > 0"
                @click="toggleCart"
                class="hidden md:flex fixed bottom-6 right-6 z-50 w-14 h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-xl items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95"
                aria-label="Open cart"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <!-- Badge -->
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full font-bold leading-none">
                    {{ cartCount > 99 ? '99+' : cartCount }}
                </span>
                <!-- Pulse ring when items just added -->
                <span class="absolute inset-0 rounded-full bg-indigo-400 animate-ping opacity-30 pointer-events-none" />
            </button>
        </Transition>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Navbar from '@/Components/Storefront/Navbar.vue';
import Footer from '@/Components/Storefront/Footer.vue';
import CartDrawer from '@/Components/Storefront/CartDrawer.vue';
import { useCartDrawer } from '@/Composables/useCartDrawer';

const page = usePage();
const cartCount = computed(() => page.props.cart_count ?? 0);
const { toggle: toggleCart } = useCartDrawer();
</script>

<style scoped>
.fab-enter-active,
.fab-leave-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.fab-enter-from,
.fab-leave-to {
    opacity: 0;
    transform: scale(0.5) translateY(1rem);
}
</style>
