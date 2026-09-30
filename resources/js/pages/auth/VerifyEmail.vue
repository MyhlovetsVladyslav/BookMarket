<script setup lang="ts">
import EmailVerificationNotificationController from '@/actions/App/Http/Controllers/Auth/EmailVerificationNotificationController';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout title="Підтвердити email" description="Будь ласка, підтвердіть вашу електронну пошту, натиснувши на посилання, яке ми щойно надіслали.">
        <Head title="Підтвердження email — knygogo" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-[#5B8A72]">
            Нове посилання для підтвердження надіслано на вашу електронну пошту.
        </div>

        <Form v-bind="EmailVerificationNotificationController.store.form()" class="space-y-6 text-center" v-slot="{ processing }">
            <Button :disabled="processing" variant="secondary">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Надіслати повторно
            </Button>

            <TextLink :href="logout()" as="button" class="mx-auto block text-sm">Вийти</TextLink>
        </Form>
    </AuthLayout>
</template>
