<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue';

const props = defineProps<{
    conversation: {
        id: number;
        other_user: { id: number; name: string };
        book: { id: number; title: string; image_path?: string; price: number } | null;
        book_title_snapshot: string | null;
        book_deleted: boolean;
    };
    messages: Array<{
        id: number;
        body: string;
        user: { id: number; name: string };
        is_mine: boolean;
        created_at: string;
    }>;
}>();

const allMessages = ref([...props.messages]);
const messagesContainer = ref<HTMLElement | null>(null);
let pollInterval: ReturnType<typeof setInterval> | null = null;

const form = useForm({
    body: '',
});

const bookDisplayTitle = props.conversation.book?.title || props.conversation.book_title_snapshot;

function sendMessage() {
    if (!form.body.trim()) return;

    form.post(`/chat/${props.conversation.id}/messages`, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('body');
            fetchNewMessages();
        },
    });
}

function scrollToBottom() {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
}

async function fetchNewMessages() {
    const lastId = allMessages.value.length > 0
        ? allMessages.value[allMessages.value.length - 1].id
        : 0;

    try {
        const response = await fetch(`/chat/${props.conversation.id}/messages?after_id=${lastId}`);
        if (response.ok) {
            const newMessages = await response.json();
            if (newMessages.length > 0) {
                allMessages.value.push(...newMessages);
                scrollToBottom();
            }
        }
    } catch (e) {
        // Silently fail on polling errors
    }
}

function formatTime(dateStr: string): string {
    const date = new Date(dateStr);
    return date.toLocaleTimeString('uk-UA', { hour: '2-digit', minute: '2-digit' });
}

function formatDate(dateStr: string): string {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Сьогодні';
    if (diffDays === 1) return 'Вчора';
    return date.toLocaleDateString('uk-UA', { day: 'numeric', month: 'long' });
}

function shouldShowDate(index: number): boolean {
    if (index === 0) return true;
    const current = new Date(allMessages.value[index].created_at).toDateString();
    const prev = new Date(allMessages.value[index - 1].created_at).toDateString();
    return current !== prev;
}

function startPolling() {
    if (!pollInterval) {
        pollInterval = setInterval(fetchNewMessages, 5000);
    }
}

function stopPolling() {
    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
}

function handleVisibilityChange() {
    if (document.hidden) {
        stopPolling();
    } else {
        fetchNewMessages();
        startPolling();
    }
}

onMounted(() => {
    scrollToBottom();
    startPolling();
    document.addEventListener('visibilitychange', handleVisibilityChange);
});

onUnmounted(() => {
    stopPolling();
    document.removeEventListener('visibilitychange', handleVisibilityChange);
});

watch(() => props.messages, (newMessages) => {
    allMessages.value = [...newMessages];
    scrollToBottom();
});
</script>

<template>
  <MainLayout>
    <Head :title="`Чат з ${conversation.other_user.name} — knygogo`" />

    <div class="max-w-3xl mx-auto px-2 sm:px-4 lg:px-8 py-4 sm:py-6 lg:py-8 flex flex-col" style="height: calc(100dvh - 73px);">

      <!-- Chat header -->
      <div class="rounded-2xl border p-3 sm:p-4 lg:p-5 mb-3 sm:mb-4 flex-shrink-0" style="background: var(--kg-surface); border-color: var(--kg-border);">
        <div class="flex items-center gap-3 sm:gap-4">
          <Link href="/chat" class="p-2 rounded-xl transition-all flex-shrink-0" style="color: var(--kg-text-muted);">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </Link>

          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
            {{ conversation.other_user.name.charAt(0) }}
          </div>

          <div class="flex-1 min-w-0">
            <h2 class="font-heading truncate" style="color: var(--kg-text);">{{ conversation.other_user.name }}</h2>
            <div v-if="bookDisplayTitle" class="flex items-center gap-2 mt-0.5">
              <span class="text-xs truncate" style="color: var(--kg-text-muted);">{{ bookDisplayTitle }}</span>
              <template v-if="conversation.book && !conversation.book_deleted">
                <span class="text-xs font-bold" style="color: var(--kg-accent);">{{ conversation.book.price }} ₴</span>
              </template>
              <span v-if="conversation.book_deleted" class="text-[10px] px-1.5 py-0.5 rounded-md font-medium flex-shrink-0" style="background: var(--kg-danger-light); color: var(--kg-danger);">неактуальне</span>
            </div>
          </div>

          <Link
            v-if="conversation.book && !conversation.book_deleted"
            :href="`/books/${conversation.book.id}`"
            class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium transition-all flex-shrink-0"
            :style="{ background: 'var(--kg-surface-alt)', color: 'var(--kg-text-secondary)' }"
          >
            <div class="w-5 h-7 rounded overflow-hidden flex items-center justify-center" style="background: var(--kg-surface);">
              <img v-if="conversation.book.image_path" :src="'/storage/' + conversation.book.image_path" class="w-full h-full object-cover" />
              <span v-else class="text-[10px] opacity-30">📖</span>
            </div>
            <span class="hidden sm:inline">Переглянути</span>
          </Link>
        </div>

        <!-- Deleted book banner -->
        <div v-if="conversation.book_deleted" class="mt-3 p-2.5 rounded-xl text-xs font-medium flex items-center gap-2" style="background: var(--kg-danger-light); color: var(--kg-danger);">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
          Це оголошення більше не актуальне — книгу «{{ bookDisplayTitle }}» було видалено
        </div>
      </div>

      <!-- Messages area -->
      <div
        ref="messagesContainer"
        class="flex-1 overflow-y-auto rounded-2xl border p-3 sm:p-4 lg:p-6 mb-3 sm:mb-4 kg-scrollbar"
        style="background: var(--kg-surface); border-color: var(--kg-border);"
      >
        <!-- Empty state -->
        <div v-if="allMessages.length === 0" class="flex flex-col items-center justify-center h-full text-center py-10">
          <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4" style="background: var(--kg-surface-alt);">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" style="color: var(--kg-text-faint);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
          </div>
          <p class="text-sm" style="color: var(--kg-text-muted);">Напишіть перше повідомлення!</p>
        </div>

        <!-- Messages -->
        <div v-else class="space-y-2.5 sm:space-y-3">
          <template v-for="(msg, index) in allMessages" :key="msg.id">
            <!-- Date separator -->
            <div v-if="shouldShowDate(index)" class="flex items-center gap-3 my-4">
              <div class="flex-1 h-px" style="background: var(--kg-border);"></div>
              <span class="text-xs font-medium" style="color: var(--kg-text-faint);">{{ formatDate(msg.created_at) }}</span>
              <div class="flex-1 h-px" style="background: var(--kg-border);"></div>
            </div>

            <!-- Message bubble -->
            <div :class="['flex', msg.is_mine ? 'justify-end' : 'justify-start']">
              <div
                class="max-w-[80%] sm:max-w-[75%] rounded-2xl px-3.5 sm:px-4 py-2.5 sm:py-3 relative"
                :style="{
                  background: msg.is_mine ? 'var(--kg-chat-mine-bg)' : 'var(--kg-chat-other-bg)',
                  color: msg.is_mine ? 'var(--kg-chat-mine-text)' : 'var(--kg-chat-other-text)',
                  borderBottomRightRadius: msg.is_mine ? '6px' : undefined,
                  borderBottomLeftRadius: !msg.is_mine ? '6px' : undefined,
                }"
              >
                <p class="text-[14px] sm:text-[15px] leading-relaxed whitespace-pre-line break-words">{{ msg.body }}</p>
                <span
                  class="text-[10px] mt-1 block text-right"
                  :style="{ color: msg.is_mine ? 'var(--kg-chat-mine-time)' : 'var(--kg-chat-other-time)' }"
                >
                  {{ formatTime(msg.created_at) }}
                </span>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Message input -->
      <div class="rounded-2xl border p-2.5 sm:p-3 lg:p-4 flex-shrink-0" style="background: var(--kg-surface); border-color: var(--kg-border);">
        <form @submit.prevent="sendMessage" class="flex items-end gap-2 sm:gap-3">
          <div class="flex-1">
            <textarea
              v-model="form.body"
              placeholder="Написати повідомлення..."
              rows="1"
              class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl border text-[14px] sm:text-[15px] resize-none transition-all focus:outline-none focus:ring-2"
              :style="{
                background: 'var(--kg-input-bg)',
                borderColor: 'var(--kg-border)',
                color: 'var(--kg-text)',
                '--tw-ring-color': 'var(--kg-accent-light)',
              }"
              style="--placeholder-color: var(--kg-text-faint);"
              @keydown.enter.exact.prevent="sendMessage"
              @input="(e: Event) => {
                const target = e.target as HTMLTextAreaElement;
                target.style.height = 'auto';
                target.style.height = Math.min(target.scrollHeight, 120) + 'px';
              }"
            ></textarea>
          </div>
          <button
            type="submit"
            :disabled="form.processing || !form.body.trim()"
            class="p-2.5 sm:p-3 text-white rounded-xl transition-all shadow-md disabled:opacity-40 disabled:cursor-not-allowed flex-shrink-0"
            :style="{ background: 'var(--kg-accent)', boxShadow: '0 4px 12px var(--kg-accent-shadow)' }"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
          </button>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<style scoped>
textarea::placeholder {
    color: var(--kg-text-faint);
}
</style>
