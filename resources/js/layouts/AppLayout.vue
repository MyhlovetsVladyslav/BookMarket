<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <MainLayout>
        <slot />
    </MainLayout>
</template>
