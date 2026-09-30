<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();
function toggleTheme() {
    updateAppearance(appearance.value === 'dark' ? 'light' : 'dark');
}
</script>

<template>
    <div class="min-h-screen" style="background: var(--kg-bg);">
        <Head title="Реєстрація — knygogo" />

        <!-- Header -->
        <header class="sticky top-0 z-50 backdrop-blur-xl border-b" style="background: var(--kg-header-bg); border-color: var(--kg-border);">
            <nav class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <Link href="/" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110 group-hover:rotate-[-4deg]" style="background: var(--kg-accent);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="text-xl font-heading tracking-tight" style="color: var(--kg-text);">knygogo<span style="color: var(--kg-accent);">.</span></span>
                    </Link>
                    <button @click="toggleTheme" class="p-2 rounded-xl transition-all" style="color: var(--kg-text-muted);">
                        <svg v-if="appearance === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </nav>
        </header>

        <div class="container mx-auto px-4 py-12 sm:py-16">
            <div class="max-w-md mx-auto">
                <div class="rounded-2xl border p-6 sm:p-8" style="background: var(--kg-surface); border-color: var(--kg-border);">
                    <h2 class="text-2xl sm:text-3xl font-heading mb-2" style="color: var(--kg-text);">
                        Створити акаунт
                    </h2>
                    <p class="mb-8" style="color: var(--kg-text-muted);">
                        Заповніть форму нижче, щоб зареєструватися
                    </p>

                    <Form
                        v-bind="RegisteredUserController.store.form()"
                        :reset-on-success="['password', 'password_confirmation']"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name" class="font-medium" :style="{ color: 'var(--kg-text-secondary)' }">Ім'я</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    name="name"
                                    required
                                    autofocus
                                    :tabindex="1"
                                    autocomplete="name"
                                    placeholder="Ваше ім'я"
                                    class="w-full px-4 py-2.5 rounded-xl border transition-all focus:ring-2"
                                    :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }"
                                />
                                <InputError :message="errors.name" :style="{ color: 'var(--kg-danger)' }" class="text-sm" />
                            </div>

                            <div class="space-y-2">
                                <Label for="email" class="font-medium" :style="{ color: 'var(--kg-text-secondary)' }">Електронна пошта</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    :tabindex="2"
                                    autocomplete="email"
                                    placeholder="email@example.com"
                                    class="w-full px-4 py-2.5 rounded-xl border transition-all focus:ring-2"
                                    :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }"
                                />
                                <InputError :message="errors.email" :style="{ color: 'var(--kg-danger)' }" class="text-sm" />
                            </div>

                            <div class="space-y-2">
                                <Label for="password" class="font-medium" :style="{ color: 'var(--kg-text-secondary)' }">Пароль</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    :tabindex="3"
                                    autocomplete="new-password"
                                    placeholder="Мінімум 8 символів"
                                    class="w-full px-4 py-2.5 rounded-xl border transition-all focus:ring-2"
                                    :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }"
                                />
                                <InputError :message="errors.password" :style="{ color: 'var(--kg-danger)' }" class="text-sm" />
                            </div>

                            <div class="space-y-2">
                                <Label for="password_confirmation" class="font-medium" :style="{ color: 'var(--kg-text-secondary)' }">Підтвердити пароль</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    :tabindex="4"
                                    autocomplete="new-password"
                                    placeholder="Повторіть пароль"
                                    class="w-full px-4 py-2.5 rounded-xl border transition-all focus:ring-2"
                                    :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }"
                                />
                                <InputError :message="errors.password_confirmation" :style="{ color: 'var(--kg-danger)' }" class="text-sm" />
                            </div>
                        </div>

                        <Button
                            type="submit"
                            class="w-full px-6 py-3 rounded-xl text-white font-semibold transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5"
                            :style="{ background: 'var(--kg-accent)', boxShadow: '0 4px 16px var(--kg-accent-shadow)' }"
                            :tabindex="5"
                            :disabled="processing"
                        >
                            <LoaderCircle v-if="processing" class="h-5 w-5 animate-spin mr-2" />
                            Зареєструватися
                        </Button>

                        <div class="text-center" style="color: var(--kg-text-muted);">
                            Вже маєте акаунт?
                            <Link
                                :href="login()"
                                class="font-semibold ml-1 transition-colors"
                                :style="{ color: 'var(--kg-accent)' }"
                                :tabindex="6"
                            >
                                Увійти
                            </Link>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
