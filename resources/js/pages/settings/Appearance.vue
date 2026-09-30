<script setup lang="ts">
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head } from '@inertiajs/vue3';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const themes = [
    {
        id: 'light',
        title: 'Світла тема',
        subtitle: 'Теплі тони паперу',
        description: 'Класичний світлий інтерфейс для комфортного перегляду та читання вдень.',
        previewBg: '#FDFBF7',
        cardBg: '#ffffff',
        accentColor: '#5B8A72',
        textColor: '#1a1a1a',
    },
    {
        id: 'dark',
        title: 'Темна тема',
        subtitle: 'Глибокі нічні кольори',
        description: 'Стильне темне оформлення для зниження втоми очей при слабкому освітленні.',
        previewBg: '#0f0f0f',
        cardBg: '#1a1a1a',
        accentColor: '#6d9d85',
        textColor: '#f0f0f0',
    },
    {
        id: 'system',
        title: 'Системна',
        subtitle: 'Автоматичний вибір',
        description: 'Тема сайту синхронізується з налаштуваннями вашої операційної системи чи браузера.',
        previewBg: 'linear-gradient(135deg, #FDFBF7 50%, #0f0f0f 50%)',
        cardBg: 'linear-gradient(135deg, #ffffff 50%, #1a1a1a 50%)',
        accentColor: '#5B8A72',
        textColor: 'inherit',
    },
] as const;
</script>

<template>
    <SettingsLayout>
        <Head title="Зовнішній вигляд — knygogo" />

        <div class="rounded-2xl border p-6 sm:p-8 transition-all" style="background: var(--kg-surface); border-color: var(--kg-border);">
            <div class="border-b pb-5 mb-6" style="border-color: var(--kg-border);">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4 4 4 0 014-4 4 4 0 014 4 4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-heading" style="color: var(--kg-text);">Зовнішній вигляд</h2>
                        <p class="text-sm mt-0.5" style="color: var(--kg-text-muted);">
                            Оберіть тему оформлення, яка вам найбільше подобається
                        </p>
                    </div>
                </div>
            </div>

            <!-- Theme Cards Grid -->
            <div class="grid sm:grid-cols-3 gap-5">
                <div
                    v-for="theme in themes"
                    :key="theme.id"
                    @click="updateAppearance(theme.id)"
                    class="relative rounded-2xl border p-5 cursor-pointer transition-all duration-200 group flex flex-col justify-between"
                    :style="{
                        background: appearance === theme.id ? 'var(--kg-accent-light)' : 'var(--kg-surface-alt)',
                        borderColor: appearance === theme.id ? 'var(--kg-accent)' : 'var(--kg-border)',
                        boxShadow: appearance === theme.id ? '0 0 0 2px var(--kg-accent)' : 'none',
                    }"
                >
                    <!-- Checkmark badge for active theme -->
                    <div
                        v-if="appearance === theme.id"
                        class="absolute top-4 right-4 w-6 h-6 rounded-full flex items-center justify-center text-white text-xs shadow-sm"
                        style="background: var(--kg-accent);"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <!-- Mini UI Mockup Preview -->
                    <div
                        class="h-28 rounded-xl p-3 border mb-4 flex flex-col justify-between overflow-hidden shadow-xs transition-transform group-hover:scale-[1.02]"
                        :style="{
                            background: theme.previewBg,
                            borderColor: 'rgba(128,128,128,0.2)',
                        }"
                    >
                        <!-- Header bar mockup -->
                        <div class="flex items-center justify-between pb-2 border-b border-black/10 dark:border-white/10">
                            <div class="w-12 h-2.5 rounded-full" :style="{ background: theme.accentColor }"></div>
                            <div class="w-3.5 h-3.5 rounded-full" :style="{ background: theme.accentColor, opacity: 0.8 }"></div>
                        </div>

                        <!-- Card preview mockup -->
                        <div
                            class="p-2 rounded-lg border shadow-xs"
                            :style="{
                                background: theme.cardBg,
                                borderColor: 'rgba(128,128,128,0.15)',
                            }"
                        >
                            <div class="w-16 h-2 rounded bg-neutral-400/40 mb-1.5"></div>
                            <div class="w-10 h-1.5 rounded bg-neutral-400/20"></div>
                        </div>
                    </div>

                    <!-- Text info -->
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <!-- Sun icon -->
                            <svg v-if="theme.id === 'light'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :style="{ color: 'var(--kg-accent)' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <!-- Moon icon -->
                            <svg v-else-if="theme.id === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :style="{ color: 'var(--kg-accent)' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <!-- Monitor icon -->
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :style="{ color: 'var(--kg-accent)' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="font-heading text-base font-semibold" style="color: var(--kg-text);">
                                {{ theme.title }}
                            </span>
                        </div>
                        <p class="text-xs leading-relaxed" style="color: var(--kg-text-secondary);">
                            {{ theme.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </SettingsLayout>
</template>
