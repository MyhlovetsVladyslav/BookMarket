<script setup lang="ts">
import { login, profile, logout, register } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const mobileMenuOpen = ref(false);
const page = usePage();
const unreadCount = computed(() => (page.props.unread_messages_count as number) || 0);
const isGuest = computed(() => !page.props.auth?.user);
const isAdmin = computed(() => {
    const role = (page.props.auth?.user as any)?.role;
    return role === 'super_admin' || role === 'senior_admin';
});

const { appearance, updateAppearance } = useAppearance();

function toggleTheme() {
    if (appearance.value === 'dark') {
        updateAppearance('light');
    } else {
        updateAppearance('dark');
    }
}
</script>

<template>
  <div class="min-h-screen font-body selection:bg-[var(--kg-accent-light)] selection:text-[var(--kg-text)]" style="background: var(--kg-bg); color: var(--kg-text);">
    <!-- ===== HEADER ===== -->
    <header class="sticky top-0 z-50 backdrop-blur-xl border-b" style="background: var(--kg-header-bg); border-color: var(--kg-border);">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-[72px]">
          <Link href="/" class="flex items-center gap-2.5 group">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110 group-hover:rotate-[-4deg]" style="background: var(--kg-accent);">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <span class="text-xl font-heading tracking-tight" style="color: var(--kg-text);">knygogo<span style="color: var(--kg-accent);">.</span></span>
          </Link>

          <!-- Desktop Nav Links -->
          <div class="hidden md:flex items-center gap-6 text-[15px] font-medium" style="color: var(--kg-text-secondary);">
            <Link href="/#catalog" class="relative py-1 transition-colors hover:text-[var(--kg-text)]">Каталог</Link>
          </div>

          <!-- Right side -->
          <div class="flex items-center gap-2">
            <!-- Theme toggle — always visible -->
            <button
              @click="toggleTheme"
              class="p-2 rounded-xl transition-all"
              :style="{ color: 'var(--kg-text-muted)' }"
              @mouseenter="($event.target as HTMLElement).style.background = 'var(--kg-surface-hover)'"
              @mouseleave="($event.target as HTMLElement).style.background = 'transparent'"
              :title="appearance === 'dark' ? 'Світла тема' : 'Темна тема'"
            >
              <!-- Sun icon (shown in dark mode) -->
              <svg v-if="appearance === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <!-- Moon icon (shown in light mode) -->
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              </svg>
            </button>

            <div class="hidden sm:flex items-center gap-2 text-sm font-medium">
              <template v-if="isGuest">
                <Link :href="login()" class="px-4 py-2 rounded-xl transition-all" style="color: var(--kg-text-secondary);" @mouseenter="($event.target as HTMLElement).style.background = 'var(--kg-surface-hover)'" @mouseleave="($event.target as HTMLElement).style.background = 'transparent'">Увійти</Link>
                <Link :href="register()" class="px-4 py-2 rounded-xl text-white font-semibold transition-all shadow-sm" style="background: var(--kg-accent);" @mouseenter="($event.target as HTMLElement).style.background = 'var(--kg-accent-hover)'" @mouseleave="($event.target as HTMLElement).style.background = 'var(--kg-accent)'">Зареєструватися</Link>
              </template>
              <template v-else>
                <!-- Chat link -->
                <Link href="/chat" class="p-2 rounded-xl transition-all relative" style="color: var(--kg-text-secondary);" title="Повідомлення" @mouseenter="($event.target as HTMLElement).style.background = 'var(--kg-surface-hover)'" @mouseleave="($event.target as HTMLElement).style.background = 'transparent'">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  <span v-if="unreadCount > 0" class="absolute -top-0.5 -right-0.5 text-white text-[10px] font-bold min-w-[18px] h-[18px] flex items-center justify-center rounded-full px-1 shadow-sm" style="background: var(--kg-danger);">
                    {{ unreadCount > 99 ? '99+' : unreadCount }}
                  </span>
                </Link>

                <!-- Admin panel button -->
                <Link
                  v-if="isAdmin"
                  href="/admin"
                  class="px-3.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all shadow-xs"
                  :style="{
                    background: 'var(--kg-accent-light)',
                    color: 'var(--kg-accent-light-text)',
                    border: '1px solid var(--kg-accent)'
                  }"
                  title="Панель адміністратора"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                  Адмінка
                </Link>

                <Link :href="profile()" class="px-4 py-2 rounded-xl transition-all flex items-center gap-2" style="color: var(--kg-text-secondary);" @mouseenter="($event.target as HTMLElement).style.background = 'var(--kg-surface-hover)'" @mouseleave="($event.target as HTMLElement).style.background = 'transparent'">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Мій профіль
                </Link>

                <Link href="/books/create" class="px-4 py-2 text-white rounded-xl text-sm font-semibold transition-all shadow-sm flex items-center gap-2" style="background: var(--kg-accent);" @mouseenter="($event.target as HTMLElement).style.background = 'var(--kg-accent-hover)'" @mouseleave="($event.target as HTMLElement).style.background = 'var(--kg-accent)'">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                   </svg>
                   Продати
                </Link>

                <Link :href="logout()" method="post" as="button" class="p-2 rounded-xl transition-all" style="color: var(--kg-text-muted);" title="Вийти" @mouseenter="($event.target as HTMLElement).style.cssText = 'color: var(--kg-danger); background: var(--kg-surface-hover)'" @mouseleave="($event.target as HTMLElement).style.cssText = 'color: var(--kg-text-muted); background: transparent'">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                </Link>
              </template>
            </div>

            <!-- Mobile Menu Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl transition-all" style="color: var(--kg-text-secondary);" aria-label="Меню">
              <svg v-if="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="md:hidden pb-4 pt-3 space-y-1 animate-slide-down" style="border-top: 1px solid var(--kg-border);">
          <Link href="/#catalog" class="block px-4 py-2.5 rounded-xl transition-all" style="color: var(--kg-text-secondary);" @click="mobileMenuOpen = false">Каталог</Link>
          <template v-if="isGuest">
            <Link :href="login()" class="block px-4 py-2.5 rounded-xl transition-all" style="color: var(--kg-text-secondary);" @click="mobileMenuOpen = false">Увійти</Link>
            <Link :href="register()" class="block px-4 py-2.5 rounded-xl text-white font-semibold text-center transition-all" style="background: var(--kg-accent);" @click="mobileMenuOpen = false">Зареєструватися</Link>
          </template>
          <template v-else>
            <Link
              v-if="isAdmin"
              href="/admin"
              class="block px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 font-medium"
              :style="{
                background: 'var(--kg-accent-light)',
                color: 'var(--kg-accent-light-text)'
              }"
              @click="mobileMenuOpen = false"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              Адмін-панель
            </Link>

            <Link href="/chat" class="block px-4 py-2.5 rounded-xl transition-all flex items-center gap-2" style="color: var(--kg-text-secondary);" @click="mobileMenuOpen = false">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              Повідомлення
              <span v-if="unreadCount > 0" class="ml-auto text-white text-[10px] font-bold min-w-[18px] h-[18px] flex items-center justify-center rounded-full px-1" style="background: var(--kg-danger);">{{ unreadCount }}</span>
            </Link>
            <Link :href="profile()" class="block px-4 py-2.5 rounded-xl transition-all flex items-center gap-2" style="color: var(--kg-text-secondary);" @click="mobileMenuOpen = false">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Мій профіль
            </Link>
            <Link href="/books/create" class="block px-4 py-2.5 rounded-xl text-white font-semibold text-center transition-all" style="background: var(--kg-accent);" @click="mobileMenuOpen = false">Продати книгу</Link>
            <Link :href="logout()" method="post" as="button" class="block w-full text-left px-4 py-2.5 rounded-xl transition-all flex items-center gap-2" style="color: var(--kg-danger);" @click="mobileMenuOpen = false">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Вийти
            </Link>
          </template>
        </div>
      </nav>
    </header>

    <main class="min-h-[70vh]">
        <slot />
    </main>

    <!-- ===== FOOTER ===== -->
    <footer style="background: var(--kg-bg); border-top: 1px solid var(--kg-border);" class="pt-12 pb-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-3 gap-8 mb-8">
          <div>
            <div class="flex items-center gap-2 mb-3">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--kg-accent);">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <span class="text-lg font-heading" style="color: var(--kg-text);">knygogo<span style="color: var(--kg-accent);">.</span></span>
            </div>
            <p class="text-sm leading-relaxed" style="color: var(--kg-text-muted);">Книжкова барахолка для справжніх книголюбів. Купуй, продавай та обмінюйся книгами.</p>
          </div>
          <div>
            <h4 class="text-sm font-semibold mb-3" style="color: var(--kg-text);">Навігація</h4>
            <div class="space-y-2">
              <Link href="/#catalog" class="block text-sm transition-colors" style="color: var(--kg-text-muted);">Каталог</Link>
              <Link href="/books/create" class="block text-sm transition-colors" style="color: var(--kg-text-muted);">Продати книгу</Link>
            </div>
          </div>
          <div>
            <h4 class="text-sm font-semibold mb-3" style="color: var(--kg-text);">Контакти</h4>
            <p class="text-sm" style="color: var(--kg-text-muted);">support@knygogo.ua</p>
          </div>
        </div>
        <div class="pt-6 text-center text-sm" style="border-top: 1px solid var(--kg-border); color: var(--kg-text-faint);">
          <p>© {{ new Date().getFullYear() }} knygogo. Твоя книжкова барахолка.</p>
        </div>
      </div>
    </footer>
  </div>
</template>
