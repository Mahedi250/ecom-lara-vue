<template>
    <StorefrontLayout :title="title">
        <div class="min-h-[60vh] flex items-center justify-center px-4">
            <div class="text-center max-w-md">

                <div class="text-[120px] font-black text-gray-100 leading-none select-none">
                    {{ status }}
                </div>

                <h1 class="text-2xl font-bold text-gray-900 -mt-4 mb-3">{{ title }}</h1>
                <p class="text-gray-500 text-sm mb-8">{{ description }}</p>

                <div class="flex items-center justify-center gap-3">
                    <a href="/"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Go Home
                    </a>
                    <button @click="$router ? $router.back() : history.back()"
                        class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 hover:bg-gray-50 font-semibold px-6 py-3 rounded-xl transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Go Back
                    </button>
                </div>

            </div>
        </div>
    </StorefrontLayout>
</template>

<script setup>
import { computed } from 'vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const props = defineProps({ status: Number });

const title = computed(() => ({
    404: 'Page Not Found',
    403: 'Access Forbidden',
    500: 'Server Error',
    503: 'Service Unavailable',
}[props.status] ?? 'Something Went Wrong'));

const description = computed(() => ({
    404: 'The page you are looking for does not exist or has been moved.',
    403: 'You do not have permission to access this page.',
    500: 'Something went wrong on our end. Please try again later.',
    503: 'We are down for maintenance. Please check back soon.',
}[props.status] ?? 'An unexpected error occurred.'));
</script>
