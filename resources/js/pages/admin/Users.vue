<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface UserItem {
    id: number;
    name: string;
    email: string;
    role: string;
    books_count: number;
    created_at: string;
}

interface Props {
    users: {
        data: UserItem[];
        current_page: number;
        last_page: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
        total: number;
    };
    filters: {
        search?: string;
        role?: string;
    };
    can_manage_roles: boolean;
    current_user_id: number;
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || '');

function handleSearch() {
    router.get(
        '/admin/users',
        {
            search: search.value || undefined,
            role: roleFilter.value || undefined,
        },
        { preserveState: true, replace: true }
    );
}

function updateRole(userId: number, newRole: string, userName: string) {
    const roleNames: Record<string, string> = {
        super_admin: 'Головний адміністратор (Super Admin)',
        senior_admin: 'Старший адміністратор (Senior Admin)',
        user: 'Звичайний користувач',
    };

    if (confirm(`Ви дійсно бажаєте призначити користувачу ${userName} роль "${roleNames[newRole]}"?`)) {
        router.patch(`/admin/users/${userId}/role`, {
            role: newRole,
        }, {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Керування користувачами — knygogo" />

        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-heading tracking-tight" style="color: var(--kg-text);">
                    Користувачі та ролі
                </h1>
                <p class="text-sm mt-1" style="color: var(--kg-text-muted);">
                    Всього зареєстровано: {{ users.total }} користувачів
                    <span v-if="!can_manage_roles" class="text-xs text-amber-600 dark:text-amber-400 block sm:inline sm:ml-2">
                        (Зміна ролей доступна лише Головному адміністратору)
                    </span>
                </p>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="rounded-2xl border p-4 sm:p-5 mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between" style="background: var(--kg-surface); border-color: var(--kg-border);">
            <div class="flex-1 w-full sm:max-w-md relative">
                <input
                    v-model="search"
                    @keydown.enter="handleSearch"
                    type="text"
                    placeholder="Пошук за ім'ям або email..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm outline-none transition-all focus:ring-2"
                    :style="{
                        background: 'var(--kg-input-bg)',
                        borderColor: 'var(--kg-border)',
                        color: 'var(--kg-text)'
                    }"
                />
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3.5 top-3.5" style="color: var(--kg-text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select
                    v-model="roleFilter"
                    @change="handleSearch"
                    class="px-4 py-2.5 rounded-xl border text-sm outline-none transition-all cursor-pointer"
                    :style="{
                        background: 'var(--kg-input-bg)',
                        borderColor: 'var(--kg-border)',
                        color: 'var(--kg-text)'
                    }"
                >
                    <option value="">Всі ролі</option>
                    <option value="super_admin">Головні адміни (Super Admin)</option>
                    <option value="senior_admin">Старші адміни (Senior Admin)</option>
                    <option value="user">Звичайні користувачі</option>
                </select>

                <button
                    @click="handleSearch"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all shadow-sm flex-shrink-0 cursor-pointer"
                    :style="{ background: 'var(--kg-accent)' }"
                >
                    Знайти
                </button>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="rounded-2xl border overflow-hidden" style="background: var(--kg-surface); border-color: var(--kg-border);">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b text-xs uppercase tracking-wider font-semibold" style="border-color: var(--kg-border); color: var(--kg-text-muted); background: var(--kg-surface-alt);">
                        <tr>
                            <th class="py-3.5 px-5">Користувач</th>
                            <th class="py-3.5 px-5">Книг</th>
                            <th class="py-3.5 px-5">Реєстрація</th>
                            <th class="py-3.5 px-5">Поточна роль</th>
                            <th class="py-3.5 px-5 text-right">Дії / Призначення ролі</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--kg-border);">
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="py-10 text-center text-sm" style="color: var(--kg-text-muted);">
                                Користувачів не знайдено за даним запитом
                            </td>
                        </tr>
                        <tr
                            v-for="u in users.data"
                            :key="u.id"
                            class="transition-colors"
                            @mouseenter="($event.currentTarget as HTMLElement).style.background = 'var(--kg-surface-hover)'"
                            @mouseleave="($event.currentTarget as HTMLElement).style.background = 'transparent'"
                        >
                            <!-- Name & Email -->
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                                        {{ u.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold" style="color: var(--kg-text);">
                                            {{ u.name }}
                                            <span v-if="u.id === current_user_id" class="ml-1 text-xs px-2 py-0.5 rounded bg-neutral-200 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400">
                                                Ви
                                            </span>
                                        </div>
                                        <div class="text-xs truncate" style="color: var(--kg-text-muted);">{{ u.email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Books count -->
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold" style="background: var(--kg-surface-alt); color: var(--kg-text);">
                                    {{ u.books_count }} книг
                                </span>
                            </td>

                            <!-- Registered date -->
                            <td class="py-4 px-5 text-xs" style="color: var(--kg-text-muted);">
                                {{ new Date(u.created_at).toLocaleDateString('uk-UA') }}
                            </td>

                            <!-- Role badge -->
                            <td class="py-4 px-5">
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5"
                                    :style="{
                                        background: u.role === 'super_admin' ? 'rgba(217, 119, 6, 0.15)' : u.role === 'senior_admin' ? 'var(--kg-accent-light)' : 'var(--kg-surface-alt)',
                                        color: u.role === 'super_admin' ? '#d97706' : u.role === 'senior_admin' ? 'var(--kg-accent-light-text)' : 'var(--kg-text-secondary)',
                                        border: '1px solid ' + (u.role === 'super_admin' ? 'rgba(217, 119, 6, 0.3)' : u.role === 'senior_admin' ? 'var(--kg-accent)' : 'var(--kg-border)')
                                    }"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full" :style="{ background: u.role === 'super_admin' ? '#d97706' : u.role === 'senior_admin' ? 'var(--kg-accent)' : 'var(--kg-text-muted)' }"></span>
                                    {{ u.role === 'super_admin' ? 'Super Admin' : u.role === 'senior_admin' ? 'Senior Admin' : 'Користувач' }}
                                </span>
                            </td>

                            <!-- Actions / Role Assignment -->
                            <td class="py-4 px-5 text-right">
                                <div v-if="can_manage_roles" class="inline-flex items-center gap-1.5">
                                    <!-- Role selector select -->
                                    <select
                                        :value="u.role"
                                        @change="updateRole(u.id, ($event.target as HTMLSelectElement).value, u.name)"
                                        class="px-3 py-1.5 rounded-xl text-xs font-semibold border outline-none transition-all cursor-pointer"
                                        :style="{
                                            background: 'var(--kg-surface-alt)',
                                            borderColor: 'var(--kg-border)',
                                            color: 'var(--kg-text)'
                                        }"
                                    >
                                        <option value="user">Користувач</option>
                                        <option value="senior_admin">Старший адмін</option>
                                        <option value="super_admin">Головний адмін</option>
                                    </select>
                                </div>
                                <div v-else class="text-xs italic" style="color: var(--kg-text-muted);">
                                    Лише перегляд
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.last_page > 1" class="p-4 border-t flex items-center justify-between" style="border-color: var(--kg-border);">
                <div class="text-xs" style="color: var(--kg-text-muted);">
                    Сторінка {{ users.current_page }} з {{ users.last_page }}
                </div>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="(link, i) in users.links"
                        :key="i"
                        :href="link.url || '#'"
                        v-html="link.label"
                        :class="[
                            'px-3 py-1.5 rounded-xl text-xs font-medium transition-all',
                            link.active ? 'text-white font-bold' : 'hover:bg-neutral-200 dark:hover:bg-neutral-800',
                            !link.url ? 'opacity-40 pointer-events-none' : ''
                        ]"
                        :style="{
                            background: link.active ? 'var(--kg-accent)' : 'transparent',
                            color: link.active ? '#ffffff' : 'var(--kg-text)'
                        }"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
