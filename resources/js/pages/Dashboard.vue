<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

defineProps({
    clientAmount: Number,
    projectAmount: Number,
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <Card>
                <template #title><span class="text-base">Total Clients</span></template>
                <template #content>
                    <p class="m-0">
                        <span class="text-2xl">{{ clientAmount }}</span>
                    </p>
                </template>
            </Card>

            <Card>
                <template #title><span class="text-base">Total Projects</span></template>
                <template #content>
                    <p class="m-0">
                        <span class="text-2xl">{{ projectAmount }}</span>
                    </p>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
