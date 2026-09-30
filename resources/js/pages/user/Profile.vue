<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    books?: Array<{
        id: number;
        title: string;
        author: string;
        price: number;
        condition: string;
        image_path?: string;
    }>;
}>();

function deleteBook(id: number) {
    if (confirm('Ви впевнені, що хочете видалити цю книгу?')) {
        router.delete(`/books/${id}`);
    }
}
</script>

<template>
  <MainLayout>
    <Head title="Мій профіль — knygogo" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-14">
      <!-- Profile Header -->
      <div class="rounded-2xl p-5 sm:p-6 lg:p-8 border mb-8" style="background: var(--kg-surface); border-color: var(--kg-border);">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5">
          <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center text-xl sm:text-2xl font-bold" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
            {{ $page.props.auth.user.name.charAt(0) }}
          </div>
          <div class="flex-1">
            <h1 class="text-xl sm:text-2xl font-heading" style="color: var(--kg-text);">{{ $page.props.auth.user.name }}</h1>
            <p class="text-sm" style="color: var(--kg-text-muted);">{{ $page.props.auth.user.email }}</p>
            <p class="text-xs mt-1" style="color: var(--kg-text-faint);">Учасник з {{ new Date($page.props.auth.user.created_at).toLocaleDateString('uk-UA') }}</p>
          </div>
          <Link href="/settings/profile" class="px-4 py-2 text-sm font-medium rounded-xl transition-all flex items-center gap-2" :style="{ background: 'var(--kg-surface-alt)', color: 'var(--kg-text-secondary)' }">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Налаштування
          </Link>
        </div>
      </div>

      <!-- My Books -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-heading" style="color: var(--kg-text);">Мої оголошення</h2>
        <Link href="/books/create" class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold rounded-xl transition-all shadow-sm text-sm" style="background: var(--kg-accent);">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          Додати книгу
        </Link>
      </div>

      <div v-if="!books || books.length === 0" class="rounded-2xl p-10 border text-center" style="background: var(--kg-surface); border-color: var(--kg-border);">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background: var(--kg-surface-alt);">
          <span class="text-2xl">📚</span>
        </div>
        <h3 class="text-lg font-bold mb-2" style="color: var(--kg-text);">У вас ще немає оголошень</h3>
        <p class="mb-6 max-w-sm mx-auto text-sm" style="color: var(--kg-text-muted);">Виставте свою першу книгу та знайдіть для неї нового власника.</p>
        <Link href="/books/create" class="inline-block px-6 py-3 border font-semibold rounded-xl transition-all shadow-sm" :style="{ borderColor: 'var(--kg-accent)', color: 'var(--kg-accent)', background: 'var(--kg-surface)' }">
          Створити оголошення
        </Link>
      </div>

      <div v-else class="space-y-3 sm:space-y-4">
        <div v-for="book in books" :key="book.id" class="rounded-2xl border overflow-hidden transition-shadow hover:shadow-md" style="background: var(--kg-surface); border-color: var(--kg-border);">
          <div class="flex flex-col sm:flex-row">
            <!-- Image -->
            <Link :href="`/books/${book.id}`" class="sm:w-28 lg:w-36 flex-shrink-0">
              <div class="aspect-[4/3] sm:aspect-auto sm:h-full flex items-center justify-center overflow-hidden" style="background: var(--kg-surface-alt);">
                <img v-if="book.image_path" :src="'/storage/' + book.image_path" class="w-full h-full object-cover" />
                <span v-else class="text-3xl opacity-20">📖</span>
              </div>
            </Link>

            <!-- Info -->
            <div class="flex-1 p-4 lg:p-5 flex flex-col">
              <div class="flex-1">
                <Link :href="`/books/${book.id}`" class="font-heading text-[15px] sm:text-[17px] transition-colors" style="color: var(--kg-text);">
                  {{ book.title }}
                </Link>
                <p class="text-sm mt-0.5" style="color: var(--kg-text-muted);">{{ book.author }}</p>
                <div class="flex items-center gap-3 mt-2">
                  <span class="text-lg font-bold" style="color: var(--kg-accent);">{{ book.price }} ₴</span>
                  <span class="text-xs px-2 py-0.5 rounded-md" style="background: var(--kg-surface-alt); color: var(--kg-text-muted);">{{ book.condition }}</span>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex items-center gap-3 mt-4 pt-3" style="border-top: 1px solid var(--kg-surface-alt);">
                <Link :href="`/books/${book.id}`" class="text-xs font-medium" style="color: var(--kg-accent);">Переглянути</Link>
                <Link :href="`/books/${book.id}/edit`" class="text-xs font-medium transition-colors" style="color: var(--kg-text-secondary);">Редагувати</Link>
                <button @click="deleteBook(book.id)" class="text-xs font-medium transition-colors" style="color: var(--kg-text-muted);">Видалити</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>