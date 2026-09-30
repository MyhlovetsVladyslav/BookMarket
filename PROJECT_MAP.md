# BookMarket — Project Map

## Stack

| Layer      | Technology                                              |
|------------|---------------------------------------------------------|
| Backend    | PHP 8.2, Laravel 12, Laravel Fortify, Laravel Wayfinder |
| Frontend   | Vue 3.5, Inertia.js v2, TypeScript                      |
| UI         | TailwindCSS 4, Reka UI (headless), lucide-vue-next      |
| Build      | Vite 7, LightningCSS                                    |
| DB         | MySQL (Eloquent ORM)                                    |
| Auth       | Fortify (session, 2FA TOTP)                             |
| Routing FE | Vue Router (SPA, file-based via `routes/`)              |
| Routing BE | `routes/web.php`, `routes/auth.php`, `routes/settings.php` |

---

## Entry Points

| File                                 | Role                                      |
|--------------------------------------|-------------------------------------------|
| `public/index.php`                   | PHP entry, bootstraps Laravel             |
| `resources/views/app.blade.php`      | Единственный Blade-шаблон, монтирует Vue  |
| `resources/js/app.ts`                | Vue/Inertia bootstrap, регистрация layout |
| `resources/js/ssr.ts`                | SSR entry (опционально)                   |
| `resources/js/routes/index.ts`       | Корень Vue Router, агрегирует все маршруты |
| `vite.config.ts`                     | Сборка, aliases, laravel-vite-plugin      |

---

## Backend — `app/`

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php              — базовый контроллер
│   │   ├── BookController.php          — CRUD книг (пользовательская зона)
│   │   ├── ChatController.php          — чат (Conversation + Message)
│   │   ├── Admin/
│   │   │   ├── BookController.php      — CRUD книг (админ)
│   │   │   ├── DashboardController.php
│   │   │   └── UserController.php
│   │   ├── Auth/                       — Fortify-совместимые контроллеры
│   │   └── Settings/                   — Profile, Password, TwoFactor
│   ├── Middleware/
│   │   ├── AdminMiddleware.php
│   │   ├── SuperAdminMiddleware.php
│   │   ├── HandleInertiaRequests.php   — SharedProps → Vue (auth.user и т.д.)
│   │   └── HandleAppearance.php
│   └── Requests/                       — FormRequest валидация
├── Models/
│   ├── User.php                        — auth, roles, 2FA
│   ├── Book.php                        — title, author, price, cover, ...
│   ├── Conversation.php
│   └── Message.php
└── Providers/
    ├── AppServiceProvider.php
    └── FortifyServiceProvider.php      — регистрация Fortify-фич
```

---

## Frontend — `resources/js/`

```
resources/js/
├── app.ts                              — Inertia createInertiaApp, resolveComponent
├── ssr.ts
├── routes/                             — Vue Router маршруты (по доменам)
│   ├── index.ts                        — агрегатор
│   ├── admin/                          — /admin/books, /admin/users
│   ├── books/                          — /books, /books/:id, /books/create
│   ├── chat/                           — /chat, /chat/:id
│   ├── login/, register/, password/,
│   │   two-factor/, verification/,
│   │   profile/, storage/
├── pages/                              — Inertia-страницы (1:1 с маршрутами BE)
│   ├── Welcome.vue
│   ├── Dashboard.vue
│   ├── admin/                          — Books.vue, Users.vue, Dashboard.vue
│   ├── books/                          — Create.vue, Edit.vue, Show.vue
│   ├── Chat/                           — Index.vue, Show.vue
│   ├── auth/                           — Login, Register, ForgotPassword, ...
│   ├── settings/                       — Profile, Password, TwoFactor, Appearance
│   └── user/                           — Profile.vue
├── layouts/
│   ├── AppLayout.vue                   — обёртка с сайдбаром (app/)
│   ├── AuthLayout.vue                  — обёртка для auth-страниц
│   ├── AdminLayout.vue                 — обёртка для /admin/*
│   ├── MainLayout.vue
│   ├── app/                            — AppHeaderLayout, AppSidebarLayout
│   ├── auth/                           — Card, Simple, Split варианты
│   └── settings/Layout.vue
├── components/
│   ├── ui/                             — атомарные UI (shadcn-style поверх Reka UI)
│   │   ├── button/, input/, card/,
│   │   │   dialog/, select/, table/,
│   │   │   sidebar/, tooltip/, ...
│   └── UserInfo.vue, UserMenuContent.vue, NavMain.vue, ...
├── composables/
│   ├── useAppearance.ts                — dark/light mode
│   ├── useInitials.ts
│   └── useTwoFactorAuth.ts
├── lib/utils.ts                        — cn() хелпер (clsx + tailwind-merge)
├── types/index.d.ts                    — User, Book, SharedProps, PageProps
├── wayfinder/index.ts                  — типизированные URL-хелперы (Wayfinder)
```

---

## Routes — `routes/`

| File              | Prefix        | Middleware           |
|-------------------|---------------|----------------------|
| `web.php`         | `/`           | web, auth (частично) |
| `auth.php`        | `/`           | guest / auth         |
| `settings.php`    | `/settings`   | auth                 |

Админ-маршруты определены в `routes/web.php` под middleware `admin`.

---

## Data Flows

**Чтение страницы:**
```
Browser → Inertia (XHR/full) → Laravel Router → Controller
→ Inertia::render('Page', $props) → HandleInertiaRequests (SharedProps)
→ Vue page component (props via defineProps / usePage)
```

**Форма (submit):**
```
Vue useForm().post('/route') → Inertia PUT/POST → FormRequest → Controller
→ redirect()->back() / Inertia::render → Vue реактивный ответ
```

**Чат (Conversation/Message):**
```
ChatController → Conversation::firstOrCreate → Message::create
→ страница Chat/Show.vue получает messages через Inertia props
```

**Аутентификация:**
```
Fortify → FortifyServiceProvider (фичи: login, 2FA, reset, verify)
→ Auth controllers → Session / Cookie → HandleInertiaRequests SharedProps auth.user
```

---

## Key Config Files

| File              | Purpose                              |
|-------------------|--------------------------------------|
| `vite.config.ts`  | aliases `@/` → `resources/js/`       |
| `tsconfig.json`   | TS paths, strict mode                |
| `components.json` | shadcn/ui CLI конфиг                 |
| `.prettierrc`     | форматирование                       |
| `phpunit.xml`     | тест-конфиг                          |
