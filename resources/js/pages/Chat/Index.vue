<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    conversations: Array<{
        id: number;
        other_user: { id: number; name: string };
        book: { id: number; title: string; image_path?: string } | null;
        book_title_snapshot: string | null;
        book_deleted: boolean;
        latest_message: { body: string; created_at: string; is_mine: boolean } | null;
        unread_count: number;
        updated_at: string;
    }>;
}>();

function formatTime(dateStr: string): string {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffDays === 0) {
        return date.toLocaleTimeString('uk-UA', { hour: '2-digit', minute: '2-digit' });
    } else if (diffDays === 1) {
        return 'Вчора';
    } else if (diffDays < 7) {
        return date.toLocaleDateString('uk-UA', { weekday: 'short' });
    } else {
        return date.toLocaleDateString('uk-UA', { day: 'numeric', month: 'short' });
    }
}

function getBookDisplayTitle(conv: { book: { title: string } | null; book_title_snapshot: string | null }): string | null {
    if (conv.book) return conv.book.title;
    if (conv.book_title_snapshot) return conv.book_title_snapshot;
    return null;
}
</script>

<template>
  <MainLayout>
    <Head title="Повідомлення — knygogo" />

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-14">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-heading" style="color: var(--kg-text);">Повідомлення</h1>
          <p class="mt-1 text-sm" style="color: var(--kg-text-muted);">Ваші розмови з продавцями та покупцями</p>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="!conversations || conversations.length === 0" class="rounded-2xl p-10 border text-center" style="background: var(--kg-surface); border-color: var(--kg-border);">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background: var(--kg-surface-alt);">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" style="color: var(--kg-text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold mb-2" style="color: var(--kg-text);">Поки що немає повідомлень</h3>
        <p class="mb-6 max-w-sm mx-auto text-sm" style="color: var(--kg-text-muted);">Знайдіть книгу в каталозі та натисніть «Зв'язатись з продавцем», щоб розпочати розмову.</p>
        <Link href="/" class="inline-block px-6 py-3 text-white font-semibold rounded-xl transition-all shadow-sm" style="background: var(--kg-accent);">
          Перейти до каталогу
        </Link>
      </div>

      <!-- Conversations list -->
      <div v-else class="space-y-2.5">
        <Link
          v-for="conv in conversations"
          :key="conv.id"
          :href="`/chat/${conv.id}`"
          class="block rounded-2xl border p-4 lg:p-5 transition-all group"
          :style="{ background: 'var(--kg-surface)', borderColor: 'var(--kg-border)' }"
        >
          <div class="flex items-start gap-3 sm:gap-4">
            <!-- Avatar -->
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-base sm:text-lg font-bold flex-shrink-0" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
              {{ conv.other_user.name.charAt(0) }}
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2 mb-1">
                <h3 class="font-heading text-[15px] sm:text-base truncate transition-colors" style="color: var(--kg-text);">
                  {{ conv.other_user.name }}
                </h3>
                <span v-if="conv.latest_message" class="text-xs flex-shrink-0" style="color: var(--kg-text-faint);">
                  {{ formatTime(conv.latest_message.created_at) }}
                </span>
              </div>

              <!-- Book tag -->
              <div v-if="getBookDisplayTitle(conv)" class="flex items-center gap-2 mb-2">
                <div class="w-5 h-7 sm:w-6 sm:h-8 rounded flex items-center justify-center overflow-hidden flex-shrink-0" style="background: var(--kg-surface-alt);">
                  <img v-if="conv.book?.image_path" :src="'/storage/' + conv.book.image_path" class="w-full h-full object-cover" />
                  <span v-else class="text-[10px] opacity-30">📖</span>
                </div>
                <span class="text-xs truncate" style="color: var(--kg-text-muted);">{{ getBookDisplayTitle(conv) }}</span>
                <span v-if="conv.book_deleted" class="text-[10px] px-1.5 py-0.5 rounded-md font-medium flex-shrink-0" style="background: var(--kg-danger-light); color: var(--kg-danger);">неактуальне</span>
              </div>

              <!-- Last message -->
              <div class="flex items-center justify-between gap-2">
                <p v-if="conv.latest_message" class="text-sm truncate" style="color: var(--kg-text-muted);">
                  <span v-if="conv.latest_message.is_mine" style="color: var(--kg-text-faint);">Ви: </span>
                  {{ conv.latest_message.body }}
                </p>
                <p v-else class="text-sm italic" style="color: var(--kg-text-faint);">Ще немає повідомлень</p>

                <!-- Unread badge -->
                <span v-if="conv.unread_count > 0" class="text-white text-xs font-bold px-2 py-0.5 rounded-full flex-shrink-0 min-w-[20px] text-center" style="background: var(--kg-accent);">
                  {{ conv.unread_count }}
                </span>
              </div>
            </div>
          </div>
        </Link>
      </div>
    </div>
  </MainLayout>
</template>
