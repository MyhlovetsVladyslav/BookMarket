<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    book: {
        id: number;
        title: string;
        author: string;
        price: number;
        condition: string;
        description?: string;
        image_path?: string;
    };
}>();

const form = useForm({
    _method: 'PUT',
    title: props.book.title,
    author: props.book.author,
    price: String(props.book.price),
    condition: props.book.condition,
    description: props.book.description || '',
    image: null as File | null,
});

const imagePreview = ref<string | null>(props.book.image_path ? `/storage/${props.book.image_path}` : null);

function handleImageChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
}

const submit = () => {
    form.post(`/books/${props.book.id}`, {
        forceFormData: true,
    });
};
</script>

<template>
    <MainLayout>
        <Head title="Редагувати книгу — knygogo" />
        <div class="max-w-3xl mx-auto px-4 py-8 lg:py-14">
            <div class="mb-8">
                <Link href="/dashboard" class="inline-flex items-center gap-2 text-sm font-medium transition-colors mb-4" style="color: var(--kg-text-muted);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Назад до моїх книг
                </Link>
                <h1 class="text-2xl sm:text-3xl font-heading" style="color: var(--kg-text);">Редагувати книгу</h1>
                <p class="mt-2" style="color: var(--kg-text-muted);">Оновіть деталі вашого оголошення.</p>
            </div>

            <div class="rounded-2xl p-5 sm:p-6 lg:p-8 border" style="background: var(--kg-surface); border-color: var(--kg-border);">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Image upload -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold" style="color: var(--kg-text);">Фото книги</label>
                        <div class="relative group cursor-pointer" @click="($refs.imageInput as HTMLInputElement).click()">
                            <div class="aspect-[16/9] rounded-xl border-2 border-dashed transition-colors flex items-center justify-center overflow-hidden" style="background: var(--kg-surface-alt); border-color: var(--kg-border);">
                                <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                                <div v-else class="text-center p-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2" style="color: var(--kg-text-faint);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm" style="color: var(--kg-text-muted);">Натисніть, щоб завантажити нове фото</p>
                                </div>
                            </div>
                            <input ref="imageInput" type="file" class="hidden" accept="image/*" @change="handleImageChange" />
                        </div>
                        <div v-if="form.errors.image" class="text-xs" style="color: var(--kg-danger);">{{ form.errors.image }}</div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5 sm:gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold" style="color: var(--kg-text);">Назва книги *</label>
                            <input v-model="form.title" type="text" required class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2" :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }" />
                            <div v-if="form.errors.title" class="text-xs" style="color: var(--kg-danger);">{{ form.errors.title }}</div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold" style="color: var(--kg-text);">Автор *</label>
                            <input v-model="form.author" type="text" required class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2" :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }" />
                            <div v-if="form.errors.author" class="text-xs" style="color: var(--kg-danger);">{{ form.errors.author }}</div>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5 sm:gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold" style="color: var(--kg-text);">Ціна (₴) *</label>
                            <input v-model="form.price" type="number" required min="0" class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2" :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }" />
                            <div v-if="form.errors.price" class="text-xs" style="color: var(--kg-danger);">{{ form.errors.price }}</div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold" style="color: var(--kg-text);">Стан книги *</label>
                            <select v-model="form.condition" class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all focus:ring-2" :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }">
                                <option>Нова</option>
                                <option>Ідеальний стан</option>
                                <option>Гарний стан</option>
                                <option>Задовільний</option>
                            </select>
                            <div v-if="form.errors.condition" class="text-xs" style="color: var(--kg-danger);">{{ form.errors.condition }}</div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold" style="color: var(--kg-text);">Опис</label>
                        <textarea v-model="form.description" rows="4" class="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all resize-y focus:ring-2" :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }" placeholder="Розкажіть більше..."></textarea>
                        <div v-if="form.errors.description" class="text-xs" style="color: var(--kg-danger);">{{ form.errors.description }}</div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="form.processing" class="px-8 py-3 text-white font-semibold rounded-xl transition-all shadow-md hover:-translate-y-0.5 disabled:opacity-70" :style="{ background: 'var(--kg-accent)', boxShadow: '0 4px 16px var(--kg-accent-shadow)' }">
                            {{ form.processing ? 'Зберігаємо...' : 'Зберегти зміни' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>
