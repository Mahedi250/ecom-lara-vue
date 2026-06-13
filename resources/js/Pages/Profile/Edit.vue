<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});
</script>

<template>
    <Head title="My Profile" />

    <CustomerLayout>
        <div class="space-y-3">

            <!-- Profile info card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-50 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm">Personal Information</p>
                        <p class="text-xs text-gray-500">Update your name and email</p>
                    </div>
                </div>
                <div class="p-4">
                    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
                </div>
            </div>

            <!-- Password card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-50 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm">Change Password</p>
                        <p class="text-xs text-gray-500">Keep your account secure</p>
                    </div>
                </div>
                <div class="p-4">
                    <UpdatePasswordForm />
                </div>
            </div>

            <!-- Danger zone card -->
            <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-4 py-4 border-b border-red-50 bg-gradient-to-r from-red-50 to-rose-50">
                    <div class="w-9 h-9 bg-red-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-red-700 text-sm">Danger Zone</p>
                        <p class="text-xs text-red-400">Permanent account actions</p>
                    </div>
                </div>
                <div class="p-4">
                    <DeleteUserForm />
                </div>
            </div>

        </div>
    </CustomerLayout>
</template>
