<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { getConditionStyle } from '@/lib/conditions';

const props = defineProps<{
    book: {
        id: number;
        title: string;
        author: string;
        price: number;
        condition: string;
        description?: string;
        image_path?: string;
        created_at: string;
        user: {
            id: number;
            name: string;
        };
    };
}>();

const page = usePage();
const currentUserId = page.props.auth?.user?.id;
const isOwner = currentUserId === props.book.user.id;



const chatForm = useForm({
    seller_id: props.book.user.id,
    book_id: props.book.id,
});

function contactSeller() {
    if (!currentUserId) {
        window.location.href = '/login';
        return;
    }
    chatForm.post('/chat');
}
</script>

<template>
    <MainLayout>
        <Head :title="`${book.title} — knygogo`" />

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-14">
            <!-- Back -->
            <Link href="/" class="inline-flex items-center gap-2 text-sm font-medium transition-colors mb-6 sm:mb-8" style="color: var(--kg-text-muted);">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Назад до каталогу
            </Link>

            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
                <!-- Image -->
                <div class="aspect-[3/4] rounded-2xl overflow-hidden border" style="background: var(--kg-surface-alt); border-color: var(--kg-border);">
                    <img v-if="book.image_path" :src="'/storage/' + book.image_path" :alt="book.title" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <span class="text-8xl opacity-20">📖</span>
                    </div>
                </div>

                <!-- Details -->
                <div class="flex flex-col">
                    <span
                      class="inline-block self-start px-3 py-1 text-xs font-bold rounded-lg mb-4"
                      :style="{ background: getConditionStyle(book.condition).bg, color: getConditionStyle(book.condition).text }"
                    >
                        {{ book.condition }}
                    </span>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-heading mb-2" style="color: var(--kg-text);">{{ book.title }}</h1>
                    <p class="text-base sm:text-lg mb-6" style="color: var(--kg-text-muted);">{{ book.author }}</p>

                    <div class="text-2xl sm:text-3xl font-bold mb-8" style="color: var(--kg-accent);">{{ book.price }} ₴</div>

                    <!-- Description -->
                    <div v-if="book.description" class="mb-8">
                        <h3 class="text-sm font-semibold uppercase tracking-widest mb-3" style="color: var(--kg-text-muted);">Опис</h3>
                        <p class="leading-relaxed whitespace-pre-line" style="color: var(--kg-text-secondary);">{{ book.description }}</p>
                    </div>

                    <!-- Seller -->
                    <div class="rounded-2xl p-5 mb-6" style="background: var(--kg-surface-alt);">
                        <h3 class="text-sm font-semibold uppercase tracking-widest mb-3" style="color: var(--kg-text-muted);">Продавець</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                                {{ book.user.name.charAt(0) }}
                            </div>
                            <span class="font-semibold" style="color: var(--kg-text);">{{ book.user.name }}</span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-wrap gap-3 mt-auto pt-4">
                        <!-- Contact seller button — hidden if owner -->
                        <button
                            v-if="!isOwner"
                            @click="contactSeller"
                            :disabled="chatForm.processing"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 sm:py-4 text-white font-semibold rounded-xl transition-all shadow-md hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed"
                            :style="{ background: 'var(--kg-accent)', boxShadow: '0 8px 24px var(--kg-accent-shadow)' }"
                        >
                            <LoaderCircle v-if="chatForm.processing" class="h-5 w-5 animate-spin" />
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Зв'язатись з продавцем
                        </button>

                        <!-- Edit/Delete buttons if owner -->
                        <template v-if="isOwner">
                            <Link :href="`/books/${book.id}/edit`" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 sm:py-4 font-semibold rounded-xl transition-all" :style="{ background: 'var(--kg-surface-alt)', color: 'var(--kg-text-secondary)' }">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Редагувати
                            </Link>
                        </template>
                    </div>

                    <p class="text-xs mt-4" style="color: var(--kg-text-faint);">Опубліковано: {{ new Date(book.created_at).toLocaleDateString('uk-UA') }}</p>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
