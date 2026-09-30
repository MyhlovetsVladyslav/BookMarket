<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const page = usePage();
const { appearance, updateAppearance } = useAppearance();

const user = computed(() => page.props.auth?.user as any);
const isSuperAdmin = computed(() => user.value?.role === 'super_admin');
const isSeniorAdmin = computed(() => user.value?.role === 'senior_admin');

const flashSuccess = computed(() => (page.props.flash as any)?.success);
const flashError = computed(() => (page.props.flash as any)?.error);

function toggleTheme() {
    updateAppearance(appearance.value === 'dark' ? 'light' : 'dark');
}

const navItems = [
    {
        title: 'Огляд та статистика',
        href: '/admin',
        icon: 'dashboard',
    },
    {
        title: 'Користувачі та ролі',
        href: '/admin/users',
        icon: 'users',
    },
    {
        title: 'Модерація книг',
        href: '/admin/books',
        icon: 'books',
    },
];

function isActive(url: string) {
    if (url === '/admin') {
        return page.url === '/admin' || page.url === '/admin/';
    }
    return page.url.startsWith(url);
}
</script>

<template>
  <div class="min-h-screen font-body selection:bg-[var(--kg-accent-light)] selection:text-[var(--kg-text)]" style="background: var(--kg-bg); color: var(--kg-text);">
    <!-- ===== ADMIN HEADER ===== -->
    <header class="sticky top-0 z-50 backdrop-blur-xl border-b" style="background: var(--kg-header-bg); border-color: var(--kg-border);">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Left: Logo & Admin badge -->
          <div class="flex items-center gap-3">
            <Link href="/" class="flex items-center gap-2.5 group">
              <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105" style="background: var(--kg-accent);">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <span class="text-lg font-heading tracking-tight" style="color: var(--kg-text);">knygogo<span style="color: var(--kg-accent);">.</span></span>
            </Link>

            <span
              class="px-2.5 py-0.5 rounded-lg text-xs font-bold tracking-wide uppercase flex items-center gap-1.5 shadow-2xs"
              :style="{
                background: isSuperAdmin ? 'rgba(217, 119, 6, 0.15)' : 'var(--kg-accent-light)',
                color: isSuperAdmin ? '#d97706' : 'var(--kg-accent-light-text)',
                border: '1px solid ' + (isSuperAdmin ? 'rgba(217, 119, 6, 0.3)' : 'var(--kg-accent)')
              }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              {{ isSuperAdmin ? 'Головний Адмін' : 'Старший Адмін' }}
            </span>
          </div>

          <!-- Center: Quick Nav Links -->
          <nav class="hidden md:flex items-center gap-1">
            <Link
              v-for="item in navItems"
              :key="item.href"
              :href="item.href"
              class="px-3.5 py-1.5 rounded-xl text-sm font-medium transition-all flex items-center gap-2"
              :style="{
                background: isActive(item.href) ? 'var(--kg-accent-light)' : 'transparent',
                color: isActive(item.href) ? 'var(--kg-accent-light-text)' : 'var(--kg-text-secondary)',
                fontWeight: isActive(item.href) ? '600' : '500'
              }"
              @mouseenter="!isActive(item.href) && (($event.currentTarget as HTMLElement).style.background = 'var(--kg-surface-hover)')"
              @mouseleave="!isActive(item.href) && (($event.currentTarget as HTMLElement).style.background = 'transparent')"
            >
              <!-- Dashboard icon -->
              <svg v-if="item.icon === 'dashboard'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
              <!-- Users icon -->
              <svg v-else-if="item.icon === 'users'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <!-- Books icon -->
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              {{ item.title }}
            </Link>
          </nav>

          <!-- Right: Actions -->
          <div class="flex items-center gap-2.5">
            <!-- Theme toggle -->
            <button
              @click="toggleTheme"
              class="p-2 rounded-xl transition-all cursor-pointer"
              :style="{ color: 'var(--kg-text-muted)' }"
              @mouseenter="($event.target as HTMLElement).style.background = 'var(--kg-surface-hover)'"
              @mouseleave="($event.target as HTMLElement).style.background = 'transparent'"
              :title="appearance === 'dark' ? 'Світла тема' : 'Темна тема'"
            >
              <svg v-if="appearance === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              </svg>
            </button>

            <!-- Back to website button -->
            <Link
              href="/"
              class="px-3.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all border"
              :style="{
                background: 'var(--kg-surface)',
                borderColor: 'var(--kg-border)',
                color: 'var(--kg-text)'
              }"
              @mouseenter="($event.currentTarget as HTMLElement).style.background = 'var(--kg-surface-hover)'"
              @mouseleave="($event.currentTarget as HTMLElement).style.background = 'var(--kg-surface)'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              До сайту
            </Link>
          </div>
        </div>

        <!-- Mobile sub-bar with navigation tabs -->
        <div class="md:hidden flex items-center gap-2 overflow-x-auto py-2.5 border-t scrollbar-none" style="border-color: var(--kg-border);">
          <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="px-3 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all flex-shrink-0"
            :style="{
              background: isActive(item.href) ? 'var(--kg-accent-light)' : 'var(--kg-surface)',
              color: isActive(item.href) ? 'var(--kg-accent-light-text)' : 'var(--kg-text-secondary)',
              border: '1px solid ' + (isActive(item.href) ? 'var(--kg-accent)' : 'var(--kg-border)'),
              fontWeight: isActive(item.href) ? '600' : '500'
            }"
          >
            {{ item.title }}
          </Link>
        </div>
      </div>
    </header>

    <!-- ===== FLASH MESSAGES ===== -->
    <div v-if="flashSuccess || flashError" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
      <div
        v-if="flashSuccess"
        class="p-4 rounded-xl text-sm font-medium flex items-center gap-2 border animate-slide-down"
        style="background: var(--kg-accent-light); color: var(--kg-accent-light-text); border-color: var(--kg-accent);"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        {{ flashSuccess }}
      </div>
      <div
        v-if="flashError"
        class="p-4 rounded-xl text-sm font-medium flex items-center gap-2 border animate-slide-down"
        style="background: var(--kg-danger-light); color: var(--kg-danger); border-color: var(--kg-danger);"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        {{ flashError }}
      </div>
    </div>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">
      <slot />
    </main>
  </div>
</template>
