<template>
    <div class="min-h-screen flex">
        <Head title="Create Account" />

        <!-- Left Panel -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-purple-600 via-indigo-700 to-indigo-800 relative overflow-hidden flex-col justify-between p-12">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-16 -right-16 w-80 h-80 bg-white/5 rounded-full"></div>
                <div class="absolute top-1/2 -left-20 w-64 h-64 bg-white/5 rounded-full"></div>
                <div class="absolute -bottom-12 right-1/4 w-56 h-56 bg-purple-400/10 rounded-full"></div>
            </div>

            <div class="relative">
                <Link href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-bold text-xl leading-none">নিত্য গ্যাজেট</p>
                        <p class="text-indigo-200 text-xs">Best Gadgets Always</p>
                    </div>
                </Link>
            </div>

            <div class="relative">
                <h1 class="text-4xl font-bold text-white leading-tight mb-4">
                    Join thousands of happy shoppers
                </h1>
                <p class="text-indigo-200 text-lg mb-10">
                    Create your free account and start shopping the best deals today.
                </p>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-2xl font-bold text-white mb-1">50K+</p>
                        <p class="text-indigo-200 text-sm">Happy customers</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-2xl font-bold text-white mb-1">10K+</p>
                        <p class="text-indigo-200 text-sm">Products available</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-2xl font-bold text-white mb-1">99%</p>
                        <p class="text-indigo-200 text-sm">Satisfaction rate</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-2xl font-bold text-white mb-1">24/7</p>
                        <p class="text-indigo-200 text-sm">Customer support</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <p class="text-indigo-300 text-sm">© 2025 নিত্য গ্যাজেট. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="flex-1 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16 bg-white overflow-y-auto">
            <div class="lg:hidden mb-8 flex justify-center">
                <Link href="/" class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-gray-900">নিত্য গ্যাজেট</span>
                </Link>
            </div>

            <div class="w-full max-w-md mx-auto">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Create your account</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Already have an account?
                        <Link :href="route('login')" class="text-indigo-600 font-medium hover:text-indigo-700">Sign in</Link>
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                autocomplete="name"
                                required
                                autofocus
                                placeholder="John Doe"
                                class="block w-full pl-10 pr-4 py-2.5 text-sm border rounded-lg transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="form.errors.name ? 'border-red-300 bg-red-50' : 'border-gray-300 bg-white'"
                            />
                        </div>
                        <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                autocomplete="username"
                                required
                                placeholder="you@example.com"
                                class="block w-full pl-10 pr-4 py-2.5 text-sm border rounded-lg transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="form.errors.email ? 'border-red-300 bg-red-50' : 'border-gray-300 bg-white'"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                required
                                placeholder="Min. 8 characters"
                                class="block w-full pl-10 pr-10 py-2.5 text-sm border rounded-lg transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="form.errors.password ? 'border-red-300 bg-red-50' : 'border-gray-300 bg-white'"
                            />
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                                <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                required
                                placeholder="Repeat password"
                                class="block w-full pl-10 pr-4 py-2.5 text-sm border rounded-lg transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="form.errors.password_confirmation ? 'border-red-300 bg-red-50' : 'border-gray-300 bg-white'"
                            />
                        </div>
                        <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-red-600">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-200"
                    >
                        <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ form.processing ? 'Creating account...' : 'Create account' }}
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-400">
                        By creating an account you agree to our
                        <a href="#" class="text-indigo-600 hover:underline">Terms of Service</a>
                        and
                        <a href="#" class="text-indigo-600 hover:underline">Privacy Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const showPassword = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>
