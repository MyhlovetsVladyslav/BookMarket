<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toUrl } from '@/lib/utils';
import { appearance } from '@/routes';
import { edit as editPassword } from '@/routes/password';
import { edit } from '@/routes/profile';
import { show } from '@/routes/two-factor';

const page = usePage();

const navItems = [
    {
        title: 'Профіль',
        description: 'Особисті дані та email',
        href: edit(),
        icon: 'user',
    },
    {
        title: 'Пароль',
        description: 'Безпека та пароль',
        href: editPassword(),
        icon: 'lock',
    },
    {
        title: '2FA захист',
        description: 'Двоетапна перевірка',
        href: show(),
        icon: 'shield',
    },
    {
        title: 'Зовнішній вигляд',
        description: 'Тема та колір сайту',
        href: appearance(),
        icon: 'palette',
    },
];

function isItemActive(itemHref: any) {
    const url = toUrl(itemHref);
    if (!url) return false;
    const current = page.url;
    return current === url || current.startsWith(url + '?');
}
</script>

<template>
    <MainLayout>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <!-- Back to profile link -->
            <div class="mb-6">
                <Link
                    href="/profile"
                    class="inline-flex items-center gap-2 text-sm font-medium transition-all group"
                    style="color: var(--kg-text-muted);"
                    @mouseenter="($event.currentTarget as HTMLElement).style.color = 'var(--kg-accent)'"
                    @mouseleave="($event.currentTarget as HTMLElement).style.color = 'var(--kg-text-muted)'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Назад до мого профілю
                </Link>
            </div>

            <!-- Page Title Header -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-heading tracking-tight" style="color: var(--kg-text);">
                    Налаштування
                </h1>
                <p class="text-sm sm:text-base mt-1.5" style="color: var(--kg-text-muted);">
                    Керуйте параметрами свого облікового запису, безпекою та зовнішнім виглядом
                </p>
            </div>

            <!-- Mobile Navigation (Horizontal Pills) -->
            <div class="lg:hidden flex items-center gap-2 overflow-x-auto pb-3 mb-6 scrollbar-none">
                <Link
                    v-for="item in navItems"
                    :key="toUrl(item.href)"
                    :href="item.href"
                    class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap flex items-center gap-2 transition-all flex-shrink-0"
                    :style="{
                        background: isItemActive(item.href) ? 'var(--kg-accent-light)' : 'var(--kg-surface)',
                        color: isItemActive(item.href) ? 'var(--kg-accent-light-text)' : 'var(--kg-text-secondary)',
                        border: '1px solid ' + (isItemActive(item.href) ? 'var(--kg-accent)' : 'var(--kg-border)'),
                        fontWeight: isItemActive(item.href) ? '600' : '500'
                    }"
                >
                    <!-- User icon -->
                    <svg v-if="item.icon === 'user'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <!-- Lock icon -->
                    <svg v-else-if="item.icon === 'lock'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <!-- Shield icon -->
                    <svg v-else-if="item.icon === 'shield'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <!-- Palette icon -->
                    <svg v-else-if="item.icon === 'palette'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4 4 4 0 014-4 4 4 0 014 4 4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                    {{ item.title }}
                </Link>
            </div>

            <!-- Desktop Grid Layout (Sidebar + Content) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Sidebar -->
                <aside class="hidden lg:block lg:col-span-4 xl:col-span-3 space-y-2">
                    <nav class="space-y-2">
                        <Link
                            v-for="item in navItems"
                            :key="toUrl(item.href)"
                            :href="item.href"
                            class="group block p-3.5 rounded-2xl border transition-all"
                            :style="{
                                background: isItemActive(item.href) ? 'var(--kg-accent-light)' : 'var(--kg-surface)',
                                borderColor: isItemActive(item.href) ? 'var(--kg-accent)' : 'var(--kg-border)',
                            }"
                            @mouseenter="!isItemActive(item.href) && (($event.currentTarget as HTMLElement).style.background = 'var(--kg-surface-hover)')"
                            @mouseleave="!isItemActive(item.href) && (($event.currentTarget as HTMLElement).style.background = 'var(--kg-surface)')"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105"
                                    :style="{
                                        background: isItemActive(item.href) ? 'var(--kg-accent)' : 'var(--kg-surface-alt)',
                                        color: isItemActive(item.href) ? '#ffffff' : 'var(--kg-text-secondary)'
                                    }"
                                >
                                    <!-- User icon -->
                                    <svg v-if="item.icon === 'user'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <!-- Lock icon -->
                                    <svg v-else-if="item.icon === 'lock'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <!-- Shield icon -->
                                    <svg v-else-if="item.icon === 'shield'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <!-- Palette icon -->
                                    <svg v-else-if="item.icon === 'palette'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4 4 4 0 014-4 4 4 0 014 4 4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="text-sm font-semibold tracking-tight transition-colors"
                                        :style="{ color: isItemActive(item.href) ? 'var(--kg-accent-light-text)' : 'var(--kg-text)' }"
                                    >
                                        {{ item.title }}
                                    </div>
                                    <div class="text-xs truncate mt-0.5" style="color: var(--kg-text-muted);">
                                        {{ item.description }}
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </nav>
                </aside>

                <!-- Content Area -->
                <main class="lg:col-span-8 xl:col-span-9">
                    <div class="space-y-8">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </MainLayout>
</template>
