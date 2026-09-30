<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';

import InputError from '@/components/InputError.vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

const isExpanded = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);
</script>

<template>
    <div
        class="rounded-2xl border transition-all duration-200 overflow-hidden"
        :style="{
            background: 'var(--kg-surface)',
            borderColor: isExpanded ? 'rgba(226, 114, 91, 0.3)' : 'var(--kg-border)',
        }"
    >
        <!-- Header / Toggle row (always small and neat) -->
        <div
            @click="isExpanded = !isExpanded"
            class="px-5 py-4 flex items-center justify-between gap-4 cursor-pointer select-none transition-colors"
            @mouseenter="($event.currentTarget as HTMLElement).style.background = 'var(--kg-surface-hover)'"
            @mouseleave="($event.currentTarget as HTMLElement).style.background = 'transparent'"
        >
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors"
                    :style="{
                        background: isExpanded ? 'var(--kg-danger-light)' : 'var(--kg-surface-alt)',
                        color: isExpanded ? 'var(--kg-danger)' : 'var(--kg-text-muted)'
                    }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold" :style="{ color: isExpanded ? 'var(--kg-danger)' : 'var(--kg-text)' }">
                        Видалення облікового запису
                    </h3>
                    <p class="text-xs" style="color: var(--kg-text-muted);">
                        Опція остаточного видалення профілю та всіх даних
                    </p>
                </div>
            </div>

            <!-- Expand / Collapse chevron indicator -->
            <button
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5 transition-all"
                :style="{
                    background: isExpanded ? 'var(--kg-danger-light)' : 'var(--kg-surface-alt)',
                    color: isExpanded ? 'var(--kg-danger)' : 'var(--kg-text-secondary)',
                }"
            >
                <span>{{ isExpanded ? 'Приховати' : 'Розгорнути' }}</span>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3.5 w-3.5 transition-transform duration-200"
                    :class="{ 'rotate-180': isExpanded }"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <!-- Collapsible Content Section (Hidden by default to prevent accidental clicks) -->
        <div v-show="isExpanded" class="px-5 pb-5 pt-1 border-t" style="border-color: var(--kg-border);">
            <div
                class="mt-3 p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                style="background: var(--kg-danger-light); border-color: rgba(226, 114, 91, 0.25);"
            >
                <div class="space-y-1">
                    <p class="text-xs font-semibold" style="color: var(--kg-danger);">
                        Увага: цю дію неможливо скасувати
                    </p>
                    <p class="text-xs max-w-lg leading-relaxed" style="color: var(--kg-text-secondary);">
                        Всі ваші оголошення, історія переписок та збережені налаштування будуть видалені назавжди без можливості відновлення.
                    </p>
                </div>

                <!-- Delete Confirmation Dialog Trigger -->
                <Dialog>
                    <DialogTrigger as-child>
                        <button
                            type="button"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-white transition-all shadow-sm flex-shrink-0 hover:opacity-90 cursor-pointer"
                            style="background: var(--kg-danger);"
                            data-test="delete-user-button"
                        >
                            Видалити мій акаунт
                        </button>
                    </DialogTrigger>
                    <DialogContent style="background: var(--kg-surface); border-color: var(--kg-border); color: var(--kg-text);">
                        <Form
                            v-bind="ProfileController.destroy.form()"
                            reset-on-success
                            @error="() => passwordInput?.focus()"
                            :options="{
                                preserveScroll: true,
                            }"
                            class="space-y-6"
                            v-slot="{ errors, processing, reset, clearErrors }"
                        >
                            <DialogHeader class="space-y-3">
                                <DialogTitle class="text-lg font-heading text-red-600 dark:text-red-400">
                                    Підтвердження видалення акаунту
                                </DialogTitle>
                                <DialogDescription style="color: var(--kg-text-secondary);" class="text-sm">
                                    Після підтвердження всі ваші оголошення та особисті дані будуть видалені назавжди.
                                    Будь ласка, введіть свій поточний пароль для підтвердження.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="space-y-2">
                                <label for="delete-password" class="block text-sm font-semibold" style="color: var(--kg-text);">
                                    Ваш пароль
                                </label>
                                <input
                                    id="delete-password"
                                    ref="passwordInput"
                                    type="password"
                                    name="password"
                                    required
                                    placeholder="Введіть пароль"
                                    class="w-full px-4 py-2.5 border rounded-xl text-sm outline-none transition-all focus:ring-2"
                                    :style="{
                                        background: 'var(--kg-input-bg)',
                                        borderColor: 'var(--kg-border)',
                                        color: 'var(--kg-text)'
                                    }"
                                />
                                <InputError :message="errors.password" />
                            </div>

                            <DialogFooter class="gap-2 sm:gap-0">
                                <DialogClose as-child>
                                    <button
                                        type="button"
                                        class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all cursor-pointer"
                                        :style="{
                                            background: 'var(--kg-surface-alt)',
                                            color: 'var(--kg-text-secondary)'
                                        }"
                                        @click="() => { clearErrors(); reset(); }"
                                    >
                                        Скасувати
                                    </button>
                                </DialogClose>

                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all disabled:opacity-50 ml-2 cursor-pointer"
                                    style="background: var(--kg-danger);"
                                    data-test="confirm-delete-user-button"
                                >
                                    {{ processing ? 'Видалення...' : 'Так, видалити акаунт' }}
                                </button>
                            </DialogFooter>
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    </div>
</template>
