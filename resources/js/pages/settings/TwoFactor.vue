<script setup lang="ts">
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { disable, enable } from '@/routes/two-factor';
import { Form, Head } from '@inertiajs/vue3';
import { onUnmounted, ref } from 'vue';

interface Props {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
}

withDefaults(defineProps<Props>(), {
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

onUnmounted(() => {
    clearTwoFactorAuthData();
});
</script>

<template>
    <SettingsLayout>
        <Head title="Двоетапна автентифікація — knygogo" />

        <div class="rounded-2xl border p-6 sm:p-8 transition-all" style="background: var(--kg-surface); border-color: var(--kg-border);">
            <div class="border-b pb-5 mb-6" style="border-color: var(--kg-border);">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-heading" style="color: var(--kg-text);">Двоетапна автентифікація</h2>
                            <span
                                v-if="twoFactorEnabled"
                                class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                style="background: var(--kg-accent-light); color: var(--kg-accent-light-text);"
                            >
                                Увімкнено
                            </span>
                            <span
                                v-else
                                class="px-2.5 py-0.5 text-xs font-semibold rounded-full text-amber-700 bg-amber-100 dark:text-amber-300 dark:bg-amber-900/30"
                            >
                                Вимкнено
                            </span>
                        </div>
                        <p class="text-sm mt-0.5" style="color: var(--kg-text-muted);">
                            Захистіть свій обліковий запис додатковим кодом підтвердження при вході
                        </p>
                    </div>
                </div>
            </div>

            <div v-if="!twoFactorEnabled" class="space-y-6">
                <p class="text-sm leading-relaxed max-w-2xl" style="color: var(--kg-text-secondary);">
                    Коли двоетапна автентифікація увімкнена, під час входу в систему крім пароля вам знадобиться ввести одноразовий 6-значний код безпеки з мобільного додатку (Google Authenticator, Microsoft Authenticator, 1Password тощо).
                </p>

                <div>
                    <button
                        v-if="hasSetupData"
                        @click="showSetupModal = true"
                        class="px-6 py-3 text-white font-semibold rounded-xl text-sm transition-all shadow-md hover:-translate-y-0.5 flex items-center gap-2"
                        :style="{ background: 'var(--kg-accent)', boxShadow: '0 4px 16px var(--kg-accent-shadow)' }"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Продовжити налаштування
                    </button>
                    <Form v-else v-bind="enable.form()" @success="showSetupModal = true" #default="{ processing }">
                        <button
                            type="submit"
                            :disabled="processing"
                            class="px-6 py-3 text-white font-semibold rounded-xl text-sm transition-all shadow-md hover:-translate-y-0.5 disabled:opacity-60 flex items-center gap-2"
                            :style="{ background: 'var(--kg-accent)', boxShadow: '0 4px 16px var(--kg-accent-shadow)' }"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            {{ processing ? 'Увімкнення...' : 'Увімкнути 2FA' }}
                        </button>
                    </Form>
                </div>
            </div>

            <div v-else class="space-y-6">
                <p class="text-sm leading-relaxed max-w-2xl" style="color: var(--kg-text-secondary);">
                    Двоетапну автентифікацію активовано. Під час кожного входу вам потрібно буде вказувати тимчасовий код із вашого додатку автентифікації.
                </p>

                <TwoFactorRecoveryCodes />

                <div class="pt-4 border-t" style="border-color: var(--kg-border);">
                    <Form v-bind="disable.form()" #default="{ processing }">
                        <button
                            type="submit"
                            :disabled="processing"
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all disabled:opacity-60 flex items-center gap-2"
                            style="background: var(--kg-danger);"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            {{ processing ? 'Вимкнення...' : 'Вимкнути 2FA' }}
                        </button>
                    </Form>
                </div>
            </div>

            <TwoFactorSetupModal
                v-model:isOpen="showSetupModal"
                :requiresConfirmation="requiresConfirmation"
                :twoFactorEnabled="twoFactorEnabled"
            />
        </div>
    </SettingsLayout>
</template>
