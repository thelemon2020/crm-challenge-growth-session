<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const clients = ref();

onMounted(() => {
    // fetch clients info through ClientService
    // but for now, let's create sample data here
    clients.value = [
        {
            id: 1,
            name: 'John Smith',
            email: 'john.smith@example.com',
            phone: '555-123-4567',
            company: 'Acme Corp',
            address: '123 Main St, New York, NY',
            status: 'active',
        },
        {
            id: 2,
            name: 'Jane Doe',
            email: 'jane.doe@example.com',
            phone: '555-987-6543',
            company: 'Tech Solutions',
            address: '456 Oak Ave, Los Angeles, CA',
            status: 'active',
        },
        {
            id: 3,
            name: 'Bob Johnson',
            email: 'bob.johnson@example.com',
            phone: '555-456-7890',
            company: 'Global Industries',
            address: '789 Pine Rd, Chicago, IL',
            status: 'inactive',
        },
        {
            id: 4,
            name: 'Alice Williams',
            email: 'alice.w@example.com',
            phone: '555-321-0987',
            company: 'StartUp Inc',
            address: '321 Elm Blvd, Austin, TX',
            status: 'pending',
        },
    ];
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="card">
                <DataTable :value="clients" :dt="{
                    headerCell: {
                        background: '{surface.800}',
                        color: '{surface.100}'
                    }
                }">
                    <Column field="name" header="Name"></Column>
                    <Column field="email" header="Email"></Column>
                    <Column field="status" header="Status"></Column>
                    <Column field="company" header="Company"></Column>
                    <Column field="address" header="Address"></Column>
                    <Column field="phone" header="Phone"></Column>
                    <Column header="Actions">
                        <template #body>
                            <div class="flex gap-1">
                                <Button label="Edit" size="small" />
                                <Button label="Delete" size="small" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
