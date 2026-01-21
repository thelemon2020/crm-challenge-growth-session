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

defineProps({ clients: Array });
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="card">
                <DataTable
                    :value="clients"
                    :dt="{
                        headerCell: {
                            background: '{surface.800}',
                            color: '{surface.100}',
                        },
                    }"
                >
                    <Column
                        field="name"
                        header="Name"
                        :sortable="true"
                    ></Column>
                    <Column
                        field="email"
                        header="Email"
                        :sortable="true"
                    ></Column>
                    <Column
                        field="status"
                        header="Status"
                        :sortable="true"
                    ></Column>
                    <Column
                        field="company"
                        header="Company"
                        :sortable="true"
                    ></Column>
                    <Column
                        field="address"
                        header="Address"
                        :sortable="true"
                    ></Column>
                    <Column
                        field="phone"
                        header="Phone"
                        :sortable="true"
                    ></Column>
                    <Column header="Actions">
                        <template #body>
                            <div class="flex gap-1">
                                <Button label="Edit" size="small" raised />
                                <Button
                                    label="Delete"
                                    size="small"
                                    severity="secondary"
                                    raised
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
