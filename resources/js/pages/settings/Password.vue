<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import InputError from '@/components/InputError.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);
</script>

<template>
    <SettingsLayout>
        <Head title="Зміна пароля — knygogo" />

        <div class="rounded-2xl border p-6 sm:p-8 transition-all" style="background: var(--kg-surface); border-color: var(--kg-border);">
            <div class="border-b pb-5 mb-6" style="border-color: var(--kg-border);">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-heading" style="color: var(--kg-text);">Зміна пароля</h2>
                        <p class="text-sm mt-0.5" style="color: var(--kg-text-muted);">
                            Для безпеки акаунту використовуйте довгий та надійний пароль
                        </p>
                    </div>
                </div>
            </div>

            <Form
                v-bind="PasswordController.update.form()"
                :options="{
                    preserveScroll: true,
                }"
                reset-on-success
                :reset-on-error="['password', 'password_confirmation', 'current_password']"
                class="space-y-6 max-w-xl"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <div class="space-y-2">
                    <label for="current_password" class="block text-sm font-semibold" style="color: var(--kg-text);">
                        Поточний пароль *
                    </label>
                    <input
                        id="current_password"
                        ref="currentPasswordInput"
                        name="current_password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2"
                        :style="{
                            background: 'var(--kg-input-bg)',
                            borderColor: 'var(--kg-border)',
                            color: 'var(--kg-text)'
                        }"
                    />
                    <InputError :message="errors.current_password" />
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold" style="color: var(--kg-text);">
                        Новий пароль *
                    </label>
                    <input
                        id="password"
                        ref="passwordInput"
                        name="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Мінімум 8 символів"
                        class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2"
                        :style="{
                            background: 'var(--kg-input-bg)',
                            borderColor: 'var(--kg-border)',
                            color: 'var(--kg-text)'
                        }"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-semibold" style="color: var(--kg-text);">
                        Підтвердження нового пароля *
                    </label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Повторіть новий пароль"
                        class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2"
                        :style="{
                            background: 'var(--kg-input-bg)',
                            borderColor: 'var(--kg-border)',
                            color: 'var(--kg-text)'
                        }"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <button
                        type="submit"
                        :disabled="processing"
                        class="px-7 py-3 text-white font-semibold rounded-xl text-sm transition-all shadow-md hover:-translate-y-0.5 disabled:opacity-60 cursor-pointer"
                        :style="{ background: 'var(--kg-accent)', boxShadow: '0 4px 16px var(--kg-accent-shadow)' }"
                        data-test="update-password-button"
                    >
                        {{ processing ? 'Оновлюємо...' : 'Оновити пароль' }}
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
                            Пароль успішно змінено!
                        </span>
                    </Transition>
                </div>
            </Form>
        </div>
    </SettingsLayout>
</template>
