<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import InputError from '@/components/InputError.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <SettingsLayout>
        <Head title="Налаштування профілю — knygogo" />

        <!-- Profile Information Card -->
        <div class="rounded-2xl border p-6 sm:p-8 transition-all" style="background: var(--kg-surface); border-color: var(--kg-border);">
            <div class="border-b pb-5 mb-6" style="border-color: var(--kg-border);">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-lg font-bold flex-shrink-0" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <h2 class="text-xl font-heading" style="color: var(--kg-text);">Особисті дані</h2>
                        <p class="text-sm mt-0.5" style="color: var(--kg-text-muted);">
                            Оновіть своє ім'я та адресу електронної пошти
                        </p>
                    </div>
                </div>
            </div>

            <Form v-bind="ProfileController.update.form()" class="space-y-6" v-slot="{ errors, processing, recentlySuccessful }">
                <div class="grid sm:grid-cols-2 gap-5 sm:gap-6">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-semibold" style="color: var(--kg-text);">
                            Ваше ім'я *
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            :value="user.name"
                            required
                            autocomplete="name"
                            placeholder="Ваше ім'я"
                            class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2"
                            :style="{
                                background: 'var(--kg-input-bg)',
                                borderColor: 'var(--kg-border)',
                                color: 'var(--kg-text)'
                            }"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold" style="color: var(--kg-text);">
                            Email адреса *
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            :value="user.email"
                            required
                            autocomplete="username"
                            placeholder="name@example.com"
                            class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2"
                            :style="{
                                background: 'var(--kg-input-bg)',
                                borderColor: 'var(--kg-border)',
                                color: 'var(--kg-text)'
                            }"
                        />
                        <InputError :message="errors.email" />
                    </div>
                </div>

                <div v-if="mustVerifyEmail && !user.email_verified_at" class="p-4 rounded-xl border" style="background: var(--kg-surface-alt); border-color: var(--kg-border);">
                    <p class="text-sm" style="color: var(--kg-text-secondary);">
                        Ваша електронна пошта не підтверджена.
                        <Link
                            :href="send()"
                            as="button"
                            class="font-semibold underline ml-1 transition-colors"
                            style="color: var(--kg-accent);"
                        >
                            Надіслати лист для підтвердження ще раз.
                        </Link>
                    </p>

                    <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium" style="color: var(--kg-accent);">
                        Нове посилання для підтвердження надіслано на вашу електронну пошту.
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <button
                        type="submit"
                        :disabled="processing"
                        class="px-7 py-3 text-white font-semibold rounded-xl text-sm transition-all shadow-md hover:-translate-y-0.5 disabled:opacity-60 cursor-pointer"
                        :style="{ background: 'var(--kg-accent)', boxShadow: '0 4px 16px var(--kg-accent-shadow)' }"
                        data-test="update-profile-button"
                    >
                        {{ processing ? 'Зберігаємо...' : 'Зберегти зміни' }}
                    </button>

                    <Transition
                        enter-active-class="transition ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition ease-in duration-200"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <span v-show="recentlySuccessful" class="inline-flex items-center gap-1.5 text-sm font-medium" style="color: var(--kg-accent);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Збережено!
                        </span>
                    </Transition>
                </div>
            </Form>
        </div>

        <!-- Danger Zone: Delete User -->
        <DeleteUser />
    </SettingsLayout>
</template>
