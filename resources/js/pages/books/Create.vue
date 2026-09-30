<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import imageCompression from 'browser-image-compression';
import { conditionOptions, getConditionStyle } from '@/lib/conditions';
import { Trash2, UploadCloud, Search, PlusCircle, CheckCircle2 } from 'lucide-vue-next';

interface DraftBook {
    id: string; // temp id
    file: File | null;
    previewUrl: string;
    title: string;
    author: string;
    price: string;
    condition: string;
    isCompressing: boolean;
    searchResults: Array<{ title: string; author: string; thumbnail?: string }>;
    isSearching: boolean;
    showDropdown: boolean;
}

const drafts = ref<DraftBook[]>([]);
const isSubmitting = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const addFiles = async (files: FileList | File[]) => {
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (!file.type.startsWith('image/')) continue;

        const draft: DraftBook = {
            id: Math.random().toString(36).substring(7),
            file: null,
            previewUrl: URL.createObjectURL(file),
            title: '',
            author: '',
            price: '',
            condition: 'Гарний стан',
            isCompressing: true,
            searchResults: [],
            isSearching: false,
            showDropdown: false,
        };
        
        drafts.value.unshift(draft);

        // Compress image in background
        try {
            const options = {
                maxSizeMB: 0.3,
                maxWidthOrHeight: 1200,
                useWebWorker: true,
                fileType: 'image/webp'
            };
            const compressedFile = await imageCompression(file, options);
            draft.file = compressedFile;
            draft.previewUrl = URL.createObjectURL(compressedFile); // Update with compressed preview
        } catch (error) {
            console.error("Compression error:", error);
            draft.file = file; // Fallback to original
        } finally {
            draft.isCompressing = false;
        }
    }
};

const handleDrop = (e: DragEvent) => {
    if (e.dataTransfer?.files) {
        addFiles(e.dataTransfer.files);
    }
};

const handleFileSelect = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (files) {
        addFiles(files);
    }
    if (fileInput.value) fileInput.value.value = ''; // Reset input
};

const removeDraft = (id: string) => {
    const index = drafts.value.findIndex(d => d.id === id);
    if (index !== -1) {
        URL.revokeObjectURL(drafts.value[index].previewUrl);
        drafts.value.splice(index, 1);
    }
};

let searchTimeout: any = null;
const searchGoogleBooks = (draft: DraftBook) => {
    if (draft.title.trim().length < 3) {
        draft.searchResults = [];
        draft.showDropdown = false;
        return;
    }

    clearTimeout(searchTimeout);
    draft.isSearching = true;
    draft.showDropdown = true;

    searchTimeout = setTimeout(async () => {
        try {
            const query = encodeURIComponent(`intitle:${draft.title}`);
            const res = await fetch(`https://www.googleapis.com/books/v1/volumes?q=${query}&maxResults=5&langRestrict=uk`);
            const data = await res.json();
            
            if (data.items) {
                draft.searchResults = data.items.map((item: any) => ({
                    title: item.volumeInfo.title,
                    author: item.volumeInfo.authors ? item.volumeInfo.authors.join(', ') : 'Невідомий автор',
                    thumbnail: item.volumeInfo.imageLinks?.smallThumbnail
                }));
            } else {
                draft.searchResults = [];
            }
        } catch (e) {
            console.error("Book search failed", e);
        } finally {
            draft.isSearching = false;
        }
    }, 500);
};

const selectSearchResult = (draft: DraftBook, result: { title: string; author: string }) => {
    draft.title = result.title;
    draft.author = result.author;
    draft.showDropdown = false;
};

const canSubmit = computed(() => {
    if (drafts.value.length === 0) return false;
    // Check if all drafts have title, author, price, condition and file (compression done)
    return drafts.value.every(d => 
        d.title.trim() !== '' && 
        d.author.trim() !== '' && 
        d.price !== '' && 
        d.file !== null && 
        !d.isCompressing
    );
});

const submitAll = () => {
    if (!canSubmit.value) return;

    isSubmitting.value = true;
    const formData = new FormData();

    drafts.value.forEach((draft, index) => {
        formData.append(`books[${index}][title]`, draft.title);
        formData.append(`books[${index}][author]`, draft.author);
        formData.append(`books[${index}][price]`, draft.price);
        formData.append(`books[${index}][condition]`, draft.condition);
        if (draft.file) {
            formData.append(`books[${index}][image]`, draft.file);
        }
    });

    router.post('/books/batch', formData, {
        forceFormData: true,
        onFinish: () => { isSubmitting.value = false; }
    });
};

const delayHideDropdown = (draft: DraftBook) => {
    setTimeout(() => {
        draft.showDropdown = false;
    }, 200);
};
</script>

<template>
    <MainLayout>
        <Head title="Пакетна публікація — knygogo" />
        <div class="max-w-4xl mx-auto px-4 py-8 lg:py-14">
            
            <!-- Header section -->
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <Link href="/dashboard" class="inline-flex items-center gap-2 text-sm font-medium transition-colors mb-4 hover:text-opacity-80" style="color: var(--kg-text-muted);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Назад до профілю
                    </Link>
                    <h1 class="text-2xl sm:text-4xl font-heading tracking-tight" style="color: var(--kg-text);">Виставити книги</h1>
                    <p class="mt-2 text-sm sm:text-base" style="color: var(--kg-text-muted);">Перетягніть фотографії сюди. Ви можете додати відразу кілька книг.</p>
                </div>
                
                <button 
                    v-if="drafts.length > 0"
                    @click="submitAll" 
                    :disabled="!canSubmit || isSubmitting"
                    class="px-8 py-3 text-white font-semibold rounded-xl transition-all shadow-md hover:-translate-y-0.5 disabled:opacity-50 disabled:hover:translate-y-0 disabled:cursor-not-allowed flex items-center gap-2"
                    :style="{ background: 'var(--kg-accent)', boxShadow: '0 4px 16px var(--kg-accent-shadow)' }"
                >
                    <CheckCircle2 v-if="!isSubmitting" class="w-5 h-5" />
                    <svg v-else class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isSubmitting ? 'Публікуємо...' : `Опублікувати всі (${drafts.length})` }}
                </button>
            </div>

            <!-- Dropzone -->
            <div 
                @dragover.prevent 
                @drop.prevent="handleDrop"
                class="rounded-3xl border-2 border-dashed p-8 md:p-12 mb-8 flex flex-col items-center justify-center text-center transition-all cursor-pointer hover:bg-opacity-50 group"
                :style="{ background: 'var(--kg-surface-alt)', borderColor: 'var(--kg-border)' }"
                @click="fileInput?.click()"
            >
                <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 transition-transform group-hover:scale-110" :style="{ background: 'var(--kg-surface)', color: 'var(--kg-accent)' }">
                    <UploadCloud class="w-10 h-10" />
                </div>
                <h3 class="text-xl font-heading mb-2" style="color: var(--kg-text);">Перетягніть фото сюди</h3>
                <p class="text-sm max-w-md" style="color: var(--kg-text-muted);">або натисніть, щоб вибрати файли. Зображення будуть автоматично стиснуті для економії трафіку.</p>
                <input ref="fileInput" type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect" />
            </div>

            <!-- Drafts List -->
            <div v-if="drafts.length > 0" class="space-y-6 relative">
                <div 
                    v-for="(draft, index) in drafts" 
                    :key="draft.id"
                    class="rounded-2xl border p-4 sm:p-6 transition-all relative overflow-visible flex flex-col sm:flex-row gap-5 sm:gap-8"
                    :style="{ background: 'var(--kg-surface)', borderColor: 'var(--kg-border)' }"
                >
                    <!-- Image Preview -->
                    <div class="w-full sm:w-40 h-48 sm:h-56 flex-shrink-0 relative rounded-xl overflow-hidden shadow-sm bg-neutral-100 dark:bg-neutral-800 border" :style="{ borderColor: 'var(--kg-border)' }">
                        <img :src="draft.previewUrl" class="w-full h-full object-cover" />
                        
                        <!-- Compression Loading overlay -->
                        <div v-if="draft.isCompressing" class="absolute inset-0 bg-black/40 backdrop-blur-sm flex flex-col items-center justify-center text-white">
                            <svg class="animate-spin w-8 h-8 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-medium">Стискаємо...</span>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="flex-1 flex flex-col min-w-0">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-xs font-bold px-2 py-1 rounded-md" :style="{ background: 'var(--kg-surface-alt)', color: 'var(--kg-text-muted)' }">Книга #{{ drafts.length - index }}</span>
                            <button @click.prevent="removeDraft(draft.id)" class="text-rose-500 hover:text-rose-600 transition-colors p-1" title="Видалити">
                                <Trash2 class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Title with Autocomplete -->
                            <div class="relative space-y-1.5 md:col-span-2">
                                <label class="text-xs font-semibold" style="color: var(--kg-text-muted);">Назва книги *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <Search class="h-4 w-4" style="color: var(--kg-text-faint);" />
                                    </div>
                                    <input 
                                        v-model="draft.title" 
                                        @input="searchGoogleBooks(draft)"
                                        @focus="draft.showDropdown = draft.searchResults.length > 0"
                                        @blur="delayHideDropdown(draft)"
                                        type="text" 
                                        required 
                                        class="w-full pl-10 pr-4 py-2.5 border rounded-xl text-sm outline-none transition-all focus:ring-2" 
                                        :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }" 
                                        placeholder="Наприклад: Місто" 
                                    />
                                </div>
                                
                                <!-- Autocomplete Dropdown -->
                                <div v-if="draft.showDropdown && (draft.searchResults.length > 0 || draft.isSearching)" 
                                     class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-900 border rounded-xl shadow-xl overflow-hidden"
                                     :style="{ borderColor: 'var(--kg-border)' }">
                                    <div v-if="draft.isSearching" class="p-3 text-center text-xs text-neutral-500">Шукаємо в базі...</div>
                                    <ul v-else class="max-h-60 overflow-y-auto">
                                        <li 
                                            v-for="(res, i) in draft.searchResults" 
                                            :key="i"
                                            @click="selectSearchResult(draft, res)"
                                            class="p-3 hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer flex gap-3 items-center border-b last:border-0 transition-colors"
                                            :style="{ borderColor: 'var(--kg-surface-alt)' }"
                                        >
                                            <div class="w-8 h-12 bg-neutral-200 dark:bg-neutral-700 flex-shrink-0 rounded overflow-hidden">
                                                <img v-if="res.thumbnail" :src="res.thumbnail" class="w-full h-full object-cover" />
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium truncate" style="color: var(--kg-text);">{{ res.title }}</div>
                                                <div class="text-xs truncate" style="color: var(--kg-text-muted);">{{ res.author }}</div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold" style="color: var(--kg-text-muted);">Автор *</label>
                                <input v-model="draft.author" type="text" required class="w-full px-4 py-2.5 border rounded-xl text-sm outline-none transition-all focus:ring-2" :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }" placeholder="Наприклад: Валер'ян Підмогильний" />
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold" style="color: var(--kg-text-muted);">Ціна (₴) *</label>
                                <input v-model="draft.price" type="number" required min="1" class="w-full px-4 py-2.5 border rounded-xl text-sm outline-none transition-all focus:ring-2" :style="{ background: 'var(--kg-input-bg)', borderColor: 'var(--kg-border)', color: 'var(--kg-text)' }" placeholder="0" />
                            </div>

                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-xs font-semibold" style="color: var(--kg-text-muted);">Стан книги *</label>
                                <div class="flex flex-wrap gap-2">
                                    <button 
                                        v-for="cond in conditionOptions" 
                                        :key="cond"
                                        @click.prevent="draft.condition = cond"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all border"
                                        :style="{ 
                                            background: draft.condition === cond ? getConditionStyle(cond).bg : 'transparent',
                                            color: draft.condition === cond ? getConditionStyle(cond).text : 'var(--kg-text-muted)',
                                            borderColor: draft.condition === cond ? getConditionStyle(cond).bg : 'var(--kg-border)'
                                        }"
                                    >
                                        {{ cond }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-else class="text-center p-8 opacity-50">
                <p>Додайте фотографії, щоб почати</p>
            </div>

        </div>
    </MainLayout>
</template>
