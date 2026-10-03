<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { onMounted, nextTick, computed } from 'vue';
import { getConditionStyle } from '@/lib/conditions';

const page = usePage();
const isGuest = computed(() => !page.props.auth?.user);

interface BookItem {
    id: number;
    title: string;
    author: string;
    price: number;
    condition: string;
    image_path?: string;
    user?: { id: number; name: string };
}

const props = defineProps<{
    books?: {
        data: BookItem[];
        current_page: number;
        last_page: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
        total: number;
    };
}>();

const bookList = computed(() => props.books?.data ?? []);

const testimonials = [
  { name: 'Олена К.', text: 'Знайшла рідкісну книгу за чудовою ціною. Продавець відповів одразу. Дуже зручна барахолка!', rating: 5 },
  { name: 'Максим Д.', text: 'Продав 15 книг за тиждень. Все просто: додав фото, вказав ціну — і покупці знаходяться самі.', rating: 5 },
  { name: 'Анна П.', text: 'Подобається еко-підхід. Купую б/в книги тут і даю їм друге життя. Ціни дуже приємні!', rating: 4 },
];

onMounted(() => {
  nextTick(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1, rootMargin: '0px 0px -60px 0px' }
    );
    document.querySelectorAll('.reveal').forEach((el) => {
      observer.observe(el);
    });
  });
});
</script>

<template>
  <MainLayout>
    <Head title="Книжкова барахолка">
      <meta name="description" content="Knygogo — книжкова барахолка. Купуй та продавай книги швидко та зручно." />
    </Head>

    <!-- ===== HERO SECTION ===== -->
    <section class="relative overflow-hidden">
      <div class="absolute top-20 right-0 w-96 h-96 rounded-full blur-3xl animate-pulse-soft" style="background: var(--kg-accent-light);"></div>
      <div class="absolute bottom-0 left-10 w-72 h-72 rounded-full blur-3xl animate-pulse-soft" style="background: var(--kg-danger-light); animation-delay: 2s;"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-28">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
          <div class="max-w-xl animate-fade-in-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold mb-8" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              Продавай та купуй книги легко
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-[3.25rem] xl:text-6xl font-heading leading-[1.1] mb-6" style="color: var(--kg-text);">
              Купуй та продавай книги на
              <span class="relative inline-block">
                <span class="relative z-10">справжній барахолці</span>
                <span class="absolute bottom-1 left-0 right-0 h-3 rounded-sm -rotate-1" style="background: var(--kg-accent-light);"></span>
              </span>
            </h1>

            <p class="text-lg leading-relaxed mb-10 max-w-md" style="color: var(--kg-text-muted);">
              knygogo — це місце, де кожен може знайти рідкісні видання та продати власні прочитані книги іншим книголюбам.
            </p>

            <div class="flex flex-wrap gap-3 sm:gap-4">
              <a href="#catalog" class="group inline-flex items-center gap-2.5 px-6 sm:px-7 py-3.5 sm:py-4 text-white font-semibold rounded-2xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5" style="background: var(--kg-accent); box-shadow: 0 8px 24px var(--kg-accent-shadow);">
                Перейти до каталогу
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </a>
              <Link
                :href="isGuest ? '/register' : '/books/create'"
                class="inline-flex items-center gap-2 px-6 sm:px-7 py-3.5 sm:py-4 font-semibold rounded-2xl border transition-all hover:shadow-md hover:-translate-y-0.5"
                :style="{ background: 'var(--kg-surface)', color: 'var(--kg-text)', borderColor: 'var(--kg-border)' }"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                {{ isGuest ? 'Почати продавати' : 'Продати книгу' }}
              </Link>
            </div>

            <div class="flex gap-8 sm:gap-10 mt-12 sm:mt-14">
              <div>
                <div class="text-2xl font-heading" style="color: var(--kg-text);">{{ books?.total || 0 }}</div>
                <div class="text-sm mt-1" style="color: var(--kg-text-muted);">книг у каталозі</div>
              </div>
              <div class="w-px" style="background: var(--kg-border);"></div>
              <div>
                <div class="text-2xl font-heading" style="color: var(--kg-text);">4.9 ★</div>
                <div class="text-sm mt-1" style="color: var(--kg-text-muted);">рейтинг покупців</div>
              </div>
            </div>
          </div>

          <div class="relative hidden lg:block animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="absolute -inset-4 rounded-[2.5rem] blur-2xl" style="background: linear-gradient(135deg, var(--kg-accent-light), var(--kg-danger-light));"></div>
            <div class="relative">
              <img
                src="https://images.unsplash.com/photo-1495446815901-a7297e633e8d?q=80&w=1200&auto=format&fit=crop"
                alt="Затишний куточок для читання"
                class="rounded-[2rem] shadow-2xl w-full object-cover h-[520px]"
                style="border: 1px solid var(--kg-border);"
              />
              <div class="absolute -bottom-5 -left-5 rounded-2xl shadow-xl p-4 border animate-float" style="background: var(--kg-surface); border-color: var(--kg-border);">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: var(--kg-danger-light);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: var(--kg-danger);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <div>
                    <div class="font-bold text-sm" style="color: var(--kg-text);">Купуй напряму</div>
                    <div class="text-xs" style="color: var(--kg-text-muted);">від продавця</div>
                  </div>
                </div>
              </div>
              <div class="absolute -top-4 -right-4 rounded-2xl shadow-xl px-4 py-3 border animate-float" style="background: var(--kg-surface); border-color: var(--kg-border); animation-delay: 3s;">
                <div class="flex items-center gap-2">
                  <span class="text-lg">📚</span>
                  <span class="font-bold text-sm" style="color: var(--kg-text);">Нові оголошення щодня</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== BENEFITS SECTION ===== -->
    <section class="text-white py-20 lg:py-24" style="background: var(--kg-section-dark-bg);">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
          <h2 class="text-3xl lg:text-4xl font-heading mb-4">Чому обирають knygogo?</h2>
          <p style="color: var(--kg-section-dark-text);" class="max-w-lg mx-auto">Найзручніша книжкова барахолка для справжніх книголюбів</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6">
          <div v-for="(benefit, i) in [
            { icon: 'M12 4v16m8-8H4', title: 'Продавай легко', desc: 'Додай фото, вкажи ціну — і покупці знайдуть твою книгу самі.' },
            { icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', title: 'Чат з продавцем', desc: 'Домовляйся про ціну та доставку напряму з продавцем.' },
            { icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', title: 'Безпечні угоди', desc: 'Перевіряй стан книги перед покупкою. Прозорий досвід для всіх.' },
            { icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', title: 'Еко-підхід', desc: 'Даємо книгам друге життя. Зберігаємо природу разом із кожною прочитаною сторінкою.' }
          ]" :key="i"
            class="reveal backdrop-blur-sm rounded-2xl p-6 border group transition-all duration-300"
            :style="{ background: 'var(--kg-section-dark-card)', borderColor: 'var(--kg-section-dark-border)', animationDelay: `${(i + 1) * 0.1}s` }"
          >
            <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform" style="background: rgba(91, 138, 114, 0.2);">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: #7DC4A5;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" :d="benefit.icon" />
              </svg>
            </div>
            <h3 class="text-lg font-heading mb-2">{{ benefit.title }}</h3>
            <p class="text-sm leading-relaxed" style="color: var(--kg-section-dark-text);">{{ benefit.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== CATALOG SECTION ===== -->
    <section id="catalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
      <div class="reveal flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
        <div>
          <h2 class="text-3xl lg:text-4xl font-heading" style="color: var(--kg-text);">Каталог книг</h2>
          <p class="mt-2" style="color: var(--kg-text-muted);">Знайди свою наступну улюблену книгу</p>
        </div>
      </div>

      <!-- Empty catalog -->
      <div v-if="bookList.length === 0" class="rounded-2xl p-16 border text-center" style="background: var(--kg-surface); border-color: var(--kg-border);">
        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-5" style="background: var(--kg-surface-alt);">
          <span class="text-3xl">📚</span>
        </div>
        <h3 class="text-lg font-bold mb-2" style="color: var(--kg-text);">Каталог поки порожній</h3>
        <p class="mb-6 max-w-sm mx-auto text-sm" style="color: var(--kg-text-muted);">Станьте першим, хто виставить книгу на продаж!</p>
        <Link :href="isGuest ? '/register' : '/books/create'" class="inline-block px-6 py-3 text-white font-semibold rounded-xl transition-all shadow-sm" style="background: var(--kg-accent);">
          {{ isGuest ? 'Зареєструватися' : 'Продати книгу' }}
        </Link>
      </div>

      <!-- Book Grid -->
      <div v-else class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        <Link
          v-for="(book, index) in bookList"
          :key="book.id"
          :href="`/books/${book.id}`"
          class="reveal group flex flex-col rounded-2xl overflow-hidden border transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
          :style="{ background: 'var(--kg-surface)', borderColor: 'var(--kg-border)', animationDelay: `${index * 0.05}s` }"
        >
          <div class="relative overflow-hidden aspect-[3/4]" style="background: var(--kg-surface-alt);">
            <span
              class="absolute top-2.5 left-2.5 px-2.5 py-1 text-[11px] font-bold rounded-lg z-10 shadow-sm"
              :style="{ background: getConditionStyle(book.condition).bg, color: getConditionStyle(book.condition).text }"
            >
              {{ book.condition }}
            </span>
            <img v-if="book.image_path" :src="'/storage/' + book.image_path" :alt="book.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" />
            <div v-else class="w-full h-full flex items-center justify-center">
              <span class="text-5xl opacity-20">📖</span>
            </div>
          </div>

          <div class="flex-1 flex flex-col p-4 sm:p-5">
            <h3 class="font-heading text-[15px] sm:text-[17px] leading-snug mb-1 transition-colors line-clamp-2" style="color: var(--kg-text);">
              {{ book.title }}
            </h3>
            <p class="text-xs sm:text-sm mb-1" style="color: var(--kg-text-muted);">{{ book.author }}</p>
            <p v-if="book.user" class="text-xs mb-3 sm:mb-4" style="color: var(--kg-text-faint);">від {{ book.user.name }}</p>

            <div class="mt-auto flex items-center justify-between">
              <span class="text-lg sm:text-xl font-bold" style="color: var(--kg-text);">{{ book.price }} ₴</span>
              <span class="text-[11px] sm:text-xs font-medium px-2.5 py-1 rounded-lg hidden sm:inline-block" style="color: var(--kg-accent-light-text); background: var(--kg-accent-light);">Переглянути</span>
            </div>
          </div>
        </Link>
      </div>
    </section>

    <!-- ===== TESTIMONIALS ===== -->
    <section class="py-20 lg:py-24" style="background: var(--kg-surface-alt);">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 reveal">
          <h2 class="text-3xl lg:text-4xl font-heading" style="color: var(--kg-text);">Що кажуть наші користувачі</h2>
          <p class="mt-2" style="color: var(--kg-text-muted);">Тисячі задоволених книголюбів</p>
        </div>

        <div class="grid md:grid-cols-3 gap-5 lg:gap-6">
          <div
            v-for="(t, i) in testimonials"
            :key="i"
            class="reveal rounded-2xl p-6 sm:p-7 border transition-all duration-300 hover:shadow-lg"
            :style="{ background: 'var(--kg-surface)', borderColor: 'var(--kg-border)', animationDelay: `${i * 0.15}s` }"
          >
            <div class="flex gap-1 mb-4">
              <svg v-for="s in t.rating" :key="s" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg v-for="s in 5 - t.rating" :key="'e' + s" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" style="color: var(--kg-border);" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
            <p class="text-[15px] leading-relaxed mb-5 italic" style="color: var(--kg-text-secondary);">"{{ t.text }}"</p>
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                {{ t.name.charAt(0) }}
              </div>
              <span class="font-semibold text-sm" style="color: var(--kg-text);">{{ t.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== NEWSLETTER CTA ===== -->
    <section class="relative overflow-hidden py-20 lg:py-24" style="background: var(--kg-accent);">
      <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Ccircle%20cx%3D%221%22%20cy%3D%221%22%20r%3D%221%22%20fill%3D%22rgba(255%2C255%2C255%2C0.05)%22%2F%3E%3C%2Fsvg%3E')] opacity-50"></div>

      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 reveal">
        <h2 class="text-3xl lg:text-4xl font-heading text-white mb-4">
          Не пропускай нові надходження
        </h2>
        <p class="text-white/70 text-lg mb-10 max-w-lg mx-auto">
          Підпишись на розсилку та отримуй анонси рідкісних видань і персональні рекомендації.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
          <input
            type="email"
            placeholder="Твій email"
            class="flex-1 px-5 py-3.5 sm:py-4 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 text-white placeholder:text-white/50 text-sm focus:outline-none focus:border-white/50 focus:ring-2 focus:ring-white/20 transition-all"
          />
          <button class="px-8 py-3.5 sm:py-4 bg-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 whitespace-nowrap" style="color: var(--kg-accent);">
            Підписатися
          </button>
        </div>

        <p class="text-white/40 text-xs mt-4">Жодного спаму. Тільки книги 📚</p>
      </div>
    </section>
  </MainLayout>
</template>
