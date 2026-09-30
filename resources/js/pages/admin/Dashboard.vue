<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    stats: {
        total_users: number;
        total_admins: number;
        total_super_admins: number;
        total_senior_admins: number;
        total_books: number;
        total_conversations: number;
        total_messages: number;
    };
    recent_books: Array<{
        id: number;
        title: string;
        author: string;
        price: number;
        condition: string;
        image_path?: string;
        created_at: string;
        user: {
            id: number;
            name: string;
            email: string;
        };
    }>;
    recent_users: Array<{
        id: number;
        name: string;
        email: string;
        role: string;
        created_at: string;
    }>;
    current_user_role: string;
}

const props = defineProps<Props>();
</script>

<template>
    <AdminLayout>
        <Head title="Адмін-панель — knygogo" />

        <!-- Header banner -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-heading tracking-tight" style="color: var(--kg-text);">
                    Панель управління
                </h1>
                <p class="text-sm sm:text-base mt-1" style="color: var(--kg-text-muted);">
                    Огляд стану маркетплейсу, модерація контенту та статистика
                </p>
            </div>

            <!-- Role pill -->
            <div class="flex items-center gap-2">
                <span
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 border shadow-2xs"
                    :style="{
                        background: current_user_role === 'super_admin' ? 'rgba(217, 119, 6, 0.12)' : 'var(--kg-accent-light)',
                        borderColor: current_user_role === 'super_admin' ? '#d97706' : 'var(--kg-accent)',
                        color: current_user_role === 'super_admin' ? '#d97706' : 'var(--kg-accent-light-text)'
                    }"
                >
                    <span class="w-2 h-2 rounded-full" :style="{ background: current_user_role === 'super_admin' ? '#d97706' : 'var(--kg-accent)' }"></span>
                    {{ current_user_role === 'super_admin' ? 'Повний доступ (Super Admin)' : 'Модератор (Senior Admin)' }}
                </span>
            </div>
        </div>

        <!-- Metric Stat Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Total Users Card -->
            <div class="rounded-2xl border p-5 transition-all" style="background: var(--kg-surface); border-color: var(--kg-border);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--kg-text-muted);">Користувачі</span>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-bold font-heading" style="color: var(--kg-text);">
                    {{ stats.total_users }}
                </div>
                <div class="text-xs mt-1" style="color: var(--kg-text-muted);">
                    Адміністраторів: <span class="font-semibold" style="color: var(--kg-accent);">{{ stats.total_admins }}</span>
                </div>
            </div>

            <!-- Total Books Card -->
            <div class="rounded-2xl border p-5 transition-all" style="background: var(--kg-surface); border-color: var(--kg-border);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--kg-text-muted);">Оголошення книг</span>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(14, 116, 171, 0.12); color: #0284c7;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-bold font-heading" style="color: var(--kg-text);">
                    {{ stats.total_books }}
                </div>
                <div class="text-xs mt-1" style="color: var(--kg-text-muted);">
                    Активні в каталозі
                </div>
            </div>

            <!-- Total Chats Card -->
            <div class="rounded-2xl border p-5 transition-all" style="background: var(--kg-surface); border-color: var(--kg-border);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--kg-text-muted);">Чати / Діалоги</span>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-bold font-heading" style="color: var(--kg-text);">
                    {{ stats.total_conversations }}
                </div>
                <div class="text-xs mt-1" style="color: var(--kg-text-muted);">
                    Всього повідомлень: <span class="font-semibold">{{ stats.total_messages }}</span>
                </div>
            </div>

            <!-- Role & Permissions Card -->
            <div class="rounded-2xl border p-5 transition-all" style="background: var(--kg-surface); border-color: var(--kg-border);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--kg-text-muted);">Ваші права</span>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <div class="text-sm font-bold" style="color: var(--kg-text);">
                    {{ current_user_role === 'super_admin' ? 'Головний адмін' : 'Старший адмін' }}
                </div>
                <div class="text-xs mt-1 leading-relaxed" style="color: var(--kg-text-muted);">
                    {{ current_user_role === 'super_admin' ? 'Керування ролями, користувачами та книгами' : 'Модерація книг та перегляд користувачів' }}
                </div>
            </div>
        </div>

        <!-- Quick Navigation Cards -->
        <div class="grid sm:grid-cols-2 gap-5 mb-8">
            <Link
                href="/admin/users"
                class="rounded-2xl border p-5 flex items-center justify-between transition-all group"
                style="background: var(--kg-surface); border-color: var(--kg-border);"
                @mouseenter="($event.currentTarget as HTMLElement).style.borderColor = 'var(--kg-accent)'"
                @mouseleave="($event.currentTarget as HTMLElement).style.borderColor = 'var(--kg-border)'"
            >
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-heading font-semibold text-base" style="color: var(--kg-text);">Керування користувачами</h3>
                        <p class="text-xs mt-0.5" style="color: var(--kg-text-muted);">
                            {{ current_user_role === 'super_admin' ? 'Призначення ролей адмінів, пошук та перегляд акаунтів' : 'Перегляд зареєстрованих користувачів' }}
                        </p>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:translate-x-1" style="color: var(--kg-text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </Link>

            <Link
                href="/admin/books"
                class="rounded-2xl border p-5 flex items-center justify-between transition-all group"
                style="background: var(--kg-surface); border-color: var(--kg-border);"
                @mouseenter="($event.currentTarget as HTMLElement).style.borderColor = 'var(--kg-accent)'"
                @mouseleave="($event.currentTarget as HTMLElement).style.borderColor = 'var(--kg-border)'"
            >
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105" style="background: rgba(14, 116, 171, 0.12); color: #0284c7;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-heading font-semibold text-base" style="color: var(--kg-text);">Модерація книг</h3>
                        <p class="text-xs mt-0.5" style="color: var(--kg-text-muted);">Перегляд та видалення неприйнятних або спам-оголошень</p>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:translate-x-1" style="color: var(--kg-text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </Link>
        </div>

        <!-- Activity Tables Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Books -->
            <div class="rounded-2xl border p-5 sm:p-6" style="background: var(--kg-surface); border-color: var(--kg-border);">
                <div class="flex items-center justify-between pb-4 mb-4 border-b" style="border-color: var(--kg-border);">
                    <h2 class="text-lg font-heading" style="color: var(--kg-text);">Останні додані книги</h2>
                    <Link href="/admin/books" class="text-xs font-semibold hover:underline" style="color: var(--kg-accent);">
                        Усі книги →
                    </Link>
                </div>

                <div v-if="recent_books.length === 0" class="text-center py-8 text-sm" style="color: var(--kg-text-muted);">
                    Немає оголошень
                </div>
                <div v-else class="space-y-3">
                    <div
                        v-for="book in recent_books"
                        :key="book.id"
                        class="p-3 rounded-xl border flex items-center gap-3 transition-colors"
                        style="background: var(--kg-surface-alt); border-color: var(--kg-border);"
                    >
                        <div class="w-12 h-14 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center" style="background: var(--kg-surface);">
                            <img v-if="book.image_path" :src="'/storage/' + book.image_path" class="w-full h-full object-cover" />
                            <span v-else class="text-xl">📖</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <Link :href="`/books/${book.id}`" class="text-sm font-semibold truncate block hover:underline" style="color: var(--kg-text);">
                                {{ book.title }}
                            </Link>
                            <p class="text-xs truncate" style="color: var(--kg-text-muted);">{{ book.author }} • {{ book.user.name }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-sm font-bold" style="color: var(--kg-accent);">{{ book.price }} ₴</span>
                            <span class="block text-[11px]" style="color: var(--kg-text-muted);">{{ book.condition }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="rounded-2xl border p-5 sm:p-6" style="background: var(--kg-surface); border-color: var(--kg-border);">
                <div class="flex items-center justify-between pb-4 mb-4 border-b" style="border-color: var(--kg-border);">
                    <h2 class="text-lg font-heading" style="color: var(--kg-text);">Нещодавно зареєстровані</h2>
                    <Link href="/admin/users" class="text-xs font-semibold hover:underline" style="color: var(--kg-accent);">
                        Усі користувачі →
                    </Link>
                </div>

                <div v-if="recent_users.length === 0" class="text-center py-8 text-sm" style="color: var(--kg-text-muted);">
                    Немає користувачів
                </div>
                <div v-else class="space-y-3">
                    <div
                        v-for="u in recent_users"
                        :key="u.id"
                        class="p-3 rounded-xl border flex items-center gap-3 transition-colors"
                        style="background: var(--kg-surface-alt); border-color: var(--kg-border);"
                    >
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                            {{ u.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold truncate" style="color: var(--kg-text);">{{ u.name }}</div>
                            <div class="text-xs truncate" style="color: var(--kg-text-muted);">{{ u.email }}</div>
                        </div>
                        <div class="flex-shrink-0">
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                :style="{
                                    background: u.role === 'super_admin' ? 'rgba(217, 119, 6, 0.15)' : u.role === 'senior_admin' ? 'var(--kg-accent-light)' : 'var(--kg-surface)',
                                    color: u.role === 'super_admin' ? '#d97706' : u.role === 'senior_admin' ? 'var(--kg-accent-light-text)' : 'var(--kg-text-muted)',
                                    border: '1px solid ' + (u.role === 'super_admin' ? 'rgba(217, 119, 6, 0.3)' : u.role === 'senior_admin' ? 'var(--kg-accent)' : 'var(--kg-border)')
                                }"
                            >
                                {{ u.role === 'super_admin' ? 'Super Admin' : u.role === 'senior_admin' ? 'Senior Admin' : 'Користувач' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
