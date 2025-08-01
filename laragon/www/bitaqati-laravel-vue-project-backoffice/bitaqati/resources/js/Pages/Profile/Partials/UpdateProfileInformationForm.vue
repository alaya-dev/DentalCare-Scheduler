<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const { __, isRTL } = useTranslations();

const form = useForm({
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">{{ __('common.profile_information') }}</h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('common.update_profile') }}
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" :value="__('auth.name')" />

                <TextInput
                    id="name"
                    type="text"
                    :class="[
                        'mt-1 block w-full',
                        isRTL ? 'text-right' : 'text-left'
                    ]"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" :value="__('auth.email')" />

                <TextInput
                    id="email"
                    type="email"
                    :class="[
                        'mt-1 block w-full',
                        isRTL ? 'text-right' : 'text-left'
                    ]"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('common.email_unverified') }}
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        {{ __('common.resend_verification') }}
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                    {{ __('common.verification_sent') }}
                </div>
            </div>

            <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">{{ __('common.save') }}</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">{{ __('common.profile_updated') }}</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
