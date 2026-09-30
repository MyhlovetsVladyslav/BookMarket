<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const { appearance, updateAppearance } = useAppearance();
function toggleTheme() {
    updateAppearance(appearance.value === 'dark' ? 'light' : 'dark');
}
</script>

<template>
    <div class="min-h-screen" style="background: var(--kg-bg);">
        <Head title="Увійти — knygogo" />

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
                        З поверненням!
                    </h2>
                    <p class="mb-8" style="color: var(--kg-text-muted);">
                        Введіть свою електронну пошту та пароль для входу
                    </p>

                    <div v-if="status" class="mb-6 p-4 rounded-xl text-sm font-medium" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                        {{ status }}
                    </div>

                    <Form
                        v-bind="AuthenticatedSessionController.store.form()"
                        :reset-on-success="['password']"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label for="email" class="font-medium" :style="{ color: 'var(--kg-text-secondary)' }">Електронна пошта</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autofocus
                                    :tabindex="1"
                                    autocomplete="email"
                                    placeholder="email@example.com"
                                    class="w-full px-4 py-2.5 rounded-xl border transition-all focus:ring-2"
                                    :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }"
                                />
                                <InputError :message="errors.email" :style="{ color: 'var(--kg-danger)' }" class="text-sm" />
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label for="password" class="font-medium" :style="{ color: 'var(--kg-text-secondary)' }">Пароль</Label>
                                    <Link
                                        v-if="canResetPassword"
                                        :href="request()"
                                        class="text-sm transition-colors"
                                        :style="{ color: 'var(--kg-accent)' }"
                                        :tabindex="5"
                                    >
                                        Забули пароль?
                                    </Link>
                                </div>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    :tabindex="2"
                                    autocomplete="current-password"
                                    placeholder="Ваш пароль"
                                    class="w-full px-4 py-2.5 rounded-xl border transition-all focus:ring-2"
                                    :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }"
                                />
                                <InputError :message="errors.password" :style="{ color: 'var(--kg-danger)' }" class="text-sm" />
                            </div>

                            <div class="flex items-center space-x-2">
                                <Checkbox id="remember" name="remember" :tabindex="3" />
                                <Label for="remember" :style="{ color: 'var(--kg-text-secondary)' }">Запам'ятати мене</Label>
                            </div>
                        </div>

                        <Button
                            type="submit"
                            class="w-full px-6 py-3 rounded-xl text-white font-semibold transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5"
                            :style="{ background: 'var(--kg-accent)', boxShadow: '0 4px 16px var(--kg-accent-shadow)' }"
                            :tabindex="4"
                            :disabled="processing"
                        >
                            <LoaderCircle v-if="processing" class="h-5 w-5 animate-spin mr-2" />
                            Увійти
                        </Button>

                        <div class="text-center" style="color: var(--kg-text-muted);">
                            Ще немає акаунту?
                            <Link
                                :href="register()"
                                class="font-semibold ml-1 transition-colors"
                                :style="{ color: 'var(--kg-accent)' }"
                                :tabindex="6"
                            >
                                Зареєструватися
                            </Link>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
