<template>
    <Head :title="__('client.profile')" />

    <ClientLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('client.profile') }}</h1>
                    <p class="mt-2 text-gray-600">{{ __('client.personal_info') }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Profile Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="text-center">
                                <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-2xl font-bold text-blue-600">{{ user.name.charAt(0) }}</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ user.name }}</h3>
                                <p class="text-gray-600">{{ user.email }}</p>
                                <div class="mt-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        {{ __('client.active_user') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('client.quick_stats') }}</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('client.designs_created') }}</span>
                                    <span class="font-semibold">{{ stats.designs_count || 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('client.orders_placed') }}</span>
                                    <span class="font-semibold">{{ stats.orders_count || 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('client.member_since') }}</span>
                                    <span class="font-semibold">{{ formatDate(user.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Personal Information Form -->
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('client.personal_info') }}</h3>
                            </div>
                            <div class="p-6">
                                <form @submit.prevent="updateProfile" class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('auth.name') }}
                                            </label>
                                            <input
                                                id="name"
                                                type="text"
                                                v-model="profileForm.name"
                                                required
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                :class="{ 'border-red-500': profileForm.errors.name }"
                                            />
                                            <div v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600">
                                                {{ profileForm.errors.name }}
                                            </div>
                                        </div>

                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('auth.email') }}
                                            </label>
                                            <input
                                                id="email"
                                                type="email"
                                                v-model="profileForm.email"
                                                required
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                :class="{ 'border-red-500': profileForm.errors.email }"
                                            />
                                            <div v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">
                                                {{ profileForm.errors.email }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            :disabled="profileForm.processing"
                                            class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50"
                                        >
                                            {{ profileForm.processing ? __('common.saving') : __('client.update_profile') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Change Password Form -->
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('client.change_password') }}</h3>
                            </div>
                            <div class="p-6">
                                <form @submit.prevent="updatePassword" class="space-y-6">
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('client.current_password') }}
                                        </label>
                                        <input
                                            id="current_password"
                                            type="password"
                                            v-model="passwordForm.current_password"
                                            required
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            :class="{ 'border-red-500': passwordForm.errors.current_password }"
                                        />
                                        <div v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                                            {{ passwordForm.errors.current_password }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('client.new_password') }}
                                            </label>
                                            <input
                                                id="password"
                                                type="password"
                                                v-model="passwordForm.password"
                                                required
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                :class="{ 'border-red-500': passwordForm.errors.password }"
                                            />
                                            <div v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                                                {{ passwordForm.errors.password }}
                                            </div>
                                        </div>

                                        <div>
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('client.confirm_new_password') }}
                                            </label>
                                            <input
                                                id="password_confirmation"
                                                type="password"
                                                v-model="passwordForm.password_confirmation"
                                                required
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                :class="{ 'border-red-500': passwordForm.errors.password_confirmation }"
                                            />
                                            <div v-if="passwordForm.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                                                {{ passwordForm.errors.password_confirmation }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            :disabled="passwordForm.processing"
                                            class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors disabled:opacity-50"
                                        >
                                            {{ passwordForm.processing ? __('common.saving') : __('client.change_password') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

// Props
const props = defineProps({
    user: Object,
    stats: {
        type: Object,
        default: () => ({})
    }
});

// Composables
const { __ } = useTranslations();

// Forms
const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Methods
const updateProfile = () => {
    profileForm.patch(route('client.profile.update'), {
        onSuccess: () => {
            // Show success message
        }
    });
};

const updatePassword = () => {
    passwordForm.put(route('client.password.update'), {
        onSuccess: () => {
            passwordForm.reset();
            // Show success message
        }
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ar-SA');
};
</script>
