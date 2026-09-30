<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface BookItem {
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
}

interface Props {
    books: {
        data: BookItem[];
        current_page: number;
        last_page: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
        total: number;
    };
    filters: {
        search?: string;
        condition?: string;
    };
    current_user_role: string;
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const conditionFilter = ref(props.filters.condition || '');

function handleSearch() {
    router.get(
        '/admin/books',
        {
            search: search.value || undefined,
            condition: conditionFilter.value || undefined,
        },
        { preserveState: true, replace: true }
    );
}

function deleteBook(bookId: number, title: string) {
    if (confirm(`Ви дійсно бажаєте видалити оголошення "${title}"?`)) {
        router.delete(`/admin/books/${bookId}`, {
            preserveScroll: true,
        });
    }
}

function decodePaginationLabel(label: string): string {
    return label
        .replace(/&laquo;/g, '«')
        .replace(/&raquo;/g, '»')
        .replace(/&amp;/g, '&');
}
</script>

<template>
    <AdminLayout>
        <Head title="Модерація книг — knygogo" />

        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-heading tracking-tight" style="color: var(--kg-text);">
                    Модерація книг
                </h1>
                <p class="text-sm mt-1" style="color: var(--kg-text-muted);">
                    Всього в каталозі: {{ books.total }} оголошень. Перегляд та видалення неприйнятного контенту.
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
                    placeholder="Пошук за назвою або автором..."
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
                    v-model="conditionFilter"
                    @change="handleSearch"
                    class="px-4 py-2.5 rounded-xl border text-sm outline-none transition-all cursor-pointer"
                    :style="{
                        background: 'var(--kg-input-bg)',
                        borderColor: 'var(--kg-border)',
                        color: 'var(--kg-text)'
                    }"
                >
                    <option value="">Всі стани</option>
                    <option value="Нова">Нова</option>
                    <option value="Ідеальний стан">Ідеальний стан</option>
                    <option value="Гарний стан">Гарний стан</option>
                    <option value="Задовільний">Задовільний</option>
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

        <!-- Books Table Card -->
        <div class="rounded-2xl border overflow-hidden" style="background: var(--kg-surface); border-color: var(--kg-border);">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b text-xs uppercase tracking-wider font-semibold" style="border-color: var(--kg-border); color: var(--kg-text-muted); background: var(--kg-surface-alt);">
                        <tr>
                            <th class="py-3.5 px-5">Книга</th>
                            <th class="py-3.5 px-5">Продавець</th>
                            <th class="py-3.5 px-5">Ціна</th>
                            <th class="py-3.5 px-5">Стан</th>
                            <th class="py-3.5 px-5">Дата</th>
                            <th class="py-3.5 px-5 text-right">Дії</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--kg-border);">
                        <tr v-if="books.data.length === 0">
                            <td colspan="6" class="py-10 text-center text-sm" style="color: var(--kg-text-muted);">
                                Оголошень не знайдено за даним запитом
                            </td>
                        </tr>
                        <tr
                            v-for="b in books.data"
                            :key="b.id"
                            class="transition-colors"
                            @mouseenter="($event.currentTarget as HTMLElement).style.background = 'var(--kg-surface-hover)'"
                            @mouseleave="($event.currentTarget as HTMLElement).style.background = 'transparent'"
                        >
                            <!-- Cover & Title -->
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-11 h-14 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center border" style="background: var(--kg-surface-alt); border-color: var(--kg-border);">
                                        <img v-if="b.image_path" :src="'/storage/' + b.image_path" class="w-full h-full object-cover" />
                                        <span v-else class="text-xl">📖</span>
                                    </div>
                                    <div class="min-w-0">
                                        <Link :href="`/books/${b.id}`" class="font-semibold block truncate hover:underline" style="color: var(--kg-text);">
                                            {{ b.title }}
                                        </Link>
                                        <div class="text-xs truncate" style="color: var(--kg-text-muted);">{{ b.author }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Seller -->
                            <td class="py-4 px-5">
                                <div class="text-xs font-semibold" style="color: var(--kg-text);">{{ b.user?.name || 'Невідомий' }}</div>
                                <div class="text-[11px] truncate max-w-[150px]" style="color: var(--kg-text-muted);">{{ b.user?.email }}</div>
                            </td>

                            <!-- Price -->
                            <td class="py-4 px-5">
                                <span class="font-bold text-sm" style="color: var(--kg-accent);">
                                    {{ b.price }} ₴
                                </span>
                            </td>

                            <!-- Condition -->
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-medium" style="background: var(--kg-surface-alt); color: var(--kg-text-secondary);">
                                    {{ b.condition }}
                                </span>
                            </td>

                            <!-- Date -->
                            <td class="py-4 px-5 text-xs" style="color: var(--kg-text-muted);">
                                {{ new Date(b.created_at).toLocaleDateString('uk-UA') }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <Link
                                        :href="`/books/${b.id}`"
                                        target="_blank"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
                                        :style="{ background: 'var(--kg-surface-alt)', color: 'var(--kg-text)' }"
                                        title="Відкрити сторінку книги"
                                    >
                                        Переглянути
                                    </Link>
                                    <button
                                        @click="deleteBook(b.id, b.title)"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all shadow-2xs hover:opacity-90 cursor-pointer"
                                        style="background: var(--kg-danger);"
                                        title="Видалити оголошення (модерація)"
                                    >
                                        Видалити
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="books.last_page > 1" class="p-4 border-t flex items-center justify-between" style="border-color: var(--kg-border);">
                <div class="text-xs" style="color: var(--kg-text-muted);">
                    Сторінка {{ books.current_page }} з {{ books.last_page }}
                </div>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="(link, i) in books.links"
                        :key="i"
                        :href="link.url || '#'"
                        v-text="decodePaginationLabel(link.label)"
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
