<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index, create, edit } from '@/routes/clients';
import { type BreadcrumbItem, Client } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useConfirm } from 'primevue';
import ClientController from '@/actions/App/Http/Controllers/ClientController';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Clients',
        href: index().url,
    },
];

interface ClientIndexProps {
    can: {
        manage_clients: boolean
    },
    clients: {
        data: Client[];
    };
}

defineProps<ClientIndexProps>();

const confirm = useConfirm();

const createClientRoute = computed(() => {
    return create().url;
});

function getStatusTagSeverity(status: 'active' | 'inactive') {
    const tagSeverityMapping = {
        active: 'success',
        inactive: 'warn',
    };

    return tagSeverityMapping[status];
}

function deleteClient(id: number) {
    confirm.require({
        message: 'Are you sure you want to delete this client?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger',
        },
        accept: () => {
            const clientDestroyRoute = ClientController.destroy(id);

            router.visit(clientDestroyRoute.url, {
                method: clientDestroyRoute.method,
            });
        },
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="card grid gap-4">
                <Button
                    as="a"
                    :href="createClientRoute"
                    class="w-fit"
                    label="Add Client"
                    icon="pi pi-plus"
                />

                <DataTable
                    :value="clients.data"
                    :dt="{
                        headerCell: {
                            background: '{surface.800}',
                            color: '{surface.100}',
                        },
                    }"
                >
                    <Column field="name" header="Name" :sortable="true" />
                    <Column
                        field="email"
                        header="Email"
                        :sortable="true"
                    ></Column>
                    <Column field="status" header="Status" :sortable="true">
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.status"
                                :severity="
                                    getStatusTagSeverity(slotProps.data.status)
                                "
                            />
                        </template>
                    </Column>
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
                        <template #body="slotProps">
                            <div class="flex gap-1">
                                <Button
                                    as="a"
                                    :href="edit(slotProps.data.id).url"
                                    label="Edit"
                                    size="small"
                                    raised
                                />
                                <Button
                                    v-if="can.manage_clients"
                                    type="submit"
                                    label="Delete"
                                    size="small"
                                    severity="secondary"
                                    raised
                                    @click="deleteClient(slotProps.data.id)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>


        <ConfirmDialog></ConfirmDialog>
    </AppLayout>
</template>
